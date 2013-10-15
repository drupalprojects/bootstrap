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
      $('#edit-navbar', context).drupalSetSummary(function(context) {
        var summary = [];

        summary.push(Drupal.t($('select[name="bootstrap_navbar_position"] :selected', context).text()));
        if ($('input[name="bootstrap_navbar_inverse"]', context).is(':checked')) {
          summary.push(Drupal.t('Inverse'));
        }
        return summary.join(', ');
      });
      if (Drupal.settings.ajaxPageState.theme === 'bootstrap') {
        var $navbar_preview = $('#edit-navbar', context);
        $navbar_preview.once('navbar', function () {
          $navbar_preview.find('select[name="bootstrap_navbar_position"]').bind('change', function () {
            var $navbar_position = $('select[name="bootstrap_navbar_position"] :selected', context).val();
            var $navbar = $('#navbar');
            var $inverse = $navbar.hasClass('navbar-inverse');

            $navbar.removeClass();
            $navbar.addClass('navbar');
            if ($navbar_position !== '') {
              $navbar.addClass('navbar-'+ $navbar_position);
            }
            if ($inverse) {
              $navbar.addClass('navbar-inverse');
            } else  {
              $navbar.addClass('navbar-default');
            }

            $('body').removeClass('navbar-is-fixed-top navbar-is-fixed-bottom navbar-is-static-top');
            switch ($navbar_position) {
              case 'fixed-top':
                $('body').addClass('navbar-is-fixed-top');
                break;
              case 'fixed-bottom':
                $('body').addClass('navbar-is-fixed-bottom');
                break;
              case 'static-top':
                $('body').addClass('navbar-is-static-top');
                break;
            }
          });
          $navbar_preview.find('input[name="bootstrap_navbar_inverse"]').bind('change', function () {
            $('#navbar').toggleClass('navbar-inverse navbar-default');
          });
        });
      }

      var $bootswatch_preview = $('#bootswatch-preview', context);
      $bootswatch_preview.once('bootswatch', function () {
        $.get("http://api.bootswatch.com/3/", function (data) {
          var themes = data.themes;
          for (var i = 0, len = themes.length; i < len; i++) {
            $bootswatch_preview.append('<a id="'+ themes[i].name.toLowerCase() +'" class="bootswatch-preview element-invisible" href="'+ themes[i].preview +'" target="_blank"><img src="'+ themes[i].thumbnail +'" alt="' + themes[i].name + '"></a>');
          };
          $bootswatch_preview.parent().find('select[name="bootstrap_bootswatch"]').bind('change', function () {
            $bootswatch_preview.find('.bootswatch-preview').addClass('element-invisible');
            if ($(this).val().length) {
              $bootswatch_preview.find('#' + $(this).val()).removeClass('element-invisible');
            }
          }).change();
        }, "json");
      });
    }
  };

})(jQuery);
