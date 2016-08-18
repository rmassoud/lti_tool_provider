<?php

/**
 * @file
 * Contains \Drupal\lti_tool_provider\Controller\LTIToolProviderController.
 */

namespace Drupal\lti_tool_provider\Controller;

use Drupal\comment\CommentInterface;


use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Drupal\user\Form\UserLoginForm;
use Drupal\user\Controller\UserController;

use Drupal\lti_tool_provider\LTIToolProviderOAuthDataStore;
use Drupal\lti_tool_provider\OAuth\OAuthException;
use Drupal\lti_tool_provider\OAuth\OAuthRequest;
use Drupal\lti_tool_provider\OAuth\OAuthServer;
use Drupal\lti_tool_provider\OAuth\OAuthSignatureMethod_HMAC_SHA1;

use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;

use Drupal\Core\Utility\Token;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Field;
use Drupal\Core\Session\UserSession;
use Drupal\Core\Session\SessionManager;


/**
 * Returns responses for lti_tool_provider module routes.
 */
class LTIToolProviderController {

/**
 * LTI launch.
 *
 * Implements the [base_url]/lti path which is called to launch the
 * tool from the LMS
 *  - Verifies the request using OAuth and adds the lti context_info
 *    to the session
 *  - Authenticates the user, possibly after provisioning the account
 *  - Authorises the user via global role mapping
 *  - If OG is configured a course may be provisioned from the
 *    context_info and group roles are mapped
 *  - Finally the destination is calculated and user is redirected there.
 *
 * @return string
 *   Page content.
 */
function lti_tool_provider_launch() {
  $user = \Drupal::currentUser();
  // If not an LTI launch request, then ignore.
  if (!lti_tool_provider_is_basic_lti_request()) {
    drupal_set_message(t('Not a LTI request.'), 'info');
    return t('Error: Not a LTI request.');
  }
  // Insure we have a valid context.
  if (empty($_REQUEST['oauth_consumer_key'])) {
    unset($_SESSION['lti_tool_provider_context_info']);
    drupal_set_message(t('Not a valid LTI context.'), 'info');
    return t('Error: Invalid context. Missing oauth_consumer_key in request.');
  }
  // Begin a new session based on this LTI launch request.
  //drupal_session_start();
  // SessionManager::start();
  $oauth_consumer_key = $_REQUEST["oauth_consumer_key"];
  // Verify the message signature.
  $store = new LTIToolProviderOAuthDataStore();
  $server = new OAuthServer($store);
  $method = new OAuthSignatureMethod_HMAC_SHA1();
  $server->add_signature_method($method);
  $request = OAuthRequest::from_request();

  try {
    $server->verify_request($request);
  } catch (OAuthException $e) {
    drupal_set_message($e->getMessage(), 'error');
    // return t('Error: Invalid context, OAuth failure.');
    return array('#markup' => t('Error: Invalid context, OAuth failure.'));
  }

  // Collect the launch information for later storage in the session.
  $launch_info = $request->get_parameters();
  if (empty($launch_info['context_id'])) {
    if (isset($launch_info['launch_presentation_return_url'])) {
      lti_tool_provider_goto(\Drupal::url($launch_info['launch_presentation_return_url'], array('query' => array('lti_errormsg' => t('Error: Invalid context, No context Id.')))));
    }
    else {
       drupal_set_message('Error: Invalid context, No context Id.', 'error');
      return array('#markup' => '');
    }
  }
  $consumer = lti_tool_provider_get_consumer_by_key($oauth_consumer_key);
  $launch_info['consumer_id'] = $consumer->lti_tool_provider_consumer_id->value;
  $launch_info['consumer_domain'] = isset($consumer->lti_tool_provider_consumer_domain->value)?$consumer->lti_tool_provider_consumer_domain->value:'';

  if (!empty($launch_info['user_id'])) {
    $lti_user = $launch_info['user_id'] . $launch_info['consumer_domain'];
  }
  else {
    $lti_user = 'lti_user' . $launch_info['consumer_domain'];
  }
  // Revalidate incoming user.
  if ($user->id() > 0 && $user->getUsername() != $lti_user) {
    \Drupal::logger('user')->notice('Session closed for ' . $user->getUsername() . '.');
    user_logout();
    \Drupal::service('session_manager')->start();
    drupal_set_message(t('Logged current user out.'), 'info');
  }

  if ($user->id() == 0) {
    if (empty($launch_info['lis_person_contact_email_primary'])) {
      if ($launch_info['consumer_domain'] == '') {
        $launch_info['lis_person_contact_email_primary'] = $lti_user . '@invalid';
      }
      else {
        $launch_info['lis_person_contact_email_primary'] = $lti_user . '.invalid';
      }
    }
    // Unauthenticated so create user if necessary.
    if (($account = user_load_by_name($lti_user)) || ($account = user_load_by_mail($launch_info['lis_person_contact_email_primary']))) {
      // User exists by name or mail.
      if ($account->id() == 1) {
        // User 1 must use drupal authentication.
        if (isset($launch_info['launch_presentation_return_url'])) {
          // lti_tool_provider_goto(\Drupal::url($launch_info['launch_presentation_return_url'], array('query' => array('lti_errormsg' => t('Admin account must use Drupal authentication.')))));
          drupal_set_message(t('Admin account must use Drupal authentication.'), 'error');
          return array('#markup' => '');
        }
        else {
          drupal_set_message(t('Admin account must use Drupal authentication.'), 'error');
          return array('#markup' => '');
        }
      } else {
        drupal_set_message('not admin.', 'info');
      }
    }
    else {
      if (!$account = lti_tool_provider_create_account($lti_user, $launch_info)) {
        if (isset($launch_info['launch_presentation_return_url'])) {
          // lti_tool_provider_goto(url($launch_info['launch_presentation_return_url'], array('query' => array('lti_errormsg' => t('Account creation failed.')))));        
          drupal_set_message(t('Account creation failed.'), 'error');
          return array('#markup' => '');
        }
        else {
          drupal_set_message(t('Account creation failed.'), 'error');
          return array('#markup' => '');
        }
      }
    }
    user_login_finalize($account);
  }
  else {
     $account = user_load($user->id());
  }
  // $account is the $lti_user.
  // Map Drupal global roles based on the user LTI role.
  if (!empty($launch_info['roles'])) {
    lti_tool_provider_assign_global_roles_to_user($launch_info['roles'], $account);
  }
  $launch_info['destination'] = '';
  // drupal_alter('lti_tool_provider_launch', $launch_info, $account);
  // Calculate the final destination.
  if (!empty($launch_info['custom_destination'])) {
    $launch_info['destination'] .= '/' . $launch_info['custom_destination'];
  }
  // Save launch information in session.
  $_SESSION['lti_tool_provider_context_info'] = $launch_info;
  // Set language in session.
  if (!empty($launch_info['launch_presentation_locale'])) {
    $_SESSION['language'] = lti_tool_provider_strtolower($launch_info['launch_presentation_locale']);
  }

  $search_node_types = \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->get('course_types_array');
  // if(  ) {
  //   drupal_set_message(t('No Drupal Content Types to LTI Course mapping is defined.'), 'error');
  //   return array('#markup' => '');
  // }
  $custom_url = \Drupal::configFactory()->getEditable('lti_tool_provider.settings')->get('custom_url');

  if( !empty($custom_url) && !empty(array_keys($search_node_types)) ) {

    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('title', $launch_info['context_title'], '=')
      ->condition('type', array_keys($search_node_types), 'IN')
    ;
    
    $nids = $query->execute();  
    if (!empty($nids)) {
      $t_node = Node::load(reset($nids));
      $data = array('node' => $t_node, 'user' => $account);
      $token_service = \Drupal::token();
      return new RedirectResponse($token_service->replace($custom_url, $data));
    } else {
      return array('#markup' => 'Package not found!');
      // return new RedirectResponse('/');
    }
  }

  return new RedirectResponse('/');

}

/**
 * Menu page callback for the LTI Info menu items.
 *
 * @return array
 *   The conent for the page.
 */
  public function lti_tool_provider_info() {
    // Display all the key/value pairs in the ltitp_context_info.
    $content = array();
    $content[] = array(
      '#type' => 'item',
      '#markup' => t('LTI Context Session Variables'),
    );
    $info = $_SESSION['lti_tool_provider_context_info'];
    $rows = array();
    $loop_counter = 0;
    foreach ($info as $key => $value) {
      $rows[$loop_counter]['data']['key'] = $key;
      $rows[$loop_counter]['data']['value'] = $value;
      $loop_counter++;
    }
    $content['table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => array(t('Key'), t('Value')),
      '#empty' => t('There are no LTI Context variables.'),
    );
    return $content;
  }

}


