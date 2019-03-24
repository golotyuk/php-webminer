<?php

# Get html, parse content based on regular expressions and return structured results
# $f = new fetcher( [
#	'ua_file' => 'ua.txt', # name of the file with user agents (each on a separate line)
#	'proxies' => ['1.1.1.1:123'], # array of proxies in host:port format
#	'resolve' => ['domain' => ['1.1.1.1', '2.2.2.2']], # custom ips to resolve domain into
#	'pattern_map' => [ 'test' => '/w+/misu' ], # patterns map (see parser.php)
# ] )
#

namespace webminer;
require 'parser.php';

class fetcher {
	protected $user_agents = [];
	protected $proxies = [];
	protected $resolve = [];
	protected $pattern_map = [];

	public function __construct( $params ) {
		$this->user_agents = explode("\n", file_get_contents($params['ua_file'] ? : __DIR__ . '/ua.desktop.gen.txt'));
		if ( $params['proxies'] ) $this->proxies = $params['proxies'];
		if ( $params['resolve'] ) $this->resolve = $params['resolve'];

		if ( $params['pattern_map'] ) $this->pattern_map = $params['pattern_map'];
		else throw new exception('At least one pattern should be specified in config ');
	}

	protected function get_user_agent() {
		return $this->user_agents[array_rand($this->user_agents)];
	}

	protected function get_proxy() {
		return $this->proxies ? $this->proxies[ array_rand($this->proxies) ] : null;
	}

	private function get_resolving_ips($url) {
		if ( !$this->resolve ) return;

		$host = parse_url($url, PHP_URL_HOST);
		$port = parse_url($url, PHP_URL_SCHEME) == 'https' ? '443' : '80';

		if ( !$ips = $this->resolve[ $host ] ) return;
		
		$ip = $ips[array_rand($ips)];
		return "{$host}:{$port}:{$ip}";
	}

	public function get( $url ) {
		$c = curl_init( $url );
		
		$options = [
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_ENCODING => ''
		];

		if ( $proxy = $this->get_proxy() ) $options[CURLOPT_PROXY] = $proxy;

		if ( $resolve = $this->get_resolving_ips( $url ) ) $options[CURLOPT_RESOLVE] = [$resolve];

		if ( $ua = $this->get_user_agent() ) $options[CURLOPT_USERAGENT] = $ua;

		curl_setopt_array($c, $options);
		$html = curl_exec($c);
		$request = curl_getinfo($c);
		
		$request['proxy'] = $proxy;
		$request['resolve'] = $resolve;
		$request['user_agent'] = $ua;

		curl_close($c);

		$parser = new parser( $this->pattern_map );
		$data = $parser->parse( $html );

		$response = [
			'source' => $html,
			'request' => $request,
			'data' => $data
		];

		return $response;
	}
}
