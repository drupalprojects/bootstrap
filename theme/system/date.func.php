<?php
/**
 * @file
 * date.func.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Overrides theme_date().
 */
function bootstrap_date($variables) {
  $element = $variables['element'];

  $attributes = array();
  if (isset($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  if (!empty($element['#attributes']['class'])) {
    $attributes['class'] = (array) $element['#attributes']['class'];
  }
  $attributes['class'][] = 'form-inline';

  return '<div' . new Attribute($attributes) . '>' . drupal_render_children($element) . '</div>';
}
