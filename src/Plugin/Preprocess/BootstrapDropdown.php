<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\BootstrapDropdown.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Utility\Unicode;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "bootstrap_dropdown" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess("bootstrap_dropdown")
 */
class BootstrapDropdown extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  protected function preprocessVariables(Variables $variables, $hook, array $info) {
    // Convert "dropbutton" theme suggestion variables.
    if (Unicode::strpos($variables->theme_hook_original, 'links__dropbutton') !== FALSE) {
      $variables->toggle = array_shift($variables->links);
      unset($variables->toggle['#dropbutton']);
      $variables->items = array_values($variables->links);
      $variables->split = TRUE;
      unset($variables->links);
    }

    $toggle = Element::create($variables->toggle);
    $toggle->setProperty('split', $variables->split);

    // Convert the items into a proper item list.
    $variables->items = [
      '#theme' => 'item_list__dropdown',
      '#alignment' => $variables->alignment,
      '#items' => $variables->items,
    ];

    // Ensure all attributes are proper objects.
    $this->preprocessAttributes($variables, $hook, $info);
  }

}
