<?php
/**
 * @file
 * Stub file for bootstrap_menu_local_task().
 */

/**
 * Returns HTML for a single local task link.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: A render element containing:
 *     - #link: A menu link array with 'title', 'href', and 'localized_options'
 *       keys.
 *     - #active: A boolean indicating whether the local task is active.
 *
 * @return string
 *   The constructed HTML.
 *
 * @see theme_menu_local_task()
 *
 * @ingroup theme_functions
 */
function bootstrap_menu_local_task($variables) {
  return theme_menu_local_task($variables);
}
