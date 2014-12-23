<?php
/**
 * @file
 * button.vars.php
 */

/**
 * Implements hook_preprocess_button().
 */
function bootstrap_preprocess_button(&$variables) {
  $element = &$variables['element'];

  // Set the element's attributes.
  \Drupal\Core\Render\Element::setAttributes($element, array('id', 'name', 'value', 'type'));

  // Add the base Bootstrap button class.
  $variables['element']['#attributes']['class'][] = 'btn';

  // Add button size, if necessary.
  if ($size = theme_get_setting('bootstrap_button_size')) {
    $variables['element']['#attributes']['class'][] = $size;
  }

  // Colorize button.
  _bootstrap_colorize_button($element);

  // Add in the button type class.
  $variables['element']['#attributes']['class'][] = 'form-' . $element['#button_type'];

  // Ensure that all classes are unique, no need for duplicates.
  $variables['element']['#attributes']['class'] = array_unique($element['#attributes']['class']);
}
