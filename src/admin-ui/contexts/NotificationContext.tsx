import React, { createContext, useContext, useReducer, ReactNode } from 'react';
import { Notification, NotificationType } from '../components/ui/Notification';

interface NotificationItem {
  id: string;
  type: NotificationType;
  title: string;
  message?: string;
  autoHide?: boolean;
  autoHideDelay?: number;
  actions?: Array<{
    label: string;
    onClick: () => void;
    variant?: 'primary' | 'secondary';
  }>;
}

interface NotificationState {
  notifications: NotificationItem[];
}

type NotificationAction =
  | { type: 'ADD_NOTIFICATION'; payload: Omit<NotificationItem, 'id'> }
  | { type: 'REMOVE_NOTIFICATION'; payload: string }
  | { type: 'CLEAR_ALL' };

interface NotificationContextType {
  notifications: NotificationItem[];
  addNotification: (notification: Omit<NotificationItem, 'id'>) => string;
  removeNotification: (id: string) => void;
  clearAll: () => void;
  showSuccess: (title: string, message?: string) => string;
  showError: (title: string, message?: string) => string;
  showWarning: (title: string, message?: string) => string;
  showInfo: (title: string, message?: string) => string;
}

const NotificationContext = createContext<NotificationContextType | undefined>(undefined);

function notificationReducer(state: NotificationState, action: NotificationAction): NotificationState {
  switch (action.type) {
    case 'ADD_NOTIFICATION':
      return {
        ...state,
        notifications: [
          ...state.notifications,
          {
            ...action.payload,
            id: Math.random().toString(36).substr(2, 9)
          }
        ]
      };
    case 'REMOVE_NOTIFICATION':
      return {
        ...state,
        notifications: state.notifications.filter(n => n.id !== action.payload)
      };
    case 'CLEAR_ALL':
      return {
        ...state,
        notifications: []
      };
    default:
      return state;
  }
}

interface NotificationProviderProps {
  children: ReactNode;
  maxNotifications?: number;
}

export const NotificationProvider: React.FC<NotificationProviderProps> = ({
  children,
  maxNotifications = 5
}) => {
  const [state, dispatch] = useReducer(notificationReducer, { notifications: [] });

  const addNotification = (notification: Omit<NotificationItem, 'id'>): string => {
    const id = Math.random().toString(36).substr(2, 9);

    // Remove oldest notification if we exceed the limit
    if (state.notifications.length >= maxNotifications) {
      const oldestId = state.notifications[0]?.id;
      if (oldestId) {
        dispatch({ type: 'REMOVE_NOTIFICATION', payload: oldestId });
      }
    }

    dispatch({
      type: 'ADD_NOTIFICATION',
      payload: {
        ...notification,
        autoHide: notification.autoHide ?? true,
        autoHideDelay: notification.autoHideDelay ?? 5000
      }
    });

    return id;
  };

  const removeNotification = (id: string) => {
    dispatch({ type: 'REMOVE_NOTIFICATION', payload: id });
  };

  const clearAll = () => {
    dispatch({ type: 'CLEAR_ALL' });
  };

  const showSuccess = (title: string, message?: string): string => {
    return addNotification({ type: 'success', title, message });
  };

  const showError = (title: string, message?: string): string => {
    return addNotification({
      type: 'error',
      title,
      message,
      autoHide: false // Errors should not auto-hide
    });
  };

  const showWarning = (title: string, message?: string): string => {
    return addNotification({ type: 'warning', title, message });
  };

  const showInfo = (title: string, message?: string): string => {
    return addNotification({ type: 'info', title, message });
  };

  const contextValue: NotificationContextType = {
    notifications: state.notifications,
    addNotification,
    removeNotification,
    clearAll,
    showSuccess,
    showError,
    showWarning,
    showInfo
  };

  return (
    <NotificationContext.Provider value={contextValue}>
      {children}

      {/* Notification Container */}
      <div className="fixed top-4 right-4 z-50 space-y-2 max-w-md">
        {state.notifications.map((notification) => (
          <Notification
            key={notification.id}
            type={notification.type}
            title={notification.title}
            message={notification.message}
            autoHide={notification.autoHide}
            autoHideDelay={notification.autoHideDelay}
            actions={notification.actions}
            onDismiss={() => removeNotification(notification.id)}
          />
        ))}
      </div>
    </NotificationContext.Provider>
  );
};

export const useNotifications = (): NotificationContextType => {
  const context = useContext(NotificationContext);
  if (context === undefined) {
    throw new Error('useNotifications must be used within a NotificationProvider');
  }
  return context;
};