<?php
/**
 * @file
 * This contains deprecated functions that will be removed in a future release.
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Plugin\ProviderManager;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Utility\Unicode;
use Drupal\Component\Utility\NestedArray;

/**
 * The base file system path for CDN providers.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   echo BOOTSTRAP_CDN_PROVIDER_PATH;
 *
 *   // After.
 *   echo \Drupal\bootstrap\Plugin\ProviderManager::FILE_PATH;
 * @endcode
 *
 * @see \Drupal\bootstrap\Plugin\ProviderManager::FILE_PATH
 */
define('BOOTSTRAP_CDN_PROVIDER_PATH', ProviderManager::FILE_PATH);

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
 * Adds a class to an element's render array.
 *
 * @param string|array $class
 *   An individual class or an array of classes to add.
 * @param array $element
 *   The individual renderable array element. It is possible to also pass the
 *   $variables parameter in [pre]process functions and it will logically
 *   determine the correct path to that particular theme hook's classes array.
 *   Passed by reference.
 * @param string $property
 *   Determines which attributes array to retrieve. By default, this is the
 *   element's normal "attributes", but it could also be one of the following:
 *   - "content_attributes"
 *   - "input_group_attributes"
 *   - "title_attributes"
 *   - "wrapper_attributes".
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   _bootstrap_add_class('my-class', $element);
 *
 *   // After.
 *   $e = new \Drupal\bootstrap\Utility\Element($element);
 *   $e->addClass('my-class');
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Element::addClass()
 */
function _bootstrap_add_class($class, array &$element, $property = 'attributes') {
  Bootstrap::deprecated();
  $e = new Element($element);
  $e->addClass($class, $property);
}

/**
 * Adds a specific Bootstrap class to color a button based on its text value.
 *
 * @param array $element
 *   The form element, passed by reference.
 */
function _bootstrap_colorize_button(array &$element) {
  Bootstrap::deprecated();
  $e = new Element($element);
  $e->colorize();
}

/**
 * Matches a Bootstrap class based on a string value.
 *
 * @param string $string
 *   The string to match classes against.
 * @param string $default
 *   The default class to return if no match is found.
 *
 * @return string
 *   The Bootstrap class matched against the value of $haystack or $default if
 *   no match could be made.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $class = _bootstrap_colorize_text($string, $default);
 *
 *   // After.
 *   $class = \Drupal\bootstrap\Bootstrap::cssClassFromString($string, $default);
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::cssClassFromString()
 */
function _bootstrap_colorize_text($string, $default = '') {
  Bootstrap::deprecated();
  return Bootstrap::cssClassFromString($string, $default);
}

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
  return Bootstrap::getTheme()->fileScan($mask, $dir, $options);
}

/**
 * Retrieves an element's "attributes" array.
 *
 * @param array $element
 *   The individual renderable array element, passed by reference.
 * @param string $property
 *   Determines which attributes array to retrieve. By default, this is the
 *   element's normal "attributes", but it could also be one of the following:
 *   - "content_attributes"
 *   - "input_group_attributes"
 *   - "title_attributes"
 *   - "wrapper_attributes".
 *
 * @return \Drupal\Core\Template\Attribute
 *   The attributes object.
 *
 * @deprecated Will be removed in a future release.
 *
 * Note: this used to be passed by reference in 7.x, however given that all
 * attributes arrays are not objects in 8.x, this function can no longer
 * return the array by reference. You will have to update your code to use
 * the new objects (which does reference the element).
 *
 * @code
 *   // Before.
 *   $attributes = &_bootstrap_get_attributes($element);
 *   $attributes['class'][] = 'my-class';
 *
 *   // After.
 *   $e = new \Drupal\bootstrap\Utility\Element($element);
 *   $attributes = $e->getAttributes();
 *   $attributes->addClass('my-class');
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Element::getAttributes()
 * @see \Drupal\Core\Template\Attribute
 */
function _bootstrap_get_attributes(array &$element, $property = 'attributes') {
  Bootstrap::deprecated();
  $e = new Element($element);
  return $e->getAttributes($property);
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
  $themes = array_keys(Bootstrap::getTheme($theme_key)->getAncestry());
  if (!$include_theme_key) {
    array_pop($themes);
  }
  return $themes;
}

/**
 * Retrieves an element's "class" attribute array.
 *
 * @param array $element
 *   The individual renderable array element, passed by reference.
 * @param string $property
 *   Determines which attributes array to retrieve. By default, this is the
 *   element's normal "attributes", but it could also be one of the following:
 *   - "content_attributes"
 *   - "input_group_attributes"
 *   - "title_attributes"
 *   - "wrapper_attributes".
 *
 * @return array
 *   The classes array.
 *
 * @deprecated Will be removed in a future release. There is no replacement.
 *
 * Note: this used to be passed by reference in 7.x, however given that all
 * attributes arrays are not objects in 8.x, this function can no longer
 * return the array by reference. You will have to update your code to use
 * the new objects (which does reference the element).
 *
 * @code
 *   // Before.
 *   $classes = &_bootstrap_get_classes($element);
 *   $classes[] = 'my-class';
 *
 *   // After.
 *   $e = new \Drupal\bootstrap\Utility\Element($element);
 *   $attributes => $e->getAttributes();
 *   $attributes->addClass('my-class');
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Element::getAttributes()
 * @see \Drupal\Core\Template\Attribute
 */
function _bootstrap_get_classes(array &$element, $property = 'attributes') {
  $e = new Element($element);
  return $e->getAttributes($property)->toArray()['class'];
}

/**
 * Returns a list of available Bootstrap Glyphicons.
 *
 * @param string $version
 *   The specific version of glyphicons to return. If not set, the latest
 *   BOOTSTRAP_VERSION will be used.
 *
 * @return array
 *   An associative array of icons keyed by their classes.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $glyphicons = _bootstrap_glyphicons($version);
 *
 *   // After.
 *   $glyphicons = \Drupal\bootstrap\Bootstrap::glyphicons($version);
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::glyphicons()
 */
function _bootstrap_glyphicons($version = NULL) {
  Bootstrap::deprecated();
  return Bootstrap::glyphicons($version);
}

/**
 * Determine whether or not Bootstrap Glyphicons can be used.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $supported = _bootstrap_glyphicons_supported();
 *
 *   // After.
 *   $supported = \Drupal\bootstrap\Bootstrap::getTheme()->hasGlyphicons();
 * @endcode
 *
 * @see \Drupal\bootstrap\Theme::hasGlyphicons()
 */
function _bootstrap_glyphicons_supported() {
  Bootstrap::deprecated();
  return Bootstrap::getTheme()->hasGlyphicons();
}

/**
 * Returns a specific Bootstrap Glyphicon.
 *
 * @param string $name
 *   The icon name, minus the "glyphicon-" prefix.
 * @param string $default
 *   (Optional) The default value to return.
 *
 * @return string
 *   The HTML markup containing the icon defined by $name, $default value if
 *   icon does not exist or returns empty output for whatever reason.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $icon = _bootstrap_icon($name, $default);
 *
 *   // After.
 *   $glyphicon = \Drupal\bootstrap\Bootstrap::glyphicon($name, ['#markup' => $default]);
 *   $icon = \Drupal::service('renderer')->render($glyphicon);
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::glyphicon()
 */
function _bootstrap_icon($name, $default = NULL) {
  Bootstrap::deprecated();
  return \Drupal::service('renderer')->render(Bootstrap::glyphicon($name, ['#markup' => $default]));
}

/**
 * Adds an icon to button element based on its text value.
 *
 * @param array $element
 *   The form element, passed by reference.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   _bootstrap_iconize_button($element);
 *
 *   // After.
 *   $e = new \Drupal\bootstrap\Utility\Element($element);
 *   $e->setIcon();
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Element::setIcon()
 */
function _bootstrap_iconize_button(array &$element) {
  Bootstrap::deprecated();
  $e = new Element($element);
  $e->setIcon();
}

/**
 * Matches a Bootstrap Glyphicon based on a string value.
 *
 * @param string $string
 *   The string to match classes against.
 * @param string $default
 *   The default icon to return if no match is found.
 *
 * @return string
 *   The Bootstrap icon matched against the value of $haystack or $default if
 *   no match could be made.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $icon = _bootstrap_iconize_text($string, $default);
 *
 *   // After.
 *   $icon = \Drupal\bootstrap\Bootstrap::glyphiconFromString($string, ['#markup' => $default]);
 * @endcode
 *
 * @see \Drupal\bootstrap\Bootstrap::glyphiconFromString()
 */
function _bootstrap_iconize_text($string, $default = '') {
  Bootstrap::deprecated();
  return Bootstrap::glyphiconFromString($string, ['#markup' => $default]);
}

/**
 * Determines if an element is a button.
 *
 * @param array $element
 *   A render array element.
 *
 * @return bool
 *   TRUE or FALSE.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $button = _bootstrap_is_button($element);
 *
 *   // After.
 *   $e = new \Drupal\bootstrap\Utility\Element($element);
 *   $button = $e->isButton();
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Element::isButton()
 */
function _bootstrap_is_button(array $element) {
  Bootstrap::deprecated();
  $e = new \Drupal\bootstrap\Utility\Element($element);
  return $e->isButton();
}

/**
 * Determines if a string of text is considered "simple".
 *
 * @param string $string
 *   The string of text to check "simple" criteria on.
 * @param int|FALSE $length
 *   The length of characters used to determine whether or not $string is
 *   considered "simple". Set explicitly to FALSE to disable this criteria.
 * @param array|FALSE $allowed_tags
 *   An array of allowed tag elements. Set explicitly to FALSE to disable this
 *   criteria.
 * @param bool $html
 *   A variable, passed by reference, that indicates whether or not the
 *   string contains HTML.
 *
 * @return bool
 *   Returns TRUE if the $string is considered "simple", FALSE otherwise.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $simple = _bootstrap_is_simple_string($string, $length, $allowed_tags, $html);
 *
 *   // After.
 *   $simple = \Drupal\bootstrap\Utility\Unicode::isSimple($string, $length, $allowed_tags, $html);
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Unicode::isSimple()
 */
function _bootstrap_is_simple_string($string, $length = 250, $allowed_tags = NULL, &$html = FALSE) {
  Bootstrap::deprecated();
  return Unicode::isSimple($string, $length, $allowed_tags, $html);
}

/**
 * Removes a class from an element's attributes array.
 *
 * @param string|array $class
 *   An individual class or an array of classes to remove.
 * @param array $element
 *   The individual renderable array element.
 * @param string $property
 *   Determines which attributes array to retrieve. By default, this is the
 *   element's normal "attributes", but it could also be one of the following:
 *   - "content_attributes"
 *   - "input_group_attributes"
 *   - "title_attributes"
 *   - "wrapper_attributes".
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   _bootstrap_remove_class('my-class', $element);
 *
 *   // After.
 *   $e = new \Drupal\bootstrap\Utility\Element($element);
 *   $e->removeClass('my-class');
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Element::removeClass()
 */
function _bootstrap_remove_class($class, array &$element, $property = 'attributes') {
  Bootstrap::deprecated();
  $e = new Element($element);
  $e->removeClass($class, $property);
}

/**
 * Retrieves a list of available CDN providers for the Bootstrap framework.
 *
 * @param string $provider
 *   A specific provider data to return.
 * @param bool $reset
 *   Toggle determining whether or not to reset the database cache.
 *
 * @return array|FALSE
 *   An associative array of CDN providers, keyed by their machine name if
 *   $provider is not set. If $provider is set and exists, it's individual
 *   data array will be returned. If $provider is set and the data does not
 *   exist then FALSE will be returned.
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   $providers = bootstrap_cdn_provider();
 *   $jsdelivr = bootstrap_cdn_provider('jsdelivr');
 *
 *   // After.
 *   $provider_manager = new \Drupal\bootstrap\Plugin\ProviderManager(Bootstrap::getTheme());
 *   $providers = $provider_manager->getDefinitions();
 *   $jsdelivr = $provider_manager->getDefinition('jsdelivr');
 * @endcode
 *
 * @see \Drupal\bootstrap\Plugin\ProviderManager
 * @see \Drupal\bootstrap\Theme::getProviders()
 * @see \Drupal\bootstrap\Theme::getProvider()
 */
function bootstrap_cdn_provider($provider = NULL, $reset = FALSE) {
  Bootstrap::deprecated();
  $provider_manager = new ProviderManager(Bootstrap::getTheme());
  if ($reset) {
    $provider_manager->clearCachedDefinitions();
  }
  if (isset($provider)) {
    if ($provider_manager->hasDefinition($provider)) {
      return $provider_manager->getDefinition($provider);
    }
    return FALSE;
  }
  return $provider_manager->getDefinitions();
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
 *
 * @deprecated Will be removed in a future release.
 *
 * @code
 *   // Before.
 *   bootstrap_element_smart_description($element, $target, $input_only, $length);
 *
 *   // After.
 *   $e = new \Drupal\bootstrap\Utility\Element($element);
 *   $e->smartDescription($target, $input_only, $length);
 * @endcode
 *
 * @see \Drupal\bootstrap\Utility\Element::smartDescription()
 */
function bootstrap_element_smart_description(array &$element, array &$target = NULL, $input_only = TRUE, $length = NULL) {
  Bootstrap::deprecated();
  $e = new Element($element);
  $e->smartDescription($target, $input_only, $length);
}

/**
 * Retrieves CDN assets for the active provider, if any.
 *
 * @param string|array $type
 *   The type of asset to retrieve: "css" or "js", defaults to an array
 *   array containing both if not set.
 * @param string $provider
 *   The name of a specific CDN provider to use, defaults to the active provider
 *   set in the theme settings.
 * @param string $theme
 *   The name of a specific theme the settings should be retrieved from,
 *   defaults to the active theme.
 *
 * @return array
 *   If $type is a string or an array with only one (1) item in it, the assets
 *   are returned as an indexed array of files. Otherwise, an associative array
 *   is returned keyed by the type.
 */
function bootstrap_get_cdn_assets($type = NULL, $provider = NULL, $theme = NULL) {
  Bootstrap::deprecated();
  $assets = [];
  $theme = Bootstrap::getTheme($theme);
  if ($provider = $theme->getProvider($provider)) {
    $assets = $provider->getAssets($type);
  }
  return $assets;
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
  $theme = Bootstrap::getTheme($theme);
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
  $theme = Bootstrap::getTheme($theme);
  $prefix = $prefix !== 'bootstrap' && !empty($prefix) ? $prefix . '_' : '';
  return $theme->getSetting($prefix . $name);
}
