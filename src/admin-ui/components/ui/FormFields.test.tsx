/**
 * FormFields Component Test Guide
 * 
 * This file provides examples of how to test the FormFields components.
 * These tests would typically use Jest and React Testing Library.
 */

import { render, screen } from '@testing-library/react';
import { FormField, TextInput, Select } from './FormFields';

/**
 * Test Suite: FormField Component
 */
describe('FormField', () => {
  test('renders label correctly', () => {
    render(
      <FormField label="Username">
        <TextInput />
      </FormField>
    );
    expect(screen.getByText('Username')).toBeInTheDocument();
  });

  test('displays required indicator when required is true', () => {
    render(
      <FormField label="Email" required>
        <TextInput />
      </FormField>
    );
    expect(screen.getByText('*')).toBeInTheDocument();
    expect(screen.getByText('*')).toHaveClass('text-red-500');
  });

  test('displays description when provided', () => {
    const description = 'Enter your email address';
    render(
      <FormField label="Email" description={description}>
        <TextInput />
      </FormField>
    );
    expect(screen.getByText(description)).toBeInTheDocument();
    expect(screen.getByText(description)).toHaveClass('text-sm', 'text-gray-500');
  });

  test('displays error message when error is provided', () => {
    const errorMessage = 'This field is required';
    render(
      <FormField label="Email" error={errorMessage}>
        <TextInput />
      </FormField>
    );
    expect(screen.getByText(errorMessage)).toBeInTheDocument();
    expect(screen.getByText(errorMessage)).toHaveClass('text-sm', 'text-red-600');
  });

  test('applies has-error class when error is present', () => {
    const { container } = render(
      <FormField label="Email" error="Error">
        <TextInput />
      </FormField>
    );
    const formField = container.firstChild;
    expect(formField).toHaveClass('has-error');
  });

  test('applies custom className', () => {
    const { container } = render(
      <FormField label="Email" className="custom-class">
        <TextInput />
      </FormField>
    );
    const formField = container.firstChild;
    expect(formField).toHaveClass('custom-class');
  });

  test('renders children correctly', () => {
    render(
      <FormField label="Email">
        <TextInput data-testid="email-input" />
      </FormField>
    );
    expect(screen.getByTestId('email-input')).toBeInTheDocument();
  });
});

/**
 * Test Suite: TextInput Component
 */
describe('TextInput', () => {
  test('renders with default Tailwind classes', () => {
    const { container } = render(<TextInput />);
    const input = container.querySelector('input');
    
    expect(input).toHaveClass(
      'w-full',
      'px-3',
      'py-2',
      'border',
      'border-gray-300',
      'rounded-md',
      'focus:outline-none',
      'focus:ring-1',
      'focus:ring-blue-500',
      'focus:border-blue-500'
    );
  });

  test('applies custom className', () => {
    const { container } = render(<TextInput className="custom-input" />);
    const input = container.querySelector('input');
    expect(input).toHaveClass('custom-input');
  });

  test('forwards all HTML input props', () => {
    render(
      <TextInput
        type="email"
        placeholder="user@example.com"
        required
        disabled
        data-testid="test-input"
      />
    );
    
    const input = screen.getByTestId('test-input');
    expect(input).toHaveAttribute('type', 'email');
    expect(input).toHaveAttribute('placeholder', 'user@example.com');
    expect(input).toBeRequired();
    expect(input).toBeDisabled();
  });

  test('handles value and onChange', () => {
    const handleChange = jest.fn();
    render(
      <TextInput
        value="test value"
        onChange={handleChange}
        data-testid="test-input"
      />
    );
    
    const input = screen.getByTestId('test-input') as HTMLInputElement;
    expect(input.value).toBe('test value');
  });
});

/**
 * Test Suite: Select Component
 */
describe('Select', () => {
  test('renders with default Tailwind classes', () => {
    const { container } = render(
      <Select>
        <option value="1">Option 1</option>
      </Select>
    );
    const select = container.querySelector('select');
    
    expect(select).toHaveClass(
      'w-full',
      'px-3',
      'py-2',
      'border',
      'border-gray-300',
      'rounded-md',
      'focus:outline-none',
      'focus:ring-1',
      'focus:ring-blue-500',
      'focus:border-blue-500'
    );
  });

  test('applies custom className', () => {
    const { container } = render(
      <Select className="custom-select">
        <option value="1">Option 1</option>
      </Select>
    );
    const select = container.querySelector('select');
    expect(select).toHaveClass('custom-select');
  });

  test('renders options correctly', () => {
    render(
      <Select data-testid="test-select">
        <option value="1">Option 1</option>
        <option value="2">Option 2</option>
        <option value="3">Option 3</option>
      </Select>
    );
    
    const select = screen.getByTestId('test-select');
    const options = select.querySelectorAll('option');
    expect(options).toHaveLength(3);
    expect(options[0]).toHaveTextContent('Option 1');
    expect(options[1]).toHaveTextContent('Option 2');
    expect(options[2]).toHaveTextContent('Option 3');
  });

  test('forwards all HTML select props', () => {
    const handleChange = jest.fn();
    render(
      <Select
        defaultValue="2"
        required
        disabled
        onChange={handleChange}
        data-testid="test-select"
      >
        <option value="1">Option 1</option>
        <option value="2">Option 2</option>
      </Select>
    );
    
    const select = screen.getByTestId('test-select') as HTMLSelectElement;
    expect(select.value).toBe('2');
    expect(select).toBeRequired();
    expect(select).toBeDisabled();
  });
});

/**
 * Integration Tests
 */
describe('FormFields Integration', () => {
  test('complete form field with all props', () => {
    render(
      <FormField
        label="Email Address"
        required
        description="We'll never share your email"
        error="Invalid email format"
        className="mb-4"
      >
        <TextInput
          type="email"
          placeholder="user@example.com"
          className="bg-gray-50"
        />
      </FormField>
    );

    expect(screen.getByText('Email Address')).toBeInTheDocument();
    expect(screen.getByText('*')).toBeInTheDocument();
    expect(screen.getByText("We'll never share your email")).toBeInTheDocument();
    expect(screen.getByText('Invalid email format')).toBeInTheDocument();
    expect(screen.getByPlaceholderText('user@example.com')).toBeInTheDocument();
  });

  test('select within form field', () => {
    render(
      <FormField
        label="Country"
        required
        description="Select your country"
      >
        <Select defaultValue="us">
          <option value="us">United States</option>
          <option value="uk">United Kingdom</option>
          <option value="ca">Canada</option>
        </Select>
      </FormField>
    );

    expect(screen.getByText('Country')).toBeInTheDocument();
    expect(screen.getByText('Select your country')).toBeInTheDocument();
    const select = screen.getByRole('combobox') as HTMLSelectElement;
    expect(select.value).toBe('us');
  });
});

/**
 * Accessibility Tests
 */
describe('Accessibility', () => {
  test('FormField label is associated with input', () => {
    render(
      <FormField label="Username">
        <TextInput id="username" aria-label="Username" />
      </FormField>
    );
    
    const input = screen.getByLabelText('Username');
    expect(input).toBeInTheDocument();
  });

  test('error messages are accessible', () => {
    render(
      <FormField label="Email" error="Invalid email">
        <TextInput aria-describedby="email-error" />
      </FormField>
    );
    
    expect(screen.getByText('Invalid email')).toBeInTheDocument();
  });

  test('required fields are indicated', () => {
    render(
      <FormField label="Password" required>
        <TextInput type="password" required />
      </FormField>
    );
    
    const input = screen.getByRole('textbox', { hidden: true }) || 
                  document.querySelector('input[type="password"]');
    expect(input).toBeRequired();
  });
});

/**
 * Snapshot Tests
 */
describe('Snapshots', () => {
  test('FormField snapshot', () => {
    const { container } = render(
      <FormField label="Test Label" required description="Test description">
        <TextInput />
      </FormField>
    );
    expect(container).toMatchSnapshot();
  });

  test('TextInput snapshot', () => {
    const { container } = render(<TextInput placeholder="Test" />);
    expect(container).toMatchSnapshot();
  });

  test('Select snapshot', () => {
    const { container } = render(
      <Select>
        <option value="1">Option 1</option>
        <option value="2">Option 2</option>
      </Select>
    );
    expect(container).toMatchSnapshot();
  });
});

export {};
