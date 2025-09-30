import React, { useState, useEffect } from 'react';
import { FormField, Button, TextInput } from '../ui';
import { useNotifications } from '../../contexts';
import { useNetworks } from '../../hooks/useNetworks';
import { useSettings } from '../../hooks';
import { NetworkConfig, CustomNetwork } from '../../types';
import {
	Facebook,
	Twitter,
	Linkedin,
	MessageSquare,
	GripVertical,
} from 'lucide-react';
import {
	DndContext,
	closestCenter,
	KeyboardSensor,
	PointerSensor,
	useSensor,
	useSensors,
} from '@dnd-kit/core';
import {
	arrayMove,
	SortableContext,
	sortableKeyboardCoordinates,
	verticalListSortingStrategy,
	useSortable,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

const defaultNetworks: NetworkConfig[] = [
	{
		id: 'facebook',
		name: 'Facebook',
		label: 'Facebook',
		share_url: 'https://www.facebook.com/sharer/sharer.php?u={url}',
		requires_handle: false,
		icon_class: 'fab fa-facebook-f',
		color: '#1877f2',
	},
	{
		id: 'twitter',
		name: 'Twitter',
		label: 'Twitter',
		share_url: 'https://twitter.com/intent/tweet?url={url}&text={title}',
		requires_handle: false,
		icon_class: 'fab fa-twitter',
		color: '#1da1f2',
	},
	{
		id: 'linkedin',
		name: 'LinkedIn',
		label: 'LinkedIn',
		share_url: 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
		requires_handle: false,
		icon_class: 'fab fa-linkedin-in',
		color: '#0077b5',
	},
	{
		id: 'pinterest',
		name: 'Pinterest',
		label: 'Pinterest',
		share_url:
			'https://pinterest.com/pin/create/button/?url={url}&description={title}',
		requires_handle: false,
		icon_class: 'fab fa-pinterest-p',
		color: '#bd081c',
	},
	{
		id: 'reddit',
		name: 'Reddit',
		label: 'Reddit',
		share_url: 'https://reddit.com/submit?url={url}&title={title}',
		requires_handle: false,
		icon_class: 'fab fa-reddit-alien',
		color: '#ff4500',
	},
	{
		id: 'whatsapp',
		name: 'WhatsApp',
		label: 'WhatsApp',
		share_url: 'https://wa.me/?text={title}%20{url}',
		requires_handle: false,
		icon_class: 'fab fa-whatsapp',
		color: '#25d366',
	},
	{
		id: 'google-plus',
		name: 'Google Plus',
		label: 'Google Plus',
		share_url: 'https://plus.google.com/share?url={url}',
		requires_handle: false,
		icon_class: 'fab fa-google-plus-g',
		color: '#dd4b39',
	},
	{
		id: 'google-bookmarks',
		name: 'Google Bookmarks',
		label: 'Google Bookmarks',
		share_url:
			'https://www.google.com/bookmarks/mark?op=edit&bkmk={url}&title={title}',
		requires_handle: false,
		icon_class: 'fab fa-google',
		color: '#4285f4',
	},
	{
		id: 'email',
		name: 'Email',
		label: 'Email',
		share_url: 'mailto:?subject={title}&body={url}',
		requires_handle: false,
		icon_class: 'fas fa-envelope',
		color: '#666666',
	},
];
const networkLucideMap: Record< string, React.ReactNode | undefined > = {
	facebook: <Facebook size={ 16 } />,
	twitter: <Twitter size={ 16 } />,
	linkedin: <Linkedin size={ 16 } />,
	whatsapp: <MessageSquare size={ 16 } />,
};

const pluginUrl =
	typeof window !== 'undefined' &&
	( window as any ).hssAdminConfig &&
	( window as any ).hssAdminConfig.pluginUrl
		? ( window as any ).hssAdminConfig.pluginUrl
		: '';

interface SortableNetworkItemProps {
	network: NetworkConfig;
}

const SortableNetworkItem: React.FC< SortableNetworkItemProps > = ( {
	network,
} ) => {
	const {
		attributes,
		listeners,
		setNodeRef,
		transform,
		transition,
		isDragging,
	} = useSortable( { id: network.id } );

	const style = {
		transform: CSS.Transform.toString( transform ),
		transition,
	};

	return (
		<div
			ref={ setNodeRef }
			style={ style }
			className={ `flex items-center px-3 py-2 bg-white border border-gray-200 rounded cursor-move transition-all duration-200 hover:shadow-sm hover:border-gray-300 ${
				isDragging ? 'opacity-50' : ''
			}` }
			{ ...attributes }
			{ ...listeners }
		>
			<div
				className="w-4 h-4 rounded mr-2 flex-shrink-0"
				style={ { backgroundColor: network.color } }
			/>
			<span className="text-sm flex-1">{ network.label }</span>
			<div className="ml-2">
				<GripVertical size={ 14 } className="text-gray-400" />
			</div>
		</div>
	);
};

export const NetworksTab: React.FC = () => {
	const { networks: apiNetworks, updateNetwork } = useNetworks();
	const { settings, updateSetting, saveSettings } = useSettings();
	const { showSuccess, showError } = useNotifications();

	// Keep local state for immediate UI updates and form handling
	const [ localNetworks ] = useState< NetworkConfig[] >( defaultNetworks );
	const [ enabledNetworks, setEnabledNetworks ] = useState< string[] >( [
		'facebook',
		'twitter',
		'linkedin',
	] );
	const [ customNetworks, setCustomNetworks ] = useState< CustomNetwork[] >(
		settings?.custom_networks ?? []
	);
	const [ isSaving, setIsSaving ] = useState( false );
	const [ showCustomNetworkForm, setShowCustomNetworkForm ] = useState(
		false
	);
	const [ customNetworkForm, setCustomNetworkForm ] = useState( {
		name: '',
		label: '',
		share_url: '',
		color: '#666666',
		icon_class: '',
	} );

	// Use API networks if available, otherwise fall back to local defaults
	const networks = apiNetworks.length > 0 ? apiNetworks : localNetworks;

	// Helper to normalize CustomNetwork -> NetworkConfig for rendering
	const customAsNetworkConfig = ( c: CustomNetwork ): NetworkConfig => ( {
		id: c.id,
		name: c.name,
		label: c.label,
		share_url: c.share_url,
		requires_handle: false,
		icon_class: c.icon_class || 'fas fa-share',
		color: c.color || '#666666',
		enabled: c.enabled,
	} );

	const allNetworks: NetworkConfig[] = [
		...networks,
		...( ( settings?.custom_networks ?? customNetworks ).map( customAsNetworkConfig ) ),
	];

	// Keep local customNetworks in sync with settings when available
	useEffect( () => {
		if ( settings ) {
			setCustomNetworks( settings.custom_networks ?? [] );
		}
	}, [ settings ] );

	const sensors = useSensors(
		useSensor( PointerSensor ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	const handleNetworkToggle = ( networkId: string, enabled: boolean ) => {
		if ( enabled ) {
			setEnabledNetworks( ( prev ) => [ ...prev, networkId ] );
		} else {
			setEnabledNetworks( ( prev ) =>
				prev.filter( ( id ) => id !== networkId )
			);
		}
	};

	const handleDragEnd = ( event: any ) => {
		const { active, over } = event;

		if ( active.id !== over.id ) {
			setEnabledNetworks( ( items ) => {
				const oldIndex = items.indexOf( active.id );
				const newIndex = items.indexOf( over.id );

				return arrayMove( items, oldIndex, newIndex );
			} );
		}
	};

	const handleNetworkLabelChange = async (
		networkId: string,
		label: string
	) => {
		try {
			// Update via API if available
			if ( apiNetworks.length > 0 ) {
				await updateNetwork( networkId, { label } );
			}

			showSuccess( `${ label } label updated!` );
		} catch ( error ) {
			showError( 'Failed to update network label', 'Please try again.' );
		}
	};

	const handleAddCustomNetwork = async () => {
		if (
			! customNetworkForm.name.trim() ||
			! customNetworkForm.share_url.trim()
		) {
			showError(
				'Please fill in all required fields',
				'Name and Share URL are required.'
			);
			return;
		}

		const newCustom: CustomNetwork = {
			id: `custom-${ Date.now() }`,
			name: customNetworkForm.name.trim(),
			label: customNetworkForm.label.trim() || customNetworkForm.name.trim(),
			share_url: customNetworkForm.share_url.trim(),
			color: customNetworkForm.color,
			icon_class: customNetworkForm.icon_class.trim() || 'fas fa-share',
			enabled: true,
		};

		// Update settings so custom networks persist
		const updatedCustoms = [ ...( settings?.custom_networks ?? customNetworks ), newCustom ];
		setCustomNetworks( updatedCustoms );
		updateSetting( 'custom_networks', updatedCustoms );
		// Add to enabled list and order by default
		const updatedEnabled = [ ...( settings?.enabled_networks ?? enabledNetworks ), newCustom.id ];
		updateSetting( 'enabled_networks', updatedEnabled );
		updateSetting( 'network_order', [ ...( settings?.network_order ?? [] ), ...updatedEnabled ] );
		setCustomNetworkForm( {
			name: '',
			label: '',
			share_url: '',
			color: '#666666',
			icon_class: '',
		} );
		setShowCustomNetworkForm( false );
		try {
			await saveSettings();
			showSuccess( 'Custom network added successfully!' );
		} catch ( e ) {
			showError( 'Failed to save custom network', 'Please try again.' );
		}
	};

	const handleDeleteCustomNetwork = async ( networkId: string ) => {
		const updatedCustoms = ( settings?.custom_networks ?? customNetworks ).filter( ( n ) => n.id !== networkId );
		updateSetting( 'custom_networks', updatedCustoms );
		const updatedEnabled = ( settings?.enabled_networks ?? enabledNetworks ).filter( ( id ) => id !== networkId );
		updateSetting( 'enabled_networks', updatedEnabled );
		const updatedOrder = ( settings?.network_order ?? enabledNetworks ).filter( ( id ) => id !== networkId );
		updateSetting( 'network_order', updatedOrder );
		setCustomNetworks( updatedCustoms );
		setEnabledNetworks( updatedEnabled );
		try {
			await saveSettings();
			showSuccess( 'Custom network removed successfully!' );
		} catch ( e ) {
			showError( 'Failed to remove custom network', 'Please try again.' );
		}
	};

	const handleSave = async () => {
		setIsSaving( true );
		try {
			// Update settings to persist enabled networks and order
			updateSetting( 'enabled_networks', enabledNetworks );
			updateSetting( 'network_order', enabledNetworks );
			updateSetting( 'custom_networks', settings?.custom_networks ?? customNetworks );

			// If API is available, save individual network enabled state via useNetworks API
			const networkUpdates = allNetworks.map(
				( network: NetworkConfig ) => ( {
					...network,
					enabled: enabledNetworks.includes( network.id ),
				} )
			);

			if ( apiNetworks.length > 0 ) {
				await Promise.all(
					networkUpdates.map( ( network: NetworkConfig ) =>
						updateNetwork( network.id, {
							enabled: network.enabled,
						} )
					)
				);
			}

			// Persist via central settings endpoint
			await saveSettings();

			showSuccess( 'Network settings saved successfully!' );
		} catch ( error ) {
			showError( 'Failed to save settings', 'Please try again.' );
		} finally {
			setIsSaving( false );
		}
	};

	return (
		<div className="networks-tab">
			<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
				<h2 className="text-xl font-semibold mb-4">Social Networks</h2>
				<p className="text-gray-600 mb-6">
					Choose which social networks to make available for sharing
					and customize their appearance.
				</p>

				<div className="space-y-4">
					<h3 className="text-lg font-medium text-gray-800 mb-3">
						Available Networks
					</h3>

					<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
						{ allNetworks.map( ( network: NetworkConfig ) => {
							const isEnabled = enabledNetworks.includes(
								network.id
							);

							return (
								<div
									key={ network.id }
									className={ `transition-all duration-200 border rounded-lg p-4 cursor-pointer hover:shadow-md ${
										isEnabled
											? 'border-blue-500 bg-blue-50 hover:bg-blue-100'
											: 'border-gray-200 hover:border-gray-300'
									}` }
									onClick={ () =>
										handleNetworkToggle(
											network.id,
											! isEnabled
										)
									}
									onKeyDown={ ( e ) => {
										if (
											e.key === 'Enter' ||
											e.key === ' '
										) {
											e.preventDefault();
											handleNetworkToggle(
												network.id,
												! isEnabled
											);
										}
									} }
									role="button"
									tabIndex={ 0 }
									aria-pressed={ isEnabled }
								>
									<div className="flex items-center mb-3">
										<div
											className="w-8 h-8 rounded flex items-center justify-center mr-3"
											style={ {
												backgroundColor: network.color,
											} }
										>
											{ networkLucideMap[ network.id ] ? (
												<span
													className="text-white"
													aria-hidden
												>
													{
														networkLucideMap[
															network.id
														]
													}
												</span>
											) : (
												( () => {
													const imgSrc = `${ pluginUrl }assets/iconset/default_square/${ network.id }.png`;
													return (
														<img
															src={ imgSrc }
															alt={ `${ network.name } icon` }
															className="w-5 h-5"
															onError={ ( e ) => {
																(
																	e.currentTarget as HTMLImageElement
																 ).style.display =
																	'none';
																const placeholder =
																	document.createElement(
																		'span'
																	);
																placeholder.className =
																	'inline-flex items-center justify-center w-5 h-5 rounded-full bg-white text-xs text-gray-700';
																placeholder.textContent =
																	network.name.charAt(
																		0
																	);
																e.currentTarget.parentElement?.appendChild(
																	placeholder
																);
															} }
														/>
													);
												} )()
											) }
										</div>
										<div className="flex-1">
											<h4 className="font-medium text-gray-800">
												{ network.name }
												{ network.id.startsWith(
													'custom-'
												) && (
													<span className="ml-2 text-xs text-orange-600 font-normal">
														Custom
													</span>
												) }
											</h4>
											{ isEnabled && (
												<span className="text-xs text-blue-600 font-medium">
													Enabled
												</span>
											) }
										</div>
										{ network.id.startsWith( 'custom-' ) && (
											<button
												onClick={ ( e ) => {
													e.stopPropagation();
													handleDeleteCustomNetwork(
														network.id
													);
												} }
												className="ml-2 text-red-500 hover:text-red-700 p-1"
												title="Remove custom network"
											>
												×
											</button>
										) }
									</div>

									{ isEnabled && (
										<div className="mt-3">
											<FormField
												label="Button Label"
												description="Text displayed on the button"
											>
												<TextInput
													value={ network.label }
													onChange={ ( value ) =>
														handleNetworkLabelChange(
															network.id,
															value
														)
													}
													placeholder={ network.name }
												/>
											</FormField>
										</div>
									) }
								</div>
							);
						} ) }
					</div>

					<div className="mt-6 p-4 bg-gray-50 rounded-lg">
						<h4 className="font-medium text-gray-800 mb-2">
							Network Order
						</h4>
						<p className="text-sm text-gray-600 mb-3">
							Drag and drop to reorder the networks as they will
							appear on your site.
						</p>
						<DndContext
							sensors={ sensors }
							collisionDetection={ closestCenter }
							onDragEnd={ handleDragEnd }
						>
							<SortableContext
								items={ enabledNetworks }
								strategy={ verticalListSortingStrategy }
							>
								<div className="flex flex-col space-y-2">
									{ enabledNetworks.map( ( networkId ) => {
										const network = allNetworks.find(
											( n: NetworkConfig ) =>
												n.id === networkId
										);
										if ( ! network ) {
											return null;
										}

										return (
											<SortableNetworkItem
												key={ networkId }
												network={ network }
											/>
										);
									} ) }
								</div>
							</SortableContext>
						</DndContext>
					</div>

					<div className="mt-6 p-4 bg-gray-50 rounded-lg">
						<div className="flex justify-between items-center mb-3">
							<h4 className="font-medium text-gray-800">
								Custom Networks
							</h4>
							<Button
								onClick={ () =>
									setShowCustomNetworkForm(
										! showCustomNetworkForm
									)
								}
								variant="secondary"
								size="small"
							>
								{ showCustomNetworkForm
									? 'Cancel'
									: 'Add Custom Network' }
							</Button>
						</div>
						<p className="text-sm text-gray-600 mb-3">
							Create your own custom social networks with custom
							share URLs and branding.
						</p>

						{ showCustomNetworkForm && (
							<div className="bg-white p-4 rounded border border-gray-200">
								<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
									<FormField
										label="Network Name"
										description="Display name for the network"
									>
										<TextInput
											value={ customNetworkForm.name }
											onChange={ ( value ) =>
												setCustomNetworkForm( ( prev ) => ( {
													...prev,
													name: value,
												} ) )
											}
											placeholder="e.g., My Custom Network"
										/>
									</FormField>

									<FormField
										label="Button Label"
										description="Text shown on the share button"
									>
										<TextInput
											value={ customNetworkForm.label }
											onChange={ ( value ) =>
												setCustomNetworkForm( ( prev ) => ( {
													...prev,
													label: value,
												} ) )
											}
											placeholder="e.g., Share"
										/>
									</FormField>

									<FormField
										label="Share URL"
										description="URL template with {url} and {title} placeholders"
									>
										<TextInput
											value={ customNetworkForm.share_url }
											onChange={ ( value ) =>
												setCustomNetworkForm( ( prev ) => ( {
													...prev,
													share_url: value,
												} ) )
											}
											placeholder="https://example.com/share?url={url}&title={title}"
										/>
									</FormField>

									<FormField
										label="Brand Color"
										description="Hex color for the network button"
									>
										<TextInput
											value={ customNetworkForm.color }
											onChange={ ( value ) =>
												setCustomNetworkForm( ( prev ) => ( {
													...prev,
													color: value,
												} ) )
											}
											placeholder="#666666"
										/>
									</FormField>

									<div className="md:col-span-2">
										<FormField
											label="Icon Class (Optional)"
											description="FontAwesome icon class (e.g., fab fa-share)"
										>
											<TextInput
												value={ customNetworkForm.icon_class }
												onChange={ ( value ) =>
													setCustomNetworkForm( ( prev ) => ( {
														...prev,
														icon_class: value,
													} ) )
												}
												placeholder="fab fa-share"
											/>
										</FormField>
									</div>
								</div>

								<div className="flex justify-end mt-4">
									<Button
										onClick={ handleAddCustomNetwork }
										variant="primary"
									>
										Add Network
									</Button>
								</div>
							</div>
						) }

						{ customNetworks.length > 0 && (
							<div className="mt-4">
								<h5 className="text-sm font-medium text-gray-700 mb-2">
									Your Custom Networks ({ customNetworks.length })
								</h5>
								<div className="space-y-2">
									{ customNetworks.map( ( network ) => (
										<div
											key={ network.id }
											className="flex items-center justify-between p-2 bg-white rounded border border-gray-200"
										>
											<div className="flex items-center">
												<div
													className="w-6 h-6 rounded flex items-center justify-center mr-3"
													style={ {
														backgroundColor:
															network.color,
													} }
												>
													<span className="text-white text-xs">
														{ network.name.charAt( 0 ) }
													</span>
												</div>
												<div>
													<span className="font-medium text-sm">
														{ network.name }
													</span>
													<p className="text-xs text-gray-500">
														{ network.share_url }
													</p>
												</div>
											</div>
											<Button
												onClick={ () =>
													handleDeleteCustomNetwork(
														network.id
													)
												}
												variant="secondary"
												size="small"
												className="text-red-600 hover:text-red-700"
											>
												Remove
											</Button>
										</div>
									) ) }
								</div>
							</div>
						) }
					</div>

				</div>

				<div className="mt-8 pt-4 border-t border-gray-200">
					<div className="flex justify-between items-center">
						<p className="text-sm text-gray-600">
							{ enabledNetworks.length } network
							{ enabledNetworks.length !== 1 ? 's' : '' } enabled
						</p>
						<Button
							onClick={ handleSave }
							loading={ isSaving }
							variant="primary"
						>
							Save Changes
						</Button>
					</div>
				</div>
			</div>
		</div>
	);
};
