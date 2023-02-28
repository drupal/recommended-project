<?php

/**
 * @file
 * Eau de Web Theme theme file.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 */
function frontend_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['edwt'] = [
    '#type' => 'vertical_tabs',
    '#title' => t('Theme settings'),
    '#weight' => -10,
  ];

  // Main settings.
  $form['settings'] = [
    '#type' => 'details',
    '#title' => t('Main'),
    // '#description'  => t('some description'),
    '#group' => 'edwt',
    '#weight' => 1,
  ];
  include 'theme-settings/settings-global.inc';

  // Sections settings.
  $form['sections'] = [
    '#type' => 'details',
    '#title' => t('Sections'),
    '#group' => 'edwt',
    '#weight' => 2,
  ];
  include 'theme-settings/settings-sections.inc';

  // Style settings.
  $form['style'] = [
    '#type'         => 'details',
    '#title'        => t('Style'),
    '#description'  => t('Style colors, sizes etc'),
    '#group' => 'edwt',
    '#weight' => 3,
    '#tree' => TRUE,
  ];

  $themeHandler = \Drupal::service('theme_handler');
  $themePath = $themeHandler->getTheme($themeHandler->getDefault())->getPath();

  $cssFilePath = $themePath . '/css/vars.css';

  // Rad variables file.
  $content = file_get_contents($cssFilePath);

  $ca = new FrontendCssArray();
  $b = $ca->convert($content);

  $form['style']['general'] = [
    '#type' => 'details',
    '#title' => t('General'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#tree' => TRUE,
  ];

  $form['style']['colors'] = [
    '#type' => 'details',
    '#title' => t('Colors'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#tree' => TRUE,
  ];

  $rows = [];
  foreach ($b[':root'] as $k => $v) {
    $var_value = trim(str_replace("'", "", $v));

    if (strpos($var_value, '#') === 0) {
      $color = [
        'data' => [
          '#type' => 'color',
          '#value' => $v,
        ],
      ];
      $rows['colors'][] = [$k, $v, $color];
    }
    else {
      $rows['default'][] = [$k, $v];
    }
  }

  $form['style']['general']['table'] = [
    '#type' => 'table',
    '#rows' => $rows['default'],
    '#header' => [
      t('Name'),
      t('Value'),
    ],
  ];

  $form['style']['colors']['table'] = [
    '#type' => 'table',
    '#rows' => $rows['colors'],
    '#header' => [
      t('Name'),
      t('Value'),
      t('Color'),
    ],
  ];
}
