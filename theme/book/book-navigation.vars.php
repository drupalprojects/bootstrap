<?php
/**
 * @file
 * book-navigation.vars.php
 */

use Drupal\Core\Render\Element;

/**
 * Implements hook_preprocess_book_navigation().
 */
function bootstrap_preprocess_book_navigation(&$variables) {
  /** @var \Drupal\menu_link\MenuTreeInterface $menu_tree */
  $menu_tree = \Drupal::service('menu_link.tree');
  // Rebuild entire menu tree for the book.
  $tree = $menu_tree->renderMenu($variables['book_link']['menu_name']);

  // Fix the theme hook suggestions.
  _bootstrap_book_fix_theme_hooks($variables['book_link']['nid'], $tree);

  $variables['tree'] = drupal_render($variables['book_link']);
}

/**
 * Helper function to fix theme hooks in book TOC menus.
 *
 * @param int $bid
 *   The book identification number.
 * @param array $element
 *   The element to iterate over, passed by reference.
 * @param int $level
 *   Used internally to determine the current level of the menu.
 */
function _bootstrap_book_fix_theme_hooks($bid, array &$element, $level = 0) {
  $hook = $level === 0 ? $bid : 'sub_menu__' . $bid;
  $element['#theme_wrappers'] = array('menu_tree__book_toc__' . $hook);
  foreach (Element::children($element) as $child) {
    $element[$child]['#theme'] = 'menu_link__book_toc__' . $hook;
    // Iterate through all child menu items as well.
    if (!empty($element[$child]['#below'])) {
      _bootstrap_book_fix_theme_hooks($bid, $element[$child]['#below'], ($level + 1));
    }
  }
}
