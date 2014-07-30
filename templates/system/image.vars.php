<?php
/**
 * @file
 * image.vars.php
 */

/**
 * Implements hook_preprocess_image().
 */
function bootstrap_preprocess_image(&$variables) {
  // Contrib modules have a very bad habit of frequently adding classes as
  // strings, convert them to a proper array.
  // @see https://www.drupal.org/node/2269653
  if (isset($variables['attributes']['class']) && !is_array($variables['attributes']['class'])) {
    $variables['attributes']['class'] = explode(' ', $variables['attributes']['class']);
  }

  // Add image shape, if necessary.
  if ($shape = theme_get_setting('bootstrap_image_shape')) {
    $variables['attributes']['class'][] = $shape;
  }

  // Add responsiveness, if necessary.
  if ($shape = theme_get_setting('bootstrap_image_responsive')) {
    $variables['attributes']['class'][] = 'img-responsive';
  }
}
