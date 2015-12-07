<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Links.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;

/**
 * Pre-processes variables for the "links" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "links"
 * )
 */
class Links implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    if (isset($variables['attributes']) && isset($variables['attributes']['class'])) {
      $string = is_string($variables['attributes']['class']);
      if ($string) {
        $variables['attributes']['class'] = explode(' ', $variables['attributes']['class']);
      }

      if ($key = array_search('inline', $variables['attributes']['class'])) {
        $variables['attributes']['class'][$key] = 'list-inline';
      }

      if ($string) {
        $variables['attributes']['class'] = implode(' ', $variables['attributes']['class']);
      }
    }
  }

}
