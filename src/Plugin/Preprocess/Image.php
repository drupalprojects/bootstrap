<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Image.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\PluginBase;
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
class Image extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $attributes = new Attribute($variables['attributes']);

    // Add image shape, if necessary.
    if ($shape = $this->theme->getSetting('image_shape')) {
      $attributes->addClass($shape);
    }

    // Add responsiveness, if necessary.
    if ($this->theme->getSetting('image_responsive')) {
      $attributes->addClass('img-responsive');
    }

    $variables['attributes'] = $attributes->toArray();
  }

}
