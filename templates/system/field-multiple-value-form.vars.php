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
    $header_attributes = new Attribute(array('class' => array('label')));
    $header = array(
      array(
        'data' => array(
          '#prefix' => '<label' . $header_attributes . '>',
          'title' => array(
            '#markup' => $element['#title'],
          ),
          '#suffix' => '</label>',
        ),
        'colspan' => 2,
        'class' => array('field-label'),
      ),
      t('Order', array(), array('context' => 'Sort order')),
    );

    $variables['table']['#header'] = $header;
  }
}
