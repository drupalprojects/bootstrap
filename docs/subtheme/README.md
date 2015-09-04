<!-- @file Overview of how to sub-theme the Drupal Bootstrap base theme. -->
<!-- @defgroup -->
# Sub-Theming

Below are instructions on how to create a Bootstrap sub-theme. There are several
different variations on how to accomplish this task, but this topic will focus
on the two primarily and most common ways:

1. Using the "out-of-the-box" implementation via the [jsDelivr CDN].
2. Using the [Bootstrap Framework] source and a local [LESS] preprocessor.

- [Prerequisite](#prerequisite)
- [Setup](#setup)
- [Enable](#enable)
- [File Structure](#file_structure)
- [Icons](#icons)

## Prerequisite
Read the @link getting_started Getting Started @endlink documentation topic.

### Conditional Requirements for Method 1: Bootstrap Source Files

- When using 7.x-3.0: [Bootstrap 3.x.x Source](https://github.com/twbs/bootstrap/releases)
- The Bootstrap source files are written with LESS language. You must use a **[local](https://www.google.com/search?q=less+compiler)** LESS compiler.

## Unsupported Modules
The following modules are not "officially" supported or documented by the
Drupal Bootstrap base theme. This does not mean we "dislike" them or have any
"ill will" towards them. It is more about the time, energy and effort it would
take to document "every possible scenario".

It is certainly possible that some of these modules may eventually come off this
list. That is, only if enough people actually help to contribute solid solutions
towards a common goal.

Until then, however, if you choose to use one of these modules you really do so
at your own expense. Do not expect support from this base theme or the project
you are attempting to integrate with. You are sailing into the unknown:

- Color module (in core)
- [Bootstrap API](https://www.drupal.org/project/bootstrap_api)
- [Bootstrap Library](https://www.drupal.org/project/bootstrap_library)
- [Display Suite](https://www.drupal.org/project/ds)
- [Display Suite Bootstrap Layouts](https://www.drupal.org/project/ds_bootstrap_layouts)
- [LESS module](https://drupal.org/project/less)
- [Panels](https://www.drupal.org/project/panels)
- [Panels Bootstrap Layouts](https://www.drupal.org/project/panels_bootstrap_layouts)

## Setup

Copy the starter kit sub-theme into `sites/all/themes` or a respective
`sites/*/themes` folder. You should never modify a theme or a sub-theme bundled
directly as all changes would be lost if the base theme were to be updated.
Once copied, rename the folder to something of your choosing:
`my_bootstrap_theme`. Then make sure you rename the
`bootstrap_subtheme.info.starterkit` file to match the folder name, like:
`my_bootstrap_theme.info`. Be sure to change the name and description properties
inside the file as well.

{.alert.alert-info} **IMPORTANT NOTE:** Ensure that the `.starterkit` suffix is
not added to your sub-theme's .info filename. This suffix is simply a stop-gap
measure to ensure that the bundled starter kit sub-theme cannot be enabled or
used directly. This helps people unfamiliar with Drupal avoid modifying the
starter kit sub-theme directly and forces the new sub-theme to be properly
configured.

### Bootstrap Methods
There are currently two types of supported methods for adding Bootstrap into
your sub-theme. By default, the Bootstrap base theme enables a CDN to provide
the necessary files. If this method suits you then you can skip this step.

The first method is probably the most dynamic and will grant you the ability to
change the variables and utilize the mixins provided by Bootstrap.

The second method is rather simple and utilizes CDN Bootstrap via the base
theme. It is very static and will require you to override existing styling in
your sub-theme.

Regardless of which method you choose, you will need to un-comment the
appropriate lines for your desired method in your sub-theme's .info file.

#### Method 1: Bootstrap Source Files
Download and extract the [Latest Bootstrap source](https://github.com/twbs/bootstrap/releases)
into your new sub-theme. After it has been extracted, the folder should read
`bootstrap`. If for whatever reason you have an additional bootstrap folder
wrapping the the bootstrap folder (like: bootstrap/bootstrap), remove the
wrapping bootstrap folder. You will not need to touch these files again.
This allows the framework to be updated in the future.

Copy over the bootstrap/less/variables.less to less/variables.less then fix
paths as stated in less/README.txt.

Compile the `./less/style.less` file. A new file should be generated as
`./css/style.css`.

Now, you will need to uncomment the lines under 'METHOD 1: Bootstrap Source' in
your sub-theme's .info file (pertaining to this method).

#### Method 2: [jsDelivr CDN]
This method is rather simple and the easiest. You don't have to do anything
unless you wish to override the default Bootstrap base theme settings. If so,
just un-comment the lines pertaining to Method 2.

Edit the provided `./css/style.css` file to your liking.

## Enable
Navigate to `admin/appearance` and click "Enable and set default" for your
newly created sub-theme.

## File Structure
The following paths are relative to your sub-theme's base folder. These folders
have an initial README.md files. Please read them for a more detailed
explanation of their contents.

- `./css` - Compiled sub-theme source files.
- `./less` - Sub-theme source files.
- `./templates` - Template files.

## Icons
Bootstrap comes packaged with the default [Glyphicons](http://getbootstrap.com/components/#glyphicons).
This base-theme has support for utilizing these icons via the [Icon API](https://drupal.org/project/icon).
However, given the limited capability of static sprite images, it is
recommended that you consider using an alternative solution, like
[Fontello](http://drupal.org/project/fontello) instead.

[Bootstrap Framework]: http://getbootstrap.com
[jsDelivr CDN]: http://www.jsdelivr.com
[LESS]: http://lesscss.org
