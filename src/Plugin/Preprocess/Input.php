<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Input.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Plugin\PluginBase;
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
class Input extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $element = Element::create($variables['element']);
    $element->map(['id', 'name', 'value', 'type']);

    // Autocomplete.
    if ($route = $element->getProperty('autocomplete_route_name')) {
      $variables['autocomplete'] = TRUE;

      // Use an icon for autocomplete "throbber".
      $icon = Bootstrap::glyphicon('refresh', [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => [
          'class' => ['ajax-progress', 'ajax-progress-throbber', 'invisible'],
        ],
        'throbber' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => ['class' => ['throbber']],
        ],
      ]);

      $element->setProperty('input_group', TRUE);
      $element->setProperty('field_suffix', $icon);
    }

    $variables['attributes'] = new Attribute($element->getAttributes());
    $variables['icon'] = $element->getProperty('icon');
    $variables['input_group'] = $element->hasProperty('input_group') || $element->hasProperty('input_group_button');
    $variables['type'] = $element->getProperty('type');

    // Create variables for #input_group and #input_group_button flags.
    if ($variables['input_group']) {
      $input_group_attributes = ['class' => ['input-group-' . ($element->hasProperty('input_group_button') ? 'btn' : 'addon')]];
      if ($element->hasProperty('field_prefix')) {
        $variables['prefix'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => $input_group_attributes,
          '#value' => Element::create($element->getProperty('field_prefix'))->render(),
          '#weight' => -1,
        ];
        $element->setProperty('field_prefix', NULL);
      }
      if ($element->hasProperty('field_suffix')) {
        $variables['suffix'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => $input_group_attributes,
          '#value' => Element::create($element->getProperty('field_suffix'))->render(),
          '#weight' => 1,
        ];
        $element->setProperty('field_suffix', NULL);
      }
    }

  }

}
