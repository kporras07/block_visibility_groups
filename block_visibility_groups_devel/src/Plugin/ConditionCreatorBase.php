<?php
/**
 * @file
 * Contains \Drupal\block_visibility_groups_devel\Plugin\ConditionCreatorBase.
 */


namespace Drupal\block_visibility_groups_devel\Plugin;


use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ConditionCreatorBase extends PluginBase implements BlockVisibilityGroupCreatorInterface {

  use StringTranslationTrait;
  /** @var \Drupal\Component\Plugin\PluginManagerInterface */
  protected $pluginManager;

  /** @var \Drupal\Core\Routing\CurrentRouteMatch */
  protected $route;

  /**
   * RouteConditionCreator constructor.
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, PluginManagerInterface $plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->route = $configuration['route'];
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
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.condition')
    );
  }
}
