<?php
/**
 * @file
 * Stub file for "image" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\BaseTheme;

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
  $theme = BaseTheme::getTheme();

  // Add image shape, if necessary.
  if ($shape = $theme->getSetting('image_shape')) {
    _bootstrap_add_class($shape, $variables);
  }

  // Add responsiveness, if necessary.
  if ($theme->getSetting('image_responsive')) {
    _bootstrap_add_class('img-responsive', $variables);
  }
}
