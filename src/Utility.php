<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Utility.
 */

namespace Drupal\bootstrap;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\Unicode;

/**
 * Contains useful helper methods.
 */
abstract class Utility {

  /**
   * Extracts the hook name from a function name.
   *
   * @param string $function
   *   The function name to extract the hook name from.
   * @param string $suffix
   *   A suffix hook ending (like "alter") to also remove.
   * @param string $prefix
   *   A prefix hook beginning (like "form") to also remove.
   *
   * @return string
   *   The extracted hook name.
   */
  public static function extractHook($function, $suffix = NULL, $prefix = NULL) {
    $regex = '^(' . implode('|', array_keys(Bootstrap::getTheme()->getAncestry())) . ')';
    $regex .= $prefix ? '_' . $prefix : '';
    $regex .= $suffix ? '_|_' . $suffix . '$' : '';
    return preg_replace("/$regex/", '', $function);
  }

  /**
   * Finds proper class to use for the active theme, inheriting theme ancestry.
   *
   * @param string|array $class
   *   A single class name or an array of class names to search for, with out
   *   the namespace. The first matching class will be used.
   * @param string $namespace
   *   An additional namespace to use. Due to how core autoloads namespaces,
   *   any additional namespace provided will automatically be prefixed with
   *   the project namespace (e.g. \Drupal\bootstrap). This will traverse the
   *   active theme's ancestry and will match the first theme that implements
   *   the entire namespace.
   *
   * @return \ReflectionClass|FALSE
   *   The reflection class instance or FALSE if the class could not be found.
   */
  public static function findClass($class, $namespace = NULL) {
    $theme = Bootstrap::getTheme();

    // Retrieve cache.
    $classes = $theme->getCache('classes', []);

    // Generate a unique hash for all parameters passed as a change in any of
    // them could potentially return different results.
    $hash = static::generateHash($class, $namespace);

    // Retrieve cached entry.
    if ($name = $classes->get($hash)) {
      try {
        $reflection = new \ReflectionClass($name);
        return $reflection;
      }
      catch (\Exception $e) {
      }
    }

    // Generate namespace possibilities from theme ancestry.
    $namespaces = [];
    foreach ((array) $class as $name) {
      foreach ($theme->getAncestry(TRUE) as $ancestor) {
        $namespaces[] = '\\Drupal\\' . $ancestor->getName() . ($namespace ? '\\' . $namespace : '') . '\\' . $name;
      }
    }
    foreach ($namespaces as $name) {
      try {
        $reflection = new \ReflectionClass($name);
        $classes->set($hash, $reflection->getName());
        return $reflection;
      }
      catch (\Exception $e) {
      }
    }

    $classes->set($hash, FALSE);
    return FALSE;
  }

  /**
   * Generates a unique hash name.
   *
   * @param ...
   *   All arguments passed will be serialized and used to generate the hash.
   *
   * @return string
   *   The generated hash identifier.
   */
  public static function generateHash() {
    $args = func_get_args();
    $hash = '';
    if (is_string($args[0])) {
      $hash = $args[0] . ':';
    }
    elseif (is_array($args[0])) {
      $hash = implode(':', $args[0]) . ':';
    }
    $hash .= Crypt::hashBase64(serialize($args));
    return $hash;
  }

  /**
   * Converts a snake_cased string to CamelCase.
   *
   * @param string $string
   *   The string to convert.
   *
   * @return string
   *   The converted string to class name.
   */
  public static function snakeToCamelCase($string) {
    static $strings = [];
    if (!isset($strings[$string])) {
      $name = preg_replace('/(_|-)+/', '_', Unicode::strtolower($string));
      $name = preg_replace('/[^a-z0-9_]+/', '', $name);
      $name = explode('_', $name);
      foreach ($name as &$word) {
        $word = Unicode::ucfirst($word);
      }
      $strings[$string] = implode('', $name);
    }
    return $strings[$string];
  }

}
