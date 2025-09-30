import React, { useEffect, useMemo, useState } from 'react';
import {
	Button,
	Checkbox,
	FormField,
	LoadingOverlay,
	Select,
	TextInput,
} from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';

type DisplaySettings = Pick<
	PluginSettings,
	| 'show_on_front_page'
	| 'show_on_posts'
	| 'show_on_pages'
	| 'show_on_archives'
	| 'auto_placement'
	| 'placement_position'
	| 'placement_post_types'
	| 'exclude_pages'
>;

const defaultDisplaySettings: DisplaySettings = {
	show_on_front_page: true,
	show_on_posts: true,
	show_on_pages: false,
	show_on_archives: false,
	auto_placement: false,
	placement_position: 'after',
	placement_post_types: [ 'post' ],
	exclude_pages: '',
};

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
		description: 'Other registered post types',
	},
];

export const DisplayTab: React.FC = () => {
	const {
		settings: apiSettings,
		updateSettings,
		saveSettings,
		saving,
	} = useSettingsContext();
	const { showSuccess, showError } = useNotifications();

	const [ localSettings, setLocalSettings ] = useState< DisplaySettings >(
		defaultDisplaySettings
	);

	useEffect( () => {
		if ( apiSettings ) {
			setLocalSettings( {
				show_on_front_page:
					apiSettings.show_on_front_page ??
					defaultDisplaySettings.show_on_front_page,
				show_on_posts:
					apiSettings.show_on_posts ??
					defaultDisplaySettings.show_on_posts,
				show_on_pages:
					apiSettings.show_on_pages ??
					defaultDisplaySettings.show_on_pages,
				show_on_archives:
					apiSettings.show_on_archives ??
					defaultDisplaySettings.show_on_archives,
				auto_placement:
					apiSettings.auto_placement ??
					defaultDisplaySettings.auto_placement,
				placement_position:
					apiSettings.placement_position ??
					defaultDisplaySettings.placement_position,
				placement_post_types: apiSettings.placement_post_types?.length
					? apiSettings.placement_post_types
					: defaultDisplaySettings.placement_post_types,
				exclude_pages:
					apiSettings.exclude_pages ??
					defaultDisplaySettings.exclude_pages,
			} );
		}
	}, [ apiSettings ] );

	const selectedPostTypes = useMemo(
		() => new Set( localSettings.placement_post_types ),
		[ localSettings.placement_post_types ]
	);

	const updateLocal = < K extends keyof DisplaySettings >(
		key: K,
		value: DisplaySettings[ K ]
	) => {
		setLocalSettings( ( prev ) => ( {
			...prev,
			[ key ]: value,
		} ) );
	};

	const handlePostTypeToggle = ( postType: string, enabled: boolean ) => {
		const currentTypes = localSettings.placement_post_types ?? [];
		if ( enabled ) {
			const nextTypes = Array.from(
				new Set( [ ...currentTypes, postType ] )
			);
			updateLocal( 'placement_post_types', nextTypes );
			return;
		}

		updateLocal(
			'placement_post_types',
			currentTypes.filter( ( type ) => type !== postType )
		);
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
				<h2 className="text-xl font-semibold mb-4">
					Display &amp; Placement
				</h2>
				<p className="text-gray-600 mb-6">
					Control where social share buttons automatically appear
					across your site.
				</p>

				<div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<section>
						<h3 className="text-lg font-medium text-gray-800 mb-3">
							Display Locations
						</h3>
						<div className="space-y-4">
							<FormField
								label="Show on Front Page"
								description="Display buttons on the site homepage"
							>
								<Checkbox
									checked={ localSettings.show_on_front_page }
									onChange={ ( checked ) =>
										updateLocal(
											'show_on_front_page',
											checked
										)
									}
									label="Enable on front page"
								/>
							</FormField>

							<FormField
								label="Show on Posts"
								description="Automatically append share buttons to posts"
							>
								<Checkbox
									checked={ localSettings.show_on_posts }
									onChange={ ( checked ) =>
										updateLocal( 'show_on_posts', checked )
									}
									label="Enable on posts"
								/>
							</FormField>

							<FormField
								label="Show on Pages"
								description="Include share buttons on static pages"
							>
								<Checkbox
									checked={ localSettings.show_on_pages }
									onChange={ ( checked ) =>
										updateLocal( 'show_on_pages', checked )
									}
									label="Enable on pages"
								/>
							</FormField>

							<FormField
								label="Show on Archives"
								description="Display share buttons on archive and taxonomy listings"
							>
								<Checkbox
									checked={ localSettings.show_on_archives }
									onChange={ ( checked ) =>
										updateLocal(
											'show_on_archives',
											checked
										)
									}
									label="Enable on archives"
								/>
							</FormField>
						</div>
					</section>

					<section>
						<h3 className="text-lg font-medium text-gray-800 mb-3">
							Automatic Placement
						</h3>
						<div className="space-y-4">
							<FormField
								label="Enable Auto Placement"
								description="Automatically attach buttons without editing templates"
							>
								<Checkbox
									checked={ localSettings.auto_placement }
									onChange={ ( checked ) =>
										updateLocal( 'auto_placement', checked )
									}
									label="Enable automatic placement"
								/>
							</FormField>

							{ localSettings.auto_placement && (
								<div className="space-y-4">
									<FormField
										label="Placement Position"
										description="Choose where to output buttons relative to content"
									>
										<Select
											value={
												localSettings.placement_position
											}
											onChange={ ( value ) =>
												updateLocal(
													'placement_position',
													value as DisplaySettings[ 'placement_position' ]
												)
											}
											options={ positionOptions }
										/>
									</FormField>

									<FormField
										label="Exclude Pages"
										description="Comma-separated list of IDs, slugs, or titles to skip"
									>
										<TextInput
											value={
												localSettings.exclude_pages ||
												''
											}
											onChange={ ( value ) =>
												updateLocal(
													'exclude_pages',
													value
												)
											}
											placeholder="1, about-us, landing-page"
										/>
									</FormField>

									<div>
										<h4 className="font-medium text-gray-800 mb-2">
											Content Types
										</h4>
										<p className="text-sm text-gray-600 mb-3">
											Select the content types that should
											render buttons automatically.
										</p>

										<div className="space-y-3">
											{ availablePostTypes.map(
												( postType ) => {
													const isEnabled =
														selectedPostTypes.has(
															postType.id
														);

													return (
														<div
															key={ postType.id }
															className="flex items-start space-x-3 p-3 border border-gray-200 rounded hover:bg-gray-50"
														>
															<Checkbox
																checked={
																	isEnabled
																}
																onChange={ (
																	checked
																) =>
																	handlePostTypeToggle(
																		postType.id,
																		checked
																	)
																}
																label=""
																className="mt-1"
															/>
															<div className="flex-1">
																<h5 className="font-medium text-gray-800">
																	{
																		postType.name
																	}
																</h5>
																<p className="text-sm text-gray-600">
																	{
																		postType.description
																	}
																</p>
															</div>
														</div>
													);
												}
											) }
										</div>
									</div>
								</div>
							) }
						</div>
					</section>
				</div>

				<div className="mt-8 pt-4 border-t border-gray-200">
					<div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
						<p className="text-sm text-gray-600">
							{ localSettings.auto_placement
								? 'Auto-placement is enabled.'
								: 'Auto-placement is disabled.' }{ ' ' }
							{ localSettings.placement_post_types.length }{ ' ' }
							content type
							{ localSettings.placement_post_types.length !== 1
								? 's'
								: '' }{ ' ' }
							targeted.
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
