<?php
/**
 * @file
 * pre-render.inc
 *
 * Contains various implementations for #pre_render callbacks on elements.
 */

/**
 * Implements hook_pre_render().
 */
function bootstrap_pre_render($element) {
  if (!empty($element['#bootstrap_ignore_pre_render'])) {
    return $element;
  }

  // Only add the "form-control" class for specific element input types.
  $types = array(
    // Core.
    'password',
    'password_confirm',
    'machine_name',
    'select',
    'textarea',
    'textfield',

    // Elements module (HTML5).
    'date',
    'datefield',
    'email',
    'emailfield',
    'number',
    'numberfield',
    'range',
    'rangefield',
    'search',
    'searchfield',
    'tel',
    'telfield',
    'url',
    'urlfield',

    // Webform module.
    'webform_email',
    'webform_number',
  );

  // Add necessary classes for specific types.
  if (!empty($element['#type'])) {
    if (in_array($element['#type'], $types) || ($element['#type'] === 'file' && empty($element['#managed_file']))) {
      $element['#attributes']['class'][] = 'form-control';
    }
    if ($element['#type'] === 'machine_name') {
      $element['#wrapper_attributes']['class'][] = 'form-inline';
    }
    if ($element['#type'] !== 'checkbox' || $element['#type'] !== 'radio' || $element['#type'] !== 'checkboxes' || $element['#type'] !== 'radios') {
      if (!empty($element['#description']) && empty($element['#attributes']['title']) && _bootstrap_tooltip_description($element['#description'])) {
        $element['#attributes']['title'] = $element['#description'];
      }

      if (!empty($element['#attributes']['title'])) {
        $element['#attributes']['data-toggle'] = 'tooltip';
      }
    }
  }

  return $element;
}

/**
 * Implements hook_pre_render_HOOK().
 */
function bootstrap_pre_render_fieldset($element) {
  // Fieldsets may be rendered outside of a Form API context.
  if (!empty($element['#bootstrap_ignore_pre_render']) || !isset($element['#parents']) || !isset($element['#groups'])) {
    return $element;
  }
  // Inject group member elements belonging to this group.
  $parents = implode('][', $element['#parents']);
  $children = element_children($element['#groups'][$parents]);
  if (!empty($children)) {
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
 * Pre-render input elements.
 *
 * @deprecated Use proper hooks instead. Will be removed in 7.x-3.2.
 *
 * @see hook_pre_render()
 *
 * @todo Remove in 7.x-3.2.
 */
function _bootstrap_pre_render_input($element) {
  return bootstrap_pre_render($element);
}

/**
 * Pre-render fieldset element.
 *
 * @deprecated Use proper hooks instead. Will be removed in 7.x-3.2.
 *
 * @see hook_pre_render_HOOK()
 *
 * @todo Remove in 7.x-3.2.
 */
function _bootstrap_pre_render_fieldset($element) {
  return bootstrap_pre_render_fieldset($element);
}
