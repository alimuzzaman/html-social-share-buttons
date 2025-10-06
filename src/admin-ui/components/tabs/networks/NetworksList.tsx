import React from 'react';
import {
	DndContext,
	closestCenter,
	KeyboardSensor,
	PointerSensor,
	useSensor,
	useSensors,
	DragEndEvent,
} from '@dnd-kit/core';
import {
	SortableContext,
	sortableKeyboardCoordinates,
	verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { SortableNetworkItem } from './SortableNetworkItem';
import { NetworkConfig } from '../../../types';

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
	const sensors = useSensors(
		useSensor( PointerSensor ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	const orderedNetworkIds = orderedNetworks.map( ( n ) => n.id );

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
				onDragEnd={ onDragEnd }
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
			</DndContext>
		</div>
	);
};
