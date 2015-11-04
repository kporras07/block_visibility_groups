<?php

/*
 * @file
 * Contains \Drupal\block_visibility_groups\Tests\BlockVisibilityGroupsTestBase
 */

namespace Drupal\block_visibility_groups\Tests;

use Drupal\simpletest\WebTestBase;

abstract class BlockVisibilityGroupsTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * var array
   */
  public static $modules = ['block', 'block_visibility_groups'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Create and login with user who can administer blocks.
    $this->drupalLogin($this->drupalCreateUser([
      'administer blocks',
    ]));
  }
}
