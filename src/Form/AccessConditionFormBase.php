<?php

/**
 * @file
 * Contains \Drupal\block_visibility_groups\Form\AccessConditionFormBase.
 */

namespace Drupal\block_visibility_groups\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a base form for editing and adding an access condition.
 */
abstract class AccessConditionFormBase extends ConditionFormBase {


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $configuration = $this->condition->getConfiguration();
    // If this access condition is new, add it to the block_visibility_group.
    if (!isset($configuration['uuid'])) {
      $this->block_visibility_group->addAccessCondition($configuration);
    }

    // Save the block_visibility_group entity.
    $this->block_visibility_group->save();

    $form_state->setRedirectUrl($this->block_visibility_group->urlInfo('edit-form'));
  }

}
