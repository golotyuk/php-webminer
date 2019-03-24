<?php

# Parse HTML or other text formats based on regex map with named groups:
# $map = [
#   'pattern_name_1' => 'pattern',
#   'pattern_name_2' => [
#     'parent' => 'pattern',
#     'child' => 'pattern'
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

	private function parse_patterns($html, $patterns) {
		$found = [];

		if ( is_array($patterns) ) {

			if ( $patterns['iterate'] ) {
				$parent = $this->parse_by_pattern($html, $patterns['iterate']);
				unset($patterns['iterate']);

				foreach ( $parent as $item ) {
					$found[] = $this->parse_patterns( $item['content'], $patterns );
				}
				
				return $found;
			}

			foreach ( $patterns as $key => $pattern ) {
				if ( $key == 'any' ) {
					foreach ( $pattern as $sub_pattern ) {
						$r = $this->parse_patterns($html, $sub_pattern);
						if ( $r ) {
							$found = $r;
							break;
						}
					}
				}
				else if ( $key == 'all' ) {
					foreach ( $pattern as $sub_pattern ) {
						$r = $this->parse_patterns($html, $sub_pattern);
						if ( $r ) {
							foreach ( $r as $row ) $found[] = $row;
						}
					}
				}
				else {
					$r = $this->parse_patterns($html, $pattern);

					if ( $r ) {
						$found[$key] = $r;
					}
				}
			}
		}
		else {
			$pattern = $patterns;
			$found = $this->parse_by_pattern($html, $pattern);
		}

		return $found;
	}

	public function parse($html) {
		$found = $this->parse_patterns($html, $this->map);
		return $found;
	}
}
