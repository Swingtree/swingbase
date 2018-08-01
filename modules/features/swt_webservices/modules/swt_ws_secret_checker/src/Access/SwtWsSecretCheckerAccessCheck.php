<?php

namespace Drupal\swt_ws_secret_checker\Access;


class SwtWsSecretCheckerAccessCheck extends SwtWsSecretCheckerAccessCheckBase {

  /**
   * This method should return key/secret from the source the extender use
   *
   * @return mixed
   */
  protected function retrieveKeySecret() {

    // Retrieve the key secret from parameters

    /** @var \Symfony\Component\HttpFoundation\Request $request */
    $request = \Drupal::request();

    $key = NULL;
    $secret = NULL;

    $parameters = $request->request->all();
    if(array_key_exists('key', $parameters)){
      $key = $parameters['key'];
    }

    if( array_key_exists('secret', $parameters)){
      $secret = $parameters['secret'];
    }

    return [$key, $secret];
  }
}