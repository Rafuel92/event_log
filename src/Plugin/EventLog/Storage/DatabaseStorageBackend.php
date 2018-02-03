<?php

namespace Drupal\event_log\Plugin\EventLog\Storage;

use Drupal\Core\Annotation\Translation;
use Drupal\event_log\Annotation\StorageBackend;
use Drupal\event_log\StorageBackendInterface;

/**
 * Class DatabaseStorageBackend.
 *
 * @StorageBackend(
 *   id = "database",
 *   label = @Translation("Database"),
 *   description = @Translation("Store event logs in the database.")
 * )
 */
class DatabaseStorageBackend implements StorageBackendInterface {

  /**
   * {@inheritdoc}
   */
  public function save($data) {
    // TODO: Implement save() method.
  }

  public function deleteAll() {
    // TODO: Implement deleteAll() method.
  }
}
