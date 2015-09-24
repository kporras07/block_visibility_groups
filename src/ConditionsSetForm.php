<?php
/**
 * Author: Ted Bowman
 * Date: 9/23/15
 * Time: 6:14 PM
 */

namespace Drupal\block_visibility_groups;

use Drupal\block_visibility_groups\Entity\BlockVisibilityGroup;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;


trait ConditionsSetForm {

  use StringTranslationTrait;
  /**
   * @param array $form
   * @param $block_visibility_group
   *
   * @return array
   */
  protected function createConditionsSet(array $form, BlockVisibilityGroup $block_visibility_group, $redirect = 'edit') {
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
        'form-item',
      ]
    ]);
    $form['access_section_section'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Access Conditions'),
      '#open' => TRUE,
    ];

    $form['access_section_section']['add_condition'] = [
      '#type' => 'link',
      '#title' => $this->t('Add new access condition'),
      '#url' => Url::fromRoute('block_visibility_groups.access_condition_select', [
        'block_visibility_group' => $block_visibility_group->id(),
        'redirect' => $redirect,
      ]),
      '#attributes' => $add_button_attributes,
      '#attached' => [
        'library' => [
          'core/drupal.ajax',
        ],
      ],
    ];
    if ($access_conditions = $block_visibility_group->getConditions()) {
      $form['access_section_section']['access_section'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Label'),
          $this->t('Description'),
          $this->t('Operations'),
        ],
        '#empty' => $this->t('There are no access conditions.'),
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
            'block_visibility_group' => $block_visibility_group->id(),
            'condition_id' => $access_id,
            'redirect' => $redirect,
          ]),
          'attributes' => $attributes,
        ];
        $operations['delete'] = [
          'title' => $this->t('Delete'),
          'url' => Url::fromRoute('block_visibility_groups.access_condition_delete', [
            'block_visibility_group' => $block_visibility_group->id(),
            'condition_id' => $access_id,
            'redirect' => $redirect,
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
    return $form['access_section_section'];
  }
}
