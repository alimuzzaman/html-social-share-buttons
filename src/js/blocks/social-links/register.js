import metadata from '../../../../blocks/social-links/block.json';
import { editorData } from '../shared/block-data';
import { networks } from '../shared/networks';

/* Bundled at build time through src/js/social-links.js. */
( function ( blocks, element, blockEditor, components, i18n ) {
	'use strict';

	const el = element.createElement;
	const Fragment = element.Fragment;
	const InspectorControls = blockEditor.InspectorControls;
	const PanelBody = components.PanelBody;
	const SelectControl = components.SelectControl;
	const TextControl = components.TextControl;
	const __ = i18n.__;
	const sprintf = i18n.sprintf;
	const localized = editorData( 'hssbSocialLinksBlock' );
	const iconsets = Object.keys( localized.iconsets ).length
		? localized.iconsets
		: {
				inherit: __(
					'Inherit from plugin settings',
					'html-social-share-buttons'
				),
				'bootstrap-solid': __(
					'Bootstrap Solid',
					'html-social-share-buttons'
				),
		  };
	const iconsetAssets = localized.iconsetAssets;
	const inheritedIconset = localized.inheritedIconset;
	const availableNetworks = networks( __ );
	const supportsBlockApiV3 =
		localized.apiVersion === 3 &&
		typeof blockEditor.useBlockProps === 'function';

	function profileLinksForPreview( attributes ) {
		if ( attributes.profile_links_mode === 'none' ) {
			return {};
		}

		if ( attributes.profile_links_mode === 'custom' ) {
			return attributes.profile_links || {};
		}

		return localized.profileLinks;
	}

	blocks.registerBlockType( metadata.name, {
		...metadata,
		apiVersion: supportsBlockApiV3 ? metadata.apiVersion : 1,
		edit( props ) {
			const attributes = props.attributes;
			const selectableIconsets = Object.assign( {}, iconsets );
			if ( localized.legacyIconsets[ attributes.iconset ] ) {
				selectableIconsets[ attributes.iconset ] =
					localized.legacyIconsets[ attributes.iconset ];
			}
			const activeIconsetId =
				attributes.iconset === 'inherit'
					? inheritedIconset
					: attributes.iconset;
			const activeIconset = iconsetAssets[ activeIconsetId ] || {
				types: [ 'square' ],
				icons: {},
			};
			const supportedTypes = activeIconset.types.length
				? activeIconset.types
				: [ 'square' ];
			const previewType = supportedTypes.indexOf(
				attributes.iconset_type
			) !== -1
				? attributes.iconset_type
				: supportedTypes[ 0 ];
			const profileLinks = profileLinksForPreview( attributes );
			const selectedNetworks = availableNetworks.filter( function ( network ) {
				return !! profileLinks[ network.id ];
			} );
			const blockProps = Object.assign(
				{ key: 'preview' },
				supportsBlockApiV3
					? blockEditor.useBlockProps( {
							className: 'hssb-social-links-block-preview',
					  } )
					: { className: 'hssb-social-links-block-preview' }
			);

			return el( Fragment, {}, [
				el(
					InspectorControls,
					{ key: 'controls' },
					el(
						PanelBody,
						{
							title: __(
								'Social links',
								'html-social-share-buttons'
							),
							initialOpen: true,
						},
						[
							el( TextControl, {
								key: 'title',
								label: __(
									'Title',
									'html-social-share-buttons'
								),
								value: attributes.title,
								onChange( value ) {
									props.setAttributes( { title: value } );
								},
							} ),
							el( SelectControl, {
								key: 'iconset',
								label: __(
									'Icon set',
									'html-social-share-buttons'
								),
								value: attributes.iconset,
								options: Object.keys( selectableIconsets ).map( function ( id ) {
									return { label: selectableIconsets[ id ], value: id };
								} ),
								onChange( value ) {
									props.setAttributes( { iconset: value } );
								},
							} ),
							el( SelectControl, {
								key: 'shape',
								label: __(
									'Button shape',
									'html-social-share-buttons'
								),
								value: attributes.iconset_type,
								options: supportedTypes.map( function ( type ) {
									return {
										label:
											type === 'circle'
												? __(
														'Circle',
														'html-social-share-buttons'
													)
												: __(
														'Square',
														'html-social-share-buttons'
													),
										value: type,
									};
								} ),
								onChange( value ) {
									props.setAttributes( { iconset_type: value } );
								},
							} ),
							el( SelectControl, {
								key: 'profile-links-mode',
								label: __(
									'Profile links',
									'html-social-share-buttons'
								),
								value: attributes.profile_links_mode,
								options: [
									{
										label: __(
											'Inherit from plugin settings',
											'html-social-share-buttons'
										),
										value: 'inherit',
									},
									{
										label: __(
											'Use custom profile links',
											'html-social-share-buttons'
										),
										value: 'custom',
									},
									{
										label: __(
											'Do not display profile links',
											'html-social-share-buttons'
										),
										value: 'none',
									},
								],
								onChange( value ) {
									props.setAttributes( {
										profile_links_mode: value,
									} );
								},
							} ),
							attributes.profile_links_mode === 'custom'
								? availableNetworks.map( function ( network ) {
										const isMail = network.id === 'mail';
										return el( TextControl, {
											key: network.id,
											label: isMail
												? __(
														'Email destination',
														'html-social-share-buttons'
													)
												: sprintf(
														/* translators: %s is the social network name. */
														__(
															'%s profile URL',
															'html-social-share-buttons'
														),
														network.label
													  ),
											type: 'url',
											value:
												( attributes.profile_links || {} )[ network.id ] || '',
											placeholder: isMail
												? 'mailto:hello@example.com'
												: 'https://',
											onChange( value ) {
													const updatedProfileLinks = Object.assign(
													{},
													attributes.profile_links || {}
												);
													updatedProfileLinks[ network.id ] = value;
													props.setAttributes( {
														profile_links: updatedProfileLinks,
												} );
											},
										} );
									  } )
								: null,
						]
					)
				),
				el(
					'div',
					blockProps,
					[
						attributes.title
							? el( 'h3', { key: 'title' }, attributes.title )
							: null,
						selectedNetworks.length
							? el(
									'div',
									{
										key: 'icons',
										className: 'hssb-social-links-block-preview__icons',
										style: {
											display: 'flex',
											flexWrap: 'wrap',
											gap: '8px',
											alignItems: 'center',
											padding: '8px 0',
										},
									},
									selectedNetworks.map( function ( network ) {
										const src =
											activeIconset.icons[ network.id ] &&
											activeIconset.icons[ network.id ][ previewType ];
										return src
											? el( 'img', {
													key: network.id,
													src,
													alt: network.label,
													className:
														'hssb-social-links-block-preview__icon',
													style: {
														width: '34px',
														height: '34px',
														objectFit: 'contain',
														borderRadius:
															previewType === 'circle'
																? '50%'
																: '2px',
													},
											  } )
											: null;
									} )
								  )
							: el(
									'span',
									{
										key: 'empty',
										className:
											'hssb-social-links-block-preview__empty',
									},
									__(
										'Add profile links in the plugin settings or choose custom links.',
										'html-social-share-buttons'
									)
								  ),
					]
				),
			] );
		},
		save() {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n
);
