<?php

namespace webminer;
require __DIR__ . '/../fetcher.php';

$f = new fetcher([
  'pattern_map' => [
    # a pattern will collect all links on php.net webpage with urls and titles
    'link_titles' => '/<a[^>]*href="(?<url>[^ ]+)"[^>]*>(?<title>[^<]+)<\/a>/misu'
  ]
]);

$res = $f->get('http://php.net/');
print_r($res['data']);
