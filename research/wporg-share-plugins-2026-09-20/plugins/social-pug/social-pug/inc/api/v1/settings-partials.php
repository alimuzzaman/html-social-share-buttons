<?php


namespace Mediavine\Grow\API\V1\Partials;

/**
 * Get settings partials.
 *
 * @return array[]
 */
function get_settings_partials() {
	return [
		'active'                              => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether or not this tool is active', 'mediavine' ),
		],
		'button_style'                        => [
			'type'        => 'integer',
			'description' => esc_html__( 'The style of button to render', 'mediavine' ),

		],
		'count_round'                         => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether or not to round the share counts', 'mediavine' ),

		],
		'custom_color'                        => [
			'type'        => 'string',
			'description' => esc_html__( 'A custom color for the buttons', 'mediavine' ),
			'format'      => 'hex-color',

		],
		'custom_hover_color'                  => [
			'type'        => 'string',
			'description' => esc_html__( 'A custom hover color for the buttons', 'mediavine' ),
			'format'      => 'hex-color',
		],
		'icon_animation'                      => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether or not icons can be animated', 'mediavine' ),

		],
		'intro_animation'                     => [
			'type'        => 'integer',
			'description' => esc_html__( 'What intro animation to use', 'mediavine' ),
			'enum'        => [
				- 1,
				0,
				1,
			],
		],
		'minimum_individual_count'            => [
			'type'        => 'integer',
			'description' => esc_html__( 'Minimum number of share counts for a particular network for those counts to show in the front end', 'mediavine' ),
		],
		'position'                            => [
			'type'        => 'string',
			'description' => esc_html__( 'Button Position', 'mediavine' ),
			'enum'        => [
				'left',
				'right',
				'top',
				'bottom',
				'both',
			],
		],
		'column_count'                        => [
			'type'        => 'string',
			'description' => esc_html__( 'Column Count', 'mediavine' ),
			'enum'        => [
				'auto',
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
			],
		],
		'message'                             => [
			'type'        => 'string',
			'description' => esc_html__( 'Message to appear above share buttons', 'mediavine' ),
		],
		'screen_size'                         => [
			'type'        => 'integer',
			'description' => esc_html__( 'Number of pixels for a screen width to determine mobile or desktop', 'mediavine' ),
		],
		'shape'                               => [
			'type'        => 'string',
			'description' => esc_html__( 'Shape of the buttons', 'mediavine' ),
			'enum'        => [
				'circular',
				'rounded',
				'rectangle',
			],
		],
		'show_after_scrolling'                => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether or not to show the sidebar only after scrolling', 'mediavine' ),

		],
		'show_count'                          => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether to show the share count per network', 'mediavine' ),

		],
		'show_count_total'                    => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether to show the total share count', 'mediavine' ),

		],
		'show_labels'                         => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether to show labels or not', 'mediavine' ),

		],
		'show_labels_mobile'                         => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether to show labels on mobile or not', 'mediavine' ),

		],
		'show_mobile'                         => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether to show on mobile or not', 'mediavine' ),

		],
		'size'                                => [
			'type'        => 'string',
			'description' => esc_html__( 'Button Size', 'mediavine' ),
			'enum'        => [
				'small',
				'medium',
				'large',
			],
		],
		'spacing'                             => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether buttons should be spaced out or not', 'mediavine' ),

		],

		'double_inline_content_markup'        => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Whether to allow dupblicate renders of inline content', 'mediavine' ),
		],
		'networks'                            => [
			'type'  => 'array',
			'items' => [
				'type'       => 'object',
				'properties' => [
					'slug'  => [
						'type'        => 'string',
						'description' => esc_html__( 'Slug value for this particular network', 'mediavine' ),
					],
					'label' => [
						'type'        => 'string',
						'description' => esc_html__( 'Label for this particular network', 'mediavine' ),
					],
				],
			],
		],
		'post_type_display'                   => [
			'type'        => 'object',
			'description' => esc_html__( 'Post types to display the sidebar on', 'mediavine' ),
			'properties'  => [
				'post' => [
					'type'        => 'boolean',
					'description' => esc_html__( 'Should this tool show on Posts', 'mediavine' ),
				],
				'page' => [
					'type'        => 'boolean',
					'description' => esc_html__( 'Should this tool show on pages', 'mediavine' ),
				],
			],
		],
		'utm_tracking'                        => [
			'type'        => 'boolean',
			'description' => esc_html__( ' Enable UTM Tracking?', 'mediavine' ),
		],
		'utm_source'                          => [
			'type'        => 'string',
			'description' => esc_html__( 'UTM Campaign Source', 'mediavine' ),
		],
		'utm_medium'                          => [
			'type'        => 'string',
			'description' => esc_html__( 'UTM Campaign Medium', 'mediavine' ),
		],
		'utm_campaign'                        => [
			'type'        => 'string',
			'description' => esc_html__( 'UTM Campaign Name', 'mediavine' ),
		],
		'http_and_https_share_counts'         => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Combine HTTP & HTTPS Protocols', 'mediavine' ),
		],
		'previous_permalink_share_counts'     => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Combine Previous Permalink Format', 'mediavine' ),
		],
		'previous_permalink_structure'        => [
			'type'        => 'string',
			'description' => esc_html__(
				'Previous Permalink Format
			', 'mediavine'
			),
			'enum'        => [
				'plain',
				'/%year%/%monthnum%/%day%/%postname%/',
				'/%year%/%monthnum%/%postname%/',
				'/archives/%post_id%',
				'/%postname%/',
				'custom',
			],
		],
		'previous_permalink_structure_custom' => [
			'type'        => 'string',
			'description' => esc_html__( 'Custom Permalink Format', 'mediavine' ),
		],
		'previous_domain_share_counts'        => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Combine Previous Domain', 'mediavine' ),
		],
		'previous_base_domain'                => [
			'type'        => 'string',
			'description' => esc_html__( 'Previous Base Domain', 'mediavine' ),
		],
		'branch_key'                          => [
			'type'        => 'string',
			'description' => esc_html__( 'Branch Key', 'mediavine' ),
		],
		'branch_custom_id_parameter'          => [
			'type'        => 'string',
			'description' => esc_html__( 'Branch ID Parameter', 'mediavine' ),
		],
		'branch_custom_title_parameter'       => [
			'type'        => 'string',
			'description' => esc_html__( 'Branch Title Parameter', 'mediavine' ),
		],
		'branch_custom_description_parameter' => [
			'type'        => 'string',
			'description' => esc_html__( 'Branch Description Parameter', 'mediavine' ),
		],
		'branch_custom_image_url_parameter'   => [
			'type'        => 'string',
			'description' => esc_html__( 'Branch Image URL Parameter', 'mediavine' ),
		],
		'branch_custom_date_parameter'        => [
			'type'        => 'string',
			'description' => esc_html__( 'Branch Date Parameter', 'mediavine' ),
		],
		'branch_custom_post_url_parameter'    => [
			'type'        => 'string',
			'description' => esc_html__( 'Branch URL Parameter', 'mediavine' ),
		],
		'ctt_style'                           => [
			'type'        => 'string',
			'description' => esc_html__( 'Tweet Box Theme', 'mediavine' ),
			'enum'        => [
				'1',
				'2',
				'3',
				'4',
				'5',
			],
		],
		'ctt_link_position'                   => [
			'type'        => 'string',
			'description' => esc_html__( 'Call to Action Position', 'mediavine' ),
			'enum'        => [
				'left',
				'right',
			],
		],
		'ctt_link_text'                       => [
			'type'        => 'string',
			'description' => esc_html__( 'Call to Action Text', 'mediavine' ),
		],
		'ctt_link_icon_animation'             => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Show Icon Animation', 'mediavine' ),
		],
		'product_serial'                      => [
			'type'        => 'string',
			'description' => esc_html__( 'Old Serial Number for Social Pug', 'mediavine' ),
		],
		'mv_grow_license'                     => [
			'type'        => 'string',
			'description' => esc_html__( 'License for Hubbub Pro', 'mediavine' ),
		],
		'facebook_app_id'                     => [
			'type'        => 'string',
			'description' => esc_html__( 'Facebook App ID for the Graph API', 'mediavine' ),
		],
		'facebook_app_secret'                 => [
			'type'        => 'string',
			'description' => esc_html__( 'Facebook App Secret for the Graph API', 'mediavine' ),
		],
		'facebook_app_access_token'           => [
			'type'        => 'string',
			'description' => esc_html__( 'FB Access token for the Hubbub Pro Facebook App', 'mediavine' ),
		],
		'facebook_share_counts_provider'      => [
			'type'        => 'string',
			'description' => esc_html__( 'Provider to use for Facebook Share Counts', 'mediavine' ),
			'enum'        => [
				'authorized_app',
				'own_app',
			],
		],
		'shortening_service'                  => [
			'type'        => 'string',
			'description' => esc_html__( 'Which URL Shortening Service to use', 'mediavine' ),
			'enum'        => [
				'branch',
				'bitly',
			],
		],
		'debugger_enabled'                    => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Enable System Debugger', 'mediavine' ),
		],
		'legacy_javascript'                   => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Use Legacy jQuery javascript', 'mediavine' ),
		],
		'tweets_have_username'                => [
			'type'        => 'boolean',
			'description' => esc_html__( 'Add Twitter username to all tweets', 'mediavine' ),
		],
		'twitter_username'                    => [
			'type'        => 'string',
			'description' => esc_html__( 'Twitter Username', 'mediavine' ),
		],
		'facebook_username'                   => [
			'type'        => 'string',
			'description' => esc_html__( 'Facebook Username', 'mediavine' ),
		],
		'pinterest_username'                  => [
			'type'        => 'string',
			'description' => esc_html__( 'Pinterest Username', 'mediavine' ),
		],
		'linkedin_username'                   => [
			'type'        => 'string',
			'description' => esc_html__( 'LinkedIn Username', 'mediavine' ),
		],
		'reddit_username'                     => [
			'type'        => 'string',
			'description' => esc_html__( 'Reddit Username', 'mediavine' ),
		],
		'vkontakte_username'                  => [
			'type'        => 'string',
			'description' => esc_html__( 'VKontakte Username', 'mediavine' ),
		],
		'tumblr_username'                     => [
			'type'        => 'string',
			'description' => esc_html__( 'Tumblr Username', 'mediavine' ),
		],
		'instagram_username'                  => [
			'type'        => 'string',
			'description' => esc_html__( 'Instagram Username', 'mediavine' ),
		],
		'youtube_username'                    => [
			'type'        => 'string',
			'description' => esc_html__( 'YouTube Username', 'mediavine' ),
		],
		'vimeo_username'                      => [
			'type'        => 'string',
			'description' => esc_html__( 'Vimeo Username', 'mediavine' ),
		],
		'soundcloud_username'                 => [
			'type'        => 'string',
			'description' => esc_html__( 'SoundCloud Username', 'mediavine' ),
		],
		'twitch_username'                     => [
			'type'        => 'string',
			'description' => esc_html__( 'Twitch Username', 'mediavine' ),
		],
		'behance_username'                    => [
			'type'        => 'string',
			'description' => esc_html__( 'Behance Username', 'mediavine' ),
		],
		'xing_username'                       => [
			'type'        => 'string',
			'description' => esc_html__( 'Xing Username', 'mediavine' ),
		],
		'github_username'                     => [
			'type'        => 'string',
			'description' => esc_html__( 'GitHub Username', 'mediavine' ),
		],
		'telegram_username'                   => [
			'type'        => 'string',
			'description' => esc_html__( 'Telegram Username', 'mediavine' ),
		],
		'medium_username'                     => [
			'type'        => 'string',
			'description' => esc_html__( 'Medium Username', 'mediavine' ),
		],
		'flipboard_username'                  => [
			'type'        => 'string',
			'description' => esc_html__( 'Flipboard Username', 'mediavine' ),
		],
	];
}

/**
 * Get an array of array partials based on the passed keys.
 * @param string[] $properties Array of keys for partials to get
 *
 * @return array[] Array of settings partials
 */
function get_partials_by_keys( $properties ) {
	$partials = get_settings_partials();
	return array_intersect_key( $partials, array_fill_keys( $properties, '' ) );
}
