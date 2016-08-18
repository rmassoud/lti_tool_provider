<?php

/**
 * @file
 * Contains \Drupal\lti_tool_provider\ConsumerAccessControlHandler.
 */

namespace Drupal\lti_tool_provider;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the comment entity.
 *
 * @see \Drupal\comment\Entity\Comment.
 */
class ConsumerAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   *
   * Link the activities to the permissions. checkAccess is called with the
   * $operation as defined in the routing.yml file.
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'access lti_tool_provider_consumer entities');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'administer lti_tool_provider module');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'administer lti_tool_provider module');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   *
   * Separate from the checkAccess because the entity does not yet exist, it
   * will be created during the 'add' process.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'administer lti_tool_provider module');
  }

}
