<?php

namespace ConditionalAddToCart\Core\Utility;

use \ConditionalAddToCart\Core\Utility\Plugin as PluginUtility;
use Exception;

class Condition {

	/**
	 * Return all available conditions class files.
	 *
	 * @return array|false
	 */
	public static function getAllConditionsClassFiles() {
		return glob( dirname(dirname( __DIR__)) . '/Conditions/*.php' );
	}


	public static function getSupportedConditions() {
		$files = array_filter( static::getAllConditionsClassFiles(), function ( $file ) {
			// Exclude base condition.
			return strpos( $file, 'Condition.php' ) === false;
		} );
		return array_map( function ( $file ) {
			$file  = pathinfo( $file, PATHINFO_FILENAME );
			$class = PluginUtility::getNamespace() . '\Conditions\\' . $file;

			return new $class;
		}, $files );
	}

	/**
	 * Returns default condition instance.
	 * @return null|\ConditionalAddToCart\Conditions\Condition
	 */
	public static function getDefaultCondition() {

		return Arr::findCallback( static::getSupportedConditions(), function ( $condition ) {
			/**
			 * @var \ConditionalAddToCart\Conditions\Condition $condition
			 */
			return $condition->isDefault();
		} );

	}

	/**
	 * Returns a given condition instance.
	 *
	 * @param $conditionSlug
	 *
	 * @return \ConditionalAddToCart\Conditions\Condition
	 * @throws Exception
	 */
	public static function getCondition( $conditionSlug ) {
		$class = CaseConverter::convertSnakeCaseToPascalCase( $conditionSlug );
		$class = PluginUtility::getNamespace() . '\Conditions\Condition' . $class;

		if ( ! class_exists( $class ) ) {
			throw new Exception( __( sprintf( 'Condition not found: %s', $conditionSlug ), 'conditional-add-to-cart' ) );
		}

		return new $class;
	}

	/**
	 * @param array $entries
	 * @param string $groupId
	 * @param string $conditionId
	 * @param \ConditionalAddToCart\Conditions\Condition $condition
	 *
	 * @return mixed|null
	 */
	public static function findConditionEntry( $entries, $groupId, $conditionId, $condition ) {
		if ( ! isset( $entries[ $groupId ][ $conditionId ] ) ) {
			return null;
		}
		$entry = $entries[ $groupId ][ $conditionId ];
		if ( $entry[ 'condition_slug' ] !== $condition->getSlug() ) {
			return null;
		}

		return $entry;
	}

	/**
	 *
	 * @param mixed $value
	 * @param \ConditionalAddToCart\Conditions\Condition $condition
	 *
	 * @return array
	 */
	public static function prepareSearchFieldValue( $value, $condition ) {
		if ( $condition->getValueFieldArgs()[ 'type' ] === 'search'
		     && is_array( $value )
		     && is_callable( $callback = $condition->getValueFieldArgs()[ 'options' ] ) ) {
			$items = $callback( '', [], $value );

			return array_map( function ( $item ) {
				$item[ 'value' ] = $item[ 'id' ];
			}, $items );
		}

		return $value;
	}

	public static function parseSearchValueField( $value ) {
		$arr = json_decode( $value, true );
		if ( $arr === null ) {
			return $value;
		}
		try {
			return array_map( function ( $item ) {
				return intval( $item[ 'id' ] );
			}, $arr );
		} catch ( Exception $e ) {
			return $value;
		}
	}

	public static function match() {
		$groups = Settings::get( 'conditions' );
		$matches = [];
		foreach ( $groups as $groupId => $group ) {
			foreach ( $group as $condition ) {
				$conditionObject       = static::getCondition( $condition[ 'condition_slug' ] );
				$matches[ $groupId ][] = $conditionObject->match( $condition[ 'operator' ], $condition[ 'value' ] );
			}
		}
		foreach ( $matches as $groupMatch ) {
			// At least one group must match?
			if ( count( $groupMatch ) === count( array_filter( $groupMatch ) ) ) {
				return true;
			}
		}

		return false;
	}

	

}
