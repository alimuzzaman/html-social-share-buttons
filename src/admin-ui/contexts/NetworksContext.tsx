import React, { createContext, useContext, ReactNode } from 'react';
import { NetworkConfig } from '../types';
import { useNetworks } from '../hooks/useNetworks';

// Networks Context
interface NetworksContextType {
	networks: NetworkConfig[];
	loading: boolean;
	error: string | null;
	updateNetwork: (
		networkId: string,
		updates: Partial< NetworkConfig >
	) => Promise< void >;
	refreshNetworks: () => Promise< void >;
}

const NetworksContext = createContext< NetworksContextType | undefined >(
	undefined
);

// Networks Provider
interface NetworksProviderProps {
	children: ReactNode;
}

export const NetworksProvider: React.FC< NetworksProviderProps > = ( {
	children,
} ) => {
	const networksHook = useNetworks();

	return (
		<NetworksContext.Provider value={ networksHook }>
			{ children }
		</NetworksContext.Provider>
	);
};

// Custom hook to use networks context
export const useNetworksContext = (): NetworksContextType => {
	const context = useContext( NetworksContext );
	if ( context === undefined ) {
		throw new Error(
			'useNetworksContext must be used within a NetworksProvider'
		);
	}
	return context;
};
