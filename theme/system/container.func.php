<?php
/**
 * @file
 * container.func.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Overrides theme_container().
 */
function bootstrap_container($variables) {
  $element = $variables['element'];

  // Special handling for form elements.
  if (isset($element['#array_parents'])) {
    // Assign an html ID.
    if (!isset($element['#attributes']['id'])) {
      $element['#attributes']['id'] = $element['#id'];
    }
    // Add classes.
    $element['#attributes']['class'][] = 'form-wrapper';
    $element['#attributes']['class'][] = 'form-group';
  }

  return '<div' . new Attribute($element['#attributes']) . '>' . $element['#children'] . '</div>';
}
