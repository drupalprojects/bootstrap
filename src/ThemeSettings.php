<?php
/**
 * @file
 * Contains \Drupal\bootstrap\ThemeSettings.
 */

namespace Drupal\bootstrap;

use Drupal\Component\Utility\DiffArray;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\StorageException;

/**
 * Provides a configuration API wrapper for runtime merged theme settings.
 *
 * This is a wrapper around theme_get_setting() since it does not inherit
 * base theme config nor handle default/overridden values very well.
 */
class ThemeSettings extends Config {

  /**
   * The default settings.
   *
   * @var array
   */
  protected $defaults;

  /**
   * The current theme object.
   *
   * @var \Drupal\bootstrap\Theme
   */
  protected $theme;

  /**
   * {@inheritdoc}
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme->getName() . '.settings', \Drupal::service('config.storage'), \Drupal::service('event_dispatcher'), \Drupal::service('config.typed'));
    $this->theme = $theme;

    // Retrieve cache.
    $cache = $theme->getCache('settings');

    // Use cached settings.
    if (!$cache->isEmpty()) {
      $this->defaults = $cache->get('defaults');
      $this->initWithData($cache->get('data'));
      return;
    }

    // Retrieve the global settings from configuration.
    $global = \Drupal::config('system.theme.global')->get();

    // Retrieve the theme setting plugin discovery defaults (code).
    $defaults = [];
    foreach ($theme->getSettingPlugins() as $name => $setting) {
      if ($name === 'schema') {
        continue;
      }
      $defaults[$name] = $setting->getDefaultValue();
    }

    // Set the defaults.
    $this->defaults = NestedArray::mergeDeepArray([$global, $defaults], TRUE);

    // Retrieve the theme ancestry.
    $ancestry = $theme->getAncestry();

    // Remove the active theme from the ancestry.
    $active_theme = array_pop($ancestry);

    // Iterate and merge all ancestor theme config into the defaults.
    foreach ($ancestry as $ancestor) {
      $this->defaults = NestedArray::mergeDeepArray([$this->defaults, $this->getThemeConfig($ancestor)], TRUE);
    }

    // Merge the active theme config.
    $this->initWithData($this->getThemeConfig($active_theme, TRUE));

    // Cache the data and defaults.
    $cache->set('data', $this->data);
    $cache->set('defaults', $this->defaults);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return ['rendered'];
  }

  /**
   * {@inheritdoc}
   */
  public function get($key = '') {
    if (empty($key)) {
      return NestedArray::mergeDeepArray([$this->defaults, $this->data], TRUE);
    }
    else {
      $value = parent::get($key);
      if (!isset($value)) {
        $value = $this->getOriginal($key);
      }
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginal($key = '', $apply_overrides = TRUE) {
    $original_data = $this->defaults;
    if ($apply_overrides) {
      // Apply overrides.
      if (isset($this->moduleOverrides) && is_array($this->moduleOverrides)) {
        $original_data = NestedArray::mergeDeepArray(array($original_data, $this->moduleOverrides), TRUE);
      }
      if (isset($this->settingsOverrides) && is_array($this->settingsOverrides)) {
        $original_data = NestedArray::mergeDeepArray(array($original_data, $this->settingsOverrides), TRUE);
      }
    }

    if (empty($key)) {
      return $original_data;
    }
    else {
      $parts = explode('.', $key);
      if (count($parts) == 1) {
        return isset($original_data[$key]) ? $original_data[$key] : NULL;
      }
      else {
        $value = NestedArray::getValue($original_data, $parts, $key_exists);
        return $key_exists ? $value : NULL;
      }
    }
  }

  /**
   * Retrieves a specific theme's stored config settings.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   A theme object.
   * @param bool $active_theme
   *   Flag indicating whether or not $theme is the active theme.
   *
   * @return array
   *   A array diff of overridden config theme settings.
   */
  public function getThemeConfig(Theme $theme, $active_theme = FALSE) {
    $config = new \Drupal\Core\Theme\ThemeSettings($theme->getName());

    // Retrieve configured theme-specific settings, if any.
    try {
      if ($theme_settings = \Drupal::config($theme->getName() . '.settings')->get()) {
        // Remove the schema version if not the active theme.
        if (!$active_theme) {
          unset($theme_settings['schema']);
        }
        $config->merge($theme_settings);
      }
    }
    catch (StorageException $e) {
    }

    // If the theme does not support a particular feature, override the
    // global setting and set the value to NULL.
    $info = $theme->getInfo();
    if (!empty($info['features'])) {
      foreach (_system_default_theme_features() as $feature) {
        if (!in_array($feature, $info['features'])) {
          $config->set('features.' . $feature, NULL);
        }
      }
    }

    // Generate the path to the logo image.
    if ($config->get('logo.use_default')) {
      $config->set('logo.url', file_create_url($theme->getPath() . '/logo.svg'));
    }
    elseif ($logo_path = $config->get('logo.path')) {
      $config->set('logo.url', file_create_url($logo_path));
    }

    // Generate the path to the favicon.
    if ($config->get('features.favicon')) {
      $favicon_path = $config->get('favicon.path');
      if ($config->get('favicon.use_default')) {
        if (file_exists($favicon = $theme->getPath() . '/favicon.ico')) {
          $config->set('favicon.url', file_create_url($favicon));
        }
        else {
          $config->set('favicon.url', file_create_url('core/misc/favicon.ico'));
        }
      }
      elseif ($favicon_path) {
        $config->set('favicon.url', file_create_url($favicon_path));
      }
      else {
        $config->set('features.favicon', FALSE);
      }
    }

    // Return a diff of the overrides from set defaults.
    $diff = DiffArray::diffAssocRecursive($config->get(), $this->defaults);
    return $diff;
  }

  /**
   * Determines if a setting overrides the default value.
   *
   * @param string $name
   *   The name of the setting to check.
   * @param mixed $value
   *   The new value to check.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function overridesValue($name, $value) {
    return !!DiffArray::diffAssocRecursive([$name => $value], [$name => $this->get($name)]);
  }

  /**
   * {@inheritdoc}
   */
  public function save($has_trusted_data = FALSE) {
    parent::save($has_trusted_data);
    $this->theme->getCache('settings')->deleteAll();
    return $this;
  }

}
