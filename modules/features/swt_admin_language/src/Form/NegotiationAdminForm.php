<?php

namespace Drupal\swt_admin_language\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure the selected language negotiation method for this site.
 *
 * @internal
 */
class NegotiationAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'swt_admin_language_negotiation_configure_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['language.swt_admin_language'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('language.swt_admin_language');
    $form['forced_admin_langcode'] = [
      '#type' => 'language_select',
      '#title' => $this->t('Language'),
      '#languages' => LanguageInterface::STATE_CONFIGURABLE | LanguageInterface::STATE_SITE_DEFAULT,
      '#default_value' => $config->get('forced_admin_langcode'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('language.swt_admin_language')
      ->set('forced_admin_langcode', $form_state->getValue('forced_admin_langcode'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
