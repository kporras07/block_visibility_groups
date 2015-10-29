<?php

/**
 * @file
 * Contains Drupal\block_visibility_groups_devel\GroupCreatorManager.
 */

namespace Drupal\block_visibility_groups_devel;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Routing\RouteMatch;
use Symfony\Component\Routing\Route;


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
    parent::__construct('Plugin/BlockVisibilityGroupCreator', $namespaces, $module_handler, 'Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreatorInterface');
    $this->alterInfo('block_visibility_group_creator');
    $this->setCacheBackend($cache_backend, 'block_visibility_groups_devel:creator');
  }

  /**
   * @param string $plugin_id
   * @param array $configuration
   *
   * @return object
   * @throws \Exception
   */
  public function createInstance($plugin_id, array $configuration = array()) {
    if (empty($configuration['route_name'])) {
      // @todo Also check for parameters?
      throw new \Exception('Route name is require configuration for GroupCreatorManager');
    }
    $route_name = $configuration['route_name'];
    /** @var \Drupal\Core\Routing\RouteProvider $route_provider */
    $route_provider = \Drupal::getContainer()->get('router.route_provider');
    $configuration['route'] = new RouteMatch($route_name,$route_provider->getRouteByName($route_name));
    unset($configuration['route_name']);
    return parent::createInstance($plugin_id, $configuration);
  }


}
