<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Theme\Registry.
 */

// Name of the base theme must be lowercase for it to be autoload discoverable.
namespace Drupal\bootstrap\Theme;

use Drupal\Core\Theme\ActiveTheme;

/**
 * @addtogroup registry
 * @{
 */

// Define additional sub-groups for creating lists for all the theme files.
/**
 * @defgroup theme_functions Theme Functions (.func.php)
 *
 * List of theme functions used in the Drupal Bootstrap base theme.
 *
 * View the parent topic for additional documentation.
 */
/**
 * @defgroup theme_preprocess Theme Preprocess Functions (.vars.php)
 *
 * List of theme preprocess functions used in the Drupal Bootstrap base theme.
 *
 * View the parent topic for additional documentation.
 */
/**
 * @defgroup templates Theme Templates (.html.twig)
 *
 * List of theme templates used in the Drupal Bootstrap base theme.
 *
 * View the parent topic for additional documentation.
 */

/**
 * Extends the theme registry to override and use protected functions.
 *
 * @todo Refactor into a proper theme.registry service replacement in a
 * bootstrap_core sub-module once this theme can add it as a dependency.
 *
 * @see https://www.drupal.org/node/474684
 *
 * @ingroup registry
 */
class Registry extends \Drupal\Core\Theme\Registry {

  /**
   * Alters the theme registry.
   *
   * @param array $cache
   *   The theme registry, as documented in
   *   \Drupal\Core\Theme\Registry::processExtension().
   */
  public function alter(array &$cache) {
    $this->init();

    // Sort the registry alphabetically (for easier debugging).
    ksort($cache);

    // Process each base theme.
    /** @var ActiveTheme $base */
    foreach (array_reverse($this->theme->getBaseThemes()) as $base) {
      $this->discoverFiles($cache, $base);
    }

    // Hooks provided by the theme itself.
    $this->discoverFiles($cache, $this->theme);

    // Discover and add all preprocess functions for theme hook suggestions.
    $this->postProcessExtension($cache, $this->theme);
  }

  /**
   * Discovers files relevant to theme hooks.
   *
   * @param array $cache
   *   The theme registry, as documented in
   *   \Drupal\Core\Theme\Registry::processExtension().
   * @param \Drupal\Core\Theme\ActiveTheme $theme
   *   Current active theme.
   *
   * @see \Drupal\Core\Theme\Registry::processExtension()
   */
  protected function discoverFiles(array &$cache, ActiveTheme $theme) {
    $name = $theme->getName();
    $path = $theme->getPath();

    // Find theme hook files.
    foreach (_bootstrap_file_scan_directory($path, '/(\.func\.php|\.vars\.php|\.html\.twig)$/') as $file) {
      // Transform "-" in file names to "_" to match theme hook naming scheme.
      $hook = strtr($file->name, '-', '_');

      // Strip off the extension.
      if (($pos = strpos($hook, '.')) !== FALSE) {
        $hook = substr($hook, 0, $pos);
      }

      // File to be included by core when a theme hook is invoked.
      if (isset($cache[$hook])) {
        // Due to the order in which templates are discovered, a theme's
        // templates are first discovered while in the twig engine's
        // hook_theme() invocation. Correct the path to the template here.
        if (preg_match('/twig$/', $file->uri)) {
          $cache[$hook]['path'] = dirname($file->uri);
        }
        // Include the file now so its functions can be discovered later.
        else {
          include_once DRUPAL_ROOT . '/' . $file->uri;
          if (!isset($cache[$hook]['includes'])) {
            $cache[$hook]['includes'] = array();
          }
          if (!in_array($file->uri, $cache[$hook]['includes'])) {
            $cache[$hook]['includes'][] = $file->uri;
          }
        }

        if (!isset($cache[$hook]['preprocess functions'])) {
          $cache[$hook]['preprocess functions'] = array();
        }
        if (isset($cache[$hook]['template']) && function_exists($name . '_preprocess')) {
          $cache[$hook]['preprocess functions'][] = $name . '_preprocess';
        }
        if (function_exists($name . '_preprocess_' . $hook)) {
          $cache[$hook]['preprocess functions'][] = $name . '_preprocess_' . $hook;
          $cache[$hook]['theme path'] = $path;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function postProcessExtension(array &$cache, ActiveTheme $theme) {
    parent::postProcessExtension($cache, $theme);

    foreach ($cache as $hook => $info) {
      foreach (array('includes', 'preprocess functions') as $type) {
        // Ensure properties exist (temporarily at least).
        if (!isset($cache[$hook][$type])) {
          $cache[$hook][$type] = array();
        }

        // Merge in base hook values.
        if (!empty($info['base hook'])) {
          if (isset($cache[$info['base hook']][$type])) {
            $cache[$hook][$type] = array_merge($cache[$info['base hook']][$type], $cache[$hook][$type]);
          }
        }

        // Ensure uniqueness.
        if (!empty($info[$type])) {
          $cache[$hook][$type] = array_unique($cache[$hook][$type]);
        }

        // Remove if empty.
        if (empty($cache[$hook][$type])) {
          unset($cache[$hook][$type]);
        }
      }

      // Correct any unset theme path.
      if (!isset($info['theme path'])) {
        $cache[$hook]['theme path'] = $theme->getPath();
      }

      // Add extra variables to all theme hooks.
      if (isset($info['variables'])) {
        $variables = array(
          // Allow #context to be passed to every template and theme function.
          // @see https://drupal.org/node/2035055
          'context' => array(),

          // Allow #icon to be passed to every template and theme function.
          // @see https://drupal.org/node/2219965
          'icon' => NULL,
          'icon_position' => 'before',
        );
        foreach ($variables as $name => $value) {
          if (!isset($info['variables'][$name])) {
            $cache[$hook]['variables'][$name] = $value;
          }
        }
      }

      // Sort the preprocess functions.
      // @see https://www.drupal.org/node/2098551
      if (isset($info['preprocess functions'])) {
        $this->sortFunctions($cache[$hook]['preprocess functions'], $hook, $theme);
      }
    }
  }

  /**
   * Ensures the phase functions are invoked in the correct order.
   *
   * @param array $functions
   *   The phase functions to iterate over.
   * @param string $hook
   *   The current hook being processed.
   * @param \Drupal\Core\Theme\ActiveTheme $theme
   *   Current active theme.
   *
   * @see https://www.drupal.org/node/2098551
   */
  protected function sortFunctions(array &$functions, $hook, ActiveTheme $theme) {
    // Immediately return if there is nothing to sort.
    if (count($functions) < 2) {
      return;
    }

    $themes = array_keys($theme->getBaseThemes());
    $themes[] = $theme->getName();

    // Create an associative array of theme functions to ensure sort order.
    $theme_functions = array_fill_keys($themes, array());

    // Iterate over all the themes.
    foreach ($themes as $theme) {
      // Only add the function to the array of theme functions if it currently
      // exists in the $functions array.
      $function = $theme . '_preprocess_' . $hook;
      $key = array_search($function, $functions);
      if ($key !== FALSE) {
        // Save the theme function to be added later, but sorted.
        $theme_functions[$theme][] = $function;

        // Remove it from the current $functions array.
        unset($functions[$key]);
      }
    }

    // Iterate over all the captured theme functions and place them back into
    // the phase functions array.
    foreach ($theme_functions as $array) {
      $functions = array_merge($functions, $array);
    }
  }

}

/**
 * @} End of "addtogroup registry".
 */
