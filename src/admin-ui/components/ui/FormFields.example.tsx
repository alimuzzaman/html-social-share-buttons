import React from 'react';
import { FormField, TextInput, Select } from './FormFields';

/**
 * Example usage of FormFields components with pure Tailwind CSS
 * 
 * This example demonstrates how to use the FormField, TextInput, and Select
 * components that have been migrated from custom wp-* classes to pure Tailwind CSS.
 */

const FormFieldsExample: React.FC = () => {
  return (
    <div className="max-w-2xl mx-auto p-6 space-y-6">
      <h1 className="text-2xl font-bold text-gray-900 mb-6">
        Form Fields Example
      </h1>

      {/* Basic Text Input with FormField */}
      <FormField
        label="Website Title"
        required
        description="Enter the title of your website"
      >
        <TextInput
          type="text"
          placeholder="My Awesome Website"
        />
      </FormField>

      {/* Text Input with Error State */}
      <FormField
        label="Email Address"
        required
        error="Please enter a valid email address"
      >
        <TextInput
          type="email"
          placeholder="user@example.com"
        />
      </FormField>

      {/* Select Dropdown with FormField */}
      <FormField
        label="Icon Style"
        description="Choose the icon style for social share buttons"
      >
        <Select defaultValue="flat">
          <option value="default">Default</option>
          <option value="flat">Flat</option>
          <option value="long-shadow">Long Shadow</option>
          <option value="prajin">Prajin</option>
        </Select>
      </FormField>

      {/* Multiple Fields Example */}
      <FormField
        label="Display Position"
        required
      >
        <Select>
          <option value="left">Left Side</option>
          <option value="right">Right Side</option>
          <option value="before">Before Post</option>
          <option value="after">After Post</option>
        </Select>
      </FormField>

      {/* Text Area Example (using TextInput with custom className) */}
      <FormField
        label="Exclude Pages"
        description="Enter page IDs, titles, or slugs to exclude (one per line)"
      >
        <textarea
          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-vertical"
          rows={4}
          placeholder="page-slug&#10;123&#10;Page Title"
        />
      </FormField>

      {/* Custom styled input with additional classes */}
      <FormField
        label="Custom Styled Input"
        className="bg-gray-50 p-4 rounded-lg"
      >
        <TextInput
          type="text"
          className="bg-white"
          placeholder="This input has custom styling"
        />
      </FormField>
    </div>
  );
};

export default FormFieldsExample;
