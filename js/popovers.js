/**
 * @file
 * Bootstrap Popovers.
 */

var Drupal = Drupal || {};

(function ($, Drupal, drupalSettings) {
  "use strict";

  /**
   * Bootstrap Popovers.
   */
  Drupal.behaviors.bootstrapPopovers = {
    attach: function (context) {
      var $currentPopover = $();
      var defaultOptions = {
        animation: !!drupalSettings.bootstrap.popover_animation,
        html: !!drupalSettings.bootstrap.popover_html,
        placement: drupalSettings.bootstrappopover_placement,
        selector: drupalSettings.bootstrap.popover_selector,
        trigger: _.filter(_.values(drupalSettings.bootstrap.popover_trigger)).join(' '),
        triggerAutoclose: !!drupalSettings.bootstrap.popover_trigger_autoclose,
        title: drupalSettings.bootstrap.popover_title,
        content: drupalSettings.bootstrap.popover_content,
        delay: parseInt(drupalSettings.bootstrap.popover_delay, 10),
        container: drupalSettings.bootstrap.popover_container
      };

      if (defaultOptions.triggerAutoclose) {
        $(document).on('click', function (e) {
          if ($currentPopover.length && !$(e.target).is('[data-toggle=popover]') && $(e.target).parents('.popover.in').length === 0) {
            $currentPopover.popover('hide');
            $currentPopover = $();
          }
        });
      }
      var elements = $(context).find('[data-toggle=popover]').toArray();
      for (var i = 0; i < elements.length; i++) {
        var $element = $(elements[i]);
        var options = $.extend({}, defaultOptions, $element.data());
        if (!options.content) {
          options.content = function () {
            var target = $(this).data('target');
            return target && $(target) && $(target).length && $(target).clone().removeClass('visually-hidden').wrap('<div/>').parent()[$(this).data('bs.popover').options.html ? 'html' : 'text']() || '';
          }
        }
        $element.popover(options).on('click', function (e) {
          e.preventDefault();
        });
        if (options.triggerAutoclose) {
          $element.on('show.bs.popover', function () {
            if ($currentPopover.length) {
              $currentPopover.popover('hide');
            }
            $currentPopover = $(this);
          });
        }
      }
    }
  };

})(jQuery, Drupal, drupalSettings);
