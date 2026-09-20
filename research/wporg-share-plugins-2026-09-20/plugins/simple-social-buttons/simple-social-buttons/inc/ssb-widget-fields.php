<?php
/**
 * SSB Widget Fields.
 *
 * @package SimpleSocialButtons
 * @since   7.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
		<?php esc_attr_e( 'Title:', 'simple-social-buttons' ); ?>
	</label>
	<input
		class="widefat"
		id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
		name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
		type="text"
		value="<?php echo esc_attr( $title ); ?>"
	>
</p>
<div class="simpleshare-checklist">
	<input
		type="checkbox"
		value="1"
		class="show_fb_check"
		name="<?php echo esc_attr( $this->get_field_name( 'show_facebook' ) ); ?>"
		id="<?php echo esc_attr( $this->get_field_id( 'show_facebook' ) ); ?>"
		<?php checked( $show_facebook, 1 ); ?>
	>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_facebook' ) ); ?>">
		<?php esc_html_e( 'Facebook', 'simple-social-buttons' ); ?>
	</label>
	<br/>
	<input
		type="checkbox"
		value="1"
		class="show_twitter_check"
		name="<?php echo esc_attr( $this->get_field_name( 'show_twitter' ) ); ?>"
		id="<?php echo esc_attr( $this->get_field_id( 'show_twitter' ) ); ?>"
		<?php checked( $show_twitter ); ?>
	>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_twitter' ) ); ?>">
		<?php esc_html_e( 'Twitter', 'simple-social-buttons' ); ?>
	</label>
	<br/>
	<input
		type="checkbox"
		value="1"
		class="show_youtube_check"
		name="<?php echo esc_attr( $this->get_field_name( 'show_youtube' ) ); ?>"
		id="<?php echo esc_attr( $this->get_field_id( 'show_youtube' ) ); ?>"
		<?php checked( $show_youtube, 1 ); ?>
	>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_youtube' ) ); ?>">
		<?php esc_html_e( 'YouTube', 'simple-social-buttons' ); ?>
	</label>
	<br/>
	<input
		type="checkbox"
		value="1"
		class="show_pinterest_check"
		name="<?php echo esc_attr( $this->get_field_name( 'show_pinterest' ) ); ?>"
		id="<?php echo esc_attr( $this->get_field_id( 'show_pinterest' ) ); ?>"
		<?php checked( $show_pinterest, 1 ); ?>
	>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_pinterest' ) ); ?>">
		<?php esc_html_e( 'Pinterest', 'simple-social-buttons' ); ?>
	</label>
	<br/>
	<input
		type="checkbox"
		value="1"
		class="show_instagram_check"
		name="<?php echo esc_attr( $this->get_field_name( 'show_instagram' ) ); ?>"
		id="<?php echo esc_attr( $this->get_field_id( 'show_instagram' ) ); ?>"
		<?php checked( $show_instagram, 1 ); ?>
	>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_instagram' ) ); ?>">
		<?php esc_html_e( 'Instagram', 'simple-social-buttons' ); ?>
	</label>
	<br/>
	<input
		type="checkbox"
		value="1"
		class="show_whatsapp_check"
		name="<?php echo esc_attr( $this->get_field_name( 'show_whatsapp' ) ); ?>"
		id="<?php echo esc_attr( $this->get_field_id( 'show_whatsapp' ) ); ?>"
		<?php checked( $show_whatsapp, 1 ); ?>
	>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_whatsapp' ) ); ?>">
		<?php esc_html_e( 'WhatsApp', 'simple-social-buttons' ); ?>
	</label>
</div>
<!--facebook-->
<div
	class="show_fb simpleshare-widget-settings"
	style="display: <?php echo esc_attr( ( $display === $show_facebook ) ? 'block' : 'none' ); ?>"
>
	<h2><span><?php esc_html_e( 'Facebook', 'simple-social-buttons' ); ?></span></h2>
	<p class="facebook_text" style="display: block ">
		<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_text' ) ); ?>">
			<?php esc_attr_e( 'Facebook Button Text', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'facebook_text' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'facebook_text' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $facebook_text ); ?>"
			placeholder="Like us on Facebook"
		>
	</p>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_id' ) ); ?>">
			<?php esc_html_e( 'Facebook Page Name:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'facebook_id' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'facebook_id' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $facebook_id ); ?>"
		>
	</p>
	<p>
		<input
			type="checkbox"
			name="<?php echo esc_attr( $this->get_field_name( 'facebook_show_counter' ) ); ?>"
			id="<?php echo esc_attr( $this->get_field_id( 'facebook_show_counter' ) ); ?>"
			value="1"
			<?php checked( $facebook_show_counter, 1 ); ?>
			class="fb_count_check"
		>
		<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_show_counter' ) ); ?>">
			<?php esc_html_e( 'Display Facebook like counter', 'simple-social-buttons' ); ?>
		</label>
	</p>
	<p class="fb-error" style="color: red"></p>

	<div
		class="fb_api_key"
		style="display:<?php echo esc_attr( ( $display === $facebook_show_counter ) ? 'block' : 'none' ); ?> "
	>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_app_id' ) ); ?>">
				<?php esc_attr_e( 'Facebook App Id:', 'simple-social-buttons' ); ?>
			</label>
			<input
				class="widefat fb_app_id"
				id="<?php echo esc_attr( $this->get_field_id( 'facebook_app_id' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'facebook_app_id' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $facebook_app_id ); ?>"
			>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_security_key' ) ); ?>">
				<?php esc_attr_e( 'Facebook Security Key:', 'simple-social-buttons' ); ?>
			</label>
			<input
				class="widefat fb_secret_key"
				id="<?php echo esc_attr( $this->get_field_id( 'facebook_security_key' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'facebook_security_key' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $facebook_security_key ); ?>"
			>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_access_token' ) ); ?>">
				<?php esc_attr_e( 'Facebook Access Token: ', 'simple-social-buttons' ); ?>
				<a href="javascript:void(0)" class="get_fb_token" target="_blank">get  access token</a>
				<img
					src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"
					id="token_loader"
					style="display: none"
				>
			</label>
			<input
				class="widefat fb_access_token"
				id="<?php echo esc_attr( $this->get_field_id( 'facebook_access_token' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'facebook_access_token' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $facebook_access_token ); ?>"
			>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_default_count' ) ); ?>">
				<small>
					<?php
					esc_html_e(
						'Default count to show instead of 0 when API\'s are not available.',
						'simple-social-buttons'
					);
					?>
				</small>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'facebook_default_count' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'facebook_default_count' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $facebook_default_count ); ?>"
			>
		</p>
	</div>
	<hr/>
</div>
<!--twitter-->
<div
	class="show_twitter simpleshare-widget-settings"
	style="display: <?php echo esc_attr( ( $display === $show_twitter ) ? 'block' : 'none' ); ?>"
>
	<h2><span><?php esc_html_e( 'Twitter', 'simple-social-buttons' ); ?></span></h2>
	<p class="twitter_text" style="display: block ">
		<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_text' ) ); ?>">
			<?php esc_attr_e( 'Twitter Button Text:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'twitter_text' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'twitter_text' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $twitter_text ); ?>"
			placeholder="Follow us on Twitter"
		>
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>">
			<?php esc_attr_e( 'Twitter Handle:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'twitter' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $twitter ); ?>"
			placeholder="Handle"
		>
	</p>
	<p>
		<input
			type="checkbox"
			name="<?php echo esc_attr( $this->get_field_name( 'twitter_show_counter' ) ); ?>"
			id="<?php echo esc_attr( $this->get_field_id( 'twitter_show_counter' ) ); ?>"
			value="1"
			<?php checked( $twitter_show_counter, 1 ); ?>
			class="twitter_count_check"
		>
		<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_show_counter' ) ); ?>">
			<?php esc_html_e( 'Display Twitter follower counter', 'simple-social-buttons' ); ?>
		</label>
	</p>

	<div
		class="twitter_api_key"
		style="display: <?php echo esc_attr( ( $display === $twitter_show_counter ) ? 'block' : 'none' ); ?>"
	>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_api_key' ) ); ?>">
				<?php esc_attr_e( 'Twitter Api Key: ', 'simple-social-buttons' ); ?>
				<a href="https://apps.twitter.com/" target="_blank">
					How to get the Twitter  security and api key
				</a>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'twitter_api_key' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'twitter_api_key' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $twitter_api_key ); ?>"
			>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_secret_key' ) ); ?>">
				<?php esc_attr_e( 'Twitter Secret Key:', 'simple-social-buttons' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'twitter_secret_key' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'twitter_secret_key' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $twitter_secret_key ); ?>"
			>
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_default_count' ) ); ?>">
				<small>
					<?php
					esc_html_e(
						'Default count to show instead of 0 when API\'s are not available.',
						'simple-social-buttons'
					);
					?>
				</small>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'twitter_default_count' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'twitter_default_count' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $twitter_default_count ); ?>"
			>
		</p>
	</div>
	<hr/>
</div>
<!--youtube-->
<div
	class="show_youtube simpleshare-widget-settings"
	style="display: <?php echo esc_attr( ( $display === $show_youtube ) ? 'block' : 'none' ); ?>"
>
	<h2><span><?php esc_html_e( 'YouTube', 'simple-social-buttons' ); ?></span></h2>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'youtube_text' ) ); ?>">
			<?php esc_attr_e( 'YouTube Button Text:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'youtube_text' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'youtube_text' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $youtube_text ); ?>"
			placeholder="Subscribe  us on YouTube"
		>
	</p>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'youtube_type' ) ); ?>">
			<?php esc_attr_e( 'Type :', 'simple-social-buttons' ); ?>
		</label>
		<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'youtube_type' ) ); ?>">
			<option <?php selected( $youtube_type, 'channel' ); ?> value="channel">
				<?php esc_html_e( 'Channel', 'simple-social-buttons' ); ?>
			</option>
			<option <?php selected( $youtube_type, 'user' ); ?> value="user">
				<?php esc_html_e( 'Username', 'simple-social-buttons' ); ?>
			</option>
		</select>
	</p>

	<p class="youtube_text" style="display: block">
		<label for="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>">
			<?php esc_attr_e( 'YouTube Channel ID:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $youtube ); ?>"
		>
		<input
			type="checkbox"
			name="<?php echo esc_attr( $this->get_field_name( 'youtube_show_counter' ) ); ?>"
			id="<?php echo esc_attr( $this->get_field_id( 'youtube_show_counter' ) ); ?>"
			value="1"
			<?php checked( $youtube_show_counter, 1 ); ?>
			class="youtube_count_check"
		>
		<label for="<?php echo esc_attr( $this->get_field_id( 'youtube_show_counter' ) ); ?>">
			<?php esc_html_e( 'Display YouTube subscriber counter', 'simple-social-buttons' ); ?>
		</label>
	</p>

	<div
		class="youtube_api_key"
		style="display: <?php echo esc_attr( ( $display === $youtube_show_counter ) ? 'block' : 'none' ); ?>"
	>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'youtube_api_key' ) ); ?>">
				<?php esc_attr_e( 'YouTube Api Key: ', 'simple-social-buttons' ); ?>
				<a href="https://developers.google.com/youtube/registering_an_application" target="_blank">
					How to get the YouTube API Key
				</a>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'youtube_api_key' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'youtube_api_key' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $youtube_api_key ); ?>"
			>
		</p>
	</div>
	<hr/>
</div>
<!--pinterest-->
<div
	class="show_pinterest simpleshare-widget-settings"
	style="display: <?php echo esc_attr( ( $display === $show_pinterest ) ? 'block' : 'none' ); ?>"
>
	<h2><span><?php esc_html_e( 'Pinterest', 'simple-social-buttons' ); ?></span></h2>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'pinterest_text' ) ); ?>">
			<?php esc_attr_e( 'Pinterest Button Text:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'pinterest_text' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'pinterest_text' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $pinterest_text ); ?>"
			placeholder="Follow  us on Pinterest"
		>
	</p>
	<p class="pinterest_text" style="display: block">
		<label for="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>">
			<?php esc_attr_e( 'Pinterest User ID:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'pinterest' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $pinterest ); ?>"
			placeholder="Username"
		>
		<input
			type="checkbox"
			name="<?php echo esc_attr( $this->get_field_name( 'pinterest_show_counter' ) ); ?>"
			id="<?php echo esc_attr( $this->get_field_id( 'pinterest_show_counter' ) ); ?>"
			value="1"
			<?php checked( $pinterest_show_counter, 1 ); ?>
			class="pinterest_count_check"
		>
		<label for="<?php echo esc_attr( $this->get_field_id( 'pinterest_show_counter' ) ); ?>">
			<?php esc_html_e( 'Display Pinterest follower counter', 'simple-social-buttons' ); ?>
		</label>
	</p>
	<div class="pinterest_api_key" style="display:none;visibility:hidden">
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pinterest_api_key' ) ); ?>">
				<?php esc_attr_e( 'Printerest Access Token: ', 'simple-social-buttons' ); ?>
				<a href="https://developers.pinterest.com/tools/access_token/" target="_blank">
					How to get the Pinterest access token
				</a>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'pinterest_api_key' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'pinterest_api_key' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $pinterest_api_key ); ?>"
			>
		</p>
	</div>
	<hr/>
</div>

<!--instagram-->
<div
	class="show_instagram simpleshare-widget-settings"
	style="display: <?php echo esc_attr( ( $display === $show_instagram ) ? 'block' : 'none' ); ?>"
>
	<h2><span><?php esc_html_e( 'Instagram', 'simple-social-buttons' ); ?></span></h2>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'instagram_text' ) ); ?>">
			<?php esc_attr_e( 'Instagram Button Text:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'instagram_text' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'instagram_text' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $instagram_text ); ?>"
			placeholder="Follow  us on Instagram"
		>
	</p>
	<p class="instagram_text" style="display: block">
		<label for="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>">
			<?php esc_attr_e( 'Instagram User Name:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'instagram' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $instagram ); ?>"
			placeholder="Username"
		>
		<input
			type="checkbox"
			name="<?php echo esc_attr( $this->get_field_name( 'instagram_show_counter' ) ); ?>"
			id="<?php echo esc_attr( $this->get_field_id( 'instagram_show_counter' ) ); ?>"
			value="1"
			<?php checked( $instagram_show_counter, 1 ); ?>
			class="instagram_count_check"
		>
		<label for="<?php echo esc_attr( $this->get_field_id( 'instagram_show_counter' ) ); ?>">
			<?php esc_html_e( 'Display Instagram follower counter', 'simple-social-buttons' ); ?>
		</label>
	</p>
	<div
		class="instagram_api_key"
		style="display: <?php echo esc_attr( ( $display === $instagram_show_counter ) ? 'block' : 'none' ); ?>"
	>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'instagram_access_token' ) ); ?>">
				<?php esc_attr_e( 'Instagram Access Token: ', 'simple-social-buttons' ); ?>
				<a href="https://instagram.pixelunion.net/" target="_blank">
					How to get the Instagram Access Token.
				</a>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'instagram_access_token' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'instagram_access_token' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $instagram_access_token ); ?>"
			>
		</p>
	</div>
	<hr/>
</div>

<!--WhatsApp-->
<div
	class="show_whatsapp simpleshare-widget-settings"
	style="display: <?php echo esc_attr( ( $display === $show_whatsapp ) ? 'block' : 'none' ); ?>"
>
	<h2><span><?php esc_html_e( 'WhatsApp', 'simple-social-buttons' ); ?></span></h2>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'whatsapp_text' ) ); ?>">
			<?php esc_attr_e( 'WhatsApp Button Text:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'whatsapp_text' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'whatsapp_text' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $whatsapp_text ); ?>"
			placeholder="Message  us on WhatsApp"
		>
	</p>
	<p class="whatsapp_text" style="display: block">
		<label for="<?php echo esc_attr( $this->get_field_id( 'whatsapp' ) ); ?>">
			<?php esc_attr_e( 'WhatsApp Mobile Number:', 'simple-social-buttons' ); ?>
		</label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'whatsapp' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'whatsapp' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $whatsapp ); ?>"
			placeholder="923444XXXXXX"
		>
	</p>
	<hr/>
</div>
