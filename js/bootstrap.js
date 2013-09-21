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

    }
  };

})(jQuery, Drupal);
