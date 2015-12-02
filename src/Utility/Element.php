<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Utility\Element.
 */

namespace Drupal\bootstrap\Utility;

use Drupal\bootstrap\Bootstrap;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Template\Attribute;

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
   * The element type.
   *
   * @var string
   */
  protected $type = FALSE;

  /**
   * Element constructor.
   *
   * @param array $element
   *   A render array element.
   */
  public function __construct(array &$element) {
    $this->element = &$element;
    if (isset($element['#type'])) {
      $this->type = &$element['#type'];
    }
    $attributes = $this->getAttributes();
    if (!$attributes->offsetExists('class')) {
      $attributes->setAttribute('class', []);
    }
  }

  /**
   * Magic get method (for child elements only).
   *
   * @param string $name
   *   The name of the child element to retrieve.
   *
   * @return \Drupal\bootstrap\Utility\Element
   *   The child element object.
   *
   * @throws \InvalidArgumentException
   *   Throws this error when the name is a property (key starting with #).
   */
  public function __get($name) {
    if (\Drupal\Core\Render\Element::property($name)) {
      throw new \InvalidArgumentException('Cannot dynamically retrieve element property. Please use \Drupal\bootstrap\Utility\Element::getProperty instead.');
    }
    if (!isset($this->element[$name])) {
      $this->element[$name] = [];
    }
    return new static($this->element[$name]);
  }

  /**
   * Magic set method (for child elements only).
   *
   * @param string $name
   *   The name of the child element to set.
   * @param mixed $value
   *   The value of $name to set.
   *
   * @throws \InvalidArgumentException
   *   Throws this error when the name is a property (key starting with #).
   */
  public function __set($name, $value) {
    if (\Drupal\Core\Render\Element::property($name)) {
      throw new \InvalidArgumentException('Cannot dynamically retrieve element property. Please use \Drupal\bootstrap\Utility\Element::setProperty instead.');
    }
    $this->element[$name] = $value instanceof Element ? $value->getArray() : $value;
  }

  /**
   * Adds a class to the element's attributes array.
   *
   * @param string|array $class
   *   An individual class or an array of classes to remove.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   */
  public function addClass($class, $property = 'attributes') {
    $this->getAttributes($property)->addClass($class);
  }

  /**
   * Retrieves the children of an element array, optionally sorted by weight.
   *
   * The children of a element array are those key/value pairs whose key does
   * not start with a '#'. See drupal_render() for details.
   *
   * @param bool $sort
   *   Boolean to indicate whether the children should be sorted by weight.
   *
   * @return \Drupal\bootstrap\Utility\Element[]
   *   An array child elements.
   */
  public function children($sort = FALSE) {
    $children = [];
    foreach ($this->childrenKeys($sort) as $child) {
      $children[$child] = new static($this->element[$child]);
    }
    return $children;
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
  public function childrenKeys($sort = FALSE) {
    return \Drupal\Core\Render\Element::children($this->element, $sort);
  }


  /**
   * Adds a specific Bootstrap class to color a button based on its text value.
   */
  public function colorize() {
    $button = $this->isButton();

    // Do nothing if setting is disabled.
    if (!$button || !Bootstrap::getTheme()->getSetting('button_colorize')) {
      return;
    }

    // @todo refactor this more so it's not just "button" specific.
    $prefix = $button ? 'btn' : 'has';

    $attributes = $this->getAttributes();

    // Don't add a class if one is already present in the array.
    $button_classes = [
      "$prefix-default", "$prefix-primary", "$prefix-success", "$prefix-info",
      "$prefix-warning", "$prefix-danger", "$prefix-link",
    ];

    foreach ($button_classes as $class) {
      if ($attributes->hasClass($class)) {
        return;
      }
    }

    $this->addClass("$prefix-" . Bootstrap::cssClassFromString($this->element['#value'], 'default'));
  }

  /**
   * Retrieves the render array for the element.
   *
   * @return array
   *   The render array.
   */
  public function getArray() {
    return $this->element;
  }

  /**
   * Retrieves an element's "attributes" array.
   *
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   *
   * @return \Drupal\Core\Template\Attribute
   *   An attributes object.
   */
  public function getAttributes($property = 'attributes') {
    // Create the attributes if necessary.
    if (!isset($this->element["#$property"])) {
      $this->element["#$property"] = [];
    }

    // Convert to the Attribute object if necessary.
    if (is_array($this->element["#$property"])) {
      $this->element["#$property"] = new Attribute($this->element["#$property"]);
    }

    return $this->element["#$property"];
  }

  /**
   * Retrieves the render array for the element.
   *
   * @param string $name
   *   The name of the child element to retrieve.
   *
   * @return mixed
   *   The property value, NULL if not set.
   */
  public function getProperty($name) {
    if (!\Drupal\Core\Render\Element::property($name)) {
      $name = '#' . $name;
    }
    return isset($this->element[$name]) ? $this->element[$name] : NULL;
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
   * Determines if the element is a button.
   *
   * @return bool
   *   TRUE or FALSE.
   */
  public function isButton() {
    return !empty($this->element['#is_button']) || $this->isType(['button', 'submit', 'reset', 'image_button']);
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
   * Checks if the element is a specific type of element.
   *
   * @param string|array $type
   *   The element type(s) to check.
   *
   * @return bool
   *   TRUE if element is or one of $type.
   */
  public function isType($type) {
    $types = is_array($type) ? $type : [$type];
    return $this->type && in_array($this->type, $types);
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
   * Removes a class from an element's attributes array.
   *
   * @param string|array $class
   *   An individual class or an array of classes to remove.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   */
  public function removeClass($class, $property = 'attributes') {
    $this->getAttributes($property)->removeClass($class);
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
   * Adds an icon to button element based on its text value.
   *
   * @param array $icon
   *   An icon render array.
   *
   * @see \Drupal\bootstrap\Bootstrap::glyphicon()
   */
  public function setIcon(array $icon = NULL) {
    if ($this->isButton() && !Bootstrap::getTheme()->getSetting('button_iconize')) {
      return;
    }
    $icon = isset($icon) ? $icon : Bootstrap::glyphiconFromString($this->getProperty('value'));
    $this->setProperty('icon', $icon);
  }

  /**
   * Magic set method (for child elements only).
   *
   * @param string $name
   *   The name of the child element to set.
   * @param mixed $value
   *   The value of $name to set.
   */
  public function setProperty($name, $value) {
    if (!\Drupal\Core\Render\Element::property($name)) {
      $name = '#' . $name;
    }
    $this->element[$name] = $value instanceof Element ? $value->getArray() : $value;
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
    $theme = Bootstrap::getTheme();

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

    $t = new Element($target);

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
        if (Unicode::isSimple($this->element['#description'], $length, $allowed_tags, $html)) {

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
          $attributes = $t->getAttributes($property);

          // Set the tooltip attributes.
          $attributes->setAttribute('title', $allowed_tags !== FALSE ? Xss::filter((string) $this->element['#description'], $allowed_tags) : $this->element['#description']);
          $attributes->setAttribute('data-toggle', 'tooltip');
          if ($html || $allowed_tags === FALSE) {
            $attributes->setAttribute('data-html', 'true');
          }

          $target["#$property"] = $attributes->toArray();

          // Remove the element description so it isn't (re-)rendered later.
          unset($this->element['#description']);
        }
      }
    }
  }

}
