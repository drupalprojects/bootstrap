<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\FormElement.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
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

    // Set errors flag.
    $variables['errors'] = $element->hasProperty('has_error');

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

    // Remove the #field_prefix and #field_suffix values set in
    // template_preprocess_form_element(). These are handled on the input level.
    // @see \Drupal\bootstrap\Plugin\Preprocess\Input::preprocess().
    if ($element->hasProperty('input_group') || $element->hasProperty('input_group_button')) {
      $variables['prefix'] = FALSE;
      $variables['suffix'] = FALSE;
    }

  }

}
