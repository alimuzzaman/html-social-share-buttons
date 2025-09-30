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