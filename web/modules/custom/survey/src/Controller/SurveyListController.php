<?php

namespace Drupal\survey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for listing surveys.
 */
class SurveyListController extends ControllerBase {

    /**
     * The form builder service.
     *
     * @var \Drupal\Core\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * Constructs a new SurveyListController object.
     *
     * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
     *   The form builder service.
     */
    public function __construct(FormBuilderInterface $form_builder) {
        $this->formBuilder = $form_builder;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static($container->get('form_builder'));
    }

    /**
     * Display the survey list page.
     */
    public function surveyList() {
        $search_form = $this->formBuilder->getForm('Drupal\survey\Form\SurveySearchForm');
        $header = [
            ['data' => $this->t('Survey Name')],
            ['data' => $this->t('Status')],
            ['data' => $this->t('Created By')],
            ['data' => $this->t('Operations')],
        ];
    
        $query = Database::getConnection()->select('survey', 's')
            ->fields('s', ['id', 'name', 'status', 'creator_id'])
            ->execute();
    
        $rows = [];
        foreach ($query as $record) {
            $rows[] = [
                'data' => [
                    $record->name,
                    $record->status ? $this->t('Published') : $this->t('Draft'),
                    \Drupal\user\Entity\User::load($record->creator_id)->getDisplayName(),
                    [
                        'data' => [
                            [
                                '#type' => 'link',
                                '#title' => $this->t('Edit'),
                                '#url' => \Drupal\Core\Url::fromRoute('survey.edit', ['survey_id' => $record->id]),
                            ],
                            [
                                '#type' => 'link',
                                '#title' => $this->t('Delete'),
                                '#url' => \Drupal\Core\Url::fromRoute('survey.delete', ['survey_id' => $record->id]),
                            ],
                            [
                                '#type' => 'link',
                                '#title' => $this->t('View'),
                                '#url' => \Drupal\Core\Url::fromRoute('survey.detail', ['survey_id' => $record->id]),
                            ],
                        ],
                    ],
                ],
            ];
        }
    
        return [
            'search_form' => $search_form,
            'table' => [
                '#type' => 'table',
                '#header' => $header,
                '#rows' => $rows,
                '#empty' => $this->t('No surveys found.'),
            ],
        ];
    }
    

}
