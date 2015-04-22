(function ($, Drupal) {

  "use strict";

  /**
   * Process elements with the .dropbutton class on page load.
   */
  Drupal.behaviors.dropButton = {
    attach: function (context, settings) {
      var $dropbuttons = $(context).find('.dropbutton-wrapper').once('dropbutton');
      if ($dropbuttons.length) {
        // Initialize all buttons.
        for (var i = 0, il = $dropbuttons.length; i < il; i++) {
          DropButton.dropbuttons.push(new DropButton($dropbuttons[i], settings.dropbutton));
        }
      }
    }
  };

  /**
   * A DropButton presents an HTML list as a button with a primary action.
   *
   * All secondary actions beyond the first in the list are presented in a
   * dropdown list accessible through a toggle arrow associated with the button.
   *
   * @param {jQuery} $dropbutton
   *   A jQuery element.
   *
   * @param {Object} settings
   *   A list of options including:
   *    - {String} title: The text inside the toggle link element. This text is
   *      hidden from visual UAs.
   */
  function DropButton(dropbutton, settings) {
    // Merge defaults with settings.
    var options = $.extend({'title': Drupal.t('List additional actions')}, settings);
    var $dropbutton = $(dropbutton);
    $dropbutton.addClass('form-group');
    this.$dropbutton = $dropbutton;
    this.$list = $dropbutton.find('.dropbutton');
    this.$dropbutton_widget = this.$dropbutton.find('.dropbutton-widget');
    // Find actions and mark them.
    this.$actions = this.$list.find('li').addClass('dropbutton-action');

    // Add the special dropdown only if there are hidden actions.
    if (this.$actions.length > 1) {
      this.$dropbutton_widget.addClass('btn-group');
      this.$list.addClass('dropdown-menu');

      // Identify the first element of the collection and additional elements.
      var $primary = this.$actions.slice(0, 1);
      var $primary_action = $primary.children().first();
      var $secondary = this.$actions.slice(1).addClass('secondary-action');

      this.$dropbutton_widget.prepend(Drupal.theme('dropbuttonToggle', options));
      $primary_action.clone().prependTo(this.$dropbutton_widget);

      // Handle dropbutton links that are input buttons.
      if ($primary_action[0].localName == 'input') {
        options.classes = $primary.children().first().attr('class');
        $primary_action.addClass('btn-link');
        process_secondary_links(this.$list);
      }
      else {
        options.classes = 'btn';
        this.$dropbutton_widget.children().first().addClass('btn btn-default');
        process_secondary_links(this.$list);
      }
      // Add additional classes from the first element.
      this.$dropbutton.find('.dropdown-toggle').addClass(this.$dropbutton.find('.btn').first().attr('class'));
    }
    else {
      this.$dropbutton.addClass('dropbutton-single');
    }
  }

  /**
   * Extend the DropButton constructor.
   */
  $.extend(DropButton, {
    /**
     * Store all processed DropButtons.
     *
     * @type {Array}
     */
    dropbuttons: []
  });

  $.extend(Drupal.theme, {
    /**
     * A toggle is an interactive element often bound to a click handler.
     *
     * @param {Object} options
     *   - {String} title: (optional) The HTML anchor title attribute and
     *     text for the inner span element.
     *
     * @return {String}
     *   A string representing a DOM fragment.
     */
    dropbuttonToggle: function (options) {
      return '<button type="button" class="dropdown-toggle ' + options.classes + '" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>';
    }
  });

  /**
   * Process secondary links by converting items to Bootstrap button links.
   *
   * @param $element
   *   - jQuery secondary links.
   */
  function process_secondary_links($element) {
    $.each($element, function (index, value) {
      $(this).find('.btn').addClass('btn-link');
      //Remove any button icons.
      $(this).find('span.glyphicon').remove();
    });
  }

  // Expose constructor in the public space.
  Drupal.DropButton = DropButton;

})(jQuery, Drupal);
