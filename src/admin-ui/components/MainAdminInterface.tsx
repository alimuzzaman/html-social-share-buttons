import React from 'react';
import { SettingsProvider, NetworksProvider, NotificationProvider } from '../contexts';
import { ErrorBoundary } from './ErrorBoundary';
import { ReactAdminInterface } from './ReactAdminInterface';

/**
 * Main admin interface with all context providers and error handling
 */
export const MainAdminInterface: React.FC = () => {
  const handleError = (error: Error, errorInfo: React.ErrorInfo) => {
    // Log error to console and potentially send to error tracking service
    console.error('React Admin Interface Error:', error, errorInfo);

    // You could integrate with error tracking services here
    // For example: Sentry.captureException(error, { extra: errorInfo });
  };

  return (
    <ErrorBoundary onError={handleError}>
      <NotificationProvider>
        <SettingsProvider>
          <NetworksProvider>
            <ReactAdminInterface />
          </NetworksProvider>
        </SettingsProvider>
      </NotificationProvider>
    </ErrorBoundary>
  );
};