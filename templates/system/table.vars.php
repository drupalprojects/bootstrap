<?php
/**
 * @file
 * Stub file for "table" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\BaseTheme;

/**
 * Pre-processes variables for the "table" theme hook.
 *
 * See theme function for list of available variables.
 *
 * @see bootstrap_table()
 * @see theme_table()
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_table(&$variables) {
  // Prepare classes array if necessary.
  if (!isset($variables['attributes']['class'])) {
    $variables['attributes']['class'] = [];
  }
  // Convert classes to an array.
  elseif (isset($variables['attributes']['class']) && is_string($variables['attributes']['class'])) {
    $variables['attributes']['class'] = explode(' ', $variables['attributes']['class']);
  }

  // Add the necessary classes to the table.
  _bootstrap_table_add_classes($variables['attributes']['class'], $variables);
}

/**
 * Helper function for adding the necessary classes to a table.
 *
 * @param array $classes
 *   The array of classes, passed by reference.
 * @param array $variables
 *   The variables of the theme hook, passed by reference.
 */
function _bootstrap_table_add_classes(&$classes, &$variables) {
  $theme = BaseTheme::getTheme();

  $context = $variables['context'];

  // Generic table class for all tables.
  $classes[] = 'table';

  // Bordered table.
  if (!empty($context['bordered']) || $theme->getSetting('table_bordered')) {
    $classes[] = 'table-bordered';
  }

  // Condensed table.
  if (!empty($context['condensed']) || $theme->getSetting('table_condensed')) {
    $classes[] = 'table-condensed';
  }

  // Hover rows.
  if (!empty($context['hover']) || $theme->getSetting('table_hover')) {
    $classes[] = 'table-hover';
  }

  // Striped rows.
  if (!empty($context['striped']) || $theme->getSetting('table_striped')) {
    $classes[] = 'table-striped';
  }

  $variables['responsive'] = !empty($context['responsive']) ? $context['responsive'] : $theme->getSetting('table_responsive');
}
