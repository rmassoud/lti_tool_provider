<?php
/**
 * @file
 * Contains Drupal\lti_tool_provider\Form\LTIToolProviderConsumerForm.
 */

namespace Drupal\lti_tool_provider\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the lti_tool_provider entity edit forms.
 *
 * @ingroup lti_tool_provider
 */
class ConsumerForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\lti_tool_provider\Entity\Consumer */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->getUntranslated()->language()->getId(),
      '#languages' => Language::STATE_ALL,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('lti_tool_provider.consumer_list');
    $entity = $this->getEntity();
    $entity->save();
  }
}
