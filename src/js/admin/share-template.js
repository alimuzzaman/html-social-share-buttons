export function splitShareTemplate(template, fallback) {
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

export function parseShareTemplateParameters(query, fallbackQuery) {
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

export function serializeShareTemplateParameters(parameters) {
	return (parameters || []).map(function (parameter) {
		return parameter.name + (parameter.hasEquals ? '=' + parameter.value : '');
	}).join('&');
}
