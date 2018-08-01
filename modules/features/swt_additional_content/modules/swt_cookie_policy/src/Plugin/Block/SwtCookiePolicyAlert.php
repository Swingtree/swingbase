<?php

namespace Drupal\swt_cookie_policy\Plugin\Block;


use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Config;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Class SwtCookiePolicyAlert
 * @Block(
 *   id="swt_cookie_alert",
 *   admin_label=@Translation("Swt Cookie Policy - Alert"),
 *   category=@Translation("Swt Cookie Policy")
 * )
 */
class SwtCookiePolicyAlert extends BlockBase {

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * If a block should not be rendered because it has no content, then this
   * method must also ensure to return no content: it must then only return an
   * empty array, or an empty array with #cache set (with cacheability metadata
   * indicating the circumstances for it being empty).
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build() {

    /** @var \Drupal\Core\Config\ImmutableConfig $config */
    $config = \Drupal::config('swt_cookie_policy.alert');

    $entityQuery = \Drupal::entityQuery('node')
      ->condition('type','additional_content')
      ->condition('field_route','cookie_policy.info')
      ->range(0,1);

    $nids = $entityQuery->execute();

    $node = Node::load(current($nids));

    return [
      'content' => [
        '#theme' => 'swt_cookie_policy_alert',
        '#content' => \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser'),
        '#config' => [
          'accept_label' => $config->get('accept_label'),
          'more_label' => $config->get('more_label')
        ],
        '#more_url' => Url::fromRoute('swt_cookie_policy.info'),
      ],
      '#attached' => ['library' => ['swt_cookie_policy/swt_cookie_policy.alert']],
    ];
  }

  /**
   * @return array|string[]
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages']);
  }
}