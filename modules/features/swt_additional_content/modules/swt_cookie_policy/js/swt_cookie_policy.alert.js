(function($) {

  /**
   * Swt cookie policy alert
   * Will be displayed if cookie not yet accepted
   * @type {{attach: Drupal.behaviors.swtCookiePolicyAlert.attach}}
   */
  Drupal.behaviors.swtCookiePolicyAlert = {
    attach: function(context, settings) {
      $('.block-swt-cookie-policy.block-swt-cookie-alert', context).
          once('swtCookiePolicyAlert').
          each(function() {

            // can we create cookies?
            if (!$.isFunction($.cookie)) {
              throw Error('No cookie engine available for swt_cookie_policy');
            }

            var $elem = $(this);
            if (!$.cookie('cookie_acceptance')) {

              // TO be sure the policy alert run when all other things are
              // loaded
              window.addEventListener('load', function() {
                // After a delay
                setTimeout(function() {
                  console.log('timeOut done');
                  $elem.css('display', 'block');
                  $elem.addClass('show');
                }, 1250);

              });

              $elem.find('.cookie-policy__interface button.accept').
                  on('click', function(e) {
                    $.cookie('cookie_acceptance', 3, {expires: 365, path: '/'});
                    $elem.css('pointer-events','none');
                    $elem.removeClass('show');
                  });
            }

          });
    },
  };
}(jQuery));