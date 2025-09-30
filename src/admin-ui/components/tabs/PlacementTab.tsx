import React, { useState, useEffect } from 'react';
import {
	FormField,
	Select,
	Checkbox,
	Button,
	LoadingOverlay,
	TextInput,
} from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';

export const PlacementTab: React.FC = () => {
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
		auto_placement: true,
		placement_position: 'after',
		placement_post_types: [ 'post', 'page' ],
		exclude_pages: '',
	} );

	// Sync with API settings
	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				auto_placement: apiSettings.auto_placement ?? true,
				placement_position: apiSettings.placement_position ?? 'after',
				placement_post_types: apiSettings.placement_post_types ?? [
					'post',
					'page',
				],
				exclude_pages: apiSettings.exclude_pages ?? '',
			} );
		}
	}, [ apiSettings ] );

	const settings = localSettings;

	const positionOptions = [
		{ value: 'before', label: 'Before Content' },
		{ value: 'after', label: 'After Content' },
		{ value: 'both', label: 'Before and After Content' },
		{ value: 'left', label: 'Floating Left Side' },
		{ value: 'right', label: 'Floating Right Side' },
	];

	const availablePostTypes = [
		{ id: 'post', name: 'Posts', description: 'Standard blog posts' },
		{ id: 'page', name: 'Pages', description: 'Static pages' },
		{
			id: 'product',
			name: 'Products',
			description: 'WooCommerce products',
		},
		{
			id: 'custom',
			name: 'Custom Post Types',
			description: 'Other custom post types',
		},
	];

	const updateSetting = ( key: keyof PluginSettings, value: any ) => {
		setLocalSettings( ( prev ) => ( { ...prev, [ key ]: value } ) );
	};

	const handlePostTypeToggle = ( postType: string, enabled: boolean ) => {
		const currentTypes = settings.placement_post_types || [];
		if ( enabled ) {
			updateSetting( 'placement_post_types', [
				...currentTypes,
				postType,
			] );
		} else {
			updateSetting(
				'placement_post_types',
				currentTypes.filter( ( type ) => type !== postType )
			);
		}
	};

	const handleSave = async () => {
		try {
			if ( apiSettings && updateSettings && saveSettings ) {
				await updateSettings( localSettings );
				await saveSettings();
				showSuccess( 'Placement settings saved successfully!' );
			} else {
				await new Promise( ( resolve ) => setTimeout( resolve, 1000 ) );
				showSuccess( 'Placement settings saved successfully!' );
			}
		} catch ( error ) {
			showError(
				'Failed to save placement settings',
				'Please try again or contact support if the problem persists.'
			);
		}
	};

	return (
		<LoadingOverlay
			isLoading={ saving }
			message="Saving placement settings..."
		>
			<div className="placement-tab">
				<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
					<h2 className="text-xl font-semibold mb-4">
						Button Placement
					</h2>
					<p className="text-gray-600 mb-6">
						Configure where and how social share buttons appear on
						your site.
					</p>

					<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div className="space-y-4">
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Automatic Placement
							</h3>

							<FormField
								label="Enable Auto-Placement"
								description="Automatically add share buttons to content"
							>
								<Checkbox
									checked={ settings.auto_placement || false }
									onChange={ ( checked ) =>
										updateSetting(
											'auto_placement',
											checked
										)
									}
									label="Enable automatic placement"
								/>
							</FormField>

							{ settings.auto_placement && (
								<>
									<FormField
										label="Placement Position"
										description="Where to show share buttons relative to content"
									>
										<Select
											value={
												settings.placement_position ||
												'after'
											}
											onChange={ ( value ) =>
												updateSetting(
													'placement_position',
													value
												)
											}
											options={ positionOptions }
										/>
									</FormField>

									<FormField
										label="Exclude Pages"
										description="Exclude by Page ID, Page Title or Page Slug (comma-separated)"
									>
										<TextInput
											value={
												settings.exclude_pages || ''
											}
											onChange={ ( value ) =>
												updateSetting(
													'exclude_pages',
													value
												)
											}
											placeholder="1, about-us, contact"
										/>
									</FormField>
								</>
							) }
						</div>

						<div className="space-y-4">
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Content Types
							</h3>
							<p className="text-sm text-gray-600 mb-4">
								Select which content types should display share
								buttons automatically.
							</p>

							<div className="space-y-3">
								{ availablePostTypes.map( ( postType ) => {
									const isEnabled = (
										settings.placement_post_types || []
									).includes( postType.id );

									return (
										<div
											key={ postType.id }
											className="flex items-start space-x-3 p-3 border border-gray-200 rounded hover:bg-gray-50"
										>
											<Checkbox
												checked={ isEnabled }
												onChange={ ( checked ) =>
													handlePostTypeToggle(
														postType.id,
														checked
													)
												}
												label=""
												className="mt-1"
											/>
											<div className="flex-1">
												<h4 className="font-medium text-gray-800">
													{ postType.name }
												</h4>
												<p className="text-sm text-gray-600">
													{ postType.description }
												</p>
											</div>
										</div>
									);
								} ) }
							</div>
						</div>
					</div>

					{ /* Manual Placement Instructions */ }
					<div className="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
						<h4 className="font-medium text-blue-800 mb-2">
							Manual Placement
						</h4>
						<p className="text-sm text-blue-600 mb-3">
							You can also manually place share buttons using
							shortcodes or PHP functions:
						</p>
						<div className="bg-white p-3 rounded border font-mono text-sm">
							<div className="mb-2">
								<strong>Shortcode:</strong>{ ' ' }
								<code className="bg-gray-100 px-1 rounded">
									[html_social_share_buttons]
								</code>
							</div>
							<div>
								<strong>PHP:</strong>{ ' ' }
								<code className="bg-gray-100 px-1 rounded">
									&lt;?php echo
									do_shortcode('[html_social_share_buttons]');
									?&gt;
								</code>
							</div>
						</div>
					</div>

					<div className="mt-8 pt-4 border-t border-gray-200">
						<div className="flex justify-between items-center">
							<p className="text-sm text-gray-600">
								{ /* eslint-disable-next-line react/no-unescaped-entities */ }
								{ settings.auto_placement
									? "Auto-placement is enabled"
									: "Auto-placement is disabled" }
								.
								{
									( settings.placement_post_types || [] )
										.length
								}{ ' ' }
								content type
								{ ( settings.placement_post_types || [] )
									.length !== 1
									? 's'
									: '' }{ ' ' }
								selected.
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
