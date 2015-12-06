<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\ProcessManager.
 */

namespace Drupal\bootstrap\Plugin;

use Drupal\bootstrap\Theme;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Manages discovery and instantiation of Bootstrap form process callbacks.
 */
class ProcessManager extends PluginManager {

  /**
   * Constructs a new \Drupal\bootstrap\Plugin\ProcessManager object.
   *
   * @param \Drupal\bootstrap\Theme $theme
   *   The theme to use for discovery.
   */
  public function __construct(Theme $theme) {
    parent::__construct($theme, 'Plugin/Process', 'Drupal\bootstrap\Plugin\Process\ProcessInterface', 'Drupal\bootstrap\Annotation\BootstrapProcess');
    $this->setCacheBackend(\Drupal::cache('discovery'), 'theme:' . $theme->getName() . ':process', $this->getCacheTags());
  }

  /**
   * Global #process callback for form elements.
   *
   * @param array $element
   *   The element render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The altered element array.
   *
   * @see \Drupal\bootstrap\Plugin\Alter\ElementInfo::alter
   */
  public static function process(array $element, FormStateInterface $form_state, array &$complete_form) {
    if (!empty($element['#bootstrap_ignore_process'])) {
      return $element;
    }

    if (!empty($element['#attributes']['class']) && is_array($element['#attributes']['class'])) {
      $key = array_search('container-inline', $element['#attributes']['class']);
      if ($key !== FALSE) {
        $element['#attributes']['class'][$key] = 'form-inline';
      }

      if (in_array('form-wrapper', $element['#attributes']['class'])) {
        $element['#attributes']['class'][] = 'form-group';
      }
    }

    // Automatically inject the nearest button found after this element if
    // #input_group_button exists.
    if (!empty($element['#input_group_button'])) {
      // Obtain the parent array to limit search.
      $array_parents = [];
      if (!empty($element['#array_parents'])) {
        $array_parents += $element['#array_parents'];
        // Remove the current element from the array.
        array_pop($array_parents);
      }

      // If element is nested, return the referenced parent from the form.
      if (!empty($array_parents)) {
        $parent = &NestedArray::getValue($form, $array_parents);
      }
      // Otherwise return the complete form.
      else {
        $parent = &$form;
      }

      // Ignore buttons before we find the element in the form.
      $found_current_element = FALSE;
      foreach (Element::children($parent) as $child) {
        if ($parent[$child] === $element) {
          $found_current_element = TRUE;
          continue;
        }

        if ($found_current_element && (_bootstrap_is_button($parent[$child]) || (is_array($parent[$child]) && _bootstrap_is_button(current($parent[$child]))))) {
          _bootstrap_iconize_button($parent[$child]);
          $element['#field_suffix'] = \Drupal::service('renderer')->render($parent[$child]);
          break;
        }
      }
    }

    return $element;
  }

}
