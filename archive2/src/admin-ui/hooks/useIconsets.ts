import { useState, useEffect } from 'react';

/**
 * Hook for managing available iconsets
 * Iconsets are loaded from localized script data (hssAdminConfig.iconsets)
 * since they're static and won't change after page load
 */
export const useIconsets = () => {
	const [ iconsets, setIconsets ] = useState< Record<
		string,
		{ label: string; description: string }
	> | null >( null );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState< string | null >( null );

	// Load iconsets from localized data on mount
	useEffect( () => {
		try {
			const config =
				typeof window !== 'undefined' &&
				( window as any ).hssAdminConfig
					? ( window as any ).hssAdminConfig
					: null;

			if ( config && config.iconsets ) {
				setIconsets( config.iconsets );
			} else {
				setError( 'Iconsets data not found in configuration' );
			}
		} catch ( err ) {
			setError(
				err instanceof Error ? err.message : 'Failed to load iconsets'
			);
		} finally {
			setLoading( false );
		}
	}, [] );

	return {
		iconsets,
		loading,
		error,
	};
};
