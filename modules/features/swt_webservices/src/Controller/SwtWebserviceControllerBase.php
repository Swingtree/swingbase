<?php

namespace Drupal\swt_webservices\Controller;


use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class SwtWebserviceControllerBase extends ControllerBase {

  protected function respond( $status, $message, $data = NULL ){
    $responseData = [
      'status' => $status,
      'message' => $message,
    ];
    
    if( !empty($data) ){
      $responseData['data'] = $data;
    }
    
    return JsonResponse::create( $responseData, $status);
  }
}