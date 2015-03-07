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
 * Include #pre_render callbacks for elements.
 */
bootstrap_include('bootstrap', 'includes/pre-render.inc');

/**
 * Include #process callbacks for elements.
 */
bootstrap_include('bootstrap', 'includes/process.inc');

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
 * Implements hook_element_info_alter().
 *
 * @todo This currently does not work due to the following issue.
 * @see https://www.drupal.org/node/2024217#comment-9696043
 */
function bootstrap_element_info_alter(&$info) {
  $theme_key = \Drupal::theme()->getActiveTheme()->getName();

  $cid = "theme_registry:bootstrap:element_info";
  $element_info = array();
  if (($cache = \Drupal::cache()->get($cid)) && !empty($cache->data)) {
    $element_info = $cache->data;
  }

  if (!isset($element_info[$theme_key])) {
    $themes = _bootstrap_get_base_themes($theme_key, TRUE);
    foreach ($themes as $theme) {
      foreach ($info as $type => &$element) {
        // Ensure elements that have a base type with the #input set match.
        if (isset($element['#base_type']) && isset($info[$element['#base_type']]['#input'])) {
          $element['#input'] = $info[$element['#base_type']]['#input'];
        }

        // Setup a default "icon" variable. This allows #icon to be passed
        // to every template and theme function.
        // @see https://drupal.org/node/2219965
        if (!isset($element['#icon'])) {
          $element['#icon'] = NULL;
        }
        if (!isset($element['#icon_position'])) {
          $element['#icon_position'] = 'before';
        }

        $properties = array(
          '#process' => array(
            'form_process',
            'form_process_' . $type,
          ),
          '#pre_render' => array(
            'pre_render',
            'pre_render_' . $type,
          ),
        );
        foreach ($properties as $property => $callbacks) {
          foreach ($callbacks as $callback) {
            $function = $theme . '_' . $callback;
            if (function_exists($function)) {
              // Replace direct core function correlation.
              if (!empty($element[$property]) && ($key = array_search($callback, $element[$property])) !== FALSE) {
                $element[$property][$key] = $function;
              }
              // Check for a "form_" prefix instead (for #pre_render).
              else if (!empty($element[$property]) && ($key = array_search('form_' . $callback, $element[$property])) !== FALSE) {
                $element[$property][$key] = $function;
              }
              // Otherwise, append the function.
              else {
                $element[$property][] = $function;
              }
            }
          }
        }
      }
    }

    // Cache the element information.
    $element_info[$theme_key] = $info;
    \Drupal::cache()->set($cid, $element_info);
  }

  // Set the element info to the cached processed data.
  $info = $element_info[$theme_key];
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
    if ($file->name == 'bootstrap' || $file->name == 'bootstrap.admin') {
      continue;
    }
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
    if (!empty($path) && array_key_exists($path, $js)) {
      $bootstrap_js_defaults = array(
        'type' => 'file',
        'group' => JS_DEFAULT,
        'every_page' => FALSE,
        'weight' => 0,
        'scope' => 'footer',
        'cache' => TRUE,
        'preprocess' => TRUE,
        'attributes' => array(),
        'version' => NULL,
        'data' => $file->uri,
        'browsers' => array(),
      );
      // Replace file.
      if ($replace) {
        $js[$file->uri] = $bootstrap_js_defaults;
        unset($js['core/' . $path]);
      }
      // Add file.
      else {
        $js[$file->uri] = $bootstrap_js_defaults;
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
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function bootstrap_theme_suggestions_input_alter(array &$suggestions, array $variables) {
  if (!empty($variables['element']['#is_button'])) {
    $suggestions[] = 'input__button';
  }
}

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function bootstrap_theme_suggestions_fieldset_alter(array &$suggestions, array $variables) {
  $suggestions[] = 'bootstrap_panel';
}

/**
 * Implements hook_form_alter().
 */
function bootstrap_form_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  $config = \Drupal::config('bootstrap.settings');

  if ($form_id) {
    switch ($form_id) {
      case 'system_theme_settings':
        // Include the settings form here.
        bootstrap_include('bootstrap', 'includes/settings.inc');
        _bootstrap_settings_form($form, $form_state);
        break;

      case 'search_form':
        // Add a clearfix class so the results don't overflow onto the form.
        $form['#attributes']['class'][] = 'clearfix';

        // Remove container-inline from the container classes.
        $form['basic']['#attributes']['class'] = array();

        // Hide the default button from display.
        $form['basic']['submit']['#attributes']['class'][] = 'visually-hidden';
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

  // Convert delete links in forms to Bootstrap buttons with delete links.
  // @TODO revisit to see if there's a better place to handle this.
  if (!empty($form['actions']['delete']['#type']) && $form['actions']['delete']['#type'] === 'link') {
    $form['actions']['delete']['#attributes']['class'][] = 'btn';
    if ($config->get('bootstrap_button_colorize')) {
      $form['actions']['delete']['#attributes']['class'][] = 'btn-danger';
    }
    if ($config->get('bootstrap_button_iconize')) {
      $form['actions']['delete']['#options']['html'] = 'true';
      $form['actions']['delete']['#title'] = _bootstrap_icon('trash') . ' Delete';
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
