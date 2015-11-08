<?php
/**
 * @file
 * Stub file for "html" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "html" theme hook.
 *
 * See template for list of available variables.
 *
 * @see html.tpl.php
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_html(&$variables) {
  switch (bootstrap_setting('navbar_position')) {
    case 'fixed-top':
      $variables['attributes']['class'][] = 'navbar-is-fixed-top';
      break;

    case 'fixed-bottom':
      $variables['attributes']['class'][] = 'navbar-is-fixed-bottom';
      break;

    case 'static-top':
      $variables['attributes']['class'][] = 'navbar-is-static-top';
      break;
  }
}
