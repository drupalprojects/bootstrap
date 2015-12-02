<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Preprocess\PreprocessInterface.
 */

namespace Drupal\bootstrap\Preprocess;

/**
 * Defines the interface for an object oriented preprocess plugin.
 */
interface PreprocessInterface {

  /**
   * Preprocess theme hook variables.
   *
   * @param array $variables
   *   The variables array, passed by reference.
   */
  public function preprocess(array &$variables);

}
