<?php

namespace Drupal\swt_ws_secret_checker\Access;


use Drupal\Core\Access\AccessResult;
use Drupal\Core\Database\Database;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Plugin\views\filter\Access;
use Symfony\Component\Routing\Route;

class SwtWsSecretCheckerAuthAccessCheck extends SwtWsSecretCheckerAccessCheckBase {

  /**
   * This method should return key/secret from the source the extender use
   *
   * @return mixed
   */
  protected function retrieveKeySecret() {

    // Retrieve key/secret from basic auth

    /** @var \Symfony\Component\HttpFoundation\Request $request */
    $request = \Drupal::request();
    // Basic auth

    $username = $request->headers->get('PHP_AUTH_USER');
    $password = $request->headers->get('PHP_AUTH_PW');

    return [$username,$password];
  }

}