<?php
/**
 * @file
 * input.vars.php
 */

/**
 * Preprocess input.
 */
function bootstrap_preprocess_input(&$variables) {
  $element = $variables['element'];

  // @todo Not converting to buttons. Should we? Also should we remove button and button--primary classes?
  _bootstrap_colorize_button($element);
  //_bootstrap_iconize_button($element);

  $variables['attributes'] = $element['#attributes'];

  if (_bootstrap_is_button($element)) {
    $variables['attributes']['class'][] = 'btn';
  }

  // Setup a default "icon" variable. This allows #icon to be passed
  // to every template and theme function.
  // @see https://drupal.org/node/2219965
  if (!isset($element['#icon'])) {
    $variables['element']['#icon'] = NULL;
  }
  if (!isset($element['#icon_position'])) {
    $variables['element']['#icon_position'] = 'before';
  }
  $variables = _bootstrap_prerender_input($variables);
}

function _bootstrap_prerender_input($variables) {
  $element = $variables['element'];
  $type = $element['#type'];

  // Only add the "form-control" class for specific element input types.
  $types = array(
    // Core.
    'password',
    'password_confirm',
    'select',
    'textarea',
    'textfield',
    // HTML5.
    'email',
    // Webform module.
    'webform_email',
    'webform_number',
    // Elements module.
    'emailfield',
    'numberfield',
    'rangefield',
    'searchfield',
    'telfield',
    'urlfield',
  );

  if (!empty($type) && (in_array($type, $types) || ($type === 'file' && empty($element['#managed_file'])))) {
    $variables['attributes']['class'][] = 'form-control';
  }

  // Tooltips for non- radios and checkboxes.
  if (!empty($type) && ($type !== 'checkbox' || $type !== 'radio' || $type !== 'checkboxes' || $type !== 'radios')) {
    if (!empty($element['#description']) && empty($element['#attributes']['title']) && _bootstrap_tooltip_description($element['#description'])) {
      $variables['attributes']['title'] = $element['#description'];
    }

    if (!empty($variables['attributes']['title'])) {
      $variables['attributes']['data-toggle'] = 'tooltip';
    }
  }
  return $variables;
}
