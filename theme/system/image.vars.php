<?php
/**
 * @file
 * image.vars.php
 */

/**
 * Implements hook_preprocess_image().
 */
function bootstrap_preprocess_image(&$variables) {
  // Add image shape, if necessary.
  if ($shape = theme_get_setting('bootstrap_image_shape')) {
    $variables['attributes']['class'][] = $shape;
  }
  // Add responsiveness, if necessary.
  if ($shape = theme_get_setting('bootstrap_image_responsive')) {
    $variables['attributes']['class'][] = 'img-responsive';
  }
}
