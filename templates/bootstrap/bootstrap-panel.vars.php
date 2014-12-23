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
  $variables['attributes']['class'][] = 'panel';
  $variables['attributes']['class'][] = 'panel-default';

  // states.js requires form-wrapper on fieldset to work properly.
  $variables['attributes']['class'][] = 'form-wrapper';

  $variables['collapsible'] = FALSE;

  if (isset($element['#collapsible'])) {
    $variables['collapsible'] = $element['#collapsible'];
    $variables['attributes']['class'][] = 'collapsible';
  }

  $variables['collapsed'] = FALSE;
  if (isset($element['#collapsed'])) {
    $variables['collapsed'] = $element['#collapsed'];
    $variables['attributes']['class'][] = 'collapsed';
  }

  // Force grouped fieldsets to not be collapsible (for vertical tabs).
  if (!empty($element['#group'])) {
    $variables['collapsible'] = FALSE;
    $variables['collapsed'] = FALSE;
  }

  if (!isset($element['#id']) && $variables['collapsible']) {
    $element['#id'] = \Drupal\Component\Utility\Html::getUniqueId('bootstrap-panel');
  }

  $variables['target'] = NULL;
  if (isset($element['#id'])) {
    $variables['attributes']['id'] = $element['#id'];
    $variables['target'] = '#' . $element['#id'] . ' > .collapse';
  }
  $variables['content'] = $element['#children'];
  if (isset($element['#value'])) {
    $variables['content'] .= $element['#value'];
  }

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
