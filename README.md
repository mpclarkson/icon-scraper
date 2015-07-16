Get Apple Touch Icons and Favicon from a Website
==========================================

Overview
--------

This library returns an array of `apple-touch-icon` and the `favicon` for a given url.


Composer
---------

Use [Composer](https://getcomposer.org) by adding the following lines in your `composer.json`:

    "require": {
        "mpclarkson/icon-Scraper": "dev-master"
    },

Usage
-----

```php

    require_once('vendor/autoload.php');

    $Scraper = new \Mpclarkson\IconScraper\Scraper();

    $icons = $Scraper->get('http://hilenium.com');

    foreach($icons as $icon) {
        $icon->getType(); //Returns favicon or apple-touch
        $icon->getHref(); //Returns a url
        $icon->getSize(); //Returns a string for apple-touch icons only (eg 72x72)
    }

```

Todos
-----

  * More tests
  * Improve code style


Credits
-----

This library is significant fork of this library by [Arthur Hoaro](https://github.com/ArthurHoaro/favicon). Key changes include:

  * Returns an array of icons, rather than just the favicon
  * Icons returned as Icon objects which include the following properties: type, href and type
  * No caching
  * PSR-4

Thanks goes to [Hilenium](http://hilenium.com).
