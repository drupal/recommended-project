<?php

namespace Drupal\edw_blocks\Plugin\Block;

use Drupal\Component\Plugin\Context\Context;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\views\Plugin\Block\ViewsBlock;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Block display plugin for Search.
 *
 * Allows for greater control over Views block settings.
 */
class EdwSearchBlock extends ViewsBlock {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $contentTypes = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    $contentTypeOptions = [];
    foreach ($contentTypes as $contentType) {
      $contentTypeOptions[$contentType->id()] = $contentType->label();
    }

    $form['content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Content types'),
      '#options' => $contentTypeOptions,
      '#default_value' => $this->configuration['content_types'] ?? [],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['content_types'] = $form_state->getValue('content_types');
    parent::blockSubmit($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content_types = 'all';
    if (!empty($this->configuration['content_types'])) {
      $content_types = implode('+', $this->configuration['content_types']);
    }

    $this->context['type'] = new Context(new ContextDefinition(), $content_types);
    return parent::build();
  }

}
