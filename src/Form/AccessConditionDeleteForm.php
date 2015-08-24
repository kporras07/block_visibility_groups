<?php

/**
 * @file
 * Contains \Drupal\block_groups\Form\AccessConditionDeleteForm.
 */

namespace Drupal\block_groups\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\block_groups\Entity\BlockGroup;
use Drupal\Core\Form\ConfirmFormBase;

/**
 * Provides a form for deleting an access condition.
 */
class AccessConditionDeleteForm extends ConfirmFormBase {

  /**
   * The block_group entity this selection condition belongs to.
   *
   * @var \Drupal\block_groups\Entity\BlockGroup
   */
  protected $block_group;

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
    return 'block_group_manager_access_condition_delete_form';
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
    return $this->block_group->urlInfo('edit-form');
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
  public function buildForm(array $form, FormStateInterface $form_state, BlockGroup $block_group = NULL, $condition_id = NULL) {
    $this->block_group = $block_group;
    $this->accessCondition = $block_group->getAccessCondition($condition_id);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->block_group->removeAccessCondition($this->accessCondition->getConfiguration()['uuid']);
    $this->block_group->save();
    drupal_set_message($this->t('The access condition %name has been removed.', ['%name' => $this->accessCondition->getPluginDefinition()['label']]));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
