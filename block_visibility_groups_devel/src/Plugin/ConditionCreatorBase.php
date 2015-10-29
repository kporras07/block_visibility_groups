<?php
/**
 * @file
 * Contains \Drupal\block_visibility_groups_devel\Plugin\ConditionCreatorBase.
 */


namespace Drupal\block_visibility_groups_devel\Plugin;


use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConditionCreatorBase extends PluginBase {

  /** @var \Drupal\Component\Plugin\PluginManagerInterface */
  protected $pluginManager;

  /** @var \Drupal\Core\Routing\CurrentRouteMatch */
  protected $currentRoute;

  /**
   * RouteConditionCreator constructor.
   */
  public function __construct(RouteMatchInterface $current_route_match, PluginManagerInterface $plugin_manager) {
    $this->currentRoute = $current_route_match;
    $this->pluginManager = $plugin_manager;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('current_route_match'),
      $container->get('plugin.manager.condition')
    );
  }
}
