<!-- @file Instructions on how to sub-theme the Drupal Bootstrap base theme. -->
<!-- @defgroup -->
# Sub-Theming

You should never modify any theme or sub-theme that is packaged and released from Drupal.org, such as Drupal Bootstrap. If you do, all changes you have made will be lost once that theme is updated. Instead, you should create a subtheme from one of the provided starterkits (this is considered a best practice). Once you've done that, you can override CSS, templates, and theme processing. 

## Which starterkit should I use? 

* If you're new to Drupal theming the CDN starterkit is a simple way to create a subtheme. The CDN starterkit uses a Content Delivery Network to provide the Bootstrap framework for your site. You can then add and override CSS and templates.
* For more advance users who prefer to use a CSS preprocessor, the LESS starterkit may be a better choice. 

The basic steps for creating a subtheme from one of the starterkits follows. For this example, let's assume you'll name your subtheme `mybootstrap`:

1. Copy one of the starterkits from the `./starterkits` directory to the themes directory and rename the directory to `mybootstrap`
2. Rename the following files: `THEMENAME.theme` to `mybootstrap.theme`, `THEMENAME.libraries.yml` to `mybootstrap.libraries.yml`, and `THEMENAME.starterkit.yml` to `mybootstrap.info.yml`
3. Edit `mybootstrap.info.yml` file and change '- THEMENAME/globalstyling' to '- mybootstrap/globalstyling'. You can also change the theme name and description.

You can now enable your new subtheme under Admin/Appearances. 

For more information on customizing your newly create subtheme, refer to the @link subtheme_cdn CDN Starterkit @endlink or @link subtheme_less LESS Starterkit @endlink documentation.
