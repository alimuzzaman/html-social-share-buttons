import { serializeShareTemplateParameters } from '../share-template';

export function attachTemplatePreviewBehavior(App, dependencies) {
	var $ = dependencies.$;
	var data = dependencies.data;
	var e = dependencies.createElement;
	var Button = dependencies.Button;
	var text = dependencies.text;

	App.prototype.previewTemplateValue = function (platform) {
		var override = (this.state.shareTemplateOverrides || {})[platform] || '';
		return String(override).trim() ? this.getTemplateParts(platform).current.prefix + serializeShareTemplateParameters(this.getShareTemplateParameters(platform)) : '';
	};

	App.prototype.setTemplatePreview = function (platform, preview) {
		this.setState(function (previous) {
			var previews = $.extend({}, previous.templatePreviews || {});
			previews[platform] = preview;
			return { templatePreviews: previews };
		});
	};

	App.prototype.invalidateTemplatePreview = function (platform) {
		var request = this.templatePreviewRequests[platform];
		delete this.templatePreviewRequests[platform];
		if (request && request.abort) {
			request.abort();
		}
		this.setTemplatePreview(platform, { status: 'idle' });
	};

	App.prototype.disposeTemplatePreviews = function () {
		var requests = this.templatePreviewRequests;
		this.templatePreviewRequests = {};
		Object.keys(requests).forEach(function (platform) {
			if (requests[platform].abort) {
				requests[platform].abort();
			}
		});
	};

	App.prototype.previewShareTemplate = function (platform) {
		var self = this;
		if (this.templatePreviewRequests[platform]) {
			return;
		}
		if (!data.ajax_url || !data.nonce) {
			this.setTemplatePreview(platform, { status: 'error' });
			return;
		}
		this.setTemplatePreview(platform, { status: 'loading' });
		var request = $.ajax({
			url: data.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'hssb_preview_share_template',
				nonce: data.nonce,
				network: platform,
				template: this.previewTemplateValue(platform)
			}
		});
		this.templatePreviewRequests[platform] = request;
		request.done(function (response) {
			if (self.templatePreviewRequests[platform] !== request) {
				return;
			}
			var result = response && response.success && response.data;
			if (!result || result.network !== platform || typeof result.resolved_url !== 'string' || !Array.isArray(result.diagnostics)) {
				self.setTemplatePreview(platform, { status: 'error' });
				return;
			}
			self.setTemplatePreview(platform, {
				status: 'result',
				url: result.resolved_url,
				diagnostics: result.diagnostics.filter(function (item) { return item && typeof item.message === 'string'; }),
				usesDefault: !!result.uses_default
			});
		}).fail(function () {
			if (self.templatePreviewRequests[platform] === request) {
				self.setTemplatePreview(platform, { status: 'error' });
			}
		}).always(function () {
			if (self.templatePreviewRequests[platform] === request) {
				delete self.templatePreviewRequests[platform];
			}
		});
	};

	App.prototype.renderTemplatePreview = function (platform) {
		var self = this;
		var preview = (this.state.templatePreviews || {})[platform] || { status: 'idle' };
		var samples = data.share_template_preview_samples || {};
		var loading = preview.status === 'loading';
		return e('div', { key: 'preview', className: 'zm_template_preview' }, [
			e(Button, {
				key: 'button',
				type: 'button',
				isSecondary: true,
				disabled: loading,
				'aria-controls': 'zm-template-preview-' + platform,
				onClick: function () { self.previewShareTemplate(platform); }
			}, text('previewSample', 'Preview sample')),
			e('p', { key: 'help', className: 'components-base-control__help' }, text('previewHelp', 'Uses sample content without saving or opening a share service. Live URLs may differ if another plugin customizes sharing.')),
			e('details', { key: 'samples' }, [
				e('summary', { key: 'label' }, text('previewSampleContent', 'Sample content')),
				e('dl', { key: 'values' }, Object.keys(samples).map(function (token) {
					return e('div', { key: token }, [
						e('dt', { key: 'token' }, e('code', null, '%%' + token + '%%')),
						e('dd', { key: 'value' }, samples[token])
					]);
				}))
			]),
			e('div', {
				key: 'result', id: 'zm-template-preview-' + platform,
				className: 'zm_template_preview_result', role: 'status',
				'aria-live': 'polite', 'aria-atomic': true, 'aria-busy': loading
			}, [
				loading ? e('p', { key: 'loading' }, text('previewLoading', 'Preparing preview…')) : null,
				preview.status === 'error' ? e('p', { key: 'error' }, text('previewError', 'Preview could not be loaded. Try again. Your settings have not changed.')) : null,
				preview.status === 'result' ? e('div', { key: 'resolved' }, [
					e('p', { key: 'label' }, preview.usesDefault ? text('previewDefault', 'Resolved URL using the default template:') : text('previewResolved', 'Resolved URL:')),
					preview.url ? e('code', { key: 'url', className: 'zm_template_preview_url' }, preview.url) : e('p', { key: 'empty' }, text('previewEmpty', 'This template does not produce a usable link.')),
					preview.diagnostics.length ? e('ul', { key: 'diagnostics' }, preview.diagnostics.map(function (item, index) {
						return e('li', { key: index }, item.message);
					})) : null
				]) : null
			])
		]);
	};
}
