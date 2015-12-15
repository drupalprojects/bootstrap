/**
 * @file
 * Provides a click handler for buttons in dropdown menus.
 */

(function ($) {
  "use strict";

  // Delegates links that are really "actions" (buttons) in the dropdown menu
  // to the actual button so it can actually submit the form.
  $(document).on('click', 'a[data-target="dropdown-button"]', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).parent().find('button').trigger('click');
  });

})(jQuery);
