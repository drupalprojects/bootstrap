<?php
/**
 * @file
 * form-element-label.vars.php
 */

/**
 * Overrides theme_form_element_label().
 */
function bootstrap_preprocess_form_element_label(&$variables) {
  $element = $variables['element'];
  // Determine if certain things should skip for checkbox or radio elements.
  $skip = (isset($element['#type']) && ('checkbox' === $element['#type'] || 'radio' === $element['#type']));

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '' && !$skip) && empty($element['#required'])) {
    return '';
  }

  $title = $element['#title']; //filter_xss_admin($element['#title']);

  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after' && !$skip) {
    $variables['attributes']['class'][] = $element['#type'];
  }
  // Show label only to screen readers to avoid disruption in visual flows.
  elseif ($element['#title_display'] == 'invisible') {
    $variables['attributes']['class'][] = 'visually-hidden';
  }

  // Add generic Bootstrap identifier class.
  $variables['attributes']['class'][] = 'control-label';

  if (!empty($element['#id'])) {
    $variables['attributes']['for'] = $element['#id'];
  }

  // Add description.
  // @todo: We don't want to always do this.
  if (!empty($element['#description'])) {
    $variables['description'] = $element['#description'];
  }

  $variables['required'] = !empty($element['#required']);

}
