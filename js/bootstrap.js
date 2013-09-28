/**
 * @file
 * bootstrap.js
 *
 * Provides general enhancements and fixes to Bootstrap's JS files.
 */

var Drupal = Drupal || {};

(function($, Drupal){
  "use strict";

  Drupal.behaviors.bootstrap = {
    attach: function(context) {

      // Fix collapsible links prevent the default click behavior.
      $(context).find('a[data-toggle="collapse"]').once('bootstrap-collapse', function () {
        $(this).on('click', function (e) {
          e.preventDefault();
        });
      });

      // Provide some Bootstrap tab/Drupal integration.
      $(context).find('.tabbable').once('bootstrap-tabs', function () {
        var $wrapper = $(this);
        var $tabs = $wrapper.find('.nav-tabs');
        var $content = $wrapper.find('.tab-content');
        var borderRadius = parseInt($content.css('borderTopRightRadius'), 10);

        var bootstrapTabResize = function() {
          if ($wrapper.hasClass('tabs-left') || $wrapper.hasClass('tabs-right')) {
            $content.css('min-height', $tabs.outerHeight());
          }
        }

        // Provide summary support.
        $tabs.find('a').append($('<div class="summary"></div>'));
        $content.on('summaryUpdated', '.tab-pane', function () {
          $tabs.find('a[href="#' + $(this).attr('id') + '"]').parent().find('.summary').html($(this).drupalGetSummary());
          bootstrapTabResize();
        });
        $content.find('.tab-pane').trigger('summaryUpdated');

        // Add min-height on content for left and right tabs.
        bootstrapTabResize();

        // Detect tab switch.
        $tabs.on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
          bootstrapTabResize();
          if ($(e.target).parent().is(':first-child')) {
            $content.css('borderTopLeftRadius', '0');
          }
          else {
            $content.css('borderTopLeftRadius', borderRadius + 'px');
          }
        });

      });

    }
  };

})(jQuery, Drupal);
