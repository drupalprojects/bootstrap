<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\ThemeRegistry.
 */

// Name of the base theme must be lowercase for it to be autoload discoverable.
namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Theme;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\Registry;

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
 * @BootstrapAlter(
 *   id = "theme_registry"
 * )
 */
class ThemeRegistry extends Registry implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    // This is technically a plugin constructor, but because we wish to use the
    // protected methods of the Registry class, we must extend from it. Thus,
    // to properly construct the extended Registry object, we must pass the
    // arguments it would normally get from the service container to "fake" it.
    parent::__construct(
      \Drupal::service('app.root'),
      \Drupal::service('cache.default'),
      \Drupal::service('lock'),
      \Drupal::service('module_handler'),
      \Drupal::service('theme_handler'),
      \Drupal::service('theme.initialization'),
      Bootstrap::getTheme()->getName()
    );
    $this->setThemeManager(\Drupal::service('theme.manager'));
    $this->init();
  }

  /**
   * {@inheritdoc}
   */
  public function alter(&$cache, &$context1 = NULL, &$context2 = NULL) {
    // Sort the registry alphabetically (for easier debugging).
    ksort($cache);

    // Discover all theme files.
    foreach (Bootstrap::getTheme()->getAncestry() as $ancestor) {
      $this->discoverFiles($cache, $ancestor);
    }

    // Discover and add all preprocess functions for theme hook suggestions.
    $this->postProcessExtension($cache, $this->theme);
  }

  /**
   * Discovers files relevant to theme hooks.
   *
   * @param array $cache
   *   The theme registry, as documented in
   *   \Drupal\Core\Theme\Registry::processExtension().
   * @param \Drupal\bootstrap\Theme $theme
   *   Current active theme.
   *
   * @see \Drupal\Core\Theme\Registry::processExtension()
   */
  protected function discoverFiles(array &$cache, Theme $theme) {
    $name = $theme->getName();
    $path = $theme->getPath();

    // Find theme hook files.
    foreach ($theme->fileScan('/(\.func\.php|\.vars\.php|\.html\.twig)$/') as $file) {
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
            $cache[$hook]['includes'] = [];
          }
          if (!in_array($file->uri, $cache[$hook]['includes'])) {
            $cache[$hook]['includes'][] = $file->uri;
          }
        }

        if (!isset($cache[$hook]['preprocess functions'])) {
          $cache[$hook]['preprocess functions'] = [];
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
      foreach (['includes', 'preprocess functions'] as $type) {
        // Ensure properties exist (temporarily at least).
        if (!isset($cache[$hook][$type])) {
          $cache[$hook][$type] = [];
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
    $theme_functions = array_fill_keys($themes, []);

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
