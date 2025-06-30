((Drupal) => {
  Drupal.behaviors.animatedButton = {
    attach(context) {
      context.querySelectorAll('.animated-btn').forEach((btn) => {

        btn.addEventListener('click', () => {
          const isExpanded = btn.getAttribute('aria-expanded') === 'true';
          btn.setAttribute('aria-expanded', !isExpanded);
        })
      });
    },
  };
})(Drupal);
