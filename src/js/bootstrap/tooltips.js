/**
 * Bootstrap Tooltips.
 */
Drupal.behaviors.bootstrapTooltips = {
  attach: function (context, settings) {
    if (settings.bootstrap && settings.bootstrap.tooltipEnabled) {
      var elements = $(context).find('[data-toggle="tooltip"]').toArray();
      for (var i = 0; i < elements.length; i++) {
        var $element = $(elements[i]);
        var options = $.extend({}, settings.bootstrap.tooltipOptions, $element.data());
        $element.tooltip(options);
      }
    }
  }
};
