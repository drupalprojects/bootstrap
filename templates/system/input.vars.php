<?php
/**
 * @file
 * input.vars.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Preprocess input.
 */
function bootstrap_preprocess_input(&$variables) {
  $element = &$variables['element'];
  $attributes = new Attribute($variables['attributes']);

  // Set the element's attributes.
  \Drupal\Core\Render\Element::setAttributes($element, array('id', 'name', 'value', 'type'));

  // Handle button inputs.
  if (_bootstrap_is_button($element)) {
    $variables['attributes']['class'][] = 'btn';
    _bootstrap_colorize_button($variables);
    _bootstrap_iconize_button($element);

    // Add button size, if necessary.
    if ($size = bootstrap_setting('button_size')) {
      $variables['attributes']['class'][] = $size;
    }

    // Add in the button type class.
    $variables['attributes']['class'][] = 'form-' . $element['#type'];
    $variables['label'] = $element['#value'];
  }

  _bootstrap_prerender_input($variables);

  // Autocomplete fields.
  if (!empty($element['#autocomplete_route_name']) && Drupal::PathValidator($element['#autocomplete_route_name'])) {
    $variables['autocomplete'] = TRUE;

    // Attributes for hidden input field.
    $autocomplete_attributes = new Attribute();
    $autocomplete_attributes['type'] = 'hidden';
    $autocomplete_attributes['id'] = $element['#attributes']['id'] . '-autocomplete';
    $autocomplete_attributes['value'] = Drupal::Url($element['#autocomplete_route_name'], $element['#autocomplete_route_parameters']);
    $autocomplete_attributes['disabled'] = 'disabled';
    $autocomplete_attributes['class'] = 'autocomplete';

    // Uses icon for autocomplete "throbber".
    $icon = _bootstrap_icon('refresh');

    // Fallback to using core's throbber.
    if (empty($icon)) {
      $icon = array(
        '#type' => 'container',
        '#attributes' => array(
          'class' => array(
            'ajax-progress',
            'ajax-progress-throbber',
            'invisible',
          ),
        ),
        'throbber' => array(
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => array(
            'class' => array('throbber'),
          ),
        ),
      );
    }
    $variables['autocomplete_icon'] = $icon;
    $variables['autocomplete_attributes'] = $autocomplete_attributes;
  }

  // Search fields.
  if ($element['#type'] == 'search') {
    $attributes['placeholder'] = t('Search');
    $attributes['data-original-title'] = t('Enter the terms you wish to search for.');
  }

  // Additional Twig variables.
  $variables['icon'] = $element['#icon'];
  $variables['element'] = $element;
}

function _bootstrap_prerender_input(&$variables) {
  $element = $variables['element'];
  $type = $element['#type'];

  // Only add the "form-control" class for specific element input types.
  $types = array(
    // Core.
    'password',
    'password_confirm',
    'select',
    'textfield',
    'machine_name',
    'entity_autocomplete',
    // HTML5.
    'email',
    // Webform module.
    'webform_email',
    'webform_number',
    // Elements module.
    'date',
    'color',
    'email',
    'number',
    'range',
    'search',
    'tel',
    'url',
  );

  if (!empty($type) && (in_array($type, $types) || ($type === 'file' && empty($element['#managed_file'])))) {
    $variables['attributes']['class'][] = 'form-control';
  }
  return $variables;
}
