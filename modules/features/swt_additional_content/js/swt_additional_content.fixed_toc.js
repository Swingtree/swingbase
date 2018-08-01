(function ($, Drupal) {
  /**
   * Prepare theme to adjust something when toc out of window
   * As it depends on lot of theme settings, this compute is easy to work after
   * So themes can handle how to threat this value according to their breakpoints and settings
   * @type {{attach: Drupal.behaviors.swtAdditionalContentFixedToc.attach}}
   */
  Drupal.behaviors.swtAdditionalContentFixedToc = {
    attach: function attach(context) {
      $('.block-swt-additional-content-page-summary', context).once('swtAdditionalContentFixedToc').each(function(){
        var $pageSummary = $(this);

        var $parent = $pageSummary.parents('.sidebar');

        function isElementInViewport (el) {

          //special bonus for those using jQuery
          if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
          }

          var rect = el.getBoundingClientRect();

          return (
              rect.top
          );
        }

        window.addEventListener('scroll', function(e){
          var rectTop = isElementInViewport($parent);
          if( !rectTop >= 0){
            $parent.addClass('fixed-toc');
            $parent.attr('data-fixed-toc-offset',rectTop);
          }else{
            $parent.removeClass('fixed-toc');
            $parent.removeAttr('data-fixed-toc-offset');
          }
        });
      });
    }
  }
})(jQuery, Drupal);