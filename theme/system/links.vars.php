<?php
/**
 * @file
 * links.vars.php
 */

/**
 * Implements hook_preprocess_links().
 */
function bootstrap_preprocess_links(&$variables) {
  if (isset($variables['attributes']) && isset($variables['attributes']['class'])) {
    if ($key = array_search('inline', $variables['attributes']['class'])) {
      $variables['attributes']['class'][$key] = 'list-inline';
    }
  }
}
