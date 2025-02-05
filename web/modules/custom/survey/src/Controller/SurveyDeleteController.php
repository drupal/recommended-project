<?php

namespace Drupal\survey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for deleting surveys.
 */
class SurveyDeleteController extends ControllerBase {

    /**
     * Deletes a survey.
     */
    public function delete($survey_id) {
        $connection = Database::getConnection();
        $connection->delete('survey')
            ->condition('id', $survey_id)
            ->execute();

        $this->messenger()->addMessage($this->t('Survey deleted.'));
        return new RedirectResponse(\Drupal\Core\Url::fromRoute('survey.list')->toString());
    }
}
