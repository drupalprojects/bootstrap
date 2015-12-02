<?php
/**
 * @file
 * Stub file for "breadcrumb" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;
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
  $theme = Bootstrap::getTheme();
  $breadcrumb = &$variables['breadcrumb'];

  // Optionally get rid of the homepage link.
  $show_breadcrumb_home = $theme->getSetting('breadcrumb_home');
  if (!$show_breadcrumb_home) {
    array_shift($breadcrumb);
  }

  if ($theme->getSetting('breadcrumb_title') && !empty($breadcrumb)) {
    $request = \Drupal::request();
    $route_match = \Drupal::routeMatch();
    $page_title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());

    if (!empty($page_title)) {
      $breadcrumb[] = [
        'text' => $page_title,
        'attributes' => new Attribute(['class' => ['active']]),
      ];
    }
  }
}
