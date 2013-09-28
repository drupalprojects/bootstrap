<?php
/**
 * @file
 * bootstrap-tabs.vars.php
 */

/**
 * Implements hook_preprocess_bootstrap_tabs().
 */
function bootstrap_preprocess_bootstrap_tabs(&$variables) {
  // Attributes for the tabs container.
  $attributes = array('class' => array('tabbable'));
  // Process form vertical tabs.
  if (!isset($variables['tabs'])) {
    $variables['tabs'] = array();
    if (isset($variables['element']) && ($element = &$variables['element'])) {
      if (!empty($element['#tab_alignment'])) {
        $attributes['class'][] = 'tabs-' . $element['#tab_alignment'];
      }
      $tabs = array();
      $children = element_children($element['group']);
      foreach ($children as $key) {
        $child = $element['group'][$key];
        // Create an item_list item data structure.
        $tabs[$key] = array(
          'data' => l($child['#title'], '#' . $child['#id'], array(
            'external' => TRUE,
            'attributes' => array(
              'data-toggle' => 'tab',
            ),
          )),
        );
        // Use first tab if no default tab available.
        if (empty($element['#default_tab'])) {
          $tab_keys = array_keys($children);
          $element['#default_tab'] = $element['group'][array_shift($tab_keys)]['#id'];
        }
        // Set the active tab.
        if ($element['#default_tab'] === $child['#id']) {
          $tabs[$key]['class'][] = 'active';
        }
      }
      $variables['tabs'] = $tabs;
      $variables['content'] = $element['#children'];

      // Iterate over optional variables.
      $keys = array(
        'prefix',
        'suffix',
      );
      foreach ($keys as $key) {
        $variables[$key] = !empty($element["#$key"]) ? $element["#$key"] : FALSE;
      }
    }
  }
  // Add attributes for the tabs container.
  $variables['attributes'] = drupal_attributes($attributes);
}

/**
 * Implements hook_process_bootstrap_tabs().
 */
function bootstrap_process_bootstrap_tabs(&$variables) {
  // Render tabs.
  if (is_array($variables['tabs']) && !isset($variables['tabs']['#theme'])) {
    $tabs = array(
      '#theme' => 'item_list__bootstrap_tabs',
      '#items' => $variables['tabs'],
      '#attributes' => array(
        'class' => array('nav', 'nav-tabs'),
      ),
    );
    $variables['tabs'] = drupal_render($tabs);
  }
}
