<!-- @mainpage -->
<!-- @summary Documentation landing page and topics for the http://drupal-bootstrap.org site. -->
# Drupal Bootstrap Documentation

This site is an API reference for the Drupal Bootstrap base theme, generated
from Markdown files and DOXYGEN based comments embedded in the PHP source code.

The term "bootstrap" can be used quite excessively through out this project's
documentation. For clarity, we will attempt to only use it verbosely in one of
the following ways:

- "[Drupal Bootstrap](https://www.drupal.org/project/bootstrap)"  
  Will be used when referring to the Drupal base theme project. The word
  "Drupal" should always precede it. Both words should be capitalized to
  indicate a proper name.
- "[Bootstrap Framework](http://getbootstrap.com)"  
  Will be used when referring to the external front end framework. The word
  "Framework" should always follow it. Both words should be capitalized to
  indicate a proper name.
- "[drupal_bootstrap](https://api.drupal.org/apis/drupal_bootstrap)"  
  Will be used when referring to Drupal's bootstrapping process or phase. An
  underscore (\_) should always be used to join the two terms together to
  indicate that it is a procedural term.
  
When referring to files inside the Drupal Bootstrap project directory, they
should always start with `./` and continue to specify the full path to the file
or directory inside it. For example, the file that is responsible for displaying
this text is located at `./README.md`.

When referring to files inside a Drupal Bootstrap sub-theme, they should always
start with `./example_subtheme/` and continue to specify the full path to the
file or directory inside it. For example, the main file Drupal will search for:
`./example_subtheme/template.php`.

---

Here are some topics to help you get started using and developing with Drupal
Bootstrap. They correlate with the various folders and files underneath the
`./docs` folder inside the project's root folder.

## Theme Settings
- @link settings Overview of Drupal Bootstrap Theme Settings @endlink

## Sub-themes
- @link subtheme Overview of Drupal Bootstrap Sub-Theming @endlink
  - @link subtheme_drush Drush Support @endlink
  - @link subtheme_utility Utility Functions @endlink

## APIs
- @link api Overview of Drupal Bootstrap APIs @endlink

## Theme Registry
- @link registry Overview of Drupal Bootstrap and the Theme Registry @endlink

## Project
- @link project Overview of Drupal Bootstrap Project Development @endlink
  - @link project_grunt Grunt Tasks @endlink
  - @link project_releases Releases @endlink
