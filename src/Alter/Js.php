<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\Js.
 */

namespace Drupal\bootstrap\Alter;

use \Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Theme;

/**
 * Implements hook_js_alter().
 */
class Js implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(&$js, &$context1 = NULL, &$context2 = NULL) {
    $theme = Bootstrap::getTheme();
    $config = \Drupal::config('bootstrap.settings');

    // Exclude specified JavaScript files from theme.
    // @todo add excludes.
    $excludes = $config->get('bootstrap_excludes_js');

    // Add or replace JavaScript files when matching paths are detected.
    // Replacement files must begin with '_', like '_node.js'.
    $flags = Theme::IGNORE_CORE | Theme::IGNORE_DEV | Theme::IGNORE_DOCS;
    $files = $theme->fileScan('/\.js$/', $theme->getPath() . '/js', $flags);
    foreach ($files as $file) {
      if ($file->name == 'bootstrap' || $file->name == 'bootstrap.admin') {
        continue;
      }
      $path = str_replace($theme->getPath() . '/js/', '', $file->uri);
      // Detect if this is a replacement file.
      $replace = FALSE;
      if (preg_match('/^[_]/', $file->filename)) {
        $replace = TRUE;
        $path = dirname($path) . '/' . preg_replace('/^[_]/', '', $file->filename);
      }
      $matches = array();
      if (preg_match('/^modules\/([^\/]*)/', $path, $matches)) {
        if (!\Drupal::moduleHandler()->moduleExists($matches[1])) {
          continue;
        }
        else {
          $path = str_replace('modules/' . $matches[1], drupal_get_path('module', $matches[1]), $path);
        }
      }
      // Path should always exist to either add or replace JavaScript file.
      if (!empty($path) && array_key_exists($path, $js)) {
        $bootstrap_js_defaults = array(
          'type' => 'file',
          'group' => JS_DEFAULT,
          'every_page' => FALSE,
          'weight' => 0,
          'scope' => 'footer',
          'cache' => TRUE,
          'preprocess' => TRUE,
          'attributes' => array(),
          'version' => NULL,
          'data' => $file->uri,
          'browsers' => array(),
        );
        // Replace file.
        if ($replace) {
          $js[$file->uri] = $bootstrap_js_defaults;
          unset($js[$path]);
        }
        // Add file.
        else {
          $js[$file->uri] = $bootstrap_js_defaults;
        }
      }
    }

    if (!empty($excludes)) {
      $js = array_diff_key($js, array_combine($excludes, $excludes));
    }

    // Add CDN assets, if any.
    if ($cdn_assets = bootstrap_get_cdn_assets('js')) {
      $cdn_weight = -99.99;
      foreach ($cdn_assets as $cdn_asset) {
        $cdn_weight += .01;
        $js[$cdn_asset] = drupal_js_defaults($cdn_asset);
        $js[$cdn_asset]['type'] = 'external';
        $js[$cdn_asset]['every_page'] = TRUE;
        $js[$cdn_asset]['scope'] = 'footer';
        $js[$cdn_asset]['weight'] = $cdn_weight;
      }
    }
  }

}
