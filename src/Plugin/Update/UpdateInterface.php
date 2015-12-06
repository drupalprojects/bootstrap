<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Update\UpdateInterface.
 */

namespace Drupal\bootstrap\Plugin\Update;

/**
 * Defines the interface for an object oriented preprocess plugin.
 */
interface UpdateInterface {

  /**
   * Update callback.
   */
  public function update();

}
