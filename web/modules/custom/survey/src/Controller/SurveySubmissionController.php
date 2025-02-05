<?php

namespace Drupal\survey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Controller for displaying survey submissions.
 */
class SurveySubmissionController extends ControllerBase {

    /**
     * Displays a table of survey submissions.
     *
     * If a survey ID is passed as a query parameter, only submissions for that
     * survey will be displayed.
     *
     * @return array
     *   A render array representing the submissions table.
     */
    public function listSubmissions() {
        $survey_id = \Drupal::request()->query->get('survey_id');

        $query = Database::getConnection()->select('survey_submission', 'ss');
        $query->fields('ss');
        if (!empty($survey_id)) {
        $query->condition('survey_id', $survey_id);
        }
        $results = $query->execute()->fetchAll();

        $header = [
        $this->t('ID'),
        $this->t('Survey ID'),
        $this->t('User ID'),
        $this->t('Created'),
        $this->t('Payload'),
        ];

        $rows = [];
        $date_formatter = \Drupal::service('date.formatter');
        foreach ($results as $record) {
        if (is_numeric($record->created)) {
            $formatted_date = $date_formatter->format($record->created, 'short');
        } else {
            $formatted_date = $this->t('Invalid date');
        }

        $rows[] = [
            $record->id,
            $record->survey_id,
            $record->uid,
            $formatted_date,
            $record->payload,
        ];
        }

        $build = [
        'submissions_table' => [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#empty' => $this->t('No submissions found.'),
        ],
        ];

        
        $build['search_link'] = [
        '#type' => 'link',
        '#title' => $this->t('Search submissions by survey'),
        '#url' => Url::fromRoute('survey.submission_search_form'),
        '#attributes' => ['class' => ['button']],
        ];

        return $build;
    }

}
