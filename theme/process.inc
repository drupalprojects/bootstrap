<?php
/**
 * @file
 * process.inc
 *
 * Contains various implementations for #process callbacks on elements.
 */

/**
 * Process input elements.
 */
function _bootstrap_process_input(&$element, &$form_state) {

  // Don't inject the "form-control" class on these input types.
  $ignore_types = array(
    'checkbox',
    'checkboxes',
    'file',
    'hidden',
    'image_button',
    'radio',
    'radios',
    'submit',
    'tableselect',
  );

  if (!in_array($element['#type'], $ignore_types)) {
    $element['#attributes']['class'][] = 'form-control';
  }

  return $element;
}

/**
 * Process fieldset element.
 */
function _bootstrap_process_fieldset(&$element, &$form_state) {
  $parents = implode('][', $element['#parents']);

  // Each fieldset forms a new group. The #type 'vertical_tabs' basically only
  // injects a new fieldset.
  $form_state['groups'][$parents]['#group_exists'] = TRUE;
  $element['#groups'] = &$form_state['groups'];

  // Process vertical tabs group member fieldsets.
  if (isset($element['#group'])) {
    // Add this fieldset to the defined group (by reference).
    $element['#theme_wrappers'] = array('bootstrap_panel');
    $group = $element['#group'];
    $form_state['groups'][$group][] = &$element;
  }

  $element['#attributes']['class'][] = 'panel';
  $element['#attributes']['class'][] = 'panel-default';

  // Contains form element summary functionalities.
  $element['#attached']['library'][] = array('system', 'drupal.form');

  // The .form-wrapper class is required for #states to treat fieldsets like
  // containers.
  if (!isset($element['#attributes']['class'])) {
    $element['#attributes']['class'] = array();
  }

  return $element;
}
