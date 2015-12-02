<?php
/**
 * @file
 * Stub file for "image" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Utility\Element;

/**
 * Pre-processes variables for the "image" theme hook.
 *
 * See theme function for list of available variables.
 *
 * @see theme_image()
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_image(&$variables) {
  $theme = Bootstrap::getTheme();
  $e = new Element($variables['element']);

  // Add image shape, if necessary.
  if ($shape = $theme->getSetting('image_shape')) {
    $e->addClass($shape);
  }

  // Add responsiveness, if necessary.
  if ($theme->getSetting('image_responsive')) {
    $e->addClass('img-responsive');
  }
}
