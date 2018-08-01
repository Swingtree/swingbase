(function($) {

  /**
   * Swt cookie policy preferences
   * Will be displayed if cookie not yet accepted
   * @type {{attach: Drupal.behaviors.swtCookiePolicyPreferences.attach}}
   */
  Drupal.behaviors.swtCookiePolicyPreferences = {
    attach: function(context, settings) {
      $('.block-swt-cookie-policy.block-swt-cookie-preferences', context).
          once('swtCookiePolicyPreferences').
          each(function() {

          });
    },
  };
}(jQuery));