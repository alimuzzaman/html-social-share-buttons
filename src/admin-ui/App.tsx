import React, { useState } from 'react';
import { Tabs, TabPanel } from './components/ui';
import { TabConfig } from './types';

// Import tab components
import {
	DisplayTab,
	NetworksTab,
	ProfilesTab,
	IntegrationsTab,
	DesignTab,
	ShortcodeTab,
	AdvancedTab,
} from './components/tabs';
import {
	Home,
	Share,
	Users,
	Palette,
	Plug,
	Code,
	Settings,
} from 'lucide-react';

const tabs: TabConfig[] = [
	{
		id: 'display',
		title: 'Display',
		icon: <Home className="w-4 h-4" />,
		description: 'Configure where buttons appear by default',
	},
	{
		id: 'networks',
		title: 'Networks',
		icon: <Share className="w-4 h-4" />,
		description: 'Enable networks, reorder cards, and add customs',
	},
	{
		id: 'profiles',
		title: 'Profiles',
		icon: <Users className="w-4 h-4" />,
		description: 'Manage button profiles and configurations',
	},
	{
		id: 'design',
		title: 'Design',
		icon: <Palette className="w-4 h-4" />,
		description: 'Style, defaults, and custom CSS',
	},
	{
		id: 'integrations',
		title: 'Integrations',
		icon: <Plug className="w-4 h-4" />,
		description: 'BetterLinks and page builder integrations',
	},
	{
		id: 'shortcode',
		title: 'Shortcode',
		icon: <Code className="w-4 h-4" />,
		description: 'Generate and customize shortcodes',
	},
	{
		id: 'advanced',
		title: 'Advanced',
		icon: <Settings className="w-4 h-4" />,
		description: 'Advanced settings and performance options',
	},
];

export const App: React.FC = () => {
	const [ activeTab, setActiveTab ] = useState( 'display' );

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
			case 'shortcode':
				return <ShortcodeTab />;
			case 'advanced':
				return <AdvancedTab />;
			default:
				return <DisplayTab />;
		}
	};

	return (
		<div className="wrap html-social-share-admin">
			<h1 className="text-2xl font-semibold text-gray-900 mb-2">
				HTML Social Share Buttons
			</h1>

			<hr className="border-t border-gray-200 my-6" />

			<div className="html-social-share-tabs-wrapper">
				<Tabs
					tabs={ tabs }
					activeTab={ activeTab }
					onTabChange={ setActiveTab }
					className="mb-6"
				/>

				<div className="tab-content">
					{ tabs.map( ( tab ) => (
						<TabPanel
							key={ tab.id }
							id={ tab.id }
							activeTab={ activeTab }
						>
							{ renderTabContent() }
						</TabPanel>
					) ) }
				</div>
			</div>
		</div>
	);
};
