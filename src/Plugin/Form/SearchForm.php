<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\SearchForm.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
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
    // Add a clearfix class so the results don't overflow onto the form.
    $form['#attributes']['class'][] = 'clearfix';

    // Remove container-inline from the container classes.
    $form['basic']['#attributes']['class'] = [];

    // Hide the default button from display.
    $form['basic']['submit']['#attributes']['class'][] = 'visually-hidden';
  }

}
