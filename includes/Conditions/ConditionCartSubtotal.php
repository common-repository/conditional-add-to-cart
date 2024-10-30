<?php

namespace ConditionalAddToCart\Conditions;


class ConditionCartSubtotal extends Condition {

	public function __construct() {
		$this->slug = 'cart_subtotal';
		$this->name = __( 'Cart subtotal', 'conditional-add-to-cart' );
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
		$cart_total = (float)\WC()->cart->subtotal;
		$value = (float)$value;
		switch($operator){
			case '==':
				return $cart_total == $value;
			case '!=':
				return $cart_total != $value;
			case '<=':
			return $cart_total <= $value;
			case '>=':
			return $cart_total >= $value;
			default:
			return false;
		}
}

}
