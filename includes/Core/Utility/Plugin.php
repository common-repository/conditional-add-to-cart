<?php

namespace ConditionalAddToCart\Core\Utility;
class Plugin {
	public static function getNamespace() {
		return explode( '\\', __NAMESPACE__ )[ 0 ];
	}

}