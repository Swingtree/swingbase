<?php

use Drupal\Core\Database\Query\AlterableInterface;

/**
 * Add a random query tag
 * @URL https://www.drupal.org/node/1174806
 *
 * $query->addTag('random');
 * Will trigger this alteration hook
 *
 */
function swingbase_query_random_alter(AlterableInterface $query) {
  $query->orderRandom();
}