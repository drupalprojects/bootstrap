<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\ThemeSuggestions.
 */

namespace Drupal\bootstrap\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Unicode;
use Drupal\bootstrap\Utility\Variables;

/**
 * Implements hook_theme_suggestions_alter().
 *
 * @ingroup plugins_alter
 *
 * @BootstrapAlter("theme_suggestions")
 */
class ThemeSuggestions extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$suggestions, &$context1 = NULL, &$hook = NULL) {
    $variables = Variables::create($context1);

    switch ($hook) {
      case 'links':
        if (Unicode::strpos($variables['theme_hook_original'], 'links__dropbutton') !== FALSE) {
          // Handle dropbutton "subtypes".
          // @see \Drupal\bootstrap\Plugin\Prerender\Dropbutton::preRenderElement()
          if ($suggestion = Unicode::substr($variables['theme_hook_original'], 17)) {
            $suggestions[] = 'bootstrap_dropdown' . $suggestion;
          }
          $suggestions[] = 'bootstrap_dropdown';
        }
        break;

      case 'fieldset':
      case 'details':
        if ($variables->element && $variables->element->getProperty('bootstrap_panel', TRUE)) {
          $suggestions[] = 'bootstrap_panel';
        }
        break;

      case 'input':
        if ($variables->element && $variables->element->isButton()) {
          if ($variables->element->getProperty('dropbutton')) {
            $suggestions[] = 'input__button__dropdown';
          }
          else {
            $suggestions[] = $variables->element->getProperty('split') ? 'input__button__split' : 'input__button';
          }
        }
        elseif ($variables->element && !$variables->element->isType(['checkbox', 'hidden', 'radio'])) {
          $suggestions[] = 'input__form_control';
        }
        break;
    }
  }

}
