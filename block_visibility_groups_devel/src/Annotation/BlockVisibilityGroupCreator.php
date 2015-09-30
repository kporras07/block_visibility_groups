<?php

/**
 * @file
 * Contains \\${NAMESPACE}\GroupCreator.
 */

namespace Drupal\block_visibility_groups_devel\Annotation;
use Drupal\Component\Annotation\Plugin;

/**
 * Class GroupCreator
 *
 * @package Drupal\block_visibility_groups_devel\Annotation
 *
 * @Annotation
 */
class BlockVisibilityGroupCreator extends Plugin{

  public $id;

  public $label;
}
