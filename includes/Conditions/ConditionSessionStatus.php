<?php

namespace ConditionalAddToCart\Conditions;

use ConditionalAddToCart\Core\Utility\Plugin;
use ConditionalAddToCart\Plugin as ConditionalAddToCartPlugin;

class ConditionSessionStatus extends Condition {

	public function __construct() {
		$this->slug = 'session_status';
		$this->name = __( 'User Login Status', 'conditional-add-to-cart' );
		parent::__construct();
	}

	public function getOperators() {
		return array_filter( parent::getOperators(), function ($operator) {
			return in_array( $operator, [ '!=', '==' ] );
		}, ARRAY_FILTER_USE_KEY );
	}

	public function getValueFieldArgs() {
		return [
			'type'        => 'select',
			'placeholder' => __( '', 'conditional-add-to-cart' ),
			'options'     => [
				'logged_in'  => __( 'Logged in', 'conditional-add-to-cart' ),
				'logged_out' => __( 'Logged out', 'conditional-add-to-cart' ),
			]
		];
	}

	public function match( $operator, $value) {
		$match = ($value === 'logged_in' && is_user_logged_in())
							|| ($value === 'logged_out' && !is_user_logged_in());
			if($operator === '=='){
				return $match;
			}elseif($operator === '!='){
				return !$match;
			}

			return false;
	}

	public function isDefault() {
		return false;
	}


}
