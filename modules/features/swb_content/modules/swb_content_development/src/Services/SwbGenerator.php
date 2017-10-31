<?php

namespace Drupal\swb_content_development\Services;

use Drupal\data_fixtures\Services\DummyGenerator;

class SwbGenerator extends DummyGenerator {


  /**
   * Add the possibility to provide titles templates
   *
   * @param array $titles
   * @param int $maxLength
   * @return string
   */
  public function getTitle( $titles = [],$maxLength = 255) {
    if( !empty($titles)){
      return $this->getRandomValue($titles);
    }
    return parent::getTitle($maxLength); // TODO: Change the autogenerated stub
  }

  public function getRandomValue( $arr ){
    return $arr[$this->getRandomIndex($arr)];
  }
}