import React from 'react';

export const AppearanceTab: React.FC = () => {
  return (
    <div className="appearance-tab">
      <div className="wp-admin-card p-6">
        <h2 className="text-xl font-semibold mb-4">Appearance Settings</h2>
        <p className="text-wp-gray-600 mb-6">
          Customize the visual appearance of your social share buttons.
        </p>

        <div className="bg-wp-gray-50 p-4 rounded-lg">
          <p className="text-center text-wp-gray-600">
            Appearance customization component coming soon...
          </p>
        </div>
      </div>
    </div>
  );
};