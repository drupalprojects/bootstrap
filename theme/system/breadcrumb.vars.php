<?php
/**
 * @file
 * breadcrumb.vars.php
 */

/**
 * Implements hook_preprocess_breadcrumb().
 */
function bootstrap_preprocess_breadcrumb(&$variables) {
  $config = \Drupal::config('bootstrap.settings');
  $breadcrumb = &$variables['breadcrumb'];

  // Optionally get rid of the homepage link.
  $show_breadcrumb_home = $config->get('breadcrumb_home');
  if (!$show_breadcrumb_home) {
    array_shift($breadcrumb);
  }

  if ($config->get('breadcrumb_title') && !empty($breadcrumb)) {
    $item = menu_get_item();
    $breadcrumb[] = array(
      // If we are on a non-default tab, use the tab's title.
      'data' => !empty($item['tab_parent']) ? check_plain($item['title']) : drupal_get_title(),
      'class' => array('active'),
    );
  }
}
