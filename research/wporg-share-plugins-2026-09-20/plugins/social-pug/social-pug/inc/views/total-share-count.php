<?php
use Mediavine\Grow\Critical_Styles;
?>

<div class="dpsp-total-share-wrapper" <?php echo Critical_Styles::get( 'total-share-wrapper', $args['location'] ); ?>>
	<span class="dpsp-icon-total-share" <?php echo Critical_Styles::get( 'total-share-icon', $args['location'] ); ?>><?php echo $args['icon']; ?></span>
	<span class="dpsp-total-share-count"><?php echo esc_html( $args['count'] ); ?></span>
	<span><?php echo esc_html( $args['text'] ); ?></span>
</div>
