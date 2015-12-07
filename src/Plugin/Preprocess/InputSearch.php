<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\InputSearch.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Utility\Element;

/**
 * Pre-processes variables for the "input__search" theme hook.
 *
 * @ingroup theme_preprocess
 *
 * @BootstrapPreprocess(
 *   id = "input__search"
 * )
 */
class InputSearch extends Input implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $element = new Element($variables['element']);
    $element->setAttributes([
      'placeholder' => t('Search'),
      'data-toggle' => 'tooltip',
      'title' => t('Enter the terms you wish to search for.'),
    ]);
    parent::preprocess($variables, $hook, $info);
  }

}
