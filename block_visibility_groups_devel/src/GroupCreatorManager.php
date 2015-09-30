<?php

/**
 * @file
 * Contains Drupal\block_visibility_groups_devel\GroupCreatorManager.
 */

namespace Drupal\block_visibility_groups_devel;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;


/**
 * Class GroupCreatorManager.
 *
 * @package Drupal\block_visibility_groups_devel
 */
class GroupCreatorManager extends DefaultPluginManager {
  /**
   * Constructor.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/BlockVisibilityGroupCreator', $namespaces, $module_handler, 'Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreatorInterface', 'Drupal\block_visibility_groups_devel\Annotation\BlockVisibilityGroupCreator');
    $this->alterInfo('block_visibility_group_creator');
    $this->setCacheBackend($cache_backend, 'block_visibility_groups_devel:creator');
  }

}
