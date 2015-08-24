<?php

/**
 * @file
 * Contains Drupal\block_groups\Entity\BlockGroup.
 */

namespace Drupal\block_groups\Entity;

use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\block_groups\BlockGroupInterface;

/**
 * Defines the Block group entity.
 *
 * @ConfigEntityType(
 *   id = "block_group",
 *   label = @Translation("Block group"),
 *   handlers = {
 *     "list_builder" = "Drupal\block_groups\Controller\BlockGroupListBuilder",
 *     "form" = {
 *       "add" = "Drupal\block_groups\Form\BlockGroupForm",
 *       "edit" = "Drupal\block_groups\Form\BlockGroupForm",
 *       "delete" = "Drupal\block_groups\Form\BlockGroupDeleteForm"
 *     }
 *   },
 *   config_prefix = "block_group",
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
 *     "canonical" = "/admin/structure/block/block-group/{block_group}",
 *     "edit-form" = "/admin/structure/block/block-group/{block_group}/edit",
 *     "delete-form" = "/admin/structure/block/block-group/{block_group}/delete",
 *     "collection" =  "/admin/structure/block/block-group"
 *   }
 * )
 */
class BlockGroup extends ConfigEntityBase implements BlockGroupInterface {
  /**
   * The Block group ID.
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
   * The Block group label.
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
