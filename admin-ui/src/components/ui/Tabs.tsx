import React from 'react';
import { TabConfig } from '../../types';

interface TabsProps {
  tabs: TabConfig[];
  activeTab: string;
  onTabChange: (tabId: string) => void;
  className?: string;
}

export const Tabs: React.FC<TabsProps> = ({
  tabs,
  activeTab,
  onTabChange,
  className = ''
}) => {
  return (
    <div className={`wp-tabs ${className}`}>
      <nav className="nav-tab-wrapper wp-clearfix" role="tablist">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            role="tab"
            aria-selected={activeTab === tab.id}
            className={`nav-tab ${activeTab === tab.id ? 'nav-tab-active' : ''}`}
            onClick={() => onTabChange(tab.id)}
          >
            {tab.icon && (
              <span className={`dashicons ${tab.icon} mr-1`} aria-hidden="true" />
            )}
            {tab.title}
          </button>
        ))}
      </nav>
    </div>
  );
};

interface TabPanelProps {
  id: string;
  activeTab: string;
  children: React.ReactNode;
  className?: string;
}

export const TabPanel: React.FC<TabPanelProps> = ({
  id,
  activeTab,
  children,
  className = ''
}) => {
  if (activeTab !== id) return null;

  return (
    <div
      role="tabpanel"
      aria-labelledby={`tab-${id}`}
      className={`tab-panel ${className}`}
    >
      {children}
    </div>
  );
};