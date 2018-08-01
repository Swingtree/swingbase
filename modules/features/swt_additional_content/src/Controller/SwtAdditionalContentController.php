<?php

namespace Drupal\swt_additional_content\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class SwtAdditionalContentController extends ControllerBase {

  /**
   * Display pages from additional content
   * @param $route
   *
   * @return array
   */
  function page($route){
    $nids = \Drupal::entityQuery('node')
      ->condition('type','additional_content')
      ->condition('field_route', $route, 'LIKE')
      ->sort('field_route','ASC')
      ->execute();

    if( empty($nids)){
      return ['#markup'=> $this->t('No additional content match the %route route',['%route'=>$route])];
    }

    $nodes = Node::loadMultiple($nids);

    return \Drupal::entityTypeManager()->getViewBuilder('node')->viewMultiple($nodes);

  }

  /**
   * Title callback
   * @param $route
   *
   * @return array
   */
  function pageTitle($route){
    $nids = \Drupal::entityQuery('node')
      ->condition('type','additional_content')
      ->condition('field_route', $route, 'LIKE')
      ->sort('field_route','ASC')
      ->execute();

    if( empty($nids)){
      return ['#markup'=> $this->t('No additional content match the %route route',['%route'=>$route])];
    }

    $nodes = Node::loadMultiple($nids);

    $build = array();
    $build['#markup'] = current($nodes)->getTitle();
    return $build;
  }

}