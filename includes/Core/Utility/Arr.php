<?php

namespace ConditionalAddToCart\Core\Utility;

class Arr {

	public static function findCallback( array $array, callable $callback ) {
		$result = array_filter( $array, $callback );

		return empty( $result ) ? null : array_shift( $result );
	}
}