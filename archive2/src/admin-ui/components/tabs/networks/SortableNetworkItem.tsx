import React from 'react';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { GripVertical } from 'lucide-react';
import { Checkbox } from '../../ui';
import { NetworkConfig } from '../../../types';

const pluginUrl =
	typeof window !== 'undefined' &&
	( window as any ).hssAdminConfig &&
	( window as any ).hssAdminConfig.pluginUrl
		? ( window as any ).hssAdminConfig.pluginUrl
		: '';

interface SortableNetworkItemProps {
	network: NetworkConfig;
	enabled: boolean;
	onToggle: ( networkId: string, enabled: boolean ) => void;
}

export const SortableNetworkItem: React.FC< SortableNetworkItemProps > = ( {
	network,
	enabled,
	onToggle,
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
		transition: isDragging ? transition : 'transform 200ms ease',
		opacity: isDragging ? 0.4 : 1,
	};

	return (
		<div
			ref={ setNodeRef }
			style={ style }
			className={ `flex items-center px-4 py-3 bg-white border rounded transition-all ${ 
				enabled ? 'border-blue-500 bg-blue-50' : 'border-gray-200'
			} ${
				isDragging
					? 'shadow-sm scale-105'
					: 'hover:shadow-md hover:border-gray-300 hover:scale-[1.01]'
			}` }
		>
			<div
				className="cursor-grab active:cursor-grabbing mr-3 text-gray-400 hover:text-gray-600 transition-colors"
				{ ...attributes }
				{ ...listeners }
			>
				<GripVertical size={ 20 } />
			</div>

			<div
				className="w-8 h-8 rounded flex items-center justify-center mr-3 flex-shrink-0"
				style={ { backgroundColor: network.color } }
			>
				<img
					src={ `${ pluginUrl }assets/iconset/default_square/${ network.id }.png` }
					alt={ `${ network.name } icon` }
					className="w-5 h-5"
					onError={ ( e ) => {
						( e.currentTarget as HTMLImageElement ).style.display = 'none';
						const placeholder = document.createElement( 'span' );
						placeholder.className =
							'inline-flex items-center justify-center w-5 h-5 rounded-full bg-white text-xs text-gray-700 font-semibold';
						placeholder.textContent = network.name.charAt( 0 );
						e.currentTarget.parentElement?.appendChild( placeholder );
					} }
				/>
			</div>

			<div className="flex-1">
				<h4 className="font-medium text-gray-800">{ network.name }</h4>
				{ enabled && (
					<span className="text-xs text-blue-600">Enabled</span>
				) }
			</div>

			<Checkbox
				checked={ enabled }
				onChange={ ( checked ) => onToggle( network.id, checked ) }
				label=""
			/>
		</div>
	);
};
