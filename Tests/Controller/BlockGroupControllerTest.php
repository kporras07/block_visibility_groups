<?php

/**
 * @file
 * Contains Drupal\block_groups\Tests\BlockGroupController.
 */

namespace Drupal\block_groups\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Condition\ConditionManager;

/**
 * Provides automated tests for the block_groups module.
 */
class BlockGroupControllerTest extends WebTestBase {

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
      'name' => "block_groups BlockGroupController's controller functionality",
      'description' => 'Test Unit for module block_groups and controller BlockGroupController.',
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
   * Tests block_groups functionality.
   */
  public function testBlockGroupController() {
    // Check that the basic functions of module block_groups.
    $this->assertEqual(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
