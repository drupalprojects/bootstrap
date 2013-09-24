(function ($) {

  Drupal.behaviors.bootstrapBootswatch = {
    attach: function (context) {
      // Provide vertical tab summaries.
      $('#edit-bootstrap-cdn', context).drupalSetSummary(function(context) {
        var version = $('select[name="bootstrap_cdn"]', context).val();
        if (version.length) {
          return version;
        }
        else {
          return Drupal.t('Disabled');
        }
      });
      $('#edit-bootswatch', context).drupalSetSummary(function(context) {
        var theme = $('select[name="bootstrap_bootswatch"]', context).val();
        if (theme.length) {
          return $('select[name="bootstrap_bootswatch"] :selected', context).text();
        }
        else {
          return Drupal.t('Disabled');
        }
      });
      $('#edit-advanced', context).drupalSetSummary(function(context) {
        var summary = [];
        if ($('input[name="bootstrap_rebuild_registry"]', context).is(':checked')) {
          summary.push(Drupal.t('Rebuild Registry'));
        }
        return summary.join(', ');
      });
      $('#edit-breadcrumbs', context).drupalSetSummary(function(context) {
        var summary = [$('select[name="bootstrap_breadcrumb"] :selected', context).text()];
        var breadcrumb = parseInt($('select[name="bootstrap_breadcrumb"]', context).val(), 10);
        if (breadcrumb) {
          if ($('input[name="bootstrap_breadcrumb_home"]', context).is(':checked')) {
            summary.push(Drupal.t('Home breadcrumb link'));
          }
          if ($('input[name="bootstrap_breadcrumb_title"]', context).is(':checked')) {
            summary.push(Drupal.t('Current page title'));
          }
        }
        return summary.join(', ');
      });


      var $preview = $('#bootswatch-preview', context);
      $preview.once('bootswatch', function () {
        $.get("http://api.bootswatch.com/3/", function (data) {
          var themes = data.themes;
          for (var i = 0, len = themes.length; i < len; i++) {
            $preview.append('<a id="'+ themes[i].name.toLowerCase() +'" class="bootswatch-preview element-invisible" href="'+ themes[i].preview +'" target="_blank"><img src="'+ themes[i].thumbnail +'" alt="' + themes[i].name + '"></a>');
          };
          $preview.parent().find('select[name="bootstrap_bootswatch"]').bind('change', function () {
            $preview.find('.bootswatch-preview').addClass('element-invisible');
            if ($(this).val().length) {
              $preview.find('#' + $(this).val()).removeClass('element-invisible');
            }
          }).change();
        }, "json");
      });
    }
  };

})(jQuery);
