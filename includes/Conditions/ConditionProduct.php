<?php

namespace ConditionalAddToCart\Conditions;

class ConditionProduct extends Condition {

	public function __construct() {
		$this->slug = 'product';
		$this->name = __( 'Product', 'conditional-add-to-cart' );
		parent::__construct();
	}


	/**
	 * Return only supported operators by this condition.
	 * @return array
	 */
	public function getOperators() {
		return array_filter( parent::getOperators(), function ( $operator ) {
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
			'placeholder' => __( 'Search...', 'conditional-add-to-cart' ),
			'multiple'    => true,
			'options'     => function ( $keyword, $excludeProductIds = [], $includeProductIds = [] ) {
				$products = get_posts(
					[
						'posts_per_page' => -1,
						'post_type'   => 'product',
						's'  => $keyword,
						'post__not_in'    => $excludeProductIds,
						'post__in'    => $includeProductIds
					]
				);

				return array_map( function ( $product ) {
					return [
						'id'   => $product->ID,
						'text' => $product->post_title
											. ($product->post_type === 'product_variation'
											? ' (#' . $product->ID . ')' : '')
					];
				}, $products );
			}
		];
	}

	/**
	 * @param $operator
	 * @param $catIds
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function match( $operator, $productIds ) {
		if(empty($productIds)) return false;
		
		$product = wc_get_product();

		if(! $product){
			return false;
		}

		$the_product_id = $product->get_id();
		
		if($operator === '=='){
			return in_array($the_product_id, $productIds);
		}
		if($operator === '!='){
			return ! in_array($the_product_id, $productIds);
		}
	}


	public function isDefault() {
		return true;
	}

}
