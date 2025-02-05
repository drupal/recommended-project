<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class SurveySubmissionForm extends FormBase {

    public function getFormId() {
        return 'survey_submission_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
        // إضافة الحقول المطلوبة هنا
        $form['survey_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Survey ID'),
            '#required' => TRUE,
        ];

        $form['user_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('User ID'),
            '#required' => TRUE,
        ];

        $form['submission_data'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Submission Data'),
            '#required' => TRUE,
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();

        // إدخال البيانات في جدول survey_submission
        $connection = Database::getConnection();
        $connection->insert('survey_submission')
            ->fields([
                'survey_id' => $values['survey_id'],
                'user_id' => $values['user_id'],
                'submission_data' => $values['submission_data'],
                // 'submitted_at' => time(),
            ])
            ->execute();

        // عرض رسالة تأكيد
        \Drupal::messenger()->addMessage($this->t('Survey submitted successfully.'));

        // إعادة التوجيه إلى قائمة الاستبيانات
        $url = Url::fromRoute('survey.list')->toString();
        $form_state->setResponse(new RedirectResponse($url));
    }
}
