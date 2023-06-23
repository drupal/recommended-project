<?php

namespace Drupal\edw_blocks\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'EDW Content' Block.
 *
 * This block requires the 'content_listing' to function properly.
 *
 * @Block(
 *   id = "edw_content",
 *   admin_label = @Translation("EDW content"),
 *   category = @Translation("EDW"),
 * )
 */
class EdwContentBlock extends EdwBlockBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $view = Views::getView('content_listing');
    if (empty($view)) {
      return [];
    }

    $view->setDisplay('block_generic_listing');

    $contentTypesList = !empty($config['content_types'])
      ? implode('+', $config['content_types'])
      : 'all';
    // Set arguments based on the selected content types.
    $view->setArguments([$contentTypesList]);

    $pager = $view->display_handler->getOption('pager');

    // Set the number of items per page / total number of results.
    $view->display_handler->options['pager']['type'] = 'full';
    $pager['options']['items_per_page'] = $config['items_per_page'];
    if (!empty($config['number_of_results']) || $config['display_mode'] == 'slick') {
      $pager['type'] = 'some';
      $pager['options']['items_per_page'] = $config['number_of_results'];
    }
    $view->display_handler->setOption('pager', $pager);

    // If Display mode is list, remove col-md-4 class.
    if ($config['display_mode'] == 'list') {
      $style = $view->getDisplay()->getOption('style');
      $style['options']['col_class_custom'] = FALSE;
      $view->getDisplay()->setOption('style', $style);
    }
    elseif ($config['display_mode'] == 'slick') {
      $style = $view->getDisplay()->getOption('style');
      $style['type'] = 'slick';
      $style['options'] = [
        'caption' => ['title' => 'title'],
        'optionset' => 'carousel',
        'skin' => 'default',
        'style' => 'grid',
      ];
      $view->getDisplay()->setOption('style', $style);
    }

    $footer = $view->display_handler->getOption('footer');
    $footer['area_text_custom']['content'] = '<a href="' . $config['see_more_link'] . '">' . $config['see_more_title'] . '</a>';
    $view->display_handler->setOption('footer', $footer);

    // Change display mode based on display mode.
    $rowOptions = $view->display_handler->getOption('row');
    $rowOptions['options']['view_mode'] = $config['view_mode'];
    $view->getDisplay()->setOption('row', $rowOptions);

    // Remove the promoted to frontpage filter.
    if (empty($config['display_frontpage_promoted_items'])) {
      unset($view->getDisplay()->getHandlers('filter')['promote']);
    }

    $view->execute();

    return [
      '#type' => 'view',
      '#view' => $view,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $contentTypes = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    $contentTypeOptions = [];
    foreach ($contentTypes as $contentType) {
      $contentTypeOptions[$contentType->id()] = $contentType->label();
    }

    $form['content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Content types'),
      '#options' => $contentTypeOptions,
      '#default_value' => $config['content_types'] ?? [],
    ];

    $form['display_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Display mode'),
      '#options' => [
        'grid' => $this->t('Grid'),
        'list' => $this->t('List'),
      ],
      '#default_value' => $config['display_mode'] ?? 'grid',
    ];
    if ($this->moduleHandler->moduleExists('slick_views')) {
      $form['display_mode']['#options']['slick'] = $this->t('Slick slideshow');
    }

    $form['view_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('View mode'),
      '#options' => [
        'teaser' => $this->t('Teaser'),
        'compact_teaser' => $this->t('Compact teaser'),
      ],
      '#default_value' => $config['view_mode'] ?? 'teaser',
    ];

    $form['see_more_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('See more link'),
      '#default_value' => $config['see_more_link'] ?? '',
    ];

    $form['see_more_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('See more title'),
      '#default_value' => $config['see_more_title'] ?? '',
    ];

    $form['number_of_results'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of results'),
      '#default_value' => $config['number_of_results'] ?? 10,
    ];

    $form['items_per_page'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items per page'),
      '#default_value' => $config['items_per_page'] ?? '',
      '#states' => [
        'invisible' => [
          ':input[name="settings[display_mode]"]' => ['value' => 'slick'],
        ],
      ],
    ];

    $form['display_frontpage_promoted_items'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display only frontpage promoted items'),
      '#default_value' => $config['display_frontpage_promoted_items'] ?? FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['display_mode'] = $values['display_mode'];
    $this->configuration['view_mode'] = $values['view_mode'];
    $this->configuration['see_more_link'] = $values['see_more_link'];
    $this->configuration['see_more_title'] = $values['see_more_title'];
    $this->configuration['number_of_results'] = $values['number_of_results'];
    $this->configuration['items_per_page'] = $values['items_per_page'];
    $this->configuration['content_types'] = array_filter($values['content_types']);
    $this->configuration['display_frontpage_promoted_items'] = $values['display_frontpage_promoted_items'];
  }

}
