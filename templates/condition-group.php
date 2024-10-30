<?php
/**
 * Condition group template
 *
 * @link       http://conditionaladdtocart.com
 * @since      1.0.0
 *
 * @package    ConditionalAddToCart
 * @subpackage ConditionalAddToCart/templates
 */

use  \ConditionalAddToCart\Core\Utility\Condition as ConditionUtility;
use \ConditionalAddToCart\Plugin;

/**
 * @var string Group entry ID.
 */
$groupId = ! empty( $groupId ) ? $groupId : uniqid( 'group_' );
?>
<div class="catcConditionGroup" data-group-id="<?php echo $groupId ?>">
	<?php
	$conditions = ! empty( $conditions ) ? $conditions : [];

	if ( ! empty( $conditions ) ) {
		foreach ( $conditions as $theConditionId => $theConditionEntry ) {
			$theCondition                 = ConditionUtility::getCondition( $theConditionEntry[ 'condition_slug' ] );
			include	Plugin::instance()->getPath() . 'templates/condition.php';
		}
	} else {
		include	Plugin::instance()->getPath() . 'templates/condition.php';
	}
	?>
    <div style="margin:6px 0;">or</div>
</div>
