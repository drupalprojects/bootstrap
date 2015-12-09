<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\SearchForm.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
use Drupal\bootstrap\Bootstrap;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @BootstrapForm(
 *   id = "search_form",
 * )
 */
class SearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    $container = Bootstrap::element($form['basic'], $form_state);
    $container->removeClass('container-inline');
    $container->keys->setProperty('input_group_button', TRUE);
  }

}
