<?php
/**
 * @file
 * menu-local-task.func.php
 */

use Drupal\Component\Utility\SafeMarkup;

/**
 * Implements hook_preprocess_menu_local_task().
 */
function bootstrap_preprocess_menu_local_task(&$variables) {
  if (!empty($variables['element']['#active'])) {
    $variables['attributes']['class'][] = 'active';
  }
}
