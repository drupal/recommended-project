<?php

namespace Drupal\survey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\TypedData\Plugin\DataType\Uri;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for displaying survey details.
 */
class SurveyDetailController extends ControllerBase
{

    /**
     * Display the details of a survey.
     */
    public function surveyDetail($survey_id)
    {
        $connection = Database::getConnection();

        $survey = $connection->select('survey', 's')
            ->fields('s', ['id', 'name', 'status', 'creator_id'])
            ->condition('id', $survey_id)
            ->execute()
            ->fetchObject();

        if (!$survey) {
            return ['#markup' => $this->t('Survey not found.')];
        }

        $header = [
            ['data' => $this->t('Question Label')],
            ['data' => $this->t('Type')],
            ['data' => $this->t('Operations')],
        ];

        $questions_query = $connection->select('survey_questions', 'q')
            ->fields('q', ['id', 'label', 'type'])
            ->condition('survey_id', $survey_id)
            ->execute();

        $rows = [];
        foreach ($questions_query as $question) {
            $edit_url = Url::fromRoute('survey.question_edit', ['question_id' => $question->id])->toString();
            $delete_url = Url::fromRoute('survey.question_delete', ['question_id' => $question->id])->toString();

            $operations = [
                '#type' => 'markup',
                '#markup' => $this->t(
                    '<a href="@edit_url" class="button button--secondary">@edit_title</a> | <a href="@delete_url" class="button button--danger">@delete_title</a>',
                    [
                        '@edit_url' => $edit_url,
                        '@delete_url' => $delete_url,
                        '@edit_title' => $this->t('Edit'),
                        '@delete_title' => $this->t('Delete'),
                    ]
                ),
            ];
            $link = [
                '#type' => 'link',
                '#url' => $edit_url,
                '#title' => 'edit',
            ];
            $rows[] = [
                'data' => [
                    $question->label,
                    ucfirst($question->type),
                    [
                        'data' => [
                            [
                                '#type' => 'link',
                                '#title' => $this->t('Edit'),
                                '#url' => Url::fromRoute('survey.question_edit', ['question_id' => $question->id]),
                            ],
                            [
                                '#type' => 'link',
                                '#title' => $this->t('Delete'),
                                '#url' => Url::fromRoute('survey.question_delete', ['question_id' => $question->id]),
                            ],
                            [
                                '#type' => 'link',
                                '#title' => $this->t('View'),
                                '#url' => Url::fromRoute('survey.question_detail', ['question_id' => $question->id]),
                            ],
                        ],
                    ],
                ],
            ];
        }

        return [
            'survey_details' => [
                '#theme' => 'item_list',
                '#items' => [
                    $this->t('Name: @name', ['@name' => $survey->name]),
                    $this->t('Status: @status', ['@status' => $survey->status ? 'Published' : 'Draft']),
                    $this->t('Created by: @creator', ['@creator' => \Drupal\user\Entity\User::load($survey->creator_id)->getDisplayName()]),
                ],
            ],
            'add_question_link' => [
                '#type' => 'link',
                '#title' => $this->t('Add Question'),
                '#url' => Url::fromRoute('survey.question_add', ['survey_id' => $survey_id]),
                '#attributes' => ['class' => ['button', 'button--primary']],
            ],
            'questions_table' => [
                '#type' => 'table',
                '#header' => $header,
                '#rows' => $rows,
                '#empty' => $this->t('No questions found.'),
            ],
        ];
    }

    public function questionDetail($question_id) {
        $connection = \Drupal::database();
        $question = $connection->select('survey_questions', 'q')
            ->fields('q', ['id', 'survey_id', 'label', 'machine_name', 'type', 'options'])
            ->condition('id', $question_id)
            ->execute()
            ->fetchObject();
    
        if (!$question) {
            return [
                '#markup' => $this->t('No question found with ID @id.', ['@id' => $question_id]),
            ];
        }
    
        $options_list = [];
        if (!empty($question->options)) {
            $options = unserialize($question->options);
            if (is_array($options)) {
                foreach ($options as $option) {
                    $options_list[] = "{$option}, " . ucfirst($option);
                }
            }
        }
    
        $header = [
            ['data' => $this->t('Question Label')],
            ['data' => $this->t('Type')],
            ['data' => $this->t('Options')],
        ];
    
        $rows = [
            [$question->label, $question->type, implode('<br>', $options_list)],
        ];
    
        return [
            'questions_table' => [
                '#type' => 'table',
                '#header' => $header,
                '#rows' => $rows,
                '#empty' => $this->t('No questions found.'),
            ],
        ];
    }
    
    
}