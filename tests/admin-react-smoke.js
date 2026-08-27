#!/usr/bin/env node
const fs = require('fs');
const vm = require('vm');

const scriptPath = 'build/admin-react.js';
const code = fs.readFileSync(scriptPath, 'utf8');
function sourceFilesIn(directory) {
	return fs
		.readdirSync(directory, { recursive: true })
		.filter((file) => file.endsWith('.js'))
		.sort()
		.map((file) => `${directory}/${file}`);
}

const sourceFiles = sourceFilesIn('src/js/admin');
const sourceCode = sourceFiles.map((file) => fs.readFileSync(file, 'utf8')).join('\n');
const adminCss = fs.readFileSync('assets/admin.css', 'utf8');
const settingsPageCode = fs.readFileSync(
	'src/Presentation/Admin/SettingsPageController.php',
	'utf8'
);
const settingsImplementationCode = [
	settingsPageCode,
	...fs
		.readdirSync('src/Presentation/Admin')
		.filter((file) => file.endsWith('.php'))
		.sort()
		.map((file) => fs.readFileSync(`src/Presentation/Admin/${file}`, 'utf8')),
].join('\n');
const settingsSchema = JSON.parse(fs.readFileSync('tests/fixtures/settings-schema-baseline.json', 'utf8'));
const schemaIconIds = Object.keys(settingsSchema.default_options.icons);
const enabledSchemaIconIds = schemaIconIds.filter((id) => id !== 'telegram');
const roots = {
	'zmsh-react-settings-root': {},
};
const animationFrames = [];

function jqueryMock(arg) {
	if (typeof arg === 'function') {
		arg();
	}

	return {
		css() {
			return this;
		},
		ready(callback) {
			callback();
			return this;
		},
	};
}

jqueryMock.extend = (target, ...sources) => Object.assign(target, ...sources);
jqueryMock.inArray = (value, values) => values.indexOf(value);

const context = {
	console,
	jQuery: jqueryMock,
	window: {
	requestAnimationFrame(callback) {
		animationFrames.push(callback);
		return animationFrames.length;
	},
	setTimeout(callback) {
		callback();
		return 1;
	},
	clearTimeout() {},
	},
	document: {
		getElementById(id) {
			return roots[id] || null;
		},
	},
};

context.window.zm_sh_react_settings = {
	defaultIconset: 'bootstrap-solid',
	button_appearances: [
		{ value: 'legacy', label: 'Legacy (current)', help: 'Keep current presentation.' },
		{ value: 'minimal', label: 'Minimal (recommended)', help: 'Use consistent targets.' },
		{ value: 'framed', label: 'Framed', help: 'Add an outline.' },
		{ value: 'soft-shadow', label: 'Soft shadow', help: 'Add a shadow.' },
	],
	assets_img: '/assets/image/',
	iconsets: [
		{
			id: 'bootstrap-solid',
			name: 'Bootstrap Solid',
			preview_img: '/preview.png',
			types: ['square', 'circle'],
			icons: schemaIconIds.map((id) => ({
				id,
				name: id,
				preview_url: `/${id}.png`,
			})),
		},
	],
	options: Object.assign({}, settingsSchema.default_options, {
		button_appearance: 'minimal',
		excludes: '42,43,44',
		show_in: {
			show_left: 'square',
			show_right: false,
			show_before_post: false,
			show_after_post: true,
		},
		show_left: 'square',
		show_right: 'circle',
		show_before_post: 'square',
		show_after_post: 'circle',
		icons: Object.fromEntries(
			schemaIconIds.map((id) => [id, id === 'telegram' ? 0 : 1])
		),
		share_templates: settingsSchema.share_template_defaults,
		profile_links: {
			facebook: 'https://www.facebook.com/example',
			x: 'https://x.com/example',
		},
		g_analytics: 0,
		auto_hide_btn: 0,
		use_port: 0,
		nofollow: 0,
		show_for_current_user: 0,
		show_for_logged_in_user: 1,
		show_for_logged_out_user: 1,
	}),
	share_template_defaults: settingsSchema.share_template_defaults,
	share_template_overrides: {
		facebook: 'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
		x: 'https://example.com/custom?url=%%permalink%%',
		telegram: 'https://t.me/share/url?url=%%permalink%%',
	},
	exclude_items: [
		{ id: '42', token: '#42 - About (page)' },
		{ id: '43', token: '#43 - Contact (page)' },
		{ id: '44', token: '#44 - Sample page (page)' },
	],
	exclude_custom: ['aefs'],
};

let appInstance = null;

const element = {
	createElement(type, props, ...children) {
		if (typeof type === 'function' && type.prototype && typeof type.prototype.render === 'function') {
			const instance = new type(props || {});
			if (typeof instance.handleSubmit === 'function' && typeof instance.openModal === 'function') {
				appInstance = instance;
			}
			return instance.render();
		}

		if (typeof type === 'function') {
			return type(Object.assign({}, props, { children }));
		}

		return {
			type,
			props: props || {},
			children,
		};
	},
	useState(initialValue) {
		return [initialValue, () => {}];
	},
	useEffect(callback) {
		callback();
	},
	useMemo(callback) {
		return callback();
	},
	render(tree, root) {
		root.tree = tree;
	},
};

element.Component = function Component(props) {
	this.props = props || {};
};

element.Component.prototype.setState = function setState(update) {
	const next = typeof update === 'function' ? update(this.state || {}, this.props || {}) : update;
	this.state = Object.assign({}, this.state || {}, next || {});
};

function componentContainer(props) {
	return element.createElement('div', props, props.children);
}

function componentSelect(props) {
	return element.createElement('select', props, props.children);
}

function componentText(props) {
	return element.createElement('input', props);
}

function componentTokenField(props) {
	return element.createElement('div', Object.assign({}, props, { className: 'mock-form-token-field' }), props.children);
}

function componentToggle(props) {
	return element.createElement('input', Object.assign({}, props, { type: 'checkbox' }));
}

context.window.wp = {
	element,
	components: {
		Card: componentContainer,
		CardBody: componentContainer,
		Button: componentContainer,
		FormTokenField: componentTokenField,
		SelectControl: componentSelect,
		Snackbar: componentContainer,
		TextControl: componentText,
		ToggleControl: componentToggle,
	},
};
context.wp = context.window.wp;

vm.createContext(context);
vm.runInContext(code, context, { filename: scriptPath });

const initialTree = roots['zmsh-react-settings-root'].tree;
if (!initialTree || !initialTree.props || !String(initialTree.props.className).includes('zm_settings_loader--react')) {
	throw new Error('React settings loader did not render before the app.');
}

if (!settingsPageCode.includes('zm_settings_loader--html')) {
	throw new Error('Server-rendered settings loader is missing.');
}

if (animationFrames.length !== 1) {
	throw new Error('React app mount was not scheduled after the loader.');
}

animationFrames.shift()();

if (!roots['zmsh-react-settings-root'].tree) {
	throw new Error('React settings root did not mount.');
}

function collectNodes(tree) {
	const items = [];
	function walk(node) {
		if (!node || typeof node !== 'object') {
			return;
		}

		items.push(node);
		(node.children || []).flat(Infinity).forEach(walk);
	}
	walk(tree);
	return items;
}

function collectText(tree) {
	if (typeof tree === 'string' || typeof tree === 'number') {
		return String(tree);
	}
	if (Array.isArray(tree)) {
		return tree.map(collectText).join('');
	}
	if (!tree || typeof tree !== 'object') {
		return '';
	}
	return collectText(tree.children || []);
}

const nodes = collectNodes(roots['zmsh-react-settings-root'].tree);
if (!nodes.some((node) => node.props && node.props.className === 'zm_settings_top_grid')) {
	throw new Error('Responsive settings top grid did not render.');
}
const names = new Set();
nodes.forEach((node) => {
	if (!node || typeof node !== 'object') {
		return;
	}

	if (node.props && node.props.name) {
		names.add(node.props.name);
	}
});

const phpGenerator = nodes.find((node) => node.props && String(node.props.className).includes('get_phpcode'));
const shortcodeGenerator = nodes.find((node) => node.props && String(node.props.className).includes('get_shortcode'));
const codeModal = nodes.find((node) => node.props && node.props.id === 'zm-sh-thick-box');
const modalTitle = nodes.find((node) => node.props && node.props.id === 'zm-sh-code-modal-title');
const modalClose = nodes.find((node) => node.props && node.props.className === 'close');
if (!phpGenerator || phpGenerator.type !== 'button' || phpGenerator.props.type !== 'button' || !shortcodeGenerator || shortcodeGenerator.type !== 'button' || !codeModal || codeModal.props.role !== 'dialog' || codeModal.props['aria-modal'] !== 'true' || codeModal.props['aria-labelledby'] !== 'zm-sh-code-modal-title' || !modalTitle || !modalClose || modalClose.children.join('') !== 'Close') {
	throw new Error('Code generator modal controls should use accessible buttons and dialog semantics.');
}
if (!sourceCode.includes('this.modalTrigger = trigger || null') || !sourceCode.includes('this.modalTrigger.focus()') || !sourceCode.includes('this.modalCloseButton.focus()') || !sourceCode.includes('handleModalKeyDown') || !sourceCode.includes("event.key === 'Escape'")) {
	throw new Error('Code generator modal should restore focus to its trigger.');
}

const requiredNames = settingsSchema.field_names;

const missing = requiredNames.filter((name) => !names.has(name));
if (missing.length > 0) {
	throw new Error(`Missing legacy field names: ${missing.join(', ')}`);
}

const appearanceField = nodes.find(
	(node) => node.props && node.props.name === 'zm_shbt_fld[button_appearance]'
);
const appearancePreview = nodes.find(
	(node) => node.props && String(node.props.className || '').includes('hssb-appearance--minimal')
);
if (!appearanceField || appearanceField.props.value !== 'minimal' || appearanceField.props.options.length !== 4 || !appearancePreview) {
	throw new Error('Button appearance control and shared-CSS live preview did not render.');
}
appearanceField.props.onChange('framed');
if (appInstance.state.options.button_appearance !== 'framed') {
	throw new Error('Button appearance changes should update canonical settings state.');
}

const audienceKeys = [
	'show_for_current_user',
	'show_for_logged_in_user',
	'show_for_logged_out_user',
];
audienceKeys.forEach((key) => {
	const audienceFields = nodes.filter(
		(node) =>
			node.props &&
			node.props.name === `zm_shbt_fld[${key}]`
	);
	const hidden = audienceFields.find((node) => node.props.type === 'hidden');
	const toggle = audienceFields.find((node) => node.props.type === 'checkbox');
	if (!hidden || hidden.props.value !== '0' || !toggle) {
		throw new Error(`Audience ${key} must serialize an explicit true or false value.`);
	}
});
const currentUserToggle = nodes.find(
	(node) =>
		node.props &&
		node.props.name === 'zm_shbt_fld[show_for_current_user]' &&
		node.props.type === 'checkbox'
);
if (!currentUserToggle || currentUserToggle.props.checked !== false) {
	throw new Error('Current-user audience setting did not retain its saved false value.');
}
currentUserToggle.props.onChange(true);
if (appInstance.state.options.show_for_current_user !== true) {
	throw new Error('Audience toggles should update canonical settings state.');
}

const profileFields = nodes.filter((node) => node.props && String(node.props.name || '').indexOf('zm_shbt_fld[profile_links][') === 0);
const facebookProfile = profileFields.find((node) => node.props.name === 'zm_shbt_fld[profile_links][facebook]');
const xProfile = profileFields.find((node) => node.props.name === 'zm_shbt_fld[profile_links][x]');
const mailProfile = profileFields.find((node) => node.props.name === 'zm_shbt_fld[profile_links][mail]');
if (profileFields.length !== schemaIconIds.length || !facebookProfile || facebookProfile.props.value !== 'https://www.facebook.com/example' || !xProfile || xProfile.props.value !== 'https://x.com/example' || !mailProfile || mailProfile.props.placeholder !== 'mailto:hello@example.com') {
	throw new Error('Profile-link settings should render one independent destination for every canonical network.');
}
facebookProfile.props.onChange('https://www.facebook.com/updated');
if (appInstance.state.options.profile_links.facebook !== 'https://www.facebook.com/updated') {
	throw new Error('Profile-link edits should update the canonical nested settings state.');
}

const placementProfileFields = nodes.filter((node) => node.props && String(node.props.name || '').indexOf('zm_shbt_fld[profile_link_placements][') === 0);
const leftPlacementProfiles = placementProfileFields.filter((node) => node.props.name === 'zm_shbt_fld[profile_link_placements][show_left]');
if (placementProfileFields.length !== 4 || leftPlacementProfiles.length !== 1 || leftPlacementProfiles[0].props.value !== 'inherit') {
	throw new Error('Each automatic placement should expose an additive profile-link inheritance control.');
}
leftPlacementProfiles[0].props.onChange('none');
if (appInstance.state.options.profile_link_placements.show_left !== 'none') {
	throw new Error('Placement profile-link controls should update their isolated nested setting.');
}

const profileGridCss = adminCss.match(/\.hssb-profile-link-grid\s*\{[^}]*\}/);
if (!profileGridCss || !profileGridCss[0].includes('grid-template-columns: repeat(2, minmax(0, 1fr))') || !adminCss.includes('@media screen and (max-width: 600px)')) {
	throw new Error('Profile-link settings should use a responsive two-column layout.');
}

const facebookTemplate = nodes.find((node) => node.props && node.props.id === 'share_template_facebook_0');
const xTemplate = nodes.find((node) => node.props && node.props.id === 'share_template_x_0');
const networkColumns = nodes.filter((node) => node.props && node.props.className === 'zm_network_column');
const facebookPrefix = nodes.find((node) => node.props && node.props.className === 'zm_template_prefix' && node.children && node.children[0].includes('facebook.com'));
const facebookSerializedTemplate = nodes.find((node) => node.props && node.props.name === 'zm_shbt_fld[share_templates][facebook]');
const xInitialSerializedTemplate = nodes.find((node) => node.props && node.props.name === 'zm_shbt_fld[share_templates][x]');
const telegramSerializedTemplate = nodes.find((node) => node.props && node.props.name === 'zm_shbt_fld[share_templates][telegram]');
const facebookPlaceholder = nodes.find((node) => node.props && node.props.className === 'zm_template_placeholder' && node.children && node.children[0] === '%%permalink%%');
if (!facebookTemplate || facebookTemplate.props.className !== 'zm_template_parameter_editor' || !facebookTemplate.props.contentEditable || !facebookPrefix || !facebookPlaceholder || !facebookSerializedTemplate || facebookSerializedTemplate.props.value !== '' || networkColumns.length !== 2) {
	throw new Error('Canonical templates should show a fixed URL and separately editable parameter values.');
}

if (!xTemplate || xTemplate.props.className !== 'zm_template_parameter_editor' || xTemplate.props.role !== 'combobox' || xTemplate.props['aria-autocomplete'] !== 'list' || xTemplate.props['aria-haspopup'] !== 'listbox') {
	throw new Error('Saved platform template override did not render.');
}
if (!xInitialSerializedTemplate || xInitialSerializedTemplate.props.value !== 'https://example.com/custom?url=%%permalink%%') {
	throw new Error('Hidden templates should retain the exact saved full URL.');
}
if (!telegramSerializedTemplate || telegramSerializedTemplate.props.value !== 'https://t.me/share/url?url=%%permalink%%') {
	throw new Error('Collapsed network panels should preserve saved template overrides.');
}

if (nodes.some((node) => node.props && node.props.className === 'zm_template_placeholders') || sourceCode.includes('Insert into selected value') || sourceCode.includes('zm_template_parameter_value')) {
	throw new Error('Retired placeholder insertion controls should not render.');
}

const parameterListCss = adminCss.match(/\.zm_template_parameter_list\s*\{[^}]*\}/);
if (!parameterListCss || !parameterListCss[0].includes('border: 1px solid #c3c4c7') || !adminCss.includes('.zm_template_parameter_list:focus-within') || !adminCss.includes('var(--zmsh-accent)')) {
	throw new Error('Template parameter editor should keep a neutral resting border and scheme-aware active state.');
}

const prefixCss = adminCss.match(/\.zm_template_prefix\s*\{[^}]*\}/);
if (!prefixCss || !prefixCss[0].includes('background: #f0f0f1') || !prefixCss[0].includes('border-left: 3px solid #c3c4c7') || prefixCss[0].includes('border: 1px solid')) {
	throw new Error('Share URL prefix should render as static code context, not as an input-like control.');
}

const networkColumnsCss = adminCss.match(/\.zm_network_columns\s*\{[^}]*\}/);
const networkColumnCss = adminCss.match(/\.zm_network_column\s*\{[^}]*\}/);
if (!networkColumnsCss || !networkColumnsCss[0].includes('grid-template-columns: repeat(2, minmax(0, 1fr))') || !networkColumnCss || !networkColumnCss[0].includes('display: flex') || !networkColumnCss[0].includes('flex-direction: column')) {
	throw new Error('Social networks should use independent desktop column stacks.');
}

const networkTemplateCss = adminCss.match(/\.zm_network_template\s*\{[^}]*\}/);
const sharedPanelToggleCss = adminCss.match(/\.zm_panel_toggle\s*\{[^}]*\}/);
const sharedPanelIdentityCss = adminCss.match(/\.zm_panel_identity\s*\{[^}]*\}/);
if (!networkTemplateCss || !sharedPanelToggleCss || !sharedPanelToggleCss[0].includes('min-height: 74px') || !sharedPanelIdentityCss) {
	throw new Error('Placement and social network cards should use the same identity header anatomy.');
}

const expandablePanelCss = adminCss.match(/\.zm_expandable_toggle_panel_details\s*\{[^}]*\}/);
if (!expandablePanelCss || !expandablePanelCss[0].includes('border: 1px solid var(--zmsh-inner-border)') || !expandablePanelCss[0].includes('border-top: 0') || !adminCss.includes('.zm_native_toggle.zm_panel_toggle') || adminCss.includes('background: #f7fbff')) {
	throw new Error('Enabled placements should use the shared joined inner-border detail panel, not a tinted selected card.');
}

const expandablePanelUses = sourceCode.match(/e\(ExpandableTogglePanel/g) || [];
if (expandablePanelUses.length < 2 || !adminCss.includes('.zm_expandable_toggle_panel.is-enabled > .zm_native_toggle')) {
	throw new Error('Placement and social network items should share the expandable toggle-panel component.');
}

if (!settingsImplementationCode.includes("'preview_url'") || !settingsImplementationCode.includes("'preview_urls'") || !sourceCode.includes('function getIconPreview') || !sourceCode.includes('networkPreviewType') || !nodes.some((node) => node.props && node.props.className === 'zm_panel_marker zm_network_marker')) {
	throw new Error('Social network card headers should expose the active icon-set marker.');
}

if (!settingsImplementationCode.includes("strlen( $query ) < 2") || !/'posts_per_page'\s*=>\s*20/.test(settingsImplementationCode) || !/'no_found_rows'\s*=>\s*true/.test(settingsImplementationCode) || !sourceCode.includes("trim().length < 2")) {
	throw new Error('Exclude content search should reject short queries and keep the server query bounded.');
}

if (!sourceCode.includes('excludeSearchRequest') || !sourceCode.includes('excludeSearchRequest.abort') || !sourceCode.includes('request !== self.excludeSearchRequest')) {
	throw new Error('Exclude content search should prevent stale responses from replacing newer suggestions.');
}

const settingsSectionCss = adminCss.match(/\.zm_settings_section\s*\{[^}]*\}/);
if (!settingsSectionCss || !settingsSectionCss[0].includes('border-left: 3px solid var(--zmsh-accent-light)')) {
	throw new Error('Top-level settings sections should use the scheme-aware accent border.');
}

if (!sourceCode.includes("className: 'zm_network_columns zm_placement_columns'") || !sourceCode.includes('var placementColumns = [')) {
	throw new Error('Placement cards should use explicit independent columns so collapsed cards do not inherit a neighboring card height.');
}

xTemplate.props.onFocus();
appInstance.getTemplateSelection = function () { return { start: 0, end: 0 }; };
appInstance.insertSharePlaceholder('x', '%%imageurl%%');
if (appInstance.state.options.share_templates.x !== 'https://example.com/custom?url=%%imageurl%%%%permalink%%') {
	throw new Error('Template placeholder insertion did not preserve the selection offset.');
}

appInstance.getTemplateSelection = function () { return null; };
const editorAfterInsertion = collectNodes(appInstance.render()).find((node) => node.props && node.props.id === 'share_template_x_0');
const plainTextValue = 'ordinary text ';
appInstance.getTemplateSelection = function () { return { start: plainTextValue.length, end: plainTextValue.length }; };
editorAfterInsertion.props.onInput({ currentTarget: { textContent: plainTextValue } });
if (appInstance.state.templateAutocomplete) {
	throw new Error('Ordinary spaces should not open the placeholder listbox.');
}

const triggerValue = 'ordinary text %%';
appInstance.getTemplateSelection = function () { return { start: triggerValue.length, end: triggerValue.length }; };
editorAfterInsertion.props.onInput({ currentTarget: { textContent: triggerValue } });
if (!appInstance.state.templateAutocomplete) {
	throw new Error('Typing %% should open the placeholder listbox at the caret.');
}
const triggerEditor = collectNodes(appInstance.render()).find((node) => node.props && node.props.id === 'share_template_x_0');
if (!triggerEditor || triggerEditor.props.key !== editorAfterInsertion.props.key) {
	throw new Error('Normal typing should not remount the contenteditable editor.');
}

let compositionPrevented = false;
editorAfterInsertion.props.onKeyDown({
	key: 'Enter',
	isComposing: true,
	currentTarget: {},
	preventDefault() { compositionPrevented = true; },
});
if (compositionPrevented || !appInstance.state.templateAutocomplete || appInstance.state.options.share_templates.x !== 'https://example.com/custom?url=ordinary text %%') {
	throw new Error('IME composition Enter should not select an autocomplete option.');
}

editorAfterInsertion.props.onKeyDown({
	key: 'Enter',
	currentTarget: {},
	preventDefault() {},
});
if (appInstance.state.templateAutocomplete || appInstance.state.options.share_templates.x !== 'https://example.com/custom?url=ordinary text %%title%%') {
	throw new Error('Selecting a typed %% suggestion should replace the trigger characters exactly once.');
}
const normalizedEditorNodes = collectNodes(appInstance.render());
const normalizedEditor = normalizedEditorNodes.find((node) => node.props && node.props.id === 'share_template_x_0');
const normalizedPlaceholder = normalizedEditorNodes.find((node) => node.props && node.props.className === 'zm_template_placeholder' && node.children && node.children[0] === '%%title%%');
if (!normalizedEditor || normalizedEditor.props.key === triggerEditor.props.key || collectText(normalizedEditor) !== 'ordinary text %%title%%' || !normalizedPlaceholder) {
	throw new Error('Autocomplete insertion should remount the editor from safe styled state children.');
}

let prevented = false;
editorAfterInsertion.props.onKeyDown({
	ctrlKey: true,
	key: ' ',
	currentTarget: {},
	preventDefault() { prevented = true; },
});
if (!prevented || !appInstance.state.templateAutocomplete) {
	throw new Error('Ctrl+Space should open the placeholder listbox without inserting text.');
}

editorAfterInsertion.props.onKeyDown({
	key: 'Escape',
	currentTarget: {},
	preventDefault() {},
});
if (appInstance.state.templateAutocomplete) {
	throw new Error('Escape should close the placeholder listbox.');
}

editorAfterInsertion.props.onKeyDown({
	ctrlKey: true,
	key: ' ',
	currentTarget: {},
	preventDefault() {},
});

let autocompleteNodes = collectNodes(appInstance.render());
const listbox = autocompleteNodes.find((node) => node.props && node.props.role === 'listbox');
if (!listbox || listbox.props['aria-label'] !== 'Insert share parameter placeholder') {
	throw new Error('Placeholder suggestions should render as an accessible listbox.');
}
const activeEditor = autocompleteNodes.find((node) => node.props && node.props.id === 'share_template_x_0');
if (!activeEditor || activeEditor.props['aria-controls'] !== listbox.props.id || activeEditor.props['aria-expanded'] !== true) {
	throw new Error('The active editor should expose its combobox/listbox relationship.');
}

editorAfterInsertion.props.onKeyDown({
	key: 'ArrowDown',
	currentTarget: {},
	preventDefault() {},
});
if (appInstance.state.templateAutocomplete.selectedIndex !== 1) {
	throw new Error('ArrowDown should change the active placeholder suggestion.');
}

editorAfterInsertion.props.onKeyDown({
	key: 'ArrowUp',
	currentTarget: {},
	preventDefault() {},
});
if (appInstance.state.templateAutocomplete.selectedIndex !== 0) {
	throw new Error('ArrowUp should change the active placeholder suggestion.');
}

editorAfterInsertion.props.onKeyDown({
	key: 'ArrowDown',
	currentTarget: {},
	preventDefault() {},
});

appInstance.getTemplateSelection = function () {
	const value = appInstance.getShareTemplateParameters('x')[0].value;
	return { start: value.length, end: value.length };
};
editorAfterInsertion.props.onKeyDown({
	key: 'Enter',
	currentTarget: {},
	preventDefault() {},
});
if (appInstance.state.templateAutocomplete || !appInstance.state.options.share_templates.x.endsWith('%%permalink%%')) {
	throw new Error('Enter should insert the selected placeholder and close the listbox.');
}

const updatedTemplateNodes = collectNodes(appInstance.render());
const xSerializedTemplate = updatedTemplateNodes.find((node) => node.props && node.props.name === 'zm_shbt_fld[share_templates][x]');
if (!xSerializedTemplate || xSerializedTemplate.props.value !== appInstance.state.options.share_templates.x) {
	throw new Error('Template form serialization did not preserve the full URL contract.');
}

const excludeTokenField = nodes.find((node) => node.props && node.props.className === 'mock-form-token-field');
if (!excludeTokenField || excludeTokenField.props.value.join('|') !== '#42 - About (page)|#43 - Contact (page)|#44 - Sample page (page)|aefs') {
	throw new Error('Exclude CSV value did not render as WordPress tokens.');
}

const legacyPlacement = nodes.find((node) => node.props && node.props.name === 'zm_shbt_fld[show_in][show_left]');
if (!legacyPlacement || legacyPlacement.props.checked !== true) {
	throw new Error('Legacy saved placement shape did not normalize to enabled.');
}

const beforePostShape = nodes.find((node) => node.type === 'select' && node.props && node.props.name === 'zm_shbt_fld[show_before_post]');
if (beforePostShape) {
	throw new Error('Disabled placement controls should remain collapsed.');
}

legacyPlacement.props.onChange(false);
const collapsedPlacementNodes = collectNodes(appInstance.render());
if (collapsedPlacementNodes.find((node) => node.type === 'select' && node.props && node.props.name === 'zm_shbt_fld[show_left]')) {
	throw new Error('Disabling a placement should collapse its button shape control.');
}

legacyPlacement.props.onChange(true);
const expandedPlacementNodes = collectNodes(appInstance.render());
if (!expandedPlacementNodes.find((node) => node.type === 'select' && node.props && node.props.name === 'zm_shbt_fld[show_left]')) {
	throw new Error('Enabling a placement should reveal its existing button shape control.');
}

excludeTokenField.props.onChange(['#42 - About (page)', '#44 - Sample page (page)']);
const excludeNodes = collectNodes(appInstance.render());
const excludeHiddenInput = excludeNodes.find((node) => node.props && node.props.name === 'zm_shbt_fld[excludes]');
if (!excludeHiddenInput || excludeHiddenInput.props.value !== '42,44') {
	throw new Error('Exclude tokens did not preserve the comma-separated saving format.');
}

const shortcodeTextarea = nodes.find((node) => node.type === 'textarea' && node.props && node.props.id === 'copy_shortcode');
if (!shortcodeTextarea) {
	throw new Error('Code generator textarea was not rendered.');
}

const expectedShortcode = `[zm_sh_btn iconset='bootstrap-solid' iconset_type='square' icons='${enabledSchemaIconIds.join(',')}']`;
if (shortcodeTextarea.props.value !== expectedShortcode) {
	throw new Error(`Unexpected shortcode output: ${shortcodeTextarea.props.value}`);
}

if (!sourceCode.includes("$options['icons']\\t\\t\\t= array( '")) {
	throw new Error('PHP code generator no longer matches the legacy icons assignment spacing.');
}

if (collectText(phpGenerator) !== '<\\?> Get PHP Code' || collectText(shortcodeGenerator) !== '[] Get Shortcode') {
	throw new Error('Code generator action labels were not rendered.');
}

const modal = nodes.find((node) => node.type === 'div' && node.props && node.props.id === 'zm-sh-thick-box');
if (!modal) {
	throw new Error('Legacy modal id #zm-sh-thick-box was not rendered.');
}

const phpGeneratorAction = nodes.find((node) => node.type === 'button' && node.props && String(node.props.className).includes('get_phpcode'));
if (!phpGeneratorAction || typeof phpGeneratorAction.props.onClick !== 'function') {
	throw new Error('PHP code generator click handler was not rendered.');
}

phpGeneratorAction.props.onClick({ currentTarget: { focus() {} } });
if (!appInstance) {
	throw new Error('React settings class instance was not captured.');
}
const rerenderedTree = appInstance.render();
const rerenderedNodes = collectNodes(rerenderedTree);
const phpTextarea = rerenderedNodes.find((node) => node.type === 'textarea' && node.props && node.props.id === 'copy_shortcode');
const expectedPhpFragment = `$options['icons']\t\t\t= array( '${enabledSchemaIconIds.join("', '")}' );`;

if (!phpTextarea || !phpTextarea.props.value.includes(expectedPhpFragment)) {
	throw new Error('PHP code generator output did not include the expected legacy icon assignment.');
}

console.log(`Admin React smoke passed: ${requiredNames.length} legacy fields present, shortcode and PHP generators intact.`);
