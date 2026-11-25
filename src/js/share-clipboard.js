export function createClipboardController() {
	var COPIED_CLASS = 'is-copied';
	var FAILED_CLASS = 'is-copy-failed';
	var FEEDBACK_MS = 2000;
	var liveRegion;
	var styled = false;
	var announceTimer = 0;
	var feedbackTimers = typeof WeakMap === 'function' ? new WeakMap() : null;

	// The legacy appearance loads no shared stylesheet, so the feedback rules
	// travel with the script that needs them.
	function addStyles() {
		var style;
		if (styled || !document.head) {
			return;
		}
		styled = true;
		if (typeof document.getElementById === 'function' && document.getElementById('hssb-copy-status-style')) {
			return;
		}
		style = document.createElement('style');
		style.id = 'hssb-copy-status-style';
		style.textContent = '.hssb-copy-status{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0}' +
			'.zmshbt a.copy,.zmshbt .native{position:relative}' +
			'.zmshbt a.copy.is-copied::after,.zmshbt a.copy.is-copy-failed::after,.zmshbt .native.is-copied::after,.zmshbt .native.is-copy-failed::after{content:attr(data-hssb-feedback);position:absolute;left:50%;bottom:100%;transform:translate(-50%,-4px);padding:2px 6px;border-radius:3px;background:#1d2327;color:#fff;font:12px/1.4 sans-serif;white-space:nowrap;pointer-events:none;z-index:10}';
		document.head.appendChild(style);
	}

	function announce(message) {
		if (!liveRegion && typeof document.getElementById === 'function') {
			liveRegion = document.getElementById('hssb-copy-live-region');
		}
		if (!liveRegion) {
			liveRegion = document.createElement('span');
			liveRegion.id = 'hssb-copy-live-region';
			liveRegion.className = 'hssb-copy-status';
			liveRegion.setAttribute('role', 'status');
			liveRegion.setAttribute('aria-live', 'polite');
			document.body.appendChild(liveRegion);
		}
		// Clear first so repeating the same message is announced again.
		liveRegion.textContent = '';
		if (announceTimer && typeof window.clearTimeout === 'function') {
			window.clearTimeout(announceTimer);
		}
		announceTimer = window.setTimeout(function () {
			announceTimer = 0;
			liveRegion.textContent = message;
		}, 50);
	}

	function legacyCopy(text) {
		var field = document.createElement('textarea');
		var copied = false;
		var ownerDocument = field.ownerDocument || document;
		var focused = ownerDocument.activeElement;
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
		if (focused && typeof focused.focus === 'function') {
			focused.focus();
		}
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
		var previousTimer;
		var nextTimer;
		addStyles();
		if (feedbackTimers && typeof window.clearTimeout === 'function') {
			previousTimer = feedbackTimers.get(link);
			if (previousTimer) {
				window.clearTimeout(previousTimer);
			}
		}
		link.classList.remove(COPIED_CLASS, FAILED_CLASS);
		if (label) {
			link.setAttribute('data-hssb-feedback', label);
			announce(label);
		}
		link.classList.add(copied ? COPIED_CLASS : FAILED_CLASS);
		nextTimer = window.setTimeout(function () {
			if (feedbackTimers) {
				feedbackTimers.delete(link);
			}
			link.classList.remove(COPIED_CLASS, FAILED_CLASS);
		}, FEEDBACK_MS);
		if (feedbackTimers) {
			feedbackTimers.set(link, nextTimer);
		}
	}

	return {
		announce: function (message) { addStyles(); announce(message); },
		copy: function (link, url) {
			return copyText(url).then(function (copied) {
				if (!copied && typeof window.prompt === 'function') {
					// eslint-disable-next-line no-alert
					window.prompt(link.getAttribute('aria-label') || '', url);
				}
				showFeedback(link, copied);
			});
		}
	};
}
