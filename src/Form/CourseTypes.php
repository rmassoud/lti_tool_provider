<?php

/**
 * @file
 * Contains \Drupal\lti_tool_provider\Form\GlobalRoles.
 */

namespace Drupal\lti_tool_provider\Form;

use Drupal\Component\Utility\String;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\NodeType;


/**
 * Form that displays all the config variables to edit them.
 */
class CourseTypes extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'lti_tool_provider_course_types';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $filter = '') {
    $form['description'] = array(
      '#type' => 'item',
      '#title' => t('Map Drupal Content Types to LTI Course'),
      '#description' => t('Map each Content Type to LTI Course.'),
    );
    $select_types = array_keys(NodeType::loadMultiple());
    sort($select_types);
    $old_types = \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->get('course_types_array');
    
    $form['types'] = array(
      '#type' => 'checkboxes',
      '#options' => array_combine($select_types, $select_types),
      '#description' => t(''),
      '#default_value' => !empty($old_types)?array_keys($old_types):array(),
    );

    $form['custom_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom URL'),
      '#default_value' => \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->get('custom_url'),
      '#description' => t('User & Node tokens can be used in the URL.'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save Course Types'),
    );
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  // public function validateForm(array &$form, FormStateInterface $form_state) {

  // }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $types = array_filter($form_state->getValues()['types']);
    $custom_url = $form_state->getValues()['custom_url'];

    \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->set('course_types_array', $types)->save();
    \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->set('custom_url', $custom_url)->save();

    drupal_set_message(t('Course types mapping saved.'));
  }

}
