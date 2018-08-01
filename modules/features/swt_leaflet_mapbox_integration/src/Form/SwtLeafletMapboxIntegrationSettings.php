<?php

namespace Drupal\swt_leaflet_mapbox_integration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SwtLeafletMapboxIntegrationSettings extends ConfigFormBase {

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [
      'swt_leaflet_mapbox_integration.settings'
    ];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return "swt_leaflet_mapbox_integration_settings";
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('swt_leaflet_mapbox_integration.settings');

    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mapbox API Token'),
      '#default_value' => $config->get('token'),
    ];

    return parent::buildForm( $form, $form_state );

  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('swt_leaflet_mapbox_integration.settings');

    $config->set('token', $values['token'])->save();

    parent::submitForm($form, $form_state);
  }


}