export function attachModalBehavior(App, dependencies) {
	var findIconset = dependencies.findIconset;
	var ensureType = dependencies.ensureType;

	App.prototype.openModal = function (mode, trigger) {
		var options = this.state.options;
		var currentIconset = findIconset(options.iconset);
		var modalType = ensureType(currentIconset, this.state.modalType || options.show_left || 'square');
		this.modalTrigger = trigger || null;
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
		if (this.modalTrigger && this.modalTrigger.focus) {
			this.modalTrigger.focus();
		}
		this.modalTrigger = null;
	};

	App.prototype.handleModalKeyDown = function (event) {
		var panel;
		var focusable;
		var first;
		var last;
		var activeElement;

		if (event.key === 'Escape') {
			event.preventDefault();
			this.closeModal();
			return;
		}
		if (event.key !== 'Tab') {
			return;
		}

		panel = event.currentTarget;
		activeElement = panel && panel.ownerDocument ? panel.ownerDocument.activeElement : null;
		focusable = panel && panel.querySelectorAll ? panel.querySelectorAll('button:not([disabled]), select, textarea, input:not([disabled]), [contenteditable="true"], [tabindex]:not([tabindex="-1"])') : [];
		if (!focusable || !focusable.length) {
			return;
		}
		first = focusable[0];
		last = focusable[focusable.length - 1];
		if (event.shiftKey && activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	};
}
