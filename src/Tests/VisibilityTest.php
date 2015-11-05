<?php
/**
 * @file
 * Contains \Drupal\block_visibility_groups\Tests\VisibilityTest.
 */


namespace Drupal\block_visibility_groups\Tests;


use Drupal\block_content\Entity\BlockContent;
use Drupal\block_visibility_groups\Entity\BlockVisibilityGroup;
use Drupal\node\Plugin\Condition\NodeType;

/**
 * Tests the block_visibility_groups Visibility Settings.
 *
 * @group block_visibility_groups
 */
class VisibilityTest extends BlockVisibilityGroupsTestBase {

  /**
   * Modules to enable.
   *
   * var array
   */
  public static $modules = ['block', 'block_visibility_groups', 'node'];

  public function testSingleConditions() {
    $group = BlockVisibilityGroup::create(
      [
        'id' => $this->randomMachineName(),
        'label' => $this->randomString(),
      ]
    );
    $config = [
      'id' => 'node_type',
      'bundles' => ['page'],
      'negate' => 0,
      'context_mapping' => ['node' => '@node.node_route_context:node'],
    ];
    $group->save();
    $group->addCondition($config);
    $group->save();

    $block_title = $this->randomMachineName();
    $this->placeBlockInGroup('system_powered_by_block', $group->id(), $block_title);

    $page_node = $this->drupalCreateNode();
    $this->drupalGet('node/' . $page_node->id());
    $this->assertText($block_title,'Block shows up on page node.');

    $article_node = $this->drupalCreateNode(['type' => 'article']);
    $this->drupalGet('node/' . $article_node->id());
    $this->assertNoText($block_title,'Block does not show up on article node.');
  }
}
