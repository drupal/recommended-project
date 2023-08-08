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
   * Creates a new EdwSearchBlock instance.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container for dependencies.
   * @param array<string, mixed> $configuration
   *   Configuration values for this plugin.
   * @param string $plugin_id
   *   The plugin's ID.
   * @param mixed $plugin_definition
   *   Details about the plugin from annotations.
   *
   * @return static
   *   A new EdwSearchBlock instance.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  /**
   * Builds the block's configuration form.
   *
   * @param array<string, mixed> $form
   *   The initial form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array<string, mixed>
   *   The modified form array.
   *
   * @throws \Exception
   *    Throws an exception.
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
   * Handles the block's form submission.
   *
   * @param array<string, mixed> $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
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

    $context_definition = new ContextDefinition();
    $context = new Context($context_definition, $content_types);
    $this->setContext('type', $context);
    return parent::build();
  }

}
