<?php

namespace Drupal\survey\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Form for adding a question to a survey.
 */
class SurveyQuestionForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'survey_question_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $survey_id = NULL) {
        $form['survey_id'] = [
            '#type' => 'hidden',
            '#value' => $survey_id,
        ];

        $form['label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Question Label'),
            '#required' => TRUE,
        ];

        $form['machine_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Machine Name'),
            '#required' => TRUE,
            '#description' => $this->t('A unique identifier for this question.'),
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
            '#required' => TRUE,
            '#ajax' => [
                'callback' => '::updateOptionsField',
                'wrapper' => 'options-wrapper',
            ],
        ];

        $form['options_wrapper'] = [
            '#type' => 'container',
            '#tree' => TRUE,
            '#attributes' => ['id' => 'options-wrapper'],
        ];

        $form['options_wrapper']['options'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Options (comma-separated)'),
            '#description' => $this->t('Enter options for Dropdown, Radio, or Checkbox. Separate each option with a comma.'),
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
            '#value' => $this->t('Save Question'),
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
    
        // قراءة الخيارات المدخلة
        $options = NULL;
        if (in_array($values['type'], ['dropdown', 'radio', 'checkbox'])) {
            // التحقق من وجود القيمة في options
            $options_input = isset($values['options_wrapper']['options']) ? trim($values['options_wrapper']['options']) : '';
    
            // التحقق من أن القيمة غير فارغة
            if (!empty($options_input)) {
                // تقسيم الخيارات على أساس الفاصلة وتحويلها إلى مصفوفة بعد تنظيف القيم
                $options_array = array_filter(array_map('trim', explode(',', $options_input)));
    
                // تأكد من أن المصفوفة غير فارغة
                if (!empty($options_array)) {
                    // تحويل المصفوفة إلى سلسلة `serialized`
                    $options = serialize($options_array);
                }
            }
        }
    
        // قم باضافة رسالة سجل للتحقق من القيم المدخلة
        \Drupal::logger('survey')->notice('<pre>' . print_r($options, TRUE) . '</pre>');
    
        // إذا كانت القيمة NULL، يتم التحقق من أنها فارغة قبل إدخالها في قاعدة البيانات
        if ($options === NULL) {
            \Drupal::logger('survey')->notice('No options to save.');
        }
    
        // حفظ البيانات في قاعدة البيانات
        $connection = Database::getConnection();
        $connection->insert('survey_questions')
            ->fields([
                'survey_id' => $values['survey_id'],
                'label' => $values['label'],
                'machine_name' => $values['machine_name'],
                'type' => $values['type'],
                'options' => $options, // تخزين الخيار كـ serialized
            ])
            ->execute();
    
        // إضافة رسالة نجاح
        $this->messenger()->addMessage($this->t('Question added successfully!'));
        $form_state->setRedirect('survey.detail', ['survey_id' => $values['survey_id']]);
    }
    
    
}
