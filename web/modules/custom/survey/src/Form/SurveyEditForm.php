<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for editing survey details.
 */
class SurveyEditForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'survey_edit_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $survey_id = NULL) {
        $connection = \Drupal::database();
        $survey = $connection->select('survey', 's')
            ->fields('s', ['id', 'name', 'status'])
            ->condition('id', $survey_id)
            ->execute()
            ->fetchObject();
    
        if (!$survey) {
            $this->messenger()->addMessage($this->t('Survey not found'), 'error');
            return [];
        }
    
        $form['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Survey Name'),
            '#default_value' => $survey->name,
            '#required' => TRUE,
        ];
    
        $form['status'] = [
            '#type' => 'select',
            '#title' => $this->t('Status'),
            '#options' => [
                0 => $this->t('Draft'),
                1 => $this->t('Published'),
            ],
            '#default_value' => $survey->status,
        ];
        $form['actions'] = [
            '#type' => 'actions',
            ];
            $form['actions']['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Save Survey'),
            ];
            
    
        return $form;
    }
    

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $survey_id = \Drupal::routeMatch()->getParameter('survey_id');
        $name = $form_state->getValue('name');
        $status = $form_state->getValue('status');
    
        $connection = \Drupal::database();
        $connection->update('survey')
            ->fields(['name' => $name, 'status' => $status])
            ->condition('id', $survey_id)
            ->execute();
    
        $this->messenger()->addMessage($this->t('Survey updated successfully.'));
    }
    
}
