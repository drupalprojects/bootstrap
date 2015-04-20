<?php
/**
 * @file
 * breadcrumb.vars.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_breadcrumb().
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
