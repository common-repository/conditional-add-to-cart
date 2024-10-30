<?php

use \ConditionalAddToCart\Core\Utility\Condition as ConditionUtility;

/**
 * @var \ConditionalAddToCart\Conditions\Condition Current or default condition.
 */
$theCondition = isset( $theCondition ) ? $theCondition : ConditionUtility::getDefaultCondition();

/**
 * @var string $theConditionId Condition entry ID.
 */
$theConditionId = isset( $theConditionId ) ? $theConditionId : uniqid( 'condition_' );
?>
<div
	style="display: flex; align-items:center; margin-top:5px"
	data-condition-id="<?php echo $theConditionId ?>" class="catcCondition">
	<!-- Condition -->
	<div>
		<select name="catc_settings[conditions][<?php echo $groupId ?>][<?php echo $theConditionId ?>][condition_slug]"
		        class="catcConditionSelect">
			<?php foreach ( ConditionUtility::getSupportedConditions() as $condition ) :
				?>
				<option value="<?php echo $condition->getSlug() ?>"
					<?php
					echo ( isset( $theCondition ) && $theCondition->getSlug() === $condition->getSlug() ) ?'selected' : '';
					?>
				>
					<?php echo $condition->getName(); ?></option>
			<?php endforeach; ?>
		</select>

	</div>
	<!-- Operator -->
	<div>
		<select name="catc_settings[conditions][<?php echo $groupId ?>][<?php echo $theConditionId ?>][operator]">
			<?php
			foreach ( $theCondition->getOperators() as $operator => $name ) :
				?>
				<option value="<?php echo $operator ?>"

					<?php
					if ( ! empty( $theConditionEntry ) && $theConditionEntry[ 'operator' ] === $operator ) {
						echo 'selected';
					}
					?>
				><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>

	</div>
	<!-- Value -->
	<?php
	$args = $theCondition->getValueFieldArgs();
	switch ( $args[ 'type' ] ):

		case 'number':?>
		<div >
			<input type="number" step="0.01" name="catc_settings[conditions][<?php echo $groupId ?>][<?php echo $theConditionId ?>][value]" 
			<?php if( ! empty( $theConditionEntry )){
				echo 'value="' . $theConditionEntry['value'].'"';
			} ?>
			>
			</div>
			<?php
		break;
		case 'select':
			?>
			<select name="catc_settings[conditions][<?php echo $groupId ?>][<?php echo $theConditionId ?>][value]">
				<?php foreach ( $args[ 'options' ] as $value => $name ) : ?>
					<option value="<?php echo $value ?>"
						<?php
						if ( ! empty( $theConditionEntry ) && $theConditionEntry[ 'value' ] == $value ) {
							echo 'selected';
						}
						?>
					><?php echo $name ?></option>
				<?php endforeach; ?>
			</select>
			<?php
			break;
		case 'search':
			?>
			<div style="max-width: 400px">
				<select
					multiple
					class="catcConditionSearch"
					name="catc_settings[conditions][<?php echo $groupId ?>][<?php echo $theConditionId ?>][value][]"
					data-condition-slug="<?php echo $theCondition->getSlug() ?>"
					placeholder="<?php echo $args[ 'placeholder' ] ?>">
					<?php
					if ( empty( $theConditionEntry[ 'value' ] ) ) {
						$options = [];
					} else {
						$options = $theCondition->getValueFieldArgs()[ 'options' ];
						$options = $options( null, [], $theConditionEntry[ 'value' ] );
					}
					foreach ( $options as $option ):
						?>
						<option selected value="<?php echo $option[ 'id' ] ?>"><?php echo $option[ 'text' ] ?></option>
					<?php
					endforeach;
					?>
				</select>
			</div>
			<?php
			break;
	endswitch;
	?>
	<!-- Add -->
	<div>
		<button type="button" class="button button-default hurryt-mr-5 catcAddCondition">And</button>

	</div>
	<!-- Delete -->
	<div>
		<button type="button" class="catcDeleteCondition">
			<svg
				xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 357 357">
				<polygon
					points="357,35.7 321.3,0 178.5,142.8 35.7,0 0,35.7 142.8,178.5 0,321.3 35.7,357 178.5,214.2 321.3,357 357,321.3 214.2,178.5"/>
			</svg>
		</button>
	</div>
</div>
