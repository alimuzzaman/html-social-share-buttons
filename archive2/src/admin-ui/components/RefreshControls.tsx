import React from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { RefreshCw, Settings as LucideSettings } from 'lucide-react';
import { AdminIcon } from './ui/Icons';
import { Button, Checkbox } from './ui';

interface RefreshControlsProps {
	selectedPosts: number[];
	onSelectionChange: ( postIds: number[] ) => void;
	onBulkRefresh: () => void;
	refreshing: boolean;
}

export const RefreshControls: React.FC< RefreshControlsProps > = ( {
	selectedPosts,
	onSelectionChange,
	onBulkRefresh,
	refreshing,
} ) => {
	const handleSelectAll = ( checked: boolean ) => {
		if ( checked ) {
			// This would need to get all post IDs from the data
			// For now, just clear selection
			onSelectionChange( [] );
		} else {
			onSelectionChange( [] );
		}
	};

	return (
		<div className="hss-refresh-controls bg-white border border-gray-200 rounded-lg p-4 mb-6">
			<div className="flex items-center justify-between">
				<div className="flex items-center space-x-4">
					<Checkbox
						checked={ selectedPosts.length > 0 }
						onChange={ handleSelectAll }
						label={ __(
							'Select All Posts',
							'html-social-share-buttons'
						) }
					/>

					{ selectedPosts.length > 0 && (
						<p className="text-sm text-gray-600">
							{ sprintf(
								/* translators: %d: number of posts selected */
								_n(
									'%d post selected for refresh',
									'%d posts selected for refresh',
									selectedPosts.length,
									'html-social-share-buttons'
								),
								selectedPosts.length
							) }
						</p>
					) }
				</div>

				<div className="flex items-center space-x-3">
					<Button
						onClick={ onBulkRefresh }
						disabled={ refreshing || selectedPosts.length === 0 }
						loading={ refreshing }
						variant="secondary"
						className="flex items-center space-x-2"
					>
						<AdminIcon
							candidates={ [
								'update',
								'refresh',
								'arrowClockwise',
								'redo',
							] }
							lucide={ <RefreshCw size={ 16 } /> }
							size={ 16 }
						/>
						<span>
							{ refreshing
								? __(
										'Refreshing…',
										'html-social-share-buttons'
								  )
								: __(
										'Refresh Selected',
										'html-social-share-buttons'
								  ) }
						</span>
					</Button>

					<Button
						variant="primary"
						className="flex items-center space-x-2"
						onClick={ () => {
							/* open settings - noop for now */
						} }
					>
						<AdminIcon
							candidates={ [ 'settings', 'cog', 'gear' ] }
							lucide={ <LucideSettings size={ 16 } /> }
							size={ 16 }
						/>
						<span>
							{ __( 'Settings', 'html-social-share-buttons' ) }
						</span>
					</Button>
				</div>
			</div>
		</div>
	);
};
