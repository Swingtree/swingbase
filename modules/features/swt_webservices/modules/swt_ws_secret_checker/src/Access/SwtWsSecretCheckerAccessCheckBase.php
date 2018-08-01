<?php

namespace Drupal\swt_ws_secret_checker\Access;


use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Plugin\views\filter\Access;
use PhpParser\Node\Stmt\Return_;
use Symfony\Component\Routing\Route;

abstract class SwtWsSecretCheckerAccessCheckBase implements AccessInterface{

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * The flood service.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  private $flood;

  /**
   * SwtWsSecretCheckerAccessCheckBase constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Flood\FloodInterface $flood
   */
  public function __construct(ConfigFactoryInterface $config_factory, FloodInterface $flood) {
    $this->configFactory = $config_factory;
    $this->flood = $flood;
  }

  /**
   * This abstract method should return key/secret from the source the extender use
   * @return mixed
   */
  abstract protected function retrieveKeySecret();

  /**
   * Access check on key/secret
   * @param \Symfony\Component\Routing\Route $route
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResultAllowed|\Drupal\Core\Access\AccessResultForbidden
   */
  public final function access( Route $route, RouteMatchInterface $route_match, AccountInterface $account) {

    $flood_config = $this->configFactory->get('user.flood');


    //Get value from extender
    list($key, $secret) = $this->retrieveKeySecret();

    // Flood protection: this is very similar to the user login form code.
    // @see \Drupal\user\Form\UserLoginForm::validateAuthentication()
    // Do not allow any login from the current user's IP if the limit has been
    // reached. Default is 50 failed attempts allowed in one hour. This is
    // independent of the per-user limit to catch attempts from one IP to log
    // in to many different user accounts.  We have a reasonably high limit
    // since there may be only one apparent IP for all users at an institution.
    $floodKey = 'swt_ws_secret_checked.failed_attempt_ip';
    $ipLimit = $flood_config->get('ip_limit');
    $ipWindow = $flood_config->get('ip_window');

    if ($this->flood->isAllowed( $floodKey, $ipLimit, $ipWindow )) {
      // Empty keys will return an unauthorized
      if (!empty($key) && !empty($secret)) {

        $floodIdentifier = $key . '-' .\Drupal::request()->getClientIp();

        $userLimit = $flood_config->get('user_limit');
        $userWindow = $flood_config->get('user_window');
        $floodUserKey = 'swt_ws_secret_checked.failed_attempt_key';
        if ($this->flood->isAllowed($floodUserKey, $userLimit, $userWindow, $floodIdentifier)) {

          //If key not empty, we can load based on parameters
          $ksid = Database::getConnection()
            ->select('swt_ws_key_sec_registry', 'ksr')
            ->fields('ksr', ['ksid'])
            ->condition('ksr.`key`', $key)
            ->condition('secret', $secret)
            ->execute()->fetchAllAssoc('ksid');

          // If result found, access allowed
          if (!empty($ksid)) {

            // Clear any previous flood for this identifier
            $this->flood->clear($floodUserKey, $floodIdentifier);

            return AccessResult::allowed();
          }

          // Store a login attempt
          $this->flood->register($floodUserKey, $userWindow, $floodIdentifier);

          // If no result found, return a forbidden access
          return AccessResult::forbidden('Forbidden access');
        }

        // Store a login attempt even if it was already ban
        $this->flood->register($floodUserKey, $userWindow, $floodIdentifier);

        // And access unauthorized
        return AccessResult::forbidden('Too many wrong attempts leads to temporary ban. Try again later');

      }

      // Always register an IP-based failed login event.
      $this->flood->register($floodKey, $ipWindow);

      // and access unauthorized
      return AccessResult::forbidden('Unauthorized');
    }

    // Even if this is already ban
    // Always register an IP-based failed login event.
    $this->flood->register($floodKey, $ipWindow);

    // And access unauthorized
    return AccessResult::forbidden('Too many wrong attempts leads to temporary ban. Try again later');
  }
}