<!-- @file Frequently Asked Questions -->
<!-- @defgroup -->
# FAQ - Frequently Asked Questions

## Internet Explorer Compatibility
The [Bootstrap Framework] does not officially support older Internet Explorer
[compatibility modes]. To ensure you are using the latest rendering mode for IE,
consider installing the [HTML5 Tools](https://drupal.org/project/html5_tools)
module.

Internet Explorer 8 requires the use of [Respond.js] to enable media queries
(Responsive Web Design). However, [Respond.js] does not work with CSS that is
referenced via a CSS `@import` statement, which is the default way Drupal
adds CSS files to a page when CSS aggregation is disabled. To ensure
[Respond.js] works properly, enable CSS aggregation at the bottom of:
`admin/config/development/performance`.

[Bootstrap Framework]: http://getbootstrap.com
[Respond.js]: https://github.com/scottjehl/Respond
[compatibility modes]: http://getbootstrap.com/getting-started/#support-ie-compatibility-modes
