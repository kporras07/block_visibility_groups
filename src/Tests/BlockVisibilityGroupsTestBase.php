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

    // Create Basic page and Article node types.
    if ($this->profile != 'standard') {
      $this->drupalCreateContentType(array(
        'type' => 'page',
        'name' => 'Basic page',
        'display_submitted' => FALSE,
      ));
      $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));
    }
  }

  /**
   * @param $plugin_id
   * @param $group_id
   * @param array $settings
   *
   * @return \Drupal\block\Entity\Block
   */
  protected function placeBlockInGroupProg($plugin_id, $group_id, $settings = []) {
    $settings['label_display'] = 'visible';
    $settings['label'] = $this->randomMachineName();
    $block = $this->drupalPlaceBlock($plugin_id, $settings);
    /*
    $condition_group_settings = [
      'plugin_id' => 'condition_group',
      'negate' => FALSE,
      'block_visibility_group' => $group_id,
      'context_mapping' => [],
    ];
    $visibility = $block->getVisibility();
    $visibility['condition_group']['block_visibility_group'] = $group_id;
    */
    $block->setVisibilityConfig('condition_group',['block_visibility_group' => $group_id]);
    return $block;
  }
  protected function placeBlockInGroup($plugin_id, $group_id, $title) {

    // Enable a standard block.
    $default_theme = $this->config('system.theme')->get('default');
    $edit = array(
      'id' => strtolower($this->randomMachineName(8)),
      'region' => 'sidebar_first',
      'settings[label]' => $title,
    );
    $block_id = $edit['id'];
    $edit['visibility[condition_group][block_visibility_group]'] = $group_id;

    $this->drupalGet('admin/structure/block/add/' . $plugin_id . '/' . $default_theme);

    $this->drupalPostForm(NULL, $edit, t('Save block'));
    $this->assertText('The block configuration has been saved.', 'Block was saved');
  }
}
