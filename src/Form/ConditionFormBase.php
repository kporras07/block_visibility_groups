<?php

/**
 * @file
 * Contains \Drupal\block_group\Form\ConditionFormBase.
 */

namespace Drupal\block_groups\Form;

use Drupal\block_groups\BlockGroupInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginAssignmentTrait;
use Drupal\block_groups\Entity\BlockGroup;

/**
 * Provides a base form for editing and adding a condition.
 */
abstract class ConditionFormBase extends FormBase {

  use ContextAwarePluginAssignmentTrait;

  /**
   * The block_group entity this condition belongs to.
   *
   * @var \Drupal\block_groups\Entity\BlockGroup
   */
  protected $block_group;

  /**
   * The condition used by this form.
   *
   * @var \Drupal\Core\Condition\ConditionInterface
   */
  protected $condition;

  /**
   * Prepares the condition used by this form.
   *
   * @param string $condition_id
   *   Either a condition ID, or the plugin ID used to create a new
   *   condition.
   *
   * @return \Drupal\Core\Condition\ConditionInterface
   *   The condition object.
   */
  abstract protected function prepareCondition($condition_id);

  /**
   * Returns the text to use for the submit button.
   *
   * @return string
   *   The submit button text.
   */
  abstract protected function submitButtonText();

  /**
   * Returns the text to use for the submit message.
   *
   * @return string
   *   The submit message text.
   */
  abstract protected function submitMessageText();

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, BlockGroupInterface $block_group = NULL, $condition_id = NULL) {
    $this->block_group = $block_group;
    $this->condition = $this->prepareCondition($condition_id);
    $temporary = $form_state->getTemporary();
    $form_state->setTemporary($temporary);

    // Allow the condition to add to the form.
    $form['condition'] = $this->condition->buildConfigurationForm([], $form_state);
    $form['condition']['#tree'] = TRUE;

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->submitButtonText(),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Allow the condition to validate the form.
    $condition_values = (new FormState())->setValues($form_state->getValue('condition'));
    $this->condition->validateConfigurationForm($form, $condition_values);
    // Update the original form values.
    $form_state->setValue('condition', $condition_values->getValues());
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Allow the condition to submit the form.
    $condition_values = (new FormState())->setValues($form_state->getValue('condition'));
    $this->condition->submitConfigurationForm($form, $condition_values);
    // Update the original form values.
    $form_state->setValue('condition', $condition_values->getValues());

    if ($this->condition instanceof ContextAwarePluginInterface) {
      $this->condition->setContextMapping($condition_values->getValue('context_mapping', []));
    }

    // Set the submission message.
    drupal_set_message($this->submitMessageText());
  }

}
