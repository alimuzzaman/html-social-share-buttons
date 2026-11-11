import { createClipboardController } from './share-clipboard';
import { resolvedBrowserUrl } from './share-browser-url';

(function () {
	'use strict';
	var clipboard = createClipboardController();
	var pending = false;

	function supported() {
		return window.isSecureContext === true && window.navigator && typeof window.navigator.share === 'function';
	}

	function initialize() {
		var nodes = document.querySelectorAll('[data-hssb-native-share="1"]');
		for (var index = 0; index < nodes.length; index++) {
			var node = nodes[index];
			if (node.getAttribute('data-hssb-native-ready') === '1') {
				continue;
			}
			if (!supported()) {
				// Explicit Copy link already provides this group's fallback.
				continue;
			}
			var button = document.createElement('button');
			for (var attribute = 0; attribute < node.attributes.length; attribute++) {
				var item = node.attributes[attribute];
				if (item.name.indexOf('data-hssb-') === 0) {
					button.setAttribute(item.name, item.value);
				}
			}
			button.setAttribute('type', 'button');
			button.className = 'native hssb-native-button';
			button.setAttribute('data-hssb-native-ready', '1');
			button.setAttribute('data-hssb-native-mode', 'native');
			button.setAttribute('aria-label', node.getAttribute('data-hssb-share-label'));
			// Preserve the resolved link even when browser-URL mode is disabled.
			button.setAttribute('data-hssb-native-url', node.getAttribute('href') || node.getAttribute('data-hssb-native-url') || '');
			node.parentNode.replaceChild(button, node);
		}
	}

	function findControl(target) {
		while (target && target !== document) {
			if (target.getAttribute && target.getAttribute('data-hssb-native-share') === '1') {
				return target;
			}
			target = target.parentNode;
		}
		return null;
	}

	function failed(control, error) {
		if (error && error.name === 'AbortError') {
			return;
		}
		clipboard.announce(control.getAttribute('data-hssb-native-failed-label'));
		if (control.getAttribute('data-hssb-native-has-copy') !== '1') {
			control.setAttribute('data-hssb-native-mode', 'copy');
			control.setAttribute('aria-label', control.getAttribute('data-hssb-copy-label'));
		}
	}

	document.addEventListener('click', function (event) {
		var control = findControl(event.target);
		if (!control || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
			return;
		}
		event.preventDefault();
		if (pending) {
			return;
		}
		var url = resolvedBrowserUrl(control);
		// Do not send non-page schemes to an OS target or silently navigate.
		try {
			var parsed = new URL(url);
			if (parsed.protocol !== 'https:' && parsed.protocol !== 'http:') {
				throw new Error('Unsupported page URL');
			}
		} catch {
			clipboard.announce(control.getAttribute('data-hssb-copy-failed-label'));
			return;
		}
		if (!supported() || control.getAttribute('data-hssb-native-mode') === 'copy') {
			clipboard.copy(control, url);
			return;
		}
		var payload = { title: control.getAttribute('data-hssb-native-title') || '', url: url };
		try {
			if (typeof window.navigator.canShare === 'function' && !window.navigator.canShare(payload)) {
				throw new Error('Unsupported share data');
			}
			pending = true;
			control.setAttribute('aria-busy', 'true');
			// Keep the API call in this activation; do not await any preparatory work.
			Promise.resolve(window.navigator.share(payload)).then(function () {
				pending = false;
				control.removeAttribute('aria-busy');
			}, function (error) {
				pending = false;
				control.removeAttribute('aria-busy');
				failed(control, error);
			});
		} catch (error) {
			pending = false;
			control.removeAttribute('aria-busy');
			failed(control, error);
		}
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initialize, { once: true });
	} else {
		initialize();
	}
	if (typeof window.MutationObserver === 'function' && document.documentElement) {
		new window.MutationObserver(initialize).observe(document.documentElement, { childList: true, subtree: true });
	}
}());

export {};
