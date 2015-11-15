<?php
/**
 * @file
 * alter.inc
 *
 * Contains various implementations of hook_*_alter().
 */

use \Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Template\Attribute;
use \Drupal\Core\Render\Element;


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
  // Add CDN assets, if any.
  $provider = bootstrap_setting('cdn_provider');
  if ($cdn_assets = bootstrap_get_cdn_assets('css', $provider)) {
    $cdn_weight = -2.99;
    foreach ($cdn_assets as $cdn_asset) {
      $cdn_weight += .01;
      $css[$cdn_asset] = array(
        'data' => $cdn_asset,
        'type' => 'external',
        'every_page' => TRUE,
        'media' => 'all',
        'preprocess' => FALSE,
        'group' => CSS_AGGREGATE_THEME,
        'browsers' => array('IE' => TRUE, '!IE' => TRUE),
        'weight' => $cdn_weight,
      );
    }

    // Add a specific version and theme CSS overrides file.
    $version = bootstrap_setting('cdn_' . $provider . '_version');
    if (!$version) {
      $version = BOOTSTRAP_VERSION;
    }
    $theme = bootstrap_setting('cdn_' . $provider . '_theme');
    if (!$theme) {
      $theme = 'bootstrap';
    }
    $theme = $theme === 'bootstrap' || $theme === 'bootstrap_theme' ? '' : "-$theme";
    $overrides = "$theme_path/css/$version/overrides$theme.min.css";
    if (file_exists($overrides)) {
      $css[$overrides] = array(
        'data' => $overrides,
        'type' => 'file',
        'every_page' => TRUE,
        'media' => 'all',
        'preprocess' => TRUE,
        'group' => CSS_AGGREGATE_THEME,
        'browsers' => array('IE' => TRUE, '!IE' => TRUE),
        'weight' => -1,
      );
    }
  }
}

/**
 * Implements hook_element_info_alter().
 *
 * @todo This currently does not work due to the following issue.
 * @see https://www.drupal.org/node/2390973
 */
function bootstrap_element_info_alter(&$info) {
  $theme_key = \Drupal::theme()->getActiveTheme()->getName();

  $cid = "theme_registry:bootstrap:element_info";
  $cached = array();
  if (($cache = \Drupal::cache()->get($cid)) && !empty($cache->data)) {
    $cached = $cache->data;
  }

  $themes = _bootstrap_get_base_themes($theme_key, TRUE);
  foreach ($themes as $theme) {
    if (!isset($cached[$theme])) {
      $cached[$theme] = array();
      foreach (array_keys($info) as $type) {
        $element = array();

        // Ensure elements that have a base type with the #input set match.
        if (isset($info[$type]['#base_type']) && isset($info[$type][$info[$type]['#base_type']]['#input'])) {
          $element['#input'] = $info[$info[$type]['#base_type']]['#input'];
        }

        // Replace fieldset theme implementations with bootstrap_panel.
        if (!empty($info[$type]['#theme']) && $info[$type]['#theme'] === 'fieldset') {
          $element['#bootstrap_replace']['#theme'] = 'bootstrap_panel';
        }
        if (!empty($info[$type]['#theme_wrappers']) && array_search('fieldset', $info[$type]['#theme_wrappers']) !== FALSE) {
          $element['#bootstrap_replace']['#theme_wrappers']['fieldset'] = 'bootstrap_panel';
        }

        // Setup a default "icon" variable. This allows #icon to be passed
        // to every template and theme function.
        // @see https://drupal.org/node/2219965
        $element['#icon'] = NULL;
        $element['#icon_position'] = 'before';

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
              if (!empty($info[$type][$property]) && array_search($callback, $info[$type][$property]) !== FALSE) {
                $element['#bootstrap_replace'][$property][$callback] = $function;
              }
              // Check for a "form_" prefix instead (for #pre_render).
              else if (!empty($info[$type][$property]) && array_search('form_' . $callback, $info[$type][$property]) !== FALSE) {
                $element['#bootstrap_replace'][$property]['form_' . $callback] = $function;
              }
              // Otherwise, append the function.
              else {
                $element[$property][] = $function;
              }
            }
          }
        }
        $cached[$theme][$type] = $element;
      }

      // Cache the element information.
      \Drupal::cache()->set($cid, $cached);
    }

    // Merge in each theme's cached element info.
    $info = _bootstrap_element_info_array_merge($info, $cached[$theme]);
  }
}

/**
 * Merges the cached element information into the runtime array.
 *
 * @param array $info
 *   The element info array to merge data into.
 * @param array $cached
 *   The cached element info data array to merge from.
 *
 * @return array
 *   The altered element info array.
 */
function _bootstrap_element_info_array_merge($info, $cached) {
  foreach ($cached as $type => $element) {
    $replacement_data = isset($element['#bootstrap_replace']) ? $element['#bootstrap_replace'] : array();
    unset($element['#bootstrap_replace']);
    foreach ($element as $property => $data) {
      if (is_array($data)) {
        if (!isset($info[$type][$property])) {
          $info[$type][$property] = array();
        }
        // Append the values if not already in the array.
        foreach ($data as $key => $value) {
          if (!in_array($value, $info[$type][$property])) {
            $info[$type][$property][] = $value;
          }
        }
      }
      // Create the property, if not already set.
      else if (!isset($info[$type][$property])) {
        $info[$type][$property] = $data;
      }
    }
    // Replace data, if necessary.
    foreach ($replacement_data as $property => $data) {
      if (is_array($data)) {
        foreach ($data as $needle => $replacement) {
          if (!empty($info[$type][$property]) && ($key = array_search($needle, $info[$type][$property])) !== FALSE) {
            $info[$type][$property][$key] = $replacement;
          }
        }
      }
      // Replace the property with the new data.
      else {
        $info[$type][$property] = $data;
      }
    }
  }

  // Return the altered element info array.
  return $info;
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
        unset($js[$path]);
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

  // Add CDN assets, if any.
  if ($cdn_assets = bootstrap_get_cdn_assets('js')) {
    $cdn_weight = -99.99;
    foreach ($cdn_assets as $cdn_asset) {
      $cdn_weight += .01;
      $js[$cdn_asset] = drupal_js_defaults($cdn_asset);
      $js[$cdn_asset]['type'] = 'external';
      $js[$cdn_asset]['every_page'] = TRUE;
      $js[$cdn_asset]['scope'] = 'footer';
      $js[$cdn_asset]['weight'] = $cdn_weight;
    }
  }
}

/**
 * Implements hook_theme_suggestions_alter().
 */
function bootstrap_theme_suggestions_alter(array &$suggestions, array $variables, $hook) {
  switch ($hook) {
    case 'details':
      $suggestions[] = 'bootstrap_panel';
      break;

    case 'input':
      if (!empty($variables['element']['#is_button'])) {
        $suggestions[] = 'input__button';
      }
      break;
  }
}

/**
 * Implements hook_form_alter().
 */
function bootstrap_form_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  if ($form_id) {
    switch ($form_id) {
      case 'system_theme_settings':
        // Create vertical tabs for global settings (provided by core or other
        // contrib modules).
        if (!isset($form['global'])) {
          $form['global'] = array(
            '#type' => 'vertical_tabs',
            '#weight' => -9,
          );
          if (!empty($form_state->getBuildInfo()['args'])) {
            $form['global']['#prefix'] = '<h2><small>' . t('Override Global Settings') . '</small></h2>';
          }
        }

        // Iterate over all child elements and check to see if they should be
        // moved in the global vertical tabs.
        $global_children = Element::children($form);
        foreach ($global_children as $child) {
          if (isset($form[$child]['#type']) && $form[$child]['#type'] === 'details' && !isset($form[$child]['#group'])) {
            $form[$child]['#group'] = 'global';
          }
        }
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

        $form['keys']['#title'] = '';
        $form['keys']['#placeholder'] = (string) t('Search');

        // Hide the default button from display and implement a theme wrapper
        // to add a submit button containing a search icon directly after the
        // input element.
        $form['actions']['submit']['#attributes']['class'][] = 'visually-hidden';

        // Apply a clearfix so the results don't overflow onto the form.
        $form['#attributes']['class'][] = 'content-search';
        break;

      case 'image_style_edit_form':
        $form['effects']['new']['effect']['data']['new']['#input_group_button'] = TRUE;
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
    if (bootstrap_setting('button_colorize')) {
      $form['actions']['delete']['#attributes']['class'][] = 'btn-danger';
    }
    if (bootstrap_setting('button_iconize')) {
      $form['actions']['delete']['#options']['html'] = 'true';
      $trash_link = array(
        '#type' => 'item',
        '#markup' => '<span class="glyphicon glyphicon-trash"></span> ' . t('Delete'),
      );
      $form['actions']['delete']['#title'] = \Drupal::service('renderer')->render($trash_link);
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
 * Implements hook_page_attachments_alter().
 */
function bootstrap_page_attachments_alter(&$variables) {
  $config = \Drupal::config('bootstrap.settings');
  $variables['#attached']['drupalSettings']['bootstrap'] = array(
      'anchorsFix' => bootstrap_setting('anchors_fix'),
      'anchorsSmoothScrolling' => bootstrap_setting('anchors_smooth_scrolling'),
      'formHasError' => (int) bootstrap_setting('forms_has_error_value_toggle'),
      'popoverEnabled' => bootstrap_setting('popover_enabled'),
      'popoverOptions' => array(
        'animation' => (int) bootstrap_setting('popover_animation'),
        'html' => (int) bootstrap_setting('popover_html'),
        'placement' => bootstrap_setting('popover_placement'),
        'selector' => bootstrap_setting('popover_selector'),
        'trigger' => implode(' ', array_filter(array_values((array) bootstrap_setting('popover_trigger')))),
        'triggerAutoclose' => (int) bootstrap_setting('popover_trigger_autoclose'),
        'title' => bootstrap_setting('popover_title'),
        'content' => bootstrap_setting('popover_content'),
        'delay' => (int) bootstrap_setting('popover_delay'),
        'container' => bootstrap_setting('popover_container'),
      ),
      'tooltipEnabled' => bootstrap_setting('tooltip_enabled'),
      'tooltipOptions' => array(
        'animation' => (int) bootstrap_setting('tooltip_animation'),
        'html' => (int) bootstrap_setting('tooltip_html'),
        'placement' => bootstrap_setting('tooltip_placement'),
        'selector' => bootstrap_setting('tooltip_selector'),
        'trigger' => implode(' ', array_filter(array_values((array) bootstrap_setting('tooltip_trigger')))),
        'delay' => (int) bootstrap_setting('tooltip_delay'),
        'container' => bootstrap_setting('tooltip_container'),
      ),
  );
}
