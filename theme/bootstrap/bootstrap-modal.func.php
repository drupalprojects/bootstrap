<?php
/**
 * @file
 * bootstrap-modal.func.php
 */

/**
 * Implements theme_bootstrap_modal().
 */
function bootstrap_bootstrap_modal($variables) {
  // Convert classes to an array.
  if (isset($variables['attributes']['class']) && is_string($variables['attributes']['class'])) {
    $variables['attributes']['class'] = explode(' ', $variables['attributes']['class']);
  }
  $variables['attributes']['class'][] = 'modal';
  $output = '<div' . drupal_attributes($variables['attributes']) . '>';
  $output .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
  $output .= '<h3>' . $variables['html_heading']? $variables['heading'] : check_plain($variables['heading']) . '</h3>';
  $output .= '</div>';
  $output .= '<div class="modal-body">' . render($variables['body']) . '</div>';
  $output .= '<div class="modal-footer">' . render($variables['footer']) . '</div>';
  $output .= '</div>';
  return $output;
}
