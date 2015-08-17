<?php
/**
 * @file
 * block.vars.php
 */

/**
 * Implements hook_preprocess_block().
 */
function bootstrap_preprocess_block(&$variables) {
  // Use a bare template for the page's main content.
  if ($variables['plugin_id'] == 'system_main') {
    $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
  }
  $variables['title_attributes']['class'][] = 'block-title';
  $variables['attributes']['class'][] = 'block';
  $variables['attributes']['class'][] = 'clearfix';
}
