<?php

namespace Drupal\lti_tool_provider\OAuth;

/**
 * OAuth PECL extension includes an OAuth Exception class, so we need to wrap
 * the definition of this class in order to avoid a PHP error.
 */
if (!class_exists('OAuthException')) {
  /*
   * Generic exception class
   */
  class OAuthException extends \Exception {
    // pass
  }
}
