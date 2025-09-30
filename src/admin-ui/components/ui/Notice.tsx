import React from 'react';
import { NoticeProps } from '../../types';

export const Notice: React.FC<NoticeProps> = ({
  type,
  message,
  dismissible = false,
  onDismiss
}) => {
  const getNoticeClasses = () => {
    const baseClasses = 'notice wp-admin-notice';
    switch (type) {
      case 'success':
        return `${baseClasses} notice-success`;
      case 'warning':
        return `${baseClasses} notice-warning`;
      case 'error':
        return `${baseClasses} notice-error`;
      case 'info':
      default:
        return `${baseClasses} notice-info`;
    }
  };

  return (
    <div className={`${getNoticeClasses()} ${dismissible ? 'is-dismissible' : ''}`}>
      <p>{message}</p>
      {dismissible && onDismiss && (
        <button
          type="button"
          className="notice-dismiss"
          onClick={onDismiss}
          aria-label="Dismiss this notice"
        >
          <span className="screen-reader-text">Dismiss this notice.</span>
        </button>
      )}
    </div>
  );
};