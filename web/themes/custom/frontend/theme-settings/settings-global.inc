<?php
/**
 * @file
 * Eau de Web Theme theme file.
 */

use Drupal\Core\Url;
use Drupal\Core\Link;

// Section: Global settings.
$form['settings']['global'] = [
  '#type' => 'details',
  '#title' => t('Global settings'),
  '#collapsible' => TRUE,
  '#collapsed' => TRUE,
];

$options_container = [
  'container' => 'Fixed',
  'container-fluid' => 'Fluid',
];

$form['settings']['global']['sg__container'] = [
  '#type' => 'select',
  '#title' => t('Container type'),
  '#default_value' => theme_get_setting('sg__container'),
  '#description' => t('More details @link.', [
    '@link' => Link::fromTextAndUrl('Containers',
                    Url::fromUri('https://getbootstrap.com/docs/5.0/layout/containers/', [
                      'absolute' => TRUE,
                    ]))->toString(),
  ]),
  '#options' => $options_container,
];

$form['settings']['global']['sg__status_message_type'] = [
  '#type' => 'select',
  '#title' => t('Status message type'),
  '#default_value' => theme_get_setting('sg__status_message_type'),
  '#description' => t('Default message @alert type or @toast.', [
    '@alert' => Link::fromTextAndUrl('Toasts',
                Url::fromUri('https://getbootstrap.com/docs/5.0/components/toasts/', [
                  'absolute' => TRUE,
                ]))->toString(),
    '@toast' => Link::fromTextAndUrl('Alerts',
                Url::fromUri('https://getbootstrap.com/docs/5.0/components/alerts/', [
                  'absolute' => TRUE,
                ]))->toString(),
  ]),
  '#options' => [
    'alerts' => 'Alerts',
    'toast' => 'Toasts',
  ],
];


// Section: Region.
$form['settings']['region'] = [
  '#type' => 'details',
  '#title' => t('Region'),
  '#collapsible' => TRUE,
  '#collapsed' => TRUE,
];

$theme = \Drupal::theme()->getActiveTheme()->getName();
$region_list = system_region_list($theme);

foreach ($region_list as $name => $description) {
  $form['settings']['region'][$name] = [
    '#type' => 'details',
    '#title' => $description,
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  ];

  $form['settings']['region'][$name]['sr__clean_' . $name] = [
    '#type' => 'checkbox',
    '#title' => t('Clean wrapper for @description region', ['@description' => $description]),
    '#default_value' => theme_get_setting('sr__clean_' . $name),
  ];

  $form['settings']['region'][$name]['sr__class_' . $name] = [
    '#type' => 'textfield',
    '#title' => t('Classes for @description region', ['@description' => $description]),
    '#default_value' => theme_get_setting('sr__class_' . $name),
    '#size' => 80,
    '#maxlength' => 160,
    '#states' => [
      'invisible' => [
        ':input[name="sr__clean_' . $name . '"]' => ['checked' => TRUE],
      ],
    ],
  ];

  $form['settings']['region'][$name]['sr__container_' . $name] = [
    '#type' => 'select',
    '#title' => t('Container type'),
    '#default_value' => theme_get_setting('sr__container_' . $name),
    '#states' => [
      'invisible' => [
        ':input[name="sr__clean_' . $name . '"]' => ['checked' => TRUE],
      ],
    ],
    '#options' => [
      'none' => 'None',
      'container' => 'Fixed',
      'container-fluid' => 'Fluid',
    ],
  ];

  $form['settings']['region'][$name]['sr__container_position_' . $name] = [
    '#type' => 'select',
    '#title' => t('Container position'),
    '#default_value' => theme_get_setting('sr__container_position_' . $name),
    '#description' => t('Container position before or after regions main tag <div>'),
    '#states' => [
      'invisible' => [
        ':input[name="sr__container_' . $name . '"]' => ['value' => 'none'],
      ],
      'invisible' => [
        ':input[name="sr__clean_' . $name . '"]' => ['checked' => TRUE],
      ],
    ],
    '#options' => [
      'before' => 'Before',
      'after' => 'After',
    ],
  ];

  $form['settings']['region'][$name]['sr__extra_class_' . $name] = [
    '#type' => 'textfield',
    '#title' => t('Classes after container for @description region', ['@description' => $description]),
    '#description' => t('This classes appear after container what is placed after regions main tag <div>'),
    '#default_value' => theme_get_setting('sr__extra_class_' . $name),
    '#size' => 80,
    '#maxlength' => 160,
    '#states' => [
      'visible' => [
        ':input[name="sr__container_position_' . $name . '"]' => ['value' => 'after'],
      ],
      'invisible' => [
        ':input[name="sr__clean_' . $name . '"]' => ['checked' => TRUE],
      ],
    ],
  ];
}
