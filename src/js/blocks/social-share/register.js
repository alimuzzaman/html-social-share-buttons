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
	const sprintf = i18n.sprintf;
	const localized = editorData( 'hssbShareBlock' );
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
	const buttonAppearance = localized.buttonAppearance;
	const availableNetworks = networks( __ );
	const supportsBlockApiV3 =
		localized.apiVersion === 3 &&
		typeof blockEditor.useBlockProps === 'function';

	blocks.registerBlockType( metadata.name, {
		...metadata,
		apiVersion: supportsBlockApiV3 ? metadata.apiVersion : 1,
		edit( props ) {
			const modernAppearance = buttonAppearance !== 'legacy';
			const selected = props.attributes.icons || [];
			const selectedProfiles =
				props.attributes.profile_links_mode === 'none'
					? []
					: availableNetworks.filter( function ( network ) {
							return !! localized.profileLinks[ network.id ];
					  } );
			const selectableIconsets = Object.assign( {}, iconsets );
			if ( localized.legacyIconsets[ props.attributes.iconset ] ) {
				selectableIconsets[ props.attributes.iconset ] =
					localized.legacyIconsets[ props.attributes.iconset ];
			}
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
			const previewType =
				supportedTypes.indexOf( props.attributes.iconset_type ) !== -1
					? props.attributes.iconset_type
					: supportedTypes[ 0 ];
			const blockProps = Object.assign(
				{ key: 'preview' },
				supportsBlockApiV3
					? blockEditor.useBlockProps( {
							className: 'hssb-block-preview',
					  } )
					: { className: 'hssb-block-preview' }
			);
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
								key: 'title',
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
								key: 'iconset',
								label: __(
									'Icon set',
									'html-social-share-buttons'
								),
								value: props.attributes.iconset,
								options: Object.keys( selectableIconsets ).map(
									function ( id ) {
										return {
											label: selectableIconsets[ id ],
											value: id,
										};
									}
								),
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
								key: 'profile-links-mode',
								label: __(
									'Profile links after share buttons',
									'html-social-share-buttons'
								),
								value:
									props.attributes.profile_links_mode ||
									'inherit',
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
					selected.length || selectedProfiles.length
						? el(
								'div',
								{
									className: modernAppearance
										? 'zmshbt in_block ' + activeIconsetId + ' ' + previewType + ' hssb-appearance--' + buttonAppearance
										: 'hssb-block-preview__icons',
									style: modernAppearance ? { padding: '8px 0' } : {
										display: 'flex',
										flexWrap: 'wrap',
										gap: '8px',
										alignItems: 'center',
										padding: '8px 0',
									},
								},
								selected
									.map( function ( id ) {
										const src =
											activeIconset.icons[ id ] &&
											activeIconset.icons[ id ][
												previewType
											];
										if ( ! src ) {
											return null;
										}
										const network = availableNetworks.find(
											function ( item ) {
												return item.id === id;
											}
										);
										if ( modernAppearance ) {
											return el( 'a', {
													key: id,
													role: 'img',
													'aria-label': network ? network.label : id,
													style: { backgroundImage: 'url("' + src + '")' },
											  } );
										}
										return el( 'img', {
													key: id,
													src,
													alt: network
														? network.label
														: id,
													className:
														'hssb-block-preview__icon',
													style: {
														width: '34px',
														height: '34px',
														objectFit: 'contain',
														borderRadius:
															previewType ===
															'circle'
																? '50%'
																: '2px',
													},
												  } );
									} )
									.concat(
										selected.length &&
											selectedProfiles.length
											? [
													el( 'span', {
														key: 'profile-separator',
														className:
															'zmshbt-profile-separator',
														'aria-hidden': true,
													style: modernAppearance ? null : {
															width: '1px',
															height: '28px',
															margin: '3px 8px',
															background:
																'#c3c4c7',
														},
													} ),
											  ]
											: [],
										selectedProfiles.map(
											function ( network ) {
												const src =
													activeIconset.icons[
														network.id
													] &&
													activeIconset.icons[
														network.id
													][ previewType ];
												if ( ! src ) {
													return null;
												}
												if ( modernAppearance ) {
													return el( 'a', {
															key: 'profile-' + network.id,
															role: 'img',
															className: 'zmshbt-profile-link',
															'aria-label': network.label,
															style: { backgroundImage: 'url("' + src + '")' },
													  } );
												}
												return el( 'img', {
															key:
																'profile-' +
																network.id,
															src,
															alt:
																network.id ===
																'mail'
																	? __(
																			'Contact email',
																			'html-social-share-buttons'
																	  )
																	: sprintf(
																			/* translators: %s is the social network name. */
																			__(
																				'%s profile',
																				'html-social-share-buttons'
																			),
																			network.label
																	  ),
															className:
																'hssb-block-preview__icon hssb-block-preview__profile-icon',
															style: {
																width: '34px',
																height: '34px',
																objectFit:
																	'contain',
																borderRadius:
																	previewType ===
																	'circle'
																		? '50%'
																		: '2px',
															},
														  } );
											}
										)
									)
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
