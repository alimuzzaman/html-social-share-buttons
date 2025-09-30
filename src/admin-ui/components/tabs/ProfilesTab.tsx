import React, { useState, useEffect } from 'react';
import {
	Button,
	Checkbox,
	LoadingOverlay,
	ValidatedTextInput,
	ValidatedSelect,
	FormField,
	Select,
} from '../ui';
import { Profile, ProfileNetwork } from '../../types';
import { useNotifications } from '../../contexts';
import { useSettings } from '../../hooks';


// Icons are provided by the plugin author and placed in assets/iconset. Use localized pluginUrl at runtime.
const pluginUrl =
	typeof window !== 'undefined' &&
	( window as any ).hssAdminConfig &&
	( window as any ).hssAdminConfig.pluginUrl
		? ( window as any ).hssAdminConfig.pluginUrl
		: '';

const getNetworkIconUrl = ( networkId: string ) =>
	`${ pluginUrl }assets/iconset/default_square/${ networkId }.png`;

// Default networks available for profiles
const availableNetworks = [
	{ id: 'facebook', name: 'Facebook', icon: 'fab fa-facebook-f' },
	{ id: 'twitter', name: 'Twitter', icon: 'fab fa-twitter' },
	{ id: 'linkedin', name: 'LinkedIn', icon: 'fab fa-linkedin-in' },
	{ id: 'pinterest', name: 'Pinterest', icon: 'fab fa-pinterest-p' },
	{ id: 'reddit', name: 'Reddit', icon: 'fab fa-reddit-alien' },
	{ id: 'whatsapp', name: 'WhatsApp', icon: 'fab fa-whatsapp' },
];

export const ProfilesTab: React.FC = () => {
	const { settings, updateSetting, saveSettings } = useSettings();
	const [ editingProfile, setEditingProfile ] = useState< Profile | null >( null );
	const [ isCreating, setIsCreating ] = useState( false );
	const [ loading, setLoading ] = useState( false );
	const profiles = settings?.profiles ?? [];
	const defaultProfileId = settings?.default_profile ?? '';
	const { showSuccess, showError } = useNotifications();

	// Load profiles from API (placeholder)
	useEffect( () => {
		// TODO: Load profiles from REST API
		// loadProfiles();
	}, [] );

	const handleCreateProfile = () => {
		const newProfile: Profile = {
			id: Date.now().toString(),
			name: 'New Profile',
			networks: {},
			display_settings: {
				style: 'default',
				size: 'medium',
				text_labels: false,
				icon_only: true,
			},
		};
		// Add immediately to settings so it's persisted when saved
		updateSetting('profiles', [ ...profiles, newProfile ] );
		setEditingProfile( newProfile );
		setIsCreating( true );
	};

	const handleEditProfile = ( profile: Profile ) => {
		setEditingProfile( { ...profile } );
		setIsCreating( false );
	};

	const handleSaveProfile = async () => {
		if ( ! editingProfile ) {
			return;
		}

		// Basic validation
		if ( ! editingProfile.name.trim() ) {
			showError(
				'Profile name is required',
				'Please enter a name for the profile.'
			);
			return;
		}

		try {
			setLoading( true );

			if ( isCreating ) {
				// ensure setting already contains the profile, just mark success
				showSuccess( 'Profile created successfully!' );
			} else {
				// Update existing profile in settings
				const updated = profiles.map( ( p ) =>
					p.id === editingProfile.id ? editingProfile : p
				);
				updateSetting( 'profiles', updated );
				showSuccess( 'Profile updated successfully!' );
			}

			// Persist settings
			try {
				await saveSettings();
			} catch ( e ) {
				// ignore here; saveSettings throws on failure and UI shows errors
			}

			setEditingProfile( null );
			setIsCreating( false );
		} catch ( error ) {
			showError( 'Failed to save profile', 'Please try again.' );
		} finally {
			setLoading( false );
		}
	};

	const handleDeleteProfile = async ( profileId: string ) => {
		try {
			setLoading( true );
			const updated = profiles.filter( ( p ) => p.id !== profileId );
			updateSetting( 'profiles', updated );
			// If the deleted profile was the default, clear the default
			if ( settings?.default_profile === profileId ) {
				updateSetting( 'default_profile', '' );
			}
			await saveSettings();
			showSuccess( 'Profile deleted successfully!' );
		} catch ( error ) {
			showError( 'Failed to delete profile', 'Please try again.' );
		} finally {
			setLoading( false );
		}
	};

	const handleCancelEdit = () => {
		// Revert to settings values by clearing the editor
		setEditingProfile( null );
		setIsCreating( false );
	};

	// Default profile change handler
	const handleDefaultProfileChange = async ( id: string ) => {
		updateSetting( 'default_profile', id );
		try {
			await saveSettings();
			showSuccess( 'Default profile updated' );
		} catch ( e ) {
			showError( 'Failed to update default profile', 'Please try again.' );
		}
	};

	const updateEditingProfile = ( updates: Partial< Profile > ) => {
		if ( ! editingProfile ) {
			return;
		}
		setEditingProfile( { ...editingProfile, ...updates } );
	};

	const updateNetworkSetting = (
		networkId: string,
		settings: Partial< ProfileNetwork >
	) => {
		if ( ! editingProfile ) {
			return;
		}

		setEditingProfile( {
			...editingProfile,
			networks: {
				...editingProfile.networks,
				[ networkId ]: {
					...editingProfile.networks[ networkId ],
					...settings,
				},
			},
		} );
	};

	return (
		<LoadingOverlay isLoading={ loading } message="Saving profile...">
			<div className="profiles-tab">
				<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
					<div className="flex justify-between items-center mb-6">
						<div>
							<h2 className="text-xl font-semibold">
								Social Sharing Profiles
							</h2>
							<p className="text-gray-600">
								Create different profiles for different types of
								content or pages.
							</p>
						</div>
						<Button
							onClick={ handleCreateProfile }
							variant="primary"
						>
							Add New Profile
						</Button>
					</div>
					{ /* Default Profile Selection */ }
					<div className="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
						<FormField label="Default Profile">
							<Select
								value={ defaultProfileId }
								onChange={ ( id ) => handleDefaultProfileChange( id ) }
								options={ [
									{ value: '', label: 'No default profile' },
									...profiles.map( ( profile ) => ( {
										value: profile.id,
										label: profile.name,
									} ) ),
								] }
							/>
						</FormField>
						<p className="text-sm text-blue-700 mt-2">
							The default profile will be used when no specific
							profile is selected for content.
						</p>
					</div>{ ' ' }
					{ editingProfile ? (
						// Profile Editor
						<div className="bg-gray-50 p-6 rounded-lg mb-6">
							<h3 className="text-lg font-medium mb-4">
								{ isCreating
									? 'Create New Profile'
									: 'Edit Profile' }
							</h3>

							<div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
								{ /* Basic Settings */ }
								<div>
									<ValidatedTextInput
										label="Profile Name"
										value={ editingProfile.name }
										onChange={ ( value ) => {
											updateEditingProfile( {
												name: value,
											} );
										} }
										placeholder="Enter profile name"
										required
									/>

									<div className="mt-4">
										<h4 className="font-medium mb-3">
											Display Settings
										</h4>

										<ValidatedSelect
											label="Button Style"
											value={
												editingProfile.display_settings
													.style
											}
											onChange={ ( value ) =>
												updateEditingProfile( {
													display_settings: {
														...editingProfile.display_settings,
														style: value,
													},
												} )
											}
											options={ [
												{
													value: 'default',
													label: 'Default',
												},
												{
													value: 'rounded',
													label: 'Rounded',
												},
												{
													value: 'square',
													label: 'Square',
												},
												{
													value: 'minimal',
													label: 'Minimal',
												},
											] }
										/>

										<ValidatedSelect
											label="Button Size"
											value={
												editingProfile.display_settings
													.size
											}
											onChange={ ( value ) =>
												updateEditingProfile( {
													display_settings: {
														...editingProfile.display_settings,
														size: value,
													},
												} )
											}
											options={ [
												{
													value: 'small',
													label: 'Small',
												},
												{
													value: 'medium',
													label: 'Medium',
												},
												{
													value: 'large',
													label: 'Large',
												},
											] }
											className="mt-3"
										/>

										<div className="mt-3 space-y-2">
											<Checkbox
												checked={
													editingProfile
														.display_settings
														.text_labels
												}
												onChange={ ( checked ) =>
													updateEditingProfile( {
														display_settings: {
															...editingProfile.display_settings,
															text_labels:
																checked,
														},
													} )
												}
												label="Show text labels"
											/>

											<Checkbox
												checked={
													editingProfile
														.display_settings
														.icon_only
												}
												onChange={ ( checked ) =>
													updateEditingProfile( {
														display_settings: {
															...editingProfile.display_settings,
															icon_only: checked,
														},
													} )
												}
												label="Icon only mode"
											/>
										</div>
									</div>
								</div>

								{ /* Network Settings */ }
								<div>
									<h4 className="font-medium mb-3">
										Social Networks
									</h4>
									<div className="space-y-3">
										{ availableNetworks.map(
											( network ) => {
												const networkSettings =
													editingProfile.networks[
														network.id
													] || { enabled: false };

												return (
													<div
														key={ network.id }
														className="border border-gray-200 rounded p-3"
													>
														<div className="flex items-center justify-between mb-2">
															<div className="flex items-center">
																<img
																	src={ getNetworkIconUrl(
																		network.id
																	) }
																	alt={ `${ network.name } icon` }
																	className="mr-2 w-5 h-5"
																/>
																<span className="font-medium">
																	{
																		network.name
																	}
																</span>
															</div>
															<Checkbox
																checked={
																	networkSettings.enabled ||
																	false
																}
																onChange={ (
																	checked
																) =>
																	updateNetworkSetting(
																		network.id,
																		{
																			enabled:
																				checked,
																		}
																	)
																}
																label=""
															/>
														</div>

														{ networkSettings.enabled &&
															( network.id ===
																'twitter' ||
																network.id ===
																	'instagram' ) && (
																<div className="mt-2">
																	<ValidatedTextInput
																		label=""
																		value={
																			networkSettings.handle ||
																			''
																		}
																		onChange={ (
																			value
																		) =>
																			updateNetworkSetting(
																				network.id,
																				{
																					handle: value,
																				}
																			)
																		}
																		placeholder={ `@${ network.name.toLowerCase() }handle` }
																	/>
																</div>
															) }
													</div>
												);
											}
										) }
									</div>
								</div>
							</div>

							<div className="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
								<Button
									onClick={ handleCancelEdit }
									variant="secondary"
								>
									Cancel
								</Button>
								<Button
									onClick={ handleSaveProfile }
									variant="primary"
								>
									{ isCreating
										? 'Create Profile'
										: 'Save Changes' }
								</Button>
							</div>
						</div>
					) : (
						// Profiles List
						<div className="space-y-4">
							{ profiles.map( ( profile ) => (
								<div
									key={ profile.id }
									className="border border-gray-200 rounded-lg p-4 transition-all duration-200 hover:shadow-md hover:border-gray-300"
								>
									<div className="flex justify-between items-start">
										<div>
											<h3 className="font-medium text-lg">
												{ profile.name }
											</h3>
											<p className="text-sm text-gray-600 mt-1">
												{
													Object.values(
														profile.networks
													).filter(
														( n ) => n.enabled
													).length
												}{ ' ' }
												networks enabled
											</p>
											<div className="flex items-center mt-2 space-x-2">
												{ Object.entries(
													profile.networks
												)
													.filter(
														( [ , settings ] ) =>
															settings.enabled
													)
													.map( ( [ networkId ] ) => {
														const network =
															availableNetworks.find(
																( n ) =>
																	n.id ===
																	networkId
															);
														return network ? (
															<img
																key={
																	networkId
																}
																src={ getNetworkIconUrl(
																	networkId
																) }
																alt={
																	network.name
																}
																title={
																	network.name
																}
																className="w-5 h-5"
															/>
														) : null;
													} ) }
											</div>
										</div>
										<div className="flex space-x-2">
											<Button
												onClick={ () =>
													handleEditProfile( profile )
												}
												variant="secondary"
												size="small"
												className="transition-all duration-200 hover:shadow-sm"
											>
												Edit
											</Button>
											<Button
												onClick={ () =>
													handleDeleteProfile(
														profile.id
													)
												}
												variant="secondary"
												size="small"
												className="text-red-600 hover:text-red-700 hover:bg-red-50 transition-all duration-200 hover:shadow-sm"
											>
												Delete
											</Button>
										</div>
									</div>
								</div>
							) ) }

							{ profiles.length === 0 && (
								<div className="text-center py-8 text-gray-500">
									<p>No profiles created yet.</p>
									<p className="text-sm mt-1">
										Create your first profile to get
										started.
									</p>
								</div>
							) }
						</div>
					) }
				</div>
			</div>
		</LoadingOverlay>
	);
};
