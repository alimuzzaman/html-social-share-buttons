import React, { useState } from 'react';
import { Tabs, TabPanel } from './components/ui';
import { TabConfig } from './types';

// Import tab components
import {
  GeneralTab,
  NetworksTab,
  ProfilesTab,
  IntegrationsTab,
  AppearanceTab,
  PlacementTab,
  ShortcodeTab
} from './components/tabs';

const tabs: TabConfig[] = [
  {
    id: 'general',
    title: 'General',
    icon: 'dashicons-admin-settings',
    description: 'Basic plugin settings and display options'
  },
  {
    id: 'networks',
    title: 'Networks',
    icon: 'dashicons-share',
    description: 'Configure social media networks and sharing options'
  },
  {
    id: 'profiles',
    title: 'Profiles',
    icon: 'dashicons-groups',
    description: 'Manage button profiles and configurations'
  },
  {
    id: 'integrations',
    title: 'Integrations',
    icon: 'dashicons-admin-plugins',
    description: 'Third-party plugin integrations'
  },
  {
    id: 'appearance',
    title: 'Appearance',
    icon: 'dashicons-art',
    description: 'Style and appearance customization'
  },
  {
    id: 'placement',
    title: 'Placement',
    icon: 'dashicons-location',
    description: 'Configure where buttons appear'
  },
  {
    id: 'shortcode',
    title: 'Shortcode',
    icon: 'dashicons-editor-code',
    description: 'Generate and customize shortcodes'
  }
];

export const App: React.FC = () => {
  const [activeTab, setActiveTab] = useState('general');

  const renderTabContent = () => {
    switch (activeTab) {
      case 'general':
        return <GeneralTab />;
      case 'networks':
        return <NetworksTab />;
      case 'profiles':
        return <ProfilesTab />;
      case 'integrations':
        return <IntegrationsTab />;
      case 'appearance':
        return <AppearanceTab />;
      case 'placement':
        return <PlacementTab />;
      case 'shortcode':
        return <ShortcodeTab />;
      default:
        return <GeneralTab />;
    }
  };

  return (
    <div className="wrap html-social-share-admin">
      <h1 className="wp-heading-inline">
        HTML Social Share Buttons
      </h1>

      <hr className="wp-header-end" />

      <div className="html-social-share-tabs-wrapper">
        <Tabs
          tabs={tabs}
          activeTab={activeTab}
          onTabChange={setActiveTab}
          className="mb-6"
        />

        <div className="tab-content">
          {tabs.map((tab) => (
            <TabPanel key={tab.id} id={tab.id} activeTab={activeTab}>
              {renderTabContent()}
            </TabPanel>
          ))}
        </div>
      </div>
    </div>
  );
};