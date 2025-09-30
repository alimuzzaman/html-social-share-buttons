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
    <div className={className}>
      <nav className="border-b border-gray-200 mb-6" role="tablist">
        <div className="flex space-x-1">
          {tabs.map((tab) => (
            <button
              key={tab.id}
              role="tab"
              aria-selected={activeTab === tab.id}
              className={`px-4 py-2 text-sm font-medium rounded-t-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 ${
                activeTab === tab.id
                  ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600'
                  : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-b-2 border-transparent'
              }`}
              onClick={() => onTabChange(tab.id)}
            >
              {tab.icon && (
                <span className="mr-2" aria-hidden="true">
                  {tab.icon}
                </span>
              )}
              {tab.title}
            </button>
          ))}
        </div>
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
      className={`mt-6 ${className}`}
    >
      {children}
    </div>
  );
};