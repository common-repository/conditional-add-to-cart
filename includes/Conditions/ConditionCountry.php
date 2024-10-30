<?php

namespace ConditionalAddToCart\Conditions;

use ConditionalAddToCart\Core\Utility\Plugin;
use ConditionalAddToCart\Plugin as ConditionalAddToCartPlugin;

class ConditionCountry extends Condition {

	public function __construct() {
		$this->slug = 'country';
		$this->name = __( 'User country', 'conditional-add-to-cart' );
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
    $countries_obj   = new \WC_Countries();
    $countries   = $countries_obj->__get('countries');
		return [
      'type'        => 'select',
			'placeholder' => __( 'Select country', 'conditional-add-to-cart' ),
			'multiple'    => true,
			'options'     => $countries
		];
	}

	public function match( $operator, $country) {
    $location = \WC_Geolocation::geolocate_ip();
    if($operator === '=='){
			return strtolower($location['country']) === strtolower($country);		
		}elseif($operator === '!='){
			return strtolower($location['country']) !== strtolower($country);	
		}
		return false;
}

}
