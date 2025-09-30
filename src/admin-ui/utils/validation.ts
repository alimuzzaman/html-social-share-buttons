// React import
import React from 'react';

// Validation utilities for HTML Social Share plugin

export interface ValidationRule {
  required?: boolean;
  minLength?: number;
  maxLength?: number;
  pattern?: RegExp;
  custom?: (value: any) => string | null;
}

export interface ValidationError {
  field: string;
  message: string;
}

export interface ValidationResult {
  isValid: boolean;
  errors: ValidationError[];
}

// Common validation patterns
export const patterns = {
  url: /^https?:\/\/.+/,
  email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  twitterHandle: /^@?[a-zA-Z0-9_]{1,15}$/,
  instagramHandle: /^@?[a-zA-Z0-9_.]{1,30}$/,
  hexColor: /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/,
  alphanumeric: /^[a-zA-Z0-9_\s-]+$/,
};

// Validation rules for different form fields
export const validationRules = {
  // General settings
  profileName: {
    required: true,
    minLength: 1,
    maxLength: 100,
    pattern: patterns.alphanumeric,
  },
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Network settings
  twitterHandle: {
    pattern: patterns.twitterHandle,
    custom: (value: string) => {
      if (value && !value.startsWith('@')) {
        return 'Twitter handle should start with @';
      }
      return null;
    },
  },
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  instagramHandle: {
    pattern: patterns.instagramHandle,
    custom: (value: string) => {
      if (value && !value.startsWith('@')) {
        return 'Instagram handle should start with @';
      }
      return null;
    },
  },
<<<<<<< Updated upstream
  
  customUrl: {
    pattern: patterns.url,
  },
  
=======

  customUrl: {
    pattern: patterns.url,
  },

>>>>>>> Stashed changes
  // Display settings
  customText: {
    maxLength: 50,
    pattern: patterns.alphanumeric,
  },
<<<<<<< Updated upstream
  
  customColor: {
    pattern: patterns.hexColor,
  },
  
=======

  customColor: {
    pattern: patterns.hexColor,
  },

>>>>>>> Stashed changes
  // Advanced settings
  cssClass: {
    maxLength: 100,
    pattern: /^[a-zA-Z0-9_\s-]+$/,
  },
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  customCss: {
    maxLength: 2000,
  },
};

// Sanitization functions
export const sanitizers = {
  text: (value: string): string => {
    return value.trim().replace(/[<>'"]/g, '');
  },
<<<<<<< Updated upstream
  
  url: (value: string): string => {
    return value.trim().toLowerCase();
  },
  
=======

  url: (value: string): string => {
    return value.trim().toLowerCase();
  },

>>>>>>> Stashed changes
  handle: (value: string): string => {
    const cleaned = value.trim().replace(/[^a-zA-Z0-9_@]/g, '');
    return cleaned.startsWith('@') ? cleaned : '@' + cleaned;
  },
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  hexColor: (value: string): string => {
    const cleaned = value.trim().toUpperCase();
    return cleaned.startsWith('#') ? cleaned : '#' + cleaned;
  },
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  cssClass: (value: string): string => {
    return value.trim().replace(/[^a-zA-Z0-9_\s-]/g, '').replace(/\s+/g, ' ');
  },
};

// Validate a single field
export function validateField(
  fieldName: string,
  value: any,
  rules: ValidationRule
): ValidationError | null {
  // Required validation
  if (rules.required && (!value || value.toString().trim() === '')) {
    return {
      field: fieldName,
      message: 'This field is required',
    };
  }
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Skip other validations if field is empty and not required
  if (!value || value.toString().trim() === '') {
    return null;
  }
<<<<<<< Updated upstream
  
  const stringValue = value.toString();
  
=======

  const stringValue = value.toString();

>>>>>>> Stashed changes
  // Min length validation
  if (rules.minLength && stringValue.length < rules.minLength) {
    return {
      field: fieldName,
      message: `Minimum length is ${rules.minLength} characters`,
    };
  }
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Max length validation
  if (rules.maxLength && stringValue.length > rules.maxLength) {
    return {
      field: fieldName,
      message: `Maximum length is ${rules.maxLength} characters`,
    };
  }
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Pattern validation
  if (rules.pattern && !rules.pattern.test(stringValue)) {
    return {
      field: fieldName,
      message: getPatternErrorMessage(rules.pattern),
    };
  }
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Custom validation
  if (rules.custom) {
    const customError = rules.custom(value);
    if (customError) {
      return {
        field: fieldName,
        message: customError,
      };
    }
  }
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  return null;
}

// Validate multiple fields
export function validateFormData(data: Record<string, any>, rules: Record<string, ValidationRule>): ValidationResult {
  const errors: ValidationError[] = [];
<<<<<<< Updated upstream
  
  for (const [fieldName, fieldRules] of Object.entries(rules)) {
    const value = data[fieldName];
    const error = validateField(fieldName, value, fieldRules);
    
=======

  for (const [fieldName, fieldRules] of Object.entries(rules)) {
    const value = data[fieldName];
    const error = validateField(fieldName, value, fieldRules);

>>>>>>> Stashed changes
    if (error) {
      errors.push(error);
    }
  }
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  return {
    isValid: errors.length === 0,
    errors,
  };
}

// Get user-friendly error messages for patterns
function getPatternErrorMessage(pattern: RegExp): string {
  if (pattern === patterns.url) {
    return 'Please enter a valid URL (starting with http:// or https://)';
  }
<<<<<<< Updated upstream
  
  if (pattern === patterns.email) {
    return 'Please enter a valid email address';
  }
  
  if (pattern === patterns.twitterHandle) {
    return 'Please enter a valid Twitter handle (1-15 characters, letters, numbers, underscore)';
  }
  
  if (pattern === patterns.instagramHandle) {
    return 'Please enter a valid Instagram handle (1-30 characters, letters, numbers, underscore, period)';
  }
  
  if (pattern === patterns.hexColor) {
    return 'Please enter a valid hex color (e.g., #FF0000 or #F00)';
  }
  
  if (pattern === patterns.alphanumeric) {
    return 'Only letters, numbers, spaces, hyphens, and underscores are allowed';
  }
  
=======

  if (pattern === patterns.email) {
    return 'Please enter a valid email address';
  }

  if (pattern === patterns.twitterHandle) {
    return 'Please enter a valid Twitter handle (1-15 characters, letters, numbers, underscore)';
  }

  if (pattern === patterns.instagramHandle) {
    return 'Please enter a valid Instagram handle (1-30 characters, letters, numbers, underscore, period)';
  }

  if (pattern === patterns.hexColor) {
    return 'Please enter a valid hex color (e.g., #FF0000 or #F00)';
  }

  if (pattern === patterns.alphanumeric) {
    return 'Only letters, numbers, spaces, hyphens, and underscores are allowed';
  }

>>>>>>> Stashed changes
  return 'Please enter a valid value';
}

// React hook for form validation
export function useFormValidation<T extends Record<string, any>>(
  initialData: T,
  validationRules: Record<string, ValidationRule>
) {
  const [data, setData] = React.useState<T>(initialData);
  const [errors, setErrors] = React.useState<ValidationError[]>([]);
  const [touched, setTouched] = React.useState<Record<string, boolean>>({});
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Validate single field
  const validateSingleField = (fieldName: string, value: any) => {
    const rules = validationRules[fieldName];
    if (!rules) return;
<<<<<<< Updated upstream
    
    const error = validateField(fieldName, value, rules);
    
=======

    const error = validateField(fieldName, value, rules);

>>>>>>> Stashed changes
    setErrors(prev => {
      const filtered = prev.filter(e => e.field !== fieldName);
      return error ? [...filtered, error] : filtered;
    });
  };
<<<<<<< Updated upstream
  
  // Update field value
  const updateField = (fieldName: string, value: any) => {
    setData(prev => ({ ...prev, [fieldName]: value }));
    
=======

  // Update field value
  const updateField = (fieldName: string, value: any) => {
    setData(prev => ({ ...prev, [fieldName]: value }));

>>>>>>> Stashed changes
    if (touched[fieldName]) {
      validateSingleField(fieldName, value);
    }
  };
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Mark field as touched
  const touchField = (fieldName: string) => {
    setTouched(prev => ({ ...prev, [fieldName]: true }));
    validateSingleField(fieldName, data[fieldName]);
  };
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Validate entire form
  const validateFormMethod = (): boolean => {
    const result = validateFormData(data, validationRules);
    setErrors(result.errors);
<<<<<<< Updated upstream
    
=======

>>>>>>> Stashed changes
    // Mark all fields as touched
    const allTouched = Object.keys(validationRules).reduce(
      (acc, key) => ({ ...acc, [key]: true }),
      {}
    );
    setTouched(allTouched);
<<<<<<< Updated upstream
    
    return result.isValid;
  };
  
=======

    return result.isValid;
  };

>>>>>>> Stashed changes
  // Get error for specific field
  const getFieldError = (fieldName: string): string | null => {
    const error = errors.find(e => e.field === fieldName);
    return error ? error.message : null;
  };
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  // Check if field has error
  const hasFieldError = (fieldName: string): boolean => {
    return touched[fieldName] && !!getFieldError(fieldName);
  };
<<<<<<< Updated upstream
  
=======

>>>>>>> Stashed changes
  return {
    data,
    errors,
    touched,
    updateField,
    touchField,
    validateForm: validateFormMethod,
    getFieldError,
    hasFieldError,
    isValid: errors.length === 0,
  };
}