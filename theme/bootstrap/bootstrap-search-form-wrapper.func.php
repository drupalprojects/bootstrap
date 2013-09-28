<?php
/**
 * @file
 * bootstrap-search-form-wrapper.func.php
 */

/**
 * Theme function implementation for bootstrap_search_form_wrapper.
 */
function bootstrap_bootstrap_search_form_wrapper($variables) {
  $output = '<div class="input-group">';
  $output .= $variables['element']['#children'];
  $output .= '<span class="input-group-btn">';
  $output .= '<button type="submit" class="btn btn-default">';
  if (module_exists('icon')) {
    $output .= theme('icon', array('bundle' => 'bootstrap', 'icon' => 'glyphicon-search'));
  }
  // We can be sure that the font icons exist in CDN.
  elseif (theme_get_setting('bootstrap_cdn')) {
    $output .= '<i class="glyphicon glyphicon-search" aria-hidden="true"></i>';
  }
  else {
    $output .= t('Search');
  }
  $output .= '</button>';
  $output .= '</span>';
  $output .= '</div>';
  return $output;
}
