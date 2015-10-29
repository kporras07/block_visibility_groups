<?php

/**
 * @file
 * Contains \\${NAMESPACE}\RouteConditionCreator.
 */

namespace Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreator;

use Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreatorInterface;
use Drupal\block_visibility_groups_devel\Plugin\ConditionCreatorBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;


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
class RouteConditionCreator extends ConditionCreatorBase implements  ContainerFactoryPluginInterface{


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
    $route = $this->route->getRouteObject();
    $path = $route->getPath();
    $parameters = $this->route->getParameters();
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
