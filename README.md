PHP-library to use when mining web. Fetch URLs using curl library and extract data using regular expressions templates.

# How to use

Create fetcher object with patterns map and just get() needed url. Let's get all links with titles from php.net:

```
namespace webminer;
require 'fetcher.php';

$f = new fetcher([
  'pattern_map' => ['link_titles' => '/<a[^>]*href="(?<url>[^ ]+)"[^>]*>(?<title>[^<]+)<\/a>/misu']
]);

$res = $f->get('http://php.net/');
print_r($res['data']);
```
