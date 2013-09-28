(function ($) {

Drupal.behaviors.filterStatus = {
  attach: function (context, settings) {
    $('#filters-status-wrapper input.form-checkbox', context).once('filter-status', function () {
      var $checkbox = $(this);
      // Retrieve the tabledrag row belonging to this filter.
      var $row = $('#' + $checkbox.attr('id').replace(/-status$/, '-weight'), context).closest('tr');
      // Retrieve the tab belonging to this filter.
      var $tab = $('a[href="#' + $checkbox.attr('id').replace(/-status$/, '-settings"]'), context).parent();
      // Retrieve the tab pane belonging to this filter.
      var $pane = $('#' + $checkbox.attr('id').replace(/-status$/, '-settings'), context);

      // Bind click handler to this checkbox to conditionally show and hide the
      // filter's tableDrag row and vertical tab pane.
      $checkbox.bind('click.filterUpdate', function () {
        if ($checkbox.is(':checked')) {
          $row.show();
          if ($tab.length) {
            $tab.show();
            $tab.find('.summary').html($pane.drupalGetSummary());
          }
        }
        else {
          $row.hide();
          if ($tab.length) {
            $tab.hide();
            $tab.find('.summary').html($pane.drupalGetSummary());
          }
        }
        // Find first visible tab and show it.
        $tab.parent().find('li:visible a').first().tab('show');
        // Restripe table after toggling visibility of table row.
        Drupal.tableDrag['filter-order'].restripeTable();
      });

      // Attach summary for configurable filters (only for screen-readers).
      if ($tab.length) {
        $pane.drupalSetSummary(function () {
          return $checkbox.is(':checked') ? Drupal.t('Enabled') : Drupal.t('Disabled');
        });
      }

      // Trigger our bound click handler to update elements to initial state.
      $checkbox.triggerHandler('click.filterUpdate');
    });
  }
};

})(jQuery);
