export function mountSettingsApp(dependencies) {
	var $ = dependencies.$;
	var wp = dependencies.wp;
	var e = dependencies.createElement;
	var App = dependencies.App;
	var SettingsLoader = dependencies.SettingsLoader;

	$(document).ready(function () {
		var root = document.getElementById('zmsh-react-settings-root');
		var mountApp;
		if (!root) {
			return;
		}
		if (typeof wp.element.createRoot === 'function') {
			var reactRoot = wp.element.createRoot(root);
			reactRoot.render(e(SettingsLoader));
			mountApp = function () {
				reactRoot.render(e(App));
			};
			if (typeof window.requestAnimationFrame === 'function') {
				window.requestAnimationFrame(mountApp);
			} else {
				mountApp();
			}
			return;
		}
		if (typeof wp.element.render === 'function') {
			wp.element.render(e(SettingsLoader), root);
			mountApp = function () {
				wp.element.render(e(App), root);
			};
			if (typeof window.requestAnimationFrame === 'function') {
				window.requestAnimationFrame(mountApp);
			} else {
				mountApp();
			}
		}
	});
}
