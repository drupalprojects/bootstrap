<?php
/**
 * @file
 * common.inc
 *
 * Contains common functionality for the entire base theme.
 */

/**
 * Auto-rebuild the theme registry during theme development.
 */
if (theme_get_setting('bootstrap_rebuild_registry') && !defined('MAINTENANCE_MODE')) {
  // Rebuild .info data.
  system_rebuild_theme_data();
  // Rebuild theme registry.
  drupal_theme_rebuild();
}

/**
 * Return information from the .info file of a theme (and possible base themes).
 *
 * @param string $theme_key
 *   The machine name of the theme.
 * @param string $key
 *   The key name of the item to return from the .info file. This value can
 *   include "][" to automatically attempt to traverse any arrays.
 * @param bool $base_themes
 *   Recursively search base themes, defaults to TRUE.
 *
 * @return string|array|false
 *   A string or array depending on the type of value and if a base theme also
 *   contains the same $key, FALSE if no $key is found.
 */
function bootstrap_get_theme_info($theme_key = NULL, $key = NULL, $base_themes = TRUE) {
  // If no $theme_key is given, use the current theme if we can determine it.
  if (!isset($theme_key)) {
    $theme_key = !empty($GLOBALS['theme_key']) ? $GLOBALS['theme_key'] : FALSE;
  }
  if ($theme_key) {
    $themes = list_themes();
    if (!empty($themes[$theme_key])) {
      $theme = $themes[$theme_key];
      // If a key name was specified, return just that array.
      if ($key) {
        $value = FALSE;
        // Recursively add base theme values.
        if ($base_themes && isset($theme->base_themes)) {
          foreach (array_keys($theme->base_themes) as $base_theme) {
            $value = bootstrap_get_theme_info($base_theme, $key);
          }
        }
        if (!empty($themes[$theme_key])) {
          $info = $themes[$theme_key]->info;
          // Allow array traversal.
          $keys = explode('][', $key);
          foreach ($keys as $parent) {
            if (isset($info[$parent])) {
              $info = $info[$parent];
            }
            else {
              $info = FALSE;
            }
          }
          if (is_array($value)) {
            if (!empty($info)) {
              if (!is_array($info)) {
                $info = array($info);
              }
              $value = drupal_array_merge_deep($value, $info);
            }
          }
          else {
            if (!empty($info)) {
              if (empty($value)) {
                $value = $info;
              }
              else {
                if (!is_array($value)) {
                  $value = array($value);
                }
                if (!is_array($info)) {
                  $info = array($info);
                }
                $value = drupal_array_merge_deep($value, $info);
              }
            }
          }
        }
        return $value;
      }
      // If no info $key was specified, just return the entire info array.
      return $theme->info;
    }
  }
  return FALSE;
}

/**
 * Helper function for including theme files.
 *
 * @param string $theme
 *   Name of the theme to use for base path.
 * @param string $path
 *   Path relative to $theme.
 */
function bootstrap_include($theme, $path) {
  static $themes = array();
  if (!isset($themes[$theme])) {
    $themes[$theme] = drupal_get_path('theme', $theme);
  }
  if ($themes[$theme] && ($file = DRUPAL_ROOT . '/' . $themes[$theme] . '/' . $path) && file_exists($file)) {
    include_once $file;
  }
}

/**
 * Theme a Bootstrap Glyphicon.
 *
 * @param string $name
 *   The icon name, minus the "glyphicon-" prefix.
 *
 * @return string
 *   The icon HTML markup.
 */
function _bootstrap_icon($name) {
  $output = '';
  // Attempt to use the Icon API module, if enabled and it generates output.
  if (module_exists('icon')) {
    $output = theme('icon', array('bundle' => 'bootstrap', 'icon' => 'glyphicon-' . $name));
  }
  if (empty($output)) {
    // Mimic the Icon API markup.
    $attributes = array(
      'class' => array('icon', 'glyphicon', 'glyphicon-' . $name),
      'aria-hidden' => 'true',
    );
    $output = '<i' . drupal_attributes($attributes) . '></i>';
  }
  return $output;
}

/**
 * Helper function for determining whether an element is a button.
 *
 * @param array $element
 *   A renderable element.
 *
 * @return bool
 *   TRUE or FALSE.
 */
function _bootstrap_is_button($element) {
  return
    !empty($element['#type']) &&
    !empty($element['#value']) && (
      $element['#type'] === 'button' ||
      $element['#type'] === 'submit' ||
      $element['#type'] === 'image_button'
    );
}

/**
 * Helper function for adding colors to button elements.
 *
 * @param array $element
 *   The form element, passed by reference.
 */
function _bootstrap_colorize_button(&$element) {
  if (_bootstrap_is_button($element)) {
    // Do not add the class if one is already present in the array.
    $button_classes = array(
      'btn-default',
      'btn-primary',
      'btn-success',
      'btn-info',
      'btn-warning',
      'btn-danger',
      'btn-link',
    );
    $class_intersection = array_intersect($button_classes, $element['#attributes']['class']);
    if (empty($class_intersection)) {
      // Get the matched class.
      $class = _bootstrap_colorize_text($element['#value']);
      // If no particular class matched, use the default style.
      if (!$class) {
        $class = 'default';
      }
      $element['#attributes']['class'][] = 'btn-' . $class;
    }
  }
}

/**
 * Helper function for associating Bootstrap classes on text.
 *
 * @param string $text
 *   Text string to search against.
 *
 * @return string
 *   The Bootstrap class associated with text matching or FALSE if no match.
 */
function _bootstrap_colorize_text($text) {
  // Text values containing these specific strings, which are matched first.
  $specific_strings = array(
    'primary' => array(
      t('Download feature'),
    ),
    'success' => array(
      t('Add effect'),
      t('Add and configure'),
    ),
    'info' => array(
      t('Save and add'),
      t('Add another item'),
      t('Update style'),
    ),
  );
  // Text values containing these generic strings, which are matches last.
  $generic_strings = array(
    'primary' => array(
      t('Confirm'),
      t('Filter'),
      t('Submit'),
      t('Search'),
    ),
    'success' => array(
      t('Add'),
      t('Create'),
      t('Save'),
      t('Write'),
    ),
    'warning' => array(
      t('Export'),
      t('Import'),
      t('Restore'),
      t('Rebuild'),
    ),
    'info' => array(
      t('Apply'),
      t('Update'),
    ),
    'danger' => array(
      t('Delete'),
      t('Remove'),
    ),
  );
  // Specific matching first.
  foreach ($specific_strings as $class => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return $class;
      }
    }
  }
  // Generic matching last.
  foreach ($generic_strings as $class => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return $class;
      }
    }
  }
  return FALSE;
}

/**
 * Helper function for adding icons to button elements.
 *
 * @param array $element
 *   The form element, passed by reference.
 */
function _bootstrap_iconize_button(&$element) {
  if (_bootstrap_is_button($element) && ($icon = _bootstrap_iconize_text($element['#value']))) {
    $element['#value'] = $icon . ' ' . $element['#value'];
  }
}

/**
 * Helper function for associating Bootstrap icons with text.
 *
 * @param string $text
 *   Text string to search against.
 *
 * @return string
 *   The Bootstrap icon associated with text matching or FALSE if no match.
 */
function _bootstrap_iconize_text($text) {
  // Text values containing these specific strings, which are matched first.
  $specific_strings = array();
  // Text values containing these generic strings, which are matches last.
  $generic_strings = array(
    'cog' => array(
      t('Manage'),
      t('Configure'),
    ),
    'download' => array(
      t('Download'),
    ),
    'export' => array(
      t('Export'),
    ),
    'filter' => array(
      t('Filter'),
    ),
    'import' => array(
      t('Import'),
    ),
    'ok' => array(
      t('Save'),
      t('Update'),
    ),
    'pencil' => array(
      t('Edit'),
    ),
    'plus' => array(
      t('Add'),
      t('Write'),
    ),
    'remove' => array(
      t('Cancel'),
    ),
    'trash' => array(
      t('Delete'),
      t('Remove'),
    ),
    'upload' => array(
      t('Upload'),
    ),
  );
  // Specific matching first.
  foreach ($specific_strings as $icon => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return _bootstrap_icon($icon);
      }
    }
  }
  // Generic matching last.
  foreach ($generic_strings as $icon => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return _bootstrap_icon($icon);
      }
    }
  }
  return FALSE;
}
