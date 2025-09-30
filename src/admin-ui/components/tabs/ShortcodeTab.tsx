import React, { useState, useEffect, useCallback } from 'react';
import { FormField, Select, Checkbox, Button, TextInput } from '../ui';
import { useNotifications } from '../../contexts';

interface ShortcodeOptions {
	networks: string[];
	style: string;
	size: string;
	show_labels: boolean;
	show_counts: boolean;
	url: string;
	title: string;
	align: string;
	class: string;
}

export const ShortcodeTab: React.FC = () => {
	const { showSuccess } = useNotifications();

	const [ options, setOptions ] = useState< ShortcodeOptions >( {
		networks: [ 'facebook', 'twitter', 'linkedin' ],
		style: 'default',
		size: 'medium',
		show_labels: false,
		show_counts: false,
		url: '',
		title: '',
		align: 'left',
		class: '',
	} );

	const [ generatedShortcode, setGeneratedShortcode ] = useState( '' );

	const availableNetworks = [
		{ id: 'facebook', name: 'Facebook' },
		{ id: 'twitter', name: 'Twitter' },
		{ id: 'linkedin', name: 'LinkedIn' },
		{ id: 'pinterest', name: 'Pinterest' },
		{ id: 'reddit', name: 'Reddit' },
		{ id: 'whatsapp', name: 'WhatsApp' },
		{ id: 'email', name: 'Email' },
		{ id: 'print', name: 'Print' },
	];

	const styleOptions = [
		{ value: 'default', label: 'Default' },
		{ value: 'rounded', label: 'Rounded' },
		{ value: 'square', label: 'Square' },
		{ value: 'minimal', label: 'Minimal' },
	];

	const sizeOptions = [
		{ value: 'small', label: 'Small' },
		{ value: 'medium', label: 'Medium' },
		{ value: 'large', label: 'Large' },
	];

	const alignOptions = [
		{ value: 'left', label: 'Left' },
		{ value: 'center', label: 'Center' },
		{ value: 'right', label: 'Right' },
	];

	const generateShortcode = useCallback( () => {
		const params: string[] = [];

		if (
			options.networks.length > 0 &&
			options.networks.length < availableNetworks.length
		) {
			params.push( `networks="${ options.networks.join( ',' ) }"` );
		}

		if ( options.style !== 'default' ) {
			params.push( `style="${ options.style }"` );
		}

		if ( options.size !== 'medium' ) {
			params.push( `size="${ options.size }"` );
		}

		if ( options.show_labels ) {
			params.push( 'show_labels="true"' );
		}

		if ( options.show_counts ) {
			params.push( 'show_counts="true"' );
		}

		if ( options.url ) {
			params.push( `url="${ options.url }"` );
		}

		if ( options.title ) {
			params.push( `title="${ options.title }"` );
		}

		if ( options.align !== 'left' ) {
			params.push( `align="${ options.align }"` );
		}

		if ( options.class ) {
			params.push( `class="${ options.class }"` );
		}

		const shortcode =
			params.length > 0
				? `[html_social_share_buttons ${ params.join( ' ' ) }]`
				: '[html_social_share_buttons]';

		setGeneratedShortcode( shortcode );
	}, [ options, availableNetworks.length ] );

	// Generate shortcode whenever options change
	useEffect( () => {
		generateShortcode();
	}, [ generateShortcode ] );

	const updateOption = ( key: keyof ShortcodeOptions, value: any ) => {
		setOptions( ( prev ) => ( { ...prev, [ key ]: value } ) );
	};

	const handleNetworkToggle = ( networkId: string, enabled: boolean ) => {
		if ( enabled ) {
			updateOption( 'networks', [ ...options.networks, networkId ] );
		} else {
			updateOption(
				'networks',
				options.networks.filter( ( id ) => id !== networkId )
			);
		}
	};

	const copyToClipboard = async () => {
		try {
			await navigator.clipboard.writeText( generatedShortcode );
			showSuccess( 'Shortcode copied to clipboard!' );
		} catch ( error ) {
			// Fallback for older browsers
			const textArea = document.createElement( 'textarea' );
			textArea.value = generatedShortcode;
			document.body.appendChild( textArea );
			textArea.select();
			document.execCommand( 'copy' );
			document.body.removeChild( textArea );
			showSuccess( 'Shortcode copied to clipboard!' );
		}
	};

	return (
		<div className="shortcode-tab">
			<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
				<h2 className="text-xl font-semibold mb-4">
					Shortcode Generator
				</h2>
				<p className="text-gray-600 mb-6">
					Generate shortcodes for embedding social share buttons
					anywhere on your site.
				</p>

				<div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
					{ /* Options Panel */ }
					<div className="space-y-6">
						<div>
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Networks
							</h3>
							<div className="grid grid-cols-2 gap-2">
								{ availableNetworks.map( ( network ) => (
									<div
										key={ network.id }
										className="flex items-center space-x-2"
									>
										<Checkbox
											checked={ options.networks.includes(
												network.id
											) }
											onChange={ ( checked ) =>
												handleNetworkToggle(
													network.id,
													checked
												)
											}
											label={ network.name }
										/>
									</div>
								) ) }
							</div>
						</div>

						<div className="grid grid-cols-2 gap-4">
							<FormField label="Style">
								<Select
									value={ options.style }
									onChange={ ( value ) =>
										updateOption( 'style', value )
									}
									options={ styleOptions }
								/>
							</FormField>

							<FormField label="Size">
								<Select
									value={ options.size }
									onChange={ ( value ) =>
										updateOption( 'size', value )
									}
									options={ sizeOptions }
								/>
							</FormField>
						</div>

						<div className="space-y-3">
							<Checkbox
								checked={ options.show_labels }
								onChange={ ( checked ) =>
									updateOption( 'show_labels', checked )
								}
								label="Show text labels"
							/>

							<Checkbox
								checked={ options.show_counts }
								onChange={ ( checked ) =>
									updateOption( 'show_counts', checked )
								}
								label="Show share counts"
							/>
						</div>

						<div className="space-y-3">
							<FormField
								label="Custom URL"
								description="Leave empty to use current page URL"
							>
								<TextInput
									value={ options.url }
									onChange={ ( value ) =>
										updateOption( 'url', value )
									}
									placeholder="https://example.com/page"
								/>
							</FormField>

							<FormField
								label="Custom Title"
								description="Leave empty to use current page title"
							>
								<TextInput
									value={ options.title }
									onChange={ ( value ) =>
										updateOption( 'title', value )
									}
									placeholder="Page Title"
								/>
							</FormField>
						</div>

						<div className="grid grid-cols-2 gap-4">
							<FormField label="Alignment">
								<Select
									value={ options.align }
									onChange={ ( value ) =>
										updateOption( 'align', value )
									}
									options={ alignOptions }
								/>
							</FormField>

							<FormField
								label="CSS Class"
								description="Additional CSS classes"
							>
								<TextInput
									value={ options.class }
									onChange={ ( value ) =>
										updateOption( 'class', value )
									}
									placeholder="my-custom-class"
								/>
							</FormField>
						</div>
					</div>

					{ /* Generated Shortcode */ }
					<div className="space-y-4">
						<div>
							<h3 className="text-lg font-medium text-gray-800 mb-3">
								Generated Shortcode
							</h3>
							<div className="bg-gray-50 border border-gray-200 rounded p-4">
								<code className="text-sm font-mono break-all">
									{ generatedShortcode }
								</code>
							</div>
							<Button
								onClick={ copyToClipboard }
								variant="secondary"
								className="mt-3 w-full"
							>
								Copy to Clipboard
							</Button>
						</div>

						{ /* Usage Instructions */ }
						<div className="bg-blue-50 border border-blue-200 rounded p-4">
							<h4 className="font-medium text-blue-800 mb-2">
								How to Use
							</h4>
							<ol className="text-sm text-blue-700 space-y-1 list-decimal list-inside">
								<li>Copy the generated shortcode above</li>
								<li>
									Paste it into any post, page, or text widget
								</li>
								<li>
									The share buttons will appear with your
									selected options
								</li>
							</ol>
						</div>

						{ /* Preview */ }
						<div className="bg-green-50 border border-green-200 rounded p-4">
							<h4 className="font-medium text-green-800 mb-2">
								Preview
							</h4>
							<p className="text-sm text-green-700 mb-3">
								This is how your buttons will look (approximate
								styling):
							</p>
							<div className="flex space-x-2">
								{ options.networks
									.slice( 0, 3 )
									.map( ( networkId ) => {
										const network = availableNetworks.find(
											( n ) => n.id === networkId
										);
										return (
											<div
												key={ networkId }
												className="w-8 h-8 bg-blue-500 rounded flex items-center justify-center text-white text-xs"
												title={ network?.name }
											>
												{ network?.name.charAt( 0 ) }
											</div>
										);
									} ) }
								{ options.networks.length > 3 && (
									<div className="w-8 h-8 bg-gray-400 rounded flex items-center justify-center text-white text-xs">
										+{ options.networks.length - 3 }
									</div>
								) }
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
};
