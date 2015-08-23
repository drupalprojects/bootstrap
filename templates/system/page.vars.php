<?php
/**
 * @file
 * page.vars.php
 */

use Drupal\Core\Template\Attribute;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Implements hook_preprocess_page().
 *
 * @see page.tpl.php
 */
function bootstrap_preprocess_page(&$variables) {
  // Add information about the number of sidebars.
  $variables['content_column_attributes'] = new Attribute();
  $variables['content_column_attributes']['class'] = array();
  if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
    $variables['content_column_attributes']['class'][] = 'col-sm-6';
  }
  elseif (!empty($variables['page']['sidebar_first']) || !empty($variables['page']['sidebar_second'])) {
    $variables['content_column_attributes']['class'][] = 'col-sm-9';
  }
  else {
    $variables['content_column_attributes']['class'][] = 'col-sm-12';
  }

  $variables['navbar_attributes'] = new Attribute();
  $variables['navbar_attributes']['class'] = array('navbar');
  if (bootstrap_setting('navbar_position') !== '') {
    $variables['navbar_attributes']['class'][] = 'navbar-' . bootstrap_setting('navbar_position');
  }
  else {
    $variables['navbar_attributes']['class'][] = 'container';
  }
  if (bootstrap_setting('navbar_inverse')) {
    $variables['navbar_attributes']['class'][] = 'navbar-inverse';
  }
  else {
    $variables['navbar_attributes']['class'][] = 'navbar-default';
  }

  // Primary nav.
  $menu_tree = \Drupal::menuTree();
  // Render the top-level administration menu links.
  $parameters = new MenuTreeParameters();
  $tree = $menu_tree->load('main', $parameters);
  $manipulators = array(
    array('callable' => 'menu.default_tree_manipulators:checkAccess'),
    array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
  );
  $tree = $menu_tree->transform($tree, $manipulators);
  $variables['primary_nav'] = $menu_tree->build($tree);
  $variables['primary_nav']['#attributes']['class'][] = 'navbar-nav';

  // Primary nav.
  $menu_tree = \Drupal::menuTree();
  // Render the top-level administration menu links.
  $parameters = new MenuTreeParameters();
  $tree = $menu_tree->load('account', $parameters);
  $variables['secondary_nav'] = $menu_tree->build($tree);
  $variables['secondary_nav']['#attributes']['class'][] = 'navbar-nav';
  $variables['secondary_nav']['#attributes']['class'][] = 'secondary';
}
