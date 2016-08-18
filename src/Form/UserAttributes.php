<?php

/**
 * @file
 * Contains \Drupal\lti_tool_provider\Form\UserAttributes.
 */

namespace Drupal\lti_tool_provider\Form;

use Drupal\Component\Utility\String;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form that displays all the config variables to edit them.
 */
class UserAttributes extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'lti_tool_provider_user_attributes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $filter = '') {

    $form['description'] = array(
      '#type' => 'item',
      '#title' => t('LTI Context to User Attribute Mapping'),
      '#description' => 'Choose the user attributes to be mapped from each LTI context variable.',
    );

    $fields_array = _lti_tool_provider_retrieve_user_field_types('TEXT');
    $lis_list = lti_tool_provider_user_mapping_lis_details();
    //$saved_settings = variable_get('lti_tool_provider_user_attribute_mapping', array());
    $saved_settings = array(
      'lis_person_name_given' => 'none',
      'lis_person_name_family' => 'none',
      'lis_person_name_full' => 'none',
    );


    $form['mapping'] = array(
      '#tree' => TRUE,
      '#theme' => 'table',
      '#header' => array(t('LTI Context Variable'), t('User Attribute')),
      '#rows' => array(),
    );

    foreach ($lis_list as $detail) {
      $variable = array(
        '#type' => 'item',
        '#title' => String::checkPlain($detail),
      );
      $value = $saved_settings[$detail];
      $attribute = array(
        '#type' => 'select',
        '#options' => $fields_array,
        '#default_value' => $value,
      );
      $form['mapping'][] = array(
        'variable' => &$variable,
        'attribute' => &$attribute,
      );
      $form['mapping']['#rows'][] = array(
        array('data' => &$variable),
        array('data' => &$attribute),
      );
      unset($variable);
      unset($attribute);
    }
    
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save User Attributes'),
    );
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // $check = TRUE;
    // $lis_list = lti_tool_provider_user_mapping_lis_details();
    // $field_options = _lti_tool_provider_retrieve_user_field_types('TEXT');
    // $counters = array();
    // foreach ($field_options as $field => $desc) {
    //   $counters[$field] = 0;
    // }
    // foreach ($lis_list as $key => $variable) {
    //   $counters[$form_state['values']['mapping'][$key]['attribute']]++;
    // }
    // foreach ($field_options as $field => $desc) {
    //   if ($field != 'none' && $counters[$field] > 1) {
    //     $check = FALSE;
    //     break;
    //   }
    // }
    // if (!$check) {
    //   form_set_error('mapping', t('You may not map multiple values to the same attribute.'));
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // $lis_list = lti_tool_provider_user_mapping_lis_details();
    // $settings = array();
    // foreach ($lis_list as $key => $lis) {
    //   $setting[$lis] = $form_state['values']['mapping'][$key]['attribute'];
    // }
    // variable_set('lti_tool_provider_user_attribute_mapping', $setting);
    // drupal_set_message(t('User attribute mapping saved.'));
  }

}
