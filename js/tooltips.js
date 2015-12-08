/**
 * @file
 * Bootstrap Tooltips.
 */

var Drupal = Drupal || {};

(function ($, Drupal, drupalSettings) {
  "use strict";

  /**
   * Bootstrap Tooltips.
   */
  Drupal.behaviors.bootstrapTooltips = {
    attach: function (context) {
      var defaultOptions = {
        animation: !!drupalSettings.bootstrap.tooltip_animation,
        html: !!drupalSettings.bootstrap.tooltip_html,
        placement: drupalSettings.bootstrap.tooltip_placement,
        selector: drupalSettings.bootstrap.tooltip_selector,
        trigger: _.filter(_.values(drupalSettings.bootstrap.tooltip_trigger)).join(' '),
        delay: parseInt(drupalSettings.bootstrap.tooltip_delay, 10),
        container: drupalSettings.bootstrap.tooltip_container,
      };
      var elements = $(context).find('[data-toggle="tooltip"]').toArray();
      for (var i = 0; i < elements.length; i++) {
        var $element = $(elements[i]);
        var options = $.extend({}, defaultOptions, $element.data());
        $element.tooltip(options);
      }
    }
  };

})(jQuery, Drupal, drupalSettings);
