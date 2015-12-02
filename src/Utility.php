<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Utility.
 */

namespace Drupal\bootstrap;

use Drupal\Component\Utility\Crypt;

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

}
