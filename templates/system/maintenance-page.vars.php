<?php

/**
 * @file
 * Stub file for "maintenance_page" theme hook [pre]process functions.
 */

// Include the "html" and "page" preprocessing files.
bootstrap_include('bootstrap', 'templates/system/html.vars.php');
bootstrap_include('bootstrap', 'templates/system/page.vars.php');

/**
 * Pre-processes variables for the "maintenance_page" theme hook.
 *
 * See template for list of available variables.
 *
 * @param array $variables
 *   An associative array of variables, passed by reference.
 *
 * @see maintenance-page.tpl.php
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_maintenance_page(array &$variables) {
  bootstrap_preprocess_html($variables);
  bootstrap_preprocess_page($variables);
}

/**
 * Processes variables for the "maintenance_page" theme hook.
 *
 * See template for list of available variables.
 *
 * @param array $variables
 *   An associative array of variables, passed by reference.
 *
 * @see maintenance-page.tpl.php
 *
 * @ingroup theme_process
 */
function bootstrap_process_maintenance_page(array &$variables) {
  bootstrap_process_html($variables);
  bootstrap_process_page($variables);
}
