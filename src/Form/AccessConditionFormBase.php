<?php

/**
 * @file
 * Contains \Drupal\block_groups\Form\AccessConditionFormBase.
 */

namespace Drupal\block_groups\Form;

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
    // If this access condition is new, add it to the block_group.
    if (!isset($configuration['uuid'])) {
      $this->block_group->addAccessCondition($configuration);
    }

    // Save the block_group entity.
    $this->block_group->save();

    $form_state->setRedirectUrl($this->block_group->urlInfo('edit-form'));
  }

}
