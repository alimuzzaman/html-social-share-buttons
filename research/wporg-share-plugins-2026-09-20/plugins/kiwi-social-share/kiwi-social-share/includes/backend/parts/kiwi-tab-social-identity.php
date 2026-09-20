<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="sl-kiwi-tab-socialIdentity <?php echo ( $hash === 'sl-kiwi-tab-socialIdentity' ) ? 'epsilon-tab-active' : ''; ?>">
	<h2>
		<span><?php echo esc_html__( 'Social identities', 'kiwi-social-share' ) ?></span>
	</h2>
	<div class="clearfix">
		<label
			for="kiwi-twitter-username"> <?php echo esc_html__( 'Twitter Username', 'kiwi-social-share' ); ?> </label>
		<input type="text" id="kiwi-twitter-username"
		       name="kiwi_social_identities[twitter_username]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'twitter_username', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<div class="clearfix">
		<label
			for="kiwi-facebook-page-url"> <?php echo esc_html__( 'Facebook Page Url', 'kiwi-social-share' ); ?> </label>
		<input type="text" id="kiwi-facebook-page-url" name="kiwi_social_identities[facebook_page_url]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'facebook_page_url', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<div class="clearfix">
		<label for="kiwi-facebook-app-id">
			<?php echo esc_html__( 'Facebook App Id', 'kiwi-social-share' ); ?>
			<br><small><?php echo esc_html__( 'Required for FB share counts.', 'kiwi-social-share' ); ?></small>
		</label>
		<input type="text" id="kiwi-facebook-app-id" name="kiwi_social_identities[facebook_app_id]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'facebook_app_id', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<div class="clearfix">
		<label for="kiwi-facebook-app-secret">
			<?php echo esc_html__( 'Facebook App Secret', 'kiwi-social-share' ); ?>
			<br><small><?php echo esc_html__( 'Required for FB share counts.', 'kiwi-social-share' ); ?></small>
		</label>
		<input type="text" id="kiwi-facebook-app-secret" name="kiwi_social_identities[facebook_app_secret]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'facebook_app_secret', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<!-- start-pro-version -->
	<div class="clearfix">
		<label for="kiwi-bitly-access-token"> <?php echo esc_html__( 'Bitly Access Token', 'kiwi-social-share' ); ?> </label>
		<input type="text" id="kiwi-bitly-access-token" name="kiwi_social_identities[bitly_access_token]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'bitly_access_token', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<div class="clearfix">
		<label for="kiwi-bitly-access-login"> <?php echo esc_html__( 'Bitly Access Login', 'kiwi-social-share' ); ?> </label>
		<input type="text" id="kiwi-bitly-access-login" name="kiwi_social_identities[bitly_access_login]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'bitly_access_login', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<!-- end-pro-version -->
</div>