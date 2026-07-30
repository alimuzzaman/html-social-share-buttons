export function toBoolean(value) {
	return !(value === false || value === 0 || value === '0' || value === '' || value === null || typeof value === 'undefined' || value === 'false');
}

export function findIconsetById(iconsets, id) {
	for (var i = 0; i < iconsets.length; i++) {
		if (iconsets[i].id === id) {
			return iconsets[i];
		}
	}
	return iconsets[0] || null;
}

export function normalizeSettingsOptions($, defaults, raw) {
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

export function ensureIconsetType($, iconset, value) {
	if (!iconset || !iconset.types || !iconset.types.length) {
		return 'square';
	}
	if ($.inArray(value, iconset.types) === -1) {
		return iconset.types[0];
	}
	return value;
}

export function getIconPreview(icon, type) {
	if (icon && icon.preview_urls && icon.preview_urls[type]) {
		return icon.preview_urls[type];
	}
	return icon && icon.preview_url ? icon.preview_url : '';
}

export function buildIconState(iconset, existing) {
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

export function excludeToken(item) {
	return item.token || ('#' + item.id);
}

export function excludeIds(items) {
	return (items || []).map(function (item) {
		return String(item.id || item.token);
	}).join(',');
}

export function hasOwn(object, key) {
	return Object.prototype.hasOwnProperty.call(object || {}, key);
}

export function normalizeTemplateOverrides(templateDefaults, raw) {
	var overrides = {};
	Object.keys(raw || {}).forEach(function (platform) {
		var value = raw[platform];
		if (typeof value === 'string' && value.trim() && value !== templateDefaults[platform]) {
			overrides[platform] = value;
		}
	});
	return overrides;
}

export function normalizeForIconset($, iconsets, options) {
	var iconset = findIconsetById(iconsets, options.iconset);
	var next = $.extend({}, options);
	next.show_in = $.extend({}, options.show_in || {});
	next.icons = buildIconState(iconset, options.icons || {});
	next.show_left = ensureIconsetType($, iconset, next.show_left);
	next.show_right = ensureIconsetType($, iconset, next.show_right);
	next.show_before_post = ensureIconsetType($, iconset, next.show_before_post);
	next.show_after_post = ensureIconsetType($, iconset, next.show_after_post);
	return next;
}
