import metadata from '../../../../../block.json';

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
	const iconsets =
		window.zmShBlock && window.zmShBlock.iconsets
			? window.zmShBlock.iconsets
			: {
					inherit: __(
						'Inherit from plugin settings',
						'html-social-share-buttons'
					),
					default: __( 'Default', 'html-social-share-buttons' ),
			  };
	const iconsetAssets =
		window.zmShBlock && window.zmShBlock.iconsetAssets
			? window.zmShBlock.iconsetAssets
			: {};
	const inheritedIconset =
		window.zmShBlock && window.zmShBlock.inheritedIconset
			? window.zmShBlock.inheritedIconset
			: 'default';
	const networks = [
		{ id: 'facebook', label: __( 'Facebook', 'html-social-share-buttons' ) },
		{ id: 'x', label: 'X' },
		{ id: 'linkedin', label: __( 'LinkedIn', 'html-social-share-buttons' ) },
		{ id: 'pinterest', label: __( 'Pinterest', 'html-social-share-buttons' ) },
		{ id: 'telegram', label: __( 'Telegram', 'html-social-share-buttons' ) },
		{ id: 'bluesky', label: __( 'Bluesky', 'html-social-share-buttons' ) },
		{ id: 'mail', label: __( 'Email', 'html-social-share-buttons' ) },
	];

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
			const blockProps = { className: 'zm-sh-block-preview' };
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
												? __( 'Circle', 'html-social-share-buttons' )
												: __( 'Square', 'html-social-share-buttons' ),
										value: type,
									};
								} ),
								onChange( value ) {
									props.setAttributes( {
										iconset_type: value,
									} );
								},
							} ),
							networks.map( function ( network ) {
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
												? selected.concat( [
														network.id,
												  ] )
												: selected.filter(
														function ( id ) {
															return (
																id !==
																network.id
															);
														}
												  ),
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
									className: 'zm-sh-block-preview__icons',
									style: {
										display: 'flex',
										flexWrap: 'wrap',
										gap: '8px',
										alignItems: 'center',
										padding: '8px 0',
									},
								},
								selected.map( function ( id ) {
									const network = networks.find( function ( item ) {
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
											className: 'zm-sh-block-preview__icon',
											style: {
												width: '34px',
												height: '34px',
												objectFit: 'contain',
												borderRadius: previewType === 'circle' ? '50%' : '2px',
											},
										} )
										: null;
								} )
							)
						: el(
								'span',
								{ className: 'zm-sh-block-preview__empty' },
								__( 'Select share networks', 'html-social-share-buttons' )
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
