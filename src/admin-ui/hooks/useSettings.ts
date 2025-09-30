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
			const networksResponse = response.networks ?? {};
			const integrationsResponse = response.integrations ?? {};
			const appearanceResponse = response.appearance ?? {};
			const placementResponse = response.placement ?? {};
			const advancedResponse = response.advanced ?? {};

			const flatSettings: PluginSettings = {
				// Display & Placement
				show_on_front_page:
					response.general?.show_on_front_page ?? true,
				show_on_posts: response.general?.show_on_posts ?? true,
				show_on_pages: response.general?.show_on_pages ?? false,
				show_on_archives: response.general?.show_on_archives ?? false,
				auto_placement: placementResponse.auto_placement ?? false,
				placement_position:
					placementResponse.placement_position ?? 'after',
				placement_post_types:
					placementResponse.placement_post_types ?? [ 'post' ],
				exclude_pages: placementResponse.exclude_pages ?? '',

				// Design Defaults
				default_style: response.general?.default_style ?? 'default',
				default_size: response.general?.default_size ?? 'medium',
				title:
					appearanceResponse.title ?? 'Share this with your friends',
				icon_style: appearanceResponse.icon_style ?? 'default',
				button_size: appearanceResponse.button_size ?? 'medium',
				button_spacing: appearanceResponse.button_spacing ?? 5,
				custom_css: appearanceResponse.custom_css ?? '',

				// Network Settings
				enabled_networks: networksResponse.enabled_networks ?? [
					'facebook',
					'twitter',
					'linkedin',
				],
				network_order: networksResponse.network_order ?? [],
				custom_networks: networksResponse.custom_networks ?? [],

				// Profile Settings
				profiles: response.profiles ?? [],
				default_profile: response.default_profile ?? '',

				// Integrations
				betterlinks_enabled:
					integrationsResponse.betterlinks_enabled ?? false,
				betterlinks_shorten_urls:
					integrationsResponse.betterlinks_shorten_urls ?? true,
				betterlinks_add_tracking:
					integrationsResponse.betterlinks_add_tracking ?? true,
				betterlinks_custom_tracking:
					integrationsResponse.betterlinks_custom_tracking ?? {},
				betterlinks_available:
					integrationsResponse.betterlinks_available ?? false,
				betterlinks_pro: integrationsResponse.betterlinks_pro ?? false,
				betterlinks_version:
					integrationsResponse.betterlinks_version ?? null,
				elementor_enabled:
					integrationsResponse.elementor_enabled ?? false,
				divi_enabled: integrationsResponse.divi_enabled ?? false,
				beaver_builder_enabled:
					integrationsResponse.beaver_builder_enabled ?? false,

				// Advanced
				google_analytics: advancedResponse.google_analytics ?? false,
				auto_hide_buttons: advancedResponse.auto_hide_buttons ?? false,
				use_port_in_url: advancedResponse.use_port_in_url ?? false,
				nofollow_links: advancedResponse.nofollow_links ?? true,
				cache_enabled: advancedResponse.cache_enabled ?? true,
				cache_duration: advancedResponse.cache_duration ?? 3600,
				debug_mode: advancedResponse.debug_mode ?? false,
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
					},
					integrations: {
						betterlinks_enabled: settings.betterlinks_enabled,
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
					// Persist profiles and the default profile
					profiles: settings.profiles,
					default_profile: settings.default_profile,
				};

				const response = ( await apiFetch( {
					path: '/html-social-share/v1/settings',
					method: 'POST',
					data: apiData,
				} ) ) as any;

				setIsDirty( false );

				return {
					success: response.success ?? true,
					message: response.message ?? 'Settings saved successfully',
					updated_settings: response.updated ?? {},
				};
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
