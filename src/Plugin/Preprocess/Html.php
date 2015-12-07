<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Html.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;

/**
 * Pre-processes variables for the "html" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "html"
 * )
 */
class Html implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    switch (Bootstrap::getTheme()->getSetting('navbar_position')) {
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
