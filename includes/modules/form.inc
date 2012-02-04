<?php

function twitter_bootstrap_button(&$vars) {
  $element = $vars['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));
  
  $element['#attributes']['class'][] = 'btn'; // Add bootstrap class
  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled';
  }

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}

/**
 * Returns HTML for a form.
 *
 * @param $vars
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #action, #method, #attributes, #children
 *
 * @ingroup themeable
 */
function twitter_bootstrap_form($variables) {
  $element = $variables['element'];
  if (isset($element['#action'])) {
    $element['#attributes']['action'] = drupal_strip_dangerous_protocols($element['#action']);
  }
  element_set_attributes($element, array('method', 'id'));
  if (empty($element['#attributes']['accept-charset'])) {
    $element['#attributes']['accept-charset'] = "UTF-8";
  }
  
  $element['#attributes']['class'][] = "form-stacked";
  
  // Anonymous DIV to satisfy XHTML compliance.
  return '<form' . drupal_attributes($element['#attributes']) . '>' . $element['#children'] . '</form>';
}

/**
 * Returns HTML to wrap child elements in a container.
 *
 * Used for grouped form items. Can also be used as a #theme_wrapper for any
 * renderable element, to surround it with a <div> and add attributes such as
 * classes or an HTML id.
 *
 * @param $vars
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #id, #attributes, #children.
 *
 * @ingroup themeable
 */
function twitter_bootstrap_container($vars) {
  $element = $vars['element'];
  
  // Special handling for form elements.
  if (isset($element['#array_parents'])) {
    // Assign an html ID.
    if (!isset($element['#attributes']['id'])) {
      $element['#attributes']['id'] = $element['#id'];
    }
    // Add the 'form-wrapper' class.
    $element['#attributes']['class'][] = 'form-wrapper';
  }
  
  if(in_array('actions', $element['#parents'])) {
    $element['#attributes']['class'][] = 'actions';
  }

  return '<div' . drupal_attributes($element['#attributes']) . '>' . $element['#children'] . '</div>';
  #return $element['#children'];
}

function twitter_bootstrap_form_element($variables) {
  $element = &$variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );
  
  // Add clearfix class
  $attributes['class'][] = "control-group";
  
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';
  
  $description = '';
  if (!empty($element['#description'])) {
    $description = '<span class="help-block">' . $element['#description'] . "</span>\n";
  }
  
  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= '<div class="controls"> ' . $prefix . $element['#children'] . $suffix . $description ."</div>\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= '<div class="controls"> ' . theme('form_element_label', $variables) . $description ."</div>\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= '<div class="controls"> ' . $prefix . $element['#children'] . $suffix . $description ."</div>\n";
      break;
  }

  $output .= "</div>\n";

  return $output;
}
