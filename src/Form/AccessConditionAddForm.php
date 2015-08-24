<?php

/**
 * @file
 * Contains   \Drupal\block_groups\Form\AccessConditionAddForm.
 */

namespace Drupal\block_groups\Form;

use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding a new access condition.
 */
class AccessConditionAddForm extends AccessConditionFormBase {

  /**
   * The condition manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * Constructs a new AccessConditionAddForm.
   *
   * @param \Drupal\Core\Condition\ConditionManager $condition_manager
   *   The condition manager.
   */
  public function __construct(ConditionManager $condition_manager, ContextRepositoryInterface $context_repository) {
    $this->conditionManager = $condition_manager;
    $this->contextRepository = $context_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.condition'),
      $container->get('context.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_group_access_condition_add_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareCondition($condition_id) {
    // Create a new access condition instance.
    return $this->conditionManager->createInstance($condition_id);
  }

  /**
   * {@inheritdoc}
   */
  protected function submitButtonText() {
    return $this->t('Add access condition');
  }

  /**
   * {@inheritdoc}
   */
  protected function submitMessageText() {
    return $this->t('The %label access condition has been added.', ['%label' => $this->condition->getPluginDefinition()['label']]);
  }

}
