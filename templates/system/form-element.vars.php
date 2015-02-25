<?php
/**
 * @file
 * form-element.vars.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Preprocess form_element.
 */
function bootstrap_preprocess_form_element(&$variables) {
  $element = &$variables['element'];
  $type = $element['#type'];
  $title_display = $element['#title_display'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by
  // \Drupal::formBuilder()->doBuildForm().
  $element += array(
    '#title_display' => 'before',
  );

  // Take over any #wrapper_attributes defined by the element.
  // @todo Temporary hack for #type 'item'.
  // @see http://drupal.org/node/1829202
  $variables['attributes'] = array();
  if (isset($element['#wrapper_attributes'])) {
    $variables['attributes'] = $element['#wrapper_attributes'];
  }

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $variables['attributes']['id'] = $element['#id'];
  }

  // Pass elements #type and #name to template.
  if (!empty($element['#type'])) {
    $variables['type'] = $type;
  }
  if (!empty($element['#name'])) {
    $variables['name'] = $element['#name'];
  }

  // Pass elements disabled status to template.
  $variables['disabled'] = !empty($element['#attributes']['disabled']) ? $element['#attributes']['disabled'] : NULL;

  // If #title is not set, we don't display any label.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }

  $variables['title_display'] = $element['#title_display'];

  $variables['prefix'] = isset($element['#field_prefix']) ? $element['#field_prefix'] : NULL;
  $variables['suffix'] = isset($element['#field_suffix']) ? $element['#field_suffix'] : NULL;

  $variables['description'] = NULL;
  if (!empty($element['#description'])) {
    $variables['description_display'] = $element['#description_display'];
    $description_attributes = [];
    if (!empty($element['#id'])) {
      $description_attributes['id'] = $element['#id'] . '--description';
    }
    $variables['description']['attributes'] = new Attribute($description_attributes);
    $variables['description']['content'] = $element['#description'];
  }

  // Add label_display and label variables to template.
  $variables['label_display'] = $element['#title_display'];
  $variables['label'] = array('#theme' => 'form_element_label');
  $variables['label'] += array_intersect_key($element, array_flip(array('#id', '#required', '#title', '#title_display')));

  $variables['children'] = $element['#children'];

  if (!empty($element['#autocomplete_path']) && drupal_valid_path($element['#autocomplete_path'])) {
    $variables['attributes']['class'][] = 'form-autocomplete';
  }

  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $variables['attributes']['class'][] = 'form-disabled';
  }

  // See http://getbootstrap.com/css/#forms-controls.

  $is_checkbox = FALSE;
  $is_radio = FALSE;

  if (isset($type)) {
    if ($type == "radio") {
      $variables['attributes']['class'][] = 'radio';
      $is_radio = TRUE;
    }
    elseif ($type == "checkbox") {
      $variables['attributes']['class'][] = 'checkbox';
      $is_checkbox = TRUE;
    }
    else {
      $variables['attributes']['class'][] = 'form-group';
    }
  }

  if (($is_checkbox || $is_radio) && $title_display != 'none' && $title_display != 'invisible') {
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

  // Input Groups.
  $variables['input_group'] = $element['#input_group'];
  $variables['input_group_button'] = $element['#input_group_button'];
}
