<?php
/**
 * @file
 * file-widget.vars.php
 */

/**
 * Overrides theme_file_widget().
 */
function bootstrap_preprocess_file_widget(&$variables) {
  $element = $variables['element'];
  if (!empty($element['fids']['#value'])) {
    // Add the file size after the file name.
    $file = reset($element['#files']);
    $element['file_' . $file->id()]['filename']['#suffix'] = ' <span class="file-size badge">' . format_size($file->getSize()) . '</span> ';
  }
  // The "form-managed-file" class is required for proper Ajax functionality.
  $variables['attributes'] = array(
    'class' => array(
      'file-widget',
      'form-managed-file',
      'clearfix',
    ),
  );

  $element['upload']['#prefix'] = '<div class="input-group">';
  $element['upload_button']['#prefix'] = '<span class="input-group-btn">';
  $element['upload_button']['#suffix'] = '</span></div>';
  $element['upload_button']['#attributes']['class'] = array('btn', 'btn-primary');
  $variables['element'] = $element;
}

