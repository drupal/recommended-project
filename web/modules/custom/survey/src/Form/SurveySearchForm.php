<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form for searching surveys.
 */
class SurveySearchForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'survey_search_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['search'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Search by Name'),
        '#default_value' => \Drupal::request()->query->get('search', ''),
        ];

        $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Search'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $search_value = $form_state->getValue('search');
        $form_state->setRedirect('survey.list', [], ['query' => ['search' => $search_value]]);
    }

}
