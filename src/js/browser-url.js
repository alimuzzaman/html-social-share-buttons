import { resolvedBrowserUrl } from './share-browser-url';

(function () {
	'use strict';

	function updateLink(link) {
		link.setAttribute('href', resolvedBrowserUrl(link));
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
