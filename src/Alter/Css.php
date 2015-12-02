<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\Css.
 */

namespace Drupal\bootstrap\Alter;

use \Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\BaseTheme;

/**
 * Implements hook_css_alter().
 */
class Css implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(&$css, &$context1 = NULL, &$context2 = NULL) {
    $theme = BaseTheme::getTheme();

    // Add CDN assets, if any.
    $provider = $theme->getSetting('cdn_provider');
    if ($cdn_assets = bootstrap_get_cdn_assets('css', $provider)) {
      $cdn_weight = -2.99;
      foreach ($cdn_assets as $cdn_asset) {
        $cdn_weight += .01;
        $css[$cdn_asset] = [
          'data' => $cdn_asset,
          'type' => 'external',
          'every_page' => TRUE,
          'media' => 'all',
          'preprocess' => FALSE,
          'group' => CSS_AGGREGATE_THEME,
          'browsers' => ['IE' => TRUE, '!IE' => TRUE],
          'weight' => $cdn_weight,
        ];
      }

      // Add a specific version and theme CSS overrides file.
      $version = $theme->getSetting('cdn_' . $provider . '_version');
      if (!$version) {
        $version = Bootstrap::FRAMEWORK_VERSION;
      }
      $provider_theme = $theme->getSetting('cdn_' . $provider . '_theme');
      if (!$provider_theme) {
        $provider_theme = 'bootstrap';
      }
      $provider_theme = $provider_theme === 'bootstrap' || $provider_theme === 'bootstrap_theme' ? '' : "-$provider_theme";
      $overrides = $theme->getPath() . "/css/$version/overrides$provider_theme.min.css";
      if (file_exists($overrides)) {
        $css[$overrides] = [
          'data' => $overrides,
          'type' => 'file',
          'every_page' => TRUE,
          'media' => 'all',
          'preprocess' => TRUE,
          'group' => CSS_AGGREGATE_THEME,
          'browsers' => ['IE' => TRUE, '!IE' => TRUE],
          'weight' => -1,
        ];
      }
    }
  }

}
