<!-- @file Instructions on how to sub-theme the Drupal Bootstrap base theme. -->
<!-- @defgroup sub_theming -->
# Sub-Theming

If you haven't already installed the Drupal Bootstrap theme, read 
the @link getting_started Getting Started @endlink topic. Below 
are instructions on how to create a [Drupal Bootstrap] based sub-theme.
There are several different variations on how to accomplish this task, but this
topic will focus on the two primarily and most common ways.

{.alert.alert-warning} **Warning** You should never modify any theme or sub-theme that 
is packaged and released from Drupal.org, such as Drupal Bootstrap. If you 
do, all changes you have made will be lost once that theme is updated. Instead, 
you should create a subtheme from one of the provided starterkits (this is 
considered a best practice). Once you've done that, you can override CSS, 
templates, and theme processing.

#### Choose a Starterkit {#starterkit}

- @link sub_theming_cdn CDN Starterkit @endlink - uses the "out-of-the-box"
  CSS and JavaScript files served by the [jsDelivr CDN].
- @link sub_theming_less LESS Starterkit @endlink - uses the [Bootstrap Framework]
  [LESS] source files and a local [LESS] preprocessor.

Once you've selected one of the above starterkits, here's how to install it:

1. Copy over one of the starterkits you have chosen from the
   `./bootstrap/starterkits` directory into the `themes` directory.
2. Rename the directory to a unique machine readable name. This will be your
   sub-theme's "name". For this example and future examples we'll use `subtheme`.
3. Rename `./subtheme/THEMENAME.starterkit.yml` to match the directory name and
   append `.info.yml` (e.g. `./subtheme/subtheme.info.yml`).
4. Open `./subtheme/subtheme.info.yml` and change the name, description and any
   other properties to suite your needs. Make sure to change the library name:
   `- THEMENAME/globalstyling` to `- subtheme/globalstyling` as well.
5. Rename the sub-theme configuration files, located at: `./config/install/THEMENAME.settings.yml`
   and `./config/schema/THEMENAME.schema.yml` to match the `subtheme` name (e.g.
   `subtheme.settings.yml` and `subtheme.schema.yml`).
6. Open `./config/schema/subtheme.schema.yml` and change `- THEMENAME.settings:`
   to `- subtheme.settings:` and label: `'THEMETITLE settings'` to
   `'Subtheme settings'`

{.alert.alert-warning} **WARNING:** Ensure that the `.starterkit` suffix is
not present on your sub-theme's `.info.yml` filename. This suffix is simply a stop
gap measure to ensure that the bundled starter kit sub-theme cannot be enabled
or used directly. This helps people unfamiliar with Drupal avoid modifying the
starter kit sub-theme directly and instead forces them to create a new sub-theme
to modify.

#### Enable Your New Sub-theme {#enable}
In your Drupal site, navigate to `admin/appearance` and click the `Enable and
set default` link next to your newly created sub-theme. Now that you've enabled
your starterkit, please refer to the starterkit's documentation page to customize.


[Drupal Bootstrap]: https://www.drupal.org/project/bootstrap
[Bootstrap Framework]: http://getbootstrap.com
[jsDelivr CDN]: http://www.jsdelivr.com
[LESS]: http://lesscss.org
