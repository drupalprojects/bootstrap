<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\FormElement.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
use Drupal\Core\Form\FormState;

/**
 * Pre-processes variables for the "form_element" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "form_element"
 * )
 */
class FormElement implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $element = &$variables['element'];
    $title_display = $element['#title_display'];
    $name = !empty($element['#name']) ? $element['#name'] : FALSE;
    $type = !empty($element['#type']) ? $element['#type'] : FALSE;
    $checkbox = $type && $type === 'checkbox';
    $radio = $type && $type === 'radio';
    $has_tooltip = FALSE;

    // This function is invoked as theme wrapper, but the rendered form element
    // may not necessarily have been processed by Drupal::formBuilder()->doBuildForm().
    $element += ['#title_display' => 'before'];

    // Check for errors and set correct error class.
    $formState = new FormState();
    if ((isset($element['#parents']) && $formState->getError($element)) || (!empty($element['#required']) && Bootstrap::getTheme()
          ->getSetting('forms_required_has_error'))) {
      $variables['has_error'] = TRUE;
    }

    $path_validator = \Drupal::PathValidator();
    if (!empty($element['#autocomplete_route_name']) && $path_validator->isValid($element['#autocomplete_route_name'])) {
      $variables['is_autocomplete'] = TRUE;
    }

    // See http://getbootstrap.com/css/#forms-controls.
    if (isset($element['#type'])) {
      if ($radio) {
        $variables['is_radio'] =  TRUE;
      }
      elseif ($checkbox) {
        $variables['is_checkbox'] = TRUE;
      }
      elseif ($type != 'hidden') {
        $variables['is_form_group'] = TRUE;
      }
    }

    // If #title is not set, we don't display any label or required marker.
    if (!isset($element['#title'])) {
      $element['#title_display'] = 'none';
    }
    $variables['title_display'] = $element['#title_display'];
    // Add label_display and label variables to template.
    $variables['label_display'] = $element['#title_display'];

    // Place single checkboxes and radios in the label field.
    if (($checkbox || $radio) && $title_display != 'none' && $title_display != 'invisible') {
      $variables['label']['#children'] = $variables['children'];
      unset($variables['children']);
      unset($variables['description']);

      // Pass the label attributes to the label, if available.
      if (isset($variables['element']['#label_attributes'])) {
        $variables['label']['#label_attributes'] = $variables['element']['#label_attributes'];
      }
    }

    // Create variables for #input_group and #input_group_button flags.
    if (isset($element['#input_group'])) {
      $variables['input_group'] = $element['#input_group'];
    }
    if (isset($element['#input_group_button'])) {
      $variables['input_group_button'] = $element['#input_group_button'];
    }

    $prefix = '';
    $suffix = '';
    if (isset($element['#field_prefix']) || isset($element['#field_suffix'])) {
      // Determine if "#input_group" was specified.
      if (!empty($element['#input_group'])) {
        $prefix = [
          '#markup' => '<div class="input-group">' . (isset($element['#field_prefix']) ? '<span class="input-group-addon">' . $element['#field_prefix'] . '</span>' : ''),
        ];
        $suffix = [
          '#markup' => (isset($element['#field_suffix']) ? '<span class="input-group-addon">' . $element['#field_suffix'] . '</span>' : '') . '</div>',
        ];
      }
      // Determine if "#input_group_button" was specified.
      elseif (!empty($element['#input_group_button'])) {
        $prefix = [
          '#markup' => '<div class="input-group">' . (isset($element['#field_prefix']) ? '<span class="input-group-btn">' . $element['#field_prefix'] . '</span>' : ''),
        ];
        $suffix = [
          '#markup' => (isset($element['#field_suffix']) ? '<span class="input-group-btn">' . $element['#field_suffix'] . '</span>' : '') . '</div>',
        ];
      }
      $render = \Drupal::service('renderer');
      $variables['prefix'] = $render->render($prefix);
      $variables['suffix'] = $render->render($suffix);
    }
    else {
      $variables['prefix'] = '';
      $variables['suffix'] = '';
    }
  }

}
