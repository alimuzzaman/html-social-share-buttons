export function normalizeProfileLinks(raw) {
	var normalized = {};
	Object.keys(raw || {}).forEach(function (networkId) {
		normalized[networkId] = typeof raw[networkId] === 'string' ? raw[networkId] : '';
	});
	return normalized;
}

export function renderProfileLinksSection(dependencies) {
	var e = dependencies.createElement;
	var TextControl = dependencies.TextControl;
	var SectionHeader = dependencies.SectionHeader;
	var values = dependencies.values || {};
	var activeIcons = dependencies.activeIcons || [];
	var networks = dependencies.networks || [];
	var activeById = {};
	var text = dependencies.text;

	activeIcons.forEach(function (icon) {
		activeById[icon.id] = icon;
	});

	return e('section', { key: 'profiles', className: dependencies.sectionClassName }, [
		e(SectionHeader, {
			key: 'section-header',
				title: text('socialProfileLinks', 'Social profile links'),
				description: text('profileLinksDescription', 'Add direct profile or contact destinations beside the share buttons. Leave a field empty to hide it.')
		}),
		e('div', { key: 'profile-grid', className: 'hssb-profile-link-grid' }, networks.map(function (network) {
			var activeIcon = activeById[network.id] || network;
			var preview = dependencies.getIconPreview(activeIcon, dependencies.previewType);
			var isMail = network.id === 'mail';
			return e('div', { key: network.id, className: 'hssb-profile-link-item' }, [
				e('div', { key: 'identity', className: 'hssb-profile-link-identity' }, [
					preview ? e('img', { key: 'icon', src: preview, alt: '' }) : null,
					e('span', { key: 'name' }, network.name)
				]),
				e(TextControl, {
					key: 'field',
					id: 'profile_link_' + network.id,
					label: isMail ? text('emailDestination', 'Email destination') : text('profileUrl', '%s profile URL').replace('%s', network.name),
					name: dependencies.fieldName(network.id),
					type: 'url',
					value: values[network.id] || '',
					placeholder: isMail ? 'mailto:hello@example.com' : 'https://',
					help: isMail ? text('emailDestinationHelp', 'Use one mailto: address without subject or body parameters.') : text('httpsLinksOnly', 'HTTPS links only.'),
					onChange: function (value) {
						dependencies.onChange(network.id, value);
					},
					__next40pxDefaultSize: true,
					__nextHasNoMarginBottom: true
				})
			]);
		}))
	]);
}
