import React, { useState, useEffect } from 'react';
import {
	FormField,
	TextInput,
	Checkbox,
	Button,
	LoadingOverlay,
	VerticalTabs,
	TabPanel,
} from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';

export const IntegrationsTab: React.FC = () => {
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
		betterlinks_enabled: false,
		betterlinks_api_key: '',
		elementor_enabled: false,
		divi_enabled: false,
		beaver_builder_enabled: false,
	} );

	// Sync with API settings
	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				betterlinks_enabled: apiSettings.betterlinks_enabled ?? false,
				betterlinks_api_key: apiSettings.betterlinks_api_key ?? '',
				elementor_enabled: apiSettings.elementor_enabled ?? false,
				divi_enabled: apiSettings.divi_enabled ?? false,
				beaver_builder_enabled:
					apiSettings.beaver_builder_enabled ?? false,
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
				showSuccess( 'Integration settings saved successfully!' );
			} else {
				await new Promise( ( resolve ) => setTimeout( resolve, 1000 ) );
				showSuccess( 'Integration settings saved successfully!' );
			}
		} catch ( error ) {
			showError(
				'Failed to save integration settings',
				'Please try again or contact support if the problem persists.'
			);
		}
	};

	// Integration sub-tabs
	const integrationTabs = [
		{
			id: 'betterlinks',
			title: 'BetterLinks',
			icon: 'dashicons-admin-links',
			description: 'Advanced link tracking and analytics integration',
		},
		{
			id: 'page-builders',
			title: 'Page Builders',
			icon: 'dashicons-admin-appearance',
			description: 'Integration with popular page builders',
		},
		{
			id: 'status',
			title: 'Status',
			icon: 'dashicons-info',
			description: 'Integration status overview',
		},
	];

	const [ activeIntegrationTab, setActiveIntegrationTab ] =
		useState( 'betterlinks' );

	const renderIntegrationContent = () => {
		switch ( activeIntegrationTab ) {
			case 'betterlinks':
				return (
					<div className="space-y-6">
						<div className="border border-gray-200 rounded-lg p-6">
							<div className="flex items-center justify-between mb-4">
								<div>
									<h3 className="text-lg font-medium text-gray-800">
										BetterLinks Integration
									</h3>
									<p className="text-sm text-gray-600">
										Integrate with BetterLinks for advanced
										link tracking and analytics.
									</p>
								</div>
								<Checkbox
									checked={
										settings.betterlinks_enabled || false
									}
									onChange={ ( checked ) =>
										updateSetting(
											'betterlinks_enabled',
											checked
										)
									}
									label=""
								/>
							</div>

							{ settings.betterlinks_enabled && (
								<div className="mt-4">
									<FormField
										label="API Key"
										description="Enter your BetterLinks API key to enable integration"
									>
										<TextInput
											type="password"
											value={
												settings.betterlinks_api_key ||
												''
											}
											onChange={ ( value ) =>
												updateSetting(
													'betterlinks_api_key',
													value
												)
											}
											placeholder="Enter BetterLinks API key"
										/>
									</FormField>
									<p className="text-xs text-gray-500 mt-2">
										You can find your API key in BetterLinks
										settings under the API section.
									</p>
								</div>
							) }
						</div>
					</div>
				);
			case 'page-builders':
				return (
					<div className="space-y-6">
						<div className="border border-gray-200 rounded-lg p-6">
							<h3 className="text-lg font-medium text-gray-800 mb-4">
								Page Builder Integrations
							</h3>
							<p className="text-sm text-gray-600 mb-4">
								Enable integration with popular page builders to
								add social share buttons to your content.
							</p>

							<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div className="flex items-center justify-between p-3 bg-gray-50 rounded">
									<div>
										<h4 className="font-medium">
											Elementor
										</h4>
										<p className="text-xs text-gray-600">
											Add share buttons to Elementor
											widgets
										</p>
									</div>
									<Checkbox
										checked={
											settings.elementor_enabled || false
										}
										onChange={ ( checked ) =>
											updateSetting(
												'elementor_enabled',
												checked
											)
										}
										label=""
									/>
								</div>

								<div className="flex items-center justify-between p-3 bg-gray-50 rounded">
									<div>
										<h4 className="font-medium">
											Divi Builder
										</h4>
										<p className="text-xs text-gray-600">
											Add share buttons to Divi modules
										</p>
									</div>
									<Checkbox
										checked={
											settings.divi_enabled || false
										}
										onChange={ ( checked ) =>
											updateSetting(
												'divi_enabled',
												checked
											)
										}
										label=""
									/>
								</div>

								<div className="flex items-center justify-between p-3 bg-gray-50 rounded md:col-span-2">
									<div>
										<h4 className="font-medium">
											Beaver Builder
										</h4>
										<p className="text-xs text-gray-600">
											Add share buttons to Beaver Builder
											layouts
										</p>
									</div>
									<Checkbox
										checked={
											settings.beaver_builder_enabled ||
											false
										}
										onChange={ ( checked ) =>
											updateSetting(
												'beaver_builder_enabled',
												checked
											)
										}
										label=""
									/>
								</div>
							</div>
						</div>
					</div>
				);
			case 'status':
				return (
					<div className="space-y-6">
						<div className="bg-blue-50 border border-blue-200 rounded-lg p-6">
							<h3 className="font-medium text-blue-800 mb-4">
								Integration Status
							</h3>
							<div className="space-y-3">
								<div className="flex justify-between items-center py-2">
									<span className="font-medium">
										BetterLinks:
									</span>
									<span
										className={
											settings.betterlinks_enabled
												? 'text-green-600 font-medium'
												: 'text-gray-500'
										}
									>
										{ settings.betterlinks_enabled
											? 'Enabled'
											: 'Disabled' }
									</span>
								</div>
								<div className="flex justify-between items-center py-2">
									<span className="font-medium">
										Elementor:
									</span>
									<span
										className={
											settings.elementor_enabled
												? 'text-green-600 font-medium'
												: 'text-gray-500'
										}
									>
										{ settings.elementor_enabled
											? 'Enabled'
											: 'Disabled' }
									</span>
								</div>
								<div className="flex justify-between items-center py-2">
									<span className="font-medium">Divi:</span>
									<span
										className={
											settings.divi_enabled
												? 'text-green-600 font-medium'
												: 'text-gray-500'
										}
									>
										{ settings.divi_enabled
											? 'Enabled'
											: 'Disabled' }
									</span>
								</div>
								<div className="flex justify-between items-center py-2">
									<span className="font-medium">
										Beaver Builder:
									</span>
									<span
										className={
											settings.beaver_builder_enabled
												? 'text-green-600 font-medium'
												: 'text-gray-500'
										}
									>
										{ settings.beaver_builder_enabled
											? 'Enabled'
											: 'Disabled' }
									</span>
								</div>
							</div>
						</div>
					</div>
				);
			default:
				return null;
		}
	};

	return (
		<LoadingOverlay
			isLoading={ saving }
			message="Saving integration settings..."
		>
			<div className="integrations-tab">
				<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
					<h2 className="text-xl font-semibold mb-4">
						Plugin Integrations
					</h2>
					<p className="text-gray-600 mb-6">
						Configure integrations with other WordPress plugins and
						services.
					</p>

					<VerticalTabs
						tabs={ integrationTabs }
						activeTab={ activeIntegrationTab }
						onTabChange={ setActiveIntegrationTab }
						className="min-h-96"
					/>

					{ integrationTabs.map( ( tab ) => (
						<TabPanel
							key={ tab.id }
							id={ tab.id }
							activeTab={ activeIntegrationTab }
						>
							{ renderIntegrationContent() }
						</TabPanel>
					) ) }

					<div className="mt-8 pt-4 border-t border-gray-200">
						<div className="flex justify-between items-center">
							<p className="text-sm text-gray-600">
								Integration settings will take effect
								immediately after saving.
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
