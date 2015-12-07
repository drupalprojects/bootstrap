<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Table.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;

/**
 * Pre-processes variables for the "table" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "table"
 * )
 */
class Table implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    // Prepare classes array if necessary.
    if (!isset($variables['attributes']['class'])) {
      $variables['attributes']['class'] = [];
    }
    // Convert classes to an array.
    elseif (isset($variables['attributes']['class']) && is_string($variables['attributes']['class'])) {
      $variables['attributes']['class'] = explode(' ', $variables['attributes']['class']);
    }

    // Add the necessary classes to the table.
    self::addClasses($variables['attributes']['class'], $variables);
  }

  /**
   * Helper function for adding the necessary classes to a table.
   *
   * @param array $classes
   *   The array of classes, passed by reference.
   * @param array $variables
   *   The variables of the theme hook, passed by reference.
   */
  public static function addClasses(array &$classes, array &$variables) {
    $theme = Bootstrap::getTheme();

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

}
