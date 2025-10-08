import { useState, useEffect, useCallback } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { PluginSettings, SaveSettingsResponse } from '../types';

/**
 * Hook for managing plugin settings state and API interactions
 */
export const useSettings = () => {
	const [ settings, setSettings ] = useState< PluginSettings | null >( null );
	const [ loading, setLoading ] = useState( true );
	const [ saving, setSaving ] = useState( false );
	const [ error, setError ] = useState< string | null >( null );
	const [ isDirty, setIsDirty ] = useState( false );

	// Load settings from API
	const loadSettings = useCallback( async () => {
		try {
			setLoading( true );
			setError( null );

			const response = ( await apiFetch( {
				path: '/html-social-share/v1/settings',
				method: 'GET',
			} ) ) as any;

			// Flatten the response structure to match our PluginSettings interface
			const flatSettings: PluginSettings = {
				// General settings
				show_on_front_page:
					response.general?.show_on_front_page ?? true,
				show_on_posts: response.general?.show_on_posts ?? true,
				show_on_pages: response.general?.show_on_pages ?? false,
				show_on_archives: response.general?.show_on_archives ?? false,
				default_style: response.general?.default_style ?? 'default',
				default_size: response.general?.default_size ?? 'medium',

				// Network settings
				enabled_networks: response.networks?.enabled_networks ?? [
					'facebook',
					'twitter',
					'linkedin',
				],
				network_order: response.networks?.network_order ?? [],
				custom_networks: response.networks?.custom_networks ?? [],

				// Profile settings (from API if present)
				profiles: response.profiles ?? [],
				default_profile: response.default_profile ?? '',

				// Integrations
				betterlinks_enabled:
					response.integrations?.betterlinks_enabled ?? false,
				betterlinks_api_key:
					response.integrations?.betterlinks_api_key ?? '',
				elementor_enabled:
					response.integrations?.elementor_enabled ?? false,
				divi_enabled: response.integrations?.divi_enabled ?? false,
				beaver_builder_enabled:
					response.integrations?.beaver_builder_enabled ?? false,

				// Appearance
				iconset: response.appearance?.iconset ?? 'default_square',
				icon_style: response.appearance?.icon_style ?? 'default',
				button_size: response.appearance?.button_size ?? 'medium',
				button_spacing: response.appearance?.button_spacing ?? 5,
				custom_css: response.appearance?.custom_css ?? '',

				// Appearance (continued)
				title:
					response.appearance?.title ??
					'Share this with your friends',

				// Placement
				auto_placement: response.placement?.auto_placement ?? false,
				placement_position:
					response.placement?.placement_position ?? 'after',
				placement_post_types: response.placement
					?.placement_post_types ?? [ 'post' ],
				exclude_pages: response.placement?.exclude_pages ?? '',
				// Legacy placement options
				floating_left: response.placement?.floating_left ?? false,
				floating_right: response.placement?.floating_right ?? false,
				before_content: response.placement?.before_content ?? false,
				after_content: response.placement?.after_content ?? true,

				// Integrations (continued)
				betterlinks_shorten_urls:
					response.integrations?.betterlinks_shorten_urls ?? true,
				betterlinks_add_tracking:
					response.integrations?.betterlinks_add_tracking ?? true,
				betterlinks_custom_tracking:
					response.integrations?.betterlinks_custom_tracking ?? {},
				betterlinks_available:
					response.integrations?.betterlinks_available ?? false,
				betterlinks_pro:
					response.integrations?.betterlinks_pro ?? false,
				betterlinks_version:
					response.integrations?.betterlinks_version ?? null,

				// Advanced
				google_analytics: response.advanced?.google_analytics ?? false,
				auto_hide_buttons:
					response.advanced?.auto_hide_buttons ?? false,
				use_port_in_url: response.advanced?.use_port_in_url ?? false,
				nofollow_links: response.advanced?.nofollow_links ?? true,
				cache_enabled: response.advanced?.cache_enabled ?? true,
				cache_duration: response.advanced?.cache_duration ?? 3600,
				debug_mode: response.advanced?.debug_mode ?? false,
			};

			setSettings( flatSettings );
			setIsDirty( false );
		} catch ( err ) {
			setError(
				err instanceof Error ? err.message : 'Failed to load settings'
			);
			// Removed console.error for linting
		} finally {
			setLoading( false );
		}
	}, [] );

	// Update a specific setting
	const updateSetting = useCallback(
		< K extends keyof PluginSettings >(
			key: K,
			value: PluginSettings[ K ]
		) => {
			setSettings( ( prev ) => {
				if ( ! prev ) {
					return prev;
				}

				const updated = { ...prev, [ key ]: value };
				setIsDirty( true );
				return updated;
			} );
		},
		[]
	);

	// Update multiple settings at once
	const updateSettings = useCallback(
		( updates: Partial< PluginSettings > ) => {
			setSettings( ( prev ) => {
				if ( ! prev ) {
					return prev;
				}

				const updated = { ...prev, ...updates };
				setIsDirty( true );
				return updated;
			} );
		},
		[]
	);

	// Save settings to API
	const saveSettings =
		useCallback( async (): Promise< SaveSettingsResponse > => {
			if ( ! settings ) {
				throw new Error( 'No settings to save' );
			}

			try {
				setSaving( true );
				setError( null );

				// Restructure settings to match API format
				const apiData = {
					general: {
						show_on_front_page: settings.show_on_front_page,
						show_on_posts: settings.show_on_posts,
						show_on_pages: settings.show_on_pages,
						show_on_archives: settings.show_on_archives,
						default_style: settings.default_style,
						default_size: settings.default_size,
					},
					networks: {
						enabled_networks: settings.enabled_networks,
						network_order: settings.network_order,
						custom_networks: settings.custom_networks,
					},
					appearance: {
						title: settings.title,
						iconset: settings.iconset,
						icon_style: settings.icon_style,
						button_size: settings.button_size,
						button_spacing: settings.button_spacing,
						custom_css: settings.custom_css,
					},
					placement: {
						auto_placement: settings.auto_placement,
						placement_position: settings.placement_position,
						placement_post_types: settings.placement_post_types,
						exclude_pages: settings.exclude_pages,
						floating_left: settings.floating_left,
						floating_right: settings.floating_right,
						before_content: settings.before_content,
						after_content: settings.after_content,
					},
					integrations: {
						betterlinks_enabled: settings.betterlinks_enabled,
						betterlinks_api_key: settings.betterlinks_api_key,
						betterlinks_shorten_urls:
							settings.betterlinks_shorten_urls,
						betterlinks_add_tracking:
							settings.betterlinks_add_tracking,
						betterlinks_custom_tracking:
							settings.betterlinks_custom_tracking,
						elementor_enabled: settings.elementor_enabled,
						divi_enabled: settings.divi_enabled,
						beaver_builder_enabled: settings.beaver_builder_enabled,
					},
					advanced: {
						google_analytics: settings.google_analytics,
						auto_hide_buttons: settings.auto_hide_buttons,
						use_port_in_url: settings.use_port_in_url,
						nofollow_links: settings.nofollow_links,
						cache_enabled: settings.cache_enabled,
						cache_duration: settings.cache_duration,
						debug_mode: settings.debug_mode,
					},
					// Persist profiles and the default profile (newly added)
					profiles: settings.profiles,
					default_profile: settings.default_profile,
				};

			const response = ( await apiFetch( {
				path: '/html-social-share/v1/settings',
				method: 'POST',
				data: apiData,
			} ) ) as SaveSettingsResponse;

			// Verify saved settings by updating local state with fresh data from DB
			if ( response.success && response.settings ) {
				// Flatten the response structure like in loadSettings
				const flatSettings: PluginSettings = {
					// General settings
					show_on_front_page:
						response.settings.general?.show_on_front_page ?? true,
					show_on_posts:
						response.settings.general?.show_on_posts ?? true,
					show_on_pages:
						response.settings.general?.show_on_pages ?? false,
					show_on_archives:
						response.settings.general?.show_on_archives ?? false,
					default_style:
						response.settings.general?.default_style ??
						'default',
					default_size:
						response.settings.general?.default_size ?? 'medium',

					// Network settings
					enabled_networks:
						response.settings.networks?.enabled_networks ?? [],
					network_order:
						response.settings.networks?.network_order ?? [],
					custom_networks:
						response.settings.networks?.custom_networks ?? [],

					// Profile settings
					profiles: response.settings.profiles ?? [],
					default_profile:
						response.settings.default_profile ?? '',

					// Integrations
					betterlinks_enabled:
						response.settings.integrations
							?.betterlinks_enabled ?? false,
					betterlinks_api_key:
						response.settings.integrations?.betterlinks_api_key ??
						'',
					elementor_enabled:
						response.settings.integrations?.elementor_enabled ??
						false,
					divi_enabled:
						response.settings.integrations?.divi_enabled ??
						false,
					beaver_builder_enabled:
						response.settings.integrations
							?.beaver_builder_enabled ?? false,

					// Appearance
					iconset:
						response.settings.appearance?.iconset ??
						'default_square',
					icon_style:
						response.settings.appearance?.icon_style ??
						'default',
					button_size:
						response.settings.appearance?.button_size ??
						'medium',
					button_spacing:
						response.settings.appearance?.button_spacing ?? 5,
					custom_css:
						response.settings.appearance?.custom_css ?? '',
					title:
						response.settings.appearance?.title ??
						'Share this with your friends',

					// Placement
					auto_placement:
						response.settings.placement?.auto_placement ??
						false,
					placement_position:
						response.settings.placement?.placement_position ??
						'after',
					placement_post_types:
						response.settings.placement?.placement_post_types ??
						[ 'post' ],
					exclude_pages:
						response.settings.placement?.exclude_pages ?? '',
					floating_left:
						response.settings.placement?.floating_left ?? false,
					floating_right:
						response.settings.placement?.floating_right ?? false,
					before_content:
						response.settings.placement?.before_content ?? false,
					after_content:
						response.settings.placement?.after_content ?? true,

					// Integrations (continued)
					betterlinks_shorten_urls:
						response.settings.integrations
							?.betterlinks_shorten_urls ?? true,
					betterlinks_add_tracking:
						response.settings.integrations
							?.betterlinks_add_tracking ?? true,
					betterlinks_custom_tracking:
						response.settings.integrations
							?.betterlinks_custom_tracking ?? {},
					betterlinks_available:
						response.settings.integrations
							?.betterlinks_available ?? false,
					betterlinks_pro:
						response.settings.integrations?.betterlinks_pro ??
						false,
					betterlinks_version:
						response.settings.integrations
							?.betterlinks_version ?? null,

					// Advanced
					google_analytics:
						response.settings.advanced?.google_analytics ??
						false,
					auto_hide_buttons:
						response.settings.advanced?.auto_hide_buttons ??
						false,
					use_port_in_url:
						response.settings.advanced?.use_port_in_url ?? false,
					nofollow_links:
						response.settings.advanced?.nofollow_links ?? true,
					cache_enabled:
						response.settings.advanced?.cache_enabled ?? true,
					cache_duration:
						response.settings.advanced?.cache_duration ?? 3600,
					debug_mode:
						response.settings.advanced?.debug_mode ?? false,
				};

				setSettings( flatSettings );
			}

			setIsDirty( false );

			return response;
			} catch ( err ) {
				const errorMessage =
					err instanceof Error
						? err.message
						: 'Failed to save settings';
				setError( errorMessage );
				throw new Error( errorMessage );
			} finally {
				setSaving( false );
			}
		}, [ settings ] );

	// Reset settings to defaults
	const resetSettings = useCallback( async () => {
		try {
			setSaving( true );
			setError( null );

			await apiFetch( {
				path: '/html-social-share/v1/settings/reset',
				method: 'POST',
			} );

			// Reload settings after reset
			await loadSettings();
		} catch ( err ) {
			const errorMessage =
				err instanceof Error ? err.message : 'Failed to reset settings';
			setError( errorMessage );
			throw new Error( errorMessage );
		} finally {
			setSaving( false );
		}
	}, [ loadSettings ] );

	// Load settings on mount
	useEffect( () => {
		loadSettings();
	}, [ loadSettings ] );

	return {
		settings,
		loading,
		saving,
		error,
		isDirty,
		updateSetting,
		updateSettings,
		saveSettings,
		resetSettings,
		refreshSettings: loadSettings,
	};
};
