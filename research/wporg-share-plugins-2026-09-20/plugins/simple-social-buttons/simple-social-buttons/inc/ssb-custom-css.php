<?php
/**
 * Inline custom CSS for icon spacing.
 *
 * WPCS debt: legacy CSS template with inline echo blocks; full refactor deferred.
 *
 * @package SimpleSocialButtons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! isset( $this->selected_position['inline'] ) && ! isset( $this->selected_position['sidebar'] ) ) {
	return;
}

$ssb_inline_icon_margin = '';
if (
	isset( $this->inline_option['icon_space'], $this->inline_option['icon_space_value'] )
	&& '1' === $this->inline_option['icon_space']
	&& '' !== $this->inline_option['icon_space_value']
) {
	$ssb_inline_icon_margin = esc_attr( absint( $this->inline_option['icon_space_value'] ) ) . 'px';
}

$ssb_sidebar_icon_margin = '';
if (
	isset( $this->sidebar_option['icon_space'], $this->sidebar_option['icon_space_value'] )
	&& '1' === $this->sidebar_option['icon_space']
	&& '' !== $this->sidebar_option['icon_space_value']
) {
	$ssb_sidebar_icon_margin = esc_attr( absint( $this->sidebar_option['icon_space_value'] ) ) . 'px 0';
}
?>
<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Legacy CSS template; refactor deferred. ?>
<style media="screen">

	<?php
	if ( isset( $this->selected_position['inline'] ) && isset( $this->inline_option['icon_space'] ) ) :
		?>
	.simplesocialbuttons.simplesocialbuttons_inline .ssb-fb-like,
	.simplesocialbuttons.simplesocialbuttons_inline amp-facebook-like {
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}
	<?php endif ?>
	/*inline margin*/
	<?php
	if ( 'sm-round' === $this->selected_theme && isset( $this->selected_position['inline'] ) ) :
		?>
	.simplesocialbuttons.simplesocialbuttons_inline.simplesocial-sm-round button{
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'simple-round' === $this->selected_theme && isset( $this->selected_position['inline'] ) ) :
		?>
	.simplesocialbuttons.simplesocialbuttons_inline.simplesocial-simple-round button{
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'round-txt' === $this->selected_theme && isset( $this->selected_position['inline'] ) ) :
		?>
	.simplesocialbuttons.simplesocialbuttons_inline.simplesocial-round-txt button{
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'round-btm-border' === $this->selected_theme && isset( $this->selected_position['inline'] ) ) :
		?>
	.simplesocialbuttons.simplesocialbuttons_inline.simplesocial-round-btm-border button{
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'flat-button-border' === $this->selected_theme && isset( $this->selected_position['inline'] ) ) :
		?>
	.simplesocialbuttons.simplesocialbuttons_inline.simplesocial-flat-button-border button{
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'round-icon' === $this->selected_theme && isset( $this->selected_position['inline'] ) ) :
		?>
	.simplesocialbuttons.simplesocialbuttons_inline.simplesocial-round-icon button{
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}

	<?php endif ?>

		<?php
		if ( 'simple-icons' === $this->selected_theme && isset( $this->selected_position['inline'] ) && isset( $this->inline_option['icon_space'] ) ) :
			?>
	.simplesocialbuttons.simplesocialbuttons_inline.simplesocial-simple-icons button{
		margin: <?php echo $ssb_inline_icon_margin; ?>;
	}

		<?php endif ?>
	/*margin-digbar*/

	<?php
	if ( 'sm-round' === $this->selected_theme && isset( $this->selected_position['sidebar'] ) ) :
		?>
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-sm-round button{
		margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'simple-round' === $this->selected_theme && isset( $this->selected_position['sidebar'] ) ) :
		?>
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-simple-round button{
		margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'round-txt' === $this->selected_theme && isset( $this->selected_position['sidebar'] ) ) :
		?>
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-round-txt button{
	margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'round-btm-border' === $this->selected_theme && isset( $this->selected_position['sidebar'] ) ) :
		?>
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-round-btm-border button{
		margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'round-icon' === $this->selected_theme && isset( $this->selected_position['sidebar'] ) ) :
		?>
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-round-icon button{
	margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( 'simple-icons' === $this->selected_theme && isset( $this->selected_position['sidebar'] ) ) :
		?>
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-simple-icons button{
		margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-simple-icons .ssb-fb-like,
	div[class*="simplesocialbuttons-float"].simplesocialbuttons.simplesocial-simple-icons amp-facebook-like{
		margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	<?php endif ?>

	<?php
	if ( isset( $this->selected_position['sidebar'] ) && '1' === $this->sidebar_option['icon_space'] ) :
		?>
	div[class*="simplesocialbuttons-float"].simplesocialbuttons .ssb-fb-like,
	div[class*="simplesocialbuttons-float"].simplesocialbuttons amp-facebook-like{
		margin: <?php echo $ssb_sidebar_icon_margin; ?>;
	}
	<?php endif ?>

</style>
<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
