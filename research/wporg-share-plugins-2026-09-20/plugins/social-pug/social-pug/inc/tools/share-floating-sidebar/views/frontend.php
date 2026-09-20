<?php

use Mediavine\Grow\Tools\Floating_Sidebar;

?>
<aside id="dpsp-floating-sidebar" aria-label="social sharing sidebar" class="<?php echo esc_attr( $args['wrapper_classes'] ); ?>" data-trigger-scroll="<?php echo esc_attr( $args['scroll_trigger'] ); ?>">
	<?php echo Floating_Sidebar::compose_buttons( $args['settings'] ); ?>
</aside>
