<?php
/**
 * @file
 * Provides a banner block for news and pages.
 *
 * This file defines the banner block used on news and pages. The banner block
 * includes breadcrumbs, a title, a banner image, and a banner image description
 * for the node. It is used to enhance the visual representation of news and pages.
 */


namespace Drupal\edw\Plugin\Block;

use Drupal\iccwc\Form\SearchForm;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Hero banner' Block.
 *
 * @Block(
 *   id = "edw_hero_banner",
 *   admin_label = @Translation("Hero banner"),
 *   category = @Translation("EDW"),
 * )
 */
class HeroBannerBlock extends EdwBlockBase
{

    /**
     * The breadcrumb manager.
     *
     * @var \Drupal\Core\Breadcrumb\BreadcrumbManager
     */
    protected $breadcrumb;

    /**
     * The form builder.
     *
     * @var \Drupal\Core\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * The renderer service.
     *
     * @var \Drupal\Core\Render\RendererInterface
     */
    protected $renderer;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
        $instance->breadcrumb = $container->get('breadcrumb');
        $instance->formBuilder = $container->get('form_builder');
        $instance->renderer = $container->get('renderer');
        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $image = null;

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
            $image = $node->get('field_banner_image')->view(
                [
                'type' => 'media_responsive_thumbnail',
                'label' => 'hidden',
                'settings' => [
                'responsive_image_style' => 'hero_banner',
                ],
                ]
            );
            /**
             * @var \Drupal\file\FileInterface $file
             */
        }

        return [
        '#theme' => 'banner_block',
        '#summary' => $summary,
        '#title' => $title,
        '#breadcrumb' => $breadcrumb,
        '#image' => $image,
        '#date' => $this->dateFormatter->format($node->getCreatedTime(), 'european_date'),
        '#bundle' => $node->bundle(),
        // For some reason, sending this as a renderable array breaks page CSS.
        '#search_form' => $this->renderer->renderRoot($form),
        ];
    }

}
