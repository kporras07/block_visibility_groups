<?php

/**
 * @file
 * Contains \\${NAMESPACE}\RouteConditionCreator.
 */

namespace Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreator;

use Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreatorInterface;

/**
 * Defines the form in-place editor.
 *
 * @BlockVisibilityGroupCreator(
 *   id = "route",
 *   label = "Route Creator"
 * )
 */
class RouteConditionCreator implements  BlockVisibilityGroupCreatorInterface{
  public function createConditionsForms() {
    return [
      '#type' => 'markup',
      '#markup' => '<h1>HI</h1>',
    ];
    // TODO: Implement createConditionsForms() method.
  }

}
