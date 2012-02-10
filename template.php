<?php

global $theme_key;

include_once(dirname(__FILE__) . '/includes/twitter_bootstrap.inc');
include_once(dirname(__FILE__) . '/includes/modules/theme.inc');
include_once(dirname(__FILE__) . '/includes/modules/pager.inc');
include_once(dirname(__FILE__) . '/includes/modules/form.inc');
include_once(dirname(__FILE__) . '/includes/modules/admin.inc');


// Load module include files
$modules = module_list();
foreach ($modules as $module) {
  if (is_file(drupal_get_path('theme', $theme_key) . '/includes/modules/' . str_replace('_', '-', $module) . '.inc')) {
    include_once(drupal_get_path('theme', $theme_key) . '/includes/modules/' . str_replace('_', '-', $module) . '.inc');
  }    
}

// Lame attempt to check if bootstrap is present
// This needs to be doen correctly one time
if (!is_file(drupal_get_path('theme', $theme_key) . '/bootstrap/css/bootstrap.min.css')) {
  drupal_set_message(t("Make sure the bootstrap core files are under the theme directory [theme-dir]/bootstrap/.."), 'error');
}  

/**
 * hook_theme() 
 */
function twitter_bootstrap_theme() {
  return array(
    'twitter_bootstrap_links' => array(
      'variables' => array('links' => array(), 'attributes' => array(), 'heading' => NULL),
    ),
    'twitter_bootstrap_btn_dropdown' => array(
      'variables' => array('links' => array(), 'attributes' => array(), 'type' => NULL),
    ), 
  );
}

/**
 * Preprocess variables for html.tpl.php
 *
 * @see system_elements()
 * @see html.tpl.php
 */
function twitter_bootstrap_preprocess_html(&$vars) {
  if (module_exists('twitter_bootstrap_ui')) {
    $js = preg_split( '/\r\n|\r|\n/', variable_get('twitter_bootstrap_ui_js_files', twitter_bootstrap_theme_get_setting('twitter_bootstrap_js_files')));
    $css = preg_split( '/\r\n|\r|\n/', variable_get('twitter_bootstrap_ui_css_files', twitter_bootstrap_theme_get_setting('twitter_bootstrap_css_files')));
  }else{
    $js = preg_split( '/\r\n|\r|\n/', twitter_bootstrap_theme_get_setting('twitter_bootstrap_js_files'));
    $css = preg_split( '/\r\n|\r|\n/', twitter_bootstrap_theme_get_setting('twitter_bootstrap_css_files'));
  }
  
  foreach($js as $file) {
    drupal_add_js($file, array('scope' => 'footer', 'group' => JS_THEME));
  }
  
  foreach($css as $file) {
    drupal_add_css($file, array('type' => 'external', 'group' => CSS_THEME));
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
  
  // Our custom search because its cool :)
  $variables['search'] = FALSE;
  if(theme_get_setting('toggle_search'))
    $variables['search'] = drupal_get_form('_twitter_bootstrap_search_form');

  // Primary nav
  $variables['primary_nav'] = FALSE;
  if($variables['main_menu']) {
    // Build links
    $tree = menu_tree_page_data(variable_get('menu_main_links_source', 'main-menu'));
    $variables['main_menu'] = twitter_bootstrap_menu_navigation_links($tree);
    
    // Build list
    $variables['primary_nav'] = theme('twitter_bootstrap_links', array(
      'links' => $variables['main_menu'],
      'attributes' => array(
        'id' => 'main-menu',
        'class' => array('nav'),
      ),
    ));
  }
  
  // Secondary nav
  $variables['secondary_nav'] = FALSE;
  if($variables['secondary_menu']) {
    $secondary_menu = menu_load(variable_get('menu_secondary_links_source', 'user-menu'));
    
    // Build links
    $tree = menu_tree_page_data($secondary_menu['menu_name']);
    $variables['secondary_menu'] = twitter_bootstrap_menu_navigation_links($tree);
    
    // Build list
    $variables['secondary_nav'] = theme('twitter_bootstrap_btn_dropdown', array(
      'links' => $variables['secondary_menu'],
      'label' => $secondary_menu['title'],
      'type' => 'success',
      'attributes' => array(
        'id' => 'user-menu',
        'class' => array('pull-right'),
      ),
    ));
  }
  
  // Replace tabs with dropw down version
  $variables['tabs'] = _twitter_bootstrap_local_tasks($variables['tabs']['#primary']);
}

function _twitter_bootstrap_search_form($form, &$form_state) {
  // Get custom search form for now
  $form = search_form($form, &$form_state);

  // Cleanup
  $form['#attributes']['class'][] = 'navbar-search';
  $form['#attributes']['class'][] = 'pull-left';
  $form['basic']['keys']['#title'] = '';
  $form['basic']['keys']['#attributes']['class'][] = 'search-query';
  $form['basic']['keys']['#attributes']['class'][] = 'span2';
  $form['basic']['keys']['#attributes']['placeholder'] = t('Search');
  unset($form['basic']['submit']);
  unset($form['basic']['#type']);
  unset($form['basic']['#attributes']);
  $form += $form['basic'];
  unset($form['basic']);

  return $form;
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
