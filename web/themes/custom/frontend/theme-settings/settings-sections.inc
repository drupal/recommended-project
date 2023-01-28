<?php
/**
 * @file
 * Eau de Web Theme theme file.
 */

$form['sections']['navbar'] = [
  '#type' => 'details',
  '#title' => t('Nav bar'),
  '#collapsible' => TRUE,
  '#collapsed' => TRUE,
];

$form['sections']['navbar']['ns__navbar_position'] = [
  '#type' => 'select',
  '#title' => t('Header position'),
  '#default_value' => theme_get_setting('ns__navbar_position'),
  '#description' => t('Header position'),
  '#options' => [
    '0' => 'Default',
    'sticky-top' => 'Sticky top',
    'fixed-top' => 'Fixed top',
    'fixed-bottom' => 'Fixed bottom',
  ],
];

$form['sections']['navbar']['ns__navbar_expand'] = [
  '#type' => 'select',
  '#title' => t('Expand breakpoint'),
  '#default_value' => theme_get_setting('ns__navbar_expand'),
  '#description' => t('Select breakpoint.'),
  '#options' => [
    'navbar-expand-sm' => 'Expand Extra small screen',
    'navbar-expand-sm' => 'Expand Small screen',
    'navbar-expand-md' => 'Expand Medium screen',
    'navbar-expand-lg' => 'Expand Large screen',
    'navbar-expand-xl' => 'Expand Extra large screen',
    'navbar-expand-xxl' => 'Expand Extra extra large screen',
  ],
];

$form['sections']['navbar']['ns__navbar_align'] = [
  '#type' => 'select',
  '#title' => t('Navigation align'),
  '#default_value' => theme_get_setting('ns__navbar_align'),
  '#description' => t(''),
  '#options' => [
    'me-auto' => 'Align left',
    'mx-auto' => 'Align center',
    'ms-auto' => 'Align right'
  ],
];

$form['sections']['navbar']['ns__navbar_color'] = [
  '#type' => 'select',
  '#title' => t('Text color'),
  '#default_value' => theme_get_setting('ns__navbar_color'),
  '#description' => t(''),
  '#options' => [
    'navbar-light' => 'Text color dark',
    'navbar-dark' => 'Text color light'
  ],
];

$form['sections']['navbar']['ns__navbar_bg'] = [
  '#type' => 'select',
  '#title' => t('Background color'),
  '#default_value' => theme_get_setting('ns__navbar_bg'),
  '#description' => t(''),
  '#options' => [
    '0' => 'None',
    'bg-white' => 'Background White',
    'bg-primary' => 'Background Primary',
    'bg-dark' => 'Background Dark',
  ],
];

$form['sections']['navbar']['ns__navbar_out'] = [
  '#type' => 'select',
  '#title' => t('Delimiter'),
  '#default_value' => theme_get_setting('ns__navbar_out'),
  '#description' => t(''),
  '#options' => [
    '0' => 'None',
    'shadow-sm' => 'Small shadow',
    'shadow' => 'Regular shadow',
    'shadow-lg' => 'Larger shadow',
    'border-bottom' => 'Border bottom',
  ],
];
