<?php
/**
 * Prepares variables for select element templates.
 *
 * Default template: select.html.twig.
 *
 * It is possible to group options together; to do this, change the format of
 * $options to an associative array in which the keys are group labels, and the
 * values are associative arrays in the normal $options format.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #options, #description, #extra,
 *     #multiple, #required, #name, #attributes, #size.
 */
function bootstrap_preprocess_select(&$variables) {
  $element = $variables['element'];
  $variables['attributes']['class'][] = 'form-control';
}
