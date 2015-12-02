<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\JsCallbackFilterXss.
 */

namespace Drupal\bootstrap\Alter;

/**
 * Implements hook_js_callback_filter_xss_alter().
 */
class JsCallbackFilterXss implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(&$data, &$context1 = NULL, &$context2 = NULL) {
    $data[] = 'button';
  }

}
