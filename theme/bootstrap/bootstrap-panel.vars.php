<?php
/**
 * @file
 * bootstrap-panel.vars.php
 */

/**
 * Implements hook_preprocess_bootstrap_panel().
 */
function bootstrap_preprocess_bootstrap_panel(&$variables) {
  $element = &$variables['element'];
  $attributes = !empty($element['#attributes']) ? $element['#attributes'] : array();
  $variables['attributes'] = drupal_attributes($attributes);
  $variables['collapsible'] = $element['#collapsible'];
  $variables['collapsed'] = $element['#collapsed'];
  $variables['id'] = $element['#id'];
  $variables['content'] = $element['#children'];

  // Iterate over optional variables.
  $keys = array(
    'description',
    'prefix',
    'suffix',
    'title',
  );
  foreach ($keys as $key) {
    $variables[$key] = !empty($element["#$key"]) ? $element["#$key"] : FALSE;
  }
}
