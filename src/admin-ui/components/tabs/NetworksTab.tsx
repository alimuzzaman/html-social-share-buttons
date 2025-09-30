import React, { useState } from 'react';
import { FormField, Button, TextInput } from '../ui';
import { useNotifications } from '../../contexts';
import { useNetworks } from '../../hooks/useNetworks';
import { NetworkConfig } from '../../types';
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
		>
			<div
				className="w-4 h-4 rounded mr-2 flex-shrink-0"
				style={ { backgroundColor: network.color } }
			/>
			<span className="text-sm flex-1">{ network.label }</span>
			<div
				{ ...attributes }
				{ ...listeners }
				className="cursor-grab active:cursor-grabbing ml-2"
			>
				<GripVertical size={ 14 } className="text-gray-400" />
			</div>
		</div>
	);
};

export const NetworksTab: React.FC = () => {
	const { networks: apiNetworks, updateNetwork } = useNetworks();
	const { showSuccess, showError } = useNotifications();

	// Keep local state for immediate UI updates and form handling
	const [ localNetworks ] = useState< NetworkConfig[] >( defaultNetworks );
	const [ enabledNetworks, setEnabledNetworks ] = useState< string[] >( [
		'facebook',
		'twitter',
		'linkedin',
	] );
	const [ isSaving, setIsSaving ] = useState( false ); // Use API networks if available, otherwise fall back to local defaults
	const networks = apiNetworks.length > 0 ? apiNetworks : localNetworks;

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
	const handleSave = async () => {
		setIsSaving( true );
		try {
			// Save enabled networks configuration
			const networkUpdates = networks.map(
				( network: NetworkConfig ) => ( {
					...network,
					enabled: enabledNetworks.includes( network.id ),
				} )
			);

			// If API is available, save via API
			if ( apiNetworks.length > 0 ) {
				await Promise.all(
					networkUpdates.map( ( network: NetworkConfig ) =>
						updateNetwork( network.id, {
							enabled: network.enabled,
						} )
					)
				);
			}

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
						{ networks.map( ( network: NetworkConfig ) => {
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
										if ( e.key === 'Enter' || e.key === ' ' ) {
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
											</h4>
											{ isEnabled && (
												<span className="text-xs text-blue-600 font-medium">
													Enabled
												</span>
											) }
										</div>
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
								<div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
									{ enabledNetworks.map( ( networkId ) => {
										const network = networks.find(
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
