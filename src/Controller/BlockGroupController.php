<?php

/**
 * @file
 * Contains Drupal\block_groups\Controller\BlockGroupController.
 */

namespace Drupal\block_groups\Controller;

use Drupal\block_groups\Entity\BlockGroup;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Condition\ConditionManager;

/**
 * Class BlockGroupController.
 *
 * @package Drupal\block_groups\Controller
 */
class BlockGroupController extends ControllerBase {

  /**
   * Drupal\Core\Condition\ConditionManager definition.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $plugin_manager_condition;
  /**
   * {@inheritdoc}
   */
  public function __construct(ConditionManager $plugin_manager_condition) {
    $this->plugin_manager_condition = $plugin_manager_condition;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.condition')
    );
  }

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index($param_1, $param_2) {
    return [
        '#type' => 'markup',
        '#markup' => $this->t('Implement method: index with parameter(s): $param_1, $param_2')
    ];
  }

  /**
   * Presents a list of access conditions to add to the page entity.
   *
   * @param \Drupal\block_groups\Entity\BlockGroup $block_group
   *   The page entity.
   *
   * @return array
   *   The access condition selection page.
   */
  public function selectAccessCondition(BlockGroup $block_group) {
    $build = [
      '#theme' => 'links',
      '#links' => [],
    ];
    return $build;
    $available_plugins = $this->conditionManager->getDefinitions();
    /*foreach ($available_plugins as $access_id => $access_condition) {
      $build['#links'][$access_id] = [
        'title' => $access_condition['label'],
        'url' => Url::fromRoute('page_manager.access_condition_add', [
          'page' => $page->id(),
          'condition_id' => $access_id,
        ]),
        'attributes' => [
          'class' => ['use-ajax'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 'auto',
          ]),
        ],
      ];
    }*/
    return $build;
  }

}
