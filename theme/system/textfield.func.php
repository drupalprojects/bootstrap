<?php
/**
 * @file
 * textfield.func.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Overrides theme_textfield().
 */
function bootstrap_textfield($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'text';
  element_set_attributes($element, array(
    'id',
    'name',
    'value',
    'size',
    'maxlength',
  ));
  _form_set_class($element, array('form-text'));

  $output = '<input' . new Attribute($element['#attributes']) . ' />';

  $extra = '';
  if ($element['#autocomplete_path'] && drupal_valid_path($element['#autocomplete_path'])) {
    drupal_add_library('system', 'drupal.autocomplete');
    $element['#attributes']['class'][] = 'form-autocomplete';

    $attributes = array();
    $attributes['type'] = 'hidden';
    $attributes['id'] = $element['#attributes']['id'] . '-autocomplete';
    $attributes['value'] = url($element['#autocomplete_path'], array('absolute' => TRUE));
    $attributes['disabled'] = 'disabled';
    $attributes['class'][] = 'autocomplete';
    $output = '<div class="input-group">' . $output . '<span class="input-group-addon">' . _bootstrap_icon('refresh') . '</span></div>';
    $extra = '<input' . new Attribute($attributes) . ' />';
  }

  return $output . $extra;
}
