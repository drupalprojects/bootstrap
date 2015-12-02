<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\ElementInfo.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Plugin\PrerenderManager;
use Drupal\bootstrap\Plugin\ProcessManager;

/**
 * Implements hook_element_info_alter().
 *
 * @BootstrapAlter(
 *   id = "element_info"
 * )
 */
class ElementInfo implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$types, &$context1 = NULL, &$context2 = NULL) {
    $theme = Bootstrap::getTheme();
    $process_manager = new ProcessManager($theme);
    $pre_render_manager = new PrerenderManager($theme);

    foreach (array_keys($types) as $type) {
      $element = &$types[$type];

      // Ensure elements that have a base type with the #input set match.
      if (isset($element['#base_type']) && isset($types[$element['#base_type']]) && isset($types[$element['#base_type']]['#input'])) {
        $element['#input'] = $types[$element['#base_type']]['#input'];
      }

      // Replace detail and fieldset theme implementations with bootstrap_panel.
      if (!empty($element['#theme']) && ($element['#theme'] === 'details' || $element['#theme'] === 'fieldset')) {
        $element['#theme'] = 'bootstrap_panel';
      }
      if (!empty($element['#theme_wrappers']) && is_array($element['#theme_wrappers'])) {
        if (($key = array_search('details', $element['#theme_wrappers'])) !== FALSE) {
          $element['#theme_wrappers'][$key] = 'bootstrap_panel';
        }
        if (($key = array_search('fieldset', $element['#theme_wrappers'])) !== FALSE) {
          $element['#theme_wrappers'][$key] = 'bootstrap_panel';
        }
      }

      // Add extra variables to all elements.
      foreach (Bootstrap::extraVariables() as $key => $value) {
        if (!isset($variables["#$key"])) {
          $variables["#$key"] = $value;
        }
      }

      // Add necessary #process callbacks.
      $element['#process'][] = ['Drupal\bootstrap\Plugin\ProcessManager', 'process'];
      if ($process = $process_manager->getDefinition($type, FALSE)) {
        $element['#process'][] = [$process['class'], 'process'];
      }

      // Add necessary #pre_render callbacks.
      $element['#pre_render'][] = ['Drupal\bootstrap\Plugin\PrerenderManager', 'preRender'];
      if ($pre_render = $pre_render_manager->getDefinition($type, FALSE)) {
        $element['#pre_render'][] = [$pre_render['class'], 'preRender'];
      }
    }
  }

}
