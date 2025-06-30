/**
 * @file
 * Description.
*/

(function ($, Drupal) {
  Drupal.behaviors.pageLayout = {
    attach: function (context) {
      once('pageLayout', '#header', context).forEach(
        function (header) {
          const toggleClass = "alignTop";
          const contentHeaderHeight = document.getElementsByClassName('js--hero-header');
          let scrollHeightChangeHeader = 150;

          if (typeof(contentHeaderHeight) != 'undefined' && contentHeaderHeight != null) {
            scrollHeightChangeHeader = contentHeaderHeight[0].offsetHeight;
          }

          window.addEventListener("scroll", () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll > scrollHeightChangeHeader) {
              header.classList.add(toggleClass);
            } else {
              header.classList.remove(toggleClass);
            }
          });
        }
      )
    }
  };
})(jQuery, Drupal);

