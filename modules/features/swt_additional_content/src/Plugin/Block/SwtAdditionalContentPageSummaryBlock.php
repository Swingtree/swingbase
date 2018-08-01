<?php

namespace Drupal\swt_additional_content\Plugin\Block;


use Drupal\Component\Utility\Html;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * Class SwtAdditionalContentPageSummaryBlock
 * @Block(
 *   id="swt_additional_content_page_summary",
 *   admin_label=@Translation("Swt Additional Content - Page Summary"),
 *   category=@Translation("Swt Additional Content")
 * )
 */
class SwtAdditionalContentPageSummaryBlock extends BlockBase {

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
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function build() {

    $parameters = \Drupal::routeMatch()->getParameters()->all();

    if( empty($parameters['route'])){
      return NULL;
    }

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'additional_content')
      ->condition('field_route', $parameters['route'], 'LIKE');

    $query->sort('field_route', 'ASC');

    $nids = $query->execute();

    if (empty($nids)) {
      return NULL;
    }

    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    $links = [
      '#theme' => 'links',
      '#type' => 'ol',
      '#links'=> [],
      ];


    /** @var \Drupal\node\Entity\Node $node */
    foreach ($nodes as $node){
      $linkHash = Html::getClass($node->getTitle());
      $links['#links'][] = [
        'url' => Url::fromUserInput('#'. $linkHash ),
        'title' => $node->getTitle(),
        'attributes' => [
          'data-content-target-id' => $linkHash
        ]
      ];
    }

    return [
      '#title' => $this->t('swt_additional_content.page_summary_block.title'),
      'content' => $links,
      '#attached' => [
        'library' => [
          'swt_additional_content/swt_additional_content.fixed_toc',
          'swt_additional_content/swt_additional_content.page_navigation',
        ]
      ]
    ];

  }

  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(),['url']);
  }
}