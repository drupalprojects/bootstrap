<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\MenuLocalTask.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;

/**
 * Pre-processes variables for the "menu_local_task" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "menu_local_task"
 * )
 */
class MenuLocalTask implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    if (!empty($variables['element']['#active'])) {
      $variables['attributes']['class'][] = 'active';
    }
  }

}
