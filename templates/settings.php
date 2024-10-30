<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://conditionaladdtocart.com
 * @since      1.0.0
 *
 * @package    Conditional_Add_To_Cart
 * @subpackage Conditional_Add_To_Cart/templates
 */
?>
<div class="wrap">
    <h1><?php echo _e( 'Conditional Add to Cart', 'conditional-add-to-cart' ); ?></h1>
    <form method="post" action="options.php">
		<?php
		settings_fields( 'catc_settings' );
		do_settings_sections( 'catc_settings' );
		submit_button();
		?>
    </form>
</div>