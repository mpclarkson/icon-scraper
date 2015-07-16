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

    $scraper = new \Mpclarkson\IconScraper\Scraper();

    $icons = $scraper->get('http://hilenium.com');

    foreach($icons as $icon) {
        $icon->getType(); //Returns favicon or apple-touch-icon
        $icon->getHref(); //Returns a url
        $icon->getSize(); //Returns a string for apple-touch icons only (eg 72x72)
    }

    var_dum($icons)

    Array
    (
        [0] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => favicon
                [href:Mpclarkson\IconScraper\Icon:private] => http://d1nhhppd50p5r.cloudfront.net/favicon.ico?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] =>
            )

        [1] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => https://d1nhhppd50p5r.cloudfront.net/icon57.png?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] => 57x57
            )

        [2] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => http://d1nhhppd50p5r.cloudfront.net/icon72.png?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] => 72x72
            )

        [3] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => https://d1nhhppd50p5r.cloudfront.net/icon76.png?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] => 76x76
            )

        [4] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => https://d1nhhppd50p5r.cloudfront.net/icon114.png?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] => 114x114
            )

        [5] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => https://d1nhhppd50p5r.cloudfront.net/icon120.png?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] => 120x120
            )

        [6] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => https://d1nhhppd50p5r.cloudfront.net/icon144.png?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] => 144x144
            )

        [7] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => http://d1nhhppd50p5r.cloudfront.net/icon152.png?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] => 152x152
            )

        [8] => Mpclarkson\IconScraper\Icon Object
            (
                [type:Mpclarkson\IconScraper\Icon:private] => apple-touch-icon
                [href:Mpclarkson\IconScraper\Icon:private] => http://d1nhhppd50p5r.cloudfront.net/css/96113be.css?1433897130
                [size:Mpclarkson\IconScraper\Icon:private] =>
            )

    )

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
