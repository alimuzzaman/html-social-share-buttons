import React, { useState } from 'react';
import { Notice, LoadingSpinner } from './index';
import type { NoticeType, SpinnerSize } from './index';

/**
 * Demo component showcasing all Notice and LoadingSpinner features
 * This file demonstrates all the component variations and use cases
 */
const ComponentDemo: React.FC = () => {
  const [showSuccess, setShowSuccess] = useState(true);
  const [showWarning, setShowWarning] = useState(true);
  const [showError, setShowError] = useState(true);
  const [showInfo, setShowInfo] = useState(true);

  const noticeTypes: NoticeType[] = ['success', 'warning', 'error', 'info'];
  const spinnerSizes: SpinnerSize[] = ['sm', 'md', 'lg', 'xl'];

  return (
    <div className="p-8 space-y-8 bg-gray-50 min-h-screen">
      {/* Notice Components Section */}
      <section>
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Notice Components</h2>
        
        <div className="space-y-4">
          <h3 className="text-lg font-semibold text-gray-700">All Notice Types</h3>
          
          {showSuccess && (
            <Notice
              type="success"
              message="Operation completed successfully! Your changes have been saved."
              dismissible
              onDismiss={() => setShowSuccess(false)}
            />
          )}

          {showWarning && (
            <Notice
              type="warning"
              message="Warning: This action may affect existing configurations."
              dismissible
              onDismiss={() => setShowWarning(false)}
            />
          )}

          {showError && (
            <Notice
              type="error"
              message="Error: Unable to save changes. Please try again."
              dismissible
              onDismiss={() => setShowError(false)}
            />
          )}

          {showInfo && (
            <Notice
              type="info"
              message="Info: Remember to save your changes before leaving this page."
              dismissible
              onDismiss={() => setShowInfo(false)}
            />
          )}
        </div>

        <div className="mt-6">
          <h3 className="text-lg font-semibold text-gray-700 mb-4">Non-dismissible Notices</h3>
          <div className="space-y-4">
            {noticeTypes.map((type) => (
              <Notice
                key={type}
                type={type}
                message={`This is a non-dismissible ${type} notice that remains visible.`}
              />
            ))}
          </div>
        </div>

        <div className="mt-6">
          <button
            onClick={() => {
              setShowSuccess(true);
              setShowWarning(true);
              setShowError(true);
              setShowInfo(true);
            }}
            className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
          >
            Reset All Notices
          </button>
        </div>
      </section>

      {/* Loading Spinner Section */}
      <section>
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Loading Spinner Components</h2>
        
        <div className="space-y-6">
          <h3 className="text-lg font-semibold text-gray-700">All Spinner Sizes</h3>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {spinnerSizes.map((size) => (
              <div key={size} className="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h4 className="text-sm font-medium text-gray-900 mb-4 uppercase text-center">
                  Size: {size}
                </h4>
                <LoadingSpinner size={size} />
              </div>
            ))}
          </div>

          <h3 className="text-lg font-semibold text-gray-700 mt-8">Spinner with Message</h3>
          <div className="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
            <LoadingSpinner size="lg" message="Loading your data..." />
          </div>

          <h3 className="text-lg font-semibold text-gray-700 mt-8">Inline Usage Examples</h3>
          <div className="space-y-4">
            <div className="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex items-center space-x-3">
              <LoadingSpinner size="sm" />
              <span className="text-gray-700">Small spinner in a button or inline context</span>
            </div>
            
            <div className="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex items-center space-x-3">
              <LoadingSpinner size="md" />
              <span className="text-gray-700">Medium spinner for loading sections</span>
            </div>
          </div>
        </div>
      </section>

      {/* Accessibility Features Section */}
      <section>
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Accessibility Features</h2>
        <div className="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-3">
          <h3 className="text-lg font-semibold text-gray-700">Built-in Accessibility</h3>
          <ul className="list-disc list-inside space-y-2 text-gray-600">
            <li>Notice components include <code className="bg-gray-100 px-1 py-0.5 rounded">role="alert"</code> for screen reader announcements</li>
            <li>Error notices use <code className="bg-gray-100 px-1 py-0.5 rounded">aria-live="assertive"</code> for immediate announcements</li>
            <li>Other notices use <code className="bg-gray-100 px-1 py-0.5 rounded">aria-live="polite"</code> to avoid interrupting users</li>
            <li>Dismiss buttons include <code className="bg-gray-100 px-1 py-0.5 rounded">sr-only</code> text for screen readers</li>
            <li>Loading spinners include hidden "Loading..." text for screen readers</li>
            <li>All interactive elements have proper focus states with visible focus rings</li>
            <li>Smooth transitions (300ms) for better visual feedback</li>
          </ul>
        </div>
      </section>

      {/* Usage Examples Section */}
      <section>
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Code Examples</h2>
        <div className="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4">
          <div>
            <h3 className="text-lg font-semibold text-gray-700 mb-2">Notice Component</h3>
            <pre className="bg-gray-800 text-gray-100 p-4 rounded overflow-x-auto text-sm">
{`<Notice
  type="success"
  message="Operation completed successfully!"
  dismissible
  onDismiss={() => console.log('Notice dismissed')}
/>`}
            </pre>
          </div>
          
          <div>
            <h3 className="text-lg font-semibold text-gray-700 mb-2">Loading Spinner Component</h3>
            <pre className="bg-gray-800 text-gray-100 p-4 rounded overflow-x-auto text-sm">
{`<LoadingSpinner
  size="lg"
  message="Loading your data..."
/>`}
            </pre>
          </div>
        </div>
      </section>
    </div>
  );
};

export default ComponentDemo;
