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
    $element['#theme_wrappers'] = array('bootstrap_panel__tab_pane');
    $element['#attributes']['class'][] = 'tab-pane';
    if (theme_get_setting('bootstrap_fade_tabs')) {
      $element['#attributes']['class'][] = 'fade';
    }
    $group = $element['#group'];
    $form_state['groups'][$group][] = &$element;
  }
  else {
    $element['#attributes']['class'][] = 'panel';
    $element['#attributes']['class'][] = 'panel-default';
  }

  // Contains form element summary functionalities.
  $element['#attached']['library'][] = array('system', 'drupal.form');

  // The .form-wrapper class is required for #states to treat fieldsets like
  // containers.
  if (!isset($element['#attributes']['class'])) {
    $element['#attributes']['class'] = array();
  }

  return $element;
}

/**
 * Process vertical_tab element.
 */
function _bootstrap_process_vertical_tabs($element, &$form_state) {
  if (!isset($element['#parents'])) {
    return $element;
  }

  // Show vertical tabs as "left" bootstrap tabs.
  if (empty($element['#tab_alignment'])) {
    $element['#tab_alignment'] = 'left';
  }

  // Inject a new fieldset as child, so that form_process_fieldset() processes
  // this fieldset like any other fieldset.
  $element['group'] = array(
    '#type' => 'fieldset',
    '#theme_wrappers' => array(),
    '#parents' => $element['#parents'],
    '#vertical_tabs' => TRUE,
  );

  // The JavaScript stores the currently selected tab in this hidden
  // field so that the active tab can be restored the next time the
  // form is rendered, e.g. on preview pages or when form validation
  // fails.
  $name = implode('__', $element['#parents']);
  if (isset($form_state['values'][$name . '__active_tab'])) {
    $element['#default_tab'] = $form_state['values'][$name . '__active_tab'];
  }
  $element[$name . '__active_tab'] = array(
    '#type' => 'hidden',
    '#default_value' => $element['#default_tab'],
    '#attributes' => array('class' => array('vertical-tabs-active-tab')),
  );

  return $element;
}
