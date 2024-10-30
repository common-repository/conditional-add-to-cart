<?php

namespace ConditionalAddToCart\Conditions;

class ConditionCategory extends Condition {

	public function __construct() {
		$this->slug = 'category';
		$this->name = __( 'Product Category', 'conditional-add-to-cart' );
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
			'options'     => function ( $keyword, $excludeCatIds = [], $includeCatIds = [] ) {
				$terms = get_terms(
					[
						'taxonomy'   => 'product_cat',
						'hide_empty' => false,
						'name_like'  => $keyword,
						'exclude'    => $excludeCatIds,
						'include'    => $includeCatIds
					]
				);

				return array_map( function ( $cat ) {
					return [
						'id'   => $cat->term_id,
						'text' => $cat->name,
					];
				}, $terms );
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
	public function match( $operator, $catIds ) {
		if(empty($catIds)) return false;

		$product = wc_get_product();
		$itMatches = false;
		
		if ( ! $product ) {
			return $itMatches;
		}
		$currentCatIds = wc_get_product_cat_ids( $product->get_id() );
		$match         = array_intersect( (array) array_map('intval', $catIds), $currentCatIds );

		if ( '==' === $operator ) {
			$itMatches = ! empty( $match );

		} elseif ( '!=' === $operator ) {
			$itMatches = empty( $match );
		}

		return $itMatches;
	}


	public function isDefault() {
		return true;
	}

}
