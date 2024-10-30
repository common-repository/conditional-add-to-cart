<?php

namespace ConditionalAddToCart;

use ConditionalAddToCart\Conditions\Condition;
use ConditionalAddToCart\Conditions\ConditionSessionStatus;
use Exception;
use \ConditionalAddToCart\Core\Utility\Debug;
use \ConditionalAddToCart\Core\Utility\Condition as ConditionUtility;
use \ConditionalAddToCart\Core\Utility\Settings;

class Admin {

	/**
	 * Admin instance.
	 * @var \ConditionalAddToCart\Admin $instance
	 */
	protected static $instance = null;

	/**
	 * Return admin instance.
	 * @return \ConditionalAddToCart\Admin
	 */
	public static function instance() {
		if ( static::$instance === null ) {
			static::$instance = new self;
		}

		return static::$instance;
	}

	/**
	 * Load admin.
	 */
	function run() {

		$this->define_hooks();
	}

	/**
	 * Define admin hooks.
	 */
	function define_hooks() {

		add_action( 'admin_menu', [ $this, 'addMenu' ] );
		add_action( 'admin_init', [ $this, 'renderSettings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueScripts' ] );
		add_filter('plugin_action_links_' . Plugin::instance()->getBaseName(), [$this, 'editPluginLinks']);
		add_filter( 'pre_update_option_catc_settings', [ $this, 'preUpdateSettings' ] );

		add_action( 'wp_ajax_conditions/searchOptions', [ $this, 'ajaxConditionSearchOptions' ] );
		add_action( 'wp_ajax_conditions/addCondition', [ $this, 'ajaxAddCondition' ] );
		add_action( 'wp_ajax_conditions/changeCondition', [ $this, 'ajaxChangeCondition' ] );
		add_action( 'wp_ajax_conditions/addConditionGroup', [ $this, 'ajaxAddConditionGroup' ] );
	}

	function enqueueScripts() {
		$ver = Debug::isOn() ? time() : Plugin::instance()->getVersion();

		// scripts
		wp_enqueue_script( 'catc-select2', Plugin::instance()->getUri() . 'assets/js/select2.min.js', [ 'jquery' ], '4.0.13', true );

		wp_enqueue_script( 'conditional-add-to-cart', Plugin::instance()->getUri() . 'assets/js/main.js', [ 'catc-select2' ], $ver, true );

		wp_localize_script( 'conditional-add-to-cart', 'catc_ajax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'catc' )
		] );

		// styles

		wp_enqueue_style( 'catc-select2', Plugin::instance()->getUri() . 'assets/css/select2.min.css', [], '4.0.13' );
		wp_enqueue_style( 'conditional-add-to-cart', Plugin::instance()->getUri() . 'assets/css/main.css', ['catc-select2'], $ver );
	}

	/**
	 * Add plugin settings page link to WooCommerce menu.
	 */
	function addMenu() {
		add_submenu_page(
			'woocommerce',
			'Conditional Add to Cart',
			'Conditional Add to Cart',
			'manage_options',
			'conditional-add-to-cart',
			[ $this, 'settingsPageTemplate' ]
		);
	}

	/**
	 * Echo settings page template.
	 */
	function settingsPageTemplate() {
		include( Plugin::instance()->getPath() . 'templates/settings.php' );
	}

	/**
	 * Search product categories by keyword.
	 */
	function ajaxConditionSearchOptions() {
		check_ajax_referer( 'catc', 'nonce');

		$selected      = ! empty( $_GET[ 'selected_items' ] ) ? $_GET[ 'selected_items' ] : [];
		$conditionSlug = sanitize_key( $_GET[ 'condition_slug' ] );
		$keyword       = sanitize_text_field( $_GET[ 'keyword' ] );
		$condition     = ConditionUtility::getCondition( $conditionSlug );
		$options       = $condition->getValueFieldArgs()[ 'options' ];
		$options = $options( $keyword, $selected );
		wp_send_json_success( $options );
		wp_die();
	}

	public function renderSettings() {
		register_setting( 'catc_settings', 'catc_settings' );
		// General section.
		add_settings_section(
			'catc_settings_general',
			__( 'General', 'conditional-add-to-cart' ),
			null,
			'catc_settings'
		);

		// Set background color
		add_settings_field(
			'catc_settings_enable',
			__( 'Enable', 'conditional-add-to-cart' ),
			function ( $args ) {
				?>
				<label for="catc-enable">
					<input type="checkbox" class="catc-enable" name="catc_settings[enable]" id="catc-enable"
					       value="1" <?php checked( Settings::get( 'enable' ), 1 ); ?> />
				</label>
				<?php
			},
			'catc_settings',
			'catc_settings_general'
		);

		// Set background color
		add_settings_field(
			'catc_settings_conditions',
			__( 'Conditional', 'conditional-add-to-cart' ),
			function ( $args ) {
				$settings = Settings::get();
				?>
				<p class="description" style="margin-bottom: 10px">Match one of the condition groups:</p>

				<?php
				if ( ! empty( $settings[ 'conditions' ] ) ) {
					foreach ( $settings[ 'conditions' ] as $groupId => $conditions ) {
						include Plugin::instance()->getPath() . 'templates/condition-group.php';
					}
				}
				?>
				<label for="catc-conditions">
					<button type="button" class="button button-default catcAddConditionGroup">Add "Or" group...</button>
					<span class="spinner catc-spinner"></span>
				</label>
				<?php
			},
			'catc_settings',
			'catc_settings_general'
		);
		// Set background color
		add_settings_field(
			'catc_settings_actions',
			__( 'Actions', 'conditional-add-to-cart' ),
			function ( $args ) {
				$actions = Settings::get('actions');
				?>
				<p class="description" style="margin-bottom: 10px">If conditions met:</p>
					<?php
					include Plugin::instance()->getPath() . 'templates/action.php';
					?>
				<?php
			},
			'catc_settings',
			'catc_settings_general'
		);
	}


	function ajaxAddCondition() {
		check_ajax_referer( 'catc', 'nonce');

		$groupId = filter_input( INPUT_GET, 'groupId', FILTER_SANITIZE_STRING );
		ob_start();
		include Plugin::instance()->getPath() . 'templates/condition.php';
		echo ob_get_clean();
		wp_die();
	}

	function ajaxChangeCondition() {
		check_ajax_referer( 'catc', 'nonce');

		ob_start();
		$conditionSlug  = filter_input( INPUT_GET, 'conditionSlug', FILTER_SANITIZE_STRING );
		$groupId        = filter_input( INPUT_GET, 'groupId', FILTER_SANITIZE_STRING );
		$theConditionId = filter_input( INPUT_GET, 'conditionId', FILTER_SANITIZE_STRING );
		try {
			$theCondition      = ConditionUtility::getCondition( $conditionSlug );
			$theConditionEntry = ConditionUtility::findConditionEntry( Settings::get( 'conditions' ), $groupId, $theConditionId, $theCondition );

			include Plugin::instance()->getPath() . 'templates/condition.php';
			echo ob_get_clean();
			wp_die();
		} catch ( Exception $e ) {
			wp_die( - 1 );
		}
	}

	function ajaxAddConditionGroup() {
		check_ajax_referer( 'catc', 'nonce');
		ob_start();
		include Plugin::instance()->getPath() . 'templates/condition-group.php';
		echo ob_get_clean();
		wp_die();
	}

	function preUpdateSettings( $settings ) {

		if ( isset( $settings[ 'conditions' ] ) ) {
			foreach ( $settings[ 'conditions' ] as $groupId => $conditions ) {
				$settings[ 'conditions' ][ $groupId ] = array_map( function ( $condition ) {
					if(!isset($condition['value'])){
						$condition[ 'value' ] = [];
					}
					return $condition;
				}, $conditions );
			}

		}

		if ( ! isset( $settings[ 'enable' ] ) ) {
			$settings[ 'enable' ] = 0;
		}

		return $settings;
	}

	function editPluginLinks($links){
		$settings_url = admin_url('admin.php?page=conditional-add-to-cart');
		$settings_link      = '<a href="' . $settings_url . '">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

}
