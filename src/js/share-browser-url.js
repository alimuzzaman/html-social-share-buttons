// One descriptor calculation for the browser-URL and native-share runtimes.
export function resolvedBrowserUrl(link) {
	var fallback = link.getAttribute('data-hssb-server-href') || link.getAttribute('href') || link.getAttribute('data-hssb-native-url') || '';
	var parent = link.parentNode;
	while (parent && parent !== document && (!parent.getAttribute || parent.getAttribute('data-hssb-browser-url') !== '1')) {
		parent = parent.parentNode;
	}
	if (!parent || parent === document) {
		return fallback;
	}
	try {
		var descriptor = JSON.parse(link.getAttribute('data-hssb-browser-descriptor'));
		var url = window.location.href;
		if (!descriptor || typeof descriptor.template !== 'string' || descriptor.permalink_slot !== '%%permalink%%' || descriptor.template.indexOf(descriptor.permalink_slot) === -1 || typeof url !== 'string' || !url) {
			return fallback;
		}
		var value = descriptor.encoding === 'raw' ? url : encodeURIComponent(url).replace(/[!'()*]/g, function (character) {
			return '%' + character.charCodeAt(0).toString(16).toUpperCase();
		});
		return descriptor.template.split(descriptor.permalink_slot).join(value);
	} catch {
		return fallback;
	}
}
