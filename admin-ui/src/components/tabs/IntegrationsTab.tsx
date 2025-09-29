import React from 'react';

export const IntegrationsTab: React.FC = () => {
  return (
    <div className="integrations-tab">
      <div className="wp-admin-card p-6">
        <h2 className="text-xl font-semibold mb-4">Plugin Integrations</h2>
        <p className="text-wp-gray-600 mb-6">
          Configure integrations with other WordPress plugins and services.
        </p>

        <div className="bg-wp-gray-50 p-4 rounded-lg">
          <p className="text-center text-wp-gray-600">
            Integrations management component coming soon...
          </p>
        </div>
      </div>
    </div>
  );
};