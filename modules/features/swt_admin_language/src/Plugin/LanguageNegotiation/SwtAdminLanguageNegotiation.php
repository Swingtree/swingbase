<?php

namespace Drupal\swt_admin_language\Plugin\LanguageNegotiation;

use Drupal\Core\Annotation\Translation;
use Drupal\language\Annotation\LanguageNegotiation;
use Drupal\language\LanguageNegotiationMethodBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class for identifying language from a selected language.
 *
 * @LanguageNegotiation(
 *   id = Drupal\swt_admin_language\Plugin\LanguageNegotiation\SwtAdminLanguageNegotiation::METHOD_ID,
 *   types = {\Drupal\Core\Language\LanguageInterface::TYPE_INTERFACE},
 *   weight = -10,
 *   name = @Translation("Forced Admin Language"),
 *   description = @Translation("Force the administrative language based on a selected language."),
 *   config_route_name = "swt_admin_language.settings"
 * )
 */
class SwtAdminLanguageNegotiation extends LanguageNegotiationMethodBase {

  /**
   * The language negotiation method id.
   */
  const METHOD_ID = 'language-admin';

  /**
   * {@inheritdoc}
   */
  public function getLangcode(Request $request = NULL) {
    $langcode = NULL;

    $admin_context = \Drupal::service('router.admin_context');
    $routeObject = \Drupal::routeMatch()->getRouteName();

    if ( strpos($request->getUri(),'/admin/') !== FALSE || ( is_object($routeObject) && $admin_context->isAdminRoute( $routeObject ) ) ) {
      $langcode = $this->config->get('language.swt_admin_language')->get('forced_admin_langcode');
    }

    return $langcode;
  }

}
