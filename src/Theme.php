<?php
/**
 * @file
 * Contains \Drupal\bootstrap.
 */

namespace Drupal\bootstrap;

use Drupal\bootstrap\Alter\AlterInterface;
use Drupal\bootstrap\Form\FormInterface;
use Drupal\bootstrap\Preprocess\PreprocessInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * Defines a theme object.
 */
class Theme {

  /**
   * Ignores the following folders during file scans of a theme.
   *
   * @see \Drupal\bootstrap\Theme::IGNORE_ASSETS
   * @see \Drupal\bootstrap\Theme::IGNORE_CORE
   * @see \Drupal\bootstrap\Theme::IGNORE_DOCS
   * @see \Drupal\bootstrap\Theme::IGNORE_DEV
   */
  const IGNORE_DEFAULT = -1;

  /**
   * Ignores the folders "assets", "css", "images" and "js".
   */
  const IGNORE_ASSETS = 0x1;

  /**
   * Ignores the folders "config", "lib" and "src".
   */
  const IGNORE_CORE = 0x2;

  /**
   * Ignores the folders "docs" and "documentation".
   */
  const IGNORE_DOCS = 0x4;

  /**
   * Ignores "bower_components", "grunt", "node_modules" and "starterkits".
   */
  const IGNORE_DEV = 0x8;

  /**
   * Ignores the folders "templates" and "theme".
   */
  const IGNORE_TEMPLATES = 0x16;

  /**
   * The current theme Extension object.
   *
   * @var \Drupal\Core\Extension\Extension
   */
  protected $theme;

  /**
   * Theme handler object.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Theme constructor.
   *
   * @param \Drupal\Core\Extension\Extension $theme
   *   A theme \Drupal\Core\Extension\Extension object.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler object.
   */
  public function __construct(Extension $theme, ThemeHandlerInterface $theme_handler) {
    $this->theme = $theme;
    $this->themeHandler = $theme_handler;
  }

  /**
   * Returns the theme machine name.
   *
   * @return string
   *   Theme machine name.
   */
  public function __toString() {
    return $this->getName();
  }

  /**
   * Manages theme alter hooks as classes and allows sub-themes to sub-class.
   *
   * @param string $function
   *   The procedural function name of the alter (e.g. __FUNCTION__).
   * @param mixed $data
   *   The variable that was passed to the hook_TYPE_alter() implementation to
   *   be altered. The type of this variable depends on the value of the $type
   *   argument. For example, when altering a 'form', $data will be a structured
   *   array. When altering a 'profile', $data will be an object.
   * @param mixed $context1
   *   (optional) An additional variable that is passed by reference.
   * @param mixed $context2
   *   (optional) An additional variable that is passed by reference. If more
   *   context needs to be provided to implementations, then this should be an
   *   associative array as described above.
   */
  public function alter($function, &$data, &$context1 = NULL, &$context2 = NULL) {
    // Immediately return if the active theme is not Bootstrap based.
    if (!$this->subthemeOf('bootstrap')) {
      return;
    }

    // Extract the alter hook name.
    $hook = Utility::extractHook($function, 'alter');

    // Handle form alters separately.
    if (strpos($hook, 'form') === 0) {
      $form_id = $context2;
      if (!$form_id) {
        $form_id = Utility::extractHook($function, 'alter', 'form');
      }

      // Due to a core bug that affects admin themes, we should not double
      // process the "system_theme_settings" form twice in the global
      // hook_form_alter() invocation.
      // @see https://drupal.org/node/943212
      if ($context2 === 'system_theme_settings') {
        return;
      }

      // Retrieve a list of form definitions.
      $form_manager = new FormManager($this);

      /** @var FormInterface $form */
      if ($form_manager->hasDefinition($form_id) && ($form = $form_manager->createInstance($form_id))) {
        $data['#submit'][] = [$form, 'submit'];
        $data['#validate'][] = [$form, 'validate'];
        $form->alter($data, $context1, $context2);
      }
    }
    // Process hook alter normally.
    else {
      // Retrieve a list of alter definitions.
      $alter_manager = new AlterManager($this);

      /** @var AlterInterface $class */
      if ($alter_manager->hasDefinition($hook) && ($class = $alter_manager->createInstance($hook))) {
        $class->alter($data, $context2, $context2);
      }
    }
  }

  /**
   * Wrapper for the core file_scan_directory() function.
   *
   * Finds all files that match a given mask in the given directories and then
   * caches the results. A general site cache clear will force new scans to be
   * initiated for already cached directories.
   *
   * @param string $mask
   *   The preg_match() regular expression of the files to find.
   * @param string|array $dir
   *   The base directory or URI to scan, without trailing slash. If not set,
   *   the current theme path will be used.
   * @param array $options
   *   Options to pass, see file_scan_directory() for addition options:
   *   - ignore_flags: (int|FALSE) A bitmask to indicate which directories (if
   *     any) should be skipped during the scan. Must also not contain a
   *     "nomask" property in $options. Value can be any of the following:
   *     - \Drupal\bootstrap::IGNORE_CORE
   *     - \Drupal\bootstrap::IGNORE_ASSETS
   *     - \Drupal\bootstrap::IGNORE_DOCS
   *     - \Drupal\bootstrap::IGNORE_DEV
   *     - \Drupal\bootstrap::IGNORE_THEME
   *     Pass FALSE to iterate over all directories in $dir.
   *
   * @return array
   *   An associative array (keyed on the chosen key) of objects with 'uri',
   *   'filename', and 'name' members corresponding to the matching files.
   *
   * @see file_scan_directory()
   */
  public function fileScan($mask, $dir = NULL, array $options = []) {
    if (!isset($dir)) {
      $dir = $this->getPath();
    }

    // Default ignore flags.
    $options += [
      'ignore_flags' => static::IGNORE_DEFAULT,
    ];
    $flags = $options['ignore_flags'];
    if ($flags === static::IGNORE_DEFAULT) {
      $flags = static::IGNORE_CORE | static::IGNORE_ASSETS | static::IGNORE_DOCS | static::IGNORE_DEV;
    }

    // Save effort by skipping directories that are flagged.
    if (!isset($options['nomask']) && $flags) {
      $ignore_directories = [];
      if ($flags & static::IGNORE_ASSETS) {
        $ignore_directories += ['assets', 'css', 'images', 'js'];
      }
      if ($flags & static::IGNORE_CORE) {
        $ignore_directories += ['config', 'lib', 'src'];
      }
      if ($flags & static::IGNORE_DOCS) {
        $ignore_directories += ['docs', 'documentation'];
      }
      if ($flags & static::IGNORE_DEV) {
        $ignore_directories += ['bower_components', 'grunt', 'node_modules', 'starterkits'];
      }
      if ($flags & static::IGNORE_TEMPLATES) {
        $ignore_directories += ['templates', 'theme'];
      }
      if (!empty($ignore_directories)) {
        $options['nomask'] = '/^' . implode('|', $ignore_directories) . '$/';
      }
    }

    // Retrieve cache.
    $files = static::getCache('files', []);

    // Generate a unique hash for all parameters passed as a change in any of
    // them could potentially return different results.
    $hash = Utility::generateHash($mask, $dir, $options);

    if (!$files->has($hash)) {
      $files->set($hash, file_scan_directory($dir, $mask, $options));
    }
    return $files->get($hash, []);
  }

  /**
   * Retrieves the full base/sub-theme ancestry of a theme.
   *
   * @param bool $reverse
   *   Whether or not to return the array of themes in reverse order, where the
   *   active theme is the first entry.
   *
   * @return \Drupal\bootstrap\Theme[]
   *   An associative array of \Drupal\bootstrap objects (theme), keyed
   *   by machine name.
   */
  public function getAncestry($reverse = FALSE) {
    static $themes;
    if (!isset($themes)) {
      $themes = $this->themeHandler->listInfo();
    }
    $ancestry = $this->themeHandler->getBaseThemes($themes, $this->getName());
    foreach (array_keys($ancestry) as $name) {
      $ancestry[$name] = new static($this->themeHandler->getTheme($name), $this->themeHandler);
    }
    $ancestry[$this->getName()] = $this;
    return $reverse ? array_reverse($ancestry) : $ancestry;
  }

  /**
   * Retrieves the theme's cache from the database.
   *
   * @return \Drupal\bootstrap\Storage
   *   The cache object.
   */
  public function getStorage() {
    static $cache = [];
    $theme = $this->getName();
    if (!isset($cache[$theme])) {
      $cache[$theme] = new Storage($theme);
    }
    return $cache[$theme];
  }

  /**
   * Retrieves an individual item from a theme's cache in the database.
   *
   * @param string $name
   *   The name of the item to retrieve from the theme cache.
   * @param mixed $default
   *   The default value to use if $name does not exist.
   *
   * @return mixed|\Drupal\bootstrap\StorageItem
   *   The cached value for $name.
   */
  public function getCache($name, $default = []) {
    static $cache = [];
    $theme = $this->getName();
    $theme_cache = static::getStorage();
    if (!isset($cache[$theme][$name])) {
      if (!$theme_cache->has($name)) {
        $theme_cache->set($name, is_array($default) ? new StorageItem($default, $theme_cache) : $default);
      }
      $cache[$theme][$name] = $theme_cache->get($name);
    }
    return $cache[$theme][$name];
  }

  /**
   * Returns the machine name of the theme.
   *
   * @return string
   *   The machine name of the theme.
   */
  public function getName() {
    return $this->theme->getName();
  }

  /**
   * Returns the relative path of the theme.
   *
   * @return string
   *   The relative path of the theme.
   */
  public function getPath() {
    return $this->theme->getPath();
  }

  /**
   * Retrieves a theme setting.
   *
   * @param string $name
   *   The name of the setting to be retrieved.
   *
   * @return mixed
   *   The value of the requested setting, NULL if the setting does not exist.
   *
   * @see theme_get_setting()
   */
  public function getSetting($name) {
    return theme_get_setting($name, $this->getName());
  }

  /**
   * Includes a file from the theme.
   *
   * @param string $file
   *   The file name, including the extension.
   * @param string $path
   *   The path to the file in the theme. Defaults to: "includes". Set to FALSE
   *   or and empty string if the file resides in the theme's root directory.
   *
   * @return bool
   *   TRUE if the file exists and is included successfully, FALSE otherwise.
   */
  public function includeOnce($file, $path = 'includes') {
    static $includes = [];
    if (strpos($file, '/') !== 0) {
      $file = "/$file";
    }
    if (is_string($path) && !empty($path) && strpos($path, '/') !== 0) {
      $path = "/$path";
    }
    else {
      $path = '';
    }
    $include = DRUPAL_ROOT . base_path() . $this->getPath() . $path . $file;
    if (!isset($includes[$include])) {
      $includes[$include] = @include_once $include;
      if (!$includes[$include]) {
        drupal_set_message(t('Could not include file: @include', ['@include' => $include]), 'error');
      }
    }
    return $includes[$include];
  }

  /**
   * Preprocess theme hook variables.
   *
   * @param array $variables
   *   The variables array, passed by reference.
   * @param string $hook
   *   The name of the theme hook.
   */
  public function preprocess(array &$variables, $hook) {
    // Add extra variables to all theme hooks.
    $extra = [
      // Allow #context to be passed to every template and theme function.
      // @see https://drupal.org/node/2035055
      'context' => [],

      // Allow #icon to be passed to every template and theme function.
      // @see https://drupal.org/node/2219965
      'icon' => NULL,
      'icon_position' => 'before',
    ];
    foreach ($extra as $key => $value) {
      if (!isset($variables[$key])) {
        $variables[$key] = $value;
      }
    }

    // Retrieve a list of preprocess definitions.
    $preprocess_manager = new PreprocessManager($this);

    $hooks = array_unique([$hook, $variables['theme_hook_original']]);
    foreach ($hooks as $hook) {
      /** @var PreprocessInterface $class */
      if ($preprocess_manager->hasDefinition($hook) && ($class = $preprocess_manager->createInstance($hook))) {
        $class->preprocess($variables);
      }
    }

  }

  /**
   * Determines whether or not a theme is a sub-theme of another.
   *
   * @param string|\Drupal\bootstrap\Theme $theme
   *   The name or theme Extension object to check.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function subthemeOf($theme) {
    return (string) $theme === $this->getName() || in_array($theme, array_keys(static::getAncestry()));
  }

}
