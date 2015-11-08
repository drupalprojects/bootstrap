<?php
/**
 * @file
 * Stub file for "block" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "block" theme hook.
 *
 * See template for list of available variables.
 *
 * @see block.tpl.php
 *
 * @ingroup theme_preprocess
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
