<?php
/**
 * @file
 * Stub file for "bootstrap_panel" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "bootstrap_panel" theme hook.
 *
 * See template for list of available variables.
 *
 * @see bootstrap-panel.tpl.php
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_bootstrap_panel(&$variables) {
  $element = &$variables['element'];

  // Set the element's attributes.
  element_set_attributes($element, array('id'));

  // Retrieve the attributes for the element.
  $attributes = &_bootstrap_get_attributes($element);

  // Add panel and panel-default classes.
  $attributes['class'][] = 'panel';
  $attributes['class'][] = 'panel-default';

  // states.js requires form-wrapper on fieldset to work properly.
  $attributes['class'][] = 'form-wrapper';

  // Handle collapsible panels.
  $variables['collapsible'] = FALSE;
  if (isset($element['#collapsible'])) {
    $variables['collapsible'] = $element['#collapsible'];
  }
  $variables['collapsed'] = FALSE;
  if (isset($element['#collapsed'])) {
    $variables['collapsed'] = $element['#collapsed'];
    // Remove collapsed class as it should only be applied to the body.
    _bootstrap_remove_class('collapsed', $element);
  }
  // Force grouped fieldsets to not be collapsible (for vertical tabs).
  if (!empty($element['#group'])) {
    $variables['collapsible'] = FALSE;
    $variables['collapsed'] = FALSE;
  }

  // Generate a unique identifier for the fieldset wrapper.
  if (!isset($attributes['id'])) {
    $attributes['id'] = drupal_html_id('bootstrap-panel');
  }

  // Get body attributes.
  $body_attributes = &_bootstrap_get_attributes($element, 'body_attributes');

  // Add default .panel-body class.
  _bootstrap_add_class('panel-body', $element, 'body_attributes');

  // Add more classes to the body if collapsible.
  if ($variables['collapsible']) {
    _bootstrap_add_class(array('panel-collapse', 'collapse', 'fade', ($variables['collapsed'] ? 'collapsed' : 'in')), $element, 'body_attributes');
  }

  // Generate a unique identifier for the body.
  if (!isset($body_attributes['id'])) {
    $body_attributes['id'] = drupal_html_id($attributes['id'] . '--body');
  }

  // Set the target to the body element.
  $variables['target'] = '#' . $body_attributes['id'];

  // Build the panel content.
  $variables['content'] = $element['#children'];
  if (isset($element['#value'])) {
    $variables['content'] .= $element['#value'];
  }

  // Iterate over optional variables.
  $keys = array(
    'description',
    'prefix',
    'suffix',
    'title',
  );
  foreach ($keys as $key) {
    $variables[$key] = !empty($element["#$key"]) ? $element["#$key"] : FALSE;
  }

  // Add the attributes.
  $variables['attributes'] = $attributes;
  $variables['body_attributes'] = $body_attributes;
}

/**
 * Processes variables for the "bootstrap_panel" theme hook.
 *
 * See template for list of available variables.
 *
 * @see bootstrap-panel.tpl.php
 *
 * @ingroup theme_process
 */
function bootstrap_process_bootstrap_panel(&$variables) {
  $variables['attributes'] = drupal_attributes($variables['attributes']);
  $variables['body_attributes'] = drupal_attributes($variables['body_attributes']);
  if (!empty($variables['title'])) {
    $variables['title'] = filter_xss_admin(render($variables['title']));
  }
  if (!empty($variables['description'])) {
    $variables['description'] = filter_xss_admin(render($variables['description']));
  }
}
