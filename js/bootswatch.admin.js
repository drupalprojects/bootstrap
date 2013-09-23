(function ($) {

  Drupal.behaviors.bootstrapBootswatch = {

    attach: function (context, settings) {
      $.get("http://api.bootswatch.com/3/", function (data) {
        var themes = data.themes;
        var previews = '';
        for(var i = 0, len = themes.length; i < len; i++){
          previews += '<div class="text-center bootswatch-preview hidden" id="'+ themes[i].name +'"><p><a href="'+ themes[i].preview +'"><img src="'+ themes[i].thumbnail +'"></a></p></div>';
        };
        $('#bootswatch-previews').append(previews);
        $('select[name="bootstrap_bootswatch"]').change(function () {
          var selected = $('select[name="bootstrap_bootswatch"]').find(':selected').text();
          $('.bootswatch-preview').addClass('hidden');
          if (selected !== '') {
            var show = '#' + selected;
            $(show).removeClass('hidden');
          }
        }).change();
      }, "json");
    }
  };

})(jQuery);
