<?php

namespace ConditionalAddToCart;

use ConditionalAddToCart\Core\Utility\Condition as ConditionUtility;
use ConditionalAddToCart\Core\Utility\Hook;
use ConditionalAddToCart\Core\Utility\Settings;
use WP_Query;

class Front {

	/**
	 *
	 * Front instance.
	 *
	 * @var \ConditionalAddToCart\Front $instance
	 */
	protected static $instance = null;

	/**
	 * Return front instance.
	 * @return \ConditionalAddToCart\Front
	 */
	public static function instance() {
		if ( static::$instance === null ) {
			static::$instance = new Front;
		}

		return static::$instance;
	}

	/**
	 * Front logic.
	 */
	function run() {

		$this->defineHooks();
	}

	function is_frontend_ajax() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return false;
		}

		$referer = wp_get_referer();

		if ( ! $referer ) {
			return false;
		}

		return strpos( $referer, '/wp-admin/' ) === false;
	}

	function is_frontend() {

		// Allow frontend ajax requests.
		if ( $this->is_frontend_ajax() ) {
			return true;
		}


		// Disallow dashboard.
		if ( is_admin() ) {
			return false;
		}

		return true;
	}

	function defineHooks() {

		// check if woocommerce is active
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		if ( ! $this->is_frontend() ) {
			return;
		}
		if ( ! (bool) Settings::get( 'enable' ) ) {
			return;
		}

		add_action( 'woocommerce_before_shop_loop_item', [ $this, 'applyConditions' ] );

		add_action( 'wp', [ $this, 'applyConditions' ] );
		add_action('template_redirect', [$this, 'maybe_capture_buffer']);
		add_filter('catc_buffer', [$this, 'parse_html']);
	}

	function parse_html($html){
		$dom = new \DOMDocument();
		@$dom->loadHTML($html);
		$xpath = new \DOMXPath($dom);
		// TODO: Add support for "Read more"
		$nodes = $xpath->query('//a[contains(@class,"add_to_cart_button")]');

		$products = [];
		foreach ($nodes as $node) {
			$product_id = $node->getAttribute('data-product_id');
			if( ! empty($product_id) ){
				$products[$product_id]  = $node;
			}
		}

		if( empty($products)){
			return $html;
		}
		$args = array(
			'post_type' => 'product',
			'post__in' => array_keys($products),
			'posts_per_page' => -1
		);

		$loop = new WP_Query( $args );
        if ($loop->have_posts()) {
			$action = Settings::get( 'actions.truthy.key' );
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$match = ConditionUtility::match();
				if($match){
					$product_id = get_the_ID();
					if( empty($products[$product_id])){
						continue;
					}
					$node = $products[$product_id];
					if($action === 'hide'){
						$node->parentNode->removeChild($node);
					}elseif($action === 'customize'){
						$next_txt =$this->changeAddToCartButtonText();
						$node->nodeValue = $next_txt;
					}elseif($action === 'replace'){
						$replacement =  $this->replaceAddToCartButton();
						$replacementDOM =  new \DOMDocument();
						@$replacementDOM->loadHTML($replacement);
						$replaceNode = $dom->importNode($replacementDOM->getElementsByTagName('body')->item(0), true);
						$node->parentNode->replaceChild($replaceNode, $node);
					}
				}
			}

        }

		wp_reset_postdata();

		return $dom->saveHTML();
	}
	function maybe_capture_buffer(){
		
		ob_start([$this, 'maybe_process_buffer']);
		
	}

	function maybe_process_buffer($buffer){
		if(!$buffer) return $buffer;
		return apply_filters('catc_buffer', $buffer);
	}


	function applyConditions() {

	
		if(current_action() === 'wp' && !is_singular('product')){
			return;
		}
		$match = ConditionUtility::match();

		if ( ! $match ) {
			remove_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'changeAddToCartButtonText' ] );
			remove_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'changeAddToCartButtonText' ] );
			Hook::restoreCallback( [
				'woocommerce_template_loop_add_to_cart',
				'woocommerce_template_single_add_to_cart'
			] );

			return;
		}

		$product = wc_get_product();

		if(!$product){
			return;
		}

		$btn_selectors = [
			'.add_to_cart_button[data-product_id="{product_id}"]',
			'.single_add_to_cart_button[value="{product_id}"]',
			'form.cart[data-product_id="{product_id}"] .single_add_to_cart_button',
			'#product-{product_id} form.cart .single_add_to_cart_button',
			'.product.post-{product_id} form.cart .single_add_to_cart_button',
			'a[class*="product_type_"][data-product_id="{product_id}"]',
			'.storefront-sticky-add-to-cart__content-button'
		];

		$btn_wrapper_selectors = [
			'.product.post-{product_id} form.cart',
			'#product-{product_id} form.cart',
			'form.cart[data-product_id="{product_id}"]',
		];

		$btn_selectors         = (array) apply_filters( 'ccart_selectors', $btn_selectors );
		$btn_wrapper_selectors = (array) apply_filters( 'ccart_wrapper_selectors', $btn_wrapper_selectors );

		foreach ( $btn_selectors as $i => $selector ) {
			// Replace in the array placeholders {product_id} with actual product id.
			$btn_selectors[ $i ] = str_replace( '{product_id}', $product->get_id(), $selector );
		}
		// Replace in the array placeholders {product_id} with actual product id.
		foreach ( $btn_wrapper_selectors as $i => $selector ) {
			$btn_wrapper_selectors[ $i ] = str_replace( '{product_id}', $product->get_id(), $selector );
		}

		$actionKey = Settings::get( 'actions.truthy.key' );

		switch ( $actionKey ) {
			case 'hide':
				Hook::replaceCallback( [
					'woocommerce_template_loop_add_to_cart',
					'woocommerce_template_single_add_to_cart'
				] );

				$selectors = array_merge( $btn_selectors, $btn_wrapper_selectors );
				?>
				<style>
					<?php echo implode(',', $selectors); ?>
					{
						display: none !important;
					}
				</style>

				<?php

				// Remove the button when DOM is ready
				?>
				<script>
					document.addEventListener( 'DOMContentLoaded', function () {
						var selectors = <?php echo json_encode( $selectors ) ?>;
						for ( var i = 0; i < selectors.length; i++ ) {
							var _element = document.querySelector( selectors[ i ] )
							if ( _element ) {
								_element.remove();
							}
						}
					} );
				</script>
				<?php

				break;
			case 'customize':
				// product single
				add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'changeAddToCartButtonText' ] );
				// loop
				add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'changeAddToCartButtonText' ] );

				$new_text = $this->changeAddToCartButtonText();
				?>
				<style id="__<?php echo $product->get_id() ?>-css">
					<?php echo implode(',', $btn_selectors); ?>
					{
						display: none !important
					;
					}
				</style>
				<script>
					document.addEventListener( 'DOMContentLoaded', function () {
						var btn_selectors = <?php echo json_encode( $btn_selectors ); ?>;
						for ( var i = 0; i < btn_selectors.length; i++ ) {
							var _element = document.querySelector( btn_selectors[ i ] )
							if ( _element ) {
								_element.textContent = '<?php echo $new_text ?>';
							}
						}
						document.getElementById( '__<?php echo $product->get_id() ?>-css' ).remove();

					} );
				</script>
				<?php
				break;
			case 'replace':

				Hook::replaceCallback( [
					'woocommerce_template_loop_add_to_cart',
					'woocommerce_template_single_add_to_cart'
				], function(){
					echo $this->replaceAddToCartButton();
				} );

				
				$replacement = $this->replaceAddToCartButton();
				$selectors   = array_merge( $btn_selectors, $btn_wrapper_selectors );
				?>
				<style id="__<?php echo $product->get_id() ?>-css">
					<?php echo implode(',', $btn_selectors); ?>
					{
						display: none !important
					;
					}
				</style>
				<script>
					document.addEventListener( 'DOMContentLoaded', function () {
						var html = '<?php echo $replacement ?>';

						var btn_selectors = <?php echo json_encode( $selectors ); ?>;
						for ( var i = 0; i < btn_selectors.length; i++ ) {
							var _element = document.querySelector( btn_selectors[ i ] );
							if ( _element ) {
								// if jquery is defined
								if ( typeof jQuery !== 'undefined' ) {
									jQuery( _element ).replaceWith( html );
								} else {
									_element.outerHTML = html;
								}
							}
						}
						document.getElementById( '__<?php echo $product->get_id() ?>-css' ).remove();

					} );
				</script>
				<?php

				break;
		}
	}

	function changeAddToCartButtonText() {
		$actions = Settings::get( 'actions.truthy.value' );

		return __( $actions[ 'customize' ], 'conditional-add-to-cart' );
	}

	function replaceAddToCartButton() {
		$actions = Settings::get( 'actions.truthy.value' );
		return do_shortcode( $actions[ 'replace' ] );
	}
}
