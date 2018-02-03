<?php

namespace Drupal\event_log;

/**
 * Interface LoggerInterface.
 */
interface LoggerInterface {

  /**
   * @param array $data
   */
  public function log($data);

}
