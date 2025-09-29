<<<<<<< Updated upstream
import React, { Component, ErrorInfo, ReactNode } from 'react';

interface Props {
  children: ReactNode;
  fallback?: ReactNode;
  onError?: (error: Error, errorInfo: ErrorInfo) => void;
}

interface State {
  hasError: boolean;
  error?: Error;
  errorInfo?: ErrorInfo;
}

export class ErrorBoundary extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError(error: Error): State {
    // Update state so the next render will show the fallback UI
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    // Log error details
    console.error('ErrorBoundary caught an error:', error, errorInfo);
    
    // Update state with error info
    this.setState({
      error,
      errorInfo
    });
    
    // Call custom error handler if provided
    if (this.props.onError) {
      this.props.onError(error, errorInfo);
    }
  }

  private handleReset = () => {
    this.setState({ hasError: false, error: undefined, errorInfo: undefined });
  };

  render() {
    if (this.state.hasError) {
      // Custom fallback UI
      if (this.props.fallback) {
        return this.props.fallback;
=======
import React, { Component, ReactNode } from 'react';

interface ErrorBoundaryState {
  hasError: boolean;
  error: Error | null;
  errorInfo: React.ErrorInfo | null;
}

interface ErrorBoundaryProps {
  children: ReactNode;
  fallback?: (error: Error, errorInfo: React.ErrorInfo) => ReactNode;
  onError?: (error: Error, errorInfo: React.ErrorInfo) => void;
}

/**
 * Error Boundary component to catch and handle React errors gracefully
 */
export class ErrorBoundary extends Component<ErrorBoundaryProps, ErrorBoundaryState> {
  constructor(props: ErrorBoundaryProps) {
    super(props);
    this.state = {
      hasError: false,
      error: null,
      errorInfo: null,
    };
  }

  static getDerivedStateFromError(error: Error): Partial<ErrorBoundaryState> {
    return {
      hasError: true,
      error,
    };
  }

  componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    this.setState({
      error,
      errorInfo,
    });

    // Call optional error callback
    if (this.props.onError) {
      this.props.onError(error, errorInfo);
    }

    // Log error for debugging
    console.error('Error Boundary caught an error:', error, errorInfo);
  }

  private handleReset = () => {
    this.setState({
      hasError: false,
      error: null,
      errorInfo: null,
    });
  };

  render() {
    if (this.state.hasError && this.state.error) {
      // Use custom fallback if provided
      if (this.props.fallback) {
        return this.props.fallback(this.state.error, this.state.errorInfo!);
>>>>>>> Stashed changes
      }

      // Default error UI
      return (
<<<<<<< Updated upstream
        <div className="error-boundary">
          <div className="wp-admin-card p-6 border-l-4 border-red-500 bg-red-50">
            <div className="flex items-start">
              <div className="flex-shrink-0">
                <i className="fas fa-exclamation-triangle text-red-400 text-xl" />
              </div>
              <div className="ml-3 flex-1">
                <h3 className="text-lg font-medium text-red-800 mb-2">
                  Something went wrong
                </h3>
                <p className="text-red-600 mb-4">
                  An error occurred while rendering this component. Please try refreshing the page or contact support if the problem persists.
                </p>
                
                {process.env.NODE_ENV === 'development' && this.state.error && (
                  <details className="bg-red-100 p-3 rounded border text-sm">
                    <summary className="cursor-pointer font-medium text-red-800 mb-2">
                      Error Details (Development Mode)
                    </summary>
                    <div className="text-red-700">
                      <strong>Error:</strong> {this.state.error.message}
                      <br />
                      <strong>Stack:</strong>
                      <pre className="mt-2 text-xs overflow-auto">
                        {this.state.error.stack}
                      </pre>
                      {this.state.errorInfo && (
                        <>
                          <strong>Component Stack:</strong>
                          <pre className="mt-2 text-xs overflow-auto">
                            {this.state.errorInfo.componentStack}
                          </pre>
                        </>
                      )}
                    </div>
                  </details>
                )}
                
                <div className="flex space-x-3 mt-4">
                  <button
                    onClick={this.handleReset}
                    className="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors"
                  >
                    Try Again
                  </button>
                  <button
                    onClick={() => window.location.reload()}
                    className="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
                  >
                    Refresh Page
                  </button>
                </div>
              </div>
            </div>
          </div>
=======
        <div className="error-boundary bg-red-50 border border-red-200 rounded-lg p-6 m-4">
          <div className="flex items-center mb-4">
            <div className="text-red-500 mr-3">
              <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
              </svg>
            </div>
            <h2 className="text-lg font-semibold text-red-800">Something went wrong</h2>
          </div>

          <p className="text-red-700 mb-4">
            An error occurred while rendering this component. Please try refreshing the page or contact support if the problem persists.
          </p>

          <details className="mb-4">
            <summary className="text-sm text-red-600 cursor-pointer hover:text-red-800">
              Show error details
            </summary>
            <div className="mt-2 p-3 bg-red-100 rounded text-sm text-red-800 font-mono whitespace-pre-wrap">
              <strong>Error:</strong> {this.state.error.message}
              {this.state.errorInfo && (
                <>
                  <br /><br />
                  <strong>Stack trace:</strong>
                  {this.state.errorInfo.componentStack}
                </>
              )}
            </div>
          </details>

          <button
            onClick={this.handleReset}
            className="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            Try Again
          </button>
>>>>>>> Stashed changes
        </div>
      );
    }

    return this.props.children;
  }
}