<?php
/**
 * @file
 * alter.inc
 *
 * Contains various implementations of hook_*_alter().
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;

/**
 * Implements hook_css_alter().
 */
function bootstrap_css_alter(&$css) {
  $config = \Drupal::config('bootstrap.settings');
  $theme_path = drupal_get_path('theme', 'bootstrap');
  // Exclude specified CSS files from theme.
  $excludes = $config->get('bootstrap_css_excludes');
  // Add Bootstrap CDN file and overrides.
  $bootstrap_cdn = $config->get('bootstrap_cdn');
  if ($bootstrap_cdn) {
    // Add CDN.
    if (theme_get_setting('bootstrap_bootswatch')) {
      $cdn = '//netdna.bootstrapcdn.com/bootswatch/' . $bootstrap_cdn  . '/' . $config->get('bootstrap_bootswatch') . '/bootstrap.min.css';
    }
    else {
      $cdn = '//netdna.bootstrapcdn.com/bootstrap/' . $bootstrap_cdn  . '/css/bootstrap.min.css';
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
    $css = array_diff_key($css, array_combine($excludes, $excludes));
  }
}

/**
 * Implements hook_js_alter().
 */
function bootstrap_js_alter(&$js) {
  $config = \Drupal::config('bootstrap.settings');

  // Exclude specified JavaScript files from theme.
  // @todo add excludes.
  $excludes = $config->get('bootstrap_excludes_js');

  $theme_path = drupal_get_path('theme', 'bootstrap');

  // Add or replace JavaScript files when matching paths are detected.
  // Replacement files must begin with '_', like '_node.js'.
  $files = _bootstrap_file_scan_directory($theme_path . '/js', '/\.js$/');
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
      if (!\Drupal::moduleHandler()->moduleExists($matches[1])) {
        continue;
      }
      else {
        $path = str_replace('modules/' . $matches[1], drupal_get_path('module', $matches[1]), $path);
      }
    }
    // Path should always exist to either add or replace JavaScript file.
    if (!empty($path)) {
      // Replace file.
      if ($replace) {
        $js[$file->uri] = drupal_js_defaults($file->uri);
        unset($js['core/' . $path]);
      }
      // Add file.
      else {
        $js[$file->uri] = drupal_js_defaults($file->uri);
      }
    }
  }

  if (!empty($excludes)) {
    $js = array_diff_key($js, array_combine($excludes, $excludes));
  }

  // Add Bootstrap settings.
  $js['drupalSettings']['data']['bootstrap'] = array(
    'anchorsFix' => $config->get('bootstrap_anchors_fix'),
    'anchorsSmoothScrolling' => $config->get('bootstrap_anchors_smooth_scrolling'),
    'formHasError' => (int) $config->get('bootstrap_forms_has_error_value_toggle'),
    'popoverEnabled' => $config->get('bootstrap_popover_enabled'),
    'popoverOptions' => array(
      'animation' => (int) $config->get('bootstrap_popover_animation'),
      'html' => (int) $config->get('bootstrap_popover_html'),
      'placement' => $config->get('bootstrap_popover_placement'),
      'selector' => $config->get('bootstrap_popover_selector'),
      'trigger' => implode(' ', array_filter(array_values((array) $config->get('bootstrap_popover_trigger')))),
      'triggerAutoclose' => (int) $config->get('bootstrap_popover_trigger_autoclose'),
      'title' => $config->get('bootstrap_popover_title'),
      'content' => $config->get('bootstrap_popover_content'),
      'delay' => (int) $config->get('bootstrap_popover_delay'),
      'container' => $config->get('bootstrap_popover_container'),
    ),
    'tooltipEnabled' => $config->get('bootstrap_tooltip_enabled'),
    'tooltipOptions' => array(
      'animation' => (int) $config->get('bootstrap_tooltip_animation'),
      'html' => (int) $config->get('bootstrap_tooltip_html'),
      'placement' => $config->get('bootstrap_tooltip_placement'),
      'selector' => $config->get('bootstrap_tooltip_selector'),
      'trigger' => implode(' ', array_filter(array_values((array) $config->get('bootstrap_tooltip_trigger')))),
      'delay' => (int) $config->get('bootstrap_tooltip_delay'),
      'container' => $config->get('bootstrap_tooltip_container'),
    ),
  );

  // Add CDN.
  if ($config->get('bootstrap_cdn')) {
    $cdn = '//netdna.bootstrapcdn.com/bootstrap/' . $config->get('bootstrap_cdn')  . '/js/bootstrap.min.js';
    $js[$cdn] = drupal_js_defaults();
    $js[$cdn]['data'] = $cdn;
    $js[$cdn]['type'] = 'external';
    $js[$cdn]['every_page'] = TRUE;
    $js[$cdn]['scope'] = 'footer';
    $js[$cdn]['weight'] = -100;
  }
}

/**
 * Implements hook_theme_suggestions_alter().
 */
function bootstrap_theme_suggestions_alter(array &$suggestions, array $variables, $hook) {
  if ($hook == 'details') {
    $suggestions[] = 'bootstrap_panel';
  }

  if ($variables['elements']['#region'] == 'navigation') {
    $suggestions[] = 'region__no_wrapper';
  }
}

/**
 * Implements hook_form_alter().
 */
function bootstrap_form_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  if ($form_id) {
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
        $form['actions']['submit']['#attributes']['class'][] = 'visually-hidden';
        $form['search_block_form']['#theme_wrappers'] = array('bootstrap_search_form_wrapper');

        // Apply a clearfix so the results don't overflow onto the form.
        $form['#attributes']['class'][] = 'content-search';
        break;

      case 'image_style_form':
        $form['effects']['new']['new']['#input_group_button'] = TRUE;
        break;

      case 'path_admin_filter_form':
        $form['basic']['filter']['#input_group_button'] = TRUE;
        break;
    }
  }
}

/**
 * Implements hook_icon_bundle_list_alter().
 */
function bootstrap_icon_bundle_list_alter(&$build, $bundle) {
  $config = \Drupal::config('bootstrap.settings');
  if ($config->get('bootstrap_tooltip_enabled')) {
    foreach ($build as &$icon) {
      $icon['#attributes']['data-toggle'] = 'tooltip';
      $icon['#attributes']['data-placement'] = 'bottom';
    }
  }
}

/**
 * Implements hook_menu_local_tasks_alter().
 */
function bootstrap_menu_local_tasks_alter(&$data, &$router_item, $root_path) {
  if (!empty($data['actions']['output'])) {
    $items = array();
    foreach ($data['actions']['output'] as $item) {
      $items[] = array(
        'data' => $item,
      );
    }
    $data['actions']['output'] = array(
      '#theme' => 'item_list__action_links',
      '#items' => $items,
      '#attributes' => array(
        'class' => array('action-links'),
      ),
    );
  }
}

/**
 * Implements hook_js_callback_filter_xss_alter().
 */
function bootstrap_js_callback_filter_xss_alter(array &$allowed_tags = array()) {
  $allowed_tags[] = 'button';
}

/**
 * Include #process callbacks for elements.
 */
bootstrap_include('bootstrap', 'theme/process.inc');
