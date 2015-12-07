<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Block.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;

/**
 * Pre-processes variables for the "block" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "block"
 * )
 */
class Block implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    // Use a bare template for the page's main content.
    if ($variables['plugin_id'] == 'system_main') {
      $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
    }
    $variables['title_attributes']['class'][] = 'block-title';
    $variables['attributes']['class'][] = 'block';
    $variables['attributes']['class'][] = 'clearfix';
  }

}
