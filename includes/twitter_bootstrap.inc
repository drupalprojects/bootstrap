<?php

/**
 * A wrapper function for twitter_bootstrap_theme_get_settings().
 * 
 * @param $name
 *   The name of the setting that you want to retrieve. 
 * @param $default (optional)
 *   The name (key) of the theme that you want to fetch the
 *   setting for. Defaults to NULL.   
 * @param $theme (optional)
 *   The key (machin-readable name) of a theme. Defaults to the key of the
 *   current theme if not defined.
 *   
 * @see 
 *   twitter_bootstrap_theme_get_setting().
 */
function twitter_bootstrap_theme_get_setting($name, $theme = NULL) {
  switch($name){
	case 'exclude':
		$setting = twitter_bootstrap_theme_get_info($name, $theme);
		break;
	default:
	  $setting = theme_get_setting($name, $theme);
		break;
  }

  return isset($setting) ? $setting : NULL; 
}

function twitter_bootstrap_get_settings($theme = NULL) {

  if (!isset($theme)) {
    $theme = !empty($GLOBALS['theme_key']) ? $GLOBALS['theme_key'] : '';
  }
	if($theme) {
		$themes = list_themes();
    $theme_object = $themes[$theme];
	}
	return $theme_object->info['settings'];
}


function twitter_bootstrap_theme_get_info($setting_name, $theme = NULL) {
// If no key is given, use the current theme if we can determine it.
  if (!isset($theme)) {
    $theme = !empty($GLOBALS['theme_key']) ? $GLOBALS['theme_key'] : '';
  }

  $output = array();

  if ($theme) {
    $themes = list_themes();
    $theme_object = $themes[$theme];

    // Create a list which includes the current theme and all its base themes.
    if (isset($theme_object->base_themes)) {
      $theme_keys = array_keys($theme_object->base_themes);
      $theme_keys[] = $theme;
    }
    else {
      $theme_keys = array($theme);
    }
    foreach ($theme_keys as $theme_key) {
      if (!empty($themes[$theme_key]->info[$setting_name])) {
        $output[$setting_name] = $themes[$theme_key]->info[$setting_name];
      }
    }
  }
  
  return $output;
}


function twitter_bootstrap_clean_navigation($tree) {
  $result = array();
  foreach($tree as $id => $item) {

	  $new_item = array('title' => $item['link']['title'], 'link_path' => $item['link']['link_path'], 'href' => $item['link']['href']);

	  if ($item['below']) {
		  $new_item['below'] = twitter_bootstrap_clean_navigation($item['below']);
	  }
	  $result['menu-'. $item['link']['mlid']] = $new_item;
  }
  return $result;
}

function twitter_bootstrap_twitter_bootstrap_link($variables) {
  $link = $variables['link'];
  $attributes = $variables['attributes'];
  $heading = $variables['heading'];
  $dropdown = $variables['dropdown'];
  
  $output = '';
  $class = ($dropdown && $link['below']) ? 'dropdown' : NULL;
  $output .= '<li' . drupal_attributes(array('class' => array($class))) . '>';
  
  if (isset($link['href'])) {
   // Pass in $link as $options, they share the same keys.
   
  if($link['below']) { 
	$link['html'] = TRUE;
	$link['title'] .= '<b class="caret"></b>';
	$output .=  '<a' . drupal_attributes($link['attributes']) . ' href="#">'. $link['title'] .'</a>';
  }else{
   
   $output .= l($link['title'], $link['href'], $link);
  }
  }
  elseif (!empty($link['title'])) {
   // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
   if (empty($link['html'])) {
	 $link['title'] = check_plain($link['title']);
   }
   $span_attributes = '';
   if (isset($link['attributes'])) {
	 $span_attributes = drupal_attributes($link['attributes']);
   }
   $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
  }
  
  $i++;
  
  if($link['below']) {
	$attributes = array();
	$attributes['class'] = array('dropdown-menu');
	
	$output .= theme('twitter_bootstrap_links', array('links' => $link['below'], 'attributes' => $attributes));
  }
  
  $output .= "</li>\n";
  
  return $output;
}  


function twitter_bootstrap_twitter_bootstrap_links($variables) {
  $links = $variables['links'];
  $attributes = $variables['attributes'];
  $heading = $variables['heading'];
  $dropdown = $variables['dropdown'];
  
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading. 
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $output .= '<ul' . drupal_attributes($attributes) . '>';

    $num_links = count($links);
    $i = 1;
	
	$has_children = FALSE;
    foreach ($links as $key => $link) {
      //$class = array($key);
	  $attributes = array('class' => array($key));
      if($link['below'] && $dropdown) {
		$attributes['class'][] = 'dropdown';
		$link['attributes']['data-toggle'] = 'dropdown';
		$link['attributes']['class'][] = 'dropdown-toggle';
      }
	  
	  $output .= theme('twitter_bootstrap_link', array('link' => $link, 'dropdown' => $dropdown, 'attributes' => $attributes));

    }

    $output .= '</ul>';
  }

  return $output;
}
