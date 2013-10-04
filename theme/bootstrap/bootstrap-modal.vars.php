<?php
/**
 * @file
 * bootstrap-modal.vars.php
 */

/**
 * Implements theme_preprocess_bootstrap_modal().
 *
 * @todo: Implement the fade effect.
 */
function bootstrap_preprocess_bootstrap_modal(&$variables) {
  $element = &$variables['element'];
  $attributes = !empty($element['#attributes']) ? $element['#attributes'] : array();

  $attributes['class'][] = 'modal';
  $attributes['id'] = $variables['id'];
  $variables['attributes'] = drupal_attributes($attributes);

  $variables['heading'] = (isset($variables['html_heading']) && $variables['html_heading']) ? $variables['heading'] : check_plain($variables['heading']);
}
