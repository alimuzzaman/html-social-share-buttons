import metadata from '../../../../block.json';
import { editorData } from '../shared/block-data';
import { networks } from '../shared/networks';

/* Bundled at build time through src/js/social-share.js. */
( function ( blocks, element, blockEditor, components, i18n ) {
	'use strict';

	const el = element.createElement;
	const Fragment = element.Fragment;
	const InspectorControls = blockEditor.InspectorControls;
	const PanelBody = components.PanelBody;
	const SelectControl = components.SelectControl;
	const TextControl = components.TextControl;
	const CheckboxControl = components.CheckboxControl;
	const __ = i18n.__;
	const localized = editorData( 'hssbShareBlock' );
	const iconsets = Object.keys( localized.iconsets ).length
		? localized.iconsets
		: {
				inherit: __(
					'Inherit from plugin settings',
					'html-social-share-buttons'
				),
				default: __( 'Default', 'html-social-share-buttons' ),
		  };
	const iconsetAssets = localized.iconsetAssets;
	const inheritedIconset = localized.inheritedIconset;
	const availableNetworks = networks( __ );

	blocks.registerBlockType( metadata.name, {
		...metadata,
		edit( props ) {
			const selected = props.attributes.icons || [];
			const activeIconsetId =
				props.attributes.iconset === 'inherit'
					? inheritedIconset
					: props.attributes.iconset;
			const activeIconset = iconsetAssets[ activeIconsetId ] || {
				types: [ 'square' ],
				icons: {},
			};
			const supportedTypes = activeIconset.types.length
				? activeIconset.types
				: [ 'square' ];
			const previewType = supportedTypes.indexOf(
				props.attributes.iconset_type
			) !== -1
				? props.attributes.iconset_type
				: supportedTypes[ 0 ];
			const blockProps = { className: 'hssb-block-preview' };
			return el( Fragment, {}, [
				el(
					InspectorControls,
					{ key: 'controls' },
					el(
						PanelBody,
						{
							title: __(
								'Share buttons',
								'html-social-share-buttons'
							),
							initialOpen: true,
						},
						[
							el( TextControl, {
								label: __(
									'Title',
									'html-social-share-buttons'
								),
								value: props.attributes.title,
								onChange( value ) {
									props.setAttributes( { title: value } );
								},
							} ),
							el( SelectControl, {
								label: __(
									'Icon set',
									'html-social-share-buttons'
								),
								value: props.attributes.iconset,
								options: Object.keys( iconsets ).map(
									function ( id ) {
										return {
											label: iconsets[ id ],
											value: id,
										};
									}
								),
								onChange( value ) {
									props.setAttributes( { iconset: value } );
								},
							} ),
							el( SelectControl, {
								label: __(
									'Button shape',
									'html-social-share-buttons'
								),
								value: props.attributes.iconset_type,
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
									props.setAttributes( {
										iconset_type: value,
									} );
								},
							} ),
							el( SelectControl, {
								label: __(
									'Profile links',
									'html-social-share-buttons'
								),
								value: props.attributes.profile_links_mode || 'inherit',
								options: [
									{
										label: __(
											'Show configured profile links',
											'html-social-share-buttons'
										),
										value: 'inherit',
									},
									{
										label: __(
											'Hide profile links in this block',
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
							availableNetworks.map( function ( network ) {
								return el( CheckboxControl, {
									key: network.id,
									label: network.label,
									checked:
										selected.indexOf( network.id ) !== -1,
									disabled:
										selected.length === 1 &&
										selected[ 0 ] === network.id,
									onChange( checked ) {
										props.setAttributes( {
											icons: checked
												? selected.concat( [ network.id ] )
												: selected.filter( function ( id ) {
														return id !== network.id;
												  } ),
										} );
									},
								} );
							} ),
						]
					)
				),
				el(
					'div',
					blockProps,
					selected.length
						? el(
								'div',
								{
									className: 'hssb-block-preview__icons',
									style: {
										display: 'flex',
										flexWrap: 'wrap',
										gap: '8px',
										alignItems: 'center',
										padding: '8px 0',
									},
								},
								selected.map( function ( id ) {
									const network = availableNetworks.find( function ( item ) {
										return item.id === id;
									} );
									const src =
										activeIconset.icons[ id ] &&
										activeIconset.icons[ id ][ previewType ];
									return src
										? el( 'img', {
												key: id,
												src,
												alt: network ? network.label : id,
												className: 'hssb-block-preview__icon',
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
								{ className: 'hssb-block-preview__empty' },
								__(
									'Select share networks',
									'html-social-share-buttons'
								)
						  )
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
