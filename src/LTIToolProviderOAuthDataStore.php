<?php

/**
 * @file
 * Contains LTIToolProviderOAuthDataStore.
 */

namespace Drupal\lti_tool_provider;

use Drupal\lti_tool_provider\OAuth\OAuthDataStore;
use Drupal\lti_tool_provider\OAuth\OAuthConsumer;
use Drupal\lti_tool_provider\OAuth\OAuthToken;

class LTIToolProviderOAuthDataStore extends OAuthDataStore {

  /**
   * Find a consumer matching a key.
   *
   * @param string $consumer_key
   *   A consumer key to lookup.
   *
   * @return OAuthConsumer
   *   The corresponding consumer entity.
   */
  function lookup_consumer($consumer_key) {
    $query = \Drupal::entityQuery('lti_tool_provider_consumer');
    $result = $query
    ->condition('lti_tool_provider_consumer_key', $consumer_key, '=')
    ->addMetaData('account', user_load(1))
    ->execute();
    if ( $result ) {
      $consumer_ids = array_keys($result);
      $consumers = entity_load_multiple('lti_tool_provider_consumer', $consumer_ids);
      $consumer_entity = reset($consumers);
      $consumer = new OAuthConsumer($consumer_key, $consumer_entity->lti_tool_provider_consumer_secret->value, NULL);
    }
    else {
      $consumer = new OAuthConsumer($consumer_key, NULL, NULL);
    }
    return $consumer;
  }

  /**
   * Lookup a token.
   *
   * @param object $consumer
   *   The consumer entity.
   * @param string $token_type
   *   The type of token.
   * @param object $token
   *   The token.
   *
   * @return OAuthToken
   *   The token.
   */
  function lookup_token($consumer, $token_type, $token) {
    return new OAuthToken($consumer, '');
  }

  /**
   * Lookup a nonce (not implemented).
   *
   * @param object $consumer
   *   The consumer entity.
   * @param object $token
   *   A token.
   * @param string $nonce
   *   A nonce.
   * @param integer $timestamp
   *   A timestamp.
   *
   * @return NULL
   *   Always null.
   */
  function lookup_nonce($consumer, $token, $nonce, $timestamp) {
    return NULL;
  }

  /**
   * Get a new request token (not implemented).
   *
   * @param object $consumer
   *   The consumer entity.
   * @param string $callback
   *   A callback.
   *
   * @return NULL
   *   Always null.
   */
  function new_request_token($consumer, $callback = NULL) {
    // Return a new token attached to this consumer.
    return NULL;
  }

  /**
   * Get a new access token (not implemented).
   *
   * @param object $token
   *   A token.
   * @param object $consumer
   *   A consumer entity.
   * @param string $verifier
   *   The verifier.
   *
   * @return NULL
   *   Always null.
   */
  function new_access_token($token, $consumer, $verifier = NULL) {
    // Return a new access token attached to this consumer
    // for the user associated with this token if the request token
    // is authorized.
    // Should also invalidate the request token.
    return NULL;
  }
}
