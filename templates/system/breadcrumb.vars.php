<?php
/**
 * @file
 * Stub file for "breadcrumb" theme hook [pre]process functions.
 */

use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "breadcrumb" theme hook.
 *
 * See theme function for list of available variables.
 *
 * @see bootstrap_breadcrumb()
 * @see theme_breadcrumb()
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_breadcrumb(&$variables) {
  $breadcrumb = &$variables['breadcrumb'];

  // Optionally get rid of the homepage link.
  $show_breadcrumb_home = bootstrap_setting('breadcrumb_home');
  if (!$show_breadcrumb_home) {
    array_shift($breadcrumb);
  }

  if (bootstrap_setting('breadcrumb_title') && !empty($breadcrumb)) {
    $request = \Drupal::request();
    $route_match = \Drupal::routeMatch();
    $page_title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());

    if (!empty($page_title)) {
      $breadcrumb[] = array(
        'text' => $page_title,
        'attributes' => new Attribute(array('class' => array('active'))),
      );
    }
  }
}
