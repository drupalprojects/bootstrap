<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\FormInterface.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the interface for an object oriented form alter.
 */
interface FormInterface {

  /**
   * The alter method to store the code.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   String representing the name of the form itself. Typically this is the
   *   name of the function that generated the form.
   */
  public function alter(array &$form, FormStateInterface $form_state, $form_id = NULL);

  /**
   * The submit callback for the form.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submit(array $form, FormStateInterface $form_state);

  /**
   * The validation callback for the form.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validate(array &$form, FormStateInterface $form_state);

}
