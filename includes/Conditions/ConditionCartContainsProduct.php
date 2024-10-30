<?php

namespace ConditionalAddToCart\Conditions;

use ConditionalAddToCart\Core\Utility\Plugin;
use ConditionalAddToCart\Plugin as ConditionalAddToCartPlugin;
use WC;
class ConditionCartContainsProduct extends Condition {

	public function __construct() {
		$this->slug = 'cart_contains_product';
		$this->name = __( 'Cart contains product', 'conditional-add-to-cart' );
		parent::__construct();
	}

	public function getOperators() {
		return array_filter( parent::getOperators(), function ($operator) {
			return in_array( $operator, [ '!=', '==' ] );
		}, ARRAY_FILTER_USE_KEY );
	}

		/**
	 * Return value field args of this condition.
	 * @return array
	 */
	public function getValueFieldArgs() {
		return [
			'type'        => 'search',
			'placeholder' => __( 'Search for a product...', 'conditional-add-to-cart' ),
			'multiple'    => true,
			'options'     => function ( $keyword, $exclude = [], $include = [] ) {
				$products = get_posts(
					[
						'posts_per_page'=>   -1,
						'post_type'   =>     'product',
						'search' => 				 $keyword,
						'post__not_in'    => $exclude,
						'post__in'    => 		 $include
					]
				);

				return array_map( function ( $product ) {
					return [
						'id'   => $product->ID,
						'text' => $product->post_title,
					];
				}, $products );
			}
		];
	}

	public function match( $operator, $productIds) {
			$productCartIds = [];
			if(! wc_get_product()){
				return false;
			}
			foreach(WC()->cart->get_cart() as $cartItem){
				$product = $cartItem['data'];
				if($product && method_exists($product, 'get_id')){
					$productCartIds[] = $product->get_id();
				}
			}
			$matchedIds         = array_intersect( (array) array_map('intval', $productIds), $productCartIds );
			$match = in_array(wc_get_product()->get_id(), $matchedIds);

			if ( '==' === $operator ) {
				return ! empty( $match );
			} elseif ( '!=' === $operator ) {
				return empty( $match );
			}
		return false;		
}

}
