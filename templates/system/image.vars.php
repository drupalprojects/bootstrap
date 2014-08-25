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
    _bootstrap_add_class($shape, $variables);
  }

  // Add responsiveness, if necessary.
  if (theme_get_setting('bootstrap_image_responsive')) {
    _bootstrap_add_class('img-responsive', $variables);
  }
}
