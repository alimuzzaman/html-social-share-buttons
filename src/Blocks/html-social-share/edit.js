import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, BlockControls, AlignmentToolbar } from '@wordpress/block-editor';
import {
  PanelBody,
  SelectControl,
  ToggleControl,
  TextControl,
  RangeControl,
  CheckboxControl,
  Button,
  ButtonGroup,
  Disabled
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import './editor.css';

const availableNetworks = [
  { id: 'facebook', name: 'Facebook', icon: 'fab fa-facebook-f' },
  { id: 'twitter', name: 'Twitter', icon: 'fab fa-twitter' },
  { id: 'linkedin', name: 'LinkedIn', icon: 'fab fa-linkedin-in' },
  { id: 'pinterest', name: 'Pinterest', icon: 'fab fa-pinterest-p' },
  { id: 'reddit', name: 'Reddit', icon: 'fab fa-reddit-alien' },
  { id: 'whatsapp', name: 'WhatsApp', icon: 'fab fa-whatsapp' },
  { id: 'telegram', name: 'Telegram', icon: 'fab fa-telegram-plane' },
  { id: 'email', name: 'Email', icon: 'fas fa-envelope' },
];

export default function Edit({ attributes, setAttributes }) {
  const {
    profileId,
    networks,
    displayStyle,
    buttonSize,
    showLabels,
    iconOnly,
    customUrl,
    customTitle,
    customDescription,
    alignment,
    spacing,
    customCss
  } = attributes;

  const [profiles, setProfiles] = useState([]);
  const [isLoadingProfiles, setIsLoadingProfiles] = useState(true);

  const blockProps = useBlockProps({
    className: `wp-block-html-social-share-buttons align-${alignment}`,
  });

  // Load available profiles
  useEffect(() => {
    // Simulate loading profiles - replace with actual API call
    setTimeout(() => {
      setProfiles([
        { id: '1', name: 'Default Profile' },
        { id: '2', name: 'Blog Posts' },
        { id: '3', name: 'Product Pages' },
      ]);
      setIsLoadingProfiles(false);
    }, 1000);
  }, []);

  const updateNetworkSetting = (networkId, enabled) => {
    setAttributes({
      networks: {
        ...networks,
        [networkId]: {
          ...networks[networkId],
          enabled
        }
      }
    });
  };

  const renderPreviewButtons = () => {
    const enabledNetworks = Object.entries(networks)
      .filter(([, settings]) => settings.enabled)
      .map(([networkId]) => networkId);

    if (enabledNetworks.length === 0) {
      return (
        <div className="social-share-preview-placeholder">
          <p>{__('Select networks to display preview', 'html-social-share')}</p>
        </div>
      );
    }

    return (
      <div className={`social-share-buttons social-share-${displayStyle} social-share-${buttonSize} social-share-align-${alignment}`}>
        {enabledNetworks.map(networkId => {
          const network = availableNetworks.find(n => n.id === networkId);
          if (!network) return null;

          return (
            <Disabled key={networkId}>
              <button
                className={`social-share-button social-share-${networkId}`}
                title={`Share on ${network.name}`}
              >
                <i className={network.icon} />
                {showLabels && <span className="social-share-label">{network.name}</span>}
              </button>
            </Disabled>
          );
        })}
      </div>
    );
  };

  return (
    <>
      <BlockControls>
        <AlignmentToolbar
          value={alignment}
          onChange={(newAlignment) => setAttributes({ alignment: newAlignment })}
          alignmentControls={[
            {
              icon: 'editor-alignleft',
              title: __('Align left', 'html-social-share'),
              align: 'left',
            },
            {
              icon: 'editor-aligncenter',
              title: __('Align center', 'html-social-share'),
              align: 'center',
            },
            {
              icon: 'editor-alignright',
              title: __('Align right', 'html-social-share'),
              align: 'right',
            },
          ]}
        />
      </BlockControls>

      <InspectorControls>
        <PanelBody title={__('Profile Settings', 'html-social-share')} initialOpen={true}>
          <SelectControl
            label={__('Profile', 'html-social-share')}
            value={profileId}
            options={[
              { label: __('Custom Configuration', 'html-social-share'), value: '' },
              ...profiles.map(profile => ({
                label: profile.name,
                value: profile.id
              }))
            ]}
            onChange={(value) => setAttributes({ profileId: value })}
            help={__('Choose a predefined profile or create a custom configuration.', 'html-social-share')}
          />

          {isLoadingProfiles && (
            <p>{__('Loading profiles...', 'html-social-share')}</p>
          )}
        </PanelBody>

        <PanelBody title={__('Networks', 'html-social-share')} initialOpen={!profileId}>
          <p className="components-base-control__help">
            {__('Select which social networks to display.', 'html-social-share')}
          </p>

          {availableNetworks.map(network => (
            <CheckboxControl
              key={network.id}
              label={
                <span>
                  <i className={`${network.icon} social-share-icon`} /> {network.name}
                </span>
              }
              checked={networks[network.id]?.enabled || false}
              onChange={(enabled) => updateNetworkSetting(network.id, enabled)}
            />
          ))}
        </PanelBody>

        <PanelBody title={__('Appearance', 'html-social-share')} initialOpen={false}>
          <SelectControl
            label={__('Button Style', 'html-social-share')}
            value={displayStyle}
            options={[
              { label: __('Default', 'html-social-share'), value: 'default' },
              { label: __('Rounded', 'html-social-share'), value: 'rounded' },
              { label: __('Square', 'html-social-share'), value: 'square' },
              { label: __('Minimal', 'html-social-share'), value: 'minimal' },
            ]}
            onChange={(value) => setAttributes({ displayStyle: value })}
          />

          <SelectControl
            label={__('Button Size', 'html-social-share')}
            value={buttonSize}
            options={[
              { label: __('Small', 'html-social-share'), value: 'small' },
              { label: __('Medium', 'html-social-share'), value: 'medium' },
              { label: __('Large', 'html-social-share'), value: 'large' },
            ]}
            onChange={(value) => setAttributes({ buttonSize: value })}
          />

          <ToggleControl
            label={__('Show Labels', 'html-social-share')}
            checked={showLabels}
            onChange={(value) => setAttributes({ showLabels: value })}
            help={__('Display network names alongside icons.', 'html-social-share')}
          />

          <ToggleControl
            label={__('Icon Only Mode', 'html-social-share')}
            checked={iconOnly}
            onChange={(value) => setAttributes({ iconOnly: value })}
            help={__('Display only icons without background styling.', 'html-social-share')}
          />
        </PanelBody>

        <PanelBody title={__('Custom Share Content', 'html-social-share')} initialOpen={false}>
          <TextControl
            label={__('Custom URL', 'html-social-share')}
            value={customUrl}
            onChange={(value) => setAttributes({ customUrl: value })}
            help={__('Leave empty to use the current page URL.', 'html-social-share')}
            placeholder="https://example.com"
          />

          <TextControl
            label={__('Custom Title', 'html-social-share')}
            value={customTitle}
            onChange={(value) => setAttributes({ customTitle: value })}
            help={__('Leave empty to use the current page title.', 'html-social-share')}
          />

          <TextControl
            label={__('Custom Description', 'html-social-share')}
            value={customDescription}
            onChange={(value) => setAttributes({ customDescription: value })}
            help={__('Leave empty to use the current page excerpt.', 'html-social-share')}
          />
        </PanelBody>

        <PanelBody title={__('Advanced', 'html-social-share')} initialOpen={false}>
          <TextControl
            label={__('Custom CSS', 'html-social-share')}
            value={customCss}
            onChange={(value) => setAttributes({ customCss: value })}
            help={__('Add custom CSS styles for this block.', 'html-social-share')}
            placeholder=".social-share-buttons { margin: 20px 0; }"
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="social-share-block-wrapper">
          <div className="social-share-block-header">
            <h4 className="social-share-block-title">
              <i className="fas fa-share-alt" /> {__('Social Share Buttons', 'html-social-share')}
            </h4>
            <span className="social-share-block-subtitle">
              {profileId ? __('Profile: ', 'html-social-share') + (profiles.find(p => p.id === profileId)?.name || __('Unknown', 'html-social-share')) : __('Custom Configuration', 'html-social-share')}
            </span>
          </div>

          <div className="social-share-block-preview">
            {renderPreviewButtons()}
          </div>

          {customCss && (
            <style dangerouslySetInnerHTML={{ __html: customCss }} />
          )}
        </div>
      </div>
    </>
  );
}