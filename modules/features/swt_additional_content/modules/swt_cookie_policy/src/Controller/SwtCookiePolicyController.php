<?php

namespace Drupal\swt_cookie_policy\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Class SwtCookiePolicyController
 *
 * @package Drupal\swt_cookie_policy\Controller
 */
class SwtCookiePolicyController extends ControllerBase {

  /**
   * @return array
   */
  public function info(){

    $config = $this->config('swt_cookie_policy.alert');

    $entityQuery = \Drupal::entityQuery('node')
      ->condition('type','additional_content')
      ->condition('field_route','cookie_policy.info')
      ->range(0,1);

    $nids = $entityQuery->execute();

    $node = Node::load(current($nids));

    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node);
  }
}