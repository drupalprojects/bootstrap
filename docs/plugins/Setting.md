<!-- @file Documentation for the @BootstrapSetting annotated discovery plugin. -->
<!-- @defgroup -->
<!-- @ingroup -->
# @BootstrapSetting

- [Create a plugin](#create)
- [Customize a plugin](#customize)
- [Rebuild the cache](#rebuild)
- [Helpful Tips](#helpful-tips)

## Create a plugin {#create}

We will use `SkipLink` as our first `@BootstrapSetting` plugin to create. In
this example we want our sub-theme to specify a different skip link anchor id
to change in the Theme Settings interface altering the default of
`#main-content`.

Replace all of the following instances of `THEMENAME` with the actual machine
name of your sub-theme.

Create a file at `./THEMENAME/src/Plugin/Setting/THEMENAME/Accessibility/SkipLink.php`
with the following contents:

```php
namespace Drupal\THEMENAME\Plugin\Setting\THEMENAME\Accessibility\SkipLink;

use Drupal\bootstrap\Annotation\BootstrapSetting;
use Drupal\bootstrap\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "THEMENAME_skip_link" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @BootstrapSetting(
 *   id = "THEMENAME_skip_link",
 *   type = "textfield",
 *   title = @Translation("Anchor ID for the ""skip link"""),
 *   defaultValue = "main-content",
 *   description = @Translation("Specify the HTML ID of the element that the accessible-but-hidden ""skip link"" should link to. (<a href="":link"">Read more about skip links</a>.)", arguments = { ":link"  = "http://drupal.org/node/467976" }),
 *   groups = {
 *     "THEMENAME" = "THEMETITLE",
 *     "accessibility" = @Translation("Accessibility"),
 *   },
 * )
 */
class SkipLink extends SettingBase {}
```

Helpfully Bootstrap adds a global "theme" variable added to every template
in `Bootstrap::preprocess()`.

This variable can now simply be called in the `html.html.twig` file with the
following contents:

```twig
<a href="#{{ theme.settings.THEMENAME_skip_link }}" class="visually-hidden focusable skip-link">
  {{ 'Skip to main content'|t }}
</a>
```

In addition, the `page.html.twig` file will also need to be adjusted for this to
work properly with the new anchor id.

```twig
<a id="{{ theme.settings.THEMENAME_skip_link }}"></a>
```

## Customize a plugin {#customize}

Now that we covered how to create a basic `@BootstrapSetting` plugin, we can
discuss how to customize a setting to fulfill a range of requirements.

The `@BootstrapSetting` is implemented through the base class `SettingBase.php`
which provides a variety of public methods to assist in the customization of
a plugin.

## SettingBase::alterForm

The `alterForm` public method provides a way for you to alter the form state of
the `@BootstrapSetting` configuration.

For example the `CdnProvider::alterForm()` provides functionality to
automatically create groupings for the different cdn providers as well as
provides helpful introductory text.

Another example leveraging the `RegionWells::alterForm()` is how the
`BootstrapSetting` plugin provides configuration for specifying a class to
apply to a Region Well. It also creates dynamic well settings for every
defined region to really allow for fine grained customization.

## SettingBase::drupalSettings

The `drupalSettings` public method provides a way for you to determine whether
a theme setting should be added to `drupalSettings`. Please note that by default
this is set to false to prevent leaked information from being exposed.

## SettingBase::getCacheTags

The `getCacheTags` public method provides a way for you to add cache tags that
when the instantiated class is modified the associated cache tags will be
invalidated. This is incredibly useful for example for
`CdnCustomCss::getCacheTags()` which returns an an array of `library_info`. So
when a `CdnCustomCss` instantiated plugin changes the `library_info` cache tag
will be invalidated automatically.

```php
public function getCacheTags() {
  return ['library_info'];
}
```

## SettingBase::getElement

The `getElement` public method provides a way for you to retrieve the form
element for a particular `@BootstrapSetting`. This function while similar
to the `SettingBase::alterForm` is much more practical to use when a form needs
to be more then slightly altered.

## SettingBase::getGroup

The `getGroup` public method provides a way for you to get a group that the
`@BootstrapSetting` belongs to. You can also set properties based on the
returned group such as which group should be open by default.

## SettingBase::getGroups

The `getGroups` public method simply provides a way for you to list all of the
groups that the `@BootstrapSetting` belongs to.

## SettingBase::submitForm

The `submitForm` public method provides a way for you customize the form output
based on the actual values provided to the form. For instance the
`RegionWells::submitForm` method will extract all regions with individual
dynamic settings by checking if `/^region_well-/` exists in any of the values.

## SettingBase::validateForm

The `validateForm` public method provides a way for you to validate a
form. This can be based on the values submitted or a variety of other
conditions. This method could, for instance, be useful when a custom cdn
provider were to be added. The `validateForm` could check that the new cdn
provider is the correct version and location.

## Rebuild the cache {#rebuild}

Once you have saved, you must rebuild your cache for this new plugin to be
discovered. This must happen anytime you make a change to the actual file name
or the information inside the `@BootstrapSetting` annotation.

To rebuild your cache, navigate to `admin/config/development/performance` and
click the `Clear all caches` button. Or if you prefer, run `drush cr` from the
command line.

Voilà! After this, you should have a fully functional `@BootstrapSetting` plugin!

## Helpful tips {#helpful-tips}

A helpful primer on Annotation-based plugins can be found at:
https://www.drupal.org/node/1882526
