<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\ElementInfo.
 */

namespace Drupal\bootstrap\Alter;

use Drupal\bootstrap\Bootstrap;

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
    foreach (array_keys($types) as $type) {
      $element = &$types[$type];

      // Ensure elements that have a base type with the #input set match.
      if (isset($element['#base_type']) && isset($types[$element['#base_type']]) && isset($types[$element['#base_type']]['#input'])) {
        $element['#input'] = $types[$element['#base_type']]['#input'];
      }

      // Replace fieldset theme implementations with bootstrap_panel.
      if (!empty($element['#theme']) && $element['#theme'] === 'fieldset') {
        $element['#theme'] = 'bootstrap_panel';
      }
      if (!empty($element['#theme_wrappers']) && is_array($element['#theme_wrappers']) && ($key = array_search('fieldset', $element['#theme_wrappers'])) !== FALSE) {
        $element['#theme_wrappers'][$key] = 'bootstrap_panel';
      }

      // Setup a default "icon" variable. This allows #icon to be passed
      // to every template and theme function.
      // @see https://drupal.org/node/2219965
      if (!isset($element['#icon'])) {
        $element['#icon'] = NULL;
      }
      if (!isset($element['#icon_position'])) {
        $element['#icon_position'] = 'before';
      }

      $properties = [
        '#process' => [
          'form_process',
          'form_process_' . $type,
        ],
        '#pre_render' => [
          'pre_render',
          'pre_render_' . $type,
        ],
      ];
      foreach ($properties as $property => $callbacks) {
        foreach ($callbacks as $callback) {
          foreach (Bootstrap::getTheme()->getAncestry() as $ancestor) {
            $function = $ancestor->getName() . '_' . $callback;
            if (function_exists($function)) {
              // Replace core function.
              if (!empty($element[$property]) && ($key = array_search($callback, $element[$property])) && $key !== FALSE) {
                $element[$property][$key] = $function;
              }
              // Check for a core "form_" prefix instead (for #pre_render).
              elseif (!empty($element[$property]) && ($key = array_search('form_' . $callback, $element[$property])) !== FALSE) {
                $element[$property][$key] = $function;
              }
              // Otherwise, append the function.
              else {
                $element[$property][] = $function;
              }
            }
          }
        }
      }
    }
  }

}
