<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\ThemeSuggestions.
 */

namespace Drupal\bootstrap\Alter;

/**
 * Implements hook_theme_suggestions_alter().
 *
 * @BootstrapAlter(
 *   id = "theme_suggestions"
 * )
 */
class ThemeSuggestions implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$suggestions, &$variables = NULL, &$hook = NULL) {
    switch ($hook) {
      case 'details':
        $suggestions[] = 'bootstrap_panel';
        break;

      case 'input':
        if (!empty($variables['element']['#is_button'])) {
          $suggestions[] = 'input__button';
        }
        break;
    }
  }

}
