<?php

namespace ConditionalAddToCart\Core\Utility;
class CaseConverter {

	/**
	 * Convert `snake_case` to `PascalCase`.
	 *
	 * @param string $snakeCase
	 *
	 * @return string
	 */
	public static function convertSnakeCaseToPascalCase( $snakeCase ) {
		return preg_replace_callback( "/(?:^|_)([a-z])/", function ( $matches ) {
			return strtoupper( $matches[ 1 ] );
		}, $snakeCase );
	}
}