/**
 * Tabledrag theming elements.
 */
Drupal.theme.tableDragChangedMarker = function () {
  return '<span class="tabledrag-changed glyphicon glyphicon-warning-sign text-warning"></span>';
};

Drupal.theme.tableDragChangedWarning = function () {
  return '<div class="tabledrag-changed-warning alert alert-warning messages warning">' + Drupal.theme('tableDragChangedMarker') + ' ' + Drupal.t('Changes made in this table will not be saved until the form is submitted.') + '</div>';
};
