<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Utility\Unicode.
 */

namespace Drupal\bootstrap\Utility;

use Drupal\bootstrap\Bootstrap;
use Drupal\Component\Utility\Xss;

/**
 * Extends \Drupal\Component\Utility\Unicode.
 */
class Unicode extends \Drupal\Component\Utility\Unicode {

  /**
   * Extracts the hook name from a function name.
   *
   * @param string $string
   *   The function name to extract the hook name from.
   * @param string $suffix
   *   A suffix hook ending (like "alter") to also remove.
   * @param string $prefix
   *   A prefix hook beginning (like "form") to also remove.
   *
   * @return string
   *   The extracted hook name.
   */
  public static function extractHook($string, $suffix = NULL, $prefix = NULL) {
    $regex = '^(' . implode('|', array_keys(Bootstrap::getTheme()->getAncestry())) . ')';
    $regex .= $prefix ? '_' . $prefix : '';
    $regex .= $suffix ? '_|_' . $suffix . '$' : '';
    return preg_replace("/$regex/", '', $string);
  }

  /**
   * Determines if a string of text is considered "simple".
   *
   * @param string $string
   *   The string of text to check "simple" criteria on.
   * @param int|FALSE $length
   *   The length of characters used to determine whether or not $string is
   *   considered "simple". Set explicitly to FALSE to disable this criteria.
   * @param array|FALSE $allowed_tags
   *   An array of allowed tag elements. Set explicitly to FALSE to disable this
   *   criteria.
   * @param bool $html
   *   A variable, passed by reference, that indicates whether or not the
   *   string contains HTML.
   *
   * @return bool
   *   Returns TRUE if the $string is considered "simple", FALSE otherwise.
   */
  public static function isSimple($string, $length = 250, $allowed_tags = NULL, &$html = FALSE) {
    // Typecast to a string (if an object).
    $string_clone = (string) $string;

    // Use the advanced drupal_static() pattern.
    static $drupal_static_fast;
    if (!isset($drupal_static_fast)) {
      $drupal_static_fast['strings'] = &drupal_static(__METHOD__);
    }
    $strings = &$drupal_static_fast['strings'];
    if (!isset($strings[$string_clone])) {
      $plain_string = strip_tags($string_clone);
      $simple = TRUE;
      if ($allowed_tags !== FALSE) {
        $filtered_string = Xss::filter($string_clone, $allowed_tags);
        $html = $filtered_string !== $plain_string;
        $simple = $simple && $string_clone === $filtered_string;
      }
      if ($length !== FALSE) {
        $simple = $simple && strlen($plain_string) <= intval($length);
      }
      $strings[$string_clone] = $simple;
    }
    return $strings[$string_clone];
  }

}
