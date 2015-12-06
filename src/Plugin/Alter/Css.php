<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\Css.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Bootstrap;

/**
 * Implements hook_css_alter().
 *
 * @BootstrapAlter(
 *   id = "css"
 * )
 */
class Css implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$css, &$context1 = NULL, &$context2 = NULL) {
    $theme = Bootstrap::getTheme();

    // @todo Refactor to use libraries properly.
    $provider = $theme->getProvider();
    if ($assets = $provider->getAssets('css')) {
      $cdn_weight = -2.99;
      foreach ($assets as $asset) {
        $cdn_weight += .01;
        $css[$asset] = [
          'data' => $asset,
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
      $version = $theme->getSetting('cdn_' . $provider->getPluginId() . '_version');
      if (!$version) {
        $version = Bootstrap::FRAMEWORK_VERSION;
      }
      $provider_theme = $theme->getSetting('cdn_' . $provider->getPluginId() . '_theme');
      if (!$provider_theme) {
        $provider_theme = 'bootstrap';
      }
      $provider_theme = $provider_theme === 'bootstrap' || $provider_theme === 'bootstrap_theme' ? '' : "-$provider_theme";

      foreach ($theme->getAncestry(TRUE) as $ancestor) {
        $overrides = $ancestor->getPath() . "/css/$version/overrides$provider_theme.min.css";
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
          break;
        }
      }
    }
  }

}
