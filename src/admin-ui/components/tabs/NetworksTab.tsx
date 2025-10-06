import React, { useState, useEffect } from 'react';import React, { useState, useEffect } from 'react';

import { Button, LoadingOverlay, Checkbox } from '../ui';import { FormField, Button, TextInput } from '../ui';

import { useNotifications } from '../../contexts';import { useNotifications } from '../../contexts';

import { useNetworks } from '../../hooks/useNetworks';import { useNetworks } from '../../hooks/useNetworks';

import { useSettings } from '../../hooks';import { useSettings } from '../../hooks';

import { NetworkConfig } from '../../types';import { NetworkConfig, CustomNetwork } from '../../types';

import { GripVertical } from 'lucide-react';import {

import {	Facebook,

	DndContext,	Twitter,

	closestCenter,	Linkedin,

	KeyboardSensor,	MessageSquare,

	PointerSensor,	GripVertical,

	useSensor,} from 'lucide-react';

	useSensors,import {

	DragEndEvent,	DndContext,

} from '@dnd-kit/core';	closestCenter,

import {	KeyboardSensor,

	arrayMove,	PointerSensor,

	SortableContext,	useSensor,

	sortableKeyboardCoordinates,	useSensors,

	verticalListSortingStrategy,} from '@dnd-kit/core';

	useSortable,import {

} from '@dnd-kit/sortable';	arrayMove,

import { CSS } from '@dnd-kit/utilities';	SortableContext,

	sortableKeyboardCoordinates,

const defaultNetworks: NetworkConfig[] = [	verticalListSortingStrategy,

	{	useSortable,

		id: 'facebook',} from '@dnd-kit/sortable';

		name: 'Facebook',import { CSS } from '@dnd-kit/utilities';

		label: 'Facebook',

		share_url: 'https://www.facebook.com/sharer/sharer.php?u={url}',const defaultNetworks: NetworkConfig[] = [

		requires_handle: false,	{

		icon_class: 'fab fa-facebook-f',		id: 'facebook',

		color: '#1877f2',		name: 'Facebook',

	},		label: 'Facebook',

	{		share_url: 'https://www.facebook.com/sharer/sharer.php?u={url}',

		id: 'twitter',		requires_handle: false,

		name: 'Twitter',		icon_class: 'fab fa-facebook-f',

		label: 'Twitter',		color: '#1877f2',

		share_url: 'https://twitter.com/intent/tweet?url={url}&text={title}',	},

		requires_handle: false,	{

		icon_class: 'fab fa-twitter',		id: 'twitter',

		color: '#1da1f2',		name: 'Twitter',

	},		label: 'Twitter',

	{		share_url: 'https://twitter.com/intent/tweet?url={url}&text={title}',

		id: 'linkedin',		requires_handle: false,

		name: 'LinkedIn',		icon_class: 'fab fa-twitter',

		label: 'LinkedIn',		color: '#1da1f2',

		share_url: 'https://www.linkedin.com/sharing/share-offsite/?url={url}',	},

		requires_handle: false,	{

		icon_class: 'fab fa-linkedin-in',		id: 'linkedin',

		color: '#0077b5',		name: 'LinkedIn',

	},		label: 'LinkedIn',

	{		share_url: 'https://www.linkedin.com/sharing/share-offsite/?url={url}',

		id: 'pinterest',		requires_handle: false,

		name: 'Pinterest',		icon_class: 'fab fa-linkedin-in',

		label: 'Pinterest',		color: '#0077b5',

		share_url:	},

			'https://pinterest.com/pin/create/button/?url={url}&description={title}',	{

		requires_handle: false,		id: 'pinterest',

		icon_class: 'fab fa-pinterest-p',		name: 'Pinterest',

		color: '#bd081c',		label: 'Pinterest',

	},		share_url:

	{			'https://pinterest.com/pin/create/button/?url={url}&description={title}',

		id: 'reddit',		requires_handle: false,

		name: 'Reddit',		icon_class: 'fab fa-pinterest-p',

		label: 'Reddit',		color: '#bd081c',

		share_url: 'https://reddit.com/submit?url={url}&title={title}',	},

		requires_handle: false,	{

		icon_class: 'fab fa-reddit-alien',		id: 'reddit',

		color: '#ff4500',		name: 'Reddit',

	},		label: 'Reddit',

	{		share_url: 'https://reddit.com/submit?url={url}&title={title}',

		id: 'whatsapp',		requires_handle: false,

		name: 'WhatsApp',		icon_class: 'fab fa-reddit-alien',

		label: 'WhatsApp',		color: '#ff4500',

		share_url: 'https://wa.me/?text={title}%20{url}',	},

		requires_handle: false,	{

		icon_class: 'fab fa-whatsapp',		id: 'whatsapp',

		color: '#25d366',		name: 'WhatsApp',

	},		label: 'WhatsApp',

	{		share_url: 'https://wa.me/?text={title}%20{url}',

		id: 'google-plus',		requires_handle: false,

		name: 'Google Plus',		icon_class: 'fab fa-whatsapp',

		label: 'Google Plus',		color: '#25d366',

		share_url: 'https://plus.google.com/share?url={url}',	},

		requires_handle: false,	{

		icon_class: 'fab fa-google-plus-g',		id: 'google-plus',

		color: '#dd4b39',		name: 'Google Plus',

	},		label: 'Google Plus',

	{		share_url: 'https://plus.google.com/share?url={url}',

		id: 'google-bookmarks',		requires_handle: false,

		name: 'Google Bookmarks',		icon_class: 'fab fa-google-plus-g',

		label: 'Google Bookmarks',		color: '#dd4b39',

		share_url:	},

			'https://www.google.com/bookmarks/mark?op=edit&bkmk={url}&title={title}',	{

		requires_handle: false,		id: 'google-bookmarks',

		icon_class: 'fab fa-google',		name: 'Google Bookmarks',

		color: '#4285f4',		label: 'Google Bookmarks',

	},		share_url:

	{			'https://www.google.com/bookmarks/mark?op=edit&bkmk={url}&title={title}',

		id: 'email',		requires_handle: false,

		name: 'Email',		icon_class: 'fab fa-google',

		label: 'Email',		color: '#4285f4',

		share_url: 'mailto:?subject={title}&body={url}',	},

		requires_handle: false,	{

		icon_class: 'fas fa-envelope',		id: 'email',

		color: '#666666',		name: 'Email',

	},		label: 'Email',

];		share_url: 'mailto:?subject={title}&body={url}',

		requires_handle: false,

const pluginUrl =		icon_class: 'fas fa-envelope',

	typeof window !== 'undefined' &&		color: '#666666',

	( window as any ).hssAdminConfig &&	},

	( window as any ).hssAdminConfig.pluginUrl];

		? ( window as any ).hssAdminConfig.pluginUrlconst networkLucideMap: Record< string, React.ReactNode | undefined > = {

		: '';	facebook: <Facebook size={ 16 } />,

	twitter: <Twitter size={ 16 } />,

interface SortableNetworkItemProps {	linkedin: <Linkedin size={ 16 } />,

	network: NetworkConfig;	whatsapp: <MessageSquare size={ 16 } />,

	enabled: boolean;};

	onToggle: ( networkId: string, enabled: boolean ) => void;

}const pluginUrl =

	typeof window !== 'undefined' &&

const SortableNetworkItem: React.FC< SortableNetworkItemProps > = ( {	( window as any ).hssAdminConfig &&

	network,	( window as any ).hssAdminConfig.pluginUrl

	enabled,		? ( window as any ).hssAdminConfig.pluginUrl

	onToggle,		: '';

} ) => {

	const {interface SortableNetworkItemProps {

		attributes,	network: NetworkConfig;

		listeners,}

		setNodeRef,

		transform,const SortableNetworkItem: React.FC< SortableNetworkItemProps > = ( {

		transition,	network,

		isDragging,} ) => {

	} = useSortable( { id: network.id } );	const {

		attributes,

	const style = {		listeners,

		transform: CSS.Transform.toString( transform ),		setNodeRef,

		transition,		transform,

	};		transition,

		isDragging,

	return (	} = useSortable( { id: network.id } );

		<div

			ref={ setNodeRef }	const style = {

			style={ style }		transform: CSS.Transform.toString( transform ),

			className={ `flex items-center px-4 py-3 bg-white border rounded transition-all duration-200 ${ 		transition,

				enabled ? 'border-blue-500 bg-blue-50' : 'border-gray-200'	};

			} ${

				isDragging ? 'opacity-50 shadow-lg' : 'hover:shadow-sm hover:border-gray-300'	return (

			}` }		<div

		>			ref={ setNodeRef }

			<div			style={ style }

				className="cursor-move mr-3 text-gray-400 hover:text-gray-600"			className={ `flex items-center px-3 py-2 bg-white border border-gray-200 rounded cursor-move transition-all duration-200 hover:shadow-sm hover:border-gray-300 ${

				{ ...attributes }				isDragging ? 'opacity-50' : ''

				{ ...listeners }			}` }

			>			{ ...attributes }

				<GripVertical size={ 20 } />			{ ...listeners }

			</div>		>

			<div

			<div				className="w-4 h-4 rounded mr-2 flex-shrink-0"

				className="w-8 h-8 rounded flex items-center justify-center mr-3 flex-shrink-0"				style={ { backgroundColor: network.color } }

				style={ { backgroundColor: network.color } }			/>

			>			<span className="text-sm flex-1">{ network.label }</span>

				<img			<div className="ml-2">

					src={ `${ pluginUrl }assets/iconset/default_square/${ network.id }.png` }				<GripVertical size={ 14 } className="text-gray-400" />

					alt={ `${ network.name } icon` }			</div>

					className="w-5 h-5"		</div>

					onError={ ( e ) => {	);

						( e.currentTarget as HTMLImageElement ).style.display = 'none';};

						const placeholder = document.createElement( 'span' );

						placeholder.className =export const NetworksTab: React.FC = () => {

							'inline-flex items-center justify-center w-5 h-5 rounded-full bg-white text-xs text-gray-700 font-semibold';	const { networks: apiNetworks, updateNetwork } = useNetworks();

						placeholder.textContent = network.name.charAt( 0 );	const { settings, updateSetting, saveSettings } = useSettings();

						e.currentTarget.parentElement?.appendChild( placeholder );	const { showSuccess, showError } = useNotifications();

					} }

				/>	// Keep local state for immediate UI updates and form handling

			</div>	const [ localNetworks ] = useState< NetworkConfig[] >( defaultNetworks );

	const [ enabledNetworks, setEnabledNetworks ] = useState< string[] >( [

			<div className="flex-1">		'facebook',

				<h4 className="font-medium text-gray-800">{ network.name }</h4>		'twitter',

				{ enabled && (		'linkedin',

					<span className="text-xs text-blue-600">Enabled</span>	] );

				) }	const [ customNetworks, setCustomNetworks ] = useState< CustomNetwork[] >(

			</div>		settings?.custom_networks ?? []

	);

			<Checkbox	const [ isSaving, setIsSaving ] = useState( false );

				checked={ enabled }	const [ showCustomNetworkForm, setShowCustomNetworkForm ] =

				onChange={ ( checked ) => onToggle( network.id, checked ) }		useState( false );

				label=""	const [ customNetworkForm, setCustomNetworkForm ] = useState( {

			/>		name: '',

		</div>		label: '',

	);		share_url: '',

};		color: '#666666',

		icon_class: '',

export const NetworksTab: React.FC = () => {	} );

	const { networks: apiNetworks } = useNetworks();

	const { settings, updateSetting, saveSettings } = useSettings();	// Use API networks if available, otherwise fall back to local defaults

	const { showSuccess, showError } = useNotifications();	const networks = apiNetworks.length > 0 ? apiNetworks : localNetworks;



	// Use API networks if available, otherwise fall back to defaults	// Helper to normalize CustomNetwork -> NetworkConfig for rendering

	const networks = apiNetworks.length > 0 ? apiNetworks : defaultNetworks;	const customAsNetworkConfig = ( c: CustomNetwork ): NetworkConfig => ( {

		id: c.id,

	// Initialize from settings or defaults		name: c.name,

	const [ orderedNetworkIds, setOrderedNetworkIds ] = useState< string[] >(		label: c.label,

		settings?.network_order && settings.network_order.length > 0		share_url: c.share_url,

			? settings.network_order		requires_handle: false,

			: networks.map( ( n ) => n.id )		icon_class: c.icon_class || 'fas fa-share',

	);		color: c.color || '#666666',

	const [ enabledNetworkIds, setEnabledNetworkIds ] = useState< string[] >(		enabled: c.enabled,

		settings?.enabled_networks ?? [ 'facebook', 'twitter', 'linkedin' ]	} );

	);

	const [ isSaving, setIsSaving ] = useState( false );	const allNetworks: NetworkConfig[] = [

		...networks,

	// Sync with settings when they change		...( settings?.custom_networks ?? customNetworks ).map(

	useEffect( () => {			customAsNetworkConfig

		if ( settings ) {		),

			if ( settings.network_order && settings.network_order.length > 0 ) {	];

				setOrderedNetworkIds( settings.network_order );

			}	// Keep local customNetworks in sync with settings when available

			if ( settings.enabled_networks ) {	useEffect( () => {

				setEnabledNetworkIds( settings.enabled_networks );		if ( settings ) {

			}			setCustomNetworks( settings.custom_networks ?? [] );

		}		}

	}, [ settings ] );	}, [ settings ] );



	// Ensure all networks are in the ordered list	const sensors = useSensors(

	useEffect( () => {		useSensor( PointerSensor ),

		const allNetworkIds = networks.map( ( n ) => n.id );		useSensor( KeyboardSensor, {

		const missingIds = allNetworkIds.filter(			coordinateGetter: sortableKeyboardCoordinates,

			( id ) => ! orderedNetworkIds.includes( id )		} )

		);	);

		if ( missingIds.length > 0 ) {

			setOrderedNetworkIds( ( prev ) => [ ...prev, ...missingIds ] );	const handleNetworkToggle = ( networkId: string, enabled: boolean ) => {

		}		if ( enabled ) {

	}, [ networks, orderedNetworkIds ] );			setEnabledNetworks( ( prev ) => [ ...prev, networkId ] );

		} else {

	const sensors = useSensors(			setEnabledNetworks( ( prev ) =>

		useSensor( PointerSensor ),				prev.filter( ( id ) => id !== networkId )

		useSensor( KeyboardSensor, {			);

			coordinateGetter: sortableKeyboardCoordinates,		}

		} )	};

	);

	const handleDragEnd = ( event: any ) => {

	const handleToggle = ( networkId: string, enabled: boolean ) => {		const { active, over } = event;

		if ( enabled ) {

			setEnabledNetworkIds( ( prev ) => [ ...prev, networkId ] );		if ( active.id !== over.id ) {

		} else {			setEnabledNetworks( ( items ) => {

			setEnabledNetworkIds( ( prev ) =>				const oldIndex = items.indexOf( active.id );

				prev.filter( ( id ) => id !== networkId )				const newIndex = items.indexOf( over.id );

			);

		}				return arrayMove( items, oldIndex, newIndex );

	};			} );

		}

	const handleDragEnd = ( event: DragEndEvent ) => {	};

		const { active, over } = event;

	const handleNetworkLabelChange = async (

		if ( over && active.id !== over.id ) {		networkId: string,

			setOrderedNetworkIds( ( items ) => {		label: string

				const oldIndex = items.indexOf( active.id as string );	) => {

				const newIndex = items.indexOf( over.id as string );		try {

			// Update via API if available

				return arrayMove( items, oldIndex, newIndex );			if ( apiNetworks.length > 0 ) {

			} );				await updateNetwork( networkId, { label } );

		}			}

	};

			showSuccess( `${ label } label updated!` );

	const handleSave = async () => {		} catch ( error ) {

		setIsSaving( true );			showError( 'Failed to update network label', 'Please try again.' );

		try {		}

			updateSetting( 'enabled_networks', enabledNetworkIds );	};

			updateSetting( 'network_order', orderedNetworkIds );

	const handleAddCustomNetwork = async () => {

			await saveSettings();		if (

			! customNetworkForm.name.trim() ||

			showSuccess( 'Network settings saved successfully!' );			! customNetworkForm.share_url.trim()

		} catch ( error ) {		) {

			showError( 'Failed to save settings', 'Please try again.' );			showError(

		} finally {				'Please fill in all required fields',

			setIsSaving( false );				'Name and Share URL are required.'

		}			);

	};			return;

		}

	// Build ordered list of networks

	const orderedNetworks = orderedNetworkIds		const newCustom: CustomNetwork = {

		.map( ( id ) => networks.find( ( n ) => n.id === id ) )			id: `custom-${ Date.now() }`,

		.filter( ( n ): n is NetworkConfig => n !== undefined );			name: customNetworkForm.name.trim(),

			label:

	return (				customNetworkForm.label.trim() || customNetworkForm.name.trim(),

		<LoadingOverlay			share_url: customNetworkForm.share_url.trim(),

			isLoading={ isSaving }			color: customNetworkForm.color,

			message="Saving network settings..."			icon_class: customNetworkForm.icon_class.trim() || 'fas fa-share',

		>			enabled: true,

			<div className="bg-white border border-gray-200 rounded shadow-sm p-6">		};

				<h2 className="text-xl font-semibold text-gray-900 mb-2">

					Social Networks		// Update settings so custom networks persist

				</h2>		const updatedCustoms = [

				<p className="text-sm text-gray-600 mb-6">			...( settings?.custom_networks ?? customNetworks ),

					Enable networks and drag to reorder how they appear on your			newCustom,

					site.		];

				</p>		setCustomNetworks( updatedCustoms );

		updateSetting( 'custom_networks', updatedCustoms );

				<div className="space-y-6">		// Add to enabled list and order by default

					<div>		const updatedEnabled = [

						<h3 className="text-lg font-medium text-gray-900 mb-4">			...( settings?.enabled_networks ?? enabledNetworks ),

							Available Networks &amp; Order			newCustom.id,

						</h3>		];

						<p className="text-sm text-gray-600 mb-4">		updateSetting( 'enabled_networks', updatedEnabled );

							Check to enable a network, then drag to reorder.		updateSetting( 'network_order', [

							Disabled networks appear at the bottom.			...( settings?.network_order ?? [] ),

						</p>			...updatedEnabled,

		] );

						<DndContext		setCustomNetworkForm( {

							sensors={ sensors }			name: '',

							collisionDetection={ closestCenter }			label: '',

							onDragEnd={ handleDragEnd }			share_url: '',

						>			color: '#666666',

							<SortableContext			icon_class: '',

								items={ orderedNetworkIds }		} );

								strategy={ verticalListSortingStrategy }		setShowCustomNetworkForm( false );

							>		try {

								<div className="space-y-2">			await saveSettings();

									{ orderedNetworks.map( ( network ) => (			showSuccess( 'Custom network added successfully!' );

										<SortableNetworkItem		} catch ( e ) {

											key={ network.id }			showError( 'Failed to save custom network', 'Please try again.' );

											network={ network }		}

											enabled={ enabledNetworkIds.includes(	};

												network.id

											) }	const handleDeleteCustomNetwork = async ( networkId: string ) => {

											onToggle={ handleToggle }		const updatedCustoms = (

										/>			settings?.custom_networks ?? customNetworks

									) ) }		).filter( ( n ) => n.id !== networkId );

								</div>		updateSetting( 'custom_networks', updatedCustoms );

							</SortableContext>		const updatedEnabled = (

						</DndContext>			settings?.enabled_networks ?? enabledNetworks

					</div>		).filter( ( id ) => id !== networkId );

		updateSetting( 'enabled_networks', updatedEnabled );

					<div className="flex justify-between items-center pt-6 border-t border-gray-200">		const updatedOrder = (

						<p className="text-sm text-gray-600">			settings?.network_order ?? enabledNetworks

							{ enabledNetworkIds.length } network		).filter( ( id ) => id !== networkId );

							{ enabledNetworkIds.length !== 1 ? 's' : '' }{' '}		updateSetting( 'network_order', updatedOrder );

							enabled		setCustomNetworks( updatedCustoms );

						</p>		setEnabledNetworks( updatedEnabled );

						<Button		try {

							onClick={ handleSave }			await saveSettings();

							disabled={ isSaving }			showSuccess( 'Custom network removed successfully!' );

							className="px-6 py-2"		} catch ( e ) {

						>			showError( 'Failed to remove custom network', 'Please try again.' );

							{ isSaving ? 'Saving...' : 'Save Settings' }		}

						</Button>	};

					</div>

				</div>	const handleSave = async () => {

			</div>		setIsSaving( true );

		</LoadingOverlay>		try {

	);			// Update settings to persist enabled networks and order

};			updateSetting( 'enabled_networks', enabledNetworks );

			updateSetting( 'network_order', enabledNetworks );
			updateSetting(
				'custom_networks',
				settings?.custom_networks ?? customNetworks
			);

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
										{ network.id.startsWith(
											'custom-'
										) && (
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
												setCustomNetworkForm(
													( prev ) => ( {
														...prev,
														name: value,
													} )
												)
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
												setCustomNetworkForm(
													( prev ) => ( {
														...prev,
														label: value,
													} )
												)
											}
											placeholder="e.g., Share"
										/>
									</FormField>

									<FormField
										label="Share URL"
										description="URL template with {url} and {title} placeholders"
									>
										<TextInput
											value={
												customNetworkForm.share_url
											}
											onChange={ ( value ) =>
												setCustomNetworkForm(
													( prev ) => ( {
														...prev,
														share_url: value,
													} )
												)
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
												setCustomNetworkForm(
													( prev ) => ( {
														...prev,
														color: value,
													} )
												)
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
												value={
													customNetworkForm.icon_class
												}
												onChange={ ( value ) =>
													setCustomNetworkForm(
														( prev ) => ( {
															...prev,
															icon_class: value,
														} )
													)
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
									Your Custom Networks (
									{ customNetworks.length })
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
														{ network.name.charAt(
															0
														) }
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
