#!/usr/bin/env node
const fs = require('fs');
const vm = require('vm');

const scriptPath = 'assets/admin-react.js';
const code = fs.readFileSync(scriptPath, 'utf8');
const roots = {
	'zmsh-react-settings-root': {},
};

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
	window: {},
	document: {
		getElementById(id) {
			return roots[id] || null;
		},
	},
};

context.window.zm_sh_react_settings = {
	defaultIconset: 'default',
	assets_img: '/assets/image/',
	iconsets: [
		{
			id: 'default',
			name: 'Default',
			preview_img: '/preview.png',
			types: ['square', 'circle'],
			icons: [
				{ id: 'facebook', name: 'Facebook' },
				{ id: 'x', name: 'X' },
			],
		},
	],
	options: {
		title: 'Share this with your friends',
		iconset: 'default',
		excludes: '',
		show_in: {
			show_left: true,
			show_right: false,
			show_before_post: false,
			show_after_post: true,
		},
		show_left: 'square',
		show_right: 'circle',
		show_before_post: 'square',
		show_after_post: 'circle',
		icons: {
			facebook: 1,
			x: 1,
		},
		g_analytics: 0,
		auto_hide_btn: 0,
		use_port: 0,
		nofollow: 0,
	},
};

let appInstance = null;

const element = {
	createElement(type, props, ...children) {
		if (typeof type === 'function' && type.prototype && typeof type.prototype.render === 'function') {
			const instance = new type(props || {});
			if (type.name === 'App') {
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

context.window.wp = { element };
context.wp = context.window.wp;

vm.createContext(context);
vm.runInContext(code, context, { filename: scriptPath });

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

const nodes = collectNodes(roots['zmsh-react-settings-root'].tree);
const names = new Set();
nodes.forEach((node) => {
	if (!node || typeof node !== 'object') {
		return;
	}

	if (node.props && node.props.name) {
		names.add(node.props.name);
	}
});

const requiredNames = [
	'zm_shbt_fld[title]',
	'zm_shbt_fld[excludes]',
	'zm_shbt_fld[iconset]',
	'zm_shbt_fld[show_in][show_left]',
	'zm_shbt_fld[show_left]',
	'zm_shbt_fld[show_in][show_right]',
	'zm_shbt_fld[show_right]',
	'zm_shbt_fld[show_in][show_before_post]',
	'zm_shbt_fld[show_before_post]',
	'zm_shbt_fld[show_in][show_after_post]',
	'zm_shbt_fld[show_after_post]',
	'zm_shbt_fld[icons][facebook]',
	'zm_shbt_fld[icons][x]',
	'zm_shbt_fld[g_analytics]',
	'zm_shbt_fld[auto_hide_btn]',
	'zm_shbt_fld[use_port]',
	'zm_shbt_fld[nofollow]',
];

const missing = requiredNames.filter((name) => !names.has(name));
if (missing.length > 0) {
	throw new Error(`Missing legacy field names: ${missing.join(', ')}`);
}

const shortcodeTextarea = nodes.find((node) => node.type === 'textarea' && node.props && node.props.id === 'copy_shortcode');
if (!shortcodeTextarea) {
	throw new Error('Code generator textarea was not rendered.');
}

const expectedShortcode = "[zm_sh_btn iconset='default' iconset_type='square' icons='facebook,x']";
if (shortcodeTextarea.props.value !== expectedShortcode) {
	throw new Error(`Unexpected shortcode output: ${shortcodeTextarea.props.value}`);
}

if (!code.includes("$options['icons']\\t\\t\\t= array( '")) {
	throw new Error('PHP code generator no longer matches the legacy icons assignment spacing.');
}

const generatorTitles = new Set(
	nodes
		.filter((node) => node.type === 'a' && node.props && node.props.title)
		.map((node) => node.props.title)
);

if (!generatorTitles.has('<\\?> Get PHP Code') || !generatorTitles.has('[] Get Shortcode')) {
	throw new Error('Code generator action titles were not rendered.');
}

const generatorHrefs = new Set(
	nodes
		.filter((node) => node.type === 'a' && node.props && node.props.href)
		.map((node) => node.props.href)
);

if (!generatorHrefs.has('#zm-sh-thick-box')) {
	throw new Error('Code generator actions no longer point at #zm-sh-thick-box.');
}

const modal = nodes.find((node) => node.type === 'div' && node.props && node.props.id === 'zm-sh-thick-box');
if (!modal) {
	throw new Error('Legacy modal id #zm-sh-thick-box was not rendered.');
}

const phpGeneratorLink = nodes.find((node) => node.type === 'a' && node.props && node.props.className === 'get_phpcode button button-default');
if (!phpGeneratorLink || typeof phpGeneratorLink.props.onClick !== 'function') {
	throw new Error('PHP code generator click handler was not rendered.');
}

phpGeneratorLink.props.onClick({ preventDefault() {} });
if (!appInstance) {
	throw new Error('React settings class instance was not captured.');
}
const rerenderedTree = appInstance.render();
const rerenderedNodes = collectNodes(rerenderedTree);
const phpTextarea = rerenderedNodes.find((node) => node.type === 'textarea' && node.props && node.props.id === 'copy_shortcode');
const expectedPhpFragment = "$options['icons']\t\t\t= array( 'facebook', 'x' );";

if (!phpTextarea || !phpTextarea.props.value.includes(expectedPhpFragment)) {
	throw new Error('PHP code generator output did not include the expected legacy icon assignment.');
}

console.log(`Admin React smoke passed: ${requiredNames.length} legacy fields present, shortcode and PHP generators intact.`);
