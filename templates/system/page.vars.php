<?php
/**
 * @file
 * Stub file for "page" theme hook [pre]process functions.
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\BaseTheme;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * See template for list of available variables.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_page(&$variables) {
  $theme = BaseTheme::getTheme();

  // Add information about the number of sidebars.
  $variables['content_column_attributes'] = new Attribute();
  $variables['content_column_attributes']['class'] = [];
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
  $variables['navbar_attributes']['class'] = ['navbar'];
  if ($theme->getSetting('navbar_position') !== '') {
    $variables['navbar_attributes']['class'][] = 'navbar-' . $theme->getSetting('navbar_position');
  }
  else {
    $variables['navbar_attributes']['class'][] = 'container';
  }
  if ($theme->getSetting('navbar_inverse')) {
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
  $manipulators = [
    ['callable' => 'menu.default_tree_manipulators:checkAccess'],
    ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
  ];
  $tree = $menu_tree->transform($tree, $manipulators);
  $variables['primary_nav'] = $menu_tree->build($tree);
  $variables['primary_nav']['#attributes']['class'][] = 'navbar-nav';

  // Primary nav.
  $menu_tree = \Drupal::menuTree();
  // Render the top-level administration menu links.
  $parameters = new MenuTreeParameters();
  $tree = $menu_tree->load('account', $parameters);
  $manipulators = [
    ['callable' => 'menu.default_tree_manipulators:checkAccess'],
    ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
  ];
  $tree = $menu_tree->transform($tree, $manipulators);
  $variables['secondary_nav'] = $menu_tree->build($tree);
  $variables['secondary_nav']['#attributes']['class'][] = 'navbar-nav';
  $variables['secondary_nav']['#attributes']['class'][] = 'secondary';
}
