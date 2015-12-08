<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Html.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\PluginBase;

/**
 * Pre-processes variables for the "html" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "html"
 * )
 */
class Html extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    switch ($this->theme->getSetting('navbar_position')) {
      case 'fixed-top':
        $variables['attributes']['class'][] = 'navbar-is-fixed-top';
        break;

      case 'fixed-bottom':
        $variables['attributes']['class'][] = 'navbar-is-fixed-bottom';
        break;

      case 'static-top':
        $variables['attributes']['class'][] = 'navbar-is-static-top';
        break;
    }
  }

}
