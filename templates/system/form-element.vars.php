<?php
/**
 * @file
 * form-element.vars.php
 *

/**
 * Preprocess form_element.
 */
function bootstrap_preprocess_form_element(&$variables) {
  $element = &$variables['element'];
  $type = $element['#type'];
  $is_checkbox = FALSE;
  $is_radio = FALSE;

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  if (empty($element['#wrapper_attributes'])) {
    $element['#wrapper_attributes'] = array();
  }
  $wrapper_attributes = &$element['#wrapper_attributes'];

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $wrapper_attributes['id'] = $element['#id'];
  }

  if (!empty($type)) {
    $wrapper_attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }

  if (!empty($element['#name'])) {
    $wrapper_attributes['class'][] = 'form-item-' . strtr($element['#name'], array(
        ' ' => '-',
        '_' => '-',
        '[' => '-',
        ']' => '',
      ));
  }

  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $wrapper_attributes['class'][] = 'form-disabled';
  }

  if (!empty($element['#autocomplete_path']) && drupal_valid_path($element['#autocomplete_path'])) {
    $wrapper_attributes['class'][] = 'form-autocomplete';
  }

  $wrapper_attributes['class'][] = 'form-item';

  // See http://getbootstrap.com/css/#forms-controls.
  if (isset($type)) {
    if ($type == "radio") {
      $wrapper_attributes['class'][] = 'radio';
      $is_radio = TRUE;
    }
    elseif ($type == "checkbox") {
      $wrapper_attributes['class'][] = 'checkbox';
      $is_checkbox = TRUE;
    }
    else {
      $wrapper_attributes['class'][] = 'form-group';
    }
  }

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $variables['element']['#title_display'] = 'none';
  }

  if ($is_checkbox || $is_radio) {
    $description = array(
      '#markup' => $element['#description'],
    );
    // Place the description in the label.
    $variables['label']['#description'] = \Drupal::service('renderer')->render($description);
    $variables['label']['#children'] = $variables['children'];
    unset($variables['children']);
    unset($variables['description']);
  }

  $tooltip_description = !empty($element['#description']) && _bootstrap_tooltip_description($element['#description']);
  if ($tooltip_description && ($type === 'checkbox' || $type === 'radio' || $type === 'checkboxes' || $type === 'radios')) {
    $wrapper_attributes['title'] = $element['#description'];
    $wrapper_attributes['data-toggle'] = 'tooltip';
  }

  // Remove description when tooltips are used.
  if (!empty($element['#description']) && $tooltip_description) {
    unset($variables['description']);
  }

  $variables['attributes'] = $wrapper_attributes;

  return $variables;
}
