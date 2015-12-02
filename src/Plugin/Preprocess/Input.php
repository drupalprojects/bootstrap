<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Input.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

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
    $element = new Element($variables['element']);
    $attributes = $element->getAttributes();

    // Set the element's attributes.
    $element->setAttributes(['id', 'name', 'value', 'type']);

    // Handle button inputs.
    if ($element->isButton()) {
      $attributes->addClass('btn');
      $element->colorize();
      $element->setIcon();
      if ($size = Bootstrap::getTheme()->getSetting('button_size')) {
        $attributes->addClass($size);
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
      $attributes->setAttribute('placeholder', t('Search'));
      $attributes->setAttribute('data-original-title', t('Enter the terms you wish to search for.'));
    }

    // Additional Twig variables.
    $variables['icon'] = $element->getProperty('icon');
    $variables['attributes'] = $attributes->toArray();
    $variables['element'] = $element->getArray();
    $variables['label'] = $element->getProperty('value');
  }

}
