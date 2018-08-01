(function ($, Drupal) {

  /**
   * Handle the navigation inside additional_page
   * @type {{attach: Drupal.behaviors.swtAdditionalContentPageNavigation.attach}}
   */
  Drupal.behaviors.swtAdditionalContentPageNavigation = {
    attach: function attach(context) {
      $('.block-swt-additional-content-page-summary', context).once('swtAdditionalContentPageNavigation').each(function(){

        var $summary = $(this);
          var $linksInPage = $summary.find('.block-content .links a');
          var $contentInPages = $('body .node--type-additional-content.node--view-mode-full');

          /**
           * Remove the active class on all links
           */
          var removeActiveLink = function(){
              $linksInPage.removeClass('active');
          };

          /**
           * Add event listener on all links
           */
          $linksInPage.click(function(e){
            e.preventDefault();

            //Remove all active class on all links
            removeActiveLink();

            //Add active class on currently clicked parent list
            $(e.currentTarget).addClass('active');

            var chapter = document.querySelector(e.currentTarget.getAttribute('href'));

            /**
             * If chapter found
             */
            if( chapter ){
              // Ease the scroll through the head
              var newY = chapter.offsetTop + chapter.parentElement.offsetTop;
//              TweenLite.to(window, 1, {scrollTo: chapter.offsetTop + chapter.parentElement.offsetTop , ease: Cubic.easeOut});
              window.scrollTo(0,newY);

            }
          });


          var getPageOffsetY = function(){
            if (window.pageYOffset != undefined)
            {
              return pageYOffset;
            }
            else
            {
              var sx, sy, d = document, r = d.documentElement, b = d.body;
              sy = r.scrollTop || b.scrollTop || 0;
              return sy;
            }
          };

          /*******************************************************************************************************************
           * Chapters Positions ( Y )
           * @type {Array}
           */
          var contentPositions = [];
          for (var i = 0; i < $contentInPages.length; i++) {
            contentPositions.push({e: $contentInPages[i],y:$contentInPages[i].offsetTop});
          }

          //Update positions
          var updateChapterPositions = function(){
            for (var i = 0; i < contentPositions.length; i++) {
              contentPositions[i].y = contentPositions[i].e.offsetTop;
            }
          };

          //Recalculate position when page loaded
          window.addEventListener('load',function(e){
            updateChapterPositions();
          });
          //Recalculate position when page resize
          window.addEventListener('resize',function(e){
            updateChapterPositions();
          });


          /**
           * Add scroll listener
           */
          window.addEventListener('scroll',function(e){

            //Get current page y position
            var pageOffsetY = getPageOffsetY();

            var minimalDistance = 99999;
            var selectedChapter = null;

            // Loop through all chapters to found the nearest one
            contentPositions.forEach(
                function (p)
                {
                  var diffTop = Math.abs(p.y - pageOffsetY);
                  var diffBottom = Math.abs(p.y - pageOffsetY);

                  var best = Math.floor(Math.min(diffTop, diffBottom));

                  if (best <= minimalDistance)
                  {
                    selectedChapter = p;
                    minimalDistance = best;
                  }

                }
            );

            removeActiveLink();

            /**
             * Find the matching link based on nearest chapter identifiant
             */
            for (var i = 0; i < $linksInPage.length; i++) {
              var link = $linksInPage[i];
              if( link.getAttribute('data-content-target-id') === selectedChapter.e.getAttribute('data-content-id') ){
                link.classList.add('active');
              }
            }
          });


      });
    }
  }
})(jQuery, Drupal);
