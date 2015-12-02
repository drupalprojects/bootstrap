<?php
/**
 * @file
 * form-multiple-value-form.vars.php
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess().
 *
 * @see template_preprocess_field_multiple_value_form().
 */
function bootstrap_preprocess_field_multiple_value_form(&$variables) {
  $element = $variables['element'];

  // Wrap header columns in label element for Bootstrap.
  if ($variables['multiple']) {
    $header_attributes = new Attribute([
      'class' => ['label'],
    ]);
    $header = [
      [
        'data' => [
          '#prefix' => '<label' . $header_attributes . '>',
          'title' => ['#markup' => $element['#title']],
          '#suffix' => '</label>',
        ],
        'colspan' => 2,
        'class' => ['field-label'],
      ],
      t('Order', [], ['context' => 'Sort order']),
    ];

    $variables['table']['#header'] = $header;
  }
}
