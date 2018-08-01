<?php

namespace Drupal\swt_social_login_approval;

use Drupal\social_auth\SocialAuthUserManager;

class SwtSocialLoginApprovalManager extends SocialAuthUserManager{

  /**
   * Do not require approbal for social registered users
   * @return bool
   */
  protected function isApprovalRequired() {
    return FALSE;
  }
}