<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;

/**
 * Provides a form to create a new survey.
 */
class SurveyForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'survey_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Survey Name'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => 'survey-name',
      ],
    ];

    $form['slug'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Slug'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => 'survey-slug',
      ],
      '#description' => $this->t('The URL-friendly version of the survey name.'),
    ];

    $form['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => [
        0 => $this->t('Draft'),
        1 => $this->t('Published'),
      ],
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Create Survey'),
    ];

    // إضافة JavaScript لتوليد الـ "Slug" تلقائيًا.
    $form['#attached']['library'][] = 'survey/survey_slug';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $connection = Database::getConnection();
    $connection->insert('survey')
      ->fields([
        'name' => $form_state->getValue('name'),
        'slug' => $form_state->getValue('slug'),
        'status' => $form_state->getValue('status'),
        'creator_id' => \Drupal::currentUser()->id(),
        'created_at' => time(),
        ])
        ->execute();

    \Drupal::messenger()->addMessage($this->t('Survey created successfully.'));

    $url = Url::fromRoute('survey.add')->toString();
    $form_state->setResponse(new RedirectResponse($url));
  }

}
