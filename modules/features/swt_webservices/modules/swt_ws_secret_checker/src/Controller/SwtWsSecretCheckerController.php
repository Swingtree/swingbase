<?php


namespace Drupal\swt_ws_secret_checker\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\swt_webservices\Controller\SwtWebserviceControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SwtWsSecretCheckerController extends SwtWebserviceControllerBase {

  public function check() {
    return $this->respond(200, '', 'You successfully passed through the key secret checker');
  }

}