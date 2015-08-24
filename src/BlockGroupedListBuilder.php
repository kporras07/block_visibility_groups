<?php
/**
 * Author: Ted Bowman
 * Date: 8/22/15
 * Time: 6:22 PM
 */

namespace Drupal\block_groups;


use Drupal\block\BlockListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\block\Entity\Block;
use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BlockGroupedListBuilder extends BlockListBuilder{

  /**
   * The entity storage class for Block Groups.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $block_group_storage;
  /**
   * Constructs a new BlockGroupedListBuilder object.
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
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, ThemeManagerInterface $theme_manager, FormBuilderInterface $form_builder, EntityStorageInterface $block_group_storage) {
    parent::__construct($entity_type, $storage, $theme_manager, $form_builder);

    $this->block_group_storage = $block_group_storage;
  }

  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('theme.manager'),
      $container->get('form_builder'),
      $container->get('entity.manager')->getStorage('block_group')
    );
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $group_options = $this->getBlockGroupOptions();
    $current_block_group = $this->getCurrentBlockGroup();
    $options = [];
    foreach ($group_options as $key => $group_option) {
      if ($current_block_group == $key) {
        $default_value = $group_option['path'];
      }
      $options[$group_option['path']] = $group_option['label'];
    }

    $form['extra'] = array(
      '#type' => 'select',
      '#title' => $this->t('Block Group'),
      '#options' => $options,
      '#default_value' => $default_value,
      '#weight' => -100,
      // @todo Is there a better way to do this?
      '#attributes' => ['onchange' => 'this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value)'],
    );
    return $form;
  }

  protected function getCurrentBlockGroup() {
    $request_id = $this->request->query->get('block_group');
    return $request_id;
  }

  protected function getBlockGroupOptions() {
    $block_groups = $this->block_group_storage->loadMultiple();
    $route_options = [
      '' => ['label' => $this->t('All Blocks')],
      '-unset' => ['label' => $this->t('Unset Only')],
    ];
    foreach ($block_groups as $type) {
      $group_id = $type->id();
      $route_options[$group_id] = ['label' => $type->label()];
    }
    foreach ($route_options as $key => &$route_option) {

        $url = Url::fromRoute('block.admin_display_theme', [
          'theme' => $this->theme,
        ],
          [
            'query' => ['block_group' => $key]
          ]
        );
      $route_option['path'] = $url->toString();
    }

    return $route_options;
  }

  protected function buildBlocksForm() {
    $form = parent::buildBlocksForm();
    if ($block_group = $this->getBlockGroup()) {
      foreach ($form as $row_key => &$row_info) {
        if (isset($row_info['title']['#url'])) {
          /** @var \Drupal\Core\Url $url */
          $url = $row_info['title']['#url'];
          $query = $url->getOption('query');
          $url = Url::fromRoute('block_groups.admin_library',
            [
              'theme' => $this->getThemeName(),
              'block_group' => $block_group,
            ],
            [
              'query' => [
                'region' => $query['region'],
              ],
            ]);
          $row_info['title']['#url'] = $url;
          //$query['block_group'] = $this->getBlockGroup();
          //$url->setOption('query', $query);
        }
      }
    }

    return $form;

  }

  protected function getBlockGroup() {
    return $this->request->query->get('block_group');
  }


  protected function getEntityIds() {
    $entity_ids = parent::getEntityIds();
    if ($current_block_group = $this->getCurrentBlockGroup()) {
      $entities = $this->storage->loadMultipleOverrideFree($entity_ids);
      /** @var Block $block */
      foreach ($entities as $block) {
        /** @var ConditionPluginCollection $conditions */
        $conditions = $block->getVisibilityConditions();
        $config_block_group = '';
        if ($conditions->has('condition_group')) {
          $condition_config = $conditions->get('condition_group')->getConfiguration();
          $config_block_group = $condition_config['block_group'];
        }
        if ('-unset' == $current_block_group) {
          if (!empty($config_block_group)) {
            unset($entity_ids[$block->id()]);
          }
        }
        elseif ($config_block_group != $current_block_group) {
          unset($entity_ids[$block->id()]);
        }
      }
  }
    return $entity_ids;
  }



}

