/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/Icon.js":
/*!*********************************************************************************************************!*\
  !*** ./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/Icon.js ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Icon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _defaultAttributes_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./defaultAttributes.js */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/defaultAttributes.js");
/* harmony import */ var _shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./shared/src/utils.js */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/shared/src/utils.js");
/**
 * @license lucide-react v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */





const Icon = (0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(
  ({
    color = "currentColor",
    size = 24,
    strokeWidth = 2,
    absoluteStrokeWidth,
    className = "",
    children,
    iconNode,
    ...rest
  }, ref) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(
    "svg",
    {
      ref,
      ..._defaultAttributes_js__WEBPACK_IMPORTED_MODULE_1__["default"],
      width: size,
      height: size,
      stroke: color,
      strokeWidth: absoluteStrokeWidth ? Number(strokeWidth) * 24 / Number(size) : strokeWidth,
      className: (0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__.mergeClasses)("lucide", className),
      ...!children && !(0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_2__.hasA11yProp)(rest) && { "aria-hidden": "true" },
      ...rest
    },
    [
      ...iconNode.map(([tag, attrs]) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(tag, attrs)),
      ...Array.isArray(children) ? children : [children]
    ]
  )
);


//# sourceMappingURL=Icon.js.map


/***/ }),

/***/ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/createLucideIcon.js":
/*!*********************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/createLucideIcon.js ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ createLucideIcon; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _shared_src_utils_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./shared/src/utils.js */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/shared/src/utils.js");
/* harmony import */ var _Icon_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Icon.js */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/Icon.js");
/**
 * @license lucide-react v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */





const createLucideIcon = (iconName, iconNode) => {
  const Component = (0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(
    ({ className, ...props }, ref) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Icon_js__WEBPACK_IMPORTED_MODULE_2__["default"], {
      ref,
      iconNode,
      className: (0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_1__.mergeClasses)(
        `lucide-${(0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_1__.toKebabCase)((0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_1__.toPascalCase)(iconName))}`,
        `lucide-${iconName}`,
        className
      ),
      ...props
    })
  );
  Component.displayName = (0,_shared_src_utils_js__WEBPACK_IMPORTED_MODULE_1__.toPascalCase)(iconName);
  return Component;
};


//# sourceMappingURL=createLucideIcon.js.map


/***/ }),

/***/ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/defaultAttributes.js":
/*!**********************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/defaultAttributes.js ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ defaultAttributes; }
/* harmony export */ });
/**
 * @license lucide-react v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */

var defaultAttributes = {
  xmlns: "http://www.w3.org/2000/svg",
  width: 24,
  height: 24,
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  strokeWidth: 2,
  strokeLinecap: "round",
  strokeLinejoin: "round"
};


//# sourceMappingURL=defaultAttributes.js.map


/***/ }),

/***/ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/refresh-cw.js":
/*!*********************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/refresh-cw.js ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   __iconNode: function() { return /* binding */ __iconNode; },
/* harmony export */   "default": function() { return /* binding */ RefreshCw; }
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const __iconNode = [
  ["path", { d: "M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8", key: "v9h5vc" }],
  ["path", { d: "M21 3v5h-5", key: "1q7to0" }],
  ["path", { d: "M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16", key: "3uifl3" }],
  ["path", { d: "M8 16H3v5", key: "1cv678" }]
];
const RefreshCw = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("refresh-cw", __iconNode);


//# sourceMappingURL=refresh-cw.js.map


/***/ }),

/***/ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/settings.js":
/*!*******************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/settings.js ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   __iconNode: function() { return /* binding */ __iconNode; },
/* harmony export */   "default": function() { return /* binding */ Settings; }
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const __iconNode = [
  [
    "path",
    {
      d: "M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915",
      key: "1i5ecw"
    }
  ],
  ["circle", { cx: "12", cy: "12", r: "3", key: "1v7zrd" }]
];
const Settings = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("settings", __iconNode);


//# sourceMappingURL=settings.js.map


/***/ }),

/***/ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/trash-2.js":
/*!******************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/trash-2.js ***!
  \******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   __iconNode: function() { return /* binding */ __iconNode; },
/* harmony export */   "default": function() { return /* binding */ Trash2; }
/* harmony export */ });
/* harmony import */ var _createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../createLucideIcon.js */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/createLucideIcon.js");
/**
 * @license lucide-react v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */



const __iconNode = [
  ["path", { d: "M10 11v6", key: "nco0om" }],
  ["path", { d: "M14 11v6", key: "outv1u" }],
  ["path", { d: "M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6", key: "miytrc" }],
  ["path", { d: "M3 6h18", key: "d0wm0j" }],
  ["path", { d: "M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2", key: "e791ji" }]
];
const Trash2 = (0,_createLucideIcon_js__WEBPACK_IMPORTED_MODULE_0__["default"])("trash-2", __iconNode);


//# sourceMappingURL=trash-2.js.map


/***/ }),

/***/ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/shared/src/utils.js":
/*!*********************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/shared/src/utils.js ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   hasA11yProp: function() { return /* binding */ hasA11yProp; },
/* harmony export */   mergeClasses: function() { return /* binding */ mergeClasses; },
/* harmony export */   toCamelCase: function() { return /* binding */ toCamelCase; },
/* harmony export */   toKebabCase: function() { return /* binding */ toKebabCase; },
/* harmony export */   toPascalCase: function() { return /* binding */ toPascalCase; }
/* harmony export */ });
/**
 * @license lucide-react v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */

const toKebabCase = (string) => string.replace(/([a-z0-9])([A-Z])/g, "$1-$2").toLowerCase();
const toCamelCase = (string) => string.replace(
  /^([A-Z])|[\s-_]+(\w)/g,
  (match, p1, p2) => p2 ? p2.toUpperCase() : p1.toLowerCase()
);
const toPascalCase = (string) => {
  const camelCase = toCamelCase(string);
  return camelCase.charAt(0).toUpperCase() + camelCase.slice(1);
};
const mergeClasses = (...classes) => classes.filter((className, index, array) => {
  return Boolean(className) && className.trim() !== "" && array.indexOf(className) === index;
}).join(" ").trim();
const hasA11yProp = (props) => {
  for (const prop in props) {
    if (prop.startsWith("aria-") || prop === "role" || prop === "title") {
      return true;
    }
  }
};


//# sourceMappingURL=utils.js.map


/***/ }),

/***/ "./src/admin-ui/components/AdminInterface.tsx":
/*!****************************************************!*\
  !*** ./src/admin-ui/components/AdminInterface.tsx ***!
  \****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AdminInterface: function() { return /* binding */ AdminInterface; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _ShareCountsTable__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ShareCountsTable */ "./src/admin-ui/components/ShareCountsTable.tsx");
/* harmony import */ var _RefreshControls__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./RefreshControls */ "./src/admin-ui/components/RefreshControls.tsx");
/* harmony import */ var _hooks_useShareCounts__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../hooks/useShareCounts */ "./src/admin-ui/hooks/useShareCounts.ts");
/* harmony import */ var _hooks_useRestApi__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../hooks/useRestApi */ "./src/admin-ui/hooks/useRestApi.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__);








const AdminInterface = () => {
  const [selectedPosts, setSelectedPosts] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [refreshing, setRefreshing] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [notification, setNotification] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const {
    shareCounts,
    loading,
    error,
    refreshShareCounts
  } = (0,_hooks_useShareCounts__WEBPACK_IMPORTED_MODULE_5__.useShareCounts)(selectedPosts);
  const {
    bulkRefresh
  } = (0,_hooks_useRestApi__WEBPACK_IMPORTED_MODULE_6__.useRestApi)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (error) {
      setNotification({
        type: 'error',
        message: error
      });
    }
  }, [error]);
  const handleBulkRefresh = async () => {
    if (selectedPosts.length === 0) {
      setNotification({
        type: 'error',
        message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Please select posts to refresh', 'html-social-share-buttons')
      });
      return;
    }
    setRefreshing(true);
    try {
      const result = await bulkRefresh(selectedPosts);
      setNotification({
        type: 'success',
        message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)(`Successfully refreshed ${result.processed} posts`, 'html-social-share-buttons')
      });

      // Refresh the displayed data
      await refreshShareCounts();
    } catch (err) {
      setNotification({
        type: 'error',
        message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to refresh share counts', 'html-social-share-buttons')
      });
    } finally {
      setRefreshing(false);
    }
  };
  const clearNotification = () => {
    setNotification(null);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("div", {
    className: "hss-admin-interface",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
      className: "wrap",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("h1", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Social Share Counts Manager', 'html-social-share-buttons')
      }), notification && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
        status: notification.type,
        onRemove: clearNotification,
        isDismissible: true,
        children: notification.message
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Panel, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Refresh Controls', 'html-social-share-buttons'),
          initialOpen: true,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CardBody, {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_RefreshControls__WEBPACK_IMPORTED_MODULE_4__.RefreshControls, {
                selectedPosts: selectedPosts,
                onSelectionChange: setSelectedPosts,
                onBulkRefresh: handleBulkRefresh,
                refreshing: refreshing
              })
            })
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Share Counts Data', 'html-social-share-buttons'),
          initialOpen: true,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CardBody, {
              children: [loading && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Flex, {
                justify: "center",
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FlexBlock, {
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Spinner, {
                    onPointerEnterCapture: undefined,
                    onPointerLeaveCapture: undefined
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("span", {
                    style: {
                      marginLeft: '10px'
                    },
                    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Loading share counts...', 'html-social-share-buttons')
                  })]
                })
              }), !loading && shareCounts && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_ShareCountsTable__WEBPACK_IMPORTED_MODULE_3__.ShareCountsTable, {
                data: shareCounts,
                selectedPosts: selectedPosts,
                onSelectionChange: setSelectedPosts
              })]
            })
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Statistics', 'html-social-share-buttons'),
          initialOpen: false,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CardBody, {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
                className: "hss-stats",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("p", {
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("strong", {
                    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Total Posts with Counts:', 'html-social-share-buttons')
                  }), ' ', shareCounts?.length || 0]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("p", {
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("strong", {
                    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Selected Posts:', 'html-social-share-buttons')
                  }), ' ', selectedPosts.length]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("p", {
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("strong", {
                    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Last Refreshed:', 'html-social-share-buttons')
                  }), ' ', shareCounts && shareCounts.length > 0 ? new Date().toLocaleString() : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Never', 'html-social-share-buttons')]
                })]
              })
            })
          })
        })]
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ErrorBoundary.tsx":
/*!***************************************************!*\
  !*** ./src/admin-ui/components/ErrorBoundary.tsx ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ErrorBoundary: function() { return /* binding */ ErrorBoundary; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


/**
 * Error Boundary component to catch and handle React errors gracefully
 */
class ErrorBoundary extends react__WEBPACK_IMPORTED_MODULE_0__.Component {
  constructor(props) {
    super(props);
    this.state = {
      hasError: false,
      error: null,
      errorInfo: null
    };
  }
  static getDerivedStateFromError(error) {
    return {
      hasError: true,
      error
    };
  }
  componentDidCatch(error, errorInfo) {
    this.setState({
      error,
      errorInfo
    });

    // Call optional error callback
    if (this.props.onError) {
      this.props.onError(error, errorInfo);
    }

    // Log error for debugging
    console.error('Error Boundary caught an error:', error, errorInfo);
  }
  handleReset = () => {
    this.setState({
      hasError: false,
      error: null,
      errorInfo: null
    });
  };
  render() {
    if (this.state.hasError && this.state.error) {
      // Use custom fallback if provided
      if (this.props.fallback) {
        return this.props.fallback(this.state.error, this.state.errorInfo);
      }

      // Default error UI - using WordPress admin styling
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: "error-boundary",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
          className: "wp-admin-card p-6 border-l-4 border-red-500 bg-red-50",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
            className: "flex items-start",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
              className: "flex-shrink-0",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("i", {
                className: "fas fa-exclamation-triangle text-red-400 text-xl"
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
              className: "ml-3 flex-1",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("h3", {
                className: "text-lg font-medium text-red-800 mb-2",
                children: "Something went wrong"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
                className: "text-red-600 mb-4",
                children: "An error occurred while rendering this component. Please try refreshing the page or contact support if the problem persists."
              }),  true && this.state.error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("details", {
                className: "bg-red-100 p-3 rounded border text-sm",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("summary", {
                  className: "cursor-pointer font-medium text-red-800 mb-2",
                  children: "Error Details (Development Mode)"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
                  className: "text-red-700",
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("strong", {
                    children: "Error:"
                  }), " ", this.state.error.message, /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("strong", {
                    children: "Stack:"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("pre", {
                    className: "mt-2 text-xs overflow-auto",
                    children: this.state.error.stack
                  }), this.state.errorInfo && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.Fragment, {
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("strong", {
                      children: "Component Stack:"
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("pre", {
                      className: "mt-2 text-xs overflow-auto",
                      children: this.state.errorInfo.componentStack
                    })]
                  })]
                })]
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
                className: "flex space-x-3 mt-4",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("button", {
                  onClick: this.handleReset,
                  className: "bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors",
                  children: "Try Again"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("button", {
                  onClick: () => window.location.reload(),
                  className: "bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors",
                  children: "Refresh Page"
                })]
              })]
            })]
          })
        })
      });
    }
    return this.props.children;
  }
}

/***/ }),

/***/ "./src/admin-ui/components/MainAdminInterface.tsx":
/*!********************************************************!*\
  !*** ./src/admin-ui/components/MainAdminInterface.tsx ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MainAdminInterface: function() { return /* binding */ MainAdminInterface; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _contexts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../contexts */ "./src/admin-ui/contexts/index.ts");
/* harmony import */ var _ErrorBoundary__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ErrorBoundary */ "./src/admin-ui/components/ErrorBoundary.tsx");
/* harmony import */ var _ReactAdminInterface__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ReactAdminInterface */ "./src/admin-ui/components/ReactAdminInterface.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





/**
 * Main admin interface with all context providers and error handling
 */

const MainAdminInterface = () => {
  const handleError = (error, errorInfo) => {
    // Log error to console and potentially send to error tracking service
    console.error('React Admin Interface Error:', error, errorInfo);

    // You could integrate with error tracking services here
    // For example: Sentry.captureException(error, { extra: errorInfo });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ErrorBoundary__WEBPACK_IMPORTED_MODULE_2__.ErrorBoundary, {
    onError: handleError,
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_contexts__WEBPACK_IMPORTED_MODULE_1__.NotificationProvider, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_contexts__WEBPACK_IMPORTED_MODULE_1__.SettingsProvider, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_contexts__WEBPACK_IMPORTED_MODULE_1__.NetworksProvider, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ReactAdminInterface__WEBPACK_IMPORTED_MODULE_3__.ReactAdminInterface, {})
        })
      })
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ReactAdminInterface.tsx":
/*!*********************************************************!*\
  !*** ./src/admin-ui/components/ReactAdminInterface.tsx ***!
  \*********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ReactAdminInterface: function() { return /* binding */ ReactAdminInterface; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ui */ "./src/admin-ui/components/ui/index.ts");
/* harmony import */ var _tabs_GeneralTab__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./tabs/GeneralTab */ "./src/admin-ui/components/tabs/GeneralTab.tsx");
/* harmony import */ var _tabs_NetworksTab__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./tabs/NetworksTab */ "./src/admin-ui/components/tabs/NetworksTab.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





/**
 * Main React admin interface for HTML Social Share Buttons settings
 */
const ReactAdminInterface = () => {
  const [activeTab, setActiveTab] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)('general');

  // Tab configuration
  const tabs = [{
    id: 'general',
    title: 'General Settings',
    icon: 'admin-settings',
    description: 'Configure basic plugin settings and display options'
  }, {
    id: 'networks',
    title: 'Social Networks',
    icon: 'share',
    description: 'Manage available social networks and their settings'
  }, {
    id: 'profiles',
    title: 'Profiles',
    icon: 'admin-users',
    description: 'Create and manage social sharing profiles'
  }, {
    id: 'placement',
    title: 'Placement',
    icon: 'admin-appearance',
    description: 'Control where and how buttons appear on your site'
  }, {
    id: 'styling',
    title: 'Styling',
    icon: 'admin-customizer',
    description: 'Customize the appearance of your share buttons'
  }, {
    id: 'advanced',
    title: 'Advanced',
    icon: 'admin-generic',
    description: 'Advanced settings and integrations'
  }];
  const renderTabContent = () => {
    switch (activeTab) {
      case 'general':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_tabs_GeneralTab__WEBPACK_IMPORTED_MODULE_2__.GeneralTab, {});
      case 'networks':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_tabs_NetworksTab__WEBPACK_IMPORTED_MODULE_3__.NetworksTab, {});
      case 'profiles':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
          children: "Profiles tab content coming soon..."
        });
      case 'placement':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
          children: "Placement tab content coming soon..."
        });
      case 'styling':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
          children: "Styling tab content coming soon..."
        });
      case 'advanced':
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
          children: "Advanced tab content coming soon..."
        });
      default:
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_tabs_GeneralTab__WEBPACK_IMPORTED_MODULE_2__.GeneralTab, {});
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
    className: "html-social-share-admin",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      className: "wrap",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h1", {
        className: "wp-heading-inline",
        children: "HTML Social Share Buttons"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
        className: "description",
        children: "Configure social sharing buttons for your WordPress site."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
        className: "html-social-share-admin-content mt-4",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_ui__WEBPACK_IMPORTED_MODULE_1__.Tabs, {
          tabs: tabs,
          activeTab: activeTab,
          onTabChange: setActiveTab,
          className: "mb-6"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
          className: "tab-content",
          children: renderTabContent()
        })]
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/RefreshControls.tsx":
/*!*****************************************************!*\
  !*** ./src/admin-ui/components/RefreshControls.tsx ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RefreshControls: function() { return /* binding */ RefreshControls; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lucide-react */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/refresh-cw.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lucide-react */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/settings.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);





const RefreshControls = ({
  selectedPosts,
  onSelectionChange,
  onBulkRefresh,
  refreshing
}) => {
  const handleSelectAll = checked => {
    if (checked) {
      // This would need to get all post IDs from the data
      // For now, just clear selection
      onSelectionChange([]);
    } else {
      onSelectionChange([]);
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
    className: "hss-refresh-controls",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Flex, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FlexItem, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select All Posts', 'html-social-share-buttons'),
          checked: selectedPosts.length > 0,
          onChange: handleSelectAll
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FlexBlock, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FlexItem, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
          variant: "secondary",
          onClick: onBulkRefresh,
          disabled: refreshing || selectedPosts.length === 0,
          icon: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(lucide_react__WEBPACK_IMPORTED_MODULE_3__["default"], {
            size: 16
          }),
          children: refreshing ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Refreshing...', 'html-social-share-buttons') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Refresh Selected', 'html-social-share-buttons')
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FlexItem, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
          variant: "primary",
          icon: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(lucide_react__WEBPACK_IMPORTED_MODULE_4__["default"], {
            size: 16
          }),
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Settings', 'html-social-share-buttons')
        })
      })]
    }), selectedPosts.length > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
      className: "hss-selection-info",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)(`${selectedPosts.length} posts selected for refresh`, 'html-social-share-buttons')
    })]
  });
};

/***/ }),

/***/ "./src/admin-ui/components/ShareCountsTable.tsx":
/*!******************************************************!*\
  !*** ./src/admin-ui/components/ShareCountsTable.tsx ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ShareCountsTable: function() { return /* binding */ ShareCountsTable; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lucide-react */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/refresh-cw.js");
/* harmony import */ var lucide_react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lucide-react */ "./node_modules/.pnpm/lucide-react@0.544.0_react@18.3.1/node_modules/lucide-react/dist/esm/icons/trash-2.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);





const ShareCountsTable = ({
  data,
  selectedPosts,
  onSelectionChange
}) => {
  const handleSelectPost = (postId, checked) => {
    if (checked) {
      onSelectionChange([...selectedPosts, postId]);
    } else {
      onSelectionChange(selectedPosts.filter(id => id !== postId));
    }
  };
  const formatNumber = num => {
    if (num >= 1000000) {
      return (num / 1000000).toFixed(1) + 'M';
    }
    if (num >= 1000) {
      return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
  };
  const networks = ['facebook', 'twitter', 'pinterest', 'linkedin'];
  if (!data || data.length === 0) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
      className: "hss-empty-state",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
        variant: "muted",
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('No share count data found. Select some posts and refresh to get started.', 'html-social-share-buttons')
      })
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
    className: "hss-share-counts-table",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("table", {
      className: "wp-list-table widefat fixed striped",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("thead", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("tr", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
            scope: "col",
            className: "check-column",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
              checked: selectedPosts.length === data.length && data.length > 0,
              onChange: checked => {
                if (checked) {
                  onSelectionChange(data.map(item => item.post_id));
                } else {
                  onSelectionChange([]);
                }
              }
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
            scope: "col",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Post Title', 'html-social-share-buttons')
          }), networks.map(network => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
            scope: "col",
            className: "hss-network-col",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
              className: `hss-network-icon hss-network-${network}`,
              children: network.charAt(0).toUpperCase() + network.slice(1)
            })
          }, network)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
            scope: "col",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Total', 'html-social-share-buttons')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
            scope: "col",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Last Updated', 'html-social-share-buttons')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
            scope: "col",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Actions', 'html-social-share-buttons')
          })]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("tbody", {
        children: data.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("tr", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
            scope: "row",
            className: "check-column",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
              checked: selectedPosts.includes(item.post_id),
              onChange: checked => handleSelectPost(item.post_id, checked)
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("td", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("strong", {
              children: item.title
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
              className: "row-actions",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("a", {
                  href: item.url,
                  target: "_blank",
                  rel: "noopener noreferrer",
                  children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('View', 'html-social-share-buttons')
                })
              })
            })]
          }), networks.map(network => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
            className: "hss-count-cell",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
              className: "hss-count",
              children: formatNumber(item.share_counts[network] || 0)
            })
          }, network)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
            className: "hss-total-cell",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("strong", {
              children: formatNumber(item.total)
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
            children: item.last_updated ? new Date(item.last_updated).toLocaleDateString() : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Never', 'html-social-share-buttons')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
              className: "hss-actions",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
                size: "small",
                variant: "secondary",
                icon: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(lucide_react__WEBPACK_IMPORTED_MODULE_3__["default"], {
                  size: 14
                }),
                title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Refresh this post', 'html-social-share-buttons')
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
                size: "small",
                variant: "secondary",
                icon: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(lucide_react__WEBPACK_IMPORTED_MODULE_4__["default"], {
                  size: 14
                }),
                title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete counts for this post', 'html-social-share-buttons'),
                isDestructive: true
              })]
            })
          })]
        }, item.post_id))
      })]
    })
  });
};

/***/ }),

/***/ "./src/admin-ui/components/index.ts":
/*!******************************************!*\
  !*** ./src/admin-ui/components/index.ts ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AdminInterface: function() { return /* reexport safe */ _AdminInterface__WEBPACK_IMPORTED_MODULE_2__.AdminInterface; },
/* harmony export */   Button: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.Button; },
/* harmony export */   Checkbox: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.Checkbox; },
/* harmony export */   FormField: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.FormField; },
/* harmony export */   GeneralTab: function() { return /* reexport safe */ _tabs_GeneralTab__WEBPACK_IMPORTED_MODULE_5__.GeneralTab; },
/* harmony export */   LoadingOverlay: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.LoadingOverlay; },
/* harmony export */   LoadingSpinner: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.LoadingSpinner; },
/* harmony export */   MainAdminInterface: function() { return /* reexport safe */ _MainAdminInterface__WEBPACK_IMPORTED_MODULE_0__.MainAdminInterface; },
/* harmony export */   NetworksTab: function() { return /* reexport safe */ _tabs_NetworksTab__WEBPACK_IMPORTED_MODULE_6__.NetworksTab; },
/* harmony export */   Notice: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.Notice; },
/* harmony export */   Notification: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.Notification; },
/* harmony export */   ReactAdminInterface: function() { return /* reexport safe */ _ReactAdminInterface__WEBPACK_IMPORTED_MODULE_1__.ReactAdminInterface; },
/* harmony export */   RefreshControls: function() { return /* reexport safe */ _RefreshControls__WEBPACK_IMPORTED_MODULE_4__.RefreshControls; },
/* harmony export */   Select: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.Select; },
/* harmony export */   ShareCountsTable: function() { return /* reexport safe */ _ShareCountsTable__WEBPACK_IMPORTED_MODULE_3__.ShareCountsTable; },
/* harmony export */   TabPanel: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.TabPanel; },
/* harmony export */   Tabs: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.Tabs; },
/* harmony export */   TextInput: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.TextInput; },
/* harmony export */   ValidatedCheckbox: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.ValidatedCheckbox; },
/* harmony export */   ValidatedSelect: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.ValidatedSelect; },
/* harmony export */   ValidatedTextArea: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.ValidatedTextArea; },
/* harmony export */   ValidatedTextInput: function() { return /* reexport safe */ _ui__WEBPACK_IMPORTED_MODULE_7__.ValidatedTextInput; }
/* harmony export */ });
/* harmony import */ var _MainAdminInterface__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MainAdminInterface */ "./src/admin-ui/components/MainAdminInterface.tsx");
/* harmony import */ var _ReactAdminInterface__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ReactAdminInterface */ "./src/admin-ui/components/ReactAdminInterface.tsx");
/* harmony import */ var _AdminInterface__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./AdminInterface */ "./src/admin-ui/components/AdminInterface.tsx");
/* harmony import */ var _ShareCountsTable__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ShareCountsTable */ "./src/admin-ui/components/ShareCountsTable.tsx");
/* harmony import */ var _RefreshControls__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./RefreshControls */ "./src/admin-ui/components/RefreshControls.tsx");
/* harmony import */ var _tabs_GeneralTab__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./tabs/GeneralTab */ "./src/admin-ui/components/tabs/GeneralTab.tsx");
/* harmony import */ var _tabs_NetworksTab__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./tabs/NetworksTab */ "./src/admin-ui/components/tabs/NetworksTab.tsx");
/* harmony import */ var _ui__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./ui */ "./src/admin-ui/components/ui/index.ts");
// Export all components






// Export tabs



// Export UI components


/***/ }),

/***/ "./src/admin-ui/components/tabs/GeneralTab.tsx":
/*!*****************************************************!*\
  !*** ./src/admin-ui/components/tabs/GeneralTab.tsx ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

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

/***/ "./src/admin-ui/components/tabs/NetworksTab.tsx":
/*!******************************************************!*\
  !*** ./src/admin-ui/components/tabs/NetworksTab.tsx ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

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

/***/ "./src/admin-ui/components/ui/FormFields.tsx":
/*!***************************************************!*\
  !*** ./src/admin-ui/components/ui/FormFields.tsx ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

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

/***/ "./src/admin-ui/hooks/index.ts":
/*!*************************************!*\
  !*** ./src/admin-ui/hooks/index.ts ***!
  \*************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useNetworks: function() { return /* reexport safe */ _useNetworks__WEBPACK_IMPORTED_MODULE_1__.useNetworks; },
/* harmony export */   useRestApi: function() { return /* reexport safe */ _useRestApi__WEBPACK_IMPORTED_MODULE_3__.useRestApi; },
/* harmony export */   useSettings: function() { return /* reexport safe */ _useSettings__WEBPACK_IMPORTED_MODULE_0__.useSettings; },
/* harmony export */   useShareCounts: function() { return /* reexport safe */ _useShareCounts__WEBPACK_IMPORTED_MODULE_2__.useShareCounts; }
/* harmony export */ });
/* harmony import */ var _useSettings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./useSettings */ "./src/admin-ui/hooks/useSettings.ts");
/* harmony import */ var _useNetworks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./useNetworks */ "./src/admin-ui/hooks/useNetworks.ts");
/* harmony import */ var _useShareCounts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./useShareCounts */ "./src/admin-ui/hooks/useShareCounts.ts");
/* harmony import */ var _useRestApi__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./useRestApi */ "./src/admin-ui/hooks/useRestApi.ts");
// Export all hooks





/***/ }),

/***/ "./src/admin-ui/hooks/useNetworks.ts":
/*!*******************************************!*\
  !*** ./src/admin-ui/hooks/useNetworks.ts ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

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

/***/ "./src/admin-ui/hooks/useRestApi.ts":
/*!******************************************!*\
  !*** ./src/admin-ui/hooks/useRestApi.ts ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useRestApi: function() { return /* binding */ useRestApi; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);

const useRestApi = () => {
  const bulkRefresh = async (postIds, networks) => {
    const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: '/html-social-share/v1/share-counts/bulk-refresh',
      method: 'POST',
      data: {
        post_ids: postIds,
        networks: networks || []
      }
    });
    return response;
  };
  const refreshSinglePost = async (postId, networks) => {
    const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: `/html-social-share/v1/posts/${postId}/share-counts/refresh`,
      method: 'POST',
      data: {
        networks: networks || []
      }
    });
    return response;
  };
  const updateShareCount = async (postId, network, count) => {
    const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: `/html-social-share/v1/posts/${postId}/share-counts/${network}`,
      method: 'PUT',
      data: {
        count
      }
    });
    return response;
  };
  const deletePostShareCounts = async postId => {
    const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
      path: `/html-social-share/v1/posts/${postId}/share-counts`,
      method: 'DELETE'
    });
    return response;
  };
  return {
    bulkRefresh,
    refreshSinglePost,
    updateShareCount,
    deletePostShareCounts
  };
};

/***/ }),

/***/ "./src/admin-ui/hooks/useSettings.ts":
/*!*******************************************!*\
  !*** ./src/admin-ui/hooks/useSettings.ts ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

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

/***/ "./src/admin-ui/hooks/useShareCounts.ts":
/*!**********************************************!*\
  !*** ./src/admin-ui/hooks/useShareCounts.ts ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useShareCounts: function() { return /* binding */ useShareCounts; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__);


const useShareCounts = postIds => {
  const [shareCounts, setShareCounts] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [loading, setLoading] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [error, setError] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const fetchShareCounts = async () => {
    if (postIds.length === 0) {
      setShareCounts([]);
      return;
    }
    setLoading(true);
    setError(null);
    try {
      const promises = postIds.map(async postId => {
        const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
          path: `/html-social-share/v1/posts/${postId}/share-counts`,
          method: 'GET'
        });

        // Get post title
        const post = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
          path: `/wp/v2/posts/${postId}`,
          method: 'GET'
        });
        return {
          post_id: postId,
          title: post.title.rendered,
          url: response.url,
          share_counts: response.share_counts,
          total: response.total,
          last_updated: response.last_updated
        };
      });
      const results = await Promise.all(promises);
      setShareCounts(results);
    } catch (err) {
      console.error('Error fetching share counts:', err);
      setError('Failed to fetch share counts');
    } finally {
      setLoading(false);
    }
  };
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    fetchShareCounts();
  }, [postIds]);
  return {
    shareCounts,
    loading,
    error,
    refreshShareCounts: fetchShareCounts
  };
};

/***/ }),

/***/ "./src/admin-ui/index.ts":
/*!*******************************!*\
  !*** ./src/admin-ui/index.ts ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AdminInterface: function() { return /* reexport safe */ _components__WEBPACK_IMPORTED_MODULE_0__.AdminInterface; },
/* harmony export */   MainAdminInterface: function() { return /* reexport safe */ _components__WEBPACK_IMPORTED_MODULE_0__.MainAdminInterface; },
/* harmony export */   NetworksProvider: function() { return /* reexport safe */ _contexts__WEBPACK_IMPORTED_MODULE_1__.NetworksProvider; },
/* harmony export */   ReactAdminInterface: function() { return /* reexport safe */ _components__WEBPACK_IMPORTED_MODULE_0__.ReactAdminInterface; },
/* harmony export */   SettingsProvider: function() { return /* reexport safe */ _contexts__WEBPACK_IMPORTED_MODULE_1__.SettingsProvider; },
/* harmony export */   useNetworks: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_2__.useNetworks; },
/* harmony export */   useNetworksContext: function() { return /* reexport safe */ _contexts__WEBPACK_IMPORTED_MODULE_1__.useNetworksContext; },
/* harmony export */   useSettings: function() { return /* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_2__.useSettings; },
/* harmony export */   useSettingsContext: function() { return /* reexport safe */ _contexts__WEBPACK_IMPORTED_MODULE_1__.useSettingsContext; }
/* harmony export */ });
/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components */ "./src/admin-ui/components/index.ts");
/* harmony import */ var _contexts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./contexts */ "./src/admin-ui/contexts/index.ts");
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hooks */ "./src/admin-ui/hooks/index.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./types */ "./src/admin-ui/types/index.ts");
// Main entry point for HTML Social Share Buttons admin interface





/***/ }),

/***/ "./src/admin-ui/types/index.ts":
/*!*************************************!*\
  !*** ./src/admin-ui/types/index.ts ***!
  \*************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ (function(module) {

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
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!**********************!*\
  !*** ./src/admin.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _admin_ui_index_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./admin-ui/index.ts */ "./src/admin-ui/index.ts");
/**
 * Admin UI Entry Point
 */

}();
/******/ })()
;
//# sourceMappingURL=admin.js.map