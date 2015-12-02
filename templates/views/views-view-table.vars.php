<?php
/**
 * @file
 * Stub file for "views_view_table" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;

/**
 * Pre-processes variables for the "views_view_table" theme hook.
 *
 * See template for list of available variables.
 *
 * @see views-view-table.tpl.php
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_views_view_table(&$variables) {
  Bootstrap::getTheme('bootstrap')->includeOnce('table.vars.php', 'templates/system');
  _bootstrap_table_add_classes($variables['attributes']['class'], $variables);
}
