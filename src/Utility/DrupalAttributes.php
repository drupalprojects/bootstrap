<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Utility\DrupalAttributes.
 */

namespace Drupal\bootstrap\Utility;

/**
 * Custom ArrayObject implementation.
 *
 * The native ArrayObject is unnecessarily complicated.
 */
class DrupalAttributes extends ArrayObject {

  /**
   * Stored attribute instances.
   *
   * @var \Drupal\bootstrap\Utility\Attributes[]
   */
  protected $attributes = [];

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
   *
   * @return $this
   */
  public function addClass($class, $property = 'attributes') {
    $this->getAttributes($property)->addClass($class);
    return $this;
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
    $attributes = $this->getAttributes($property);
    return $attributes->offsetGet($name, $default);
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
   * @param mixed $default
   *   The default attributes to provide, passed by reference.
   *
   * @return \Drupal\bootstrap\Utility\Attributes
   *   An attributes object.
   */
  public function getAttributes($property = 'attributes', &$default = NULL) {
    if (!isset($default)) {
      $default = &$this->offsetGet($property, []);
    }
    if (!isset($this->attributes[$property])) {
      $this->attributes[$property] = new Attributes($default);
    }
    return $this->attributes[$property];
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
    $classes = $this->getAttributes($property)->getClasses();
    return $classes;
  }

  /**
   * Indicates whether the element has a specific attribute.
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
    return $this->getAttributes($property)->offsetExists($name);
  }

  /**
   * Indicates whether the element has a specific class.
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
    return $this->getAttributes($property)->hasClass($class);
  }

  /**
   * Removes a class from an element's attributes array.
   *
   * @param string|array $name
   *   The name of the attribute to remove.
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
  public function removeAttribute($name, $property = 'attributes') {
    $this->getAttributes($property)->offsetUnset($name);
    return $this;
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
    $this->getAttributes($property)->removeClass($class);
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
    $this->getAttributes($property)->replaceClass($old, $new);
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
    $this->getAttributes($property)->offsetSet($name, $value);
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
    $this->getAttributes($property, $default)->merge($values);
    return $this;
  }


}
