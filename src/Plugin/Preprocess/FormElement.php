<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\FormElement.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Element;

/**
 * Pre-processes variables for the "form_element" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "form_element"
 * )
 */
class FormElement extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $element = new Element($variables['element']);

    if ($element->getProperty('autocomplete_route_name')) {
      $variables['is_autocomplete'] = TRUE;
    }

    // See http://getbootstrap.com/css/#forms-controls.
    $checkbox = $variables['is_checkbox'] = $element->isType('checkbox');
    $radio = $variables['is_radio'] = $element->isType('radio');
    $variables['is_form_group'] = !$variables['is_checkbox'] && !$variables['is_radio'] && !$element->isType('hidden');

    // Add label_display and label variables to template.
    $display = $variables['label_display'] = $variables['title_display'] = $element->getProperty('title_display');

    // Place single checkboxes and radios in the label field.
    if (($checkbox || $radio) && $display !== 'none' && $display !== 'invisible') {
      $label = new Element($variables['label']);
      $children = &$label->getProperty('children', '');
      $children .= $variables['children'];
      unset($variables['children']);

      // Pass the label attributes to the label, if available.
      if ($element->hasProperty('label_attributes')) {
        $label->setAttributes($element->getProperty('label_attributes'));
      }
    }

    // Create variables for #input_group and #input_group_button flags.
    $prefix = [];
    $suffix = [];
    if ($element->hasProperty('input_group') || $element->hasProperty('input_group_button')) {
      $input_group_attributes = ['class' => ['input-group-' . ($element->hasProperty('input_group_button') ? 'btn' : 'addon')]];
      if ($element->hasProperty('field_prefix')) {
        $prefix[] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => $input_group_attributes,
          '#value' => $element->getProperty('field_prefix'),
          '#weight' => -1,
        ];
      }
      if ($element->hasProperty('field_suffix')) {
        $suffix[] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => $input_group_attributes,
          '#value' => $element->getProperty('field_suffix'),
          '#weight' => 1,
        ];
      }
    }
    $variables['prefix'] = $prefix;
    $variables['suffix'] = $suffix;

    $variables['errors'] = $element->hasProperty('has_error');
  }

}
