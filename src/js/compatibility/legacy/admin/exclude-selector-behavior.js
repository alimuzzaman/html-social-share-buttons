import { excludeIds, excludeToken } from './settings-model';

export function attachExcludeSelectorBehavior(App, dependencies) {
	var $ = dependencies.$;
	var data = dependencies.data;

	App.prototype.updateExcludeTokens = function (tokens) {
		var known = {};
		(this.state.excludeItems || []).forEach(function (item) {
			known[excludeToken(item)] = item;
		});
		(this.state.excludeSuggestionItems || []).forEach(function (item) {
			known[excludeToken(item)] = item;
		});
		(this.state.excludeSuggestions || []).forEach(function (token) {
			if (!known[token]) {
				known[token] = { id: token, token: token, custom: true };
			}
		});

		var selected = [];
		(tokens || []).forEach(function (token) {
			selected.push(known[token] || { id: token, token: token, custom: true });
		});

		this.setState({
			excludeItems: selected,
			options: $.extend({}, this.state.options, { excludes: excludeIds(selected) })
		});
	};

	App.prototype.searchExcludeContent = function (query) {
		var self = this;
		if (this.excludeSearchTimer) {
			window.clearTimeout(this.excludeSearchTimer);
		}
		if (this.excludeSearchRequest && this.excludeSearchRequest.abort) {
			this.excludeSearchRequest.abort();
			this.excludeSearchRequest = null;
		}
		if (String(query || '').trim().length < 2) {
			this.setState({ excludeSuggestions: [], excludeSuggestionItems: [] });
			return;
		}
		this.excludeSearchTimer = window.setTimeout(function () {
			var request = $.post(data.ajax_url, {
				action: 'zm_sh_search_content',
				nonce: data.nonce,
				query: query || ''
			}).done(function (response) {
				if (request !== self.excludeSearchRequest) {
					return;
				}
				if (response && response.success && response.data) {
					self.setState({
						excludeSuggestions: response.data.map(excludeToken),
						excludeSuggestionItems: response.data
					});
				}
			}).always(function () {
				if (request === self.excludeSearchRequest) {
					self.excludeSearchRequest = null;
				}
			});
			self.excludeSearchRequest = request;
		}, 250);
	};
}
