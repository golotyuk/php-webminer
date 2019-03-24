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
