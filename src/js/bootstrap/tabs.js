/**
 * Bootstrap Tabs.
 */
Drupal.behaviors.bootstrapTabs = {
  attach: function(context) {
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
