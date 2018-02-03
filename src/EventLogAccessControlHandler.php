<?php

namespace Drupal\event_log;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Event log entity.
 *
 * @see \Drupal\event_log\Entity\EventLog.
 */
class EventLogAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\event_log\Entity\EventLogInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished event log entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published event log entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit event log entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete event log entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add event log entities');
  }

}
