(function () {
	'use strict';

	var COPIED_CLASS = 'is-copied';
	var FAILED_CLASS = 'is-copy-failed';
	var FEEDBACK_MS = 2000;
	var liveRegion;
	var styled = false;

	// The legacy appearance loads no shared stylesheet, so the feedback rules
	// travel with the script that needs them.
	function addStyles() {
		var style;
		if (styled) {
			return;
		}
		styled = true;
		style = document.createElement('style');
		style.textContent = '.hssb-copy-status{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0}' +
			'.zmshbt a.copy{position:relative}' +
			'.zmshbt a.copy.is-copied::after,.zmshbt a.copy.is-copy-failed::after{content:attr(data-hssb-feedback);position:absolute;left:50%;bottom:100%;transform:translate(-50%,-4px);padding:2px 6px;border-radius:3px;background:#1d2327;color:#fff;font:12px/1.4 sans-serif;white-space:nowrap;pointer-events:none;z-index:10}';
		document.head.appendChild(style);
	}

	function announce(message) {
		if (!liveRegion) {
			liveRegion = document.createElement('span');
			liveRegion.className = 'hssb-copy-status';
			liveRegion.setAttribute('role', 'status');
			liveRegion.setAttribute('aria-live', 'polite');
			document.body.appendChild(liveRegion);
		}
		// Clear first so repeating the same message is announced again.
		liveRegion.textContent = '';
		window.setTimeout(function () {
			liveRegion.textContent = message;
		}, 50);
	}

	function legacyCopy(text) {
		var field = document.createElement('textarea');
		var copied = false;
		field.value = text;
		field.setAttribute('readonly', '');
		field.style.position = 'fixed';
		field.style.top = '-1000px';
		field.style.opacity = '0';
		document.body.appendChild(field);
		field.select();
		try {
			copied = document.execCommand('copy');
		} catch {
			copied = false;
		}
		document.body.removeChild(field);
		return copied;
	}

	function copyText(text) {
		if (window.navigator && window.navigator.clipboard && typeof window.navigator.clipboard.writeText === 'function' && window.isSecureContext !== false) {
			return window.navigator.clipboard.writeText(text).then(function () {
				return true;
			}, function () {
				return legacyCopy(text);
			});
		}
		return Promise.resolve(legacyCopy(text));
	}

	function showFeedback(link, copied) {
		var label = copied
			? link.getAttribute('data-hssb-copied-label')
			: link.getAttribute('data-hssb-copy-failed-label');
		addStyles();
		link.classList.remove(COPIED_CLASS, FAILED_CLASS);
		if (label) {
			link.setAttribute('data-hssb-feedback', label);
			announce(label);
		}
		link.classList.add(copied ? COPIED_CLASS : FAILED_CLASS);
		window.setTimeout(function () {
			link.classList.remove(COPIED_CLASS, FAILED_CLASS);
		}, FEEDBACK_MS);
	}

	function findLink(target) {
		while (target && target !== document) {
			if (target.getAttribute && target.getAttribute('data-hssb-copy-link') === '1') {
				return target;
			}
			target = target.parentNode;
		}
		return null;
	}

	document.addEventListener('click', function (event) {
		var link = findLink(event.target);
		var url;
		// Leave modified and non-primary clicks to the browser (open in new tab, etc.).
		if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
			return;
		}
		url = link.getAttribute('href');
		if (!url) {
			return;
		}
		event.preventDefault();
		copyText(url).then(function (copied) {
			if (!copied && typeof window.prompt === 'function') {
				// Last resort: show the URL selected so the visitor can copy it by hand.
				// eslint-disable-next-line no-alert
				window.prompt(link.getAttribute('aria-label') || '', url);
			}
			showFeedback(link, copied);
		});
	});
}());

export {};
