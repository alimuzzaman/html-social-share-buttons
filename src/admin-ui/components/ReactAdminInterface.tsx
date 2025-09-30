import React, { useState } from 'react';
import { Tabs } from './ui';
import {
	DisplayTab,
	NetworksTab,
	ProfilesTab,
	IntegrationsTab,
	DesignTab,
	AdvancedTab,
	ShortcodeTab,
} from './tabs';
import { TabConfig } from '../types';
import {
	Home,
	Share,
	Users,
	Palette,
	Plug,
	Settings,
	Code,
} from 'lucide-react';

/**
 * Main React admin interface for HTML Social Share Buttons settings
 */
export const ReactAdminInterface: React.FC = () => {
	const [ activeTab, setActiveTab ] = useState( 'display' );

	// Tab configuration
	const tabs: TabConfig[] = [
		{
			id: 'display',
			title: 'Display',
			icon: <Home className="w-4 h-4" />,
			description:
				'Decide where share buttons appear and which content types they follow',
		},
		{
			id: 'networks',
			title: 'Networks',
			icon: <Share className="w-4 h-4" />,
			description:
				'Enable networks, reorder cards, and manage custom sharing targets',
		},
		{
			id: 'profiles',
			title: 'Profiles',
			icon: <Users className="w-4 h-4" />,
			description:
				'Create reusable sharing presets for different contexts',
		},
		{
			id: 'design',
			title: 'Design',
			icon: <Palette className="w-4 h-4" />,
			description:
				'Set default button styles, spacing, and custom CSS overrides',
		},
		{
			id: 'integrations',
			title: 'Integrations',
			icon: <Plug className="w-4 h-4" />,
			description:
				'BetterLinks, page builders, and other plugin integrations',
		},
		{
			id: 'advanced',
			title: 'Advanced',
			icon: <Settings className="w-4 h-4" />,
			description:
				'Analytics, link behavior, caching, and debugging tools',
		},
		{
			id: 'shortcode',
			title: 'Shortcode',
			icon: <Code className="w-4 h-4" />,
			description:
				'Dynamic shortcode generation based on current settings',
		},
	];

	const renderTabContent = () => {
		switch ( activeTab ) {
			case 'display':
				return <DisplayTab />;
			case 'networks':
				return <NetworksTab />;
			case 'profiles':
				return <ProfilesTab />;
			case 'design':
				return <DesignTab />;
			case 'integrations':
				return <IntegrationsTab />;
			case 'advanced':
				return <AdvancedTab />;
			case 'shortcode':
				return <ShortcodeTab />;
			default:
				return <DisplayTab />;
		}
	};

	return (
		<div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
			<div className="mb-8">
				<h1 className="text-3xl font-bold text-gray-900 mb-2">
					HTML Social Share Buttons
				</h1>
				<p className="text-gray-600 text-lg">
					Configure social sharing buttons for your WordPress site.
				</p>
			</div>

			<div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
				<Tabs
					tabs={ tabs }
					activeTab={ activeTab }
					onTabChange={ setActiveTab }
				/>

				<div className="p-6">{ renderTabContent() }</div>
			</div>
		</div>
	);
};
