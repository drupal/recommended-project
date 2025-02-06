<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class SurveySubmissionSearchForm extends FormBase {
  public function getFormId() {
    return 'survey_submission_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [];
    $connection = Database::getConnection();
    $query = $connection->select('survey', 's')
      ->fields('s', ['id', 'slug']);
    $result = $query->execute();

    $count_query = $query->countQuery();
    $count_result = $count_query->execute()->fetchField();

    \Drupal::logger('survey')->notice('استعلام الاستطلاعات: ' . $query->__toString());
    \Drupal::logger('survey')->notice('عدد النتائج: ' . $count_result);

    foreach ($result as $record) {
      $options[$record->id] = $record->title;
    }

    $form['  _id'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Survey'),
      '#options' => ['' => $this->t('- All -')] + $options,
      '#default_value' =>[ \Drupal::request()->query->get('id')]+ $options,
      '#empty_value' => '',
      '#empty_option' => $this->t('- All -'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];
// dd($form);

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $survey_id = $form_state->getValue('id');
    $form_state->setRedirect('survey.submissions', [], ['query' => ['id' => $survey_id]]);
  }
}
