<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Alter\FormSystemThemeSettings.
 */

namespace Drupal\bootstrap\Alter;

use Drupal\bootstrap\Bootstrap;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

Bootstrap::getTheme('bootstrap')->includeOnce('cdn.inc');

/**
 * Implements hook_form_FORM_ID_alter().
 */
class FormSystemThemeSettings implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public static function alter(array &$form, FormStateInterface &$form_state, $form_id = NULL) {
    // Work-around for a core bug affecting admin themes.
    // @see https://drupal.org/node/943212
    $args = $form_state->getBuildInfo()['args'];
    if (isset($form_id) || empty($args[0])) {
      return;
    }

    // Do not add Bootstrap specific settings to non-bootstrap based themes,
    $theme = Bootstrap::getTheme($args[0]);
    if (!$theme->subthemeOf('bootstrap')) {
      return;
    }

    // Create vertical tabs for global settings (provided by core or other
    // contrib modules).
    if (!isset($form['global'])) {
      $form['global'] = [
        '#type' => 'vertical_tabs',
        '#weight' => -9,
        '#prefix' => '<h2><small>' . t('Override Global Settings') . '</small></h2>',
      ];
    }

    // Iterate over all child elements and check to see if they should be
    // moved in the global vertical tabs.
    $global_children = Element::children($form);
    foreach ($global_children as $child) {
      if (isset($form[$child]['#type']) && $form[$child]['#type'] === 'details' && !isset($form[$child]['#group'])) {
        $form[$child]['#group'] = 'global';
      }
    }

    $form['bootstrap'] = [
      '#type' => 'vertical_tabs',
      '#attached' => [
        'library'  => ['bootstrap/adminscript'],
      ],
      '#prefix' => '<h2><small>' . t('Bootstrap Settings') . '</small></h2>',
      '#weight' => -10,
    ];

    // General.
    $form['general'] = [
      '#type' => 'details',
      '#title' => t('General'),
      '#group' => 'bootstrap',
    ];

    // Container.
    $form['general']['container'] = [
      '#type' => 'fieldset',
      '#title' => t('Container'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['general']['container']['fluid_container'] = [
      '#type' => 'checkbox',
      '#title' => t('Fluid container'),
      '#default_value' => $theme->getSetting('fluid_container'),
      '#description' => t('Use <code>.container-fluid</code> class. See <a href=":url">Fluid container</a>', [
        ':url' => 'http://getbootstrap.com/css/#grid-example-fluid',
      ]),
    ];

    // Buttons.
    $form['general']['buttons'] = [
      '#type' => 'details',
      '#title' => t('Buttons'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['general']['buttons']['button_size'] = [
      '#type' => 'select',
      '#title' => t('Default button size'),
      '#default_value' => $theme->getSetting('button_size'),
      '#empty_option' => t('Normal'),
      '#options' => [
        'btn-xs' => t('Extra Small'),
        'btn-sm' => t('Small'),
        'btn-lg' => t('Large'),
      ],
    ];
    $form['general']['buttons']['button_colorize'] = [
      '#type' => 'checkbox',
      '#title' => t('Colorize Buttons'),
      '#default_value' => $theme->getSetting('button_colorize'),
      '#description' => t('Adds classes to buttons based on their text value. See: <a href=":bootstrap_url" target="_blank">Buttons</a> and <a href=":api_url" target="_blank">hook_bootstrap_colorize_text_alter()</a>', [
        ':bootstrap_url' => 'http://getbootstrap.com/css/#buttons',
        ':api_url' => 'http://drupal-bootstrap.org/apis/hook_bootstrap_colorize_text_alter',
      ]),
    ];
    $form['general']['buttons']['button_iconize'] = [
      '#type' => 'checkbox',
      '#title' => t('Iconize Buttons'),
      '#default_value' => $theme->getSetting('button_iconize'),
      '#description' => t('Adds icons to buttons based on the text value. See: <a href=":api_url" target="_blank">hook_bootstrap_iconize_text_alter()</a>', [
        ':api_url' => 'http://drupal-bootstrap.org/apis/hook_bootstrap_iconize_text_alter',
      ]),
    ];

    // Forms.
    $form['general']['forms'] = [
      '#type' => 'details',
      '#title' => t('Forms'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['general']['forms']['forms_required_has_error'] = [
      '#type' => 'checkbox',
      '#title' => t('Make required elements display as an error'),
      '#default_value' => $theme->getSetting('forms_required_has_error'),
      '#description' => t('If an element in a form is required, enabling this will always display the element with a <code>.has-error</code> class. This turns the element red and helps in usability for determining which form elements are required to submit the form.  This feature compliments the "JavaScript > Forms > Automatically remove error classes when values have been entered" feature.'),
    ];
    $form['general']['forms']['forms_smart_descriptions'] = [
      '#type' => 'checkbox',
      '#title' => t('Smart form descriptions (via Tooltips)'),
      '#description' => t('Convert descriptions into tooltips (must be enabled) automatically based on certain criteria. This helps reduce the, sometimes unnecessary, amount of noise on a page full of form elements.'),
      '#default_value' => $theme->getSetting('forms_smart_descriptions'),
    ];
    $form['general']['forms']['forms_smart_descriptions_limit'] = [
      '#type' => 'textfield',
      '#title' => t('"Smart form descriptions" maximum character limit'),
      '#description' => t('Prevents descriptions from becoming tooltips by checking the character length of the description (HTML is not counted towards this limit). To disable this filtering criteria, leave an empty value.'),
      '#default_value' => $theme->getSetting('forms_smart_descriptions_limit'),
      '#states' => [
        'visible' => [
          ':input[name="forms_smart_descriptions"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['general']['forms']['forms_smart_descriptions_allowed_tags'] = [
      '#type' => 'textfield',
      '#title' => t('"Smart form descriptions" allowed (HTML) tags'),
      '#description' => t('Prevents descriptions from becoming tooltips by checking for HTML not in the list above (i.e. links). Separate by commas. To disable this filtering criteria, leave an empty value.'),
      '#default_value' => $theme->getSetting('forms_smart_descriptions_allowed_tags'),
      '#states' => [
        'visible' => [
          ':input[name="forms_smart_descriptions"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Images.
    $form['general']['images'] = [
      '#type' => 'details',
      '#title' => t('Images'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['general']['images']['image_shape'] = [
      '#type' => 'select',
      '#title' => t('Default image shape'),
      '#description' => t('Add classes to an <code>&lt;img&gt;</code> element to easily style images in any project. Note: Internet Explorer 8 lacks support for rounded corners. See: <a href=":bootstrap_url" target="_blank">Image Shapes</a>', [
        ':bootstrap_url' => 'http://getbootstrap.com/css/#images-shapes',
      ]),
      '#default_value' => $theme->getSetting('image_shape'),
      '#empty_option' => t('None'),
      '#options' => [
        'img-rounded' => t('Rounded'),
        'img-circle' => t('Circle'),
        'img-thumbnail' => t('Thumbnail'),
      ],
    ];
    $form['general']['images']['image_responsive'] = [
      '#type' => 'checkbox',
      '#title' => t('Responsive Images'),
      '#default_value' => $theme->getSetting('image_responsive'),
      '#description' => t('Images in Bootstrap 3 can be made responsive-friendly via the addition of the <code>.img-responsive</code> class. This applies <code>max-width: 100%;</code> and <code>height: auto;</code> to the image so that it scales nicely to the parent element.'),
    ];

    // Tables.
    $form['general']['tables'] = [
      '#type' => 'details',
      '#title' => t('Tables'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['general']['tables']['table_bordered'] = [
      '#type' => 'checkbox',
      '#title' => t('Bordered table'),
      '#default_value' => $theme->getSetting('table_bordered'),
      '#description' => t('Add borders on all sides of the table and cells.'),
    ];
    $form['general']['tables']['table_condensed'] = [
      '#type' => 'checkbox',
      '#title' => t('Condensed table'),
      '#default_value' => $theme->getSetting('table_condensed'),
      '#description' => t('Make tables more compact by cutting cell padding in half.'),
    ];
    $form['general']['tables']['table_hover'] = [
      '#type' => 'checkbox',
      '#title' => t('Hover rows'),
      '#default_value' => $theme->getSetting('table_hover'),
      '#description' => t('Enable a hover state on table rows.'),
    ];
    $form['general']['tables']['table_striped'] = [
      '#type' => 'checkbox',
      '#title' => t('Striped rows'),
      '#default_value' => $theme->getSetting('table_striped'),
      '#description' => t('Add zebra-striping to any table row within the <code>&lt;tbody&gt;</code>. <strong>Note:</strong> Striped tables are styled via the <code>:nth-child</code> CSS selector, which is not available in Internet Explorer 8.'),
    ];
    $form['general']['tables']['table_responsive'] = [
      '#type' => 'checkbox',
      '#title' => t('Responsive tables'),
      '#default_value' => $theme->getSetting('table_responsive'),
      '#description' => t('Makes tables responsive by wrapping them in <code>.table-responsive</code> to make them scroll horizontally up to small devices (under 768px). When viewing on anything larger than 768px wide, you will not see any difference in these tables.'),
    ];

    // Components.
    $form['components'] = [
      '#type' => 'details',
      '#title' => t('Components'),
      '#group' => 'bootstrap',
    ];

    // Breadcrumbs.
    $form['components']['breadcrumbs'] = [
      '#type' => 'details',
      '#title' => t('Breadcrumbs'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['components']['breadcrumbs']['breadcrumb'] = [
      '#type' => 'select',
      '#title' => t('Breadcrumb visibility'),
      '#default_value' => $theme->getSetting('breadcrumb'),
      '#options' => [
        0 => t('Hidden'),
        1 => t('Visible'),
        2 => t('Only in admin areas'),
      ],
    ];
    $form['components']['breadcrumbs']['breadcrumb_home'] = [
      '#type' => 'checkbox',
      '#title' => t('Show "Home" breadcrumb link'),
      '#default_value' => $theme->getSetting('breadcrumb_home'),
      '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is enabled.'),
      '#states' => [
        'invisible' => [
          ':input[name="breadcrumb"]' => ['value' => 0],
        ],
      ],
    ];
    $form['components']['breadcrumbs']['breadcrumb_title'] = [
      '#type' => 'checkbox',
      '#title' => t('Show current page title at end'),
      '#default_value' => $theme->getSetting('breadcrumb_title'),
      '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is disabled.'),
      '#states' => [
        'invisible' => [
          ':input[name="breadcrumb"]' => ['value' => 0],
        ],
      ],
    ];

    // Navbar.
    $form['components']['navbar'] = [
      '#type' => 'details',
      '#title' => t('Navbar'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['components']['navbar']['navbar_position'] = [
      '#type' => 'select',
      '#title' => t('Navbar Position'),
      '#description' => t('Select your Navbar position.'),
      '#default_value' => $theme->getSetting('navbar_position'),
      '#options' => [
        'static-top' => t('Static Top'),
        'fixed-top' => t('Fixed Top'),
        'fixed-bottom' => t('Fixed Bottom'),
      ],
      '#empty_option' => t('Normal'),
    ];
    $form['components']['navbar']['navbar_inverse'] = [
      '#type' => 'checkbox',
      '#title' => t('Inverse navbar style'),
      '#description' => t('Select if you want the inverse navbar style.'),
      '#default_value' => $theme->getSetting('navbar_inverse'),
    ];

    // Region wells.
    $wells = [
      '' => t('None'),
      'well' => t('.well (normal)'),
      'well well-sm' => t('.well-sm (small)'),
      'well well-lg' => t('.well-lg (large)'),
    ];
    $form['components']['region_wells'] = [
      '#type' => 'details',
      '#title' => t('Region wells'),
      '#description' => t('Enable the <code>.well</code>, <code>.well-sm</code> or <code>.well-lg</code> classes for specified regions. See: documentation on <a href=":wells" target="_blank">Bootstrap Wells</a>.', [
        ':wells' => 'http://getbootstrap.com/components/#wells',
      ]),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    // Get defined regions.
    $regions = system_region_list('bootstrap');
    foreach ($regions as $name => $title) {
      $form['components']['region_wells']['region_well-' . $name] = [
        '#title' => $title,
        '#type' => 'select',
        '#attributes' => [
          'class' => ['input-sm'],
        ],
        '#options' => $wells,
        '#default_value' => $theme->getSetting('region_well-' . $name),
      ];
    }

    // JavaScript settings.
    $form['javascript'] = [
      '#type' => 'details',
      '#title' => t('JavaScript'),
      '#group' => 'bootstrap',
    ];

    // Anchors.
    $form['javascript']['anchors'] = [
      '#type' => 'details',
      '#title' => t('Anchors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#description' => t('This plugin is not able to be configured from the UI as it is severely broken. In an effort to balance not break backwards compatibility and to prevent new users from running into unforeseen issues, you must manually opt-in/out inside your theme\'s setting configuration file. Please see the following issue for more details: <a href=":url" target="_blank">Replace custom JS with the bootstrap-anchor plugin</a>', [
        ':url' => 'https://www.drupal.org/node/2462645',
      ]),
    ];
    $form['javascript']['anchors']['anchors_fix'] = [
      '#type' => 'checkbox',
      '#title' => t('Fix anchor positions'),
      '#default_value' => $theme->getSetting('anchors_fix'),
      '#description' => t('Ensures anchors are correctly positioned only when there is margin or padding detected on the BODY element. This is useful when fixed navbar or administration menus are used.'),
      // Prevent UI edits, see description above.
      '#disabled' => TRUE,
    ];
    $form['javascript']['anchors']['anchors_smooth_scrolling'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable smooth scrolling'),
      '#default_value' => $theme->getSetting('anchors_smooth_scrolling'),
      '#description' => t('Animates page by scrolling to an anchor link target smoothly when clicked.'),
      '#states' => [
        'invisible' => [
          ':input[name="anchors_fix"]' => ['checked' => FALSE],
        ],
      ],
      // Prevent UI edits, see description above.
      '#disabled' => TRUE,
    ];

    // Forms.
    $form['javascript']['forms'] = [
      '#type' => 'details',
      '#title' => t('Forms'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['javascript']['forms']['forms_has_error_value_toggle'] = [
      '#type' => 'checkbox',
      '#title' => t('Automatically remove error classes when values have been entered'),
      '#default_value' => $theme->getSetting('forms_has_error_value_toggle'),
      '#description' => t('If an element has a <code>.has-error</code> class attached to it, enabling this will automatically remove that class when a value is entered. This feature compliments the "General > Forms > Make required elements display as an error" feature.'),
    ];

    // Popovers.
    $form['javascript']['popovers'] = [
      '#type' => 'details',
      '#title' => t('Popovers'),
      '#description' => t('Add small overlays of content, like those on the iPad, to any element for housing secondary information.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['javascript']['popovers']['popover_enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable popovers.'),
      '#description' => t('Elements that have the <code>data-toggle="popover"</code> attribute set will automatically initialize the popover upon page load. <strong class="error text-error">WARNING: This feature can sometimes impact performance. Disable if pages appear to "hang" after initial load.</strong>'),
      '#default_value' => $theme->getSetting('popover_enabled'),
    ];
    $form['javascript']['popovers']['options'] = [
      '#type' => 'details',
      '#title' => t('Options'),
      '#description' => t('These are global options. Each popover can independently override desired settings by appending the option name to <code>data-</code>. Example: <code>data-animation="false"</code>.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="popover_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['javascript']['popovers']['options']['popover_animation'] = [
      '#type' => 'checkbox',
      '#title' => t('animate'),
      '#description' => t('Apply a CSS fade transition to the popover.'),
      '#default_value' => $theme->getSetting('popover_animation'),
    ];
    $form['javascript']['popovers']['options']['popover_html'] = [
      '#type' => 'checkbox',
      '#title' => t('HTML'),
      '#description' => t("Insert HTML into the popover. If false, jQuery's text method will be used to insert content into the DOM. Use text if you're worried about XSS attacks."),
      '#default_value' => $theme->getSetting('popover_html'),
    ];
    $options = [
      'top',
      'bottom',
      'left',
      'right',
      'auto',
      'auto top',
      'auto bottom',
      'auto left',
      'auto right',
    ];
    $form['javascript']['popovers']['options']['popover_placement'] = [
      '#type' => 'select',
      '#title' => t('placement'),
      '#description' => t('Where to position the popover. When "auto" is specified, it will dynamically reorient the popover. For example, if placement is "auto left", the popover will display to the left when possible, otherwise it will display right.'),
      '#default_value' => $theme->getSetting('popover_placement'),
      '#options' => array_combine($options, $options),
    ];
    $form['javascript']['popovers']['options']['popover_selector'] = [
      '#type' => 'textfield',
      '#title' => t('selector'),
      '#description' => t('If a selector is provided, tooltip objects will be delegated to the specified targets. In practice, this is used to enable dynamic HTML content to have popovers added. See <a href=":this" target="_blank">this</a> and <a href=":example" target="_blank">an informative example</a>.', [
        ':this' => 'https://github.com/twbs/bootstrap/issues/4215',
        ':example' => 'http://jsfiddle.net/fScua/',
      ]),
      '#default_value' => $theme->getSetting('popover_selector'),
    ];
    $options = [
      'click',
      'hover',
      'focus',
      'manual',
    ];
    $form['javascript']['popovers']['options']['popover_trigger'] = [
      '#type' => 'checkboxes',
      '#title' => t('trigger'),
      '#description' => t('How a popover is triggered.'),
      '#default_value' => $theme->getSetting('popover_trigger'),
      '#options' => array_combine($options, $options),
    ];
    $form['javascript']['popovers']['options']['popover_trigger_autoclose'] = [
      '#type' => 'checkbox',
      '#title' => t('Auto-close on document click'),
      '#description' => t('Will automatically close the current popover if a click occurs anywhere else other than the popover element.'),
      '#default_value' => $theme->getSetting('popover_trigger_autoclose'),
    ];
    $form['javascript']['popovers']['options']['popover_title'] = [
      '#type' => 'textfield',
      '#title' => t('title'),
      '#description' => t("Default title value if \"title\" attribute isn't present."),
      '#default_value' => $theme->getSetting('popover_title'),
    ];
    $form['javascript']['popovers']['options']['popover_content'] = [
      '#type' => 'textfield',
      '#title' => t('content'),
      '#description' => t('Default content value if "data-content" or "data-target" attributes are not present.'),
      '#default_value' => $theme->getSetting('popover_content'),
    ];
    $form['javascript']['popovers']['options']['popover_delay'] = [
      '#type' => 'textfield',
      '#title' => t('delay'),
      '#description' => t('The amount of time to delay showing and hiding the popover (in milliseconds). Does not apply to manual trigger type.'),
      '#default_value' => $theme->getSetting('popover_delay'),
    ];
    $form['javascript']['popovers']['options']['popover_container'] = [
      '#type' => 'textfield',
      '#title' => t('container'),
      '#description' => t('Appends the popover to a specific element. Example: "body". This option is particularly useful in that it allows you to position the popover in the flow of the document near the triggering element - which will prevent the popover from floating away from the triggering element during a window resize.'),
      '#default_value' => $theme->getSetting('popover_container'),
    ];

    // Tooltips.
    $form['javascript']['tooltips'] = [
      '#type' => 'details',
      '#title' => t('Tooltips'),
      '#description' => t('Inspired by the excellent jQuery.tipsy plugin written by Jason Frame; Tooltips are an updated version, which don\'t rely on images, use CSS3 for animations, and data-attributes for local title storage. See <a href=":url" target="_blank">Bootstrap tooltips</a> for more documentation.', [
        ':url' => 'http://getbootstrap.com/javascript/#tooltips',
      ]),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['javascript']['tooltips']['tooltip_enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable tooltips'),
      '#description' => t('Elements that have the <code>data-toggle="tooltip"</code> attribute set will automatically initialize the tooltip upon page load. <strong class="error text-error">WARNING: This feature can sometimes impact performance. Disable if pages appear to "hang" after initial load.</strong>'),
      '#default_value' => $theme->getSetting('tooltip_enabled'),
    ];
    $form['javascript']['tooltips']['options'] = [
      '#type' => 'details',
      '#title' => t('Options'),
      '#description' => t('These are global options. Each tooltip can independently override desired settings by appending the option name to <code>data-</code>. Example: <code>data-animation="false"</code>.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="tooltip_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['javascript']['tooltips']['options']['tooltip_animation'] = [
      '#type' => 'checkbox',
      '#title' => t('animate'),
      '#description' => t('Apply a CSS fade transition to the tooltip.'),
      '#default_value' => $theme->getSetting('tooltip_animation'),
    ];
    $form['javascript']['tooltips']['options']['tooltip_html'] = [
      '#type' => 'checkbox',
      '#title' => t('HTML'),
      '#description' => t("Insert HTML into the tooltip. If false, jQuery's text method will be used to insert content into the DOM. Use text if you're worried about XSS attacks."),
      '#default_value' => $theme->getSetting('tooltip_html'),
    ];
    $options = [
      'top',
      'bottom',
      'left',
      'right',
      'auto',
      'auto top',
      'auto bottom',
      'auto left',
      'auto right',
    ];
    $form['javascript']['tooltips']['options']['tooltip_placement'] = [
      '#type' => 'select',
      '#title' => t('placement'),
      '#description' => t('Where to position the tooltip. When "auto" is specified, it will dynamically reorient the tooltip. For example, if placement is "auto left", the tooltip will display to the left when possible, otherwise it will display right.'),
      '#default_value' => $theme->getSetting('tooltip_placement'),
      '#options' => array_combine($options, $options),
    ];
    $form['javascript']['tooltips']['options']['tooltip_selector'] = [
      '#type' => 'textfield',
      '#title' => t('selector'),
      '#description' => t('If a selector is provided, tooltip objects will be delegated to the specified targets.'),
      '#default_value' => $theme->getSetting('tooltip_selector'),
    ];
    $options = ['click', 'hover', 'focus', 'manual'];
    $form['javascript']['tooltips']['options']['tooltip_trigger'] = [
      '#type' => 'checkboxes',
      '#title' => t('trigger'),
      '#description' => t('How a tooltip is triggered.'),
      '#default_value' => $theme->getSetting('tooltip_trigger'),
      '#options' => array_combine($options, $options),
    ];
    $form['javascript']['tooltips']['options']['tooltip_delay'] = [
      '#type' => 'textfield',
      '#title' => t('delay'),
      '#description' => t('The amount of time to delay showing and hiding the tooltip (in milliseconds). Does not apply to manual trigger type.'),
      '#default_value' => $theme->getSetting('tooltip_delay'),
    ];
    $form['javascript']['tooltips']['options']['tooltip_container'] = [
      '#type' => 'textfield',
      '#title' => t('container'),
      '#description' => t('Appends the tooltip to a specific element. Example: "body"'),
      '#default_value' => $theme->getSetting('tooltip_container'),
    ];

    // Advanced settings.
    $form['advanced'] = [
      '#type' => 'details',
      '#title' => t('Advanced'),
      '#group' => 'bootstrap',
    ];

    // BootstrapCDN.
    bootstrap_cdn_provider_settings_form($form, $form_state, $theme);
  }

}
