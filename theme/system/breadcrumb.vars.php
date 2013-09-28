<?php
/**
 * @file
 * breadcrumb.vars.php
 */

/**
 * Implements hook_preprocess_breadcrumb().
 */
function bootstrap_preprocess_breadcrumb(&$variables) {
  $breadcrumb = &$variables['breadcrumb'];

  // Optionally get rid of the homepage link.
  $show_breadcrumb_home = theme_get_setting('bootstrap_breadcrumb_home');
  if (!$show_breadcrumb_home) {
    array_shift($breadcrumb);
  }

  if (theme_get_setting('bootstrap_breadcrumb_title') && !empty($breadcrumb)) {
    $item = menu_get_item();
    if (!empty($item['tab_parent'])) {
      // If we are on a non-default tab, use the tab's title.
      $breadcrumb[] = check_plain($item['title']);
    }
    else {
      $breadcrumb[] = drupal_get_title();
    }
  }
}
