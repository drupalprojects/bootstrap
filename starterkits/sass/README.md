<!-- @file Instructions for subtheming using the Sass Starterkit. -->
<!-- @defgroup sub_theming_sass -->
<!-- @ingroup sub_theming -->
# Sass Starterkit

This is a port of the [Drupal Bootstrap project] D8 LESS starterkit.

Below are instructions on how to create a Bootstrap sub-theme using a Sass
preprocessor.

- [Prerequisites](#prerequisites)
- [Additional Setup](#setup)
- [Override Styles](#styles)
- [Override Settings](#settings)
- [Override Templates and Theme Functions](#registry)

## Prerequisites
- Read the @link getting_started Getting Started @endlink documentation topic.
- You must understand the basic concept of using the [Sass] CSS pre-processor.
- You must use a **[local Sass compiler](https://www.google.com/search?q=sass+compiler)**.
- You must use the [Bootstrap Framework Sass Port Source Files] ending in the `.scss`
  extension, not files ending in `.css`.

## Additional Setup {#setup}
Download and extract the **latest** 3.x.x version of
[Bootstrap Framework Sass Port Source Files] into the root of your new sub-theme. After
it has been extracted, copy the `assets` directory to a new directory named
`./bootstrap`.  You can use the script `get-boostrap.sh` for this.

The contents of this new directory should be:
* `fonts`
* `images`
* `javascripts`
* `stylesheets`

{.alert.alert-warning} **WARNING:** Do not modify the files inside of
`./bootstrap` directly. Doing so may cause issues when upgrading the
[Bootstrap Framework] in the future.

## Override Styles {#styles}
The `./sass/_variable-overrides.scss` file is generally where you will
the majority of your time overriding the variables provided by the [Bootstrap
Framework].

The `./sass/overrides.scss` file contains various Drupal overrides to
properly integrate with the [Bootstrap Framework]. It may contain a few
enhancements, feel free to edit this file as you see fit.

The `./scss/style.scss` file is the glue that combines the
`bootstrap.scss` and `overrides.scss` files together. Generally, you will not
need to modify this file unless you need to add or remove files to be imported.
This is the file that you should compile to `./css/styles.css` (note
the same file name, using a different extension of course).

## Override Theme Settings {#settings}
Please refer to the @link theme_settings Sub-theme Settings @endlink topic.

## Override Templates and Theme Functions {#registry}
Please refer to the @link registry Theme Registry @endlink topic.

[Bootstrap Framework]: http://getbootstrap.com
[Bootstrap Framework Sass Port Source Files]: https://github.com/twbs/bootstrap-sass
[Sass]: http://sass-lang.com
[Drupal Bootstrap project]: https://www.drupal.org/project/bootstrap
