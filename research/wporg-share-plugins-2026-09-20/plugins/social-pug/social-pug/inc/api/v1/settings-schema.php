<?php

namespace Mediavine\Grow\API\V1\SettingsSchema;

use Mediavine\Grow\API\V1\Partials;

/**
 * Get the schema for the GET Tool settings endpoint
 *
 * @return array
 */
function get_tool_settings() : array {
	$schema = Partials\get_settings_partials();

	return [
		'active'            => $schema['active'],
		'button_style'      => $schema['button_style'],
		'display'           => [
			'type'        => 'object',
			'description' => esc_html__( 'Display properties of the button', 'mediavine' ),
			'properties'  => Partials\get_partials_by_keys(
				[
					'count_round',
					'custom_color',
					'custom_hover_color',
					'icon_animation',
					'intro_animation',
					'minimum_individual_count',
					'position',
					'column_count',
					'message',
					'screen_size',
					'shape',
					'show_after_scrolling',
					'show_count',
					'show_count_total',
					'show_labels',
					'show_labels_mobile',
					'show_mobile',
					'size',
					'spacing',
					'double_inline_content_markup',
				]
			),
		],
		'networks'          => $schema['networks'],
		'post_type_display' => $schema['post_type_display'],
	];

}

/**
 * Get the schema for the GET General settings endpoint
 *
 * @return array
 */
function get_general_settings() : array {
	return Partials\get_partials_by_keys(
		[
			'utm_tracking',
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'http_and_https_share_counts',
			'previous_permalink_share_counts',
			'previous_permalink_structure',
			'previous_permalink_structure_custom',
			'previous_domain_share_counts',
			'previous_base_domain',
			'branch_key',
			'branch_custom_id_parameter',
			'branch_custom_title_parameter',
			'branch_custom_description_parameter',
			'branch_custom_image_url_parameter',
			'branch_custom_date_parameter',
			'branch_custom_post_url_parameter',
			'ctt_style',
			'ctt_link_position',
			'ctt_link_text',
			'ctt_link_icon_animation',
			'product_serial',
			'mv_grow_license',
			'facebook_app_id',
			'facebook_app_secret',
			'facebook_app_access_token',
			'facebook_share_counts_provider',
			'shortening_service',
			'debugger_enabled',
			'legacy_javascript',
			'tweets_have_username',
			'twitter_username',
			'facebook_username',
			'pinterest_username',
			'linkedin_username',
			'reddit_username',
			'vkontakte_username',
			'tumblr_username',
			'instagram_username',
			'youtube_username',
			'vimeo_username',
			'soundcloud_username',
			'twitch_username',
			'behance_username',
			'xing_username',
			'github_username',
			'telegram_username',
			'medium_username',
			'flipboard_username',
		] );
}

