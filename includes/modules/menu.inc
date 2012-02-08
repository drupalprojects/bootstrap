<?php

/**
 * Returns HTML for primary and secondary local tasks.
 *
 * @ingroup themeable
 */

function twitter_bootstrap_menu_local_tasks(&$vars) {
  $output = '';
  if (!empty($vars['primary'])) {
    $vars['primary']['#prefix'] .= '<ul class="nav nav-tabs">';
    $vars['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['primary']);
  }
  if (!empty($vars['secondary'])) {
    $vars['secondary']['#prefix'] .= '<ul class="nav nav-pills">';
    $vars['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['secondary']);
  }

  return $output;
}

function twitter_bootstrap_menu_tree(&$variables) {
  return '<ul class="nav">' . $variables['tree'] . '</ul>';
}

function twitter_bootstrap_menu_link(array $variables) {
  $element = $variables['element'];
  $sub_menu = '';
  
  // Sanitize title
  $element['#title'] = check_plain($element['#title']);
  
  if ($element['#below']) {
	// Ad our own wrapper
	unset($element['#below']['#theme_wrappers']);
    $sub_menu = '<ul>' . drupal_render($element['#below']) . '</u>';

    //$element['#localized_options']['attributes']['class'][] = 'dropdown-toggle';
	//$element['#localized_options']['attributes']['data-toggle'] = 'dropdown';
	//$element['#attributes']['class'][] = 'dropdown';
	$element['#localized_options']['html'] = TRUE;
	//$element['#title'] .= '<b class="caret"></b>';
	
	// auch
	$element['#href'] = "";
  }
  
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}
