<?php

namespace Drupal\event_log;

/**
 * Interface StorageBackendInterface.
 */
interface StorageBackendInterface {

  /**
   * @param array $data
   *
   * @return mixed
   */
  public function save($data);

  public function deleteAll();
}
