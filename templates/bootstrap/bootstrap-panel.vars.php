<?php
/**
 * @file
 * Stub file for "bootstrap_panel" theme hook [pre]process functions.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Template\Attribute;
use Drupal\Component\Utility\Xss;

/**
 * Pre-processes variables for the "bootstrap_panel" theme hook.
 *
 * See template for list of available variables.
 *
 * @see bootstrap-panel.html.twig
 *
 * @ingroup theme_preprocess
 */
function bootstrap_preprocess_bootstrap_panel(&$variables) {
  $element = $variables['element'];
  Element::setAttributes($element, array('id'));
  Element\RenderElement::setAttributes($element);
  $variables['attributes'] = $element['#attributes'];
  $variables['prefix'] = isset($element['#field_prefix']) ? $element['#field_prefix'] : NULL;
  $variables['suffix'] = isset($element['#field_suffix']) ? $element['#field_suffix'] : NULL;
  $variables['title_display'] = isset($element['#title_display']) ? $element['#title_display'] : NULL;
  $variables['children'] = $element['#children'];
  $variables['required'] = !empty($element['#required']) ? $element['#required'] : NULL;

  $variables['legend']['title'] = !empty($element['#title']) ? Xss::filterAdmin($element['#title']) : '';
  $variables['legend']['attributes'] = new Attribute();
  $variables['legend_span']['attributes'] = new Attribute();

  if (!empty($element['#description'])) {
    $description_id = $element['#attributes']['id'] . '--description';
    $description_attributes['id'] = $description_id;
    $variables['description']['attributes'] = new Attribute($description_attributes);
    $variables['description']['content'] = $element['#description'];

    // Add the description's id to the fieldset aria attributes.
    $variables['attributes']['aria-describedby'] = $description_id;
  }

  $variables['collapsible'] = FALSE;
  if (isset($element['#collapsible'])) {
    $variables['collapsible'] = $element['#collapsible'];
    $variables['attributes']['class'][] = 'collapsible';
  }

  $variables['collapsed'] = FALSE;
  if (isset($element['#collapsed'])) {
    $variables['collapsed'] = $element['#collapsed'];
  }

  // Force grouped fieldsets to not be collapsible (for vertical tabs).
  if (!empty($element['#group'])) {
    $variables['collapsible'] = FALSE;
    $variables['collapsed'] = FALSE;
  }

  if (!isset($element['#id']) && $variables['collapsible']) {
    $element['#id'] = \Drupal\Component\Utility\Html::getUniqueId('bootstrap-panel');
  }

  $variables['target'] = NULL;
  if (isset($element['#id'])) {
    if (!isset($variables['attributes']['id'])) {
      $variables['attributes']['id'] = $element['#id'];
    }
    $variables['target'] = '#' . $element['#id'] . ' > .collapse';
  }

  // Iterate over optional variables.
  $keys = array(
    'description',
    'prefix',
    'suffix',
    'title',
    'value',
  );
  foreach ($keys as $key) {
    $variables[$key] = !empty($element["#$key"]) ? $element["#$key"] : FALSE;
  }
}
