<?php
/**
 * Author: Ted Bowman
 * Date: 8/24/15
 * Time: 9:17 AM
 */

namespace Drupal\block_visibility_groups\Controller;


use Drupal\block\Controller\BlockLibraryController;
use Drupal\block_visibility_groups\Entity\BlockVisibilityGroup;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a list of block plugins to be added to the layout.
 */
class BlockLibraryGroupsController extends BlockLibraryController{
  public function listBlocks(Request $request, $theme, BlockVisibilityGroup $block_visibility_group) {
    $list = parent::listBlocks($request, $theme);
    $a = '';
    return $list;
  }

}
