<?php
/**
 * @file
 * Stub file for "image" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\Core\Template\Attribute;

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
  $attributes = new Attribute($variables['attributes']);

  // Add image shape, if necessary.
  if ($shape = $theme->getSetting('image_shape')) {
    $attributes->addClass($shape);
  }

  // Add responsiveness, if necessary.
  if ($theme->getSetting('image_responsive')) {
    $attributes->addClass('img-responsive');
  }

  $variables['attributes'] = $attributes->toArray();
}
