<?php
/**
 * @file
 * List of functions used to alter the theme registry in Bootstrap based themes.
 */

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
 * @defgroup theme_process Theme Process Functions (.vars.php)
 *
 * List of theme process functions used in the Drupal Bootstrap base theme.
 *
 * View the parent topic for additional documentation.
 */
/**
 * @defgroup templates Theme Templates (.tpl.php)
 *
 * List of theme templates used in the Drupal Bootstrap base theme.
 *
 * View the parent topic for additional documentation.
 */

use Drupal\Component\Utility\NestedArray;

/**
 * Stub implementation for bootstrap_theme().
 *
 * This base-theme's custom theme hook implementations. Never define "path"
 * or "template" as these are detected and automatically added.
 *
 * @see bootstrap_theme_registry_alter()
 * @see bootstrap_hook_theme_complete()
 * @see bootstrap_theme()
 * @see hook_theme()
 */
function _bootstrap_theme(&$existing, $type, $theme, $path) {
  // Bootstrap Carousels.
  $hooks['bootstrap_carousel'] = array(
    'variables' => array(
      'attributes' => array(),
      'items' => array(),
      'start_index' => 0,
      'controls' => TRUE,
      'indicators' => TRUE,
      'interval' => 5000,
      'pause' => 'hover',
      'wrap' => TRUE,
    ),
  );

  // Bootstrap Dropdowns.
  $hooks['bootstrap_dropdown'] = array(
    'render element' => 'element',
  );

  // Bootstrap Modals.
  $hooks['bootstrap_modal'] = array(
    'variables' => array(
      'heading' => '',
      'body' => '',
      'footer' => '',
      'dialog_attributes' => array(),
      'attributes' => array(),
      'size' => '',
      'html_heading' => FALSE,
    ),
  );

  // Bootstrap Panels.
  $hooks['bootstrap_panel'] = array(
    'render element' => 'element',
  );
  return $hooks;
}

/**
 * Implements hook_theme_registry_alter().
 */
function bootstrap_theme_registry_alter(&$registry) {
  // Retrieve the active theme names.
  $themes = _bootstrap_get_base_themes(NULL, TRUE);

  // Return the theme registry unaltered if it is not Bootstrap based.
  if (!in_array('bootstrap', $themes)) {
    return;
  }

  // Inject the "footer" variable default in the existing "table" hook.
  // @see https://drupal.org/node/806982
  // @todo Make this discoverable in some way instead of a manual injection.
  $registry['table']['variables']['footer'] = NULL;

  // Process registered hooks in the theme registry.
  _bootstrap_process_theme_registry($registry, $themes);

  // Process registered hooks in the theme registry to add necessary theme hook
  // suggestion phased function invocations. This must be run after separately
  // and after all includes have been loaded.
  _bootstrap_process_theme_registry_suggestions($registry, $themes);

  // Post-process theme registry. This happens after all altering has occurred.
  foreach ($registry as $hook => $info) {
    // The 'base hook' is only applied to derivative hooks already registered
    // from a pattern. This is typically set from
    // drupal_find_theme_functions() and drupal_find_theme_templates().
    // If a theme hook has a base hook, mark its preprocess functions always
    // incomplete in order to inherit the base hook's preprocess functions.
    if (!empty($info['incomplete preprocess functions'])) {
      _bootstrap_complete_suggestion($hook, $registry);
      unset($registry[$hook]['incomplete preprocess functions']);
    }
    // Ensure uniqueness.
    if (!empty($registry[$hook]['includes'])) {
      $registry[$hook]['includes'] = array_unique($registry[$hook]['includes']);
    }
    if (!empty($registry[$hook]['preprocess functions'])) {
      $registry[$hook]['preprocess functions'] = array_unique($registry[$hook]['preprocess functions']);
    }
    if (!empty($registry[$hook]['process functions'])) {
      $registry[$hook]['process functions'] = array_unique($registry[$hook]['process functions']);
    }

    // Ensure "theme path" is set.
    if (!isset($registry[$hook]['theme path'])) {
      $registry[$hook]['theme path'] = \Drupal::theme()->getActiveTheme()->getPath();
    }
  }
}

/**
 * Processes registered hooks in the theme registry against list of themes.
 *
 * Discovers and fills missing elements in the theme registry. This is similar
 * to _theme_process_registry(), however severely modified for Bootstrap based
 * themes.
 *
 * All additions or modifications must live in `./templates`, relative to the
 * base theme or sub-theme's base folder. These files can be organized in any
 * order using sub-folders as it searches recursively.
 *
 * Adds or modifies the following theme hook keys:
 *  - `includes`: When a variables file `*.vars.php` is found.
 *  - `includes`: When a function file `*.func.php` is found.
 *  - `function`: When a specific theme hook function override is found.
 *  - `template`: When a template file `*.tpl.php` is found in. Note, if both
 *    a function and a template are defined, a template implementation will
 *    always be used and the `function` will be unset.
 *  - `path`: When a template file `*.tpl.php` is found.
 *  - `preprocess functions`: When a specific theme hook suggestion function
 *    `hook_preprocess_HOOK__SUGGESTION` is found.
 *  - `process functions` When a specific theme hook suggestion function
 *    `hook_process_HOOK__SUGGESTION` is found.
 *
 * @param array $registry
 *   The theme registry array, passed by reference.
 * @param string|array $themes
 *   The name of the theme or list of theme names to process.
 *
 * @see bootstrap_theme_registry_alter()
 * @see _theme_process_registry()
 * @see _theme_build_registry()
 */
function _bootstrap_process_theme_registry(&$registry, $themes) {
  // Convert to an array if needed.
  if (is_string($themes)) {
    $themes = array();
  }

  // Processor functions work in two distinct phases with the process
  // functions always being executed after the preprocess functions.
  $variable_process_phases = array(
    'preprocess functions' => 'preprocess',
    'process functions'    => 'process',
  );

  // Iterate over each theme passed.
  // Iterate over the [pre]process phases.
  foreach ($variable_process_phases as $phase_key => $phase) {
    foreach ($themes as $theme) {
      // Get the theme's base path.
      $path = drupal_get_path('theme', $theme);

      // Find theme function overrides.
      foreach (_bootstrap_file_scan_directory($path . '/templates', '/\.vars\.php$/') as $file) {
        $name = $file->name;
        // Strip off the extension.
        if (($pos = strpos($name, '.')) !== FALSE) {
          $name = substr($name, 0, $pos);
        }

        // Transform "-" in file names to "_" to match theme hook naming scheme.
        $hook = strtr($name, '-', '_');

        // File to be included by core's theme function when a theme hook is
        // invoked.
        if (isset($registry[$hook])) {
          if (!isset($registry[$hook][$phase_key])) {
            $registry[$hook][$phase_key] = array();
          }
          if (!isset($registry[$hook]['includes'])) {
            $registry[$hook]['includes'] = array();
          }

          // Include the file now so functions can be discovered below.
          include_once DRUPAL_ROOT . '/' . $file->uri;
          if (!in_array($file->uri, $registry[$hook]['includes'])) {
            $registry[$hook]['includes'][] = $file->uri;
          }
        }
      }

      // Process core's normal functionality.
      _bootstrap_registry_process_extension($registry, $theme, \Drupal::theme()->getActiveTheme()->getName() === $theme ? 'theme' : 'base_theme', $theme, $path);

      // Find necessary templates in the theme.
      $registry = NestedArray::mergeDeep($registry, drupal_find_theme_templates($registry, '.html.twig', $path));

      // Iterate over each registered hook.
      foreach ($registry as $hook => $info) {
        // Remove function callbacks if a template was found.
        if (isset($info['function']) && isset($info['template'])) {
          unset($registry[$hook]['function']);
        }

        // Correct template theme paths.
        if (!isset($info['theme path'])) {
          $registry[$hook]['theme path'] = $path;
        }

        // Correct the type that is implementing this override.
        $registry[$hook]['type'] = \Drupal::theme()->getActiveTheme()->getPath() === $registry[$hook]['theme path'] ? 'theme' : 'base_theme';

        // Sort the phase functions.
        // @see https://www.drupal.org/node/2098551
        _bootstrap_registry_sort_phase_functions($registry[$hook][$phase_key], $hook, $phase, $themes);

        // Setup a default "context" variable. This allows #context to be passed
        // to every template and theme function.
        // @see https://drupal.org/node/2035055
        if (isset($info['variables']) && !isset($info['variables']['context'])) {
          $registry[$hook]['variables']['context'] = array();
        }

        // Setup a default "icon" variable. This allows #icon to be passed
        // to every template and theme function.
        // @see https://drupal.org/node/2219965
        if (isset($info['variables']) && !isset($info['variables']['icon'])) {
          $registry[$hook]['variables']['icon'] = NULL;
        }
        if (isset($info['variables']) && !isset($info['variables']['icon_position'])) {
          $registry[$hook]['variables']['icon_position'] = 'before';
        }
      }
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
 * @param string $phase
 *   The current phase being processed.
 * @param array $themes
 *   An indexed array of current themes.
 *
 * @see https://www.drupal.org/node/2098551
 */
function _bootstrap_registry_sort_phase_functions(&$functions, $hook, $phase, $themes) {
  // Immediately return if there is nothing to sort.
  if (count($functions) < 2) {
    return;
  }

  // Create an associative array of theme functions to ensure sort order.
  $theme_functions = array_fill_keys($themes, array());

  // Iterate over all the themes.
  foreach ($themes as $theme) {
    // Only add the function to the array of theme functions if it currently
    // exists in the $functions array.
    $function = $theme . '_' . $phase . '_' . $hook;
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

/**
 * Processes registered hooks in the theme registry against list of themes.
 *
 * This is used to add the necessary phased functions to theme hook suggestions.
 * Because it uses get_defined_functions(), it must be invoked after all
 * includes have been detected and loaded. This is similar to
 * drupal_find_theme_functions(), however severely modified for Bootstrap based
 * themes.
 *
 * @param array $registry
 *   The theme registry array, passed by reference.
 * @param string|array $themes
 *   The name of the theme or list of theme names to process.
 *
 * @return array
 *   The functions found, suitable for returning from hook_theme;
 *
 * @see https://drupal.org/node/939462
 * @see drupal_find_theme_functions()
 */
function _bootstrap_process_theme_registry_suggestions(&$registry, $themes) {
  // Convert to an array if needed.
  if (is_string($themes)) {
    $themes = array();
  }

  // Merge in normal core detections first.
  $registry = NestedArray::mergeDeep($registry, drupal_find_theme_functions($registry, $themes));

  // Processor functions work in two distinct phases with the process
  // functions always being executed after the preprocess functions.
  $variable_process_phases = array(
    'preprocess functions' => 'preprocess',
    'process functions'    => 'process',
  );

  $grouped_functions = \Drupal::service('theme.registry')->getPrefixGroupedUserFunctions();

  // Iterate over each theme passed.
  foreach ($themes as $theme) {
    // Iterate over each registered hook.
    foreach ($registry as $hook => $info) {
      // The pattern to match.
      $pattern = isset($info['pattern']) ? $info['pattern'] : ($hook . '__');

      // Only process hooks that have not explicitly "turned off" patterns.
      if (empty($pattern)) {
        continue;
      }

      // Iterate over the [pre]process phases.
      foreach ($variable_process_phases as $phase_key => $phase) {
        // Find functions matching the specific theme and phase prefix.
        $prefix = $theme . '_' . $phase;

        // Grep only the functions which are within the prefix group.
        list($first_prefix,) = explode('_', $prefix, 2);
        if (isset($grouped_functions[$first_prefix]) && ($matches = preg_grep('/^' . $prefix . '_' . $pattern . '/', $grouped_functions[$first_prefix]))) {
          foreach ($matches as $match) {
            // Determine the current theme implementation.
            $hook = substr($match, strlen($prefix) + 1);
            $base_hook = $hook;

            // If there's no current theme implementation, keep checking for
            // more generic base hooks. If there's still no implementation,
            // one must be created using the last found implementation
            // information.
            if (!isset($registry[$base_hook]) || isset($registry[$base_hook]['base hook'])) {
              // Iteratively strip everything after the last '__' delimiter,
              // until an implementation is found.
              while ($pos = strrpos($base_hook, '__')) {
                $base_hook = substr($base_hook, 0, $pos);
                if (isset($registry[$base_hook])) {
                  break;
                }
              }

              // No base hook was found, this allows the implementation to be
              // ignored in the next steps.
              if (!isset($registry[$base_hook])) {
                $base_hook = FALSE;
              }
            }

            // Process specific base hook implementations if necessary.
            if ($base_hook) {
              // The matched theme implementation does not exist in the
              // registry, one must be created if base hook information was
              // found, otherwise it will be ignored.
              if (!isset($registry[$hook])) {
                $hook_type = isset($registry[$base_hook]['function']) ? 'function' : 'template';
                $arg_name = isset($registry[$base_hook]['variables']) ? 'variables' : 'render element';
                $registry[$hook] = array(
                  $hook_type => $registry[$base_hook][$hook_type],
                  $arg_name => $registry[$base_hook][$arg_name],
                  'base hook' => $base_hook,
                  'type' => $registry[$base_hook]['type'],
                  'preprocess functions' => array(),
                  'process functions' => array(),
                );
                if (isset($registry[$base_hook]['path'])) {
                  $registry[$hook]['path'] = $registry[$base_hook]['path'];
                }
                if (isset($registry[$base_hook]['theme path'])) {
                  $registry[$hook]['theme path'] = $registry[$base_hook]['theme path'];
                }
              }
            }

            // If the hook exists, merge in the functions. Otherwise ignore it
            // since there was no base hook found and a new implementation
            // could not be created.
            if (isset($registry[$hook])) {
              $registry[$hook] = NestedArray::mergeDeep($registry[$hook], array(
                $phase_key => array($match),
              ));

              // Due to how theme() functions, if a base hook implements
              // preprocess or process functions, then the base hook info is
              // used to invoke the necessary phase functions instead of the
              // suggestion hook info. To get around this, a helper function
              // must be appended to the base hook info so it can call the
              // theme suggestion implementation's phase function.
              $function = '_bootstrap_' . $phase . '_theme_suggestion';
              if (!in_array($function, $registry[$base_hook][$phase_key])) {
                $registry[$base_hook][$phase_key][] = $function;
              }
            }
          }
        }
      }
    }
  }
}

/**
 * Completes the definition of the requested suggestion hook.
 *
 * @param string $hook
 *   The name of the suggestion hook to complete.
 * @param array $cache
 *   The theme registry, as documented in
 *   \Drupal\Core\Theme\Registry::processExtension().
 */
function _bootstrap_complete_suggestion($hook, &$cache) {
  $previous_hook = $hook;
  $incomplete_previous_hook = array();
  while ((!isset($cache[$previous_hook]) || isset($cache[$previous_hook]['incomplete preprocess functions']))
    && $pos = strrpos($previous_hook, '__')) {
    if (isset($cache[$previous_hook]) && !$incomplete_previous_hook && isset($cache[$previous_hook]['incomplete preprocess functions'])) {
      $incomplete_previous_hook = $cache[$previous_hook];
      unset($incomplete_previous_hook['incomplete preprocess functions']);
    }
    $previous_hook = substr($previous_hook, 0, $pos);

    // If base hook exists clone of it for the preprocess function
    // without a template.
    // @see https://www.drupal.org/node/2457295
    if (isset($cache[$previous_hook]) && !isset($cache[$previous_hook]['incomplete preprocess functions'])) {
      $cache[$hook] = $incomplete_previous_hook + $cache[$previous_hook];
      if (isset($incomplete_previous_hook['preprocess functions'])) {
        $diff = array_diff($incomplete_previous_hook['preprocess functions'], $cache[$previous_hook]['preprocess functions']);
        $cache[$hook]['preprocess functions'] = array_merge($cache[$previous_hook]['preprocess functions'], $diff);
      }
      // If a base hook isn't set, this is the actual base hook.
      if (!isset($cache[$previous_hook]['base hook'])) {
        $cache[$hook]['base hook'] = $previous_hook;
      }
    }
  }
}

/**
 * Process a single implementation of hook_theme().
 *
 * @param array $cache
 *   The theme registry that will eventually be cached; It is an associative
 *   array keyed by theme hooks, whose values are associative arrays
 *   describing the hook:
 *   - 'type': The passed-in $type.
 *   - 'theme path': The passed-in $path.
 *   - 'function': The name of the function generating output for this theme
 *     hook. Either defined explicitly in hook_theme() or, if neither
 *     'function' nor 'template' is defined, then the default theme function
 *     name is used. The default theme function name is the theme hook
 *     prefixed by either 'theme_' for modules or '$name_' for everything
 *     else. If 'function' is defined, 'template' is not used.
 *   - 'template': The filename of the template generating output for this
 *     theme hook. The template is in the directory defined by the 'path' key
 *     of hook_theme() or defaults to "$path/templates".
 *   - 'variables': The variables for this theme hook as defined in
 *     hook_theme(). If there is more than one implementation and 'variables'
 *     is not specified in a later one, then the previous definition is kept.
 *   - 'render element': The renderable element for this theme hook as defined
 *     in hook_theme(). If there is more than one implementation and
 *     'render element' is not specified in a later one, then the previous
 *     definition is kept.
 *   - 'preprocess functions': See _theme() for detailed documentation.
 * @param string $name
 *   The name of the module, theme engine, base theme engine, theme or base
 *   theme implementing hook_theme().
 * @param string $type
 *   One of 'module', 'theme_engine', 'base_theme_engine', 'theme', or
 *   'base_theme'. Unlike regular hooks that can only be implemented by
 *   modules, each of these can implement hook_theme(). This function is
 *   called in aforementioned order and new entries override older ones. For
 *   example, if a theme hook is both defined by a module and a theme, then
 *   the definition in the theme will be used.
 * @param string $theme
 *   The actual name of theme, module, etc. that is being processed.
 * @param string $path
 *   The directory where $name is. For example, modules/system or
 *   themes/bartik.
 *
 * @see \Drupal\Core\Theme\ThemeManagerInterface::render()
 * @see hook_theme()
 * @see \Drupal\Core\Extension\ThemeHandler::listInfo()
 * @see twig_render_template()
 *
 * @throws \BadFunctionCallException
 */
function _bootstrap_registry_process_extension(array &$cache, $name, $type, $theme, $path) {
  $result = array();

  $hook_defaults = array(
    'variables' => TRUE,
    'render element' => TRUE,
    'pattern' => TRUE,
    'base hook' => TRUE,
  );

  $module_list = array_keys((array) \Drupal::moduleHandler()->getModuleList());

  // Invoke the hook_theme() implementation, preprocess what is returned, and
  // merge it into $cache.
  $function = $name . '_theme';
  if (function_exists($function)) {
    $result = $function($cache, $type, $theme, $path);
    foreach ($result as $hook => $info) {
      // When a theme or engine overrides a module's theme function
      // $result[$hook] will only contain key/value pairs for information being
      // overridden.  Pull the rest of the information from what was defined by
      // an earlier hook.

      // Fill in the type and path of the module, theme, or engine that
      // implements this theme function.
      $result[$hook]['type'] = $type;
      $result[$hook]['theme path'] = $path;

      if (isset($cache[$hook]['includes'])) {
        $result[$hook]['includes'] = $cache[$hook]['includes'];
      }

      // Load the includes, as they may contain preprocess functions.
      if (isset($info['includes'])) {
        foreach ($info['includes'] as $include_file) {
          include_once $this->root . '/' . $include_file;
        }
      }

      // If the theme implementation defines a file, then also use the path
      // that it defined. Otherwise use the default path. This allows
      // system.module to declare theme functions on behalf of core .include
      // files.
      if (isset($info['file'])) {
        $include_file = isset($info['path']) ? $info['path'] : $path;
        $include_file .= '/' . $info['file'];
        include_once $this->root . '/' . $include_file;
        $result[$hook]['includes'][] = $include_file;
      }

      // A template file is the default implementation for a theme hook, but
      // if the theme hook specifies a function callback instead, check to
      // ensure the function actually exists.
      if (isset($info['function']) && !function_exists($info['function'])) {
        throw new \BadFunctionCallException(sprintf(
          'Theme hook "%s" refers to a theme function callback that does not exist: "%s"',
          $hook,
          $info['function']
        ));
      }
      // Provide a default naming convention for 'template' based on the
      // hook used. If the template does not exist, the theme engine used
      // should throw an exception at runtime when attempting to include
      // the template file.
      elseif (!isset($info['template'])) {
        $info['template'] = strtr($hook, '_', '-');
        $result[$hook]['template'] = $info['template'];
      }

      // Prepend the current theming path when none is set. This is required
      // for the default theme engine to know where the template lives.
      if (isset($result[$hook]['template']) && !isset($info['path'])) {
        $result[$hook]['path'] = $path . '/templates';
      }

      // If the default keys are not set, use the default values registered
      // by the module.
      if (isset($cache[$hook])) {
        $result[$hook] += array_intersect_key($cache[$hook], $hook_defaults);
      }

      // Preprocess variables for all theming hooks, whether the hook is
      // implemented as a template or as a function. Ensure they are arrays.
      if (!isset($info['preprocess functions']) || !is_array($info['preprocess functions'])) {
        $info['preprocess functions'] = array();
        $prefixes = array();
        if ($type == 'module') {
          // Default variable preprocessor prefix.
          $prefixes[] = 'template';
          // Add all modules so they can intervene with their own variable
          // preprocessors. This allows them to provide variable preprocessors
          // even if they are not the owner of the current hook.
          $prefixes = array_merge($prefixes, $module_list);
        }
        elseif ($type == 'theme_engine' || $type == 'base_theme_engine') {
          // Theme engines get an extra set that come before the normally
          // named variable preprocessors.
          $prefixes[] = $name . '_engine';
          // The theme engine registers on behalf of the theme using the
          // theme's name.
          $prefixes[] = $theme;
        }
        else {
          // This applies when the theme manually registers their own variable
          // preprocessors.
          $prefixes[] = $name;
        }
        foreach ($prefixes as $prefix) {
          // Only use non-hook-specific variable preprocessors for theming
          // hooks implemented as templates. See _theme().
          if (isset($info['template']) && function_exists($prefix . '_preprocess')) {
            $info['preprocess functions'][] = $prefix . '_preprocess';
          }
          if (function_exists($prefix . '_preprocess_' . $hook)) {
            $info['preprocess functions'][] = $prefix . '_preprocess_' . $hook;
          }
        }
      }
      // Check for the override flag and prevent the cached variable
      // preprocessors from being used. This allows themes or theme engines
      // to remove variable preprocessors set earlier in the registry build.
      if (!empty($info['override preprocess functions'])) {
        // Flag not needed inside the registry.
        unset($result[$hook]['override preprocess functions']);
      }
      elseif (isset($cache[$hook]['preprocess functions']) && is_array($cache[$hook]['preprocess functions'])) {
        $info['preprocess functions'] = array_merge($cache[$hook]['preprocess functions'], $info['preprocess functions']);
      }
      $result[$hook]['preprocess functions'] = $info['preprocess functions'];
    }

    // Merge the newly created theme hooks into the existing cache.
    $cache = $result + $cache;
  }

  // Let themes have variable preprocessors even if they didn't register a
  // template.
  if ($type == 'theme' || $type == 'base_theme') {
    foreach ($cache as $hook => $info) {
      // Check only if not registered by the theme or engine.
      if (empty($result[$hook])) {
        if (!isset($info['preprocess functions'])) {
          $cache[$hook]['preprocess functions'] = array();
        }
        // Only use non-hook-specific variable preprocessors for theme hooks
        // implemented as templates. See _theme().
        if (isset($info['template']) && function_exists($name . '_preprocess')) {
          $cache[$hook]['preprocess functions'][] = $name . '_preprocess';
        }
        if (function_exists($name . '_preprocess_' . $hook)) {
          $cache[$hook]['preprocess functions'][] = $name . '_preprocess_' . $hook;
          $cache[$hook]['theme path'] = $path;
        }
        // Ensure uniqueness.
        $cache[$hook]['preprocess functions'] = array_unique($cache[$hook]['preprocess functions']);
        if (!empty($cache[$hook]['base hook'])) {
          $cache[$hook]['incomplete preprocess functions'] = TRUE;
        }
      }
    }
  }
}

/**
 * @} End of "addtogroup registry".
 */
