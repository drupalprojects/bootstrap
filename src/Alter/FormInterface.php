<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\FormInterface.
 */

namespace Drupal\bootstrap\Alter;

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
   *   The current state of the form. The arguments that
   *   \Drupal::formBuilder()->getForm() was originally called with are
   *   available in the array $form_state->getBuildInfo()['args'].
   */
  public static function alter(array &$form, FormStateInterface &$form_state);

}
