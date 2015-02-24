<?php
/**
 * @file
 * form-element-label.vars.php
 */

use Drupal\Component\Utility\Xss;

/**
 * Overrides preprocess_form_element_label().
 */
function bootstrap_preprocess_form_element_label(&$variables) {
  // Add generic Bootstrap identifier class.
  $variables['attributes']['class'][] = 'control-label';

  // Add description.
  if (!empty($element['#description'])) {
    $variables['description'] = $element['#description'];
  }
}
