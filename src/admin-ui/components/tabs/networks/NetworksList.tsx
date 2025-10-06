import React, { useState } from 'react';
import {
	DndContext,
	closestCenter,
	KeyboardSensor,
	PointerSensor,
	useSensor,
	useSensors,
	DragEndEvent,
	DragStartEvent,
	DragOverlay,
} from '@dnd-kit/core';
import {
	SortableContext,
	sortableKeyboardCoordinates,
	verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { SortableNetworkItem } from './SortableNetworkItem';
import { NetworkConfig } from '../../../types';
import { GripVertical } from 'lucide-react';

interface NetworksListProps {
	orderedNetworks: NetworkConfig[];
	enabledNetworkIds: string[];
	onToggle: ( networkId: string, enabled: boolean ) => void;
	onDragEnd: ( event: DragEndEvent ) => void;
}

export const NetworksList: React.FC< NetworksListProps > = ( {
	orderedNetworks,
	enabledNetworkIds,
	onToggle,
	onDragEnd,
} ) => {
	const [ activeId, setActiveId ] = useState< string | null >( null );

	const sensors = useSensors(
		useSensor( PointerSensor, {
			activationConstraint: {
				distance: 8,
			},
		} ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	const orderedNetworkIds = orderedNetworks.map( ( n ) => n.id );

	const handleDragStart = ( event: DragStartEvent ) => {
		setActiveId( event.active.id as string );
	};

	const handleDragEnd = ( event: DragEndEvent ) => {
		setActiveId( null );
		onDragEnd( event );
	};

	const handleDragCancel = () => {
		setActiveId( null );
	};

	const activeNetwork = activeId
		? orderedNetworks.find( ( n ) => n.id === activeId )
		: null;

	return (
		<div>
			<h3 className="text-lg font-medium text-gray-900 mb-4">
				Available Networks &amp; Order
			</h3>
			<p className="text-sm text-gray-600 mb-4">
				Check to enable a network, then drag to reorder. Disabled
				networks appear at the bottom.
			</p>

			<DndContext
				sensors={ sensors }
				collisionDetection={ closestCenter }
				onDragStart={ handleDragStart }
				onDragEnd={ handleDragEnd }
				onDragCancel={ handleDragCancel }
			>
				<SortableContext
					items={ orderedNetworkIds }
					strategy={ verticalListSortingStrategy }
				>
					<div className="space-y-2">
						{ orderedNetworks.map( ( network ) => (
							<SortableNetworkItem
								key={ network.id }
								network={ network }
								enabled={ enabledNetworkIds.includes(
									network.id
								) }
								onToggle={ onToggle }
							/>
						) ) }
					</div>
				</SortableContext>
				<DragOverlay dropAnimation={ null }>
					{ activeNetwork ? (
						<div
							className="flex items-center px-4 py-3 bg-white border-2 border-blue-500 rounded shadow-2xl"
							style={ { cursor: 'grabbing' } }
						>
							<div className="mr-3 text-gray-400">
								<GripVertical size={ 20 } />
							</div>
							<div
								className="w-8 h-8 rounded flex items-center justify-center mr-3 flex-shrink-0"
								style={ {
									backgroundColor: activeNetwork.color,
								} }
							>
								<img
									src={ `${
										typeof window !== 'undefined' &&
										( window as any ).hssAdminConfig &&
										( window as any ).hssAdminConfig
											.pluginUrl
											? ( window as any ).hssAdminConfig
													.pluginUrl
											: ''
									}assets/iconset/default_square/${
										activeNetwork.id
									}.png` }
									alt={ `${ activeNetwork.name } icon` }
									className="w-5 h-5"
									onError={ ( e ) => {
										const target = e.currentTarget as HTMLImageElement;
										target.style.display = 'none';
										const placeholder = document.createElement(
											'span'
										);
										placeholder.className =
											'inline-flex items-center justify-center w-5 h-5 rounded-full bg-white text-xs text-gray-700 font-semibold';
										placeholder.textContent =
											activeNetwork.name.charAt( 0 );
										target.parentElement?.appendChild(
											placeholder
										);
									} }
								/>
							</div>
							<div className="flex-1">
								<h4 className="font-medium text-gray-800">
									{ activeNetwork.name }
								</h4>
								{ enabledNetworkIds.includes(
									activeNetwork.id
								) && (
									<span className="text-xs text-blue-600">
										Enabled
									</span>
								) }
							</div>
						</div>
					) : null }
				</DragOverlay>
			</DndContext>
		</div>
	);
};
