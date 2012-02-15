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
function twitter_bootstrap_preprocess_html(&$variables) {
  global $theme_key;
  $theme_path = drupal_get_path('theme', $theme_key);
  
  // Try to load the library, otherwise use dropped in files
  if (!module_exists('twitter_bootstrap_ui') ||
    (($library = libraries_load('twitter_bootstrap', 'minified')) && empty($library['loaded']))) {
   
    // Define our needed files
    $css = array(
      $theme_path .'/bootstrap/css/bootstrap.min.css',
      $theme_path .'/bootstrap/css/bootstrap-responsive.min.css',
    );
    
    $js = array(
      $theme_path .'/bootstrap/js/bootstrap.min.js',
    );
    
    // Check if files are there
    $files = $css + $js;
    foreach($files as $file) {
      if (!is_file($file)) {
        drupal_set_message(t("Make sure the bootstrap core files are under the theme directory [theme-dir]/bootstrap/..<br> Or install twitter_bootstrap_ui to use the libraries API"), 'error');
        break;
      }
    }
    
    foreach($js as $file) {
      drupal_add_js($file, array('scope' => 'footer', 'group' => JS_THEME));
    }
    
    foreach($css as $file) {
      drupal_add_css($file, array('group' => CSS_THEME));
    }
  }
}

function twitter_bootstrap_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];

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
function twitter_bootstrap_preprocess_node(&$variables) {
  if($variables['teaser'])
    $variables['classes_array'][] = 'row-fluid';
}

/**
 * Preprocess variables for block.tpl.php
 *
 * @see block.tpl.php
 */
function twitter_bootstrap_preprocess_block(&$variables, $hook) {
  //$variables['classes_array'][] = 'row';
  // Use a bare template for the page's main content.
  if ($variables['block_html_id'] == 'block-system-main') {
    $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
  }
  $variables['title_attributes_array']['class'][] = 'block-title';
}

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function twitter_bootstrap_process_block(&$variables, $hook) {
  // Drupal 7 should use a $title variable instead of $block->subject.
  $variables['title'] = $variables['block']->subject;
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



/**
 * Preprocess variables for region.tpl.php
 *
 * @see region.tpl.php
 */
function twitter_bootstrap_preprocess_region(&$variables, $hook) {
  // Set span in wrapper for now
  /*
  if($variables['region'] == "sidebar_first")
    $variables['classes_array'][] = 'span3';
  
  if($variables['region'] == "sidebar_second")
    $variables['classes_array'][] = 'span3';
    
  if($variables['region'] == "highlight")
    $variables['classes_array'][] = 'span12';
  */
  
  if ($variables['region'] == 'content') {
    $variables['theme_hook_suggestions'][] = 'region__no_wrapper';
  }
  
  // Me likes
  if($variables['region'] == "sidebar_first")
    $variables['classes_array'][] = 'well';
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
      $class = 'span9';
      break;
    case 3:
      $class = 'span6';
      break;
  }
  
  return $class;
}