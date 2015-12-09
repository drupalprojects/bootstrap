<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Utility\Element.
 */

namespace Drupal\bootstrap\Utility;

use Drupal\bootstrap\Bootstrap;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormStateInterface;

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
   * The current state of the form.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * The element type.
   *
   * @var string
   */
  protected $type = FALSE;

  /**
   * Element constructor.
   *
   * @param array|string $element
   *   A render array element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function __construct(&$element, FormStateInterface $form_state = NULL) {
    if (!is_array($element)) {
      $element = [
        '#markup' => $element,
      ];
    }
    $this->element = &$element;
    $this->formState = $form_state;
    if (isset($element['#type'])) {
      $this->type = &$element['#type'];
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
   * Determines if a child element is set.
   *
   * @param string $name
   *   The name of the child element to check.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function __isset($name) {
    return isset($this->element[$name]);
  }

  /**
   * Adds a class to the element's attributes array.
   *
   * @param string|array $class
   *   An individual class or an array of classes to add.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   */
  public function addClass($class, $property = 'attributes') {
    // Retrieve the element's classes.
    $classes = &$this->getClasses($property);

    // Add the class(es).
    $classes = array_unique(array_merge($classes, (array) $class));
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

    // Don't add a class if one is already present in the array.
    $button_classes = [
      "$prefix-default", "$prefix-primary", "$prefix-success", "$prefix-info",
      "$prefix-warning", "$prefix-danger", "$prefix-link",
    ];

    foreach ($button_classes as $class) {
      if ($this->hasClass($class)) {
        return;
      }
    }

    $this->addClass("$prefix-" . Bootstrap::cssClassFromString($this->element['#value'], 'default'));
  }

  /**
   * Retrieves the render array for the element.
   *
   * @return array
   *   The element render array, passed by reference.
   */
  public function &getArray() {
    return $this->element;
  }

  /**
   * Retrieves a specific attribute from an element's "attributes" array.
   *
   * @param string $name
   *   The specific attribute to retrieve.
   * @param mixed $default
   *   The default value to set if the attribute does not exist.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   *
   * @return array
   *   An attributes array, passed by reference.
   */
  public function &getAttribute($name, $default = NULL, $property = 'attributes') {
    $attributes = &$this->getAttributes($property);
    if (!isset($attributes[$name])) {
      $attributes[$name] = $default;
    }
    return $attributes[$name];
  }

  /**
   * Retrieves an element's entire "attributes" array.
   *
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   *
   * @return array
   *   An attributes array, passed by reference.
   */
  public function &getAttributes($property = 'attributes') {
    $attributes = &$this->getProperty($property, []);
    return $attributes;
  }

  /**
   * Retrieves an element's "class" array.
   *
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   *
   * @return array
   *   The classes array, passed by reference.
   */
  public function &getClasses($property = 'attributes') {
    $classes = &$this->getAttribute('class', [], $property);
    $classes = array_unique($classes);
    return $classes;
  }

  /**
   * Returns the error message filed against the given form element.
   *
   * Form errors higher up in the form structure override deeper errors as well
   * as errors on the element itself.
   *
   * @return string|null
   *   Either the error message for this element or NULL if there are no errors.
   *
   * @throws \BadMethodCallException
   *   When the element instance was not constructed with a valid form state
   *   object.
   */
  public function getError() {
    if (!$this->formState) {
      throw new \BadMethodCallException('The element instance must be constructed with a valid form state object to use this method.');
    }
    return $this->formState->getError($this->element);
  }

  /**
   * Retrieves the render array for the element.
   *
   * @param string $name
   *   The name of the child element to retrieve.
   * @param mixed $default
   *   The default to set if property does not exist.
   *
   * @return mixed
   *   The property value, NULL if not set.
   */
  public function &getProperty($name, $default = NULL) {
    if (!\Drupal\Core\Render\Element::property($name)) {
      $name = '#' . $name;
    }
    if (!isset($this->element[$name])) {
      $this->element[$name] = $default;
    }
    return $this->element[$name];
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
   * Determines if an element's attributes array has a specific attribute.
   *
   * @param string $name
   *   The attribute to search for.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   */
  public function hasAttribute($name, $property = 'attributes') {
    return array_search($name, $this->getAttributes($property)) !== FALSE;
  }

  /**
   * Determines if an element's attributes array has a specific class.
   *
   * @param string $class
   *   The class to search for.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   */
  public function hasClass($class, $property = 'attributes') {
    return array_search($class, $this->getClasses($property)) !== FALSE;
  }

  /**
   * Determines if an element has an error set on it.
   *
   * @throws \BadMethodCallException
   *   When the element instance was not constructed with a valid form state
   *   object.
   */
  public function hasError() {
    $error = $this->getError();
    return isset($error);
  }

  /**
   * Determines if an element has a specific property.
   *
   * @param string $name
   *   The property to check.
   */
  public function hasProperty($name) {
    if (!\Drupal\Core\Render\Element::property($name)) {
      $name = '#' . $name;
    }
    return isset($this->element[$name]);
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
   *
   * @return $this
   */
  public function removeClass($class, $property = 'attributes') {
    $classes = &$this->getClasses($property);
    $classes = array_values(array_diff($classes, (array) $class));
    return $this;
  }

  /**
   * Replaces a class in an element's attributes array.
   *
   * @param string $old
   *   The old class to remove.
   * @param string $new
   *   The new class. It will not be added if the $old class does not exist.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   *
   * @return $this
   */
  public function replaceClass($old, $new, $property = 'attributes') {
    $classes = &$this->getClasses($property);
    $key = array_search($old, $classes);
    if ($key !== FALSE) {
      $classes[$key] = $new;
    }
    return $this;
  }

  /**
   * Sets an element's "attributes" array.
   *
   * @param string $name
   *   The name of the attribute to set.
   * @param mixed $value
   *   The value of the attribute to set.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   *
   * @return $this
   */
  public function setAttribute($name, $value, $property = 'attributes') {
    $attributes = &$this->getAttributes($property);
    $attributes[$name] = $value;
    return $this;
  }

  /**
   * Sets an element's "attributes" array.
   *
   * @param array $values
   *   An associative key/value array of attributes to set.
   * @param string $property
   *   Determines which attributes array to retrieve. By default, this is the
   *   element's normal "attributes", but it could also be one of the following:
   *   - "content_attributes"
   *   - "input_group_attributes"
   *   - "title_attributes"
   *   - "wrapper_attributes".
   *
   * @return $this
   */
  public function setAttributes(array $values, $property = 'attributes') {
    $attributes = &$this->getAttributes($property);
    $attributes = NestedArray::mergeDeepArray([$attributes, $values], TRUE);
    return $this;
  }

  /**
   * Flags an element as having an error.
   *
   * @param string $message
   *   (optional) The error message to present to the user.
   *
   * @return $this
   *
   * @throws \BadMethodCallException
   *   When the element instance was not constructed with a valid form state
   *   object.
   */
  public function setError($message = '') {
    if (!$this->formState) {
      throw new \BadMethodCallException('The element instance must be constructed with a valid form state object to use this method.');
    }
    $this->formState->setError($this->element, $message);
    return $this;
  }

  /**
   * Adds an icon to button element based on its text value.
   *
   * @param array $icon
   *   An icon render array.
   *
   * @return $this
   *
   * @see \Drupal\bootstrap\Bootstrap::glyphicon()
   */
  public function setIcon(array $icon = NULL) {
    if ($this->isButton() && !Bootstrap::getTheme()->getSetting('button_iconize')) {
      return $this;
    }
    $icon = isset($icon) ? $icon : Bootstrap::glyphiconFromString($this->getProperty('value'));
    $this->setProperty('icon', $icon);
    return $this;
  }

  /**
   * Magic set method (for child elements only).
   *
   * @param string $name
   *   The name of the property to set.
   * @param mixed $value
   *   The value of $name to set.
   *
   * @return $this
   */
  public function setProperty($name, $value) {
    if (!\Drupal\Core\Render\Element::property($name)) {
      $name = '#' . $name;
    }
    $this->element[$name] = $value instanceof Element ? $value->getArray() : $value;
    return $this;
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
    static $theme;
    if (!isset($theme)) {
      $theme = Bootstrap::getTheme();
    }

    // Determine if tooltips are enabled.
    static $enabled;
    if (!isset($enabled)) {
      $enabled = $theme->getSetting('tooltip_enabled') && $theme->getSetting('forms_smart_descriptions');
    }

    // Immediately return if tooltip descriptions are not enabled.
    if (!$enabled) {
      return;
    }

    // Allow a different element to attach the tooltip.
    $t = new Element(isset($target) ? $target : $this->element);

    // Retrieve the length limit for smart descriptions.
    if (!isset($length)) {
      // Disable length checking by setting it to FALSE if empty.
      $length = (int) $theme->getSetting('forms_smart_descriptions_limit') ?: FALSE;
    }

    // Retrieve the allowed tags for smart descriptions. This is primarily used
    // for display purposes only (i.e. non-UI/UX related elements that wouldn't
    // require a user to "click", like a link). Disable length checking by
    // setting it to FALSE if empty.
    static $allowed_tags;
    if (!isset($allowed_tags)) {
      $allowed_tags = array_filter(array_unique(array_map('trim', explode(',', $theme->getSetting('forms_smart_descriptions_allowed_tags') . '')))) ?: FALSE;
    }

    // Return if element or target shouldn't have "simple" tooltip descriptions.
    $html = FALSE;
    if (($input_only && !$t->hasProperty('input'))
      || !$this->getProperty('smart_description', TRUE)
      || !$t->getProperty('smart_description', TRUE)
      || !$this->hasProperty('description')
      || $t->hasAttribute('title')
      || $t->hasAttribute('data-toggle')
      || !Unicode::isSimple($this->getProperty('description'), $length, $allowed_tags, $html)
    ) {
      return;
    }

    // Default property (on the element itself).
    $property = 'attributes';

    // Use #label_attributes for 'checkbox' and 'radio' elements.
    if ($this->isType(['checkbox', 'radio'])) {
      $property = 'label_attributes';
    }
    // Use #wrapper_attributes for 'checkboxes' and 'radios' elements.
    elseif ($this->isType(['checkboxes', 'radios'])) {
      $property = 'wrapper_attributes';
    }

    // Retrieve the proper attributes array.
    $attributes = &$t->getAttributes($property);

    // Set the tooltip attributes.
    $attributes['title'] = $allowed_tags !== FALSE ? Xss::filter((string) $this->element['#description'], $allowed_tags) : $this->element['#description'];
    $attributes['data-toggle'] = 'tooltip';
    if ($html || $allowed_tags === FALSE) {
      $attributes['data-html'] = 'true';
    }

    // Remove the element description so it isn't (re-)rendered later.
    unset($this->element['#description']);
  }

}
