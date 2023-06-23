<?php

namespace Drupal\edw_blocks\Plugin\Block;

use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Hero banner' Block.
 *
 * To function properly, this block needs the following:
 *  - field_banner_text on node.
 *  - field_banner_image media field on node.
 *  - european_date date format.
 *
 * @Block(
 *   id = "edw_hero_banner",
 *   admin_label = @Translation("Hero banner"),
 *   category = @Translation("EDW"),
 * )
 */
class EdwHeroBannerBlock extends EdwBlockBase {

  /**
   * The breadcrumb manager.
   *
   * @var \Drupal\Core\Breadcrumb\BreadcrumbManager
   */
  protected $breadcrumb;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->breadcrumb = $container->get('breadcrumb');
    $instance->dateFormatter = $container->get('date.formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $image = NULL;
    $breadcrumb = $this->breadcrumb->build($this->routeMatch)->toRenderable();

    $node = $this->routeMatch->getParameter('node');
    if (!$node instanceof NodeInterface) {
      return [];
    }

    if ($node->hasField('field_banner_text')) {
      $summary = $node->get('field_banner_text')->value;
    }
    if (empty($summary) && $node->hasField('body')) {
      $summary = $node->get('body')->summary;
    }

    $title = $node->get('title')->value;
    if (!$node->hasField('field_banner_image') || $node->get('field_banner_image')->isEmpty()) {
      return [];
    }

    /**
     * @var \Drupal\media\MediaInterface $media
     */
    $media = $node->get('field_banner_image')->entity;
    if ($media instanceof MediaInterface) {
      $image = $node->get('field_banner_image')->view([
        'type' => 'media_responsive_thumbnail',
        'label' => 'hidden',
        'settings' => [
          'responsive_image_style' => 'hero_banner',
        ],
      ]);
    }

    return [
      '#theme' => 'edw_hero_banner_block',
      '#summary' => $summary,
      '#title' => $title,
      '#breadcrumb' => $breadcrumb,
      '#image' => $image,
      '#date' => $this->dateFormatter->format($node->getCreatedTime(), 'european_date'),
      '#bundle' => $node->bundle(),
      '#attached' => [
        'library' => [
          'edw_blocks/hero_banner',
        ],
      ],
    ];
  }

}
