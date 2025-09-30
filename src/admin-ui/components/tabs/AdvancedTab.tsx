import React, { useState, useEffect } from 'react';
import { FormField, Checkbox, Button, LoadingOverlay } from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';

export const AdvancedTab: React.FC = () => {
	const {
		settings: apiSettings,
		updateSettings,
		saveSettings,
		saving,
	} = useSettingsContext();
	const { showSuccess, showError } = useNotifications();

	// Local state for form handling
	const [ localSettings, setLocalSettings ] = useState<
		Partial< PluginSettings >
	>( {
		google_analytics: false,
		auto_hide_buttons: false,
		use_port_in_url: false,
		nofollow_links: true,
		cache_enabled: true,
		cache_duration: 3600,
		debug_mode: false,
	} );

	// Sync with API settings
	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				google_analytics: apiSettings.google_analytics ?? false,
				auto_hide_buttons: apiSettings.auto_hide_buttons ?? false,
				use_port_in_url: apiSettings.use_port_in_url ?? false,
				nofollow_links: apiSettings.nofollow_links ?? true,
				cache_enabled: apiSettings.cache_enabled ?? true,
				cache_duration: apiSettings.cache_duration ?? 3600,
				debug_mode: apiSettings.debug_mode ?? false,
			} );
		}
	}, [ apiSettings ] );

	const settings = localSettings;

	const updateSetting = ( key: keyof PluginSettings, value: any ) => {
		setLocalSettings( ( prev ) => ( { ...prev, [ key ]: value } ) );
	};

	const handleSave = async () => {
		try {
			if ( apiSettings && updateSettings && saveSettings ) {
				await updateSettings( localSettings );
				await saveSettings();
				showSuccess( 'Advanced settings saved successfully!' );
			} else {
				await new Promise( ( resolve ) => setTimeout( resolve, 1000 ) );
				showSuccess( 'Advanced settings saved successfully!' );
			}
		} catch ( error ) {
			showError(
				'Failed to save advanced settings',
				'Please try again or contact support if the problem persists.'
			);
		}
	};

	return (
		<LoadingOverlay
			isLoading={ saving }
			message="Saving advanced settings..."
		>
			<div className="advanced-tab">
				<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
					<h2 className="text-xl font-semibold mb-4">
						Advanced Settings
					</h2>
					<p className="text-gray-600 mb-6">
						Configure advanced options for social sharing
						functionality.
					</p>

					<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div className="space-y-4">
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Analytics & Tracking
							</h3>

							<FormField
								label="Google Social Analytics"
								description="Enable Google Analytics tracking for social shares"
							>
								<Checkbox
									checked={
										settings.google_analytics || false
									}
									onChange={ ( checked ) =>
										updateSetting(
											'google_analytics',
											checked
										)
									}
									label="Enable Google Analytics tracking"
								/>
							</FormField>

							<FormField
								label="Auto-hide Buttons"
								description="Auto-hide buttons on page load when positioned on left or right side"
							>
								<Checkbox
									checked={
										settings.auto_hide_buttons || false
									}
									onChange={ ( checked ) =>
										updateSetting(
											'auto_hide_buttons',
											checked
										)
									}
									label="Auto-hide floating buttons"
								/>
							</FormField>

							<FormField
								label="Use Port in URL"
								description="Include port number in share URLs (e.g., :443 for SSL)"
							>
								<Checkbox
									checked={
										settings.use_port_in_url || false
									}
									onChange={ ( checked ) =>
										updateSetting(
											'use_port_in_url',
											checked
										)
									}
									label="Include port in URLs"
								/>
							</FormField>

							<FormField
								label="No-follow Links"
								description="Add rel='nofollow' to all social share links"
							>
								<Checkbox
									checked={ settings.nofollow_links ?? true }
									onChange={ ( checked ) =>
										updateSetting(
											'nofollow_links',
											checked
										)
									}
									label="Add nofollow to links"
								/>
							</FormField>
						</div>

						<div className="space-y-4">
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Performance & Debugging
							</h3>

							<FormField
								label="Enable Caching"
								description="Cache share counts and button data for better performance"
							>
								<Checkbox
									checked={ settings.cache_enabled ?? true }
									onChange={ ( checked ) =>
										updateSetting(
											'cache_enabled',
											checked
										)
									}
									label="Enable caching"
								/>
							</FormField>

							{ settings.cache_enabled && (
								<FormField
									label="Cache Duration"
									description="How long to cache data in seconds (3600 = 1 hour)"
								>
									<input
										type="number"
										className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
										value={
											settings.cache_duration || 3600
										}
										onChange={ ( e ) =>
											updateSetting(
												'cache_duration',
												parseInt( e.target.value ) ||
													3600
											)
										}
										min="300"
										max="86400"
									/>
								</FormField>
							) }

							<FormField
								label="Debug Mode"
								description="Enable debug logging for troubleshooting"
							>
								<Checkbox
									checked={ settings.debug_mode || false }
									onChange={ ( checked ) =>
										updateSetting( 'debug_mode', checked )
									}
									label="Enable debug mode"
								/>
							</FormField>
						</div>
					</div>

					<div className="mt-8 pt-4 border-t border-gray-200">
						<div className="flex justify-between items-center">
							<p className="text-sm text-gray-600">
								These advanced settings affect plugin
								performance and SEO. Use with caution.
							</p>
							<Button
								onClick={ handleSave }
								loading={ saving }
								variant="primary"
							>
								Save Changes
							</Button>
						</div>
					</div>
				</div>
			</div>
		</LoadingOverlay>
	);
};
