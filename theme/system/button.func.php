<?php
/**
 * @file
 * button.func.php
 */

/**
 * Overrides theme_button().
 */
function bootstrap_button($variables) {
  // This line break adds inherent margin between multiple buttons.
  return '<button' . drupal_attributes($variables['element']['#attributes']) . '>' . $variables['element']['#value'] . "</button>\n";
}
