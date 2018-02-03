<?php

namespace Drupal\event_log;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface as DrupalLogger;

/**
 * Class Logger.
 */
class Logger implements LoggerInterface {

  /**
   * @var \Drupal\event_log\StorageBackendPluginManagerInterface
   */
  private $storageBackendPluginManager;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  private $drupalLogger;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $config;

  /**
   * Logger constructor.
   *
   * @param \Drupal\event_log\StorageBackendPluginManagerInterface $storage_backend_plugin_manager
   * @param \Psr\Log\LoggerInterface $drupal_logger
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   */
  public function __construct(StorageBackendPluginManagerInterface $storage_backend_plugin_manager, DrupalLogger $drupal_logger, ConfigFactoryInterface $config) {
    $this->storageBackendPluginManager = $storage_backend_plugin_manager;
    $this->drupalLogger = $drupal_logger;
    $this->config = $config;
  }

  /**
   * @param array $data
   */
  public function log($data) {
    // Uncomment this once the storage_backend_id is configurable.
    // $storageBackendId = $this->config->get('event_log.settings')->get('storage_backend_id');
    
    try {
      /** @var $storageBackend \Drupal\event_log\StorageBackendInterface */
      $storageBackend = $this->storageBackendPluginManager->createInstance('database');
      $storageBackend->save($data);
    } catch (PluginException $e) {
      $this->drupalLogger->error("Errors in logging data %data: %error", [
        print_r($data, TRUE),
        $e->getMessage(),
      ]);
    }
  }

}
