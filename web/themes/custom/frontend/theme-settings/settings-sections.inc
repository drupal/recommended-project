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
    '0' => t('Default'),
    'sticky-top' => t('Sticky top'),
    'fixed-top' => t('Fixed top'),
    'fixed-bottom' => t('Fixed bottom'),
  ],
];

$form['sections']['navbar']['ns__navbar_expand'] = [
  '#type' => 'select',
  '#title' => t('Expand breakpoint'),
  '#default_value' => theme_get_setting('ns__navbar_expand'),
  '#description' => t('Select breakpoint.'),
  '#options' => [
    'navbar-expand-sm' => t('Expand Extra small screen'),
    'navbar-expand-sm' => t('Expand Small screen'),
    'navbar-expand-md' => t('Expand Medium screen'),
    'navbar-expand-lg' => t('Expand Large screen'),
    'navbar-expand-xl' => t('Expand Extra large screen'),
    'navbar-expand-xxl' => t('Expand Extra extra large screen'),
  ],
];

$form['sections']['navbar']['ns__navbar_align'] = [
  '#type' => 'select',
  '#title' => t('Navigation align'),
  '#default_value' => theme_get_setting('ns__navbar_align'),
  '#description' => '',
  '#options' => [
    'me-auto' => t('Align left'),
    'mx-auto' => t('Align center'),
    'ms-auto' => t('Align right'),
  ],
];

$form['sections']['navbar']['ns__navbar_color'] = [
  '#type' => 'select',
  '#title' => t('Text color'),
  '#default_value' => theme_get_setting('ns__navbar_color'),
  '#description' => '',
  '#options' => [
    'navbar-light' => t('Text color dark'),
    'navbar-dark' => t('Text color light'),
  ],
];

$form['sections']['navbar']['ns__navbar_bg'] = [
  '#type' => 'select',
  '#title' => t('Background color'),
  '#default_value' => theme_get_setting('ns__navbar_bg'),
  '#description' => '',
  '#options' => [
    '0' => t('None'),
    'bg-white' => t('Background White'),
    'bg-primary' => t('Background Primary'),
    'bg-dark' => t('Background Dark'),
  ],
];

$form['sections']['navbar']['ns__navbar_out'] = [
  '#type' => 'select',
  '#title' => t('Delimiter'),
  '#default_value' => theme_get_setting('ns__navbar_out'),
  '#description' => '',
  '#options' => [
    '0' => t('None'),
    'shadow-sm' => t('Small shadow'),
    'shadow' => t('Regular shadow'),
    'shadow-lg' => t('Larger shadow'),
    'border-bottom' => t('Border bottom'),
  ],
];
