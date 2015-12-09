<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Process\Search.
 */

namespace Drupal\bootstrap\Plugin\Process;

use Drupal\bootstrap\Annotation\BootstrapProcess;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Processes the "actions" element.
 *
 * @BootstrapProcess(
 *   id = "search"
 * )
 */
class Search extends PluginBase implements ProcessInterface {

  /**
   * {@inheritdoc}
   */
  public static function process(array $element, FormStateInterface $form_state, array &$complete_form) {
    if (!empty($element['#bootstrap_ignore_process'])) {
      return $element;
    }

    $e = Element::create($element);
    $e->setProperty('title_display', 'invisible');
    $e->setAttribute('placeholder', $e->getProperty('placeholder', $e->getProperty('title', t('Search'))));
    if (!$e->hasProperty('description')) {
      $e->setProperty('description', t('Enter the terms you wish to search for.'));
    }

    return $element;
  }

}
