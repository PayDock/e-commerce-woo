(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('isomorphic-dompurify')) :
        typeof define === 'function' && define.amd ? define(['exports', 'isomorphic-dompurify'], factory) :
            (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.paydock = {}));
}(this, (function (exports) { 'use strict';

    function _iterableToArrayLimit(r, l) {
        var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
        if (null != t) {
            var e,
                n,
                i,
                u,
                a = [],
                f = !0,
                o = !1;
            try {
                if (i = (t = t.call(r)).next, 0 === l) {
                    if (Object(t) !== t) return;
                    f = !1;
                } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
            } catch (r) {
                o = !0, n = r;
            } finally {
                try {
                    if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return;
                } finally {
                    if (o) throw n;
                }
            }
            return a;
        }
    }
    function ownKeys(e, r) {
        var t = Object.keys(e);
        if (Object.getOwnPropertySymbols) {
            var o = Object.getOwnPropertySymbols(e);
            r && (o = o.filter(function (r) {
                return Object.getOwnPropertyDescriptor(e, r).enumerable;
            })), t.push.apply(t, o);
        }
        return t;
    }
    function _objectSpread2(e) {
        for (var r = 1; r < arguments.length; r++) {
            var t = null != arguments[r] ? arguments[r] : {};
            r % 2 ? ownKeys(Object(t), !0).forEach(function (r) {
                _defineProperty(e, r, t[r]);
            }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) {
                Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
            });
        }
        return e;
    }
    function _regeneratorRuntime() {
        _regeneratorRuntime = function () {
            return e;
        };
        var t,
            e = {},
            r = Object.prototype,
            n = r.hasOwnProperty,
            o = Object.defineProperty || function (t, e, r) {
                t[e] = r.value;
            },
            i = "function" == typeof Symbol ? Symbol : {},
            a = i.iterator || "@@iterator",
            c = i.asyncIterator || "@@asyncIterator",
            u = i.toStringTag || "@@toStringTag";
        function define(t, e, r) {
            return Object.defineProperty(t, e, {
                value: r,
                enumerable: !0,
                configurable: !0,
                writable: !0
            }), t[e];
        }
        try {
            define({}, "");
        } catch (t) {
            define = function (t, e, r) {
                return t[e] = r;
            };
        }
        function wrap(t, e, r, n) {
            var i = e && e.prototype instanceof Generator ? e : Generator,
                a = Object.create(i.prototype),
                c = new Context(n || []);
            return o(a, "_invoke", {
                value: makeInvokeMethod(t, r, c)
            }), a;
        }
        function tryCatch(t, e, r) {
            try {
                return {
                    type: "normal",
                    arg: t.call(e, r)
                };
            } catch (t) {
                return {
                    type: "throw",
                    arg: t
                };
            }
        }
        e.wrap = wrap;
        var h = "suspendedStart",
            l = "suspendedYield",
            f = "executing",
            s = "completed",
            y = {};
        function Generator() {}
        function GeneratorFunction() {}
        function GeneratorFunctionPrototype() {}
        var p = {};
        define(p, a, function () {
            return this;
        });
        var d = Object.getPrototypeOf,
            v = d && d(d(values([])));
        v && v !== r && n.call(v, a) && (p = v);
        var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p);
        function defineIteratorMethods(t) {
            ["next", "throw", "return"].forEach(function (e) {
                define(t, e, function (t) {
                    return this._invoke(e, t);
                });
            });
        }
        function AsyncIterator(t, e) {
            function invoke(r, o, i, a) {
                var c = tryCatch(t[r], t, o);
                if ("throw" !== c.type) {
                    var u = c.arg,
                        h = u.value;
                    return h && "object" == typeof h && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) {
                        invoke("next", t, i, a);
                    }, function (t) {
                        invoke("throw", t, i, a);
                    }) : e.resolve(h).then(function (t) {
                        u.value = t, i(u);
                    }, function (t) {
                        return invoke("throw", t, i, a);
                    });
                }
                a(c.arg);
            }
            var r;
            o(this, "_invoke", {
                value: function (t, n) {
                    function callInvokeWithMethodAndArg() {
                        return new e(function (e, r) {
                            invoke(t, n, e, r);
                        });
                    }
                    return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
                }
            });
        }
        function makeInvokeMethod(e, r, n) {
            var o = h;
            return function (i, a) {
                if (o === f) throw new Error("Generator is already running");
                if (o === s) {
                    if ("throw" === i) throw a;
                    return {
                        value: t,
                        done: !0
                    };
                }
                for (n.method = i, n.arg = a;;) {
                    var c = n.delegate;
                    if (c) {
                        var u = maybeInvokeDelegate(c, n);
                        if (u) {
                            if (u === y) continue;
                            return u;
                        }
                    }
                    if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) {
                        if (o === h) throw o = s, n.arg;
                        n.dispatchException(n.arg);
                    } else "return" === n.method && n.abrupt("return", n.arg);
                    o = f;
                    var p = tryCatch(e, r, n);
                    if ("normal" === p.type) {
                        if (o = n.done ? s : l, p.arg === y) continue;
                        return {
                            value: p.arg,
                            done: n.done
                        };
                    }
                    "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg);
                }
            };
        }
        function maybeInvokeDelegate(e, r) {
            var n = r.method,
                o = e.iterator[n];
            if (o === t) return r.delegate = null, "throw" === n && e.iterator.return && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y;
            var i = tryCatch(o, e.iterator, r.arg);
            if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y;
            var a = i.arg;
            return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y);
        }
        function pushTryEntry(t) {
            var e = {
                tryLoc: t[0]
            };
            1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e);
        }
        function resetTryEntry(t) {
            var e = t.completion || {};
            e.type = "normal", delete e.arg, t.completion = e;
        }
        function Context(t) {
            this.tryEntries = [{
                tryLoc: "root"
            }], t.forEach(pushTryEntry, this), this.reset(!0);
        }
        function values(e) {
            if (e || "" === e) {
                var r = e[a];
                if (r) return r.call(e);
                if ("function" == typeof e.next) return e;
                if (!isNaN(e.length)) {
                    var o = -1,
                        i = function next() {
                            for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next;
                            return next.value = t, next.done = !0, next;
                        };
                    return i.next = i;
                }
            }
            throw new TypeError(typeof e + " is not iterable");
        }
        return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", {
            value: GeneratorFunctionPrototype,
            configurable: !0
        }), o(GeneratorFunctionPrototype, "constructor", {
            value: GeneratorFunction,
            configurable: !0
        }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) {
            var e = "function" == typeof t && t.constructor;
            return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name));
        }, e.mark = function (t) {
            return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t;
        }, e.awrap = function (t) {
            return {
                __await: t
            };
        }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () {
            return this;
        }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) {
            void 0 === i && (i = Promise);
            var a = new AsyncIterator(wrap(t, r, n, o), i);
            return e.isGeneratorFunction(r) ? a : a.next().then(function (t) {
                return t.done ? t.value : a.next();
            });
        }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () {
            return this;
        }), define(g, "toString", function () {
            return "[object Generator]";
        }), e.keys = function (t) {
            var e = Object(t),
                r = [];
            for (var n in e) r.push(n);
            return r.reverse(), function next() {
                for (; r.length;) {
                    var t = r.pop();
                    if (t in e) return next.value = t, next.done = !1, next;
                }
                return next.done = !0, next;
            };
        }, e.values = values, Context.prototype = {
            constructor: Context,
            reset: function (e) {
                if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t);
            },
            stop: function () {
                this.done = !0;
                var t = this.tryEntries[0].completion;
                if ("throw" === t.type) throw t.arg;
                return this.rval;
            },
            dispatchException: function (e) {
                if (this.done) throw e;
                var r = this;
                function handle(n, o) {
                    return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o;
                }
                for (var o = this.tryEntries.length - 1; o >= 0; --o) {
                    var i = this.tryEntries[o],
                        a = i.completion;
                    if ("root" === i.tryLoc) return handle("end");
                    if (i.tryLoc <= this.prev) {
                        var c = n.call(i, "catchLoc"),
                            u = n.call(i, "finallyLoc");
                        if (c && u) {
                            if (this.prev < i.catchLoc) return handle(i.catchLoc, !0);
                            if (this.prev < i.finallyLoc) return handle(i.finallyLoc);
                        } else if (c) {
                            if (this.prev < i.catchLoc) return handle(i.catchLoc, !0);
                        } else {
                            if (!u) throw new Error("try statement without catch or finally");
                            if (this.prev < i.finallyLoc) return handle(i.finallyLoc);
                        }
                    }
                }
            },
            abrupt: function (t, e) {
                for (var r = this.tryEntries.length - 1; r >= 0; --r) {
                    var o = this.tryEntries[r];
                    if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) {
                        var i = o;
                        break;
                    }
                }
                i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null);
                var a = i ? i.completion : {};
                return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a);
            },
            complete: function (t, e) {
                if ("throw" === t.type) throw t.arg;
                return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y;
            },
            finish: function (t) {
                for (var e = this.tryEntries.length - 1; e >= 0; --e) {
                    var r = this.tryEntries[e];
                    if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y;
                }
            },
            catch: function (t) {
                for (var e = this.tryEntries.length - 1; e >= 0; --e) {
                    var r = this.tryEntries[e];
                    if (r.tryLoc === t) {
                        var n = r.completion;
                        if ("throw" === n.type) {
                            var o = n.arg;
                            resetTryEntry(r);
                        }
                        return o;
                    }
                }
                throw new Error("illegal catch attempt");
            },
            delegateYield: function (e, r, n) {
                return this.delegate = {
                    iterator: values(e),
                    resultName: r,
                    nextLoc: n
                }, "next" === this.method && (this.arg = t), y;
            }
        }, e;
    }
    function _typeof(o) {
        "@babel/helpers - typeof";

        return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
            return typeof o;
        } : function (o) {
            return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
        }, _typeof(o);
    }
    function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    }
    function _defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor);
        }
    }
    function _createClass(Constructor, protoProps, staticProps) {
        if (protoProps) _defineProperties(Constructor.prototype, protoProps);
        if (staticProps) _defineProperties(Constructor, staticProps);
        Object.defineProperty(Constructor, "prototype", {
            writable: false
        });
        return Constructor;
    }
    function _defineProperty(obj, key, value) {
        key = _toPropertyKey(key);
        if (key in obj) {
            Object.defineProperty(obj, key, {
                value: value,
                enumerable: true,
                configurable: true,
                writable: true
            });
        } else {
            obj[key] = value;
        }
        return obj;
    }
    function _extends() {
        _extends = Object.assign ? Object.assign.bind() : function (target) {
            for (var i = 1; i < arguments.length; i++) {
                var source = arguments[i];
                for (var key in source) {
                    if (Object.prototype.hasOwnProperty.call(source, key)) {
                        target[key] = source[key];
                    }
                }
            }
            return target;
        };
        return _extends.apply(this, arguments);
    }
    function _inherits(subClass, superClass) {
        if (typeof superClass !== "function" && superClass !== null) {
            throw new TypeError("Super expression must either be null or a function");
        }
        subClass.prototype = Object.create(superClass && superClass.prototype, {
            constructor: {
                value: subClass,
                writable: true,
                configurable: true
            }
        });
        Object.defineProperty(subClass, "prototype", {
            writable: false
        });
        if (superClass) _setPrototypeOf(subClass, superClass);
    }
    function _getPrototypeOf(o) {
        _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
            return o.__proto__ || Object.getPrototypeOf(o);
        };
        return _getPrototypeOf(o);
    }
    function _setPrototypeOf(o, p) {
        _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
            o.__proto__ = p;
            return o;
        };
        return _setPrototypeOf(o, p);
    }
    function _isNativeReflectConstruct() {
        if (typeof Reflect === "undefined" || !Reflect.construct) return false;
        if (Reflect.construct.sham) return false;
        if (typeof Proxy === "function") return true;
        try {
            Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
            return true;
        } catch (e) {
            return false;
        }
    }
    function _assertThisInitialized(self) {
        if (self === void 0) {
            throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        }
        return self;
    }
    function _possibleConstructorReturn(self, call) {
        if (call && (typeof call === "object" || typeof call === "function")) {
            return call;
        } else if (call !== void 0) {
            throw new TypeError("Derived constructors may only return object or undefined");
        }
        return _assertThisInitialized(self);
    }
    function _createSuper(Derived) {
        var hasNativeReflectConstruct = _isNativeReflectConstruct();
        return function _createSuperInternal() {
            var Super = _getPrototypeOf(Derived),
                result;
            if (hasNativeReflectConstruct) {
                var NewTarget = _getPrototypeOf(this).constructor;
                result = Reflect.construct(Super, arguments, NewTarget);
            } else {
                result = Super.apply(this, arguments);
            }
            return _possibleConstructorReturn(this, result);
        };
    }
    function _superPropBase(object, property) {
        while (!Object.prototype.hasOwnProperty.call(object, property)) {
            object = _getPrototypeOf(object);
            if (object === null) break;
        }
        return object;
    }
    function _get() {
        if (typeof Reflect !== "undefined" && Reflect.get) {
            _get = Reflect.get.bind();
        } else {
            _get = function _get(target, property, receiver) {
                var base = _superPropBase(target, property);
                if (!base) return;
                var desc = Object.getOwnPropertyDescriptor(base, property);
                if (desc.get) {
                    return desc.get.call(arguments.length < 3 ? target : receiver);
                }
                return desc.value;
            };
        }
        return _get.apply(this, arguments);
    }
    function _slicedToArray(arr, i) {
        return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
    }
    function _toConsumableArray(arr) {
        return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
    }
    function _arrayWithoutHoles(arr) {
        if (Array.isArray(arr)) return _arrayLikeToArray(arr);
    }
    function _arrayWithHoles(arr) {
        if (Array.isArray(arr)) return arr;
    }
    function _iterableToArray(iter) {
        if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
    }
    function _unsupportedIterableToArray(o, minLen) {
        if (!o) return;
        if (typeof o === "string") return _arrayLikeToArray(o, minLen);
        var n = Object.prototype.toString.call(o).slice(8, -1);
        if (n === "Object" && o.constructor) n = o.constructor.name;
        if (n === "Map" || n === "Set") return Array.from(o);
        if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
    }
    function _arrayLikeToArray(arr, len) {
        if (len == null || len > arr.length) len = arr.length;
        for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
        return arr2;
    }
    function _nonIterableSpread() {
        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }
    function _nonIterableRest() {
        throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }
    function _createForOfIteratorHelper(o, allowArrayLike) {
        var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
        if (!it) {
            if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
                if (it) o = it;
                var i = 0;
                var F = function () {};
                return {
                    s: F,
                    n: function () {
                        if (i >= o.length) return {
                            done: true
                        };
                        return {
                            done: false,
                            value: o[i++]
                        };
                    },
                    e: function (e) {
                        throw e;
                    },
                    f: F
                };
            }
            throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        }
        var normalCompletion = true,
            didErr = false,
            err;
        return {
            s: function () {
                it = it.call(o);
            },
            n: function () {
                var step = it.next();
                normalCompletion = step.done;
                return step;
            },
            e: function (e) {
                didErr = true;
                err = e;
            },
            f: function () {
                try {
                    if (!normalCompletion && it.return != null) it.return();
                } finally {
                    if (didErr) throw err;
                }
            }
        };
    }
    function _toPrimitive(input, hint) {
        if (typeof input !== "object" || input === null) return input;
        var prim = input[Symbol.toPrimitive];
        if (prim !== undefined) {
            var res = prim.call(input, hint || "default");
            if (typeof res !== "object") return res;
            throw new TypeError("@@toPrimitive must return a primitive value.");
        }
        return (hint === "string" ? String : Number)(input);
    }
    function _toPropertyKey(arg) {
        var key = _toPrimitive(arg, "string");
        return typeof key === "symbol" ? key : String(key);
    }

    var ACCESS_HEADER_NAME = {
        accessToken: 'x-access-token',
        publicKey: 'x-user-public-key'
    };
    var AccessToken = /*#__PURE__*/function () {
        function AccessToken() {
            _classCallCheck(this, AccessToken);
        }
        _createClass(AccessToken, null, [{
            key: "validateJWT",
            value: function validateJWT(jwt) {
                if (!jwt) return null;
                var _jwt$split = jwt.split("."),
                    _jwt$split2 = _slicedToArray(_jwt$split, 3),
                    rawHead = _jwt$split2[0],
                    rawBody = _jwt$split2[1],
                    signature = _jwt$split2[2];
                if (!rawHead || !rawBody || !signature) return null;
                if (2 + rawHead.length + rawBody.length + signature.length !== jwt.length) return null;
                try {
                    var head = JSON.parse(atob(rawHead));
                    var body = JSON.parse(atob(rawBody));
                    return {
                        head: head,
                        body: body,
                        signature: signature
                    };
                } catch (_a) {
                    return null;
                }
            }
        }, {
            key: "extractData",
            value: function extractData(body) {
                try {
                    return JSON.parse(atob(body.meta));
                } catch (_a) {
                    return null;
                }
            }
        }, {
            key: "extractMeta",
            value: function extractMeta(body) {
                try {
                    var _JSON$parse = JSON.parse(atob(body.meta)),
                        meta = _JSON$parse.meta;
                    return meta;
                } catch (_a) {
                    return null;
                }
            }
        }, {
            key: "getAccessHeaderNameByToken",
            value: function getAccessHeaderNameByToken(token) {
                return !!AccessToken.validateJWT(token) ? ACCESS_HEADER_NAME.accessToken : ACCESS_HEADER_NAME.publicKey;
            }
        }]);
        return AccessToken;
    }();

    var SDK = /*#__PURE__*/function () {
        function SDK() {
            _classCallCheck(this, SDK);
        }
        _createClass(SDK, null, [{
            key: "version",
            get: function get() {
                return SDK._version.trim() || null;
            }
        }]);
        return SDK;
    }();
    SDK.type = 'client';
    SDK.headerKeys = Object.freeze({
        version: 'x-sdk-version',
        type: 'x-sdk-type'
    });
    // This variable is injected through the grunt build command,
    // the empty string is set in case if version not provided.
    //
    // e.g: grunt build --sdk-version=v1.0.0
    SDK._version = 'v1.101.1';

    var ENV = {
        SANDBOX: 'sandbox',
        SANDBOX_KOVENA: 'sandbox-kovena',
        SANDBOX_DEMO: 'sandbox-demo',
        SANDBOX_DEMO_KOVENA: 'sandbox-demo-kovena',
        PROD: 'production',
        STAGING: 'staging',
        STAGING_1: 'staging_1',
        STAGING_2: 'staging_2',
        STAGING_3: 'staging_3',
        STAGING_4: 'staging_4',
        STAGING_5: 'staging_5',
        STAGING_6: 'staging_6',
        STAGING_7: 'staging_7',
        STAGING_8: 'staging_8',
        STAGING_9: 'staging_9',
        STAGING_10: 'staging_10',
        STAGING_11: 'staging_11',
        STAGING_12: 'staging_12',
        STAGING_13: 'staging_13',
        STAGING_14: 'staging_14',
        STAGING_15: 'staging_15',
        STAGING_CBA: 'staging_cba',
        PREPROD_CBA: 'preproduction_cba',
        PROD_CBA: 'production_cba'
    };
    var WIDGET_URL = [{
        env: ENV.SANDBOX,
        url: 'https://widget-sandbox.'
    }, {
        env: ENV.SANDBOX_KOVENA,
        url: 'https://widget-sandbox.'
    }, {
        env: ENV.SANDBOX_DEMO,
        url: 'https://widget-sandbox-demo.'
    }, {
        env: ENV.SANDBOX_DEMO_KOVENA,
        url: 'https://widget-sandbox-demo.'
    }, {
        env: ENV.PROD,
        url: 'https://widget.'
    }, {
        env: ENV.STAGING,
        url: 'https://widsta.'
    }, {
        env: ENV.STAGING_1,
        url: 'https://widsta-1.'
    }, {
        env: ENV.STAGING_2,
        url: 'https://widsta-2.'
    }, {
        env: ENV.STAGING_3,
        url: 'https://widsta-3.'
    }, {
        env: ENV.STAGING_4,
        url: 'https://widsta-4.'
    }, {
        env: ENV.STAGING_5,
        url: 'https://widsta-5.'
    }, {
        env: ENV.STAGING_6,
        url: 'https://widsta-6.'
    }, {
        env: ENV.STAGING_7,
        url: 'https://widsta-7.'
    }, {
        env: ENV.STAGING_8,
        url: 'https://widsta-8.'
    }, {
        env: ENV.STAGING_9,
        url: 'https://widsta-9.'
    }, {
        env: ENV.STAGING_10,
        url: 'https://widsta-10.'
    }, {
        env: ENV.STAGING_11,
        url: 'https://widsta-11.'
    }, {
        env: ENV.STAGING_12,
        url: 'https://widsta-12.'
    }, {
        env: ENV.STAGING_13,
        url: 'https://widsta-13.'
    }, {
        env: ENV.STAGING_14,
        url: 'https://widsta-14.'
    }, {
        env: ENV.STAGING_15,
        url: 'https://widsta-15.'
    }, {
        env: ENV.STAGING_CBA,
        url: 'https://widget.staging.powerboard.'
    }, {
        env: ENV.PREPROD_CBA,
        url: 'https://widget.preproduction.powerboard.'
    }, {
        env: ENV.PROD_CBA,
        url: 'https://widget.powerboard.'
    }];
    var API_URL = [{
        env: ENV.SANDBOX,
        url: 'https://api-sandbox.'
    }, {
        env: ENV.PROD,
        url: 'https://api.'
    }, {
        env: ENV.STAGING,
        url: 'https://apista.'
    }, {
        env: ENV.STAGING_1,
        url: 'https://apista-1.'
    }, {
        env: ENV.STAGING_2,
        url: 'https://apista-2.'
    }, {
        env: ENV.STAGING_3,
        url: 'https://apista-3.'
    }, {
        env: ENV.STAGING_4,
        url: 'https://apista-4.'
    }, {
        env: ENV.STAGING_5,
        url: 'https://apista-5.'
    }, {
        env: ENV.STAGING_6,
        url: 'https://apista-6.'
    }, {
        env: ENV.STAGING_7,
        url: 'https://apista-7.'
    }, {
        env: ENV.STAGING_8,
        url: 'https://apista-8.'
    }, {
        env: ENV.STAGING_9,
        url: 'https://apista-9.'
    }, {
        env: ENV.STAGING_10,
        url: 'https://apista-10.'
    }, {
        env: ENV.STAGING_11,
        url: 'https://apista-11.'
    }, {
        env: ENV.STAGING_12,
        url: 'https://apista-12.'
    }, {
        env: ENV.STAGING_13,
        url: 'https://apista-13.'
    }, {
        env: ENV.STAGING_14,
        url: 'https://apista-14.'
    }, {
        env: ENV.STAGING_15,
        url: 'https://apista-15.'
    }, {
        env: ENV.STAGING_CBA,
        url: 'https://api.staging.powerboard.'
    }, {
        env: ENV.PREPROD_CBA,
        url: 'https://api.preproduction.powerboard.'
    }, {
        env: ENV.PROD_CBA,
        url: 'https://api.powerboard.'
    }];
    var ADDITIONAL_ENV = [ENV.SANDBOX_KOVENA, ENV.SANDBOX_DEMO_KOVENA];
    var REGEX_ALIAS = '^([a-zA-Z0-9](?:(?:[a-zA-Z0-9-.]*(?!-)\\.(?![-.]))*[a-zA-Z0-9]+)?)$';
    var DEFAULT_ENV = ENV.SANDBOX;
    var DEFAULT_ALIAS = {
        PAYDOCK: 'paydock.com',
        KOVENA: 'kovena.com'
    };
    var Env = /*#__PURE__*/function () {
        function Env(configs) {
            var defaultEnv = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : DEFAULT_ENV;
            _classCallCheck(this, Env);
            this.configs = configs;
            this.setEnv(defaultEnv);
        }
        _createClass(Env, [{
            key: "setEnv",
            value: function setEnv(env, alias) {
                if (!this.isValidMode(this.configs, env)) throw new Error('unknown env: ' + env);
                this.env = env;
                if (alias && !alias.match(REGEX_ALIAS)) throw new Error('invalid: ' + alias);
                if (alias) this.alias = alias;else if (ADDITIONAL_ENV.indexOf(this.env) !== -1) this.alias = DEFAULT_ALIAS.KOVENA;else this.alias = DEFAULT_ALIAS.PAYDOCK;
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                return this.env;
            }
        }, {
            key: "getConf",
            value: function getConf() {
                for (var index in this.configs) {
                    if (!this.configs.hasOwnProperty(index)) continue;
                    if (this.configs[index].env === this.getEnv()) return {
                        url: this.configs[index].url.indexOf('localhost') !== -1 ? this.configs[index].url : this.configs[index].url + this.alias,
                        env: this.configs[index].env
                    };
                }
                throw new Error('invalid env');
            }
        }, {
            key: "isValidMode",
            value: function isValidMode(configs, env) {
                for (var index in configs) {
                    if (!configs.hasOwnProperty(index)) continue;
                    if (configs[index].env === env) return true;
                }
                return false;
            }
        }]);
        return Env;
    }();

    var Url = /*#__PURE__*/function () {
        function Url() {
            _classCallCheck(this, Url);
        }
        _createClass(Url, null, [{
            key: "extendSearchParams",
            value: function extendSearchParams(uri, key, val) {
                return uri.replace(new RegExp("([?&]" + key + "(?=[=&#]|$)[^#&]*|(?=#|$))"), "&" + key + "=" + encodeURIComponent(val)).replace(/^([^?&]+)&/, "$1?");
            }
        }, {
            key: "serialize",
            value: function serialize(params) {
                return Object.keys(params).map(function (key) {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
                }).join('&');
            }
        }]);
        return Url;
    }();

    var Uuid = /*#__PURE__*/function () {
        function Uuid() {
            _classCallCheck(this, Uuid);
        }
        _createClass(Uuid, null, [{
            key: "generate",
            value: function generate() {
                if (typeof window !== "undefined" && typeof window.crypto !== "undefined" && typeof window.crypto.getRandomValues !== "undefined") {
                    var buf = new Uint16Array(8);
                    window.crypto.getRandomValues(buf);
                    return this.hash(buf[0]) + this.hash(buf[1]) + "-" + this.hash(buf[2]) + "-" + this.hash(buf[3]) + "-" + this.hash(buf[4]) + "-" + this.hash(buf[5]) + this.hash(buf[6]) + this.hash(buf[7]);
                } else {
                    return this.random() + this.random() + "-" + this.random() + "-" + this.random() + "-" + this.random() + "-" + this.random() + this.random() + this.random();
                }
            }
        }, {
            key: "hash",
            value: function hash(number) {
                var hash = number.toString(16);
                while (hash.length < 4) {
                    hash = "0" + hash;
                }
                return hash;
            }
        }, {
            key: "random",
            value: function random() {
                return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
            }
        }]);
        return Uuid;
    }();

    var WIDGET_LINK = '/remote-action';
    var VAULT_DISPLAY_WIDGET_LINK = '/vault-display';
    var PAYMENT_SOURCE_LINK = '/payment-sources';
    var SECURE_3D = '/3ds/webhook';
    var FLYPAY_LINK = '/wallet/flypay';
    var FLYPAY_LOGO_LINK = '/images/logo.png';
    var VISA_SRC = '/secure-remote-commerce/visa';
    var Link = /*#__PURE__*/function () {
        function Link(linkResource) {
            _classCallCheck(this, Link);
            this.params = {};
            this.widgetId = Uuid.generate();
            this.linkResource = linkResource;
            this.env = new Env(WIDGET_URL);
            this.setParams({
                widget_id: this.widgetId
            });
        }
        _createClass(Link, [{
            key: "getNetUrl",
            value: function getNetUrl() {
                return this.env.getConf().url + this.linkResource;
            }
        }, {
            key: "getUrl",
            value: function getUrl() {
                var url = this.getNetUrl();
                var params = this.getParams();
                for (var key in params) {
                    if (params.hasOwnProperty(key)) url = Url.extendSearchParams(url, key, params[key]);
                }
                return url;
            }
        }, {
            key: "setParams",
            value: function setParams(params) {
                this.params = _extends({}, this.params, params);
            }
        }, {
            key: "concatParams",
            value: function concatParams(params) {
                for (var key in params) {
                    if (!params.hasOwnProperty(key) || !params[key].length) continue;
                    if (typeof this.params[key] !== 'string') this.params[key] = '';
                    if (this.params[key].length) this.params[key] += ',' + params[key];else this.params[key] += params[key];
                }
            }
        }, {
            key: "getParams",
            value: function getParams() {
                return this.params;
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.env.setEnv(env, alias);
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                return this.env.getEnv();
            }
        }, {
            key: "getBaseUrl",
            value: function getBaseUrl() {
                return this.env.getConf().url;
            }
        }]);
        return Link;
    }();

    var ObjectHelper = /*#__PURE__*/function () {
        function ObjectHelper() {
            _classCallCheck(this, ObjectHelper);
        }
        _createClass(ObjectHelper, null, [{
            key: "values",
            value: function values(object) {
                return Object.keys(object).map(function (key) {
                    return object[key];
                });
            }
        }]);
        return ObjectHelper;
    }();

    var PAYMENT_SOURCE_TYPE = {
        CARD: 'card',
        BANK_ACCOUNT: 'bank_account',
        CHECKOUT: 'checkout'
    };
    var FORM_FIELD = {
        CARD_NAME: 'card_name',
        CARD_NUMBER: 'card_number',
        EXPIRE_MONTH: 'expire_month',
        EXPIRE_YEAR: 'expire_year',
        CARD_CCV: 'card_ccv',
        CARD_PIN: 'card_pin',
        ACCOUNT_NAME: 'account_name',
        ACCOUNT_BSB: 'account_bsb',
        ACCOUNT_NUMBER: 'account_number',
        ACCOUNT_ROUTING: 'account_routing',
        ACCOUNT_HOLDER_TYPE: 'account_holder_type',
        ACCOUNT_BANK_NAME: 'account_bank_name',
        ACCOUNT_TYPE: 'account_type',
        FIRST_NAME: 'first_name',
        LAST_NAME: 'last_name',
        EMAIL: 'email',
        PHONE: 'phone',
        PHONE2: 'phone2',
        ADDRESS_LINE1: 'address_line1',
        ADDRESS_LINE2: 'address_line2',
        ADDRESS_STATE: 'address_state',
        ADDRESS_COUNTRY: 'address_country',
        ADDRESS_CITY: 'address_city',
        ADDRESS_POSTCODE: 'address_postcode',
        ADDRESS_COMPANY: 'address_company'
    };
    var STYLE = {
        BACKGROUND_COLOR: 'background_color',
        BACKGROUND_ACTIVE_COLOR: 'background_active_color',
        TEXT_COLOR: 'text_color',
        BORDER_COLOR: 'border_color',
        ICON_SIZE: 'icon_size',
        BUTTON_COLOR: 'button_color',
        ERROR_COLOR: 'error_color',
        SUCCESS_COLOR: 'success_color',
        FONT_SIZE: 'font_size',
        FONT_FAMILY: 'font_family'
    };
    var VAULT_DISPLAY_STYLE = {
        BACKGROUND_COLOR: 'background_color',
        TEXT_COLOR: 'text_color',
        BORDER_COLOR: 'border_color',
        BUTTON_COLOR: 'button_color',
        FONT_SIZE: 'font_size',
        FONT_FAMILY: 'font_family'
    };
    var TEXT = {
        TITLE: 'title',
        TITLE_H1: 'title_h1',
        TITLE_H2: 'title_h2',
        TITLE_H3: 'title_h3',
        TITLE_H4: 'title_h4',
        TITLE_H5: 'title_h5',
        TITLE_H6: 'title_h6',
        FINISH: 'finish_text',
        TITLE_DESCRIPTION: 'title_description',
        SUBMIT_BUTTON: 'submit_button',
        SUBMIT_BUTTON_PROCESSING: 'submit_button_processing'
    };
    var ELEMENT = {
        SUBMIT_BUTTON: 'submit_button',
        TABS: 'tabs'
    };
    var SUPPORTED_CARD_TYPES = {
        AMEX: 'amex',
        AUSBC: 'ausbc',
        DINERS: 'diners',
        DISCOVER: 'discover',
        JAPCB: 'japcb',
        LASER: 'laser',
        MASTERCARD: 'mastercard',
        SOLO: 'solo',
        VISA: 'visa',
        VISA_WHITE: 'visa_white'
    };
    var SUPPORTED_CHECKOUT_META_COLLECTION = [].concat(['brand_name', 'cart_border_color', 'reference', 'email', 'hdr_img', 'logo_img', 'pay_flow_color', 'first_name', 'last_name', 'address_line', 'address_line2', 'address_city', 'address_state', 'address_postcode', 'address_country', 'phone', 'hide_shipping_address'], ['first_name', 'last_name', 'phone', 'tokenize', 'email', 'gender', 'date_of_birth', 'charge', 'statistics', 'hide_shipping_address'], ['amount', 'currency', 'email', 'first_name', 'last_name', 'address_line', 'address_line2', 'address_city', 'address_state', 'address_postcode', 'address_country', 'phone'], ['customer_storage_number', 'tokenise_algorithm']);
    var WALLET_GATEWAY;
    (function (WALLET_GATEWAY) {
        WALLET_GATEWAY["STRIPE"] = "Stripe";
        WALLET_GATEWAY["FLYPAY"] = "Flypay";
        WALLET_GATEWAY["FLYPAY_V2"] = "FlypayV2";
        WALLET_GATEWAY["PAYPAL"] = "Paypal";
        WALLET_GATEWAY["MASTERCARD"] = "MasterCard";
        WALLET_GATEWAY["AFTERPAY"] = "Afterpay";
    })(WALLET_GATEWAY || (WALLET_GATEWAY = {}));
    var WALLET_TYPE;
    (function (WALLET_TYPE) {
        WALLET_TYPE["GOOGLE"] = "google";
        WALLET_TYPE["APPLE"] = "apple";
        WALLET_TYPE["FLYPAY"] = "flypay";
        WALLET_TYPE["FLYPAY_V2"] = "flypayV2";
        WALLET_TYPE["PAYPAL"] = "paypal";
        WALLET_TYPE["AFTERPAY"] = "afterpay";
    })(WALLET_TYPE || (WALLET_TYPE = {}));

    /**
     * List of available payment source types
     *
     * @type {object}
     * @param {string} CARD=card
     * @param {string} BANK_ACCOUNT=bank_account
     * @param {string} CHECKOUT=checkout
     */
    var PAYMENT_TYPE = {
        CARD: 'card',
        GIFT_CARD: 'gift_card',
        BANK_ACCOUNT: 'bank_account',
        CHECKOUT: 'checkout'
    };
    /**
     * Purposes
     * @type {object}
     * @param {string} PAYMENT_SOURCE=payment_source
     * @param {string} CARD_PAYMENT_SOURCE_WITH_CVV=card_payment_source_with_cvv
     * @param {string} CARD_PAYMENT_SOURCE_WITHOUT_CVV=card_payment_source_without_cvv
     * */
    var PURPOSE = {
        PAYMENT_SOURCE: 'payment_source',
        CARD_PAYMENT_SOURCE_WITH_CVV: 'card_payment_source_with_cvv',
        CARD_PAYMENT_SOURCE_WITHOUT_CVV: 'card_payment_source_without_cvv'
    };
    var CONFIGURATION_LINK = '/v1/remote-action/configs';
    /**
     * Class Configuration include methods for creating configuration token
     * @constructor
     *
     * @example
     * var config = new Configuration('gatewayId'); // short
     *
     * var config = new Configuration('gatewayId', 'bank_account', 'paymentSource'); // extend
     *
     * var config = new Configuration('not_configured'); // without gateway
     *
     * @param {string} [gatewayID=default] - gateway ID. By default or if put 'default', it will use the selected default gateway. If put 'not_configured', it wonвЂ™t use gateway to create downstream token.
     * @param {string} paymentType - Type of payment source which shows in widget form. Available parameters [PAYMENT_TYPE]{@link PAYMENT_TYPE}
     * @param {string} purpose - Param which describes payment purpose. By default uses Available parameters [PURPOSE]{@link PURPOSE}
     **/
    var Configuration = /*#__PURE__*/function () {
        function Configuration() {
            var gatewayID = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'default';
            var paymentType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : PAYMENT_TYPE.CARD;
            var purpose = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : PURPOSE.PAYMENT_SOURCE;
            _classCallCheck(this, Configuration);
            if (ObjectHelper.values(PAYMENT_TYPE).indexOf(paymentType) === -1) throw new Error('unsupported payment type');else if (paymentType === PAYMENT_TYPE.CHECKOUT) throw new Error('Payment type "checkout" is deprecated. Use CheckoutButton for this type of payments https://www.npmjs.com/package/@paydock/client-sdk#checkout-button');
            if (ObjectHelper.values(PURPOSE).indexOf(purpose) === -1) throw new Error('unsupported purpose');
            this.env = new Env(API_URL);
            this.configs = {
                purpose: purpose,
                meta: {},
                dynamic_fields_position: true,
                predefined_fields: {
                    gateway_id: gatewayID,
                    type: paymentType,
                    gift_card_scheme: null,
                    processing_network: null
                }
            };
        }
        _createClass(Configuration, [{
            key: "setWebHookDestination",
            value:
                /**
                 * Destination, where customer will receive all successful responses.
                 * Response will contain вЂњdataвЂќ object with вЂњpayment_sourceвЂќ or other parameters, in depending on 'purpose'
                 *
                 * @example
                 * config.setWebHookDestination('http://google.com');
                 *
                 * @param {string} url - Your endpoint for post request.
                 */
                function setWebHookDestination(url) {
                    this.configs.webhook_destination = url;
                }
            /**
             * URL to which the Customer will be redirected to after the success finish
             *
             * @example
             * config.setSuccessRedirectUrl('google.com/search?q=success');
             *
             * @param {string}  url
             */
        }, {
            key: "setSuccessRedirectUrl",
            value: function setSuccessRedirectUrl(url) {
                this.configs.success_redirect_url = url;
            }
            /**
             * URL to which the Customer will be redirected to if an error is triggered in the process of operation
             *
             * @example
             * config.setErrorRedirectUrl('google.com/search?q=error');
             *
             * @param {string} url
             */
        }, {
            key: "setErrorRedirectUrl",
            value: function setErrorRedirectUrl(url) {
                this.configs.error_redirect_url = url;
            }
            /**
             *  Set list with widget form field, which will be shown in form. Also you can set the required validation for these fields
             *
             * @example
             * config.setFormFields(['phone', 'email', 'first_name*']);
             *
             * @param {string[]} fields - name of fields which can be shown in a widget.
             *   If after a name of a field, you put вЂњ*вЂќ, this field will be required on client-side.
             *   (For validation, you can specify any fields, even those that are shown by default: card_number, expiration, etc... ) [FORM_FIELD]{@link FORM_FIELD}
             */
        }, {
            key: "setFormFields",
            value: function setFormFields(fields) {
                if (!Array.isArray(this.configs.defined_form_fields)) this.configs.defined_form_fields = [];
                for (var index in fields) {
                    if (!fields.hasOwnProperty(index)) continue;
                    if (ObjectHelper.values(FORM_FIELD).indexOf(fields[index].replace('*', '')) !== -1) this.configs.defined_form_fields.push(fields[index]);else console.warn("Configuration::setFormFields: unsupported form field ".concat(fields[index]));
                }
            }
            /**
             * Method for setting meta information for checkout page
             *
             * @example
             * config.setMeta({
             brand_name: 'paydock',
             reference: '15',
             email: 'wault@paydock.com'
             });
             *
             * @param {IPayPalMeta | IZipmoneyMeta | IAfterpayMeta | IBamboraMeta} object -
             *    data which can be shown on checkout page [IPayPalMeta]{@link IPayPalMeta} [IZipmoneyMeta]{@link IZipmoneyMeta} [IAfterpayMeta]{@link IAfterpayMeta} [IBamboraMeta]{@link IBamboraMeta}
             */
        }, {
            key: "setMeta",
            value: function setMeta(meta) {
                for (var key in meta) {
                    if (!meta.hasOwnProperty(key)) continue;
                    if (SUPPORTED_CHECKOUT_META_COLLECTION.indexOf(key) !== -1) this.configs.meta[key] = meta[key];else console.warn("Configuration::setMeta: unsupported meta key ".concat(key));
                }
            }
            /**
             * Current method can change environment. By default environment = sandbox.
             * Also we can change domain alias for this environment. By default domain_alias = paydock.com
             *
             * @example
             * config.setEnv('production');
             * @param {string} env - sandbox, production
             * @param {string} [alias] - Own domain alias
             */
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.env.setEnv(env, alias);
            }
            /**
             * Title for tab which can be set instead of default
             *
             * @example
             * config.setLabel('custom label');
             *
             * @param {string} label - Text label for tab
             */
        }, {
            key: "setLabel",
            value: function setLabel(label) {
                this.configs.label = label;
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                return this.env.getEnv();
            }
            /**
             * createToken - method which exactly create payment one time token
             *
             * @example
             * config.createToken('582035346f65cdd57ee81192d6e5w65w4e5',
             *  function (data) {
             *      console.log(data);
             *  }, function (error) {
             *      console.log(error);
             * });
             *
             * @param {string} accessToken - Customer access token or public key which provided for each client
             * @param {createToken~requestCallback} cb - The callback that handles the success response.
             * @param {createToken~requestCallback} errorCb - The callback that handles the failed response.
             */
        }, {
            key: "createToken",
            value: function createToken(accessToken, cb) {
                var errorCb = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function (err) {};
                this.send(accessToken, function (data, status) {
                    if (status >= 200 && status < 300) return cb(data.resource.data.configuration_token);
                    if (typeof data.error === "undefined" || typeof data.error.message === "undefined") errorCb('unknown error');else errorCb(data.error.message);
                });
            }
        }, {
            key: "send",
            value: function send(accessToken, cb) {
                var request = new XMLHttpRequest();
                request.open('POST', this.getUrl(), true);
                request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                request.setRequestHeader(AccessToken.getAccessHeaderNameByToken(accessToken), accessToken);
                if (SDK.version) {
                    request.setRequestHeader(SDK.headerKeys.version, SDK.version);
                    request.setRequestHeader(SDK.headerKeys.type, SDK.type);
                }
                request.send(JSON.stringify(this.getConfigs()));
                request.onload = function () {
                    var res = {};
                    try {
                        res = JSON.parse(request.responseText);
                    } catch (e) {}
                    cb(res, request.status);
                };
            }
        }, {
            key: "getConfigs",
            value: function getConfigs() {
                return this.configs;
            }
        }, {
            key: "getUrl",
            value: function getUrl() {
                return this.env.getConf().url + CONFIGURATION_LINK;
            }
        }, {
            key: "setGiftCardSchemeData",
            value: function setGiftCardSchemeData(giftCardScheme, processingNetwork) {
                if (this.configs.predefined_fields.type !== PAYMENT_TYPE.GIFT_CARD) throw new Error('unsupported payment type');
                if (!giftCardScheme || !processingNetwork) throw new Error('');
                this.configs.predefined_fields.gift_card_scheme = giftCardScheme;
                this.configs.predefined_fields.processing_network = processingNetwork;
            }
        }], [{
            key: "createEachToken",
            value: function createEachToken(accessToken, configs, cb) {
                var errorCb = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function (errors) {};
                var tokens = new Array(configs.length);
                var errors = [];
                var counter = 0;
                var _loop = function _loop(index) {
                    if (configs.hasOwnProperty(index)) configs[index].createToken(accessToken, function (token) {
                        tokens[index] = token;
                        counter++;
                        if (configs.length === counter) Configuration.finishCreatingEachToken(tokens, errors, cb, errorCb);
                    }, function (err) {
                        errors.push("gateway: ".concat(configs[index].getConfigs().predefined_fields.gateway_id, " | ").concat(err));
                        counter++;
                        if (configs.length === counter) Configuration.finishCreatingEachToken(tokens, errors, cb, errorCb);
                    });
                };
                for (var index in configs) {
                    _loop(index);
                }
            }
        }, {
            key: "finishCreatingEachToken",
            value: function finishCreatingEachToken(tokens, errors, cb, errorCb) {
                if (errors.length >= 1) errorCb(errors);else cb(tokens);
            }
        }]);
        return Configuration;
    }();

    /**
     * Current constant include available type of element for styling
     * @const STYLABLE_ELEMENT
     * @type {object}
     * @param {string} INPUT=input.
     *   These states are available: [STYLABLE_ELEMENT_STATE.ERROR]{@link STYLABLE_ELEMENT_STATE}, [STYLABLE_ELEMENT_STATE.FOCUS]{@link STYLABLE_ELEMENT_STATE}.
     *   These styles are available [IElementStyleInput]{@link IElementStyleInput}
     * @param {string} SUBMIT_BUTTON=submit_button
     *   These states are available: [STYLABLE_ELEMENT_STATE.HOVER]{@link STYLABLE_ELEMENT_STATE}.
     *   These styles are available [IElementStyleSubmitButton]{@link IElementStyleSubmitButton}
     * @param {string} LABEL=label.
     *   These styles are available [IElementStyleLabel]{@link IElementStyleLabel}
     * @param {string} TITLE=title.
     *   These styles are available [IElementStyleTitle]{@link IElementStyleTitle}
     * @param {string} TITLE_DESCRIPTION=title_description.
     *   These styles are available [IElementStyleTitleDescription]{@link IElementStyleTitleDescription}
     * */
    var STYLABLE_ELEMENT = {
        INPUT: 'input',
        SUBMIT_BUTTON: 'submit_button',
        LABEL: 'label',
        TITLE: 'title',
        TITLE_DESCRIPTION: 'title_description'
    };
    /**
     * Current constant include available states of element for styling
     * @const STYLABLE_ELEMENT_STATE
     * @type {object}
     * @param {string} ERROR=error client|server validation. This state applies to: input
     * @param {string} FOCUS=focus focus. This state applies to: input
     * @param {string} HOVER=hover focus. This state applies to: submit_button
     * */
    var STYLABLE_ELEMENT_STATE = {
        ERROR: 'error',
        FOCUS: 'focus',
        HOVER: 'hover'
    };
    var stylableElements = [{
        element: STYLABLE_ELEMENT.INPUT,
        states: [STYLABLE_ELEMENT_STATE.FOCUS, STYLABLE_ELEMENT_STATE.ERROR],
        styles: ['color', 'border', 'border_radius', 'background_color', 'height', 'text_decoration', 'font_size', 'font_family', 'line_height', 'font_weight', 'padding', 'margin', 'transition']
    }, {
        element: STYLABLE_ELEMENT.SUBMIT_BUTTON,
        states: [STYLABLE_ELEMENT_STATE.HOVER],
        styles: ['color', 'border', 'border_radius', 'background_color', 'text_decoration', 'font_size', 'font_family', 'line_height', 'font_weight', 'padding', 'margin', 'transition', 'opacity']
    }, {
        element: STYLABLE_ELEMENT.LABEL,
        states: [],
        styles: ['color', 'text_decoration', 'font_size', 'font_family', 'line_height', 'font_weight', 'padding', 'margin']
    }, {
        element: STYLABLE_ELEMENT.TITLE,
        states: [],
        styles: ['color', 'text_decoration', 'font_size', 'font_family', 'line_height', 'font_weight', 'padding', 'margin', 'text_align']
    }, {
        element: STYLABLE_ELEMENT.TITLE_DESCRIPTION,
        states: [],
        styles: ['color', 'text_decoration', 'font_size', 'font_family', 'line_height', 'font_weight', 'padding', 'margin', 'text_align']
    }];
    /**
     * Interface for styling input element.
     * @interface IElementStyleInput
     *
     * @param {string} [color] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/color}
     * @param {string} [border] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/border}
     * @param {string} [border_radius] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/border-radius}
     * @param {string} [background_color] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/background-color}
     * @param {string} [height] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/height}
     * @param {string} [text_decoration] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/text-decoration}
     * @param {string} [font_size] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-size}
     * @param {string} [font_family] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-family}
     * @param {string} [transition] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/transition}
     * @param {string} [line_height] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/line-height}
     * @param {string} [font_weight] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight}
     * @param {string} [padding] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/padding}
     * @param {string} [margin] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/margin}
     */
    /**
     * Interface for styling submit_button element.
     * @interface IElementStyleSubmitButton
     *
     * @param {string} [color] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/color}
     * @param {string} [border] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/border}
     * @param {string} [border_radius] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/border-radius}
     * @param {string} [background_color] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/background-color}
     * @param {string} [text_decoration] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/text-decoration}
     * @param {string} [font_size] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-size}
     * @param {string} [font_family] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-family}
     * @param {string} [padding] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/padding}
     * @param {string} [margin] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/margin}
     * @param {string} [transition] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/transition}
     * @param {string} [line_height] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/line-height}
     * @param {string} [font_weight] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight}
     * @param {string} [opacity] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/opacity}
     */
    /**
     * Interface for styling label element.
     * @interface IElementStyleLabel
     *
     * @param {string} [color] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/color}
     * @param {string} [text_decoration] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/text-decoration}
     * @param {string} [font_size] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-size}
     * @param {string} [font_family] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-family}
     * @param {string} [line_height] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/line-height}
     * @param {string} [font_weight] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight}
     * @param {string} [padding] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/padding}
     * @param {string} [margin] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/margin}
     */
    /**
     * Interface for styling title element.
     * @interface IElementStyleTitle
     *
     * @param {string} [color] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/color}
     * @param {string} [text_decoration] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/text-decoration}
     * @param {string} [font_size] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-size}
     * @param {string} [font_family] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-family}
     * @param {string} [line_height] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/line-height}
     * @param {string} [font_weight] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight}
     * @param {string} [padding] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/padding}
     * @param {string} [margin] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/margin}
     * @param {string} ['text-align',] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/text-align}
     */
    /**
     * Interface for styling title_description element.
     * @interface IElementStyleTitleDescription
     *
     * @param {string} [color] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/color}
     * @param {string} [text_decoration] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/text-decoration}
     * @param {string} [font_size] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-size}
     * @param {string} [font_family] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-family}
     * @param {string} [line_height] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/line-height}
     * @param {string} [font_weight] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight}
     * @param {string} [padding] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/padding}
     * @param {string} [margin] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/margin}
     * @param {string} ['text-align',] Look more [mozilla.org/color]{@link https://developer.mozilla.org/en-US/docs/Web/CSS/text-align}
     */

    var ElementStyle = /*#__PURE__*/function () {
        function ElementStyle() {
            _classCallCheck(this, ElementStyle);
        }
        _createClass(ElementStyle, null, [{
            key: "check",
            value: function check(stylableElements, element, states, styles) {
                for (var index in stylableElements) {
                    if (!stylableElements.hasOwnProperty(index) || stylableElements[index].element !== element) continue;
                    if (stylableElements[index].states.indexOf(states) === -1 && states) return false;
                    for (var property in styles) {
                        if (!styles.hasOwnProperty(property)) continue;
                        if (stylableElements[index].styles.indexOf(property.replace('-', '_')) === -1) return false;
                    }
                    return true;
                }
                return false;
            }
        }, {
            key: "encode",
            value: function encode(element, states, styles) {
                var encodedStyles = [];
                for (var property in styles) {
                    if (styles.hasOwnProperty(property)) encodedStyles.push("".concat(property.replace('_', '-'), ":").concat(styles[property]));
                }
                var encodedStyleBlock = encodedStyles.join(';');
                if (states) return "".concat(element, "::").concat(states, "{").concat(encodedStyleBlock, "}");else return "".concat(element, "{").concat(encodedStyleBlock, "}");
            }
        }, {
            key: "decode",
            value: function decode(data) {
                var state = (data.match('::(.*){') || ['', null])[1];
                var element = state !== null ? (data.match('(.*)::') || ['', ''])[1].trim() : (data.match('(.*){') || ['', ''])[1].trim();
                var body = (data.match('{(.*)}') || ['', ''])[1].split(';');
                var styles = {};
                for (var index in body) {
                    if (!body.hasOwnProperty(index)) continue;
                    var style = body[index].split(':');
                    if (!style && style.length !== 2) continue;
                    styles[style[0].trim()] = (style[1] || '').trim();
                }
                return {
                    element: element,
                    state: state,
                    styles: styles
                };
            }
        }]);
        return ElementStyle;
    }();

    /**
     *
     * Class MultiWidget include method for for creating iframe url
     * @constructor
     *
     * @param {string} accessToken - PayDock users access token or public key
     * @param {(Configuration | string | Configuration[] | string[])} conf - exemplar[s] Configuration class OR configuration token
     *
     * @example
     * var widget = new MultiWidget('accessToken','configurationToken'); // With a pre-created configuration token
     *
     * var widget = new MultiWidget('accessToken',['configurationToken', 'configurationToken2']); // With pre-created configuration tokens
     *
     * var widget = new MultiWidget('accessToken', new Configuration('gatewayId')); With Configuration
     *
     * var widget = new MultiWidget('accessToken',[ With Configurations
     *      Configuration('gatewayId'),
     *      Configuration('gatewayId', 'bank_account')
     * ]);
     **/
    var MultiWidget = /*#__PURE__*/function () {
        function MultiWidget(accessToken, conf) {
            _classCallCheck(this, MultiWidget);
            this.configs = [];
            this.configTokens = [];
            this.link = new Link(WIDGET_LINK);
            if (SDK.version) {
                this.link.setParams({
                    sdk_version: SDK.version,
                    sdk_type: SDK.type
                });
            }
            if (!!AccessToken.validateJWT(accessToken)) {
                this.link.setParams({
                    token: accessToken
                });
            } else {
                this.link.setParams({
                    public_key: accessToken
                });
            }
            this.accessToken = accessToken;
            if (!conf || Array.isArray(conf) && !conf.length) throw Error('configuration token is required');
            if (typeof conf === 'string') this.configTokens.push(conf);else if (conf instanceof Configuration) this.configs.push(conf);else if (Array.isArray(conf) && typeof conf[0] === 'string') this.configTokens = conf;else if (Array.isArray(conf) && conf[0] instanceof Configuration) this.configs = conf;else throw Error('Unsupported type of configuration token');
        }
        /**
         * Object contain styles for widget
         *
         * @example
         * widget.setStyles({
         *       background_color: 'rgb(0, 0, 0)',
         *       border_color: 'yellow',
         *       text_color: '#FFFFAA',
         *       button_color: 'rgba(255, 255, 255, 0.9)',
         *       font_size: '20px'
         *       fort_family: 'fantasy'
         *   });
         * @param {IStyles} fields - name of styles which can be shown in widget [STYLE]{@link STYLE}
         */
        _createClass(MultiWidget, [{
            key: "setStyles",
            value: function setStyles(styles) {
                for (var index in styles) {
                    if (styles.hasOwnProperty(index)) this.setStyle(index, styles[index]);
                }
            }
            /**
             * Method to set a country code mask for the phone input.
             *
             * @example
             * widget.usePhoneCountryMask();
             *
             * @example
             * widget.usePhoneCountryMask({
             *       default_country: 'au',
             *       preferred_countries: ['au', 'gb'],
             *       only_countries: ['au', 'gb', 'us', 'ua']
             *   });
             *
             * @param {object} [options] - Options for configure the phone mask.
             * @param {string} [options.default_country] - Set a default country for the mask.
             * @param {Array.<string>} [options.preferred_countries] - Set list of preferred countries for the top of the select box .
             * @param {Array.<string>} [options.only_countries] - Set list of countries to show in the select box.
             */
        }, {
            key: "usePhoneCountryMask",
            value: function usePhoneCountryMask(options) {
                if (!options) return this.link.setParams({
                    use_country_phone_mask: true
                });
                if (options.only_countries && (!/^[a-z]+$/.test(options.only_countries.join('')) || options.only_countries.length * 2 !== options.only_countries.join('').length)) return console.warn("Widget::usePhoneCountryMask[s: only_countries - unsupported symbols or incorrect length of value");
                if (options.preferred_countries && (!/^[a-z]+$/.test(options.preferred_countries.join('')) || options.preferred_countries.length * 2 !== options.preferred_countries.join('').length)) return console.warn("Widget::usePhoneCountryMask[s: preferred_countries - unsupported symbols or incorrect length of value");
                if (options.default_country && options.default_country.length !== 2) return console.warn("Widget::usePhoneCountryMask[s: default_country - incorrect value length");
                this.link.setParams({
                    use_country_phone_mask: true
                });
                if (options.only_countries) this.link.setParams({
                    phone_mask_only_countries: options.only_countries.join(',')
                });
                if (options.preferred_countries) this.link.setParams({
                    phone_mask_preferred_countries: options.preferred_countries.join(',')
                });
                if (options.default_country) this.link.setParams({
                    phone_mask_default_country: options.default_country
                });
            }
        }, {
            key: "setStyle",
            value: function setStyle(param, value) {
                if (ObjectHelper.values(STYLE).indexOf(param) !== -1) this.link.setParams(_defineProperty({}, param, value));else console.warn("Widget::setStyle[s: unsupported style param ".concat(param));
            }
            /**
             * Method for set different texts inside the widget
             *
             * @example
             * widget.setTexts({
             *       title: 'Your card',
             *       finish_text: 'Payment resource was successfully accepted',
             *       title_description: '* indicates required field',
             *       submit_button: 'Save',
             *       submit_button_processing: 'Load...',
             *   });
             *
             * @param {ITexts} fields - name of text items which can be shown in widget [TEXT]{@link TEXT}
             */
        }, {
            key: "setTexts",
            value: function setTexts(texts) {
                for (var index in texts) {
                    if (texts.hasOwnProperty(index)) this.setText(index, texts[index]);
                }
            }
        }, {
            key: "setText",
            value: function setText(param, value) {
                if (ObjectHelper.values(TEXT).indexOf(param) !== -1) this.link.setParams(_defineProperty({}, param, value));else console.warn("Widget::setText[s: unsupported text param ".concat(param));
            }
            /**
             * Method for set styles for different elements and states
             *
             * @example
             * widget.setElementStyle('input', {
             *   border: 'green solid 1px'
             * });
             *
             * widget.setElementStyle('input', 'focus', {
             *   border: 'blue solid 1px'
             * });
             *
             * widget.setElementStyle('input', 'error', {
             *  border: 'red solid 1px'
             * });
             *
             * @param {string} element - type of element for styling. These elements are available [STYLABLE_ELEMENT]{@link STYLABLE_ELEMENT}
             * @param {string} [state] - state of element for styling. These states are available [STYLABLE_ELEMENT_STATE]{@link STYLABLE_ELEMENT_STATE}
             * @param {IElementStyleInput | IElementStyleSubmitButton | IElementStyleLabel | IElementStyleTitle | IElementStyleTitleDescription} styles - styles list
             */
        }, {
            key: "setElementStyle",
            value: function setElementStyle(element, a2, a3) {
                var state = arguments.length === 3 ? a2 : null;
                var styles = arguments.length === 3 ? a3 : a2;
                if (!ElementStyle.check(stylableElements, element, state, styles)) return console.warn("Styles for \"".concat(element, "\" element with \"").concat(state || 'default', "\" state was ignored because some of the arguments are unacceptable"));
                this.link.concatParams({
                    element_styles: ElementStyle.encode(element, state, styles)
                });
            }
            /**
             * The method to set the predefined values for the form fields inside the widget
             *
             * @example
             *   widget.setFormValues({
             *       email: 'predefined@email.com',
             *       card_name: 'Houston'
             *   });
             *
             * @param { Object } fieldValues - Key of object is one of [FORM_FIELD]{@link FORM_FIELD}, The object value is what we are expecting
             */
        }, {
            key: "setFormValues",
            value: function setFormValues(fieldValues) {
                for (var key in fieldValues) {
                    if (fieldValues.hasOwnProperty(key)) this.setFormValue(key, fieldValues[key]);
                }
            }
        }, {
            key: "setFormValue",
            value: function setFormValue(key, value) {
                if (ObjectHelper.values(FORM_FIELD).indexOf(key) === -1) return console.warn("Widget::setFormValues[s: unsupported field ".concat(key));
                if (/\,/.test(value) || /\:/.test(value)) return console.warn("Widget::setFormValues[s: ".concat(key, " - unsupported symbols (: or ,) in value"));
                if (typeof this.link.getParams().form_values === 'string' && this.link.getParams().form_values.length) this.link.setParams({
                    form_values: "".concat(this.link.getParams().form_values, ",").concat(key, ":").concat(value)
                });else this.link.setParams({
                    form_values: "".concat(key, ":").concat(value)
                });
            }
            /**
             * The method to set custom form field labels
             *
             * @example
             *   widget.setFormPlaceholders({
             *       card_name: 'Card Holder Name',
             *       email: 'Email For Receipt'
             *   })
             *
             * @param { Object } fieldLabels - Key of object is one of [FORM_FIELD]{@link FORM_FIELD}, The object value is what we are expecting
             */
        }, {
            key: "setFormLabels",
            value: function setFormLabels(fieldLabels) {
                for (var key in fieldLabels) {
                    if (fieldLabels.hasOwnProperty(key)) this.setFormLabel(key, fieldLabels[key]);
                }
            }
        }, {
            key: "setFormLabel",
            value: function setFormLabel(key, label) {
                if (ObjectHelper.values(FORM_FIELD).indexOf(key) === -1) return console.warn("Widget::setFormLabel[s: unsupported field ".concat(key));
                var modifiedLabel = label === null || label === '' ? ' ' : label;
                if (/\,/.test(modifiedLabel) || /\:/.test(modifiedLabel)) return console.warn("Widget::setFormLabel[s: ".concat(key, " - unsupported symbols (: or ,) in value"));
                this.link.concatParams({
                    form_labels: "".concat(key, ":").concat(modifiedLabel)
                });
            }
            /**
             * The method to set custom form fields placeholders
             *
             * @example
             *   widget.setFormPlaceholders({
             *       card_name: 'Input your card holder name...',
             *       email: 'Input your email, like test@example.com'
             *   })
             *
             * @param { Object } fieldPlaceholders - Key of object is one of [FORM_FIELD]{@link FORM_FIELD}, Value of object is expected placeholder
             */
        }, {
            key: "setFormPlaceholders",
            value: function setFormPlaceholders(fieldPlaceholders) {
                for (var key in fieldPlaceholders) {
                    if (fieldPlaceholders.hasOwnProperty(key)) this.setFormPlaceholder(key, fieldPlaceholders[key]);
                }
            }
        }, {
            key: "setFormPlaceholder",
            value: function setFormPlaceholder(key, placeholder) {
                if (ObjectHelper.values(FORM_FIELD).indexOf(key) === -1) return console.warn("Widget::setFormPlaceholder[s: unsupported field ".concat(key));
                var modifiedPlaceholder = placeholder === null || placeholder === '' ? ' ' : placeholder;
                if (/\,/.test(modifiedPlaceholder) || /\:/.test(modifiedPlaceholder)) return console.warn("Widget::setFormPlaceholder[s: ".concat(key, " - unsupported symbols (: or ,) in value"));
                this.link.concatParams({
                    form_placeholders: "".concat(key, ":").concat(modifiedPlaceholder)
                });
            }
            /**
             * The method to set the full configuration for the all specific form elements (label, placeholder, value)
             * You can also use the other method for the partial configuration like: setFormValues, setFormPlaceholder, setFormLabel
             *
             * @example
             *   widget.setFormElements([
             *       {
             *           field:  'card_name',
             *           placeholder: 'Input your card holder name...',
             *           label: 'Card Holder Name',
             *           value: 'Houston',
             *       },
             *       {
             *           field:  'email',
             *           placeholder: 'Input your email, like test@example.com',
             *           label: 'Email For Receipt',
             *           value: 'predefined@email.com',
             *       },
             *   ])
             *
             * @param { string } elements - The list of elements
             * @param { string } elements[].field - Field name of the element [FORM_FIELD]{@link FORM_FIELD}
             * @param { string } elements[].placeholder - Set custom form field placeholder
             * @param { string } elements[].label - Set custom labels near form field
             * @param { string } elements[].value - Set predefined values for the form field
             */
        }, {
            key: "setFormElements",
            value: function setFormElements(elements) {
                var _this = this;
                elements.forEach(function (element) {
                    return _this.setFormElement(element);
                });
            }
        }, {
            key: "setFormElement",
            value: function setFormElement(element) {
                if (element.value) this.setFormValue(element.field, element.value);
                if (element.label) this.setFormLabel(element.field, element.label);
                if (element.placeholder) this.setFormPlaceholder(element.field, element.placeholder);
            }
            /**
             * The method to change the widget icons
             *
             * @TODO DEPRECATED
             */
        }, {
            key: "setIcons",
            value: function setIcons(icons) {
                for (var key in icons) {
                    if (icons.hasOwnProperty(key)) this.setIcon(key, icons[key]);
                }
            }
        }, {
            key: "setIcon",
            value: function setIcon(key, value) {
                if (/\,/.test(value) || /\:/.test(value)) return console.warn("Widget::setIcon[s: ".concat(key, " - unsupported symbols (: or ,) in value"));
                if (typeof this.link.getParams().icons === 'string' && this.link.getParams().icons.length) this.link.setParams({
                    icons: "".concat(this.link.getParams().icons, ",").concat(key, ":").concat(value)
                });else this.link.setParams({
                    icons: "".concat(key, ":").concat(value)
                });
            }
            /**
             * Using this method you can set hidden elements inside widget
             *
             * @example
             * widget.setHiddenElements(['submit_button', 'email']);
             *
             * @param {string[]} elements -  list of element which can be hidden [ELEMENT]{@link ELEMENT} || [FORM_FIELD]{@link FORM_FIELD}
             */
        }, {
            key: "setHiddenElements",
            value: function setHiddenElements(elements) {
                var filteredElements = [];
                var supportedElements = ObjectHelper.values(ELEMENT).concat(ObjectHelper.values(FORM_FIELD));
                for (var index in elements) {
                    if (!elements.hasOwnProperty(index)) continue;
                    if (supportedElements.indexOf(elements[index]) !== -1) filteredElements.push(elements[index]);else console.warn("Widget::setHiddenElements: unsupported element ".concat(elements[index]));
                }
                if (filteredElements.length) this.link.concatParams({
                    hidden_elements: filteredElements.join(',')
                });
            }
            /**
             * Current method can set custom ID to identify the data in the future
             *
             * @example
             * widget.setRefId('id');
             *
             * @param {string} refId - custom id
             */
        }, {
            key: "setRefId",
            value: function setRefId(refId) {
                this.link.setParams({
                    ref_id: refId
                });
            }
            /**
             * Current method can add visual validation from gateway to widget's form fields
             *
             * @example
             * widget.useGatewayFieldValidation();
             */
        }, {
            key: "useGatewayFieldValidation",
            value: function useGatewayFieldValidation() {
                this.link.setParams({
                    fields_validation: true
                });
            }
            /**
             * Current method can set icons of supported card types
             *
             * @example
             *
             * widget.setSupportedCardIcons(['mastercard', 'visa'], validateCardNumberInput);
             *
             * @param {string[]} elements - [SUPPORTED_CARD_TYPES]{@link SUPPORTED_CARD_TYPES}
             * @param {boolean} validateCardNumberInput - [validateCardNumberInput=false] - using this param you allow validation for card number input on supported card types
             */
        }, {
            key: "setSupportedCardIcons",
            value: function setSupportedCardIcons(elements, validateCardNumberInput) {
                var supportedCards = [];
                for (var index in elements) {
                    if (!elements.hasOwnProperty(index)) continue;
                    if (ObjectHelper.values(SUPPORTED_CARD_TYPES).indexOf(elements[index]) !== -1) supportedCards.push(elements[index]);else console.warn("Widget::cardTypes: unsupported type of cards ".concat(elements[index]));
                }
                if (supportedCards.length) this.link.concatParams({
                    supported_card_types: supportedCards.join(',')
                });
                if (validateCardNumberInput) this.link.setParams({
                    validate_card_types: validateCardNumberInput
                });
            }
            /**
             * Current method can change environment. By default environment = sandbox.
             * Also we can change domain alias for this environment. By default domain_alias = paydock.com
             *
             * @example
             * widget.setEnv('production', 'paydock.com');
             * @param {string} env - sandbox, production
             * @param {string} [alias] - Own domain alias
             */
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.link.setEnv(env, alias);
                for (var index in this.configs) {
                    if (this.configs.hasOwnProperty(index)) this.configs[index].setEnv(env, alias);
                }
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                this.link.getEnv();
            }
            /**
             * Method for creating iframe url
             *
             * @example
             * widget.loadIFrameUrl(function (url) {
             *      console.log(url);
             * }, function (errors) {
             *      console.log(errors);
             * });
             */
        }, {
            key: "loadIFrameUrl",
            value: function loadIFrameUrl(cb) {
                var _this2 = this;
                var errorCb = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : function (errors) {};
                this.link.setParams({
                    configuration_tokens: ''
                });
                if (this.configTokens.length) {
                    this.link.setParams({
                        configuration_tokens: this.configTokens.join(',')
                    });
                    return cb(this.link.getUrl());
                }
                Configuration.createEachToken(this.accessToken, this.configs, function (tokens) {
                    _this2.link.concatParams({
                        configuration_tokens: tokens.join(',')
                    });
                    return cb(_this2.link.getUrl());
                }, function (errors) {
                    errorCb(errors);
                });
            }
            /**
             * Method for setting a custom language code
             *
             * @example
             * config.setLanguage('en');
             * @param {string} code - ISO 639-1
             */
        }, {
            key: "setLanguage",
            value: function setLanguage(code) {
                this.link.setParams({
                    language: code
                });
            }
        }]);
        return MultiWidget;
    }();

    var Event = /*#__PURE__*/function () {
        function Event() {
            _classCallCheck(this, Event);
        }
        _createClass(Event, null, [{
            key: "insertToInput",
            value: function insertToInput(selector, dataType, eventData) {
                if (typeof eventData[dataType] === "undefined") return;
                var input = document.querySelector(selector);
                if (!input) return;
                input.value = eventData[dataType];
            }
        }, {
            key: "subscribe",
            value: function subscribe(name, subject, listener) {
                if (subject.addEventListener) {
                    subject.addEventListener(name, listener);
                } else {
                    subject.attachEvent("on".concat(name), listener);
                }
            }
        }]);
        return Event;
    }();

    var Container = /*#__PURE__*/function () {
        function Container(selector) {
            _classCallCheck(this, Container);
            this.selector = selector;
        }
        _createClass(Container, [{
            key: "isExist",
            value: function isExist() {
                return !!this.getElement();
            }
        }, {
            key: "getStyles",
            value: function getStyles(allowValue) {
                if (!this.isExist()) return;
                var container = this.getElement();
                var styles = container.getAttribute("widget-style");
                if (!styles) return {};
                var containerStyleCollection = styles.split(';');
                if (typeof containerStyleCollection !== 'undefined' && !containerStyleCollection.length) return {};
                return this.convertConfigs(containerStyleCollection, allowValue);
            }
        }, {
            key: "on",
            value: function on(name, cb) {
                if (!this.isExist()) return;
                Event.subscribe(name, this.getElement(), cb);
            }
        }, {
            key: "getAttr",
            value: function getAttr(allowValue) {
                if (!this.isExist()) return;
                var container = this.getElement();
                var containerTextCollection = [];
                for (var index in allowValue) {
                    if (!allowValue.hasOwnProperty(index)) continue;
                    var item = allowValue[index].replace(/_/g, "-");
                    var currentConfig = container.getAttribute(item);
                    if (currentConfig) {
                        containerTextCollection.push("".concat(allowValue[index], ":").concat(currentConfig));
                    }
                }
                if (typeof containerTextCollection !== 'undefined' && !containerTextCollection.length) return {};
                return this.convertConfigs(containerTextCollection, allowValue);
            }
        }, {
            key: "getElement",
            value: function getElement() {
                return document.querySelector(this.selector);
            }
        }, {
            key: "convertConfigs",
            value: function convertConfigs(params, allowValue) {
                var config = {};
                for (var index in params) {
                    if (!params.hasOwnProperty(index)) continue;
                    var style = params[index].split(':');
                    var collectionKey = style[0].replace(/-/g, "_").trim();
                    if (allowValue.indexOf(collectionKey) !== -1) config[collectionKey] = style[1].trim();
                }
                return config;
            }
        }]);
        return Container;
    }();

    var IFrame = /*#__PURE__*/function () {
        function IFrame(container) {
            _classCallCheck(this, IFrame);
            this.container = container;
        }
        _createClass(IFrame, [{
            key: "load",
            value: function load(link) {
                var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
                if (!this.container.isExist() || this.isExist()) return;
                var iFrame = document.createElement('iframe');
                iFrame.setAttribute('src', link);
                if (options.title) iFrame.title = options.title;
                this.container.getElement().appendChild(iFrame);
            }
        }, {
            key: "loadFromHtml",
            value: function loadFromHtml(content) {
                var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
                if (!this.container.isExist() || this.isExist()) return;
                var iFrame = document.createElement('iframe');
                iFrame.setAttribute('height', '100%');
                iFrame.setAttribute('width', '100%');
                if (options.title) iFrame.title = options.title;
                var template = "<html><head><style>html, body {margin: 0;} iframe {border: 0; width: 100%}</style><title></title></head><body>{{content}}</body></html>";
                this.container.getElement().appendChild(iFrame);
                var iFrameDocument = this.getElement().contentDocument;
                iFrameDocument.open();
                iFrameDocument.write(template.replace('{{content}}', content));
                iFrameDocument.close();
            }
        }, {
            key: "remove",
            value: function remove() {
                if (!this.container.isExist() || !this.isExist()) return;
                var iFrame = this.getElement();
                this.container.getElement().removeChild(iFrame);
            }
        }, {
            key: "show",
            value: function show() {
                if (!this.isExist()) return;
                this.setStyle('visibility', 'visible');
                this.setStyle('display', 'block');
            }
        }, {
            key: "hide",
            value: function hide() {
                var saveSize = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
                if (!this.isExist()) return;
                if (saveSize) this.setStyle('visibility', 'hidden');else this.setStyle('display', 'none');
            }
        }, {
            key: "isExist",
            value: function isExist() {
                return !!this.getElement();
            }
        }, {
            key: "getElement",
            value: function getElement() {
                if (!this.container.isExist()) return null;
                return this.container.getElement().querySelector('iframe');
            }
        }, {
            key: "setStyle",
            value: function setStyle(property, value) {
                var iFrame = this.getElement();
                iFrame.style[property] = value;
            }
        }]);
        return IFrame;
    }();

    /**
     * Interface of data from event.
     * @interface IEventData
     *
     * @param {string} event
     * @param {string} purpose
     * @param {string} message_source
     * @param {string} [ref_id]
     * */
    var EVENT = {
        AFTER_LOAD: 'afterLoad',
        SUBMIT: 'submit',
        FINISH: 'finish',
        VALIDATION_ERROR: 'validationError',
        SYSTEM_ERROR: 'systemError',
        CHECKOUT_SUCCESS: 'checkoutSuccess',
        CHECKOUT_READY: 'checkoutReady',
        CHECKOUT_ERROR: 'checkoutError',
        CHECKOUT_COMPLETED: 'checkoutCompleted',
        VALIDATION: 'validation',
        SELECT: 'select',
        UNSELECT: 'unselect',
        NEXT: 'next',
        PREV: 'prev',
        META_CHANGE: 'metaChange',
        RESIZE: 'resize',
        CHARGE_AUTH_SUCCESS: 'chargeAuthSuccess',
        CHARGE_AUTH_REJECT: 'chargeAuthReject',
        CHARGE_AUTH_CANCELLED: 'chargeAuthCancelled',
        ADDITIONAL_DATA_SUCCESS: 'additionalDataCollectSuccess',
        ADDITIONAL_DATA_REJECT: 'additionalDataCollectReject',
        CHARGE_AUTH: 'chargeAuth',
        DISPATCH_SUCCESS: 'dispatchSuccess',
        DISPATCH_ERROR: 'dispatchError'
    };
    var IFrameEvent = /*#__PURE__*/function () {
        function IFrameEvent(subject) {
            var _this = this;
            _classCallCheck(this, IFrameEvent);
            this.listeners = [];
            if (!subject) return;
            Event.subscribe('message', subject, function (event) {
                var data;
                try {
                    data = JSON.parse(event.data);
                } catch (error) {}
                if (!data)
                    // @TODO add filter on message_source
                    return;
                _this.emit(data);
            });
        }
        _createClass(IFrameEvent, [{
            key: "emit",
            value: function emit(data) {
                for (var key in this.listeners) {
                    if (this.listeners[key].event === data.event && data.widget_id === this.listeners[key].widget_id) this.listeners[key].listener.apply(this, [data]);
                }
            }
        }, {
            key: "on",
            value: function on(eventName, widgetId, cb) {
                for (var event in EVENT) {
                    if (!EVENT.hasOwnProperty(event)) continue;
                    if (eventName === EVENT[event]) {
                        this.listeners.push({
                            event: eventName,
                            listener: cb,
                            widget_id: widgetId
                        });
                    }
                }
            }
        }, {
            key: "clear",
            value: function clear() {
                this.listeners = [];
            }
        }, {
            key: "subscribe",
            value: function subscribe(subject, listener) {
                if (subject.addEventListener) {
                    subject.addEventListener("message", listener);
                } else {
                    subject.attachEvent("onmessage", listener);
                }
            }
        }]);
        return IFrameEvent;
    }();

    /**
     * Interface for classes that represent a trigger data.
     * @interface ITriggerData
     *
     * @param {string} [configuration_token]
     * @param {string} [tab_number]
     * @param {string} [elements]
     * @param {string} [form_values]
     * */
    /**
     * List of available triggers
     *
     * @type {object}
     * @param {string} SUBMIT_FORM=submit_form
     * @param {string} CHANGE_TAB=tab
     * @param {string} HIDE_ELEMENTS=hide_elements
     * @param {string} SHOW_ELEMENTS=show_elements
     * @param {string} REFRESH_CHECKOUT=refresh_checkout
     * @param {string} UPDATE_FORM_VALUES=update_form_values
     * @param {string} INIT_CHECKOUT=init_checkout
     */
    var TRIGGER = {
        SUBMIT_FORM: 'submit_form',
        CHANGE_TAB: 'tab',
        HIDE_ELEMENTS: 'hide_elements',
        SHOW_ELEMENTS: 'show_elements',
        REFRESH_CHECKOUT: 'refresh_checkout',
        UPDATE_FORM_VALUES: 'update_form_values',
        INIT_CHECKOUT: 'init_checkout'
    };
    var Trigger = /*#__PURE__*/function () {
        function Trigger(iFrame) {
            _classCallCheck(this, Trigger);
            this.iFrame = iFrame;
        }
        _createClass(Trigger, [{
            key: "push",
            value: function push(triggerName) {
                var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
                if (!this.iFrame.isExist()) return;
                if (ObjectHelper.values(TRIGGER).indexOf(triggerName) === -1) console.warn('unsupported trigger type');
                var body = {
                    trigger: triggerName,
                    destination: 'widget.paydock',
                    data: data
                };
                this.iFrame.getElement().contentWindow.postMessage(JSON.stringify(body), '*');
            }
        }]);
        return Trigger;
    }();

    var FormInterceptor = /*#__PURE__*/function () {
        function FormInterceptor(selector) {
            _classCallCheck(this, FormInterceptor);
            this.intercepted = false;
            this.selector = selector;
        }
        _createClass(FormInterceptor, [{
            key: "getElement",
            value: function getElement() {
                return document.querySelector(this.selector);
            }
        }, {
            key: "isExist",
            value: function isExist() {
                return !!this.getElement();
            }
        }, {
            key: "beforeSubmit",
            value: function beforeSubmit(cb) {
                var _this = this;
                if (!this.isExist()) return;
                this.subscribe(this.getElement(), function (event) {
                    event.preventDefault();
                    _this.intercepted = true;
                    cb.apply(_this, []);
                });
            }
        }, {
            key: "continueSubmit",
            value: function continueSubmit() {
                var _this2 = this;
                if (!this.isExist() || !this.intercepted) return;
                this.intercepted = false;
                setTimeout(function () {
                    _this2.getElement().submit();
                }, 50);
            }
        }, {
            key: "subscribe",
            value: function subscribe(subject, listener) {
                if (subject.addEventListener) {
                    subject.addEventListener("submit", listener);
                } else {
                    subject.attachEvent("onsubmit", listener);
                }
            }
        }]);
        return FormInterceptor;
    }();

    /**
     * Interface of data from validation event.
     * @interface IFormValidation
     *
     * @param {string} event Event name
     * @param {string} purpose System variable. Purpose of event
     * @param {string} message_source System variable. Event source
     * @param {string} [ref_id] Custom value for identify result of processed operation
     * @param {boolean} [form_valid] Form is valid
     * @param {array} [invalid_fields] Invalid form fields
     * @param {array} [invalid_showed_fields] List of fields on which the error is already displayed
     * @param {array} [validators] List of validators with fields
     * */
    /**
     * Interface of data from event.
     * @interface IEventMetaData
     *
     * @param {string} event Event name
     * @param {string} purpose System variable. Purpose of event
     * @param {string} message_source System variable. Event source
     * @param {string} [ref_id] Custom value for identify result of processed operation
     * @param {string} configuration_token Token received from our API with widget data
     * @param {string} type Payment type 'card', 'bank_account'
     * @param {string} gateway_type Gateway type
     * @param {string} [card_number_last4] Last 4 digit of your card
     * @param {string} [card_scheme] Card scheme
     * @param {number} [card_number_length] Card number length
     * @param {string} [account_name] Bank account account name
     * @param {string} [account_number] Bank account account number
     * */
    /**
     * Interface of data from event.
     * @interface IEventAfterLoadData
     *
     * @param {string} event Event name
     * @param {string} purpose System variable. Purpose of event
     * @param {string} message_source System variable. Event source
     * @param {string} [ref_id] Custom value for identify result of processed operation
     * */
    /**
     * Interface of data from event.
     * @interface IEventFinishData
     *
     * @param {string} event Event name
     * @param {string} purpose System variable. Purpose of event
     * @param {string} message_source System variable. Event source
     * @param {string} [ref_id] Custom value for identify result of processed operation
     * @param {string} payment_source One time token. Result from this endpoint [API docs](https://docs.paydock.com/#tokens)
     * */
    /**
     * List of available event's name
     * @const EVENT
     *
     * @type {object}
     * @param {string} AFTER_LOAD=afterLoad
     * @param {string} SUBMIT=submit
     * @param {string} FINISH=finish
     * @param {string} VALIDATION=validation
     * @param {string} VALIDATION_ERROR=validationError
     * @param {string} SYSTEM_ERROR=systemError
     * @param {string} META_CHANGE=metaChange
     * @param {string} RESIZE=resize
     */
    /**
     * List of available event's name
     * @const VAULT_DISPLAY_EVENT
     *
     * @type {object}
     * @param {string} AFTER_LOAD=afterLoad
     * @param {string} SYSTEM_ERROR=system_error
     * @param {string} CVV_SECURE_CODE_REQUESTED=cvv_secure_code_requested
     * @param {string} CARD_NUMBER_SECURE_CODE_REQUESTED=card_number_secure_code_requested
     * @param {string} ACCESS_FORBIDDEN=access_forbidden
     * @param {string} SESSION_EXPIRED=systemError
     * @param {string} SYSTEM_ERROR=session_expired
     * @param {string} OPERATION_FORBIDDEN=operation_forbidden
     */
    /**
     * Class HtmlMultiWidget include method for working with html
     * @constructor
     * @extends MultiWidget
     *
     * @param {string} selector - Selector of html element. Container for widget
     * @param {string} publicKey - PayDock users public key
     * @param {(Configuration | string | Configuration[] | string[])} conf - exemplar[s] Configuration class OR configuration token
     * @example
     * var widget = new MultiWidget('#widget', 'publicKey','configurationToken'); // With a pre-created configuration token
     *
     * var widget = new MultiWidget('#widget', 'publicKey',['configurationToken', 'configurationToken2']); // With pre-created configuration tokens
     *
     * var widget = new MultiWidget('#widget', 'publicKey', new Configuration('gatewayId')); With Configuration
     *
     * var widget = new MultiWidget('#widget', 'publicKey',[ With Configurations
     *      Configuration(), // default gateway_id,
     *      Configuration('not_configured'), // without gateway,
     *      Configuration('gatewayId'),
     *      Configuration('gatewayId', 'bank_account')
     * ]);
     **/
    var HtmlMultiWidget = /*#__PURE__*/function (_MultiWidget) {
        _inherits(HtmlMultiWidget, _MultiWidget);
        var _super = _createSuper(HtmlMultiWidget);
        function HtmlMultiWidget(selector, publicKey, conf) {
            var _this;
            _classCallCheck(this, HtmlMultiWidget);
            _this = _super.call(this, publicKey, conf);
            _this.validationData = {};
            _this.container = new Container(selector);
            _this.iFrame = new IFrame(_this.container);
            _this.triggerElement = new Trigger(_this.iFrame);
            _this.event = new IFrameEvent(window);
            return _this;
        }
        /**
         * The final method to beginning, the load process of widget to html
         *
         */
        _createClass(HtmlMultiWidget, [{
            key: "load",
            value: function load() {
                var _this2 = this;
                this.setStyles(this.container.getStyles(ObjectHelper.values(STYLE)));
                this.setTexts(this.container.getAttr(ObjectHelper.values(TEXT)));
                this.loadIFrameUrl(function (url) {
                    _this2.iFrame.load(url, {
                        title: 'Card details'
                    });
                    _this2.afterLoad();
                }, function (errors) {
                    console.error('Errors when creating a token[s, widget will not be load:');
                    for (var index in errors) {
                        if (errors.hasOwnProperty(index)) console.error("--- | ".concat(errors[index]));
                    }
                });
            }
        }, {
            key: "afterLoad",
            value: function afterLoad() {
                var _this3 = this;
                this.on(EVENT.VALIDATION, function (data) {
                    _this3.validationData = data;
                });
            }
            /**
             * Listen to events of widget
             *
             * @example
             *
             * widget.on('form_submit', function (data) {
             *      console.log(data);
             * });
             * // or
             *  widget.on('form_submit').then(function (data) {
             *      console.log(data);
             * });
             * @param {string} eventName - Available event names [EVENT]{@link EVENT}
             * @param {listener} [cb]
             * @return {Promise<IEventData | IEventMetaData | IEventFinishData | IFormValidation> | void}
             */
        }, {
            key: "on",
            value: function on(eventName, cb) {
                var _this4 = this;
                if (typeof cb === "function") return this.event.on(eventName, this.link.getParams().widget_id, cb);
                return new Promise(function (resolve) {
                    return _this4.event.on(eventName, _this4.link.getParams().widget_id, function (res) {
                        return resolve(res);
                    });
                });
            }
            /**
             * This callback will be called for every trigger
             *
             * @param {triggerName} triggers - submit_form, tab
             * @param {ITriggerData} data which will be sending to widget
             */
        }, {
            key: "trigger",
            value: function trigger(triggerName) {
                var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
                this.triggerElement.push(triggerName, data);
            }
            /**
             * Using this method you can get validation state information
             * @return {IFormValidation} Form validation object
             */
        }, {
            key: "getValidationState",
            value: function getValidationState() {
                return this.validationData;
            }
            /**
             * Using this method you can check if the form is valid
             * @return {boolean} Form is valid
             */
        }, {
            key: "isValidForm",
            value: function isValidForm() {
                return !!this.validationData.form_valid;
            }
            /**
             * Using this method you can check if a specific form field is invalid
             * @param {string} field - Field name
             * @return {boolean} Field is invalid
             */
        }, {
            key: "isInvalidField",
            value: function isInvalidField() {
                var field = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
                if (!this.validationData.invalid_fields) return false;
                return this.validationData.invalid_fields.indexOf(field) !== -1;
            }
            /**
             * Using this method you can check if an error is displayed on a specific field
             * @param {string} field - Field name
             * @return {boolean} Error is showed on field
             */
        }, {
            key: "isFieldErrorShowed",
            value: function isFieldErrorShowed() {
                var field = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
                if (!this.validationData.invalid_showed_fields) return false;
                return this.validationData.invalid_showed_fields.indexOf(field) !== -1;
            }
            /**
             * Using this method you can check if a specific field is invalid
             * @param {string} field - Field name
             * @param {string} validator - Validator name. Available validators: `required, cardNumberValidator, expireDateValidation`
             * @return {boolean} Field is invalid by validator
             */
        }, {
            key: "isInvalidFieldByValidator",
            value: function isInvalidFieldByValidator() {
                var field = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
                var validator = arguments.length > 1 ? arguments[1] : undefined;
                if (this.validationData.validators && !this.validationData.validators[validator]) return false;
                return this.validationData.validators[validator].indexOf(field) !== -1;
            }
            /**
             * Using this method you can hide widget after load
             * @param {boolean} [saveSize=false] - using this param you can save iframe's size
             */
        }, {
            key: "hide",
            value: function hide(saveSize) {
                this.iFrame.hide(saveSize);
            }
            /**
             * Using this method you can show widget after using hide method
             *
             */
        }, {
            key: "show",
            value: function show() {
                this.iFrame.show();
            }
            /**
             * Using this method you can reload widget
             *
             */
        }, {
            key: "reload",
            value: function reload() {
                this.iFrame.remove();
                this.load();
            }
            /**
             * Using this method you can hide any elements inside widget
             *
             * @example
             * widget.hideElements(['submit_button', 'email']);
             *
             * @param {string[]} elements -  list of element which can be hidden [ELEMENT]{@link ELEMENT} || [FORM_FIELD]{@link FORM_FIELD}
             */
        }, {
            key: "hideElements",
            value: function hideElements(elements) {
                var filteredElements = [];
                var supportedElements = ObjectHelper.values(ELEMENT).concat(ObjectHelper.values(FORM_FIELD));
                for (var index in elements) {
                    if (!elements.hasOwnProperty(index)) continue;
                    if (supportedElements.indexOf(elements[index]) !== -1) filteredElements.push(elements[index]);else console.warn("Widget::hideElements: unsupported element ".concat(elements[index]));
                }
                if (filteredElements.length) this.trigger(TRIGGER.HIDE_ELEMENTS, {
                    elements: filteredElements.join(',')
                });
            }
            /**
             * Using this method you can show any elements inside widget
             *
             * * @example
             * widget.showElements(['submit_button', 'email']);
             *
             * @param {string[]} elements -  list of element which can be showed [ELEMENT]{@link ELEMENT} || [FORM_FIELD]{@link FORM_FIELD}
             *
             */
        }, {
            key: "showElements",
            value: function showElements(elements) {
                var filteredElements = [];
                var supportedElements = ObjectHelper.values(ELEMENT).concat(ObjectHelper.values(FORM_FIELD));
                for (var index in elements) {
                    if (!elements.hasOwnProperty(index)) continue;
                    if (supportedElements.indexOf(elements[index]) !== -1) filteredElements.push(elements[index]);else console.warn("Widget::showElements: unsupported element ".concat(elements[index]));
                }
                if (filteredElements.length) this.trigger(TRIGGER.SHOW_ELEMENTS, {
                    elements: filteredElements.join(',')
                });
            }
            /**
             * Method for update values for form fields inside the widget
             *
             * @example
             * widget.updateFormValues({
             *       email: 'predefined@email.com',
             *       card_name: 'Houston'
             *   });
             *
             * @param {IFormValues} fieldValues - Fields with values
             */
        }, {
            key: "updateFormValues",
            value: function updateFormValues(fieldValues) {
                for (var key in fieldValues) {
                    if (fieldValues.hasOwnProperty(key)) this.updateFormValue(key, fieldValues[key]);
                }
            }
        }, {
            key: "updateFormValue",
            value: function updateFormValue(key, value) {
                if (ObjectHelper.values(FORM_FIELD).indexOf(key) === -1) return console.warn("Widget::setFormValues[s: unsupported field ".concat(key));
                if (/\,/.test(value) || /\:/.test(value)) return console.warn("Widget::setFormValues[s: ".concat(key, " - unsupported symbols (: or ,) in value"));
                this.trigger(TRIGGER.UPDATE_FORM_VALUES, {
                    form_values: "".concat(key, ":").concat(value)
                });
            }
            /**
             * After finish event of widget, data (dataType) will be insert to input (selector)
             *
             * @param {string} selector - css selector . [] #
             * @param {string} dataType - data type of IEventData object.
             */
        }, {
            key: "onFinishInsert",
            value: function onFinishInsert(selector, dataType) {
                this.on(EVENT.FINISH, function (event) {
                    Event.insertToInput(selector, dataType, event);
                });
            }
            /**
             * Widget will intercept submit of your form for processing widget
             *
             * Process: click by submit button in your form --> submit widget ---> submit your form
             * @note  submit button in widget will be hidden.
             *
             * @param {string} selector - css selector of your form
             *
             * @example
             *  <form id="myForm">
             *      <input name="amount">
             *      <button type="submit">Submit</button>
             *  </form>
             * <!--
             * -->
             * <script>
             *     widget.interceptSubmitForm('#myForm');
             * </script>
             */
        }, {
            key: "interceptSubmitForm",
            value: function interceptSubmitForm(selector) {
                var _this5 = this;
                this.setHiddenElements([ELEMENT.SUBMIT_BUTTON]);
                var formInterceptor = new FormInterceptor(selector);
                formInterceptor.beforeSubmit(function () {
                    _this5.triggerElement.push(TRIGGER.SUBMIT_FORM, {});
                    _this5.event.on(EVENT.FINISH, _this5.link.getParams().widget_id, function () {
                        formInterceptor.continueSubmit();
                    });
                });
            }
            /**
             * This method hides a submit button and automatically execute form submit
             */
        }, {
            key: "useCheckoutAutoSubmit",
            value: function useCheckoutAutoSubmit() {
                var _this6 = this;
                this.setHiddenElements([ELEMENT.SUBMIT_BUTTON]);
                this.on(EVENT.CHECKOUT_SUCCESS, function (data) {
                    _this6.trigger(TRIGGER.SUBMIT_FORM);
                });
                this.on(EVENT.VALIDATION_ERROR, function (data) {
                    _this6.trigger(TRIGGER.REFRESH_CHECKOUT);
                });
                this.on(EVENT.SYSTEM_ERROR, function (data) {
                    _this6.trigger(TRIGGER.REFRESH_CHECKOUT);
                });
            }
            /**
             * Use this method for resize iFrame according content height
             *
             * @example
             * widget.useAutoResize();
             *
             */
        }, {
            key: "useAutoResize",
            value: function useAutoResize() {
                var _this7 = this;
                this.on(EVENT.RESIZE, function (data) {
                    if (_this7.iFrame.getElement()) {
                        _this7.iFrame.getElement().scrolling = 'no';
                        if (data.height) _this7.iFrame.setStyle('height', data.height + 'px');
                    }
                });
            }
        }]);
        return HtmlMultiWidget;
    }(MultiWidget);

    /**
     * Class Widget include method for working on html and include extended by HtmlMultiWidget methods
     * @constructor
     * @extends HtmlMultiWidget
     * @extends MultiWidget
     *
     * @example
     * var widget = new HtmlWidget('#widget', 'publicKey', 'gatewayID'); // short
     *
     * var widget = new HtmlWidget('#widget', 'publicKey', 'gatewayID', 'bank_account', 'payment_source'); // extend
     *
     * var widget = new HtmlWidget('#widget', 'publicKey', 'not_configured'); // without gateway
     *
     * @param {string} selector - Selector of html element. Container for widget
     * @param {string} publicKey - PayDock users public key
     * @param {string} [gatewayID=default] - ID of a gateway connected to PayDock. By default or if put 'default', it will use the selected default gateway. If put 'not_configured', it wonвЂ™t use gateway to create downstream token.
     * @param {string} [paymentType=card] - Type of payment source which shows in widget form. Available parameters : вЂњcardвЂќ, вЂњbank_accountвЂќ.
     * @param {string} [purpose=payment_source] - Purpose of widget form. Available parameters: вЂpayment_sourceвЂ™, вЂcard_payment_source_with_cvvвЂ™, вЂcard_payment_source_without_cvvвЂ™
     **/
    var HtmlWidget = /*#__PURE__*/function (_HtmlMultiWidget) {
        _inherits(HtmlWidget, _HtmlMultiWidget);
        var _super = _createSuper(HtmlWidget);
        function HtmlWidget(selector, publicKey) {
            var gatewayID = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'default';
            var paymentType = arguments.length > 3 ? arguments[3] : undefined;
            var purpose = arguments.length > 4 ? arguments[4] : undefined;
            _classCallCheck(this, HtmlWidget);
            var conf = new Configuration(gatewayID, paymentType, purpose);
            return _super.call(this, selector, publicKey, conf);
        }
        /**
         * Destination, where customer will receive all successful responses.
         * Response will contain вЂњdataвЂќ object with вЂњpayment_sourceвЂќ or other parameters, in depending on 'purpose'
         *
         * @example
         * widget.setWebHookDestination('http://google.com');
         *
         * @param {string} url - Your endpoint for post request.
         */
        _createClass(HtmlWidget, [{
            key: "setWebHookDestination",
            value: function setWebHookDestination(url) {
                this.configs[0].setWebHookDestination(url);
            }
            /**
             * URL to which the Customer will be redirected to after the success finish
             *
             * @example
             * widget.setSuccessRedirectUrl('google.com/search?q=success');
             *
             * @param {string}  url
             */
        }, {
            key: "setSuccessRedirectUrl",
            value: function setSuccessRedirectUrl(url) {
                this.configs[0].setSuccessRedirectUrl(url);
            }
            /**
             * URL to which the Customer will be redirected to if an error is triggered in the process of operation
             *
             * @example
             * widget.setErrorRedirectUrl('google.com/search?q=error');
             *
             * @param {string} url
             */
        }, {
            key: "setErrorRedirectUrl",
            value: function setErrorRedirectUrl(url) {
                this.configs[0].setErrorRedirectUrl(url);
            }
            /**
             *  Set list with widget form field, which will be shown in form. Also you can set the required validation for these fields
             *
             * @example
             * widget.setFormFields(['phone', 'email', 'first_name*']);
             *
             * @param {string[]} fields - name of fields which can be shown in a widget.
             * If after a name of a field, you put вЂњ*вЂќ, this field will be required on client-side. (For validation, you can specify any fields, even those that are shown by default: card_number, expiration, etc... ) [FORM_FIELD]{@link FORM_FIELD}
             */
        }, {
            key: "setFormFields",
            value: function setFormFields(fields) {
                this.configs[0].setFormFields(fields);
            }
            /**
             * The method to set the full configuration for the all specific form elements (visibility, required, label, placeholder, value)
             * You can also use the other method for the partial configuration like: setFormFields, setFormValues, setFormPlaceholder, setFormLabel
             *
             * @example
             *   widget.setFormElements([
             *       {
             *           field:  'card_name*',
             *           placeholder: 'Input your card holder name...',
             *           label: 'Card Holder Name',
             *           value: 'Houston',
             *       },
             *       {
             *           field:  'email',
             *           placeholder: 'Input your email, like test@example.com',
             *           label: 'Email for the receipt',
             *           value: 'predefined@email.com',
             *       },
             *   ])
             *
             * @param { Object[] } elements - List of elements
             * @param { string } elements[].field - Field name of element. If after a name of a field, you put вЂњ*вЂќ, this field will be required on client-side. (For validation, you can specify any fields, even those that are shown by default: card_number, expiration, etc... ) [FORM_FIELD]{@link FORM_FIELD}
             * @param { string } elements[].placeholder - Set custom placeholders in form fields
             * @param { string } elements[].label - Set a custom labels near the form field
             * @param { string } elements[].value - Set predefined values for the form field
             */
        }, {
            key: "setFormElements",
            value: function setFormElements(elements) {
                var _this = this;
                elements.forEach(function (element) {
                    return _this.setFormElement(element);
                });
            }
        }, {
            key: "setFormElement",
            value: function setFormElement(element) {
                this.configs[0].setFormFields([element.field]);
                _get(_getPrototypeOf(HtmlWidget.prototype), "setFormElement", this).call(this, _extends(_extends({}, element), {
                    field: element.field.replace('*', '')
                }));
            }
            /**
             * The method to set meta information for the checkout page
             *
             * @example
             * config.setMeta({
             brand_name: 'paydock',
             reference: '15',
             email: 'wault@paydock.com'
             });
             *
             * @param {IPayPalMeta | IBamboraMeta} object - data which can be shown on checkout page [IPayPalMeta]{@link IPayPalMeta} [IBamboraMeta]{@link IBamboraMeta}
             */
        }, {
            key: "setMeta",
            value: function setMeta(meta) {
                this.configs[0].setMeta(meta);
            }
        }, {
            key: "setGiftCardScheme",
            value: function setGiftCardScheme(giftCardScheme, processingNetwork) {
                this.configs[0].setGiftCardSchemeData(giftCardScheme, processingNetwork);
            }
        }]);
        return HtmlWidget;
    }(HtmlMultiWidget);

    /**
     * @type {object}
     * @param {string} CLICK=click
     * @param {string} POPUP_REDIRECT=popup_redirect
     * @param {string} ERROR=error
     * @param {string} ACCEPTED=accepted
     * @param {string} FINISH=finish
     * @param {string} CLOSE=close
     */
    var CHECKOUT_BUTTON_EVENT = {
        CLICK: 'click',
        POPUP_REDIRECT: 'popupRedirect',
        REDIRECT: 'redirect',
        ERROR: 'error',
        REFERRED: 'referred',
        DECLINED: 'declined',
        CANCELLED: 'cancelled',
        ACCEPTED: 'accepted',
        FINISH: 'finish',
        CLOSE: 'close'
    };
    /**
     * @type {object}
     * @param {string} CONTEXTUAL=contextual
     * @param {string} REDIRECT=redirect
     */
    var CHECKOUT_MODE;
    (function (CHECKOUT_MODE) {
        CHECKOUT_MODE["CONTEXTUAL"] = "contextual";
        CHECKOUT_MODE["REDIRECT"] = "redirect";
    })(CHECKOUT_MODE || (CHECKOUT_MODE = {}));
    /**
     * @type {object}
     * @param {string} ZIPMONEY=Zipmoney
     * @param {string} PAYPAL=PaypalClassic
     * @param {string} AFTERPAY=Afterpay
     */
    var GATEWAY_TYPE;
    (function (GATEWAY_TYPE) {
        GATEWAY_TYPE["ZIPMONEY"] = "Zipmoney";
        GATEWAY_TYPE["PAYPAL"] = "PaypalClassic";
        GATEWAY_TYPE["AFTERPAY"] = "Afterpay";
    })(GATEWAY_TYPE || (GATEWAY_TYPE = {}));
    var CHECKOUT_BTN_LOG_PREFIX = '[Paydock:CheckoutButton]';

    var HttpCore = /*#__PURE__*/function () {
        function HttpCore() {
            _classCallCheck(this, HttpCore);
            this.env = new Env(API_URL);
        }
        _createClass(HttpCore, [{
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.env.setEnv(env, alias);
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                return this.env.getEnv();
            }
        }, {
            key: "getUrl",
            value: function getUrl() {
                return this.env.getConf().url + this.getLink();
            }
        }, {
            key: "create",
            value: function create(accessToken, data, cb, errorCb) {
                var _this = this;
                var request = new XMLHttpRequest();
                request.onload = function () {
                    _this.parser(request.responseText, request.status, cb, errorCb);
                };
                request.open('POST', this.getUrl(), true);
                request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                request.setRequestHeader(AccessToken.getAccessHeaderNameByToken(accessToken), accessToken);
                if (SDK.version) {
                    request.setRequestHeader(SDK.headerKeys.version, SDK.version);
                    request.setRequestHeader(SDK.headerKeys.type, SDK.type);
                }
                request.send(JSON.stringify(data));
            }
        }, {
            key: "get",
            value: function get(accessToken, cb, errorCb) {
                var _this2 = this;
                var request = new XMLHttpRequest();
                request.onload = function () {
                    _this2.parser(request.responseText, request.status, cb, errorCb);
                };
                request.open('GET', this.getUrl(), true);
                request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                request.setRequestHeader(AccessToken.getAccessHeaderNameByToken(accessToken), accessToken);
                if (SDK.version) {
                    request.setRequestHeader(SDK.headerKeys.version, SDK.version);
                    request.setRequestHeader(SDK.headerKeys.type, SDK.type);
                }
                request.send();
            }
        }, {
            key: "parser",
            value: function parser(text, status, cb, errorCb) {
                var res = {};
                try {
                    res = JSON.parse(text);
                } catch (e) {}
                if (status >= 200 && status < 300 || status === 302) return cb(res.resource.data, status);else errorCb(res.error || {
                    message: 'unknown error'
                }, status);
            }
        }]);
        return HttpCore;
    }();

    var EXTERNAL_CHECKOUT_LINK = '/v1/payment_sources/external_checkout';
    var Builder = /*#__PURE__*/function (_HttpCore) {
        _inherits(Builder, _HttpCore);
        var _super = _createSuper(Builder);
        function Builder(gatewayID, successRedirectUrl, errorRedirectUrl) {
            var _this;
            _classCallCheck(this, Builder);
            _this = _super.call(this);
            _this.body = {
                gateway_id: gatewayID,
                meta: {},
                success_redirect_url: successRedirectUrl,
                error_redirect_url: errorRedirectUrl,
                redirect_url: successRedirectUrl
            };
            return _this;
        }
        _createClass(Builder, [{
            key: "getLink",
            value: function getLink() {
                return EXTERNAL_CHECKOUT_LINK;
            }
        }, {
            key: "setDescriptions",
            value: function setDescriptions(text) {
                this.body.description = text;
            }
        }, {
            key: "setMeta",
            value: function setMeta(meta) {
                for (var key in meta) {
                    if (!meta.hasOwnProperty(key)) continue;
                    if (SUPPORTED_CHECKOUT_META_COLLECTION.indexOf(key) !== -1) this.body.meta[key] = meta[key];else console.warn("ExternalCheckout::setMeta: unsupported meta key ".concat(key));
                }
            }
        }, {
            key: "getConfigs",
            value: function getConfigs() {
                return this.body;
            }
        }, {
            key: "send",
            value: function send(publicKey, cb) {
                var errorCb = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function (err, code) {};
                this.create(publicKey, this.getConfigs(), function (data, status) {
                    cb(data);
                }, function (err, status) {
                    if (typeof err.message === 'undefined') errorCb("".concat(status, ": unknown error"), 'unknown_error');else errorCb(err.message, err.code);
                });
            }
        }]);
        return Builder;
    }(HttpCore);

    var LINK = '/v1/payment_sources/external_checkout/:token';
    var Checker = /*#__PURE__*/function (_HttpCore) {
        _inherits(Checker, _HttpCore);
        var _super = _createSuper(Checker);
        function Checker(token) {
            var _this;
            _classCallCheck(this, Checker);
            _this = _super.call(this);
            _this.token = token;
            return _this;
        }
        _createClass(Checker, [{
            key: "getLink",
            value: function getLink() {
                return LINK.replace(':token', this.token);
            }
        }, {
            key: "send",
            value: function send(accessToken, cb) {
                var errorCb = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function (err) {};
                this.get(accessToken, function (data, status) {
                    cb(data);
                }, function (err, status) {
                    if (typeof err.message === 'undefined') errorCb("".concat(status, ": unknown error"));else errorCb(err.message);
                });
            }
        }]);
        return Checker;
    }(HttpCore);

    var EventEmitter = /*#__PURE__*/function () {
        function EventEmitter() {
            _classCallCheck(this, EventEmitter);
            this.events = {};
        }
        _createClass(EventEmitter, [{
            key: "emit",
            value: function emit(eventName, data) {
                var event = this.events[eventName];
                if (event) {
                    event.forEach(function (fn) {
                        fn.call(null, data);
                    });
                }
            }
        }, {
            key: "subscribe",
            value: function subscribe(eventName, handler) {
                var _this = this;
                if (!this.events[eventName]) {
                    this.events[eventName] = [];
                }
                this.events[eventName].push(handler);
                return function () {
                    _this.events[eventName] = _this.events[eventName].filter(function (storedHandler) {
                        return handler !== storedHandler;
                    });
                };
            }
        }]);
        return EventEmitter;
    }();

    var TEMPLATE = "\n    <div class=\"cs-loader\">\n      <div class=\"cs-loader-inner\">\n        <label>\t\u25CF</label>\n        <label>\t\u25CF</label>\n        <label>\t\u25CF</label>\n        <label>\t\u25CF</label>\n        <label>\t\u25CF</label>\n        <label>\t\u25CF</label>\n      </div>\n    </div>\n";
    var STYLE$1 = "\n    <style> \n        .cs-loader {\n          position: absolute;\n          top: 0;\n          left: 0;\n          height: 100%;\n          width: 100%;\n        }\n        \n        .cs-loader-inner {\n          transform: translateY(-50%);\n          top: 50%;\n          position: absolute;\n          width: calc(100% - 200px);\n          color: #8e8d8c;\n          padding: 0 100px;\n          text-align: center;\n        }\n        \n        \n        .cs-loader-inner label {\n          font-size: 20px;\n          opacity: 0;\n          display:inline-block;\n        }\n        \n        @keyframes lol {\n          0% {\n            opacity: 0;\n            transform: translateX(-300px);\n          }\n          33% {\n            opacity: 1;\n            transform: translateX(0px);\n          }\n          66% {\n            opacity: 1;\n            transform: translateX(0px);\n          }\n          100% {\n            opacity: 0;\n            transform: translateX(300px);\n          }\n        }\n        \n        @-webkit-keyframes lol {\n          0% {\n            opacity: 0;\n            -webkit-transform: translateX(-300px);\n          }\n          33% {\n            opacity: 1;\n            -webkit-transform: translateX(0px);\n          }\n          66% {\n            opacity: 1;\n            -webkit-transform: translateX(0px);\n          }\n          100% {\n            opacity: 0;\n            -webkit-transform: translateX(300px);\n            -moz-transform: translateX(300px);\n          }\n        }\n        \n        .cs-loader-inner label:nth-child(6) {\n          -webkit-animation: lol 3s infinite ease-in-out;\n          -moz-animation: lol 3s infinite ease-in-out;\n          animation: lol 3s infinite ease-in-out;\n        }\n        \n        .cs-loader-inner label:nth-child(5) {\n          -webkit-animation: lol 3s 100ms infinite ease-in-out;\n          -moz-animation: lol 3s 100ms infinite ease-in-out;\n          animation: lol 3s 100ms infinite ease-in-out;\n        }\n        \n        .cs-loader-inner label:nth-child(4) {\n          -webkit-animation: lol 3s 200ms infinite ease-in-out;\n          -moz-animation: lol 3s 200ms infinite ease-in-out;\n          animation: lol 3s 200ms infinite ease-in-out;\n        }\n        \n        .cs-loader-inner label:nth-child(3) {\n          -webkit-animation: lol 3s 300ms infinite ease-in-out;\n          -moz-animation: lol 3s 300ms infinite ease-in-out;\n          animation: lol 3s 300ms infinite ease-in-out;\n        }\n        \n        .cs-loader-inner label:nth-child(2) {\n          -webkit-animation: lol 3s 400ms infinite ease-in-out;\n          -moz-animation: lol 3s 400ms infinite ease-in-out;\n          animation: lol 3s 400ms infinite ease-in-out;\n        }\n        \n        .cs-loader-inner label:nth-child(1) {\n          -webkit-animation: lol 3s 500ms infinite ease-in-out;\n          -moz-animation: lol 3s 500ms infinite ease-in-out;\n          animation: lol 3s 500ms infinite ease-in-out;\n        }\n    </style>\n";
    var EXTRA_TEMPLATE = "\n    <div class=\"circ\">\n      <div class=\"load\">A little patience ...</div>\n      <div class=\"hands\"></div>\n      <div class=\"body\"></div>\n      <div class=\"head\">\n        <div class=\"eye\"></div>\n      </div>\n    </div>\n";
    var EXTRA_STYLE = "\n    <style>\n        html{width: 100%;height: 100%;}\n        body{margin: 0px;padding: 0px;background-color: #111;}\n        \n        .eye{\n          width: 20px; height: 8px;\n          background-color: #eee;\n          border-radius:0px 0px 20px 20px;\n          position: relative;\n          top: 40px;\n          left: 10px;\n          box-shadow:  40px 0px 0px 0px #eee;              \n        }\n        \n        .head{\n          -webkit-backface-visibility: hidden;\n          -moz-backface-visibility: hidden;\n          backface-visibility: hidden;          \n          position: relative;\n          margin: -250px auto;\n          width: 80px; height: 80px;\n          background-color: #111;\n          border-radius:50px;\n          box-shadow: inset -4px 2px 0px 0px #eee;\n           -webkit-animation:node 1.5s infinite alternate;\n          -webkit-animation-timing-function:ease-out;\n          -moz-animation:node 1.5s infinite alternate;\n          -moz-animation-timing-function:ease-out;\n          animation:node 1.5s infinite alternate;\n          animation-timing-function:ease-out;\n        }\n        .body{ \n          position: relative;\n          margin: 90px auto;\n          width: 140px; height: 120px;\n          background-color: #111;\n          border-radius: 50px/25px ;\n          box-shadow: inset -5px 2px 0px 0px #eee;\n          -webkit-animation:node2 1.5s infinite alternate;\n          -webkit-animation-timing-function:ease-out;  \n          -moz-animation:node2 1.5s infinite alternate;\n          -moz-animation-timing-function:ease-out;  \n          animation:node2 1.5s infinite alternate;\n          animation-timing-function:ease-out; \n        }\n        \n        @keyframes node {0%{ top:0px; }50%{ top:10px; }100% { top:0px;} }\n        @keyframes node2 {0%{ top:-5px; }50%{ top:10px; }100% { top:-5px;}}\n        @-moz-keyframes node {0%{ top:0px; }50%{ top:10px; }100% { top:0px;} }\n        @-moz-keyframes node2 {0%{ top:-5px; }50%{ top:10px; }100% { top:-5px;}}\n        @-webkit-keyframes node {0%{ top:0px; }50%{ top:10px; }100% { top:0px;} }\n        @-webkit-keyframes node2 {0%{ top:-5px; }50%{ top:10px; }100% { top:-5px;}}\n      \n               \n        .circ{\n          -webkit-backface-visibility: hidden;\n          -moz-backface-visibility: hidden;\n          backface-visibility: hidden;\n           margin: 60px auto;\n          width: 180px; height: 180px;\n          background-color: #111;\n          border-radius: 0px 0px 50px 50px;\n          position: relative;\n          z-index: -1;  \n          left: 0%;\n          top: 20%;\n          overflow: hidden;\n        }\n        \n        .hands{\n          -webkit-backface-visibility: hidden;\n          -moz-backface-visibility: hidden;\n          backface-visibility: hidden;\n          margin-top: 140px;\n          width: 120px;height: 120px;\n          position: absolute;\n          background-color: #111;\n          border-radius:20px;\n          box-shadow:-1px -4px 0px 0px #eee;\n          transform:rotate(45deg);\n          -webkit-transform:rotate(45deg);\n          -mox-transform:rotate(45deg);\n          top:75%;left: 16%;\n          z-index: 1;\n          -webkit-animation:node2 1.5s infinite alternate;\n          -webkit-animation-timing-function:ease-out;\n          -moz-animation:node2 1.5s infinite alternate;\n          -moz-animation-timing-function:ease-out;\n          animation:node2 1.5s infinite alternate;\n          animation-timing-function:ease-out;\n        }\n        \n        .load{  position: absolute;\n          width: 100px; height: 20px;\n           margin: -10px auto;\n           -webkit-font-smoothing: antialiased;\n          -moz-font-smoothing: antialiased;\n          font-smoothing: antialiased;\n          font-family: 'Julius Sans One', sans-serif;\n          font-size:30px;\n          font-weight:400;\n          color:#eee;\n          left: 10%;\n          top: 5%;\n        }\n    </style>\n";

    var Browser = /*#__PURE__*/function () {
        function Browser() {
            _classCallCheck(this, Browser);
        }
        _createClass(Browser, null, [{
            key: "isFacebook",
            value: function isFacebook() {
                return navigator.userAgent.indexOf('FBSN/iOS') !== -1 && navigator.userAgent.indexOf('AppleWebKit') !== -1 && navigator.userAgent.indexOf('(KHTML, like Gecko)') !== -1;
            }
        }, {
            key: "isInstagram",
            value: function isInstagram() {
                return navigator.userAgent.indexOf('iOS') !== -1 && navigator.userAgent.indexOf('Instagram') !== -1 && navigator.userAgent.indexOf('(KHTML, like Gecko)') !== -1;
            }
        }, {
            key: "isSupportPopUp",
            value: function isSupportPopUp() {
                return !this.isFacebook() && !this.isInstagram();
            }
        }, {
            key: "getLanguage",
            value: function getLanguage() {
                return window.navigator.language || '';
            }
        }, {
            key: "getTimezoneOffset",
            value: function getTimezoneOffset() {
                return new Date().getTimezoneOffset();
            }
        }, {
            key: "getBrowserName",
            value: function getBrowserName() {
                var sUsrAg = navigator.userAgent;
                // "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:61.0) Gecko/20100101 Firefox/61.0"
                if (sUsrAg.indexOf('Firefox') > -1) return 'Mozilla Firefox';
                if (sUsrAg.indexOf('Opera') > -1) return 'Opera';
                // "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; Zoom 3.6.0; wbx 1.0.0; rv:11.0) like Gecko"
                if (sUsrAg.indexOf('Trident') > -1) return 'Microsoft Internet Explorer';
                // "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299"
                if (sUsrAg.indexOf('Edge') > -1) return 'Microsoft Edge';
                // "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/66.0.3359.181 Chrome/66.0.3359.181 Safari/537.36"
                if (sUsrAg.indexOf('Chrome') > -1) return 'Google Chrome or Chromium';
                // "Mozilla/5.0 (iPhone; CPU iPhone OS 11_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.0 Mobile/15E148 Safari/604.1 980x1306"
                if (sUsrAg.indexOf('Safari') > -1) return 'Apple Safari';
                return 'unknown';
            }
        }, {
            key: "isJavaEnabled",
            value: function isJavaEnabled() {
                return navigator.javaEnabled();
            }
        }, {
            key: "getColorDepth",
            value: function getColorDepth() {
                return screen.colorDepth;
            }
        }, {
            key: "getScreenHeight",
            value: function getScreenHeight() {
                return screen.height;
            }
        }, {
            key: "getScreenWidth",
            value: function getScreenWidth() {
                return screen.width;
            }
        }, {
            key: "getBrowserInfo",
            value: function getBrowserInfo() {
                var ua = navigator.userAgent;
                var M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
                var tem;
                if (/trident/i.test(M[1])) {
                    tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
                    return {
                        name: 'IE',
                        version: tem[1] || ''
                    };
                }
                if (M[1] === 'Chrome') {
                    tem = ua.match(/\bOPR|Edge\/(\d+)/);
                    if (tem != null) {
                        return {
                            name: 'Opera',
                            version: tem[1]
                        };
                    }
                }
                M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
                // tslint:disable-next-line:no-conditional-assignment
                if ((tem = ua.match(/version\/(\d+)/i)) != null) {
                    M.splice(1, 1, tem[1]);
                }
                return {
                    name: M[0],
                    version: M[1]
                };
            }
        }]);
        return Browser;
    }();

    /**
     *
     * @type {object}
     * @param {string} CLOSE=close
     * @param {string} FOCUS=focus
     */
    var TRIGGER$1 = {
        CLOSE: 'close',
        FOCUS: 'focus'
    };
    var TEMPLATE$1 = "\n    <div class=\"checkout-container\">\n        <strong class=\"checkout-title\" data-title>{{title}}</strong>\n        <p data-description>{{description}}</p>\n        <a href=\"#\" data-continue>Continue</a>\n        <a href=\"#\" data-close>Close</a>\n    </div>\n";
    var STYLES = "\n    .hide-continue-button [data-continue] {\n        display: none;\n    }\n    \n    .checkout-overlay .cs-loader-inner {\n         color: #ddd;\n    }\n\n    .checkout-overlay { \n        position: fixed;\n        top: 0;\n        left: 0;\n        right: 0;\n        bottom: 0;\n        background: rgba(0,0,0, 0.5);\n        text-align: center;\n        color: #fff;\n        opacity: 0;\n    }\n    .checkout-overlay.display { \n        opacity: 1;\n        transition: opacity 0.7s ease-out;\n    }\n    .checkout-overlay a { color: #00f; }\n    .checkout-container {\n        position: absolute;\n        top: 50%;\n        left: 0;\n        width: 100%;\n        margin-top: -{{width}}px;\n    }\n    .checkout-title {\n        font-size: 24px;\n        display: block;\n        text-transform: uppercase;\n    }\n    [data-close] {\n        position: fixed;\n        right: 32px;\n        top: 32px;\n        width: 32px;\n        height: 32px;\n        opacity: 0.3;\n        overflow: hidden;\n        text-indent: -9999px;\n    }\n    [data-close]:hover { opacity: 1; }\n    [data-close]:before, [data-close]:after {\n        position: absolute;\n        left: 15px;\n        content: ' ';\n        height: 33px;\n        width: 2px;\n        background-color: #00f;\n    }\n    [data-close]:before { transform: rotate(45deg); }\n    [data-close]:after { transform: rotate(-45deg); }\n";
    /**
     * Class Background create overlay for checkout
     *
     * @example
     * var overlay = new Background();
     **/
    var Background = /*#__PURE__*/function () {
        function Background() {
            _classCallCheck(this, Background);
            this.description = "Don't see the secure checkout browser? We'll help you re-launch the window to complete your purchase";
            this.title = 'Checkout';
            this.overlay = null;
            this.style = null;
            this.showControl = true;
            this.showLoader = true;
            this.eventEmitter = new EventEmitter();
        }
        _createClass(Background, [{
            key: "initControl",
            value: function initControl() {
                if (this.isInit() || !this.showControl) return;
                if (!Browser.isSupportPopUp()) return this.createLoader();
                this.createTemplate();
                this.createStyles();
                this.eventHandler();
            }
        }, {
            key: "initLoader",
            value: function initLoader() {
                if (this.isInit() || !this.showLoader) return;
                this.createStyles();
                this.createLoader();
            }
        }, {
            key: "eventHandler",
            value: function eventHandler() {
                var _this = this;
                var closeButton = document.querySelector('[data-close]');
                var focusButton = document.querySelector('[data-continue]');
                if (closeButton) Event.subscribe('click', closeButton, function () {
                    return _this.eventEmitter.emit(TRIGGER$1.CLOSE, {});
                });
                if (focusButton) Event.subscribe('click', focusButton, function () {
                    return _this.eventEmitter.emit(TRIGGER$1.FOCUS, {});
                });
            }
        }, {
            key: "clear",
            value: function clear() {
                if (!this.style && !this.overlay) return;
                this.style.parentNode.removeChild(this.style);
                this.overlay.parentNode.removeChild(this.overlay);
                this.style = null;
                this.overlay = null;
            }
        }, {
            key: "createLoader",
            value: function createLoader() {
                var _this2 = this;
                var body = document.body || document.getElementsByTagName('body')[0];
                this.overlay = document.createElement('div');
                this.overlay.classList.add('checkout-overlay');
                this.overlay.setAttribute('checkout-overlay', ' ');
                this.overlay.innerHTML = STYLE$1 + TEMPLATE;
                body.appendChild(this.overlay);
                setTimeout(function () {
                    if (_this2.isInit()) _this2.overlay.classList.add('display');
                }, 5);
            }
        }, {
            key: "createTemplate",
            value: function createTemplate() {
                var _this3 = this;
                var body = document.body || document.getElementsByTagName('body')[0];
                var template = String(TEMPLATE$1);
                template = template.replace('{{description}}', this.description);
                template = template.replace('{{title}}', this.title);
                this.overlay = document.createElement('div');
                this.overlay.classList.add('checkout-overlay');
                this.overlay.setAttribute('checkout-overlay', ' ');
                this.overlay.innerHTML = template;
                body.appendChild(this.overlay);
                setTimeout(function () {
                    if (_this3.isInit()) _this3.overlay.classList.add('display');
                }, 5);
            }
        }, {
            key: "createStyles",
            value: function createStyles() {
                var head = document.head || document.getElementsByTagName('head')[0];
                var css = String(STYLES);
                var container = document.querySelector('.checkout-container');
                css = css.replace('{{width}}', container ? String(container.offsetHeight / 2) : '0');
                this.style = document.createElement('style');
                this.style.type = 'text/css';
                this.style.appendChild(document.createTextNode(css));
                head.appendChild(this.style);
            }
        }, {
            key: "setBackdropDescription",
            value: function setBackdropDescription(text) {
                this.description = text;
            }
        }, {
            key: "setBackdropTitle",
            value: function setBackdropTitle(text) {
                this.title = text;
            }
        }, {
            key: "onTrigger",
            value: function onTrigger(triggerName, cb) {
                this.eventEmitter.subscribe(triggerName, cb);
            }
        }, {
            key: "isInit",
            value: function isInit() {
                return !!(this.overlay && this.style);
            }
        }, {
            key: "hideContinueControl",
            value: function hideContinueControl() {
                if (!this.isInit()) return;
                this.overlay.classList.add('hide-continue-button');
            }
        }, {
            key: "turnOffControl",
            value: function turnOffControl() {
                this.showControl = false;
            }
        }, {
            key: "turnOffLoader",
            value: function turnOffLoader() {
                this.showLoader = false;
            }
        }]);
        return Background;
    }();

    var BaseRunner = /*#__PURE__*/function () {
        function BaseRunner() {
            _classCallCheck(this, BaseRunner);
            this.widgetEnv = new Env(WIDGET_URL);
        }
        _createClass(BaseRunner, [{
            key: "error",
            value: function error(_error, code, callback) {
                callback(true);
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.widgetEnv.setEnv(env, alias);
            }
        }]);
        return BaseRunner;
    }();

    var RUNNER_EVENT;
    (function (RUNNER_EVENT) {
        RUNNER_EVENT["SUCCESS"] = "success";
        RUNNER_EVENT["DECLINED"] = "declined";
        RUNNER_EVENT["CLOSE"] = "close";
        RUNNER_EVENT["REFERRED"] = "referred";
        RUNNER_EVENT["ERROR"] = "error";
    })(RUNNER_EVENT || (RUNNER_EVENT = {}));
    function ContextualRunner() {
        var Runner = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : BaseRunner;
        return /*#__PURE__*/function (_Runner) {
            _inherits(_class, _Runner);
            var _super = _createSuper(_class);
            function _class() {
                var _this;
                _classCallCheck(this, _class);
                for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                    args[_key] = arguments[_key];
                }
                _this = _super.call.apply(_super, [this].concat(args));
                _this.background = new Background();
                _this.background.onTrigger(TRIGGER$1.FOCUS, function () {
                    return _this["continue"]();
                });
                _this.background.onTrigger(TRIGGER$1.CLOSE, function () {
                    return _this.stop();
                });
                return _this;
            }
            _createClass(_class, [{
                key: "continue",
                value: function _continue() {}
            }, {
                key: "stop",
                value: function stop() {}
            }, {
                key: "error",
                value: function error(_error, code, callback) {
                    callback(true);
                }
            }, {
                key: "setSuspendedRedirectUri",
                value: function setSuspendedRedirectUri(uri) {
                    this.suspendedRedirectUri = uri;
                }
            }, {
                key: "setBackgroundTitle",
                value: function setBackgroundTitle(text) {
                    this.background.setBackdropTitle(text);
                }
            }, {
                key: "setBackgroundDescription",
                value: function setBackgroundDescription(text) {
                    this.background.setBackdropDescription(text);
                }
            }, {
                key: "turnOffBackdrop",
                value: function turnOffBackdrop() {
                    this.background.turnOffControl();
                    this.background.turnOffLoader();
                }
            }]);
            return _class;
        }(Runner);
    }

    var DISPATCHER_LINK = '/dispatcher';
    var DISPATCHER_ELEMENT_ID = 'paydock-dispatcher';
    var Dispatcher = /*#__PURE__*/function () {
        function Dispatcher(messageSource) {
            _classCallCheck(this, Dispatcher);
            this.messageSource = messageSource;
            this.env = new Env(WIDGET_URL);
        }
        _createClass(Dispatcher, [{
            key: "restartDispatcher",
            value: function restartDispatcher() {
                var dispatcher = document.getElementById(DISPATCHER_ELEMENT_ID);
                if (dispatcher && dispatcher.parentNode) dispatcher.parentNode.removeChild(dispatcher);
                var iFrame = document.createElement('iframe');
                iFrame.setAttribute('src', this.env.getConf().url + DISPATCHER_LINK);
                iFrame.id = DISPATCHER_ELEMENT_ID;
                iFrame.style.display = 'none';
                document.body.appendChild(iFrame);
            }
        }, {
            key: "on",
            value: function on(name, cb) {
                var _this = this;
                Event.subscribe('message', window, function (event) {
                    var data = null;
                    try {
                        data = JSON.parse(event.data);
                    } catch (e) {}
                    if (!data || data.message_source !== _this.messageSource || data.event !== name) return;
                    cb(data);
                });
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.env.setEnv(env, alias);
                this.restartDispatcher();
            }
        }]);
        return Dispatcher;
    }();

    var EVENT_CLOSE = 'close';
    var ERROR_STYLES = "<style>\n        .error-wrapper {\n            color: #ff0000;\n            display: flex;\n            justify-content: center;\n            align-items: center;\n            height: 100%;\n        }\n        </style>";
    var ERROR_TEMPLATE = "<div class=\"error-wrapper\"><div>{{error}}</div></div>";
    var Popup = /*#__PURE__*/function () {
        function Popup() {
            _classCallCheck(this, Popup);
            this.configs = {
                width: 500,
                height: 500,
                scrollbars: true,
                resizable: true,
                top: 0,
                left: 0
            };
            this.eventEmitter = new EventEmitter();
        }
        _createClass(Popup, [{
            key: "isExist",
            value: function isExist() {
                return !!(this.getElement() && !this.getElement().closed);
            }
        }, {
            key: "getElement",
            value: function getElement() {
                return this.window;
            }
        }, {
            key: "init",
            value: function init() {
                var _this = this;
                if (!Browser.isSupportPopUp()) return this.window = window;
                var configs = this.getConfigs();
                this.window = window.open('about:blank', '_blank', "noopener=false,width=".concat(configs.width, ",height=").concat(configs.height, ",top=").concat(configs.top, ",left=").concat(configs.left, ",scrollbars=").concat(configs.scrollbars ? 'yes' : 'no', ",resizable=").concat(configs.resizable ? 'yes' : 'no'));
                this.showLoader();
                var timer = setInterval(function () {
                    if (!_this.isExist()) {
                        clearInterval(timer);
                        _this.eventEmitter.emit(EVENT_CLOSE, {});
                    }
                }, 200);
            }
        }, {
            key: "redirect",
            value: function redirect(url) {
                if (!this.isExist()) return;
                this.window.location.href = url;
            }
        }, {
            key: "close",
            value: function close() {
                if (!this.isExist() || !this.getElement().close) return;
                this.getElement().close();
                this.window = null;
            }
        }, {
            key: "focus",
            value: function focus() {
                if (!this.isExist() || !this.getElement().focus) return;
                this.getElement().focus();
            }
        }, {
            key: "setConfigs",
            value: function setConfigs(configs) {
                this.configs = _extends(this.configs, configs);
            }
        }, {
            key: "getNetConfigs",
            value: function getNetConfigs() {
                return _extends({}, this.configs);
            }
        }, {
            key: "getConfigs",
            value: function getConfigs() {
                var configs = this.getNetConfigs();
                configs.left = window.screenX + (window.screen.width / 2 - configs.width / 2);
                configs.top = window.screenY + (window.screen.height / 2 - configs.height / 2);
                return configs;
            }
        }, {
            key: "onClose",
            value: function onClose(cb) {
                this.eventEmitter.subscribe(EVENT_CLOSE, cb);
            }
        }, {
            key: "initError",
            value: function initError(error) {
                this.getElement().document.write(".");
                var body = this.getElement().document.body || this.getElement().document.getElementsByTagName('body')[0];
                body.innerHTML = ERROR_STYLES + ERROR_TEMPLATE.replace('{{error}}', error);
            }
        }, {
            key: "showLoader",
            value: function showLoader() {
                this.getElement().document.write(".");
                var body = this.getElement().document.body || this.getElement().document.getElementsByTagName('body')[0];
                body.innerHTML = STYLE$1 + TEMPLATE;
                if (this.env !== ENV.STAGING || this.env !== ENV.STAGING_1 || this.env !== ENV.STAGING_2 || this.env !== ENV.STAGING_3 || this.env !== ENV.STAGING_4 || this.env !== ENV.STAGING_5 || this.env !== ENV.STAGING_6 || this.env !== ENV.STAGING_7 || this.env !== ENV.STAGING_8 || this.env !== ENV.STAGING_9 || this.env !== ENV.STAGING_10 || this.env !== ENV.STAGING_11 || this.env !== ENV.STAGING_12 || this.env !== ENV.STAGING_13 || this.env !== ENV.STAGING_14 || this.env !== ENV.STAGING_15) return;
                var clickCount = 0;
                Event.subscribe('click', body, function () {
                    clickCount++;
                    if (clickCount !== 5) return;
                    body.innerHTML = EXTRA_STYLE + EXTRA_TEMPLATE;
                });
            }
        }, {
            key: "setEnv",
            value: function setEnv(env) {
                this.env = env;
            }
        }]);
        return Popup;
    }();

    var PopupRunner = /*#__PURE__*/function (_ContextualRunner) {
        _inherits(PopupRunner, _ContextualRunner);
        var _super = _createSuper(PopupRunner);
        function PopupRunner(publicKey) {
            var _this;
            _classCallCheck(this, PopupRunner);
            _this = _super.call(this);
            _this.publicKey = publicKey;
            _this.checkout = null;
            _this.dispatcher = new Dispatcher('checkout.paydock');
            setTimeout(function () {
                return _this.dispatcher.restartDispatcher();
            }, 200);
            _this.popup = new Popup();
            return _this;
        }
        _createClass(PopupRunner, [{
            key: "run",
            value: function run() {
                if (this.isRunning()) return;
                this.popup.init();
                this.background.initControl();
            }
        }, {
            key: "isRunning",
            value: function isRunning() {
                return this.popup.isExist();
            }
        }, {
            key: "next",
            value: function next(checkoutData) {
                this.checkout = checkoutData;
                if (!Browser.isSupportPopUp()) window.localStorage.setItem('paydock_checkout_token', JSON.stringify(this.checkout));
                this.popup.redirect(this.checkout.link);
            }
        }, {
            key: "continue",
            value: function _continue() {
                this.popup.focus();
            }
        }, {
            key: "stop",
            value: function stop() {
                this.popup.close();
            }
        }, {
            key: "onStop",
            value: function onStop(cb) {
                var _this2 = this;
                this.popup.onClose(function () {
                    _this2.background.clear();
                    _this2.checkout = null;
                    cb();
                });
            }
        }, {
            key: "onCheckout",
            value: function onCheckout(event, cb) {
                var _this3 = this;
                this.dispatcher.on(event, function (data) {
                    if (_this3.checkout && _this3.checkout.reference_id === data.reference_id) {
                        _this3.background.clear();
                        cb(_this3.checkout);
                    } else if (!Browser.isSupportPopUp()) {
                        var item = window.localStorage.getItem('paydock_checkout_token');
                        if (!item) return;
                        var checkout = JSON.parse(item);
                        if (checkout && checkout.reference_id === data.reference_id) {
                            window.localStorage.removeItem('paydock_checkout_token');
                            _this3.checkout = checkout;
                            _this3.background.clear();
                            cb(_this3.checkout);
                        }
                    }
                });
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                _get(_getPrototypeOf(PopupRunner.prototype), "setEnv", this).call(this, env, alias);
                this.dispatcher.setEnv(env, alias);
                this.popup.setEnv(env);
            }
        }]);
        return PopupRunner;
    }(ContextualRunner());

    function RedirectRunner() {
        var Runner = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : BaseRunner;
        return /*#__PURE__*/function (_Runner) {
            _inherits(_class, _Runner);
            var _super = _createSuper(_class);
            function _class() {
                _classCallCheck(this, _class);
                return _super.apply(this, arguments);
            }
            _createClass(_class, [{
                key: "setRedirectUrl",
                value: function setRedirectUrl(url) {
                    this.merchantRedirectUrl = url;
                }
            }, {
                key: "getRedirectUrl",
                value: function getRedirectUrl() {
                    return this.merchantRedirectUrl;
                }
            }, {
                key: "error",
                value: function error(_error, code, callback) {
                    callback(false);
                }
            }]);
            return _class;
        }(Runner);
    }

    function isContextualRunner(runner) {
        return 'run' in runner;
    }
    function isRedirectRunner(runner) {
        return 'setRedirectUrl' in runner;
    }

    var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

    function commonjsRequire () {
        throw new Error('Dynamic requires are not currently supported by rollup-plugin-commonjs');
    }

    function unwrapExports (x) {
        return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
    }

    function createCommonjsModule(fn, module) {
        return module = { exports: {} }, fn(module, module.exports), module.exports;
    }

    var zipmoney = createCommonjsModule(function (module) {
        !function e(t, n, r) {
            function o(u, c) {
                if (!n[u]) {
                    if (!t[u]) {
                        var a = "function" == typeof commonjsRequire && commonjsRequire;
                        if (!c && a) return a(u, !0);
                        if (i) return i(u, !0);
                        var s = new Error("Cannot find module '" + u + "'");
                        throw s.code = "MODULE_NOT_FOUND", s;
                    }
                    var f = n[u] = {
                        exports: {}
                    };
                    t[u][0].call(f.exports, function (e) {
                        var n = t[u][1][e];
                        return o(n || e);
                    }, f, f.exports, e, t, n, r);
                }
                return n[u].exports;
            }
            for (var i = "function" == typeof commonjsRequire && commonjsRequire, u = 0; u < r.length; u++) o(r[u]);
            return o;
        }({
            1: [function (e, t, n) {
                function r() {
                    throw new Error("setTimeout has not been defined");
                }
                function o() {
                    throw new Error("clearTimeout has not been defined");
                }
                function i(e) {
                    if (l === setTimeout) return setTimeout(e, 0);
                    if ((l === r || !l) && setTimeout) return l = setTimeout, setTimeout(e, 0);
                    try {
                        return l(e, 0);
                    } catch (t) {
                        try {
                            return l.call(null, e, 0);
                        } catch (t) {
                            return l.call(this, e, 0);
                        }
                    }
                }
                function u(e) {
                    if (p === clearTimeout) return clearTimeout(e);
                    if ((p === o || !p) && clearTimeout) return p = clearTimeout, clearTimeout(e);
                    try {
                        return p(e);
                    } catch (t) {
                        try {
                            return p.call(null, e);
                        } catch (t) {
                            return p.call(this, e);
                        }
                    }
                }
                function c() {
                    y && h && (y = !1, h.length ? v = h.concat(v) : m = -1, v.length && a());
                }
                function a() {
                    if (!y) {
                        var e = i(c);
                        y = !0;
                        for (var t = v.length; t;) {
                            for (h = v, v = []; ++m < t;) h && h[m].run();
                            m = -1, t = v.length;
                        }
                        h = null, y = !1, u(e);
                    }
                }
                function s(e, t) {
                    this.fun = e, this.array = t;
                }
                function f() {}
                var l,
                    p,
                    d = t.exports = {};
                !function () {
                    try {
                        l = "function" == typeof setTimeout ? setTimeout : r;
                    } catch (e) {
                        l = r;
                    }
                    try {
                        p = "function" == typeof clearTimeout ? clearTimeout : o;
                    } catch (e) {
                        p = o;
                    }
                }();
                var h,
                    v = [],
                    y = !1,
                    m = -1;
                d.nextTick = function (e) {
                    var t = new Array(arguments.length - 1);
                    if (arguments.length > 1) for (var n = 1; n < arguments.length; n++) t[n - 1] = arguments[n];
                    v.push(new s(e, t)), 1 !== v.length || y || i(a);
                }, s.prototype.run = function () {
                    this.fun.apply(null, this.array);
                }, d.title = "browser", d.browser = !0, d.env = {}, d.argv = [], d.version = "", d.versions = {}, d.on = f, d.addListener = f, d.once = f, d.off = f, d.removeListener = f, d.removeAllListeners = f, d.emit = f, d.prependListener = f, d.prependOnceListener = f, d.listeners = function (e) {
                    return [];
                }, d.binding = function (e) {
                    throw new Error("process.binding is not supported");
                }, d.cwd = function () {
                    return "/";
                }, d.chdir = function (e) {
                    throw new Error("process.chdir is not supported");
                }, d.umask = function () {
                    return 0;
                };
            }, {}],
            2: [function (e, t, n) {
                (function (e) {
                    !function (e) {

                        if ("function" == typeof bootstrap) bootstrap("promise", e);else if ("object" == _typeof(n) && "object" == _typeof(t)) t.exports = e();else if ("undefined" != typeof ses) {
                            if (!ses.ok()) return;
                            ses.makeQ = e;
                        } else {
                            if ("undefined" == typeof window && "undefined" == typeof self) throw new Error("This environment was not anticipated by Q. Please file a bug.");
                            var r = "undefined" != typeof window ? window : self,
                                o = r.Q;
                            r.Q = e(), r.Q.noConflict = function () {
                                return r.Q = o, this;
                            };
                        }
                    }(function () {

                        function t(e) {
                            return function () {
                                return Z.apply(e, arguments);
                            };
                        }
                        function n(e) {
                            return e === Object(e);
                        }
                        function r(e) {
                            return "[object StopIteration]" === ne(e) || e instanceof F;
                        }
                        function o(e, t) {
                            if (N && t.stack && "object" == _typeof(e) && null !== e && e.stack) {
                                for (var n = [], r = t; r; r = r.source) r.stack && (!e.__minimumStackCounter__ || e.__minimumStackCounter__ > r.stackCounter) && (K(e, "__minimumStackCounter__", {
                                    value: r.stackCounter,
                                    configurable: !0
                                }), n.unshift(r.stack));
                                n.unshift(e.stack);
                                var o = n.join("\n" + re + "\n"),
                                    u = i(o);
                                K(e, "stack", {
                                    value: u,
                                    configurable: !0
                                });
                            }
                        }
                        function i(e) {
                            for (var t = e.split("\n"), n = [], r = 0; r < t.length; ++r) {
                                var o = t[r];
                                a(o) || u(o) || !o || n.push(o);
                            }
                            return n.join("\n");
                        }
                        function u(e) {
                            return -1 !== e.indexOf("(module.js:") || -1 !== e.indexOf("(node.js:");
                        }
                        function c(e) {
                            var t = /at .+ \((.+):(\d+):(?:\d+)\)$/.exec(e);
                            if (t) return [t[1], Number(t[2])];
                            var n = /at ([^ ]+):(\d+):(?:\d+)$/.exec(e);
                            if (n) return [n[1], Number(n[2])];
                            var r = /.*@(.+):(\d+)$/.exec(e);
                            return r ? [r[1], Number(r[2])] : void 0;
                        }
                        function a(e) {
                            var t = c(e);
                            if (!t) return !1;
                            var n = t[0],
                                r = t[1];
                            return n === W && r >= Q && r <= se;
                        }
                        function s() {
                            if (N) try {
                                throw new Error();
                            } catch (r) {
                                var e = r.stack.split("\n"),
                                    t = e[0].indexOf("@") > 0 ? e[1] : e[2],
                                    n = c(t);
                                if (!n) return;
                                return W = n[0], n[1];
                            }
                        }
                        function f(e) {
                            return e instanceof h ? e : g(e) ? O(e) : j(e);
                        }
                        function l() {
                            function e(e) {
                                t = e, f.longStackSupport && N && (i.source = e), $(n, function (t, n) {
                                    f.nextTick(function () {
                                        e.promiseDispatch.apply(e, n);
                                    });
                                }, void 0), n = void 0, r = void 0;
                            }
                            var t,
                                n = [],
                                r = [],
                                o = Y(l.prototype),
                                i = Y(h.prototype);
                            if (i.promiseDispatch = function (e, o, i) {
                                var u = X(arguments);
                                n ? (n.push(u), "when" === o && i[1] && r.push(i[1])) : f.nextTick(function () {
                                    t.promiseDispatch.apply(t, u);
                                });
                            }, i.valueOf = function () {
                                if (n) return i;
                                var e = y(t);
                                return m(e) && (t = e), e;
                            }, i.inspect = function () {
                                return t ? t.inspect() : {
                                    state: "pending"
                                };
                            }, f.longStackSupport && N) try {
                                throw new Error();
                            } catch (e) {
                                i.stack = e.stack.substring(e.stack.indexOf("\n") + 1), i.stackCounter = oe++;
                            }
                            return o.promise = i, o.resolve = function (n) {
                                t || e(f(n));
                            }, o.fulfill = function (n) {
                                t || e(j(n));
                            }, o.reject = function (n) {
                                t || e(T(n));
                            }, o.notify = function (e) {
                                t || $(r, function (t, n) {
                                    f.nextTick(function () {
                                        n(e);
                                    });
                                }, void 0);
                            }, o;
                        }
                        function p(e) {
                            if ("function" != typeof e) throw new TypeError("resolver must be a function.");
                            var t = l();
                            try {
                                e(t.resolve, t.reject, t.notify);
                            } catch (e) {
                                t.reject(e);
                            }
                            return t.promise;
                        }
                        function d(e) {
                            return p(function (t, n) {
                                for (var r = 0, o = e.length; r < o; r++) f(e[r]).then(t, n);
                            });
                        }
                        function h(e, t, n) {
                            void 0 === t && (t = function t(e) {
                                return T(new Error("Promise does not support operation: " + e));
                            }), void 0 === n && (n = function n() {
                                return {
                                    state: "unknown"
                                };
                            });
                            var r = Y(h.prototype);
                            if (r.promiseDispatch = function (n, o, i) {
                                var u;
                                try {
                                    u = e[o] ? e[o].apply(r, i) : t.call(r, o, i);
                                } catch (e) {
                                    u = T(e);
                                }
                                n && n(u);
                            }, r.inspect = n, n) {
                                var o = n();
                                "rejected" === o.state && (r.exception = o.reason), r.valueOf = function () {
                                    var e = n();
                                    return "pending" === e.state || "rejected" === e.state ? r : e.value;
                                };
                            }
                            return r;
                        }
                        function v(e, t, n, r) {
                            return f(e).then(t, n, r);
                        }
                        function y(e) {
                            if (m(e)) {
                                var t = e.inspect();
                                if ("fulfilled" === t.state) return t.value;
                            }
                            return e;
                        }
                        function m(e) {
                            return e instanceof h;
                        }
                        function g(e) {
                            return n(e) && "function" == typeof e.then;
                        }
                        function b(e) {
                            return m(e) && "pending" === e.inspect().state;
                        }
                        function w(e) {
                            return !m(e) || "fulfilled" === e.inspect().state;
                        }
                        function k(e) {
                            return m(e) && "rejected" === e.inspect().state;
                        }
                        function E() {
                            ie.length = 0, ue.length = 0, ae || (ae = !0);
                        }
                        function _(t, n) {
                            ae && ("object" == _typeof(e) && "function" == typeof e.emit && f.nextTick.runAfter(function () {
                                -1 !== J(ue, t) && (e.emit("unhandledRejection", n, t), ce.push(t));
                            }), ue.push(t), n && void 0 !== n.stack ? ie.push(n.stack) : ie.push("(no stack) " + n));
                        }
                        function x(t) {
                            if (ae) {
                                var n = J(ue, t);
                                -1 !== n && ("object" == _typeof(e) && "function" == typeof e.emit && f.nextTick.runAfter(function () {
                                    var r = J(ce, t);
                                    -1 !== r && (e.emit("rejectionHandled", ie[n], t), ce.splice(r, 1));
                                }), ue.splice(n, 1), ie.splice(n, 1));
                            }
                        }
                        function T(e) {
                            var t = h({
                                when: function when(t) {
                                    return t && x(this), t ? t(e) : this;
                                }
                            }, function () {
                                return this;
                            }, function () {
                                return {
                                    state: "rejected",
                                    reason: e
                                };
                            });
                            return _(t, e), t;
                        }
                        function j(e) {
                            return h({
                                when: function when() {
                                    return e;
                                },
                                get: function get(t) {
                                    return e[t];
                                },
                                set: function set(t, n) {
                                    e[t] = n;
                                },
                                "delete": function _delete(t) {
                                    delete e[t];
                                },
                                post: function post(t, n) {
                                    return null === t || void 0 === t ? e.apply(void 0, n) : e[t].apply(e, n);
                                },
                                apply: function apply(t, n) {
                                    return e.apply(t, n);
                                },
                                keys: function keys() {
                                    return te(e);
                                }
                            }, void 0, function () {
                                return {
                                    state: "fulfilled",
                                    value: e
                                };
                            });
                        }
                        function O(e) {
                            var t = l();
                            return f.nextTick(function () {
                                try {
                                    e.then(t.resolve, t.reject, t.notify);
                                } catch (e) {
                                    t.reject(e);
                                }
                            }), t.promise;
                        }
                        function C(e) {
                            return h({
                                isDef: function isDef() {}
                            }, function (t, n) {
                                return z(e, t, n);
                            }, function () {
                                return f(e).inspect();
                            });
                        }
                        function R(e, t, n) {
                            return f(e).spread(t, n);
                        }
                        function M(e) {
                            return function () {
                                function t(e, t) {
                                    var u;
                                    if ("undefined" == typeof StopIteration) {
                                        try {
                                            u = n[e](t);
                                        } catch (e) {
                                            return T(e);
                                        }
                                        return u.done ? f(u.value) : v(u.value, o, i);
                                    }
                                    try {
                                        u = n[e](t);
                                    } catch (e) {
                                        return r(e) ? f(e.value) : T(e);
                                    }
                                    return v(u, o, i);
                                }
                                var n = e.apply(this, arguments),
                                    o = t.bind(t, "next"),
                                    i = t.bind(t, "throw");
                                return o();
                            };
                        }
                        function S(e) {
                            f.done(f.async(e)());
                        }
                        function P(e) {
                            throw new F(e);
                        }
                        function L(e) {
                            return function () {
                                return R([this, H(arguments)], function (t, n) {
                                    return e.apply(t, n);
                                });
                            };
                        }
                        function z(e, t, n) {
                            return f(e).dispatch(t, n);
                        }
                        function H(e) {
                            return v(e, function (e) {
                                var t = 0,
                                    n = l();
                                return $(e, function (r, o, i) {
                                    var u;
                                    m(o) && "fulfilled" === (u = o.inspect()).state ? e[i] = u.value : (++t, v(o, function (r) {
                                        e[i] = r, 0 == --t && n.resolve(e);
                                    }, n.reject, function (e) {
                                        n.notify({
                                            index: i,
                                            value: e
                                        });
                                    }));
                                }, void 0), 0 === t && n.resolve(e), n.promise;
                            });
                        }
                        function U(e) {
                            if (0 === e.length) return f.resolve();
                            var t = f.defer(),
                                n = 0;
                            return $(e, function (r, o, i) {
                                function u(e) {
                                    t.resolve(e);
                                }
                                function c(e) {
                                    0 === --n && (e.message = "Q can't get fulfillment value from any promise, all promises were rejected. Last error message: " + e.message, t.reject(e));
                                }
                                function a(e) {
                                    t.notify({
                                        index: i,
                                        value: e
                                    });
                                }
                                var s = e[i];
                                n++, v(s, u, c, a);
                            }, void 0), t.promise;
                        }
                        function A(e) {
                            return v(e, function (e) {
                                return e = V(e, f), v(H(V(e, function (e) {
                                    return v(e, B, B);
                                })), function () {
                                    return e;
                                });
                            });
                        }
                        function I(e) {
                            return f(e).allSettled();
                        }
                        function q(e, t) {
                            return f(e).then(void 0, void 0, t);
                        }
                        function D(e, t) {
                            return f(e).nodeify(t);
                        }
                        var N = !1;
                        try {
                            throw new Error();
                        } catch (e) {
                            N = !!e.stack;
                        }
                        var W,
                            F,
                            Q = s(),
                            B = function B() {},
                            G = function () {
                                function t() {
                                    for (var e, t; r.next;) r = r.next, e = r.task, r.task = void 0, t = r.domain, t && (r.domain = void 0, t.enter()), n(e, t);
                                    for (; a.length;) e = a.pop(), n(e);
                                    i = !1;
                                }
                                function n(e, n) {
                                    try {
                                        e();
                                    } catch (e) {
                                        if (c) throw n && n.exit(), setTimeout(t, 0), n && n.enter(), e;
                                        setTimeout(function () {
                                            throw e;
                                        }, 0);
                                    }
                                    n && n.exit();
                                }
                                var r = {
                                        task: void 0,
                                        next: null
                                    },
                                    o = r,
                                    i = !1,
                                    u = void 0,
                                    c = !1,
                                    a = [];
                                if (G = function G(t) {
                                    o = o.next = {
                                        task: t,
                                        domain: c && e.domain,
                                        next: null
                                    }, i || (i = !0, u());
                                }, "object" == _typeof(e) && "[object process]" === e.toString() && e.nextTick) c = !0, u = function u() {
                                    e.nextTick(t);
                                };else if ("function" == typeof setImmediate) u = "undefined" != typeof window ? setImmediate.bind(window, t) : function () {
                                    setImmediate(t);
                                };else if ("undefined" != typeof MessageChannel) {
                                    var s = new MessageChannel();
                                    s.port1.onmessage = function () {
                                        u = f, s.port1.onmessage = t, t();
                                    };
                                    var f = function f() {
                                        s.port2.postMessage(0);
                                    };
                                    u = function u() {
                                        setTimeout(t, 0), f();
                                    };
                                } else u = function u() {
                                    setTimeout(t, 0);
                                };
                                return G.runAfter = function (e) {
                                    a.push(e), i || (i = !0, u());
                                }, G;
                            }(),
                            Z = Function.call,
                            X = t(Array.prototype.slice),
                            $ = t(Array.prototype.reduce || function (e, t) {
                                var n = 0,
                                    r = this.length;
                                if (1 === arguments.length) for (;;) {
                                    if (n in this) {
                                        t = this[n++];
                                        break;
                                    }
                                    if (++n >= r) throw new TypeError();
                                }
                                for (; n < r; n++) n in this && (t = e(t, this[n], n));
                                return t;
                            }),
                            J = t(Array.prototype.indexOf || function (e) {
                                for (var t = 0; t < this.length; t++) if (this[t] === e) return t;
                                return -1;
                            }),
                            V = t(Array.prototype.map || function (e, t) {
                                var n = this,
                                    r = [];
                                return $(n, function (o, i, u) {
                                    r.push(e.call(t, i, u, n));
                                }, void 0), r;
                            }),
                            Y = Object.create || function (e) {
                                function t() {}
                                return t.prototype = e, new t();
                            },
                            K = Object.defineProperty || function (e, t, n) {
                                return e[t] = n.value, e;
                            },
                            ee = t(Object.prototype.hasOwnProperty),
                            te = Object.keys || function (e) {
                                var t = [];
                                for (var n in e) ee(e, n) && t.push(n);
                                return t;
                            },
                            ne = t(Object.prototype.toString);
                        F = "undefined" != typeof ReturnValue ? ReturnValue : function (e) {
                            this.value = e;
                        };
                        var re = "From previous event:";
                        f.resolve = f, f.nextTick = G, f.longStackSupport = !1;
                        var oe = 1;
                        "object" == _typeof(e) && e && e.env && e.env.Q_DEBUG && (f.longStackSupport = !0), f.defer = l, l.prototype.makeNodeResolver = function () {
                            var e = this;
                            return function (t, n) {
                                t ? e.reject(t) : arguments.length > 2 ? e.resolve(X(arguments, 1)) : e.resolve(n);
                            };
                        }, f.Promise = p, f.promise = p, p.race = d, p.all = H, p.reject = T, p.resolve = f, f.passByCopy = function (e) {
                            return e;
                        }, h.prototype.passByCopy = function () {
                            return this;
                        }, f.join = function (e, t) {
                            return f(e).join(t);
                        }, h.prototype.join = function (e) {
                            return f([this, e]).spread(function (e, t) {
                                if (e === t) return e;
                                throw new Error("Q can't join: not the same: " + e + " " + t);
                            });
                        }, f.race = d, h.prototype.race = function () {
                            return this.then(f.race);
                        }, f.makePromise = h, h.prototype.toString = function () {
                            return "[object Promise]";
                        }, h.prototype.then = function (e, t, n) {
                            function r(t) {
                                try {
                                    return "function" == typeof e ? e(t) : t;
                                } catch (e) {
                                    return T(e);
                                }
                            }
                            function i(e) {
                                if ("function" == typeof t) {
                                    o(e, c);
                                    try {
                                        return t(e);
                                    } catch (e) {
                                        return T(e);
                                    }
                                }
                                return T(e);
                            }
                            function u(e) {
                                return "function" == typeof n ? n(e) : e;
                            }
                            var c = this,
                                a = l(),
                                s = !1;
                            return f.nextTick(function () {
                                c.promiseDispatch(function (e) {
                                    s || (s = !0, a.resolve(r(e)));
                                }, "when", [function (e) {
                                    s || (s = !0, a.resolve(i(e)));
                                }]);
                            }), c.promiseDispatch(void 0, "when", [void 0, function (e) {
                                var t,
                                    n = !1;
                                try {
                                    t = u(e);
                                } catch (e) {
                                    if (n = !0, !f.onerror) throw e;
                                    f.onerror(e);
                                }
                                n || a.notify(t);
                            }]), a.promise;
                        }, f.tap = function (e, t) {
                            return f(e).tap(t);
                        }, h.prototype.tap = function (e) {
                            return e = f(e), this.then(function (t) {
                                return e.fcall(t).thenResolve(t);
                            });
                        }, f.when = v, h.prototype.thenResolve = function (e) {
                            return this.then(function () {
                                return e;
                            });
                        }, f.thenResolve = function (e, t) {
                            return f(e).thenResolve(t);
                        }, h.prototype.thenReject = function (e) {
                            return this.then(function () {
                                throw e;
                            });
                        }, f.thenReject = function (e, t) {
                            return f(e).thenReject(t);
                        }, f.nearer = y, f.isPromise = m, f.isPromiseAlike = g, f.isPending = b, h.prototype.isPending = function () {
                            return "pending" === this.inspect().state;
                        }, f.isFulfilled = w, h.prototype.isFulfilled = function () {
                            return "fulfilled" === this.inspect().state;
                        }, f.isRejected = k, h.prototype.isRejected = function () {
                            return "rejected" === this.inspect().state;
                        };
                        var ie = [],
                            ue = [],
                            ce = [],
                            ae = !0;
                        f.resetUnhandledRejections = E, f.getUnhandledReasons = function () {
                            return ie.slice();
                        }, f.stopUnhandledRejectionTracking = function () {
                            E(), ae = !1;
                        }, E(), f.reject = T, f.fulfill = j, f.master = C, f.spread = R, h.prototype.spread = function (e, t) {
                            return this.all().then(function (t) {
                                return e.apply(void 0, t);
                            }, t);
                        }, f.async = M, f.spawn = S, f["return"] = P, f.promised = L, f.dispatch = z, h.prototype.dispatch = function (e, t) {
                            var n = this,
                                r = l();
                            return f.nextTick(function () {
                                n.promiseDispatch(r.resolve, e, t);
                            }), r.promise;
                        }, f.get = function (e, t) {
                            return f(e).dispatch("get", [t]);
                        }, h.prototype.get = function (e) {
                            return this.dispatch("get", [e]);
                        }, f.set = function (e, t, n) {
                            return f(e).dispatch("set", [t, n]);
                        }, h.prototype.set = function (e, t) {
                            return this.dispatch("set", [e, t]);
                        }, f.del = f["delete"] = function (e, t) {
                            return f(e).dispatch("delete", [t]);
                        }, h.prototype.del = h.prototype["delete"] = function (e) {
                            return this.dispatch("delete", [e]);
                        }, f.mapply = f.post = function (e, t, n) {
                            return f(e).dispatch("post", [t, n]);
                        }, h.prototype.mapply = h.prototype.post = function (e, t) {
                            return this.dispatch("post", [e, t]);
                        }, f.send = f.mcall = f.invoke = function (e, t) {
                            return f(e).dispatch("post", [t, X(arguments, 2)]);
                        }, h.prototype.send = h.prototype.mcall = h.prototype.invoke = function (e) {
                            return this.dispatch("post", [e, X(arguments, 1)]);
                        }, f.fapply = function (e, t) {
                            return f(e).dispatch("apply", [void 0, t]);
                        }, h.prototype.fapply = function (e) {
                            return this.dispatch("apply", [void 0, e]);
                        }, f["try"] = f.fcall = function (e) {
                            return f(e).dispatch("apply", [void 0, X(arguments, 1)]);
                        }, h.prototype.fcall = function () {
                            return this.dispatch("apply", [void 0, X(arguments)]);
                        }, f.fbind = function (e) {
                            var t = f(e),
                                n = X(arguments, 1);
                            return function () {
                                return t.dispatch("apply", [this, n.concat(X(arguments))]);
                            };
                        }, h.prototype.fbind = function () {
                            var e = this,
                                t = X(arguments);
                            return function () {
                                return e.dispatch("apply", [this, t.concat(X(arguments))]);
                            };
                        }, f.keys = function (e) {
                            return f(e).dispatch("keys", []);
                        }, h.prototype.keys = function () {
                            return this.dispatch("keys", []);
                        }, f.all = H, h.prototype.all = function () {
                            return H(this);
                        }, f.any = U, h.prototype.any = function () {
                            return U(this);
                        }, f.allResolved = function (e, t, n) {
                            return function () {
                                return "undefined" != typeof console && "function" == typeof console.warn && console.warn(t + " is deprecated, use " + n + " instead.", new Error("").stack), e.apply(e, arguments);
                            };
                        }(A, "allResolved", "allSettled"), h.prototype.allResolved = function () {
                            return A(this);
                        }, f.allSettled = I, h.prototype.allSettled = function () {
                            return this.then(function (e) {
                                return H(V(e, function (e) {
                                    function t() {
                                        return e.inspect();
                                    }
                                    return e = f(e), e.then(t, t);
                                }));
                            });
                        }, f.fail = f["catch"] = function (e, t) {
                            return f(e).then(void 0, t);
                        }, h.prototype.fail = h.prototype["catch"] = function (e) {
                            return this.then(void 0, e);
                        }, f.progress = q, h.prototype.progress = function (e) {
                            return this.then(void 0, void 0, e);
                        }, f.fin = f["finally"] = function (e, t) {
                            return f(e)["finally"](t);
                        }, h.prototype.fin = h.prototype["finally"] = function (e) {
                            if (!e || "function" != typeof e.apply) throw new Error("Q can't apply finally callback");
                            return e = f(e), this.then(function (t) {
                                return e.fcall().then(function () {
                                    return t;
                                });
                            }, function (t) {
                                return e.fcall().then(function () {
                                    throw t;
                                });
                            });
                        }, f.done = function (e, t, n, r) {
                            return f(e).done(t, n, r);
                        }, h.prototype.done = function (t, n, r) {
                            var i = function i(e) {
                                    f.nextTick(function () {
                                        if (o(e, u), !f.onerror) throw e;
                                        f.onerror(e);
                                    });
                                },
                                u = t || n || r ? this.then(t, n, r) : this;
                            "object" == _typeof(e) && e && e.domain && (i = e.domain.bind(i)), u.then(void 0, i);
                        }, f.timeout = function (e, t, n) {
                            return f(e).timeout(t, n);
                        }, h.prototype.timeout = function (e, t) {
                            var n = l(),
                                r = setTimeout(function () {
                                    t && "string" != typeof t || (t = new Error(t || "Timed out after " + e + " ms"), t.code = "ETIMEDOUT"), n.reject(t);
                                }, e);
                            return this.then(function (e) {
                                clearTimeout(r), n.resolve(e);
                            }, function (e) {
                                clearTimeout(r), n.reject(e);
                            }, n.notify), n.promise;
                        }, f.delay = function (e, t) {
                            return void 0 === t && (t = e, e = void 0), f(e).delay(t);
                        }, h.prototype.delay = function (e) {
                            return this.then(function (t) {
                                var n = l();
                                return setTimeout(function () {
                                    n.resolve(t);
                                }, e), n.promise;
                            });
                        }, f.nfapply = function (e, t) {
                            return f(e).nfapply(t);
                        }, h.prototype.nfapply = function (e) {
                            var t = l(),
                                n = X(e);
                            return n.push(t.makeNodeResolver()), this.fapply(n).fail(t.reject), t.promise;
                        }, f.nfcall = function (e) {
                            var t = X(arguments, 1);
                            return f(e).nfapply(t);
                        }, h.prototype.nfcall = function () {
                            var e = X(arguments),
                                t = l();
                            return e.push(t.makeNodeResolver()), this.fapply(e).fail(t.reject), t.promise;
                        }, f.nfbind = f.denodeify = function (e) {
                            if (void 0 === e) throw new Error("Q can't wrap an undefined function");
                            var t = X(arguments, 1);
                            return function () {
                                var n = t.concat(X(arguments)),
                                    r = l();
                                return n.push(r.makeNodeResolver()), f(e).fapply(n).fail(r.reject), r.promise;
                            };
                        }, h.prototype.nfbind = h.prototype.denodeify = function () {
                            var e = X(arguments);
                            return e.unshift(this), f.denodeify.apply(void 0, e);
                        }, f.nbind = function (e, t) {
                            var n = X(arguments, 2);
                            return function () {
                                function r() {
                                    return e.apply(t, arguments);
                                }
                                var o = n.concat(X(arguments)),
                                    i = l();
                                return o.push(i.makeNodeResolver()), f(r).fapply(o).fail(i.reject), i.promise;
                            };
                        }, h.prototype.nbind = function () {
                            var e = X(arguments, 0);
                            return e.unshift(this), f.nbind.apply(void 0, e);
                        }, f.nmapply = f.npost = function (e, t, n) {
                            return f(e).npost(t, n);
                        }, h.prototype.nmapply = h.prototype.npost = function (e, t) {
                            var n = X(t || []),
                                r = l();
                            return n.push(r.makeNodeResolver()), this.dispatch("post", [e, n]).fail(r.reject), r.promise;
                        }, f.nsend = f.nmcall = f.ninvoke = function (e, t) {
                            var n = X(arguments, 2),
                                r = l();
                            return n.push(r.makeNodeResolver()), f(e).dispatch("post", [t, n]).fail(r.reject), r.promise;
                        }, h.prototype.nsend = h.prototype.nmcall = h.prototype.ninvoke = function (e) {
                            var t = X(arguments, 1),
                                n = l();
                            return t.push(n.makeNodeResolver()), this.dispatch("post", [e, t]).fail(n.reject), n.promise;
                        }, f.nodeify = D, h.prototype.nodeify = function (e) {
                            if (!e) return this;
                            this.then(function (t) {
                                f.nextTick(function () {
                                    e(null, t);
                                });
                            }, function (t) {
                                f.nextTick(function () {
                                    e(t);
                                });
                            });
                        }, f.noConflict = function () {
                            throw new Error("Q.noConflict only works when Q is used as a global");
                        };
                        var se = s();
                        return f;
                    });
                }).call(this, e("_process"));
            }, {
                _process: 1
            }],
            3: [function (e, t, n) {

                var r = function r(e) {
                    switch (_typeof(e)) {
                        case "string":
                            return e;
                        case "boolean":
                            return e ? "true" : "false";
                        case "number":
                            return isFinite(e) ? e : "";
                        default:
                            return "";
                    }
                };
                t.exports = function (e, t, n, o) {
                    return t = t || "&", n = n || "=", null === e && (e = void 0), "object" == _typeof(e) ? Object.keys(e).map(function (o) {
                        var i = encodeURIComponent(r(o)) + n;
                        return Array.isArray(e[o]) ? e[o].map(function (e) {
                            return i + encodeURIComponent(r(e));
                        }).join(t) : i + encodeURIComponent(r(e[o]));
                    }).join(t) : o ? encodeURIComponent(r(o)) + n + encodeURIComponent(r(e)) : "";
                };
            }, {}],
            4: [function (e, t, n) {

                function r(e) {
                    return e && e.__esModule ? e : {
                        "default": e
                    };
                }
                function o(e, t) {
                    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
                }
                function i(e) {
                    var t = new s["default"]();
                    t.onClose = e.onComplete.bind(e), t.build(), u(e, function (e) {
                        return t.setUri(e.redirectUri);
                    }, function (e) {
                        return t.close(!1);
                    });
                }
                function u(e, t, n) {
                    return v["default"].information("zip:checkout:init"), m["default"].Promise(function (t, n) {
                        return e.onCheckout(t, n, {});
                    }).then(function (r) {
                        var o = r.redirect_uri || r.redirectUri || r.data && (r.data.redirect_uri || r.data.redirectUri);
                        if (!o) return v["default"].debug("zip:checkout:error", "Response does not contain redirectUri property"), n(r), void e.onError({
                            code: "checkout_error",
                            message: "The response does not contain the redirectUri property",
                            detail: r
                        });
                        v["default"].debug("zip:checkout:success", r), t({
                            redirectUri: o
                        });
                    })["catch"](function (t) {
                        v["default"].debug("zip:checkout:error", t), n(t), e.onError({
                            code: "checkout_error",
                            message: "Checkout response error",
                            detail: t
                        });
                    });
                }
                Object.defineProperty(n, "__esModule", {
                    value: !0
                }), n.Checkout = void 0;
                var c = function () {
                        function e(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var r = t[n];
                                r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r);
                            }
                        }
                        return function (t, n, r) {
                            return n && e(t.prototype, n), r && e(t, r), t;
                        };
                    }(),
                    a = e("./modal"),
                    s = r(a),
                    f = e("./options"),
                    l = r(f),
                    p = e("./utility"),
                    d = r(p),
                    h = e("./console"),
                    v = r(h),
                    y = (e("./events"), e("q")),
                    m = r(y),
                    g = function () {
                        function e() {
                            o(this, e);
                        }
                        return c(e, null, [{
                            key: "init",
                            value: function value(e) {
                                if ("function" != typeof Object.assign && (Object.assign = function (e) {
                                    if (null == e) throw new TypeError("Cannot convert undefined or null to object");
                                    e = Object(e);
                                    for (var t = 1; t < arguments.length; t++) {
                                        var n = arguments[t];
                                        if (null != n) for (var r in n) Object.prototype.hasOwnProperty.call(n, r) && (e[r] = n[r]);
                                    }
                                    return e;
                                }), e = _extends({}, l["default"], e), !this._validate(e)) return v["default"].setLevel(e.logLevel), e.redirect ? u(e, function (t) {
                                    return e.redirectFn(t.redirectUri);
                                }) : i(e);
                            }
                        }, {
                            key: "attachButton",
                            value: function value(e, t) {
                                var n = document.querySelectorAll(e);
                                if (!n.length) return config.onError({
                                    code: "attach_error",
                                    message: "Cannot find button to attach zipMoney checkout"
                                });
                                for (var r = 0; r < n.length; r++) d["default"].addEventHandler(n[r], "click", function () {
                                    return Zip.Checkout.init(t);
                                });
                            }
                        }, {
                            key: "_validate",
                            value: function value(e) {
                                return ["error", "information", "debug"].indexOf(e.logLevel.toLowerCase()) < 0 && (e.logLevel = "error"), ["standard", "express"].indexOf(e.request.toLowerCase()) < 0 && (e.request = "standard"), e.onComplete = e.onComplete || function () {}, e.onError = e.onError || function () {}, e.checkoutUri || e.onCheckout !== l["default"].onCheckout ? "express" === e.request ? e.onError({
                                    code: "not_implemented",
                                    message: "This feature is not yet implemented"
                                }) : e.redirect ? void 0 : e.onComplete !== l["default"].onComplete || e.redirectUri ? void 0 : e.onError({
                                    code: "validation",
                                    message: "if onComplete function is not specified then redirectUri must be specified"
                                }) : e.onError({
                                    code: "validation",
                                    message: "if onCheckout function is not specified then checkoutUri must be specified"
                                });
                            }
                        }]), e;
                    }();
                n.Checkout = g;
            }, {
                "./console": 5,
                "./events": 6,
                "./modal": 8,
                "./options": 9,
                "./utility": 10,
                q: 2
            }],
            5: [function (e, t, n) {

                Object.defineProperty(n, "__esModule", {
                    value: !0
                });
                var r = "error",
                    o = {
                        error: function error() {
                            var e;
                            (e = window.console).log.apply(e, arguments);
                        },
                        information: function information() {
                            if ("error" !== r) {
                                var e;
                                (e = window.console).log.apply(e, arguments);
                            }
                        },
                        debug: function debug() {
                            if ("debug" === r) {
                                var e;
                                (e = window.console).log.apply(e, arguments);
                            }
                        },
                        setLevel: function setLevel(e) {
                            r = e;
                        }
                    };
                n["default"] = o;
            }, {}],
            6: [function (e, t, n) {
                (function (t) {

                    function r(e, t) {
                        if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
                    }
                    function o(e) {
                        var n = new t.CustomEvent("zipmoney", {
                            detail: e
                        });
                        t.dispatchEvent(n);
                    }
                    function i(e) {
                        console.log("Unexpected Event", e);
                    }
                    Object.defineProperty(n, "__esModule", {
                        value: !0
                    }), n.EventListener = void 0;
                    var u = function () {
                            function e(e, t) {
                                for (var n = 0; n < t.length; n++) {
                                    var r = t[n];
                                    r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r);
                                }
                            }
                            return function (t, n, r) {
                                return n && e(t.prototype, n), r && e(t, r), t;
                            };
                        }(),
                        c = e("./utility");
                    if (function (e) {
                        return e && e.__esModule ? e : {
                            "default": e
                        };
                    }(c)["default"].isIe) {
                        var a = function a(e, t) {
                            t = t || {
                                bubbles: !1,
                                cancelable: !1,
                                detail: void 0
                            };
                            var n = document.createEvent("CustomEvent");
                            return n.initCustomEvent(e, t.bubbles, t.cancelable, t.detail), n;
                        };
                        a.prototype = t.Event.prototype, t.CustomEvent = a;
                    }
                    var s = {},
                        f = n.EventListener = function () {
                            function e() {
                                r(this, e);
                            }
                            return u(e, null, [{
                                key: "constructor",
                                value: function value() {
                                    s = {};
                                }
                            }, {
                                key: "on",
                                value: function value(e, t) {
                                    s[e] = t;
                                }
                            }, {
                                key: "off",
                                value: function value(e) {
                                    s[e] = null;
                                }
                            }]), e;
                        }();
                    f.Event = function (e, t) {
                        this.eventType = e, this.data = t || {};
                    }, f.Event.eventTypes = {
                        resize: "resize",
                        transition: "transition",
                        close: "close",
                        complete: "complete",
                        clear: "clear"
                    };
                    var l = window.addEventListener ? "addEventListener" : "attachEvent",
                        p = window[l];
                    p("attachEvent" == l ? "onmessage" : "message", function (e) {
                        e.data.zipmoney && o(e.data.msg);
                    }, !1), p("zipmoney", function (e) {
                        var t = e.detail.eventType,
                            n = s[t];
                        n ? n(e.detail.data || {}) : i(e);
                    }, !1);
                }).call(this, "undefined" != typeof commonjsGlobal ? commonjsGlobal : "undefined" != typeof self ? self : "undefined" != typeof window ? window : {});
            }, {
                "./utility": 10
            }],
            7: [function (e, t, n) {
                (function (t) {

                    var n = e("./checkout"),
                        r = e("./events");
                    t.Zip = t.Zip || {}, t.Zip.Checkout = n.Checkout, t.zipMoneyEvent = r.EventListener.ZipEvent;
                }).call(this, "undefined" != typeof commonjsGlobal ? commonjsGlobal : "undefined" != typeof self ? self : "undefined" != typeof window ? window : {});
            }, {
                "./checkout": 4,
                "./events": 6
            }],
            8: [function (e, t, n) {

                function r(e) {
                    return e && e.__esModule ? e : {
                        "default": e
                    };
                }
                function o(e, t) {
                    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
                }
                function i() {
                    return Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                }
                function u() {
                    var e = document.body,
                        t = document.documentElement;
                    return Math.max(e.scrollHeight, e.offsetHeight, t.clientHeight, t.scrollHeight, t.offsetHeight);
                }
                function c(e, t) {
                    var n = document.createElement("div");
                    return n.className = "zipmoney-overlay", _extends(n.style, {
                        position: "absolute",
                        left: "0",
                        top: "0",
                        display: "table-cell",
                        textAlign: "center",
                        verticalAlign: "middle",
                        background: "rgba(0, 0, 0, 0.75)",
                        zIndex: "10000",
                        height: "100%",
                        width: "100%"
                    }), n;
                }
                function a(e, t) {
                    var n = document.createElement("iframe");
                    return n.id = "zipmoney-iframe", n.frameborder = 0, _extends(n.style, {
                        padding: "0",
                        border: "none",
                        zIndex: "999999",
                        backgroundColor: "#FFF",
                        backgroundImage: "url(" + k + "spinner.gif)",
                        backgroundRepeat: "no-repeat",
                        backgroundPosition: "50% 50%"
                    }), t ? _extends(n.style, {
                        overflow: "scroll",
                        width: "100%",
                        height: "100%",
                        position: "absolute",
                        top: "0",
                        bottom: "0",
                        left: "0",
                        right: "0",
                        margin: "0"
                    }) : _extends(n.style, {
                        width: b.iframeWidth + "px",
                        minWidth: b.iframeMinWidth + "px",
                        height: b.iframeInitialHeight + "px",
                        margin: b.verticalMargin + "px auto 0 auto",
                        display: "table-row",
                        backgroundSize: "25%",
                        textAlign: "center",
                        boxShadow: "0px 0px 70px 0px rgb(0, 0, 0)"
                    }), n.src = e || "", n;
                }
                function s() {
                    var e = document.createElement("img");
                    return e.src = k + "icon-close.png", _extends(e.style, {
                        width: "50px",
                        height: "50px",
                        position: "absolute",
                        top: "20px",
                        right: "20px",
                        cursor: "pointer"
                    }), e;
                }
                function f() {
                    var e = document.createElement("div");
                    e.style.width = b.iframeWidth + "px", e.style.minWidth = b.iframeMinWidth + "px", e.style.margin = "10px auto 0 auto", e.style.overflow = "hidden";
                    var t = document.createElement("img");
                    t.src = k + "iframe-secure.png", t.style.cssFloat = "left";
                    var n = document.createElement("img");
                    return n.src = k + "poweredby-trans.png", n.style.cssFloat = "right", e.appendChild(t), e.appendChild(n), e;
                }
                function l(e) {
                    for (var t = document.querySelectorAll("html, body"), n = 0; n < t.length; n++) t[n].style.overflowY = e;
                }
                function p(e) {
                    var t = document.body.currentStyle || window.getComputedStyle(document.body),
                        n = document.body.offsetHeight,
                        r = parseInt(t.marginTop, 10) + parseInt(t.marginBottom, 10),
                        o = n + r,
                        i = o - parseInt(t.height, 10),
                        u = e - i;
                    document.body.style.height = u + "px";
                }
                function d(e) {
                    document.body.style.height = "initial";
                }
                Object.defineProperty(n, "__esModule", {
                    value: !0
                });
                var h = function () {
                        function e(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var r = t[n];
                                r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r);
                            }
                        }
                        return function (t, n, r) {
                            return n && e(t.prototype, n), r && e(t, r), t;
                        };
                    }(),
                    v = e("./utility"),
                    y = r(v),
                    m = e("./events"),
                    g = e("./console"),
                    b = (r(g), {
                        iframeWidth: 400,
                        iframeMinWidth: 320,
                        iframeInitialHeight: 704,
                        iframeMinHeight: 600,
                        verticalMargin: 35
                    }),
                    w = ["resize", "transition", "close", "complete", "clear"],
                    k = "https://d3k1w8lx8mqizo.cloudfront.net/zm/",
                    E = function () {
                        function e() {
                            var t = this;
                            o(this, e), this._events = [], this._isMobile = y["default"].isMobileDevice(), w.forEach(function (e) {
                                return m.EventListener.on(m.EventListener.Event.eventTypes[e], t[e].bind(t));
                            });
                        }
                        return h(e, [{
                            key: "build",
                            value: function value() {
                                this._initialHtmlHeight = u(), window.scrollTo(0, 0);
                                var e = this._frame = a(this._frameUri, this._isMobile);
                                if (!this._isMobile) {
                                    var t = s();
                                    t.onclick = this.close.bind(this), this._overlay = c(), this._overlay.appendChild(t), this._overlay.appendChild(this._frame), this._overlay.appendChild(f()), e = this._overlay;
                                }
                                document.body.appendChild(e), this._isMobile && p(this._frame.offsetHeight), l("auto"), this._startMonitoringWindowResize();
                            }
                        }, {
                            key: "setUri",
                            value: function value(e) {
                                this._frameUri = e, this._frame && (this._frame.src = e);
                            }
                        }, {
                            key: "resize",
                            value: function value(e) {
                                var t = this._isMobile ? 16 : 0,
                                    e = e >= b.iframeMinHeight ? e : b.iframeMinHeight;
                                e += t;
                                var n = e + 2 * b.verticalMargin,
                                    r = i();
                                this._frame.style.height = e + "px", this._overlay && (this._overlay.style.height = Math.max(r, this._initialHtmlHeight, n) + "px"), this._isMobile && p(e);
                            }
                        }, {
                            key: "transition",
                            value: function value() {
                                window.scroll(0, 0);
                            }
                        }, {
                            key: "close",
                            value: function value() {
                                var e = !(arguments.length > 0 && void 0 !== arguments[0]) || arguments[0],
                                    t = {
                                        state: "cancelled"
                                    };
                                this._events.length && (t = this._events.pop()), this._destroy(), this.onClose && e && this.onClose(t);
                            }
                        }, {
                            key: "complete",
                            value: function value(e) {
                                this._events.push(e);
                            }
                        }, {
                            key: "clear",
                            value: function value(e) {
                                this._events = [];
                            }
                        }, {
                            key: "_startMonitoringWindowResize",
                            value: function value() {
                                function e(e) {
                                    var t = i(),
                                        n = u(),
                                        r = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                                    e._overlay && (e._overlay.style.height = Math.max(t, n) + "px"), e._isMobile || r < b.iframeWidth ? e._frame.style.width = "100%" : e._frame.style.width = b.iframeWidth + "px";
                                }
                                var t = this;
                                this._resizeHandler = y["default"].debounce(function () {
                                    return e(t);
                                }, 250), y["default"].addEventHandler(window, "resize", this._resizeHandler);
                            }
                        }, {
                            key: "_stopMonitoringWindowResize",
                            value: function value() {
                                y["default"].removeEventHandler(window, "resize", this._resizeHandler);
                            }
                        }, {
                            key: "_destroy",
                            value: function value() {
                                this._stopMonitoringWindowResize(), this._overlay ? document.body.removeChild(this._overlay) : document.body.removeChild(this._frame), this._isMobile && d(), l("initial"), this._overlay = this._frame = null, w.forEach(function (e) {
                                    return m.EventListener.off(m.EventListener.Event.eventTypes[e]);
                                });
                            }
                        }]), e;
                    }();
                n["default"] = E;
            }, {
                "./console": 5,
                "./events": 6,
                "./utility": 10
            }],
            9: [function (e, t, n) {

                function r(e) {
                    return e && e.__esModule ? e : {
                        "default": e
                    };
                }
                Object.defineProperty(n, "__esModule", {
                    value: !0
                });
                var o = e("./xr"),
                    i = r(o),
                    u = e("./console"),
                    c = r(u);
                i["default"].configure({
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });
                var a = {
                    request: "standard",
                    redirect: !1,
                    logLevel: "Error",
                    onCheckout: function onCheckout(e, t, n) {
                        i["default"].post(this.checkoutUri).then(function (t) {
                            return e(t.data);
                        })["catch"](t);
                    },
                    onShippingAddressChanged: function onShippingAddressChanged(e, t, n) {
                        i["default"].post(this.shippingUri).then(e)["catch"](t);
                    },
                    onComplete: function onComplete(e) {
                        if (c["default"].information("zip:completed", e), "cancelled" !== e.state) {
                            var t = e.checkoutId ? "&checkoutId=" + e.checkoutId : "";
                            this.redirectFn(this.redirectUri + "?result=" + e.state + t);
                        }
                    },
                    onError: function onError(e) {
                        c["default"].error(e);
                    },
                    redirectFn: function redirectFn(e) {
                        window.location.href = e;
                    }
                };
                n["default"] = a;
            }, {
                "./console": 5,
                "./xr": 11
            }],
            10: [function (e, t, n) {

                function r(e, t) {
                    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
                }
                Object.defineProperty(n, "__esModule", {
                    value: !0
                });
                var o = function () {
                        function e(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var r = t[n];
                                r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r);
                            }
                        }
                        return function (t, n, r) {
                            return n && e(t.prototype, n), r && e(t, r), t;
                        };
                    }(),
                    i = function () {
                        function e() {
                            r(this, e);
                        }
                        return o(e, null, [{
                            key: "isIe",
                            value: function value() {
                                var e = -1,
                                    t = window.navigator.userAgent,
                                    n = t.indexOf("MSIE "),
                                    r = t.indexOf("Trident/");
                                if (n > 0) e = parseInt(t.substring(n + 5, t.indexOf(".", n)), 10);else if (r > 0) {
                                    var o = t.indexOf("rv:");
                                    e = parseInt(t.substring(o + 3, t.indexOf(".", o)), 10);
                                }
                                return e > -1 ? e : void 0;
                            }
                        }, {
                            key: "isMobileDevice",
                            value: function value() {
                                var e = navigator.userAgent || navigator.vendor || window.opera;
                                return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(e) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(e.substr(0, 4));
                            }
                        }, {
                            key: "debounce",
                            value: function value(e, t) {
                                var n;
                                return function () {
                                    var r = this,
                                        o = arguments,
                                        i = function i() {
                                            n = null, e.apply(r, o);
                                        },
                                        u = !n;
                                    clearTimeout(n), n = setTimeout(i, t), u && e.apply(r, o);
                                };
                            }
                        }, {
                            key: "addEventHandler",
                            value: function value(e, t, n) {
                                e.addEventListener ? e.addEventListener(t, n, !1) : e.attachEvent && e.attachEvent("on" + t, n);
                            }
                        }, {
                            key: "removeEventHandler",
                            value: function value(e, t, n) {
                                e.removeEventListener ? e.removeEventListener(t, n, !1) : e.detachEvent && e.detachEvent("on" + t, n);
                            }
                        }]), e;
                    }();
                n["default"] = i;
            }, {}],
            11: [function (e, t, n) {

                function r(e) {
                    return e && e.__esModule ? e : {
                        "default": e
                    };
                }
                function o(e, t) {
                    return {
                        status: e.status,
                        response: e.response,
                        data: t,
                        xhr: e
                    };
                }
                function i(e) {
                    for (var t = arguments.length, n = Array(t > 1 ? t - 1 : 0), r = 1; r < t; r++) n[r - 1] = arguments[r];
                    for (var o in n) if ({}.hasOwnProperty.call(n, o)) {
                        var i = n[o];
                        if ("object" === (void 0 === i ? "undefined" : s(i))) for (var u in i) ({}).hasOwnProperty.call(i, u) && (e[u] = i[u]);
                    }
                    return e;
                }
                function u(e) {
                    m = i({}, m, e);
                }
                function c(e, t) {
                    return (e && e.promise ? e.promise : m.promise || y.promise)(t);
                }
                function a(e) {
                    return c(e, function (t, n) {
                        var r = i({}, y, m, e),
                            u = r.xmlHttpRequest();
                        r.abort && e.abort(function () {
                            n(o(u)), u.abort();
                        }), u.open(r.method, r.params ? r.url.split("?")[0] + "?" + (0, l["default"])(r.params) : r.url, !0), u.withCredentials = r.withCredentials, u.addEventListener(v.LOAD, function () {
                            if (u.status >= 200 && u.status < 300) {
                                var e = null;
                                u.responseText && (e = !0 === r.raw ? u.responseText : r.load(u.responseText)), t(o(u, e));
                            } else n(o(u));
                        }), u.addEventListener(v.ABORT, function () {
                            return n(o(u));
                        }), u.addEventListener(v.ERROR, function () {
                            return n(o(u));
                        }), u.addEventListener(v.TIMEOUT, function () {
                            return n(o(u));
                        });
                        for (var c in r.headers) ({}).hasOwnProperty.call(r.headers, c) && u.setRequestHeader(c, r.headers[c]);
                        for (var a in r.events) ({}).hasOwnProperty.call(r.events, a) && u.addEventListener(a, r.events[a].bind(null, u), !1);
                        var f = "object" !== s(r.data) || r.raw ? r.data : r.dump(r.data);
                        void 0 !== f ? u.send(f) : u.send();
                    });
                }
                Object.defineProperty(n, "__esModule", {
                    value: !0
                });
                var s = "function" == typeof Symbol && "symbol" == _typeof(Symbol.iterator) ? function (e) {
                        return _typeof(e);
                    } : function (e) {
                        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : _typeof(e);
                    },
                    f = e("querystring/encode"),
                    l = r(f),
                    p = e("q"),
                    d = r(p),
                    h = {
                        GET: "GET",
                        POST: "POST",
                        PUT: "PUT",
                        DELETE: "DELETE",
                        PATCH: "PATCH",
                        OPTIONS: "OPTIONS"
                    },
                    v = {
                        READY_STATE_CHANGE: "readystatechange",
                        LOAD_START: "loadstart",
                        PROGRESS: "progress",
                        ABORT: "abort",
                        ERROR: "error",
                        LOAD: "load",
                        TIMEOUT: "timeout",
                        LOAD_END: "loadend"
                    },
                    y = {
                        method: h.GET,
                        data: void 0,
                        headers: {
                            Accept: "application/json",
                            "Content-Type": "application/json"
                        },
                        dump: JSON.stringify,
                        load: JSON.parse,
                        xmlHttpRequest: function xmlHttpRequest() {
                            return new XMLHttpRequest();
                        },
                        promise: function promise(e) {
                            return d["default"].Promise(e);
                        },
                        withCredentials: !1
                    },
                    m = {};
                a.assign = i, a.encode = l["default"], a.configure = u, a.Methods = h, a.Events = v, a.defaults = y, a.get = function (e, t, n) {
                    return a(i({
                        url: e,
                        method: h.GET,
                        params: t
                    }, n));
                }, a.put = function (e, t, n) {
                    return a(i({
                        url: e,
                        method: h.PUT,
                        data: t
                    }, n));
                }, a.post = function (e, t, n) {
                    return a(i({
                        url: e,
                        method: h.POST,
                        data: t
                    }, n));
                }, a.patch = function (e, t, n) {
                    return a(i({
                        url: e,
                        method: h.PATCH,
                        data: t
                    }, n));
                }, a.del = function (e, t) {
                    return a(i({
                        url: e,
                        method: h.DELETE
                    }, t));
                }, a.options = function (e, t) {
                    return a(i({
                        url: e,
                        method: h.OPTIONS
                    }, t));
                }, n["default"] = a;
            }, {
                q: 2,
                "querystring/encode": 3
            }]
        }, {}, [7]);
    });
    unwrapExports(zipmoney);

    var ECHO_URI = '/v1/echo';
    var LOG_PREFIX = '[CheckoutButton:Zipmoney]';
    var ZipmoneyRunner = /*#__PURE__*/function (_BaseRunner) {
        _inherits(ZipmoneyRunner, _BaseRunner);
        var _super = _createSuper(ZipmoneyRunner);
        function ZipmoneyRunner() {
            var _this;
            _classCallCheck(this, ZipmoneyRunner);
            _this = _super.call(this);
            _this.apiEnv = new Env(API_URL);
            return _this;
        }
        _createClass(ZipmoneyRunner, [{
            key: "setEnv",
            value: function setEnv(env, alias) {
                _get(_getPrototypeOf(ZipmoneyRunner.prototype), "setEnv", this).call(this, env, alias);
                this.apiEnv.setEnv(env, alias);
            }
        }, {
            key: "getCheckoutUri",
            value: function getCheckoutUri(redirectUri) {
                return this.apiEnv.getConf().url + ECHO_URI + '?' + Url.serialize({
                    json_body: JSON.stringify({
                        redirect_uri: redirectUri
                    })
                });
            }
        }]);
        return ZipmoneyRunner;
    }(BaseRunner);

    var ZIPMONEY_PROXY_REDIRECT_URL = '/checkout/zipmoney/response';
    var ZIPMONEY_DEFAULT_SUSPENDED_URL = '/checkout/zipmoney/suspended';
    var ZIPMONEY_MESSAGE_SOURCE = 'zipmoney.checkout.paydock';

    var ZipmoneyContextualRunner = /*#__PURE__*/function (_ContextualRunner) {
        _inherits(ZipmoneyContextualRunner, _ContextualRunner);
        var _super = _createSuper(ZipmoneyContextualRunner);
        function ZipmoneyContextualRunner() {
            var _this;
            _classCallCheck(this, ZipmoneyContextualRunner);
            _this = _super.call(this);
            _this.runs = false;
            _this.eventEmitter = new EventEmitter();
            return _this;
        }
        _createClass(ZipmoneyContextualRunner, [{
            key: "run",
            value: function run() {
                this.runs = true;
                this.background.initLoader();
            }
        }, {
            key: "isRunning",
            value: function isRunning() {
                return this.runs;
            }
        }, {
            key: "next",
            value: function next(checkoutData) {
                var _this2 = this;
                this.background.clear();
                this.checkout = checkoutData;
                var checkoutUri = this.getCheckoutUri(this.checkout.link);
                Zip.Checkout.init({
                    checkoutUri: checkoutUri,
                    onComplete: function onComplete(args) {
                        return _this2.eventHandler(args);
                    },
                    onError: function onError(args) {
                        return _this2.eventHandler(args);
                    }
                });
            }
        }, {
            key: "getSuccessRedirectUri",
            value: function getSuccessRedirectUri() {
                return this.suspendedRedirectUri ? this.suspendedRedirectUri : this.widgetEnv.getConf().url + String(ZIPMONEY_DEFAULT_SUSPENDED_URL);
            }
        }, {
            key: "getErrorRedirectUri",
            value: function getErrorRedirectUri() {
                return this.getSuccessRedirectUri();
            }
        }, {
            key: "stop",
            value: function stop() {
                _get(_getPrototypeOf(ZipmoneyContextualRunner.prototype), "stop", this).call(this);
                this.runs = false;
                var element = document.querySelector('.zipmoney-overlay');
                if (element) element.remove();
                this.eventEmitter.emit(RUNNER_EVENT.CLOSE);
            }
        }, {
            key: "onStop",
            value: function onStop(cb) {
                var _this3 = this;
                this.eventEmitter.subscribe(RUNNER_EVENT.CLOSE, function () {
                    _this3.background.clear();
                    cb();
                });
            }
        }, {
            key: "onCheckout",
            value: function onCheckout(event, cb) {
                var _this4 = this;
                this.eventEmitter.subscribe(event, function () {
                    cb(_this4.checkout);
                });
            }
        }, {
            key: "eventHandler",
            value: function eventHandler(args) {
                this.runs = false;
                switch (args.state) {
                    case "approved" /* APPROVED */:
                        this.eventEmitter.emit(RUNNER_EVENT.CLOSE);
                        this.eventEmitter.emit(RUNNER_EVENT.SUCCESS);
                        break;
                    case "declined" /* DECLINED */:
                        this.eventEmitter.emit(RUNNER_EVENT.CLOSE);
                        this.eventEmitter.emit(RUNNER_EVENT.DECLINED);
                        break;
                    case "cancelled" /* CANCELLED */:
                        this.eventEmitter.emit(RUNNER_EVENT.CLOSE);
                        break;
                    case "referred" /* REFERRED */:
                        this.eventEmitter.emit(RUNNER_EVENT.CLOSE);
                        this.eventEmitter.emit(RUNNER_EVENT.REFERRED);
                        break;
                    default:
                        console.warn("".concat(LOG_PREFIX, " Unknown gateway status."));
                        break;
                }
            }
        }]);
        return ZipmoneyContextualRunner;
    }(ContextualRunner(ZipmoneyRunner));

    var STORAGE_DISPATCHER_URI = '/storage-dispatcher';
    var STORAGE_DISPATCHER_ID = 'pd-storage-dispatcher';
    var LOG_PREFIX$1 = '[Paydock:StorageDispatcher]';
    var StorageDataIntent;
    (function (StorageDataIntent) {
        StorageDataIntent["WIDGET_SESSION"] = "widget-session";
    })(StorageDataIntent || (StorageDataIntent = {}));
    var StorageDispatcher = /*#__PURE__*/function () {
        function StorageDispatcher(messageSource) {
            _classCallCheck(this, StorageDispatcher);
            this.messageSource = messageSource;
            this.defaultPayload = {
                destination: 'widget.paydock'
            };
            this.env = new Env(WIDGET_URL);
            this.defaultPayload.source = messageSource;
            this.iframeEvent = new IFrameEvent(window);
        }
        _createClass(StorageDispatcher, [{
            key: "create",
            value: function create(onLoadCallback) {
                this.onLoadCallback = onLoadCallback;
                if (this.dispatcherFrame) this.destroy();
                this.widgetId = Uuid.generate();
                this.setupIframeEventListeners();
                var iFrame = document.createElement('iframe');
                iFrame.setAttribute('src', this.env.getConf().url + STORAGE_DISPATCHER_URI + "?widgetId=".concat(this.widgetId));
                iFrame.setAttribute('id', STORAGE_DISPATCHER_ID);
                iFrame.style.display = 'none';
                document.body.appendChild(iFrame);
                this.dispatcherFrame = iFrame;
                console.info("".concat(LOG_PREFIX$1, " initialized."));
                return iFrame;
            }
        }, {
            key: "destroy",
            value: function destroy() {
                if (this.dispatcherFrame && this.dispatcherFrame.parentNode) {
                    this.dispatcherFrame.parentNode.removeChild(this.dispatcherFrame);
                }
                this.iframeEvent.clear();
                this.widgetId = undefined;
                this.dispatcherFrame = undefined;
            }
        }, {
            key: "push",
            value: function push(payload, pushCallbacks) {
                var _a;
                this.pushCallbacks = pushCallbacks;
                if (!this.dispatcherFrame) {
                    console.error("".concat(LOG_PREFIX$1, " dispatcher is not initialized."));
                    return;
                }
                var body = _extends(_extends({}, this.defaultPayload), payload);
                (_a = this.dispatcherFrame.contentWindow) === null || _a === void 0 ? void 0 : _a.postMessage(body, this.env.getConf().url);
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.env.setEnv(env, alias);
                this.create(this.onLoadCallback);
            }
        }, {
            key: "setupIframeEventListeners",
            value: function setupIframeEventListeners() {
                var _this = this;
                if (!this.widgetId) return;
                this.iframeEvent.on(EVENT.AFTER_LOAD, this.widgetId, function (_event) {
                    var _a;
                    (_a = _this.onLoadCallback) === null || _a === void 0 ? void 0 : _a.call(_this);
                });
                this.iframeEvent.on(EVENT.DISPATCH_SUCCESS, this.widgetId, function (_event) {
                    var _a, _b;
                    (_b = (_a = _this.pushCallbacks) === null || _a === void 0 ? void 0 : _a.onSuccess) === null || _b === void 0 ? void 0 : _b.call(_a);
                });
                this.iframeEvent.on(EVENT.DISPATCH_ERROR, this.widgetId, function (_event) {
                    var _a, _b;
                    (_b = (_a = _this.pushCallbacks) === null || _a === void 0 ? void 0 : _a.onError) === null || _b === void 0 ? void 0 : _b.call(_a);
                });
            }
        }]);
        return StorageDispatcher;
    }();

    var ZipmoneyRedirectRunner = /*#__PURE__*/function (_RedirectRunner) {
        _inherits(ZipmoneyRedirectRunner, _RedirectRunner);
        var _super = _createSuper(ZipmoneyRedirectRunner);
        function ZipmoneyRedirectRunner() {
            var _this;
            _classCallCheck(this, ZipmoneyRedirectRunner);
            _this = _super.call(this);
            _this.storageDispatcher = new StorageDispatcher(ZIPMONEY_MESSAGE_SOURCE);
            return _this;
        }
        _createClass(ZipmoneyRedirectRunner, [{
            key: "getProxyRedirectUrl",
            value: function getProxyRedirectUrl() {
                return this.widgetEnv.getConf().url + ZIPMONEY_PROXY_REDIRECT_URL;
            }
        }, {
            key: "next",
            value: function next(checkout, params) {
                var _this2 = this;
                this.storageDispatcher.create(function () {
                    var widgetSessionData = {
                        merchant_redirect_url: _this2.getRedirectUrl(),
                        checkout_token: checkout.token,
                        public_key: params.public_key,
                        gateway_id: params.gateway_id
                    };
                    _this2.storageDispatcher.push({
                        intent: StorageDataIntent.WIDGET_SESSION,
                        data: widgetSessionData
                    }, {
                        onSuccess: function onSuccess() {
                            var checkoutUri = _this2.getCheckoutUri(checkout.link);
                            Zip.Checkout.init({
                                checkoutUri: checkoutUri,
                                redirect: true
                            });
                        },
                        onError: function onError() {
                            console.error('Error initializing Zip Checkout');
                        }
                    });
                });
            }
            // for backward compatibility
        }, {
            key: "getSuccessRedirectUri",
            value: function getSuccessRedirectUri() {
                return this.getProxyRedirectUrl();
            }
            // for backward compatibility
        }, {
            key: "getErrorRedirectUri",
            value: function getErrorRedirectUri() {
                return this.getProxyRedirectUrl();
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                _get(_getPrototypeOf(ZipmoneyRedirectRunner.prototype), "setEnv", this).call(this, env, alias);
                this.storageDispatcher.setEnv(env, alias);
            }
        }]);
        return ZipmoneyRedirectRunner;
    }(RedirectRunner(ZipmoneyRunner));

    var REDIRECT_URI_SUCCESS = '/checkout/success';
    var REDIRECT_URI_ERROR = '/checkout/error';
    var PaypalRunner = /*#__PURE__*/function (_PopupRunner) {
        _inherits(PaypalRunner, _PopupRunner);
        var _super = _createSuper(PaypalRunner);
        function PaypalRunner() {
            _classCallCheck(this, PaypalRunner);
            return _super.apply(this, arguments);
        }
        _createClass(PaypalRunner, [{
            key: "getSuccessRedirectUri",
            value: function getSuccessRedirectUri() {
                return this.widgetEnv.getConf().url + Url.extendSearchParams(REDIRECT_URI_SUCCESS, 'merchant', encodeURIComponent(window.location.href));
            }
        }, {
            key: "getErrorRedirectUri",
            value: function getErrorRedirectUri() {
                return this.widgetEnv.getConf().url + Url.extendSearchParams(REDIRECT_URI_ERROR, 'merchant', encodeURIComponent(window.location.href));
            }
        }]);
        return PaypalRunner;
    }(PopupRunner);

    var REDIRECT_URI_SUCCESS$1 = '/checkout/afterpay/merchant/{{merchant}}/success';
    var REDIRECT_URI_ERROR$1 = '/checkout/afterpay/merchant/{{merchant}}/error';
    var CHECKOUT_URL = '/checkout/afterpay/init';
    var AfterpayRunner = /*#__PURE__*/function (_PopupRunner) {
        _inherits(AfterpayRunner, _PopupRunner);
        var _super = _createSuper(AfterpayRunner);
        function AfterpayRunner() {
            _classCallCheck(this, AfterpayRunner);
            return _super.apply(this, arguments);
        }
        _createClass(AfterpayRunner, [{
            key: "getSuccessRedirectUri",
            value: function getSuccessRedirectUri() {
                return this.widgetEnv.getConf().url + REDIRECT_URI_SUCCESS$1.replace('{{merchant}}', encodeURIComponent(window.btoa(window.location.href)));
            }
        }, {
            key: "getErrorRedirectUri",
            value: function getErrorRedirectUri() {
                return this.widgetEnv.getConf().url + REDIRECT_URI_ERROR$1.replace('{{merchant}}', encodeURIComponent(window.btoa(window.location.href)));
            }
        }, {
            key: "next",
            value: function next(checkoutData, params) {
                this.checkout = checkoutData;
                if (!Browser.isSupportPopUp()) window.localStorage.setItem('paydock_checkout_token', JSON.stringify(this.checkout));
                this.popup.redirect(this.getRedirectUrl(this.checkout, params));
            }
        }, {
            key: "error",
            value: function error(_error, code, callback) {
                if (!code || code && code !== 'invalid_amount') return callback(true);
                this.popup.initError(_error);
                return callback(false);
            }
        }, {
            key: "run",
            value: function run() {
                if (this.isRunning()) return;
                this.popup.setConfigs({
                    width: 420,
                    height: 715
                });
                this.popup.init();
                this.background.initControl();
            }
        }, {
            key: "getRedirectUrl",
            value: function getRedirectUrl(checkout, params) {
                return this.widgetEnv.getConf().url + CHECKOUT_URL + '?' + Url.serialize(_extends(_extends({}, params), {
                    token: checkout.reference_id,
                    env: checkout.mode === 'live' ? 'live' : 'test'
                }));
            }
        }]);
        return AfterpayRunner;
    }(PopupRunner);

    var TYPE = {
        EXTERNAL_CHECKOUT_TOKEN: 'external_checkout_token',
        CHECKOUT_TOKEN: 'checkout_token',
        BANK_ACCOUNT: 'bank_account',
        CARD: 'card'
    };
    var LINK$1 = '/v1/payment_sources/tokens';
    var Builder$1 = /*#__PURE__*/function (_HttpCore) {
        _inherits(Builder, _HttpCore);
        var _super = _createSuper(Builder);
        function Builder(gatewayID, body) {
            var _this;
            var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : TYPE.CARD;
            _classCallCheck(this, Builder);
            _this = _super.call(this);
            _this.body = {
                gateway_id: gatewayID,
                type: type
            };
            switch (type) {
                case TYPE.CARD:
                case TYPE.BANK_ACCOUNT:
                    delete body.gateway_id;
                    delete body.type;
                    delete body.checkout_token;
                    _this.body = _extends(_this.body, body);
                    break;
                case TYPE.CHECKOUT_TOKEN:
                case TYPE.EXTERNAL_CHECKOUT_TOKEN:
                    _this.body.checkout_token = body;
                    break;
                default:
                    throw new Error('Unsupported type of PaymentSourceToken');
            }
            return _this;
        }
        _createClass(Builder, [{
            key: "getLink",
            value: function getLink() {
                return LINK$1;
            }
        }, {
            key: "send",
            value: function send(accessToken, cb) {
                var errorCb = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function (err) {};
                this.create(accessToken, this.getConfigs(), function (data, status) {
                    return cb(data);
                }, function (err, status) {
                    if (typeof err.message === "undefined") errorCb('unknown error');else errorCb(err.message);
                });
            }
        }, {
            key: "getConfigs",
            value: function getConfigs() {
                return this.body;
            }
        }]);
        return Builder;
    }(HttpCore);

    var CheckoutContextualHandler = /*#__PURE__*/function () {
        function CheckoutContextualHandler(background, runner, eventEmitter, params) {
            var _this = this;
            _classCallCheck(this, CheckoutContextualHandler);
            this.background = background;
            this.runner = runner;
            this.eventEmitter = eventEmitter;
            this.params = params;
            this.runner.onStop(function () {
                _this.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.CLOSE);
            });
        }
        _createClass(CheckoutContextualHandler, [{
            key: "init",
            value: function init(env) {
                var _this2 = this;
                this.setEnv(env);
                this.runner.onCheckout(RUNNER_EVENT.SUCCESS, function (checkout) {
                    _this2.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.ACCEPTED, {});
                    _this2.background.initLoader();
                    _this2.runner.stop();
                    _this2.checkToken(checkout.token, function () {
                        _this2.createOneTimeToken(checkout.token);
                    });
                });
                this.runner.onCheckout(RUNNER_EVENT.ERROR, function () {
                    _this2.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.ERROR);
                    console.error("".concat(CHECKOUT_BTN_LOG_PREFIX, " Error from checkout server."));
                    _this2.runner.stop();
                });
                this.runner.onCheckout(RUNNER_EVENT.REFERRED, function () {
                    _this2.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.REFERRED);
                    _this2.runner.stop();
                });
                this.runner.onCheckout(RUNNER_EVENT.DECLINED, function () {
                    _this2.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.DECLINED);
                    _this2.runner.stop();
                });
                this.eventEmitter.subscribe(CHECKOUT_BUTTON_EVENT.ERROR, function () {
                    _this2.runner.stop();
                });
                this.eventEmitter.subscribe(CHECKOUT_BUTTON_EVENT.FINISH, function (data) {
                    _this2.background.clear();
                });
            }
        }, {
            key: "setEnv",
            value: function setEnv(env) {
                this.env = env;
            }
        }, {
            key: "checkToken",
            value: function checkToken(token, cb) {
                var _this3 = this;
                var checker = new Checker(token);
                checker.setEnv(this.env);
                checker.send(this.params.accessToken, function (details) {
                    _this3.details = details;
                    cb();
                }, function () {
                    _this3.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.ERROR, {});
                    console.error("".concat(CHECKOUT_BTN_LOG_PREFIX, " Error during creating payment source token."));
                });
            }
        }, {
            key: "createOneTimeToken",
            value: function createOneTimeToken(token) {
                var _this4 = this;
                var paymentSourceToken = new Builder$1(this.params.gatewayId, token, TYPE.CHECKOUT_TOKEN);
                paymentSourceToken.setEnv(this.env);
                paymentSourceToken.send(this.params.accessToken, function (token) {
                    _this4.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.FINISH, {
                        payment_source_token: token,
                        checkout_email: _this4.details.checkout_email,
                        checkout_holder: _this4.details.checkout_holder,
                        gateway_type: _this4.details.gateway_type
                    });
                }, function () {
                    _this4.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.ERROR, {});
                    console.error("".concat(CHECKOUT_BTN_LOG_PREFIX, " Error during creating payment source token."));
                });
            }
        }]);
        return CheckoutContextualHandler;
    }();

    /**
     * Class CheckoutButton transform usual button into checkout
     *
     * @constructor
     *
     * @param {string} selector - Selector of html element.
     * @param {string} aceessToken - PayDock access token or users public key
     * @param {string} [gatewayId=default] - PayDock's gatewayId. By default or if put 'default', it will use the selected default gateway
     * @param {string} [type=PaypalClassic] - Type of gateway (PaypalClassic, Zipmoney)
     * @example
     * var widget = new CheckoutButton('#button', 'accessToken','gatewayId');
     **/
    var CheckoutButton = /*#__PURE__*/function () {
        function CheckoutButton(selector, accessToken) {
            var gatewayId = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'default';
            var gatewayType = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : GATEWAY_TYPE.PAYPAL;
            var mode = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : CHECKOUT_MODE.CONTEXTUAL;
            _classCallCheck(this, CheckoutButton);
            this.accessToken = accessToken;
            this.gatewayId = gatewayId;
            this.gatewayType = gatewayType;
            this.mode = mode;
            this.window = window;
            this.meta = {};
            this.env = DEFAULT_ENV;
            this.eventEmitter = new EventEmitter();
            this.container = new Container(selector);
            this.initCheckout(this.container);
            this.chooseRunner(gatewayType, mode);
        }
        _createClass(CheckoutButton, [{
            key: "chooseRunner",
            value: function chooseRunner(gatewayType, mode) {
                var Runner = this.getRunnerByMode(gatewayType, mode);
                this.runner = new Runner(this.accessToken);
                if (isContextualRunner(this.runner)) {
                    this.background = new Background();
                    this.checkoutHandler = new CheckoutContextualHandler(this.background, this.runner, this.eventEmitter, {
                        accessToken: this.accessToken,
                        gatewayId: this.gatewayId
                    });
                    this.checkoutHandler.init(this.env);
                } else {
                    this.background = undefined;
                    this.checkoutHandler = undefined;
                }
            }
        }, {
            key: "buildAdditionalParams",
            value: function buildAdditionalParams() {
                return {};
            }
        }, {
            key: "initCheckout",
            value: function initCheckout(container) {
                var _this = this;
                container.on('click', function (event) {
                    if (isContextualRunner(_this.runner)) {
                        if (_this.runner.isRunning()) return;else _this.runner.run();
                    } else if (isRedirectRunner(_this.runner)) {
                        if (!_this.runner.getRedirectUrl()) throw Error("".concat(CHECKOUT_BTN_LOG_PREFIX, " The merchant redirect URL should is required in the '").concat(_this.mode, "' mode."));
                    }
                    _this.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.CLICK);
                    var externalCheckout = new Builder(_this.gatewayId, _this.runner.getSuccessRedirectUri(), _this.runner.getErrorRedirectUri());
                    externalCheckout.setMeta(_this.meta);
                    externalCheckout.setEnv(_this.env);
                    externalCheckout.send(_this.accessToken, function (checkout) {
                        var buttonEvent = isContextualRunner(_this.runner) ? CHECKOUT_BUTTON_EVENT.POPUP_REDIRECT : CHECKOUT_BUTTON_EVENT.REDIRECT;
                        _this.eventEmitter.emit(buttonEvent);
                        _this.runner.next(checkout, _this.buildAdditionalParams());
                    }, function (error, code) {
                        console.error("".concat(CHECKOUT_BTN_LOG_PREFIX, " ").concat(error));
                        _this.eventEmitter.emit(CHECKOUT_BUTTON_EVENT.ERROR, {
                            error: error,
                            code: code
                        });
                        _this.runner.error(error, code, function (close) {
                            if (close) _this.close();
                        });
                    });
                });
            }
            /**
             * This callback will be called for each event in payment source widget
             *
             * @callback listener
             */
            /**
             * Listen to events of widget
             *
             * @example
             *
             * widget.on('click', function () {
             *
             * });
             * @param {string} eventName - Available event names [CHECKOUT_BUTTON_EVENT]{@link CHECKOUT_BUTTON_EVENT}
             * @param {listener} cb
             */
        }, {
            key: "on",
            value: function on(name, handler) {
                this.eventEmitter.subscribe(name, handler);
            }
            /**
             * Close popup window forcibly.
             * Only for 'contextual' mode.
             *
             */
        }, {
            key: "close",
            value: function close() {
                if (this.assertMethodSupport(this.runner, CHECKOUT_MODE.CONTEXTUAL)) this.runner.stop();
            }
            /**
             * After finish event of checkout button, data (dataType) will be insert to input (selector)
             *
             * @param {string} selector - css selector . [] #
             * @param {string} dataType - data type of IEventCheckoutFinishData [IEventCheckoutFinishData]{@link #IEventCheckoutFinishData}.
             */
        }, {
            key: "onFinishInsert",
            value: function onFinishInsert(selector, dataType) {
                this.on(CHECKOUT_BUTTON_EVENT.FINISH, function (event) {
                    Event.insertToInput(selector, dataType, event);
                });
            }
            /**
             * Method for setting meta information for checkout page
             *
             * @example
             * button.setMeta({
             brand_name: 'paydock',
             reference: '15',
             email: 'wault@paydock.com'
             });
             *
             * @param {(IPayPalMeta|IAfterpayMeta|IZipmoneyMeta)} meta - Data which can be shown on checkout page [IPayPalMeta]{@link IPayPalMeta} [IZipmoneyMeta]{@link IZipmoneyMeta} [IAfterpayMeta]{@link IAfterpayMeta}
             */
        }, {
            key: "setMeta",
            value: function setMeta(meta) {
                this.meta = _extends(this.meta, meta);
            }
            /**
             * Method for setting backdrop description.
             * Only for 'contextual' mode.
             *
             * @example
             * button.setBackdropDescription('Custom description');
             *
             * @param {string} text - description which can be shown in overlay of back side checkout page
             */
        }, {
            key: "setBackdropDescription",
            value: function setBackdropDescription(text) {
                if (this.assertMethodSupport(this.runner, CHECKOUT_MODE.CONTEXTUAL)) this.runner.setBackgroundDescription(text);
            }
            /**
             * Method for setting backdrop title.
             * Only for 'contextual' mode.
             *
             * @example
             * button.setBackdropTitle('Custom title');
             *
             * @param {text} string - title which can be shown in overlay of back side checkout page
             */
        }, {
            key: "setBackdropTitle",
            value: function setBackdropTitle(text) {
                if (this.assertMethodSupport(this.runner, CHECKOUT_MODE.CONTEXTUAL)) this.runner.setBackgroundTitle(text);
            }
            /**
             * Method for setting suspended redirect uri. Redirect after referred checkout.
             * Only for 'contextual' mode.
             *
             * @param {uri} string - uri for redirect (by default)
             */
        }, {
            key: "setSuspendedRedirectUri",
            value: function setSuspendedRedirectUri(uri) {
                if (this.assertMethodSupport(this.runner, CHECKOUT_MODE.CONTEXTUAL)) this.runner.setSuspendedRedirectUri(uri);
            }
            /**
             * Method for setting the merchant redirect URL.
             * Merchant's customers redirect after successfull checkout.
             * Only for 'redirect' mode.
             *
             * @param {url} string - redirect url
             */
        }, {
            key: "setRedirectUrl",
            value: function setRedirectUrl(url) {
                if (this.assertMethodSupport(this.runner, CHECKOUT_MODE.REDIRECT)) this.runner.setRedirectUrl(url);
            }
        }, {
            key: "getSuccessRedirectUri",
            value: function getSuccessRedirectUri() {
                return this.runner.getSuccessRedirectUri();
            }
            /**
             * Method for disable backdrop on the page.
             * Only for 'contextual' mode.
             *
             * @example
             * button.turnOffBackdrop();
             *
             */
        }, {
            key: "turnOffBackdrop",
            value: function turnOffBackdrop() {
                this.turnOffControlBackdrop();
                this.turnOffLoaderBackdrop();
            }
        }, {
            key: "turnOffControlBackdrop",
            value: function turnOffControlBackdrop() {
                if (this.assertMethodSupport(this.runner, CHECKOUT_MODE.CONTEXTUAL)) this.runner.turnOffBackdrop();
            }
        }, {
            key: "turnOffLoaderBackdrop",
            value: function turnOffLoaderBackdrop() {
                var _a;
                (_a = this.background) === null || _a === void 0 ? void 0 : _a.turnOffLoader();
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                var _a;
                this.env = env;
                this.alias = alias;
                (_a = this.checkoutHandler) === null || _a === void 0 ? void 0 : _a.setEnv(env);
                this.runner.setEnv(env, alias);
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                return this.env;
            }
        }, {
            key: "getRunnerByMode",
            value: function getRunnerByMode(gatewayType, mode) {
                if (gatewayType === GATEWAY_TYPE.PAYPAL) {
                    if (mode === CHECKOUT_MODE.REDIRECT) throw Error("".concat(CHECKOUT_BTN_LOG_PREFIX, " Gateway '").concat(gatewayType, "' do not support '").concat(mode, "' mode"));
                    return PaypalRunner;
                } else if (gatewayType === GATEWAY_TYPE.AFTERPAY) {
                    if (mode === CHECKOUT_MODE.REDIRECT) throw Error("".concat(CHECKOUT_BTN_LOG_PREFIX, " Gateway '").concat(gatewayType, "' do not support '").concat(mode, "' mode"));
                    return AfterpayRunner;
                } else if (gatewayType === GATEWAY_TYPE.ZIPMONEY) {
                    return mode === CHECKOUT_MODE.CONTEXTUAL ? ZipmoneyContextualRunner : ZipmoneyRedirectRunner;
                } else {
                    throw Error("".concat(CHECKOUT_BTN_LOG_PREFIX, " Unsupported gateway."));
                }
            }
        }, {
            key: "assertMethodSupport",
            value: function assertMethodSupport(runner, mode) {
                var warnMessage = "".concat(CHECKOUT_BTN_LOG_PREFIX, " The method is not supported in the '").concat(mode, "' mode.");
                switch (mode) {
                    case CHECKOUT_MODE.CONTEXTUAL:
                        if (isContextualRunner(runner)) return true;else console.warn(warnMessage);
                        break;
                    case CHECKOUT_MODE.REDIRECT:
                        if (isRedirectRunner(runner)) return true;else console.warn(warnMessage);
                        break;
                }
                return false;
            }
        }]);
        return CheckoutButton;
    }();
    /**
     * @interface IEventCheckoutFinishData
     *
     * @param {string} [payment_source_token]
     */

    /**
     * Class ZipmoneyCheckoutButton is wrapper of CheckoutButton transform usual button into checkout
     *
     * @extends CheckoutButton
     *
     * @constructor
     *
     * @param {string} selector - Selector of html element.
     * @param {string} publicKey - PayDock users public key
     * @param {string} [gatewayId=default] - PayDock's gatewayId. By default or if put 'default', it will use the selected default gateway
     * @param {string} [gatewayId=default] - Checkout mode, it could be set to 'contextual' or 'redirect'. By default it 'contextual'
     * @example
     * var widget = new ZipmoneyCheckoutButton('#button', 'publicKey','gatewayId');
     **/
    var ZipmoneyCheckoutButton = /*#__PURE__*/function (_CheckoutButton) {
        _inherits(ZipmoneyCheckoutButton, _CheckoutButton);
        var _super = _createSuper(ZipmoneyCheckoutButton);
        function ZipmoneyCheckoutButton(selector, publicKey) {
            var _this;
            var gatewayId = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'default';
            var mode = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : CHECKOUT_MODE.CONTEXTUAL;
            _classCallCheck(this, ZipmoneyCheckoutButton);
            _this = _super.call(this, selector, publicKey, gatewayId, GATEWAY_TYPE.ZIPMONEY, mode);
            _this.publicKey = publicKey;
            _this.gatewayId = gatewayId;
            _this.mode = mode;
            return _this;
        }
        /**
         * Method for setting suspended redirect uri. Redirect after referred checkout
         *
         * The URI is used for a redirect after the checkout is complete.
         * This can be provided, even if using in-context checkout (sdk). By default, the standard styled page will be used.
         * If using in-context (sdk) we will not automatically redirect to this URI.
         *
         * @param {uri} string - uri for suspended redirect (by default)
         */
        _createClass(ZipmoneyCheckoutButton, [{
            key: "setSuspendedRedirectUri",
            value: function setSuspendedRedirectUri(uri) {
                _get(_getPrototypeOf(ZipmoneyCheckoutButton.prototype), "setSuspendedRedirectUri", this).call(this, uri);
            }
            /**
             * Method for setting the merchant redirect URL.
             * The merchant's customers would be redirected to the specified URL
             * at the end of ZipMoney checkout flow.
             *
             * Once the redirect URL would be set, the checkout flow would be immediately switched
             * from 'contextual' mode to the 'redirect' mode.
             * The merchant's customer would be automatically redirected to this URL after the checkout is complete.
             *
             * @param {url} string - URL for redirect
             */
        }, {
            key: "setRedirectUrl",
            value: function setRedirectUrl(url) {
                if (isContextualRunner(this.runner)) {
                    _get(_getPrototypeOf(ZipmoneyCheckoutButton.prototype), "chooseRunner", this).call(this, GATEWAY_TYPE.ZIPMONEY, CHECKOUT_MODE.REDIRECT);
                    _get(_getPrototypeOf(ZipmoneyCheckoutButton.prototype), "setEnv", this).call(this, this.env, this.alias);
                }
                _get(_getPrototypeOf(ZipmoneyCheckoutButton.prototype), "setRedirectUrl", this).call(this, url);
            }
        }, {
            key: "buildAdditionalParams",
            value: function buildAdditionalParams() {
                var defaultParams = _get(_getPrototypeOf(ZipmoneyCheckoutButton.prototype), "buildAdditionalParams", this).call(this);
                var params = _extends(_extends({}, defaultParams), {
                    public_key: this.publicKey,
                    gateway_id: this.gatewayId
                });
                return params;
            }
        }]);
        return ZipmoneyCheckoutButton;
    }(CheckoutButton);

    /**
     * Class AfterpayCheckoutButton is wrapper of CheckoutButton transform usual button into checkout
     *
     * @extends CheckoutButton
     *
     * @constructor
     *
     * @param {string} selector - Selector of html element.
     * @param {string} accessToken - PayDock access-token or users public key
     * @param {string} [gatewayId=default] - PayDock's gatewayId. By default or if put 'default', it will use the selected default gateway
     * @example
     * var widget = new AfterpayCheckoutButton('#button', 'access-token','gatewayId');
     **/
    var AfterpayCheckoutButton = /*#__PURE__*/function (_CheckoutButton) {
        _inherits(AfterpayCheckoutButton, _CheckoutButton);
        var _super = _createSuper(AfterpayCheckoutButton);
        function AfterpayCheckoutButton(selector, accessToken) {
            var _this;
            var gatewayId = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'default';
            _classCallCheck(this, AfterpayCheckoutButton);
            _this = _super.call(this, selector, accessToken, gatewayId, GATEWAY_TYPE.AFTERPAY);
            _this.accessToken = accessToken;
            _this.gatewayId = gatewayId;
            _this.showETP = false;
            return _this;
        }
        /**
         * Method which toggles the "Enhanced Tracking Protection" warning popup to 'on' mode.
         *
         * This popup with a warning about "Enhanced Tracking Protection" limitations
         * would be shown in the Mozilla Firefox browser version 100+
         *
         * By default, the popup would not be shown, until
         * the flag would be set to `true`
         * @param {doShow} boolean - flag which toggle the popup visibility
         */
        _createClass(AfterpayCheckoutButton, [{
            key: "showEnhancedTrackingProtectionPopup",
            value: function showEnhancedTrackingProtectionPopup(doShow) {
                var _Browser$getBrowserIn = Browser.getBrowserInfo(),
                    name = _Browser$getBrowserIn.name,
                    version = _Browser$getBrowserIn.version;
                if (doShow && name === 'Firefox' && +version >= 100) this.showETP = true;
            }
        }, {
            key: "buildAdditionalParams",
            value: function buildAdditionalParams() {
                var params = _get(_getPrototypeOf(AfterpayCheckoutButton.prototype), "buildAdditionalParams", this).call(this);
                if (this.showETP) {
                    params.show_etp = true;
                }
                return params;
            }
        }]);
        return AfterpayCheckoutButton;
    }(CheckoutButton);

    /**
     * Class PaypalCheckoutButton is wrapper of CheckoutButton transform usual button into checkout
     *
     * @extends CheckoutButton
     *
     * @constructor
     *
     * @param {string} selector - Selector of html element.
     * @param {string} publicKey - PayDock users public key
     * @param {string} [gatewayId=default] - PayDock's gatewayId. By default or if put 'default', it will use the selected default gateway
     * @example
     * var widget = new PaypalCheckoutButton('#button', 'publicKey','gatewayId');
     **/
    var PaypalCheckoutButton = /*#__PURE__*/function (_CheckoutButton) {
        _inherits(PaypalCheckoutButton, _CheckoutButton);
        var _super = _createSuper(PaypalCheckoutButton);
        function PaypalCheckoutButton(selector, publicKey) {
            var _this;
            var gatewayId = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'default';
            _classCallCheck(this, PaypalCheckoutButton);
            _this = _super.call(this, selector, publicKey, gatewayId, GATEWAY_TYPE.PAYPAL);
            _this.publicKey = publicKey;
            _this.gatewayId = gatewayId;
            return _this;
        }
        return _createClass(PaypalCheckoutButton);
    }(CheckoutButton);

    var FLYPAY_EVENT;
    (function (FLYPAY_EVENT) {
        FLYPAY_EVENT["AFTER_LOAD"] = "after_load";
        FLYPAY_EVENT["UNAVAILABLE"] = "unavailable";
        FLYPAY_EVENT["START_LOADING"] = "start_loading";
        FLYPAY_EVENT["END_LOADING"] = "end_loading";
        FLYPAY_EVENT["UPDATE"] = "update";
        FLYPAY_EVENT["PAYMENT_SUCCESSFUL"] = "payment_successful";
        FLYPAY_EVENT["PAYMENT_IN_REVIEW"] = "payment_in_review";
        FLYPAY_EVENT["PAYMENT_ERROR"] = "payment_error";
    })(FLYPAY_EVENT || (FLYPAY_EVENT = {}));
    var FlypayIframeEvent = /*#__PURE__*/function (_IFrameEvent) {
        _inherits(FlypayIframeEvent, _IFrameEvent);
        var _super = _createSuper(FlypayIframeEvent);
        function FlypayIframeEvent() {
            _classCallCheck(this, FlypayIframeEvent);
            return _super.apply(this, arguments);
        }
        _createClass(FlypayIframeEvent, [{
            key: "on",
            value: function on(eventName, widgetId, cb) {
                for (var event in FLYPAY_EVENT) {
                    if (!FLYPAY_EVENT.hasOwnProperty(event)) continue;
                    if (eventName === FLYPAY_EVENT[event]) {
                        this.listeners.push({
                            event: eventName,
                            listener: cb,
                            widget_id: widgetId
                        });
                    }
                }
            }
        }]);
        return FlypayIframeEvent;
    }(IFrameEvent);

    var TRIGGER$2 = {
        CLOSE: 'close',
        UPDATED: 'updated'
    };
    var WalletTrigger = /*#__PURE__*/function (_Trigger) {
        _inherits(WalletTrigger, _Trigger);
        var _super = _createSuper(WalletTrigger);
        function WalletTrigger(iFrame, widgetId) {
            var _this;
            _classCallCheck(this, WalletTrigger);
            _this = _super.call(this, iFrame);
            _this.widgetId = widgetId;
            return _this;
        }
        _createClass(WalletTrigger, [{
            key: "push",
            value: function push(triggerName) {
                var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
                if (!this.iFrame.isExist()) return;
                if (ObjectHelper.values(TRIGGER$2).indexOf(triggerName) === -1) console.warn('unsupported trigger type');
                var body = {
                    message_source: 'wallet.paydock',
                    reference_id: this.widgetId,
                    trigger: triggerName,
                    destination: 'widget.paydock',
                    data: data
                };
                this.iFrame.getElement().contentWindow.postMessage(JSON.stringify(body), '*');
            }
        }]);
        return WalletTrigger;
    }(Trigger);

    var TEMPLATE$2 = "\n    <div class=\"checkout-container\">\n        <div class=\"checkout-bg-logo\"></div>\n        <a href=\"#\" data-close>Close</a>\n    </div>\n";
    var STYLES$1 = "\n    .checkout-bg-logo {\n        display: block;\n        background: url({{url}}) no-repeat;\n        width: 100px;\n        height: 50px;\n        margin: 0 auto;\n        border-radius: 10px;\n        background-size: contain;\n    }\n";
    var WalletBackground = /*#__PURE__*/function (_Background) {
        _inherits(WalletBackground, _Background);
        var _super = _createSuper(WalletBackground);
        function WalletBackground(bgImageUrl) {
            var _this;
            _classCallCheck(this, WalletBackground);
            _this = _super.call(this);
            _this.bgImageUrl = bgImageUrl;
            _this.imageStyle = null;
            return _this;
        }
        _createClass(WalletBackground, [{
            key: "initControl",
            value: function initControl() {
                if (!this.imageStyle) this.createImageStyles();
                _get(_getPrototypeOf(WalletBackground.prototype), "initControl", this).call(this);
            }
        }, {
            key: "clear",
            value: function clear() {
                if (this.imageStyle) this.imageStyle.parentNode.removeChild(this.imageStyle);
                this.imageStyle = null;
                _get(_getPrototypeOf(WalletBackground.prototype), "clear", this).call(this);
            }
        }, {
            key: "createTemplate",
            value: function createTemplate() {
                var _this2 = this;
                var body = document.body || document.getElementsByTagName("body")[0];
                var template = String(TEMPLATE$2);
                this.overlay = document.createElement("div");
                this.overlay.classList.add("checkout-overlay");
                this.overlay.setAttribute("checkout-overlay", " ");
                this.overlay.innerHTML = template;
                body.appendChild(this.overlay);
                setTimeout(function () {
                    if (_this2.isInit()) _this2.overlay.classList.add("display");
                }, 5);
            }
        }, {
            key: "createImageStyles",
            value: function createImageStyles() {
                var head = document.head || document.getElementsByTagName("head")[0];
                var css = String(STYLES$1);
                var container = document.querySelector(".checkout-container");
                css = css.replace("{{url}}", this.bgImageUrl);
                this.imageStyle = document.createElement("style");
                this.imageStyle.type = "text/css";
                this.imageStyle.appendChild(document.createTextNode(css));
                head.appendChild(this.imageStyle);
            }
        }]);
        return WalletBackground;
    }(Background);

    var WALLET_EVENT = {
        UNAVAILABLE: 'unavailable',
        UPDATE: 'update',
        PAYMENT_METHOD_SELECTED: 'payment_method_selected',
        PAYMENT_SUCCESS: 'payment_success',
        PAYMENT_IN_REVIEW: 'payment_in_review',
        PAYMENT_ERROR: 'payment_error',
        CALLBACK: 'callback'
    };
    var WalletService = /*#__PURE__*/function () {
        function WalletService(publicKey, meta) {
            _classCallCheck(this, WalletService);
            this.publicKey = publicKey;
            this.meta = meta;
            this.env = DEFAULT_ENV;
            this.eventEmitter = new EventEmitter();
            this.initializeChildWallets();
        }
        _createClass(WalletService, [{
            key: "initializeChildWallets",
            value: function initializeChildWallets() {
                this.childWallets = [];
            }
        }, {
            key: "getGatewayName",
            value: function getGatewayName() {
                // required for Google Pay direct integrations
                throw new Error("Method not implemented");
            }
        }, {
            key: "setEnv",
            value: function setEnv(env) {
                this.env = env;
                return this;
            }
        }, {
            key: "load",
            value: function load(container) {
                this.childWallets.forEach(function (child) {
                    return child.load(container);
                });
                return;
            }
        }, {
            key: "update",
            value: function update(data) {
                // do nothing unless current wallet service overrides this method;
                return;
            }
        }, {
            key: "on",
            value: function on(eventName, cb) {
                var _this = this;
                if (ObjectHelper.values(WALLET_EVENT).indexOf(eventName) === -1) throw new Error("invalid wallet event");
                if (typeof cb === 'function') return this.eventEmitter.subscribe(eventName, cb);
                return new Promise(function (resolve) {
                    return _this.eventEmitter.subscribe(eventName, function (res) {
                        return resolve(res);
                    });
                });
            }
        }]);
        return WalletService;
    }();

    var FlypayWalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(FlypayWalletService, _WalletService);
        var _super = _createSuper(FlypayWalletService);
        function FlypayWalletService(token, meta) {
            var _this;
            _classCallCheck(this, FlypayWalletService);
            _this = _super.call(this, token, meta);
            _this.link = new Link(FLYPAY_LINK);
            var amount = meta.amount,
                currency = meta.currency,
                id = meta.id,
                gateway_mode = meta.gateway_mode,
                reference = meta.reference,
                request_shipping = meta.request_shipping;
            _this.link.setParams(_extends({
                token: token,
                amount: amount,
                currency: currency,
                gateway_mode: gateway_mode,
                credentials: reference || id
            }, request_shipping ? {
                request_shipping: request_shipping
            } : {}));
            if (SDK.version) {
                _this.link.setParams({
                    sdk_version: SDK.version,
                    sdk_type: SDK.type
                });
            }
            _this.token = token;
            _this.event = new FlypayIframeEvent(window);
            return _this;
        }
        _createClass(FlypayWalletService, [{
            key: "load",
            value: function load(container) {
                this.container = container;
                this.iFrame = new IFrame(this.container);
                var widgetId = this.link.getParams().widget_id;
                this.triggerElement = new WalletTrigger(this.iFrame, widgetId);
                this.setupIFrameEvents(widgetId);
                this.background = this.initBackground();
                this.iFrame.load(this.link.getUrl());
                return;
            }
        }, {
            key: "close",
            value: function close() {
                this.triggerElement.push(TRIGGER$2.CLOSE);
                this.background.clear();
            }
        }, {
            key: "update",
            value: function update(data) {
                this.triggerElement.push(TRIGGER$2.UPDATED, data);
            }
        }, {
            key: "setEnv",
            value: function setEnv(env) {
                this.link.setEnv(env);
                return this;
            }
        }, {
            key: "initBackground",
            value: function initBackground() {
                var _this2 = this;
                var bg = new WalletBackground(this.link.getNetUrl().replace(FLYPAY_LINK, FLYPAY_LOGO_LINK));
                bg.setBackdropTitle("");
                bg.setBackdropDescription("");
                bg.onTrigger(TRIGGER$1.CLOSE, function () {
                    return _this2.triggerElement.push(TRIGGER$2.CLOSE);
                });
                return bg;
            }
        }, {
            key: "setupIFrameEvents",
            value: function setupIFrameEvents(widgetId) {
                var _this3 = this;
                this.event.on(FLYPAY_EVENT.UNAVAILABLE, widgetId, function (_data) {
                    return _this3.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, null);
                });
                this.event.on(FLYPAY_EVENT.START_LOADING, widgetId, function (_data) {
                    return _this3.background.initControl();
                });
                this.event.on(FLYPAY_EVENT.END_LOADING, widgetId, function (_data) {
                    return _this3.background.clear();
                });
                this.event.on(FLYPAY_EVENT.UPDATE, widgetId, function (data) {
                    _this3.eventEmitter.emit(WALLET_EVENT.UPDATE, _this3.parseUpdateData(data));
                });
                this.event.on(FLYPAY_EVENT.PAYMENT_SUCCESSFUL, widgetId, function (data) {
                    _this3.eventEmitter.emit(WALLET_EVENT.PAYMENT_SUCCESS, _this3.parsePaymentSuccessfulData(data));
                    if (!_this3.iFrame.getElement()) _this3.background.clear();
                });
                this.event.on(FLYPAY_EVENT.PAYMENT_IN_REVIEW, widgetId, function (data) {
                    _this3.eventEmitter.emit(WALLET_EVENT.PAYMENT_IN_REVIEW, _this3.parsePaymentSuccessfulData(data));
                    if (!_this3.iFrame.getElement()) _this3.background.clear();
                });
                this.event.on(FLYPAY_EVENT.PAYMENT_ERROR, widgetId, function (data) {
                    _this3.eventEmitter.emit(WALLET_EVENT.PAYMENT_ERROR, data);
                    if (!_this3.iFrame.getElement()) _this3.background.clear();
                });
            }
        }, {
            key: "parsePaymentSuccessfulData",
            value: function parsePaymentSuccessfulData(data) {
                var _a;
                return {
                    id: this.meta.id,
                    amount: data.amount,
                    currency: data.currencyCode,
                    status: (_a = data.charge) === null || _a === void 0 ? void 0 : _a.status
                };
            }
        }, {
            key: "parseUpdateData",
            value: function parseUpdateData(data) {
                return _extends(_extends(_extends({
                    wallet_response_code: data.responseCode,
                    wallet_session_id: data.sessionId
                }, data.paymentMethodDetails ? {
                    payment_source: {
                        wallet_payment_method_id: data.paymentMethodDetails.paymentMethodId,
                        card_number_last4: data.paymentMethodDetails.lastFourDigitsOfPan,
                        card_scheme: data.paymentMethodDetails.paymentScheme
                    }
                } : {}), data.loyaltyAccountSummary ? {
                    wallet_loyalty_account: {
                        id: data.loyaltyAccountSummary.loyaltyAccountId,
                        barcode: data.loyaltyAccountSummary.loyaltyAccountBarcode
                    }
                } : {}), data.deliveryAddressDetails ? {
                    shipping: {
                        address_line1: data.deliveryAddressDetails.line1,
                        address_line2: data.deliveryAddressDetails.line2,
                        address_postcode: data.deliveryAddressDetails.postalCode,
                        address_city: data.deliveryAddressDetails.city,
                        address_state: data.deliveryAddressDetails.state,
                        address_country: data.deliveryAddressDetails.countryCode,
                        address_company: data.deliveryAddressDetails.companyName,
                        wallet_address_id: data.deliveryAddressDetails.addressId,
                        post_office_box_number: data.deliveryAddressDetails.postOfficeBoxNumber,
                        wallet_address_created_timestamp: data.deliveryAddressDetails.createdTimestamp,
                        wallet_address_updated_timestamp: data.deliveryAddressDetails.updatedTimestamp,
                        wallet_address_name: data.deliveryAddressDetails.name
                    }
                } : {});
            }
        }]);
        return FlypayWalletService;
    }(WalletService);

    var PaypalWalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(PaypalWalletService, _WalletService);
        var _super = _createSuper(PaypalWalletService);
        function PaypalWalletService() {
            _classCallCheck(this, PaypalWalletService);
            return _super.apply(this, arguments);
        }
        _createClass(PaypalWalletService, [{
            key: "load",
            value: function load(container) {
                if (!window.Promise) {
                    // Given that this library does not rely in any polyfill for promises, and this integration depends on them, we early return if Promises are not supported for the browser (like I.E. 11).
                    this.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, null);
                    return Promise.resolve();
                }
                if (this.meta.standalone === true) {
                    this.renderPaypalStandaloneComponent(container);
                } else {
                    this.renderPaypalCommonComponent(container);
                }
                return Promise.resolve();
            }
        }, {
            key: "update",
            value: function update(data) {
                var _this = this;
                if (!this.latestShippingChangePromiseResolve || !this.latestShippingChangePromiseReject) return;
                if (!data.success) return this.latestShippingChangePromiseReject();
                this.eventEmitter.emit(WALLET_EVENT.CALLBACK, {
                    data: {
                        request_type: "UPDATE_TRANSACTION",
                        shipping: this.latestShippingData
                    },
                    onSuccess: function onSuccess(_res) {
                        return _this.latestShippingChangePromiseResolve(true);
                    },
                    onError: function onError() {
                        return _this.latestShippingChangePromiseReject();
                    }
                });
            }
        }, {
            key: "renderPaypalCommonComponent",
            value: function renderPaypalCommonComponent(container) {
                var _this2 = this;
                var _a;
                var buttonId = ((_a = container.getElement()) === null || _a === void 0 ? void 0 : _a.id) || '';
                var paypalScript = document.createElement("script");
                paypalScript.src = "https://www.paypal.com/sdk/js?client-id=".concat(this.publicKey, "&currency=").concat(this.meta.currency).concat(this.meta.pay_later === true ? '&enable-funding=paylater&disable-funding=card' : "&disable-funding=credit,card").concat(!this.meta.capture ? "&intent=authorize" : '');
                paypalScript.async = true;
                paypalScript.onload = function () {
                    if (window.paypal) {
                        _this2.paypal = window.paypal;
                        _this2.paypal.Buttons(_extends({}, _this2.paypalSharedProps())).render("#".concat(buttonId));
                    } else {
                        _this2.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, null);
                    }
                };
                document.head.appendChild(paypalScript);
            }
        }, {
            key: "renderPaypalStandaloneComponent",
            value: function renderPaypalStandaloneComponent(container) {
                var _this3 = this;
                var _a, _b;
                var buttonId = ((_a = container.getElement()) === null || _a === void 0 ? void 0 : _a.id) || '';
                var paypalScript = document.createElement("script");
                // buyer-country is only used in Sandbox. It shouldn't be used in production. Based on buyer's geolocation this parameter determine which funding sources are eligible for a given buyer. Refer the link https://developer.paypal.com/sdk/js/configuration/#link-buyercountry
                paypalScript.src = "https://www.paypal.com/sdk/js?client-id=".concat(this.publicKey, "&currency=").concat(this.meta.currency, "&components=buttons,funding-eligibility,messages&enable-funding=paylater").concat(!this.meta.capture ? "&intent=authorize" : '').concat(((_b = this.meta) === null || _b === void 0 ? void 0 : _b.gateway_mode) === 'live' ? '' : '&buyer-country=AU');
                paypalScript.async = true;
                paypalScript.onload = function () {
                    if (window.paypal) {
                        _this3.paypal = window.paypal;
                        var isPayLater = !!_this3.meta.pay_later;
                        var button = _this3.paypal.Buttons(_extends({
                            fundingSource: isPayLater ? _this3.paypal.FUNDING.PAYLATER : _this3.paypal.FUNDING.PAYPAL
                        }, _this3.paypalSharedProps()));
                        if (button.isEligible()) {
                            button.render("#".concat(buttonId));
                            if (isPayLater) {
                                var messaging = _this3.paypal.Messages(_extends({
                                    amount: _this3.meta.amount,
                                    currency: _this3.meta.currency,
                                    placement: 'payment'
                                }, _this3.meta.style && _this3.meta.style['messages'] && {
                                    style: _this3.meta.style['messages']
                                }));
                                messaging.render("#".concat(buttonId));
                            }
                        } else {
                            _this3.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, null);
                        }
                    } else {
                        _this3.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, null);
                    }
                };
                document.head.appendChild(paypalScript);
            }
        }, {
            key: "paypalSharedProps",
            value: function paypalSharedProps() {
                var _this4 = this;
                return _extends(_extends({}, this.meta.style && {
                    style: this.meta.style
                }), {
                    createOrder: function createOrder() {
                        return new Promise(function (resolve, reject) {
                            _this4.eventEmitter.emit(WALLET_EVENT.CALLBACK, {
                                data: _extends({
                                    request_type: "CREATE_TRANSACTION"
                                }, _this4.meta.request_shipping && {
                                    request_shipping: _this4.meta.request_shipping
                                }),
                                onSuccess: function onSuccess(res) {
                                    return resolve(res.id);
                                },
                                onError: function onError(err) {
                                    return reject(err);
                                }
                            });
                        });
                    },
                    onShippingChange: function onShippingChange(data, _actions) {
                        return new Promise(function (resolve, reject) {
                            var parsedCallbackData = _this4.parseUpdateData(data);
                            _this4.latestShippingData = parsedCallbackData.shipping;
                            _this4.latestShippingChangePromiseResolve = resolve;
                            _this4.latestShippingChangePromiseReject = reject;
                            _this4.eventEmitter.emit(WALLET_EVENT.UPDATE, parsedCallbackData);
                        });
                    },
                    onApprove: function onApprove(data) {
                        _this4.pendingApprovalPromise = _this4.pendingApprovalPromise || new Promise(function (resolve, reject) {
                            return _this4.eventEmitter.emit(WALLET_EVENT.PAYMENT_METHOD_SELECTED, {
                                data: {
                                    payment_method_id: data.orderID,
                                    customer: {
                                        payment_source: {
                                            external_payer_id: data.payerID
                                        }
                                    }
                                },
                                onSuccess: function onSuccess() {
                                    _this4.pendingApprovalPromise = undefined;
                                    resolve(true);
                                },
                                onError: function onError(err) {
                                    _this4.pendingApprovalPromise = undefined;
                                    reject(err);
                                }
                            });
                        });
                        return _this4.pendingApprovalPromise;
                    },
                    onError: function onError(err) {
                        // Error handling so that paypal does not throw an uncaught error
                        // We're already handling errors and notifying Merchants at "wallet-buttons.ts"
                    }
                });
            }
        }, {
            key: "parseUpdateData",
            value: function parseUpdateData(data) {
                var _a;
                return _extends(_extends({
                    wallet_order_id: data.orderID,
                    wallet_session_id: data.paymentID,
                    payment_source: {
                        wallet_payment_method_id: data.paymentToken
                    }
                }, data.shipping_address && {
                    shipping: {
                        address_city: data.shipping_address.city,
                        address_state: data.shipping_address.state,
                        address_postcode: data.shipping_address.postal_code,
                        address_country: data.shipping_address.country_code
                    }
                }), data.selected_shipping_option && {
                    selected_shipping_option: {
                        id: data.selected_shipping_option.id,
                        label: data.selected_shipping_option.label,
                        amount: data.selected_shipping_option.amount.value,
                        currency: data.selected_shipping_option.amount.currency_code,
                        type: (_a = data.selected_shipping_option) === null || _a === void 0 ? void 0 : _a.type
                    }
                });
            }
        }]);
        return PaypalWalletService;
    }(WalletService);

    var pure = createCommonjsModule(function (module, exports) {

        Object.defineProperty(exports, '__esModule', {
            value: true
        });
        function _typeof(obj) {
            "@babel/helpers - typeof";

            if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                _typeof = function _typeof(obj) {
                    return typeof obj;
                };
            } else {
                _typeof = function _typeof(obj) {
                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                };
            }
            return _typeof(obj);
        }
        var V3_URL = 'https://js.stripe.com/v3';
        var V3_URL_REGEX = /^https:\/\/js\.stripe\.com\/v3\/?(\?.*)?$/;
        var EXISTING_SCRIPT_MESSAGE = 'loadStripe.setLoadParameters was called but an existing Stripe.js script already exists in the document; existing script parameters will be used';
        var findScript = function findScript() {
            var scripts = document.querySelectorAll("script[src^=\"".concat(V3_URL, "\"]"));
            for (var i = 0; i < scripts.length; i++) {
                var script = scripts[i];
                if (!V3_URL_REGEX.test(script.src)) {
                    continue;
                }
                return script;
            }
            return null;
        };
        var injectScript = function injectScript(params) {
            var queryString = params && !params.advancedFraudSignals ? '?advancedFraudSignals=false' : '';
            var script = document.createElement('script');
            script.src = "".concat(V3_URL).concat(queryString);
            var headOrBody = document.head || document.body;
            if (!headOrBody) {
                throw new Error('Expected document.body not to be null. Stripe.js requires a <body> element.');
            }
            headOrBody.appendChild(script);
            return script;
        };
        var registerWrapper = function registerWrapper(stripe, startTime) {
            if (!stripe || !stripe._registerWrapper) {
                return;
            }
            stripe._registerWrapper({
                name: 'stripe-js',
                version: "1.11.0",
                startTime: startTime
            });
        };
        var stripePromise = null;
        var loadScript = function loadScript(params) {
            // Ensure that we only attempt to load Stripe.js at most once
            if (stripePromise !== null) {
                return stripePromise;
            }
            stripePromise = new Promise(function (resolve, reject) {
                if (typeof window === 'undefined') {
                    // Resolve to null when imported server side. This makes the module
                    // safe to import in an isomorphic code base.
                    resolve(null);
                    return;
                }
                if (window.Stripe && params) {
                    console.warn(EXISTING_SCRIPT_MESSAGE);
                }
                if (window.Stripe) {
                    resolve(window.Stripe);
                    return;
                }
                try {
                    var script = findScript();
                    if (script && params) {
                        console.warn(EXISTING_SCRIPT_MESSAGE);
                    } else if (!script) {
                        script = injectScript(params);
                    }
                    script.addEventListener('load', function () {
                        if (window.Stripe) {
                            resolve(window.Stripe);
                        } else {
                            reject(new Error('Stripe.js not available'));
                        }
                    });
                    script.addEventListener('error', function () {
                        reject(new Error('Failed to load Stripe.js'));
                    });
                } catch (error) {
                    reject(error);
                    return;
                }
            });
            return stripePromise;
        };
        var initStripe = function initStripe(maybeStripe, args, startTime) {
            if (maybeStripe === null) {
                return null;
            }
            var stripe = maybeStripe.apply(undefined, args);
            registerWrapper(stripe, startTime);
            return stripe;
        };
        var validateLoadParams = function validateLoadParams(params) {
            var errorMessage = "invalid load parameters; expected object of shape\n\n    {advancedFraudSignals: boolean}\n\nbut received\n\n    ".concat(JSON.stringify(params), "\n");
            if (params === null || _typeof(params) !== 'object') {
                throw new Error(errorMessage);
            }
            if (Object.keys(params).length === 1 && typeof params.advancedFraudSignals === 'boolean') {
                return params;
            }
            throw new Error(errorMessage);
        };
        var loadParams;
        var loadStripeCalled = false;
        var loadStripe = function loadStripe() {
            for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                args[_key] = arguments[_key];
            }
            loadStripeCalled = true;
            var startTime = Date.now();
            return loadScript(loadParams).then(function (maybeStripe) {
                return initStripe(maybeStripe, args, startTime);
            });
        };
        loadStripe.setLoadParameters = function (params) {
            if (loadStripeCalled) {
                throw new Error('You cannot change load parameters after calling loadStripe');
            }
            loadParams = validateLoadParams(params);
        };
        exports.loadStripe = loadStripe;
    });
    unwrapExports(pure);
    var pure_1 = pure.loadStripe;

    var pure$1 = pure;
    var pure_1$1 = pure$1.loadStripe;

    var UI_COMPLETION_STATE = {
        SUCCESS: "success",
        FAIL: "fail"
    };
    var StripeWalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(StripeWalletService, _WalletService);
        var _super = _createSuper(StripeWalletService);
        function StripeWalletService() {
            _classCallCheck(this, StripeWalletService);
            return _super.apply(this, arguments);
        }
        _createClass(StripeWalletService, [{
            key: "initPaymentRequest",
            value: function initPaymentRequest() {
                return this.stripe.paymentRequest({
                    country: this.meta.country.toUpperCase(),
                    currency: this.meta.currency.toLowerCase(),
                    total: {
                        label: this.meta.amount_label,
                        amount: Math.floor(this.meta.amount * 100)
                    },
                    requestPayerName: this.meta.request_payer_name === true,
                    requestPayerEmail: this.meta.request_payer_email === true,
                    requestPayerPhone: this.meta.request_payer_phone === true
                });
            }
        }, {
            key: "createWalletButton",
            value: function createWalletButton() {
                return this.stripe.elements().create("paymentRequestButton", {
                    paymentRequest: this.paymentRequest
                });
            }
        }, {
            key: "load",
            value: function load(container) {
                var _this = this;
                return pure_1$1(this.publicKey).then(function (stripe) {
                    _this.stripe = stripe;
                    _this.paymentRequest = _this.initPaymentRequest();
                }).then(function () {
                    return _this.checkAvailability();
                }).then(function (availability) {
                    return _this.mount(container, availability);
                }).then(function () {
                    return _this.setOnPaymentMethodSelected();
                });
            }
        }, {
            key: "checkAvailability",
            value: function checkAvailability() {
                var _this2 = this;
                return this.paymentRequest.canMakePayment().then(function (available) {
                    if (available) {
                        var gpay_enabled = !_this2.meta.wallets || _this2.meta.wallets.includes(WALLET_TYPE.GOOGLE);
                        var applepay_enabled = !_this2.meta.wallets || _this2.meta.wallets.includes(WALLET_TYPE.APPLE);
                        // TODO: this isn't accurate. Discard Chrome saved cards and microsoft pay payment to show google pay button
                        return {
                            google_pay: gpay_enabled && !available.applePay,
                            apple_pay: applepay_enabled && available.applePay,
                            flypay: false
                        };
                    }
                });
            }
        }, {
            key: "mount",
            value: function mount(container, availability) {
                if (!availability || !availability.apple_pay && !availability.google_pay) return this.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, null);
                this.createWalletButton().mount(container.getElement());
            }
        }, {
            key: "setOnPaymentMethodSelected",
            value: function setOnPaymentMethodSelected() {
                var _this3 = this;
                this.paymentRequest.on("paymentmethod", function (event) {
                    var _a, _b;
                    var _event$paymentMethod = event.paymentMethod,
                        id = _event$paymentMethod.id,
                        card = _event$paymentMethod.card,
                        _event$paymentMethod$ = _event$paymentMethod.billing_details,
                        name = _event$paymentMethod$.name,
                        address = _event$paymentMethod$.address;
                    var data = {
                        payment_method_id: id,
                        customer: {
                            payer_name: event.payerName,
                            payer_email: event.payerEmail,
                            payer_phone: event.payerPhone,
                            payment_source: {
                                wallet_type: _this3.getWalletType((_a = card === null || card === void 0 ? void 0 : card.wallet) === null || _a === void 0 ? void 0 : _a.type),
                                card_name: name,
                                type: (_b = card === null || card === void 0 ? void 0 : card.wallet) === null || _b === void 0 ? void 0 : _b.type,
                                card_scheme: card === null || card === void 0 ? void 0 : card.brand,
                                card_number_last4: card === null || card === void 0 ? void 0 : card.last4,
                                expire_month: card === null || card === void 0 ? void 0 : card.exp_month,
                                expire_year: card === null || card === void 0 ? void 0 : card.exp_year,
                                address_line1: address.line1,
                                address_line2: address.line2,
                                address_city: address.city,
                                address_postcode: address.postal_code,
                                address_state: address.state,
                                address_country: address.country
                            }
                        }
                    };
                    _this3.eventEmitter.emit(WALLET_EVENT.PAYMENT_METHOD_SELECTED, {
                        data: data,
                        onSuccess: function onSuccess() {
                            return event.complete(UI_COMPLETION_STATE.SUCCESS);
                        },
                        onError: function onError(err) {
                            return event.complete(UI_COMPLETION_STATE.FAIL);
                        }
                    });
                });
            }
        }, {
            key: "getWalletType",
            value: function getWalletType(type) {
                if (!type) return null;
                return type === 'google_pay' ? WALLET_TYPE.GOOGLE : WALLET_TYPE.APPLE;
            }
        }]);
        return StripeWalletService;
    }(WalletService);

    var AppleWalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(AppleWalletService, _WalletService);
        var _super = _createSuper(AppleWalletService);
        function AppleWalletService(publicKey, meta, gatewayName, eventEmitter) {
            var _this;
            _classCallCheck(this, AppleWalletService);
            _this = _super.call(this, publicKey, meta);
            _this.gatewayName = gatewayName;
            _this.eventEmitter = eventEmitter;
            _this.latestShippingData = {};
            _this.onValidateMerchant = function (event) {
                _this.getMerchantSession().then(function (merchantSession) {
                    _this.paymentSession.completeMerchantValidation(merchantSession);
                })["catch"](function (err) {
                    return console.error("Error fetching merchant session", err);
                });
            };
            _this.onPaymentAuthorized = function (event) {
                var _a;
                var _event$payment = event.payment,
                    token = _event$payment.token,
                    billingContact = _event$payment.billingContact,
                    shippingContact = _event$payment.shippingContact;
                _this.latestShippingData.shippingContact = shippingContact;
                var shippingOptionMethod = (_a = _this.selectedShippingOption) === null || _a === void 0 ? void 0 : _a.type;
                var cardNameFromShippingContact = [shippingContact === null || shippingContact === void 0 ? void 0 : shippingContact.givenName, shippingContact === null || shippingContact === void 0 ? void 0 : shippingContact.familyName].join(' ').trim();
                _this.eventEmitter.emit(WALLET_EVENT.PAYMENT_METHOD_SELECTED, {
                    data: _extends({
                        customer: {
                            payment_source: _extends(_extends({
                                wallet_type: WALLET_TYPE.APPLE
                            }, cardNameFromShippingContact && {
                                card_name: cardNameFromShippingContact
                            }), {
                                type: token.paymentMethod.type,
                                card_scheme: token.paymentMethod.network,
                                address_line1: billingContact === null || billingContact === void 0 ? void 0 : billingContact.addressLines[0],
                                address_line2: billingContact === null || billingContact === void 0 ? void 0 : billingContact.addressLines[1],
                                address_country: billingContact === null || billingContact === void 0 ? void 0 : billingContact.countryCode,
                                address_city: billingContact === null || billingContact === void 0 ? void 0 : billingContact.locality,
                                address_postcode: billingContact === null || billingContact === void 0 ? void 0 : billingContact.postalCode,
                                address_state: billingContact === null || billingContact === void 0 ? void 0 : billingContact.administrativeArea,
                                ref_token: token.paymentData ? JSON.stringify(token.paymentData) : ''
                            })
                        }
                    }, _this.meta.request_shipping && shippingContact && {
                        shipping: _extends(_extends(_extends({}, shippingOptionMethod && {
                            method: shippingOptionMethod
                        }), _this.hasShippingOptions() && {
                            options: _this.meta.shipping_options
                        }), {
                            address_line1: shippingContact.addressLines[0],
                            address_line2: shippingContact.addressLines[1],
                            address_country: shippingContact.countryCode,
                            address_city: shippingContact.locality,
                            address_postcode: shippingContact.postalCode,
                            address_state: shippingContact.administrativeArea,
                            contact: {
                                first_name: shippingContact.givenName,
                                last_name: shippingContact.familyName,
                                email: shippingContact.emailAddress,
                                phone: shippingContact.phoneNumber
                            }
                        })
                    }),
                    onSuccess: function onSuccess() {
                        return _this.paymentSession.completePayment(ApplePaySession.STATUS_SUCCESS);
                    },
                    onError: function onError() {
                        return _this.paymentSession.completePayment(ApplePaySession.STATUS_FAILURE);
                    }
                });
            };
            _this.onShippingContactSelected = function (event) {
                _this.latestShippingData.shippingContact = event.shippingContact;
                var parsedCallbackData = _this.parseUpdateData(_this.latestShippingData);
                _this.eventEmitter.emit(WALLET_EVENT.UPDATE, parsedCallbackData);
                return new Promise(function (res, rej) {
                    _this.latestShippingChangePromiseResolve = res;
                    _this.latestShippingChangePromiseReject = rej;
                });
            };
            _this.onShippingMethodSelected = function (event) {
                var _a, _b;
                _this.latestShippingData.shippingMethod = event.shippingMethod;
                var update = {
                    newTotal: {
                        label: _this.meta.amount_label || ((_b = (_a = _this.getMetaRawDataInitialization()) === null || _a === void 0 ? void 0 : _a.total) === null || _b === void 0 ? void 0 : _b.label),
                        amount: _this.meta.amount.toString(),
                        type: "final"
                    }
                };
                _this.paymentSession.completeShippingMethodSelection(update);
            };
            _this.parseUpdateData = function (data) {
                var _a, _b, _c, _d, _e, _f, _g, _h;
                // From Apple docs (https://developer.apple.com/documentation/apple_pay_on_the_web/applepaypayment/1916097-shippingcontact):
                // Before the user authorizes the transaction with Touch ID, Face ID, or passcode, you receive redacted address information
                return _extends({
                    shipping: {
                        address_city: (_a = data === null || data === void 0 ? void 0 : data.shippingContact) === null || _a === void 0 ? void 0 : _a.locality,
                        address_state: (_b = data === null || data === void 0 ? void 0 : data.shippingContact) === null || _b === void 0 ? void 0 : _b.administrativeArea,
                        address_postcode: (_c = data === null || data === void 0 ? void 0 : data.shippingContact) === null || _c === void 0 ? void 0 : _c.postalCode,
                        address_country: (_d = data === null || data === void 0 ? void 0 : data.shippingContact) === null || _d === void 0 ? void 0 : _d.countryCode
                    }
                }, (data === null || data === void 0 ? void 0 : data.shippingMethod) && {
                    selected_shipping_option: {
                        id: (_e = data === null || data === void 0 ? void 0 : data.shippingMethod) === null || _e === void 0 ? void 0 : _e.identifier,
                        label: (_f = data === null || data === void 0 ? void 0 : data.shippingMethod) === null || _f === void 0 ? void 0 : _f.label,
                        detail: (_g = data === null || data === void 0 ? void 0 : data.shippingMethod) === null || _g === void 0 ? void 0 : _g.detail,
                        amount: (_h = data === null || data === void 0 ? void 0 : data.shippingMethod) === null || _h === void 0 ? void 0 : _h.amount
                    }
                });
            };
            _this.formatShippingOptions = function (shipping_options) {
                return shipping_options.map(function (o) {
                    return {
                        identifier: o.id,
                        label: o.label,
                        detail: (o === null || o === void 0 ? void 0 : o.detail) || '',
                        amount: o.amount
                    };
                });
            };
            _this.eventEmitter = eventEmitter;
            return _this;
        }
        _createClass(AppleWalletService, [{
            key: "getGatewayName",
            value: function getGatewayName() {
                return this.gatewayName;
            }
        }, {
            key: "getMerchantId",
            value: function getMerchantId() {
                var _a, _b, _c;
                return ((_c = (_b = (_a = this.meta) === null || _a === void 0 ? void 0 : _a.credentials) === null || _b === void 0 ? void 0 : _b[WALLET_TYPE.APPLE]) === null || _c === void 0 ? void 0 : _c.merchant) || '';
            }
        }, {
            key: "getMetaStyles",
            value: function getMetaStyles() {
                var _a, _b, _c;
                if (((_a = this.meta) === null || _a === void 0 ? void 0 : _a.style) && _typeof((_b = this.meta) === null || _b === void 0 ? void 0 : _b.style) === 'object') {
                    var metaStyles = JSON.parse(JSON.stringify((_c = this.meta) === null || _c === void 0 ? void 0 : _c.style));
                    if ('google' in metaStyles) metaStyles === null || metaStyles === void 0 ? true : delete metaStyles.google; // to offer backward compatibility
                    if ('apple' in metaStyles) return metaStyles === null || metaStyles === void 0 ? void 0 : metaStyles.apple;else return metaStyles; // to offer backward compatibility
                } else {
                    return null;
                }
            }
        }, {
            key: "getMetaRawDataInitialization",
            value: function getMetaRawDataInitialization() {
                var _a, _b, _c, _d;
                if (((_a = this.meta) === null || _a === void 0 ? void 0 : _a.raw_data_initialization) && ((_b = this.meta) === null || _b === void 0 ? void 0 : _b.raw_data_initialization) && _typeof((_c = this.meta) === null || _c === void 0 ? void 0 : _c.raw_data_initialization) === 'object') {
                    var metaRawDataInit = JSON.parse(JSON.stringify((_d = this.meta) === null || _d === void 0 ? void 0 : _d.raw_data_initialization));
                    if ('google' in metaRawDataInit) metaRawDataInit === null || metaRawDataInit === void 0 ? true : delete metaRawDataInit.google; // to offer backward compatibility
                    if ('apple' in metaRawDataInit) return metaRawDataInit === null || metaRawDataInit === void 0 ? void 0 : metaRawDataInit.apple;else return metaRawDataInit; // to offer backward compatibility
                } else {
                    return null;
                }
            }
        }, {
            key: "isShippingRequired",
            value: function isShippingRequired() {
                var _a;
                return (_a = this.meta) === null || _a === void 0 ? void 0 : _a.request_shipping;
            }
        }, {
            key: "hasShippingOptions",
            value: function hasShippingOptions() {
                var _a, _b;
                return ((_a = this.meta) === null || _a === void 0 ? void 0 : _a.request_shipping) && !!((_b = this.meta) === null || _b === void 0 ? void 0 : _b.shipping_options);
            }
        }, {
            key: "load",
            value: function load(container) {
                var _this2 = this;
                if (!window.Promise) {
                    // Given that this library does not rely in any polyfill for promises, and this integration depends on them, we early return if Promises are not supported for the browser (like I.E. 11).
                    this.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {
                        wallet: WALLET_TYPE.APPLE
                    });
                    return;
                }
                return this.checkAvailability().then(function (available) {
                    var _a;
                    if (!available) {
                        _this2.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {
                            wallet: WALLET_TYPE.APPLE
                        });
                        return;
                    }
                    // Store default shipping option
                    if (_this2.isShippingRequired() && _this2.hasShippingOptions()) {
                        _this2.selectedShippingOption = (_a = _this2.meta) === null || _a === void 0 ? void 0 : _a.shipping_options[0];
                        _this2.latestShippingData.shippingMethod = _this2.formatShippingOptions([_this2.selectedShippingOption])[0];
                    }
                    _this2.mount(container);
                })["catch"](function (err) {
                    return console.error("Error checking ApplePay availability", err);
                });
            }
        }, {
            key: "update",
            value: function update(data) {
                var _a, _b, _c;
                if (!this.latestShippingChangePromiseResolve || !this.latestShippingChangePromiseReject) return;
                if (!data.success || !data.body) return this.latestShippingChangePromiseReject(); // TODO: check how to handle Error messages from Merchant at update() callback response
                var newAmount = (_a = data === null || data === void 0 ? void 0 : data.body) === null || _a === void 0 ? void 0 : _a.amount;
                var newShippingOptions = (_b = data === null || data === void 0 ? void 0 : data.body) === null || _b === void 0 ? void 0 : _b.shipping_options;
                if (newAmount) this.meta.amount = newAmount;
                if (newShippingOptions) {
                    this.meta.shipping_options = newShippingOptions;
                    this.selectedShippingOption = newShippingOptions ? newShippingOptions[0] : undefined;
                }
                var update = _extends({
                    newTotal: {
                        label: (_c = this.meta) === null || _c === void 0 ? void 0 : _c.amount_label,
                        amount: this.meta.amount.toString(),
                        type: "final"
                    }
                }, this.isShippingRequired() && this.hasShippingOptions() && {
                    newShippingMethods: this.formatShippingOptions(this.meta.shipping_options)
                });
                this.paymentSession.completeShippingContactSelection(update);
                this.latestShippingChangePromiseResolve({});
            }
        }, {
            key: "checkAvailability",
            value: function checkAvailability() {
                var _this3 = this;
                return new Promise(function (resolve, _reject) {
                    if (!window.ApplePaySession || !ApplePaySession) {
                        resolve(false);
                    }
                    ApplePaySession.canMakePaymentsWithActiveCard(_this3.getMerchantId()).then(function (available) {
                        return resolve(available);
                    })["catch"](function (_err) {
                        return resolve(false);
                    });
                });
            }
        }, {
            key: "mount",
            value: function mount(container) {
                var _this4 = this;
                var style = document.createElement('style');
                style.innerHTML = this.createButtonStyle();
                document.head.appendChild(style);
                var button = document.createElement('div');
                button.onclick = function () {
                    return _this4.onApplePayButtonClicked();
                };
                button.classList.add('paydock-apple-container', 'apple-pay-button', 'apple-pay-button-black');
                button.setAttribute('tabindex', '0');
                button.setAttribute('role', 'button');
                container.getElement().appendChild(button);
            }
        }, {
            key: "onApplePayButtonClicked",
            value: function onApplePayButtonClicked() {
                this.paymentSession = new ApplePaySession(3, this.createRequest());
                this.paymentSession.onvalidatemerchant = this.onValidateMerchant;
                this.paymentSession.onpaymentauthorized = this.onPaymentAuthorized;
                this.paymentSession.onshippingcontactselected = this.onShippingContactSelected;
                this.paymentSession.onshippingmethodselected = this.onShippingMethodSelected;
                this.paymentSession.begin();
            }
        }, {
            key: "createRequest",
            value: function createRequest() {
                // In case Merchants decide to use other values they should provide all ApplePayPaymentRequest fields at "meta.raw_data_initialization".
                // https://developer.apple.com/documentation/apple_pay_on_the_web/applepaypaymentrequest
                var _a;
                var rawDataInitialization = this.getMetaRawDataInitialization();
                if (rawDataInitialization && _typeof(rawDataInitialization) === 'object') {
                    if (_typeof(rawDataInitialization.total) === 'object') rawDataInitialization.total.amount = this.meta.amount.toString();else rawDataInitialization.total = {
                        label: ((_a = this.meta) === null || _a === void 0 ? void 0 : _a.amount_label) || '',
                        amount: this.meta.amount.toString()
                    };
                    if (this.isShippingRequired() && this.hasShippingOptions()) rawDataInitialization.shippingMethods = this.formatShippingOptions(this.meta.shipping_options);
                }
                return rawDataInitialization || _extends(_extends(_extends({
                    countryCode: this.meta.country.toUpperCase(),
                    currencyCode: this.meta.currency.toUpperCase(),
                    merchantCapabilities: ["supports3DS", "supportsCredit", "supportsDebit"],
                    supportedNetworks: ["visa", "masterCard", "amex", "discover"]
                }, this.meta.show_billing_address && {
                    requiredBillingContactFields: ["name", "postalAddress"]
                }), this.isShippingRequired() && _extends({
                    requiredShippingContactFields: ["postalAddress", "name", "phone", "email"]
                }, this.hasShippingOptions() && {
                    shippingMethods: this.formatShippingOptions(this.meta.shipping_options)
                })), {
                    total: {
                        label: this.meta.amount_label,
                        amount: this.meta.amount.toString(),
                        type: "final"
                    }
                });
            }
        }, {
            key: "getMerchantSession",
            value: function getMerchantSession() {
                var _this5 = this;
                return new Promise(function (resolve, reject) {
                    return _this5.eventEmitter.emit(WALLET_EVENT.CALLBACK, {
                        data: _extends({
                            request_type: "CREATE_SESSION",
                            wallet_type: WALLET_TYPE.APPLE,
                            session_id: window.location.hostname
                        }, _this5.isShippingRequired() && {
                            request_shipping: _this5.meta.request_shipping
                        }),
                        onSuccess: function onSuccess(res) {
                            return resolve(res);
                        },
                        onError: function onError(message) {
                            return reject(message);
                        }
                    });
                });
            }
        }, {
            key: "createButtonStyle",
            value: function createButtonStyle() {
                var _a, _b;
                return "\n            .paydock-apple-container {\n                width: 100%;\n                height: 40px;\n            }\n\n            @supports (-webkit-appearance: -apple-pay-button) {\n                .apple-pay-button {\n                    display: inline-block;\n                    -webkit-appearance: -apple-pay-button;\n                    -apple-pay-button-type: ".concat(((_a = this.getMetaStyles()) === null || _a === void 0 ? void 0 : _a.button_type) || 'plain', "\n                }\n                .apple-pay-button-black {\n                    -apple-pay-button-style: black;\n                }\n                .apple-pay-button-white {\n                    -apple-pay-button-style: white;\n                }\n                .apple-pay-button-white-with-line {\n                    -apple-pay-button-style: white-outline;\n                }\n            }\n\n            @supports not (-webkit-appearance: -apple-pay-button) {\n                .apple-pay-button {\n                    display: inline-block;\n                    background-size: 100% 60%;\n                    background-repeat: no-repeat;\n                    background-position: 50% 50%;\n                    border-radius: 5px;\n                    padding: 0px;\n                    box-sizing: border-box;\n                    min-width: 200px;\n                    min-height: 32px;\n                    max-height: 64px;\n                    -apple-pay-button-type: ").concat(((_b = this.getMetaStyles()) === null || _b === void 0 ? void 0 : _b.button_type) || 'plain', "\n                }\n                .apple-pay-button-black {\n                    background-image: -webkit-named-image(apple-pay-logo-white);\n                    background-color: black;\n                }\n                .apple-pay-button-white {\n                    background-image: -webkit-named-image(apple-pay-logo-black);\n                    background-color: white;\n                }\n                .apple-pay-button-white-with-line {\n                    background-image: -webkit-named-image(apple-pay-logo-black);\n                    background-color: white;\n                    border: .5px solid black;\n                }\n            }\n        ");
            }
        }]);
        return AppleWalletService;
    }(WalletService);

    var GoogleWalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(GoogleWalletService, _WalletService);
        var _super = _createSuper(GoogleWalletService);
        function GoogleWalletService(publicKey, meta, gatewayName, eventEmitter) {
            var _this;
            _classCallCheck(this, GoogleWalletService);
            _this = _super.call(this, publicKey, meta);
            _this.gatewayName = gatewayName;
            _this.eventEmitter = eventEmitter;
            _this.parseUpdateData = function (data) {
                var _a, _b, _c, _d, _e, _f;
                var shippingOption = (_b = (_a = _this.meta) === null || _a === void 0 ? void 0 : _a.shipping_options) === null || _b === void 0 ? void 0 : _b.find(function (o) {
                    var _a;
                    return o.id === ((_a = data === null || data === void 0 ? void 0 : data.shippingOptionData) === null || _a === void 0 ? void 0 : _a.id);
                });
                return _extends({
                    shipping: {
                        address_city: (_c = data.shippingAddress) === null || _c === void 0 ? void 0 : _c.locality,
                        address_state: (_d = data.shippingAddress) === null || _d === void 0 ? void 0 : _d.administrativeArea,
                        address_postcode: (_e = data === null || data === void 0 ? void 0 : data.shippingAddress) === null || _e === void 0 ? void 0 : _e.postalCode,
                        address_country: (_f = data === null || data === void 0 ? void 0 : data.shippingAddress) === null || _f === void 0 ? void 0 : _f.countryCode
                    }
                }, shippingOption && {
                    selected_shipping_option: {
                        id: shippingOption === null || shippingOption === void 0 ? void 0 : shippingOption.id,
                        label: shippingOption === null || shippingOption === void 0 ? void 0 : shippingOption.label,
                        detail: shippingOption === null || shippingOption === void 0 ? void 0 : shippingOption.detail,
                        type: shippingOption === null || shippingOption === void 0 ? void 0 : shippingOption.type
                    }
                });
            };
            _this.formatShippingOptions = function (shipping_options) {
                return shipping_options.map(function (option) {
                    return {
                        id: option.id,
                        label: option.label,
                        description: (option === null || option === void 0 ? void 0 : option.detail) || ''
                    };
                });
            };
            _this.eventEmitter = eventEmitter;
            return _this;
        }
        _createClass(GoogleWalletService, [{
            key: "getGatewayName",
            value: function getGatewayName() {
                return this.gatewayName;
            }
        }, {
            key: "getMerchantId",
            value: function getMerchantId() {
                var _a, _b, _c;
                return (_c = (_b = (_a = this.meta) === null || _a === void 0 ? void 0 : _a.credentials) === null || _b === void 0 ? void 0 : _b[WALLET_TYPE.GOOGLE]) === null || _c === void 0 ? void 0 : _c.merchant;
            }
        }, {
            key: "getMetaStyles",
            value: function getMetaStyles() {
                var _a, _b, _c, _d;
                if (_typeof((_a = this.meta) === null || _a === void 0 ? void 0 : _a.style) === 'object' && 'google' in ((_b = this.meta) === null || _b === void 0 ? void 0 : _b.style)) return (_d = (_c = this.meta) === null || _c === void 0 ? void 0 : _c.style) === null || _d === void 0 ? void 0 : _d.google;else return null;
            }
        }, {
            key: "getMetaRawDataInitialization",
            value: function getMetaRawDataInitialization() {
                var _a, _b, _c, _d, _e;
                if (((_a = this.meta) === null || _a === void 0 ? void 0 : _a.raw_data_initialization) && _typeof((_b = this.meta) === null || _b === void 0 ? void 0 : _b.raw_data_initialization) === 'object' && 'google' in ((_c = this.meta) === null || _c === void 0 ? void 0 : _c.raw_data_initialization)) return (_e = (_d = this.meta) === null || _d === void 0 ? void 0 : _d.raw_data_initialization) === null || _e === void 0 ? void 0 : _e.google;else return null;
            }
        }, {
            key: "isShippingRequired",
            value: function isShippingRequired() {
                return !!this.meta.request_shipping;
            }
        }, {
            key: "hasShippingOptions",
            value: function hasShippingOptions() {
                return this.meta.request_shipping && !!this.meta.shipping_options;
            }
        }, {
            key: "load",
            value: function load(container) {
                var _this2 = this;
                if (!window.Promise) {
                    // Given that this library does not rely in any polyfill for promises, and this integration depends on them, we early return if Promises are not supported for the browser (like I.E. 11).
                    this.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {
                        wallet: WALLET_TYPE.GOOGLE
                    });
                    return;
                }
                return new Promise(function (resolve, reject) {
                    var googlePayJs = document.createElement("script");
                    googlePayJs.type = "text/javascript";
                    googlePayJs.src = "https://pay.google.com/gp/p/js/pay.js";
                    googlePayJs.async = true;
                    googlePayJs.onload = function () {
                        var _a, _b, _c, _d;
                        if (!window.google) {
                            _this2.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {
                                wallet: WALLET_TYPE.GOOGLE
                            });
                            reject();
                            return;
                        }
                        // Store default shipping option
                        if (_this2.isShippingRequired() && _this2.hasShippingOptions()) {
                            _this2.selectedShippingOption = (_a = _this2.meta) === null || _a === void 0 ? void 0 : _a.shipping_options[0];
                        }
                        _this2.paymentsClient = new google.payments.api.PaymentsClient(_extends({
                            merchantInfo: _extends(_extends({}, ((_b = _this2.meta) === null || _b === void 0 ? void 0 : _b.merchant_name) ? {
                                merchantName: (_c = _this2.meta) === null || _c === void 0 ? void 0 : _c.merchant_name
                            } : {}), {
                                merchantId: _this2.getMerchantId()
                            }),
                            paymentDataCallbacks: _extends({
                                onPaymentAuthorized: function onPaymentAuthorized(paymentData) {
                                    return _this2.onPaymentAuthorized(paymentData);
                                }
                            }, _this2.isShippingRequired() && {
                                onPaymentDataChanged: function onPaymentDataChanged(intermediatePaymentData) {
                                    return _this2.onPaymentDataChanged(intermediatePaymentData);
                                }
                            })
                        }, ((_d = _this2.meta) === null || _d === void 0 ? void 0 : _d.gateway_mode) === 'live' ? {
                            environment: "PRODUCTION"
                        } : {
                            environment: "TEST"
                        }));
                        _this2.checkAvailability().then(function (available) {
                            if (!available) {
                                _this2.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {
                                    wallet: WALLET_TYPE.GOOGLE
                                });
                                reject();
                                return;
                            }
                            _this2.mount(container);
                            resolve();
                        });
                    };
                    document.head.appendChild(googlePayJs);
                });
            }
        }, {
            key: "update",
            value: function update(data) {
                var _a, _b, _c, _d;
                if (!this.latestShippingChangePromiseResolve || !this.latestShippingChangePromiseReject) return;
                if (!data.success) return this.latestShippingChangePromiseReject(); // TODO: check how to handle Error messages from Merchant at update() callback response
                var newAmount = ((_a = data === null || data === void 0 ? void 0 : data.body) === null || _a === void 0 ? void 0 : _a.amount) || this.meta.amount;
                var newShippingOptions = (_b = data === null || data === void 0 ? void 0 : data.body) === null || _b === void 0 ? void 0 : _b.shipping_options;
                if (newAmount) this.meta.amount = newAmount;
                if (newShippingOptions) {
                    this.meta.shipping_options = newShippingOptions;
                    this.selectedShippingOption = newShippingOptions ? newShippingOptions[0] : undefined;
                }
                var paymentDataRequestUpdate = _extends({
                    newTransactionInfo: {
                        totalPriceStatus: "FINAL",
                        totalPriceLabel: this.meta.amount_label,
                        totalPrice: (_c = this.meta.amount) === null || _c === void 0 ? void 0 : _c.toString(),
                        currencyCode: this.meta.currency.toUpperCase(),
                        countryCode: this.meta.country.toUpperCase()
                    }
                }, this.isShippingRequired() && this.hasShippingOptions() && newShippingOptions && {
                    newShippingOptionParameters: {
                        defaultSelectedOptionId: (_d = this.selectedShippingOption) === null || _d === void 0 ? void 0 : _d.id,
                        shippingOptions: this.formatShippingOptions(this.meta.shipping_options)
                    }
                });
                this.latestShippingChangePromiseResolve(paymentDataRequestUpdate);
            }
        }, {
            key: "checkAvailability",
            value: function checkAvailability() {
                return this.paymentsClient.isReadyToPay(this.createRequest()).then(function (response) {
                    return !!response.result;
                })["catch"](function (err) {
                    console.error("Error checking GooglePay availability", err);
                    return false;
                });
            }
        }, {
            key: "mount",
            value: function mount(container) {
                var _this3 = this;
                var _a, _b, _c;
                container.getElement().appendChild(this.paymentsClient.createButton({
                    onClick: function onClick() {
                        return _this3.loadPaymentData();
                    },
                    buttonType: ((_a = this.getMetaStyles()) === null || _a === void 0 ? void 0 : _a.button_type) || 'pay',
                    buttonSizeMode: ((_b = this.getMetaStyles()) === null || _b === void 0 ? void 0 : _b.button_size_mode) || "fill",
                    buttonColor: ((_c = this.getMetaStyles()) === null || _c === void 0 ? void 0 : _c.button_color) || "default"
                }));
            }
        }, {
            key: "loadPaymentData",
            value: function loadPaymentData() {
                this.paymentsClient.loadPaymentData(this.createPaymentDataRequest())
                    // .then((paymentData) => {
                    //     // if using gateway tokenization, pass this token without modification
                    //     // this.paymentToken = paymentData.paymentMethodData.tokenizationData.token;
                    // })
                    ["catch"](function () {
                    console.error('Error while loading payment data');
                });
            }
        }, {
            key: "onPaymentAuthorized",
            value: function onPaymentAuthorized(paymentData) {
                var _this4 = this;
                var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o, _p, _q;
                var billingAddressLine1 = (_c = (_b = (_a = paymentData.paymentMethodData) === null || _a === void 0 ? void 0 : _a.info) === null || _b === void 0 ? void 0 : _b.billingAddress) === null || _c === void 0 ? void 0 : _c.address1;
                var billingAddressLine2 = (_f = (_e = (_d = paymentData.paymentMethodData) === null || _d === void 0 ? void 0 : _d.info) === null || _e === void 0 ? void 0 : _e.billingAddress) === null || _f === void 0 ? void 0 : _f.address2;
                var shippingAddressLine1 = (_g = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _g === void 0 ? void 0 : _g.address1;
                var shippingAddressLine2 = (_h = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _h === void 0 ? void 0 : _h.address2;
                var shippingOptionMethod = (_j = this.selectedShippingOption) === null || _j === void 0 ? void 0 : _j.type;
                var shippingAddressCountry = (_k = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _k === void 0 ? void 0 : _k.countryCode;
                var shippingAddressCity = (_l = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _l === void 0 ? void 0 : _l.locality;
                var shippingAddressPostCode = (_m = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _m === void 0 ? void 0 : _m.postalCode;
                var shippingAddressState = (_o = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _o === void 0 ? void 0 : _o.administrativeArea;
                var shippingContactFirstName = (_p = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _p === void 0 ? void 0 : _p.name;
                var shippingContactEmail = paymentData === null || paymentData === void 0 ? void 0 : paymentData.email;
                var shippingContactPhone = (_q = paymentData === null || paymentData === void 0 ? void 0 : paymentData.shippingAddress) === null || _q === void 0 ? void 0 : _q.phoneNumber;
                return new Promise(function (resolve) {
                    var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o, _p;
                    return _this4.eventEmitter.emit(WALLET_EVENT.PAYMENT_METHOD_SELECTED, {
                        data: _extends({
                            customer: {
                                payment_source: _extends(_extends(_extends(_extends({
                                    wallet_type: WALLET_TYPE.GOOGLE,
                                    // card_name: paymentData.shippingAddress?.name, // TODO: Do we want to use this value? Point 2 at https://paydock.atlassian.net/browse/P2-7209?focusedCommentId=60202
                                    type: paymentData.paymentMethodData.type,
                                    card_scheme: (_b = (_a = paymentData.paymentMethodData) === null || _a === void 0 ? void 0 : _a.info) === null || _b === void 0 ? void 0 : _b.cardNetwork
                                }, billingAddressLine1 && {
                                    address_line1: billingAddressLine1
                                }), billingAddressLine2 && {
                                    address_line2: billingAddressLine2
                                }), billingAddressLine2 && {
                                    address_line2: billingAddressLine2
                                }), {
                                    address_country: (_e = (_d = (_c = paymentData.paymentMethodData) === null || _c === void 0 ? void 0 : _c.info) === null || _d === void 0 ? void 0 : _d.billingAddress) === null || _e === void 0 ? void 0 : _e.countryCode,
                                    address_city: (_h = (_g = (_f = paymentData.paymentMethodData) === null || _f === void 0 ? void 0 : _f.info) === null || _g === void 0 ? void 0 : _g.billingAddress) === null || _h === void 0 ? void 0 : _h.locality,
                                    address_postcode: (_l = (_k = (_j = paymentData.paymentMethodData) === null || _j === void 0 ? void 0 : _j.info) === null || _k === void 0 ? void 0 : _k.billingAddress) === null || _l === void 0 ? void 0 : _l.postalCode,
                                    address_state: (_p = (_o = (_m = paymentData.paymentMethodData) === null || _m === void 0 ? void 0 : _m.info) === null || _o === void 0 ? void 0 : _o.billingAddress) === null || _p === void 0 ? void 0 : _p.administrativeArea,
                                    ref_token: paymentData.paymentMethodData.tokenizationData.token
                                })
                            }
                        }, _this4.isShippingRequired() && {
                            shipping: _extends(_extends(_extends(_extends(_extends(_extends(_extends(_extends(_extends({}, shippingOptionMethod && {
                                method: shippingOptionMethod
                            }), _this4.hasShippingOptions() && {
                                options: _this4.meta.shipping_options
                            }), shippingAddressLine1 && {
                                address_line1: shippingAddressLine1
                            }), shippingAddressLine2 && {
                                address_line2: shippingAddressLine2
                            }), shippingAddressCountry && {
                                address_country: shippingAddressCountry
                            }), shippingAddressCity && {
                                address_city: shippingAddressCity
                            }), shippingAddressPostCode && {
                                address_postcode: shippingAddressPostCode
                            }), shippingAddressState && {
                                address_state: shippingAddressState
                            }), {
                                contact: _extends(_extends(_extends({}, shippingContactFirstName && {
                                    first_name: shippingContactFirstName
                                }), shippingContactEmail && {
                                    email: shippingContactEmail
                                }), shippingContactPhone && {
                                    phone: shippingContactPhone
                                })
                            })
                        }),
                        onSuccess: function onSuccess() {
                            return resolve({
                                transactionState: 'SUCCESS'
                            });
                        },
                        onError: function onError(error) {
                            return resolve({
                                transactionState: 'ERROR',
                                error: {
                                    intent: 'PAYMENT_AUTHORIZATION',
                                    message: (error === null || error === void 0 ? void 0 : error.message) || 'Error processing payment',
                                    reason: 'PAYMENT_DATA_INVALID'
                                }
                            });
                        }
                    });
                });
            }
        }, {
            key: "onPaymentDataChanged",
            value: function onPaymentDataChanged(intermediatePaymentData) {
                var _this5 = this;
                if (!this.isShippingRequired()) return;
                var parsedUpdateData = this.parseUpdateData(intermediatePaymentData);
                var returnPromise = new Promise(function (res, rej) {
                    _this5.latestShippingChangePromiseResolve = res;
                    _this5.latestShippingChangePromiseReject = rej;
                });
                this.eventEmitter.emit(WALLET_EVENT.UPDATE, parsedUpdateData);
                return returnPromise;
            }
        }, {
            key: "createRequest",
            value: function createRequest() {
                var isSafariBrowser = Browser.getBrowserName() === 'Apple Safari';
                return {
                    apiVersion: 2,
                    apiVersionMinor: 0,
                    allowedPaymentMethods: [this.createCardData()],
                    existingPaymentMethodRequired: !isSafariBrowser
                };
            }
        }, {
            key: "createPaymentDataRequest",
            value: function createPaymentDataRequest() {
                var _a, _b, _c, _d;
                // Store default shipping option
                if (this.isShippingRequired() && this.hasShippingOptions()) {
                    this.selectedShippingOption = (_a = this.meta) === null || _a === void 0 ? void 0 : _a.shipping_options[0];
                }
                var gateway = 'paydock';
                var gatewayMerchantId = this.getMerchantId();
                return _extends({
                    apiVersion: 2,
                    apiVersionMinor: 0,
                    allowedPaymentMethods: [_extends(_extends({}, this.createCardData()), {
                        tokenizationSpecification: {
                            type: "PAYMENT_GATEWAY",
                            parameters: {
                                gateway: gateway,
                                gatewayMerchantId: gatewayMerchantId
                            }
                        }
                    })],
                    transactionInfo: {
                        totalPriceStatus: "FINAL",
                        totalPriceLabel: this.meta.amount_label,
                        totalPrice: this.meta.amount.toString(),
                        currencyCode: this.meta.currency.toUpperCase(),
                        countryCode: this.meta.country.toUpperCase()
                    },
                    merchantInfo: _extends(_extends({}, ((_b = this.meta) === null || _b === void 0 ? void 0 : _b.merchant_name) ? {
                        merchantName: (_c = this.meta) === null || _c === void 0 ? void 0 : _c.merchant_name
                    } : {}), {
                        merchantId: gatewayMerchantId
                    }),
                    callbackIntents: ["PAYMENT_AUTHORIZATION"].concat(_toConsumableArray(this.isShippingRequired() ? ["SHIPPING_ADDRESS"] : []), _toConsumableArray(this.hasShippingOptions() ? ["SHIPPING_OPTION"] : []))
                }, this.isShippingRequired() && _extends({
                    shippingAddressRequired: true
                }, this.hasShippingOptions() && {
                    shippingOptionRequired: true,
                    shippingOptionParameters: {
                        defaultSelectedOptionId: (_d = this.selectedShippingOption) === null || _d === void 0 ? void 0 : _d.id,
                        shippingOptions: this.formatShippingOptions(this.meta.shipping_options)
                    }
                }));
            }
        }, {
            key: "createCardData",
            value: function createCardData() {
                var rawDataInitialization = this.getMetaRawDataInitialization();
                return rawDataInitialization || {
                    type: "CARD",
                    parameters: {
                        allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                        allowedCardNetworks: ["AMEX", "DISCOVER", "INTERAC", "JCB", "MASTERCARD", "VISA"],
                        billingAddressRequired: !!this.meta.show_billing_address
                    }
                };
            }
        }]);
        return GoogleWalletService;
    }(WalletService);

    var MastercardWalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(MastercardWalletService, _WalletService);
        var _super = _createSuper(MastercardWalletService);
        function MastercardWalletService() {
            _classCallCheck(this, MastercardWalletService);
            return _super.apply(this, arguments);
        }
        _createClass(MastercardWalletService, [{
            key: "initializeChildWallets",
            value: function initializeChildWallets() {
                var _a, _b, _c, _d, _e, _f;
                this.childWallets = [];
                var hasAppleCredentials = !!((_c = (_b = (_a = this.meta) === null || _a === void 0 ? void 0 : _a.credentials) === null || _b === void 0 ? void 0 : _b.apple) === null || _c === void 0 ? void 0 : _c.merchant);
                var hasGoogleCredentials = !!((_f = (_e = (_d = this.meta) === null || _d === void 0 ? void 0 : _d.credentials) === null || _e === void 0 ? void 0 : _e.google) === null || _f === void 0 ? void 0 : _f.merchant);
                if (hasAppleCredentials && (!this.meta.wallets || this.meta.wallets.includes(WALLET_TYPE.APPLE))) this.childWallets.push(new AppleWalletService(this.publicKey, this.meta, this.getGatewayName(), this.eventEmitter));
                if (hasGoogleCredentials && (!this.meta.wallets || this.meta.wallets.includes(WALLET_TYPE.GOOGLE))) this.childWallets.push(new GoogleWalletService(this.publicKey, this.meta, this.getGatewayName(), this.eventEmitter));
            }
        }, {
            key: "getGatewayName",
            value: function getGatewayName() {
                return WALLET_GATEWAY.MASTERCARD;
            }
        }, {
            key: "setEnv",
            value: function setEnv(env) {
                this.childWallets.forEach(function (child) {
                    return child.setEnv(env);
                });
                return this;
            }
        }, {
            key: "update",
            value: function update(data) {
                this.childWallets.forEach(function (child) {
                    return child.update(data);
                });
            }
        }]);
        return MastercardWalletService;
    }(WalletService);

    var API_AUTH_TYPE;
    (function (API_AUTH_TYPE) {
        API_AUTH_TYPE[API_AUTH_TYPE["PUBLIC_KEY"] = 0] = "PUBLIC_KEY";
        API_AUTH_TYPE[API_AUTH_TYPE["TOKEN"] = 1] = "TOKEN";
    })(API_AUTH_TYPE || (API_AUTH_TYPE = {}));
    var ApiBase = /*#__PURE__*/function () {
        function ApiBase(auth, authType) {
            _classCallCheck(this, ApiBase);
            this.auth = auth;
            this.authType = authType || this.setAuthType();
            this.env = new Env(API_URL);
        }
        /**
         * Current method can change environment. By default environment = sandbox.
         * Also we can change domain alias for this environment. By default domain_alias = paydock.com
         *
         * @example
         * widget.setEnv('production');
         * @param {string} env - sandbox, production
         * @param {string} [alias] - Own domain alias
         */
        _createClass(ApiBase, [{
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.env.setEnv(env, alias);
                return this;
            }
        }, {
            key: "setAuthType",
            value: function setAuthType() {
                return this.authType = !!AccessToken.validateJWT(this.auth) ? API_AUTH_TYPE.TOKEN : API_AUTH_TYPE.PUBLIC_KEY;
            }
        }, {
            key: "getClient",
            value: function getClient(method, link) {
                var _this = this;
                var request = new XMLHttpRequest();
                request.open(method, this.env.getConf().url + link, true);
                request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                this.setAuthHeader(request);
                if (SDK.version) {
                    request.setRequestHeader(SDK.headerKeys.version, SDK.version);
                    request.setRequestHeader(SDK.headerKeys.type, SDK.type);
                }
                return {
                    config: request,
                    send: function send(body, cb, errorCb) {
                        request.onload = function () {
                            return _this.parser({
                                text: request.responseText,
                                status: request.status
                            }, cb, errorCb);
                        };
                        request.send(JSON.stringify(body));
                    }
                };
            }
        }, {
            key: "getClientPromise",
            value: function getClientPromise(method, link) {
                var _this2 = this;
                var request = new XMLHttpRequest();
                request.open(method, this.env.getConf().url + link, true);
                request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                this.setAuthHeader(request);
                if (SDK.version) {
                    request.setRequestHeader(SDK.headerKeys.version, SDK.version);
                    request.setRequestHeader(SDK.headerKeys.type, SDK.type);
                }
                return {
                    config: request,
                    send: function send(body) {
                        return new Promise(function (resolve, reject) {
                            request.onload = function () {
                                return resolve({
                                    text: request.responseText,
                                    status: request.status
                                });
                            };
                            request.send(JSON.stringify(body));
                        }).then(function (value) {
                            return _this2.parserPromise(value);
                        });
                    }
                };
            }
        }, {
            key: "parser",
            value: function parser(_ref, cb, errorCb) {
                var text = _ref.text,
                    status = _ref.status;
                try {
                    var res = JSON.parse(text);
                    if (status >= 200 && status < 300 || status === 302) return cb(res.resource.data);else errorCb(res.error || {
                        message: 'unknown error'
                    });
                } catch (e) {}
            }
        }, {
            key: "parserPromise",
            value: function parserPromise(_ref2) {
                var text = _ref2.text,
                    status = _ref2.status;
                try {
                    var res = JSON.parse(text);
                    return status >= 200 && status < 300 || status === 302 ? Promise.resolve(res.resource.data) : Promise.reject(res.error || {
                        message: 'unknown error'
                    });
                } catch (e) {
                    return Promise.reject(e);
                }
            }
        }, {
            key: "setAuthHeader",
            value: function setAuthHeader(request) {
                switch (this.authType) {
                    case API_AUTH_TYPE.PUBLIC_KEY:
                    {
                        request.setRequestHeader('x-user-public-key', this.auth);
                        break;
                    }
                    case API_AUTH_TYPE.TOKEN:
                    {
                        request.setRequestHeader('x-access-token', this.auth);
                        break;
                    }
                }
            }
        }]);
        return ApiBase;
    }();

    var WALLET_CAPTURE_LINK = '/v1/charges/wallet/capture';
    var WALLET_CALLBACK_LINK = '/v1/charges/wallet/callback';
    var STANDALONE_3DS_PROCESS_LINK = '/v1/charges/standalone-3ds/process';
    var STANDALONE_3DS_HANDLE_LINK = '/v1/charges/standalone-3ds/handle';
    var ApiChargeInternal = /*#__PURE__*/function () {
        function ApiChargeInternal(api) {
            _classCallCheck(this, ApiChargeInternal);
            this.api = api;
        }
        _createClass(ApiChargeInternal, [{
            key: "walletCapture",
            value: function walletCapture(payload) {
                return this.api.getClientPromise('POST', WALLET_CAPTURE_LINK).send(payload);
            }
        }, {
            key: "walletCallback",
            value: function walletCallback(payload) {
                return this.api.getClientPromise('POST', WALLET_CALLBACK_LINK).send(payload);
            }
        }, {
            key: "standalone3dsProcess",
            value: function standalone3dsProcess(payload) {
                return this.api.getClientPromise('POST', STANDALONE_3DS_PROCESS_LINK).send(payload);
            }
        }, {
            key: "standalone3dsHandle",
            value: function standalone3dsHandle() {
                return this.api.getClientPromise('GET', STANDALONE_3DS_HANDLE_LINK).send(undefined);
            }
        }]);
        return ApiChargeInternal;
    }();

    var GET_CONFIG = '/v1/services/:service_id/config';
    var ApiServiceInternal = /*#__PURE__*/function () {
        function ApiServiceInternal(api) {
            _classCallCheck(this, ApiServiceInternal);
            this.api = api;
        }
        _createClass(ApiServiceInternal, [{
            key: "getConfig",
            value: function getConfig(service_id) {
                var url = GET_CONFIG.replace(':service_id', service_id);
                return this.api.getClientPromise('GET', url).send(undefined);
            }
        }]);
        return ApiServiceInternal;
    }();
    var CARD_SCHEME_SERVICE;
    (function (CARD_SCHEME_SERVICE) {
        CARD_SCHEME_SERVICE["VISA_SRC"] = "VisaSRC";
    })(CARD_SCHEME_SERVICE || (CARD_SCHEME_SERVICE = {}));

    var ApiInternal = /*#__PURE__*/function (_ApiBase) {
        _inherits(ApiInternal, _ApiBase);
        var _super = _createSuper(ApiInternal);
        function ApiInternal() {
            _classCallCheck(this, ApiInternal);
            return _super.apply(this, arguments);
        }
        _createClass(ApiInternal, [{
            key: "charge",
            value: function charge() {
                return new ApiChargeInternal(this);
            }
        }, {
            key: "service",
            value: function service() {
                return new ApiServiceInternal(this);
            }
        }]);
        return ApiInternal;
    }(ApiBase);

    var AfterPayWalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(AfterPayWalletService, _WalletService);
        var _super = _createSuper(AfterPayWalletService);
        function AfterPayWalletService(token, meta) {
            var _this;
            _classCallCheck(this, AfterPayWalletService);
            _this = _super.call(this, token, meta);
            _this.token = token;
            _this.storageDispatcher = new StorageDispatcher('afterpay.wallet.paydock');
            return _this;
        }
        _createClass(AfterPayWalletService, [{
            key: "load",
            value: function load(container) {
                var _this2 = this;
                this.storageDispatcher.create(function () {
                    return _this2.mount(container);
                });
            }
        }, {
            key: "setEnv",
            value: function setEnv(env) {
                _get(_getPrototypeOf(AfterPayWalletService.prototype), "setEnv", this).call(this, env);
                this.storageDispatcher.setEnv(env);
                return this;
            }
        }, {
            key: "mount",
            value: function mount(container) {
                var _this3 = this;
                var _a, _b, _c, _d;
                var customStyles = {};
                if (((_a = this.meta) === null || _a === void 0 ? void 0 : _a.style) && _typeof((_b = this.meta) === null || _b === void 0 ? void 0 : _b.style) === 'object') customStyles = JSON.parse(JSON.stringify(((_c = this.meta) === null || _c === void 0 ? void 0 : _c.style['afterpay']) || ((_d = this.meta) === null || _d === void 0 ? void 0 : _d.style)));
                var afterpayButton = this.getButton(customStyles);
                afterpayButton.onclick = function () {
                    return _this3.onAfterPayButtonClicked();
                };
                var buttonWalletStyle = this.getButtonStyle(customStyles);
                container.getElement().appendChild(afterpayButton);
                container.getElement().appendChild(buttonWalletStyle);
            }
        }, {
            key: "onAfterPayButtonClicked",
            value: function onAfterPayButtonClicked() {
                var _this4 = this;
                var _a, _b;
                var afterpayJS = document.createElement('script');
                var countryCode = (_a = this.meta) === null || _a === void 0 ? void 0 : _a.country;
                afterpayJS.type = 'text/javascript';
                afterpayJS.src = ((_b = this.meta) === null || _b === void 0 ? void 0 : _b.gateway_mode) === 'live' ? 'https://portal.afterpay.com/afterpay.js' : 'https://portal.sandbox.afterpay.com/afterpay.js';
                afterpayJS.async = true;
                afterpayJS.defer = true;
                afterpayJS.onload = function () {
                    window.AfterPay.initialize({
                        countryCode: countryCode
                    });
                    _this4.storageDispatcher.push({
                        intent: StorageDataIntent.WIDGET_SESSION,
                        data: {
                            token: _this4.token
                        }
                    }, {
                        onSuccess: function onSuccess() {
                            _this4.getCheckoutSession().then(function (response) {
                                window.AfterPay.redirect({
                                    token: response.ref_token
                                });
                            })["catch"](function (err) {
                                window.AfterPay.close();
                                _this4.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {
                                    err: err
                                });
                            });
                        },
                        onError: function onError() {
                            console.error('Error initializing Afterpay wallet');
                            window.AfterPay.close();
                            _this4.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {});
                        }
                    });
                };
                document.head.appendChild(afterpayJS);
            }
        }, {
            key: "getCheckoutSession",
            value: function getCheckoutSession() {
                var _this5 = this;
                return new Promise(function (resolve, reject) {
                    return _this5.eventEmitter.emit(WALLET_EVENT.CALLBACK, {
                        data: {
                            request_type: 'CREATE_SESSION',
                            wallet_type: WALLET_TYPE.AFTERPAY
                        },
                        onSuccess: function onSuccess(res) {
                            resolve(res);
                        },
                        onError: function onError(message) {
                            reject(message);
                        }
                    });
                });
            }
        }, {
            key: "getButton",
            value: function getButton(customStyles) {
                var afterpayButton = document.createElement('button');
                afterpayButton.classList.add("afterpay-checkout-btn");
                afterpayButton.setAttribute('type', 'button');
                afterpayButton.innerHTML = "\n            <div class=\"afterpay-checkout-btn__wrapper\">\n                <svg viewBox=\"0 0 390 94\"\n                    height=\"".concat(this.getHeight(customStyles), "\"\n                    xmlns=\"http://www.w3.org/2000/svg\">\n                    <g fill=\"currentColor\">\n                        <path\n                            d=\"M388.6 21.4l-34.8 71.8h-14.4l13-26.8-20.5-45h14.8l13.2 30.1 14.3-30.1zM41 46.9c0-8.6-6.2-14.6-13.9-14.6s-13.9 6.1-13.9 14.6c0 8.4 6.2 14.6 13.9 14.6 7.6 0 13.9-6.1 13.9-14.6m.1 25.5v-6.6c-3.8 4.6-9.4 7.4-16.1 7.4C11 73.2.4 62 .4 46.9c0-15 11-26.4 24.9-26.4 6.5 0 12 2.9 15.8 7.3v-6.4h12.5v51H41.1zM114.6 61.1c-4.4 0-5.6-1.6-5.6-5.9V32.5h8.1V21.4H109V8.9H96.1v12.4H79.5v-5.1c0-4.3 1.6-5.9 6.1-5.9h2.8V.4h-6.2C71.6.4 66.6 3.9 66.6 14.5v6.8h-7.1v11.1h7.1v39.9h12.9V32.5h16.6v25c0 10.4 4 14.9 14.4 14.9h6.6V61.1h-2.5zM160.7 42.3c-.9-6.6-6.3-10.6-12.6-10.6s-11.5 3.9-12.9 10.6h25.5zm-25.6 7.9c.9 7.5 6.3 11.8 13.2 11.8 5.4 0 9.6-2.6 12-6.6h13.2c-3.1 10.8-12.7 17.7-25.5 17.7-15.4 0-26.2-10.8-26.2-26.2 0-15.4 11.4-26.5 26.5-26.5 15.2 0 26.2 11.2 26.2 26.5 0 1.1-.1 2.2-.3 3.3h-39.1zM256.2 46.9c0-8.3-6.2-14.6-13.9-14.6s-13.9 6.1-13.9 14.6c0 8.4 6.2 14.6 13.9 14.6 7.6 0 13.9-6.4 13.9-14.6m-40.4 46.3V21.4h12.5V28c3.8-4.7 9.4-7.5 16.1-7.5 13.8 0 24.6 11.3 24.6 26.3s-11 26.4-24.9 26.4c-6.4 0-11.7-2.6-15.4-6.8v26.8h-12.9zM314.2 46.9c0-8.6-6.2-14.6-13.9-14.6-7.6 0-13.9 6.1-13.9 14.6 0 8.4 6.2 14.6 13.9 14.6s13.9-6.1 13.9-14.6m.1 25.5v-6.6c-3.8 4.6-9.4 7.4-16.1 7.4-14 0-24.6-11.2-24.6-26.3 0-15 11-26.4 24.9-26.4 6.5 0 12 2.9 15.8 7.3v-6.4h12.5v51h-12.5zM193.2 26.4s3.2-5.9 11-5.9c3.3 0 5.5 1.2 5.5 1.2v13s-4.7-2.9-9.1-2.3c-4.3.6-7.1 4.6-7.1 9.9v30.2h-13v-51H193v4.9h.2z\" />\n                    </g>\n                </svg>\n                <svg viewBox=\"0 0 107 96\"\n                    height=\"").concat(this.getHeight(customStyles), "\"\n                    xmlns=\"http://www.w3.org/2000/svg\">\n                    <path\n                        d=\"M99 19.5L84.2 11l-15-8.6c-10-5.7-22.4 1.5-22.4 13v1.9c0 1.1.6 2 1.5 2.6l7 4c1.9 1.1 4.4-.3 4.4-2.5v-4.6c0-2.3 2.5-3.7 4.4-2.6l13.8 7.9L91.6 30c2 1.1 2 4 0 5.1L77.9 43l-13.8 7.9c-2 1.1-4.4-.3-4.4-2.6V46c0-11.5-12.4-18.7-22.4-13l-15 8.6-14.8 8.5c-10 5.7-10 20.2 0 26l14.8 8.5 15 8.6c10 5.7 22.4-1.5 22.4-13v-1.9c0-1.1-.6-2-1.5-2.6l-7-4c-1.9-1.1-4.4.3-4.4 2.5v4.6c0 2.3-2.5 3.7-4.4 2.6l-13.8-7.9-13.7-7.9c-2-1.1-2-4 0-5.1l13.7-7.9 13.8-7.9c2-1.1 4.4.3 4.4 2.6v2.3c0 11.5 12.4 18.7 22.4 13l15-8.6L99 45.5c10.1-5.8 10.1-20.2 0-26\"\n                        fill=\"currentColor\" />\n                </svg>\n            </div>\n        ");
                return afterpayButton;
            }
        }, {
            key: "getButtonStyle",
            value: function getButtonStyle(customStyles) {
                var styleSheet = document.createElement('style');
                var buttonColor = this.generateButtonColor(customStyles.button_type);
                styleSheet.innerText = "\n            .afterpay-checkout-btn {\n                outline: none;\n                border: none;\n                border-radius: ".concat(this.getHeight(customStyles), ";\n                padding: 0;\n                margin: 0;\n                transition: all 300ms;\n                cursor: pointer;\n            }\n\n            .afterpay-checkout-btn:active {\n                opacity: 0.7;\n            }\n\n            .afterpay-checkout-btn__wrapper {\n                display: flex;\n                align-items: center;\n                padding: 10px 20px;\n                color: ").concat(buttonColor.color, ";\n                background-color: ").concat(buttonColor.background, ";\n                border-radius: ").concat(this.getHeight(customStyles), ";\n            }\n        ");
                return styleSheet;
            }
        }, {
            key: "generateButtonColor",
            value: function generateButtonColor(button_type) {
                switch (button_type) {
                    case 'black':
                        return {
                            color: '#B2FCE3',
                            background: '#000'
                        };
                    case 'mint':
                        return {
                            color: '#000',
                            background: '#B2FCE3'
                        };
                    default:
                        return {
                            color: '#fff',
                            background: '#000'
                        };
                }
            }
        }, {
            key: "getHeight",
            value: function getHeight(customStyles) {
                if (!customStyles.height) return '40px';
                if (Number.isNaN(Number(customStyles.height))) return customStyles.height;
                return "".concat(customStyles.height, "px");
            }
        }]);
        return AfterPayWalletService;
    }(WalletService);

    // src/utils/entries-to-string.ts
    function entriesToString(obj) {
        var separator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "=";
        return !!Object.keys(obj).length ? Object.entries(obj).reduce(function (str, _ref) {
            var _ref2 = _slicedToArray(_ref, 2),
                key = _ref2[0],
                value = _ref2[1];
            return [].concat(_toConsumableArray(str), ["".concat(key).concat(separator).concat(value)]);
        }, []).join(",") : "";
    }
    var Checkout = /*#__PURE__*/function () {
        function Checkout(_ref3) {
            var _this = this,
                _this$callback;
            var url = _ref3.url,
                accessToken = _ref3.accessToken,
                refreshToken = _ref3.refreshToken,
                clientId = _ref3.clientId,
                orderId = _ref3.orderId,
                onCheckoutClosed = _ref3.onCheckoutClosed,
                onError = _ref3.onError,
                onTokensChanged = _ref3.onTokensChanged,
                onPaymentSuccess = _ref3.onPaymentSuccess,
                onPaymentError = _ref3.onPaymentError,
                title = _ref3.title,
                windowFeatures = _ref3.windowFeatures;
            _classCallCheck(this, Checkout);
            _defineProperty(this, "DEFAULT_WIDTH", 450);
            _defineProperty(this, "DEFAULT_HEIGHT", 750);
            _defineProperty(this, "DEFAULT_FEATURES", {
                resizable: "no"
            });
            _defineProperty(this, "callback", void 0);
            _defineProperty(this, "error", void 0);
            _defineProperty(this, "frame", void 0);
            _defineProperty(this, "isCheckoutOpen", void 0);
            _defineProperty(this, "onMessage", void 0);
            _defineProperty(this, "url", void 0);
            _defineProperty(this, "orderId", void 0);
            _defineProperty(this, "accessToken", void 0);
            _defineProperty(this, "refreshToken", void 0);
            _defineProperty(this, "clientId", void 0);
            _defineProperty(this, "onCheckoutClosed", void 0);
            _defineProperty(this, "onTokensChanged", void 0);
            _defineProperty(this, "title", void 0);
            _defineProperty(this, "windowFeatures", void 0);
            _defineProperty(this, "windowObserver", void 0);
            _defineProperty(this, "open", function () {
                if (_this.isCheckoutOpen) {
                    var _this$frame;
                    (_this$frame = _this.frame) === null || _this$frame === void 0 || _this$frame.focus();
                } else {
                    _this.frame = window.open(_this.url, _this.title, _this.getFeatures());
                    _this.isCheckoutOpen = true;
                    _this.startWindowObserver();
                }
            });
            _defineProperty(this, "close", function () {
                _this.frame = void 0;
                _this.isCheckoutOpen = false;
                _this.stopWindowObserver();
            });
            _defineProperty(this, "listenMessage", function () {
                if (_this.onMessage) {
                    window.addEventListener("message", _this.onMessage, false);
                }
            });
            var merchantURL = encodeURIComponent(window.location.href.replace(/\/$/, ""));
            this.callback = (_this$callback = {}, _defineProperty(_this$callback, "ERROR" /* ERROR */, function ERROR(_ref4) {
                var error = _ref4.error,
                    orderId2 = _ref4.orderId;
                onError === null || onError === void 0 || onError({
                    error: error,
                    orderId: orderId2
                });
            }), _defineProperty(_this$callback, "PAYMENT_ERROR" /* PAYMENT_ERROR */, function PAYMENT_ERROR(_ref5) {
                var error = _ref5.error,
                    orderId2 = _ref5.orderId;
                onPaymentError === null || onPaymentError === void 0 || onPaymentError({
                    error: error,
                    orderId: orderId2
                });
            }), _defineProperty(_this$callback, "PAYMENT_SUCCESS" /* PAYMENT_SUCCESS */, function PAYMENT_SUCCESS(_ref6) {
                var orderId2 = _ref6.orderId;
                onPaymentSuccess === null || onPaymentSuccess === void 0 || onPaymentSuccess({
                    orderId: orderId2
                });
            }), _defineProperty(_this$callback, "TOKENS_CHANGED" /* TOKENS_CHANGED */, function TOKENS_CHANGED(_ref7) {
                var accessToken2 = _ref7.accessToken,
                    refreshToken2 = _ref7.refreshToken;
                onTokensChanged === null || onTokensChanged === void 0 || onTokensChanged({
                    accessToken: accessToken2,
                    refreshToken: refreshToken2
                });
            }), _this$callback);
            this.isCheckoutOpen = false;
            this.error = void 0;
            this.frame = void 0;
            this.onMessage = function (e) {
                var data = e.data,
                    origin = e.origin;
                if (origin === url) {
                    if (data !== null && data !== void 0 && data.isPaymentSuccessful) {
                        _this.callback["PAYMENT_SUCCESS" /* PAYMENT_SUCCESS */]({
                            orderId: data.orderId
                        });
                    }
                    if (!(data !== null && data !== void 0 && data.isPaymentSuccessful) && !!(data !== null && data !== void 0 && data.error)) {
                        _this.callback["PAYMENT_ERROR" /* PAYMENT_ERROR */]({
                            orderId: data.orderId,
                            error: data.error
                        });
                    }
                    if (!(data !== null && data !== void 0 && data.paymentFlowState) && !!(data !== null && data !== void 0 && data.error)) {
                        _this.callback["ERROR" /* ERROR */]({
                            orderId: data.orderId,
                            error: data.error
                        });
                    }
                    if (data !== null && data !== void 0 && data.accessToken && data !== null && data !== void 0 && data.refreshToken) {
                        _this.callback["TOKENS_CHANGED" /* TOKENS_CHANGED */]({
                            accessToken: data.accessToken,
                            refreshToken: data.refreshToken
                        });
                    }
                }
            };
            this.url = "".concat(url, "?orderId=").concat(orderId, "&merchant=").concat(merchantURL);
            this.orderId = orderId;
            this.accessToken = accessToken;
            this.refreshToken = refreshToken;
            this.clientId = clientId;
            this.onCheckoutClosed = onCheckoutClosed;
            this.onTokensChanged = onTokensChanged;
            this.title = title;
            this.windowFeatures = windowFeatures;
            this.windowObserver = void 0;
        }
        _createClass(Checkout, [{
            key: "isWindowObserverOpen",
            get: function get() {
                return !!this.windowObserver;
            }
        }, {
            key: "getFeatures",
            value: function getFeatures() {
                var _this$windowFeatures, _this$windowFeatures2;
                var windowWidth = ((_this$windowFeatures = this.windowFeatures) === null || _this$windowFeatures === void 0 ? void 0 : _this$windowFeatures.width) || this.DEFAULT_WIDTH;
                var windowHeight = ((_this$windowFeatures2 = this.windowFeatures) === null || _this$windowFeatures2 === void 0 ? void 0 : _this$windowFeatures2.height) || this.DEFAULT_HEIGHT;
                var features = _objectSpread2(_objectSpread2({}, this.DEFAULT_FEATURES), this.windowFeatures);
                var centerPosition = this.getCenteredPosition(windowWidth, windowHeight);
                return entriesToString(_objectSpread2(_objectSpread2(_objectSpread2({}, centerPosition), features), {}, {
                    width: windowWidth,
                    height: windowHeight
                }));
            }
        }, {
            key: "getCenteredPosition",
            value: function getCenteredPosition(width, height) {
                return {
                    left: screen.width / 2 - width / 2,
                    top: screen.height / 2 - height / 2
                };
            }
        }, {
            key: "startWindowObserver",
            value: function startWindowObserver() {
                var _this2 = this;
                if (!this.isWindowObserverOpen) {
                    this.windowObserver = setInterval(function () {
                        var _this2$frame;
                        if ((_this2$frame = _this2.frame) !== null && _this2$frame !== void 0 && _this2$frame.closed) {
                            var _this2$onCheckoutClos;
                            _this2.close();
                            (_this2$onCheckoutClos = _this2.onCheckoutClosed) === null || _this2$onCheckoutClos === void 0 || _this2$onCheckoutClos.call(_this2, {
                                orderId: _this2.orderId
                            });
                            if (_this2.onMessage) {
                                window.removeEventListener("message", _this2.onMessage);
                            }
                        }
                    }, 300);
                }
            }
        }, {
            key: "stopWindowObserver",
            value: function stopWindowObserver() {
                clearInterval(this.windowObserver);
            }
        }]);
        return Checkout;
    }();

    var __awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
        function adopt(value) {
            return value instanceof P ? value : new P(function (resolve) {
                resolve(value);
            });
        }
        return new (P || (P = Promise))(function (resolve, reject) {
            function fulfilled(value) {
                try {
                    step(generator.next(value));
                } catch (e) {
                    reject(e);
                }
            }
            function rejected(value) {
                try {
                    step(generator["throw"](value));
                } catch (e) {
                    reject(e);
                }
            }
            function step(result) {
                result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
            }
            step((generator = generator.apply(thisArg, _arguments || [])).next());
        });
    };
    var FlypayV2WalletService = /*#__PURE__*/function (_WalletService) {
        _inherits(FlypayV2WalletService, _WalletService);
        var _super = _createSuper(FlypayV2WalletService);
        function FlypayV2WalletService(publicKey, meta) {
            var _this;
            _classCallCheck(this, FlypayV2WalletService);
            _this = _super.call(this, publicKey, meta);
            _this.onFlypayV2ButtonClick = function () {
                return __awaiter(_assertThisInitialized(_this), void 0, void 0, /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
                    var loadingOverlay, button;
                    return _regeneratorRuntime().wrap(function _callee$(_context) {
                        while (1) switch (_context.prev = _context.next) {
                            case 0:
                                loadingOverlay = document.getElementById("loading-overlay");
                                button = document.getElementById("flypay-v2-button");
                                if (button) button.disabled = true;
                                _context.prev = 3;
                                if (this.orderId) {
                                    _context.next = 9;
                                    break;
                                }
                                if (loadingOverlay) loadingOverlay.style.display = "flex";
                                _context.next = 8;
                                return this.getOrderId();
                            case 8:
                                this.orderId = _context.sent;
                            case 9:
                                if (loadingOverlay) loadingOverlay.style.display = "none";
                                if (this.orderId) {
                                    this.flypayV2Checkout(this.orderId);
                                } else {
                                    this.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE);
                                }
                                _context.next = 17;
                                break;
                            case 13:
                                _context.prev = 13;
                                _context.t0 = _context["catch"](3);
                                if (loadingOverlay) loadingOverlay.style.display = "none";
                                this.eventEmitter.emit(WALLET_EVENT.UNAVAILABLE, {
                                    err: _context.t0
                                });
                            case 17:
                            case "end":
                                return _context.stop();
                        }
                    }, _callee, this, [[3, 13]]);
                }));
            };
            _this.accessToken = meta === null || meta === void 0 ? void 0 : meta.access_token;
            _this.refreshToken = meta === null || meta === void 0 ? void 0 : meta.refresh_token;
            _this.link = new Link('');
            return _this;
        }
        _createClass(FlypayV2WalletService, [{
            key: "load",
            value: function load(container) {
                this.mount(container);
            }
        }, {
            key: "setEnv",
            value: function setEnv(env) {
                this.link.setEnv(env);
                return _get(_getPrototypeOf(FlypayV2WalletService.prototype), "setEnv", this).call(this, env);
            }
        }, {
            key: "mount",
            value: function mount(container) {
                var flypayV2Button = this.getButton();
                flypayV2Button.onclick = this.onFlypayV2ButtonClick;
                container.getElement().appendChild(flypayV2Button);
            }
        }, {
            key: "getButton",
            value: function getButton() {
                var style = document.createElement('style');
                style.innerHTML = this.createButtonStyle();
                document.head.appendChild(style);
                var flypayV2Button = document.createElement('button');
                flypayV2Button.classList.add('flypay-v2-checkout-btn');
                flypayV2Button.setAttribute('type', 'button');
                flypayV2Button.setAttribute('id', 'flypay-v2-button');
                flypayV2Button.innerHTML = "\n        <div id=\"loading-overlay\"></div>\n        <div class=\"flypay-v2-checkout-btn__wrapper\">\n            <img src=\"".concat(this.link.getBaseUrl(), "/images/flypay-checkout.png\" alt=\"Flypay Checkout\" class=\"flypay-v2-image\">\n        </div>\n    ");
                return flypayV2Button;
            }
        }, {
            key: "createButtonStyle",
            value: function createButtonStyle() {
                return "\n      #widget {\n        position: relative;\n      }\n\n      #loading-overlay {\n        position: absolute;\n        width: 268px;\n        height: 74px;\n        background: rgba(255, 255, 255, 0.7);\n        display: none;\n        justify-content: center;\n        align-items: center;\n      }\n\n      #loading-overlay::after {\n        content: \"\";\n        display: inline-block;\n        width: 40px;\n        height: 40px;\n        border: 4px solid #ccc;\n        border-top-color: #333;\n        border-radius: 50%;\n\n        /* Vendor prefixes for animation property */\n        -webkit-animation: spin 1s infinite linear;\n        -moz-animation: spin 1s infinite linear;\n        -o-animation: spin 1s infinite linear;\n        animation: spin 1s infinite linear;\n      }\n\n      @-webkit-keyframes spin {\n        0% {\n          -webkit-transform: rotate(0deg);\n          transform: rotate(0deg);\n        }\n        100% {\n          -webkit-transform: rotate(360deg);\n          transform: rotate(360deg);\n        }\n      }\n\n      @-moz-keyframes spin {\n        0% {\n          -moz-transform: rotate(0deg);\n          transform: rotate(0deg);\n        }\n        100% {\n          -moz-transform: rotate(360deg);\n          transform: rotate(360deg);\n        }\n      }\n\n      @-o-keyframes spin {\n        0% {\n          -o-transform: rotate(0deg);\n          transform: rotate(0deg);\n        }\n        100% {\n          -o-transform: rotate(360deg);\n          transform: rotate(360deg);\n        }\n      }\n\n      @keyframes spin {\n        0% {\n          transform: rotate(0deg);\n        }\n        100% {\n          transform: rotate(360deg);\n        }\n      }\n\n      .flypay-v2-checkout-btn {\n          border: none; /* Remove borders */\n          background: transparent; /* Make the button background transparent */\n          cursor: pointer; /* Make it look clickable */\n          outline: none; /* Remove focus outline */\n          padding: 0; /* Remove any default padding */\n      }\n\n      .flypay-v2-image {\n          display: block; /* Display the image as block to remove any gaps */\n          border: none; /* Ensure the image doesn't have a border */\n          width: 100%; /* Make the image take full width of the container */\n      }\n    ";
            }
        }, {
            key: "getOrderId",
            value: function getOrderId() {
                var _this2 = this;
                return new Promise(function (resolve, reject) {
                    return _this2.eventEmitter.emit(WALLET_EVENT.CALLBACK, {
                        data: {
                            request_type: "CREATE_SESSION"
                        },
                        onSuccess: function onSuccess(res) {
                            return resolve(res.id);
                        },
                        onError: function onError(res) {
                            return reject(res.error);
                        }
                    });
                });
            }
        }, {
            key: "flypayV2Checkout",
            value: function flypayV2Checkout(orderId) {
                var _this3 = this;
                this.checkout = new Checkout(_extends(_extends(_extends({
                    orderId: orderId
                }, this.accessToken && {
                    accessToken: this.accessToken
                }), this.refreshToken && {
                    refreshToken: this.refreshToken
                }), {
                    clientId: '633727af-5bd2-4733-8e08-04074a98300d',
                    url: 'https://checkout.release.cxbflypay.com.au',
                    onCheckoutClosed: function onCheckoutClosed() {
                        var button = document.getElementById("flypay-v2-button");
                        if (button) button.disabled = false;
                    },
                    onError: function onError(err) {
                        return _this3.eventEmitter.emit(WALLET_EVENT.PAYMENT_ERROR, {
                            error: err
                        });
                    },
                    onPaymentSuccess: function onPaymentSuccess(data) {
                        return _this3.eventEmitter.emit(WALLET_EVENT.PAYMENT_SUCCESS, data);
                    },
                    onPaymentError: function onPaymentError(err) {
                        return _this3.eventEmitter.emit(WALLET_EVENT.PAYMENT_ERROR, {
                            error: err
                        });
                    },
                    onTokensChanged: function onTokensChanged(_ref) {
                        var accessToken = _ref.accessToken,
                            refreshToken = _ref.refreshToken;
                        _this3.accessToken = accessToken;
                        _this3.refreshToken = refreshToken;
                    }
                }));
                this.checkout.open();
                this.checkout.listenMessage();
            }
        }]);
        return FlypayV2WalletService;
    }(WalletService);

    /**
     * List of available event's name in the wallet button lifecycle
     * @const EVENT
     *
     * @type {object}
     * @param {string} UNAVAILABLE=unavailable
     * @param {string} UPDATE=update
     * @param {string} PAYMENT_SUCCESSFUL=paymentSuccessful
     * @param {string} PAYMENT_ERROR=paymentError
     */
    var EVENT$1 = {
        UNAVAILABLE: "unavailable",
        UPDATE: "update",
        PAYMENT_SUCCESSFUL: "paymentSuccessful",
        PAYMENT_ERROR: "paymentError",
        PAYMENT_IN_REVIEW: "paymentInReview"
    };
    /**
     * Interface of data used by the wallet checkout and payment proccess.
     * @interface IWalletMeta
     *
     * @type {object}
     * @param {string} [amount_label] Label shown next to the total amount to be paid. Required for [Stripe, ApplePay, GooglePay]. N/A for [FlyPay, Flypay V2, PayPal, Afterpay].
     * @param {string} [country] Country of the user. 2 letter ISO code format. Required for [Stripe, ApplePay, GooglePay, Afterpay]. N/A for [FlyPay, Flypay V2, PayPal].
     * @param {boolean} [pay_later] Used to enable Pay Later feature in PayPal Smart Checkout WalletButton integration when available. Optional for [PayPal]. N/A for other wallets.
     * @param {boolean} [standalone] Used to enable Standalone Buttons feature in PayPal Smart Checkout WalletButton integration. Used together with `pay_later`. Optional for [PayPal]. N/A for other wallets.
     * @param {boolean} [show_billing_address] Used to hide/show the billing address on ApplePay and GooglePay popups. Default value is false. Optional for [ApplePay, GooglePay]. N/A for other wallets.
     * @param {boolean} [request_payer_name] Used mainly for fraud purposes - recommended set to true. Optional for [Stripe]. N/A for other wallets.
     * @param {boolean} [request_payer_email] Used mainly for fraud purposes - recommended set to true. Optional for [Stripe]. N/A for other wallets.
     * @param {boolean} [request_payer_phone] Used mainly for fraud purposes - recommended set to true. Optional for [Stripe]. N/A for other wallets.
     * @param {boolean} [request_shipping] Used to request or not shipping address in the Wallet checkout, being able to handle amount changes via the `update` event. Optional for [FlyPay, PayPal, ApplePay, GooglePay]. N/A for [Flypay V2, Stripe, Afterpay].
     * @param {IApplePayShippingOption[] | IPayPalShippingOption[]} [shipping_options] Used to provide available shipping options.(To use shipping_options the request_shipping flag should be true). Optional for [ApplePay]. N/A for the other wallets.
     * @param {string} [merchant_name] Merchant Name used for GooglePay integration via MPGS. Required for [GooglePay]. N/A for other wallets.
     * @param {object} [raw_data_initialization] Used to provide values to initialize wallet with raw data. Optional for [ApplePay]. N/A for the other wallets.
     * @param {object} [style] For **Paypal**: used to style the buttons, check possible values in the [style guide](https://developer.paypal.com/docs/business/checkout/reference/style-guide).
     * When `standalone` and `pay_later`, extra options can be provided in `style.messages` with the [messages style options](https://developer.paypal.com/docs/checkout/pay-later/us/integrate/reference/#stylelayout).
     * Also used at **ApplePay**, **GooglePay** and **Afterpay** to select button type.
     * Optional for [PayPal, ApplePay, GooglePay, Afterpay]. N/A for [Stripe, FlyPay, Flypay V2].
     * @param {object} [style.button_type] Used to select ApplePay button type (e.g: 'buy','donate', etc), check possible values at https://developer.apple.com/documentation/apple_pay_on_the_web/displaying_apple_pay_buttons_using_css.
     * Also select button type for GooglePay (check GooglePayStyles) and Afterpay (check AfterpayStyles). Optional for [ApplePay, GooglePay, Afterpay]. N/A for other wallets.
     * @param {object} [style.height] Used to select Afterpay button height. Optional for [Afterpay]. N/A for other wallets.
     * @param {array} [wallets] By default if this is not sent or empty, we will try to show either Apple Pay or Google Pay buttons. This can be limited sending the following array in this field: ['apple','google]. Optional for [Stripe, ApplePay, GooglePay]. N/A for other wallets.
     */
    /**
     * Interface of Shipping Options for ApplePay
     * @interface IApplePayShippingOption
     *
     * @type {object}
     * @param {string} [id] Identifier of the Shipping Option. Required.
     * @param {string} [label] Identifier of the Shipping Option. Required.
     * @param {string} [amount] Amount of the Shipping Option. Required.
     * @param {string} [detail] Details of the Shipping Option. Required.
     * @param {string} [type] Type of the Shipping Option. Values can be 'ELECTRONIC', 'GROUND', 'NOT_SHIPPED', 'OVERNIGHT', 'PICKUP', 'PRIORITY', 'SAME_DAY'. Optional.
     */
    /**
     * Interface of Shipping Options for GooglePay
     * @interface IGooglePayShippingOption
     *
     * @type {object}
     * @param {string} [id] Identifier of the Shipping Option. Required.
     * @param {string} [label] Identifier of the Shipping Option. Required.
     * @param {string} [detail] Details of the Shipping Option. Optional.
     * @param {string} [type] Type of the Shipping Option. Values can be 'ELECTRONIC', 'GROUND', 'NOT_SHIPPED', 'OVERNIGHT', 'PICKUP', 'PRIORITY', 'SAME_DAY'. Optional.
     */
    /**
     * Interface of Shipping Options for PayPal
     * @interface IPayPalShippingOption
     *
     * @type {object}
     * @param {string} [id] Identifier of the Shipping Option. Required.
     * @param {string} [label] Identifier of the Shipping Option. Required.
     * @param {string} [amount] Amount of the Shipping Option. Required.
     * @param {string} [currency] Currency of the Shipping Option. Required.
     * @param {string} [type] Type of the Shipping Option. Values can be 'SHIPPING' or 'PICKUP'. Required.
     */
    /**
     * Class WalletButtons to work with different E-Wallets within html (currently supports Apple Pay, Google Pay, Google Payв„ў and Apple Pay via Stripe, Flypay, Flypay V2, Paypal, Afterpay)
     * @constructor
     *
     * @example
     * var button = new WalletButtons('#wallet-buttons', 'charge-token', { amount_label: 'Total', country: 'us' });
     *
     * @param {string} selector - Selector of html element. Container for the WalletButtons.
     * @param {string} chargeToken - token for the wallet transaction, created with a secure call to `POST charges/wallet`.
     * @param {IWalletMeta} object - data that configures the E-Wallet, which can be shown on checkout page and configures required customer information.
     **/
    var WalletButtons = /*#__PURE__*/function () {
        function WalletButtons(selector, chargeToken, meta) {
            _classCallCheck(this, WalletButtons);
            this.hasUpdateHandler = false;
            var parsedToken = AccessToken.validateJWT(chargeToken);
            if (!parsedToken) throw new Error("Invalid charge token");
            this.eventEmitter = new EventEmitter();
            this.container = new Container(selector);
            var tokenMeta = AccessToken.extractMeta(parsedToken.body);
            this.api = new ApiInternal(chargeToken, API_AUTH_TYPE.TOKEN);
            switch (tokenMeta.gateway.type) {
                case WALLET_GATEWAY.STRIPE:
                    this.service = new StripeWalletService(tokenMeta.credentials.client_auth, _extends(_extends({}, meta), {
                        amount: tokenMeta.charge.amount,
                        currency: tokenMeta.charge.currency
                    }));
                    break;
                case WALLET_GATEWAY.FLYPAY:
                    this.service = new FlypayWalletService(chargeToken, _extends(_extends({}, meta), {
                        id: tokenMeta.charge.id,
                        gateway_mode: tokenMeta.gateway.mode,
                        amount: tokenMeta.charge.amount,
                        currency: tokenMeta.charge.currency,
                        reference: tokenMeta.charge.reference
                    }));
                    break;
                case WALLET_GATEWAY.FLYPAY_V2:
                    this.service = new FlypayV2WalletService(chargeToken, _extends(_extends({}, meta), {
                        id: tokenMeta.charge.id,
                        gateway_mode: tokenMeta.gateway.mode,
                        amount: tokenMeta.charge.amount,
                        currency: tokenMeta.charge.currency,
                        reference: tokenMeta.charge.reference
                    }));
                    break;
                case WALLET_GATEWAY.PAYPAL:
                    this.service = new PaypalWalletService(tokenMeta.credentials.client_auth, _extends(_extends({}, meta), {
                        id: tokenMeta.charge.id,
                        gateway_mode: tokenMeta.gateway.mode,
                        amount: tokenMeta.charge.amount,
                        currency: tokenMeta.charge.currency,
                        capture: tokenMeta.charge.capture
                    }));
                    break;
                case WALLET_GATEWAY.MASTERCARD:
                    this.service = new MastercardWalletService('', _extends(_extends({}, meta), {
                        credentials: tokenMeta.gateway.credentials,
                        amount: tokenMeta.charge.amount,
                        currency: tokenMeta.charge.currency,
                        gateway_mode: tokenMeta.gateway.mode
                    }));
                    break;
                case WALLET_GATEWAY.AFTERPAY:
                    this.service = new AfterPayWalletService(chargeToken, _extends(_extends({}, meta), {
                        id: tokenMeta.charge.id,
                        gateway_mode: tokenMeta.gateway.mode,
                        amount: tokenMeta.charge.amount,
                        currency: tokenMeta.charge.currency,
                        reference: tokenMeta.charge.reference
                    }));
                    break;
            }
        }
        /**
         * Initializes the availability checks and inserts the button if possible.
         * Otherwise function onUnavailable(handler: VoidFunction) will be called.
         *
         * @example
         * var button = new WalletButtons(
         *      '#buttons',
         *      token,
         *      {
         *          amount_label: 'Total',
         *          country: 'DE',
         *      }
         *  );
         *  button.load();
         */
        _createClass(WalletButtons, [{
            key: "load",
            value: function load() {
                try {
                    this.setupServiceCallbacks();
                    this.service.load(this.container);
                } catch (err) {
                    this.eventEmitter.emit(EVENT$1.UNAVAILABLE, null);
                    throw err;
                }
            }
            /**
             * Triggers the update process of the wallet, if available.
             * Currently supported by Flypay, Paypal and ApplePay/GooglePay via MPGS Wallets.
             *
             * @example
             * var button = new WalletButtons(
             *      '#buttons',
             *      token,
             *      {
             *          amount_label: 'Total',
             *          country: 'DE',
             *      }
             *  );
             *  button.on('update', (data) => {
             *      updateChargeAmountInBackend(data);
             *      button.update({ success: true });
             *  });
             *
             * @example
             * // ApplePay via MPGS example:
             * var button = new WalletButtons(
             *      '#buttons',
             *      token,
             *      {
             *          amount_label: 'Total',
             *          country: 'AU',
             *          ...
             *      }
             *  );
             *  button.on('update', (data) => {
             *      updateChargeAmountInBackend(data);
             *      button.update({
             *         success: true,
             *         body: {
             *              amount: 15,
             *              shipping_options: [
             *                   {
             *                      id: "NEW-FreeShip",
             *                       label: "NEW - Free Shipping",
             *                       detail: "Arrives in 3 to 5 days",
             *                       amount: "0.00"
             *                   },
             *                   {
             *                       id: "NEW - FastShip",
             *                       label: "NEW - Fast Shipping",
             *                       detail: "Arrives in less than 1 day",
             *                       amount: "10.00"
             *                   }
             *               ]
             *          }
             *       });
             *  });
             */
        }, {
            key: "update",
            value: function update(data) {
                this.service.update(data);
            }
            /**
             * Current method can change environment. By default environment = sandbox.
             * Also we can change domain alias for this environment. By default domain_alias = paydock.com
             * Bear in mind that you must set an environment before calling `button.load()`.
             *
             * @example
             * button.setEnv('production', 'paydock.com');
             * @param {string} env - sandbox, production
             * @param {string} [alias] - Own domain alias
             */
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.api.setEnv(env, alias);
                this.service.setEnv(env);
            }
            /**
             * Closes the checkout forcibly. Currently supported in Flypay wallet.
             *
             * @example
             * button.close();
             */
        }, {
            key: "close",
            value: function close() {
                if (typeof this.service.close === 'function') {
                    this.service.close();
                }
            }
            /**
             * Listen to events of button. `unavailable` returns no data, `paymentSuccessful` returns IWalletPaymentSuccessful
             * for Stripe or full response for Flypay, and `paymentError` an error.
             *
             * NOTE: when listening for the 'update' event, make sure to call the `button.update(result)` method on completion.
             *
             * @example
             *
             * button.on('paymentSuccessful', function (data) {
             *      console.log(data);
             * });
             * // or
             * button.on('unavailable').then(function () {
             *      console.log('No button is available);
             * });
             *
             * @param {string} eventName - Available event names [EVENT]{@link EVENT}
             * @param {listener} [cb]
             * @return {Promise<IEventData> | void}
             */
        }, {
            key: "on",
            value: function on(eventName, cb) {
                var _this = this;
                if (eventName === EVENT$1.UPDATE) this.hasUpdateHandler = true;
                if (typeof cb === 'function') return this.eventEmitter.subscribe(eventName, cb);
                return new Promise(function (resolve) {
                    return _this.eventEmitter.subscribe(eventName, function (res) {
                        return resolve(res);
                    });
                });
            }
            /**
             * User to subscribe to the no button available event. This method is used after loading when the button is not available.
             * For MPGS, since can have more than one wallet button configured (ApplePay/GooglePay) you will receive a body (({ wallet: WALLET_TYPE.GOOGLE }) or ({ wallet: WALLET_TYPE.APPLE })) indicating which button is unavailable
             * Important: Do not perform thread blocking operations in callback such as window.alert() calls.
             *
             * @example
             * button.onUnavailable(() => {
             *      console.log('No wallet buttons available');
             * });
             *
             * @example
             * button.onUnavailable().then(() => console.log('No wallet buttons available'));
             *
             * @example
             * button.onUnavailable(function (data) {console.log('data.wallet :: ', data.wallet)});
             *
             * @param {listener} [handler] - Function to be called when no button is available.
             */
        }, {
            key: "onUnavailable",
            value: function onUnavailable(handler) {
                var _this2 = this;
                if (typeof handler === 'function') return this.eventEmitter.subscribe(EVENT$1.UNAVAILABLE, handler);
                return new Promise(function (resolve) {
                    return _this2.eventEmitter.subscribe(EVENT$1.UNAVAILABLE, function () {
                        return resolve();
                    });
                });
            }
            /**
             * If the wallet performs some update in the checkout process, the function passed as parameter will be called.
             *
             * NOTE: make sure to call the `button.update(result)` method on handler completion.
             *
             * @example
             * button.onUpdate((data) => {
             *      button.update({ success: true });
             * });
             *
             * @example
             * button.onUpdate().then((data) => throw new Error());
             *
             * @param {listener} [handler] - Function to be called when the payment was successful.
             */
        }, {
            key: "onUpdate",
            value: function onUpdate(handler) {
                var _this3 = this;
                this.hasUpdateHandler = true;
                if (typeof handler === 'function') return this.eventEmitter.subscribe(EVENT$1.UPDATE, handler);
                return new Promise(function (resolve) {
                    return _this3.eventEmitter.subscribe(EVENT$1.UPDATE, function (data) {
                        return resolve(data);
                    });
                });
            }
            /**
             * If the payment was successful, the function passed as parameter will be called.
             * Important: Do not perform thread blocking operations in callback such as window.alert() calls.
             *
             * @example
             * button.onPaymentSuccessful((data) => {
             *      console.log('Payment successful!');
             * });
             *
             * @example
             * button.onPaymentSuccessful().then((data) => console.log('Payment successful!'));
             *
             * @param {listener} [handler] - Function to be called when the payment was successful.
             */
        }, {
            key: "onPaymentSuccessful",
            value: function onPaymentSuccessful(handler) {
                var _this4 = this;
                if (typeof handler === 'function') return this.eventEmitter.subscribe(EVENT$1.PAYMENT_SUCCESSFUL, handler);
                return new Promise(function (resolve) {
                    return _this4.eventEmitter.subscribe(EVENT$1.PAYMENT_SUCCESSFUL, function (data) {
                        return resolve(data);
                    });
                });
            }
            /**
             * If the payment was left in fraud review, the function passed as parameter will be called.
             * Important: Do not perform thread blocking operations in callback such as window.alert() calls.
             *
             * @example
             * button.onPaymentInReview((data) => {
             *      console.log('Payment in fraud review');
             * });
             *
             * @example
             * button.onPaymentInReview().then((data) => console.log('Payment in fraud review'));
             *
             * @param {listener} [handler] - Function to be called when the payment was left in fraud review status.
             */
        }, {
            key: "onPaymentInReview",
            value: function onPaymentInReview(handler) {
                var _this5 = this;
                if (typeof handler === 'function') return this.eventEmitter.subscribe(EVENT$1.PAYMENT_IN_REVIEW, handler);
                return new Promise(function (resolve) {
                    return _this5.eventEmitter.subscribe(EVENT$1.PAYMENT_IN_REVIEW, function (data) {
                        return resolve(data);
                    });
                });
            }
            /**
             * If the payment was not successful, the function passed as parameter will be called.
             * Important: Do not perform thread blocking operations in callback such as window.alert() calls.
             *
             * @example
             * button.onPaymentError((err) => {
             *      console.log('Payment not successful');
             * });
             *
             * @example
             * button.onPaymentError().then((err) => console.log('Payment not successful'));
             *
             * @param {listener} [handler] - Function to be called when the payment was not successful.
             */
        }, {
            key: "onPaymentError",
            value: function onPaymentError(handler) {
                var _this6 = this;
                if (typeof handler === 'function') return this.eventEmitter.subscribe(EVENT$1.PAYMENT_ERROR, handler);
                return new Promise(function (resolve) {
                    return _this6.eventEmitter.subscribe(EVENT$1.PAYMENT_ERROR, function (err) {
                        return resolve(err);
                    });
                });
            }
        }, {
            key: "setupServiceCallbacks",
            value: function setupServiceCallbacks() {
                this.setupUnavailableCallback();
                this.setupUpdateCallback();
                this.setupWalletCallback();
                this.setupPaymentCallback();
                this.setupPaymentSuccessCallback();
                this.setupPaymentInReviewCallback();
                this.setupPaymentErrorCallback();
            }
        }, {
            key: "setupUnavailableCallback",
            value: function setupUnavailableCallback() {
                var _this7 = this;
                this.service.on(WALLET_EVENT.UNAVAILABLE, function (eventData) {
                    return _this7.eventEmitter.emit(EVENT$1.UNAVAILABLE, {
                        event: EVENT$1.UNAVAILABLE,
                        data: eventData
                    });
                });
            }
        }, {
            key: "setupUpdateCallback",
            value: function setupUpdateCallback() {
                var _this8 = this;
                this.service.on(WALLET_EVENT.UPDATE, function (eventData) {
                    return _this8.hasUpdateHandler ? _this8.eventEmitter.emit(EVENT$1.UPDATE, {
                        event: EVENT$1.UPDATE,
                        data: eventData
                    }) : _this8.update({
                        success: true
                    });
                });
            }
        }, {
            key: "setupWalletCallback",
            value: function setupWalletCallback() {
                var _this9 = this;
                this.service.on(WALLET_EVENT.CALLBACK, function (eventData) {
                    var data = eventData.data,
                        onSuccess = eventData.onSuccess,
                        onError = eventData.onError;
                    _this9.api.charge().walletCallback(data).then(function (res) {
                        return onSuccess(res);
                    }, function (err) {
                        return onError(err.message);
                    });
                });
            }
        }, {
            key: "setupPaymentCallback",
            value: function setupPaymentCallback() {
                var _this10 = this;
                this.service.on(WALLET_EVENT.PAYMENT_METHOD_SELECTED, function (eventData) {
                    var data = eventData.data,
                        onSuccess = eventData.onSuccess,
                        onError = eventData.onError;
                    _this10.api.charge().walletCapture(data).then(function (captureResult) {
                        if (typeof onSuccess === 'function') onSuccess();
                        var event = captureResult.status === 'inreview' ? EVENT$1.PAYMENT_IN_REVIEW : EVENT$1.PAYMENT_SUCCESSFUL;
                        _this10.eventEmitter.emit(event, {
                            event: event,
                            data: _extends(_extends({}, captureResult), data.customer && {
                                payer_name: data.customer.payer_name,
                                payer_email: data.customer.payer_email,
                                payer_phone: data.customer.payer_phone
                            })
                        });
                    }, function (err) {
                        if (typeof onError === 'function') onError(err);
                        _this10.eventEmitter.emit(EVENT$1.PAYMENT_ERROR, {
                            event: EVENT$1.PAYMENT_ERROR,
                            data: err
                        });
                    });
                });
            }
        }, {
            key: "setupPaymentSuccessCallback",
            value: function setupPaymentSuccessCallback() {
                var _this11 = this;
                this.service.on(WALLET_EVENT.PAYMENT_SUCCESS, function (eventData) {
                    return _this11.eventEmitter.emit(EVENT$1.PAYMENT_SUCCESSFUL, {
                        event: EVENT$1.PAYMENT_SUCCESSFUL,
                        data: eventData
                    });
                });
            }
        }, {
            key: "setupPaymentInReviewCallback",
            value: function setupPaymentInReviewCallback() {
                var _this12 = this;
                this.service.on(WALLET_EVENT.PAYMENT_IN_REVIEW, function (eventData) {
                    return _this12.eventEmitter.emit(EVENT$1.PAYMENT_IN_REVIEW, {
                        event: EVENT$1.PAYMENT_IN_REVIEW,
                        data: eventData
                    });
                });
            }
        }, {
            key: "setupPaymentErrorCallback",
            value: function setupPaymentErrorCallback() {
                var _this13 = this;
                this.service.on(WALLET_EVENT.PAYMENT_ERROR, function (eventData) {
                    return _this13.eventEmitter.emit(EVENT$1.PAYMENT_ERROR, {
                        event: EVENT$1.PAYMENT_ERROR,
                        data: eventData
                    });
                });
            }
        }]);
        return WalletButtons;
    }();

    /**
     *
     * Class PaymentSourceWidget include method for for creating iframe url
     * @constructor
     *
     * @param {string} publicKey - PayDock users public key
     * @param {string} customer - PayDock's customer_id or customer_reference (In order to use the customer_reference, you must explicitly specify useReference as true)
     * @param {boolean} [useReference=false]
     *
     * @example
     * var widget = new PaymentSourceWidget('publicKey','customerId');
     * // or
     * var widget = new PaymentSourceWidget('publicKey', customerReference, true);
     **/
    var PaymentSourceWidget = /*#__PURE__*/function () {
        function PaymentSourceWidget(accessToken, queryToken) {
            _classCallCheck(this, PaymentSourceWidget);
            this.configs = [];
            this.configTokens = [];
            this.link = new Link(PAYMENT_SOURCE_LINK);
            this.link.setParams(_extends({
                query_token: queryToken
            }, AccessToken.validateJWT(accessToken) ? {
                access_token: accessToken
            } : {
                public_key: accessToken
            }));
            if (SDK.version) {
                this.link.setParams({
                    sdk_version: SDK.version,
                    sdk_type: SDK.type
                });
            }
        }
        /**
         * Object contain styles for widget
         *
         * @example
         * widget.setStyles({
         *       background_color: 'rgb(0, 0, 0)',
         *       border_color: 'yellow',
         *       text_color: '#FFFFAA',
         *       icon_size: 'small',
         *       font_size: '20px'
         *   });
         * @param {IStyles} fields - name of styles which can be shown in widget [STYLE]{@link STYLE}
         */
        _createClass(PaymentSourceWidget, [{
            key: "setStyles",
            value: function setStyles(styles) {
                for (var index in styles) {
                    if (styles.hasOwnProperty(index)) this.setStyle(index, styles[index]);
                }
            }
        }, {
            key: "setStyle",
            value: function setStyle(param, value) {
                if (ObjectHelper.values(STYLE).indexOf(param) !== -1) this.link.setParams(_defineProperty({}, param, value));else console.warn("Widget::setStyle[s: unsupported style param ".concat(param));
            }
            /**
             * Current method can set custom ID to identify the data in the future
             *
             * @example
             * widget.setRefId('id');
             *
             * @param {string} refId - custom id
             */
        }, {
            key: "setRefId",
            value: function setRefId(refId) {
                this.link.setParams({
                    ref_id: refId
                });
            }
            /**
             * Current method can set limit for payment sources count. In case when limit sets less then general count will be shown pagination buttons prev and next.
             *
             * @param {string} count - payment source count
             */
        }, {
            key: "setLimit",
            value: function setLimit(count) {
                this.link.setParams({
                    limit: count
                });
            }
            /**
             * Current method can change environment. By default environment = sandbox
             * Also we can change domain alias for this environment. By default domain_alias = paydock.com
             *
             * @example
             * widget.setEnv('production');
             * @param {string} env - sandbox, production
             * @param {string} [alias] - Own domain alias
             */
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.link.setEnv(env, alias);
                for (var index in this.configs) {
                    if (this.configs.hasOwnProperty(index)) this.configs[index].setEnv(env, alias);
                }
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                this.link.getEnv();
            }
            /**
             * Method for getting iframe's url
             */
        }, {
            key: "getIFrameUrl",
            value: function getIFrameUrl() {
                return this.link.getUrl();
            }
            /**
             * Show payment source inside widget only with requested gateway ids
             *
             *
             * @param {string[]} ids - List of gateway_id
             */
        }, {
            key: "filterByGatewayIds",
            value: function filterByGatewayIds(ids) {
                this.link.setParams({
                    gateway_ids: ids.join(',')
                });
            }
            /**
             *
             * Show payment source inside widget only with requested payment source types
             *
             * @param types - List of payment source types. Available parameters [PAYMENT_TYPE]{@link PAYMENT_TYPE}
             */
        }, {
            key: "filterByTypes",
            value: function filterByTypes(types) {
                var supportedTypes = [];
                var _iterator = _createForOfIteratorHelper(types),
                    _step;
                try {
                    for (_iterator.s(); !(_step = _iterator.n()).done;) {
                        var type = _step.value;
                        if (!types.hasOwnProperty(type)) continue;
                        if (ObjectHelper.values(PAYMENT_SOURCE_TYPE).indexOf(type) === -1) console.warn("PaymentSourceWidget::filterByTypes: unsupported type ".concat(type));else supportedTypes.push(type);
                    }
                } catch (err) {
                    _iterator.e(err);
                } finally {
                    _iterator.f();
                }
                this.link.setParams({
                    payment_source_types: supportedTypes.join(',')
                });
            }
            /**
             * Method for setting a custom language code
             *
             * @example
             * config.setLanguage('en');
             * @param {string} code - ISO 639-1
             */
        }, {
            key: "setLanguage",
            value: function setLanguage(code) {
                this.link.setParams({
                    language: code
                });
            }
        }]);
        return PaymentSourceWidget;
    }();

    /**
     * Interface of data from event.
     * @interface IEventSelectData
     *
     * @param {string} event
     * @param {string} purpose
     * @param {string} message_source
     * @param {string} [ref_id]
     * @param {string} customer_id
     * @param {string} payment_source_id
     * @param {string} gateway_id
     * @param {boolean} primary
     * @param {string} [widget_id]
     * @param {string} [card_number_last4]
     * @param {string} [card_scheme]
     * @param {string} gateway_type
     * @param {string} [checkout_email]
     * @param {string} payment_source_type
     * @param {string} [account_name]
     * @param {string} [account_number]
     * */
    /**
     * Interface of data from event.
     * @interface IEventPaginationData
     *
     * @param {string} event
     * @param {string} purpose
     * @param {string} message_source
     * @param {string} [ref_id]
     * @param {number} total_item
     * @param {number} skip
     * @param {number} limit
     * */
    /**
     * Interface of data from event.
     * @interface IEventAfterLoadData
     *
     * @param {string} event Event name
     * @param {string} purpose System variable. Purpose of event
     * @param {string} message_source System variable. Event source
     * @param {string} [ref_id] Custom value for identify result of processed operation
     * @param {number} total_item Pagination param. Total item count
     * @param {number} skip Pagination param. Skip items from first item
     * @param {number} limit Pagination param. Query limit
     * */
    /**
     * Interface of data from event.
     * @interface IEventFinishData
     *
     * @param {string} event Event name
     * @param {string} purpose System variable. Purpose of event
     * @param {string} message_source System variable. Event source
     * @param {string} [ref_id] Custom value for identify result of processed operation
     * */
    /**
     * Interface of data from event.
     * @interface IEventSizeData
     *
     * @param {number} event Event name
     * @param {number} purpose System variable. Purpose of event
     * @param {string} message_source System variable. Event source
     * @param {string} [ref_id] Custom value for identify result of processed operation
     * @param {number} height Height of iFrame
     * @param {number} width Width of iFrame
     * */
    /**
     * List of available event's name
     * @const EVENT
     *
     * @type {object}
     * @param {string} AFTER_LOAD=afterLoad
     * @param {string} SYSTEM_ERROR=systemError
     * @param {string} SELECT=select
     * @param {string} UNSELECT=unselect
     * @param {string} NEXT=next
     * @param {string} PREV=prev
     * @param {string} META_CHANGE=metaChange
     * @param {string} RESIZE=resize
     */
    /**
     * Class HtmlPaymentSourceWidget include method for working on html
     * @constructor
     * @extends PaymentSourceWidget
     *
     * @param {string} selector - Selector of html element. Container for widget
     * @param {string} publicKey - PayDock users public key
     * @param {string} queryToken - PayDock's query token that represents params to search customer by id or reference
     * @example
     *  * var widget = new HtmlPaymentSourceWidget('#widget', 'publicKey','queryToken');

     **/
    var HtmlPaymentSourceWidget = /*#__PURE__*/function (_PaymentSourceWidget) {
        _inherits(HtmlPaymentSourceWidget, _PaymentSourceWidget);
        var _super = _createSuper(HtmlPaymentSourceWidget);
        function HtmlPaymentSourceWidget(selector, publicKey, queryToken) {
            var _this;
            _classCallCheck(this, HtmlPaymentSourceWidget);
            _this = _super.call(this, publicKey, queryToken);
            _this.container = new Container(selector);
            _this.iFrame = new IFrame(_this.container);
            _this.event = new IFrameEvent(window);
            return _this;
        }
        /**
         * The final method to beginning, the load process of widget to html
         *
         */
        _createClass(HtmlPaymentSourceWidget, [{
            key: "load",
            value: function load() {
                this.iFrame.load(this.getIFrameUrl(), {
                    title: 'Payment Sources'
                });
            }
            /**
             * This callback will be called for each event in payment source widget
             *
             * @callback listener--PaymentSourceWidget
             * @param {IEventData | IEventSelectData | IEventPaginationData | IEventAfterLoadData} response
             */
            /**
             * Listen to events of widget
             *
             * @example
             *
             * widget.on('select', function (data) {
             *      console.log(data);
             * });
             * @param {string} eventName - Available event names [EVENT]{@link EVENT}
             * @param {listener--PaymentSourceWidget} cb
             */
        }, {
            key: "on",
            value: function on(eventName, cb) {
                this.event.on(eventName, this.link.getParams().widget_id, cb);
            }
            /**
             * Using this method you can hide widget after load
             * @param {boolean} [saveSize=false] - using this param you can save iframe's size
             */
        }, {
            key: "hide",
            value: function hide(saveSize) {
                console.info('PayDock SDK');
                this.iFrame.hide(saveSize);
            }
            /**
             * Using this method you can show widget after using hide method
             *
             */
        }, {
            key: "show",
            value: function show() {
                this.iFrame.show();
            }
            /**
             * Using this method you can reload widget
             *
             */
        }, {
            key: "reload",
            value: function reload() {
                this.iFrame.remove();
                this.load();
            }
            /**
             * After select event of widget, data (dataType) will be insert to input (selector)
             *
             * @param {string} selector - css selector . [] #
             * @param {string} dataType - data type of [IEventSelectData]{@link IEventSelectData}.
             */
        }, {
            key: "onSelectInsert",
            value: function onSelectInsert(selector, dataType) {
                this.on(EVENT.SELECT, function (event) {
                    Event.insertToInput(selector, dataType, event);
                });
            }
        }]);
        return HtmlPaymentSourceWidget;
    }(PaymentSourceWidget);

    var hiddenStyle = {
        visibility: "hidden",
        border: "0",
        width: "0",
        height: "0"
    };
    var PROCESS_STANDALONE_3DS_STATUS;
    (function (PROCESS_STANDALONE_3DS_STATUS) {
        PROCESS_STANDALONE_3DS_STATUS["SUCCESS"] = "success";
        PROCESS_STANDALONE_3DS_STATUS["ERROR"] = "error";
        PROCESS_STANDALONE_3DS_STATUS["PENDING"] = "pending";
    })(PROCESS_STANDALONE_3DS_STATUS || (PROCESS_STANDALONE_3DS_STATUS = {}));
    var GPAYMENTS_EVENT = {
        AUTH_SUCCESS: 'chargeAuthSuccess',
        AUTH_ERROR: 'chargeAuthReject',
        DECOUPLED: 'chargeAuthDecoupled',
        CHALLENGE: 'chargeAuthChallenge',
        INFO: 'chargeAuthInfo',
        ERROR: 'error'
    };
    var GPaymentsService = /*#__PURE__*/function () {
        function GPaymentsService(container, api, eventEmitter) {
            _classCallCheck(this, GPaymentsService);
            this.container = container;
            this.api = api;
            this.eventEmitter = eventEmitter;
            this.resultRead = false;
            this.iFrameEvent = new IFrameEvent(window);
        }
        _createClass(GPaymentsService, [{
            key: "load",
            value: function load(_ref, title) {
                var initialization_url = _ref.initialization_url,
                    secondary_url = _ref.secondary_url,
                    charge_3ds_id = _ref.charge_3ds_id;
                try {
                    this.setupIFrameEvents(charge_3ds_id);
                    this.initializeIFrames(initialization_url, secondary_url, title);
                } catch (err) {
                    this.eventEmitter.emit(GPAYMENTS_EVENT.ERROR, this.parseError(err, charge_3ds_id));
                }
            }
        }, {
            key: "initializeIFrames",
            value: function initializeIFrames(initializationUrl, secondaryUrl, title) {
                var hideAuthorization = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;
                var container = this.container.getElement();
                if (!container) return;
                var divAuthorization = document.createElement("div");
                divAuthorization.setAttribute("id", "paydock_authorization_iframe");
                container.appendChild(divAuthorization);
                this.browserAndChallengeContainer = new Container("#paydock_authorization_iframe");
                this.iFrameAuthorization = new IFrame(this.browserAndChallengeContainer);
                this.iFrameAuthorization.load(initializationUrl, {
                    title: title
                });
                if (secondaryUrl) {
                    var divSecondaryURL = document.createElement("div");
                    divSecondaryURL.setAttribute("id", "paydock_secondary_iframe");
                    container.appendChild(divSecondaryURL);
                    this.monitoringContainer = new Container("#paydock_secondary_iframe");
                    this.iFrameSecondaryUrl = new IFrame(this.monitoringContainer);
                    this.iFrameSecondaryUrl.load(secondaryUrl, {
                        title: title
                    });
                } else {
                    this.iFrameSecondaryUrl = undefined;
                }
                this.hideIframes(hideAuthorization);
            }
        }, {
            key: "hideIframes",
            value: function hideIframes() {
                var hideAuthorization = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
                var _a;
                for (var prop in hiddenStyle) {
                    if (!hiddenStyle.hasOwnProperty(prop)) continue;
                    if (hideAuthorization) this.iFrameAuthorization.setStyle(prop, hiddenStyle[prop]);
                    (_a = this.iFrameSecondaryUrl) === null || _a === void 0 ? void 0 : _a.setStyle(prop, hiddenStyle[prop]);
                }
            }
        }, {
            key: "setupIFrameEvents",
            value: function setupIFrameEvents(widgetId) {
                var _this = this;
                this.iFrameEvent.on(EVENT.CHARGE_AUTH, widgetId, function (data) {
                    if (data.status === "MethodSkipped" /* SKIPPED */ || data.status === "MethodFinished" /* FINISHED */) {
                        if (data.info) _this.eventEmitter.emit(GPAYMENTS_EVENT.INFO, {
                            info: data.info
                        });
                        _this.performAuthentication(data);
                    } else if (data.status === "AuthTimedOut" /* TIMEOUT */ || data.status === "invalid_event" /* INVALID */) _this.eventEmitter.emit(GPAYMENTS_EVENT.AUTH_ERROR, _this.parseHandleResponse({
                        status: data.status
                    }, data.charge_3ds_id));
                });
                this.iFrameEvent.on(EVENT.CHARGE_AUTH_SUCCESS, widgetId, function (data) {
                    _this.processResult(data.charge_3ds_id);
                });
            }
        }, {
            key: "parseResultData",
            value: function parseResultData(_ref2, charge3dsId) {
                var status = _ref2.status;
                return {
                    status: status,
                    charge_3ds_id: charge3dsId
                };
            }
        }, {
            key: "parseHandleResponse",
            value: function parseHandleResponse(_ref3, charge3dsId) {
                var status = _ref3.status,
                    result = _ref3.result;
                return {
                    status: status,
                    charge_3ds_id: charge3dsId,
                    result: {
                        description: result === null || result === void 0 ? void 0 : result.description
                    }
                };
            }
        }, {
            key: "parseError",
            value: function parseError(data, charge3dsId) {
                return {
                    charge_3ds_id: charge3dsId,
                    error: data
                };
            }
        }, {
            key: "processResult",
            value: function processResult(charge3dsId) {
                var _this2 = this;
                if (this.resultRead) return;
                this.resultRead = true;
                this.api.charge().standalone3dsHandle().then(function (result) {
                    var _a;
                    _this2.iFrameAuthorization.remove();
                    (_a = _this2.iFrameSecondaryUrl) === null || _a === void 0 ? void 0 : _a.remove();
                    if (result.status === PROCESS_STANDALONE_3DS_STATUS.SUCCESS) _this2.eventEmitter.emit(GPAYMENTS_EVENT.AUTH_SUCCESS, _this2.parseResultData(result, charge3dsId));else _this2.eventEmitter.emit(GPAYMENTS_EVENT.AUTH_ERROR, _this2.parseResultData(result, charge3dsId));
                }, function (err) {
                    _this2.eventEmitter.emit(GPAYMENTS_EVENT.ERROR, _this2.parseError(err, charge3dsId));
                });
            }
        }, {
            key: "externalAPI",
            value: function externalAPI(method, url) {
                var request = new XMLHttpRequest();
                request.open(method, url, true);
                return new Promise(function (resolve, reject) {
                    request.onload = function () {
                        try {
                            var body = JSON.parse(request.responseText);
                            resolve(body);
                        } catch (error) {
                            reject(error);
                        }
                    };
                    request.send();
                });
            }
        }, {
            key: "doPolling",
            value: function doPolling(url, charge3dsId) {
                var _this3 = this;
                this.externalAPI("GET", url).then(function (data) {
                    if (!data.event || data.event === "AuthResultNotReady") setTimeout(function () {
                        _this3.doPolling(url, charge3dsId);
                    }, 2000);else if (data.event === 'AuthResultReady') _this3.processResult(charge3dsId);else throw new Error("Event not supported");
                })["catch"](function (err) {
                    return _this3.eventEmitter.emit(GPAYMENTS_EVENT.ERROR, _this3.parseError(err, charge3dsId));
                });
            }
        }, {
            key: "performAuthentication",
            value: function performAuthentication(_ref4) {
                var _this4 = this;
                var charge_3ds_id = _ref4.charge_3ds_id;
                var _a;
                this.iFrameAuthorization.remove();
                (_a = this.iFrameSecondaryUrl) === null || _a === void 0 ? void 0 : _a.remove();
                this.api.charge().standalone3dsProcess({
                    charge_3ds_id: charge_3ds_id
                }).then(function (authenticationResult) {
                    var _a, _b;
                    if (authenticationResult.status === "success" /* SUCCESS */) _this4.eventEmitter.emit(GPAYMENTS_EVENT.AUTH_SUCCESS, _this4.parseHandleResponse(authenticationResult, charge_3ds_id));else if (authenticationResult.status === "pending" /* PENDING */) {
                        if ((_a = authenticationResult === null || authenticationResult === void 0 ? void 0 : authenticationResult.result) === null || _a === void 0 ? void 0 : _a.challenge) {
                            _this4.eventEmitter.emit(GPAYMENTS_EVENT.CHALLENGE, _this4.parseHandleResponse(authenticationResult, charge_3ds_id));
                            _this4.initializeIFrames(authenticationResult.result.challenge_url, undefined, 'Authentication Challenge', false);
                            if (authenticationResult.result.secondary_url) _this4.doPolling(authenticationResult.result.secondary_url, charge_3ds_id);
                        } else if ((_b = authenticationResult === null || authenticationResult === void 0 ? void 0 : authenticationResult.result) === null || _b === void 0 ? void 0 : _b.decoupled_challenge) {
                            _this4.eventEmitter.emit(GPAYMENTS_EVENT.DECOUPLED, _this4.parseHandleResponse(authenticationResult, charge_3ds_id));
                            if (authenticationResult.result.secondary_url) _this4.doPolling(authenticationResult.result.secondary_url, charge_3ds_id);
                        }
                    } else return _this4.eventEmitter.emit(GPAYMENTS_EVENT.AUTH_ERROR, _this4.parseHandleResponse(authenticationResult, charge_3ds_id));
                }, function (err) {
                    _this4.eventEmitter.emit(GPAYMENTS_EVENT.ERROR, _this4.parseError(err, charge_3ds_id));
                });
            }
        }]);
        return GPaymentsService;
    }();

    var STANDALONE_3DS_GATEWAYS = {
        GPAYMENTS: "GPayments"
    };
    var Standalone3dsService = /*#__PURE__*/function () {
        function Standalone3dsService(container, eventEmitter) {
            _classCallCheck(this, Standalone3dsService);
            this.env = DEFAULT_ENV;
            this.container = container;
            this.eventEmitter = eventEmitter;
        }
        _createClass(Standalone3dsService, [{
            key: "load",
            value: function load(token, options) {
                var parsedToken = AccessToken.validateJWT(token);
                if (!parsedToken) throw new Error("Invalid charge token");
                var tokenData = AccessToken.extractData(parsedToken.body);
                var api = new ApiInternal(token, API_AUTH_TYPE.TOKEN);
                api.setEnv(this.env, this.alias);
                switch (tokenData.service_type) {
                    case STANDALONE_3DS_GATEWAYS.GPAYMENTS:
                        new GPaymentsService(this.container, api, this.eventEmitter).load(tokenData, options.title);
                        break;
                }
            }
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.env = env;
                this.alias = alias;
            }
        }]);
        return Standalone3dsService;
    }();

    /**
     * List of available token's content formats
     * @enum TOKEN_FORMAT
     *
     * @type {object}
     * @param {string} HTML=html
     */
    var TOKEN_FORMAT;
    (function (TOKEN_FORMAT) {
        TOKEN_FORMAT["HTML"] = "html";
        TOKEN_FORMAT["URL"] = "url";
        TOKEN_FORMAT["STANDALONE_3DS"] = "standalone_3ds";
    })(TOKEN_FORMAT || (TOKEN_FORMAT = {}));
    /**
     * List of available event's name
     * @const EVENT
     *
     * @type {object}
     * @param {string} CHARGE_AUTH_SUCCESS=chargeAuthSuccess
     * @param {string} CHARGE_AUTH_REJECT=chargeAuthReject
     * @param {string} ADDITIONAL_DATA_SUCCESS=additionalDataCollectSuccess
     * @param {string} ADDITIONAL_DATA_REJECT=additionalDataCollectReject
     * @param {string} CHARGE_AUTH=chargeAuth
     */
    /**
     * List of available event's name for Standalone 3ds flow
     * @const STANDALONE_3DS_EVENT
     *
     * @type {object}
     * @param {string} CHARGE_AUTH_SUCCESS=chargeAuthSuccess
     * @param {string} CHARGE_AUTH_REJECT=chargeAuthReject
     * @param {string} CHARGE_AUTH_DECOUPLED=chargeAuthDecoupled
     * @param {string} CHARGE_AUTH_CHALLENGE=chargeAuthChallenge
     * @param {string} CHARGE_AUTH_INFO=chargeAuthInfo
     * @param {string} ERROR=error
     */
    /**
     * Class Canvas3ds include method for working on html
     * @constructor
     *
     * @param {string} selector - Selector of html element. Container for widget
     * @param {string} token - Pre authorized token
     * @example
     * var widget = new Canvas3ds('#widget', 'token');
     *
     *
     **/
    var Canvas3ds = /*#__PURE__*/function () {
        function Canvas3ds(selector, token) {
            _classCallCheck(this, Canvas3ds);
            this.configs = [];
            this.link = new Link(SECURE_3D);
            this.token = Canvas3ds.extractToken(token);
            this.link.setParams({
                ref_id: this.token.charge_3ds_id
            });
            this.container = new Container(selector);
            this.iFrame = new IFrame(this.container);
            this.eventEmitter = new EventEmitter();
            this.standalone3dsService = new Standalone3dsService(this.container, this.eventEmitter);
            this.event = new IFrameEvent(window);
        }
        _createClass(Canvas3ds, [{
            key: "load",
            value:
                /**
                 * The final method to beginning, the load process of widget to html
                 *
                 */
                function load() {
                    if (this.token.format === TOKEN_FORMAT.HTML) this.iFrame.loadFromHtml(this.token.content, {
                        title: '3d secure authentication'
                    });else if (this.token.format === TOKEN_FORMAT.URL) this.iFrame.load(this.token.content, {
                        title: '3d secure authentication'
                    });else if (this.token.format === TOKEN_FORMAT.STANDALONE_3DS) this.standalone3dsService.load(this.token.content, {
                        title: '3d secure authentication'
                    });else console.error('Token contain unsupported payload');
                }
            /**
             * Current method can change environment. By default environment = sandbox.
             * Also we can change domain alias for this environment. By default domain_alias = paydock.com
             *
             * @example
             * widget.setEnv('production');
             * @param {string} env - sandbox, production
             * @param {string} [alias] - Own domain alias
             */
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.link.setEnv(env, alias);
                this.standalone3dsService.setEnv(env, alias);
                for (var index in this.configs) {
                    if (!this.configs.hasOwnProperty(index)) continue;
                    this.configs[index].setEnv(env, alias);
                }
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                return this.link.getEnv();
            }
            /**
             * Listen to events of widget
             *
             * @example
             *
             * widget.on('chargeAuthReject', function (data) {
             *      console.log(data);
             * });
             * // or
             * widget.on('chargeAuthReject').then(function (data) {
             *      console.log(data);
             * });
             * @param {string} eventName - Available event names [EVENT]{@link EVENT} [STANDALONE_3DS_EVENT]{@link STANDALONE_3DS_EVENT}
             * @param {listener} [cb]
             * @return {Promise<IEventData> | void}
             */
        }, {
            key: "on",
            value: function on(eventName, cb) {
                var _this = this;
                if (this.token.format === TOKEN_FORMAT.STANDALONE_3DS) {
                    if (typeof cb === 'function') return this.eventEmitter.subscribe(eventName, cb);
                    return new Promise(function (resolve) {
                        return _this.eventEmitter.subscribe(eventName, function (res) {
                            return resolve(res);
                        });
                    });
                } else {
                    if (typeof cb === 'function') return this.event.on(eventName, this.link.getParams().ref_id, cb);
                    return new Promise(function (resolve) {
                        return _this.event.on(eventName, _this.link.getParams().ref_id, function (res) {
                            return resolve(res);
                        });
                    });
                }
            }
            /**
             * Using this method you can hide widget after load
             * @param {boolean} [saveSize=false] - using this param you can save iframe's size
             */
        }, {
            key: "hide",
            value: function hide(saveSize) {
                this.iFrame.hide(saveSize);
            }
            /**
             * Using this method you can show widget after using hide method
             *
             */
        }, {
            key: "show",
            value: function show() {
                this.iFrame.show();
            }
            /**
             * Using this method you can reload widget
             *
             */
        }, {
            key: "reload",
            value: function reload() {
                this.iFrame.remove();
                this.load();
            }
        }], [{
            key: "extractToken",
            value: function extractToken(token) {
                return JSON.parse(window.atob(token));
            }
        }]);
        return Canvas3ds;
    }();

    var PRE_AUTH_CHARGE_LINK = '/v1/charges/3ds';
    var ApiCharge = /*#__PURE__*/function () {
        function ApiCharge(api) {
            _classCallCheck(this, ApiCharge);
            this.api = api;
        }
        /**
         * Current method allows to work with charge related endpoints
         *
         * @example
         * api.charge().preAuth('payload', cb);
         *
         * @param {object} payload - Payload for pre authorization.
         * @param {number} payload.amount - Charge amount.
         * @param {string} payload.currency - Charge currency.
         * @param {string} payload.token - Payment source token.
         * @param {string} [payload._3ds.redirect_url] - Redirect url after 3d secure processing.
         * @param {listener} [cb]
         */
        _createClass(ApiCharge, [{
            key: "preAuth",
            value: function preAuth(payload, cb) {
                if (typeof cb === 'function') return this.api.getClient('POST', PRE_AUTH_CHARGE_LINK).send(_extends(_extends({}, payload), {
                    _3ds: _extends(_extends({}, payload._3ds), {
                        browser_details: {
                            name: Browser.getBrowserName(),
                            java_enabled: Browser.isJavaEnabled().toString(),
                            language: Browser.getLanguage(),
                            screen_height: Browser.getScreenHeight().toString(),
                            screen_width: Browser.getScreenWidth().toString(),
                            time_zone: Browser.getTimezoneOffset().toString(),
                            color_depth: Browser.getColorDepth().toString()
                        }
                    })
                }), function (data) {
                    cb(data);
                });
                return this.api.getClientPromise('POST', PRE_AUTH_CHARGE_LINK).send(_extends(_extends({}, payload), {
                    _3ds: _extends(_extends({}, payload._3ds), {
                        browser_details: {
                            name: Browser.getBrowserName(),
                            java_enabled: Browser.isJavaEnabled().toString(),
                            language: Browser.getLanguage(),
                            screen_height: Browser.getScreenHeight().toString(),
                            screen_width: Browser.getScreenWidth().toString(),
                            time_zone: Browser.getTimezoneOffset().toString(),
                            color_depth: Browser.getColorDepth().toString()
                        }
                    })
                }));
            }
        }]);
        return ApiCharge;
    }();

    /**
     * Interface for browser details response.
     * @interface BrowserDetails
     *
     * @param {string} [name]
     * @param {string} [java_enabled]
     * @param {string} [language]
     * @param {string} [screen_height]
     * @param {string} [screen_width]
     * @param {string} [time_zone]
     * @param {string} [color_depth]
     * */
    /**
     * Class Api include method for working with paydock api
     * @constructor
     *
     * @param {string} publicKey - PayDock users public key
     * @example
     * var api = new Api('publicKey');
     *
     *
     **/
    var Api = /*#__PURE__*/function (_ApiBase) {
        _inherits(Api, _ApiBase);
        var _super = _createSuper(Api);
        function Api(publicKey) {
            var _this;
            _classCallCheck(this, Api);
            _this = _super.call(this, publicKey);
            _this.publicKey = _this.auth;
            return _this;
        }
        /**
         * Method for getting browser details
         *
         * @example
         * api.getBrowserDetails();
         *
         * @return {BrowserDetails} Browser details object
         */
        _createClass(Api, [{
            key: "getBrowserDetails",
            value: function getBrowserDetails() {
                return {
                    name: Browser.getBrowserName(),
                    java_enabled: Browser.isJavaEnabled().toString(),
                    language: Browser.getLanguage(),
                    screen_height: Browser.getScreenHeight().toString(),
                    screen_width: Browser.getScreenWidth().toString(),
                    time_zone: Browser.getTimezoneOffset().toString(),
                    color_depth: Browser.getColorDepth().toString()
                };
            }
            /**
             * Current method allows to work with charge related endpoints
             *
             * @example
             * api.charge();
             */
        }, {
            key: "charge",
            value: function charge() {
                return new ApiCharge(this);
            }
        }]);
        return Api;
    }(ApiBase);

    var VAULT_DISPLAY_EVENT;
    (function (VAULT_DISPLAY_EVENT) {
        VAULT_DISPLAY_EVENT["AFTER_LOAD"] = "after_load";
        VAULT_DISPLAY_EVENT["SYSTEM_ERROR"] = "system_error";
        VAULT_DISPLAY_EVENT["CVV_SECURE_CODE_REQUESTED"] = "cvv_secure_code_requested";
        VAULT_DISPLAY_EVENT["CARD_NUMBER_SECURE_CODE_REQUESTED"] = "card_number_secure_code_requested";
        VAULT_DISPLAY_EVENT["ACCESS_FORBIDDEN"] = "access_forbidden";
        VAULT_DISPLAY_EVENT["SESSION_EXPIRED"] = "session_expired";
        VAULT_DISPLAY_EVENT["OPERATION_FORBIDDEN"] = "operation_forbidden";
    })(VAULT_DISPLAY_EVENT || (VAULT_DISPLAY_EVENT = {}));
    var VaultDisplayIframeEvent = /*#__PURE__*/function (_IFrameEvent) {
        _inherits(VaultDisplayIframeEvent, _IFrameEvent);
        var _super = _createSuper(VaultDisplayIframeEvent);
        function VaultDisplayIframeEvent() {
            _classCallCheck(this, VaultDisplayIframeEvent);
            return _super.apply(this, arguments);
        }
        _createClass(VaultDisplayIframeEvent, [{
            key: "on",
            value: function on(eventName, widgetId, cb) {
                for (var event in VAULT_DISPLAY_EVENT) {
                    if (!VAULT_DISPLAY_EVENT.hasOwnProperty(event)) continue;
                    if (eventName === VAULT_DISPLAY_EVENT[event]) {
                        this.listeners.push({
                            event: eventName,
                            listener: cb,
                            widget_id: widgetId
                        });
                    }
                }
            }
        }]);
        return VaultDisplayIframeEvent;
    }(IFrameEvent);

    /**
     * Class VaultDisplayWidget include method for working on html
     * @constructor
     *
     * @example
     * var widget = new VaultDisplayWidget('#widget', 'token');
     *
     * @param {string} selector - Selector of html element. Container for widget
     * @param {string} token - One time token
     **/
    var VaultDisplayWidget = /*#__PURE__*/function () {
        function VaultDisplayWidget(selector, token) {
            _classCallCheck(this, VaultDisplayWidget);
            this.validationData = {};
            this.configs = [];
            this.container = new Container(selector);
            this.iFrame = new IFrame(this.container);
            this.triggerElement = new Trigger(this.iFrame);
            this.event = new VaultDisplayIframeEvent(window);
            this.vaultDisplayToken = token;
            this.link = new Link(VAULT_DISPLAY_WIDGET_LINK);
            this.link.setParams({
                vault_display_token: token
            });
        }
        /**
         * Current method can change environment. By default environment = sandbox.
         * Also we can change domain alias for this environment. By default domain_alias = paydock.com
         *
         * @example
         * widget.setEnv('production', 'paydock.com');
         * @param {string} env - sandbox, production
         * @param {string} [alias] - Own domain alias
         */
        _createClass(VaultDisplayWidget, [{
            key: "setEnv",
            value: function setEnv(env, alias) {
                this.link.setEnv(env, alias);
            }
            /**
             * Listen to events of widget
             *
             * @example
             *
             * widget.on('after_load', function (data) {
             *      console.log(data);
             * });
             * // or
             *  widget.on('after_load').then(function (data) {
             *      console.log(data);
             * });
             * @param {string} eventName - Available event names [VAULT_DISPLAY_EVENT]{@link VAULT_DISPLAY_EVENT}
             * @param {listener} [cb]
             * @return {Promise<IEventData | void>}
             */
        }, {
            key: "on",
            value: function on(eventName, cb) {
                var _this = this;
                if (typeof cb === "function") return this.event.on(eventName, this.link.getParams().widget_id, cb);
                return new Promise(function (resolve) {
                    return _this.event.on(eventName, _this.link.getParams().widget_id, function (res) {
                        return resolve(res);
                    });
                });
            }
            /**
             * Object contain styles for widget
             *
             * @example
             * widget.setStyles({
             *       background_color: '#fff',
             *       border_color: 'yellow',
             *       text_color: '#FFFFAA',
             *       button_color: 'rgba(255, 255, 255, 0.9)',
             *       font_size: '20px',
             *       fort_family: 'fantasy'
             *   });
             * @param {VaultDisplayStyle} fields - name of styles which can be shown in widget [VAULT_DISPLAY_STYLE]{@link VAULT_DISPLAY_STYLE}
             */
        }, {
            key: "setStyles",
            value: function setStyles(styles) {
                for (var index in styles) {
                    if (styles.hasOwnProperty(index)) this.setStyle(index, styles[index]);
                }
            }
        }, {
            key: "setStyle",
            value: function setStyle(param, value) {
                if (ObjectHelper.values(STYLE).indexOf(param) !== -1) this.link.setParams(_defineProperty({}, param, value));else console.warn("Widget::setStyle[s: unsupported style param ".concat(param));
            }
            /**
             * The final method to beginning, the load process of widget to html
             *
             */
        }, {
            key: "load",
            value: function load() {
                this.iFrame.load(this.link.getUrl(), {
                    title: 'Vault Display'
                });
            }
        }]);
        return VaultDisplayWidget;
    }();

    var VisaSRCStyles = /*#__PURE__*/_createClass(function VisaSRCStyles() {
        _classCallCheck(this, VisaSRCStyles);
    });
    VisaSRCStyles.buttonContainerStyles = "display: flex; flex-direction: column; justify-content: center; align-items: center;";
    VisaSRCStyles.buttonStyles = "color: #ffff; background-color: #ffbe24; border: none; width: 100%; min-height: 40px; font-size: 16px; font-weight: bold; line-height: 19px; letter-spacing: 0.7px; text-transform: uppercase; border-radius: 4px; margin-bottom: 15px; cursor: pointer;";
    VisaSRCStyles.footerContainerStyles = "display: flex; flex: 1; flex-wrap: wrap; justify-content: center;";
    VisaSRCStyles.footerTextStyles = "text-align: center; color: #666666; margin: 2px 0;";
    VisaSRCStyles.verticalLineStyle = "display: inline-block; padding: 0.5px; background-color: #E5E5E5; height: 15px;";
    VisaSRCStyles.clickToPayAllCardsStyle = "height: 17px; margin-left: 8px; vertical-align: middle; padding-top: 3px;";
    // TODO: Remind to add in docs that if merchant's div is smaller than 240px the button styles will broke

    /**
     * List of available event's name in the SRC checkout lifecycle
     * @enum EVENT
     *
     * @type {object}
     * @param {string} CHECKOUT_BUTTON_LOADED=checkoutButtonLoaded
     * @param {string} CHECKOUT_BUTTON_CLICKED=checkoutButtonClicked
     * @param {string} IFRAME_LOADED=iframeLoaded
     * @param {string} CHECKOUT_READY=checkoutReady
     * @param {string} CHECKOUT_COMPLETED=checkoutCompleted
     * @param {string} CHECKOUT_ERROR=checkoutError
     */
    var EVENT$2;
    (function (EVENT) {
        EVENT["CHECKOUT_BUTTON_LOADED"] = "checkoutButtonLoaded";
        EVENT["CHECKOUT_BUTTON_CLICKED"] = "checkoutButtonClicked";
        EVENT["IFRAME_LOADED"] = "iframeLoaded";
        EVENT["CHECKOUT_READY"] = "checkoutReady";
        EVENT["CHECKOUT_COMPLETED"] = "checkoutCompleted";
        EVENT["CHECKOUT_ERROR"] = "checkoutError";
    })(EVENT$2 || (EVENT$2 = {}));
    var STYLE$2 = {
        BUTTON_TEXT_COLOR: 'button_text_color',
        PRIMARY_COLOR: 'primary_color',
        FONT_FAMILY: 'font_family',
        CARD_SCHEMES: 'card_schemes'
    };

    var CARD_SCHEME = {
        VISA: 'visa',
        MASTERCARD: 'mastercard',
        AMEX: 'amex',
        DISCOVER: 'discover'
    };
    var CHEVRON_VMAD_IMG_PATH = '/images/visa-src/Chevron_Large_VMAD.png';
    var CHEVRON_VMA_IMG_PATH = '/images/visa-src/Chevron_Large_VMA.png';
    var CHEVRON_VM_IMG_PATH = '/images/visa-src/Chevron_Large_VM.png';
    var CHEVRON_M_IMG_PATH = '/images/visa-src/Chevron_Large_M.png';
    var CHEVRON_D_IMG_PATH = '/images/visa-src/Chevron_Large_D.png';
    var CHEVRON_V_IMG_PATH = '/images/visa-src/Chevron_Large_V.png';
    var VMAD_IMG_PATH = '/images/visa-src/vmad.svg';
    var VMA_IMG_PATH = '/images/visa-src/logos/Networks_Large_VMA.png';
    var VM_IMG_PATH = '/images/visa-src/logos/Networks_Large_VM.png';
    var M_IMG_PATH = '/images/visa-src/logos/master-logo.png';
    var D_IMG_PATH = '/images/visa-src/logos/Networks_Large_D.png';
    var V_IMG_PATH = '/images/visa-src/logos/visa-logo.png';
    var GenerateCardSchemesLogo = function GenerateCardSchemesLogo(card_scheme) {
        var chevron = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
        if (!card_scheme || !Array.isArray(card_scheme)) return chevron ? CHEVRON_VMAD_IMG_PATH : VMAD_IMG_PATH;
        var card_scheme_sort = card_scheme.sort();
        if (includeArray([CARD_SCHEME.AMEX, CARD_SCHEME.MASTERCARD, CARD_SCHEME.VISA], card_scheme_sort)) return chevron ? CHEVRON_VMA_IMG_PATH : VMA_IMG_PATH;
        if (includeArray([CARD_SCHEME.MASTERCARD, CARD_SCHEME.VISA], card_scheme_sort)) return chevron ? CHEVRON_VM_IMG_PATH : VM_IMG_PATH;
        if (includeArray([CARD_SCHEME.MASTERCARD], card_scheme_sort)) return chevron ? CHEVRON_M_IMG_PATH : M_IMG_PATH;
        if (includeArray([CARD_SCHEME.DISCOVER], card_scheme_sort)) return chevron ? CHEVRON_D_IMG_PATH : D_IMG_PATH;
        if (includeArray([CARD_SCHEME.VISA], card_scheme_sort)) return chevron ? CHEVRON_V_IMG_PATH : V_IMG_PATH;
        return chevron ? CHEVRON_V_IMG_PATH : VMAD_IMG_PATH;
    };
    var includeArray = function includeArray(condition, card_scheme) {
        return condition.every(function (card, index) {
            return card === card_scheme[index];
        });
    };

    var VisaSRC = /*#__PURE__*/function () {
        function VisaSRC(button_selector, iframe_selector, service_id, public_key, meta, eventEmitter, autoResize, env, alias) {
            _classCallCheck(this, VisaSRC);
            this.meta = meta;
            this.eventEmitter = eventEmitter;
            this.autoResize = autoResize;
            this.link = new Link(VISA_SRC);
            this.link.setParams(_extends({
                service_id: service_id,
                public_key: public_key
            }, meta && {
                meta: JSON.stringify(meta)
            }));
            if (SDK.version) {
                this.link.setParams({
                    sdk_version: SDK.version,
                    sdk_type: SDK.type
                });
            }
            if (env) this.link.setEnv(env, alias);
            this.iFrameContainer = new Container(iframe_selector);
            this.iFrame = new IFrame(this.iFrameContainer);
            this.buttonContainer = new Container(button_selector);
            this.iFrameEvent = new IFrameEvent(window);
            this.setupIFrameEvents();
        }
        _createClass(VisaSRC, [{
            key: "setupIFrameEvents",
            value: function setupIFrameEvents() {
                var _this = this;
                var widgetId = this.link.getParams().widget_id;
                this.iFrameEvent.on(EVENT$2.CHECKOUT_READY, widgetId, function (_ref) {
                    var data = _ref.data;
                    _this.eventEmitter.emit(EVENT$2.CHECKOUT_READY, data);
                });
                this.iFrameEvent.on(EVENT$2.CHECKOUT_COMPLETED, widgetId, function (_ref2) {
                    var data = _ref2.data;
                    _this.eventEmitter.emit(EVENT$2.CHECKOUT_COMPLETED, data);
                });
                this.iFrameEvent.on(EVENT$2.CHECKOUT_ERROR, widgetId, function (_ref3) {
                    var data = _ref3.data;
                    _this.eventEmitter.emit(EVENT$2.CHECKOUT_ERROR, data);
                });
                if (this.autoResize) this.useAutoResize(true);
            }
        }, {
            key: "load",
            value: function load() {
                var _this2 = this;
                var _a;
                var container = document.createElement('div');
                container.setAttribute('style', VisaSRCStyles.buttonContainerStyles);
                var checkoutButton = document.createElement('button');
                checkoutButton.setAttribute('style', VisaSRCStyles.buttonStyles);
                if (this.meta.customizations.primary_color) checkoutButton.style.backgroundColor = this.meta.customizations.primary_color;
                if (this.meta.customizations.button_text_color) checkoutButton.style.color = this.meta.customizations.button_text_color;
                checkoutButton.innerHTML = 'Checkout';
                var footerContainer = document.createElement('div');
                footerContainer.setAttribute('style', VisaSRCStyles.footerContainerStyles);
                var verticalLine = document.createElement('div');
                verticalLine.setAttribute('style', VisaSRCStyles.verticalLineStyle);
                var footerText = document.createElement('p');
                footerText.setAttribute('style', VisaSRCStyles.footerTextStyles);
                footerText.innerHTML = 'WE ACCEPT';
                var clickToPayAllCards = document.createElement('img');
                clickToPayAllCards.setAttribute('style', VisaSRCStyles.clickToPayAllCardsStyle);
                clickToPayAllCards.src = this.link.getBaseUrl() + "".concat(GenerateCardSchemesLogo((_a = this.meta.customizations) === null || _a === void 0 ? void 0 : _a.card_schemes, true));
                checkoutButton.onclick = function () {
                    _this2.eventEmitter.emit(EVENT$2.CHECKOUT_BUTTON_CLICKED, {});
                    _this2.iFrame.load(_this2.link.getUrl(), {
                        title: 'Visa SRC checkout'
                    });
                    _this2.iFrame.getElement().onload = function () {
                        return _this2.eventEmitter.emit(EVENT$2.IFRAME_LOADED, {});
                    };
                };
                container.appendChild(checkoutButton);
                container.appendChild(footerContainer);
                footerContainer.appendChild(footerText);
                footerContainer.appendChild(clickToPayAllCards);
                this.buttonContainer.getElement().appendChild(container);
                this.eventEmitter.emit(EVENT$2.CHECKOUT_BUTTON_LOADED, {});
            }
        }, {
            key: "getEnv",
            value: function getEnv() {
                return this.link.getEnv();
            }
        }, {
            key: "hideButton",
            value: function hideButton(_saveSize) {
                if (this.buttonContainer.getElement()) this.buttonContainer.getElement().style['display'] = 'none';
            }
        }, {
            key: "showButton",
            value: function showButton() {
                if (this.buttonContainer.getElement()) this.buttonContainer.getElement().style['display'] = 'block';
            }
        }, {
            key: "hideCheckout",
            value: function hideCheckout(saveSize) {
                if (this.iFrame) this.iFrame.hide();
            }
        }, {
            key: "showCheckout",
            value: function showCheckout() {
                if (this.iFrame) this.iFrame.show();
            }
        }, {
            key: "reload",
            value: function reload() {
                this.iFrame.remove();
                this.load();
            }
        }, {
            key: "useAutoResize",
            value: function useAutoResize(force) {
                var _this3 = this;
                if (this.autoResize && !force) return;
                this.autoResize = true;
                this.iFrameEvent.on('resize', this.link.getParams().widget_id, function (_ref4) {
                    var data = _ref4.data;
                    if (_this3.iFrame.getElement()) {
                        _this3.iFrame.getElement().scrolling = 'no';
                        if (data.height) _this3.iFrame.setStyle('height', data.height + 'px');
                    }
                });
            }
        }]);
        return VisaSRC;
    }();

    /**
     * Interface of data used for the Visa Checkout.
     * @interface IVisaSRCMeta
     *
     * @type {object}
     * @param {string} [srci_transaction_id] Used to identify the SRC Id.
     * @param {object} [dpa_data] Object where the DPA creation data is stored.
     * @param {string} [dpa_data.dpa_presentation_name] Name in which the DPA is presented in.
     * @param {string} [dpa_data.dpa_uri] Used for indicating the DPA URI.
     * @param {object} [dpa_transaction_options] Object that stores options for creating a trasaction with DPA.
     * @param {string} [dpa_transaction_options.dpa_locale] DPAвЂ™s preferred locale, example en_US.
     * @param {Array} [dpa_transaction_options.dpa_accepted_billing_countries] Used to indicate list of accepted billing countries for DPA.
     * @param {Array} [dpa_transaction_options.dpa_accepted_shipping_countries] Used to indicate list of accepted shipping countries for DPA.
     * @param {string} [dpa_transaction_options.dpa_billing_preference] Used for listing the enumeration for billing preferences for DPA. Options are 'ALL', 'POSTAL_COUNTRY' and 'NONE'.
     * @param {string} [dpa_transaction_options.dpa_shipping_preference] Used for listing the enumeration for shipping preferences for DPA. Options are 'ALL', 'POSTAL_COUNTRY' and 'NONE'.
     * @param {boolean} [dpa_transaction_options.consumer_name_requested] Used to check if the name of the consumer is needed.
     * @param {boolean} [dpa_transaction_options.consumer_email_address_requested] Used to check if the email of the consumer is needed.
     * @param {boolean} [dpa_transaction_options.consumer_phone_number_requested] Used to check if the phone number of the consumer is needed.
     * @param {object} [dpa_transaction_options.payment_options] Object used to check the payment options that are included.
     * @param {number} [dpa_transaction_options.payment_options.dpa_dynamic_data_ttl_minutes] The minimum requested validity period for the transaction credentials.
     * @param {string} [dpa_transaction_options.payment_options.dynamic_data_type] Used for listing the enumeration for dynamic data types. Options are 'TAVV' and 'DTVV'.
     * @param {boolean} [dpa_transaction_options.payment_options.dpa_pan_requested] Used to check if PAN number was requested.
     * @param {string} [dpa_transaction_options.review_action] Used for listing the enumeration of review actions. Options are 'pay' and 'continue'.
     * @param {string} [dpa_transaction_options.checkout_description] Used for indicating the description of the checkout.
     * @param {string} [dpa_transaction_options.transaction_type] Used for listing the enumeration of the type of the transaction. 'PURCHASE', 'BILL_PAYMENT' and 'MONEY_TRANSFER'
     * @param {string} [dpa_transaction_options.order_type] Used for listing the enumeration of the type of the order. Options are 'REAUTHORIZATION', 'RECURRING' and 'INSTALLMENT'.
     * @param {object} [dpa_transaction_options.transaction_amount] Object used to describe the details of the transaction.
     * @param {number} [dpa_transaction_options.transaction_amount.transaction_amount] Used to indicate the amount of the transaction.
     * @param {string} [dpa_transaction_options.transaction_amount.transaction_currency_code] Used to indicate the currency code of the transaction. 3 letter ISO code format.
     * @param {string} [dpa_transaction_options.merchant_order_id] Used to indicate the merchants order Id.
     * @param {string} [dpa_transaction_options.merchant_category_code] Used to indicate the merchants category code.
     * @param {string} [dpa_transaction_options.merchant_country_code] Used to indicate the merchants country code. 2 letter ISO code format.
     * @param {object} [customer] Object where the customer data is stored to prefill in the checkout.
     * @param {string} [customer.email] Customer email.
     * @param {string} [customer.first_name] Customer first name.
     * @param {string} [customer.last_name] Customer last name.
     * @param {object} [customer.phone] Object where the customer phone is stored.
     * @param {string} [customer.phone.country_code] Customer phone country code (example "1" for US).
     * @param {string} [customer.phone.phone] Customer phone number.
     * @param {object} [customer.payment_source] Object where the customer billing address data is stored.
     * @param {string} [customer.payment_source.address_line1] Customer billing address line 1.
     * @param {string} [customer.payment_source.address_line2] Customer billing address line 2.
     * @param {string} [customer.payment_source.address_city] Customer billing address city.
     * @param {string} [customer.payment_source.address_postcode] Customer billing address postcode.
     * @param {string} [customer.payment_source.address_state] Customer billing address state code (if applicable for the country, example "FL" for Florida).
     * @param {string} [customer.payment_source.address_country] Customer billing address country code (example "US").
     */
    /**
     * Class SRC include methods for interacting with different secure remote commerce options such as Visa SRC
     * @constructor
     *
     * @param {string} button_selector - Selector of html element. Container for SRC checkout button.
     * @param {string} iframe_selector - Selector of html element. Container for SRC checkout iFrame.
     * @param {string} service_id - Card Scheme Service ID
     * @param {string} public_key_or_access_token - Paydock public key or Access Token
     * @param {IVisaSRCMeta} meta - Data that configures the SRC checkout
     * @example
     * var SRC = new SRC('#checkoutButton', '#checkoutIframe', 'service_id', 'public_key', {});
     *
     **/
    var SRC = /*#__PURE__*/function () {
        function SRC(button_selector, iframe_selector, service_id, public_key_or_access_token, meta) {
            _classCallCheck(this, SRC);
            this.button_selector = button_selector;
            this.iframe_selector = iframe_selector;
            this.service_id = service_id;
            this.public_key_or_access_token = public_key_or_access_token;
            this.meta = meta;
            this.autoResize = false;
            this.style = {};
            this.api = new ApiInternal(public_key_or_access_token, API_AUTH_TYPE.PUBLIC_KEY);
            this.eventEmitter = new EventEmitter();
        }
        /**
         * Object contain styles for widget - call before `.load()`.
         *
         * @example
         * widget.setStyles({
         *       button_text_color: '#32a852',
         *       primary_color: '#32a852',
         *       font_family: 'sans-serif',
         *       card_schemes: ['visa']
         *   });
         * @param {IStyles} fields - name of styles which can be shown in widget [STYLE]{@link STYLE}
         */
        _createClass(SRC, [{
            key: "setStyles",
            value: function setStyles(styles) {
                for (var index in styles) {
                    if (styles.hasOwnProperty(index)) this.setStyle(index, styles[index]);
                }
            }
        }, {
            key: "setStyle",
            value: function setStyle(param, value) {
                if (ObjectHelper.values(STYLE$2).indexOf(param) !== -1) this.style[param] = value;else console.warn("Widget::setStyle[s: unsupported style param ".concat(param));
            }
            /**
             * The final method after configuring the SRC to start the load process of SRC checkout
             *
             */
        }, {
            key: "load",
            value: function load() {
                var _this = this;
                if (this.provider) return;
                this.api.service().getConfig(this.service_id).then(function (_ref) {
                    var type = _ref.type;
                    _this.meta.customizations = _this.style; // assign the style on the start of the widget
                    switch (type) {
                        case CARD_SCHEME_SERVICE.VISA_SRC:
                            _this.provider = new VisaSRC(_this.button_selector, _this.iframe_selector, _this.service_id, _this.public_key_or_access_token, _this.meta, _this.eventEmitter, _this.autoResize, _this.env, _this.alias);
                            break;
                    }
                    if (_this.provider) _this.provider.load();
                });
            }
            /**
             * Current method can change environment. By default environment = sandbox.
             * Also we can change domain alias for this environment. By default domain_alias = paydock.com
             *
             * @example
             * SRC.setEnv('production');
             * @param {string} env - sandbox, production
             * @param {string} [alias] - Own domain alias
             */
        }, {
            key: "setEnv",
            value: function setEnv(env, alias) {
                if (this.provider) return;
                this.env = env;
                this.alias = alias;
                this.api.setEnv(env, alias);
            }
            /**
             * Method to read the current environment
             *
             * @example
             * SRC.getEnv();
             */
        }, {
            key: "getEnv",
            value: function getEnv() {
                if (this.provider) return this.provider.getEnv();else return this.env;
            }
            /**
             * Listen to events of SRC
             *
             * @example
             *
             * SRC.on('checkoutCompleted', function (token) {
             *      console.log(token);
             * });
             * // or
             * SRC.on('checkoutCompleted').then(function (token) {
             *      console.log(token);
             * });
             * @param {string} eventName - Available event names [EVENT]{@link EVENT}
             * @param {listener} [cb]
             * @return {Promise<any> | void}
             */
        }, {
            key: "on",
            value: function on(eventName, cb) {
                var _this2 = this;
                if (typeof cb === "function") return this.eventEmitter.subscribe(eventName, cb);
                return new Promise(function (resolve) {
                    return _this2.eventEmitter.subscribe(eventName, function (res) {
                        return resolve(res);
                    });
                });
            }
            /**
             * Using this method you can hide button
             * @param {boolean} [saveSize=false] - using this param you can save iframe's size (if applicable)
             *
             * @example
             * SRC.hideButton();
             */
        }, {
            key: "hideButton",
            value: function hideButton(saveSize) {
                if (this.provider && typeof this.provider.hideButton === 'function') this.provider.hideButton(saveSize);
            }
            /**
             * Using this method you can show the SRC button after using hideButton method
             *
             * @example
             * SRC.showButton();
             */
        }, {
            key: "showButton",
            value: function showButton() {
                if (this.provider && typeof this.provider.showButton === 'function') this.provider.showButton();
            }
            /**
             * Using this method you can hide checkout after load and button click
             * @param {boolean} [saveSize=false] - using this param you can save iframe's size (if applicable)
             *
             * @example
             * SRC.hideCheckout();
             */
        }, {
            key: "hideCheckout",
            value: function hideCheckout(saveSize) {
                if (this.provider && typeof this.provider.hideCheckout === 'function') this.provider.hideCheckout(saveSize);
            }
            /**
             * Using this method you can show checkout after using hideCheckout method
             *
             * @example
             * SRC.showCheckout()
             */
        }, {
            key: "showCheckout",
            value: function showCheckout() {
                if (this.provider && typeof this.provider.showCheckout === 'function') this.provider.showCheckout();
            }
            /**
             * Using this method you can reload the whole checkout
             *
             * @example
             * SRC.reload()
             */
        }, {
            key: "reload",
            value: function reload() {
                if (this.provider) this.provider.reload();
            }
            /**
             * Use this method for resize checkout iFrame according to content height, if applicable
             *
             * @example
             * SRC.useAutoResize();
             *
             */
        }, {
            key: "useAutoResize",
            value: function useAutoResize() {
                this.autoResize = true;
                if (this.provider && typeof this.provider.useAutoResize === 'function') this.provider.useAutoResize();
            }
        }]);
        return SRC;
    }();

    exports.AfterpayCheckoutButton = AfterpayCheckoutButton;
    exports.Api = Api;
    exports.CHECKOUT_BUTTON_EVENT = CHECKOUT_BUTTON_EVENT;
    exports.Canvas3ds = Canvas3ds;
    exports.Configuration = Configuration;
    exports.ELEMENT = ELEMENT;
    exports.EVENT = EVENT;
    exports.ExternalCheckoutBuilder = Builder;
    exports.ExternalCheckoutChecker = Checker;
    exports.FORM_FIELD = FORM_FIELD;
    exports.HtmlMultiWidget = HtmlMultiWidget;
    exports.HtmlPaymentSourceWidget = HtmlPaymentSourceWidget;
    exports.HtmlWidget = HtmlWidget;
    exports.MultiWidget = MultiWidget;
    exports.PAYMENT_TYPE = PAYMENT_TYPE;
    exports.PURPOSE = PURPOSE;
    exports.PaymentSourceBuilder = Builder$1;
    exports.PaymentSourceWidget = PaymentSourceWidget;
    exports.PaypalCheckoutButton = PaypalCheckoutButton;
    exports.SRC = SRC;
    exports.STYLABLE_ELEMENT = STYLABLE_ELEMENT;
    exports.STYLABLE_ELEMENT_STATE = STYLABLE_ELEMENT_STATE;
    exports.STYLE = STYLE;
    exports.SUPPORTED_CARD_TYPES = SUPPORTED_CARD_TYPES;
    exports.TEXT = TEXT;
    exports.TRIGGER = TRIGGER;
    exports.TYPE = TYPE;
    exports.VAULT_DISPLAY_STYLE = VAULT_DISPLAY_STYLE;
    exports.VaultDisplayWidget = VaultDisplayWidget;
    exports.WalletButtons = WalletButtons;
    exports.ZipmoneyCheckoutButton = ZipmoneyCheckoutButton;

    Object.defineProperty(exports, '__esModule', { value: true });

})));
