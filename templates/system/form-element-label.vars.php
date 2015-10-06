<?php
/**
 * @file
 * form-element-label.vars.php
 */

use Drupal\Component\Utility\Xss;

/**
 * Overrides theme_form_element_label().
 */
function bootstrap_preprocess_form_element_label(&$variables) {
  $element = $variables['element'];
  // If title and required marker are both empty, output no label.
  $variables['title'] = !empty($element['#title']) ? $element['#title'] : '';
  $variables['attributes'] = array();

  // Pass elements title_display to template.
  $variables['title_display'] = $element['#title_display'];

  // A #for property of a dedicated #type 'label' element as precedence.
  if (!empty($element['#for'])) {
    $variables['attributes']['for'] = $element['#for'];
    // A custom #id allows the referenced form input element to refer back to
    // the label element; e.g., in the 'aria-labelledby' attribute.
    if (!empty($element['#id'])) {
      $variables['attributes']['id'] = $element['#id'];
    }
  }
  // Otherwise, point to the #id of the form input element.
  elseif (!empty($element['#id'])) {
    $variables['attributes']['for'] = $element['#id'];
  }

  // Pass elements required to template.
  $variables['required'] = !empty($element['#required']) ? $element['#required'] : NULL;

  // Add generic Bootstrap identifier class.
  $variables['attributes']['class'][] = 'control-label';
}
