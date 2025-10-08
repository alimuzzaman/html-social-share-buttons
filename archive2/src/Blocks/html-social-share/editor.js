/**
 * Gutenberg Block Editor Script
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	CheckboxControl,
} from '@wordpress/components';
import { ServerSideRender } from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';

const Edit = ( { attributes, setAttributes } ) => {
	const { networks, iconset, title, alignment } = attributes;

	const availableNetworks = [
		{ label: 'Facebook', value: 'facebook' },
		{ label: 'X (formerly Twitter)', value: 'twitter' },
		{ label: 'LinkedIn', value: 'linkedin' },
		{ label: 'Pinterest', value: 'pinterest' },
		{ label: 'WhatsApp', value: 'whatsapp' },
		{ label: 'Telegram', value: 'telegram' },
		{ label: 'Reddit', value: 'reddit' },
		{ label: 'Tumblr', value: 'tumblr' },
		{ label: 'Email', value: 'email' },
		{ label: 'Mastodon', value: 'mastodon' },
		{ label: 'Bluesky', value: 'bluesky' },
		{ label: 'Threads', value: 'threads' },
		{ label: 'VK', value: 'vk' },
		{ label: 'WeChat', value: 'wechat' },
		{ label: 'Instagram Direct', value: 'instagram' },
		{ label: 'Messenger', value: 'messenger' },
	];

	const availableIconsets = [
		{ label: 'Default (Square)', value: 'default' },
		{ label: 'Flat Square', value: 'square' },
		{ label: 'Flat Circle', value: 'circle' },
		{ label: 'Minimal', value: 'minimal' },
	];

	const alignmentOptions = [
		{ label: 'Left', value: 'left' },
		{ label: 'Center', value: 'center' },
		{ label: 'Right', value: 'right' },
	];

	const handleNetworkChange = ( network, checked ) => {
		const newNetworks = checked
			? [ ...networks, network ]
			: networks.filter( ( n ) => n !== network );
		setAttributes( { networks: newNetworks } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Share Buttons Settings',
						'html-social-share'
					) }
				>
					<TextControl
						label={ __( 'Title', 'html-social-share' ) }
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
					/>

					<SelectControl
						label={ __( 'Icon Set', 'html-social-share' ) }
						value={ iconset }
						options={ availableIconsets }
						onChange={ ( value ) =>
							setAttributes( { iconset: value } )
						}
					/>

					<SelectControl
						label={ __( 'Alignment', 'html-social-share' ) }
						value={ alignment }
						options={ alignmentOptions }
						onChange={ ( value ) =>
							setAttributes( { alignment: value } )
						}
					/>

					<div>
						<label>
							{ __( 'Social Networks', 'html-social-share' ) }
						</label>
						{ availableNetworks.map( ( network ) => (
							<CheckboxControl
								key={ network.value }
								label={ network.label }
								checked={ networks.includes( network.value ) }
								onChange={ ( checked ) =>
									handleNetworkChange(
										network.value,
										checked
									)
								}
							/>
						) ) }
					</div>
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<ServerSideRender
					block="html-social-share/buttons"
					attributes={ attributes }
					httpMethod="POST"
				/>
			</div>
		</>
	);
};

registerBlockType( 'html-social-share/buttons', {
	edit: Edit,
	save: () => null, // Server-side rendering
} );
