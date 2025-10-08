import React, { useState, useEffect } from 'react';
import { Button, LoadingOverlay } from '../ui';
import { useNotifications } from '../../contexts';
import { useNetworks } from '../../hooks/useNetworks';
import { useSettings } from '../../hooks';
import { NetworkConfig } from '../../types';
import { arrayMove } from '@dnd-kit/sortable';
import { DragEndEvent } from '@dnd-kit/core';
import { NetworksList, defaultNetworks } from './networks';

export const NetworksTab: React.FC = () => {
	const { networks: apiNetworks } = useNetworks();
	const { settings, updateSetting, saveSettings } = useSettings();
	const { showSuccess, showError } = useNotifications();

	const networks = apiNetworks.length > 0 ? apiNetworks : defaultNetworks;

	const [ orderedNetworkIds, setOrderedNetworkIds ] = useState< string[] >(
		settings?.network_order && settings.network_order.length > 0
			? settings.network_order
			: networks.map( ( n ) => n.id )
	);
	const [ enabledNetworkIds, setEnabledNetworkIds ] = useState< string[] >(
		settings?.enabled_networks ?? [ 'facebook', 'twitter', 'linkedin' ]
	);
	const [ isSaving, setIsSaving ] = useState( false );

	useEffect( () => {
		if ( settings ) {
			if ( settings.network_order && settings.network_order.length > 0 ) {
				setOrderedNetworkIds( settings.network_order );
			}
			if ( settings.enabled_networks ) {
				setEnabledNetworkIds( settings.enabled_networks );
			}
		}
	}, [ settings ] );

	useEffect( () => {
		const allNetworkIds = networks.map( ( n ) => n.id );
		const missingIds = allNetworkIds.filter(
( id ) => ! orderedNetworkIds.includes( id )
		);
		if ( missingIds.length > 0 ) {
			setOrderedNetworkIds( ( prev ) => [ ...prev, ...missingIds ] );
		}
	}, [ networks, orderedNetworkIds ] );

	const handleToggle = ( networkId: string, enabled: boolean ) => {
		if ( enabled ) {
			setEnabledNetworkIds( ( prev ) => [ ...prev, networkId ] );
		} else {
			setEnabledNetworkIds( ( prev ) =>
				prev.filter( ( id ) => id !== networkId )
			);
		}
	};

	const handleDragEnd = ( event: DragEndEvent ) => {
		const { active, over } = event;

		if ( over && active.id !== over.id ) {
			setOrderedNetworkIds( ( items ) => {
				const oldIndex = items.indexOf( active.id as string );
				const newIndex = items.indexOf( over.id as string );

				return arrayMove( items, oldIndex, newIndex );
			} );
		}
	};

	const handleSave = async () => {
		setIsSaving( true );
		try {
			updateSetting( 'enabled_networks', enabledNetworkIds );
			updateSetting( 'network_order', orderedNetworkIds );

			await saveSettings();

			showSuccess( 'Network settings saved successfully!' );
		} catch ( error ) {
			showError( 'Failed to save settings', 'Please try again.' );
		} finally {
			setIsSaving( false );
		}
	};

	const orderedNetworks = orderedNetworkIds
		.map( ( id ) => networks.find( ( n ) => n.id === id ) )
		.filter( ( n ): n is NetworkConfig => n !== undefined );

	return (
<LoadingOverlay
			isLoading={ isSaving }
			message="Saving network settings..."
		>
			<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
				<h2 className="text-xl font-semibold text-gray-900 mb-2">
					Social Networks
				</h2>
				<p className="text-sm text-gray-600 mb-6">
					Enable networks and drag to reorder how they appear on your
					site.
				</p>

				<div className="space-y-6">
					<NetworksList
						orderedNetworks={ orderedNetworks }
						enabledNetworkIds={ enabledNetworkIds }
						onToggle={ handleToggle }
						onDragEnd={ handleDragEnd }
					/>

					<div className="flex justify-between items-center pt-6 border-t border-gray-200">
						<p className="text-sm text-gray-600">
							{ enabledNetworkIds.length } network
							{ enabledNetworkIds.length !== 1 ? 's' : '' }{' '}
							enabled
						</p>
						<Button
							onClick={ handleSave }
							disabled={ isSaving }
							className="px-6 py-2"
						>
							{ isSaving ? 'Saving...' : 'Save Settings' }
						</Button>
					</div>
				</div>
			</div>
		</LoadingOverlay>
	);
};
