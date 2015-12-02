<?php
/**
 * @file
 * Stub file for bootstrap_file_upload_help().
 */

use Drupal\bootstrap\Bootstrap;

/**
 * Returns HTML for help text based on file upload validators.
 *
 * @param array $variables
 *   An associative array containing:
 *   - description: The normal description for this field, specified by the
 *     user.
 *   - upload_validators: An array of upload validators as used in
 *     $element['#upload_validators'].
 *
 * @return string
 *   The constructed HTML.
 *
 * @see theme_file_upload_help()
 *
 * @ingroup theme_functions
 */
function bootstrap_file_upload_help($variables) {
  // If popover's are disabled, just theme this normally.
  if (!Bootstrap::getTheme()->getSetting('popover_enabled')) {
    return theme_file_upload_help($variables);
  }

  $build = [];
  if (!empty($variables['description'])) {
    $build['description'] = [
      '#markup' => $variables['description'] . '<br>',
    ];
  }

  $descriptions = [];
  $upload_validators = $variables['upload_validators'];
  if (isset($upload_validators['file_validate_size'])) {
    $descriptions[] = t('Files must be less than !size.', ['!size' => '<strong>' . format_size($upload_validators['file_validate_size'][0]) . '</strong>']);
  }
  if (isset($upload_validators['file_validate_extensions'])) {
    $descriptions[] = t('Allowed file types: !extensions.', ['!extensions' => '<strong>' . check_plain($upload_validators['file_validate_extensions'][0]) . '</strong>']);
  }
  if (isset($upload_validators['file_validate_image_resolution'])) {
    $max = $upload_validators['file_validate_image_resolution'][0];
    $min = $upload_validators['file_validate_image_resolution'][1];
    if ($min && $max && $min == $max) {
      $descriptions[] = t('Images must be exactly !size pixels.', ['!size' => '<strong>' . $max . '</strong>']);
    }
    elseif ($min && $max) {
      $descriptions[] = t('Images must be between !min and !max pixels.', ['!min' => '<strong>' . $min . '</strong>', '!max' => '<strong>' . $max . '</strong>']);
    }
    elseif ($min) {
      $descriptions[] = t('Images must be larger than !min pixels.', ['!min' => '<strong>' . $min . '</strong>']);
    }
    elseif ($max) {
      $descriptions[] = t('Images must be smaller than !max pixels.', ['!max' => '<strong>' . $max . '</strong>']);
    }
  }

  if ($descriptions) {
    $id = drupal_html_id('upload-instructions');
    $build['instructions'] = [
      '#theme' => 'link__file_upload_requirements',
      // @todo remove space between icon/text and fix via styling.
      '#text' => _bootstrap_icon('question-sign') . ' ' . t('More information'),
      '#path' => '#',
      '#options' => [
        'attributes' => [
          'data-toggle' => 'popover',
          'data-target' => "#$id",
          'data-html' => TRUE,
          'data-placement' => 'bottom',
          'data-title' => t('File requirements'),
        ],
        'html' => TRUE,
        'external' => TRUE,
      ],
    ];
    $build['requirements'] = [
      '#theme_wrappers' => ['container__file_upload_requirements'],
      '#attributes' => [
        'id' => $id,
        'class' => ['element-invisible', 'help-block'],
      ],
    ];
    $build['requirements']['validators'] = [
      '#theme' => 'item_list__file_upload_requirements',
      '#items' => $descriptions,
    ];
  }

  return drupal_render($build);
}
