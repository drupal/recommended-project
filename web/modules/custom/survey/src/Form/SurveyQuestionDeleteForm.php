<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Form for deleting a survey question.
 */
class SurveyQuestionDeleteForm extends ConfirmFormBase {

    protected $questionId;
    protected $surveyId;

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'survey_question_delete_form';
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestionId() {
        return $this->questionId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $question_id = NULL) {
        $this->questionId = $question_id;
        $connection = Database::getConnection();
        $question = $connection->select('survey_questions', 'q')
        ->fields('q', ['id', 'survey_id', 'label'])
        ->condition('id', $question_id)
        ->execute()
        ->fetchObject();

        if (!$question) {
        return ['#markup' => $this->t('Question not found.')];
        }

        $this->surveyId = $question->survey_id;

        $form['question_id'] = [
        '#type' => 'hidden',
        '#value' => $this->questionId,
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestion() {
        return $this->t('Are you sure you want to delete this question?');
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelUrl() {
        return new Url('survey.detail', ['survey_id' => $this->surveyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $connection = Database::getConnection();
        $connection->delete('survey_questions')
        ->condition('id', $this->questionId)
        ->execute();

        $this->messenger()->addMessage($this->t('Question deleted successfully!'));

        $form_state->setRedirect('survey.detail', ['survey_id' => $this->surveyId]);
    }
}
