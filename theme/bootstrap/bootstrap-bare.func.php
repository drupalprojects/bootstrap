<?php
/**
 * @file
 * bootstrap-bare.func.php
 */

/**
 * Implements theme_bootstrap_bare().
 */
function bootstrap_bootstrap_bare($variables) {
  return $variables['element']['#children'];
}
