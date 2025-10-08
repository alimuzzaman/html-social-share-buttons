import React, { useState, useEffect } from 'react';
import { FormField, Checkbox, Button, LoadingOverlay } from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';

type LegacyAdvancedSettings = Pick<
	PluginSettings,
	'google_analytics' | 'auto_hide_buttons' | 'use_port_in_url' | 'nofollow_links'
>;

const defaultLegacyAdvancedSettings: LegacyAdvancedSettings = {
	google_analytics: false,
	auto_hide_buttons: false,
	use_port_in_url: false,
	nofollow_links: true,
};

export const AdvancedTab: React.FC = () => {
	const {
		settings: apiSettings,
		updateSettings,
		saveSettings,
		saving,
	} = useSettingsContext();
	const { showSuccess, showError } = useNotifications();

	const [ localSettings, setLocalSettings ] = useState< LegacyAdvancedSettings >(
		defaultLegacyAdvancedSettings
	);

	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				google_analytics:
					apiSettings.google_analytics ??
					defaultLegacyAdvancedSettings.google_analytics,
				auto_hide_buttons:
					apiSettings.auto_hide_buttons ??
					defaultLegacyAdvancedSettings.auto_hide_buttons,
				use_port_in_url:
					apiSettings.use_port_in_url ??
					defaultLegacyAdvancedSettings.use_port_in_url,
				nofollow_links:
					apiSettings.nofollow_links ??
					defaultLegacyAdvancedSettings.nofollow_links,
			} );
		}
	}, [ apiSettings ] );

	const updateLocal = < K extends keyof LegacyAdvancedSettings >(
		key: K,
		value: LegacyAdvancedSettings[ K ]
	) => {
		setLocalSettings( ( prev ) => ( {
			...prev,
			[ key ]: value,
		} ) );
	};

	const handleSave = async () => {
		try {
			await updateSettings( localSettings );
			await saveSettings();
			showSuccess( 'Advanced settings saved successfully!' );
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
			<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
				<h2 className="text-xl font-semibold text-gray-900 mb-2">
					Advanced Settings
				</h2>
				<p className="text-sm text-gray-600 mb-6">
					Configure advanced options for social sharing functionality.
				</p>

				<div className="space-y-6">
					<div>
						<h3 className="text-lg font-medium text-gray-900 mb-4">
							Analytics &amp; Behavior
						</h3>

						<div className="space-y-4">
							<FormField
								label="Google Social Analytics"
								description="Enable Google Analytics tracking for social shares"
							>
								<Checkbox
									checked={ localSettings.google_analytics }
									onChange={ ( checked ) =>
										updateLocal( 'google_analytics', checked )
									}
									label="Enable Google Analytics tracking"
								/>
							</FormField>

							<FormField
								label="Auto-hide Buttons"
								description="Auto-hide buttons on page load when positioned on left or right side"
							>
								<Checkbox
									checked={ localSettings.auto_hide_buttons }
									onChange={ ( checked ) =>
										updateLocal( 'auto_hide_buttons', checked )
									}
									label="Auto-hide floating buttons"
								/>
							</FormField>

							<FormField
								label="Use Port in URL"
								description="Include port number in share URLs (e.g., :443 for SSL)"
							>
								<Checkbox
									checked={ localSettings.use_port_in_url }
									onChange={ ( checked ) =>
										updateLocal( 'use_port_in_url', checked )
									}
									label="Include port in URLs"
								/>
							</FormField>

							<FormField
								label="No-follow Links"
								description="Add rel='nofollow' to all social share links"
							>
								<Checkbox
									checked={ localSettings.nofollow_links }
									onChange={ ( checked ) =>
										updateLocal( 'nofollow_links', checked )
									}
									label="Add nofollow to links"
								/>
							</FormField>
						</div>
					</div>

					<div className="flex justify-end pt-6 border-t border-gray-200">
						<Button
							onClick={ handleSave }
							disabled={ saving }
							className="px-6 py-2"
						>
							{ saving ? 'Saving...' : 'Save Settings' }
						</Button>
					</div>
				</div>
			</div>
		</LoadingOverlay>
	);
};
