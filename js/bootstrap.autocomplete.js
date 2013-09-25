(function ($) {

/**
 * Attaches the autocomplete behavior to all required fields.
 */
Drupal.behaviors.autocomplete = {
  attach: function (context, settings) {
    var acdb = [];
    $('input.autocomplete', context).once('autocomplete', function () {
      var uri = this.value;
      if (!acdb[uri]) {
        acdb[uri] = new Drupal.ACDB(uri);
      }
      var $input = $('#' + this.id.substr(0, this.id.length - 13))
        .attr('autocomplete', 'OFF')
        .attr('aria-autocomplete', 'list');
      $($input[0].form).submit(Drupal.autocompleteSubmit);
      $input
        .after($('<span class="element-invisible" aria-live="assertive"></span>')
          .attr('id', $input.attr('id') + '-autocomplete-aria-live')
        );
      $input.parent().parent().attr('role', 'application');
      new Drupal.jsAC($input, acdb[uri]);
    });
  }
};

Drupal.jsAC.prototype.setStatus = function (status) {
  var $throbber = $('.glyphicon-refresh', $('#' + this.input.id).parent());

  switch (status) {
    case 'begin':
      $throbber.addClass('glyphicon-spin');
      $(this.ariaLive).html(Drupal.t('Searching for matches...'));
      break;
    case 'cancel':
    case 'error':
    case 'found':
      $throbber.removeClass('glyphicon-spin');
      break;
  }
};

})(jQuery);
