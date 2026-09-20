<?php
/**
 * SSB Widget front-end markup template.
 *
 * @package SimpleSocialButtons
 * @since   7.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $widget_title ) ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget before/after title HTML from core.
	echo $before_title . esc_html( $widget_title ) . $after_title;
}
?>

<section class="ssb_followers simplesocial-simple-round">

	<?php if ( $display === $show_facebook ) : ?>
	<a class="ssb_button simplesocial-fb-follow" rel="noopener" href="https://facebook.com/<?php echo esc_attr( $facebook_id ); ?>" target="_blank">
		<span class="simplesocialtxt"><?php echo esc_html( $facebook_text ); ?> </span>
		<span class="widget_counter"> <?php echo ( $display === $facebook_show_counter ) ? esc_html( $fb_likes ) : ''; ?> </span>
	</a>
<?php endif; ?>

<?php if ( $display === $show_twitter ) : ?>
	<a class="ssb_button simplesocial-twt-follow" rel="noopener" href="https://twitter.com/<?php echo esc_attr( $twitter_id ); ?>" target="_blank">
		<span class="simplesocialtxt"><?php echo esc_html( $twitter_text ); ?> </span>
		<span class="widget_counter"> <?php echo ( $display === $twitter_show_counter ) ? esc_html( $twitter_follower ) : ''; ?> </span>
	</a>
<?php endif; ?>

<?php if ( $display === $show_google_plus ) : ?>
	<a class="ssb_button simplesocial-gplus-follow" rel="noopener" href="https://plus.google.com/<?php echo esc_attr( $google_id ); ?>" target="_blank">
		<span class="simplesocialtxt"><?php echo esc_html( $google_text ); ?> </span>
		<span class="widget_counter"> <?php echo ( $display === $google_show_counter ) ? esc_html( $google_follower ) : ''; ?> </span>
	</a>
<?php endif; ?>

<?php if ( $display === $show_youtube ) : ?>
	<a
		class="ssb_button simplesocial-yt-follow"
		rel="noopener"
		href="https://youtube.com/<?php echo esc_attr( $youtube_type ); ?>/<?php echo esc_attr( $youtube_id ); ?>"
		target="_blank"
	>
		<span class="simplesocialtxt"><?php echo esc_html( $youtube_text ); ?> </span>
		<span class="widget_counter"> <?php echo ( $display === $youtube_show_counter ) ? esc_html( $youtube_subscriber ) : ' '; ?> </span>
	</a>
<?php endif; ?>

<?php if ( $display === $show_pinterest ) : ?>
	<a class="ssb_button simplesocial-pinterest-follow" rel="noopener" href="https://pinterest.com/<?php echo esc_attr( $pinterest_id ); ?>" target="_blank">
		<span class="simplesocialtxt"><?php echo esc_html( $pinterest_text ); ?> </span>
		<span class="widget_counter"> <?php echo ( $display === $pinterest_show_counter ) ? esc_html( $pinterest_follower ) : ''; ?> </span>
	</a>
<?php endif; ?>

<?php if ( $display === $show_instagram ) : ?>
	<a class="ssb_button simplesocial-instagram-follow" rel="noopener" href="https://www.instagram.com/<?php echo esc_attr( $instagram_id ); ?>" target="_blank">
		<span class="simplesocialtxt"><?php echo esc_html( $instagram_text ); ?> </span>
		<span class="widget_counter"> <?php echo ( $display === $instagram_show_counter ) ? esc_html( $instagram_follower ) : ''; ?> </span>
	</a>
<?php endif; ?>

<?php if ( $display === $show_whatsapp ) : ?>
	<a class="ssb_button simplesocial-whatsapp-follow" rel="noopener" href="https://api.whatsapp.com/send?phone=<?php echo intval( $whatsapp ); ?>" target="_blank">
		<span class="simplesocialtxt"><?php echo esc_html( $whatsapp_text ); ?> </span>
	</a>
<?php endif; ?>


</section>
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget after HTML from core.
echo $after_widget;

