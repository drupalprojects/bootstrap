<?php
/**
 * @file
 * pager.func.php
 */

/**
 * Implements hook_preprocess_pager().
 *
 * - Changes link title removing '«' and similar symbols.
 * - Wraps non-linked items (like ellipsis) with span tag.
 * - Adds Bootstrap pagination classes.
 */
function bootstrap_preprocess_pager(&$variables) {
  if (!empty($variables['items']['#items'])) {
    $variables['items']['#attributes']['class'] = array_diff(array('pagination', 'pager'), $variables['items']['#attributes']['class']);

    foreach ($variables['items']['#items'] as &$item) {
      if (!empty($item['link']['#options']['pager_context']['link_type'])) {
        switch ($item['link']['#options']['pager_context']['link_type']) {
          case 'first':
            $item['link']['#title'] = str_replace(t('« first'), t('first'), $item['link']['#title']);

            break;
          case 'previous':
            $item['link']['#title'] = str_replace(t('‹ previous'), t('previous'), $item['link']['#title']);
            $item['#wrapper_attributes']['class'][] = 'prev';
            break;
          case 'next':
            $item['link']['#title'] = str_replace(t('next ›'), t('next'), $item['link']['#title']);
            $item['#wrapper_attributes']['class'][] = 'next';
            break;
          case 'last':
            $item['link']['#title'] = str_replace(t('last »'), t('last'), $item['link']['#title']);
            break;
          case 'item':
            break;
        }
      }
      elseif (!empty($item['#markup'])) {
        $item['#type'] = 'html_tag';
        $item['#tag'] = 'span';
        $item['#value'] = $item['#markup'];
        unset($item['#markup']);

        if (in_array('pager-current', $item['#wrapper_attributes']['class'])) {
          $item['#wrapper_attributes']['class'][] = 'active';
        }
      }
    }
  }
}
