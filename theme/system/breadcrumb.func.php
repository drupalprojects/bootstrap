<?php
/**
 * @file
 * breadcrumb.func.php
 */

/**
 * Overrides theme_breadcrumb().
 *
 * Print breadcrumbs as an ordered list.
 */
function bootstrap_breadcrumb($variables) {
  $config = \Drupal::config('bootstrap.settings');
  $output = '';
  $breadcrumb = $variables['breadcrumb'];

  // Determine if we are to display the breadcrumb.
  $bootstrap_breadcrumb = $config->get('breadcrumb');
  if (($bootstrap_breadcrumb == 1 || ($bootstrap_breadcrumb == 2 && arg(0) == 'admin')) && !empty($breadcrumb)) {
    $links = array(
      '#theme' => 'item_list',
      '#attributes' => array(
        'class' => array('breadcrumb'),
      ),
      '#items' => $breadcrumb,
      '#list_type' => 'ol',
    );

    $output = drupal_render($links);
  }

  return $output;
}
