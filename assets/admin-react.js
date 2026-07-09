(function (wp, $) {
	'use strict';

	if (!wp || !wp.element || !wp.element.createElement || !wp.element.Component) {
		return;
	}

	var data = window.zm_sh_react_settings || {};
	var iconsets = data.iconsets || [];
	var e = wp.element.createElement;
	var Component = wp.element.Component;
	var defaults = {
		title: 'Share this with your friends',
		iconset: data.defaultIconset || 'default',
		excludes: '',
		show_in: {
			show_left: 0,
			show_right: 0,
			show_before_post: 0,
			show_after_post: 0,
		},
		show_left: 0,
		show_right: 0,
		show_before_post: 0,
		show_after_post: 0,
		icons: {},
		g_analytics: 0,
		auto_hide_btn: 0,
		use_port: 0,
		nofollow: 0,
	};

	function toBoolean(value) {
		return value === true || value === 1 || value === '1';
	}

	function findIconset(id) {
		for (var i = 0; i < iconsets.length; i++) {
			if (iconsets[i].id === id) {
				return iconsets[i];
			}
		}
		return iconsets[0] || null;
	}

	function normalizeOptions(raw) {
		var options = $.extend({}, defaults, raw || {});
		options.show_in = $.extend({}, defaults.show_in, options.show_in || {});
		options.icons = $.extend({}, options.icons || {});
		options.show_in.show_left = toBoolean(options.show_in.show_left);
		options.show_in.show_right = toBoolean(options.show_in.show_right);
		options.show_in.show_before_post = toBoolean(options.show_in.show_before_post);
		options.show_in.show_after_post = toBoolean(options.show_in.show_after_post);
		options.show_left = options.show_left || 0;
		options.show_right = options.show_right || 0;
		options.show_before_post = options.show_before_post || 0;
		options.show_after_post = options.show_after_post || 0;
		options.g_analytics = toBoolean(options.g_analytics);
		options.auto_hide_btn = toBoolean(options.auto_hide_btn);
		options.use_port = toBoolean(options.use_port);
		options.nofollow = toBoolean(options.nofollow);
		options.excludes = options.excludes || '';
		return options;
	}

	function ensureType(iconset, value) {
		if (!iconset || !iconset.types || !iconset.types.length) {
			return 'square';
		}
		if ($.inArray(value, iconset.types) === -1) {
			return iconset.types[0];
		}
		return value;
	}

	function buildIconState(iconset, existing) {
		var nextIcons = {};
		if (!iconset) {
			return nextIcons;
		}
		for (var i = 0; i < (iconset.icons || []).length; i++) {
			var icon = iconset.icons[i];
			nextIcons[icon.id] = toBoolean(existing[icon.id]);
		}
		return nextIcons;
	}

	function normalizeForIconset(options) {
		var iconset = findIconset(options.iconset);
		var next = $.extend({}, options);
		next.show_in = $.extend({}, options.show_in || {});
		next.icons = buildIconState(iconset, options.icons || {});
		next.show_left = ensureType(iconset, next.show_left);
		next.show_right = ensureType(iconset, next.show_right);
		next.show_before_post = ensureType(iconset, next.show_before_post);
		next.show_after_post = ensureType(iconset, next.show_after_post);
		return next;
	}

	function buildCode(state, type) {
		var enabled = [];
		for (var iconId in state.icons) {
			if (state.icons[iconId]) {
				enabled.push(iconId);
			}
		}
		var shortcode = "[zm_sh_btn iconset='" + state.iconset + "' iconset_type='" + type + "' icons='" + enabled.join() + "']";
		var php = "<?php\n if(function_exists('zm_sh_btn')){\n\t";
		php += "$options['iconset']\t\t= '" + state.iconset + "';\n\t";
		php += "$options['iconset_type']\t= '" + type + "';\n\t";
		php += "$options['class']\t\t\t= 'in_php_function';\n\t";
		php += "$options['icons']\t\t\t= array( '" + enabled.join("', '") + "' );\n";
		php += "\techo zm_sh_btn($options);\n}";
		php += "\n?>";
		return {
			shortcode: shortcode,
			php: php,
		};
	}

	function ToggleInput(props) {
		return e('div', { className: 'zm_option_toggle' }, [
			e('input', {
				key: 'input',
				type: 'checkbox',
				id: props.id,
				name: props.name,
				value: '1',
				checked: toBoolean(props.checked),
				onChange: function (event) {
					props.onChange(event.target.checked ? 1 : 0);
				},
				disabled: !!props.disabled,
				'data-id': props.dataId || ''
			}),
			e('label', { key: 'label', htmlFor: props.id, className: 'zm_toggle_label' }, [
				e('span', { key: 'text', className: 'zm_toggle_text' }, props.label),
				e('span', { key: 'control', className: 'for_label' }, e('span', {
					className: 'toggle-check',
					'data-on': props.yes || 'Yes',
					'data-off': props.no || 'No'
				}))
			])
		]);
	}

	function FieldRow(props) {
		return e('div', { className: 'zm_field_row' }, [
			e('label', { key: 'label', htmlFor: props.id }, props.label),
			e('div', { key: 'control', className: 'zm_field_control' }, props.children)
		]);
	}

	function SectionHeader(props) {
		return e('div', { className: 'zm_section_header' }, [
			e('h2', { key: 'title' }, props.title),
			props.description ? e('p', { key: 'description' }, props.description) : null
		]);
	}

	function PlacementInput(props) {
		var iconset = props.iconset || { types: [] };
		var types = iconset.types || [];
		return e('div', { className: 'zm_placement_card' + (toBoolean(props.enabled) ? ' is-enabled' : '') }, [
			e('div', { key: 'header', className: 'zm_placement_card_header' }, [
				e('input', {
					key: 'input',
					type: 'checkbox',
					id: props.id,
					name: 'zm_shbt_fld[show_in][' + props.id + ']',
					value: '1',
					checked: toBoolean(props.enabled),
					onChange: function (event) {
						props.onEnabled(event.target.checked ? 1 : 0);
					}
				}),
				e('label', { key: 'label', htmlFor: props.id, className: 'zm_toggle_label' }, [
					e('span', { key: 'text', className: 'zm_toggle_text' }, props.label),
					e('span', { key: 'control', className: 'for_label' }, e('span', {
						className: 'toggle-check',
						'data-on': 'Yes',
						'data-off': 'No'
					}))
				])
			]),
			e('div', { key: 'types', className: 'zm_type_picker', 'aria-hidden': toBoolean(props.enabled) ? 'false' : 'true' }, types.map(function (type) {
				var radioId = props.id + '-' + type;
				return e('span', { key: type, className: 'zm_type_option' }, [
					e('input', {
						key: 'input',
						type: 'radio',
						id: radioId,
						name: 'zm_shbt_fld[' + props.id + ']',
						value: type,
						checked: props.type === type,
						onChange: function (event) {
							props.onType(event.target.value);
						}
					}),
					e('label', { key: 'label', htmlFor: radioId }, [
						e('img', {
							key: 'image',
							src: data.assets_img + 'show_after_post-' + type + '.png',
							alt: type
						}),
						e('span', { key: 'name' }, type)
					])
				]);
			}))
		]);
	}

	function CheckboxInput(props) {
		return e(ToggleInput, {
			id: props.id,
			label: props.label,
			name: props.name,
			checked: props.checked,
			onChange: props.onChange,
			dataId: props.dataId
		});
	}

	function App(props) {
		Component.call(this, props);
		this.state = {
			options: normalizeForIconset(normalizeOptions(data.options || {})),
			modalOpen: false,
			modalMode: 'shortcode',
			modalType: 'square',
		};
	}

	App.prototype = Object.create(Component.prototype);
	App.prototype.constructor = App;

	App.prototype.componentWillUnmount = function () {
		this.setBodyLock(false);
	};

	App.prototype.setBodyLock = function (locked) {
		$('body').css(locked ? { overflow: 'hidden', height: '100%' } : { overflow: 'initial', height: 'initial' });
	};

	App.prototype.update = function (path, value) {
		this.setState(function (prev) {
			var nextOptions = $.extend({}, prev.options);
			nextOptions.show_in = $.extend({}, prev.options.show_in || {});
			nextOptions.icons = $.extend({}, prev.options.icons || {});
			switch (path) {
				case 'title':
					nextOptions.title = value;
					break;
				case 'excludes':
					nextOptions.excludes = value;
					break;
				case 'iconset':
					nextOptions.iconset = value;
					nextOptions = normalizeForIconset(nextOptions);
					break;
				case 'show_left':
				case 'show_right':
				case 'show_before_post':
				case 'show_after_post':
					nextOptions[path] = value;
					break;
				case 'show_in.show_left':
					nextOptions.show_in.show_left = toBoolean(value);
					break;
				case 'show_in.show_right':
					nextOptions.show_in.show_right = toBoolean(value);
					break;
				case 'show_in.show_before_post':
					nextOptions.show_in.show_before_post = toBoolean(value);
					break;
				case 'show_in.show_after_post':
					nextOptions.show_in.show_after_post = toBoolean(value);
					break;
				case 'g_analytics':
				case 'auto_hide_btn':
				case 'use_port':
				case 'nofollow':
					nextOptions[path] = toBoolean(value);
					break;
				default:
					if (path.indexOf('icon_') === 0) {
						nextOptions.icons[path.substring(5)] = toBoolean(value);
					}
			}
			return { options: nextOptions };
		});
	};

	App.prototype.openModal = function (mode) {
		var options = this.state.options;
		var currentIconset = findIconset(options.iconset);
		var modalType = ensureType(currentIconset, this.state.modalType || options.show_left || 'square');
		this.setBodyLock(true);
		this.setState({
			modalOpen: true,
			modalMode: mode,
			modalType: modalType,
		});
	};

	App.prototype.closeModal = function () {
		this.setBodyLock(false);
		this.setState({ modalOpen: false });
	};

	App.prototype.render = function () {
		var self = this;
		var options = this.state.options;
		var currentIconset = findIconset(options.iconset);
		var generated = buildCode(options, this.state.modalType);
		var modalOutput = this.state.modalMode === 'php' ? generated.php : generated.shortcode;
		var modalTitle = this.state.modalMode === 'php' ? '<\\?> Get PHP Code' : '[] Get Shortcode';

		return e('div', { className: 'zm_settings_shell' }, [
			e('section', { key: 'header', className: 'zm_settings_section zm_settings_section--intro' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Header',
					description: 'Set the text shown with the share buttons and choose pages where buttons should stay hidden.'
				}),
				e(FieldRow, { key: 'title-field', id: 'title', label: 'Enter a Title' },
					e('input', {
						type: 'text',
						id: 'title',
						name: 'zm_shbt_fld[title]',
						value: options.title,
						onChange: function (event) {
							self.update('title', event.target.value);
						}
					})
				),
				e(FieldRow, { key: 'exclude-field', id: 'excludes', label: 'Exclude' },
					e('textarea', {
						id: 'excludes',
						name: 'zm_shbt_fld[excludes]',
						value: options.excludes,
						placeholder: 'Exclude by Page ID, Page Title or Page Slug',
						onChange: function (event) {
							self.update('excludes', event.target.value);
						}
					})
				),
				e('p', { key: 'exclude-hint', className: 'zm_settings_hint' }, 'Exclude can contain page IDs, slugs, or titles separated by commas.')
			]),
			e('section', { key: 'icon-style', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Icon Style',
					description: 'Choose the icon pack used for every placement and generated code snippet.'
				}),
				e('div', { key: 'icon-style-panel', className: 'zm_icon_style_panel' }, [
					e(FieldRow, { key: 'iconset-field', id: 'iconset', label: 'Select Button Style' },
						e('select', {
							id: 'iconset',
							name: 'zm_shbt_fld[iconset]',
							value: options.iconset,
							onChange: function (event) {
								self.update('iconset', event.target.value);
							}
						}, iconsets.map(function (item) {
							return e('option', { key: item.id, value: item.id }, item.name);
						}))
					),
						e('div', { key: 'preview', className: 'button-style-img' }, [
						e('span', { key: 'label' }, 'Preview'),
						e('img', {
							key: 'image',
							src: currentIconset ? currentIconset.preview_img : '',
							alt: options.iconset
						})
					])
				])
			]),
			e('section', { key: 'placement', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Display placement',
					description: 'Turn each placement on or off and pick its shape.'
				}),
				e('div', { key: 'placement-grid', className: 'zm_placement_grid' }, [
					e(PlacementInput, {
						key: 'show-left',
						id: 'show_left',
						label: 'Show on Left Side',
						iconset: currentIconset,
						type: ensureType(currentIconset, options.show_left),
						enabled: options.show_in.show_left,
						onEnabled: function (value) {
							self.update('show_in.show_left', value);
						},
						onType: function (type) {
							self.update('show_left', type);
						}
					}),
					e(PlacementInput, {
						key: 'show-right',
						id: 'show_right',
						label: 'Show on Right Side',
						iconset: currentIconset,
						type: ensureType(currentIconset, options.show_right),
						enabled: options.show_in.show_right,
						onEnabled: function (value) {
							self.update('show_in.show_right', value);
						},
						onType: function (type) {
							self.update('show_right', type);
						}
					}),
					e(PlacementInput, {
						key: 'show-before-post',
						id: 'show_before_post',
						label: 'Show Before Post',
						iconset: currentIconset,
						type: ensureType(currentIconset, options.show_before_post),
						enabled: options.show_in.show_before_post,
						onEnabled: function (value) {
							self.update('show_in.show_before_post', value);
						},
						onType: function (type) {
							self.update('show_before_post', type);
						}
					}),
					e(PlacementInput, {
						key: 'show-after-post',
						id: 'show_after_post',
						label: 'Show After Post',
						iconset: currentIconset,
						type: ensureType(currentIconset, options.show_after_post),
						enabled: options.show_in.show_after_post,
						onEnabled: function (value) {
							self.update('show_in.show_after_post', value);
						},
						onType: function (type) {
							self.update('show_after_post', type);
						}
					})
				])
			]),
			e('section', { key: 'social', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Social Networks',
					description: 'Select the share buttons that should be available in the output.'
				}),
				e('div', { key: 'network-grid', className: 'zm_network_grid' }, currentIconset && currentIconset.icons && currentIconset.icons.length ? currentIconset.icons.map(function (icon) {
					return e(CheckboxInput, {
						key: icon.id,
						id: 'icon_' + icon.id,
						dataId: icon.id,
						label: 'Enable ' + icon.name,
						name: 'zm_shbt_fld[icons][' + icon.id + ']',
						checked: options.icons[icon.id],
						onChange: function (value) {
							self.update('icon_' + icon.id, value);
						}
					});
				}) : [])
			]),
			e('section', { key: 'advanced', className: 'zm_settings_section zm_settings_section--advanced' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Advanced options',
					description: 'Fine tune tracking, behavior, and link output.'
				}),
					e('div', { key: 'advanced-grid', className: 'zm_network_grid' }, [
						e(CheckboxInput, {
							key: 'g-analytics',
							id: 'g_analytics',
						label: 'Google Social analytics',
						name: 'zm_shbt_fld[g_analytics]',
						checked: options.g_analytics,
						onChange: function (value) {
							self.update('g_analytics', value);
						}
						}),
						e(CheckboxInput, {
							key: 'auto-hide',
							id: 'auto_hide_btn',
						label: 'Auto hide button',
						name: 'zm_shbt_fld[auto_hide_btn]',
						checked: options.auto_hide_btn,
						onChange: function (value) {
							self.update('auto_hide_btn', value);
						}
						}),
						e(CheckboxInput, {
							key: 'use-port',
							id: 'use_port',
						label: 'Use port on the url.',
						name: 'zm_shbt_fld[use_port]',
						checked: options.use_port,
						onChange: function (value) {
							self.update('use_port', value);
						}
						}),
						e(CheckboxInput, {
							key: 'nofollow',
							id: 'nofollow',
						label: 'No follow social link',
						name: 'zm_shbt_fld[nofollow]',
						checked: options.nofollow,
						onChange: function (value) {
							self.update('nofollow', value);
						}
					})
				])
			]),
			e('section', { key: 'generator', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Code generator',
					description: 'Generate embed code from the same icon set and selected networks.'
				}),
					e('div', { key: 'generator-actions', className: 'zm_settings_generator_actions' }, [
						e('a', {
							key: 'php',
							href: '#zm-sh-thick-box',
						className: 'get_phpcode button button-default',
						title: '<\\?> Get PHP Code',
						onClick: function (event) {
							event.preventDefault();
							self.openModal('php');
						}
						}, '<\\?> Get PHP Code'),
						e('a', {
							key: 'shortcode',
							href: '#zm-sh-thick-box',
						className: 'get_shortcode button button-default',
						title: '[] Get Shortcode',
						onClick: function (event) {
							event.preventDefault();
							self.openModal('shortcode');
						}
					}, '[] Get Shortcode')
				])
			]),
			e('div', {
				key: 'code-modal',
				className: 'zm-sh-thick-box',
				id: 'zm-sh-thick-box',
				style: {
					display: this.state.modalOpen ? 'block' : 'none',
					position: 'fixed',
					top: 0,
					left: 0,
					right: 0,
					bottom: 0,
					zIndex: 99999
				}
			}, [
				e('div', { key: 'backdrop', className: 'backdrop', onClick: function () { self.closeModal(); } }),
				e('div', { key: 'panel', className: 'zm-tabs', onMouseDown: function (event) { event.stopPropagation(); } }, [
					e('h3', { key: 'title', className: 'title' }, modalTitle),
					e('button', { key: 'close', type: 'button', className: 'close', onClick: function () { self.closeModal(); }, 'aria-label': 'Close' }, 'X'),
					e('div', { key: 'type-picker', className: 'tab', id: 'tab-1' },
							e('div', { key: 'type-picker-grid', className: 'zm_type_picker code-type' },
								((currentIconset && currentIconset.types) || ['square']).map(function (type) {
									var radioId = 'shortcode-' + type;
									return e('span', { key: type, className: 'zm_type_option' }, [
										e('input', {
											key: 'input',
											type: 'radio',
										id: radioId,
										name: 'shortcode-iconset-type',
										value: type,
										checked: self.state.modalType === type,
										onChange: function (event) {
											self.setState({ modalType: event.target.value });
										}
									}),
										e('label', { key: 'label', htmlFor: radioId }, [
											e('img', {
												key: 'image',
												src: data.assets_img + 'show_after_post-' + type + '.png',
												alt: type
											}),
											e('span', { key: 'name' }, type)
										])
									]);
								}))
					),
					e('div', { key: 'output', className: 'tab', id: 'tab-2' },
						e('textarea', {
							id: 'copy_shortcode',
							style: { width: '100%', height: '200px' },
							value: modalOutput,
							readOnly: true
						}, [])
					)
				])
			])
		]);
	};

	$(document).ready(function () {
		var root = document.getElementById('zmsh-react-settings-root');
		if (!root) {
			return;
		}
		if (typeof wp.element.createRoot === 'function') {
			wp.element.createRoot(root).render(e(App));
			return;
		}
		if (typeof wp.element.render === 'function') {
			wp.element.render(e(App), root);
		}
	});
})(window.wp, jQuery);
