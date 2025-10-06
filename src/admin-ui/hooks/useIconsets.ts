import { useState, useEffect, useCallback } from 'react';
import apiFetch from '@wordpress/api-fetch';

/**
 * Hook for managing available iconsets
 */
export const useIconsets = () => {
	const [ iconsets, setIconsets ] = useState< Record<
		string,
		{ label: string; description: string }
	> | null >( null );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState< string | null >( null );

	// Load iconsets from API
	const loadIconsets = useCallback( async () => {
		try {
			setLoading( true );
			setError( null );

			const response = ( await apiFetch( {
				path: '/html-social-share/v1/iconsets',
				method: 'GET',
			} ) ) as any;

			setIconsets( response );
		} catch ( err ) {
			setError(
				err instanceof Error ? err.message : 'Failed to load iconsets'
			);
		} finally {
			setLoading( false );
		}
	}, [] );

	// Load iconsets on mount
	useEffect( () => {
		loadIconsets();
	}, [ loadIconsets ] );

	return {
		iconsets,
		loading,
		error,
		refreshIconsets: loadIconsets,
	};
};
