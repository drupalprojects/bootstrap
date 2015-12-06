<?php
/**
 * @file
 * Contains \Drupal\bootstrap.
 */

namespace Drupal\bootstrap;

use Drupal\bootstrap\Plugin\ProviderManager;
use Drupal\bootstrap\Plugin\SettingManager;
use Drupal\bootstrap\Plugin\UpdateManager;
use Drupal\bootstrap\Utility\Crypt;
use Drupal\bootstrap\Utility\Storage;
use Drupal\bootstrap\Utility\StorageItem;
use Drupal\Core\Config\StorageException;
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
   * The current theme info.
   *
   * @var array
   */
  protected $info;

  /**
   * The provider manager instance.
   *
   * @var \Drupal\bootstrap\Plugin\ProviderManager
   */
  protected $providerManager;

  /**
   * The current theme Extension object.
   *
   * @var \Drupal\Core\Extension\Extension
   */
  protected $theme;

  /**
   * An array of installed themes.
   *
   * @var array
   */
  protected $themes;

  /**
   * Theme handler object.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * The update manager instance.
   *
   * @var \Drupal\bootstrap\Plugin\SettingManager
   */
  protected $settingManager;

  /**
   * The update manager instance.
   *
   * @var \Drupal\bootstrap\Plugin\UpdateManager
   */
  protected $updateManager;

  /**
   * Theme constructor.
   *
   * @param \Drupal\Core\Extension\Extension $theme
   *   A theme \Drupal\Core\Extension\Extension object.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler object.
   */
  public function __construct(Extension $theme, ThemeHandlerInterface $theme_handler) {
    $name = $theme->getName();
    $this->theme = $theme;
    $this->themeHandler = $theme_handler;
    $this->themes = $this->themeHandler->listInfo();
    $this->info = isset($this->themes[$name]->info) ? $this->themes[$name]->info : [];
    $this->providerManager = new ProviderManager($this);
    $this->settingManager = new SettingManager($this);
    $this->updateManager = new UpdateManager($this);

    // Only install the theme if there is no schema version currently set.
    if (!$this->getSchemaVersion()) {
      $this->install();
    }
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
   * Wrapper for the core file_scan_directory() function.
   *
   * Finds all files that match a given mask in the given directories and then
   * caches the results. A general site cache clear will force new scans to be
   * initiated for already cached directories.
   *
   * @param string $mask
   *   The preg_match() regular expression of the files to find.
   * @param string $subdir
   *   Sub-directory in the theme to start the scan, without trailing slash. If
   *   not set, the base path of the current theme will be used.
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
  public function fileScan($mask, $subdir = NULL, array $options = []) {
    $path = $this->getPath();

    // Append addition sub-directories to the path if they were provided.
    if (isset($subdir)) {
      $path .= '/' . $subdir;
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
    $hash = Crypt::generateHash($mask, $path, $options);

    if (!$files->has($hash)) {
      $files->set($hash, file_scan_directory($path, $mask, $options));
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
    $ancestry = $this->themeHandler->getBaseThemes($this->themes, $this->getName());
    foreach (array_keys($ancestry) as $name) {
      $ancestry[$name] = new static($this->themeHandler->getTheme($name), $this->themeHandler);
    }
    $ancestry[$this->getName()] = $this;
    return $reverse ? array_reverse($ancestry) : $ancestry;
  }

  /**
   * Retrieves an individual item from a theme's cache in the database.
   *
   * @param string $name
   *   The name of the item to retrieve from the theme cache.
   * @param mixed $default
   *   The default value to use if $name does not exist.
   *
   * @return mixed|\Drupal\bootstrap\Utility\StorageItem
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
   * Retrieves the theme info.
   *
   * @return array
   *   The theme info.
   */
  public function getInfo() {
    return $this->info;
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
   * Retrieves the CDN provider.
   *
   * @param string $provider
   *   A CDN provider name. Defaults to the provider set in the theme settings.
   *
   * @return \Drupal\bootstrap\Plugin\Provider\ProviderInterface
   *   A provider instance.
   */
  public function getProvider($provider = NULL) {
    return $this->providerManager->createInstance($provider ?: $this->getSetting('cdn_provider'), ['theme' => $this]);
  }

  /**
   * Retrieves all CDN providers.
   *
   * @return \Drupal\bootstrap\Plugin\Provider\ProviderInterface[]
   *   All provider instances.
   */
  public function getProviders() {
    $providers = [];
    foreach (array_keys($this->providerManager->getDefinitions()) as $provider) {
      if ($provider === 'none') {
        continue;
      }
      $providers[$provider] = $this->providerManager->createInstance($provider, ['theme' => $this]);
    }
    return $providers;
  }

  /**
   * Retrieves the installed schema version for the theme.
   *
   * @return int
   *   The schema version, 0 if not yet installed.
   */
  public function getSchemaVersion() {
    // Don't use $this->getSetting() here because we don't want to inherit
    // the schema version from the theme's ancestry.
    try {
      return \Drupal::config($this->getName() . '.settings')->get('schema');
    }
    catch (StorageException $e) {
    }
    return 0;
  }

  /**
   * Retrieves the update schema versions for the theme.
   *
   * @return array
   *   An indexed array of schema versions.
   */
  protected function getSchemaVersions() {
    return array_keys($this->getUpdates());
  }

  /**
   * Retrieves a theme setting.
   *
   * @param string $name
   *   The name of the setting to be retrieved.
   * @param bool $get_default
   *   Retrieve the default value from code, not from any potientially stored
   *   config value.
   *
   * @return mixed
   *   The value of the requested setting, NULL if the setting does not exist.
   *
   * @see theme_get_setting()
   */
  public function getSetting($name, $get_default = FALSE) {
    if ($get_default) {
      $defaults = $this->getSettingDefaults();
      if (isset($defaults[$name])) {
        return $defaults[$name];
      }
    }
    return $this->getSettings()->get($name);
  }

  /**
   * Retrieves the default values from theme setting discovery.
   *
   * @return array
   *   A key/value associative array.
   */
  public function getSettingDefaults() {
    $defaults = [];
    foreach ($this->getSettingInstances() as $name => $setting) {
      $defaults[$name] = $setting->getDefaultValue();
    }
    return $defaults;
  }

  /**
   * Retrieves the theme settings instance.
   *
   * @return \Drupal\bootstrap\ThemeSettings
   *   All settings.
   */
  public function getSettings() {
    static $themes = [];
    $name = $this->getName();
    if (!isset($themes[$name])) {
      $themes[$name] = new ThemeSettings($this, $this->themeHandler);
    }
    return $themes[$name];
  }

  /**
   * Retrieves the theme's setting plugin instances.
   *
   * @return \Drupal\bootstrap\Plugin\Setting\SettingInterface[]
   *   An associative array of setting objects, keyed by their name.
   */
  public function getSettingInstances() {
    $settings = [];
    foreach (array_keys($this->settingManager->getDefinitions()) as $setting) {
      $settings[$setting] = $this->settingManager->createInstance($setting);
    }
    return $settings;
  }

  /**
   * Retrieves the theme's cache from the database.
   *
   * @return \Drupal\bootstrap\Utility\Storage
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
   * Retrieves update plugins for the theme.
   *
   * @return \Drupal\bootstrap\Plugin\Update\UpdateInterface[]
   *   An associative array containing update objects, keyed by their version.
   */
  protected function getUpdates() {
    $updates = [];
    foreach (array_keys($this->updateManager->getDefinitions()) as $update) {
      $updates[$update] = $this->updateManager->createInstance($update, ['theme' => $this]);
    }
    return $updates;
  }

  /**
   * Determines whether or not if the theme has Bootstrap Framework Glyphicons.
   */
  public function hasGlyphicons() {
    $glyphicons = $this->getCache('glyphicons', []);
    if (!$glyphicons->has($this->getName())) {
      $exists = FALSE;
      foreach ($this->getAncestry(TRUE) as $ancestor) {
        if ($ancestor->getSetting('cdn_provider') || $ancestor->fileScan('/glyphicons-halflings-regular\.(eot|svg|ttf|woff)$/', NULL, ['ignore_flags' => FALSE])) {
          $exists = TRUE;
          break;
        }
      }
      $glyphicons->set($this->getName(), $exists);
    }
    return $glyphicons->get($this->getName(), FALSE);
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
    $file = preg_replace('`^' . $this->getPath() . '`', '', $file);
    $file = strpos($file, '/') !== 0 ? $file = "/$file" : $file;
    $path = is_string($path) && !empty($path) && strpos($path, '/') !== 0 ? $path = "/$path" : '';
    $include = DRUPAL_ROOT . base_path() . $this->getPath() . $path . $file;
    if (!isset($includes[$include])) {
      $includes[$include] = !!@include_once $include;
      if (!$includes[$include]) {
        drupal_set_message(t('Could not include file: @include', ['@include' => $include]), 'error');
      }
    }
    return $includes[$include];
  }

  /**
   * Installs a Bootstrap based theme.
   */
  final protected function install() {
    $version = \Drupal::CORE_MINIMUM_SCHEMA_VERSION;
    if ($versions = $this->getSchemaVersions()) {
      $version = max(max($versions), $version);
    }
    $this->setSetting('schema', $version);
  }

  /**
   * Removes a theme setting.
   *
   * @param string $name
   *   Name of the theme setting to remove.
   */
  public function removeSetting($name) {
    $this->getSettings()->clear($name)->save();
  }

  /**
   * Sets a value for a theme setting.
   *
   * @param string $name
   *   Name of the theme setting.
   * @param mixed $value
   *   Value to associate with the theme setting.
   */
  public function setSetting($name, $value) {
    $this->getSettings()->set($name, $value)->save();
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
