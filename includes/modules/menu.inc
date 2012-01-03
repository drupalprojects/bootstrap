<?php

/**
 * Returns HTML for primary and secondary local tasks.
 *
 * @ingroup themeable
 */

function twitter_bootstrap_menu_local_tasks(&$vars) {
  $output = '';
  if (!empty($vars['primary'])) {
    $vars['primary']['#prefix'] .= '<ul class="tabs primary">';
    $vars['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['primary']);
  }
  if (!empty($vars['secondary'])) {
    $vars['secondary']['#prefix'] .= '<ul class="pills secondary">';
    $vars['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['secondary']);
  }

  return $output;
}
