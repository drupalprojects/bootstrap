<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Preprocess\Block.
 */

namespace Drupal\bootstrap\Preprocess;

/**
 * Pre-processes variables for the "block" theme hook.
 *
 * See template for list of available variables.
 *
 * @see block.html.twig
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
  public function preprocess(array &$variables) {
    // Use a bare template for the page's main content.
    if ($variables['plugin_id'] == 'system_main') {
      $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
    }
    $variables['title_attributes']['class'][] = 'block-title';
    $variables['attributes']['class'][] = 'block';
    $variables['attributes']['class'][] = 'clearfix';
  }

}
