<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\JsCallbackFilterXss.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Plugin\PluginBase;

/**
 * Implements hook_js_callback_filter_xss_alter().
 *
 * @BootstrapAlter(
 *   id = "js_callback_filter_xss"
 * )
 */
class JsCallbackFilterXss extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$data, &$context1 = NULL, &$context2 = NULL) {
    $data[] = 'button';
  }

}
