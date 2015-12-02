<?php
/**
 * @file
 * Stub file for "html" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\BaseTheme;

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
  switch (BaseTheme::getTheme()->getSetting('navbar_position')) {
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
