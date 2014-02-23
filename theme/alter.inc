<?php
/**
 * @file
 * alter.inc
 *
 * Contains various implementations of hook_*_alter().
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_css_alter().
 */
function bootstrap_css_alter(&$css) {
  $config = \Drupal::config('bootstrap.settings');
  $theme_path = drupal_get_path('theme', 'bootstrap');
  // Exclude specified CSS files from theme.
  $excludes = $config->get('excludes.css');
  // Add Bootstrap CDN file and overrides.
  if ($config->get('cdn')) {
    // Add CDN.
    if ($config->get('bootswatch')) {
      $cdn = '//netdna.bootstrapcdn.com/bootswatch/' . $config->get('cdn')  . '/' . $config->get('bootswatch') . '/bootstrap.min.css';
    }
    else {
      $cdn = '//netdna.bootstrapcdn.com/bootstrap/' . $config->get('cdn')  . '/css/bootstrap.min.css';
    }
    $css[$cdn] = array(
      'data' => $cdn,
      'type' => 'external',
      'every_page' => TRUE,
      'media' => 'all',
      'preprocess' => FALSE,
      'group' => CSS_AGGREGATE_THEME,
      'browsers' => array('IE' => TRUE, '!IE' => TRUE),
      'weight' => -2,
    );
    // Add overrides.
    $override = $theme_path . '/css/overrides.css';
    $css[$override] = array(
      'data' => $override,
      'type' => 'file',
      'every_page' => TRUE,
      'media' => 'all',
      'preprocess' => TRUE,
      'group' => CSS_AGGREGATE_THEME,
      'browsers' => array('IE' => TRUE, '!IE' => TRUE),
      'weight' => -1,
    );
  }
  if (!empty($excludes)) {
    $css = array_diff_key($css, drupal_map_assoc($excludes));
  }
}

/**
 * Implements hook_element_info_alter().
 */
function bootstrap_element_info_alter(&$elements) {
  foreach ($elements as &$element) {
    // Process all elements.
    $element['#process'][] = '_bootstrap_process_element';
    // Process input elements.
    if (!empty($element['#input'])) {
      $element['#process'][] = '_bootstrap_process_input';
    }
    // Process core's fieldset element.
    if (!empty($element['#type']) && $element['#type'] === 'fieldset') {
      $element['#theme_wrappers'] = array('bootstrap_panel');
    }
    if (!empty($element['#theme']) && $element['#theme'] === 'fieldset') {
      $element['#theme'] = 'bootstrap_panel';
    }
    // Replace #process function.
    if (!empty($element['#process']) && ($key = array_search('form_process_fieldset', $element['#process'])) !== FALSE) {
      $element['#process'][$key] = '_bootstrap_process_fieldset';
    }
    // Replace #pre_render function.
    if (!empty($element['#pre_render']) && ($key = array_search('form_pre_render_fieldset', $element['#pre_render'])) !== FALSE) {
      $element['#pre_render'][$key] = '_bootstrap_pre_render_fieldset';
    }
    // Replace #theme_wrappers function.
    if (!empty($element['#theme_wrappers']) && ($key = array_search('fieldset', $element['#theme_wrappers'])) !== FALSE) {
      $element['#theme_wrappers'][$key] = 'bootstrap_panel';
    }
  }
}

/**
 * Implements hook_form_alter().
 */
function bootstrap_form_alter(array &$form, array &$form_state = array(), $form_id = NULL) {
  if ($form_id) {
    // IDs of forms that should be ignored. Make this configurable?
    // @todo is this still needed?
    $form_ids = array(
      'node_form',
      'system_site_information_settings',
      'user_profile_form',
      'node_delete_confirm',
    );
    // Only wrap in container for certain form.
    if (!in_array($form_id, $form_ids) && !isset($form['#node_edit_form']) && isset($form['actions']) && isset($form['actions']['#type']) && ($form['actions']['#type'] == 'actions')) {
      $form['actions']['#theme_wrappers'] = array();
    }

    switch ($form_id) {
      case 'system_theme_settings':
        // Include the settings form here.
        bootstrap_include('bootstrap', 'theme/settings.inc');
        _bootstrap_settings_form($form, $form_state);
        break;

      case 'search_form':
        // Add a clearfix class so the results don't overflow onto the form.
        $form['#attributes']['class'][] = 'clearfix';

        // Remove container-inline from the container classes.
        $form['basic']['#attributes']['class'] = array();

        // Hide the default button from display.
        $form['basic']['submit']['#attributes']['class'][] = 'element-invisible';

        // Implement a theme wrapper to add a submit button containing a search
        // icon directly after the input element.
        $form['basic']['keys']['#theme_wrappers'] = array('bootstrap_search_form_wrapper');
        $form['basic']['keys']['#title'] = '';
        $form['basic']['keys']['#attributes']['placeholder'] = t('Search');
        break;

      case 'search_block_form':
        $form['#attributes']['class'][] = 'form-search';

        $form['search_block_form']['#title'] = '';
        $form['search_block_form']['#attributes']['placeholder'] = t('Search');

        // Hide the default button from display and implement a theme wrapper
        // to add a submit button containing a search icon directly after the
        // input element.
        $form['actions']['submit']['#attributes']['class'][] = 'element-invisible';
        $form['search_block_form']['#theme_wrappers'] = array('bootstrap_search_form_wrapper');

        // Apply a clearfix so the results don't overflow onto the form.
        $form['#attributes']['class'][] = 'content-search';
        break;
    }

  }
}

/**
 * Implements hook_js_alter().
 */
function bootstrap_js_alter(&$js) {
  $config = \Drupal::config('bootstrap.settings');
  // Exclude specified JavaScript files from theme.
  $excludes = $config->get('excludes.js');

  $theme_path = drupal_get_path('theme', 'bootstrap');

  // Add or replace JavaScript files when matching paths are detected.
  // Replacement files must begin with '_', like '_node.js'.
  $files = file_scan_directory($theme_path . '/js', '/\.js$/');
  foreach ($files as $file) {
    $path = str_replace($theme_path . '/js/', '', $file->uri);
    // Detect if this is a replacement file.
    $replace = FALSE;
    if (preg_match('/^[_]/', $file->filename)) {
      $replace = TRUE;
      $path = dirname($path) . '/' . preg_replace('/^[_]/', '', $file->filename);
    }
    $matches = array();
    if (preg_match('/^modules\/([^\/]*)/', $path, $matches)) {
      if (!module_exists($matches[1])) {
        continue;
      }
      else {
        $path = str_replace('modules/' . $matches[1], drupal_get_path('module', $matches[1]), $path);
      }
    }
    // Path should always exist to either add or replace JavaScript file.
    if (!empty($js[$path])) {
      // Replace file.
      if ($replace) {
        $js[$file->uri] = $js[$path];
        $js[$file->uri]['data'] = $file->uri;
        unset($js[$path]);
      }
      // Add file.
      else {
        $js[$file->uri] = drupal_js_defaults($file->uri);
        $js[$file->uri]['group'] = JS_THEME;
      }
    }
  }

  // Always add bootstrap.js last.
  $bootstrap = $theme_path . '/js/bootstrap.js';
  $js[$bootstrap] = drupal_js_defaults($bootstrap);
  $js[$bootstrap]['group'] = JS_THEME;
  $js[$bootstrap]['scope'] = 'footer';

  if (!empty($excludes)) {
    $js = array_diff_key($js, drupal_map_assoc($excludes));
  }

  // Add Bootstrap settings.
  $js['settings']['data'][]['bootstrap'] = array(
    'anchorsFix' => $config->get('anchors_fix'),
    'anchorsSmoothScrolling' => $config->get('anchors_smooth_scrolling'),
    'popoverEnabled' => $config->get('popover_enabled'),
    'popoverOptions' => array(
      'animation' => (int) $config->get('popover_animation'),
      'html' => (int) $config->get('popover_html'),
      'placement' => $config->get('popover_placement'),
      'selector' => $config->get('popover_selector'),
      'trigger' => implode(' ', array_filter(array_values((array) $config->get('popover_trigger')))),
      'title' => $config->get('popover_title'),
      'content' => $config->get('popover_content'),
      'delay' => (int) $config->get('popover_delay'),
      'container' => $config->get('popover_container'),
    ),
    'tooltipEnabled' => $config->get('tooltip_enabled'),
    'tooltipOptions' => array(
      'animation' => (int) $config->get('tooltip_animation'),
      'html' => (int) $config->get('tooltip_html'),
      'placement' => $config->get('tooltip_placement'),
      'selector' => $config->get('tooltip_selector'),
      'trigger' => implode(' ', array_filter(array_values((array) $config->get('tooltip_trigger')))),
      'delay' => (int) $config->get('tooltip_delay'),
      'container' => $config->get('tooltip_container'),
    ),
  );

  // Add CDN.
  if ($config->get('cdn')) {
    $cdn = '//netdna.bootstrapcdn.com/bootstrap/' . $config->get('cdn')  . '/js/bootstrap.min.js';
    $js[$cdn] = drupal_js_defaults();
    $js[$cdn]['data'] = $cdn;
    $js[$cdn]['type'] = 'external';
    $js[$cdn]['every_page'] = TRUE;
    $js[$cdn]['weight'] = -100;
  }
}

/**
 * Include #pre_render callbacks for elements.
 */
bootstrap_include('bootstrap', 'theme/pre-render.inc');

/**
 * Include #process callbacks for elements.
 */
bootstrap_include('bootstrap', 'theme/process.inc');
