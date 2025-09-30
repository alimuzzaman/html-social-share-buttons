import React, { useState } from 'react';
import { Tabs } from './ui';
import { GeneralTab } from './tabs/GeneralTab';
import { NetworksTab } from './tabs/NetworksTab';
import { TabConfig } from '../types';

/**
 * Main React admin interface for HTML Social Share Buttons settings
 */
export const ReactAdminInterface: React.FC = () => {
  const [activeTab, setActiveTab] = useState('general');

  // Tab configuration
  const tabs: TabConfig[] = [
    {
      id: 'general',
      title: 'General Settings',
      icon: 'admin-settings',
      description: 'Configure basic plugin settings and display options'
    },
    {
      id: 'networks',
      title: 'Social Networks',
      icon: 'share',
      description: 'Manage available social networks and their settings'
    },
    {
      id: 'profiles',
      title: 'Profiles',
      icon: 'admin-users',
      description: 'Create and manage social sharing profiles'
    },
    {
      id: 'placement',
      title: 'Placement',
      icon: 'admin-appearance',
      description: 'Control where and how buttons appear on your site'
    },
    {
      id: 'styling',
      title: 'Styling',
      icon: 'admin-customizer',
      description: 'Customize the appearance of your share buttons'
    },
    {
      id: 'advanced',
      title: 'Advanced',
      icon: 'admin-generic',
      description: 'Advanced settings and integrations'
    }
  ];

  const renderTabContent = () => {
    switch (activeTab) {
      case 'general':
        return <GeneralTab />;
      case 'networks':
        return <NetworksTab />;
      case 'profiles':
        return <div>Profiles tab content coming soon...</div>;
      case 'placement':
        return <div>Placement tab content coming soon...</div>;
      case 'styling':
        return <div>Styling tab content coming soon...</div>;
      case 'advanced':
        return <div>Advanced tab content coming soon...</div>;
      default:
        return <GeneralTab />;
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">HTML Social Share Buttons</h1>
        <p className="text-gray-600 text-lg">
          Configure social sharing buttons for your WordPress site.
        </p>
      </div>

      <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <Tabs
          tabs={tabs}
          activeTab={activeTab}
          onTabChange={setActiveTab}
        />

        <div className="p-6">
          {renderTabContent()}
        </div>
      </div>
    </div>
  );
};