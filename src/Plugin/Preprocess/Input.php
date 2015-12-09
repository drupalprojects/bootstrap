<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\Input.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Bootstrap;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Element;

/**
 * Pre-processes variables for the "input" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "input"
 * )
 */
class Input extends PluginBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    \Drupal\Core\Render\Element::setAttributes($variables['element'], ['id', 'name', 'value', 'type']);
    $element = new Element($variables['element']);

    $variables['icon'] = $element->getProperty('icon');
    $variables['attributes'] = $element->getAttributes();
    $variables['label'] = $element->getProperty('value');

    $variables['input_group'] = $element->hasProperty('input_group') || $element->hasProperty('input_group_button');

    // Create variables for #input_group and #input_group_button flags.
    if ($variables['input_group']) {
      /** @var \Drupal\Core\Render\Renderer $renderer */
      $input_group_attributes = ['class' => ['input-group-' . ($element->hasProperty('input_group_button') ? 'btn' : 'addon')]];
      if ($element->hasProperty('field_prefix')) {
        $variables['prefix'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => $input_group_attributes,
          '#value' => Bootstrap::render($element->getProperty('field_prefix')),
          '#weight' => -1,
        ];
        $element->setProperty('field_prefix', NULL);
      }
      if ($element->hasProperty('field_suffix')) {
        $variables['suffix'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => $input_group_attributes,
          '#value' => Bootstrap::render($element->getProperty('field_suffix')),
          '#weight' => 1,
        ];
        $element->setProperty('field_suffix', NULL);
      }
    }

  }

}
