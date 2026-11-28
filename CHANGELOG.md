# Changelog

## 3.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.1 or newer. Older versions of Magento and PHP 7.1 to 8.0 are no longer
  supported; use version 2.x for these.
- Requires `infrangible/m2-foundation` 3.0, `fewedev/base` 2.4, `magento/module-backend`, `magento/module-page-cache`
  and `magento/zend-cache`; the module used `fewedev/base` and these Magento modules before without requiring them.
- The controller of the form accepts only POST requests. Before, a GET request with the URL as parameter (and the
  secret key of the admin URL) cleaned the cache as well; now it gets "404 Not Found".
- All classes have native types and use constructor property promotion; the constructors of the plugin, the
  controller and the block changed.

### Fixed

- Spaces before or after the URL, for example from copying it, made the cleaning fail without notice: the form
  reported "The url was cleaned.", but the page stayed in the cache. The URL is now trimmed.

### Changed

- The block checks the permission with the authorization of Magento's backend block instead of an own one.
- The template escapes with Magento's escaper instead of the deprecated escape methods of the block.
- The German texts speak of emptying the cache of the URL instead of removing the URL.
- The module is loaded after the modules it uses.
- Checks with PHPStan level 9, the Magento coding standard and unit tests on every push.

### Added

- Translations into 24 languages.
- Unit tests.
- README with the usage, the matching of the URL and the requirements.
