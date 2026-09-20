<div class="dpsp-card">

				<div class="dpsp-card-header">
					<?php esc_html_e( 'Social Identities', 'social-pug' ); ?>
				</div>

				<div class="dpsp-card-inner">

					<!-- Tab Top Do Action -->
					<?php do_action( 'dpsp_settings_page_tab_social_identity_top', $dpsp_settings ); ?>

					<?php dpsp_settings_field( 'text', 'dpsp_settings[email_username]', ( isset( $dpsp_settings['email_username'] ) ? $dpsp_settings['email_username'] : '' ), __( 'Email Address', 'social-pug' ), [], __( 'Used as the destination email address for the Email Follow Button.', 'social-pug' ) ); ?>
					
					<h3>
						<?php esc_html_e( 'Social Networking and Blogging', 'social-pug' ); ?>
					</h3>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[facebook_username]', ( isset( $dpsp_settings['facebook_username'] ) ? $dpsp_settings['facebook_username'] : '' ), __( 'Facebook Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[twitter_username]', ( isset( $dpsp_settings['twitter_username'] ) ? $dpsp_settings['twitter_username'] : '' ), __( 'X Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'switch', 'dpsp_settings[tweets_have_username]', ( isset( $dpsp_settings['tweets_have_username'] ) ? $dpsp_settings['tweets_have_username'] : '' ), __( 'Add X Username to all tweets', 'social-pug' ), [ 'yes' ] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[bluesky_username]', ( isset( $dpsp_settings['bluesky_username'] ) ? $dpsp_settings['bluesky_username'] : '' ), __( 'Bluesky Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[tumblr_username]', ( isset( $dpsp_settings['tumblr_username'] ) ? $dpsp_settings['tumblr_username'] : '' ), __( 'Tumblr Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[reddit_username]', ( isset( $dpsp_settings['reddit_username'] ) ? $dpsp_settings['reddit_username'] : '' ), __( 'Reddit Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[medium_username]', ( isset( $dpsp_settings['medium_username'] ) ? $dpsp_settings['medium_username'] : '' ), __( 'Medium Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[vkontakte_username]', ( isset( $dpsp_settings['vkontakte_username'] ) ? $dpsp_settings['vkontakte_username'] : '' ), __( 'VK Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[xing_username]', ( isset( $dpsp_settings['xing_username'] ) ? $dpsp_settings['xing_username'] : '' ), __( 'Xing Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[telegram_username]', ( isset( $dpsp_settings['telegram_username'] ) ? $dpsp_settings['telegram_username'] : '' ), __( 'Telegram Username/Channel', 'social-pug' ), [] ); ?>
					
					<h3>
						<?php esc_html_e( 'Publishing and Business', 'social-pug' ); ?>
					</h3>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[pinterest_username]', ( isset( $dpsp_settings['pinterest_username'] ) ? $dpsp_settings['pinterest_username'] : '' ), __( 'Pinterest Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[patreon_username]', ( isset( $dpsp_settings['patreon_username'] ) ? $dpsp_settings['patreon_username'] : '' ), __( 'Patreon Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[linkedin_username]', ( isset( $dpsp_settings['linkedin_username'] ) ? $dpsp_settings['linkedin_username'] : '' ), __( 'LinkedIn Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[github_username]', ( isset( $dpsp_settings['github_username'] ) ? $dpsp_settings['github_username'] : '' ), __( 'Github Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[substack_username]', ( isset( $dpsp_settings['substack_username'] ) ? $dpsp_settings['substack_username'] : '' ), __( 'Substack Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[beehiiv_username]', ( isset( $dpsp_settings['beehiiv_username'] ) ? $dpsp_settings['beehiiv_username'] : '' ), __( 'beehiiv URL', 'social-pug' ), [], __( 'Include the full beehiiv URL or custom domain. E.g. https://domain.beehiiv.com/ or https://yourdomain.com/', 'social-pug' ) ); ?>

					<h3>
						<?php esc_html_e( 'Video, Photo, and Streaming', 'social-pug' ); ?>
					</h3>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[youtube_username]', ( isset( $dpsp_settings['youtube_username'] ) ? $dpsp_settings['youtube_username'] : '' ), __( 'YouTube Channel', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[vimeo_username]', ( isset( $dpsp_settings['vimeo_username'] ) ? $dpsp_settings['vimeo_username'] : '' ), __( 'Vimeo Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[twitch_username]', ( isset( $dpsp_settings['twitch_username'] ) ? $dpsp_settings['twitch_username'] : '' ), __( 'Twitch Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[instagram_username]', ( isset( $dpsp_settings['instagram_username'] ) ? $dpsp_settings['instagram_username'] : '' ), __( 'Instagram Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[behance_username]', ( isset( $dpsp_settings['behance_username'] ) ? $dpsp_settings['behance_username'] : '' ), __( 'Behance Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[tiktok_username]', ( isset( $dpsp_settings['tiktok_username'] ) ? $dpsp_settings['tiktok_username'] : '' ), __( 'TikTok Username', 'social-pug' ), [] ); ?>

					<h3>
						<?php esc_html_e( 'Fediverse', 'social-pug' ); ?>
					</h3>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[flipboard_username]', ( isset( $dpsp_settings['flipboard_username'] ) ? $dpsp_settings['flipboard_username'] : '' ), __( 'Flipboard Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[threads_username]', ( isset( $dpsp_settings['threads_username'] ) ? $dpsp_settings['threads_username'] : '' ), __( 'Threads Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[mastodon_username]', ( isset( $dpsp_settings['mastodon_username'] ) ? $dpsp_settings['mastodon_username'] : '' ), __( 'Mastodon Profile URL', 'social-pug' ), [], __( 'Include the full URL of your Mastodon profile. E.g. https://mastodon.social/@morehubbub', 'social-pug' ) ) ?>
					
					<h3>
						<?php esc_html_e( 'Podcasts and Music', 'social-pug' ); ?>
					</h3>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[applepodcasts_username]', ( isset( $dpsp_settings['applepodcasts_username'] ) ? $dpsp_settings['applepodcasts_username'] : '' ), __( 'Apple Podcasts URL', 'social-pug' ), [], __( 'Include the full URL to your Apple Podcast.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[spotify_username]', ( isset( $dpsp_settings['spotify_username'] ) ? $dpsp_settings['spotify_username'] : '' ), __( 'Spotify URL', 'social-pug' ), [], __( 'Include the full URL to your Spotify profile, Artist page, or Podcast.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[youtubemusic_username]', ( isset( $dpsp_settings['youtubemusic_username'] ) ? $dpsp_settings['youtubemusic_username'] : '' ), __( 'YouTube Music URL', 'social-pug' ), [], __( 'Include the full URL to your YouTube Music profile, Artist page, or Podcast.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[pocketcasts_username]', ( isset( $dpsp_settings['pocketcasts_username'] ) ? $dpsp_settings['pocketcasts_username'] : '' ), __( 'Pocket Casts URL', 'social-pug' ), [], __( 'Include the full Pocket Casts URL. To create one, visit: https://pocketcasts.com/submit/', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[overcast_username]', ( isset( $dpsp_settings['overcast_username'] ) ? $dpsp_settings['overcast_username'] : '' ), __( 'Overcast Show URL', 'social-pug' ), [], __( 'Include the full Overcast show URL. To learn more, visit: https://overcast.fm/podcasterinfo', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[amazonmusic_username]', ( isset( $dpsp_settings['amazonmusic_username'] ) ? $dpsp_settings['amazonmusic_username'] : '' ), __( 'Amazon Music URL', 'social-pug' ), [], __( 'Include the full Amazon Music Podcast or Artist URL.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[audible_username]', ( isset( $dpsp_settings['audible_username'] ) ? $dpsp_settings['audible_username'] : '' ), __( 'Audible URL', 'social-pug' ), [], __( 'Include the full Audible URL.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[soundcloud_username]', ( isset( $dpsp_settings['soundcloud_username'] ) ? $dpsp_settings['soundcloud_username'] : '' ), __( 'SoundCloud Username', 'social-pug' ), [] ); ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[castbox_username]', ( isset( $dpsp_settings['castbox_username'] ) ? $dpsp_settings['castbox_username'] : '' ), __( 'Castbox URL', 'social-pug' ), [], __( 'Include the full Castbox URL.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[podbean_username]', ( isset( $dpsp_settings['podbean_username'] ) ? $dpsp_settings['podbean_username'] : '' ), __( 'Podbean URL', 'social-pug' ), [], __( 'Include the full Podbean URL.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[tunein_username]', ( isset( $dpsp_settings['tunein_username'] ) ? $dpsp_settings['tunein_username'] : '' ), __( 'TuneIn URL', 'social-pug' ), [], __( 'Include the full TuneIn URL.', 'social-pug' ) ) ?>
					<?php dpsp_settings_field( 'text', 'dpsp_settings[siriusxm_username]', ( isset( $dpsp_settings['siriusxm_username'] ) ? $dpsp_settings['siriusxm_username'] : '' ), __( 'SiriusXM URL', 'social-pug' ), [], __( 'Include the full SiriusXM URL.', 'social-pug' ) ) ?>

					<!-- Tab Bottom Do Action -->
					<?php do_action( 'dpsp_settings_page_tab_social_identity_bottom', $dpsp_settings ); ?>

				</div>

			</div>