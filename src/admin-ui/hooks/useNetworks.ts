import { useState, useEffect, useCallback } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { NetworkConfig } from '../types';

/**
 * Hook for managing network configurations
 */
export const useNetworks = () => {
  const [networks, setNetworks] = useState<NetworkConfig[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Load networks from API
  const loadNetworks = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);

      const response = await apiFetch({
        path: '/html-social-share/v1/networks',
        method: 'GET',
      }) as Record<string, NetworkConfig>;

      // Convert response object to array
      const networksArray = Object.values(response);
      setNetworks(networksArray);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load networks');
      console.error('Failed to load networks:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  // Update a network configuration
  const updateNetwork = useCallback(async (networkId: string, updates: Partial<NetworkConfig>) => {
    try {
      await apiFetch({
        path: `/html-social-share/v1/networks/${networkId}`,
        method: 'POST',
        data: updates,
      });

      // Update local state
      setNetworks(prev => prev.map(network =>
        network.id === networkId
          ? { ...network, ...updates }
          : network
      ));
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to update network';
      setError(errorMessage);
      throw new Error(errorMessage);
    }
  }, []);

  // Load networks on mount
  useEffect(() => {
    loadNetworks();
  }, [loadNetworks]);

  return {
    networks,
    loading,
    error,
    updateNetwork,
    refreshNetworks: loadNetworks,
  };
};