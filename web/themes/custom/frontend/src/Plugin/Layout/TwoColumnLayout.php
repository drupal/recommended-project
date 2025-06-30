<?php

namespace Drupal\frontend\Plugin\Layout;

use Drupal\layout_builder\Plugin\Layout\MultiWidthLayoutBase;

/**
 * Configurable two column layout plugin class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class TwoColumnLayout extends MultiWidthLayoutBase {

  /**
   * {@inheritdoc}
   */
  protected function getWidthOptions() {
    return [
      '50-50' => '50% / 50%',
      '33-67' => '33% / 67%',
      '67-33' => '67% / 33%',
      '25-75' => '25% / 75%',
      '75-25' => '75% / 25%',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultWidth() {
    return '50-50';
  }

}
