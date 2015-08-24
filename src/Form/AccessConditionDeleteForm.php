<?php

/**
 * @file
 * Contains \Drupal\block_visibility_groups\Form\AccessConditionDeleteForm.
 */

namespace Drupal\block_visibility_groups\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\block_visibility_groups\Entity\BlockVisibilityGroup;
use Drupal\Core\Form\ConfirmFormBase;

/**
 * Provides a form for deleting an access condition.
 */
class AccessConditionDeleteForm extends ConfirmFormBase {

  /**
   * The block_visibility_group entity this selection condition belongs to.
   *
   * @var \Drupal\block_visibility_groups\Entity\BlockVisibilityGroup
   */
  protected $block_visibility_group;

  /**
   * The access condition used by this form.
   *
   * @var \Drupal\Core\Condition\ConditionInterface
   */
  protected $accessCondition;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_visibility_group_manager_access_condition_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the access condition %name?', ['%name' => $this->accessCondition->getPluginDefinition()['label']]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->block_visibility_group->urlInfo('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, BlockVisibilityGroup $block_visibility_group = NULL, $condition_id = NULL) {
    $this->block_visibility_group = $block_visibility_group;
    $this->accessCondition = $block_visibility_group->getAccessCondition($condition_id);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->block_visibility_group->removeAccessCondition($this->accessCondition->getConfiguration()['uuid']);
    $this->block_visibility_group->save();
    drupal_set_message($this->t('The access condition %name has been removed.', ['%name' => $this->accessCondition->getPluginDefinition()['label']]));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
