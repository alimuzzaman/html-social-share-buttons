(function () {
	'use strict';

	function encodeComponent(value) {
		return encodeURIComponent(value).replace(/[!'()*]/g, function (character) {
			return '%' + character.charCodeAt(0).toString(16).toUpperCase();
		});
	}

	function browserUrl() {
		return window.location.href;
	}

	function updateLink(link) {
		var descriptorText = link.getAttribute('data-hssb-browser-descriptor');
		var serverHref = link.getAttribute('data-hssb-server-href') || link.getAttribute('href') || '';
		var descriptor;
		var currentUrl;
		if (!descriptorText) {
			return;
		}
		try {
			descriptor = JSON.parse(descriptorText);
		} catch {
			link.setAttribute('href', serverHref);
			return;
		}
		if (!descriptor || typeof descriptor.template !== 'string' || descriptor.permalink_slot !== '%%permalink%%' || descriptor.template.indexOf(descriptor.permalink_slot) === -1) {
			link.setAttribute('href', serverHref);
			return;
		}
		currentUrl = browserUrl();
		if (typeof currentUrl !== 'string' || !currentUrl) {
			link.setAttribute('href', serverHref);
			return;
		}
		link.setAttribute('href', descriptor.template.replace(descriptor.permalink_slot, encodeComponent(currentUrl)));
	}

	function updateAll() {
		var links = document.querySelectorAll('[data-hssb-browser-url="1"] [data-hssb-browser-descriptor]');
		for (var index = 0; index < links.length; index++) {
			updateLink(links[index]);
		}
	}

	function findLink(target) {
		var candidate;
		while (target && target !== document) {
			if (target.getAttribute && target.getAttribute('data-hssb-browser-descriptor')) {
				candidate = target;
				target = target.parentNode;
				while (target && target !== document) {
					if (target.getAttribute && target.getAttribute('data-hssb-browser-url') === '1') {
						return candidate;
					}
					target = target.parentNode;
				}
				return null;
			}
			target = target.parentNode;
		}
		return null;
	}

	document.addEventListener('click', function (event) {
		var link = findLink(event.target);
		if (link) {
			updateLink(link);
		}
	}, true);
	document.addEventListener('auxclick', function (event) {
		var link = findLink(event.target);
		if (link) {
			updateLink(link);
		}
	}, true);
	window.addEventListener('hashchange', updateAll);
	window.addEventListener('popstate', updateAll);
	window.addEventListener('pageshow', updateAll);
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', updateAll, { once: true });
	} else {
		updateAll();
	}
	if (typeof window.MutationObserver === 'function' && document.documentElement) {
		new window.MutationObserver(updateAll).observe(document.documentElement, { childList: true, subtree: true });
	}
}());

export {};
