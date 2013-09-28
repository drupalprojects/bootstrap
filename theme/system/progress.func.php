<?php
/**
 * @file
 * progress.func.php
 */

/**
 * Overrides theme_progress_bar().
 */
function bootstrap_progress_bar($variables) {
  $output = '<div id="progress" class="progress-wrapper">';
  $output .= '  <div class="progress progress-striped progress-info active">';
  $output .= '    <div class="bar" style="width: ' . $variables['percent'] . '%"></div>';
  $output .= '  </div>';
  $output .= '  <div class="percentage pull-right">' . $variables['percent'] . '%</div>';
  $output .= '  <div class="message">' . $variables['message'] . '</div>';
  $output .= '</div>';
  return $output;
}
