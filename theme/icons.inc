<?php

/**
 * @file
 * icons.inc
 * Provide Bootstrap Glyphicons support via the Icon API module.
 */

require_once dirname(__FILE__) . '/common.inc';

/**
 * Implements hook_icon_providers().
 */
function bootstrap_icon_providers() {
  $providers['bootstrap'] = array(
    'title' => t('Bootstrap'),
    'url' => 'http://getbootstrap.com/components/#glyphicons',
  );
  return $providers;
}

/**
 * Implements hook_icon_bundles().
 */
function bootstrap_icon_bundles() {
  $theme = variable_get('theme_default', $GLOBALS['theme']);
  $cdn = theme_get_setting('bootstrap_cdn', $theme);
  $bundles = array();
  if ($cdn || (!$cdn && file_exists(drupal_get_path('theme', $theme) . '/bootstrap/fonts/glyphicons-halflings-regular.ttf'))) {
    $bundles['bootstrap'] = array(
      'render' => 'sprite',
      'provider' => 'bootstrap',
      'title' => t('Bootstrap'),
      'version' => t('Icons by Glyphicons'),
      'variations' => array(
        'icon-white' => t('White'),
      ),
      'settings' => array(
        'tag' => 'span',
      ),
      'icons' => _bootstrap_glyphicons(),
    );
  }
  return $bundles;
}
