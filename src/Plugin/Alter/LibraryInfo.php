<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\LibraryInfo.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\Component\Utility\NestedArray;

/**
 * Implements hook_css_alter().
 *
 * @BootstrapAlter("library_info")
 */
class LibraryInfo extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$libraries, &$extension = NULL, &$context2 = NULL) {
    if ($extension === 'bootstrap') {
      $provider = $this->theme->getProvider();
      if ($assets = $provider->getAssets()) {
        $libraries['base-theme'] = NestedArray::mergeDeepArray([$assets, $libraries['base-theme']], TRUE);

        // Add a specific version and theme CSS overrides file.
        // @todo This should be retrieved by the Provider API.
        $version = $this->theme->getSetting('cdn_' . $provider->getPluginId() . '_version') ?: Bootstrap::FRAMEWORK_VERSION;
        $libraries['base-theme']['version'] = $version;
        $provider_theme = $this->theme->getSetting('cdn_' . $provider->getPluginId() . '_theme') ?: 'bootstrap';
        $provider_theme = $provider_theme === 'bootstrap' || $provider_theme === 'bootstrap_theme' ? '' : "-$provider_theme";

        foreach ($this->theme->getAncestry(TRUE) as $ancestor) {
          $overrides = $ancestor->getPath() . "/css/$version/overrides$provider_theme.min.css";
          if (file_exists($overrides)) {
            // Since this uses a relative path to the ancestor from DRUPAL_ROOT,
            // we must prefix the entire path with / so it doesn't append the
            // active theme's path (which would duplicate the prefix).
            $libraries['base-theme']['css']['theme']["/$overrides"] = [];
            break;
          }
        }
      }
    }
  }

}
