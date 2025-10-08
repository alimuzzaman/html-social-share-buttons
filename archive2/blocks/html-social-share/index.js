import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * WordPress components that create the necessary UI elements for the block
 */
import Edit from './edit.js';
import save from './save.js';
import metadata from './block.json';

/**
 * Every block starts by importing the block.json file
 */
import './style.scss';

/**
 * Register the Social Share Buttons block
 */
registerBlockType( metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,

	/**
	 * Block icon
	 */
	icon: {
		src: (
			<svg
				width="24"
				height="24"
				viewBox="0 0 24 24"
				xmlns="http://www.w3.org/2000/svg"
			>
				<path
					fill="currentColor"
					d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92S19.61 16.08 18 16.08z"
				/>
			</svg>
		),
		background: '#1e73be',
		foreground: '#ffffff',
	},

	/**
	 * Block keywords for better searchability
	 */
	keywords: [
		__( 'social', 'html-social-share' ),
		__( 'share', 'html-social-share' ),
		__( 'buttons', 'html-social-share' ),
		__( 'facebook', 'html-social-share' ),
		__( 'twitter', 'html-social-share' ),
		__( 'linkedin', 'html-social-share' ),
	],

	/**
	 * Block example for the block inserter preview
	 */
	example: {
		attributes: {
			networks: {
				facebook: { enabled: true },
				twitter: { enabled: true },
				linkedin: { enabled: true },
			},
			displayStyle: 'rounded',
			buttonSize: 'medium',
			showLabels: true,
			alignment: 'center',
		},
	},

	/**
	 * Block variations for quick insertion
	 */
	variations: [
		{
			name: 'social-share-basic',
			title: __( 'Basic Social Share', 'html-social-share' ),
			description: __(
				'Facebook, Twitter, and LinkedIn buttons',
				'html-social-share'
			),
			icon: 'share',
			attributes: {
				networks: {
					facebook: { enabled: true },
					twitter: { enabled: true },
					linkedin: { enabled: true },
				},
				displayStyle: 'default',
				buttonSize: 'medium',
			},
			scope: [ 'inserter' ],
		},
		{
			name: 'social-share-complete',
			title: __( 'Complete Social Share', 'html-social-share' ),
			description: __(
				'All popular social networks',
				'html-social-share'
			),
			icon: 'share',
			attributes: {
				networks: {
					facebook: { enabled: true },
					twitter: { enabled: true },
					linkedin: { enabled: true },
					pinterest: { enabled: true },
					reddit: { enabled: true },
					whatsapp: { enabled: true },
				},
				displayStyle: 'rounded',
				buttonSize: 'medium',
				showLabels: true,
			},
			scope: [ 'inserter' ],
		},
		{
			name: 'social-share-minimal',
			title: __( 'Minimal Social Share', 'html-social-share' ),
			description: __( 'Icon-only minimal style', 'html-social-share' ),
			icon: 'share',
			attributes: {
				networks: {
					facebook: { enabled: true },
					twitter: { enabled: true },
					linkedin: { enabled: true },
				},
				displayStyle: 'minimal',
				buttonSize: 'small',
				iconOnly: true,
				showLabels: false,
			},
			scope: [ 'inserter' ],
		},
	],
} );
