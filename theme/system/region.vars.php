<?php
/**
 * @file
 * region.vars.php
 */

/**
 * Implements hook_preprocess_region().
 */
function bootstrap_preprocess_region(&$variables) {
  if ($variables['region'] == 'content') {
    $variables['theme_hook_suggestions'][] = 'region__no_wrapper';
  }

  if ($variables['region'] == "sidebar_first") {
    $variables['classes_array'][] = 'well';
  }
}
