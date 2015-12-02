<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\Form.
 */

namespace Drupal\bootstrap\Alter;

/**
 * Implements hook_form_alter().
 */
class Form implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(&$form, &$form_state = NULL, &$form_id = NULL) {
    if ($form_id) {
      switch ($form_id) {
        case 'search_form':
          // Add a clearfix class so the results don't overflow onto the form.
          $form['#attributes']['class'][] = 'clearfix';

          // Remove container-inline from the container classes.
          $form['basic']['#attributes']['class'] = [];

          // Hide the default button from display.
          $form['basic']['submit']['#attributes']['class'][] = 'visually-hidden';
          break;

        case 'search_block_form':
          $form['#attributes']['class'][] = 'form-search';

          $form['keys']['#title'] = '';
          $form['keys']['#placeholder'] = (string) t('Search');

          // Hide the default button from display and implement a theme wrapper
          // to add a submit button containing a search icon directly after the
          // input element.
          $form['actions']['submit']['#attributes']['class'][] = 'visually-hidden';

          // Apply a clearfix so the results don't overflow onto the form.
          $form['#attributes']['class'][] = 'content-search';
          break;

        // @todo Check to see if this still works.
        case 'image_style_edit_form':
          $form['effects']['new']['effect']['data']['new']['#input_group_button'] = TRUE;
          break;

        // @todo Check to see if this still works.
        case 'path_admin_filter_form':
          $form['basic']['filter']['#input_group_button'] = TRUE;
          break;
      }
    }
  }

}
