<div class="catcActionsWrapper">

    <select name="catc_settings[actions][truthy][key]" class="catcActions">
        <option value="">Do nothing</option>
        <option value="hide" <?php
		if ( ! empty( $actions ) && $actions[ 'truthy' ][ 'key' ] === 'hide' ) {
			echo 'selected';
		} ?>>Hide Add to Cart
        </option>
        <option value="customize" <?php
		if ( ! empty( $actions ) && $actions[ 'truthy' ][ 'key' ] === 'customize' ) {
			echo 'selected';
		} ?>>Change text...
        </option>
        <option value="replace" <?php
		if ( ! empty( $actions ) && $actions[ 'truthy' ][ 'key' ] === 'replace' ) {
			echo 'selected';
		} ?>>Replace with...</option>

    </select>
    <div style="margin-top:5px">
        <input name="catc_settings[actions][truthy][value][customize]" type="text" style="width:300px" data-bind-to="customize"
			<?php
			if ( ! empty( $actions ) ) {
				echo 'value="' .  $actions[ 'truthy' ][ 'value' ]['customize'] . '"';
			}
			?>
			<?php
			if ( empty( $actions ) || $actions[ 'truthy' ][ 'key' ] !== 'customize' ) {
				echo 'class="hidden"';
			}
			?>
               placeholder="Add text...">
        <div data-bind-to="replace" <?php
        if ( empty( $actions ) || $actions[ 'truthy' ][ 'key' ] !== 'replace' ) {
	        echo 'class="hidden"';
        }
        ?>>
			<?php
			$args = [
				'textarea_name' => 'catc_settings[actions][truthy][value][replace]',
				'media_buttons' => false,
				'teeny'         => true,
				'textarea_rows' => 5
			];
			wp_editor( ( ! empty( $actions ) ? $actions[ 'truthy' ][ 'value' ]['replace'] : '' ), uniqid( 'custom_' ), $args );
			?>
			<p class="description">You can insert shortcode in the editor.</p>
        </div>
    </div>
</div>
