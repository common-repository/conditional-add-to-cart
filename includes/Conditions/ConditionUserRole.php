<?php

namespace ConditionalAddToCart\Conditions;

use ConditionalAddToCart\Core\Utility\Plugin;
use ConditionalAddToCart\Plugin as ConditionalAddToCartPlugin;

class ConditionUserRole extends Condition {

	public function __construct() {
		$this->slug = 'user_role';
		$this->name = __( 'User Role', 'conditional-add-to-cart' );
		parent::__construct();
	}

	public function getOperators() {
		return array_filter( parent::getOperators(), function ( $operator ) {
			return in_array( $operator, [ '!=', '==' ] );
		}, ARRAY_FILTER_USE_KEY );
	}

	public function getValueFieldArgs() {
		return [
			'type'        => 'select',
			'placeholder' => __( '', 'conditional-add-to-cart' ),
			'options'     => wp_roles()->get_names()
		];
	}

	public function match( $operator, $value ) {
		$user = wp_get_current_user();
		if($operator === '=='){
			return in_array( $value, (array) $user->roles );
		}
		elseif($operator === '!='){
			return !in_array( $value, (array) $user->roles );
		}

		return false;
	}

	public function isDefault() {
		return false;
	}


}
