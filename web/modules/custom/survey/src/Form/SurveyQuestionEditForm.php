<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Form for editing a survey question.
 */
class SurveyQuestionEditForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'survey_question_edit_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $question_id = NULL) {
        $connection = Database::getConnection();
        $question = $connection->select('survey_questions', 'q')
        ->fields('q', ['id', 'survey_id', 'label', 'machine_name', 'type', 'options'])
        ->condition('id', $question_id)
        ->execute()
        ->fetchObject();

        if (!$question) {
        return ['#markup' => $this->t('Question not found.')];
        }

        $form['question_id'] = [
        '#type' => 'hidden',
        '#value' => $question->id,
        ];

        $form['survey_id'] = [
        '#type' => 'hidden',
        '#value' => $question->survey_id,
        ];

        $form['label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Question Label'),
        '#default_value' => $question->label,
        '#required' => TRUE,
        ];

        $form['machine_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Machine Name'),
        '#default_value' => $question->machine_name,
        '#required' => TRUE,
        ];

        $form['type'] = [
        '#type' => 'select',
        '#title' => $this->t('Question Type'),
        '#options' => [
            'text' => $this->t('Text'),
            'number' => $this->t('Number'),
            'email' => $this->t('Email'),
            'textarea' => $this->t('Text Area'),
            'dropdown' => $this->t('Dropdown'),
            'radio' => $this->t('Radio'),
            'checkbox' => $this->t('Checkbox'),
        ],
        '#default_value' => $question->type,
        '#required' => TRUE,
        '#ajax' => [
            'callback' => '::updateOptionsField',
            'wrapper' => 'options-wrapper',
        ],
        ];

        // إضافة '#tree' => TRUE للحاوية لضمان ترقيم القيم بشكل متداخل.
        $form['options_wrapper'] = [
        '#type' => 'container',
        '#tree' => TRUE,
        '#attributes' => ['id' => 'options-wrapper'],
        ];

        $form['options_wrapper']['options'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Options (comma-separated)'),
        '#description' => $this->t('Enter options for Dropdown, Radio, or Checkbox. Separate each option with a comma.'),
        '#default_value' => $question->options ? implode(',', unserialize($question->options)) : '',
        '#states' => [
            'visible' => [
            ':input[name="type"]' => [
                ['value' => 'dropdown'],
                ['value' => 'radio'],
                ['value' => 'checkbox'],
            ],
            ],
        ],
        ];

        $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Update Question'),
        ];

        return $form;
    }

    /**
     * AJAX callback to update the options field visibility.
     */
    public function updateOptionsField(array &$form, FormStateInterface $form_state) {
        return $form['options_wrapper'];
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();

        // معالجة حقل الخيارات: التحقق من النوع ثم قراءة الحقل وتنسيقه.
        $options = NULL;
        if (in_array($values['type'], ['dropdown', 'radio', 'checkbox'])) {
        $options_input = isset($values['options_wrapper']['options']) ? trim($values['options_wrapper']['options']) : '';
        if (!empty($options_input)) {
            $options_array = array_filter(array_map('trim', explode(',', $options_input)));
            if (!empty($options_array)) {
            $options = serialize($options_array);
            }
        }
        }

        $connection = Database::getConnection();
        $connection->update('survey_questions')
        ->fields([
            'label' => $values['label'],
            'machine_name' => $values['machine_name'],
            'type' => $values['type'],
            'options' => $options,
        ])
        ->condition('id', $values['question_id'])
        ->execute();

        $this->messenger()->addMessage($this->t('Question updated successfully!'));
        $form_state->setRedirect('survey.detail', ['survey_id' => $values['survey_id']]);
    }
}
