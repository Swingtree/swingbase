<?php

namespace Drupal\swt_social_login_approval;


use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class SwtSocialLoginApprovalServiceProvider extends ServiceProviderBase {

  /**
   *
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('social_auth.user_manager');
    $definition->setClass('Drupal\swt_social_login_approval\SwtSocialLoginApprovalManager');
  }


}