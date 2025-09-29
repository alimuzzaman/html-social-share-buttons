import oe, { useState as P } from "react";
import jr from "react-dom";
var ie = { exports: {} }, M = {};
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
var Oe;
function _r() {
  if (Oe)
    return M;
  Oe = 1;
  var s = oe, o = Symbol.for("react.element"), f = Symbol.for("react.fragment"), l = Object.prototype.hasOwnProperty, m = s.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner, p = { key: !0, ref: !0, __self: !0, __source: !0 };
  function _(N, g, E) {
    var u, d = {}, b = null, w = null;
    E !== void 0 && (b = "" + E), g.key !== void 0 && (b = "" + g.key), g.ref !== void 0 && (w = g.ref);
    for (u in g)
      l.call(g, u) && !p.hasOwnProperty(u) && (d[u] = g[u]);
    if (N && N.defaultProps)
      for (u in g = N.defaultProps, g)
        d[u] === void 0 && (d[u] = g[u]);
    return { $$typeof: o, type: N, key: b, ref: w, props: d, _owner: m.current };
  }
  return M.Fragment = f, M.jsx = _, M.jsxs = _, M;
}
var q = {};
/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
var De;
function Nr() {
  return De || (De = 1, process.env.NODE_ENV !== "production" && function() {
    var s = oe, o = Symbol.for("react.element"), f = Symbol.for("react.portal"), l = Symbol.for("react.fragment"), m = Symbol.for("react.strict_mode"), p = Symbol.for("react.profiler"), _ = Symbol.for("react.provider"), N = Symbol.for("react.context"), g = Symbol.for("react.forward_ref"), E = Symbol.for("react.suspense"), u = Symbol.for("react.suspense_list"), d = Symbol.for("react.memo"), b = Symbol.for("react.lazy"), w = Symbol.for("react.offscreen"), k = Symbol.iterator, We = "@@iterator";
    function Ye(e) {
      if (e === null || typeof e != "object")
        return null;
      var t = k && e[k] || e[We];
      return typeof t == "function" ? t : null;
    }
    var A = s.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
    function j(e) {
      {
        for (var t = arguments.length, a = new Array(t > 1 ? t - 1 : 0), n = 1; n < t; n++)
          a[n - 1] = arguments[n];
        Me("error", e, a);
      }
    }
    function Me(e, t, a) {
      {
        var n = A.ReactDebugCurrentFrame, h = n.getStackAddendum();
        h !== "" && (t += "%s", a = a.concat([h]));
        var v = a.map(function(c) {
          return String(c);
        });
        v.unshift("Warning: " + t), Function.prototype.apply.call(console[e], console, v);
      }
    }
    var qe = !1, Be = !1, Ue = !1, Ve = !1, ze = !1, le;
    le = Symbol.for("react.module.reference");
    function Ge(e) {
      return !!(typeof e == "string" || typeof e == "function" || e === l || e === p || ze || e === m || e === E || e === u || Ve || e === w || qe || Be || Ue || typeof e == "object" && e !== null && (e.$$typeof === b || e.$$typeof === d || e.$$typeof === _ || e.$$typeof === N || e.$$typeof === g || // This needs to include all possible module reference object
      // types supported by any Flight configuration anywhere since
      // we don't know which Flight build this will end up being used
      // with.
      e.$$typeof === le || e.getModuleId !== void 0));
    }
    function Je(e, t, a) {
      var n = e.displayName;
      if (n)
        return n;
      var h = t.displayName || t.name || "";
      return h !== "" ? a + "(" + h + ")" : a;
    }
    function ce(e) {
      return e.displayName || "Context";
    }
    function T(e) {
      if (e == null)
        return null;
      if (typeof e.tag == "number" && j("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."), typeof e == "function")
        return e.displayName || e.name || null;
      if (typeof e == "string")
        return e;
      switch (e) {
        case l:
          return "Fragment";
        case f:
          return "Portal";
        case p:
          return "Profiler";
        case m:
          return "StrictMode";
        case E:
          return "Suspense";
        case u:
          return "SuspenseList";
      }
      if (typeof e == "object")
        switch (e.$$typeof) {
          case N:
            var t = e;
            return ce(t) + ".Consumer";
          case _:
            var a = e;
            return ce(a._context) + ".Provider";
          case g:
            return Je(e, e.render, "ForwardRef");
          case d:
            var n = e.displayName || null;
            return n !== null ? n : T(e.type) || "Memo";
          case b: {
            var h = e, v = h._payload, c = h._init;
            try {
              return T(c(v));
            } catch {
              return null;
            }
          }
        }
      return null;
    }
    var O = Object.assign, L = 0, ue, de, fe, pe, he, me, ve;
    function ge() {
    }
    ge.__reactDisabledLog = !0;
    function Ke() {
      {
        if (L === 0) {
          ue = console.log, de = console.info, fe = console.warn, pe = console.error, he = console.group, me = console.groupCollapsed, ve = console.groupEnd;
          var e = {
            configurable: !0,
            enumerable: !0,
            value: ge,
            writable: !0
          };
          Object.defineProperties(console, {
            info: e,
            log: e,
            warn: e,
            error: e,
            group: e,
            groupCollapsed: e,
            groupEnd: e
          });
        }
        L++;
      }
    }
    function Xe() {
      {
        if (L--, L === 0) {
          var e = {
            configurable: !0,
            enumerable: !0,
            writable: !0
          };
          Object.defineProperties(console, {
            log: O({}, e, {
              value: ue
            }),
            info: O({}, e, {
              value: de
            }),
            warn: O({}, e, {
              value: fe
            }),
            error: O({}, e, {
              value: pe
            }),
            group: O({}, e, {
              value: he
            }),
            groupCollapsed: O({}, e, {
              value: me
            }),
            groupEnd: O({}, e, {
              value: ve
            })
          });
        }
        L < 0 && j("disabledDepth fell below zero. This is a bug in React. Please file an issue.");
      }
    }
    var H = A.ReactCurrentDispatcher, Z;
    function z(e, t, a) {
      {
        if (Z === void 0)
          try {
            throw Error();
          } catch (h) {
            var n = h.stack.trim().match(/\n( *(at )?)/);
            Z = n && n[1] || "";
          }
        return `
` + Z + e;
      }
    }
    var Q = !1, G;
    {
      var He = typeof WeakMap == "function" ? WeakMap : Map;
      G = new He();
    }
    function be(e, t) {
      if (!e || Q)
        return "";
      {
        var a = G.get(e);
        if (a !== void 0)
          return a;
      }
      var n;
      Q = !0;
      var h = Error.prepareStackTrace;
      Error.prepareStackTrace = void 0;
      var v;
      v = H.current, H.current = null, Ke();
      try {
        if (t) {
          var c = function() {
            throw Error();
          };
          if (Object.defineProperty(c.prototype, "props", {
            set: function() {
              throw Error();
            }
          }), typeof Reflect == "object" && Reflect.construct) {
            try {
              Reflect.construct(c, []);
            } catch (C) {
              n = C;
            }
            Reflect.construct(e, [], c);
          } else {
            try {
              c.call();
            } catch (C) {
              n = C;
            }
            e.call(c.prototype);
          }
        } else {
          try {
            throw Error();
          } catch (C) {
            n = C;
          }
          e();
        }
      } catch (C) {
        if (C && n && typeof C.stack == "string") {
          for (var i = C.stack.split(`
`), R = n.stack.split(`
`), x = i.length - 1, y = R.length - 1; x >= 1 && y >= 0 && i[x] !== R[y]; )
            y--;
          for (; x >= 1 && y >= 0; x--, y--)
            if (i[x] !== R[y]) {
              if (x !== 1 || y !== 1)
                do
                  if (x--, y--, y < 0 || i[x] !== R[y]) {
                    var S = `
` + i[x].replace(" at new ", " at ");
                    return e.displayName && S.includes("<anonymous>") && (S = S.replace("<anonymous>", e.displayName)), typeof e == "function" && G.set(e, S), S;
                  }
                while (x >= 1 && y >= 0);
              break;
            }
        }
      } finally {
        Q = !1, H.current = v, Xe(), Error.prepareStackTrace = h;
      }
      var I = e ? e.displayName || e.name : "", D = I ? z(I) : "";
      return typeof e == "function" && G.set(e, D), D;
    }
    function Ze(e, t, a) {
      return be(e, !1);
    }
    function Qe(e) {
      var t = e.prototype;
      return !!(t && t.isReactComponent);
    }
    function J(e, t, a) {
      if (e == null)
        return "";
      if (typeof e == "function")
        return be(e, Qe(e));
      if (typeof e == "string")
        return z(e);
      switch (e) {
        case E:
          return z("Suspense");
        case u:
          return z("SuspenseList");
      }
      if (typeof e == "object")
        switch (e.$$typeof) {
          case g:
            return Ze(e.render);
          case d:
            return J(e.type, t, a);
          case b: {
            var n = e, h = n._payload, v = n._init;
            try {
              return J(v(h), t, a);
            } catch {
            }
          }
        }
      return "";
    }
    var W = Object.prototype.hasOwnProperty, xe = {}, ye = A.ReactDebugCurrentFrame;
    function K(e) {
      if (e) {
        var t = e._owner, a = J(e.type, e._source, t ? t.type : null);
        ye.setExtraStackFrame(a);
      } else
        ye.setExtraStackFrame(null);
    }
    function er(e, t, a, n, h) {
      {
        var v = Function.call.bind(W);
        for (var c in e)
          if (v(e, c)) {
            var i = void 0;
            try {
              if (typeof e[c] != "function") {
                var R = Error((n || "React class") + ": " + a + " type `" + c + "` is invalid; it must be a function, usually from the `prop-types` package, but received `" + typeof e[c] + "`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");
                throw R.name = "Invariant Violation", R;
              }
              i = e[c](t, c, n, a, null, "SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED");
            } catch (x) {
              i = x;
            }
            i && !(i instanceof Error) && (K(h), j("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).", n || "React class", a, c, typeof i), K(null)), i instanceof Error && !(i.message in xe) && (xe[i.message] = !0, K(h), j("Failed %s type: %s", a, i.message), K(null));
          }
      }
    }
    var rr = Array.isArray;
    function ee(e) {
      return rr(e);
    }
    function tr(e) {
      {
        var t = typeof Symbol == "function" && Symbol.toStringTag, a = t && e[Symbol.toStringTag] || e.constructor.name || "Object";
        return a;
      }
    }
    function ar(e) {
      try {
        return we(e), !1;
      } catch {
        return !0;
      }
    }
    function we(e) {
      return "" + e;
    }
    function je(e) {
      if (ar(e))
        return j("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.", tr(e)), we(e);
    }
    var Y = A.ReactCurrentOwner, nr = {
      key: !0,
      ref: !0,
      __self: !0,
      __source: !0
    }, _e, Ne, re;
    re = {};
    function sr(e) {
      if (W.call(e, "ref")) {
        var t = Object.getOwnPropertyDescriptor(e, "ref").get;
        if (t && t.isReactWarning)
          return !1;
      }
      return e.ref !== void 0;
    }
    function ir(e) {
      if (W.call(e, "key")) {
        var t = Object.getOwnPropertyDescriptor(e, "key").get;
        if (t && t.isReactWarning)
          return !1;
      }
      return e.key !== void 0;
    }
    function or(e, t) {
      if (typeof e.ref == "string" && Y.current && t && Y.current.stateNode !== t) {
        var a = T(Y.current.type);
        re[a] || (j('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. This case cannot be automatically converted to an arrow function. We ask you to manually fix this case by using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref', T(Y.current.type), e.ref), re[a] = !0);
      }
    }
    function lr(e, t) {
      {
        var a = function() {
          _e || (_e = !0, j("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)", t));
        };
        a.isReactWarning = !0, Object.defineProperty(e, "key", {
          get: a,
          configurable: !0
        });
      }
    }
    function cr(e, t) {
      {
        var a = function() {
          Ne || (Ne = !0, j("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)", t));
        };
        a.isReactWarning = !0, Object.defineProperty(e, "ref", {
          get: a,
          configurable: !0
        });
      }
    }
    var ur = function(e, t, a, n, h, v, c) {
      var i = {
        // This tag allows us to uniquely identify this as a React Element
        $$typeof: o,
        // Built-in properties that belong on the element
        type: e,
        key: t,
        ref: a,
        props: c,
        // Record the component responsible for creating this element.
        _owner: v
      };
      return i._store = {}, Object.defineProperty(i._store, "validated", {
        configurable: !1,
        enumerable: !1,
        writable: !0,
        value: !1
      }), Object.defineProperty(i, "_self", {
        configurable: !1,
        enumerable: !1,
        writable: !1,
        value: n
      }), Object.defineProperty(i, "_source", {
        configurable: !1,
        enumerable: !1,
        writable: !1,
        value: h
      }), Object.freeze && (Object.freeze(i.props), Object.freeze(i)), i;
    };
    function dr(e, t, a, n, h) {
      {
        var v, c = {}, i = null, R = null;
        a !== void 0 && (je(a), i = "" + a), ir(t) && (je(t.key), i = "" + t.key), sr(t) && (R = t.ref, or(t, h));
        for (v in t)
          W.call(t, v) && !nr.hasOwnProperty(v) && (c[v] = t[v]);
        if (e && e.defaultProps) {
          var x = e.defaultProps;
          for (v in x)
            c[v] === void 0 && (c[v] = x[v]);
        }
        if (i || R) {
          var y = typeof e == "function" ? e.displayName || e.name || "Unknown" : e;
          i && lr(c, y), R && cr(c, y);
        }
        return ur(e, i, R, h, n, Y.current, c);
      }
    }
    var te = A.ReactCurrentOwner, Ee = A.ReactDebugCurrentFrame;
    function F(e) {
      if (e) {
        var t = e._owner, a = J(e.type, e._source, t ? t.type : null);
        Ee.setExtraStackFrame(a);
      } else
        Ee.setExtraStackFrame(null);
    }
    var ae;
    ae = !1;
    function ne(e) {
      return typeof e == "object" && e !== null && e.$$typeof === o;
    }
    function Re() {
      {
        if (te.current) {
          var e = T(te.current.type);
          if (e)
            return `

Check the render method of \`` + e + "`.";
        }
        return "";
      }
    }
    function fr(e) {
      {
        if (e !== void 0) {
          var t = e.fileName.replace(/^.*[\\\/]/, ""), a = e.lineNumber;
          return `

Check your code at ` + t + ":" + a + ".";
        }
        return "";
      }
    }
    var Ce = {};
    function pr(e) {
      {
        var t = Re();
        if (!t) {
          var a = typeof e == "string" ? e : e.displayName || e.name;
          a && (t = `

Check the top-level render call using <` + a + ">.");
        }
        return t;
      }
    }
    function Se(e, t) {
      {
        if (!e._store || e._store.validated || e.key != null)
          return;
        e._store.validated = !0;
        var a = pr(t);
        if (Ce[a])
          return;
        Ce[a] = !0;
        var n = "";
        e && e._owner && e._owner !== te.current && (n = " It was passed a child from " + T(e._owner.type) + "."), F(e), j('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.', a, n), F(null);
      }
    }
    function Te(e, t) {
      {
        if (typeof e != "object")
          return;
        if (ee(e))
          for (var a = 0; a < e.length; a++) {
            var n = e[a];
            ne(n) && Se(n, t);
          }
        else if (ne(e))
          e._store && (e._store.validated = !0);
        else if (e) {
          var h = Ye(e);
          if (typeof h == "function" && h !== e.entries)
            for (var v = h.call(e), c; !(c = v.next()).done; )
              ne(c.value) && Se(c.value, t);
        }
      }
    }
    function hr(e) {
      {
        var t = e.type;
        if (t == null || typeof t == "string")
          return;
        var a;
        if (typeof t == "function")
          a = t.propTypes;
        else if (typeof t == "object" && (t.$$typeof === g || // Note: Memo only checks outer props here.
        // Inner props are checked in the reconciler.
        t.$$typeof === d))
          a = t.propTypes;
        else
          return;
        if (a) {
          var n = T(t);
          er(a, e.props, "prop", n, e);
        } else if (t.PropTypes !== void 0 && !ae) {
          ae = !0;
          var h = T(t);
          j("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?", h || "Unknown");
        }
        typeof t.getDefaultProps == "function" && !t.getDefaultProps.isReactClassApproved && j("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.");
      }
    }
    function mr(e) {
      {
        for (var t = Object.keys(e.props), a = 0; a < t.length; a++) {
          var n = t[a];
          if (n !== "children" && n !== "key") {
            F(e), j("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.", n), F(null);
            break;
          }
        }
        e.ref !== null && (F(e), j("Invalid attribute `ref` supplied to `React.Fragment`."), F(null));
      }
    }
    var ke = {};
    function Pe(e, t, a, n, h, v) {
      {
        var c = Ge(e);
        if (!c) {
          var i = "";
          (e === void 0 || typeof e == "object" && e !== null && Object.keys(e).length === 0) && (i += " You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");
          var R = fr(h);
          R ? i += R : i += Re();
          var x;
          e === null ? x = "null" : ee(e) ? x = "array" : e !== void 0 && e.$$typeof === o ? (x = "<" + (T(e.type) || "Unknown") + " />", i = " Did you accidentally export a JSX literal instead of a component?") : x = typeof e, j("React.jsx: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s", x, i);
        }
        var y = dr(e, t, a, h, v);
        if (y == null)
          return y;
        if (c) {
          var S = t.children;
          if (S !== void 0)
            if (n)
              if (ee(S)) {
                for (var I = 0; I < S.length; I++)
                  Te(S[I], e);
                Object.freeze && Object.freeze(S);
              } else
                j("React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead.");
            else
              Te(S, e);
        }
        if (W.call(t, "key")) {
          var D = T(e), C = Object.keys(t).filter(function(wr) {
            return wr !== "key";
          }), se = C.length > 0 ? "{key: someKey, " + C.join(": ..., ") + ": ...}" : "{key: someKey}";
          if (!ke[D + se]) {
            var yr = C.length > 0 ? "{" + C.join(": ..., ") + ": ...}" : "{}";
            j(`A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`, se, D, yr, D), ke[D + se] = !0;
          }
        }
        return e === l ? mr(y) : hr(y), y;
      }
    }
    function vr(e, t, a) {
      return Pe(e, t, a, !0);
    }
    function gr(e, t, a) {
      return Pe(e, t, a, !1);
    }
    var br = gr, xr = vr;
    q.Fragment = l, q.jsx = br, q.jsxs = xr;
  }()), q;
}
process.env.NODE_ENV === "production" ? ie.exports = _r() : ie.exports = Nr();
var r = ie.exports, U = {}, B = jr;
if (process.env.NODE_ENV === "production")
  U.createRoot = B.createRoot, U.hydrateRoot = B.hydrateRoot;
else {
  var X = B.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
  U.createRoot = function(s, o) {
    X.usingClientEntryPoint = !0;
    try {
      return B.createRoot(s, o);
    } finally {
      X.usingClientEntryPoint = !1;
    }
  }, U.hydrateRoot = function(s, o, f) {
    X.usingClientEntryPoint = !0;
    try {
      return B.hydrateRoot(s, o, f);
    } finally {
      X.usingClientEntryPoint = !1;
    }
  };
}
const Er = ({
  tabs: s,
  activeTab: o,
  onTabChange: f,
  className: l = ""
}) => /* @__PURE__ */ r.jsx("div", { className: `wp-tabs ${l}`, children: /* @__PURE__ */ r.jsx("nav", { className: "nav-tab-wrapper wp-clearfix", role: "tablist", children: s.map((m) => /* @__PURE__ */ r.jsxs(
  "button",
  {
    role: "tab",
    "aria-selected": o === m.id,
    className: `nav-tab ${o === m.id ? "nav-tab-active" : ""}`,
    onClick: () => f(m.id),
    children: [
      m.icon && /* @__PURE__ */ r.jsx("span", { className: `dashicons ${m.icon} mr-1`, "aria-hidden": "true" }),
      m.title
    ]
  },
  m.id
)) }) }), Rr = ({
  id: s,
  activeTab: o,
  children: f,
  className: l = ""
}) => o !== s ? null : /* @__PURE__ */ r.jsx(
  "div",
  {
    role: "tabpanel",
    "aria-labelledby": `tab-${s}`,
    className: `tab-panel ${l}`,
    children: f
  }
), Ie = ({
  type: s,
  message: o,
  dismissible: f = !1,
  onDismiss: l
}) => {
  const m = () => {
    const p = "notice wp-admin-notice";
    switch (s) {
      case "success":
        return `${p} notice-success`;
      case "warning":
        return `${p} notice-warning`;
      case "error":
        return `${p} notice-error`;
      case "info":
      default:
        return `${p} notice-info`;
    }
  };
  return /* @__PURE__ */ r.jsxs("div", { className: `${m()} ${f ? "is-dismissible" : ""}`, children: [
    /* @__PURE__ */ r.jsx("p", { children: o }),
    f && l && /* @__PURE__ */ r.jsx(
      "button",
      {
        type: "button",
        className: "notice-dismiss",
        onClick: l,
        "aria-label": "Dismiss this notice",
        children: /* @__PURE__ */ r.jsx("span", { className: "screen-reader-text", children: "Dismiss this notice." })
      }
    )
  ] });
}, $ = ({
  label: s,
  description: o,
  required: f = !1,
  error: l,
  className: m = "",
  children: p
}) => /* @__PURE__ */ r.jsxs("div", { className: `wp-form-field ${m} ${l ? "has-error" : ""}`, children: [
  /* @__PURE__ */ r.jsx("div", { className: "wp-form-field-label", children: /* @__PURE__ */ r.jsxs("label", { className: "form-label", children: [
    s,
    f && /* @__PURE__ */ r.jsx("span", { className: "required text-red-500 ml-1", children: "*" })
  ] }) }),
  /* @__PURE__ */ r.jsx("div", { className: "wp-form-field-input", children: p }),
  o && /* @__PURE__ */ r.jsx("p", { className: "description text-sm text-wp-gray-600 mt-1", children: o }),
  l && /* @__PURE__ */ r.jsx("p", { className: "error-message text-sm text-red-600 mt-1", children: l })
] }), Cr = ({
  value: s,
  onChange: o,
  placeholder: f,
  disabled: l = !1,
  type: m = "text",
  className: p = ""
}) => /* @__PURE__ */ r.jsx(
  "input",
  {
    type: m,
    value: s,
    onChange: (_) => o(_.target.value),
    placeholder: f,
    disabled: l,
    className: `wp-text-input ${p}`
  }
), $e = ({
  value: s,
  onChange: o,
  options: f,
  disabled: l = !1,
  className: m = ""
}) => /* @__PURE__ */ r.jsx(
  "select",
  {
    value: s,
    onChange: (p) => o(p.target.value),
    disabled: l,
    className: `wp-select ${m}`,
    children: f.map((p) => /* @__PURE__ */ r.jsx("option", { value: p.value, children: p.label }, p.value))
  }
), V = ({
  checked: s,
  onChange: o,
  label: f,
  disabled: l = !1,
  className: m = ""
}) => /* @__PURE__ */ r.jsxs("label", { className: `wp-checkbox-label ${m}`, children: [
  /* @__PURE__ */ r.jsx(
    "input",
    {
      type: "checkbox",
      checked: s,
      onChange: (p) => o(p.target.checked),
      disabled: l,
      className: "wp-checkbox"
    }
  ),
  /* @__PURE__ */ r.jsx("span", { className: "checkbox-text", children: f })
] }), Le = ({
  onClick: s,
  children: o,
  variant: f = "primary",
  size: l = "medium",
  disabled: m = !1,
  loading: p = !1,
  className: _ = ""
}) => {
  const N = () => {
    let g = "wp-button";
    switch (f) {
      case "primary":
        g += " button-primary";
        break;
      case "secondary":
        g += " button-secondary";
        break;
      case "tertiary":
        g += " button-tertiary";
        break;
    }
    switch (l) {
      case "small":
        g += " button-small";
        break;
      case "large":
        g += " button-large";
        break;
    }
    return `${g} ${_}`;
  };
  return /* @__PURE__ */ r.jsxs(
    "button",
    {
      type: "button",
      onClick: s,
      disabled: m || p,
      className: N(),
      children: [
        p && /* @__PURE__ */ r.jsx("span", { className: "spinner is-active inline-block mr-1", "aria-hidden": "true" }),
        o
      ]
    }
  );
}, Ae = () => {
  const [s, o] = P({
    show_on_front_page: !0,
    show_on_posts: !0,
    show_on_pages: !1,
    show_on_archives: !1,
    default_style: "default",
    default_size: "medium"
  }), [f, l] = P(!1), [m, p] = P(null), _ = [
    { value: "default", label: "Default Style" },
    { value: "minimal", label: "Minimal Style" },
    { value: "rounded", label: "Rounded Style" },
    { value: "square", label: "Square Style" }
  ], N = [
    { value: "small", label: "Small" },
    { value: "medium", label: "Medium" },
    { value: "large", label: "Large" }
  ], g = async () => {
    l(!0);
    try {
      await new Promise((u) => setTimeout(u, 1e3)), p({ type: "success", message: "Settings saved successfully!" });
    } catch {
      p({ type: "error", message: "Failed to save settings. Please try again." });
    } finally {
      l(!1);
    }
  }, E = (u, d) => {
    o((b) => ({ ...b, [u]: d }));
  };
  return /* @__PURE__ */ r.jsx("div", { className: "general-tab", children: /* @__PURE__ */ r.jsxs("div", { className: "wp-admin-card p-6", children: [
    /* @__PURE__ */ r.jsx("h2", { className: "text-xl font-semibold mb-4", children: "General Settings" }),
    /* @__PURE__ */ r.jsx("p", { className: "text-wp-gray-600 mb-6", children: "Configure where the social share buttons should appear by default." }),
    m && /* @__PURE__ */ r.jsx(
      Ie,
      {
        type: m.type,
        message: m.message,
        dismissible: !0,
        onDismiss: () => p(null)
      }
    ),
    /* @__PURE__ */ r.jsxs("div", { className: "grid grid-cols-1 md:grid-cols-2 gap-6", children: [
      /* @__PURE__ */ r.jsxs("div", { className: "space-y-4", children: [
        /* @__PURE__ */ r.jsx("h3", { className: "text-lg font-medium text-wp-gray-800 mb-3", children: "Display Options" }),
        /* @__PURE__ */ r.jsx($, { label: "Show on Front Page", description: "Display social share buttons on your homepage", children: /* @__PURE__ */ r.jsx(
          V,
          {
            checked: s.show_on_front_page || !1,
            onChange: (u) => E("show_on_front_page", u),
            label: "Enable on front page"
          }
        ) }),
        /* @__PURE__ */ r.jsx($, { label: "Show on Posts", description: "Display social share buttons on blog posts", children: /* @__PURE__ */ r.jsx(
          V,
          {
            checked: s.show_on_posts || !1,
            onChange: (u) => E("show_on_posts", u),
            label: "Enable on posts"
          }
        ) }),
        /* @__PURE__ */ r.jsx($, { label: "Show on Pages", description: "Display social share buttons on static pages", children: /* @__PURE__ */ r.jsx(
          V,
          {
            checked: s.show_on_pages || !1,
            onChange: (u) => E("show_on_pages", u),
            label: "Enable on pages"
          }
        ) }),
        /* @__PURE__ */ r.jsx($, { label: "Show on Archives", description: "Display social share buttons on archive pages", children: /* @__PURE__ */ r.jsx(
          V,
          {
            checked: s.show_on_archives || !1,
            onChange: (u) => E("show_on_archives", u),
            label: "Enable on archives"
          }
        ) })
      ] }),
      /* @__PURE__ */ r.jsxs("div", { className: "space-y-4", children: [
        /* @__PURE__ */ r.jsx("h3", { className: "text-lg font-medium text-wp-gray-800 mb-3", children: "Default Appearance" }),
        /* @__PURE__ */ r.jsx(
          $,
          {
            label: "Default Style",
            description: "Choose the default button style for new instances",
            children: /* @__PURE__ */ r.jsx(
              $e,
              {
                value: s.default_style || "default",
                onChange: (u) => E("default_style", u),
                options: _
              }
            )
          }
        ),
        /* @__PURE__ */ r.jsx(
          $,
          {
            label: "Default Size",
            description: "Choose the default button size for new instances",
            children: /* @__PURE__ */ r.jsx(
              $e,
              {
                value: s.default_size || "medium",
                onChange: (u) => E("default_size", u),
                options: N
              }
            )
          }
        )
      ] })
    ] }),
    /* @__PURE__ */ r.jsx("div", { className: "mt-8 pt-4 border-t border-wp-gray-200", children: /* @__PURE__ */ r.jsxs("div", { className: "flex justify-between items-center", children: [
      /* @__PURE__ */ r.jsx("p", { className: "text-sm text-wp-gray-600", children: "These settings will be applied as defaults for new button instances." }),
      /* @__PURE__ */ r.jsx(
        Le,
        {
          onClick: g,
          loading: f,
          variant: "primary",
          children: "Save Changes"
        }
      )
    ] }) })
  ] }) });
}, Sr = [
  {
    id: "facebook",
    name: "Facebook",
    label: "Facebook",
    share_url: "https://www.facebook.com/sharer/sharer.php?u={url}",
    requires_handle: !1,
    icon_class: "fab fa-facebook-f",
    color: "#1877f2"
  },
  {
    id: "twitter",
    name: "Twitter",
    label: "Twitter",
    share_url: "https://twitter.com/intent/tweet?url={url}&text={title}",
    requires_handle: !1,
    icon_class: "fab fa-twitter",
    color: "#1da1f2"
  },
  {
    id: "linkedin",
    name: "LinkedIn",
    label: "LinkedIn",
    share_url: "https://www.linkedin.com/sharing/share-offsite/?url={url}",
    requires_handle: !1,
    icon_class: "fab fa-linkedin-in",
    color: "#0077b5"
  },
  {
    id: "pinterest",
    name: "Pinterest",
    label: "Pinterest",
    share_url: "https://pinterest.com/pin/create/button/?url={url}&description={title}",
    requires_handle: !1,
    icon_class: "fab fa-pinterest-p",
    color: "#bd081c"
  },
  {
    id: "reddit",
    name: "Reddit",
    label: "Reddit",
    share_url: "https://reddit.com/submit?url={url}&title={title}",
    requires_handle: !1,
    icon_class: "fab fa-reddit-alien",
    color: "#ff4500"
  },
  {
    id: "whatsapp",
    name: "WhatsApp",
    label: "WhatsApp",
    share_url: "https://wa.me/?text={title}%20{url}",
    requires_handle: !1,
    icon_class: "fab fa-whatsapp",
    color: "#25d366"
  }
], Tr = () => {
  const [s, o] = P(Sr), [f, l] = P(["facebook", "twitter", "linkedin"]), [m, p] = P(!1), [_, N] = P(null), g = (d, b) => {
    l(b ? (w) => [...w, d] : (w) => w.filter((k) => k !== d));
  }, E = (d, b) => {
    o((w) => w.map(
      (k) => k.id === d ? { ...k, label: b } : k
    ));
  }, u = async () => {
    p(!0);
    try {
      await new Promise((d) => setTimeout(d, 1e3)), N({ type: "success", message: "Network settings saved successfully!" });
    } catch {
      N({ type: "error", message: "Failed to save settings. Please try again." });
    } finally {
      p(!1);
    }
  };
  return /* @__PURE__ */ r.jsx("div", { className: "networks-tab", children: /* @__PURE__ */ r.jsxs("div", { className: "wp-admin-card p-6", children: [
    /* @__PURE__ */ r.jsx("h2", { className: "text-xl font-semibold mb-4", children: "Social Networks" }),
    /* @__PURE__ */ r.jsx("p", { className: "text-wp-gray-600 mb-6", children: "Choose which social networks to make available for sharing and customize their appearance." }),
    _ && /* @__PURE__ */ r.jsx(
      Ie,
      {
        type: _.type,
        message: _.message,
        dismissible: !0,
        onDismiss: () => N(null)
      }
    ),
    /* @__PURE__ */ r.jsxs("div", { className: "space-y-4", children: [
      /* @__PURE__ */ r.jsx("h3", { className: "text-lg font-medium text-wp-gray-800 mb-3", children: "Available Networks" }),
      /* @__PURE__ */ r.jsx("div", { className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4", children: s.map((d) => {
        const b = f.includes(d.id);
        return /* @__PURE__ */ r.jsxs(
          "div",
          {
            className: `wp-network-card border rounded-lg p-4 ${b ? "border-wp-blue-500 bg-wp-blue-50" : "border-wp-gray-200"}`,
            children: [
              /* @__PURE__ */ r.jsxs("div", { className: "flex items-center mb-3", children: [
                /* @__PURE__ */ r.jsx(
                  "div",
                  {
                    className: "w-8 h-8 rounded flex items-center justify-center mr-3",
                    style: { backgroundColor: d.color },
                    children: /* @__PURE__ */ r.jsx("i", { className: `${d.icon_class} text-white text-sm` })
                  }
                ),
                /* @__PURE__ */ r.jsx("div", { className: "flex-1", children: /* @__PURE__ */ r.jsx("h4", { className: "font-medium text-wp-gray-800", children: d.name }) }),
                /* @__PURE__ */ r.jsx(
                  V,
                  {
                    checked: b,
                    onChange: (w) => g(d.id, w),
                    label: ""
                  }
                )
              ] }),
              b && /* @__PURE__ */ r.jsx("div", { className: "mt-3", children: /* @__PURE__ */ r.jsx(
                $,
                {
                  label: "Button Label",
                  description: "Text displayed on the button",
                  children: /* @__PURE__ */ r.jsx(
                    Cr,
                    {
                      value: d.label,
                      onChange: (w) => E(d.id, w),
                      placeholder: d.name
                    }
                  )
                }
              ) })
            ]
          },
          d.id
        );
      }) }),
      /* @__PURE__ */ r.jsxs("div", { className: "mt-6 p-4 bg-wp-gray-50 rounded-lg", children: [
        /* @__PURE__ */ r.jsx("h4", { className: "font-medium text-wp-gray-800 mb-2", children: "Network Order" }),
        /* @__PURE__ */ r.jsx("p", { className: "text-sm text-wp-gray-600 mb-3", children: "Drag and drop to reorder the networks as they will appear on your site." }),
        /* @__PURE__ */ r.jsx("div", { className: "flex flex-wrap gap-2", children: f.map((d) => {
          const b = s.find((w) => w.id === d);
          return b ? /* @__PURE__ */ r.jsxs(
            "div",
            {
              className: "flex items-center px-3 py-1 bg-white border border-wp-gray-200 rounded cursor-move",
              children: [
                /* @__PURE__ */ r.jsx(
                  "div",
                  {
                    className: "w-4 h-4 rounded mr-2",
                    style: { backgroundColor: b.color }
                  }
                ),
                /* @__PURE__ */ r.jsx("span", { className: "text-sm", children: b.label })
              ]
            },
            d
          ) : null;
        }) })
      ] })
    ] }),
    /* @__PURE__ */ r.jsx("div", { className: "mt-8 pt-4 border-t border-wp-gray-200", children: /* @__PURE__ */ r.jsxs("div", { className: "flex justify-between items-center", children: [
      /* @__PURE__ */ r.jsxs("p", { className: "text-sm text-wp-gray-600", children: [
        f.length,
        " network",
        f.length !== 1 ? "s" : "",
        " enabled"
      ] }),
      /* @__PURE__ */ r.jsx(
        Le,
        {
          onClick: u,
          loading: m,
          variant: "primary",
          children: "Save Changes"
        }
      )
    ] }) })
  ] }) });
}, kr = () => /* @__PURE__ */ r.jsx("div", { className: "profiles-tab", children: /* @__PURE__ */ r.jsxs("div", { className: "wp-admin-card p-6", children: [
  /* @__PURE__ */ r.jsx("h2", { className: "text-xl font-semibold mb-4", children: "Button Profiles" }),
  /* @__PURE__ */ r.jsx("p", { className: "text-wp-gray-600 mb-6", children: "Create and manage different button configurations for different contexts." }),
  /* @__PURE__ */ r.jsx("div", { className: "bg-wp-gray-50 p-4 rounded-lg", children: /* @__PURE__ */ r.jsx("p", { className: "text-center text-wp-gray-600", children: "Profiles management component coming soon..." }) })
] }) }), Pr = () => /* @__PURE__ */ r.jsx("div", { className: "integrations-tab", children: /* @__PURE__ */ r.jsxs("div", { className: "wp-admin-card p-6", children: [
  /* @__PURE__ */ r.jsx("h2", { className: "text-xl font-semibold mb-4", children: "Plugin Integrations" }),
  /* @__PURE__ */ r.jsx("p", { className: "text-wp-gray-600 mb-6", children: "Configure integrations with other WordPress plugins and services." }),
  /* @__PURE__ */ r.jsx("div", { className: "bg-wp-gray-50 p-4 rounded-lg", children: /* @__PURE__ */ r.jsx("p", { className: "text-center text-wp-gray-600", children: "Integrations management component coming soon..." }) })
] }) }), Or = () => /* @__PURE__ */ r.jsx("div", { className: "appearance-tab", children: /* @__PURE__ */ r.jsxs("div", { className: "wp-admin-card p-6", children: [
  /* @__PURE__ */ r.jsx("h2", { className: "text-xl font-semibold mb-4", children: "Appearance Settings" }),
  /* @__PURE__ */ r.jsx("p", { className: "text-wp-gray-600 mb-6", children: "Customize the visual appearance of your social share buttons." }),
  /* @__PURE__ */ r.jsx("div", { className: "bg-wp-gray-50 p-4 rounded-lg", children: /* @__PURE__ */ r.jsx("p", { className: "text-center text-wp-gray-600", children: "Appearance customization component coming soon..." }) })
] }) }), Dr = () => /* @__PURE__ */ r.jsx("div", { className: "placement-tab", children: /* @__PURE__ */ r.jsxs("div", { className: "wp-admin-card p-6", children: [
  /* @__PURE__ */ r.jsx("h2", { className: "text-xl font-semibold mb-4", children: "Button Placement" }),
  /* @__PURE__ */ r.jsx("p", { className: "text-wp-gray-600 mb-6", children: "Configure where and how social share buttons appear on your site." }),
  /* @__PURE__ */ r.jsx("div", { className: "bg-wp-gray-50 p-4 rounded-lg", children: /* @__PURE__ */ r.jsx("p", { className: "text-center text-wp-gray-600", children: "Placement configuration component coming soon..." }) })
] }) }), $r = () => /* @__PURE__ */ r.jsx("div", { className: "shortcode-tab", children: /* @__PURE__ */ r.jsxs("div", { className: "wp-admin-card p-6", children: [
  /* @__PURE__ */ r.jsx("h2", { className: "text-xl font-semibold mb-4", children: "Shortcode Generator" }),
  /* @__PURE__ */ r.jsx("p", { className: "text-wp-gray-600 mb-6", children: "Generate shortcodes for embedding social share buttons anywhere on your site." }),
  /* @__PURE__ */ r.jsx("div", { className: "bg-wp-gray-50 p-4 rounded-lg", children: /* @__PURE__ */ r.jsx("p", { className: "text-center text-wp-gray-600", children: "Shortcode generator component coming soon..." }) })
] }) }), Fe = [
  {
    id: "general",
    title: "General",
    icon: "dashicons-admin-settings",
    description: "Basic plugin settings and display options"
  },
  {
    id: "networks",
    title: "Networks",
    icon: "dashicons-share",
    description: "Configure social media networks and sharing options"
  },
  {
    id: "profiles",
    title: "Profiles",
    icon: "dashicons-groups",
    description: "Manage button profiles and configurations"
  },
  {
    id: "integrations",
    title: "Integrations",
    icon: "dashicons-admin-plugins",
    description: "Third-party plugin integrations"
  },
  {
    id: "appearance",
    title: "Appearance",
    icon: "dashicons-art",
    description: "Style and appearance customization"
  },
  {
    id: "placement",
    title: "Placement",
    icon: "dashicons-location",
    description: "Configure where buttons appear"
  },
  {
    id: "shortcode",
    title: "Shortcode",
    icon: "dashicons-editor-code",
    description: "Generate and customize shortcodes"
  }
], Ar = () => {
  const [s, o] = P("general"), f = () => {
    switch (s) {
      case "general":
        return /* @__PURE__ */ r.jsx(Ae, {});
      case "networks":
        return /* @__PURE__ */ r.jsx(Tr, {});
      case "profiles":
        return /* @__PURE__ */ r.jsx(kr, {});
      case "integrations":
        return /* @__PURE__ */ r.jsx(Pr, {});
      case "appearance":
        return /* @__PURE__ */ r.jsx(Or, {});
      case "placement":
        return /* @__PURE__ */ r.jsx(Dr, {});
      case "shortcode":
        return /* @__PURE__ */ r.jsx($r, {});
      default:
        return /* @__PURE__ */ r.jsx(Ae, {});
    }
  };
  return /* @__PURE__ */ r.jsxs("div", { className: "wrap html-social-share-admin", children: [
    /* @__PURE__ */ r.jsx("h1", { className: "wp-heading-inline", children: "HTML Social Share Buttons" }),
    /* @__PURE__ */ r.jsx("hr", { className: "wp-header-end" }),
    /* @__PURE__ */ r.jsxs("div", { className: "html-social-share-tabs-wrapper", children: [
      /* @__PURE__ */ r.jsx(
        Er,
        {
          tabs: Fe,
          activeTab: s,
          onTabChange: o,
          className: "mb-6"
        }
      ),
      /* @__PURE__ */ r.jsx("div", { className: "tab-content", children: Fe.map((l) => /* @__PURE__ */ r.jsx(Rr, { id: l.id, activeTab: s, children: f() }, l.id)) })
    ] })
  ] });
};
document.addEventListener("DOMContentLoaded", () => {
  const s = document.getElementById("hss-admin-react-root");
  s && U.createRoot(s).render(
    /* @__PURE__ */ r.jsx(oe.StrictMode, { children: /* @__PURE__ */ r.jsx(Ar, {}) })
  );
});
export {
  Ar as App
};
