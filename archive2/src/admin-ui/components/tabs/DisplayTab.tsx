import React, { useEffect, useState } from 'react';
import { Button, Checkbox, FormField, LoadingOverlay } from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';

type LegacyDisplaySettings = Pick<
	PluginSettings,
	'floating_left' | 'floating_right' | 'before_content' | 'after_content'
>;

const defaultLegacyDisplaySettings: LegacyDisplaySettings = {
	floating_left: true, // Legacy default: show_left was true
	floating_right: false,
	before_content: false,
	after_content: true, // Legacy default: show_after_post was true
};

export const DisplayTab: React.FC = () => {
	const {
		settings: apiSettings,
		updateSettings,
		saveSettings,
		saving,
	} = useSettingsContext();
	const { showSuccess, showError } = useNotifications();

	const [ localSettings, setLocalSettings ] =
		useState< LegacyDisplaySettings >( defaultLegacyDisplaySettings );

	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				floating_left:
					apiSettings.floating_left ??
					defaultLegacyDisplaySettings.floating_left,
				floating_right:
					apiSettings.floating_right ??
					defaultLegacyDisplaySettings.floating_right,
				before_content:
					apiSettings.before_content ??
					defaultLegacyDisplaySettings.before_content,
				after_content:
					apiSettings.after_content ??
					defaultLegacyDisplaySettings.after_content,
			} );
		}
	}, [ apiSettings ] );

	const updateLocal = < K extends keyof LegacyDisplaySettings >(
		key: K,
		value: LegacyDisplaySettings[ K ]
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
			showSuccess( 'Display settings saved successfully!' );
		} catch ( error ) {
			showError(
				'Failed to save display settings',
				'Please try again or contact support if the problem persists.'
			);
		}
	};

	return (
		<LoadingOverlay
			isLoading={ saving }
			message="Saving display settings..."
		>
			<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
				<h2 className="text-xl font-semibold text-gray-900 mb-2">
					Display &amp; Placement
				</h2>
				<p className="text-sm text-gray-600 mb-6">
					Choose where social share buttons should appear on your
					site.
				</p>

				<div className="space-y-6">
					<div>
						<h3 className="text-lg font-medium text-gray-900 mb-4">
							Automatic Placement
						</h3>
						<p className="text-sm text-gray-600 mb-4">
							Enable automatic placement to display share buttons
							without editing templates.
						</p>

						<div className="space-y-4">
							<FormField
								label="Show on Left Side"
								description="Display floating share buttons on the left side of the page"
							>
								<Checkbox
									checked={ localSettings.floating_left }
									onChange={ ( checked ) =>
										updateLocal( 'floating_left', checked )
									}
									label="Enable left floating buttons"
								/>
							</FormField>

							<FormField
								label="Show on Right Side"
								description="Display floating share buttons on the right side of the page"
							>
								<Checkbox
									checked={ localSettings.floating_right }
									onChange={ ( checked ) =>
										updateLocal( 'floating_right', checked )
									}
									label="Enable right floating buttons"
								/>
							</FormField>

							<FormField
								label="Show Before Post"
								description="Display share buttons before post content"
							>
								<Checkbox
									checked={ localSettings.before_content }
									onChange={ ( checked ) =>
										updateLocal( 'before_content', checked )
									}
									label="Enable before content placement"
								/>
							</FormField>

							<FormField
								label="Show After Post"
								description="Display share buttons after post content"
							>
								<Checkbox
									checked={ localSettings.after_content }
									onChange={ ( checked ) =>
										updateLocal( 'after_content', checked )
									}
									label="Enable after content placement"
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
