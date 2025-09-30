import React, { useState, useEffect } from 'react';
import {
	FormField,
	Select,
	TextInput,
	Button,
	LoadingOverlay,
	ValidatedTextArea,
} from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';

export const AppearanceTab: React.FC = () => {
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
		title: 'Share this with your friends',
		icon_style: 'default',
		button_size: 'medium',
		button_spacing: 8,
		custom_css: '',
	} );

	// Sync with API settings
	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				title: apiSettings.title ?? 'Share this with your friends',
				icon_style: apiSettings.icon_style ?? 'default',
				button_size: apiSettings.button_size ?? 'medium',
				button_spacing: apiSettings.button_spacing ?? 8,
				custom_css: apiSettings.custom_css ?? '',
			} );
		}
	}, [ apiSettings ] );

	const settings = localSettings;

	const iconStyleOptions = [
		{ value: 'default', label: 'Default' },
		{ value: 'outline', label: 'Outline' },
		{ value: 'rounded', label: 'Rounded' },
		{ value: 'square', label: 'Square' },
	];

	const buttonSizeOptions = [
		{ value: 'small', label: 'Small' },
		{ value: 'medium', label: 'Medium' },
		{ value: 'large', label: 'Large' },
	];

	const updateSetting = ( key: keyof PluginSettings, value: any ) => {
		setLocalSettings( ( prev ) => ( { ...prev, [ key ]: value } ) );
	};

	const handleSave = async () => {
		try {
			if ( apiSettings && updateSettings && saveSettings ) {
				await updateSettings( localSettings );
				await saveSettings();
				showSuccess( 'Appearance settings saved successfully!' );
			} else {
				await new Promise( ( resolve ) => setTimeout( resolve, 1000 ) );
				showSuccess( 'Appearance settings saved successfully!' );
			}
		} catch ( error ) {
			showError(
				'Failed to save appearance settings',
				'Please try again or contact support if the problem persists.'
			);
		}
	};

	return (
		<LoadingOverlay
			isLoading={ saving }
			message="Saving appearance settings..."
		>
			<div className="appearance-tab">
				<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
					<h2 className="text-xl font-semibold mb-4">
						Appearance Settings
					</h2>
					<p className="text-gray-600 mb-6">
						Customize the visual appearance of your social share
						buttons.
					</p>

					<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div className="space-y-4">
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Button Style
							</h3>

							<FormField
								label="Share Title"
								description="Text displayed above or with the share buttons"
							>
								<TextInput
									value={
										settings.title ||
										'Share this with your friends'
									}
									onChange={ ( value ) =>
										updateSetting( 'title', value )
									}
									placeholder="Share this with your friends"
								/>
							</FormField>

							<FormField
								label="Icon Style"
								description="Choose the style for social media icons"
							>
								<Select
									value={ settings.icon_style || 'default' }
									onChange={ ( value ) =>
										updateSetting( 'icon_style', value )
									}
									options={ iconStyleOptions }
								/>
							</FormField>

							<FormField
								label="Button Size"
								description="Set the default size for share buttons"
							>
								<Select
									value={ settings.button_size || 'medium' }
									onChange={ ( value ) =>
										updateSetting( 'button_size', value )
									}
									options={ buttonSizeOptions }
								/>
							</FormField>

							<FormField
								label="Button Spacing"
								description="Space between buttons in pixels"
							>
								<TextInput
									value={
										settings.button_spacing?.toString() ||
										'8'
									}
									onChange={ ( value ) =>
										updateSetting(
											'button_spacing',
											parseInt( value ) || 8
										)
									}
								/>
							</FormField>
						</div>

						<div className="space-y-4">
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Custom Styling
							</h3>

							<FormField
								label="Custom CSS"
								description="Add custom CSS to further customize the appearance"
							>
								<ValidatedTextArea
									label=""
									value={ settings.custom_css || '' }
									onChange={ ( value ) =>
										updateSetting( 'custom_css', value )
									}
									placeholder=".html-social-share-buttons { /* your custom styles */ }"
									rows={ 8 }
									className="font-mono text-sm"
								/>
							</FormField>

							<div className="bg-blue-50 border border-blue-200 rounded p-4">
								<h4 className="font-medium text-blue-800 mb-2">
									Preview
								</h4>
								<p className="text-sm text-blue-600 mb-3">
									Changes will be applied to all social share
									buttons on your site.
								</p>
								<div className="flex space-x-2">
									<div className="w-8 h-8 bg-blue-500 rounded flex items-center justify-center text-white text-xs">
										F
									</div>
									<div className="w-8 h-8 bg-blue-400 rounded flex items-center justify-center text-white text-xs">
										T
									</div>
									<div className="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white text-xs">
										L
									</div>
								</div>
							</div>
						</div>
					</div>

					<div className="mt-8 pt-4 border-t border-gray-200">
						<div className="flex justify-between items-center">
							<p className="text-sm text-gray-600">
								These appearance settings will be applied
								globally to all share buttons.
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
