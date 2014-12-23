<?php
/**
 * @file
 * region.vars.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_region().
 */
function bootstrap_preprocess_region(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $region = $variables['region'];

  // Content region.
  if ($region === 'content') {
    // @todo is this actually used properly?
    $variables['theme_hook_suggestions'][] = 'region__no_wrapper';
  }
  // Help region.
  elseif ($region === 'help' && !empty($variables['content'])) {
    $variables['content'] = _bootstrap_icon('question-sign') . $variables['content'];
    $variables['attributes']['class'][] = 'alert';
    $variables['attributes']['class'][] = 'alert-info';
    $variables['attributes']['class'][] = 'messages';
    $variables['attributes']['class'][] = 'info';
  }

  // Support for "well" classes in regions.
  static $wells;
  if (!isset($wells)) {
    foreach (system_region_list($theme) as $name => $title) {
      $wells[$name] = theme_get_setting('bootstrap_region_well-' . $name);
    }
  }
  if (!empty($wells[$region])) {
    $variables['attributes']['class'][] = $wells[$region];
  }
}
