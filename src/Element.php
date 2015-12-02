<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Element.
 */

namespace Drupal\bootstrap;

use Drupal\Component\Utility\Xss;

/**
 * Provides helper methods for Drupal render elements.
 *
 * @see \Drupal\Core\Render\Element
 */
class Element {

  /**
   * The render array element.
   *
   * @var array
   */
  protected $element;

  /**
   * Element constructor.
   *
   * @param array $element
   *   A render array element.
   */
  public function __construct(array &$element) {
    $this->element = &$element;
  }

  /**
   * Identifies the children of an element array, optionally sorted by weight.
   *
   * The children of a element array are those key/value pairs whose key does
   * not start with a '#'. See drupal_render() for details.
   *
   * @param bool $sort
   *   Boolean to indicate whether the children should be sorted by weight.
   *
   * @return array
   *   The array keys of the element's children.
   */
  public function children($sort = FALSE) {
    return \Drupal\Core\Render\Element::children($this->element, $sort);
  }

  /**
   * Returns the visible children of an element.
   *
   * @return array
   *   The array keys of the element's visible children.
   */
  public function getVisibleChildren() {
    return \Drupal\Core\Render\Element::getVisibleChildren($this->element);
  }

  /**
   * Indicates whether the given element is empty.
   *
   * An element that only has #cache set is considered empty, because it will
   * render to the empty string.
   *
   * @return bool
   *   Whether the given element is empty.
   */
  public function isEmpty() {
    return \Drupal\Core\Render\Element::isEmpty($this->element);
  }

  /**
   * Determines if an element is visible.
   *
   * @return bool
   *   TRUE if the element is visible, otherwise FALSE.
   */
  public function isVisibleElement() {
    return \Drupal\Core\Render\Element::isVisibleElement($this->element);
  }

  /**
   * Gets properties of a structured array element (keys beginning with '#').
   *
   * @return array
   *   An array of property keys for the element.
   */
  public function properties() {
    return \Drupal\Core\Render\Element::properties($this->element);
  }

  /**
   * Sets HTML attributes based on element properties.
   *
   * @param array $map
   *   An associative array whose keys are element property names and whose
   *   values are the HTML attribute names to set on the corresponding
   *   property; e.g., array('#propertyname' => 'attributename'). If both names
   *   are identical except for the leading '#', then an attribute name value is
   *   sufficient and no property name needs to be specified.
   */
  public function setAttributes(array $map) {
    \Drupal\Core\Render\Element::setAttributes($this->element, $map);
  }

  /**
   * Converts an element description into a tooltip based on certain criteria.
   *
   * @param array $target
   *   The target element render array the tooltip is to be attached to, passed
   *   by reference. If not set, it will default to the $element passed.
   * @param bool $input_only
   *   Toggle determining whether or not to only convert input elements.
   * @param int $length
   *   The length of characters to determine if description is "simple".
   */
  public function smartDescription(array &$target = NULL, $input_only = TRUE, $length = NULL) {
    $theme = BaseTheme::getTheme();

    // Determine if tooltips are enabled.
    static $enabled;
    if (!isset($enabled)) {
      $enabled = $theme->getSetting('tooltip_enabled') && $theme->getSetting('forms_smart_descriptions');
    }

    // Immediately return if "simple" tooltip descriptions are not enabled.
    if (!$enabled) {
      return;
    }

    // Allow a different element to attach the tooltip.
    if (!isset($target)) {
      $target = &$this->element;
    }

    // Retrieve the length limit for smart descriptions.
    if (!isset($length)) {
      $length = (int) $theme->getSetting('forms_smart_descriptions_limit');
      // Disable length checking by setting it to FALSE if empty.
      if (empty($length)) {
        $length = FALSE;
      }
    }

    // Retrieve the allowed tags for smart descriptions. This is primarily used
    // for display purposes only (i.e. non-UI/UX related elements that wouldn't
    // require a user to "click", like a link).
    $allowed_tags = array_filter(array_unique(array_map('trim', explode(',', $theme->getSetting('forms_smart_descriptions_allowed_tags') . ''))));

    // Disable length checking by setting it to FALSE if empty.
    if (empty($allowed_tags)) {
      $allowed_tags = FALSE;
    }

    $html = FALSE;
    $type = !empty($this->element['#type']) ? $this->element['#type'] : FALSE;
    if (!$input_only || !empty($target['#input']) || !empty($this->element['#smart_description']) || !empty($target['#smart_description'])) {
      if (!empty($this->element['#description']) && empty($target['#attributes']['title']) && empty($target['#attributes']['data-toggle'])) {
        if (_bootstrap_is_simple_string($this->element['#description'], $length, $allowed_tags, $html)) {

          // Default property (on the element itself).
          $property = 'attributes';

          // Add the tooltip to the #label_attributes property for 'checkbox'
          // and 'radio' elements.
          if ($type === 'checkbox' || $type === 'radio') {
            $property = 'label_attributes';
          }
          // Add tooltip to the #wrapper_attributes property for 'checkboxes'
          // and 'radios' elements.
          elseif ($type === 'checkboxes' || $type === 'radios') {
            $property = 'attributes';
          }
          // Add tooltip to the #input_group_attributes property for elements
          // that have valid input groups set.
          elseif ((!empty($this->element['#field_prefix']) || !empty($this->element['#field_suffix'])) && (!empty($this->element['#input_group']) || !empty($this->element['#input_group_button']))) {
            $property = 'attributes';
          }

          // Retrieve the proper attributes array.
          $attributes = &_bootstrap_get_attributes($target, $property);

          // Set the tooltip attributes.
          $attributes['title'] = $allowed_tags !== FALSE ? Xss::filter($this->element['#description'], $allowed_tags) : $this->element['#description'];
          $attributes['data-toggle'] = 'tooltip';
          if ($html || $allowed_tags === FALSE) {
            $attributes['data-html'] = 'true';
          }

          // Remove the element description so it isn't (re-)rendered later.
          unset($this->element['#description']);
        }
      }
    }
  }

}
