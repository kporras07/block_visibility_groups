<?php

/**
 * @file
 * Contains Drupal\block_visibility_groups\Tests\BlockVisibilityGroupController.
 */

namespace Drupal\block_visibility_groups\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Condition\ConditionManager;

/**
 * Provides automated tests for the block_visibility_groups module.
 */
class BlockVisibilityGroupControllerTest extends WebTestBase {

  /**
   * Drupal\Core\Condition\ConditionManager definition.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $plugin_manager_condition;
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "block_visibility_groups BlockVisibilityGroupController's controller functionality",
      'description' => 'Test Unit for module block_visibility_groups and controller BlockVisibilityGroupController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests block_visibility_groups functionality.
   */
  public function testBlockVisibilityGroupController() {
    // Check that the basic functions of module block_visibility_groups.
    $this->assertEqual(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
