<?php

/**
 * @file
 * Contains \Drupal\lti_tool_provider\Entity\Form\LTIToolProviderConsumerDeleteForm.
 */

namespace Drupal\lti_tool_provider\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a lti_tool_provider entity.
 *
 * @ingroup lti_tool_provider
 */
class ConsumerDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete entity %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   *
   * If the delete command is canceled, return to the lti_tool_provider_consumer list.
   */
  public function getCancelURL() {
    return new Url('lti_tool_provider.consumer_list');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. log() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->delete();

    \Drupal::logger('lti_tool_provider')->notice('@type: deleted %title.',
      array(
        '@type' => $this->entity->bundle(),
        '%title' => $this->entity->label(),
      ));
    $form_state->setRedirect('lti_tool_provider.consumer_list');
  }

}
