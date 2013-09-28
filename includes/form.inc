<?php
/**
 * @file
 * form.inc
 *
 * Implements various form alter and implementations.
 */

/**
 * Implements hook_form_alter().
 */
function bootstrap_form_alter(&$form) {
  // Id's of forms that should be ignored
  // Make this configurable?
  $form_ids = array(
    'node_form',
    'system_site_information_settings',
    'user_profile_form',
    'node_delete_confirm',
  );

  // Only wrap in container for certain form.
  if (isset($form['#form_id']) && !in_array($form['#form_id'], $form_ids) && !isset($form['#node_edit_form']) && isset($form['actions']) && ($form['actions']['#type'] == 'actions')) {
    $form['actions']['#theme_wrappers'] = array();
  }
}

/**
 * Implements hook_form_FORM_ID_alter() for search_form().
 */
function bootstrap_form_search_form_alter(&$form) {
  // Add a clearfix class so the results don't overflow onto the form.
  $form['#attributes']['class'][] = 'clearfix';

  // Remove container-inline from the container classes.
  $form['basic']['#attributes']['class'] = array();

  // Hide the default button from display.
  $form['basic']['submit']['#attributes']['class'][] = 'element-invisible';

  // Implement a theme wrapper to add a submit button containing a search icon
  // directly after the input element.
  $form['basic']['keys']['#theme_wrappers'] = array('bootstrap_search_form_wrapper');
  $form['basic']['keys']['#title'] = '';
  $form['basic']['keys']['#attributes']['placeholder'] = t('Search');
}

/**
 * Implements hook_form_FORM_ID_alter() for search_block_form().
 */
function bootstrap_form_search_block_form_alter(&$form) {
  $form['#attributes']['class'][] = 'form-search';

  $form['search_block_form']['#title'] = '';
  $form['search_block_form']['#attributes']['placeholder'] = t('Search');

  // Hide the default button from display and implement a theme wrapper to add
  // a submit button containing a search icon directly after the input element.
  $form['actions']['submit']['#attributes']['class'][] = 'element-invisible';
  $form['search_block_form']['#theme_wrappers'] = array('bootstrap_search_form_wrapper');

  // Apply a clearfix so the results don't overflow onto the form.
  $form['#attributes']['class'][] = 'content-search';
}

/**
 * Returns HTML for a form element.
 */
function bootstrap_form_element(&$variables) {
  $element = &$variables['element'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }

  // Check for errors and set correct error class.
  if (isset($element['#parents']) && form_get_error($element)) {
    $attributes['class'][] = 'error';
  }

  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(
        ' ' => '-',
        '_' => '-',
        '[' => '-',
        ']' => '',
    ));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  if (!empty($element['#autocomplete_path']) && drupal_valid_path($element['#autocomplete_path'])) {
    $attributes['class'][] = 'form-autocomplete';
  }
  $attributes['class'][] = 'form-item';

  // See http://getbootstrap.com/css/#forms-controls.
  if (isset($element['#type'])) {
    if ($element['#type'] == "radio") {
      $attributes['class'][] = 'radio';
    }
    elseif ($element['#type'] == "checkbox") {
      $attributes['class'][] = 'checkbox';
    }
    else {
      $attributes['class'][] = 'form-group';
    }
  }

  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $variables['#children'] = ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<p class="help-block">' . $element['#description'] . "</p>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Returns HTML for a form element label and required marker.
 */
function bootstrap_form_element_label(&$variables) {
  $element = $variables['element'];

  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // Determine if certain things should skip for checkbox or radio elements.
  $skip = ('checkbox' === $element['#type'] || 'radio' === $element['#type']);

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '' && !$skip) && empty($element['#required'])) {
    return '';
  }

  // If the element is required, a required marker is appended to the label.
  $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';

  $title = filter_xss_admin($element['#title']);

  $attributes = array();

  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after' && !$skip) {
    $attributes['class'][] = $element['#type'];
  }
  // Show label only to screen readers to avoid disruption in visual flows.
  elseif ($element['#title_display'] == 'invisible') {
    $attributes['class'][] = 'element-invisible';
  }

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  // Insert radio and checkboxes inside label elements.
  $output = '';
  if (isset($variables['#children'])) {
    $output .= $variables['#children'];
  }

  // Append label.
  $output .= $t('!title !required', array('!title' => $title, '!required' => $required));

  // The leading whitespace helps visually separate fields from inline labels.
  return ' <label' . drupal_attributes($attributes) . '>' . $output . "</label>\n";
}

/**
 * Add bootstrap_pre_render_element to process list of elements with #input.
 */
function bootstrap_element_info_alter(&$elements) {
  foreach ($elements as &$element) {

    // Bootstrap tabs and replaces core vertical tabs.
    if ('bootstrap_tabs' === $element['#type'] || 'vertical_tabs' === $element['#type']) {
      if ('vertical_tabs' === $element['#type']) {
        $element['#theme_wrappers'] = array('bootstrap_tabs');
      }
      // Replace core's #process function.
      if (($key = array_search('form_process_vertical_tabs', $element['#process'])) !== FALSE) {
        $element['#process'][$key] = 'bootstrap_process_vertical_tabs';
      }
    }

    // Replace core fieldsets with Bootstrap panels.
    if ('fieldset' === $element['#type']) {
      $element['#theme_wrappers'] = array('bootstrap_panel');
      // Replace core's #process function.
      if (($key = array_search('form_process_fieldset', $element['#process'])) !== FALSE) {
        $element['#process'][$key] = 'bootstrap_form_process_fieldset';
      }
      // Replace core's #pre_render function.
      if (($key = array_search('form_pre_render_fieldset', $element['#pre_render'])) !== FALSE) {
        $element['#pre_render'][$key] = 'bootstrap_pre_render_fieldset';
      }
    }
    if (!empty($element['#input'])) {
      $element['#process'][] = 'bootstrap_pre_render_element';
    }
  }
}

/**
 * Replace core's form_pre_render_fieldset().
 */
function bootstrap_pre_render_fieldset($element) {
  // Fieldsets may be rendered outside of a Form API context.
  if (!isset($element['#parents']) || !isset($element['#groups'])) {
    return $element;
  }
  // Inject group member elements belonging to this group.
  $parents = implode('][', $element['#parents']);
  $children = element_children($element['#groups'][$parents]);
  if (!empty($children)) {
    $element['#theme_wrappers'] = array('bootstrap_bare');
    if (empty($element['#default_tab'])) {
      $children_keys = array_values($children);
      $element['#default_tab'] = $element['#groups'][$parents][array_shift($children_keys)]['#id'];
    }
    foreach ($children as $key) {
      // Break references and indicate that the element should be rendered as
      // group member.
      $child = (array) $element['#groups'][$parents][$key];
      $child['#attributes']['id'] = $child['#id'];
      $child['#group_fieldset'] = TRUE;
      if ($element['#default_tab'] === $child['#id']) {
        $child['#attributes']['class'][] = 'active';
        $child['#attributes']['class'][] = 'in';
      }
      // Inject the element as new child element.
      $element[] = $child;

      $sort = TRUE;
    }
    // Re-sort the element's children if we injected group member elements.
    if (isset($sort)) {
      $element['#sorted'] = FALSE;
    }
  }

  if (isset($element['#group'])) {
    $group = $element['#group'];
    // If this element belongs to a group, but the group-holding element does
    // not exist, we need to render it (at its original location).
    if (!isset($element['#groups'][$group]['#group_exists'])) {
      // Intentionally empty to clarify the flow; we simply return $element.
    }
    // If we injected this element into the group, then we want to render it.
    elseif (!empty($element['#group_fieldset'])) {
      // Intentionally empty to clarify the flow; we simply return $element.
    }
    // Otherwise, this element belongs to a group and the group exists, so we do
    // not render it.
    elseif (element_children($element['#groups'][$group])) {
      $element['#printed'] = TRUE;
    }
  }

  return $element;
}

/**
 * Replace core's form_process_vertical_tabs().
 */
function bootstrap_process_vertical_tabs($element, &$form_state) {
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

/**
 * Replace core's form_process_fieldset().
 */
function bootstrap_form_process_fieldset(&$element, &$form_state) {
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
 * Implements hook_preprocess_HOOK().
 */
function bootstrap_preprocess_bootstrap_panel(&$variables) {
  $element = &$variables['element'];
  $attributes = !empty($element['#attributes']) ? $element['#attributes'] : array();
  $variables['attributes'] = drupal_attributes($attributes);
  $variables['collapsible'] = $element['#collapsible'];
  $variables['collapsed'] = $element['#collapsed'];
  $variables['id'] = $element['#id'];
  $variables['content'] = $element['#children'];

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
}

/**
 * Alters HTML of input elements.
 */
function bootstrap_pre_render_element($element) {

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
 * Preprocessor for theme('button').
 */
function bootstrap_preprocess_button(&$vars) {
  $vars['element']['#attributes']['class'][] = 'btn';

  if (isset($vars['element']['#value'])) {
    $classes = array(
      // Specific strings.
      t('Save and add') => 'btn-info',
      t('Add another item') => 'btn-info',
      t('Add effect') => 'btn-primary',
      t('Add and configure') => 'btn-primary',
      t('Update style') => 'btn-primary',
      t('Download feature') => 'btn-primary',

      // General strings.
      t('Save') => 'btn-primary',
      t('Apply') => 'btn-primary',
      t('Create') => 'btn-primary',
      t('Confirm') => 'btn-primary',
      t('Submit') => 'btn-primary',
      t('Export') => 'btn-primary',
      t('Import') => 'btn-primary',
      t('Restore') => 'btn-primary',
      t('Rebuild') => 'btn-primary',
      t('Search') => 'btn-primary',
      t('Add') => 'btn-info',
      t('Update') => 'btn-info',
      t('Delete') => 'btn-danger',
      t('Remove') => 'btn-danger',
    );
    foreach ($classes as $search => $class) {
      if (strpos($vars['element']['#value'], $search) !== FALSE) {
        $vars['element']['#attributes']['class'][] = $class;
        break;
      }
    }
  }
}

/**
 * Returns HTML for a textfield form element.
 */
function bootstrap_textfield($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'text';
  element_set_attributes($element, array('id', 'name', 'value', 'size', 'maxlength'));
  _form_set_class($element, array('form-text'));

  $output = '<input' . drupal_attributes($element['#attributes']) . ' />';

  $extra = '';
  if ($element['#autocomplete_path'] && drupal_valid_path($element['#autocomplete_path'])) {
    drupal_add_library('system', 'drupal.autocomplete');
    drupal_add_js(drupal_get_path('theme', 'bootstrap') . '/js/bootstrap.autocomplete.js');
    $element['#attributes']['class'][] = 'form-autocomplete';

    $attributes = array();
    $attributes['type'] = 'hidden';
    $attributes['id'] = $element['#attributes']['id'] . '-autocomplete';
    $attributes['value'] = url($element['#autocomplete_path'], array('absolute' => TRUE));
    $attributes['disabled'] = 'disabled';
    $attributes['class'][] = 'autocomplete';
    $output = '<div class="input-group">' . $output . '<span class="input-group-addon"><span class="glyphicon glyphicon-refresh"></span></span></div>';
    $extra = '<input' . drupal_attributes($attributes) . ' />';
  }

  return $output . $extra;
}

/**
 * Returns HTML for a button form element.
 */
function bootstrap_button($variables) {
  $element = $variables['element'];
  $label = $element['#value'];
  element_set_attributes($element, array('id', 'name', 'value', 'type'));

  // If a button type class isn't present then add in default.
  $button_classes = array(
    'btn-default',
    'btn-primary',
    'btn-success',
    'btn-info',
    'btn-warning',
    'btn-danger',
    'btn-link',
  );
  $class_intersection = array_intersect($button_classes, $element['#attributes']['class']);
  if (empty($class_intersection)) {
    $element['#attributes']['class'][] = 'btn-default';
  }

  // Add in the button type class.
  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];

  // This line break adds inherent margin between multiple buttons.
  return '<button' . drupal_attributes($element['#attributes']) . '>' . $label . "</button>\n";
}

/**
 * Used to append bootstrap input types to form elements.
 * @todo see if this is still used?
 */
function bootstrap_bootstrap_append_element(&$variables) {
  $element = &$variables['element'];

  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }

  // Check for errors and set correct error class.
  if (isset($element['#parents']) && form_get_error($element)) {
    $attributes['class'][] = 'error';
  }

  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }

  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(
      ' ' => '-',
      '_' => '-',
      '[' => '-',
      ']' => '',
    ));
  }

  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }

  $attributes['class'][] = 'form-item';

  // See http://getbootstrap.com/css/#forms-controls.
  if (isset($element['#type'])) {
    if ($element['#type'] == 'radio') {
      $attributes['class'][] = 'radio';
    }
    elseif ($element['#type'] == 'checkbox') {
      $attributes['class'][] = 'checkbox';
    }
    else {
      $attributes['class'][] = 'form-group';
    }
  }

  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? $element['#field_prefix'] : '';
  $suffix = isset($element['#field_suffix']) ? $element['#field_suffix'] : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $variables['#children'] = ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<p class="help-block">' . $element['#description'] . "</p>\n";
  }
  $output .= "</div>\n";

  return $output;
}
