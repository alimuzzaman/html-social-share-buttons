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
      }

      // Default error UI
      return (
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
        </div>
      );
    }

    return this.props.children;
  }
}