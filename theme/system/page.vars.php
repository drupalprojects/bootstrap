<?php
/**
 * @file
 * page.vars.php
 */

/**
 * Implements hook_preprocess_page().
 *
 * @see page.tpl.php
 */
function bootstrap_preprocess_page(&$variables) {
  // Ensure each region has the correct theme wrappers.
  foreach (system_region_list($GLOBALS['theme_key']) as $name => $title) {
    if (!$variables['page'][$name]) {
      $variables['page'][$name]['#theme_wrappers'] = array('region');
      $variables['page'][$name]['#region'] = $name;
    }
  }

  // Primary menu.
  $variables['primary_nav'] = array();
  if ($variables['main_menu']) {
    // Build links.
    $variables['primary_nav'] = menu_tree(variable_get('menu_main_links_source', 'main-menu'));
    // Provide default theme wrapper function.
    $variables['primary_nav']['#theme_wrappers'] = array('menu_tree__primary');
  }

  // Secondary nav.
  $variables['secondary_nav'] = array();
  if ($variables['secondary_menu']) {
    // Build links.
    $variables['secondary_nav'] = menu_tree(variable_get('menu_secondary_links_source', 'user-menu'));
    // Provide default theme wrapper function.
    $variables['secondary_nav']['#theme_wrappers'] = array('menu_tree__secondary');
  }

  // Add the site slogan to the header region.
  if ($variables['site_slogan']) {
    array_unshift($variables['page']['header'], array(
      '#theme' => 'html_tag__site_slogan',
      '#tag' => 'p',
      '#attributes' => array(
        'class' => array('lead'),
      ),
      '#value' => $variables['site_slogan'],
    ));
  }
}

/**
 * Implements hook_process_page().
 *
 * @see page.tpl.php
 */
function bootstrap_process_page(&$variables) {
  // Store the page variables in cache so it can be used in region
  // preprocessing.
  $page = &drupal_static(__FUNCTION__);
  if (!isset($page)) {
    $page = $variables;
  }
}
