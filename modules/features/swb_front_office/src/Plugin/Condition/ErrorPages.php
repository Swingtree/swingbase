<?php

namespace Drupal\swb_front_office\Plugin\Condition;


use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Error Pages' condition.
 *
 * @Condition(
 *   id = "error_pages",
 *   label = @Translation("Error Pages"),
 * )
 */
class ErrorPages extends ConditionPluginBase {

  // Store the condition configuration key
  const CONFIG_KEY = 'error_pages';

  const DISABLED = 'off';
  const ENABLED_ALL_PAGES = 'system.';
  const ENABLED_ONLY_PAGE_401 = 'system.401';
  const ENABLED_ONLY_PAGE_403 = 'system.403';
  const ENABLED_ONLY_PAGE_404 = 'system.404';


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [ self::CONFIG_KEY => self::DISABLED ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form[self::CONFIG_KEY] = [
      '#type' => 'radios',
      '#title' => $this->t('Select Error Pages condition mode'),
      '#default_value' => $this->configuration[self::CONFIG_KEY],
      '#options'=>[
        self::DISABLED => $this->t('Disabled'),
        self::ENABLED_ALL_PAGES => $this->t('Enabled on all errors pages (401-403-404)'),
        self::ENABLED_ONLY_PAGE_401 => $this->t('Enabled but only on 401 pages'),
        self::ENABLED_ONLY_PAGE_403 => $this->t('Enabled but only on 403 pages'),
        self::ENABLED_ONLY_PAGE_404 => $this->t('Enabled but only on 404 pages'),
      ]
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration[self::CONFIG_KEY] = $form_state->getValue(self::CONFIG_KEY);
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * @return bool
   */
  public function evaluate() {
    $pages = $this->configuration[self::CONFIG_KEY];
    if( $pages !== self::DISABLED ){
      $route_name = \Drupal::service('current_route_match')->getRouteName();
      if( strpos( $route_name, 'system.' ) === 0 && preg_match('/system\.40(1|3|4)/', $route_name) ){
        return strpos($route_name, $pages) === 0 * $this->isNegated();
      }
    }else{
      return TRUE;
    }
  }

  public function summary() {
    $pages = $this->configuration[self::CONFIG_KEY];
    if( $pages !== self::DISABLED ){
      if (!empty($this->configuration['negate'])) {
        return $this->t('Do not return true on the selected system pages');
      }
      return $this->t('Return true on selected system pages');
    }
    return $this->t('Disabled');

  }
}