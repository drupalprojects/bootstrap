<?php
/**
 * @file
 * menu-tree.func.php
 */

/**
 * Bootstrap theme wrapper function for the primary menu links.
 */
function bootstrap_menu_tree__primary(&$variables) {
  return '<ul class="menu nav navbar-nav">' . $variables['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the secondary menu links.
 */
function bootstrap_menu_tree__secondary(&$variables) {
  return '<ul class="menu nav navbar-nav pull-right">' . $variables['tree'] . '</ul>';
}
