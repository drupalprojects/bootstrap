<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\SearchBlockForm.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
use Drupal\bootstrap\Bootstrap;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @BootstrapForm(
 *   id = "search_block_form",
 * )
 */
class SearchBlockForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    $container = Bootstrap::element($form, $form_state);
    $container->keys->setProperty('input_group_button', TRUE);
    $container->actions->submit->setIcon();
  }

}
