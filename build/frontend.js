/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/frontend/index.js":
/*!*******************************!*\
  !*** ./src/frontend/index.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wechat_toggle_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./wechat-toggle.js */ "./src/frontend/wechat-toggle.js");
/* harmony import */ var _wechat_toggle_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wechat_toggle_js__WEBPACK_IMPORTED_MODULE_0__);
/**
 * HTML Social Share Buttons - Frontend Scripts
 *
 * This file serves as the main entry point for all frontend JavaScript functionality.
 */

// Import WeChat toggle functionality


// Initialize frontend functionality when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
  console.log('HTML Social Share Buttons frontend loaded');
});

/***/ }),

/***/ "./src/frontend/wechat-toggle.js":
/*!***************************************!*\
  !*** ./src/frontend/wechat-toggle.js ***!
  \***************************************/
/***/ (function() {

(function () {
  'use strict';

  function toggleQr(button) {
    var container = button.closest('.hssb-wechat');
    if (!container) return;
    var qr = container.querySelector('.hssb-wechat-qr');
    if (!qr) return;
    var isVisible = qr.classList.contains('hssb-wechat-qr--visible');
    if (isVisible) {
      qr.classList.remove('hssb-wechat-qr--visible');
      qr.style.display = 'none';
      button.setAttribute('aria-expanded', 'false');
    } else {
      qr.classList.add('hssb-wechat-qr--visible');
      qr.style.display = '';
      button.setAttribute('aria-expanded', 'true');
    }
  }
  function onDocumentClick(e) {
    var btn = e.target.closest('.hssb-wechat-btn');
    if (btn) {
      e.preventDefault();
      toggleQr(btn);
      return;
    }

    // Click outside any QR container closes all visible QR areas
    var openQrs = document.querySelectorAll('.hssb-wechat-qr.hssb-wechat-qr--visible');
    if (openQrs.length) {
      openQrs.forEach(function (q) {
        q.classList.remove('hssb-wechat-qr--visible');
        q.style.display = 'none';
        var container = q.closest('.hssb-wechat');
        if (container) {
          var b = container.querySelector('.hssb-wechat-btn');
          if (b) b.setAttribute('aria-expanded', 'false');
        }
      });
    }
  }
  function onKeydown(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
      var openQrs = document.querySelectorAll('.hssb-wechat-qr.hssb-wechat-qr--visible');
      openQrs.forEach(function (q) {
        q.classList.remove('hssb-wechat-qr--visible');
        q.style.display = 'none';
        var container = q.closest('.hssb-wechat');
        if (container) {
          var b = container.querySelector('.hssb-wechat-btn');
          if (b) b.setAttribute('aria-expanded', 'false');
        }
      });
    }
  }
  document.addEventListener('click', onDocumentClick);
  document.addEventListener('keydown', onKeydown);
})();

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
/*!*************************!*\
  !*** ./src/frontend.js ***!
  \*************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _frontend_index__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./frontend/index */ "./src/frontend/index.js");
/**
 * Frontend Scripts Entry Point
 */

}();
/******/ })()
;
//# sourceMappingURL=frontend.js.map