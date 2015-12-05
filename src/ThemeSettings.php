<?php
/**
 * @file
 * Contains \Drupal\bootstrap\ThemeSettings.
 */

namespace Drupal\bootstrap;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\StorageException;

/**
 * Provides a configuration API wrapper for runtime merged theme settings.
 *
 * Theme settings use configuration for base values but the runtime theme
 * settings are calculated based on various site settings and are therefore
 * not persisted.
 *
 * @see theme_get_setting()
 */
class ThemeSettings extends Config {

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
      $this->merge($cache->getAll());
      return;
    }

    // Get the global settings from configuration.
    $this->setData(\Drupal::config('system.theme.global')->get());

    // Merge in default values from theme setting plugin discovery (code).
    $this->merge($theme->getSettingDefaults());

    // Iterate over the theme's ancestry and retrieve all config overrides.
    foreach ($theme->getAncestry() as $name => $ancestor) {
      // Retrieve configured theme-specific settings, if any.
      try {
        if ($theme_settings = \Drupal::config($ancestor->getName() . '.settings')->get()) {
          $this->merge($theme_settings);
        }
      }
      catch (StorageException $e) {
      }

      // If the theme does not support a particular feature, override the
      // global setting and set the value to NULL.
      $info = $ancestor->getInfo();
      if (!empty($info['features'])) {
        foreach (_system_default_theme_features() as $feature) {
          if (!in_array($feature, $info['features'])) {
            $this->set('features.' . $feature, NULL);
          }
        }
      }

      // Generate the path to the logo image.
      if ($this->get('logo.use_default')) {
        $this->set('logo.url', file_create_url($ancestor->getPath() . '/logo.svg'));
      }
      elseif ($logo_path = $this->get('logo.path')) {
        $this->set('logo.url', file_create_url($logo_path));
      }

      // Generate the path to the favicon.
      if ($this->get('features.favicon')) {
        $favicon_path = $this->get('favicon.path');
        if ($this->get('favicon.use_default')) {
          if (file_exists($favicon = $ancestor->getPath() . '/favicon.ico')) {
            $this->set('favicon.url', file_create_url($favicon));
          }
          else {
            $this->set('favicon.url', file_create_url('core/misc/favicon.ico'));
          }
        }
        elseif ($favicon_path) {
          $this->set('favicon.url', file_create_url($favicon_path));
        }
        else {
          $this->set('features.favicon', FALSE);
        }
      }
    }

    // Cache the data.
    $cache->setMultiple($this->get());
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
  public function save($has_trusted_data = FALSE) {
    parent::save($has_trusted_data);
    $this->theme->getCache('settings')->deleteAll();
    return $this;
  }

}
