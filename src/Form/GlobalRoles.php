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

/**
 * Form that displays all the config variables to edit them.
 */
class GlobalRoles extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'lti_tool_provider_global_roles';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $filter = '') {
    $form['description'] = array(
      '#type' => 'item',
      '#title' => t('Map LTI Roles to Global Drupal Roles'),
      '#description' => t('Map each LTI role to a global Drupal role.'),
    );
    $select_roles = lti_tool_provider_user_roles(TRUE, NULL);
    $old_role_array = \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->get('global_role_array');

    $form['roles'] = array(
      '#tree' => TRUE,
      '#theme' => 'table',
      '#header' => array(t('LTI Roles'), t('Global Roles')),
      '#rows' => array(),
    );
    foreach (lti_tool_provider_get_lti_roles() as $role) {
      $lti_role = array(
        '#type' => 'item',
        '#title' => $role,
      );
      $global_role = array(
        '#type' => 'select',
        '#options' => $select_roles,
      );
      if (isset($old_role_array[$role]) && isset($select_roles[$old_role_array[$role]])) {
        $global_role['#default_value'] = $old_role_array[$role];
      }
      else {
        $global_role['#default_value'] = array_search(DRUPAL_AUTHENTICATED_RID, $select_roles);
      }
      $form['roles'][] = array(
        'lti_role' => &$lti_role,
        'global_role' => &$global_role,
      );
      $form['roles']['#rows'][] = array(
        array('data' => &$lti_role),
        array('data' => &$global_role),
      );
      unset($lti_role);
      unset($global_role);
    }
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save Global Roles'),
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
    $settings = array();
    foreach (lti_tool_provider_get_lti_roles() as $key => $role) {
      $settings[$role] = $form_state->getValue('roles')[$key]['global_role'];
    }
    \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->set('global_role_array', $settings)->save();

    drupal_set_message(t('LTI global roles mapping saved.'));
  }

}
