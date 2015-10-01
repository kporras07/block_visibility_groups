<?php
/**
 * @file
 * Contains Drupal\block_visibility_groups_devel\GroupCreatorManager.
 */

namespace Drupal\block_visibility_groups_devel\Plugin;



use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

interface BlockVisibilityGroupCreatorInterface {
  public function createConditionsForms();

  public function getNewConditionsElements();

}
