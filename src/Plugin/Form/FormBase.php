<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Form\FormBase.
 */

namespace Drupal\bootstrap\Plugin\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Base form alter class.
 */
class FormBase extends PluginBase implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {}

  /**
   * {@inheritdoc}
   */
  public static function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public static function validateForm(array &$form, FormStateInterface $form_state) {}

}
