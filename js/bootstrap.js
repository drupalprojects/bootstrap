/**
 * @file
 * bootstrap.js
 *
 * Provides general enhancements and fixes to Bootstrap's JS files.
 */

var Drupal = Drupal || {};

(function($, Drupal){
  "use strict";

  var $scrollableElement = $();

  var BootstrapAnchor = function (element) {
    this.element = element;
    this.valid = (location.hostname === element.hostname || !element.hostname) && element.hash.replace(/#/,'').length && $(element).is(':not([data-toggle],[data-target])');
    return this;
  };
  BootstrapAnchor.prototype.scrollTo = function(event) {
    if (!this.valid) {
      return;
    }
    if (event) {
      event.preventDefault();
    }
    var hash = this.element.hash;
    var attr = 'id';
    var $target = $(hash);
    if (!$target.length) {
      attr = 'name';
      $target = $('[name="' + hash.replace('#', '') + '"');
    }
    var offset = $target.offset().top - parseInt($scrollableElement.css('paddingTop'), 10);
    if ($target.length && offset >= 0) {
      var $fakeAnchor = $('<div/>')
        .addClass('element-invisible')
        .attr(attr, $target.attr(attr))
        .css({
          position: 'absolute',
          top: offset + 'px',
          zIndex: -1000
        })
        .appendTo(document);
      $target.removeAttr(attr);
      var complete = function () {
        location.hash = hash;
        $fakeAnchor.remove();
        $target.attr(attr, hash.replace('#', ''));
      };
      if (Drupal.settings.bootstrap.anchorsSmoothScrolling) {
        $scrollableElement.animate({ scrollTop: offset, avoidTransforms: true }, 400, complete);
      }
      else {
        $scrollableElement.css({ scrollTop: offset });
        complete();
      }
    }
  };

  Drupal.behaviors.bootstrapAnchors = {
    attach: function(context, settings) {
      $scrollableElement = this.scrollableElement('html', 'body');

      if (!settings.bootstrap || !settings.bootstrap.anchorsFix || (parseInt($scrollableElement.css('paddingTop'), 10) <= 0 && settings.bootstrap.anchorsFix && !settings.bootstrap.anchorsSmoothScrolling)) {
        return;
      }
      var $anchors = $(context).find('a');
      for (var i = 0; i < $anchors.length; i++) {
        var a = $anchors[i];
        a.bootstrapAnchor = new BootstrapAnchor(a);
      }
      $scrollableElement.once('bootstrap-anchors', function () {
        $scrollableElement.on('click.bootstrap-anchors', 'a[href*="#"]', function(e) {
          var a = this;
          if (typeof a.bootstrapAnchor === 'undefined') {
            a.bootstrapAnchor = new BootstrapAnchor(a);
          }
          a.bootstrapAnchor.scrollTo(e);
        });
      });
    },
    scrollableElement: function () {
      var $element = $();
      for (var i = 0; i < arguments.length; i++) {
        var $scrollElement = $(arguments[i]);
        if ($scrollElement.scrollTop() > 0) {
          $element = $scrollElement;
          break;
        }
        else {
          $scrollElement.scrollTop(1);
          if ($scrollElement.scrollTop() > 0) {
            $scrollElement.scrollTop(0);
            $element = $scrollElement;
            break;
          }
        }
      }
      return $element;
    }
};

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
        var borderRadius = parseInt($content.css('borderBottomRightRadius'), 10);

        var bootstrapTabResize = function() {
          if ($wrapper.hasClass('tabs-left') || $wrapper.hasClass('tabs-right')) {
            $content.css('min-height', $tabs.outerHeight());
          }
        };

        // Add min-height on content for left and right tabs.
        bootstrapTabResize();

        // Detect tab switch.
        if ($wrapper.hasClass('tabs-left') || $wrapper.hasClass('tabs-right')) {
          $tabs.on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            bootstrapTabResize();
            if ($wrapper.hasClass('tabs-left')) {
              if ($(e.target).parent().is(':first-child')) {
                $content.css('borderTopLeftRadius', '0');
              }
              else {
                $content.css('borderTopLeftRadius', borderRadius + 'px');
              }
            }
            else {
              if ($(e.target).parent().is(':first-child')) {
                $content.css('borderTopRightRadius', '0');
              }
              else {
                $content.css('borderTopRightRadius', borderRadius + 'px');
              }
            }
          });
        }

      });

    }
  };

})(jQuery, Drupal);
