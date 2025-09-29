import React from 'react';
import { FormFieldProps } from '../../types';

interface FormFieldWrapperProps extends FormFieldProps {
  children: React.ReactNode;
}

export const FormField: React.FC<FormFieldWrapperProps> = ({
  label,
  description,
  required = false,
  error,
  className = '',
  children
}) => {
  return (
    <div className={`wp-form-field ${className} ${error ? 'has-error' : ''}`}>
      <div className="wp-form-field-label">
        <label className="form-label">
          {label}
          {required && <span className="required text-red-500 ml-1">*</span>}
        </label>
      </div>

      <div className="wp-form-field-input">
        {children}
      </div>

      {description && (
        <p className="description text-sm text-wp-gray-600 mt-1">
          {description}
        </p>
      )}

      {error && (
        <p className="error-message text-sm text-red-600 mt-1">
          {error}
        </p>
      )}
    </div>
  );
};

interface TextInputProps {
  value: string;
  onChange: (value: string) => void;
  onBlur?: () => void;
  placeholder?: string;
  disabled?: boolean;
  type?: 'text' | 'email' | 'url' | 'password';
  className?: string;
}

export const TextInput: React.FC<TextInputProps> = ({
  value,
  onChange,
  onBlur,
  placeholder,
  disabled = false,
  type = 'text',
  className = ''
}) => {
  return (
    <input
      type={type}
      value={value}
      onChange={(e) => onChange(e.target.value)}
      onBlur={onBlur}
      placeholder={placeholder}
      disabled={disabled}
      className={`wp-text-input ${className}`}
    />
  );
};

interface SelectProps {
  value: string;
  onChange: (value: string) => void;
  options: { value: string; label: string }[];
  disabled?: boolean;
  className?: string;
}

export const Select: React.FC<SelectProps> = ({
  value,
  onChange,
  options,
  disabled = false,
  className = ''
}) => {
  return (
    <select
      value={value}
      onChange={(e) => onChange(e.target.value)}
      disabled={disabled}
      className={`wp-select ${className}`}
    >
      {options.map((option) => (
        <option key={option.value} value={option.value}>
          {option.label}
        </option>
      ))}
    </select>
  );
};

interface CheckboxProps {
  checked: boolean;
  onChange: (checked: boolean) => void;
  label: string;
  disabled?: boolean;
  className?: string;
}

export const Checkbox: React.FC<CheckboxProps> = ({
  checked,
  onChange,
  label,
  disabled = false,
  className = ''
}) => {
  return (
    <label className={`wp-checkbox-label ${className}`}>
      <input
        type="checkbox"
        checked={checked}
        onChange={(e) => onChange(e.target.checked)}
        disabled={disabled}
        className="wp-checkbox"
      />
      <span className="checkbox-text">{label}</span>
    </label>
  );
};

interface ButtonProps {
  onClick: () => void;
  children: React.ReactNode;
  variant?: 'primary' | 'secondary' | 'tertiary';
  size?: 'small' | 'medium' | 'large';
  disabled?: boolean;
  loading?: boolean;
  className?: string;
}

export const Button: React.FC<ButtonProps> = ({
  onClick,
  children,
  variant = 'primary',
  size = 'medium',
  disabled = false,
  loading = false,
  className = ''
}) => {
  const getButtonClasses = () => {
    let classes = 'wp-button';

    // Variant classes
    switch (variant) {
      case 'primary':
        classes += ' button-primary';
        break;
      case 'secondary':
        classes += ' button-secondary';
        break;
      case 'tertiary':
        classes += ' button-tertiary';
        break;
    }

    // Size classes
    switch (size) {
      case 'small':
        classes += ' button-small';
        break;
      case 'large':
        classes += ' button-large';
        break;
      case 'medium':
      default:
        // Default size
        break;
    }

    return `${classes} ${className}`;
  };

  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled || loading}
      className={getButtonClasses()}
    >
      {loading && (
        <span className="spinner is-active inline-block mr-1" aria-hidden="true" />
      )}
      {children}
    </button>
  );
};