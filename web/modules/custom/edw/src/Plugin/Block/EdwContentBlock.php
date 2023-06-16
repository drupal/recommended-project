<?php

namespace Drupal\edw\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;

/**
 * Provides a 'EDW Content' Block.
 *
 * @Block(
 *   id = "edw_content",
 *   admin_label = @Translation("EDW content"),
 *   category = @Translation("EDW"),
 * )
 */
class EdwContentBlock extends EdwBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $view = Views::getView('content_listing');
    $view->setDisplay('block_1');

    if (is_array($config['content_types'])) {
      $contentTypesList = implode('+', $config['content_types']);
    } else {
      $contentTypesList = $config['content_type'];
    }
    // Set arguments based on the selected content types.
    $view->setArguments([$contentTypesList]);

    // Set the number of items per page / total number of results
    if (!empty($config['number_of_results'])) {
      $view->display_handler->options['pager']['type'] = 'some';
      $numberOfItemsPerPage = intval($config['number_of_results']);
      $view->setItemsPerPage($numberOfItemsPerPage);
    } else {
      $view->display_handler->options['pager']['type'] = 'full';
    }

    // If Display mode is list, remove col-md-4 class
    if ($config['display_mode'] == 'list') {
      $style = $view->display_handler->getOption('style');
      $style['options']['col_class_custom'] = FALSE;
      $view->getDisplay()->setOption('style', $style);
    }
    $footer = $view->display_handler->getOption('footer');
    $footer['area_text_custom']['content'] = '<a href=' . $config['see_more_link'] . '>' . $config['see_more_title'] . '</a>';
    $view->display_handler->setOption('footer', $footer);

    // Change display mode based on display mode.
    $rowOptions = $view->display_handler->getOption('row');
    $rowOptions['options']['view_mode'] = $config['view_mode'];
    $view->getDisplay()->setOption('row', $rowOptions);

    // Remove the promoted to frontpage filter.
    unset($view->getDisplay()->getHandlers('filter')['promote']);

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

    $form['display_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Display mode'),
      '#options' => [
        'grid' => $this->t('Grid'),
        'list' => $this->t('List'),
      ],
      '#default_value' => $config['display_mode'] ?? 'grid',
    ];

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
    $this->configuration['content_types'] = array_filter($values['content_types']);
    $this->configuration['display_frontpage_promoted_items'] = $values['display_frontpage_promoted_items'];
  }
}