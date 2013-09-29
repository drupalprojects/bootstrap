<?php
/**
 * @file
 * region.vars.php
 */

/**
 * Implements hook_preprocess_region().
 */
function bootstrap_preprocess_region(&$variables) {
  switch ($variables['region']) {
    // @todo is this actually used properly?
    case 'content':
      $variables['theme_hook_suggestions'][] = 'region__no_wrapper';
      break;

    case 'help':
      $variables['content'] = _bootstrap_icon('question-sign') . $variables['content'];
      $variables['classes_array'][] = 'alert';
      $variables['classes_array'][] = 'alert-info';
      break;

    // @todo do we still want this? It makes this look ugly.
    case 'sidebar_first':
      $variables['classes_array'][] = 'well';
      break;
  }
}
