<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\ProcessManager.
 */

namespace Drupal\bootstrap\Plugin;

use Drupal\bootstrap\Theme;
use Drupal\bootstrap\Utility\Element;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;

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
        $parent = new Element(NestedArray::getValue($form, $array_parents));
      }
      // Otherwise return the complete form.
      else {
        $parent = new Element($complete_form);
      }

      // Ignore buttons before we find the element in the form.
      $current = FALSE;
      foreach ($parent->children() as $child) {
        if ($child->getArray() === $element) {
          $current = $child;
          continue;
        }

        if ($current && $child->isButton()) {
          $child->setIcon();
          $element['#field_suffix'] = $child->getArray();
          break;
        }
      }
    }

    return $element;
  }

}
