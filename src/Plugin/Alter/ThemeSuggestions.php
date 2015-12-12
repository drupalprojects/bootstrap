<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\ThemeSuggestions.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Element;

/**
 * Implements hook_theme_suggestions_alter().
 *
 * @BootstrapAlter("theme_suggestions")
 */
class ThemeSuggestions extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$suggestions, &$variables = NULL, &$hook = NULL) {
    switch ($hook) {
      case 'fieldset':
      case 'details':
        $suggestions[] = 'bootstrap_panel';
        break;

      case 'input':
        $element = Element::create($variables['element']);
        if ($element->isButton()) {
          $suggestions[] = 'input__button';
        }
        elseif (!$element->isType(['checkbox', 'hidden', 'radio'])) {
          $suggestions[] = 'input__form_control';
        }
        break;
    }
  }

}
