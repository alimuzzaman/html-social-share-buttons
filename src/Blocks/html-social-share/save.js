import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
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

  const blockProps = useBlockProps.save({
    className: `wp-block-html-social-share-buttons align-${alignment}`,
  });

  // Return placeholder for server-side rendering
  // The actual content will be rendered by PHP
  return (
    <div {...blockProps}>
      <div
        className="social-share-buttons-placeholder"
        data-profile-id={profileId}
        data-networks={JSON.stringify(networks)}
        data-display-style={displayStyle}
        data-button-size={buttonSize}
        data-show-labels={showLabels}
        data-icon-only={iconOnly}
        data-custom-url={customUrl}
        data-custom-title={customTitle}
        data-custom-description={customDescription}
        data-alignment={alignment}
        data-spacing={JSON.stringify(spacing)}
        data-custom-css={customCss}
      >
        {/* This will be replaced by server-side rendering */}
        <noscript>
          Social sharing buttons will be displayed here.
        </noscript>
      </div>

      {customCss && (
        <style dangerouslySetInnerHTML={{ __html: customCss }} />
      )}
    </div>
  );
}