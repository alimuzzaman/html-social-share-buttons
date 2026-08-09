import { createSettingsComponents } from './components';
import { attachExcludeSelectorBehavior } from './exclude-selector-behavior';
import { attachModalBehavior } from './modal-behavior';
import { attachSettingsRenderer } from './settings-renderer';
import { mountSettingsApp } from './mount-settings-app';
import {
	ensureIconsetType,
	excludeToken,
	findIconsetById,
	normalizeForIconset as normalizeIconsetOptions,
	normalizeSettingsOptions,
	normalizeTemplateOverrides as normalizeOverrides,
	toBoolean,
} from './settings-model';
import { attachTemplateEditorBehavior } from './template-editor-behavior';

/* Bundled at build time through src/js/admin-react.js. */
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
	var SelectControl = wp.components.SelectControl;
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
		profile_links: {},
		g_analytics: 0,
		auto_hide_btn: 0,
		use_port: 0,
		nofollow: 0,
	};

	function findIconset(id) {
		return findIconsetById(iconsets, id);
	}

	function normalizeOptions(raw) {
		return normalizeSettingsOptions($, defaults, raw);
	}

	function ensureType(iconset, value) {
		return ensureIconsetType($, iconset, value);
	}

	function normalizeTemplateOverrides(raw) {
		return normalizeOverrides(data.share_template_defaults || {}, raw);
	}

	function normalizeForIconset(options) {
		return normalizeIconsetOptions($, iconsets, options);
	}

	var settingsComponents = createSettingsComponents({
		createElement: e,
		SelectControl: SelectControl,
		ToggleControl: ToggleControl,
		strings: strings,
		toBoolean: toBoolean,
	});
	var SettingsLoader = settingsComponents.SettingsLoader;

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
		this.excludeSearchRequest = null;
		this.templateFields = {};
		this.activeTemplateField = {};
		this.templateSelections = {};
		this.templateEditorVersions = {};
		this.changeRevision = 0;
	}

	App.prototype = Object.create(Component.prototype);
	App.prototype.constructor = App;
	attachTemplateEditorBehavior(App, {
		$: $,
		data: data,
		createElement: e,
		sharePlaceholders: sharePlaceholders,
	});
	attachExcludeSelectorBehavior(App, {
		$: $,
		data: data,
	});
	attachModalBehavior(App, {
		findIconset: findIconset,
		ensureType: ensureType,
	});
	attachSettingsRenderer(App, {
		createElement: e,
		data: data,
		iconsets: iconsets,
		findIconset: findIconset,
		ensureType: ensureType,
		components: wp.components,
		settingsComponents: settingsComponents,
	});

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

	App.prototype.componentDidUpdate = function (prevProps, prevState) {
		if (this.$form) {
			this.$form.toggleClass('is-dirty', !!this.state.isDirty);
		}
		if (this.state.modalOpen && !prevState.modalOpen && this.modalCloseButton) {
			this.modalCloseButton.focus();
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
		if (this.excludeSearchRequest && this.excludeSearchRequest.abort) {
			this.excludeSearchRequest.abort();
			this.excludeSearchRequest = null;
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
			nextOptions.profile_links = $.extend({}, prev.options.profile_links || {});
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
					if (path.indexOf('profile_links.') === 0) {
						nextOptions.profile_links[path.substring(14)] = value;
					} else if (path.indexOf('icon_') === 0) {
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


	mountSettingsApp({
		$: $,
		wp: wp,
		createElement: e,
		App: App,
		SettingsLoader: SettingsLoader,
	});
})(window.wp, jQuery);
