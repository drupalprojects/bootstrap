<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Utility\Attributes.
 */

namespace Drupal\bootstrap\Utility;

/**
 * Class to help modify attributes.
 */
class Attributes extends ArrayObject {

  /**
   * {@inheritdoc}
   */
  public function __construct(array &$array = []) {
    $this->array = &$array;
  }

  /**
   * Adds a class to the element's attributes array.
   *
   * @param string|array $class
   *   An individual class or an array of classes to add.
   */
  public function addClass($class) {
    $classes = &$this->getClasses();
    $classes = array_unique(array_merge($classes, (array) $class));
  }

  /**
   * Retrieves the "class" array.
   *
   * @return array
   *   The classes array, passed by reference.
   */
  public function &getClasses() {
    $classes = &$this->offsetGet('class', []);
    $classes = array_unique($classes);
    return $classes;
  }

  /**
   * Indicates whether a specific class is present in the attributes array.
   *
   * @param string $class
   *   The class to search for.
   */
  public function hasClass($class) {
    return array_search($class, $this->getClasses()) !== FALSE;
  }

  /**
   * Removes a class from an element's attributes array.
   *
   * @param string|array $class
   *   An individual class or an array of classes to remove.
   */
  public function removeClass($class) {
    $classes = &$this->getClasses();
    $classes = array_values(array_diff($classes, (array) $class));
  }

  /**
   * Replaces a class in an element's attributes array.
   *
   * @param string $old
   *   The old class to remove.
   * @param string $new
   *   The new class. It will not be added if the $old class does not exist.
   */
  public function replaceClass($old, $new) {
    $classes = &$this->getClasses();
    $key = array_search($old, $classes);
    if ($key !== FALSE) {
      $classes[$key] = $new;
    }
  }

}
