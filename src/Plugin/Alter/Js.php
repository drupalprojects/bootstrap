<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\Js.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Theme;

/**
 * Implements hook_js_alter().
 *
 * @BootstrapAlter(
 *   id = "js"
 * )
 */
class Js extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$js, &$context1 = NULL, &$context2 = NULL) {
    // @todo Refactor to use libraries properly.
    foreach ($this->theme->getAncestry() as $ancestor) {
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
  }

}
