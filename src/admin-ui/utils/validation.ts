// Validation patterns
export const patterns = {
  email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  url: /^https?:\/\/.+/,
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

  instagramHandle: {
    pattern: patterns.instagramHandle,
    custom: (value: string) => {
      if (value && !value.startsWith('@')) {
        return 'Instagram handle should start with @';
      }
      return null;
    },
  },

  customUrl: {
    pattern: patterns.url,
  },

  // Display settings
  customText: {
    maxLength: 50,
    pattern: patterns.alphanumeric,
  },

  customColor: {
    pattern: patterns.hexColor,
  },

  // Advanced settings
  cssClass: {
    maxLength: 100,
    pattern: /^[a-zA-Z0-9_\s-]+$/,
  },

  customCss: {
    maxLength: 2000,
  },
};

// Sanitization functions
export const sanitizers = {
  text: (value: string): string => {
    return value.trim().replace(/[<>'"]/g, '');
  },

  url: (value: string): string => {
    return value.trim().toLowerCase();
  },

  handle: (value: string): string => {
    const cleaned = value.trim().replace(/[^a-zA-Z0-9_@]/g, '');
    return cleaned.startsWith('@') ? cleaned : '@' + cleaned;
  },

  hexColor: (value: string): string => {
    const cleaned = value.trim().toUpperCase();
    return cleaned.startsWith('#') ? cleaned : '#' + cleaned;
  },

  cssClass: (value: string): string => {
    return value.trim().replace(/[^a-zA-Z0-9_\s-]/g, '').replace(/\s+/g, ' ');
  },
};

// Type definitions
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

  // Skip other validations if field is empty and not required
  if (!value || value.toString().trim() === '') {
    return null;
  }

  const stringValue = value.toString();

  // Min length validation
  if (rules.minLength && stringValue.length < rules.minLength) {
    return {
      field: fieldName,
      message: `Minimum length is ${rules.minLength} characters`,
    };
  }

  // Max length validation
  if (rules.maxLength && stringValue.length > rules.maxLength) {
    return {
      field: fieldName,
      message: `Maximum length is ${rules.maxLength} characters`,
    };
  }

  // Pattern validation
  if (rules.pattern && !rules.pattern.test(stringValue)) {
    return {
      field: fieldName,
      message: getPatternErrorMessage(rules.pattern),
    };
  }

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

  return null;
}

// Validate multiple fields
export function validateFormData(data: Record<string, any>, rules: Record<string, ValidationRule>): ValidationResult {
  const errors: ValidationError[] = [];

  for (const [fieldName, fieldRules] of Object.entries(rules)) {
    const value = data[fieldName];
    const error = validateField(fieldName, value, fieldRules);

    if (error) {
      errors.push(error);
    }
  }

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

  return 'Invalid format';
}
