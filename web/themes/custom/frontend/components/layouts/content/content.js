(function (Drupal, once) {
  Drupal.behaviors.jsSidebar = {
    attach: function (context) {
      once('jsSidebar', '.js--show-sidebar').forEach(function (button) {

        button.addEventListener('click', () => {
          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          button.setAttribute('aria-expanded', !isExpanded);
        });
      });
    },
  };
})(Drupal, once);
