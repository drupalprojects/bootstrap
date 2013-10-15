(function ($) {

  /**
   * Provide vertical tab summaries for Bootstrap settings.
   */
  Drupal.behaviors.bootstrapSettingSummaries = {
    attach: function (context) {
      var $context = $(context);

      // BootstrapCDN.
      $context.find('#edit-bootstrap-cdn').drupalSetSummary(function () {
        var version = $context.find('select[name="bootstrap_cdn"]').val();
        if (version.length) {
          return version;
        }
        else {
          return Drupal.t('Disabled');
        }
      });

      // Bootswatch.
      $context.find('#edit-bootswatch').drupalSetSummary(function () {
        var theme = $context.find('select[name="bootstrap_bootswatch"]').val();
        if (theme.length) {
          return $context.find('select[name="bootstrap_bootswatch"] :selected').text();
        }
        else {
          return Drupal.t('Disabled');
        }
      });

      // Breadcrumbs.
      $context.find('#edit-breadcrumbs').drupalSetSummary(function () {
        var summary = [$context.find('select[name="bootstrap_breadcrumb"] :selected').text()];
        var breadcrumb = parseInt($context.find('select[name="bootstrap_breadcrumb"]').val(), 10);
        if (breadcrumb) {
          if ($context.find('input[name="bootstrap_breadcrumb_home"]').is(':checked')) {
            summary.push(Drupal.t('Home breadcrumb link'));
          }
          if ($context.find('input[name="bootstrap_breadcrumb_title"]').is(':checked')) {
            summary.push(Drupal.t('Current page title'));
          }
        }
        return summary.join(', ');
      });

      // Navbar.
      $context.find('#edit-navbar').drupalSetSummary(function () {
        var summary = [$context.find('select[name="bootstrap_navbar_position"] :selected').text()];
        if ($context.find('input[name="bootstrap_navbar_inverse"]').is(':checked')) {
          summary.push(Drupal.t('Inverse'));
        }
        return summary.join(', ');
      });

      // Advanced.
      $context.find('#edit-advanced').drupalSetSummary(function () {
        var summary = [];
        if ($context.find('input[name="bootstrap_rebuild_registry"]').is(':checked')) {
          summary.push(Drupal.t('Rebuild Registry'));
        }
        return summary.join(', ');
      });
    }
  };

  /**
   * Provide Bootstrap Bootswatch preview.
   */
  Drupal.behaviors.bootstrapBootswatchPreview = {
    attach: function (context) {
      var $context = $(context);
      var $preview = $context.find('#bootswatch-preview');
      $preview.once('bootswatch', function () {
        $.get("http://api.bootswatch.com/3/", function (data) {
          var themes = data.themes;
          for (var i = 0, len = themes.length; i < len; i++) {
            $('<a/>').attr({
              id: themes[i].name.toLowerCase(),
              class: 'bootswatch-preview element-invisible',
              href: themes[i].preview,
              target: '_blank'
            }).html(
              $('<img/>').attr({
                src: themes[i].thumbnail,
                alt: themes[i].name
              })
            )
            .appendTo($preview);
          }
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

  /**
   * Provide Bootstrap navbar preview.
   */
  Drupal.behaviors.bootstrapNavbarPreview = {
    attach: function (context) {
      var $context = $(context);
      var $preview = $context.find('#edit-navbar');
      $preview.once('navbar', function () {
        var $body = $context.find('body');
        var $navbar = $context.find('#navbar.navbar');
        $preview.find('select[name="bootstrap_navbar_position"]').bind('change', function () {
          var $position = $(this).find(':selected').val();
          $navbar.removeClass('navbar-fixed-bottom navbar-fixed-top navbar-static-top container');
          if ($position.length) {
            $navbar.addClass('navbar-'+ $position);
          }
          else {
            $navbar.addClass('container');
          }
          // Apply appropriate classes to body.
          $body.removeClass('navbar-is-fixed-top navbar-is-fixed-bottom navbar-is-static-top');
          switch ($position) {
            case 'fixed-top':
              $body.addClass('navbar-is-fixed-top');
              break;

            case 'fixed-bottom':
              $body.addClass('navbar-is-fixed-bottom');
              break;

            case 'static-top':
              $body.addClass('navbar-is-static-top');
              break;
          }
        });
        $preview.find('input[name="bootstrap_navbar_inverse"]').bind('change', function () {
          $navbar.toggleClass('navbar-inverse navbar-default');
        });
      });
    }
  };

})(jQuery);
