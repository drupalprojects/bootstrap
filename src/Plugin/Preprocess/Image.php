<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Image.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "image" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "image"
 * )
 */
class Image implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $theme = Bootstrap::getTheme();
    $attributes = new Attribute($variables['attributes']);

    // Add image shape, if necessary.
    if ($shape = $theme->getSetting('image_shape')) {
      $attributes->addClass($shape);
    }

    // Add responsiveness, if necessary.
    if ($theme->getSetting('image_responsive')) {
      $attributes->addClass('img-responsive');
    }

    $variables['attributes'] = $attributes->toArray();
  }

}
