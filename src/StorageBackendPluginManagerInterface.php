<?php

namespace Drupal\event_log;

use Drupal\Component\Plugin\FallbackPluginManagerInterface;
use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Class StorageBackendPluginManager.
 */
interface StorageBackendPluginManagerInterface extends PluginManagerInterface, FallbackPluginManagerInterface {

}
