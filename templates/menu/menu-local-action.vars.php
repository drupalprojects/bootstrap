<?php
/**
 * @file
 * menu-local-action.vars.php
 */

/**
 * Overrides theme_menu_local_action().
 *
 * Prepares variables for single local action link templates.
 *
 * Default template: menu-local-action.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: A render element containing:
 *     - #link: A menu link array with 'title', 'url', and (optionally)
 *       'localized_options' keys.
 */
function bootstrap_preprocess_menu_local_action(&$variables) {
  $link = $variables['element']['#link'];
  $link += array(
    'localized_options' => array(),
  );
  $link['localized_options']['set_active_class'] = TRUE;

  $icon = _bootstrap_iconize_text($link['title']);
  $options = isset($link['localized_options']) ? $link['localized_options'] : array();

  if (isset($link['url'])) {
    // Turn link into a mini-button and colorize based on title.
    if ($class = _bootstrap_colorize_text($link['title'])) {
      if (!isset($options['attributes']['class'])) {
        $options['attributes']['class'] = array();
      }
      $string = is_string($options['attributes']['class']);
      if ($string) {
        $options['attributes']['class'] = explode(' ', $options['attributes']['class']);
      }
      $options['attributes']['class'][] = 'btn';
      $options['attributes']['class'][] = 'btn-xs';
      $options['attributes']['class'][] = 'btn-' . $class;
      if ($string) {
        $options['attributes']['class'] = implode(' ', $options['attributes']['class']);
      }
    }
    // Force HTML so we can render any icon that may have been added.
    $options['html'] = !empty($options['html']) || !empty($icon) ? TRUE : FALSE;

    // Some browsers require ending </span> rather than self-closing tag.
    $icon = substr($icon, 0, -2) . '></span>';

    $variables['link'] = array(
      '#type' => 'link',
      '#title' => $icon . $link['title'],
      '#options' => $options,
      '#url' => $link['url'],
    );
  }
  else {
    $variables['link'] = array(
      '#type' => 'link',
      '#title' => $link['title'],
      '#options' => $options,
      '#url' => $link['url'],
    );
  }
}
