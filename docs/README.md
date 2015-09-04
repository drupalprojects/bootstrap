<!-- @file Documentation landing page and topics for the http://drupal-bootstrap.org site. -->
<!-- @mainpage -->
# Drupal Bootstrap Documentation

{.lead} The official documentation site for the [Drupal Bootstrap] base theme

The majority of this site is automatically generated from source files
located through out the project's repository. Topics are extracted from Markdown
files and the rest is extracted from embedded PHP comments.

---

## Topics

Below are some topics to help get you started using the [Drupal Bootstrap] base
theme. They are ordered solely on quickest implementation and ease of use.

#### @link getting_started Getting Started @endlink

#### @link faq FAQ @endlink

#### @link settings Theme Settings @endlink

#### @link subtheme Sub-Theming @endlink
- @link subtheme_drush Drush Support @endlink
- @link subtheme_utility Utility Functions @endlink

#### @link api APIs @endlink

#### @link registry Theme Registry @endlink

#### @link project Project Development @endlink
- @link project_grunt Grunt Tasks @endlink
- @link project_releases Releases @endlink

---

## Terminology

The term **"bootstrap"** can be used excessively through out this project's
documentation. For clarity, we will attempt to use it verbosely in one of the
following ways:

- **[Drupal Bootstrap]** refers to the Drupal base theme project.
- **[Bootstrap Framework](http://getbootstrap.com)** refers to the external
  front end framework.
- **[drupal_bootstrap](https://api.drupal.org/apis/drupal_bootstrap)** refers
  to Drupal's bootstrapping process or phase.
  
When referring to files inside the [Drupal Bootstrap] project directory, they
will always start with `./` and continue to specify the full path to the file
or directory inside it. For example, the file that is responsible for displaying
this text is located at `./README.md`.

When referring to files inside a sub-theme, they will always start with
`./example_subtheme/` and continue to specify the full path to the file or
directory inside it. For example, the main file Drupal will search for:
`./example_subtheme/template.php`.

[Drupal Bootstrap]: https://www.drupal.org/project/bootstrap
