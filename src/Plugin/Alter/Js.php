<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\Js.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Theme;

/**
 * Implements hook_js_alter().
 *
 * @BootstrapAlter(
 *   id = "js"
 * )
 */
class Js implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$js, &$context1 = NULL, &$context2 = NULL) {
    $theme = Bootstrap::getTheme();

    // @todo Refactor to use libraries properly.
    foreach ($theme->getAncestry() as $ancestor) {
      $files = $ancestor->fileScan('/\.js$/', 'js', ['ignore_flags' => Theme::IGNORE_CORE | Theme::IGNORE_DEV | Theme::IGNORE_DOCS]);
      foreach ($files as $file) {
        if ($file->name == 'bootstrap' || $file->name == 'bootstrap.admin') {
          continue;
        }

        $path = str_replace($ancestor->getPath() . '/js/', '', $file->uri);

        // Detect if this is a replacement file.
        $replace = FALSE;
        if (preg_match('/^[_]/', $file->filename)) {
          $replace = TRUE;
          $path = dirname($path) . '/' . preg_replace('/^[_]/', '', $file->filename);
        }
        $matches = [];
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
          // Replace file.
          if ($replace) {
            $js[$path]['data'] = $file->uri;
          }
          // Add file.
          else {
            $js[$file->uri] = [
              'type' => 'file',
              'group' => JS_DEFAULT,
              'every_page' => FALSE,
              'weight' => 0,
              'scope' => 'footer',
              'cache' => TRUE,
              'preprocess' => TRUE,
              'attributes' => [],
              'version' => NULL,
              'data' => $file->uri,
              'browsers' => [],
            ];
          }
        }
      }
    }

    // Add CDN assets, if any.
    if ($assets = $theme->getProvider()->getAssets('js')) {
      $weight = -99.99;
      foreach ($assets as $asset) {
        $weight += .01;
        $js[$asset] = drupal_js_defaults($asset);
        $js[$asset]['type'] = 'external';
        $js[$asset]['every_page'] = TRUE;
        $js[$asset]['scope'] = 'footer';
        $js[$asset]['weight'] = $weight;
      }
    }
  }

}
