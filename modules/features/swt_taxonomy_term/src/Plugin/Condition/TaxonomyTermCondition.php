<?php

namespace Drupal\swt_taxonomy_term\Plugin\Condition;


use Drupal\Core\Annotation\ContextDefinition;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Condition\Annotation\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Provides a 'Term' condition.
 *
 * @Condition(
 *   id = "swt_taxonomy_term_condition",
 *   label = @Translation("Swingtree Extra : Taxonomy - Term Condition"),
 *   context = {
 *     "taxonomy_term" = @ContextDefinition("entity:taxonomy_term")
 *   }
 * )
 */
class TaxonomyTermCondition extends ConditionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['bundles' => []] + parent::defaultConfiguration();
  }

  /**
   * Build the configuration form
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Load all vocabulraty
    $vocabs = Vocabulary::loadMultiple();

    // To build options list
    $vocabOptions = [];
    /** @var Vocabulary $vocab */
    foreach ($vocabs as $vocab){
      $vocabOptions[$vocab->id()] = $vocab->get('name');
    }

    $form['bundles'] = array(
      '#title' => t('Select bundles where condition should pass'),
      '#type' => 'select',
      '#options' => $vocabOptions,
      '#default_value' => $this->configuration['bundles'],
      '#multiple' => TRUE,
    );

    // Add the default negate option
    $form = parent::buildConfigurationForm($form, $form_state);

    // update the form negate to use radios
    $form['negate']['#type'] = 'radios';
    $form['negate']['#title_display'] = 'invisible';
    $form['negate']['#default_value'] = (int) $form['negate']['#default_value'];
    $form['negate']['#options'] = [
      $this->t('Show on selected vocabulary'),
      $this->t('Hide on selected vocabulary'),
    ];

    return $form;
  }

  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['bundles'] = $form_state->getValue('bundles');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * @return bool
   */
  public function evaluate() {
    // If no bundle selected
    if (!$this->configuration['bundles']) {
      // might be true if condition is negate
      return !$this->configuration['negate'];
    }

    // Get the context term
    /** @var \Drupal\taxonomy\Entity\Term $term */
    $term = $this->getContextValue('taxonomy_term');

    // If term is empty
    if( empty($term) ){
      // might be true if condition is negate
      return !$this->configuration['negate'];
    }

    $found = in_array($term->bundle(),$this->configuration['bundles']);
    if( $this->configuration['negate']){
      return !$found;
    }

    return $found;
  }

  /**
   * @return mixed
   */
  public function summary() {
    if (!empty($this->configuration['negate'])) {
      return $this->t('Do not return true on following bundles : @bundles');
    }
    return $this->t('Return true on following bundles : @bundles');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'url.path';
    return $contexts;
  }

}