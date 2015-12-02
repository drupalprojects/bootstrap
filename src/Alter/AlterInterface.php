<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\AlterInterface.
 */

namespace Drupal\bootstrap\Alter;

/**
 * Defines the interface for an object oriented alter.
 */
interface AlterInterface {

  /**
   * The alter method to store the code.
   */
  public function alter(&$data, &$context1 = NULL, &$context2 = NULL);

}
