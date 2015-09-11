<?php
/**
 * Author: Ted Bowman
 * Date: 8/22/15
 * Time: 6:22 PM
 */

namespace Drupal\block_visibility_groups;


use Drupal\block\BlockListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\block\Entity\Block;
use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BlockVisibilityGroupedListBuilder extends BlockListBuilder{

  const UNSET_GROUP = 'UNSET-GROUP';
  const ALL_GROUP = 'ALL-GROUP';
  /**
   * The entity storage class for Block Visibility Groups.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $block_visibility_group_storage;
  /**
   * Constructs a new BlockVisibilityGroupedListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, ThemeManagerInterface $theme_manager, FormBuilderInterface $form_builder, EntityStorageInterface $block_visibility_group_storage) {
    parent::__construct($entity_type, $storage, $theme_manager, $form_builder);

    $this->block_visibility_group_storage = $block_visibility_group_storage;
  }

  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('theme.manager'),
      $container->get('form_builder'),
      $container->get('entity.manager')->getStorage('block_visibility_group')
    );
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $group_options = $this->getBlockVisibilityGroupOptions();
    $default_value = $this->getCurrentBlockVisibilityGroup();
    $current_block_visibility_group = NULL;
    if (!in_array($default_value,[BlockVisibilityGroupedListBuilder::ALL_GROUP, BlockVisibilityGroupedListBuilder::UNSET_GROUP])) {
      $current_block_visibility_group = $default_value;
    }
    $options = [];

    foreach ($group_options as $key => $group_option) {
      if ($default_value == $key) {
        $default_value = $group_option['path'];
      }
      $options[$group_option['path']] = $group_option['label'];
    }
    $form['block_visibility_group'] = array(
      '#weight' => -100,
    );
    $form['block_visibility_group']['select'] = array(
      '#type' => 'select',
      '#title' => $this->t('Block Visibility Group'),
      '#options' => $options,
      '#default_value' => $default_value,

      // @todo Is there a better way to do this?
      '#attributes' => ['onchange' => 'this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value)'],

    );
    $description = $this->t('Block Visibility Groups allow you to control the visibility of multiple blocks in one place.');

    if (!$this->groupsExist()) {
      $description .= ' ' . $this->t('No Groups have been created yet.');
      $form['block_visibility_group']['create'] = array(
        '#type' => 'link',
        '#title' => t('Create a Group'),
        '#url' => Url::fromRoute('entity.block_visibility_group.add_form'),
      );
    }
    else {
      if ($current_block_visibility_group) {
        $group = $this->block_visibility_group_storage->load($current_block_visibility_group);
        $url_info = $group->urlInfo('edit-form');
        $form['block_visibility_group']['edit'] = array(
          '#type' => 'link',
          '#title' => t('Edit current Group'),
          '#url' => $url_info,
        );
      }

    }
    $form['block_visibility_group']['select']['#description'] = $description;


    return $form;
  }

  protected function getCurrentBlockVisibilityGroup() {
    $request_id = $this->request->query->get('block_visibility_group');
    if (!$request_id) {
      $request_id = BlockVisibilityGroupedListBuilder::UNSET_GROUP;
    }
    return $request_id;
  }

  protected function getBlockVisibilityGroupOptions() {

    $route_options = [
      BlockVisibilityGroupedListBuilder::UNSET_GROUP => ['label' => $this->t('- Global blocks -')],
      BlockVisibilityGroupedListBuilder::ALL_GROUP => ['label' => $this->t('- All Blocks -')],
    ];
    $block_visibility_group_labels = $this->getBlockVisibilityLabels();
    foreach ($block_visibility_group_labels as $id => $label) {
      $route_options[$id] = ['label' => $label];
    }
    foreach ($route_options as $key => &$route_option) {

        $url = Url::fromRoute('block.admin_display_theme', [
          'theme' => $this->theme,
        ],
          [
            'query' => ['block_visibility_group' => $key]
          ]
        );
      $route_option['path'] = $url->toString();
    }

    return $route_options;
  }

  protected function buildBlocksForm() {
    $form = parent::buildBlocksForm();
    if ($block_visibility_group = $this->getBlockVisibilityGroup(TRUE)) {
      foreach ($form as $row_key => &$row_info) {
        if (isset($row_info['title']['#url'])) {
          /** @var \Drupal\Core\Url $url */
          $url = $row_info['title']['#url'];
          $query = $url->getOption('query');
          $url = Url::fromRoute('block_visibility_groups.admin_library',
            [
              'theme' => $this->getThemeName(),
              'block_visibility_group' => $block_visibility_group,
            ],
            [
              'query' => [
                'region' => $query['region'],
              ],
            ]);
          $row_info['title']['#url'] = $url;
          //$query['block_visibility_group'] = $this->getBlockVisibilityGroup();
          //$url->setOption('query', $query);
        }

      }
    }

    // If viewing all blocks, add a column indicating the visibility group.
    if ($this->getBlockVisibilityGroup() == static::ALL_GROUP) {
      $entity_ids = [];
      foreach ($form as $row_key => &$row) {
        if (strpos($row_key, 'region-') !== 0) {
          $entity_ids[] = $row_key;
        }
      }
      $entities = $this->storage->loadMultipleOverrideFree($entity_ids);
      if (!empty($entities)) {
        $labels = $this->getBlockVisibilityLabels();
        foreach ($entities as $block) {
          if (!empty($form[$block->id()])) {
            // Get visibility group label.
            $visibility_group = $this->t('Global');
            $conditions = $block->getVisibilityConditions();
            if ($conditions->has('condition_group')) {
              $condition_config = $conditions->get('condition_group')->getConfiguration();
              $visibility_group = $labels[$condition_config['block_visibility_group']];
            }
            $row = &$form[$block->id()];
            // Insert visibility group at correct position.
            foreach (Element::Children($row) as $i => $child) {
              $row[$child]['#weight'] = $i;
            }
            $row['block_visibility_group'] = [
              '#markup' => $visibility_group,
              '#weight' => 1.5,
            ];
            $row['#sorted'] = FALSE;
          }
        }
        // Adjust header.
        array_splice($form['#header'], 2, 0, array($this->t('Visibility group')));
        // Increase colspan.
        foreach (Element::children($form) as $child) {
          foreach(Element::children($form[$child]) as $gchild) {
            if (isset($form[$child][$gchild]['#wrapper_attributes']['colspan'])) {
              $form[$child][$gchild]['#wrapper_attributes']['colspan'] =
                $form[$child][$gchild]['#wrapper_attributes']['colspan'] + 1;
            }
          }
        }
      }
    }


    return $form;

  }

  protected function getBlockVisibilityGroup($groups_only = FALSE) {
    $group = $this->request->query->get('block_visibility_group');
    if ($groups_only && in_array($group, [$this::ALL_GROUP, $this::UNSET_GROUP])) {
      return NULL;
    }
    return $group;
  }


  protected function getEntityIds() {
    $entity_ids = parent::getEntityIds();
    $current_block_visibility_group = $this->getCurrentBlockVisibilityGroup();
    if (!empty($current_block_visibility_group)
      && $current_block_visibility_group != $this::ALL_GROUP) {
      $entities = $this->storage->loadMultipleOverrideFree($entity_ids);
      /** @var Block $block */
      foreach ($entities as $block) {
        /** @var ConditionPluginCollection $conditions */
        $conditions = $block->getVisibilityConditions();
        $config_block_visibility_group = '';
        if ($conditions->has('condition_group')) {
          $condition_config = $conditions->get('condition_group')->getConfiguration();
          $config_block_visibility_group = $condition_config['block_visibility_group'];
        }
        if (BlockVisibilityGroupedListBuilder::UNSET_GROUP == $current_block_visibility_group) {
          if (!empty($config_block_visibility_group)) {
            unset($entity_ids[$block->id()]);
          }
        }
        elseif ($config_block_visibility_group != $current_block_visibility_group) {
          unset($entity_ids[$block->id()]);
        }
      }
    }
    return $entity_ids;
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]
   */
  protected function getBlockVisibilityLabels() {
    $block_visibility_groups = $this->block_visibility_group_storage->loadMultiple();
    $labels = [];
    foreach ($block_visibility_groups as $type) {

      $labels[$type->id()] = $type->label();
    }
    return $labels;
  }

  protected function groupsExist() {
    return !empty($this->block_visibility_group_storage->loadMultiple());
  }


}

