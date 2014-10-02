/**
 * Anchor fixes.
 */
var $scrollableElement = $();
Drupal.behaviors.bootstrapAnchors = {
  attach: function(context, settings) {
    var i, elements = ['html', 'body'];
    if (!$scrollableElement.length) {
      for (i = 0; i < elements.length; i++) {
        var $element = $(elements[i]);
        if ($element.scrollTop() > 0) {
          $scrollableElement = $element;
          break;
        }
        else {
          $element.scrollTop(1);
          if ($element.scrollTop() > 0) {
            $element.scrollTop(0);
            $scrollableElement = $element;
            break;
          }
        }
      }
    }
    if (!settings.bootstrap || !settings.bootstrap.anchorsFix) {
      return;
    }
    var anchors = $(context).find('a').toArray();
    for (i = 0; i < anchors.length; i++) {
      if (!anchors[i].scrollTo) {
        this.bootstrapAnchor(anchors[i]);
      }
    }
    $scrollableElement.once('bootstrap-anchors', function () {
      $scrollableElement.on('click.bootstrap-anchors', 'a[href*="#"]:not([data-toggle],[data-target],[data-slide])', function(e) {
        if (this.scrollTo) {
          this.scrollTo(e);
        }
      });
    });
  },
  bootstrapAnchor: function (element) {
    element.validAnchor = element.nodeName === 'A' && (location.hostname === element.hostname || !element.hostname) && (element.hash.replace(/#/,'').length > 0);
    element.scrollTo = function(event) {
      var attr = 'id';
      var $target = $(element.hash);
      // Check for anchors that use the name attribute instead.
      if (!$target.length) {
        attr = 'name';
        $target = $('[name="' + element.hash.replace('#', '') + '"]');
      }
      // Immediately stop if no anchors are found.
      if (!this.validAnchor && !$target.length) {
        return;
      }
      // Anchor is valid, continue if there is an offset.
      var offset = $target.offset().top - parseInt($scrollableElement.css('paddingTop'), 10) - parseInt($scrollableElement.css('marginTop'), 10);
      if (offset > 0) {
        if (event) {
          event.preventDefault();
        }
        var $fakeAnchor = $('<div/>')
          .addClass('element-invisible')
          .attr(attr, $target.attr(attr))
          .css({
            position: 'absolute',
            top: offset + 'px',
            zIndex: -1000
          })
          .appendTo($scrollableElement);
        $target.removeAttr(attr);
        var complete = function () {
          location.hash = element.hash;
          $fakeAnchor.remove();
          $target.attr(attr, element.hash.replace('#', ''));
        };
        if (Drupal.settings.bootstrap.anchorsSmoothScrolling) {
          $scrollableElement.animate({ scrollTop: offset, avoidTransforms: true }, 400, complete);
        }
        else {
          $scrollableElement.scrollTop(offset);
          complete();
        }
      }
    };
  }
};
