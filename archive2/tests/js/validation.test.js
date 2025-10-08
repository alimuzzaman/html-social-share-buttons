import {
  validateField,
  validateFormData,
  patterns,
  validationRules,
  sanitizers,
  useFormValidation,
} from '../../src/admin-ui/utils/validation';
import { renderHook, act } from '@testing-library/react';

describe('Validation Utilities', () => {
  describe('patterns', () => {
    test('url pattern validates URLs correctly', () => {
      expect(patterns.url.test('https://example.com')).toBe(true);
      expect(patterns.url.test('http://example.com')).toBe(true);
      expect(patterns.url.test('example.com')).toBe(false);
      expect(patterns.url.test('ftp://example.com')).toBe(false);
    });

    test('email pattern validates emails correctly', () => {
      expect(patterns.email.test('user@example.com')).toBe(true);
      expect(patterns.email.test('test.email+tag@example.co.uk')).toBe(true);
      expect(patterns.email.test('invalid-email')).toBe(false);
      expect(patterns.email.test('@example.com')).toBe(false);
    });

    test('twitter handle pattern validates correctly', () => {
      expect(patterns.twitterHandle.test('@username')).toBe(true);
      expect(patterns.twitterHandle.test('username')).toBe(true);
      expect(patterns.twitterHandle.test('@user_123')).toBe(true);
      expect(patterns.twitterHandle.test('@verylongusernamethatexceedslimit')).toBe(false);
      expect(patterns.twitterHandle.test('@user-name')).toBe(false);
    });

    test('hex color pattern validates correctly', () => {
      expect(patterns.hexColor.test('#FF0000')).toBe(true);
      expect(patterns.hexColor.test('#f00')).toBe(true);
      expect(patterns.hexColor.test('#123ABC')).toBe(true);
      expect(patterns.hexColor.test('FF0000')).toBe(false);
      expect(patterns.hexColor.test('#GG0000')).toBe(false);
    });
  });

  describe('sanitizers', () => {
    test('text sanitizer removes harmful characters', () => {
      expect(sanitizers.text('Hello <script>alert("xss")</script>')).toBe('Hello scriptalert(xss)/script');
      expect(sanitizers.text('  Text with spaces  ')).toBe('Text with spaces');
      expect(sanitizers.text('Text with "quotes" and \'apostrophes\'')).toBe('Text with quotes and apostrophes');
    });

    test('url sanitizer normalizes URLs', () => {
      expect(sanitizers.url('  HTTPS://EXAMPLE.COM  ')).toBe('https://example.com');
      expect(sanitizers.url('HTTP://Site.Com/PATH')).toBe('http://site.com/path');
    });

    test('handle sanitizer formats social handles', () => {
      expect(sanitizers.handle('username')).toBe('@username');
      expect(sanitizers.handle('@username')).toBe('@username');
      expect(sanitizers.handle('  @user_123  ')).toBe('@user_123');
      expect(sanitizers.handle('user-name!')).toBe('@username');
    });

    test('hex color sanitizer formats colors', () => {
      expect(sanitizers.hexColor('ff0000')).toBe('#FF0000');
      expect(sanitizers.hexColor('#ff0000')).toBe('#FF0000');
      expect(sanitizers.hexColor('  #abc  ')).toBe('#ABC');
    });
  });

  describe('validateField', () => {
    test('validates required fields', () => {
      const rules = { required: true };

      expect(validateField('test', '', rules)).toEqual({
        field: 'test',
        message: 'This field is required',
      });

      expect(validateField('test', 'value', rules)).toBeNull();
      expect(validateField('test', null, rules)).toEqual({
        field: 'test',
        message: 'This field is required',
      });
    });

    test('validates minimum length', () => {
      const rules = { minLength: 5 };

      expect(validateField('test', 'abc', rules)).toEqual({
        field: 'test',
        message: 'Minimum length is 5 characters',
      });

      expect(validateField('test', 'abcdef', rules)).toBeNull();
    });

    test('validates maximum length', () => {
      const rules = { maxLength: 5 };

      expect(validateField('test', 'abcdef', rules)).toEqual({
        field: 'test',
        message: 'Maximum length is 5 characters',
      });

      expect(validateField('test', 'abc', rules)).toBeNull();
    });

    test('validates pattern matching', () => {
      const rules = { pattern: patterns.email };

      expect(validateField('test', 'invalid-email', rules)).toEqual({
        field: 'test',
        message: 'Please enter a valid email address',
      });

      expect(validateField('test', 'user@example.com', rules)).toBeNull();
    });

    test('validates with custom function', () => {
      const rules = {
        custom: (value) => value === 'forbidden' ? 'This value is not allowed' : null,
      };

      expect(validateField('test', 'forbidden', rules)).toEqual({
        field: 'test',
        message: 'This value is not allowed',
      });

      expect(validateField('test', 'allowed', rules)).toBeNull();
    });

    test('skips validation for empty non-required fields', () => {
      const rules = { minLength: 5, pattern: patterns.email };

      expect(validateField('test', '', rules)).toBeNull();
      expect(validateField('test', null, rules)).toBeNull();
    });
  });

  describe('validateFormData', () => {
    test('validates multiple fields', () => {
      const data = {
        name: '',
        email: 'invalid-email',
        twitter: '@validuser',
      };

      const rules = {
        name: { required: true },
        email: { pattern: patterns.email },
        twitter: { pattern: patterns.twitterHandle },
      };

      const result = validateFormData(data, rules);

      expect(result.isValid).toBe(false);
      expect(result.errors).toHaveLength(2);
      expect(result.errors.map(e => e.field)).toContain('name');
      expect(result.errors.map(e => e.field)).toContain('email');
    });

    test('returns valid result for valid data', () => {
      const data = {
        name: 'John Doe',
        email: 'john@example.com',
        twitter: '@john',
      };

      const rules = {
        name: { required: true },
        email: { pattern: patterns.email },
        twitter: { pattern: patterns.twitterHandle },
      };

      const result = validateFormData(data, rules);

      expect(result.isValid).toBe(true);
      expect(result.errors).toHaveLength(0);
    });
  });

  describe('useFormValidation hook', () => {
    test('initializes with default data', () => {
      const initialData = { name: 'John', email: '' };
      const rules = { name: { required: true } };

      const { result } = renderHook(() => useFormValidation(initialData, rules));

      expect(result.current.data).toEqual(initialData);
      expect(result.current.errors).toEqual([]);
      expect(result.current.isValid).toBe(true);
    });

    test('updates field values', () => {
      const initialData = { name: '', email: '' };
      const rules = { name: { required: true } };

      const { result } = renderHook(() => useFormValidation(initialData, rules));

      act(() => {
        result.current.updateField('name', 'John');
      });

      expect(result.current.data.name).toBe('John');
    });

    test('validates field on touch', () => {
      const initialData = { name: '' };
      const rules = { name: { required: true } };

      const { result } = renderHook(() => useFormValidation(initialData, rules));

      act(() => {
        result.current.touchField('name');
      });

      expect(result.current.hasFieldError('name')).toBe(true);
      expect(result.current.getFieldError('name')).toBe('This field is required');
    });

    test('validates entire form', () => {
      const initialData = { name: '', email: 'invalid' };
      const rules = {
        name: { required: true },
        email: { pattern: patterns.email },
      };

      const { result } = renderHook(() => useFormValidation(initialData, rules));

      let isValid;
      act(() => {
        isValid = result.current.validateForm();
      });

      expect(isValid).toBe(false);
      expect(result.current.errors).toHaveLength(2);
    });

    test('validates on field update after touch', () => {
      const initialData = { name: '' };
      const rules = { name: { required: true } };

      const { result } = renderHook(() => useFormValidation(initialData, rules));

      // Touch field first
      act(() => {
        result.current.touchField('name');
      });

      // Update field - should validate automatically
      act(() => {
        result.current.updateField('name', 'John');
      });

      expect(result.current.hasFieldError('name')).toBe(false);
    });
  });

  describe('predefined validation rules', () => {
    test('profile name validation', () => {
      const rules = validationRules.profileName;

      expect(validateField('profileName', '', rules)).not.toBeNull();
      expect(validateField('profileName', 'Valid Name', rules)).toBeNull();
      expect(validateField('profileName', 'Name<script>', rules)).not.toBeNull();
    });

    test('twitter handle validation', () => {
      const rules = validationRules.twitterHandle;

      expect(validateField('twitterHandle', '@valid', rules)).toBeNull();
      expect(validateField('twitterHandle', 'invalid', rules)).not.toBeNull();
    });

    test('custom URL validation', () => {
      const rules = validationRules.customUrl;

      expect(validateField('customUrl', 'https://example.com', rules)).toBeNull();
      expect(validateField('customUrl', 'invalid-url', rules)).not.toBeNull();
    });
  });
});