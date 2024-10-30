<?php

namespace ConditionalAddToCart\Core\Utility;

class Debug {
	public static function isOn(){
		return defined( 'WP_DEBUG' ) && WP_DEBUG ;
	}
	public static function printErrors() {
		if ( static::isOn() ) {
			ini_set( 'display_errors', 1 );
			ini_set( 'display_startup_errors', 1 );
			error_reporting( E_ALL );
		}
	}
}