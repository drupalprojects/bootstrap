<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\SearchBlockForm.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
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
    $form['#attributes']['class'][] = 'form-search';

    $form['keys']['#title'] = '';
    $form['keys']['#placeholder'] = (string) t('Search');

    // Hide the default button from display and implement a theme wrapper
    // to add a submit button containing a search icon directly after the
    // input element.
    $form['actions']['submit']['#attributes']['class'][] = 'visually-hidden';

    // Apply a clearfix so the results don't overflow onto the form.
    $form['#attributes']['class'][] = 'content-search';
  }

}
