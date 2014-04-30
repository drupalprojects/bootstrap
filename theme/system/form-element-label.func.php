<?php
/**
 * @file
 * form-element-label.func.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Overrides theme_form_element_label().
 */
function bootstrap_form_element_label(&$variables) {
  $element = $variables['element'];

  // Determine if certain things should skip for checkbox or radio elements.
  $skip = (isset($element['#type']) && ('checkbox' === $element['#type'] || 'radio' === $element['#type']));

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '' && !$skip) && empty($element['#required'])) {
    return '';
  }

  // If the element is required, a required marker is appended to the label.
  if (!empty($element['#required'])) {
    $required_marker = array(
      '#theme' => 'form_required_market',
      '#element' => $element,
    );

    $required = drupal_render($required_marker);
  }
  else {
    $required = '';
  }

  $title = filter_xss_admin($element['#title']);

  $attributes = array();

  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after' && !$skip) {
    $attributes['class'][] = $element['#type'];
  }
  // Show label only to screen readers to avoid disruption in visual flows.
  elseif ($element['#title_display'] == 'invisible') {
    $attributes['class'][] = 'visually-hidden';
  }

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  // Insert radio and checkboxes inside label elements.
  $output = '';
  if (isset($variables['#children'])) {
    $output .= $variables['#children'];
  }

  // Append label.
  $output .= t('!title !required', array('!title' => $title, '!required' => $required));

  // The leading whitespace helps visually separate fields from inline labels.
  return ' <label' . new Attribute($attributes) . '>' . $output . "</label>\n";
}
