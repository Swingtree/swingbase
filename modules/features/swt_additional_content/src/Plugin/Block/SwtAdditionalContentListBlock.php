<?php

namespace Drupal\swt_additional_content\Plugin\Block;


use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class SwtAdditionalContentListBlock
 * @Block(
 *   id="swt_additional_content_list",
 *   admin_label=@Translation("Swt Additional Content - List"),
 *   category=@Translation("Swt Additional Content")
 * )
 */
class SwtAdditionalContentListBlock extends BlockBase {

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

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'additional_content')
      ->condition('field_route', $this->configuration['route'], 'LIKE');

    if ($this->configuration['items'] != -1 && !empty($this->configuration['items'])) {
      $query->range(0, $this->configuration['items']);
    }

    $query->sort('field_route', $this->configuration['order']);

    $nids = $query->execute();

    if (empty($nids)) {
      return NULL;
    }

    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    $classes = explode(' ',$this->configuration['classes']);
    $view_mode = str_replace('node.','',$this->configuration['viewmode']);

    $block_content = [
      '#theme' => 'swt_additional_content_list_block',
      '#content' => \Drupal::entityTypeManager()->getViewBuilder('node')->viewMultiple($nodes,$view_mode),
    ];
    if( !empty($this->configuration['readmore_path']) ){
      $block_content['#readmore'] = [
        'label' => $this->t('Read more',[],['context'=>$this->configuration['context_block']]),
        'url' => Url::fromUri( $this->configuration['readmore_path'] ),
      ];
    }
    return [
      'content' => $block_content,
      '#attributes' => ['class' => $classes],
    ];

  }

  public function defaultConfiguration() {
    return [
      'viewmode' => 'node.teaser',
      'items' => -1,
      'order' => 'ASC',
      'route' => '',
      'classes' => '',
      'readmore_path' => '',
      'context_block' => '',
    ];
  }

  /**
   *
   * Build the configuration of this block
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form['selector'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content Selection'),
    ];

    $form['selector']['route'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Route'),
      '#description' => $this->t(
        'Select content based on their route. % can be used for patterns.'
      ),
      '#default_value' => $this->configuration['route'],
    ];

    $form['selector']['items'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of items to select'),
      '#default_value' => $this->configuration['items'],
      '#description' => $this->t(
        'Define how many content should be selected by this block'
      ),
    ];

    $form['selector']['order'] = [
      '#type' => 'select',
      '#title' => $this->t('Sorting order'),
      '#description' => $this->t('Define how the items should be ordered.'),
      '#options' => ['ASC'=>'ASC', 'DESC'=>'DESC'],
      '#default_value' => $this->configuration['order'],
    ];

    $form['display'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Display'),
    ];

    $viewmodeIds = \Drupal::entityQuery('entity_view_mode')
      ->condition('targetEntityType', 'node')
      ->execute();
    $viewmodes = EntityViewMode::loadMultiple($viewmodeIds);
    $viewmodeOptions = [];
    /** @var EntityViewMode $viewmode */
    foreach ($viewmodes as $viewmode) {
      $viewmodeOptions[$viewmode->id()] = $viewmode->label();
    }

    $form['display']['viewmode'] = [
      '#type' => 'select',
      '#title' => $this->t('Viewmode'),
      '#description' => $this->t('Select the view mode to display elements'),
      '#options' => $viewmodeOptions,
      '#default_value' => $this->configuration['viewmode'],
    ];

    $form['display']['classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Additional classes'),
      '#description' => $this->t(
        'Add classes to the output block. Separate classes with spaces'
      ),
      '#default_value' => $this->configuration['classes'],
    ];

    $form['readmore'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Readmore'),
    ];

    $form['readmore']['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path'),
      '#default_value' => $this->configuration['readmore_path'],
    ];

    return $form;
  }

  /**
   * Update configuration
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function blockSubmit( $form, FormStateInterface $form_state) {
    if (!$form_state->getErrors()) {
      $selection = $form_state->getValue('selector');
      $this->configuration['route'] = $selection['route'];
      $this->configuration['items'] = $selection['items'];
      $this->configuration['order'] = $selection['order'];
      $display = $form_state->getValue('display');
      $this->configuration['viewmode'] = $display['viewmode'];
      $this->configuration['classes'] = $display['classes'];
      $readmore = $form_state->getValue('readmore');
      $this->configuration['readmore_path'] = $readmore['path'];

      /** @var \Drupal\Core\Form\SubformState $subFormState */
      $subFormState = $form_state;
      $this->configuration['context_block'] = $subFormState->getCompleteFormState()->getValue('id');
    }

  }
}