<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\FieldMultipleValueForm.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "field_multiple_value_form" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "field_multiple_value_form"
 * )
 */
class FieldMultipleValueForm implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
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

}
