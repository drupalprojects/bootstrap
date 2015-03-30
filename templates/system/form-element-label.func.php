<?php
/**
 * @file
 * form-element-label.func.php
 */

/**
 * Overrides theme_form_element_label().
 */
function bootstrap_form_element_label(&$variables) {
  $element = $variables['element'];

  // Extract variables.
  $output = '';
  $title = isset($element['#title']) ? filter_xss_admin($element['#title']) : '';
  $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';
  if ($required) {
    $title = empty($title) ? $required : "$title $required";
  }
  $display = isset($element['#title_display']) ? $element['#title_display'] : 'none';
  $type = !empty($element['#type']) ? $element['#type'] : FALSE;
  $checkbox = $type && $type === 'checkbox';
  $radio = $type && $type === 'radio';

  // Immediately return if there is no title, required marker and element is
  // not a checkbox or radio (which requires the label to be rendered).
  if (!$title && !$required && !$checkbox && !$radio) {
    return '';
  }

  // Create the attributes array for the label.
  $attributes = array(
    // Add Bootstrap label class.
    'class' => array('control-label')
  );

  // Add the necessary 'for' attribute if the element ID exists.
  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  // Checkboxes and radios must construct the label differently.
  if ($checkbox || $radio) {
    if ($display === 'before') {
      $output .= $title;
    }
    elseif ($display === 'invisible') {
      $output .= '<span class="element-invisible">' . $title . '</span>';
    }
    // Inject the rendered checkbox or radio element inside the label.
    if (!empty($element['#children'])) {
      $output .= $element['#children'];
    }
    if ($display === 'after') {
      $output .= $title;
    }
  }
  // Otherwise, just render the title as the label.
  else {
    // Show label only to screen readers to avoid disruption in visual flows.
    if ($display === 'invisible') {
      $attributes['class'][] = 'element-invisible';
    }
    $output .= $title;
  }

  // The leading whitespace helps visually separate fields from inline labels.
  return ' <label' . drupal_attributes($attributes) . '>' . $output . "</label>\n";
}
