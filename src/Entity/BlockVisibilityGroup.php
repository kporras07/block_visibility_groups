<?php

/**
 * @file
 * Contains Drupal\block_visibility_groups\Entity\BlockVisibilityGroup.
 */

namespace Drupal\block_visibility_groups\Entity;

use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\block_visibility_groups\BlockVisibilityGroupInterface;

/**
 * Defines the Block Visibility Group entity.
 *
 * @ConfigEntityType(
 *   id = "block_visibility_group",
 *   label = @Translation("Block Visibility Group"),
 *   handlers = {
 *     "list_builder" = "Drupal\block_visibility_groups\Controller\BlockVisibilityGroupListBuilder",
 *     "form" = {
 *       "add" = "Drupal\block_visibility_groups\Form\BlockVisibilityGroupForm",
 *       "edit" = "Drupal\block_visibility_groups\Form\BlockVisibilityGroupForm",
 *       "delete" = "Drupal\block_visibility_groups\Form\BlockVisibilityGroupDeleteForm"
 *     }
 *   },
 *   config_prefix = "block_visibility_group",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "access_logic",
 *     "access_conditions",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/block/block-visibility-group/{block_visibility_group}",
 *     "edit-form" = "/admin/structure/block/block-visibility-group/{block_visibility_group}/edit",
 *     "delete-form" = "/admin/structure/block/block-visibility-group/{block_visibility_group}/delete",
 *     "collection" =  "/admin/structure/block/block-visibility-group"
 *   }
 * )
 */
class BlockVisibilityGroup extends ConfigEntityBase implements BlockVisibilityGroupInterface {
  /**
   * The Block Visibility Group ID.
   *
   * @var string
   */
  protected $id;

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return [
      'access_conditions' => $this->getAccessConditions(),
    ];
  }
  /**
   * The Block Visibility Group label.
   *
   * @var string
   */
  protected $label;

  /**
   * The configuration of access conditions.
   *
   * @var array
   */
  protected $access_conditions = [];

  /**
   * Tracks the logic used to compute access, either 'and' or 'or'.
   *
   * @var string
   */
  protected $access_logic = 'and';

  /**
   * @return string
   */
  public function getAccessLogic() {
    return $this->access_logic;
  }

  /**
   * @param string $access_logic
   */
  public function setAccessLogic($access_logic) {
    $this->access_logic = $access_logic;
  }

  /**
   * The plugin collection that holds the access conditions.
   *
   * @var \Drupal\Component\Plugin\LazyPluginCollection
   */
  protected $accessConditionCollection;

  /**
   * Returns the conditions.
   *
   * @return \Drupal\Core\Condition\ConditionInterface[]|\Drupal\Core\Condition\ConditionPluginCollection
   *   An array of configured condition plugins.
   */
  public function getAccessConditions() {
    if (!$this->accessConditionCollection) {
      $this->accessConditionCollection = new ConditionPluginCollection(\Drupal::service('plugin.manager.condition'), $this->get('access_conditions'));
    }
    return $this->accessConditionCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccessCondition($condition_id) {
    return $this->getAccessConditions()->get($condition_id);
  }
  /**
   * {@inheritdoc}
   */
  public function addAccessCondition(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getAccessConditions()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  /**
   * {@inheritdoc}
   */
  public function removeAccessCondition($condition_id) {
    $this->getAccessConditions()->removeInstanceId($condition_id);
    return $this;
  }


}
