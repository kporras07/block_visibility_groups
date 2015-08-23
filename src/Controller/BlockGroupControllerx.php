<?php
/**
 * Author: Ted Bowman
 * Date: 8/23/15
 * Time: 2:19 PM
 */

namespace Drupal\block_groups\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BlockGroupControllerx extends ControllerBase {
  /**
   * Constructs a new DisplayVariantEditForm.
   *
   * @param \Drupal\Core\Block\BlockManagerInterface $block_manager
   *   The block manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $condition_manager
   *   The condition manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $variant_manager
   *   The variant manager.
   * @param \Drupal\Core\Plugin\Context\ContextHandlerInterface $context_handler
   *   The context handler.
   */
  public function __construct(PluginManagerInterface $condition_manager) {
    $this->conditionManager = $condition_manager;
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
   * Presents a list of access conditions to add to the page entity.
   *
   * @param \Drupal\page_manager\PageInterface $page
   *   The page entity.
   *
   * @return array
   *   The access condition selection page.
   */
  public function selectAccessCondition(PageInterface $page) {
    $build = [
      '#theme' => 'links',
      '#links' => [],
    ];
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
