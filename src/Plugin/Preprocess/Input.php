<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Input.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "input" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "input"
 * )
 */
class Input implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables) {
    \Drupal\Core\Render\Element::setAttributes($variables['element'], ['id', 'name', 'value', 'type']);

    $element = new Element($variables['element']);

    // Handle button inputs.
    if ($element->isButton()) {
      $element->addClass('btn');
      $element->colorize();
      $element->setIcon($element->getProperty('icon'));
      if ($size = Bootstrap::getTheme()->getSetting('button_size')) {
        $element->addClass($size);
      }
    }

    // Autocomplete fields.
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

    // Search fields.
    if ($element->isType('search')) {
      $element->setAttributes([
        'placeholder' => t('Search'),
        'data-original-title' => t('Enter the terms you wish to search for.'),
      ]);
    }

    // Additional Twig variables.
    $variables['icon'] = $element->getProperty('icon');
    $variables['attributes'] = $element->getAttributes();
    $variables['element'] = $element->getArray();
    $variables['label'] = $element->getProperty('value');
  }

}
