<?php

namespace Drupal\swt_cookie_policy\Plugin\Block;


use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Class SwtCookiePolicyPreferences
 * @Block(
 *   id="swt_cookie_preferences",
 *   admin_label=@Translation("Swt Cookie Policy - Preferences"),
 *   category=@Translation("Swt Cookie Policy")
 * )
 */
class SwtCookiePolicyPreferences extends BlockBase {

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
    return [
      '#title' => $this->t('swt_cookie_policy.preferences.title'),
      'content' => [
        '#theme' => 'swt_cookie_policy_preferences',
      ],
      '#attached' => ['library' => ['swt_cookie_policy/swt_cookie_policy.preferences']],
    ];
  }

  /**
   * @return array|string[]
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages']);
  }
}