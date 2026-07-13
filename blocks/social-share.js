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
	const networks = [
		{ id: 'facebook', label: 'Facebook' },
		{ id: 'x', label: 'X' },
		{ id: 'linkedin', label: 'LinkedIn' },
		{ id: 'pinterest', label: 'Pinterest' },
		{ id: 'telegram', label: 'Telegram' },
		{ id: 'bluesky', label: 'Bluesky' },
		{ id: 'mail', label: 'Email' },
	];

	blocks.registerBlockType( 'html-social-share/social-share', {
		title: __( 'Html Social Share', 'html-social-share-buttons' ),
		icon: 'share',
		category: 'widgets',
		attributes: {
			title: { type: 'string', default: 'Share this page' },
			iconset: { type: 'string', default: 'inherit' },
			iconset_type: { type: 'string', default: 'square' },
			icons: {
				type: 'array',
				default: [ 'facebook', 'x', 'linkedin', 'pinterest', 'mail' ],
			},
		},
		edit( props ) {
			const selected = props.attributes.icons || [];
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
								options: [
									{
										label: __(
											'Square',
											'html-social-share-buttons'
										),
										value: 'square',
									},
									{
										label: __(
											'Circle',
											'html-social-share-buttons'
										),
										value: 'circle',
									},
								],
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
					el(
						'strong',
						{},
						__( 'Html Social Share', 'html-social-share-buttons' )
					),
					el(
						'p',
						{},
						props.attributes.title ||
							__( 'Share this page', 'html-social-share-buttons' )
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
