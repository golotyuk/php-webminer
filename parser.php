<?php

# Parse HTML or other text formats based on regex map with named groups:
# $map = [
#   'pattern_name' => 'pattern',
#	'pattern_name' => [
#	  'parent' => 'pattern',
#	  'child' => 'pattern'
#   ]
# ]

namespace webminer;

class parser {
	private $map = [];

	public function __construct($map) {
		$this->map = $map;
	}

	public function parse_by_pattern($html, $pattern) {
		preg_match_all($pattern, $html, $matches);

		foreach ( $matches as $name => $lines ) if ( !is_numeric($name) ) {
			foreach ( $lines as $i => $value ) {
				$found[$i][$name] = $value;
			}
		}

		return $found;
	}

	public function parse($html) {
		$found = [];

		foreach ( $this->map as $type => $pattern ) {
			if ( is_array($pattern) && $pattern['parent'] && $pattern['child'] ) {
				$parent_matches = null;
				preg_match_all($pattern['parent'], $html, $parent_matches);

				foreach ( $parent_matches as $k => $lines ) if ( $k > 0 ) {
					foreach ( $lines as $value ) {
						$found[$type] = $this->parse_by_pattern($html, $pattern['child']);
					}
				}
			}
			else
			{
				$found[$type] = $this->parse_by_pattern($html, $pattern);
			}
		}

		return $found;
	}
}