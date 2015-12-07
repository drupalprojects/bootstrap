<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\InputTextfield.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "input__textfield" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "input__textfield"
 * )
 */
class InputTextfield extends Input implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $element = new Element($variables['element']);

    // Autocomplete fields.
    // @todo this isn't working because pathValidator isn't for routes.
    $path_validator = \Drupal::pathValidator();
    $route = $element->getProperty('autocomplete_route_name');
    if (!empty($route) && $path_validator->isValid($route)) {
      $variables['autocomplete'] = TRUE;

      // Attributes for hidden input field.
      $autocomplete_attributes = new Attribute();
      $autocomplete_attributes['type'] = 'hidden';
      $autocomplete_attributes['id'] = $element->getProperty('attributes')['id'] . '-autocomplete';
      $autocomplete_attributes['value'] = \Drupal::Url($route, $element->getProperty('autocomplete_route_parameters'));
      $autocomplete_attributes['disabled'] = 'disabled';
      $autocomplete_attributes['class'] = 'autocomplete';

      // Uses icon for autocomplete "throbber".
      $icon = Bootstrap::glyphicon('refresh');

      // Fallback to using core's throbber.
      if (empty($icon)) {
        $icon = [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'ajax-progress',
              'ajax-progress-throbber',
              'invisible',
            ],
          ],
          'throbber' => [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => [
              'class' => ['throbber'],
            ],
          ],
        ];
      }
      $variables['autocomplete_icon'] = $icon;
      $variables['autocomplete_attributes'] = $autocomplete_attributes;
    }

    parent::preprocess($variables, $hook, $info);
  }

}
