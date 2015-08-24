<?php

/**
 * @file
 * Contains Drupal\block_groups\BlockGroupInterface.
 */

namespace Drupal\block_groups;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;

/**
 * Provides an interface for defining Block group entities.
 */
interface BlockGroupInterface extends ConfigEntityInterface, EntityWithPluginCollectionInterface {
  // Add get/set methods for your configuration properties here.

}
