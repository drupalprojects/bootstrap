<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\IconBundleList.
 */

namespace Drupal\bootstrap\Alter;

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\BaseTheme;

/**
 * Implements hook_icon_bundle_list_alter().
 */
class IconBundleList implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(&$data, &$context1 = NULL, &$context2 = NULL) {
    if (BaseTheme::getTheme()->getSetting('tooltip_enabled')) {
      foreach ($data as &$icon) {
        $icon['#attributes']['data-toggle'] = 'tooltip';
        $icon['#attributes']['data-placement'] = 'bottom';
      }
    }
  }

}
