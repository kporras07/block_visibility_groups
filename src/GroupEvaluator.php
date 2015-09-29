<?php
/**
 * @file
 * Contains Drupal\block_visibility_groups\GroupEvaluator.
 */

namespace Drupal\block_visibility_groups;
use Drupal\block_visibility_groups\Entity\BlockVisibilityGroup;
use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Core\Condition\ConditionAccessResolverTrait;
use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;


/**
 * Class ConditionEvaluator.
 *
 * @package Drupal\block_visibility_groups
 */
class GroupEvaluator implements GroupEvaluatorInterface {

  use ConditionAccessResolverTrait;

  /**
   * The plugin context handler.
   *
   * @var \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected $contextHandler;

  /**
   * The context manager service.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;
  /**
   * @var array $group_evaluations;
   */
  protected $group_evaluations = [];
  /**
   * Constructor.
   */
  public function __construct(ContextHandlerInterface $context_handler, ContextRepositoryInterface $context_repository) {

    $this->contextRepository = $context_repository;
    $this->contextHandler = $context_handler;
  }

  /**
   * @param \Drupal\block_visibility_groups\Entity\BlockVisibilityGroup $block_visibility_group
   *
   * @return boolean
   */
  public function evaluateGroup(BlockVisibilityGroup $block_visibility_group) {
    $group_id = $block_visibility_group->id();
    if (!isset($this->group_evaluations[$group_id])) {
      /** @var ConditionPluginCollection $conditions */
      $conditions = $block_visibility_group->getConditions();
      if ($this->applyContexts($conditions)) {
        $this->group_evaluations[$group_id] = $this->resolveConditions($conditions, $block_visibility_group->getLogic());
      }
      else {
        $this->group_evaluations[$group_id] = FALSE;
      }
    }
    return $this->group_evaluations[$group_id];
  }

  protected function applyContexts(ConditionPluginCollection &$conditions) {
    foreach ($conditions as $condition) {
      if ($condition instanceof ContextAwarePluginInterface) {
        try {
          $contexts = $this->contextRepository->getRuntimeContexts(array_values($condition->getContextMapping()));
          $this->contextHandler->applyContextMapping($condition, $contexts);
        }
        catch (ContextException $e) {
          return FALSE;
        }
      }
    }
    return TRUE;
  }
}
