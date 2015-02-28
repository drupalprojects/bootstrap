(function ($) {

  /**
   * Add an asterisk or other marker to the changed row.
   */
  if (typeof Drupal.tableDrag !== 'undefined') {
    Drupal.tableDrag.prototype.row.prototype.markChanged = function () {
      var marker = Drupal.theme('tableDragChangedMarker');
      var $cell = $(this.element).find('td:first');
      // Find the first appropriate place to insert the marker.
      var $target = $($cell.find('.file-size').get(0) || $cell.find('.file').get(0) || $cell.find('.tabledrag-handle').get(0));
      if (!$cell.find('.tabledrag-changed').length) {
        $target.after(' ' + marker + ' ');
      }
    };
  }

})(jQuery);
