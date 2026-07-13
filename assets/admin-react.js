(function (wp, $) {
	'use strict';

	if (!wp || !wp.element || !wp.element.createElement || !wp.element.Component || !wp.components) {
		return;
	}

	var data = window.zm_sh_react_settings || {};
	var iconsets = data.iconsets || [];
	var strings = data.strings || {};
	var e = wp.element.createElement;
	var Component = wp.element.Component;
	var Button = wp.components.Button;
	var FormTokenField = wp.components.FormTokenField;
	var SelectControl = wp.components.SelectControl;
	var Snackbar = wp.components.Snackbar;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var sharePlaceholders = [
		{ syntax: '%%title%%', label: 'Post title', description: 'The title of the shared post' },
		{ syntax: '%%permalink%%', label: 'Permalink', description: 'The canonical post URL' },
		{ syntax: '%%imageurl%%', label: 'Featured image URL', description: 'The post\'s featured image' }
	];
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
		share_templates: {},
		g_analytics: 0,
		auto_hide_btn: 0,
		use_port: 0,
		nofollow: 0,
	};

	function toBoolean(value) {
		return !(value === false || value === 0 || value === '0' || value === '' || value === null || typeof value === 'undefined' || value === 'false');
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
		options.share_templates = $.extend({}, options.share_templates || {});
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

	function excludeToken(item) {
		return item.token || ('#' + item.id);
	}

	function excludeIds(items) {
		return (items || []).map(function (item) {
			return String(item.id || item.token);
		}).join(',');
	}

	function hasOwn(object, key) {
		return Object.prototype.hasOwnProperty.call(object || {}, key);
	}

	function normalizeTemplateOverrides(raw) {
		var overrides = {};
		var templateDefaults = data.share_template_defaults || {};
		Object.keys(raw || {}).forEach(function (platform) {
			var value = raw[platform];
			if (typeof value === 'string' && value.trim() && value !== templateDefaults[platform]) {
				overrides[platform] = value;
			}
		});
		return overrides;
	}

	function splitShareTemplate(template, fallback) {
		var source = String(template || '');
		var questionMark = source.indexOf('?');
		if (questionMark === -1) {
			if (fallback && String(fallback).indexOf('?') !== -1) {
				return {
					prefix: String(fallback).substring(0, String(fallback).indexOf('?') + 1),
					query: source
				};
			}
			return { prefix: source, query: '' };
		}
		return {
			prefix: source.substring(0, questionMark + 1),
			query: source.substring(questionMark + 1)
		};
	}

	function parseShareTemplateParameters(query, fallbackQuery) {
		var source = String(query || '');
		var fallback = String(fallbackQuery || '');
		var parts;
		var parameters;

		/* Older broken overrides could contain only a value. Present it as the
		 * first known parameter so the next save repairs the stored URL. */
		if (source && source.indexOf('=') === -1 && fallback.indexOf('=') !== -1) {
			parameters = parseShareTemplateParameters(fallback);
			parameters[0].value = source;
			return parameters;
		}

		parts = source.split('&');
		parameters = [];
		for (var i = 0; i < parts.length; i++) {
			var equals = parts[i].indexOf('=');
			if (!parts[i] && parts.length > 1) {
				continue;
			}
			parameters.push({
				name: equals === -1 ? parts[i] : parts[i].substring(0, equals),
				value: equals === -1 ? '' : parts[i].substring(equals + 1),
				hasEquals: equals !== -1
			});
		}
		return parameters;
	}

	function serializeShareTemplateParameters(parameters) {
		return (parameters || []).map(function (parameter) {
			return parameter.name + (parameter.hasEquals ? '=' + parameter.value : '');
		}).join('&');
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
		return e('div', { className: 'zm_native_toggle' + (props.className ? ' ' + props.className : '') }, [
			props.headerContent || null,
			e(ToggleControl, {
				key: 'control',
				label: props.label,
				name: props.name,
				value: '1',
				checked: toBoolean(props.checked),
				onChange: function (checked) {
					props.onChange(checked ? 1 : 0);
				},
				disabled: !!props.disabled,
				__nextHasNoMarginBottom: true
			})
		]);
	}

	function ExpandableTogglePanel(props) {
		var enabled = toBoolean(props.checked);
		var headerContent = props.headerContent;

		if (props.title) {
			headerContent = e('div', { key: 'identity', className: 'zm_panel_identity' }, [
				props.marker || null,
				e('div', { key: 'copy', className: 'zm_panel_copy' }, [
					e('h3', { key: 'title' }, props.title),
					props.description ? e('p', { key: 'description' }, props.description) : null
				])
			]);
		}

		return e('div', { className: 'zm_expandable_toggle_panel' + (props.className ? ' ' + props.className : '') + (enabled ? ' is-enabled' : '') }, [
			e(ToggleInput, {
				key: 'toggle',
				className: 'zm_panel_toggle' + (props.headerClassName ? ' ' + props.headerClassName : ''),
				headerContent: headerContent,
				label: props.label,
				name: props.name,
				checked: enabled,
				onChange: props.onChange,
				disabled: props.disabled
			}),
			enabled ? e('div', { key: 'details', className: 'zm_expandable_toggle_panel_details' + (props.detailsClassName ? ' ' + props.detailsClassName : '') }, props.children) : (props.preservedControl || null)
		]);
	}

	function SettingsLoader() {
		return e('div', {
			className: 'zm_settings_loader zm_settings_loader--react',
			role: 'status',
			'aria-live': 'polite'
		}, [
			e('span', { key: 'spinner', className: 'zm_settings_loader_spinner', 'aria-hidden': 'true' }),
			e('span', { key: 'label' }, strings.loading || 'Loading settings...')
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
		return e(ExpandableTogglePanel, {
			className: 'zm_placement_item',
			detailsClassName: 'zm_placement_details',
			marker: e('span', { key: 'diagram', className: 'zm_panel_marker zm_placement_diagram zm_placement_diagram--' + props.id, 'aria-hidden': 'true' }, [
						e('span', { key: 'copy', className: 'zm_placement_diagram_copy' }),
						e('span', { key: 'buttons', className: 'zm_placement_diagram_buttons' })
					]),
			title: props.label,
			description: props.description,
			label: props.enabled ? 'Enabled' : 'Disabled',
			name: 'zm_shbt_fld[show_in][' + props.id + ']',
			checked: props.enabled,
			onChange: function (checked) {
				props.onEnabled(checked ? 1 : 0);
			},
			preservedControl: e('input', {
				key: 'preserved-type',
				type: 'hidden',
				name: 'zm_shbt_fld[' + props.id + ']',
				value: props.type
			})
		}, [
				e(SelectControl, {
					key: 'type',
					label: 'Button shape',
					name: 'zm_shbt_fld[' + props.id + ']',
					value: props.type,
					options: types.map(function (type) {
						return { label: type, value: type };
					}),
					onChange: props.onType,
					__next40pxDefaultSize: true,
					__nextHasNoMarginBottom: true
				})
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
		var initialExcludeItems = (data.exclude_items || []).concat((data.exclude_custom || []).map(function (value) {
			return { id: value, token: value, custom: true };
		}));
		this.state = {
			options: normalizeForIconset(normalizeOptions(data.options || {})),
			shareTemplateOverrides: normalizeTemplateOverrides(data.share_template_overrides || {}),
			excludeItems: initialExcludeItems,
			excludeSuggestions: initialExcludeItems.filter(function (item) { return !item.custom; }).map(excludeToken),
			excludeSuggestionItems: initialExcludeItems.filter(function (item) { return !item.custom; }),
			modalOpen: false,
			modalMode: 'shortcode',
			modalType: 'square',
			isSaving: false,
			isDirty: false,
			notice: null,
			templateAutocomplete: null,
		};
		this.noticeTimer = null;
		this.submitLabel = '';
		this.excludeSearchTimer = null;
		this.templateFields = {};
		this.activeTemplateField = {};
		this.templateSelections = {};
		this.templateEditorVersions = {};
		this.changeRevision = 0;
	}

	App.prototype = Object.create(Component.prototype);
	App.prototype.constructor = App;

	App.prototype.componentDidMount = function () {
		this.$form = $('#zm-social-share-settings');
		this.handleSubmitBound = this.handleSubmit.bind(this);
		if (data.ajax_url && data.nonce) {
			this.$form.on('submit.zmShareSettings', this.handleSubmitBound);
		}
		this.$form.toggleClass('is-dirty', !!this.state.isDirty);
		this.handleTemplateDocumentPointerDownBound = this.handleTemplateDocumentPointerDown.bind(this);
		if (document.addEventListener) {
			document.addEventListener('mousedown', this.handleTemplateDocumentPointerDownBound);
		}
	};

	App.prototype.componentDidUpdate = function () {
		if (this.$form) {
			this.$form.toggleClass('is-dirty', !!this.state.isDirty);
		}
	};

	App.prototype.componentWillUnmount = function () {
		this.setBodyLock(false);
		if (this.$form && this.handleSubmitBound) {
			this.$form.off('submit.zmShareSettings', this.handleSubmitBound);
		}
		if (this.noticeTimer) {
			window.clearTimeout(this.noticeTimer);
		}
		if (this.excludeSearchTimer) {
			window.clearTimeout(this.excludeSearchTimer);
		}
		if (document.removeEventListener && this.handleTemplateDocumentPointerDownBound) {
			document.removeEventListener('mousedown', this.handleTemplateDocumentPointerDownBound);
		}
	};

	App.prototype.showNotice = function (message, status) {
		var self = this;
		if (this.noticeTimer) {
			window.clearTimeout(this.noticeTimer);
		}
		this.setState({ notice: { message: message, status: status } });
		this.noticeTimer = window.setTimeout(function () {
			self.setState({ notice: null });
		}, 5000);
	};

	App.prototype.handleSubmit = function (event) {
		var self = this;
		var $submit;
		var submittedRevision;

		event.preventDefault();
		if (this.state.isSaving) {
			return;
		}

		$submit = this.$form.find('#submit');
		submittedRevision = this.changeRevision;
		this.submitLabel = $submit.val();
		$submit.prop('disabled', true).attr('aria-busy', 'true').addClass('is-busy').val(strings.saving || 'Saving...');
		this.setState({ isSaving: true, notice: null });

		$.ajax({
			url: data.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'zm_sh_save_settings',
				nonce: data.nonce,
				settings: this.$form.serialize()
			}
		}).done(function (response) {
			if (response && response.success) {
				self.showNotice((response.data && response.data.message) || strings.saved || 'Settings saved.', 'success');
				if (self.changeRevision === submittedRevision) {
					self.setState({ isDirty: false });
				}
				return;
			}
			self.showNotice((response && response.data && response.data.message) || strings.saveError || 'Settings could not be saved. Try again.', 'error');
		}).fail(function (request) {
			var response = request.responseJSON;
			self.showNotice((response && response.data && response.data.message) || strings.saveError || 'Settings could not be saved. Try again.', 'error');
		}).always(function () {
			$submit.prop('disabled', false).removeAttr('aria-busy').removeClass('is-busy').val(self.submitLabel || 'Save Changes');
			self.setState({ isSaving: false });
		});
	};

	App.prototype.setBodyLock = function (locked) {
		$('body').css(locked ? { overflow: 'hidden', height: '100%' } : { overflow: 'initial', height: 'initial' });
	};

	App.prototype.update = function (path, value) {
		this.changeRevision += 1;
		this.setState({ isDirty: true });
		this.setState(function (prev) {
			var nextOptions = $.extend({}, prev.options);
			nextOptions.show_in = $.extend({}, prev.options.show_in || {});
			nextOptions.icons = $.extend({}, prev.options.icons || {});
			nextOptions.share_templates = $.extend({}, prev.options.share_templates || {});
			switch (path) {
				case 'title':
					nextOptions.title = value;
					break;
				case 'excludes':
					nextOptions.excludes = value;
					break;
				case 'share_templates.facebook':
				case 'share_templates.x':
				case 'share_templates.linkedin':
				case 'share_templates.pinterest':
				case 'share_templates.telegram':
				case 'share_templates.bluesky':
				case 'share_templates.mail':
					nextOptions.share_templates[path.substring(16)] = value;
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
			var nextState = { options: nextOptions };
			if (path.indexOf('share_templates.') === 0) {
				nextState.shareTemplateOverrides = $.extend({}, prev.shareTemplateOverrides || {});
				nextState.shareTemplateOverrides[path.substring(16)] = value;
			}
			return nextState;
		});
	};

	App.prototype.resetShareTemplate = function (platform) {
		this.update('share_templates.' + platform, '');
	};

	App.prototype.getTemplateParts = function (platform) {
		var template = hasOwn(this.state.shareTemplateOverrides, platform) ? this.state.shareTemplateOverrides[platform] : '';
		var defaultTemplate = data.share_template_defaults && data.share_template_defaults[platform] ? data.share_template_defaults[platform] : '';
		return {
			current: splitShareTemplate(template || defaultTemplate, defaultTemplate),
			defaultValue: splitShareTemplate(defaultTemplate)
		};
	};

	App.prototype.updateShareTemplateQuery = function (platform, query) {
		var parts = this.getTemplateParts(platform);
		this.update('share_templates.' + platform, parts.current.prefix + query);
	};

	App.prototype.getShareTemplateParameters = function (platform) {
		var parts = this.getTemplateParts(platform);
		return parseShareTemplateParameters(parts.current.query, parts.defaultValue.query);
	};

	App.prototype.updateShareTemplateParameter = function (platform, index, value) {
		var parameters = this.getShareTemplateParameters(platform);
		if (!parameters[index]) {
			return;
		}
		parameters[index].value = value;
		parameters[index].hasEquals = true;
		this.updateShareTemplateQuery(platform, serializeShareTemplateParameters(parameters));
	};

	App.prototype.setActiveTemplateField = function (platform, index) {
		this.activeTemplateField[platform] = index;
	};

	App.prototype.templateFieldKey = function (platform, index) {
		return platform + ':' + index;
	};

	App.prototype.getTemplateSelection = function (field) {
		var selection;
		var range;
		var before;
		var selected;

		if (!field || !window.getSelection) {
			return null;
		}
		selection = window.getSelection();
		if (!selection || !selection.rangeCount) {
			return null;
		}
		range = selection.getRangeAt(0);
		if (!field.contains(range.startContainer) || !field.contains(range.endContainer)) {
			return null;
		}
		before = range.cloneRange();
		before.selectNodeContents(field);
		before.setEnd(range.startContainer, range.startOffset);
		selected = range.cloneRange();
		return {
			start: before.toString().length,
			end: before.toString().length + selected.toString().length
		};
	};

	App.prototype.setTemplateSelection = function (field, start, end) {
		var selection;
		var range;
		var nodes = [];
		var index = 0;

		function collect(node) {
			var child;
			if (node.nodeType === 3) {
				nodes.push(node);
				return;
			}
			for (child = node.firstChild; child; child = child.nextSibling) {
				collect(child);
			}
		}

		function pointAt(offset) {
			var node;
			var length;
			for (index = 0; index < nodes.length; index++) {
				node = nodes[index];
				length = node.nodeValue.length;
				if (offset <= length) {
					return { node: node, offset: offset };
				}
				offset -= length;
			}
			return nodes.length ? { node: nodes[nodes.length - 1], offset: nodes[nodes.length - 1].nodeValue.length } : { node: field, offset: 0 };
		}

		if (!field || !document.createRange || !window.getSelection) {
			return;
		}
		collect(field);
		range = document.createRange();
		var startPoint = pointAt(start);
		var endPoint = pointAt(end);
		range.setStart(startPoint.node, startPoint.offset);
		range.setEnd(endPoint.node, endPoint.offset);
		selection = window.getSelection();
		selection.removeAllRanges();
		selection.addRange(range);
	};

	App.prototype.restoreTemplateSelection = function (fieldKey) {
		var selection = this.templateSelections[fieldKey];
		var field = this.templateFields[fieldKey];
		if (!selection || !field) {
			return;
		}
		field.focus();
		this.setTemplateSelection(field, selection.start, selection.end);
		delete this.templateSelections[fieldKey];
	};

	App.prototype.scheduleTemplateSelectionRestore = function (fieldKey) {
		var self = this;
		window.setTimeout(function () {
			self.restoreTemplateSelection(fieldKey);
		}, 0);
	};

	App.prototype.getTemplateAutocompletePosition = function (field) {
		var fieldRect;
		var root;
		var rootRect;
		var selection;
		var range;
		var caretRect;
		var width = 264;
		var height = 154;
		var viewportWidth = window.innerWidth || 0;
		var viewportHeight = window.innerHeight || 0;
		var left;
		var top;

		if (!field || !field.getBoundingClientRect) {
			return { left: 8, top: 38 };
		}
		fieldRect = field.getBoundingClientRect();
		root = field.closest ? field.closest('.zm_network_template') : null;
		rootRect = root && root.getBoundingClientRect ? root.getBoundingClientRect() : fieldRect;
		caretRect = fieldRect;
		if (window.getSelection) {
			selection = window.getSelection();
			if (selection && selection.rangeCount) {
				range = selection.getRangeAt(0).cloneRange();
				range.collapse(false);
				if (range.getBoundingClientRect) {
					caretRect = range.getBoundingClientRect() || fieldRect;
				}
			}
		}
		left = caretRect.left - rootRect.left;
		top = caretRect.bottom - rootRect.top + 6;
		left = Math.max(8, Math.min(left, Math.max(8, rootRect.width - width - 8)));
		if (viewportWidth) {
			left = Math.max(8, Math.min(left, viewportWidth - rootRect.left - width - 8));
		}
		if (viewportHeight) {
			top = Math.min(top, Math.max(8, viewportHeight - rootRect.top - height - 8));
		}
		return { left: Math.round(left), top: Math.round(Math.max(8, top)) };
	};

	App.prototype.openTemplateAutocomplete = function (platform, index, field, replaceRange) {
		this.setState({
			templateAutocomplete: {
				platform: platform,
				index: index,
				selectedIndex: 0,
				position: this.getTemplateAutocompletePosition(field),
				replaceRange: replaceRange || null
			}
		});
	};

	App.prototype.closeTemplateAutocomplete = function () {
		if (this.state.templateAutocomplete) {
			this.setState({ templateAutocomplete: null });
		}
	};

	App.prototype.handleTemplateDocumentPointerDown = function (event) {
		var target = event.target;
		if (!target || !target.closest || (!target.closest('.zm_template_parameter_editor') && !target.closest('.zm_template_autocomplete'))) {
			this.closeTemplateAutocomplete();
		}
	};

	App.prototype.handleTemplateInput = function (platform, index, event) {
		var field = event.currentTarget || event.target;
		var value = field && typeof field.textContent === 'string' ? field.textContent : '';
		var fieldKey = this.templateFieldKey(platform, index);
		var selection = this.getTemplateSelection(field);
		var isComposing = !!(event.isComposing || (event.nativeEvent && event.nativeEvent.isComposing));

		this.setActiveTemplateField(platform, index);
		if (selection) {
			this.templateSelections[fieldKey] = selection;
		}
		this.updateShareTemplateParameter(platform, index, value);
		if (!isComposing && selection && selection.start === selection.end && value.substring(0, selection.start).slice(-2) === '%%') {
			this.openTemplateAutocomplete(platform, index, field, {
				start: selection.start - 2,
				end: selection.start
			});
		} else {
			this.closeTemplateAutocomplete();
		}
		this.scheduleTemplateSelectionRestore(fieldKey);
	};

	App.prototype.handleTemplateKeyDown = function (platform, index, event) {
		var autocomplete = this.state.templateAutocomplete;
		var isActive = autocomplete && autocomplete.platform === platform && autocomplete.index === index;
		var field = event.currentTarget || event.target;

		if (event.isComposing || (event.nativeEvent && event.nativeEvent.isComposing) || event.keyCode === 229) {
			return;
		}
		if (event.ctrlKey && (event.key === ' ' || event.key === 'Spacebar')) {
			event.preventDefault();
			this.openTemplateAutocomplete(platform, index, field);
			return;
		}
		if (!isActive) {
			return;
		}
		if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
			event.preventDefault();
			this.setState({
				templateAutocomplete: $.extend({}, autocomplete, {
					selectedIndex: (autocomplete.selectedIndex + (event.key === 'ArrowDown' ? 1 : sharePlaceholders.length - 1)) % sharePlaceholders.length
				})
			});
		} else if (event.key === 'Enter') {
			event.preventDefault();
			this.insertSharePlaceholder(platform, sharePlaceholders[autocomplete.selectedIndex].syntax);
		} else if (event.key === 'Escape') {
			event.preventDefault();
			this.closeTemplateAutocomplete();
		}
	};

	App.prototype.handleTemplateBlur = function () {
		var self = this;
		window.setTimeout(function () {
			self.closeTemplateAutocomplete();
		}, 0);
	};

	App.prototype.handleTemplatePaste = function (platform, index, event) {
		var field = event.currentTarget || event.target;
		var clipboard = event.clipboardData || window.clipboardData;
		var text = clipboard && clipboard.getData ? clipboard.getData('text/plain') : '';
		var selection;
		var range;
		var textNode;

		event.preventDefault();
		if (!field || !document.createTextNode || !window.getSelection) {
			return;
		}
		selection = window.getSelection();
		if (!selection || !selection.rangeCount) {
			return;
		}
		range = selection.getRangeAt(0);
		range.deleteContents();
		textNode = document.createTextNode(text);
		range.insertNode(textNode);
		range.setStartAfter(textNode);
		range.collapse(true);
		selection.removeAllRanges();
		selection.addRange(range);
		this.handleTemplateInput(platform, index, { currentTarget: field });
	};

	App.prototype.renderTemplateValue = function (value) {
		var parts = String(value || '').split(/(%%(?:permalink|title|imageurl)%%)/g);
		return parts.map(function (part, index) {
			if (/^%%(?:permalink|title|imageurl)%%$/.test(part)) {
				return e('span', {
					key: index,
					className: 'zm_template_placeholder',
					title: part
				}, part);
			}
			return part;
		});
	};

	App.prototype.renderTemplateAutocomplete = function (platform, index, autocomplete) {
		var self = this;
		var listboxId = 'share_template_suggestions_' + platform + '_' + index;
		return e('div', {
			key: 'autocomplete',
			id: listboxId,
			className: 'zm_template_autocomplete',
			role: 'listbox',
			'aria-label': 'Insert share parameter placeholder',
			style: {
				left: autocomplete.position.left + 'px',
				top: autocomplete.position.top + 'px'
			}
		}, sharePlaceholders.map(function (placeholder, placeholderIndex) {
			var optionId = listboxId + '_option_' + placeholderIndex;
			return e('div', {
				key: placeholder.syntax,
				id: optionId,
				className: 'zm_template_autocomplete_option' + (autocomplete.selectedIndex === placeholderIndex ? ' is-selected' : ''),
				role: 'option',
				'aria-selected': autocomplete.selectedIndex === placeholderIndex,
				onMouseDown: function (event) {
					event.preventDefault();
					self.setActiveTemplateField(platform, index);
					self.insertSharePlaceholder(platform, placeholder.syntax);
				}
			}, [
				e('span', { key: 'label', className: 'zm_template_autocomplete_label' }, placeholder.label),
				e('span', { key: 'description', className: 'zm_template_autocomplete_description' }, placeholder.description),
				e('code', { key: 'syntax', className: 'zm_template_autocomplete_syntax' }, placeholder.syntax)
			]);
		}));
	};

	App.prototype.insertSharePlaceholder = function (platform, placeholder) {
		var parameters = this.getShareTemplateParameters(platform);
		var index = typeof this.activeTemplateField[platform] === 'number' ? this.activeTemplateField[platform] : 0;
		var fieldKey = this.templateFieldKey(platform, index);
		var field = this.templateFields[fieldKey];
		var current = parameters[index] ? parameters[index].value : '';
		var selection = this.getTemplateSelection(field);
		var start = selection ? selection.start : current.length;
		var end = selection ? selection.end : start;
		var autocomplete = this.state.templateAutocomplete;
		var replaceRange = autocomplete && autocomplete.platform === platform && autocomplete.index === index ? autocomplete.replaceRange : null;

		if (replaceRange && replaceRange.start >= 0 && replaceRange.end >= replaceRange.start && replaceRange.end <= current.length) {
			start = replaceRange.start;
			end = replaceRange.end;
		}
		var next = current.substring(0, start) + placeholder + current.substring(end);

		this.templateEditorVersions[fieldKey] = (this.templateEditorVersions[fieldKey] || 0) + 1;
		this.templateSelections[fieldKey] = { start: start + placeholder.length, end: start + placeholder.length };
		this.updateShareTemplateParameter(platform, index, next);
		this.closeTemplateAutocomplete();
		this.scheduleTemplateSelectionRestore(fieldKey);
	};

	App.prototype.updateExcludeTokens = function (tokens) {
		var known = {};
		(this.state.excludeItems || []).forEach(function (item) {
			known[excludeToken(item)] = item;
		});
		(this.state.excludeSuggestionItems || []).forEach(function (item) {
			known[excludeToken(item)] = item;
		});
		(this.state.excludeSuggestions || []).forEach(function (token) {
			if (!known[token]) {
				known[token] = { id: token, token: token, custom: true };
			}
		});

		var selected = [];
		(tokens || []).forEach(function (token) {
			selected.push(known[token] || { id: token, token: token, custom: true });
		});

		this.setState({
			excludeItems: selected,
			options: $.extend({}, this.state.options, { excludes: excludeIds(selected) })
		});
	};

	App.prototype.searchExcludeContent = function (query) {
		var self = this;
		if (this.excludeSearchTimer) {
			window.clearTimeout(this.excludeSearchTimer);
		}
		this.excludeSearchTimer = window.setTimeout(function () {
			$.post(data.ajax_url, {
				action: 'zm_sh_search_content',
				nonce: data.nonce,
				query: query || ''
			}).done(function (response) {
				if (response && response.success && response.data) {
					self.setState({
						excludeSuggestions: response.data.map(excludeToken),
						excludeSuggestionItems: response.data
					});
				}
			});
		}, 250);
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
		var socialNetworkColumns = [[], []];

		if (currentIconset && currentIconset.icons && currentIconset.icons.length) {
			currentIconset.icons.forEach(function (icon, index) {
				socialNetworkColumns[index % 2].push(icon);
			});
		}
		var placementColumns = [
			[
				{ id: 'show_left', label: 'Left side', description: 'A vertical rail on the left edge of the screen.' },
				{ id: 'show_before_post', label: 'Before post', description: 'A row of buttons placed above post content.' }
			],
			[
				{ id: 'show_right', label: 'Right side', description: 'A vertical rail on the right edge of the screen.' },
				{ id: 'show_after_post', label: 'After post', description: 'A row of buttons placed below post content.' }
			]
		];

		return e('div', { className: 'zm_settings_shell' + (this.state.isDirty ? ' is-dirty' : '') }, [
			e('div', { key: 'top-grid', className: 'zm_settings_top_grid' }, [
				e('section', { key: 'header', className: 'zm_settings_section zm_settings_section--intro' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Header',
					description: 'Set the text shown with the share buttons and choose pages where buttons should stay hidden.'
				}),
				e(TextControl, {
					key: 'title-field',
					id: 'title',
					label: 'Enter a title',
					name: 'zm_shbt_fld[title]',
					value: options.title,
					onChange: function (value) {
						self.update('title', value);
					},
					__next40pxDefaultSize: true,
					__nextHasNoMarginBottom: true
				}),
				e('div', { key: 'exclude-field', className: 'zm_exclude_control' }, [
					e(FormTokenField, {
						key: 'tokens',
						label: 'Exclude pages or posts',
						value: this.state.excludeItems.map(excludeToken),
						suggestions: this.state.excludeSuggestions,
						help: 'Search published pages and posts, or press Enter to add a custom value.',
						placeholder: 'Search pages, posts, or add a custom value',
						tokenizeOnBlur: true,
						__next40pxDefaultSize: true,
						onInputChange: function (value) {
							self.searchExcludeContent(value);
						},
						onChange: function (values) {
							self.updateExcludeTokens(values);
						}
					}),
					e('input', {
						key: 'value',
						type: 'hidden',
						id: 'excludes',
						name: 'zm_shbt_fld[excludes]',
						value: excludeIds(this.state.excludeItems)
					})
				])
				]),
				e('section', { key: 'icon-style', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Icon Style',
					description: 'Choose the icon pack used for every placement and generated code snippet.'
				}),
				e('div', { key: 'icon-style-panel', className: 'zm_icon_style_panel' }, [
					e(SelectControl, {
						key: 'iconset-field',
						id: 'iconset',
						label: 'Button style',
						name: 'zm_shbt_fld[iconset]',
						value: options.iconset,
						options: iconsets.map(function (item) {
							return { label: item.name, value: item.id };
						}),
						onChange: function (value) {
							self.update('iconset', value);
						},
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true
					}),
					e('div', { key: 'preview', className: 'button-style-img' }, [
						e('span', { key: 'label' }, 'Preview'),
						e('img', {
							key: 'image',
							src: currentIconset ? currentIconset.preview_img : '',
							alt: options.iconset
						})
					])
					])
				])
			]),
			e('section', { key: 'placement', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Display placement',
					description: 'Turn each placement on or off and pick its shape.'
				}),
				e('div', { key: 'placement-columns', className: 'zm_network_columns zm_placement_columns' }, placementColumns.map(function (column, columnIndex) {
					return e('div', { key: 'placement-column-' + columnIndex, className: 'zm_network_column zm_placement_column' }, column.map(function (placement) {
						return e(PlacementInput, {
							key: placement.id,
							id: placement.id,
							label: placement.label,
							description: placement.description,
							iconset: currentIconset,
							type: ensureType(currentIconset, options[placement.id]),
							enabled: options.show_in[placement.id],
							onEnabled: function (value) {
								self.update('show_in.' + placement.id, value);
							},
							onType: function (type) {
								self.update(placement.id, type);
							}
						});
					}));
				}))
			]),
			e('section', { key: 'social', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: 'Social Networks',
					description: 'Select the share buttons that should be available in the output.'
				}),
					e('div', { key: 'network-columns', className: 'zm_network_columns' }, socialNetworkColumns.map(function (column, columnIndex) {
						return e('div', { key: 'network-column-' + columnIndex, className: 'zm_network_column' }, column.map(function (icon) {
					var enabled = toBoolean(options.icons[icon.id]);
					var template = hasOwn(self.state.shareTemplateOverrides, icon.id) ? self.state.shareTemplateOverrides[icon.id] : '';
					var defaultTemplate = data.share_template_defaults && data.share_template_defaults[icon.id] ? data.share_template_defaults[icon.id] : (options.share_templates[icon.id] || '');
					var isCustomTemplate = String(template).trim().length > 0;
					var templateParts = splitShareTemplate(template || defaultTemplate, defaultTemplate);
					var defaultTemplateParts = splitShareTemplate(defaultTemplate);
					var templateParameters = parseShareTemplateParameters(templateParts.query, defaultTemplateParts.query);
					var serializedTemplate = isCustomTemplate ? templateParts.prefix + serializeShareTemplateParameters(templateParameters) : '';
					var autocomplete = self.state.templateAutocomplete;
					return e(ExpandableTogglePanel, {
						key: icon.id,
						className: 'zm_network_item',
						detailsClassName: 'zm_network_template',
						marker: e('span', { key: 'icon', className: 'zm_panel_marker zm_network_marker', 'aria-hidden': 'true' }, icon.preview_url ? e('img', {
							key: 'image',
							src: icon.preview_url,
							alt: ''
						}) : icon.name.substring(0, 1)),
						title: icon.name,
						description: icon.id === 'mail' ? 'Share the current page by email.' : 'Share the current page on ' + icon.name + '.',
						label: enabled ? 'Enabled' : 'Disabled',
						name: 'zm_shbt_fld[icons][' + icon.id + ']',
						checked: enabled,
						onChange: function (value) {
							self.update('icon_' + icon.id, value);
						}
					}, [
							e('div', { key: 'template-heading', className: 'zm_network_template_heading' }, [
								e('span', { key: 'label', className: 'zm_network_template_label' }, icon.name + ' share template'),
								e(Button, {
									key: 'reset',
									isLink: true,
									type: 'button',
									className: 'zm_template_reset',
									disabled: !isCustomTemplate,
									onClick: function () { self.resetShareTemplate(icon.id); }
								}, 'Restore defaults')
							]),
							e('p', { key: 'prefix-row', className: 'zm_template_prefix_row' }, [
								e('span', { key: 'prefix-label' }, 'Share URL'),
								e('code', { key: 'prefix', className: 'zm_template_prefix', title: templateParts.prefix }, templateParts.prefix)
							]),
							e('fieldset', { key: 'parameters', className: 'zm_template_parameters' }, [
								e('legend', { key: 'legend' }, [
									e('span', { key: 'label' }, 'Share parameters'),
									e('span', { key: 'hint', className: 'zm_template_parameters_hint' }, 'Parameter names are managed automatically')
								]),
								e('div', { key: 'parameter-list', className: 'zm_template_parameter_list' + (autocomplete && autocomplete.platform === icon.id ? ' is-autocomplete-active' : '') }, templateParameters.map(function (parameter, parameterIndex) {
									var inputId = 'share_template_' + icon.id + '_' + parameterIndex;
									var fieldKey = self.templateFieldKey(icon.id, parameterIndex);
									var isAutocompleteActive = autocomplete && autocomplete.platform === icon.id && autocomplete.index === parameterIndex;
									var listboxId = 'share_template_suggestions_' + icon.id + '_' + parameterIndex;
									var activeOptionId = isAutocompleteActive ? listboxId + '_option_' + autocomplete.selectedIndex : null;
									return e('div', { key: inputId, className: 'zm_template_parameter' }, [
										e('span', { key: 'name', className: 'zm_template_parameter_name' }, parameter.name || 'Parameter'),
										e('div', {
											key: 'value-' + (self.templateEditorVersions[fieldKey] || 0),
											id: inputId,
											className: 'zm_template_parameter_editor',
											contentEditable: true,
											suppressContentEditableWarning: true,
											role: 'combobox',
											'aria-label': icon.name + ' ' + (parameter.name || 'parameter') + ' value',
											'aria-autocomplete': 'list',
											'aria-haspopup': 'listbox',
											'aria-controls': isAutocompleteActive ? listboxId : null,
											'aria-expanded': !!isAutocompleteActive,
											'aria-activedescendant': activeOptionId,
											ref: function (node) { self.templateFields[fieldKey] = node; },
											onFocus: function () { self.setActiveTemplateField(icon.id, parameterIndex); },
											onInput: function (event) { self.handleTemplateInput(icon.id, parameterIndex, event); },
											onKeyDown: function (event) { self.handleTemplateKeyDown(icon.id, parameterIndex, event); },
											onBlur: function () { self.handleTemplateBlur(); },
											onPaste: function (event) { self.handleTemplatePaste(icon.id, parameterIndex, event); },
											onDrop: function (event) { event.preventDefault(); }
										}, self.renderTemplateValue(parameter.value)),
										isAutocompleteActive ? self.renderTemplateAutocomplete(icon.id, parameterIndex, autocomplete) : null
									]);
								}))
							]),
							e('input', {
								key: 'serialized-template',
								type: 'hidden',
								name: 'zm_shbt_fld[share_templates][' + icon.id + ']',
								value: serializedTemplate
							}),
							e('p', { key: 'help', className: 'components-base-control__help' }, isCustomTemplate ? 'Custom template saved for this platform.' : 'Using the canonical template shown as the placeholder.')
						]);
						}));
					}))
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
			this.state.notice ? e('div', {
				key: 'toaster',
				className: 'zm_settings_toaster is-' + this.state.notice.status
			}, e(Snackbar, {
				onRemove: function () {
					self.setState({ notice: null });
				}
			}, this.state.notice.message)) : null,
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
					e(SelectControl, {
						key: 'type-picker',
						id: 'shortcode-iconset-type',
						label: 'Button shape',
						value: self.state.modalType,
						options: ((currentIconset && currentIconset.types) || ['square']).map(function (type) {
							return { label: type, value: type };
						}),
						onChange: function (value) {
							self.setState({ modalType: value });
						},
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true
					}),
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
		var mountApp;
		if (!root) {
			return;
		}
		if (typeof wp.element.createRoot === 'function') {
			var reactRoot = wp.element.createRoot(root);
			reactRoot.render(e(SettingsLoader));
			mountApp = function () {
				reactRoot.render(e(App));
			};
			if (typeof window.requestAnimationFrame === 'function') {
				window.requestAnimationFrame(mountApp);
			} else {
				mountApp();
			}
			return;
		}
		if (typeof wp.element.render === 'function') {
			wp.element.render(e(SettingsLoader), root);
			mountApp = function () {
				wp.element.render(e(App), root);
			};
			if (typeof window.requestAnimationFrame === 'function') {
				window.requestAnimationFrame(mountApp);
			} else {
				mountApp();
			}
		}
	});
})(window.wp, jQuery);
