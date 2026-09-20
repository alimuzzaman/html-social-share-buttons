<?php
namespace Mediavine\Grow;

/**
 * Pro-level social networks.
 */
class Pro_Networks {
	/**
	 * Get raw pro network data
	 * @return array[]
	 */
	public static function get_raw() {
		// @TODO Replace this class/method with a better pattern for loading this array
		return [
			[
				'name'            => 'Reddit',
				'slug'            => 'reddit',
				'share_format'    => 'https://www.reddit.com/submit?url=%1$s&title=%2$s',
				'follow_format'   => 'https://www.reddit.com/user/%1$s',
				'has_share_count' => true,
			],
			[
				'slug'          => 'vkontakte',
				'name'          => 'VK',
				'share_format'  => 'http://vk.com/share.php?url=%1$s',
				'follow_format' => 'https://vk.com/%1$s',
			],
			[
				'slug'         => 'whatsapp',
				'name'         => 'WhatsApp',
				'share_format' => 'https://wa.me/?text=%1$s+%2$s',
			],
			[
				'slug'         => 'sms',
				'name'         => 'SMS',
				'share_format' => 'sms:?&body=%2$s %1$s',
			],
			[
				'slug'         => 'pocket',
				'name'         => 'Pocket',
				'share_format' => 'https://getpocket.com/edit?url=%1$s',
			],
			[
				'slug'         => 'buffer',
				'name'         => 'Buffer',
				'share_format' => 'https://buffer.com/add?url=%1$s&text=%2$s',
			],
			[
				'slug'          => 'tumblr',
				'name'          => 'Tumblr',
				'share_format'  => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl=%1$s',
				'follow_format' => 'https://%1$s.tumblr.com/',
			],
			[
				'slug'          => 'xing',
				'name'          => 'Xing',
				'share_format'  => 'https://www.xing.com/spi/shares/new?url=%1$s',
				'follow_format' => 'https://www.xing.com/profile/%1$s',
			],
			[
				'slug'          => 'flipboard',
				'name'          => 'Flipboard',
				'share_format'  => 'https://share.flipboard.com/bookmarklet/popout?v=2&url=%1$s&title=%2$s',
				'follow_format' => 'https://flipboard.com/@%1$s',
			],
			[
				'slug'          => 'telegram',
				'name'          => 'Telegram',
				'share_format'  => 'https://telegram.me/share/url?url=%1$s&text=%2$s',
				'follow_format' => 'https://telegram.me/%1$s',
			],
			[
				'slug'         => 'mix',
				'name'         => 'Mix',
				'share_format' => 'https://mix.com/add?url=%1$s',
			],
			[
				'slug'          => 'instagram',
				'name'          => 'Instagram',
				'follow_format' => 'https://www.instagram.com/%1$s',
			],
			[
				'slug'          => 'youtube',
				'name'          => 'YouTube',
				'follow_format' => 'https://www.youtube.com/%1$s',
			],
			[
				'slug'          => 'vimeo',
				'name'          => 'Vimeo',
				'follow_format' => 'https://vimeo.com/%1$s',
			],
			[
				'slug'          => 'soundcloud',
				'name'          => 'SoundCloud',
				'follow_format' => 'https://soundcloud.com/%1$s',
			],
			[
				'slug'          => 'twitch',
				'name'          => 'Twitch',
				'follow_format' => 'https://www.twitch.tv/%1$s/profile',
			],
			[
				'slug'          => 'behance',
				'name'          => 'Behance',
				'follow_format' => 'https://www.behance.net/%1$s',
			],
			[
				'slug'          => 'github',
				'name'          => 'GitHub',
				'follow_format' => 'https://github.com/%1$s',
			],
			[
				'slug'          => 'medium',
				'name'          => 'Medium',
				'follow_format' => 'https://medium.com/@%1$s',
			],
			[
				'slug'         => 'threads',
				'name'         => 'Threads',
				'share_format' => 'https://www.threads.net/intent/post?text=%2$s+%1$s',
			    'follow_format'=> 'https://www.threads.net/@%1$s',
			],
			[
				'slug'         => 'mastodon',
				'name'         => 'Mastodon',
				'share_format' => '/share?text=%2$s+%1$s', // Requires Instance URL at moment of share
			    'follow_format'=> '@%1$s', // Entire URL required in settings
			],
			[
				'slug'         => 'messenger',
				'name'         => 'Messenger',
				'share_format' => 'https://www.messenger.com/new?text=%1$s', // Only used if app not installed
			],
			[
				'slug'         => 'tiktok',
				'name'         => 'TikTok',
			    'follow_format'=> 'https://www.tiktok.com/@%1$s',
			],
			[
				'slug'          => 'bluesky',
				'name'          => 'Bluesky',
			    'share_format'	=> 'https://bsky.app/intent/compose?text=%2$s+%1$s',
				'follow_format' => 'https://bsky.app/profile/%1$s',
			],
			[
				'slug'          => 'applepodcasts',
				'name'          => 'Apple Podcasts',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'spotify',
				'name'          => 'Spotify',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'youtubemusic',
				'name'          => 'YouTube Music',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'pocketcasts',
				'name'          => 'Pocket Casts',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'overcast',
				'name'          => 'Overcast',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'amazonmusic',
				'name'          => 'Amazon Music',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'audible',
				'name'          => 'Audible',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'podbean',
				'name'          => 'Podbean',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'castbox',
				'name'          => 'Castbox',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
			[
				'slug'          => 'tunein',
				'name'          => 'TuneIn',
				'follow_format' => '%1$s', // Entire URL required in settings
			],
                        [
                                'slug'          => 'siriusxm',
                                'name'          => 'SiriusXM',
                                'follow_format' => '%1$s', // Entire URL required in settings
                        ],
                        [
                                'slug'          => 'substack',
                                'name'          => 'Substack',
                                'follow_format' => 'https://substack.com/@%1$s',
                        ],
                        [
                                'slug'          => 'beehiiv',
                                'name'          => 'beehiiv',
                                'follow_format' => '%1$s',
                        ],
                        [
                                'slug'          => 'patreon',
                                'name'          => 'Patreon',
                                'follow_format' => 'https://www.patreon.com/%1$s',
                        ],
		];
	}
}
