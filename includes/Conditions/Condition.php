<?php

namespace ConditionalAddToCart\Conditions;
class Condition {

	/**
	 * Display name
	 * @var string $name
	 */
	protected $name;

	/**
	 * Condition key
	 *
	 * @var string $slug
	 */
	protected $slug;

	/**
	 * Supported operators
	 * 
	 * @var array $operators
	 */
	protected $operators;

	/**
	 * Condition description
	 * 
	 * @var array $description
	 */
	protected $description;


	public function __construct() {
	}

	/**
	 * Return all supported operators.
	 * 
	 * @return array
	 */
	protected function getOperators() {
		return [
			'==' => __( 'Equal to', 'conditional-add-to-cart' ),
			'!=' => __( 'Not equal to', 'conditional-add-to-cart' ),
			'<='  => __( 'Less than or equal to', 'conditional-add-to-cart' ),
			'>='  => __( 'Greater than or equal to', 'conditional-add-to-cart' ),
		];
	}

	/**
	 * Return value field args.
	 * 
	 * @return array 
	 */
	public function getValueFieldArgs() {
		return [
			'type'        => 'text',
			'placeholder' => __( 'Add value', 'conditional-add-to-cart' ),
			'value'       => '',
			'options'     => []
		];
	}

	 public function match( $operator, $value ){
		return false;
	}


	/**
	 * Get $key
	 *
	 * @return  string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * Get $name
	 *
	 * @return  string
	 */
	public function getName() {
		return $this->name;
	}

	public function isDefault() {
		return false;
	}
    
    


}
