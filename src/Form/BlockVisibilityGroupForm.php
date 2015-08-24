<?php

/**
 * @file
 * Contains Drupal\block_visibility_groups\Form\BlockVisibilityGroupForm.
 */

namespace Drupal\block_visibility_groups\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\block_visibility_groups\Entity\BlockVisibilityGroup;

/**
 * Class BlockVisibilityGroupForm.
 *
 * @package Drupal\block_visibility_groups\Form
 */
class BlockVisibilityGroupForm extends EntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var BlockVisibilityGroup $block_visibility_group */
    $block_visibility_group = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $block_visibility_group->label(),
      '#description' => $this->t("Label for the Block Visibility Group."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $block_visibility_group->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\block_visibility_groups\Entity\BlockVisibilityGroup::load',
      ),
      '#disabled' => !$block_visibility_group->isNew(),
    );
    if (!$block_visibility_group->isNew()) {
      $attributes = [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'width' => 'auto',
        ]),
      ];
      $add_button_attributes = NestedArray::mergeDeep($attributes, [
        'class' => [
          'button',
          'button--small',
          'button-action',
        ]
      ]);
      $form['access_section_section'] = [
        '#type' => 'details',
        '#title' => $this->t('Access Conditions'),
        '#open' => TRUE,
      ];
      $form['access_section_section']['add_condition'] = [
        '#type' => 'link',
        '#title' => $this->t('Add new access condition'),
        // @todo Add route for selecting
        '#url' => Url::fromRoute('block_visibility_groups.access_condition_select', [
          'block_visibility_group' => $this->entity->id(),
        ]),
        '#attributes' => $add_button_attributes,
        '#attached' => [
          'library' => [
            'core/drupal.ajax',
          ],
        ],
      ];
      if ($access_conditions = $block_visibility_group->getAccessConditions()) {
        $form['access_section_section']['access_section'] = [
          '#type' => 'table',
          '#header' => [
            $this->t('Label'),
            $this->t('Description'),
            $this->t('Operations'),
          ],
          '#empty' => $this->t('There are no access conditions.'),
        ];

        $form['access_section_section']['access_logic'] = [
          '#type' => 'radios',
          '#options' => [
            'and' => $this->t('All conditions must pass'),
            'or' => $this->t('Only one condition must pass'),
          ],
          '#default_value' => $this->entity->getAccessLogic(),
        ];

        $form['access_section_section']['access'] = [
          '#tree' => TRUE,
        ];
        foreach ($access_conditions as $access_id => $access_condition) {
          $row = [];
          $row['label']['#markup'] = $access_condition->getPluginDefinition()['label'];
          $row['description']['#markup'] = $access_condition->summary();
          $operations = [];
          $operations['edit'] = [
            'title' => $this->t('Edit'),
            'url' => Url::fromRoute('block_visibility_groups.access_condition_edit', [
              'block_visibility_group' => $this->entity->id(),
              'condition_id' => $access_id,
            ]),
            'attributes' => $attributes,
          ];
          $operations['delete'] = [
            'title' => $this->t('Delete'),
            'url' => Url::fromRoute('block_visibility_groups.access_condition_delete', [
              'block_visibility_group' => $this->entity->id(),
              'condition_id' => $access_id,
            ]),
            'attributes' => $attributes,
          ];
          $row['operations'] = [
            '#type' => 'operations',
            '#links' => $operations,
          ];
          $form['access_section_section']['access_section'][$access_id] = $row;
        }
      }
    }

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $block_visibility_group = $this->entity;
    $status = $block_visibility_group->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label Block Visibility Group.', array(
        '%label' => $block_visibility_group->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label Block Visibility Group was not saved.', array(
        '%label' => $block_visibility_group->label(),
      )));
    }
    $form_state->setRedirectUrl($block_visibility_group->urlInfo('collection'));
  }

}
