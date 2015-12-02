<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Form\SearchForm.
 */

namespace Drupal\bootstrap\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 */
class SearchForm extends FormAlterBase {

  /**
   * {@inheritdoc}
   */
  public static function alter(array &$form, FormStateInterface &$form_state) {
    // Add a clearfix class so the results don't overflow onto the form.
    $form['#attributes']['class'][] = 'clearfix';

    // Remove container-inline from the container classes.
    $form['basic']['#attributes']['class'] = [];

    // Hide the default button from display.
    $form['basic']['submit']['#attributes']['class'][] = 'visually-hidden';
  }

}
