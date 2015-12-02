<?php
/**
 * @file
 * file-upload-help.vars.php
 */

use Drupal\bootstrap\Bootstrap;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SafeMarkup;

/**
 * Prepares variables for file upload help text templates.
 *
 * Default template: file-upload-help.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - description: The normal description for this field, specified by the
 *     user.
 *   - upload_validators: An array of upload validators as used in
 *     $element['#upload_validators'].
 */
function bootstrap_preprocess_file_upload_help(&$variables) {
  $upload_validators = $variables['upload_validators'];
  $cardinality = $variables['cardinality'];
  $descriptions = [];

  if (isset($cardinality)) {
    if ($cardinality == -1) {
      $descriptions[] = t('Unlimited number of files can be uploaded to this field.');
    }
    else {
      $descriptions[] = \Drupal::translation()->formatPlural($cardinality, 'One file only.', 'Maximum @count files.');
    }
  }
  if (isset($upload_validators['file_validate_size'])) {
    $descriptions[] = t('@size limit.', ['@size' => format_size($upload_validators['file_validate_size'][0])]);
  }
  if (isset($upload_validators['file_validate_extensions'])) {
    $descriptions[] = t('Allowed types: @extensions.', ['@extensions' => $upload_validators['file_validate_extensions'][0]]);
  }

  if (isset($upload_validators['file_validate_image_resolution'])) {
    $max = $upload_validators['file_validate_image_resolution'][0];
    $min = $upload_validators['file_validate_image_resolution'][1];
    if ($min && $max && $min == $max) {
      $descriptions[] = t('Images must be exactly <strong>@size</strong> pixels.', ['@size' => $max]);
    }
    elseif ($min && $max) {
      $descriptions[] = t('Images must be larger than <strong>@min</strong> pixels. Images larger than <strong>@max</strong> pixels will be resized.', ['@min' => $min, '@max' => $max]);
    }
    elseif ($min) {
      $descriptions[] = t('Images must be larger than <strong>@min</strong> pixels.', ['@min' => $min]);
    }
    elseif ($max) {
      $descriptions[] = t('Images larger than <strong>@max</strong> pixels will be resized.', ['@max' => $max]);
    }
  }

  // If popovers are enabled.
  if (Bootstrap::getTheme()->getSetting('popover_enabled')) {
    $id = html::getUniqueId('upload-instructions');

    $icon = _bootstrap_icon('question-sign');
    $link_title = SafeMarkup::format($icon . ' ' . '@text', ['@text' => 'More information']);
    $variables['popover_link'] = _bootstrap_popover_link($link_title, $id, t('File requirements'), 'bottom');

    $description_content = [
      '#theme' => 'item_list',
      '#items' => $descriptions,
    ];
    $variables['popover_content'] = _bootstrap_popover_content($id, [$description_content]);
  }

  $variables['descriptions'] = $descriptions;
}
