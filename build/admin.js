/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/.pnpm/react-dom@18.3.1_react@18.3.1/node_modules/react-dom/client.js":
/*!*******************************************************************************************!*\
  !*** ./node_modules/.pnpm/react-dom@18.3.1_react@18.3.1/node_modules/react-dom/client.js ***!
  \*******************************************************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


var m = __webpack_require__(/*! react-dom */ "react-dom");
if (false) // removed by dead control flow
{} else {
  var i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
  exports.createRoot = function(c, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.createRoot(c, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
  exports.hydrateRoot = function(c, h, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.hydrateRoot(c, h, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
}


/***/ }),

/***/ "./src/admin-ui/App.tsx":
/*!******************************!*\
  !*** ./src/admin-ui/App.tsx ***!
  \******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* binding */ App; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/ui */ "./src/admin-ui/components/ui/index.ts");
/* harmony import */ var _components_tabs__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/tabs */ "./src/admin-ui/components/tabs/index.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);


// Import tab components


const tabs = [{
  id: 'general',
  title: 'General',
  icon: 'dashicons-admin-settings',
  description: 'Basic plugin settings and display options'
}, {
  id: 'networks',
  title: 'Networks',
  icon: 'dashicons-share',
  description: 'Configure social media networks and sharing options'
}, {
  id: 'profiles',
  title: 'Profiles',
  icon: 'dashicons-groups',
  description: 'Manage button profiles and configurations'
}, {
  id: 'integrations',
  title: 'Integrations',
  icon: 'dashicons-admin-plugins',
  description: 'Third-party plugin integrations'
}, {
  id: 'appearance',
  title: 'Appearance',
  icon: 'dashicons-art',
  description: 'Style and appearance customization'
}, {
  id: 'placement',
  title: 'Placement',
  icon: 'dashicons-location',
  description: 'Configure where buttons appear'
}, {
  id: 'shortcode',
  title: 'Shortcode',
  icon: 'dashicons-editor-code',
  description: 'Generate and customize shortcodes'
}];
const App = () => {
  const [activeTab, setActiveTab] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('general');
  const renderTabContent = () => {
    switch (activeTab) {
      case 'general':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.GeneralTab, {});
      case 'networks':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.NetworksTab, {});
      case 'profiles':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.ProfilesTab, {});
      case 'integrations':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.IntegrationsTab, {});
      case 'appearance':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.AppearanceTab, {});
      case 'placement':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.PlacementTab, {});
      case 'shortcode':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.ShortcodeTab, {});
      default:
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_tabs__WEBPACK_IMPORTED_MODULE_2__.GeneralTab, {});
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: "wrap html-social-share-admin",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h1", {
      className: "wp-heading-inline",
      children: "HTML Social Share Buttons"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("hr", {
      className: "wp-header-end"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      className: "html-social-share-tabs-wrapper",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_ui__WEBPACK_IMPORTED_MODULE_1__.Tabs, {
        tabs: tabs,
        activeTab: activeTab,
        onTabChange: setActiveTab,
        className: "mb-6"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        className: "tab-content",
        children: tabs.map(tab => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_ui__WEBPACK_IMPORTED_MODULE_1__.TabPanel, {
          id: tab.id,
          activeTab: activeTab,
          children: renderTabContent()
        }, tab.id))
      })]
    })]
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/AppearanceTab.tsx":
/*!********************************************************!*\
  !*** ./src/admin-ui/components/tabs/AppearanceTab.tsx ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AppearanceTab: function() { return /* binding */ AppearanceTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const AppearanceTab = () => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    className: "appearance-tab",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "wp-admin-card p-6",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("h2", {
        className: "text-xl font-semibold mb-4",
        children: "Appearance Settings"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
        className: "text-wp-gray-600 mb-6",
        children: "Customize the visual appearance of your social share buttons."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: "bg-wp-gray-50 p-4 rounded-lg",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
          className: "text-center text-wp-gray-600",
          children: "Appearance customization component coming soon..."
        })
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/GeneralTab.tsx":
/*!*****************************************************!*\
  !*** ./src/admin-ui/components/tabs/GeneralTab.tsx ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GeneralTab: function() { return /* binding */ GeneralTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../ui */ "./src/admin-ui/components/ui/index.ts");
/* harmony import */ var _contexts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../contexts */ "./src/admin-ui/contexts/index.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);





const GeneralTab = () => {
  const {
    settings: apiSettings,
    updateSettings,
    saveSettings,
    saving,
    error
  } = (0,_contexts__WEBPACK_IMPORTED_MODULE_2__.useSettingsContext)();
  const {
    showSuccess,
    showError
  } = (0,_contexts__WEBPACK_IMPORTED_MODULE_2__.useNotifications)();

  // Local state for form handling - sync with API settings when available
  const [localSettings, setLocalSettings] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)({
    show_on_front_page: true,
    show_on_posts: true,
    show_on_pages: false,
    show_on_archives: false,
    default_style: 'default',
    default_size: 'medium'
  }); // Sync local settings with API settings when they load
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (apiSettings) {
      var _apiSettings$show_on_, _apiSettings$show_on_2, _apiSettings$show_on_3, _apiSettings$show_on_4, _apiSettings$default_, _apiSettings$default_2;
      setLocalSettings({
        show_on_front_page: (_apiSettings$show_on_ = apiSettings.show_on_front_page) !== null && _apiSettings$show_on_ !== void 0 ? _apiSettings$show_on_ : true,
        show_on_posts: (_apiSettings$show_on_2 = apiSettings.show_on_posts) !== null && _apiSettings$show_on_2 !== void 0 ? _apiSettings$show_on_2 : true,
        show_on_pages: (_apiSettings$show_on_3 = apiSettings.show_on_pages) !== null && _apiSettings$show_on_3 !== void 0 ? _apiSettings$show_on_3 : false,
        show_on_archives: (_apiSettings$show_on_4 = apiSettings.show_on_archives) !== null && _apiSettings$show_on_4 !== void 0 ? _apiSettings$show_on_4 : false,
        default_style: (_apiSettings$default_ = apiSettings.default_style) !== null && _apiSettings$default_ !== void 0 ? _apiSettings$default_ : 'default',
        default_size: (_apiSettings$default_2 = apiSettings.default_size) !== null && _apiSettings$default_2 !== void 0 ? _apiSettings$default_2 : 'medium'
      });
    }
  }, [apiSettings]);

  // Use local settings for form state
  const settings = localSettings;
  const styleOptions = [{
    value: 'default',
    label: 'Default Style'
  }, {
    value: 'minimal',
    label: 'Minimal Style'
  }, {
    value: 'rounded',
    label: 'Rounded Style'
  }, {
    value: 'square',
    label: 'Square Style'
  }];
  const sizeOptions = [{
    value: 'small',
    label: 'Small'
  }, {
    value: 'medium',
    label: 'Medium'
  }, {
    value: 'large',
    label: 'Large'
  }];
  const updateSetting = (key, value) => {
    setLocalSettings(prev => ({
      ...prev,
      [key]: value
    }));
  };
  const handleSave = async () => {
    try {
      // Use API if available, otherwise simulate save
      if (apiSettings && updateSettings && saveSettings) {
        await updateSettings(localSettings);
        await saveSettings();
        showSuccess('Settings saved successfully!');
      } else {
        // Fallback simulation
        await new Promise(resolve => setTimeout(resolve, 1000));
        showSuccess('Settings saved successfully!');
      }
    } catch (error) {
      showError('Failed to save settings', 'Please try again or contact support if the problem persists.');
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.LoadingOverlay, {
    isLoading: saving,
    message: "Saving settings...",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
      className: "general-tab",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        className: "wp-admin-card p-6",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h2", {
          className: "text-xl font-semibold mb-4",
          children: "General Settings"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
          className: "text-wp-gray-600 mb-6",
          children: "Configure where the social share buttons should appear by default."
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          className: "grid grid-cols-1 md:grid-cols-2 gap-6",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
            className: "space-y-4",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h3", {
              className: "text-lg font-medium text-wp-gray-800 mb-3",
              children: "Display Options"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.FormField, {
              label: "Show on Front Page",
              description: "Display social share buttons on your homepage",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                checked: settings.show_on_front_page || false,
                onChange: checked => updateSetting('show_on_front_page', checked),
                label: "Enable on front page"
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.FormField, {
              label: "Show on Posts",
              description: "Display social share buttons on blog posts",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                checked: settings.show_on_posts || false,
                onChange: checked => updateSetting('show_on_posts', checked),
                label: "Enable on posts"
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.FormField, {
              label: "Show on Pages",
              description: "Display social share buttons on static pages",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                checked: settings.show_on_pages || false,
                onChange: checked => updateSetting('show_on_pages', checked),
                label: "Enable on pages"
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.FormField, {
              label: "Show on Archives",
              description: "Display social share buttons on archive pages",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                checked: settings.show_on_archives || false,
                onChange: checked => updateSetting('show_on_archives', checked),
                label: "Enable on archives"
              })
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
            className: "space-y-4",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h3", {
              className: "text-lg font-medium text-wp-gray-800 mb-3",
              children: "Default Appearance"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.FormField, {
              label: "Default Style",
              description: "Choose the default button style for new instances",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Select, {
                value: settings.default_style || 'default',
                onChange: value => updateSetting('default_style', value),
                options: styleOptions
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.FormField, {
              label: "Default Size",
              description: "Choose the default button size for new instances",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Select, {
                value: settings.default_size || 'medium',
                onChange: value => updateSetting('default_size', value),
                options: sizeOptions
              })
            })]
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          className: "mt-8 pt-4 border-t border-wp-gray-200",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
            className: "flex justify-between items-center",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
              className: "text-sm text-wp-gray-600",
              children: "These settings will be applied as defaults for new button instances."
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
              onClick: handleSave,
              loading: saving,
              variant: "primary",
              children: "Save Changes"
            })]
          })
        })]
      })
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/IntegrationsTab.tsx":
/*!**********************************************************!*\
  !*** ./src/admin-ui/components/tabs/IntegrationsTab.tsx ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   IntegrationsTab: function() { return /* binding */ IntegrationsTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const IntegrationsTab = () => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    className: "integrations-tab",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "wp-admin-card p-6",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("h2", {
        className: "text-xl font-semibold mb-4",
        children: "Plugin Integrations"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
        className: "text-wp-gray-600 mb-6",
        children: "Configure integrations with other WordPress plugins and services."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: "bg-wp-gray-50 p-4 rounded-lg",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
          className: "text-center text-wp-gray-600",
          children: "Integrations management component coming soon..."
        })
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/NetworksTab.tsx":
/*!******************************************************!*\
  !*** ./src/admin-ui/components/tabs/NetworksTab.tsx ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NetworksTab: function() { return /* binding */ NetworksTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../ui */ "./src/admin-ui/components/ui/index.ts");
/* harmony import */ var _contexts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../contexts */ "./src/admin-ui/contexts/index.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);





const defaultNetworks = [{
  id: 'facebook',
  name: 'Facebook',
  label: 'Facebook',
  share_url: 'https://www.facebook.com/sharer/sharer.php?u={url}',
  requires_handle: false,
  icon_class: 'fab fa-facebook-f',
  color: '#1877f2'
}, {
  id: 'twitter',
  name: 'Twitter',
  label: 'Twitter',
  share_url: 'https://twitter.com/intent/tweet?url={url}&text={title}',
  requires_handle: false,
  icon_class: 'fab fa-twitter',
  color: '#1da1f2'
}, {
  id: 'linkedin',
  name: 'LinkedIn',
  label: 'LinkedIn',
  share_url: 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
  requires_handle: false,
  icon_class: 'fab fa-linkedin-in',
  color: '#0077b5'
}, {
  id: 'pinterest',
  name: 'Pinterest',
  label: 'Pinterest',
  share_url: 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
  requires_handle: false,
  icon_class: 'fab fa-pinterest-p',
  color: '#bd081c'
}, {
  id: 'reddit',
  name: 'Reddit',
  label: 'Reddit',
  share_url: 'https://reddit.com/submit?url={url}&title={title}',
  requires_handle: false,
  icon_class: 'fab fa-reddit-alien',
  color: '#ff4500'
}, {
  id: 'whatsapp',
  name: 'WhatsApp',
  label: 'WhatsApp',
  share_url: 'https://wa.me/?text={title}%20{url}',
  requires_handle: false,
  icon_class: 'fab fa-whatsapp',
  color: '#25d366'
}];
const NetworksTab = () => {
  const {
    networks: apiNetworks,
    updateNetwork,
    loading
  } = (0,_contexts__WEBPACK_IMPORTED_MODULE_2__.useNetworksContext)();
  const {
    showSuccess,
    showError
  } = (0,_contexts__WEBPACK_IMPORTED_MODULE_2__.useNotifications)();

  // Keep local state for immediate UI updates and form handling
  const [localNetworks] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(defaultNetworks);
  const [enabledNetworks, setEnabledNetworks] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(['facebook', 'twitter', 'linkedin']);
  const [isSaving, setIsSaving] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false); // Use API networks if available, otherwise fall back to local defaults
  const networks = apiNetworks.length > 0 ? apiNetworks : localNetworks;
  const handleNetworkToggle = (networkId, enabled) => {
    if (enabled) {
      setEnabledNetworks(prev => [...prev, networkId]);
    } else {
      setEnabledNetworks(prev => prev.filter(id => id !== networkId));
    }
  };
  const handleNetworkLabelChange = async (networkId, label) => {
    try {
      // Update via API if available
      if (apiNetworks.length > 0) {
        await updateNetwork(networkId, {
          label
        });
      }
      showSuccess(`${label} label updated!`);
    } catch (error) {
      showError('Failed to update network label', 'Please try again.');
    }
  };
  const handleSave = async () => {
    setIsSaving(true);
    try {
      // Save enabled networks configuration
      const networkUpdates = networks.map(network => ({
        ...network,
        enabled: enabledNetworks.includes(network.id)
      }));

      // If API is available, save via API
      if (apiNetworks.length > 0) {
        await Promise.all(networkUpdates.map(network => updateNetwork(network.id, {
          enabled: network.enabled
        })));
      }
      showSuccess('Network settings saved successfully!');
    } catch (error) {
      showError('Failed to save settings', 'Please try again.');
    } finally {
      setIsSaving(false);
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
    className: "networks-tab",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      className: "wp-admin-card p-6",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h2", {
        className: "text-xl font-semibold mb-4",
        children: "Social Networks"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
        className: "text-wp-gray-600 mb-6",
        children: "Choose which social networks to make available for sharing and customize their appearance."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        className: "space-y-4",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h3", {
          className: "text-lg font-medium text-wp-gray-800 mb-3",
          children: "Available Networks"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4",
          children: networks.map(network => {
            const isEnabled = enabledNetworks.includes(network.id);
            return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
              className: `wp-network-card border rounded-lg p-4 ${isEnabled ? 'border-wp-blue-500 bg-wp-blue-50' : 'border-wp-gray-200'}`,
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                className: "flex items-center mb-3",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
                  className: "w-8 h-8 rounded flex items-center justify-center mr-3",
                  style: {
                    backgroundColor: network.color
                  },
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("i", {
                    className: `${network.icon_class} text-white text-sm`
                  })
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
                  className: "flex-1",
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h4", {
                    className: "font-medium text-wp-gray-800",
                    children: network.name
                  })
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                  checked: isEnabled,
                  onChange: checked => handleNetworkToggle(network.id, checked),
                  label: ""
                })]
              }), isEnabled && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
                className: "mt-3",
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.FormField, {
                  label: "Button Label",
                  description: "Text displayed on the button",
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.TextInput, {
                    value: network.label,
                    onChange: value => handleNetworkLabelChange(network.id, value),
                    placeholder: network.name
                  })
                })
              })]
            }, network.id);
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          className: "mt-6 p-4 bg-wp-gray-50 rounded-lg",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h4", {
            className: "font-medium text-wp-gray-800 mb-2",
            children: "Network Order"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
            className: "text-sm text-wp-gray-600 mb-3",
            children: "Drag and drop to reorder the networks as they will appear on your site."
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
            className: "flex flex-wrap gap-2",
            children: enabledNetworks.map(networkId => {
              const network = networks.find(n => n.id === networkId);
              if (!network) return null;
              return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                className: "flex items-center px-3 py-1 bg-white border border-wp-gray-200 rounded cursor-move",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
                  className: "w-4 h-4 rounded mr-2",
                  style: {
                    backgroundColor: network.color
                  }
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                  className: "text-sm",
                  children: network.label
                })]
              }, networkId);
            })
          })]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        className: "mt-8 pt-4 border-t border-wp-gray-200",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          className: "flex justify-between items-center",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("p", {
            className: "text-sm text-wp-gray-600",
            children: [enabledNetworks.length, " network", enabledNetworks.length !== 1 ? 's' : '', " enabled"]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
            onClick: handleSave,
            loading: isSaving,
            variant: "primary",
            children: "Save Changes"
          })]
        })
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/PlacementTab.tsx":
/*!*******************************************************!*\
  !*** ./src/admin-ui/components/tabs/PlacementTab.tsx ***!
  \*******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PlacementTab: function() { return /* binding */ PlacementTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const PlacementTab = () => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    className: "placement-tab",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "wp-admin-card p-6",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("h2", {
        className: "text-xl font-semibold mb-4",
        children: "Button Placement"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
        className: "text-wp-gray-600 mb-6",
        children: "Configure where and how social share buttons appear on your site."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: "bg-wp-gray-50 p-4 rounded-lg",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
          className: "text-center text-wp-gray-600",
          children: "Placement configuration component coming soon..."
        })
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/ProfilesTab.tsx":
/*!******************************************************!*\
  !*** ./src/admin-ui/components/tabs/ProfilesTab.tsx ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ProfilesTab: function() { return /* binding */ ProfilesTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../ui */ "./src/admin-ui/components/ui/index.ts");
/* harmony import */ var _contexts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../contexts */ "./src/admin-ui/contexts/index.ts");
/* harmony import */ var _utils_validation__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../utils/validation */ "./src/admin-ui/utils/validation.ts");
/* harmony import */ var _utils_validation__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_utils_validation__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





// Default networks available for profiles

const availableNetworks = [{
  id: 'facebook',
  name: 'Facebook',
  icon: 'fab fa-facebook-f'
}, {
  id: 'twitter',
  name: 'Twitter',
  icon: 'fab fa-twitter'
}, {
  id: 'linkedin',
  name: 'LinkedIn',
  icon: 'fab fa-linkedin-in'
}, {
  id: 'pinterest',
  name: 'Pinterest',
  icon: 'fab fa-pinterest-p'
}, {
  id: 'reddit',
  name: 'Reddit',
  icon: 'fab fa-reddit-alien'
}, {
  id: 'whatsapp',
  name: 'WhatsApp',
  icon: 'fab fa-whatsapp'
}];

// Sample profiles for demo (will be replaced with API data)
const sampleProfiles = [{
  id: '1',
  name: 'Default Profile',
  networks: {
    facebook: {
      enabled: true
    },
    twitter: {
      enabled: true,
      handle: '@example'
    },
    linkedin: {
      enabled: true
    }
  },
  display_settings: {
    style: 'default',
    size: 'medium',
    text_labels: false,
    icon_only: true
  }
}, {
  id: '2',
  name: 'Blog Posts',
  networks: {
    facebook: {
      enabled: true
    },
    twitter: {
      enabled: true,
      handle: '@blogexample'
    },
    pinterest: {
      enabled: true
    }
  },
  display_settings: {
    style: 'rounded',
    size: 'large',
    text_labels: true,
    icon_only: false
  }
}];
const ProfilesTab = () => {
  const [profiles, setProfiles] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(sampleProfiles);
  const [editingProfile, setEditingProfile] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [isCreating, setIsCreating] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [loading, setLoading] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const {
    showSuccess,
    showError
  } = (0,_contexts__WEBPACK_IMPORTED_MODULE_2__.useNotifications)();

  // Form validation for profile editing
  const {
    data: formData,
    updateField,
    touchField,
    validateForm,
    getFieldError,
    hasFieldError
  } = (0,_utils_validation__WEBPACK_IMPORTED_MODULE_3__.useFormValidation)(editingProfile || {
    name: '',
    networks: {},
    display_settings: {
      style: 'default',
      size: 'medium',
      text_labels: false,
      icon_only: true
    }
  }, {
    name: _utils_validation__WEBPACK_IMPORTED_MODULE_3__.validationRules.profileName
  });

  // Update form data when editing profile changes
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (editingProfile) {
      Object.keys(editingProfile).forEach(key => {
        updateField(key, editingProfile[key]);
      });
    }
  }, [editingProfile]);

  // Load profiles from API (placeholder)
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    // TODO: Load profiles from REST API
    // loadProfiles();
  }, []);
  const handleCreateProfile = () => {
    const newProfile = {
      id: Date.now().toString(),
      name: 'New Profile',
      networks: {},
      display_settings: {
        style: 'default',
        size: 'medium',
        text_labels: false,
        icon_only: true
      }
    };
    setEditingProfile(newProfile);
    setIsCreating(true);
  };
  const handleEditProfile = profile => {
    setEditingProfile({
      ...profile
    });
    setIsCreating(false);
  };
  const handleSaveProfile = async () => {
    if (!editingProfile) return;

    // Validate form before saving
    if (!validateForm()) {
      showError('Please fix the validation errors before saving', 'Check the form fields for errors.');
      return;
    }
    try {
      setLoading(true);
      if (isCreating) {
        // Add to profiles list
        setProfiles(prev => [...prev, editingProfile]);
        showSuccess('Profile created successfully!');
      } else {
        // Update existing profile
        setProfiles(prev => prev.map(p => p.id === editingProfile.id ? editingProfile : p));
        showSuccess('Profile updated successfully!');
      }
      setEditingProfile(null);
      setIsCreating(false);
    } catch (error) {
      showError('Failed to save profile', 'Please try again.');
    } finally {
      setLoading(false);
    }
  };
  const handleDeleteProfile = async profileId => {
    if (!confirm('Are you sure you want to delete this profile?')) return;
    try {
      setLoading(true);
      setProfiles(prev => prev.filter(p => p.id !== profileId));
      showSuccess('Profile deleted successfully!');
    } catch (error) {
      showError('Failed to delete profile', 'Please try again.');
    } finally {
      setLoading(false);
    }
  };
  const handleCancelEdit = () => {
    setEditingProfile(null);
    setIsCreating(false);
  };
  const updateEditingProfile = updates => {
    if (!editingProfile) return;
    setEditingProfile({
      ...editingProfile,
      ...updates
    });
  };
  const updateNetworkSetting = (networkId, settings) => {
    if (!editingProfile) return;
    setEditingProfile({
      ...editingProfile,
      networks: {
        ...editingProfile.networks,
        [networkId]: {
          ...editingProfile.networks[networkId],
          ...settings
        }
      }
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.LoadingOverlay, {
    isLoading: loading,
    message: "Saving profile...",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
      className: "profiles-tab",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
        className: "wp-admin-card p-6",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          className: "flex justify-between items-center mb-6",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h2", {
              className: "text-xl font-semibold",
              children: "Social Sharing Profiles"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
              className: "text-wp-gray-600",
              children: "Create different profiles for different types of content or pages."
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
            onClick: handleCreateProfile,
            variant: "primary",
            children: "Add New Profile"
          })]
        }), editingProfile ?
        /*#__PURE__*/
        // Profile Editor
        (0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          className: "bg-wp-gray-50 p-6 rounded-lg mb-6",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h3", {
            className: "text-lg font-medium mb-4",
            children: isCreating ? 'Create New Profile' : 'Edit Profile'
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
            className: "grid grid-cols-1 lg:grid-cols-2 gap-6",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.ValidatedTextInput, {
                label: "Profile Name",
                value: editingProfile.name,
                onChange: value => {
                  updateEditingProfile({
                    name: value
                  });
                  updateField('name', value);
                },
                onBlur: () => touchField('name'),
                error: getFieldError('name'),
                placeholder: "Enter profile name",
                required: true
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                className: "mt-4",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h4", {
                  className: "font-medium mb-3",
                  children: "Display Settings"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.ValidatedSelect, {
                  label: "Button Style",
                  value: editingProfile.display_settings.style,
                  onChange: value => updateEditingProfile({
                    display_settings: {
                      ...editingProfile.display_settings,
                      style: value
                    }
                  }),
                  options: [{
                    value: 'default',
                    label: 'Default'
                  }, {
                    value: 'rounded',
                    label: 'Rounded'
                  }, {
                    value: 'square',
                    label: 'Square'
                  }, {
                    value: 'minimal',
                    label: 'Minimal'
                  }]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.ValidatedSelect, {
                  label: "Button Size",
                  value: editingProfile.display_settings.size,
                  onChange: value => updateEditingProfile({
                    display_settings: {
                      ...editingProfile.display_settings,
                      size: value
                    }
                  }),
                  options: [{
                    value: 'small',
                    label: 'Small'
                  }, {
                    value: 'medium',
                    label: 'Medium'
                  }, {
                    value: 'large',
                    label: 'Large'
                  }],
                  className: "mt-3"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                  className: "mt-3 space-y-2",
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                    checked: editingProfile.display_settings.text_labels,
                    onChange: checked => updateEditingProfile({
                      display_settings: {
                        ...editingProfile.display_settings,
                        text_labels: checked
                      }
                    }),
                    label: "Show text labels"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                    checked: editingProfile.display_settings.icon_only,
                    onChange: checked => updateEditingProfile({
                      display_settings: {
                        ...editingProfile.display_settings,
                        icon_only: checked
                      }
                    }),
                    label: "Icon only mode"
                  })]
                })]
              })]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h4", {
                className: "font-medium mb-3",
                children: "Social Networks"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
                className: "space-y-3",
                children: availableNetworks.map(network => {
                  const networkSettings = editingProfile.networks[network.id] || {
                    enabled: false
                  };
                  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                    className: "border border-wp-gray-200 rounded p-3",
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                      className: "flex items-center justify-between mb-2",
                      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                        className: "flex items-center",
                        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("i", {
                          className: `${network.icon} mr-2 text-wp-gray-600`
                        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
                          className: "font-medium",
                          children: network.name
                        })]
                      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Checkbox, {
                        checked: networkSettings.enabled || false,
                        onChange: checked => updateNetworkSetting(network.id, {
                          enabled: checked
                        }),
                        label: ""
                      })]
                    }), networkSettings.enabled && (network.id === 'twitter' || network.id === 'instagram') && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
                      className: "mt-2",
                      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.ValidatedTextInput, {
                        label: "",
                        value: networkSettings.handle || '',
                        onChange: value => updateNetworkSetting(network.id, {
                          handle: value
                        }),
                        placeholder: `@${network.name.toLowerCase()}handle`,
                        error: getFieldError(`${network.id}Handle`)
                      })
                    })]
                  }, network.id);
                })
              })]
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
            className: "flex justify-end space-x-3 mt-6 pt-4 border-t border-wp-gray-200",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
              onClick: handleCancelEdit,
              variant: "secondary",
              children: "Cancel"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
              onClick: handleSaveProfile,
              variant: "primary",
              children: isCreating ? 'Create Profile' : 'Save Changes'
            })]
          })]
        }) :
        /*#__PURE__*/
        // Profiles List
        (0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          className: "space-y-4",
          children: [profiles.map(profile => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
            className: "border border-wp-gray-200 rounded-lg p-4",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
              className: "flex justify-between items-start",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h3", {
                  className: "font-medium text-lg",
                  children: profile.name
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
                  className: "text-sm text-wp-gray-600 mt-1",
                  children: [Object.values(profile.networks).filter(n => n.enabled).length, " networks enabled"]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
                  className: "flex items-center mt-2 space-x-2",
                  children: Object.entries(profile.networks).filter(([, settings]) => settings.enabled).map(([networkId]) => {
                    const network = availableNetworks.find(n => n.id === networkId);
                    return network ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("i", {
                      className: `${network.icon} text-wp-gray-500`,
                      title: network.name
                    }, networkId) : null;
                  })
                })]
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                className: "flex space-x-2",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
                  onClick: () => handleEditProfile(profile),
                  variant: "secondary",
                  size: "small",
                  children: "Edit"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Button, {
                  onClick: () => handleDeleteProfile(profile.id),
                  variant: "secondary",
                  size: "small",
                  className: "text-red-600 hover:text-red-700",
                  children: "Delete"
                })]
              })]
            })
          }, profile.id)), profiles.length === 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
            className: "text-center py-8 text-wp-gray-500",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
              children: "No profiles created yet."
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
              className: "text-sm mt-1",
              children: "Create your first profile to get started."
            })]
          })]
        })]
      })
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/ShortcodeTab.tsx":
/*!*******************************************************!*\
  !*** ./src/admin-ui/components/tabs/ShortcodeTab.tsx ***!
  \*******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ShortcodeTab: function() { return /* binding */ ShortcodeTab; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const ShortcodeTab = () => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    className: "shortcode-tab",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "wp-admin-card p-6",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("h2", {
        className: "text-xl font-semibold mb-4",
        children: "Shortcode Generator"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
        className: "text-wp-gray-600 mb-6",
        children: "Generate shortcodes for embedding social share buttons anywhere on your site."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: "bg-wp-gray-50 p-4 rounded-lg",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
          className: "text-center text-wp-gray-600",
          children: "Shortcode generator component coming soon..."
        })
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/tabs/index.ts":
/*!***********************************************!*\
  !*** ./src/admin-ui/components/tabs/index.ts ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AppearanceTab: function() { return /* reexport safe */ _AppearanceTab__WEBPACK_IMPORTED_MODULE_4__.AppearanceTab; },
/* harmony export */   GeneralTab: function() { return /* reexport safe */ _GeneralTab__WEBPACK_IMPORTED_MODULE_0__.GeneralTab; },
/* harmony export */   IntegrationsTab: function() { return /* reexport safe */ _IntegrationsTab__WEBPACK_IMPORTED_MODULE_3__.IntegrationsTab; },
/* harmony export */   NetworksTab: function() { return /* reexport safe */ _NetworksTab__WEBPACK_IMPORTED_MODULE_1__.NetworksTab; },
/* harmony export */   PlacementTab: function() { return /* reexport safe */ _PlacementTab__WEBPACK_IMPORTED_MODULE_5__.PlacementTab; },
/* harmony export */   ProfilesTab: function() { return /* reexport safe */ _ProfilesTab__WEBPACK_IMPORTED_MODULE_2__.ProfilesTab; },
/* harmony export */   ShortcodeTab: function() { return /* reexport safe */ _ShortcodeTab__WEBPACK_IMPORTED_MODULE_6__.ShortcodeTab; }
/* harmony export */ });
/* harmony import */ var _GeneralTab__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./GeneralTab */ "./src/admin-ui/components/tabs/GeneralTab.tsx");
/* harmony import */ var _NetworksTab__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./NetworksTab */ "./src/admin-ui/components/tabs/NetworksTab.tsx");
/* harmony import */ var _ProfilesTab__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ProfilesTab */ "./src/admin-ui/components/tabs/ProfilesTab.tsx");
/* harmony import */ var _IntegrationsTab__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./IntegrationsTab */ "./src/admin-ui/components/tabs/IntegrationsTab.tsx");
/* harmony import */ var _AppearanceTab__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./AppearanceTab */ "./src/admin-ui/components/tabs/AppearanceTab.tsx");
/* harmony import */ var _PlacementTab__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./PlacementTab */ "./src/admin-ui/components/tabs/PlacementTab.tsx");
/* harmony import */ var _ShortcodeTab__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./ShortcodeTab */ "./src/admin-ui/components/tabs/ShortcodeTab.tsx");
// Tab Component exports








/***/ }),

/***/ "./src/admin-ui/components/ui/FormFields.tsx":
/*!***************************************************!*\
  !*** ./src/admin-ui/components/ui/FormFields.tsx ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Button: function() { return /* binding */ Button; },
/* harmony export */   Checkbox: function() { return /* binding */ Checkbox; },
/* harmony export */   FormField: function() { return /* binding */ FormField; },
/* harmony export */   Select: function() { return /* binding */ Select; },
/* harmony export */   TextInput: function() { return /* binding */ TextInput; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const FormField = ({
  label,
  description,
  required = false,
  error,
  className = '',
  children
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
    className: `wp-form-field ${className} ${error ? 'has-error' : ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
      className: "wp-form-field-label",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("label", {
        className: "form-label",
        children: [label, required && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
          className: "required text-red-500 ml-1",
          children: "*"
        })]
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
      className: "wp-form-field-input",
      children: children
    }), description && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
      className: "description text-sm text-wp-gray-600 mt-1",
      children: description
    }), error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
      className: "error-message text-sm text-red-600 mt-1",
      children: error
    })]
  });
};
const TextInput = ({
  value,
  onChange,
  onBlur,
  placeholder,
  disabled = false,
  type = 'text',
  className = ''
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("input", {
    type: type,
    value: value,
    onChange: e => onChange(e.target.value),
    onBlur: onBlur,
    placeholder: placeholder,
    disabled: disabled,
    className: `wp-text-input ${className}`
  });
};
const Select = ({
  value,
  onChange,
  options,
  disabled = false,
  className = ''
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("select", {
    value: value,
    onChange: e => onChange(e.target.value),
    disabled: disabled,
    className: `wp-select ${className}`,
    children: options.map(option => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("option", {
      value: option.value,
      children: option.label
    }, option.value))
  });
};
const Checkbox = ({
  checked,
  onChange,
  label,
  disabled = false,
  className = ''
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("label", {
    className: `wp-checkbox-label ${className}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("input", {
      type: "checkbox",
      checked: checked,
      onChange: e => onChange(e.target.checked),
      disabled: disabled,
      className: "wp-checkbox"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
      className: "checkbox-text",
      children: label
    })]
  });
};
const Button = ({
  onClick,
  children,
  variant = 'primary',
  size = 'medium',
  disabled = false,
  loading = false,
  className = ''
}) => {
  const getButtonClasses = () => {
    let classes = 'wp-button';

    // Variant classes
    switch (variant) {
      case 'primary':
        classes += ' button-primary';
        break;
      case 'secondary':
        classes += ' button-secondary';
        break;
      case 'tertiary':
        classes += ' button-tertiary';
        break;
    }

    // Size classes
    switch (size) {
      case 'small':
        classes += ' button-small';
        break;
      case 'large':
        classes += ' button-large';
        break;
      case 'medium':
      default:
        // Default size
        break;
    }
    return `${classes} ${className}`;
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("button", {
    type: "button",
    onClick: onClick,
    disabled: disabled || loading,
    className: getButtonClasses(),
    children: [loading && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
      className: "spinner is-active inline-block mr-1",
      "aria-hidden": "true"
    }), children]
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ui/LoadingSpinner.tsx":
/*!*******************************************************!*\
  !*** ./src/admin-ui/components/ui/LoadingSpinner.tsx ***!
  \*******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LoadingOverlay: function() { return /* binding */ LoadingOverlay; },
/* harmony export */   LoadingSpinner: function() { return /* binding */ LoadingSpinner; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


/**
 * Reusable loading spinner component
 */
const LoadingSpinner = ({
  size = 'medium',
  message,
  className = ''
}) => {
  const sizeClasses = {
    small: 'w-4 h-4',
    medium: 'w-6 h-6',
    large: 'w-8 h-8'
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    className: `flex items-center justify-center ${className}`,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "flex flex-col items-center",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: `animate-spin ${sizeClasses[size]} text-wp-blue-600`,
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("svg", {
          className: "w-full h-full",
          xmlns: "http://www.w3.org/2000/svg",
          fill: "none",
          viewBox: "0 0 24 24",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("circle", {
            className: "opacity-25",
            cx: "12",
            cy: "12",
            r: "10",
            stroke: "currentColor",
            strokeWidth: "4"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
            className: "opacity-75",
            fill: "currentColor",
            d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          })]
        })
      }), message && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
        className: "mt-2 text-sm text-wp-gray-600",
        children: message
      })]
    })
  });
};
/**
 * Loading overlay that shows a spinner over existing content
 */
const LoadingOverlay = ({
  isLoading,
  message,
  children
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
    className: "relative",
    children: [children, isLoading && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
      className: "absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(LoadingSpinner, {
        size: "large",
        message: message
      })
    })]
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ui/Notice.tsx":
/*!***********************************************!*\
  !*** ./src/admin-ui/components/ui/Notice.tsx ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Notice: function() { return /* binding */ Notice; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const Notice = ({
  type,
  message,
  dismissible = false,
  onDismiss
}) => {
  const getNoticeClasses = () => {
    const baseClasses = 'notice wp-admin-notice';
    switch (type) {
      case 'success':
        return `${baseClasses} notice-success`;
      case 'warning':
        return `${baseClasses} notice-warning`;
      case 'error':
        return `${baseClasses} notice-error`;
      case 'info':
      default:
        return `${baseClasses} notice-info`;
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
    className: `${getNoticeClasses()} ${dismissible ? 'is-dismissible' : ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
      children: message
    }), dismissible && onDismiss && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("button", {
      type: "button",
      className: "notice-dismiss",
      onClick: onDismiss,
      "aria-label": "Dismiss this notice",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
        className: "screen-reader-text",
        children: "Dismiss this notice."
      })
    })]
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ui/Notification.tsx":
/*!*****************************************************!*\
  !*** ./src/admin-ui/components/ui/Notification.tsx ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Notification: function() { return /* binding */ Notification; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


/**
 * Notification component for displaying user feedback
 */
const Notification = ({
  type,
  title,
  message,
  dismissible = true,
  autoHide = false,
  autoHideDelay = 5000,
  onDismiss,
  actions
}) => {
  const [isVisible, setIsVisible] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (autoHide && autoHideDelay > 0) {
      const timer = setTimeout(() => {
        handleDismiss();
      }, autoHideDelay);
      return () => clearTimeout(timer);
    }
  }, [autoHide, autoHideDelay]);
  const handleDismiss = () => {
    setIsVisible(false);
    if (onDismiss) {
      onDismiss();
    }
  };
  if (!isVisible) {
    return null;
  }
  const typeStyles = {
    success: {
      container: 'bg-green-50 border-green-200',
      icon: 'text-green-500',
      title: 'text-green-800',
      message: 'text-green-700'
    },
    error: {
      container: 'bg-red-50 border-red-200',
      icon: 'text-red-500',
      title: 'text-red-800',
      message: 'text-red-700'
    },
    warning: {
      container: 'bg-yellow-50 border-yellow-200',
      icon: 'text-yellow-500',
      title: 'text-yellow-800',
      message: 'text-yellow-700'
    },
    info: {
      container: 'bg-blue-50 border-blue-200',
      icon: 'text-blue-500',
      title: 'text-blue-800',
      message: 'text-blue-700'
    }
  };
  const icons = {
    success: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("svg", {
      className: "w-5 h-5",
      fill: "currentColor",
      viewBox: "0 0 20 20",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
        fillRule: "evenodd",
        d: "M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z",
        clipRule: "evenodd"
      })
    }),
    error: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("svg", {
      className: "w-5 h-5",
      fill: "currentColor",
      viewBox: "0 0 20 20",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
        fillRule: "evenodd",
        d: "M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z",
        clipRule: "evenodd"
      })
    }),
    warning: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("svg", {
      className: "w-5 h-5",
      fill: "currentColor",
      viewBox: "0 0 20 20",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
        fillRule: "evenodd",
        d: "M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z",
        clipRule: "evenodd"
      })
    }),
    info: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("svg", {
      className: "w-5 h-5",
      fill: "currentColor",
      viewBox: "0 0 20 20",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
        fillRule: "evenodd",
        d: "M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z",
        clipRule: "evenodd"
      })
    })
  };
  const styles = typeStyles[type];
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    className: `rounded-lg border p-4 ${styles.container}`,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "flex",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: `flex-shrink-0 ${styles.icon}`,
        children: icons[type]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
        className: "ml-3 flex-1",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("h3", {
          className: `text-sm font-medium ${styles.title}`,
          children: title
        }), message && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
          className: `mt-1 text-sm ${styles.message}`,
          children: message
        }), actions && actions.length > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
          className: "mt-3 flex space-x-2",
          children: actions.map((action, index) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("button", {
            onClick: action.onClick,
            className: `text-sm font-medium rounded px-3 py-1 ${action.variant === 'primary' ? `bg-${type}-600 text-white hover:bg-${type}-700` : `text-${type}-600 hover:text-${type}-800`}`,
            children: action.label
          }, index))
        })]
      }), dismissible && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: "ml-auto pl-3",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
          className: "-mx-1.5 -my-1.5",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("button", {
            onClick: handleDismiss,
            className: `inline-flex rounded-md p-1.5 ${styles.icon} hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2`,
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
              className: "sr-only",
              children: "Dismiss"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("svg", {
              className: "w-5 h-5",
              fill: "currentColor",
              viewBox: "0 0 20 20",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
                fillRule: "evenodd",
                d: "M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z",
                clipRule: "evenodd"
              })
            })]
          })
        })
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ui/Tabs.tsx":
/*!*********************************************!*\
  !*** ./src/admin-ui/components/ui/Tabs.tsx ***!
  \*********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TabPanel: function() { return /* binding */ TabPanel; },
/* harmony export */   Tabs: function() { return /* binding */ Tabs; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const Tabs = ({
  tabs,
  activeTab,
  onTabChange,
  className = ''
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    className: `wp-tabs ${className}`,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("nav", {
      className: "nav-tab-wrapper wp-clearfix",
      role: "tablist",
      children: tabs.map(tab => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("button", {
        role: "tab",
        "aria-selected": activeTab === tab.id,
        className: `nav-tab ${activeTab === tab.id ? 'nav-tab-active' : ''}`,
        onClick: () => onTabChange(tab.id),
        children: [tab.icon && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
          className: `dashicons ${tab.icon} mr-1`,
          "aria-hidden": "true"
        }), tab.title]
      }, tab.id))
    })
  });
};
const TabPanel = ({
  id,
  activeTab,
  children,
  className = ''
}) => {
  if (activeTab !== id) return null;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
    role: "tabpanel",
    "aria-labelledby": `tab-${id}`,
    className: `tab-panel ${className}`,
    children: children
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ui/ValidatedFields.tsx":
/*!********************************************************!*\
  !*** ./src/admin-ui/components/ui/ValidatedFields.tsx ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ValidatedCheckbox: function() { return /* binding */ ValidatedCheckbox; },
/* harmony export */   ValidatedSelect: function() { return /* binding */ ValidatedSelect; },
/* harmony export */   ValidatedTextArea: function() { return /* binding */ ValidatedTextArea; },
/* harmony export */   ValidatedTextInput: function() { return /* binding */ ValidatedTextInput; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var ___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! . */ "./src/admin-ui/components/ui/index.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);



const ValidatedTextInput = ({
  label,
  value,
  onChange,
  onBlur,
  error,
  required,
  placeholder,
  type = 'text',
  disabled,
  helpText,
  className
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(___WEBPACK_IMPORTED_MODULE_1__.FormField, {
    label: label,
    required: required,
    className: className,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
      className: "space-y-1",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(___WEBPACK_IMPORTED_MODULE_1__.TextInput, {
        type: type,
        value: value,
        onChange: onChange,
        onBlur: onBlur,
        placeholder: placeholder,
        disabled: disabled,
        className: error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''
      }), error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("p", {
        className: "text-sm text-red-600 flex items-center",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("i", {
          className: "fas fa-exclamation-triangle mr-1"
        }), error]
      }), helpText && !error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("p", {
        className: "text-sm text-wp-gray-500",
        children: helpText
      })]
    })
  });
};
const ValidatedSelect = ({
  label,
  value,
  onChange,
  onBlur,
  error,
  required,
  disabled,
  options,
  placeholder,
  helpText,
  className
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(___WEBPACK_IMPORTED_MODULE_1__.FormField, {
    label: label,
    required: required,
    className: className,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
      className: "space-y-1",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("select", {
        value: value,
        onChange: e => onChange(e.target.value),
        onBlur: onBlur,
        disabled: disabled,
        className: `
            w-full p-2 border rounded-md focus:ring-2 focus:ring-wp-blue-500 focus:border-wp-blue-500
            ${error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-wp-gray-300'}
            ${disabled ? 'bg-wp-gray-100 cursor-not-allowed' : 'bg-white'}
          `,
        children: [placeholder && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("option", {
          value: "",
          disabled: true,
          children: placeholder
        }), options.map(option => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("option", {
          value: option.value,
          children: option.label
        }, option.value))]
      }), error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("p", {
        className: "text-sm text-red-600 flex items-center",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("i", {
          className: "fas fa-exclamation-triangle mr-1"
        }), error]
      }), helpText && !error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("p", {
        className: "text-sm text-wp-gray-500",
        children: helpText
      })]
    })
  });
};
const ValidatedTextArea = ({
  label,
  value,
  onChange,
  onBlur,
  error,
  required,
  placeholder,
  disabled,
  rows = 4,
  maxLength,
  helpText,
  className
}) => {
  const remainingChars = maxLength ? maxLength - value.length : null;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(___WEBPACK_IMPORTED_MODULE_1__.FormField, {
    label: label,
    required: required,
    className: className,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
      className: "space-y-1",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("textarea", {
        value: value,
        onChange: e => onChange(e.target.value),
        onBlur: onBlur,
        placeholder: placeholder,
        disabled: disabled,
        rows: rows,
        maxLength: maxLength,
        className: `
            w-full p-2 border rounded-md focus:ring-2 focus:ring-wp-blue-500 focus:border-wp-blue-500 resize-vertical
            ${error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-wp-gray-300'}
            ${disabled ? 'bg-wp-gray-100 cursor-not-allowed' : 'bg-white'}
          `
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
        className: "flex justify-between items-start",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
          className: "flex-1",
          children: [error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("p", {
            className: "text-sm text-red-600 flex items-center",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("i", {
              className: "fas fa-exclamation-triangle mr-1"
            }), error]
          }), helpText && !error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("p", {
            className: "text-sm text-wp-gray-500",
            children: helpText
          })]
        }), maxLength && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("p", {
          className: `text-xs ml-2 ${remainingChars !== null && remainingChars < 10 ? 'text-red-600' : 'text-wp-gray-400'}`,
          children: [remainingChars, " remaining"]
        })]
      })]
    })
  });
};
const ValidatedCheckbox = ({
  label,
  checked,
  onChange,
  error,
  disabled,
  helpText,
  className
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
    className: `space-y-1 ${className || ''}`,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("label", {
      className: `flex items-center ${disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}`,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("input", {
        type: "checkbox",
        checked: checked,
        onChange: e => onChange(e.target.checked),
        disabled: disabled,
        className: `
            mr-2 h-4 w-4 rounded border-wp-gray-300 text-wp-blue-600
            focus:ring-wp-blue-500 focus:ring-2 focus:ring-offset-0
            ${error ? 'border-red-500' : ''}
            ${disabled ? 'cursor-not-allowed' : ''}
          `
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("span", {
        className: "text-sm font-medium text-wp-gray-700",
        children: label
      })]
    }), error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("p", {
      className: "text-sm text-red-600 flex items-center ml-6",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("i", {
        className: "fas fa-exclamation-triangle mr-1"
      }), error]
    }), helpText && !error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("p", {
      className: "text-sm text-wp-gray-500 ml-6",
      children: helpText
    })]
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ui/index.ts":
/*!*********************************************!*\
  !*** ./src/admin-ui/components/ui/index.ts ***!
  \*********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Button: function() { return /* reexport safe */ _FormFields__WEBPACK_IMPORTED_MODULE_4__.Button; },
/* harmony export */   Checkbox: function() { return /* reexport safe */ _FormFields__WEBPACK_IMPORTED_MODULE_4__.Checkbox; },
/* harmony export */   FormField: function() { return /* reexport safe */ _FormFields__WEBPACK_IMPORTED_MODULE_4__.FormField; },
/* harmony export */   LoadingOverlay: function() { return /* reexport safe */ _LoadingSpinner__WEBPACK_IMPORTED_MODULE_2__.LoadingOverlay; },
/* harmony export */   LoadingSpinner: function() { return /* reexport safe */ _LoadingSpinner__WEBPACK_IMPORTED_MODULE_2__.LoadingSpinner; },
/* harmony export */   Notice: function() { return /* reexport safe */ _Notice__WEBPACK_IMPORTED_MODULE_1__.Notice; },
/* harmony export */   Notification: function() { return /* reexport safe */ _Notification__WEBPACK_IMPORTED_MODULE_3__.Notification; },
/* harmony export */   Select: function() { return /* reexport safe */ _FormFields__WEBPACK_IMPORTED_MODULE_4__.Select; },
/* harmony export */   TabPanel: function() { return /* reexport safe */ _Tabs__WEBPACK_IMPORTED_MODULE_0__.TabPanel; },
/* harmony export */   Tabs: function() { return /* reexport safe */ _Tabs__WEBPACK_IMPORTED_MODULE_0__.Tabs; },
/* harmony export */   TextInput: function() { return /* reexport safe */ _FormFields__WEBPACK_IMPORTED_MODULE_4__.TextInput; },
/* harmony export */   ValidatedCheckbox: function() { return /* reexport safe */ _ValidatedFields__WEBPACK_IMPORTED_MODULE_5__.ValidatedCheckbox; },
/* harmony export */   ValidatedSelect: function() { return /* reexport safe */ _ValidatedFields__WEBPACK_IMPORTED_MODULE_5__.ValidatedSelect; },
/* harmony export */   ValidatedTextArea: function() { return /* reexport safe */ _ValidatedFields__WEBPACK_IMPORTED_MODULE_5__.ValidatedTextArea; },
/* harmony export */   ValidatedTextInput: function() { return /* reexport safe */ _ValidatedFields__WEBPACK_IMPORTED_MODULE_5__.ValidatedTextInput; }
/* harmony export */ });
/* harmony import */ var _Tabs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Tabs */ "./src/admin-ui/components/ui/Tabs.tsx");
/* harmony import */ var _Notice__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Notice */ "./src/admin-ui/components/ui/Notice.tsx");
/* harmony import */ var _LoadingSpinner__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./LoadingSpinner */ "./src/admin-ui/components/ui/LoadingSpinner.tsx");
/* harmony import */ var _Notification__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Notification */ "./src/admin-ui/components/ui/Notification.tsx");
/* harmony import */ var _FormFields__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./FormFields */ "./src/admin-ui/components/ui/FormFields.tsx");
/* harmony import */ var _ValidatedFields__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./ValidatedFields */ "./src/admin-ui/components/ui/ValidatedFields.tsx");
// UI Component exports







/***/ }),

/***/ "./src/admin-ui/contexts/NetworksContext.tsx":
/*!***************************************************!*\
  !*** ./src/admin-ui/contexts/NetworksContext.tsx ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NetworksProvider: function() { return /* binding */ NetworksProvider; },
/* harmony export */   useNetworksContext: function() { return /* binding */ useNetworksContext; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_useNetworks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../hooks/useNetworks */ "./src/admin-ui/hooks/useNetworks.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);



// Networks Context

const NetworksContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(undefined);

// Networks Provider

const NetworksProvider = ({
  children
}) => {
  const networksHook = (0,_hooks_useNetworks__WEBPACK_IMPORTED_MODULE_1__.useNetworks)();
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(NetworksContext.Provider, {
    value: networksHook,
    children: children
  });
};

// Custom hook to use networks context
const useNetworksContext = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(NetworksContext);
  if (context === undefined) {
    throw new Error('useNetworksContext must be used within a NetworksProvider');
  }
  return context;
};

/***/ }),

/***/ "./src/admin-ui/contexts/NotificationContext.tsx":
/*!*******************************************************!*\
  !*** ./src/admin-ui/contexts/NotificationContext.tsx ***!
  \*******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NotificationProvider: function() { return /* binding */ NotificationProvider; },
/* harmony export */   useNotifications: function() { return /* binding */ useNotifications; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_ui_Notification__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../components/ui/Notification */ "./src/admin-ui/components/ui/Notification.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);



const NotificationContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(undefined);
function notificationReducer(state, action) {
  switch (action.type) {
    case 'ADD_NOTIFICATION':
      return {
        ...state,
        notifications: [...state.notifications, {
          ...action.payload,
          id: Math.random().toString(36).substr(2, 9)
        }]
      };
    case 'REMOVE_NOTIFICATION':
      return {
        ...state,
        notifications: state.notifications.filter(n => n.id !== action.payload)
      };
    case 'CLEAR_ALL':
      return {
        ...state,
        notifications: []
      };
    default:
      return state;
  }
}
const NotificationProvider = ({
  children,
  maxNotifications = 5
}) => {
  const [state, dispatch] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useReducer)(notificationReducer, {
    notifications: []
  });
  const addNotification = notification => {
    var _notification$autoHid, _notification$autoHid2;
    const id = Math.random().toString(36).substr(2, 9);

    // Remove oldest notification if we exceed the limit
    if (state.notifications.length >= maxNotifications) {
      const oldestId = state.notifications[0]?.id;
      if (oldestId) {
        dispatch({
          type: 'REMOVE_NOTIFICATION',
          payload: oldestId
        });
      }
    }
    dispatch({
      type: 'ADD_NOTIFICATION',
      payload: {
        ...notification,
        autoHide: (_notification$autoHid = notification.autoHide) !== null && _notification$autoHid !== void 0 ? _notification$autoHid : true,
        autoHideDelay: (_notification$autoHid2 = notification.autoHideDelay) !== null && _notification$autoHid2 !== void 0 ? _notification$autoHid2 : 5000
      }
    });
    return id;
  };
  const removeNotification = id => {
    dispatch({
      type: 'REMOVE_NOTIFICATION',
      payload: id
    });
  };
  const clearAll = () => {
    dispatch({
      type: 'CLEAR_ALL'
    });
  };
  const showSuccess = (title, message) => {
    return addNotification({
      type: 'success',
      title,
      message
    });
  };
  const showError = (title, message) => {
    return addNotification({
      type: 'error',
      title,
      message,
      autoHide: false // Errors should not auto-hide
    });
  };
  const showWarning = (title, message) => {
    return addNotification({
      type: 'warning',
      title,
      message
    });
  };
  const showInfo = (title, message) => {
    return addNotification({
      type: 'info',
      title,
      message
    });
  };
  const contextValue = {
    notifications: state.notifications,
    addNotification,
    removeNotification,
    clearAll,
    showSuccess,
    showError,
    showWarning,
    showInfo
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)(NotificationContext.Provider, {
    value: contextValue,
    children: [children, /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("div", {
      className: "fixed top-4 right-4 z-50 space-y-2 max-w-md",
      children: state.notifications.map(notification => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_components_ui_Notification__WEBPACK_IMPORTED_MODULE_1__.Notification, {
        type: notification.type,
        title: notification.title,
        message: notification.message,
        autoHide: notification.autoHide,
        autoHideDelay: notification.autoHideDelay,
        actions: notification.actions,
        onDismiss: () => removeNotification(notification.id)
      }, notification.id))
    })]
  });
};
const useNotifications = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(NotificationContext);
  if (context === undefined) {
    throw new Error('useNotifications must be used within a NotificationProvider');
  }
  return context;
};

/***/ }),

/***/ "./src/admin-ui/contexts/SettingsContext.tsx":
/*!***************************************************!*\
  !*** ./src/admin-ui/contexts/SettingsContext.tsx ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SettingsProvider: function() { return /* binding */ SettingsProvider; },
/* harmony export */   useSettingsContext: function() { return /* binding */ useSettingsContext; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _hooks_useSettings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../hooks/useSettings */ "./src/admin-ui/hooks/useSettings.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);



// Settings Context

const SettingsContext = (0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(undefined);

// Settings Provider

const SettingsProvider = ({
  children
}) => {
  const settingsHook = (0,_hooks_useSettings__WEBPACK_IMPORTED_MODULE_1__.useSettings)();
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(SettingsContext.Provider, {
    value: settingsHook,
    children: children
  });
};

// Custom hook to use settings context
const useSettingsContext = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(SettingsContext);
  if (context === undefined) {
    throw new Error('useSettingsContext must be used within a SettingsProvider');
  }
  return context;
};

/***/ }),

/***/ "./src/admin-ui/contexts/index.ts":
/*!****************************************!*\
  !*** ./src/admin-ui/contexts/index.ts ***!
  \****************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NetworksProvider: function() { return /* reexport safe */ _NetworksContext__WEBPACK_IMPORTED_MODULE_1__.NetworksProvider; },
/* harmony export */   NotificationProvider: function() { return /* reexport safe */ _NotificationContext__WEBPACK_IMPORTED_MODULE_2__.NotificationProvider; },
/* harmony export */   SettingsProvider: function() { return /* reexport safe */ _SettingsContext__WEBPACK_IMPORTED_MODULE_0__.SettingsProvider; },
/* harmony export */   useNetworksContext: function() { return /* reexport safe */ _NetworksContext__WEBPACK_IMPORTED_MODULE_1__.useNetworksContext; },
/* harmony export */   useNotifications: function() { return /* reexport safe */ _NotificationContext__WEBPACK_IMPORTED_MODULE_2__.useNotifications; },
/* harmony export */   useSettingsContext: function() { return /* reexport safe */ _SettingsContext__WEBPACK_IMPORTED_MODULE_0__.useSettingsContext; }
/* harmony export */ });
/* harmony import */ var _SettingsContext__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SettingsContext */ "./src/admin-ui/contexts/SettingsContext.tsx");
/* harmony import */ var _NetworksContext__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./NetworksContext */ "./src/admin-ui/contexts/NetworksContext.tsx");
/* harmony import */ var _NotificationContext__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./NotificationContext */ "./src/admin-ui/contexts/NotificationContext.tsx");
// Export all context providers and hooks




/***/ }),

/***/ "./src/admin-ui/hooks/useNetworks.ts":
/*!*******************************************!*\
  !*** ./src/admin-ui/hooks/useNetworks.ts ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useNetworks: function() { return /* binding */ useNetworks; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


// WordPress API fetch function

/**
 * Hook for managing network configurations
 */
const useNetworks = () => {
  const [networks, setNetworks] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [loading, setLoading] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const [error, setError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);

  // Load networks from API
  const loadNetworks = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await wp.apiFetch({
        path: '/html-social-share/v1/networks',
        method: 'GET'
      });

      // Convert response object to array
      const networksArray = Object.values(response);
      setNetworks(networksArray);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load networks');
      console.error('Failed to load networks:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  // Update a network configuration
  const updateNetwork = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async (networkId, updates) => {
    try {
      await wp.apiFetch({
        path: `/html-social-share/v1/networks/${networkId}`,
        method: 'POST',
        data: updates
      });

      // Update local state
      setNetworks(prev => prev.map(network => network.id === networkId ? {
        ...network,
        ...updates
      } : network));
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to update network';
      setError(errorMessage);
      throw new Error(errorMessage);
    }
  }, []);

  // Load networks on mount
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    loadNetworks();
  }, [loadNetworks]);
  return {
    networks,
    loading,
    error,
    updateNetwork,
    refreshNetworks: loadNetworks
  };
};

/***/ }),

/***/ "./src/admin-ui/hooks/useSettings.ts":
/*!*******************************************!*\
  !*** ./src/admin-ui/hooks/useSettings.ts ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSettings: function() { return /* binding */ useSettings; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


// WordPress API fetch function (should be available in WordPress admin)

/**
 * Hook for managing plugin settings state and API interactions
 */
const useSettings = () => {
  const [settings, setSettings] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [loading, setLoading] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const [saving, setSaving] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [error, setError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [isDirty, setIsDirty] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);

  // Load settings from API
  const loadSettings = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      var _response$general$sho, _response$general$sho2, _response$general$sho3, _response$general$sho4, _response$general$def, _response$general$def2, _response$networks$en, _response$networks$ne, _response$networks$cu, _response$integration, _response$integration2, _response$integration3, _response$integration4, _response$integration5, _response$appearance$, _response$appearance$2, _response$appearance$3, _response$appearance$4, _response$placement$a, _response$placement$p, _response$placement$p2, _response$advanced$ca, _response$advanced$ca2, _response$advanced$de;
      setLoading(true);
      setError(null);
      const response = await wp.apiFetch({
        path: '/html-social-share/v1/settings',
        method: 'GET'
      });

      // Flatten the response structure to match our PluginSettings interface
      const flatSettings = {
        // General settings
        show_on_front_page: (_response$general$sho = response.general?.show_on_front_page) !== null && _response$general$sho !== void 0 ? _response$general$sho : true,
        show_on_posts: (_response$general$sho2 = response.general?.show_on_posts) !== null && _response$general$sho2 !== void 0 ? _response$general$sho2 : true,
        show_on_pages: (_response$general$sho3 = response.general?.show_on_pages) !== null && _response$general$sho3 !== void 0 ? _response$general$sho3 : false,
        show_on_archives: (_response$general$sho4 = response.general?.show_on_archives) !== null && _response$general$sho4 !== void 0 ? _response$general$sho4 : false,
        default_style: (_response$general$def = response.general?.default_style) !== null && _response$general$def !== void 0 ? _response$general$def : 'default',
        default_size: (_response$general$def2 = response.general?.default_size) !== null && _response$general$def2 !== void 0 ? _response$general$def2 : 'medium',
        // Network settings
        enabled_networks: (_response$networks$en = response.networks?.enabled_networks) !== null && _response$networks$en !== void 0 ? _response$networks$en : ['facebook', 'twitter', 'linkedin'],
        network_order: (_response$networks$ne = response.networks?.network_order) !== null && _response$networks$ne !== void 0 ? _response$networks$ne : [],
        custom_networks: (_response$networks$cu = response.networks?.custom_networks) !== null && _response$networks$cu !== void 0 ? _response$networks$cu : [],
        // Profile settings (placeholder)
        profiles: [],
        default_profile: '',
        // Integrations
        betterlinks_enabled: (_response$integration = response.integrations?.betterlinks_enabled) !== null && _response$integration !== void 0 ? _response$integration : false,
        betterlinks_api_key: (_response$integration2 = response.integrations?.betterlinks_api_key) !== null && _response$integration2 !== void 0 ? _response$integration2 : '',
        elementor_enabled: (_response$integration3 = response.integrations?.elementor_enabled) !== null && _response$integration3 !== void 0 ? _response$integration3 : false,
        divi_enabled: (_response$integration4 = response.integrations?.divi_enabled) !== null && _response$integration4 !== void 0 ? _response$integration4 : false,
        beaver_builder_enabled: (_response$integration5 = response.integrations?.beaver_builder_enabled) !== null && _response$integration5 !== void 0 ? _response$integration5 : false,
        // Appearance
        icon_style: (_response$appearance$ = response.appearance?.icon_style) !== null && _response$appearance$ !== void 0 ? _response$appearance$ : 'default',
        button_size: (_response$appearance$2 = response.appearance?.button_size) !== null && _response$appearance$2 !== void 0 ? _response$appearance$2 : 'medium',
        button_spacing: (_response$appearance$3 = response.appearance?.button_spacing) !== null && _response$appearance$3 !== void 0 ? _response$appearance$3 : 5,
        custom_css: (_response$appearance$4 = response.appearance?.custom_css) !== null && _response$appearance$4 !== void 0 ? _response$appearance$4 : '',
        // Placement
        auto_placement: (_response$placement$a = response.placement?.auto_placement) !== null && _response$placement$a !== void 0 ? _response$placement$a : false,
        placement_position: (_response$placement$p = response.placement?.placement_position) !== null && _response$placement$p !== void 0 ? _response$placement$p : 'after',
        placement_post_types: (_response$placement$p2 = response.placement?.placement_post_types) !== null && _response$placement$p2 !== void 0 ? _response$placement$p2 : ['post'],
        // Advanced
        cache_enabled: (_response$advanced$ca = response.advanced?.cache_enabled) !== null && _response$advanced$ca !== void 0 ? _response$advanced$ca : true,
        cache_duration: (_response$advanced$ca2 = response.advanced?.cache_duration) !== null && _response$advanced$ca2 !== void 0 ? _response$advanced$ca2 : 3600,
        debug_mode: (_response$advanced$de = response.advanced?.debug_mode) !== null && _response$advanced$de !== void 0 ? _response$advanced$de : false
      };
      setSettings(flatSettings);
      setIsDirty(false);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load settings');
      console.error('Failed to load settings:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  // Update a specific setting
  const updateSetting = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((key, value) => {
    setSettings(prev => {
      if (!prev) return prev;
      const updated = {
        ...prev,
        [key]: value
      };
      setIsDirty(true);
      return updated;
    });
  }, []);

  // Update multiple settings at once
  const updateSettings = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(updates => {
    setSettings(prev => {
      if (!prev) return prev;
      const updated = {
        ...prev,
        ...updates
      };
      setIsDirty(true);
      return updated;
    });
  }, []);

  // Save settings to API
  const saveSettings = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    if (!settings) {
      throw new Error('No settings to save');
    }
    try {
      var _response$success, _response$message, _response$updated;
      setSaving(true);
      setError(null);

      // Restructure settings to match API format
      const apiData = {
        general: {
          show_on_front_page: settings.show_on_front_page,
          show_on_posts: settings.show_on_posts,
          show_on_pages: settings.show_on_pages,
          show_on_archives: settings.show_on_archives,
          default_style: settings.default_style,
          default_size: settings.default_size
        },
        networks: {
          enabled_networks: settings.enabled_networks,
          network_order: settings.network_order,
          custom_networks: settings.custom_networks
        },
        appearance: {
          icon_style: settings.icon_style,
          button_size: settings.button_size,
          button_spacing: settings.button_spacing,
          custom_css: settings.custom_css
        },
        placement: {
          auto_placement: settings.auto_placement,
          placement_position: settings.placement_position,
          placement_post_types: settings.placement_post_types
        },
        integrations: {
          betterlinks_enabled: settings.betterlinks_enabled,
          betterlinks_api_key: settings.betterlinks_api_key,
          elementor_enabled: settings.elementor_enabled,
          divi_enabled: settings.divi_enabled,
          beaver_builder_enabled: settings.beaver_builder_enabled
        },
        advanced: {
          cache_enabled: settings.cache_enabled,
          cache_duration: settings.cache_duration,
          debug_mode: settings.debug_mode
        }
      };
      const response = await wp.apiFetch({
        path: '/html-social-share/v1/settings',
        method: 'POST',
        data: apiData
      });
      setIsDirty(false);
      return {
        success: (_response$success = response.success) !== null && _response$success !== void 0 ? _response$success : true,
        message: (_response$message = response.message) !== null && _response$message !== void 0 ? _response$message : 'Settings saved successfully',
        updated_settings: (_response$updated = response.updated) !== null && _response$updated !== void 0 ? _response$updated : {}
      };
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to save settings';
      setError(errorMessage);
      throw new Error(errorMessage);
    } finally {
      setSaving(false);
    }
  }, [settings]);

  // Reset settings to defaults
  const resetSettings = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      setSaving(true);
      setError(null);
      await wp.apiFetch({
        path: '/html-social-share/v1/settings/reset',
        method: 'POST'
      });

      // Reload settings after reset
      await loadSettings();
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to reset settings';
      setError(errorMessage);
      throw new Error(errorMessage);
    } finally {
      setSaving(false);
    }
  }, [loadSettings]);

  // Load settings on mount
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    loadSettings();
  }, [loadSettings]);
  return {
    settings,
    loading,
    saving,
    error,
    isDirty,
    updateSetting,
    updateSettings,
    saveSettings,
    resetSettings,
    refreshSettings: loadSettings
  };
};

/***/ }),

/***/ "./src/admin-ui/index.css":
/*!********************************!*\
  !*** ./src/admin-ui/index.css ***!
  \********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/admin-ui/main.tsx":
/*!*******************************!*\
  !*** ./src/admin-ui/main.tsx ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: function() { return /* reexport safe */ _App__WEBPACK_IMPORTED_MODULE_2__.App; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_dom_client__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom/client */ "./node_modules/.pnpm/react-dom@18.3.1_react@18.3.1/node_modules/react-dom/client.js");
/* harmony import */ var _App__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./App */ "./src/admin-ui/App.tsx");
/* harmony import */ var _index_css__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./index.css */ "./src/admin-ui/index.css");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





// Mount the React app when DOM is ready

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('hss-admin-react-root');
  if (container) {
    const root = react_dom_client__WEBPACK_IMPORTED_MODULE_1__.createRoot(container);
    root.render(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)((react__WEBPACK_IMPORTED_MODULE_0___default().StrictMode), {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_App__WEBPACK_IMPORTED_MODULE_2__.App, {})
    }));
  }
});

// Export for potential external use


/***/ }),

/***/ "./src/admin-ui/utils/validation.ts":
/*!******************************************!*\
  !*** ./src/admin-ui/utils/validation.ts ***!
  \******************************************/
/***/ (function() {

throw new Error("Module build failed (from ./node_modules/.pnpm/babel-loader@9.2.1_@babel+core@7.25.7_webpack@5.102.0_webpack-cli@5.1.4_/node_modules/babel-loader/lib/index.js):\nSyntaxError: /Users/alim/Sites/git/html-social-share-buttons/src/admin-ui/utils/validation.ts: Unexpected token (43:0)\n\n\u001b[0m \u001b[90m 41 |\u001b[39m     pattern\u001b[33m:\u001b[39m patterns\u001b[33m.\u001b[39malphanumeric\u001b[33m,\u001b[39m\n \u001b[90m 42 |\u001b[39m   }\u001b[33m,\u001b[39m\n\u001b[31m\u001b[1m>\u001b[22m\u001b[39m\u001b[90m 43 |\u001b[39m \u001b[33m<<\u001b[39m\u001b[33m<<\u001b[39m\u001b[33m<<\u001b[39m\u001b[33m<\u001b[39m \u001b[33mUpdated\u001b[39m upstream\n \u001b[90m    |\u001b[39m \u001b[31m\u001b[1m^\u001b[22m\u001b[39m\n \u001b[90m 44 |\u001b[39m   \n \u001b[90m 45 |\u001b[39m \u001b[33m===\u001b[39m\u001b[33m===\u001b[39m\u001b[33m=\u001b[39m\n \u001b[90m 46 |\u001b[39m\u001b[0m\n    at constructor (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:367:19)\n    at TypeScriptParserMixin.raise (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:6630:19)\n    at TypeScriptParserMixin.unexpected (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:6650:16)\n    at TypeScriptParserMixin.parsePropertyName (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12034:18)\n    at TypeScriptParserMixin.parsePropertyDefinition (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:11899:10)\n    at TypeScriptParserMixin.parseObjectLike (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:11840:21)\n    at TypeScriptParserMixin.parseExprAtom (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:11343:23)\n    at TypeScriptParserMixin.parseExprSubscripts (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:11085:23)\n    at TypeScriptParserMixin.parseUpdate (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:11070:21)\n    at TypeScriptParserMixin.parseMaybeUnary (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:11050:23)\n    at TypeScriptParserMixin.parseMaybeUnary (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:9857:18)\n    at TypeScriptParserMixin.parseMaybeUnaryOrPrivate (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:10903:61)\n    at TypeScriptParserMixin.parseExprOps (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:10908:23)\n    at TypeScriptParserMixin.parseMaybeConditional (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:10885:23)\n    at TypeScriptParserMixin.parseMaybeAssign (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:10835:21)\n    at TypeScriptParserMixin.parseMaybeAssign (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:9806:20)\n    at /Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:10804:39\n    at TypeScriptParserMixin.allowInAnd (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12431:16)\n    at TypeScriptParserMixin.parseMaybeAssignAllowIn (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:10804:17)\n    at TypeScriptParserMixin.parseVar (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:13393:91)\n    at TypeScriptParserMixin.parseVarStatement (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:13239:10)\n    at TypeScriptParserMixin.parseVarStatement (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:9498:31)\n    at TypeScriptParserMixin.parseStatementContent (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12860:23)\n    at TypeScriptParserMixin.parseStatementContent (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:9532:18)\n    at TypeScriptParserMixin.parseStatementLike (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12776:17)\n    at TypeScriptParserMixin.parseStatementListItem (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12756:17)\n    at TypeScriptParserMixin.parseExportDeclaration (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:13943:17)\n    at TypeScriptParserMixin.parseExportDeclaration (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:9657:85)\n    at TypeScriptParserMixin.maybeParseExportDeclaration (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:13902:31)\n    at TypeScriptParserMixin.parseExport (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:13821:29)\n    at TypeScriptParserMixin.parseExport (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:9475:20)\n    at TypeScriptParserMixin.parseStatementContent (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12887:27)\n    at TypeScriptParserMixin.parseStatementContent (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:9532:18)\n    at TypeScriptParserMixin.parseStatementLike (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12776:17)\n    at TypeScriptParserMixin.parseModuleItem (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12753:17)\n    at TypeScriptParserMixin.parseBlockOrModuleBlockBody (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:13325:36)\n    at TypeScriptParserMixin.parseBlockBody (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:13318:10)\n    at TypeScriptParserMixin.parseProgram (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12634:10)\n    at TypeScriptParserMixin.parseTopLevel (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:12624:25)\n    at TypeScriptParserMixin.parse (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:14501:10)\n    at TypeScriptParserMixin.parse (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:10149:18)\n    at parse (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+parser@7.28.4/node_modules/@babel/parser/lib/index.js:14535:38)\n    at parser (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+core@7.25.7/node_modules/@babel/core/lib/parser/index.js:41:34)\n    at parser.next (<anonymous>)\n    at normalizeFile (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+core@7.25.7/node_modules/@babel/core/lib/transformation/normalize-file.js:64:37)\n    at normalizeFile.next (<anonymous>)\n    at run (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+core@7.25.7/node_modules/@babel/core/lib/transformation/index.js:21:50)\n    at run.next (<anonymous>)\n    at transform (/Users/alim/Sites/git/html-social-share-buttons/node_modules/.pnpm/@babel+core@7.25.7/node_modules/@babel/core/lib/transform.js:22:33)\n    at transform.next (<anonymous>)");

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

"use strict";
module.exports = window["React"];

/***/ }),

/***/ "react-dom":
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/***/ (function(module) {

"use strict";
module.exports = window["ReactDOM"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ (function(module) {

"use strict";
module.exports = window["ReactJSXRuntime"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
!function() {
"use strict";
/*!**********************!*\
  !*** ./src/admin.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _admin_ui_main_tsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./admin-ui/main.tsx */ "./src/admin-ui/main.tsx");
/**
 * Admin UI Entry Point
 */

}();
/******/ })()
;
//# sourceMappingURL=admin.js.map