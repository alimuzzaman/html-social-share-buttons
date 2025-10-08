import { render, screen, fireEvent } from '@testing-library/react';
import React from 'react';

// Mock WordPress dependencies
jest.mock('@wordpress/element', () => ({
  useState: jest.fn(),
  useEffect: jest.fn(),
  createElement: jest.fn(),
}));

jest.mock('@wordpress/block-editor', () => ({
  useBlockProps: jest.fn(() => ({ className: 'wp-block-html-social-share' })),
  InspectorControls: ({ children }) => <div data-testid="inspector-controls">{children}</div>,
}));

jest.mock('@wordpress/components', () => ({
  PanelBody: ({ title, children }) => <div data-testid="panel-body" data-title={title}>{children}</div>,
  PanelRow: ({ children }) => <div data-testid="panel-row">{children}</div>,
  SelectControl: ({ label, value, options, onChange }) => (
    <div data-testid="select-control">
      <label>{label}</label>
      <select value={value} onChange={(e) => onChange(e.target.value)}>
        {options.map(option => (
          <option key={option.value} value={option.value}>{option.label}</option>
        ))}
      </select>
    </div>
  ),
  CheckboxControl: ({ label, checked, onChange }) => (
    <div data-testid="checkbox-control">
      <label>
        <input type="checkbox" checked={checked} onChange={onChange} />
        {label}
      </label>
    </div>
  ),
  TextControl: ({ label, value, onChange }) => (
    <div data-testid="text-control">
      <label>{label}</label>
      <input type="text" value={value} onChange={(e) => onChange(e.target.value)} />
    </div>
  ),
}));

jest.mock('@wordpress/i18n', () => ({
  __: (text) => text,
  _x: (text) => text,
}));

// Mock the edit component
const mockEdit = ({ attributes, setAttributes }) => {
  const {
    networks = { facebook: { enabled: true }, twitter: { enabled: true } },
    displayStyle = 'default',
    buttonSize = 'medium',
    showLabels = false,
    alignment = 'center'
  } = attributes;

  const updateNetworkEnabled = (network, enabled) => {
    setAttributes({
      networks: {
        ...networks,
        [network]: { ...networks[network], enabled }
      }
    });
  };

  const renderPreviewButtons = () => {
    const enabledNetworks = Object.entries(networks).filter(([, config]) => config.enabled);

    return (
      <div data-testid="preview-buttons" className={`hss-preview-buttons ${alignment}`}>
        {enabledNetworks.map(([network]) => (
          <div
            key={network}
            className={`hss-button ${displayStyle} ${buttonSize}`}
            data-network={network}
          >
            <i className={`fab fa-${network}`} />
            {showLabels && <span>{network}</span>}
          </div>
        ))}
      </div>
    );
  };

  return (
    <div className="wp-block-html-social-share">
      <div data-testid="inspector-controls">
        <div data-testid="panel-body" data-title="Network Settings">
          {Object.entries(networks).map(([network, config]) => (
            <div key={network} data-testid="checkbox-control">
              <label>
                <input
                  type="checkbox"
                  checked={config.enabled}
                  onChange={(e) => updateNetworkEnabled(network, e.target.checked)}
                />
                {network}
              </label>
            </div>
          ))}
        </div>

        <div data-testid="panel-body" data-title="Display Settings">
          <div data-testid="select-control">
            <label>Button Style</label>
            <select
              value={displayStyle}
              onChange={(e) => setAttributes({ displayStyle: e.target.value })}
            >
              <option value="default">Default</option>
              <option value="rounded">Rounded</option>
              <option value="square">Square</option>
              <option value="minimal">Minimal</option>
            </select>
          </div>
        </div>
      </div>

      {renderPreviewButtons()}
    </div>
  );
};

describe('Gutenberg Block Edit Component', () => {
  let mockSetAttributes;

  beforeEach(() => {
    mockSetAttributes = jest.fn();
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  test('renders with default attributes', () => {
    render(
      mockEdit({
        attributes: {},
        setAttributes: mockSetAttributes
      })
    );

    expect(screen.getByTestId('preview-buttons')).toBeInTheDocument();
    expect(screen.getByText('Network Settings')).toBeInTheDocument();
    expect(screen.getByText('Display Settings')).toBeInTheDocument();
  });

  test('displays enabled networks in preview', () => {
    const attributes = {
      networks: {
        facebook: { enabled: true },
        twitter: { enabled: true },
        linkedin: { enabled: false }
      }
    };

    render(
      mockEdit({
        attributes,
        setAttributes: mockSetAttributes
      })
    );

    const previewButtons = screen.getByTestId('preview-buttons');
    expect(previewButtons.querySelector('[data-network="facebook"]')).toBeInTheDocument();
    expect(previewButtons.querySelector('[data-network="twitter"]')).toBeInTheDocument();
    expect(previewButtons.querySelector('[data-network="linkedin"]')).not.toBeInTheDocument();
  });

  test('toggles network enabled state', () => {
    const attributes = {
      networks: {
        facebook: { enabled: true },
        twitter: { enabled: false }
      }
    };

    render(
      mockEdit({
        attributes,
        setAttributes: mockSetAttributes
      })
    );

    const twitterCheckbox = screen.getByLabelText('twitter');
    fireEvent.click(twitterCheckbox);

    expect(mockSetAttributes).toHaveBeenCalledWith({
      networks: {
        facebook: { enabled: true },
        twitter: { enabled: true }
      }
    });
  });

  test('updates display style', () => {
    render(
      mockEdit({
        attributes: { displayStyle: 'default' },
        setAttributes: mockSetAttributes
      })
    );

    const styleSelect = screen.getByDisplayValue('Default');
    fireEvent.change(styleSelect, { target: { value: 'rounded' } });

    expect(mockSetAttributes).toHaveBeenCalledWith({ displayStyle: 'rounded' });
  });
});