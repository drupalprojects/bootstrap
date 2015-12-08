<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Table.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\PluginBase;

/**
 * Pre-processes variables for the "table" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "table"
 * )
 */
class Table extends PluginBase implements PreprocessInterface {

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
    $this->addClasses($variables['attributes']['class'], $variables);
  }

  /**
   * Helper function for adding the necessary classes to a table.
   *
   * @param array $classes
   *   The array of classes, passed by reference.
   * @param array $variables
   *   The variables of the theme hook, passed by reference.
   */
  protected function addClasses(array &$classes, array &$variables) {
    $context = $variables['context'];

    // Generic table class for all tables.
    $classes[] = 'table';

    // Bordered table.
    if (!empty($context['bordered']) || $this->theme->getSetting('table_bordered')) {
      $classes[] = 'table-bordered';
    }

    // Condensed table.
    if (!empty($context['condensed']) || $this->theme->getSetting('table_condensed')) {
      $classes[] = 'table-condensed';
    }

    // Hover rows.
    if (!empty($context['hover']) || $this->theme->getSetting('table_hover')) {
      $classes[] = 'table-hover';
    }

    // Striped rows.
    if (!empty($context['striped']) || $this->theme->getSetting('table_striped')) {
      $classes[] = 'table-striped';
    }

    $variables['responsive'] = !empty($context['responsive']) ? $context['responsive'] : $this->theme->getSetting('table_responsive');
  }

}
