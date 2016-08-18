<?php
/**
 * @file
 * Contains Drupal\lti_tool_provider\Form\ContactSettingsForm.
 */

namespace Drupal\lti_tool_provider\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConsumerSettingsForm.
 * @package Drupal\lti_tool_provider\Form
 * @ingroup lti_tool_provider
 */
class ConsumerSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'lti_tool_provider_settings';
  }

  /**
   * Form submission handler.
   *
   * @param FormStateInterface $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   An associative array containing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }


  /**
   * Define the form used for LTIToolProvider settings.
   * @return array
   *   Form definition array.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['consumer_settings']['#markup'] = 'Settings form for ContentEntityExample. Manage field settings here.';
    return $form;
  }
}
