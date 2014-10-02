/**
 * Bootstrap Popovers.
 */
Drupal.behaviors.bootstrapPopovers = {
  attach: function (context, settings) {
    var $context = $(context);
    if (settings.bootstrap && settings.bootstrap.popoverEnabled) {
      var $currentPopover = $();
      if (settings.bootstrap.popoverOptions.triggerAutoclose) {
        $(document).on('click', function (e) {
          if ($currentPopover.length && !$(e.target).is('[data-toggle=popover]') && $(e.target).parents('.popover.in').length === 0) {
            $currentPopover.popover('hide');
            $currentPopover = $();
          }
        });
      }
      $context.find('[data-toggle="popover"]').each(function () {
        var $element = $(this);
        var options = $.extend({}, settings.bootstrap.popoverOptions, $element.data());
        if (!options.content) {
          options.content = function () {
            var target = $(this).data('target');
            return target && $(target) && $(target).length && $(target).clone().removeClass('element-invisible').wrap('<div/>').parent()[$(this).data('bs.popover').options.html ? 'html' : 'text']() || '';
          };
        }
        $element.popover(options).on('click', function (e) {
          e.preventDefault();
        });
        if (settings.bootstrap.popoverOptions.triggerAutoclose) {
          $element.on('show.bs.popover', function () {
            if ($currentPopover.length) {
              $currentPopover.popover('hide');
            }
            $currentPopover = $(this);
          });
        }
      });
    }
  }
};
