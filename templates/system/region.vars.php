<?php
/**
 * @file
 * region.vars.php
 */

/**
 * Implements hook_preprocess_region().
 */
function bootstrap_preprocess_region(&$variables) {
  $region = $variables['region'];
  $regions = system_region_list($GLOBALS['theme_key']);
  $variables['page'] = &drupal_static('bootstrap_process_page');

  $attributes = &$variables['attributes_array'];
  $attributes['class'] = $variables['classes_array'];
  $content_attributes = &$variables['content_attributes_array'];

  // Handle regions.
  switch ($region) {
    case 'navigation':
      $attributes['class'][] = 'navbar';
      $content_attributes['class'][] = 'container';
      if (theme_get_setting('bootstrap_navbar_position') !== '') {
        $attributes['class'][] = 'navbar-' . theme_get_setting('bootstrap_navbar_position');
      }
      else {
        $attributes['class'][] = 'container';
      }
      if (theme_get_setting('bootstrap_navbar_inverse')) {
        $attributes['class'][] = 'navbar-inverse';
      }
      else {
        $attributes['class'][] = 'navbar-default';
      }
      break;

    case 'highlighted':
      $attributes['class'][] = 'highlighted';
      $attributes['class'][] = 'jumbotron';
      break;

    case 'header':
      $variables['attributes_array']['id'] = 'page-header';
      $variables['attributes_array']['role'] = 'banner';
      break;

    case 'sidebar_first':
    case 'sidebar_second':
      $variables['attributes_array']['role'] = 'complementary';
      $variables['theme_hook_suggestions'] = array(
        'region__sidebar',
        'region__' . $region,
      );
      break;

    case 'help':
      if (!empty($variables['content'])) {
        $variables['content'] = _bootstrap_icon('question-sign') . $variables['content'];
        $attributes['class'][] = 'alert';
        $attributes['class'][] = 'alert-info';
        $attributes['class'][] = 'messages';
        $attributes['class'][] = 'info';
      }
      break;

    case 'footer':
      $attributes['class'][] = 'container';
      break;
  }

  // Provide a "front" page suggestion for regions.
  if (drupal_is_front_page()) {
    foreach ($variables['theme_hook_suggestions'] as $suggestion) {
      $variables['theme_hook_suggestions'][] = $suggestion . '__front';
    }
  }

  // Provide entity based suggestions for regions.
  static $entities;
  if (!isset($entities)) {
    $entities = entity_get_info();
  }
  foreach ($entities as $entity_type => $entity_info) {
    if ($entity = menu_get_object($entity_type)) {
      $id = $entity_info['entity keys']['id'];
      $bundle = $entity_info['entity keys']['bundle'];
      foreach ($variables['theme_hook_suggestions'] as $suggestion) {
        $variables['theme_hook_suggestions'][] = $suggestion . '__' . $entity_type;
        if ($bundle) {
          $variables['theme_hook_suggestions'][] = $suggestion . '__' . $entity_type . '__' . $entity->{$bundle};
        }
        if ($id) {
          $variables['theme_hook_suggestions'][] = $suggestion . '__' . $entity_type . '__' . $entity->{$id};
        }
      }
      break;
    }
  }

  // Add "well" classes to the region content wrapper.
  static $wells;
  if (!isset($wells)) {
    foreach ($regions as $name => $title) {
      $wells[$name] = theme_get_setting('bootstrap_region_well-' . $name) ? : FALSE;
    }
  }
  if ($wells[$region]) {
    $content_attributes['class'][] = $wells[$region];
  }

  // Add "column" classes to regions.
  static $region_columns;
  if (!isset($region_columns)) {
    foreach ($regions as $name => $title) {
      $region_columns[$name] = theme_get_setting('bootstrap_region_grid-' . $name) ? : 0;
    }
    $columns = theme_get_setting('bootstrap_grid_columns') ? : 12;
    foreach ($regions as $name => $title) {
      if ($dynamic_regions = theme_get_setting('bootstrap_region_grid_dynamic-' . $name) ? : array()) {
        // Enforce the region to have the maximum number of columns.
        $column = $columns;
        foreach ($dynamic_regions as $dynamic_region) {
          if (is_array($variables['page']['page'][$dynamic_region]) &&
              element_children($variables['page']['page'][$dynamic_region])) {
            $column -= $region_columns[$dynamic_region];
          }
        }
        $region_columns[$name] = $column;
      }
    }
  }
  if ($region_columns[$region]) {
    $attributes['class'][] = (theme_get_setting('bootstrap_grid_class_prefix') ? : 'col-sm') . '-' . $region_columns[$region];
  }
}
