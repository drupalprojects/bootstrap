<?php

global $theme_key;

include_once(dirname(__FILE__) . '/includes/twitter_bootstrap.inc');
include_once(dirname(__FILE__) . '/includes/modules/theme.inc');
include_once(dirname(__FILE__) . '/includes/modules/pager.inc');
include_once(dirname(__FILE__) . '/includes/modules/form.inc');

$modules = module_list();

foreach ($modules as $module) {
  if (is_file(drupal_get_path('theme', $theme_key) . '/includes/modules/' . str_replace('_', '-', $module) . '.inc')) {
    include_once(drupal_get_path('theme', $theme_key) . '/includes/modules/' . str_replace('_', '-', $module) . '.inc');
  }    
}

/**
 * Preprocess variables for html.tpl.php
 *
 * @see system_elements()
 * @see html.tpl.php
 */
function twitter_bootstrap_preprocess_html(&$vars) {
  $js = preg_split( '/\r\n|\r|\n/', twitter_bootstrap_theme_get_setting('twitter_bootstrap_js_files'));
  foreach($js as $file) {
    drupal_add_js($file, array('scope' => 'footer'));
  }
  
  $css = preg_split( '/\r\n|\r|\n/', twitter_bootstrap_theme_get_setting('twitter_bootstrap_css_files'));
  foreach($css as $file) {
    drupal_add_css($file, array('type' => 'external'));
  }
}

function twitter_bootstrap_breadcrumb($vars) {
  $breadcrumb = $vars['breadcrumb'];

  if (!empty($breadcrumb)) {
    $breadcrumbs = '<ul class="breadcrumb">';
    
    $count = count($breadcrumb) - 1;
    foreach($breadcrumb as $key => $value) {
      if($count != $key) {
        $breadcrumbs .= '<li>'.$value.'<span class="divider">/</span></li>';
      }else{
        $breadcrumbs .= '<li>'.$value.'</li>';
      }
    }
    $breadcrumbs .= '</ul>';
    
    return $breadcrumbs;
  }
}

/**
 * Preprocess variables for node.tpl.php
 *
 * @see node.tpl.php
 */
function twitter_bootstrap_preprocess_node(&$vars) {
  if($vars['teaser'])
    $vars['classes_array'][] = 'row-fluid';
}

/**
 * Preprocess variables for block.tpl.php
 *
 * @see block.tpl.php
 */
function twitter_bootstrap_preprocess_block(&$vars) {
  //$vars['classes_array'][] = 'row';
}

/**
 * Preprocess variables for page.tpl.php
 *
 * @see page.tpl.php
 */
function twitter_bootstrap_preprocess_page(&$variables) {
  // Add information about the number of sidebars.
  if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
    $variables['columns'] = 3;
  }
  elseif (!empty($variables['page']['sidebar_first'])) {
    $variables['columns'] = 2;
  }
  elseif (!empty($variables['page']['sidebar_second'])) {
    $variables['columns'] = 2;
  }
  else {
    $variables['columns'] = 1;
  }
}  

function twitter_bootstrap_preprocess_form_element(&$vars) {
}

/**
 * Preprocess variables for region.tpl.php
 *
 * @see region.tpl.php
 */
function twitter_bootstrap_preprocess_region(&$vars) {  
  if($vars['region'] == "sidebar_first" || $vars['region'] == "sidebar_second")
    $vars['classes_array'][] = 'span3';
  
  if($vars['region'] == "sidebar_second")
    $vars['classes_array'][] = 'span3';
    
  if($vars['region'] == "highlight")
    $vars['classes_array'][] = 'span12';  
}

/**
 * Returns the correct span class for a region
 */
function _twitter_bootstrap_content_span($columns = 1) {
  switch($columns) {
    case 1:
      $class = 'span12';
      break;
    case 2:
      $class = 'span8';
      break;
    case 3:
      $class = 'span5';
      break;
  }
  
  return $class;
}
