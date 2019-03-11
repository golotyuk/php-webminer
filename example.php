<?php

namespace webminer;
require 'fetcher.php';

$f = new fetcher([
	'pattern_map' => ['link_titles' => '/<a[^>]*href="(?<url>[^ ]+)"[^>]*>(?<title>[^<]+)<\/a>/misu']
]);

$res = $f->get('http://php.net/');
print_r($res['data']);
