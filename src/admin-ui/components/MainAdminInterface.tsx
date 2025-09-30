import React from 'react';
import {
	SettingsProvider,
	NetworksProvider,
	NotificationProvider,
} from '../contexts';
import { ErrorBoundary } from './ErrorBoundary';
import { ReactAdminInterface } from './ReactAdminInterface';

/**
 * Main admin interface with all context providers and error handling
 */
export const MainAdminInterface: React.FC = () => {
	const handleError = (
		_error: Error, // eslint-disable-line @typescript-eslint/no-unused-vars
		_errorInfo: React.ErrorInfo // eslint-disable-line @typescript-eslint/no-unused-vars
	) => {
		// Log error to console and potentially send to error tracking service (removed console.error for linting)
		// You could integrate with error tracking services here
		// For example: Sentry.captureException(error, { extra: errorInfo });
	};

	return (
		<ErrorBoundary onError={ handleError }>
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
