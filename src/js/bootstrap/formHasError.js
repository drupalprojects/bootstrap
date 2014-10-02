/**
 * Behavior for .
 */
Drupal.behaviors.bootstrapFormHasError = {
  attach: function (context, settings) {
    if (settings.bootstrap && settings.bootstrap.formHasError) {
      var $context = $(context);
      $context.find('.form-item.has-error:not(.form-type-password.has-feedback)').once('error', function () {
        var $formItem = $(this);
        var $input = $formItem.find(':input');
        $input.on('keyup focus blur', function () {
          var value = $input.val() || false;
          $formItem[value ? 'removeClass' : 'addClass']('has-error');
          $input[value ? 'removeClass' : 'addClass']('error');
        });
      });
    }
  }
};
