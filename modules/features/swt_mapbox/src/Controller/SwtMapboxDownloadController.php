<?php

namespace Drupal\swt_mapbox\Controller;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Image\ImageFactory;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\image\ImageStyleInterface;
use Drupal\system\FileDownloadController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Defines a controller to serve image styles.
 */
class SwtMapboxDownloadController extends FileDownloadController {

  /**
   * The lock backend.
   *
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * The image factory.
   *
   * @var \Drupal\Core\Image\ImageFactory
   */
  protected $imageFactory;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a ImageStyleDownloadController object.
   *
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   The lock backend.
   * @param \Drupal\Core\Image\ImageFactory $image_factory
   *   The image factory.
   */
  public function __construct(LockBackendInterface $lock, ImageFactory $image_factory) {
    $this->lock = $lock;
    $this->imageFactory = $image_factory;
    $this->logger = $this->getLogger('swt_mapbox');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('lock'),
      $container->get('image.factory')
    );
  }

  /**
   * Generates a derivative, given a style and image path.
   *
   * After generating an image, transfer it to the requesting agent.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
   *   The transferred file as response or some error response.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *   Thrown when the user does not have access to the file.
   * @throws \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
   *   Thrown when the file is still being generated.
   */
  public function deliver(Request $request) {
    $target = $request->query->get('file');

    $directory = 'public://mapbox/cache/static-map/';
    $image_uri = 'https://api.mapbox.com/v4/' . $target;


    $derivative_uri = $directory.$target;

    $headers = [];

    // Don't start generating the image if the derivative already exists or if
    // generation is in progress in another thread.
    if (!file_exists($derivative_uri)) {
      $lock_name = 'swt_mapbox_cache_deliver:' . $derivative_uri . ':' . Crypt::hashBase64($image_uri);
      $lock_acquired = $this->lock->acquire($lock_name);
      if (!$lock_acquired) {
        // Tell client to retry again in 3 seconds. Currently no browsers are
        // known to support Retry-After.
        throw new ServiceUnavailableHttpException(3, $this->t('Image generation in progress. Try again shortly.'));
      }
    }

    // Try to generate the image, unless another thread just did it while we
    // were acquiring the lock.

    // Build the destination folder tree if it doesn't already exist.
    if (!file_prepare_directory($directory, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
      \Drupal::logger('swt_mapbox')->error('Failed to create style directory: %directory', ['%directory' => $directory]);
    }


    $success = file_exists($derivative_uri) || $this->generateDerivative($target);
    $i=2;

    if (!empty($lock_acquired)) {
      $this->lock->release($lock_name);
    }

    if ($success) {
      $image = $this->imageFactory->get($derivative_uri);
      $uri = $image->getSource();
      $headers += [
        'Content-Type' => $image->getMimeType(),
        'Content-Length' => $image->getFileSize(),
      ];
      // \Drupal\Core\EventSubscriber\FinishResponseSubscriber::onRespond()
      // sets response as not cacheable if the Cache-Control header is not
      // already modified. We pass in FALSE for non-private schemes for the
      // $public parameter to make sure we don't change the headers.
      return new BinaryFileResponse($uri, 200, $headers, TRUE);
    }
    else {
      $this->logger->notice('Unable to generate the derived image located at %path.', ['%path' => $derivative_uri]);
      return new Response($this->t('Error generating image.'), 500);
    }
  }

  private function generateDerivative($target){
    $base_directory = 'public://mapbox/cache/static-map/';
    $image_uri = 'https://api.mapbox.com/styles/v1/' . substr($target,0,strpos($target,'.png'));

    // TODO: access token from settings
    $image_uri .= '?access_token=pk.eyJ1Ijoic3dpbmd0cmVlLXN0dWRpbyIsImEiOiJjamR5ZXI1MDYxMzF0MnZvaHAxeGhtcWU2In0.xpY4V8dZ3Jj6T6Do-Fl0Qg';
    $image_uri .= '&attribution=false&logo=false';

    $file_data = file_get_contents($image_uri);


    $directory = $base_directory . $target;
    $directory = dirname($directory);
    file_prepare_directory( $directory, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS );
    return file_unmanaged_save_data($file_data, $base_directory.$target);
  }

}
