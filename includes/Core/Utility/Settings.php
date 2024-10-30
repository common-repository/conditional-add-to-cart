<?php

namespace ConditionalAddToCart\Core\Utility;

class Settings {

	/**
	 * @var string OPTION_NAME
	 */
	const OPTION_NAME = 'catc_settings';

	/**
	 * Return a single or all settings.
	 *
	 * @param string|null $name
	 *
	 * @return array|mixed
	 */
	public static function get( $name = null ) {
		if ( is_null( $name ) ) {
			return self::getAll();
		}

		if(strpos($name, '.') !== false){
			return static::findNested($name);
		}

		return self::find( $name );
	}

	/**
	 * Find a single setting.
	 *
	 * @param string $name
	 *
	 * @return null|mixed
	 */
	public static function find( $name ) {
		$settings = self::getAll();

		return array_key_exists( $name, $settings ) ? $settings[ $name ] : null;
	}

	public static function findNested($nestedNames){
		$nestedNames = explode('.', $nestedNames);
		return array_reduce($nestedNames, function($accum, $name) {
			if(is_null($accum)){
				return static::get($name);
			}
			return isset($accum[$name]) ? $accum[$name]: null;
		}, null);
	}


	/**
	 * Return all settings.
	 * @return array
	 */
	public static function getAll() {
		$defaults = [
			'enable'     => 1,
			'conditions' => [],
			'actions'    => [
				'truthy' => [
					'key'   => 'hide',
					'value' => [
						'replace' => '',
						'customize'    => ''
					]
				]
			]
		];

		return wp_parse_args( get_option( self::OPTION_NAME, [] ), $defaults );
	}

	/**
	 * Clear all settings.
	 */
	public static function clearAll() {
		delete_option( self::OPTION_NAME );
	}
}