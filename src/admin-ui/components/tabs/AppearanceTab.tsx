import React, { useEffect, useState } from 'react';
import {
	Button,
	Checkbox,
	FormField,
	LoadingOverlay,
	Select,
	TextInput,
	ValidatedTextArea,
} from '../ui';
import { PluginSettings } from '../../types';
import { useNotifications, useSettingsContext } from '../../contexts';
import { useIconsets } from '../../hooks';

type LegacyDesignSettings = Pick<
	PluginSettings,
	'title' | 'exclude_pages' | 'iconset'
>;

const defaultLegacyDesignSettings: LegacyDesignSettings = {
	title: 'Share this with your friends',
	exclude_pages: '',
	iconset: 'default_square',
};

export const AppearanceTab: React.FC = () => {
	const {
		settings: apiSettings,
		updateSettings,
		saveSettings,
		saving,
	} = useSettingsContext();
	const { showSuccess, showError } = useNotifications();
	const { iconsets, loading: iconsetsLoading } = useIconsets();

	const [ localSettings, setLocalSettings ] = useState< LegacyDesignSettings >(
		defaultLegacyDesignSettings
	);

	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				title: apiSettings.title ?? defaultLegacyDesignSettings.title,
				exclude_pages:
					apiSettings.exclude_pages ??
					defaultLegacyDesignSettings.exclude_pages,
				iconset:
					apiSettings.iconset ?? defaultLegacyDesignSettings.iconset,
			} );
		}
	}, [ apiSettings ] );

	const updateLocal = < K extends keyof LegacyDesignSettings >(
		key: K,
		value: LegacyDesignSettings[ K ]
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
			showSuccess( 'Design settings saved successfully!' );
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
				<h2 className="text-xl font-semibold text-gray-900 mb-2">
					Design &amp; Settings
				</h2>
				<p className="text-sm text-gray-600 mb-6">
					Configure the appearance and basic settings for social share
					buttons.
				</p>

				<div className="space-y-6">
					<section>
						<h3 className="text-lg font-medium text-gray-900 mb-4">
							Basic Settings
						</h3>

						<div className="space-y-4">
							<FormField
								label="Share Title"
								description="Text displayed with the share buttons"
							>
								<TextInput
									value={ localSettings.title }
									onChange={ ( value ) =>
										updateLocal( 'title', value )
									}
									placeholder="Share this with your friends"
								/>
							</FormField>

							<FormField
								label="Icon Set"
								description="Choose the icon set for social share buttons"
							>
								<Select
									value={ localSettings.iconset }
									onChange={ ( value ) =>
										updateLocal( 'iconset', value )
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
								label="Exclude Pages"
								description="Comma-separated list of page IDs, slugs, or titles to exclude from showing buttons"
							>
								<ValidatedTextArea
									label=""
									value={ localSettings.exclude_pages }
									onChange={ ( value ) =>
										updateLocal( 'exclude_pages', value )
									}
									placeholder="1, about-us, contact, privacy-policy"
									rows={ 3 }
								/>
							</FormField>
						</div>
					</section>

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
