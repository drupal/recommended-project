/**
 * @file
 * Description.
 */

(function ($, Drupal) {
  Drupal.behaviors.seeAllToggle = {
    attach: function (context) {
      $(once('seeAllToggle', '.js--show-all', context)).each(function () {
        const initBtnText = $(this).html();

        $(this).on('click', function() {
          const btn = $(this);
          const container_participants = btn
            .parents(".view")
            .find(".toggleable-grid");
          container_participants.toggleClass("close");
          btn.toggleClass("show-all");
          if (btn.hasClass("show-all")) {
            btn.html("See less");
          } else {
            btn.html(initBtnText);
          }
        });
      });
    },
  };
})(jQuery, Drupal);
