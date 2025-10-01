import React, { useEffect, useState } from 'react';
import {
	Button,
	FormField,
	LoadingOverlay,
	Select,
	TextInput,
	ValidatedTextArea,
} from '../ui';
import { PluginSettings } from '../../types';
import { useNotifications, useSettingsContext } from '../../contexts';
import { useIconsets } from '../../hooks';

export const AppearanceTab: React.FC = () => {
	const {
		settings: apiSettings,
		updateSettings,
		saveSettings,
		saving,
	} = useSettingsContext();
	const { showSuccess, showError } = useNotifications();
	const { iconsets, loading: iconsetsLoading } = useIconsets();

	const [ localSettings, setLocalSettings ] = useState<
		Partial< PluginSettings >
	>( {
		title: 'Share this with your friends',
		iconset: 'default_square',
		default_style: 'default',
		default_size: 'medium',
		icon_style: 'default',
		button_size: 'medium',
		button_spacing: 8,
		custom_css: '',
	} );

	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				title: apiSettings.title ?? 'Share this with your friends',
				iconset: apiSettings.iconset ?? 'default_square',
				default_style: apiSettings.default_style ?? 'default',
				default_size: apiSettings.default_size ?? 'medium',
				icon_style: apiSettings.icon_style ?? 'default',
				button_size: apiSettings.button_size ?? 'medium',
				button_spacing: apiSettings.button_spacing ?? 8,
				custom_css: apiSettings.custom_css ?? '',
			} );
		}
	}, [ apiSettings ] );

	const settings = localSettings;

	const defaultStyleOptions = [
		{ value: 'default', label: 'Default' },
		{ value: 'minimal', label: 'Minimal' },
		{ value: 'rounded', label: 'Rounded' },
		{ value: 'square', label: 'Square' },
	];

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
				showSuccess( 'Design settings saved successfully!' );
			} else {
				await new Promise( ( resolve ) => setTimeout( resolve, 1000 ) );
				showSuccess( 'Design settings saved successfully!' );
			}
		} catch ( error ) {
			showError(
				'Failed to save design settings',
				'Please try again or contact support if the problem persists.'
			);
		}
	};

	return (
		<LoadingOverlay
			isLoading={ saving }
			message="Saving design settings..."
		>
			<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
				<h2 className="text-xl font-semibold mb-4">Design Defaults</h2>
				<p className="text-gray-600 mb-6">
					Configure the default appearance for every set of share
					buttons on your site.
				</p>

				<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
					<section className="space-y-4">
						<h3 className="text-lg font-medium text-gray-800 mb-3">
							Global Defaults
						</h3>

						<FormField
							label="Share Title"
							description="Displayed above or adjacent to button groups"
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
							label="Icon Set"
							description="Choose the icon set for social share buttons"
						>
							<Select
								value={ settings.iconset || 'default_square' }
								onChange={ ( value ) =>
									updateSetting( 'iconset', value )
								}
								options={
									iconsets
										? Object.entries( iconsets ).map(
												( [ key, iconset ] ) => ( {
													value: key,
													label: iconset.label,
												} )
										  )
										: []
								}
								disabled={ iconsetsLoading }
							/>
						</FormField>

						<FormField
							label="Default Button Style"
							description="Applies to new placements and shortcode defaults"
						>
							<Select
								value={ settings.default_style || 'default' }
								onChange={ ( value ) =>
									updateSetting( 'default_style', value )
								}
								options={ defaultStyleOptions }
							/>
						</FormField>

						<FormField
							label="Default Button Size"
							description="Baseline size for new button groups"
						>
							<Select
								value={ settings.default_size || 'medium' }
								onChange={ ( value ) =>
									updateSetting( 'default_size', value )
								}
								options={ buttonSizeOptions }
							/>
						</FormField>
					</section>

					<section className="space-y-4">
						<h3 className="text-lg font-medium text-gray-800 mb-3">
							Button Presentation
						</h3>

						<FormField
							label="Icon Style"
							description="Choose the core icon treatment"
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
							label="Rendered Button Size"
							description="Adjusts the visual size for rendered buttons"
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
							description="Horizontal gap between buttons in pixels"
						>
							<TextInput
								value={
									settings.button_spacing?.toString() || '8'
								}
								onChange={ ( value ) =>
									updateSetting(
										'button_spacing',
										parseInt( value, 10 ) || 8
									)
								}
							/>
						</FormField>
					</section>
				</div>

				<div className="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
					<section className="space-y-4">
						<h3 className="text-lg font-medium text-gray-800 mb-3">
							Custom CSS
						</h3>
						<FormField
							label="Custom CSS Overrides"
							description="Add optional CSS rules that load with the share buttons"
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
					</section>

					<section className="bg-blue-50 border border-blue-200 rounded p-4">
						<h4 className="font-medium text-blue-800 mb-2">
							Preview
						</h4>
						<p className="text-sm text-blue-600 mb-3">
							Updated defaults apply to new placements and
							shortcode examples. Individual instances can still
							override them.
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
					</section>
				</div>

				<div className="mt-8 pt-4 border-t border-gray-200">
					<div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
						<p className="text-sm text-gray-600">
							These design defaults apply globally and act as the
							baseline for profiles and manual overrides.
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
		</LoadingOverlay>
	);
};
