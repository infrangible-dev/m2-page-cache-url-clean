# Infrangible Page Cache Url Clean

Removes single pages from Magento's built-in full page cache, by their URL, instead of flushing the whole cache.

## Usage

The page **System > Tools > Cache Management** gets the form **Page Cache Url Clean**. Enter the URL of a page as it
is opened in the store, for example `https://www.example.com/joust-duffle-bag.html`, and click **Clean url**. The next
request of the URL renders the page again. Spaces before and after the URL are ignored.

The URL has to match exactly, with protocol, domain and query: `https://www.example.com/joust-duffle-bag.html?color=red`
is a page of its own in the cache and is cleaned with this URL.

The form is shown to admin users with the permission **System > Tools > Cache Management > Page Cache Clean Url**.

## Behavior

- With the built-in full page cache enabled, the module adds a tag with the URL (its SHA-1 hash) to the cache tags
  of every page of the store; cleaning removes the pages with this tag.
- With Varnish, the module does nothing; the form has no effect there.
- There are no settings.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.1 or newer

## Installation

```bash
composer require infrangible/m2-page-cache-url-clean
bin/magento module:enable Infrangible_PageCacheUrlClean
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy`.

## License

Infrangible Page Cache Url Clean is licensed under the MIT License - see the LICENSE file for details.
