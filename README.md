PHP-library to use when mining web. Fetch URLs using curl library and extract data using regular expressions templates.

# Example 1. Extract data from php.net

Create fetcher object with patterns map and just get() needed url. Let's get all links with titles from php.net:

```
namespace webminer;
require 'fetcher.php';

$f = new fetcher([
  'pattern_map' => [
    # a pattern will collect all links on php.net webpage with urls and titles
    'link_titles' => '/<a[^>]*href="(?<url>[^ ]+)"[^>]*>(?<title>[^<]+)<\/a>/misu'
  ]
]);

$res = $f->get('http://php.net/');
print_r($res['data']);
```

Will output:
```
Array
(
    [link_titles] => Array
        (
            [0] => Array
                (
                    [url] => /downloads
                    [title] => Downloads
                )

            [1] => Array
                (
                    [url] => /docs.php
                    [title] => Documentation
                )

...
```

# Example 2. Extract data from Amazon product listing

Using subpatterns we can easily extract data from nested structures:

```
<?php

namespace webminer;
require __DIR__ . '/../fetcher.php';

$f = new fetcher([
	'pattern_map' => [
		'product_listing' => [
			'iterate' => '/(?<content><div data-asin="[^"]+".+?)<\/div><\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div><\/div>/misu',

			'title' => [
				'any' => [
					'/<h5 class="a-color-base s-line-clamp-4">\s*<a class="a-link-normal a-text-normal" href="(?<url>[^"]+)">\s*<span class="a-size-base-plus a-color-base a-text-normal">(?<title>[^<]+)<\/span>/misu',
					'/<a class="a-link-normal a-text-normal" href="(?<url>[^"]+)">\s*<span class="a-size-[^ ]+ a-color-base a-text-normal">(?<title>[^<]+)<\/span>\s*<\/a>/misu'
				]
			],

			'price' => '/<span class="a-price"[^>]+><span[^>]+>(?<price>[^<]+)<\/span>/misu',
		]
	]
]);

$res = $f->get('https://www.amazon.com/s?i=electronics-intl-ship&bbn=16225009011&rh=n%3A16225009011%2Cn%3A541966&s=review-rank&dc&_encoding=UTF8&qid=1552473333&refresh=1&ref=sr_st_review-rank');
print_r($res['data']);
```

Will output:
```
Array
(
    [product_listing] => Array
        (
            [0] => Array
                (
                    [title] => Array
                        (
                            [0] => Array
                                (
                                    [url] => /Charger-Certified-Lightning-Charging-Compatible/dp/B07PN8VKRJ/ref=sr_1_1?_encoding=UTF8&amp;qid=1553440494&amp;refresh=1&amp;s=electronics&amp;sr=1-1
                                    [title] => iPhone Fast Charger, MFi Certified Lightning Cable 5 Pack [3 FT] Extra Long Nylon Braided USB Charging &amp; Syncing Cord Compatible with iPhone Xs/Max/XR/X/8/8Plus/7/7 Plus/6S/6S Plus/iPad Silver&amp;White
                                )

                        )

                    [price] => Array
                        (
                            [0] => Array
                                (
                                    [price] => $12.99
                                )

                        )

                )
...
```
