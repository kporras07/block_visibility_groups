<?php

/**
 * @file
 * Contains \Drupal\block_visibility_groups_devel\Form\ConditionCreatorForm.
 */

namespace Drupal\block_visibility_groups_devel\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

class ConditionCreatorForm extends FormBase{

  /** @var  \Drupal\Component\Plugin\PluginManagerInterface $manager; */
  protected $manager;
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.block_visibility_groups_devel.group_creator')
    );
  }

  /**
   * ConditionCreatorForm constructor.
   */
  public function __construct(PluginManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function getFormId() {
    return 'block_visibility_groups_devel_creator';
  }


  public function buildForm(array $form, FormStateInterface $form_state, $route_name = NULL, $parameters = NULL) {
    if (empty($route_name)) {
      // @todo Throw error
    }
    $form['conditions'] = $this->conditionOptions($route_name);
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Create new Group'),
    ];
    return $form;
  }

  protected function conditionOptions($route_name) {
    $elements = [
      '#tree' => TRUE,
    ];
    $this->manager->getDefinitions();
    $defs = $this->manager->getDefinitions();
    foreach ($defs as $id => $info) {
      $options = [];
      /** @var \Drupal\block_visibility_groups_devel\Plugin\BlockVisibilityGroupCreatorInterface $creator */
      $creator = $this->manager->createInstance($id,['route_name' => $route_name]);
      $condition_elements = $creator->getNewConditionsElements();
      if ($condition_elements) {
        $elements[$id] = $condition_elements;
      }
    }
    return $elements;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {
    $conditions = $this->getConditionValues($form_state);
    $A = 'A';
  }

  protected function getConditionValues(FormStateInterface $form_state) {
    $conditions = $form_state->cleanValues()->getValue('conditions');
    foreach ($conditions as $type => $condition_options) {
      $conditions[$type] = array_filter($condition_options);
      if (empty($conditions[$type])) {
        unset($conditions[$type]);
      }
    }
    return $conditions;
  }



}
