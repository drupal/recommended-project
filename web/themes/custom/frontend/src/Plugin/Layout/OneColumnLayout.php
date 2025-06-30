<?php

namespace Drupal\frontend\Plugin\Layout;

use Drupal\layout_builder\Plugin\Layout\MultiWidthLayoutBase;

/**
 * Configurable one column layout plugin class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class OneColumnLayout extends MultiWidthLayoutBase {

  /**
   * {@inheritdoc}
   */
  protected function getWidthOptions() {
    return [
      'default' => 'Default width',
      'small' => 'Narrower width',
      'full' => 'Full width',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultWidth() {
    return 'default';
  }

}
