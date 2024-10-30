<?php

namespace ConditionalAddToCart\Exceptions;

use Exception;

class ClassNotFoundException extends Exception {

	public function __construct( $class = "", $code = 0 ) {
		parent::__construct( sprintf( 'Class not found: %s', $class ), $code );
	}
}