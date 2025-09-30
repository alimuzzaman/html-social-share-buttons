import React, { createContext, useContext, ReactNode } from 'react';
import { PluginSettings, SaveSettingsResponse } from '../types';
import { useSettings } from '../hooks/useSettings';

// Settings Context
interface SettingsContextType {
	settings: PluginSettings | null;
	loading: boolean;
	saving: boolean;
	error: string | null;
	isDirty: boolean;
	updateSetting: < K extends keyof PluginSettings >(
		key: K,
		value: PluginSettings[ K ]
	) => void;
	updateSettings: ( updates: Partial< PluginSettings > ) => void;
	saveSettings: () => Promise< SaveSettingsResponse >;
	resetSettings: () => Promise< void >;
	refreshSettings: () => Promise< void >;
}

const SettingsContext = createContext< SettingsContextType | undefined >(
	undefined
);

// Settings Provider
interface SettingsProviderProps {
	children: ReactNode;
}

export const SettingsProvider: React.FC< SettingsProviderProps > = ( {
	children,
} ) => {
	const settingsHook = useSettings();

	return (
		<SettingsContext.Provider value={ settingsHook }>
			{ children }
		</SettingsContext.Provider>
	);
};

// Custom hook to use settings context
export const useSettingsContext = (): SettingsContextType => {
	const context = useContext( SettingsContext );
	if ( context === undefined ) {
		throw new Error(
			'useSettingsContext must be used within a SettingsProvider'
		);
	}
	return context;
};
