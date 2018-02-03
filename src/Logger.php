<?php

namespace Drupal\event_log;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Psr\Log\LoggerInterface as DrupalLogger;
use Symfony\Component\HttpFoundation\RequestStack;

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
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * Logger constructor.
   *
   * @param \Drupal\event_log\StorageBackendPluginManagerInterface $storage_backend_plugin_manager
   * @param \Psr\Log\LoggerInterface $drupal_logger
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   */
  public function __construct(
    StorageBackendPluginManagerInterface $storage_backend_plugin_manager,
    DrupalLogger $drupal_logger,
    ConfigFactoryInterface $config,
    EntityTypeManager $entity_type_manager,
    RequestStack $request
  ) {
    $this->storageBackendPluginManager = $storage_backend_plugin_manager;
    $this->drupalLogger = $drupal_logger;
    $this->config = $config;
    $this->entityTypeManager = $entity_type_manager;
    $this->request = $request;
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

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   */
  public function checkIfEntityIsEnabled(EntityInterface $entity){
    $entity_type_id = $entity->getEntityType()->id();
    $event_log_config = $this->config->get('event_log.config');
    $event_log_content_entities = $event_log_config->get('enabled_content_entities') ? $event_log_config->get('enabled_content_entities') : [];
    $event_log_config_entities = $event_log_config->get('enabled_config_entities') ? $event_log_config->get('enabled_config_entities') : [];
    if(in_array($entity_type_id,$event_log_content_entities) || in_array($entity_type_id,$event_log_config_entities)){
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $type
   */
  public function createLogEntity(EntityInterface $entity, $type) {
    $values = [];
    $values['type'][0]['value'] = $entity->getEntityType()->id() . '_' . $type;
    $values['operation'][0]['value'] = $type;
    $values['path'][0]['value'] = $this->request->getCurrentRequest()->getRequestUri();
    $values['ref_numeric'][0]['value'] = $entity->id();
    //manage title for standard nodes and name for custom content entities
    $title = $entity->get('title');
    if($title){
      $title =  $title->getValue()[0]['value'];
    } else {
      $title =  $entity->get('name')->getValue()[0]['value'];
    }
    $values['ref_char'][0]['value'] = $title;
    $values['description'][0]['value'] =  $this->getLogDescription($entity,$type);
    $event_log_storage = $this->entityTypeManager->getStorage('event_log');
    $event_log_entity = $event_log_storage->create($values);
    $event_log_entity->save();
  }

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $type
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  protected function getLogDescription(EntityInterface $entity, $type){
    $name = \Drupal::currentUser()->getAccountName();
    $uid = \Drupal::currentUser()->id();
    $description = t('user %name (uid %uid) performed %type operation on entity %entityname (id %id)', [
        '%name' => $name,
        '%uid' => $uid,
        '%entityname' => $entity->getEntityType()->getLabel(),
        '%id' => $entity->id(),
        '%operation' => $type
      ]
    );
    return $description;
  }

  protected function PurgeOldLogs(){
    $maxnum = $this->config->get('event_log.config');
    //@todo remove old logs
  }

}
