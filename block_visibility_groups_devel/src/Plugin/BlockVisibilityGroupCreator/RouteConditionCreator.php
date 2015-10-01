<?php

/**
 * @file
 * Contains \\${NAMESPACE}\RouteConditionCreator.
 */

namespace Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreator;

use Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreatorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Routing\CurrentRouteMatch;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the form in-place editor.
 *
 * @Plugin(
 *   id = "route",
 *   label = "Route Creator"
 * )
 */
class RouteConditionCreator extends PluginBase implements  BlockVisibilityGroupCreatorInterface, ContainerFactoryPluginInterface{

  /** @var \Drupal\Core\Routing\CurrentRouteMatch */
  protected $currentRoute;
  /**
   * RouteConditionCreator constructor.
   */
  public function __construct(RouteMatchInterface $current_route_match) {
    $this->currentRoute = $current_route_match;
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
      $container->get('current_route_match')
    );
  }
  public function createConditionsForms() {
    $path = $this->getPathPattern();


    return [
      '#type' => 'markup',
      '#markup' => "<h1>$path</h1>",
    ];
    // TODO: Implement createConditionsForms() method.

  }

  /**
   * @return mixed|string
   */
  protected function getPathPattern() {
    $route = $this->currentRoute->getRouteObject();
    $path = $route->getPath();
    $parameters = $this->currentRoute->getParameters();
    foreach ($parameters as $pkey => $pvalue) {
      $path = str_replace('{' . $pkey . '}', '*', $path);
    }
    return $path;
  }

  public function getNewConditionsElements() {
    $current_path = $this->getPathPattern();
    return [
      'current_route' => [
        '#type' => 'checkbox',
        '#return_value' => $current_path,
        //'#default_value' => $current_path,
        '#title' => $this->t('Current path: @path', ['@path' => $current_path]),
      ],
    ];
  }


}
