<?php


namespace Drupal\survey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;

class SubmissionController extends ControllerBase {

  public function listSubmissions() {
    $build = [];
    
    $build['search_form'] = \Drupal::formBuilder()->getForm('Drupal\survey\Form\SurveySubmissionSearchForm');
    
  
    $survey_id = \Drupal::request()->query->get('survey_id');
    $query = \Drupal::database()->select('survey_submission', 'ss')
      ->fields('ss');
    if ($survey_id) {
      $query->condition('survey_id', $survey_id);
    }
    $results = $query->execute()->fetchAll();

    $header = [
      $this->t('ID'),
      $this->t('Survey'),
      $this->t('User'),
      $this->t('Created'),
      $this->t('Payload'),
    ];
    $rows = [];
    foreach ($results as $record) {
      $rows[] = [
        $record->id,
        $record->survey_id,
        $record->uid,
        \Drupal::service('date.formatter')->format($record->created, 'short'),
        $record->payload,
      ];
    }
    $build['submissions_table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No submissions found.'),
    ];

    return $build;
  }
}
