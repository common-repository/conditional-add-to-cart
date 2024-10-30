<?php

namespace ConditionalAddToCart\Conditions;

use ConditionalAddToCart\Core\Utility\Plugin;
use ConditionalAddToCart\Plugin as ConditionalAddToCartPlugin;

class ConditionCartQuantity extends Condition {

	public function __construct() {
		$this->slug = 'cart_quantity';
		$this->name = __( 'Cart quantity', 'conditional-add-to-cart' );
		parent::__construct();
	}

	public function getOperators() {
		return array_filter( parent::getOperators(), function ($operator) {
			return in_array( $operator, [ '!=', '==', '>=', '<=' ] );
		}, ARRAY_FILTER_USE_KEY );
	}

		/**
	 * Return value field args of this condition.
	 * @return array
	 */
	public function getValueFieldArgs() {
		return [
      'type'        => 'number',
			'placeholder' => ''
		];
	}

	public function match( $operator, $value) {
		$cartQuantity = \WC()->cart->get_cart_contents_count();
		$value =(int) $value;
		switch($operator){
			case '==':
				return $cartQuantity === $value;
			case '!=':
				return $cartQuantity !== $value;
			case '<=':
			return $cartQuantity <= $value;
			case '>=':
			return $cartQuantity >= $value;
			default:
			return false;
		}
}

}
