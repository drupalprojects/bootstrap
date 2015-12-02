<?php
/**
 * @file
 * This contains deprecated functions that will be removed in a future release.
 */

use Drupal\bootstrap\BaseTheme;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Element;
use Drupal\Component\Utility\NestedArray;

/**
 * The current supported Bootstrap framework major version number.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   echo BOOTSTRAP_VERSION_MAJOR;
 *
 *   // After.
 *   echo substr(\Drupal\bootstrap\Bootstrap::VERSION, 0, 1);
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::FRAMEWORK_VERSION
 */
define('BOOTSTRAP_VERSION_MAJOR', substr(Bootstrap::FRAMEWORK_VERSION, 0, 1));

/**
 * The current supported Bootstrap framework minor version number.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   echo BOOTSTRAP_VERSION_MINOR;
 *
 *   // After.
 *   echo substr(\Drupal\bootstrap\Bootstrap::VERSION, 2, 1);
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::FRAMEWORK_VERSION
 */
define('BOOTSTRAP_VERSION_MINOR', substr(Bootstrap::FRAMEWORK_VERSION, 2, 1));

/**
 * The current supported Bootstrap framework patch version number.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   echo BOOTSTRAP_VERSION_PATCH;
 *
 *   // After.
 *   echo substr(\Drupal\bootstrap\Bootstrap::VERSION, 4, 1);
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::FRAMEWORK_VERSION
 */
define('BOOTSTRAP_VERSION_PATCH', substr(Bootstrap::FRAMEWORK_VERSION, 4, 1));

/**
 * The current supported Bootstrap framework version.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   echo BOOTSTRAP_VERSION;
 *
 *   // After.
 *   echo \Drupal\bootstrap\Bootstrap::VERSION;
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::FRAMEWORK_VERSION
 */
define('BOOTSTRAP_VERSION', Bootstrap::FRAMEWORK_VERSION);

/**
 * Wrapper for the core file_scan_directory() function.
 *
 * Finds all files that match a given mask in a given directory and then caches
 * the results. A general site cache clear will force new scans to be initiated
 * for already cached directories.
 *
 * @param string $dir
 *   The base directory or URI to scan, without trailing slash.
 * @param string $mask
 *   The preg_match() regular expression of the files to find.
 * @param array $options
 *   Additional options to pass to file_scan_directory().
 *
 * @return array
 *   An associative array (keyed on the chosen key) of objects with 'uri',
 *   'filename', and 'name' members corresponding to the matching files.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $files = _bootstrap_file_scan_directory($theme_path . '/js', '/\.js$/');
 *
 *   // After.
 *   $theme = Bootstrap::getTheme();
 *   $files = $theme->fileScan('/\.js$/', $theme->getPath(). '/js');
 * @endcode
 *
 * @see \Drupal\bootstrap\Theme::fileScan()
 * @see file_scan_directory()
 */
function _bootstrap_file_scan_directory($dir, $mask, array $options = []) {
  Bootstrap::deprecated();
  return BaseTheme::getTheme()->fileScan($mask, $dir, $options);
}

/**
 * Returns a list of base themes for active or provided theme.
 *
 * @param string $theme_key
 *   The machine name of the theme to check, if not set the active theme name
 *   will be used.
 * @param bool $include_theme_key
 *   Whether to append the returned list with $theme_key.
 *
 * @return array
 *   An indexed array of base themes.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before (including active theme).
 *   $base_themes = _bootstrap_get_base_themes(NULL, TRUE);
 *
 *   // After (including active theme).
 *   $base_themes = array_keys(Bootstrap::getTheme()->getAncestry());
 *
 *   // Before (excluding active theme).
 *   $base_themes = _bootstrap_get_base_themes('my_subtheme');
 *
 *   // After (excluding active theme).
 *   $base_themes = array_keys(Bootstrap::getTheme('my_subtheme')->getAncestry());
 *   array_pop($base_themes);
 * @endcode
 *
 * @see \Drupal\bootstrap\Theme::getAncestry()
 */
function _bootstrap_get_base_themes($theme_key = NULL, $include_theme_key = FALSE) {
  Bootstrap::deprecated();
  $themes = array_keys(BaseTheme::getTheme($theme_key)->getAncestry());
  if (!$include_theme_key) {
    array_pop($themes);
  }
  return $themes;
}

/**
 * Converts an element description into a tooltip based on certain criteria.
 *
 * @param array $element
 *   An element render array, passed by reference.
 * @param array $target
 *   The target element render array the tooltip is to be attached to, passed
 *   by reference. If not set, it will default to the $element passed.
 * @param bool $input_only
 *   Toggle determining whether or not to only convert input elements.
 * @param int $length
 *   The length of characters to determine if description is "simple".
 */
function bootstrap_element_smart_description(array &$element, array &$target = NULL, $input_only = TRUE, $length = NULL) {
  Bootstrap::deprecated();
  $e = new Element($element);
  $e->smartDescription($target, $input_only, $length);
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
 *
 * @deprecated Will be removed in a future release. There is no replacement.
 */
function bootstrap_get_theme_info($theme_key = NULL, $key = NULL, $base_themes = TRUE) {
  Bootstrap::deprecated();
  // If no $theme_key is given, use the current theme if we can determine it.
  if (!isset($theme_key)) {
    $theme_key = !empty($GLOBALS['theme_key']) ? $GLOBALS['theme_key'] : FALSE;
  }
  if ($theme_key) {
    $themes = \Drupal::service('theme_handler')->listInfo();
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
                $info = [$info];
              }
              $value = NestedArray::mergeDeep($value, $info);
            }
          }
          else {
            if (!empty($info)) {
              if (empty($value)) {
                $value = $info;
              }
              else {
                if (!is_array($value)) {
                  $value = [$value];
                }
                if (!is_array($info)) {
                  $info = [$info];
                }
                $value = NestedArray::mergeDeep($value, $info);
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
 * Includes a file from a theme.
 *
 * @param string $theme
 *   Name of the theme to use for base path.
 * @param string $path
 *   Path relative to $theme.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   bootstrap_include('my_subtheme', 'includes/file.inc');
 *   bootstrap_include('my_subtheme', 'some/other/path/file.inc');
 *
 *   // After.
 *   $my_subtheme = Bootstrap::getTheme('my_subtheme');
 *   $my_subtheme->includeOnce('file.inc');
 *   $my_subtheme->includeOnce('file.inc', 'some/other/path');
 * @endcode
 *
 * @see \Drupal\bootstrap\Theme::includeOnce()
 * @see \Drupal\bootstrap\Bootstrap::getTheme()
 */
function bootstrap_include($theme, $path) {
  Bootstrap::deprecated();
  $theme = BaseTheme::getTheme($theme);
  $parts = explode('/', $path);
  $file = array_pop($parts);
  $dir = implode('/', $parts);
  $theme->includeOnce($file, $dir);
}

/**
 * Retrieves a setting for the current theme or for a given theme.
 *
 * @param string $name
 *   The name of the setting to be retrieved.
 * @param string $theme
 *   The name of a given theme; defaults to the currently active theme.
 * @param string $prefix
 *   The prefix used on the $name of the setting, this will be appended with
 *   "_" automatically if set.
 *
 * @return mixed
 *   The value of the requested setting, NULL if the setting does not exist.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before ("button_colorize" and "my_subtheme_custom_option").
 *   $colorize = bootstrap_setting('button_colorize', 'my_subtheme');
 *   $custom_option = bootstrap_setting('custom_option', 'my_subtheme', 'my_subtheme');
 *
 *   // After ("button_colorize" and "my_subtheme_custom_option").
 *   $my_subtheme = Bootstrap::getTheme('my_subtheme');
 *   $my_subtheme->getSetting('button_colorize');
 *   $my_subtheme->getSetting('my_subtheme_custom_option');
 * @endcode
 *
 * @see \Drupal\bootstrap\Theme::getSetting()
 * @see \Drupal\bootstrap\Bootstrap::getTheme()
 */
function bootstrap_setting($name, $theme = NULL, $prefix = 'bootstrap') {
  Bootstrap::deprecated();
  $theme = BaseTheme::getTheme($theme);
  $prefix = !empty($prefix) ? $prefix . '_' : '';
  return $theme->getSetting($prefix . $name);
}
