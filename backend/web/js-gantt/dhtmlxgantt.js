
!function (t) {
    function e(n) {
        if (i[n])
            return i[n].exports;
        var r = i[n] = {
            i: n,
            l: !1,
            exports: {}
        };
        return t[n].call(r.exports, r, r.exports, e), r.l = !0, r.exports
    }
    var i = {};
    e.m = t, e.c = i, e.d = function (t, i, n) {
        e.o(t, i) || Object.defineProperty(t, i, {
            configurable: !1,
            enumerable: !0,
            get: n
        })
    }, e.n = function (t) {
        var i = t && t.__esModule ? function () {
            return t.default
        } : function () {
            return t
        };
        return e.d(i, "a", i), i
    }, e.o = function (t, e) {
        return Object.prototype.hasOwnProperty.call(t, e)
    }, e.p = "", e(e.s = 23)
}([function (t, e, i) {
        function n(t) {
            var e, i, r;
            if (t && "object" == typeof t) {
                for (r = {}, i = [Array, Number, String, Boolean], e = 0; e < i.length; e++)
                    t instanceof i[e] && (r = e ? new i[e](t) : new i[e]);
                h.isDate(t) && (r = new Date(t));
                for (e in t)
                    Object.prototype.hasOwnProperty.apply(t, [e]) && (r[e] = n(t[e]))
            }
            return r || t
        }

        function r(t, e, i) {
            for (var n in e)
                (void 0 === t[n] || i) && (t[n] = e[n]);
            return t
        }

        function a(t) {
            return void 0 !== t
        }

        function s() {
            return c || (c = (new Date).valueOf()), ++c
        }

        function o(t, e) {
            return t.bind ? t.bind(e) : function () {
                return t.apply(e, arguments)
            }
        }

        function l(t, e, i, n) {
            t.addEventListener ? t.addEventListener(e, i, void 0 !== n && n) : t.attachEvent && t.attachEvent("on" + e, i)
        }

        function d(t, e, i, n) {
            t.removeEventListener ? t.removeEventListener(e, i, void 0 !== n && n) : t.detachEvent && t.detachEvent("on" + e, i)
        }
        var c, h = i(4);
        t.exports = {
            copy: n,
            defined: a,
            mixin: r,
            uid: s,
            bind: o,
            event: l,
            eventRemove: d
        }
    }, function (t, e) {
        function i(t) {
            var e = 0,
                    i = 0,
                    n = 0,
                    r = 0;
            if (t.getBoundingClientRect) {
                var a = t.getBoundingClientRect(),
                        s = document.body,
                        o = document.documentElement || document.body.parentNode || document.body,
                        l = window.pageYOffset || o.scrollTop || s.scrollTop,
                        d = window.pageXOffset || o.scrollLeft || s.scrollLeft,
                        c = o.clientTop || s.clientTop || 0,
                        h = o.clientLeft || s.clientLeft || 0;
                e = a.top + l - c, i = a.left + d - h, n = document.body.offsetWidth - a.right, r = document.body.offsetHeight - a.bottom
            } else {
                for (; t; )
                    e += parseInt(t.offsetTop, 10), i += parseInt(t.offsetLeft, 10), t = t.offsetParent;
                n = document.body.offsetWidth - t.offsetWidth - i, r = document.body.offsetHeight - t.offsetHeight - e
            }
            return {
                y: Math.round(e),
                x: Math.round(i),
                width: t.offsetWidth,
                height: t.offsetHeight,
                right: Math.round(n),
                bottom: Math.round(r)
            }
        }

        function n(t) {
            var e = !1,
                    i = !1;
            if (window.getComputedStyle) {
                var n = window.getComputedStyle(t, null);
                e = n.display, i = n.visibility
            } else
                t.currentStyle && (e = t.currentStyle.display, i = t.currentStyle.visibility);
            return "none" != e && "hidden" != i
        }

        function r(t) {
            return !isNaN(t.getAttribute("tabindex")) && 1 * t.getAttribute("tabindex") >= 0
        }

        function a(t) {
            return !{
                a: !0,
                area: !0
            }[t.nodeName.loLowerCase()] || !!t.getAttribute("href")
        }

        function s(t) {
            return !{
                input: !0,
                select: !0,
                textarea: !0,
                button: !0,
                object: !0
            }[t.nodeName.toLowerCase()] || !t.hasAttribute("disabled")
        }

        function o(t) {
            for (var e = t.querySelectorAll(["a[href]", "area[href]", "input", "select", "textarea", "button", "iframe", "object", "embed", "[tabindex]", "[contenteditable]"].join(", ")), i = Array.prototype.slice.call(e, 0), o = 0; o < i.length; o++) {
                var l = i[o];
                (r(l) || s(l) || a(l)) && n(l) || (i.splice(o, 1), o--)
            }
            return i
        }

        function l() {
            var t = document.createElement("div");
            t.style.cssText = "visibility:hidden;position:absolute;left:-1000px;width:100px;padding:0px;margin:0px;height:110px;min-height:100px;overflow-y:scroll;", document.body.appendChild(t);
            var e = t.offsetWidth - t.clientWidth;
            return document.body.removeChild(t), e
        }

        function d(t) {
            if (!t)
                return "";
            var e = t.className || "";
            return e.baseVal && (e = e.baseVal), e.indexOf || (e = ""), m(e)
        }

        function c(t, e) {
            e && -1 === t.className.indexOf(e) && (t.className += " " + e)
        }

        function h(t, e) {
            e = e.split(" ");
            for (var i = 0; i < e.length; i++) {
                var n = new RegExp("\\s?\\b" + e[i] + "\\b(?![-_.])", "");
                t.className = t.className.replace(n, "")
            }
        }

        function u(t) {
            return "string" == typeof t ? document.getElementById(t) || document.querySelector(t) || document.body : t || document.body
        }

        function _(t, e) {
            $.innerHTML = e;
            var i = $.firstChild;
            return t.appendChild(i), i
        }

        function g(t) {
            t && t.parentNode && t.parentNode.removeChild(t)
        }

        function f(t, e) {
            for (var i = t.childNodes, n = i.length, r = [], a = 0; a < n; a++) {
                var s = i[a];
                s.className && -1 !== s.className.indexOf(e) && r.push(s)
            }
            return r
        }

        function p(t) {
            var e;
            return t.tagName ? e = t : (t = t || window.event, e = t.target || t.srcElement), e
        }

        function v(t, e) {
            if (e) {
                for (var i = p(t); i; ) {
                    if (i.getAttribute) {
                        if (i.getAttribute(e))
                            return i
                    }
                    i = i.parentNode
                }
                return null
            }
        }

        function m(t) {
            return (String.prototype.trim || function () {
                return this.replace(/^\s+|\s+$/g, "")
            }).apply(t)
        }

        function k(t, e, i) {
            void 0 === i && (i = !0);
            for (var n = p(t), r = ""; n; ) {
                if (r = d(n)) {
                    var a = r.indexOf(e);
                    if (a >= 0) {
                        if (!i)
                            return n;
                        var s = 0 === a || !m(r.charAt(a - 1)),
                                o = a + e.length >= r.length || !m(r.charAt(a + e.length));
                        if (s && o)
                            return n
                    }
                }
                n = n.parentNode
            }
            return null
        }

        function y(t, e) {
            if (t.pageX || t.pageY)
                var n = {
                    x: t.pageX,
                    y: t.pageY
                };
            var r = document.documentElement,
                    n = {
                        x: t.clientX + r.scrollLeft - r.clientLeft,
                        y: t.clientY + r.scrollTop - r.clientTop
                    },
                    a = i(e);
            return n.x = n.x - a.x + e.scrollLeft, n.y = n.y - a.y + e.scrollTop, n
        }

        function b(t, e) {
            if (!t || !e)
                return !1;
            for (; t && t != e; )
                t = t.parentNode;
            return t === e
        }
        var $ = document.createElement("div");
        t.exports = {
            getNodePosition: i,
            getFocusableNodes: o,
            getScrollSize: l,
            getClassName: d,
            addClassName: c,
            removeClassName: h,
            insertNode: _,
            removeNode: g,
            getChildNodes: f,
            toNode: u,
            locateClassName: k,
            locateAttribute: v,
            getTargetNode: p,
            getRelativeEventPosition: y,
            isChildOf: b
        }
    }, function (t, e) {
        function i(t) {
            var e = new n;
            t.attachEvent = function (t, i, n) {
                return t = "ev_" + t.toLowerCase(), e[t] || (e[t] = r(n || this)), t + ":" + e[t].addEvent(i)
            }, t.attachAll = function (t, e) {
                this.attachEvent("listen_all", t, e)
            }, t.callEvent = function (t, i, n) {
                if (e._silent_mode)
                    return !0;
                var r = "ev_" + t.toLowerCase();
                return e.ev_listen_all && e.ev_listen_all.apply(n || this, [t].concat(i)), !e[r] || e[r].apply(n || this, i)
            }, t.checkEvent = function (t) {
                return !!e["ev_" + t.toLowerCase()]
            }, t.detachEvent = function (t) {
                if (t) {
                    var i = t.split(":");
                    e[i[0]].removeEvent(i[1])
                }
            }, t.detachAllEvents = function () {
                for (var t in e)
                    0 === t.indexOf("ev_") && delete e[t]
            }
        }
        var n = function () {
            this._connected = [], this._silent_mode = !1
        };
        n.prototype = {
            _silentStart: function () {
                this._silent_mode = !0
            },
            _silentEnd: function () {
                this._silent_mode = !1
            }
        };
        var r = function (t) {
            var e = [],
                    i = function () {
                        for (var i = !0, n = 0; n < e.length; n++)
                            if (e[n]) {
                                var r = e[n].apply(t, arguments);
                                i = i && r
                            }
                        return i
                    };
            return i.addEvent = function (t) {
                return "function" == typeof t && e.push(t) - 1
            }, i.removeEvent = function (t) {
                e[t] = null
            }, i
        };
        t.exports = i
    }, function (t, e) {
        t.exports = function (t, e) {
            function i() {
                this.constructor = t
            }
            for (var n in e)
                e.hasOwnProperty(n) && (t[n] = e[n]);
            t.prototype = null === e ? Object.create(e) : (i.prototype = e.prototype, new i)
        }
    }, function (t, e) {
        function i(t) {
            var e = ["getFullYear", "getMonth", "getDate"];
            if (!t || "object" != typeof t)
                return !1;
            for (var i = 0; i < e.length; i++)
                if (!t[e[i]])
                    return !1;
            return !0
        }
        t.exports = {
            isDate: i
        }
    }, function (t, e, i) {
        var n = i(0),
                r = (i(11), i(2)),
                a = i(1),
                s = function () {
                    "use strict";

                    function t(t, e, i, s) {
                        t && (this.$container = a.toNode(t), this.$parent = t), this.$config = n.mixin(e, {
                            headerHeight: 33
                        }), this.$gantt = s, this.$domEvents = s._createDomEventScope(), this.$id = e.id || "c" + n.uid(), this.$name = "cell", this.$factory = i, r(this)
                    }
                    return t.prototype.destructor = function () {
                        this.$parent = this.$container = this.$view = null, this.$gantt.$services.getService("mouseEvents").detach("click", "gantt_header_arrow", this._headerClickHandler), this.$domEvents.detachAll(), this.callEvent("onDestroy", []), this.detachAllEvents()
                    }, t.prototype.cell = function (t) {
                        return null
                    }, t.prototype.scrollTo = function (t, e) {
                        1 * t == t && (this.$view.scrollLeft = t), 1 * e == e && (this.$view.scrollTop = e)
                    }, t.prototype.clear = function () {
                        this.getNode().innerHTML = "", this.getNode().className = "gantt_layout_content", this.getNode().style.padding = "0"
                    }, t.prototype.resize = function (t) {
                        if (this.$parent)
                            return this.$parent.resize(t);
                        !1 === t && (this.$preResize = !0);
                        var e = this.$container,
                                i = e.offsetWidth,
                                n = e.offsetHeight,
                                r = this.getSize();
                        e === document.body && (i = document.body.offsetWidth, n = document.body.offsetHeight), i < r.minWidth && (i = r.minWidth), i > r.maxWidth && (i = r.maxWidth), n < r.minHeight && (n = r.minHeight), n > r.maxHeight && (n = r.maxHeight), this.setSize(i, n);
                        this.$preResize, this.$preResize = !1
                    }, t.prototype.hide = function () {
                        this._hide(!0), this.resize()
                    }, t.prototype.show = function (t) {
                        this._hide(!1), t && this.$parent && this.$parent.show(), this.resize()
                    }, t.prototype._hide = function (t) {
                        if (!0 === t && this.$view.parentNode)
                            this.$view.parentNode.removeChild(this.$view);
                        else if (!1 === t && !this.$view.parentNode) {
                            var e = this.$parent.cellIndex(this.$id);
                            this.$parent.moveView(this, e)
                        }
                        this.$config.hidden = t
                    }, t.prototype.$toHTML = function (t, e) {
                        void 0 === t && (t = ""), e = [e || "", this.$config.css || ""].join(" ");
                        var i = this.$config,
                                n = "";
                        if (i.raw)
                            t = "string" == typeof i.raw ? i.raw : "";
                        else if (t || (t = "<div class='gantt_layout_content' " + (e ? " class='" + e + "' " : "") + " >" + (i.html || "") + "</div>"), i.header) {
                            var r = i.canCollapse ? "<div class='gantt_layout_header_arrow'></div>" : "";
                            n = "<div class='gantt_layout_header'>" + r + "<div class='gantt_layout_header_content'>" + i.header + "</div></div>"
                        }
                        return "<div class='gantt_layout_cell " + e + "' data-cell-id='" + this.$id + "'>" + n + t + "</div>"
                    }, t.prototype.$fill = function (t, e) {
                        this.$view = t, this.$parent = e, this.init()
                    }, t.prototype.getNode = function () {
                        return this.$view.querySelector("gantt_layout_cell") || this.$view
                    }, t.prototype.init = function () {
                        var t = this;
                        this._headerClickHandler = function (e) {
                            a.locateAttribute(e, "data-cell-id") == t.$id && t.toggle()
                        }, this.$gantt.$services.getService("mouseEvents").delegate("click", "gantt_header_arrow", this._headerClickHandler), this.callEvent("onReady", [])
                    }, t.prototype.toggle = function () {
                        this.$config.collapsed = !this.$config.collapsed, this.resize()
                    }, t.prototype.getSize = function () {
                        var t = {
                            height: this.$config.height || 0,
                            width: this.$config.width || 0,
                            gravity: this.$config.gravity || 1,
                            minHeight: this.$config.minHeight || 0,
                            minWidth: this.$config.minWidth || 0,
                            maxHeight: this.$config.maxHeight || 1e5,
                            maxWidth: this.$config.maxWidth || 1e5
                        };
                        if (this.$config.collapsed) {
                            var e = "x" === this.$config.mode;
                            t[e ? "width" : "height"] = t[e ? "maxWidth" : "maxHeight"] = this.$config.headerHeight
                        }
                        return t
                    }, t.prototype.getContentSize = function () {
                        var t = this.$lastSize.contentX;
                        t !== 1 * t && (t = this.$lastSize.width);
                        var e = this.$lastSize.contentY;
                        return e !== 1 * e && (e = this.$lastSize.height), {
                            width: t,
                            height: e
                        }
                    }, t.prototype._getBorderSizes = function () {
                        var t = {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0,
                            horizontal: 0,
                            vertical: 0
                        };
                        return this._currentBorders && (this._currentBorders[this._borders.left] && (t.left = 1, t.horizontal++), this._currentBorders[this._borders.right] && (t.right = 1, t.horizontal++), this._currentBorders[this._borders.top] && (t.top = 1, t.vertical++), this._currentBorders[this._borders.bottom] && (t.bottom = 1, t.vertical++)), t
                    }, t.prototype.setSize = function (t, e) {
                        this.$view.style.width = t + "px", this.$view.style.height = e + "px";
                        var i = this._getBorderSizes(),
                                n = e - i.vertical,
                                r = t - i.horizontal;
                        this.$lastSize = {
                            x: t,
                            y: e,
                            contentX: r,
                            contentY: n
                        }, this.$config.header ? this._sizeHeader() : this._sizeContent()
                    }, t.prototype._borders = {
                        left: "gantt_layout_cell_border_left",
                        right: "gantt_layout_cell_border_right",
                        top: "gantt_layout_cell_border_top",
                        bottom: "gantt_layout_cell_border_bottom"
                    }, t.prototype._setBorders = function (t, e) {
                        e || (e = this);
                        var i = e.$view;
                        for (var n in this._borders)
                            a.removeClassName(i, this._borders[n]);
                        "string" == typeof t && (t = [t]);
                        for (var r = {}, n = 0; n < t.length; n++)
                            a.addClassName(i, t[n]), r[t[n]] = !0;
                        e._currentBorders = r
                    }, t.prototype._sizeContent = function () {
                        var t = this.$view.childNodes[0];
                        t && "gantt_layout_content" == t.className && (t.style.height = this.$lastSize.contentY + "px")
                    }, t.prototype._sizeHeader = function () {
                        var t = this.$lastSize;
                        t.contentY -= this.$config.headerHeight;
                        var e = this.$view.childNodes[0],
                                i = this.$view.childNodes[1],
                                n = "x" === this.$config.mode;
                        if (this.$config.collapsed)
                            if (i.style.display = "none", n) {
                                e.className = "gantt_layout_header collapsed_x", e.style.width = t.y + "px";
                                var r = Math.floor(t.y / 2 - t.x / 2);
                                e.style.transform = "rotate(90deg) translate(" + r + "px, " + r + "px)", i.style.display = "none"
                            } else
                                e.className = "gantt_layout_header collapsed_y";
                        else
                            e.className = n ? "gantt_layout_header" : "gantt_layout_header vertical", e.style.width = "auto", e.style.transform = "", i.style.display = "", i.style.height = t.contentY + "px";
                        e.style.height = this.$config.headerHeight + "px"
                    }, t
                }();
        t.exports = s
    }, function (t, e) {
        var i = {
            isIE: navigator.userAgent.indexOf("MSIE") >= 0 || navigator.userAgent.indexOf("Trident") >= 0,
            isIE6: !window.XMLHttpRequest && navigator.userAgent.indexOf("MSIE") >= 0,
            isIE7: navigator.userAgent.indexOf("MSIE 7.0") >= 0 && navigator.userAgent.indexOf("Trident") < 0,
            isIE8: navigator.userAgent.indexOf("MSIE 8.0") >= 0 && navigator.userAgent.indexOf("Trident") >= 0,
            isOpera: navigator.userAgent.indexOf("Opera") >= 0,
            isChrome: navigator.userAgent.indexOf("Chrome") >= 0,
            isKHTML: navigator.userAgent.indexOf("Safari") >= 0 || navigator.userAgent.indexOf("Konqueror") >= 0,
            isFF: navigator.userAgent.indexOf("Firefox") >= 0,
            isIPad: navigator.userAgent.search(/iPad/gi) >= 0,
            isEdge: -1 != navigator.userAgent.indexOf("Edge")
        };
        t.exports = i
    }, function (t, e) {
        function i(t) {
            return a[t] || a.hour
        }

        function n(t, e) {
            for (var i = t.slice(), n = 0; n < i.length; n++)
                e(i[n], n)
        }

        function r(t, e) {
            for (var i = t.slice(), n = [], r = 0; r < i.length; r++)
                n.push(e(i[r], r));
            return n
        }
        var a = {
            second: 1,
            minute: 60,
            hour: 3600,
            day: 86400,
            week: 604800,
            month: 2592e3,
            quarter: 7776e3,
            year: 31536e3
        };
        t.exports = {
            getSecondsInUnit: i,
            forEach: n,
            arrayMap: r
        }
    }, function (t, e, i) {
        var n = i(1),
                r = i(0),
                a = i(2),
                s = i(50),
                o = function (t, e, i, n) {
                    this.$config = r.mixin({}, e || {}), this.$gantt = n, this.$parent = t, a(this), this.$state = {}
                };
        o.prototype = {
            init: function (t) {
                var e = this.$gantt,
                        n = e._waiAria.gridAttrString(),
                        r = e._waiAria.gridDataAttrString();
                t.innerHTML = "<div class='gantt_grid' style='height:inherit;width:inherit;' " + n + "></div>", this.$grid = t.childNodes[0], this.$grid.innerHTML = "<div class='gantt_grid_scale' " + e._waiAria.gridScaleRowAttrString() + "></div><div class='gantt_grid_data' " + r + "></div>", this.$grid_scale = this.$grid.childNodes[0], this.$grid_data = this.$grid.childNodes[1];
                var a = this.$getConfig()[this.$config.bind + "_attribute"];
                if (!a && this.$config.bind && (a = this.$config.bind + "_id"), this.$config.item_attribute = a || null, !this.$config.layers) {
                    var o = this._createLayerConfig();
                    this.$config.layers = o
                }
                var l = s(e, this);
                l.init(), this._renderHeaderResizers = l.doOnRender, this._mouseDelegates = i(9)(e), this._addLayers(this.$gantt), this._initEvents(), this.callEvent("onReady", [])
            },
            setSize: function (t, e) {
                this.$config.width = this.$state.width = t, this.$state.height = e;
                for (var i = this.getGridColumns(), n = 0, r = 0, a = i.length; r < a; r++)
                    n += 1 * i[r].width;
                var s;
                !isNaN(n) && this.$config.scrollable || (s = this._setColumnsWidth(t + 1), n = s), this.$config.scrollable ? (this.$grid_scale.style.width = n + "px", this.$grid_data.style.width = n + "px") : (this.$grid_scale.style.width = "inherit", this.$grid_data.style.width = "inherit"), this.$config.width -= 1;
                var o = this.$getConfig();
                s !== t && (o.grid_width = s, this.$config.width = s - 1);
                var l = Math.max(this.$state.height - o.scale_height, 0);
                this.$grid_data.style.height = l + "px", this.refresh()
            },
            getSize: function () {
                var t = this.$getConfig(),
                        e = this.$config.rowStore,
                        i = e ? t.row_height * e.countVisible() : 0,
                        n = this._getGridWidth();
                return {
                    x: this.$state.width,
                    y: this.$state.height,
                    contentX: this.isVisible() ? n : 0,
                    contentY: this.isVisible() ? t.scale_height + i : 0,
                    scrollHeight: this.isVisible() ? i : 0,
                    scrollWidth: this.isVisible() ? n : 0
                }
            },
            refresh: function () {
                this.$config.bind && (this.$config.rowStore = this.$gantt.getDatastore(this.$config.bind)), this._initSmartRenderingPlaceholder(), this._calculateGridWidth(), this._renderGridHeader()
            },
            scrollTo: function (t, e) {
                this.isVisible() && (1 * t == t && (this.$state.scrollLeft = this.$grid.scrollLeft = t), 1 * e == e && (this.$state.scrollTop = this.$grid_data.scrollTop = e))
            },
            getGridColumns: function () {
                return this.$getConfig().columns.slice()
            },
            isVisible: function () {
                return this.$parent && this.$parent.$config ? !this.$parent.$config.hidden : this.$grid.offsetWidth
            },
            getItemTop: function (t) {
                if (this.$config.rowStore) {
                    var e = this.$config.rowStore;
                    return e ? e.getIndexById(t) * this.$getConfig().row_height : 0
                }
                return 0
            },
            getItemHeight: function () {
                return this.$getConfig().row_height
            },
            _createLayerConfig: function () {
                var t = this;
                return [{
                        renderer: this.$gantt.$ui.layers.gridLine,
                        container: this.$grid_data,
                        filter: [function () {
                                return t.isVisible()
                            }]
                    }]
            },
            _addLayers: function (t) {
                if (this.$config.bind) {
                    this._taskLayers = [];
                    var e = this,
                            i = this.$gantt.$services.getService("layers"),
                            n = i.getDataRender(this.$config.bind);
                    n || (n = i.createDataRender({
                        name: this.$config.bind,
                        defaultContainer: function () {
                            return e.$grid_data
                        }
                    }));
                    for (var r = this.$config.layers, a = 0; r && a < r.length; a++) {
                        var s = r[a];
                        s.host = this;
                        var o = n.addLayer(s);
                        this._taskLayers.push(o)
                    }
                    this.$config.bind && (this.$config.rowStore = this.$gantt.getDatastore(this.$config.bind)), this._initSmartRenderingPlaceholder()
                }
            },
            _initSmartRenderingPlaceholder: function () {
                var t = this,
                        e = this.$config.rowStore;
                e && (this._initSmartRenderingPlaceholder = function () {}, this._staticBgHandler = e.attachEvent("onStoreUpdated", function (i, n, r) {
                    if (null === i && t.isVisible()) {
                        var a = t.$getConfig();
                        if (a.smart_rendering) {
                            var s = e ? a.row_height * e.countVisible() : 0;
                            if (s) {
                                t.$rowsPlaceholder && t.$rowsPlaceholder.parentNode && t.$rowsPlaceholder.parentNode.removeChild(t.$rowsPlaceholder);
                                var o = t.$rowsPlaceholder = document.createElement("div");
                                o.style.visibility = "hidden", o.style.height = s + "px", o.style.width = "1px", t.$grid_data.appendChild(o)
                            }
                        }
                    }
                }))
            },
            _initEvents: function () {
                this._mouseDelegates.delegate("click", "gantt_close", gantt.bind(function (t, e, i) {
                    var r = this.$config.rowStore;
                    if (!r)
                        return !0;
                    var a = n.locateAttribute(t, this.$config.item_attribute);
                    return a && r.close(a.getAttribute(this.$config.item_attribute)), !1
                }, this), this.$grid), this._mouseDelegates.delegate("click", "gantt_open", gantt.bind(function (t, e, i) {
                    var r = this.$config.rowStore;
                    if (!r)
                        return !0;
                    var a = n.locateAttribute(t, this.$config.item_attribute);
                    return a && r.open(a.getAttribute(this.$config.item_attribute)), !1
                }, this), this.$grid)
            },
            _clearLayers: function (t) {
                for (var e = this.$gantt.$services.getService("layers"), i = e.getDataRender(this.$config.bind), n = 0; n < this._taskLayers.length; n++)
                    i.removeLayer(this._taskLayers[n]);
                this._taskLayers = []
            },
            _getColumnWidth: function (t, e, i) {
                var n = t.min_width || e.min_grid_column_width,
                        r = Math.max(i, n || 10);
                return t.max_width && (r = Math.min(r, t.max_width)), r
            },
            _getGridWidthLimits: function () {
                for (var t = this.$getConfig(), e = this.getGridColumns(), i = 0, n = 0, r = 0; r < e.length; r++)
                    i += e[r].min_width ? e[r].min_width : t.min_grid_column_width, void 0 !== n && (n = e[r].max_width ? n + e[r].max_width : void 0);
                return [i, n]
            },
            _setColumnsWidth: function (t, e) {
                var i = this.$getConfig(),
                        n = this.getGridColumns(),
                        r = 0,
                        a = t;
                e = window.isNaN(e) ? -1 : e;
                for (var s = 0, o = n.length; s < o; s++)
                    r += 1 * n[s].width;
                if (window.isNaN(r)) {
                    this._calculateGridWidth(), r = 0;
                    for (var s = 0, o = n.length; s < o; s++)
                        r += 1 * n[s].width
                }
                for (var l = a - r, d = 0, s = 0; s < e + 1; s++)
                    d += n[s].width;
                r -= d;
                for (var s = e + 1; s < n.length; s++) {
                    var c = n[s],
                            h = Math.round(l * (c.width / r));
                    l < 0 ? c.min_width && c.width + h < c.min_width ? h = c.min_width - c.width : !c.min_width && i.min_grid_column_width && c.width + h < i.min_grid_column_width && (h = i.min_grid_column_width - c.width) : c.max_width && c.width + h > c.max_width && (h = c.max_width - c.width), r -= c.width, c.width += h, l -= h
                }
                for (var u = l > 0 ? 1 : -1; l > 0 && 1 === u || l < 0 && -1 === u; ) {
                    var _ = l;
                    for (s = e + 1; s < n.length; s++) {
                        var g = n[s].width + u;
                        if (g == this._getColumnWidth(n[s], i, g) && (l -= u, n[s].width = g), !l)
                            break
                    }
                    if (_ == l)
                        break
                }
                if (l && e > -1) {
                    var g = n[e].width + l;
                    g == this._getColumnWidth(n[e], i, g) && (n[e].width = g)
                }
                return this._getColsTotalWidth()
            },
            _getColsTotalWidth: function () {
                for (var t = this.getGridColumns(), e = 0, i = 0; i < t.length; i++) {
                    var n = parseFloat(t[i].width);
                    if (window.isNaN(n))
                        return !1;
                    e += n
                }
                return e
            },
            _calculateGridWidth: function () {
                for (var t = this.$getConfig(), e = this.getGridColumns(), i = 0, n = [], r = [], a = 0; a < e.length; a++) {
                    var s = parseFloat(e[a].width);
                    window.isNaN(s) && (s = t.min_grid_column_width || 10, n.push(a)), r[a] = s, i += s
                }
                var o = this._getGridWidth() + 1;
                if (t.autofit || n.length) {
                    var l = o - i;
                    if (t.autofit)
                        for (var a = 0; a < r.length; a++) {
                            var d = Math.round(l / (r.length - a));
                            r[a] += d;
                            var c = this._getColumnWidth(e[a], t, r[a]);
                            c != r[a] && (d = c - r[a], r[a] = c), l -= d
                        }
                    else if (n.length)
                        for (var a = 0; a < n.length; a++) {
                            var d = Math.round(l / (n.length - a)),
                                    h = n[a];
                            r[h] += d;
                            var c = this._getColumnWidth(e[h], t, r[h]);
                            c != r[h] && (d = c - r[h], r[h] = c), l -= d
                        }
                    for (var a = 0; a < r.length; a++)
                        e[a].width = r[a]
                } else {
                    var u = o != i;
                    this.$config.width = i - 1, t.grid_width = i, u && this.$parent._setContentSize(this.$config.width, this.$config.height)
                }
            },
            _renderGridHeader: function () {
                var t = this.$gantt,
                        e = this.$getConfig(),
                        i = this.$gantt.locale,
                        n = this.$gantt.templates,
                        r = this.getGridColumns();
                e.rtl && (r = r.reverse());
                for (var a = [], s = 0, o = i.labels, l = e.scale_height - 1, d = 0; d < r.length; d++) {
                    var c = d == r.length - 1,
                            h = r[d];
                    h.name || (h.name = t.uid() + "");
                    var u = 1 * h.width,
                            _ = this._getGridWidth();
                    c && _ > s + u && (h.width = u = _ - s), s += u;
                    var g = t._sort && h.name == t._sort.name ? "<div class='gantt_sort gantt_" + t._sort.direction + "'></div>" : "",
                            f = ["gantt_grid_head_cell", "gantt_grid_head_" + h.name, c ? "gantt_last_cell" : "", n.grid_header_class(h.name, h)].join(" "),
                            p = "width:" + (u - (c ? 1 : 0)) + "px;",
                            v = h.label || o["column_" + h.name];
                    v = v || "";
                    var m = t._waiAria.gridScaleCellAttrString(h, v),
                            k = "<div class='" + f + "' style='" + p + "' " + m + " column_id='" + h.name + "'>" + v + g + "</div>";
                    a.push(k)
                }
                this.$grid_scale.style.height = e.scale_height + "px", this.$grid_scale.style.lineHeight = l + "px", this.$grid_scale.innerHTML = a.join(""), this._renderHeaderResizers && this._renderHeaderResizers()
            },
            _getGridWidth: function () {
                return this.$config.width
            },
            destructor: function () {
                this._clearLayers(this.$gantt), this._mouseDelegates.destructor(), this._mouseDelegates = null, this.$grid = null, this.$grid_scale = null, this.$grid_data = null, this.$gantt = null, this.$config.rowStore && (this.$config.rowStore.detachEvent(this._staticBgHandler), this.$config.rowStore = null), this.callEvent("onDestroy", []), this.detachAllEvents()
            }
        }, t.exports = o
    }, function (t, e) {
        function i(t) {
            var e = [];
            return {
                delegate: function (i, n, r, a) {
                    e.push([i, n, r, a]), t.$services.getService("mouseEvents").delegate(i, n, r, a)
                },
                destructor: function () {
                    for (var i = t.$services.getService("mouseEvents"), n = 0; n < e.length; n++) {
                        var r = e[n];
                        i.detach(r[0], r[1], r[2], r[3])
                    }
                    e = []
                }
            }
        }
        t.exports = i
    }, function (t, e) {
        function i(t, e) {
            if (!e)
                return !0;
            if (t._on_timeout)
                return !1;
            var i = Math.ceil(1e3 / e);
            return i < 2 || (setTimeout(function () {
                delete t._on_timeout
            }, i), t._on_timeout = !0, !0)
        }
        t.exports = i
    }, function (t, e, i) {
        function n(t, e) {
            t = t || r.event, e = e || r.eventRemove;
            var i = [];
            return {
                attach: function (e, n, r, a) {
                    i.push({
                        element: e,
                        event: n,
                        callback: r,
                        capture: a
                    }), t(e, n, r, a)
                },
                detach: function (t, n, r, a) {
                    e(t, n, r, a);
                    for (var s = 0; s < i.length; s++) {
                        var r = i[s];
                        r.element === t && r.event === n && r.callback === r && r.capture === a && (i.splice(s, 1), s--)
                    }
                },
                detachAll: function () {
                    for (var t = 0; t < i.length; t++)
                        e(i[t].element, i[t].event, i[t].callback, i[t].capture), e(i[t].element, i[t].event, i[t].callback, void 0), e(i[t].element, i[t].event, i[t].callback, !1), e(i[t].element, i[t].event, i[t].callback, !0);
                    i = []
                },
                extend: function () {
                    return n(this.event, this.eventRemove)
                }
            }
        }
        var r = i(0);
        t.exports = n
    }, function (t, e, i) {
        var n = i(3),
                r = i(1),
                a = (i(0), i(5)),
                s = function (t) {
                    "use strict";

                    function e(e, i, n) {
                        var r = t.apply(this, arguments) || this;
                        return e && (r.$root = !0), r._parseConfig(i), r.$name = "layout", r
                    }
                    return n(e, t), e.prototype.destructor = function () {
                        this.$container && this.$view && r.removeNode(this.$view);
                        for (var e = 0; e < this.$cells.length; e++) {
                            this.$cells[e].destructor()
                        }
                        this.$cells = [], t.prototype.destructor.call(this)
                    }, e.prototype._resizeScrollbars = function (t, e) {
                        function i(t) {
                            t.$parent.show(), a = !0, s.push(t)
                        }

                        function n(t) {
                            t.$parent.hide(), a = !0, o.push(t)
                        }
                        for (var r, a = !1, s = [], o = [], l = 0; l < e.length; l++)
                            r = e[l], t[r.$config.scroll] ? n(r) : r.shouldHide() ? n(r) : r.shouldShow() ? i(r) : r.isVisible() ? s.push(r) : o.push(r);
                        for (var d = {}, l = 0; l < s.length; l++)
                            s[l].$config.group && (d[s[l].$config.group] = !0);
                        for (var l = 0; l < o.length; l++)
                            r = o[l], r.$config.group && d[r.$config.group] && i(r);
                        return a
                    }, e.prototype._syncCellSizes = function (t, e) {
                        if (t) {
                            var i = {};
                            return this._eachChild(function (t) {
                                t.$config.group && "scrollbar" != t.$name && "resizer" != t.$name && (i[t.$config.group] || (i[t.$config.group] = []), i[t.$config.group].push(t))
                            }), i[t] && this._syncGroupSize(i[t], e), i[t]
                        }
                    }, e.prototype._syncGroupSize = function (t, e) {
                        if (t.length)
                            for (var i = t[0].$parent._xLayout ? "width" : "height", n = t[0].$parent.getNextSibling(t[0].$id) ? 1 : -1, r = 0; r < t.length; r++) {
                                var a = t[r].getSize(),
                                        s = n > 0 ? t[r].$parent.getNextSibling(t[r].$id) : t[r].$parent.getPrevSibling(t[r].$id);
                                "resizer" == s.$name && (s = n > 0 ? s.$parent.getNextSibling(s.$id) : s.$parent.getPrevSibling(s.$id));
                                var o = s.getSize();
                                if (s[i]) {
                                    var l = a.gravity + o.gravity,
                                            d = a[i] + o[i],
                                            c = l / d;
                                    t[r].$config.gravity = c * e, s.$config[i] = d - e, s.$config.gravity = l - c * e
                                } else
                                    t[r].$config[i] = e;
                                var h = this.$gantt.$ui.getView("grid");
                                h && t[r].$content === h && !h.$config.scrollable && (this.$gantt.config.grid_width = e)
                            }
                    }, e.prototype.resize = function (e) {
                        var i = !1;
                        if (this.$root && !this._resizeInProgress && (this.callEvent("onBeforeResize", []), i = !0, this._resizeInProgress = !0), t.prototype.resize.call(this, !0), t.prototype.resize.call(this, !1), i) {
                            var n = [];
                            n = n.concat(this.getCellsByType("viewCell")), n = n.concat(this.getCellsByType("viewLayout")), n = n.concat(this.getCellsByType("hostCell"));
                            for (var r = this.getCellsByType("scroller"), a = 0; a < n.length; a++)
                                n[a].$config.hidden || n[a].setContentSize();
                            var s = this._getAutosizeMode(this.$config.autosize),
                                    o = this._resizeScrollbars(s, r);
                            if (this.$config.autosize && (this.autosize(this.$config.autosize), o = !0), o) {
                                this.resize();
                                for (var a = 0; a < n.length; a++)
                                    n[a].$config.hidden || n[a].setContentSize()
                            }
                            this.callEvent("onResize", [])
                        }
                        i && (this._resizeInProgress = !1)
                    }, e.prototype._eachChild = function (t, e) {
                        if (e = e || this, t(e), e.$cells)
                            for (var i = 0; i < e.$cells.length; i++)
                                this._eachChild(t, e.$cells[i])
                    }, e.prototype.isChild = function (t) {
                        var e = !1;
                        return this._eachChild(function (i) {
                            i !== t && i.$content !== t || (e = !0)
                        }), e
                    }, e.prototype.getCellsByType = function (t) {
                        var i = [];
                        if (t === this.$name && i.push(this), this.$content && this.$content.$name == t && i.push(this.$content), this.$cells)
                            for (var n = 0; n < this.$cells.length; n++) {
                                var r = e.prototype.getCellsByType.call(this.$cells[n], t);
                                r.length && i.push.apply(i, r)
                            }
                        return i
                    }, e.prototype.getNextSibling = function (t) {
                        var e = this.cellIndex(t);
                        return e >= 0 && this.$cells[e + 1] ? this.$cells[e + 1] : null
                    }, e.prototype.getPrevSibling = function (t) {
                        var e = this.cellIndex(t);
                        return e >= 0 && this.$cells[e - 1] ? this.$cells[e - 1] : null
                    }, e.prototype.cell = function (t) {
                        for (var e = 0; e < this.$cells.length; e++) {
                            var i = this.$cells[e];
                            if (i.$id === t)
                                return i;
                            var n = i.cell(t);
                            if (n)
                                return n
                        }
                    }, e.prototype.cellIndex = function (t) {
                        for (var e = 0; e < this.$cells.length; e++)
                            if (this.$cells[e].$id === t)
                                return e;
                        return -1
                    }, e.prototype.moveView = function (t, e) {
                        if (this.$cells[e] !== t)
                            return window.alert("Not implemented");
                        e += this.$config.header ? 1 : 0;
                        var i = this.$view;
                        e >= i.childNodes.length ? i.appendChild(t.$view) : i.insertBefore(t.$view, i.childNodes[e])
                    }, e.prototype._parseConfig = function (t) {
                        this.$cells = [], this._xLayout = !t.rows;
                        for (var e = t.rows || t.cols || t.views, i = 0; i < e.length; i++) {
                            var n = e[i];
                            n.mode = this._xLayout ? "x" : "y";
                            var r = this.$factory.initUI(n, this);
                            r ? (r.$parent = this, this.$cells.push(r)) : (e.splice(i, 1), i--)
                        }
                    }, e.prototype.getCells = function () {
                        return this.$cells
                    }, e.prototype.render = function () {
                        var t = r.insertNode(this.$container, this.$toHTML());
                        this.$fill(t, null), this.callEvent("onReady", []), this.resize(), this.render = this.resize
                    }, e.prototype.$fill = function (t, e) {
                        this.$view = t, this.$parent = e;
                        for (var i = r.getChildNodes(t, "gantt_layout_cell"), n = i.length - 1; n >= 0; n--) {
                            var a = this.$cells[n];
                            a.$fill(i[n], this), a.$config.hidden && a.$view.parentNode.removeChild(a.$view)
                        }
                    }, e.prototype.$toHTML = function () {
                        for (var e = this._xLayout ? "x" : "y", i = [], n = 0; n < this.$cells.length; n++)
                            i.push(this.$cells[n].$toHTML());
                        return t.prototype.$toHTML.call(this, i.join(""), (this.$root ? "gantt_layout_root " : "") + "gantt_layout gantt_layout_" + e)
                    }, e.prototype.getContentSize = function (t) {
                        for (var e, i, n, r = 0, a = 0, s = 0; s < this.$cells.length; s++)
                            i = this.$cells[s], i.$config.hidden || (e = i.getContentSize(t), "scrollbar" === i.$config.view && t[i.$config.scroll] && (e.height = 0, e.width = 0), i.$config.resizer && (this._xLayout ? e.height = 0 : e.width = 0), n = i._getBorderSizes(), this._xLayout ? (r += e.width + n.horizontal, a = Math.max(a, e.height + n.vertical)) : (r = Math.max(r, e.width + n.horizontal), a += e.height + n.vertical));
                        return n = this._getBorderSizes(), r += n.horizontal, a += n.vertical, this.$root && (r += 1, a += 1), {
                            width: r,
                            height: a
                        }
                    }, e.prototype._cleanElSize = function (t) {
                        return 1 * (t || "").toString().replace("px", "") || 0
                    }, e.prototype._getBoxStyles = function (t) {
                        var e = null;
                        e = window.getComputedStyle ? window.getComputedStyle(t, null) : {
                            width: t.clientWidth,
                            height: t.clientHeight
                        };
                        var i = ["width", "height", "paddingTop", "paddingBottom", "paddingLeft", "paddingRight", "borderLeftWidth", "borderRightWidth", "borderTopWidth", "borderBottomWidth"],
                                n = {
                                    boxSizing: "border-box" == e.boxSizing
                                };
                        e.MozBoxSizing && (n.boxSizing = "border-box" == e.MozBoxSizing);
                        for (var r = 0; r < i.length; r++)
                            n[i[r]] = e[i[r]] ? this._cleanElSize(e[i[r]]) : 0;
                        var a = {
                            horPaddings: n.paddingLeft + n.paddingRight + n.borderLeftWidth + n.borderRightWidth,
                            vertPaddings: n.paddingTop + n.paddingBottom + n.borderTopWidth + n.borderBottomWidth,
                            borderBox: n.boxSizing,
                            innerWidth: n.width,
                            innerHeight: n.height,
                            outerWidth: n.width,
                            outerHeight: n.height
                        };
                        return a.borderBox ? (a.innerWidth -= a.horPaddings, a.innerHeight -= a.vertPaddings) : (a.outerWidth += a.horPaddings, a.outerHeight += a.vertPaddings), a
                    }, e.prototype._getAutosizeMode = function (t) {
                        var e = {
                            x: !1,
                            y: !1
                        };
                        return "xy" === t ? e.x = e.y = !0 : "y" === t || !0 === t ? e.y = !0 : "x" === t && (e.x = !0), e
                    }, e.prototype.autosize = function (t) {
                        var e = this._getAutosizeMode(t),
                                i = this._getBoxStyles(this.$container),
                                n = this.getContentSize(t),
                                r = this.$container;
                        e.x && (i.borderBox && (n.width += i.horPaddings), r.style.width = n.width + "px"), e.y && (i.borderBox && (n.height += i.vertPaddings), r.style.height = n.height + "px")
                    }, e.prototype.getSize = function () {
                        this._sizes = [];
                        for (var e = 0, i = 0, n = 1e5, r = 0, a = 1e5, s = 0, o = 0; o < this.$cells.length; o++) {
                            var l = this._sizes[o] = this.$cells[o].getSize();
                            this.$cells[o].$config.hidden || (this._xLayout ? (!l.width && l.minWidth ? e += l.minWidth : e += l.width, n += l.maxWidth, i += l.minWidth, r = Math.max(r, l.height), a = Math.min(a, l.maxHeight), s = Math.max(s, l.minHeight)) : (!l.height && l.minHeight ? r += l.minHeight : r += l.height, a += l.maxHeight, s += l.minHeight, e = Math.max(e, l.width), n = Math.min(n, l.maxWidth), i = Math.max(i, l.minWidth)))
                        }
                        var d = t.prototype.getSize.call(this);
                        return d.maxWidth >= 1e5 && (d.maxWidth = n), d.maxHeight >= 1e5 && (d.maxHeight = a), d.minWidth = d.minWidth !== d.minWidth ? 0 : d.minWidth, d.minHeight = d.minHeight !== d.minHeight ? 0 : d.minHeight, this._xLayout ? (d.minWidth += this.$config.margin * this.$cells.length || 0, d.minWidth += 2 * this.$config.padding || 0, d.minHeight += 2 * this.$config.padding || 0) : (d.minHeight += this.$config.margin * this.$cells.length || 0, d.minHeight += 2 * this.$config.padding || 0), d
                    }, e.prototype._calcFreeSpace = function (t, e, i) {
                        var n = i ? e.minWidth : e.minHeight,
                                r = e.maxWidth,
                                a = t;
                        return a ? (a > r && (a = r), a < n && (a = n), this._free -= a) : (a = Math.floor(this._free / this._gravity * e.gravity), a > r && (a = r, this._free -= a, this._gravity -= e.gravity), a < n && (a = n, this._free -= a, this._gravity -= e.gravity)), a
                    }, e.prototype._calcSize = function (t, e, i) {
                        var n = t,
                                r = i ? e.minWidth : e.minHeight,
                                a = i ? e.maxWidth : e.maxHeight;
                        return n || (n = Math.floor(this._free / this._gravity * e.gravity)), n > a && (n = a), n < r && (n = r), n
                    }, e.prototype._configureBorders = function () {
                        this.$root && this._setBorders([this._borders.left, this._borders.top, this._borders.right, this._borders.bottom], this);
                        for (var t = this._xLayout ? this._borders.right : this._borders.bottom, e = this.$cells, i = e.length - 1, n = i; n >= 0; n--)
                            if (!e[n].$config.hidden) {
                                i = n;
                                break
                            }
                        for (var n = 0; n < e.length; n++)
                            if (!e[n].$config.hidden) {
                                var r = n >= i,
                                        a = "";
                                !r && e[n + 1] && "scrollbar" == e[n + 1].$config.view && (this._xLayout ? r = !0 : a = "gantt_layout_cell_border_transparent"), this._setBorders(r ? [] : [t, a], e[n])
                            }
                    }, e.prototype._updateCellVisibility = function () {
                        for (var t, e = this._visibleCells || {}, i = !this._visibleCells, n = {}, r = 0; r < this._sizes.length; r++)
                            t = this.$cells[r], !i && t.$config.hidden && e[t.$id] ? t._hide(!0) : t.$config.hidden || e[t.$id] || t._hide(!1), t.$config.hidden || (n[t.$id] = !0);
                        this._visibleCells = n
                    }, e.prototype.setSize = function (e, i) {
                        this._configureBorders(), t.prototype.setSize.call(this, e, i), i = this.$lastSize.contentY, e = this.$lastSize.contentX;
                        var n = this.$config.padding || 0;
                        this.$view.style.padding = n + "px", this._gravity = 0, this._free = this._xLayout ? e : i, this._free -= 2 * n;
                        var r, a;
                        this._updateCellVisibility();
                        for (var s = 0; s < this._sizes.length; s++)
                            if (r = this.$cells[s], !r.$config.hidden) {
                                var o = this.$config.margin || 0;
                                "resizer" != r.$name || o || (o = -1);
                                var l = r.$view,
                                        d = this._xLayout ? "marginRight" : "marginBottom";
                                s !== this.$cells.length - 1 && (l.style[d] = o + "px", this._free -= o), a = this._sizes[s], this._xLayout ? a.width || (this._gravity += a.gravity) : a.height || (this._gravity += a.gravity)
                            }
                        for (var s = 0; s < this._sizes.length; s++)
                            if (r = this.$cells[s], !r.$config.hidden) {
                                a = this._sizes[s];
                                var c = a.width,
                                        h = a.height;
                                this._xLayout ? this._calcFreeSpace(c, a, !0) : this._calcFreeSpace(h, a, !1)
                            }
                        for (var s = 0; s < this.$cells.length; s++)
                            if (r = this.$cells[s], !r.$config.hidden) {
                                a = this._sizes[s];
                                var u = void 0,
                                        _ = void 0;
                                this._xLayout ? (u = this._calcSize(a.width, a, !0), _ = i - 2 * n) : (u = e - 2 * n, _ = this._calcSize(a.height, a, !1)), r.setSize(u, _)
                            }
                    }, e
                }(a);
        t.exports = s
    }, function (t, e, i) {
        function n(t, e) {
            for (var i, n, r, a = 0, s = t.length - 1; a <= s; )
                if (i = Math.floor((a + s) / 2), n = +t[i], r = +t[i - 1], n < e)
                    a = i + 1;
                else {
                    if (!(n > e)) {
                        for (; + t[i] == + t[i + 1]; )
                            i++;
                        return i
                    }
                    if (!isNaN(r) && r < e)
                        return i - 1;
                    s = i - 1
                }
            return t.length - 1
        }
        var r = i(14),
                a = i(2),
                s = i(0),
                o = i(49),
                l = function (t, e, i, n) {
                    this.$config = s.mixin({}, e || {}), this.$scaleHelper = new r(n), this.$gantt = n, a(this)
                };
        l.prototype = {
            init: function (t) {
                t.innerHTML += "<div class='gantt_task' style='width:inherit;height:inherit;'></div>", this.$task = t.childNodes[0], this.$task.innerHTML = "<div class='gantt_task_scale'></div><div class='gantt_data_area'></div>", this.$task_scale = this.$task.childNodes[0], this.$task_data = this.$task.childNodes[1], this.$task_data.innerHTML = "<div class='gantt_task_bg'></div><div class='gantt_links_area'></div><div class='gantt_bars_area'></div>", this.$task_bg = this.$task_data.childNodes[0], this.$task_links = this.$task_data.childNodes[1], this.$task_bars = this.$task_data.childNodes[2], this._tasks = {
                    col_width: 0,
                    width: [],
                    full_width: 0,
                    trace_x: [],
                    rendered: {}
                };
                var e = this.$getConfig(),
                        i = e[this.$config.bind + "_attribute"],
                        n = e[this.$config.bindLinks + "_attribute"];
                !i && this.$config.bind && (i = this.$config.bind + "_id"), !n && this.$config.bindLinks && (n = this.$config.bindLinks + "_id"), this.$config.item_attribute = i || null, this.$config.link_attribute = n || null;
                var r = this._createLayerConfig();
                this.$config.layers || (this.$config.layers = r.tasks), this.$config.linkLayers || (this.$config.linkLayers = r.links), this._attachLayers(this.$gantt), this.callEvent("onReady", [])
            },
            setSize: function (t, e) {
                var i = this.$getConfig();
                if (1 * t === t && (this.$config.width = t), 1 * e === e) {
                    this.$config.height = e;
                    var n = Math.max(this.$config.height - i.scale_height);
                    this.$task_data.style.height = n + "px"
                }
                if (this.refresh(), this.$task_bg.style.backgroundImage = "", i.smart_rendering && this.$config.rowStore) {
                    var r = this.$config.rowStore;
                    this.$task_bg.style.height = i.row_height * r.countVisible() + "px"
                } else
                    this.$task_bg.style.height = "";
                for (var a = this._tasks, s = this.$task_data.childNodes, o = 0, l = s.length; o < l; o++) {
                    var d = s[o];
                    d.hasAttribute("data-layer") && d.style && (d.style.width = a.full_width + "px")
                }
            },
            isVisible: function () {
                return this.$parent && this.$parent.$config ? !this.$parent.$config.hidden : this.$task.offsetWidth
            },
            getSize: function () {
                var t = this.$getConfig(),
                        e = this.$config.rowStore,
                        i = e ? t.row_height * e.countVisible() : 0,
                        n = this._tasks.full_width;
                return {
                    x: this.$config.width,
                    y: this.$config.height,
                    contentX: this.isVisible() ? n : 0,
                    contentY: this.isVisible() ? t.scale_height + i : 0,
                    scrollHeight: this.isVisible() ? i : 0,
                    scrollWidth: this.isVisible() ? n : 0
                }
            },
            scrollTo: function (t, e) {
                this.isVisible() && (1 * e === e && (this.$config.scrollTop = e, this.$task_data.scrollTop = this.$config.scrollTop), 1 * t === t && (this.$task.scrollLeft = t, this.$config.scrollLeft = this.$task.scrollLeft, this._refreshScales()))
            },
            _refreshScales: function () {
                if (this.isVisible()) {
                    if (this.$getConfig().smart_scales) {
                        var t = this.$config.scrollLeft,
                                e = this.$config.width,
                                i = this._scales;
                        this.$task_scale.innerHTML = this._getScaleChunkHtml(i, t, t + e)
                    }
                }
            },
            _createLayerConfig: function () {
                var t = this,
                        e = function () {
                            return t.isVisible()
                        };
                return {
                    tasks: [{
                            expose: !0,
                            renderer: this.$gantt.$ui.layers.taskBar,
                            container: this.$task_bars,
                            filter: [e]
                        }, {
                            renderer: this.$gantt.$ui.layers.taskBg,
                            container: this.$task_bg,
                            filter: [function () {
                                    return !t.$getConfig().static_background
                                }, e]
                        }],
                    links: [{
                            expose: !0,
                            renderer: this.$gantt.$ui.layers.link,
                            container: this.$task_links,
                            filter: [e]
                        }]
                }
            },
            _attachLayers: function (t) {
                this._taskLayers = [], this._linkLayers = [];
                var e = this,
                        i = this.$gantt.$services.getService("layers");
                if (this.$config.bind) {
                    e.$config.rowStore = e.$gantt.getDatastore(e.$config.bind);
                    var n = i.getDataRender(this.$config.bind);
                    n || (n = i.createDataRender({
                        name: this.$config.bind,
                        defaultContainer: function () {
                            return e.$task_data
                        }
                    })), n.container = function () {
                        return e.$task_data
                    };
                    for (var r = this.$config.layers, a = 0; r && a < r.length; a++) {
                        var s = r[a];
                        "string" == typeof s && (s = this.$gantt.$ui.layers[s]), "function" == typeof s && (s = {
                            renderer: s
                        }), s.host = this;
                        var o = n.addLayer(s);
                        this._taskLayers.push(o), s.expose && (this._taskRenderer = n.getLayer(o))
                    }
                    this._initStaticBackgroundRender()
                }
                if (this.$config.bindLinks) {
                    e.$config.linkStore = e.$gantt.getDatastore(e.$config.bindLinks);
                    var l = i.getDataRender(this.$config.bindLinks);
                    l || (l = i.createDataRender({
                        name: this.$config.bindLinks,
                        defaultContainer: function () {
                            return e.$task_data
                        }
                    }));
                    for (var d = this.$config.linkLayers, a = 0; d && a < d.length; a++) {
                        "string" == typeof s && (s = this.$gantt.$ui.layers[s]);
                        var s = d[a];
                        s.host = this;
                        var c = l.addLayer(s);
                        this._taskLayers.push(c), d[a].expose && (this._linkRenderer = l.getLayer(c))
                    }
                }
            },
            _initStaticBackgroundRender: function () {
                var t = this,
                        e = o.create(),
                        i = t.$config.rowStore;
                i && (this._staticBgHandler = i.attachEvent("onStoreUpdated", function (i, n, r) {
                    if (null === i && t.isVisible()) {
                        var a = t.$getConfig();
                        if (a.static_background) {
                            var s = t.$gantt.getDatastore(t.$config.bind);
                            s && e.render(t.$task_bg, a, t.getScale(), a.row_height * s.countVisible())
                        }
                    }
                }), this._initStaticBackgroundRender = function () {})
            },
            _clearLayers: function (t) {
                for (var e = this.$gantt.$services.getService("layers"), i = e.getDataRender(this.$config.bind), n = e.getDataRender(this.$config.bindLinks), r = 0; r < this._taskLayers.length; r++)
                    i.removeLayer(this._taskLayers[r]);
                for (var r = 0; r < this._linkLayers.length; r++)
                    n.removeLayer(this._linkLayers[r]);
                this._linkLayers = [], this._taskLayers = []
            },
            _render_tasks_scales: function () {
                var t = this.$getConfig(),
                        e = "",
                        i = 0,
                        n = 0,
                        r = this.$gantt.getState();
                if (this.isVisible()) {
                    var a = this.$scaleHelper,
                            s = this._getScales();
                    n = t.scale_height;
                    var o = this.$config.width;
                    "x" != t.autosize && "xy" != t.autosize || (o = Math.max(t.autosize_min_width, 0));
                    var l = a.prepareConfigs(s, t.min_column_width, o, n - 1, r.min_date, r.max_date, t.rtl),
                            d = this._tasks = l[l.length - 1];
                    this._scales = l, e = this._getScaleChunkHtml(l, 0, this.$config.width), i = d.full_width + "px", n += "px"
                }
                this.$task_scale.style.height = n, this.$task_data.style.width = this.$task_scale.style.width = i, this.$task_scale.innerHTML = e
            },
            _getScaleChunkHtml: function (t, e, i) {
                for (var n = this.$gantt.$services.templates(), r = [], a = n.scale_row_class, s = 0; s < t.length; s++) {
                    var o = "gantt_scale_line",
                            l = a(t[s]);
                    l && (o += " " + l), r.push('<div class="' + o + '" style="height:' + t[s].height + "px;position:relative;line-height:" + t[s].height + 'px">' + this._prepareScaleHtml(t[s], e, i) + "</div>")
                }
                return r.join("")
            },
            _prepareScaleHtml: function (t, e, i) {
                var r = this.$getConfig(),
                        a = this.$gantt.$services.templates(),
                        s = [],
                        o = null,
                        l = null,
                        d = null;
                (t.template || t.date) && (l = t.template || this.$gantt.date.date_to_str(t.date));
                var c = 0,
                        h = t.count;
                !r.smart_scales || isNaN(e) || isNaN(i) || (c = n(t.left, e), h = n(t.left, i) + 1), d = t.css || function () {}, !t.css && r.inherit_scale_class && (d = a.scale_cell_class);
                for (var u = c; u < h && t.trace_x[u]; u++) {
                    o = new Date(t.trace_x[u]);
                    var _ = l.call(this, o),
                            g = t.width[u],
                            f = t.height,
                            p = t.left[u],
                            v = "",
                            m = "",
                            k = "";
                    if (g) {
                        v = "width:" + g + "px;height:" + f + "px;" + (r.smart_scales ? "position:absolute;left:" + p + "px" : ""), k = "gantt_scale_cell" + (u == t.count - 1 ? " gantt_last_cell" : ""), m = d.call(this, o), m && (k += " " + m);
                        var y = this.$gantt._waiAria.getTimelineCellAttr(_),
                                b = "<div class='" + k + "'" + y + " style='" + v + "'>" + _ + "</div>";
                        s.push(b)
                    }
                }
                return s.join("")
            },
            dateFromPos: function (t) {
                var e = this._tasks;
                if (t < 0 || t > e.full_width || !e.full_width)
                    return null;
                var i = n(this._tasks.left, t),
                        r = this._tasks.left[i],
                        a = e.width[i] || e.col_width,
                        s = 0;
                a && (s = (t - r) / a, e.rtl && (s = 1 - s));
                var o = 0;
                return s && (o = this._getColumnDuration(e, e.trace_x[i])), new Date(e.trace_x[i].valueOf() + Math.round(s * o))
            },
            posFromDate: function (t) {
                if (!this.isVisible())
                    return 0;
                var e = this.columnIndexByDate(t);
                //this.$gantt.assert(e >= 0, "Invalid day index");
                var i = Math.floor(e),
                        n = e % 1,
                        r = this._tasks.left[Math.min(i, this._tasks.width.length - 1)];
                return i == this._tasks.width.length && (r += this._tasks.width[this._tasks.width.length - 1]), n && (i < this._tasks.width.length ? r += this._tasks.width[i] * (n % 1) : r += 1), Math.round(r)
            },
            columnIndexByDate: function (t) {
                var e = new Date(t).valueOf(),
                        i = this._tasks.trace_x_ascending,
                        r = this._tasks.ignore_x,
                        a = this.$gantt.getState();
                if (e <= a.min_date)
                    return this._tasks.rtl ? i.length : 0;
                if (e >= a.max_date)
                    return this._tasks.rtl ? 0 : i.length;
                for (var s = n(i, e), o = +i[s]; r[o]; )
                    o = i[++s];
                var l = this._tasks.trace_index_transition,
                        d = s;
                if (!o)
                    return l ? l[0] : 0;
                var c = (t - i[s]) / this._getColumnDuration(this._tasks, i[s]);
                return l ? l[d] + (1 - c) : d + c
            },
            getItemPosition: function (t, e, i) {
                var n, r, a;
                return this._tasks.rtl ? (r = this.posFromDate(e || t.start_date), n = this.posFromDate(i || t.end_date)) : (n = this.posFromDate(e || t.start_date), r = this.posFromDate(i || t.end_date)), a = Math.max(r - n, 0), {
                    left: n,
                    top: this.getItemTop(t.id),
                    height: this.getItemHeight(),
                    width: a
                }
            },
            getItemHeight: function () {
                var t = this.$getConfig(),
                        e = t.task_height;
                if ("full" == e) {
                    var i = t.task_height_offset || 5;
                    e = t.row_height - i
                }
                return e = Math.min(e, t.row_height), Math.max(e, 0)
            },
            getRowTop: function (t) {
                return t * this.$getConfig().row_height
            },
            getItemTop: function (t) {
                if (this.$config.rowStore) {
                    var e = this.$config.rowStore;
                    return e ? e.getIndexById(t) * this.$getConfig().row_height : 0
                }
                return 0
            },
            getScale: function () {
                return this._tasks
            },
            _getScales: function () {
                var t = this.$getConfig(),
                        e = this.$scaleHelper,
                        i = [e.primaryScale()].concat(t.subscales);
                return e.sortScales(i), i
            },
            _getColumnDuration: function (t, e) {
                return this.$gantt.date.add(e, t.step, t.unit) - e
            },
            refresh: function () {
                this.$config.bind && (this.$config.rowStore = this.$gantt.getDatastore(this.$config.bind)), this.$config.bindLinks && (this.$config.linkStore = this.$gantt.getDatastore(this.$config.bindLinks)), this._initStaticBackgroundRender(), this._render_tasks_scales()
            },
            destructor: function () {
                var t = this.$gantt;
                this._clearLayers(t), this.$task = null, this.$task_scale = null, this.$task_data = null, this.$task_bg = null, this.$task_links = null, this.$task_bars = null, this.$gantt = null, this.$config.rowStore && (this.$config.rowStore.detachEvent(this._staticBgHandler), this.$config.rowStore = null), this.$config.linkStore && (this.$config.linkStore = null), this.callEvent("onDestroy", []), this.detachAllEvents()
            }
        }, t.exports = l
    }, function (t, e, i) {
        function n(t) {
            var e = new r(t);
            return e.processIgnores = function (e) {
                var i = e.count;
                if (e.ignore_x = {}, t.ignore_time || t.config.skip_off_time) {
                    var n = t.ignore_time || function () {
                        return !1
                    };
                    i = 0;
                    for (var r = 0; r < e.trace_x.length; r++)
                        n.call(t, e.trace_x[r]) || this._ignore_time_config.call(t, e.trace_x[r], e) ? (e.ignore_x[e.trace_x[r].valueOf()] = !0, e.ignored_colls = !0) : i++
                }
                e.display_count = i
            }, e
        }
        var r = i(48);
        t.exports = n
    }, function (t, e) {
        function i(t) {
            return {
                refresh: function () {
                    this._linkToTaskStore(), t.prototype.refresh.apply(this, arguments)
                },
                _linkToTaskStore: function () {
                    if (this.$config.rowStore && this.$gantt.$data.tasksStore) {
                        var t = this.$gantt.$data.tasksStore,
                                e = this.$config.rowStore;
                        if (!t["_attached_" + e.name]) {
                            t["_attached_" + e.name] = !0;
                            var i;
                            t.attachEvent("onStoreUpdated", function (t, n, r) {
                                window.requestAnimationFrame ? (cancelAnimationFrame(i), i = requestAnimationFrame(function () {
                                    e.refresh()
                                })) : e.refresh()
                            })
                        }
                        this._linkToTaskStore = function () {}
                    }
                }
            }
        }
        t.exports = i
    }, function (t, e, i) {
        var n = i(17),
                r = i(0),
                a = i(2),
                s = function (t) {
                    return this.pull = {}, this.$initItem = t.initItem, this.visibleOrder = n.$create(), this.fullOrder = n.$create(), this._skip_refresh = !1, this._filterRule = null, this._searchVisibleOrder = {}, a(this), this
                };
        s.prototype = {
            _parseInner: function (t) {
                for (var e = null, i = [], n = 0, r = t.length; n < r; n++)
                    e = t[n], this.$initItem && (e = this.$initItem(e)), this.callEvent("onItemLoading", [e]) && (this.pull.hasOwnProperty(e.id) || (this.fullOrder.push(e.id), i.push(e)), this.pull[e.id] = e);
                return i
            },
            parse: function (t) {
                this.callEvent("onBeforeParse", [t]);
                var e = this._parseInner(t);
                this.refresh(), this.callEvent("onParse", [e])
            },
            getItem: function (t) {
                return this.pull[t]
            },
            _updateOrder: function (t) {
                t.call(this.visibleOrder), t.call(this.fullOrder)
            },
            updateItem: function (t, e) {
                if (r.defined(e) || (e = this.getItem(t)), !this._skip_refresh && !1 === this.callEvent("onBeforeUpdate", [e.id, e]))
                    return !1;
                this.pull[t] = e, this._skip_refresh || (this.callEvent("onAfterUpdate", [e.id, e]), this.callEvent("onStoreUpdated", [e.id, e, "update"]))
            },
            _removeItemInner: function (t) {
                this._updateOrder(function () {
                    this.$remove(t)
                }), delete this.pull[t]
            },
            removeItem: function (t) {
                var e = this.getItem(t);
                if (!this._skip_refresh && !1 === this.callEvent("onBeforeDelete", [e.id, e]))
                    return !1;
                this._removeItemInner(t), this._skip_refresh || (this.filter(), this.callEvent("onAfterDelete", [e.id, e]), this.callEvent("onStoreUpdated", [e.id, e, "delete"]))
            },
            _addItemInner: function (t, e) {
                if (this.exists(t.id))
                    this.silent(function () {
                        this.updateItem(t.id, t)
                    });
                else {
                    var i = this.visibleOrder,
                            n = i.length;
                    (!r.defined(e) || e < 0) && (e = n), e > n && (e = Math.min(i.length, e))
                }
                this.pull[t.id] = t, this._skip_refresh || this._updateOrder(function () {
                    -1 === this.$find(t.id) && this.$insertAt(t.id, e)
                })
            },
            isVisible: function (t) {
                return this.visibleOrder.$find(t) > -1
            },
            getVisibleItems: function () {
                return this.getIndexRange()
            },
            addItem: function (t, e) {
                return r.defined(t.id) || (t.id = r.uid()), this.$initItem && (t = this.$initItem(t)), !(!this._skip_refresh && !1 === this.callEvent("onBeforeAdd", [t.id, t])) && (this._addItemInner(t, e), this._skip_refresh || (this.callEvent("onAfterAdd", [t.id, t]), this.callEvent("onStoreUpdated", [t.id, t, "add"])), t.id)
            },
            _changeIdInner: function (t, e) {
                this.pull[t] && (this.pull[e] = this.pull[t]), this.pull[e].id = e, this._updateOrder(function () {
                    this[this.$find(t)] = e
                }), this._searchVisibleOrder[e] = this._searchVisibleOrder[t], delete this._searchVisibleOrder[t], delete this.pull[t]
            },
            changeId: function (t, e) {
                this._changeIdInner(t, e), this.callEvent("onIdChange", [t, e])
            },
            exists: function (t) {
                return !!this.pull[t]
            },
            _moveInner: function (t, e) {
                var i = this.getIdByIndex(t);
                this._updateOrder(function () {
                    this.$removeAt(t), this.$insertAt(i, Math.min(this.length, e))
                })
            },
            move: function (t, e) {
                var i = this.getIdByIndex(t),
                        n = this.getItem(i);
                this._moveInner(t, e), this._skip_refresh || this.callEvent("onStoreUpdated", [n.id, n, "move"])
            },
            clearAll: function () {
                this.pull = {}, this.visibleOrder = n.$create(), this.fullOrder = n.$create(), this._skip_refresh || (this.callEvent("onClearAll", []), this.refresh())
            },
            silent: function (t, e) {
                this._skip_refresh = !0, t.call(e || this), this._skip_refresh = !1
            },
            arraysEqual: function (t, e) {
                if (t.length !== e.length)
                    return !1;
                for (var i = 0; i < t.length; i++)
                    if (t[i] !== e[i])
                        return !1;
                return !0
            },
            refresh: function (t, e) {
                if (!this._skip_refresh) {
                    if (t) {
                        if (!e) {
                            var i = this.visibleOrder;
                            this.filter(), this.arraysEqual(i, this.visibleOrder) || (t = void 0)
                        }
                    } else
                        this.filter();
                    t ? this.callEvent("onStoreUpdated", [t, this.pull[t], "paint"]) : this.callEvent("onStoreUpdated", [null, null, null])
                }
            },
            count: function () {
                return this.fullOrder.length
            },
            countVisible: function () {
                return this.visibleOrder.length
            },
            sort: function (t) {},
            serialize: function () {},
            eachItem: function (t) {
                for (var e = 0; e < this.fullOrder.length; e++) {
                    var i = this.pull[this.fullOrder[e]];
                    t.call(this, i)
                }
            },
            filter: function (t) {
                var e = n.$create();
                this.eachItem(function (t) {
                    this.callEvent("onFilterItem", [t.id, t]) && e.push(t.id)
                }), this.visibleOrder = e, this._searchVisibleOrder = {};
                for (var i = 0; i < this.visibleOrder.length; i++)
                    this._searchVisibleOrder[this.visibleOrder[i]] = i
            },
            getIndexRange: function (t, e) {
                e = Math.min(e || 1 / 0, this.countVisible() - 1);
                for (var i = [], n = t || 0; n <= e; n++)
                    i.push(this.getItem(this.visibleOrder[n]));
                return i
            },
            getItems: function () {
                var t = [];
                for (var e in this.pull)
                    t.push(this.pull[e]);
                return t
            },
            getIdByIndex: function (t) {
                return this.visibleOrder[t]
            },
            getIndexById: function (t) {
                var e = this._searchVisibleOrder[t];
                return void 0 === e && (e = -1), e
            },
            _getNullIfUndefined: function (t) {
                return void 0 === t ? null : t
            },
            getFirst: function () {
                return this._getNullIfUndefined(this.visibleOrder[0])
            },
            getLast: function () {
                return this._getNullIfUndefined(this.visibleOrder[this.visibleOrder.length - 1])
            },
            getNext: function (t) {
                return this._getNullIfUndefined(this.visibleOrder[this.getIndexById(t) + 1])
            },
            getPrev: function (t) {
                return this._getNullIfUndefined(this.visibleOrder[this.getIndexById(t) - 1])
            },
            destructor: function () {
                this.detachAllEvents(), this.pull = null, this.$initItem = null, this.visibleOrder = null, this.fullOrder = null, this._skip_refresh = null, this._filterRule = null, this._searchVisibleOrder = null
            }
        }, t.exports = s
    }, function (t, e, i) {
        var n = i(0),
                r = {
                    $create: function (t) {
                        return n.mixin(t || [], this)
                    },
                    $removeAt: function (t, e) {
                        t >= 0 && this.splice(t, e || 1)
                    },
                    $remove: function (t) {
                        this.$removeAt(this.$find(t))
                    },
                    $insertAt: function (t, e) {
                        if (e || 0 === e) {
                            var i = this.splice(e, this.length - e);
                            this[e] = t, this.push.apply(this, i)
                        } else
                            this.push(t)
                    },
                    $find: function (t) {
                        for (var e = 0; e < this.length; e++)
                            if (t == this[e])
                                return e;
                        return -1
                    },
                    $each: function (t, e) {
                        for (var i = 0; i < this.length; i++)
                            t.call(e || this, this[i])
                    },
                    $map: function (t, e) {
                        for (var i = 0; i < this.length; i++)
                            this[i] = t.call(e || this, this[i]);
                        return this
                    },
                    $filter: function (t, e) {
                        for (var i = 0; i < this.length; i++)
                            t.call(e || this, this[i]) || (this.splice(i, 1), i--);
                        return this
                    }
                };
        t.exports = r
    }, function (t, e, i) {
        var n = i(17),
                r = i(0),
                a = i(2),
                s = i(16),
                o = function (t) {
                    return this._branches = {}, this.pull = {}, this.$initItem = t.initItem, this.$parentProperty = t.parentProperty || "parent", "function" != typeof t.rootId ? this.$getRootId = function (t) {
                        return function () {
                            return t
                        }
                    }(t.rootId || 0) : this.$getRootId = t.rootId, this.$openInitially = t.openInitially, this.visibleOrder = n.$create(), this.fullOrder = n.$create(), this._searchVisibleOrder = {}, this._skip_refresh = !1, a(this), this.attachEvent("onFilterItem", function (t, e) {
                        var i = !0;
                        return this.eachParent(function (t) {
                            i = i && t.$open
                        }, e), !!i
                    }), this
                };
        o.prototype = r.mixin({
            _buildTree: function (t) {
                for (var e = null, i = this.$getRootId(), n = 0, a = t.length; n < a; n++)
                    e = t[n], this.setParent(e, this.getParent(e) || i);
                for (var n = 0, a = t.length; n < a; n++)
                    e = t[n], this._add_branch(e), e.$level = this.calculateItemLevel(e), r.defined(e.$open) || (e.$open = r.defined(e.open) ? e.open : this.$openInitially());
                this._updateOrder()
            },
            parse: function (t) {
                this.callEvent("onBeforeParse", [t]);
                var e = this._parseInner(t);
                this._buildTree(e), this.filter(), this.callEvent("onParse", [e])
            },
            _addItemInner: function (t, e) {
                var i = this.getParent(t);
                r.defined(i) || (i = this.$getRootId(), this.setParent(t, i));
                var n = this.getIndexById(i),
                        a = n + Math.min(Math.max(e, 0), this.visibleOrder.length);
                1 * a !== a && (a = void 0), s.prototype._addItemInner.call(this, t, a), this.setParent(t, i), t.hasOwnProperty("$rendered_parent") && this._move_branch(t, t.$rendered_parent), this._add_branch(t, e)
            },
            _changeIdInner: function (t, e) {
                var i = this.getChildren(t);
                s.prototype._changeIdInner.call(this, t, e);
                var n = this.getParent(e);
                this._replace_branch_child(n, t, e);
                for (var r = 0; r < i.length; r++)
                    this.setParent(this.getItem(i[r]), e);
                delete this._branches[t]
            },
            _traverseBranches: function (t, e) {
                e = e || this.$getRootId();
                var i = this._branches[e];
                if (i)
                    for (var n = 0; n < i.length; n++) {
                        var r = i[n];
                        t.call(this, r), this._branches[r] && this._traverseBranches(t, r)
                    }
            },
            _updateOrder: function (t) {
                this.fullOrder = n.$create(), this._traverseBranches(function (t) {
                    this.fullOrder.push(t)
                }), t && s.prototype._updateOrder.call(this, t)
            },
            _removeItemInner: function (t) {
                var e = [];
                this.eachItem(function (t) {
                    e.push(t)
                }, t), e.push(this.getItem(t));
                for (var i = 0; i < e.length; i++)
                    this._move_branch(e[i], this.getParent(e[i]), null), s.prototype._removeItemInner.call(this, e[i].id), this._move_branch(e[i], this.getParent(e[i]), null)
            },
            move: function (t, e, i) {
                var n = arguments[3];
                if (n) {
                    if (n === t)
                        return;
                    i = this.getParent(n), e = this.getBranchIndex(n)
                }
                if (t != i) {
                    i = i || this.$getRootId();
                    var r = this.getItem(t),
                            a = this.getParent(r.id),
                            s = (this.getChildren(this.getParent(r.id)), this.getChildren(i));
                    if (-1 == e && (e = s.length + 1), a == i) {
                        if (this.getBranchIndex(t) == e)
                            return
                    }
                    if (!1 !== this.callEvent("onBeforeItemMove", [t, i, e])) {
                        this._replace_branch_child(a, t), s = this.getChildren(i);
                        s[e] ? s = s.slice(0, e).concat([t]).concat(s.slice(e)) : s.push(t), this.setParent(r, i), this._branches[i] = s;
                        var o = this.calculateItemLevel(r) - r.$level;
                        r.$level += o, this.eachItem(function (t) {
                            t.$level += o
                        }, r.id, this), this._moveInner(this.getIndexById(t), this.getIndexById(i) + e), this.callEvent("onAfterItemMove", [t, i, e]), this.refresh()
                    }
                }
            },
            getBranchIndex: function (t) {
                for (var e = this.getChildren(this.getParent(t)), i = 0; i < e.length; i++)
                    if (e[i] == t)
                        return i;
                return -1
            },
            hasChild: function (t) {
                return r.defined(this._branches[t]) && this._branches[t].length
            },
            getChildren: function (t) {
                return r.defined(this._branches[t]) ? this._branches[t] : n.$create()
            },
            isChildOf: function (t, e) {
                if (!this.exists(t))
                    return !1;
                if (e === this.$getRootId())
                    return !0;
                for (var i = this.getItem(t), n = this.getParent(t); i && this.exists(n); ) {
                    if ((i = this.getItem(n)) && i.id == e)
                        return !0;
                    n = this.getParent(i)
                }
                return !1
            },
            getSiblings: function (t) {
                if (!this.exists(t))
                    return n.$create();
                var e = this.getParent(t);
                return this.getChildren(e)
            },
            getNextSibling: function (t) {
                for (var e = this.getSiblings(t), i = 0, n = e.length; i < n; i++)
                    if (e[i] == t)
                        return e[i + 1] || null;
                return null
            },
            getPrevSibling: function (t) {
                for (var e = this.getSiblings(t), i = 0, n = e.length; i < n; i++)
                    if (e[i] == t)
                        return e[i - 1] || null;
                return null
            },
            getParent: function (t) {
                var e = null;
                e = void 0 !== t.id ? t : this.getItem(t);
                var i = this.$getRootId();
                return e && (i = e[this.$parentProperty]), i
            },
            clearAll: function () {
                this._branches = {}, s.prototype.clearAll.call(this)
            },
            calculateItemLevel: function (t) {
                var e = 0;
                return this.eachParent(function () {
                    e++
                }, t), e
            },
            _setParentInner: function (t, e, i) {
                i || (t.hasOwnProperty("$rendered_parent") ? this._move_branch(t, t.$rendered_parent, e) : this._move_branch(t, t[this.$parentProperty], e))
            },
            setParent: function (t, e, i) {
                this._setParentInner(t, e, i), t[this.$parentProperty] = e
            },
            eachItem: function (t, e) {
                e = e || this.$getRootId();
                var i = this.getChildren(e);
                if (i)
                    for (var n = 0; n < i.length; n++) {
                        var r = this.pull[i[n]];
                        t.call(this, r), this.hasChild(r.id) && this.eachItem(t, r.id)
                    }
            },
            eachParent: function (t, e) {
                for (var i = e; this.getParent(i) && this.exists(this.getParent(i)); )
                    i = this.getItem(this.getParent(i)), t.call(this, i)
            },
            _add_branch: function (t, e, i) {
                var r = void 0 === i ? this.getParent(t) : i;
                this.hasChild(r) || (this._branches[r] = n.$create());
                for (var a = this.getChildren(r), s = !1, o = 0, l = a.length; o < l; o++)
                    if (a[o] == t.id) {
                        s = !0;
                        break
                    }
                s || (1 * e == e ? a.splice(e, 0, t.id) : a.push(t.id), t.$rendered_parent = r)
            },
            _move_branch: function (t, e, i) {
                this._replace_branch_child(e, t.id), this.exists(i) || i == this.$getRootId() ? this._add_branch(t, void 0, i) : delete this._branches[t.id], t.$level = this.calculateItemLevel(t), this.eachItem(function (t) {
                    t.$level = this.calculateItemLevel(t)
                }, t.id)
            },
            _replace_branch_child: function (t, e, i) {
                var r = this.getChildren(t);
                if (r && void 0 !== t) {
                    for (var a = n.$create(), s = 0; s < r.length; s++)
                        r[s] != e ? a.push(r[s]) : i && a.push(i);
                    this._branches[t] = a
                }
            },
            sort: function (t, e, i) {
                this.exists(i) || (i = this.$getRootId()), t || (t = "order");
                var n = "string" == typeof t ? function (e, i) {
                    return e[t] == i[t] ? 0 : e[t] > i[t] ? 1 : -1
                } : t;
                if (e) {
                    var r = n;
                    n = function (t, e) {
                        return r(e, t)
                    }
                }
                var a = this.getChildren(i);
                if (a) {
                    for (var s = [], o = a.length - 1; o >= 0; o--)
                        s[o] = this.getItem(a[o]);
                    s.sort(n);
                    for (var o = 0; o < s.length; o++)
                        a[o] = s[o].id, this.sort(t, e, a[o])
                }
            },
            filter: function (t) {
                for (var e in this.pull)
                    this.pull[e].$rendered_parent !== this.getParent(this.pull[e]) && this._move_branch(this.pull[e], this.pull[e].$rendered_parent, this.getParent(this.pull[e]));
                return s.prototype.filter.apply(this, arguments)
            },
            open: function (t) {
                this.exists(t) && (this.getItem(t).$open = !0, this.callEvent("onItemOpen", [t]))
            },
            close: function (t) {
                this.exists(t) && (this.getItem(t).$open = !1, this.callEvent("onItemClose", [t]))
            },
            destructor: function () {
                s.prototype.destructor.call(this), this._branches = null
            }
        }, s.prototype), t.exports = o
    }, function (t, e, i) {
        function n(t) {
            return t.getSubtaskDates()
        }

        function r() {
            return {
                start_date: new Date,
                end_date: new Date
            }
        }

        function a(t, e) {
            var i = {
                start_date: null,
                end_date: null
            };
            if (e.config.start_date && e.config.end_date) {
                i.start_date = e.date[t + "_start"](new Date(e.config.start_date));
                var n = new Date(e.config.end_date),
                        r = e.date[t + "_start"](new Date(n));
                n = +n != +r ? e.date.add(r, 1, t) : r, i.end_date = n
            }
            return i
        }

        function s(t) {
            var e = t.config.scale_unit,
                    i = t.config.step;
            if (t.config.scale_offset_minimal) {
                var n = new d(t),
                        r = [n.primaryScale()].concat(t.config.subscales);
                n.sortScales(r), e = r[r.length - 1].unit, i = r[r.length - 1].step || 1
            }
            return {
                unit: e,
                step: i
            }
        }

        function o(t) {
            var e = s(t),
                    i = e.unit,
                    o = e.step,
                    l = a(i, t);
            l.start_date && l.end_date || (l = n(t), l.start_date && l.end_date || (l = r(t)), l.start_date = t.date[i + "_start"](l.start_date), l.start_date = t.calculateEndDate({
                start_date: t.date[i + "_start"](l.start_date),
                duration: -1,
                unit: i,
                step: o
            }), l.end_date = t.date[i + "_start"](l.end_date), l.end_date = t.calculateEndDate({
                start_date: l.end_date,
                duration: 2,
                unit: i,
                step: o
            })), t._min_date = l.start_date, t._max_date = l.end_date
        }

        function l(t) {
            if (t.config.fit_tasks) {
                var e = +t._min_date,
                        i = +t._max_date;
                if (+t._min_date != e || +t._max_date != i)
                    return t.render(), t.callEvent("onScaleAdjusted", []), !0
            }
            return !1
        }
        var d = i(14);
        t.exports = function (t) {
            o(t), l(t)
        }
    }, function (t, e) {
        function i(t, e, i) {
            for (var n = 0; n < e.length; n++)
                t.isLinkExists(e[n]) && (i[e[n]] = t.getLink(e[n]))
        }

        function n(t, e, n) {
            i(t, e.$source, n), i(t, e.$target, n)
        }

        function r(t, e) {
            var i = {};
            return t.isTaskExists(e) && n(t, t.getTask(e), i), t.eachTask(function (e) {
                n(t, e, i)
            }, e), i
        }

        function a(t, e) {
            var i = {};
            return t.eachTask(function (t) {
                i[t.id] = t
            }, e), i
        }
        t.exports = {
            getSubtreeLinks: r,
            getSubtreeTasks: a
        }
    }, function (t, e, i) {
        var n = i(0),
                r = i(4),
                a = function (t) {
                    return {
                        getWorkHoursArguments: function () {
                            var t = arguments[0];
                            return t = r.isDate(t) ? {
                                date: t
                            } : n.mixin({}, t)
                        },
                        setWorkTimeArguments: function () {
                            return arguments[0]
                        },
                        unsetWorkTimeArguments: function () {
                            return arguments[0]
                        },
                        isWorkTimeArguments: function () {
                            var e = arguments[0];
                            return e.date ? (e = n.mixin({}, e), e.unit = e.unit || t.config.duration_unit, e.task = e.task || null, e.calendar = e.calendar || null) : (e = {}, e.date = arguments[0], e.unit = arguments[1], e.task = arguments[2], e.calendar = arguments[3]), e.unit = e.unit || t.config.duration_unit, e
                        },
                        getClosestWorkTimeArguments: function (e) {
                            return e = arguments[0], e = r.isDate(e) ? {
                                date: e
                            } : n.mixin({}, e), e.dir = e.dir || "any", e.unit = e.unit || t.config.duration_unit, e
                        },
                        getDurationConfig: function (t, e, i, n) {
                            return this.start_date = t, this.end_date = e, this.task = i, this.calendar = n, this.unit = null, this.step = null, this
                        },
                        _getStartEndConfig: function (e) {
                            var i, n = this.getDurationConfig;
                            return e instanceof n ? e : (r.isDate(e) ? i = new n(arguments[0], arguments[1], arguments[2], arguments[3]) : (i = new n(e.start_date, e.end_date, e.task), e.id && (i.task = e)), i.unit = i.unit || t.config.duration_unit, i.step = i.step || t.config.duration_step, i.start_date = i.start_date || i.start || i.date, i)
                        },
                        getDurationArguments: function (t, e, i, n) {
                            return this._getStartEndConfig.apply(this, arguments)
                        },
                        hasDurationArguments: function (t, e, i, n) {
                            return this._getStartEndConfig.apply(this, arguments)
                        },
                        calculateEndDateArguments: function (e, i, a, s) {
                            var o = arguments[0];
                            return o = r.isDate(o) ? {
                                start_date: arguments[0],
                                duration: arguments[1],
                                unit: arguments[2],
                                task: arguments[3],
                                calendar: arguments[4]
                            } : n.mixin({}, o), o.unit = o.unit || t.config.duration_unit, o.step = o.step || t.config.duration_step, o
                        }
                    }
                };
        t.exports = a
    }, function (t, e) {
//    t.exports = function(t) {
//        var e = ["href", "location", "dhtmlx.com/", "indexOf", "error", "This version of Gantt is not intended for using outside of dhtmlx.com", "message", "random", "floor"];
//        ! function() {
//            -1 == document[e[1]][e[0]][e[3]](e[2]) && setTimeout(function() {
//                t[e[6]]({
//                    type: e[4],
//                    text: e[5],
//                    expire: -1
//                })
//            }, Math[e[8]](5e3 * Math[e[7]]()) + 1e3)
//        }()
//    }
    }, function (t, e, i) {
        t.exports = i(24)
    }, function (t, e, i) {
        i(25), window.Gantt ? window.Gantt.plugin(i(22)) : i(22)(window.gantt)
    }, function (t, e, i) {
        window.Gantt = i(26), window.gantt = window.Gantt.getGanttInstance()
    }, function (t, e, i) {
        var n = {
            _seed: 0
        };
        n.plugin = function (t) {
            this._ganttPlugin.push(t), t(window.gantt)
        }, n._ganttPlugin = [], n.getGanttInstance = function () {
            for (var t = i(27)(), e = 0; e < n._ganttPlugin.length; e++)
                n._ganttPlugin[e](t);
            return t._internal_id = n._seed++, n.$syncFactory && n.$syncFactory(t), i(112)(t), t
        }, t.exports = n
    }, function (t, e, i) {
        function n() {
            this.version = "5.1.2", this.templates = {}, this.keys = {
                edit_save: 13,
                edit_cancel: 27
            }
        }
        t.exports = function () {
            var t = new n;
            i(28)(t), t.$services = t.$inject(i(29)), t.config = t.$inject(i(30)), t.ajax = i(31)(t), t.date = i(32)(t);
            var e = i(33)(t);
            t.$services.setService("dnd", function () {
                return e
            }), t.$services.setService("config", function () {
                return t.config
            }), t.$services.setService("date", function () {
                return t.date
            }), t.$services.setService("locale", function () {
                return t.locale
            }), t.$services.setService("templates", function () {
                return t.templates
            });
            var r = i(34)(t);
            t.$services.setService("templateLoader", function () {
                return r
            }), i(2)(t);
            var a = i(35),
                    s = new a;
            s.registerProvider("global", function () {
                return {
                    min_date: t._min_date,
                    max_date: t._max_date,
                    selected_task: t.$data.tasksStore.getSelectedId()
                }
            }), t.getState = s.getState, t.$services.setService("state", function () {
                return s
            });
            var o = i(0);
            o.mixin(t, o), t.env = i(6);
            var l = i(11)();
            t.event = l.attach, t.eventRemove = l.detach, t._eventRemoveAll = l.detachAll, t._createDomEventScope = l.extend, o.mixin(t, i(36)(t));
            var d = i(37).init(t);
            t.$ui = d.factory, t.$ui.layers = d.render, t.$mouseEvents = d.mouseEvents, t.$services.setService("mouseEvents", function () {
                return t.$mouseEvents
            }), t.mixin(t, d.layersApi), i(63)(t), t.$services.setService("layers", function () {
                return d.layersService
            });
            var c = i(64);
            return t.mixin(t, c()), i(65)(t), i(71)(t), i(74)(t), i(81)(t), i(82)(t), i(84)(t), i(85)(t), i(86)(t), i(87)(t), i(94)(t), i(95)(t), i(96)(t), i(97)(t), i(98)(t), i(99)(t), i(100)(t), i(101)(t), i(102)(t), i(103)(t), i(104)(t), i(105)(t), i(106)(t), i(107)(t), i(108)(t), i(109)(t), i(111)(t), t
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.$inject = function (t) {
                return t(this.$services)
            }
        }
    }, function (t, e) {
        t.exports = function () {
            function t(t, e) {
                i[t] = e
            }

            function e(t) {
                return i[t] ? i[t]() : null
            }
            var i = {};
            return {
                services: {
                    config: "config",
                    templates: "templates",
                    locale: "locale"
                },
                setService: t,
                getService: e,
                config: function () {
                    return this.getService("config")
                },
                templates: function () {
                    return this.getService("templates")
                },
                locale: function () {
                    return this.getService("locale")
                },
                destructor: function () {
                    for (var t in i)
                        if (i[t]) {
                            var e = i[t];
                            e && e.destructor && e.destructor()
                        }
                    i = null
                }
            }
        }
    }, function (t, e) {
        t.exports = function () {
            return {
                layout: {
                    css: "gantt_container",
                    rows: [{
                            cols: [{
                                    view: "grid",
                                    scrollX: "scrollHor",
                                    scrollY: "scrollVer"
                                }, {
                                    resizer: !0,
                                    width: 1
                                }, {
                                    view: "timeline",
                                    scrollX: "scrollHor",
                                    scrollY: "scrollVer"
                                }, {
                                    view: "scrollbar",
                                    id: "scrollVer"
                                }]
                        }, {
                            view: "scrollbar",
                            id: "scrollHor",
                            height: 20
                        }]
                },
                links: {
                    finish_to_start: "0",
                    start_to_start: "1",
                    finish_to_finish: "2",
                    start_to_finish: "3"
                },
                types: {
                    task: "task",
                    project: "project",
                    milestone: "milestone"
                },
                duration_unit: "day",
                work_time: !1,
                correct_work_time: !1,
                skip_off_time: !1,
                cascade_delete: !0,
                autosize: !1,
                autosize_min_width: 0,
                autoscroll: !0,
                autoscroll_speed: 30,
                show_links: !0,
                show_task_cells: !0,
                static_background: !1,
                branch_loading: !1,
                branch_loading_property: "$has_child",
                show_loading: !1,
                show_chart: !0,
                show_grid: !0,
                min_duration: 36e5,
                xml_date: "%d-%m-%Y %H:%i",
                api_date: "%d-%m-%Y %H:%i",
                start_on_monday: !0,
                server_utc: !1,
                show_progress: !0,
                fit_tasks: !1,
                select_task: !0,
                scroll_on_click: !0,
                preserve_scroll: !0,
                readonly: !1,
                date_grid: "%Y-%m-%d",
                drag_links: !0,
                drag_progress: !0,
                drag_resize: !0,
                drag_project: !1,
                drag_move: !0,
                drag_mode: {
                    resize: "resize",
                    progress: "progress",
                    move: "move",
                    ignore: "ignore"
                },
                round_dnd_dates: !0,
                link_wrapper_width: 20,
                root_id: 0,
                autofit: !1,
                columns: [{
                        name: "text",
                        tree: !0,
                        width: "*",
                        resize: !0
                    }, {
                        name: "start_date",
                        align: "center",
                        resize: !0
                    }, {
                        name: "duration",
                        align: "center"
                    }, {
                        name: "add",
                        width: 44
                    }],
                step: 1,
                scale_unit: "day",
                scale_offset_minimal: !0,
                subscales: [],
                inherit_scale_class: !1,
                time_step: 60,
                duration_step: 1,
                date_scale: "%d %M",
                task_date: "%d %F %Y",
                time_picker: "%H:%i",
                task_attribute: "task_id",
                link_attribute: "link_id",
                layer_attribute: "data-layer",
                buttons_left: ["gantt_save_btn", "gantt_cancel_btn"],
                _migrate_buttons: {
                    dhx_save_btn: "gantt_save_btn",
                    dhx_cancel_btn: "gantt_cancel_btn",
                    dhx_delete_btn: "gantt_delete_btn"
                },
                buttons_right: ["gantt_delete_btn"],
                lightbox: {
                    sections: [{
                            name: "description",
                            height: 70,
                            map_to: "text",
                            type: "textarea",
                            focus: !0
                        }, {
                            name: "time",
                            type: "duration",
                            map_to: "auto"
                        }],
                    project_sections: [{
                            name: "description",
                            height: 70,
                            map_to: "text",
                            type: "textarea",
                            focus: !0
                        }, {
                            name: "type",
                            type: "typeselect",
                            map_to: "type"
                        }, {
                            name: "time",
                            type: "duration",
                            readonly: !0,
                            map_to: "auto"
                        }],
                    milestone_sections: [{
                            name: "description",
                            height: 70,
                            map_to: "text",
                            type: "textarea",
                            focus: !0
                        }, {
                            name: "type",
                            type: "typeselect",
                            map_to: "type"
                        }, {
                            name: "time",
                            type: "duration",
                            single_date: !0,
                            map_to: "auto"
                        }]
                },
                drag_lightbox: !0,
                sort: !1,
                details_on_create: !0,
                details_on_dblclick: !0,
                initial_scroll: !0,
                task_scroll_offset: 100,
                order_branch: !1,
                order_branch_free: !1,
                task_height: "full",
                min_column_width: 70,
                min_grid_column_width: 70,
                grid_resizer_column_attribute: "column_index",
                grid_resizer_attribute: "grid_resizer",
                keep_grid_width: !1,
                grid_resize: !1,
                show_unscheduled: !0,
                readonly_property: "readonly",
                editable_property: "editable",
                calendar_property: "calendar_id",
                resource_calendars: {},
                type_renderers: {},
                open_tree_initially: !1,
                optimize_render: !0,
                prevent_default_scroll: !1,
                show_errors: !0,
                wai_aria_attributes: !0,
                smart_scales: !0,
                rtl: !1
            }
        }
    }, function (t, e, i) {
        var n = i(6);
        t.exports = function (t) {
            return {
                cache: !0,
                method: "get",
                parse: function (t) {
                    if ("string" != typeof t)
                        return t;
                    var e;
                    return t = t.replace(/^[\s]+/, ""), window.DOMParser && !n.isIE ? e = (new window.DOMParser).parseFromString(t, "text/xml") : window.ActiveXObject !== window.undefined && (e = new window.ActiveXObject("Microsoft.XMLDOM"), e.async = "false", e.loadXML(t)), e
                },
                xmltop: function (e, i, n) {
                    if (void 0 === i.status || i.status < 400) {
                        var r = i.responseXML ? i.responseXML || i : this.parse(i.responseText || i);
                        if (r && null !== r.documentElement && !r.getElementsByTagName("parsererror").length)
                            return r.getElementsByTagName(e)[0]
                    }
                    return -1 !== n && t.callEvent("onLoadXMLError", ["Incorrect XML", arguments[1], n]), document.createElement("DIV")
                },
                xpath: function (t, e) {
                    if (e.nodeName || (e = e.responseXML || e), n.isIE)
                        return e.selectNodes(t) || [];
                    for (var i, r = [], a = (e.ownerDocument || e).evaluate(t, e, null, XPathResult.ANY_TYPE, null); ; ) {
                        if (!(i = a.iterateNext()))
                            break;
                        r.push(i)
                    }
                    return r
                },
                query: function (t) {
                    this._call(t.method || "GET", t.url, t.data || "", t.async || !0, t.callback, null, t.headers)
                },
                get: function (t, e) {
                    this._call("GET", t, null, !0, e)
                },
                getSync: function (t) {
                    return this._call("GET", t, null, !1)
                },
                put: function (t, e, i) {
                    this._call("PUT", t, e, !0, i)
                },
                del: function (t, e, i) {
                    this._call("DELETE", t, e, !0, i)
                },
                post: function (t, e, i) {
                    1 == arguments.length ? e = "" : 2 != arguments.length || "function" != typeof e && "function" != typeof window[e] ? e = String(e) : (i = e, e = ""), this._call("POST", t, e, !0, i)
                },
                postSync: function (t, e) {
                    return e = null === e ? "" : String(e), this._call("POST", t, e, !1)
                },
                getLong: function (t, e) {
                    this._call("GET", t, null, !0, e, {
                        url: t
                    })
                },
                postLong: function (t, e, i) {
                    2 != arguments.length || "function" != typeof e && (window[e], 0) || (i = e, e = ""), this._call("POST", t, e, !0, i, {
                        url: t,
                        postData: e
                    })
                },
                _call: function (e, i, r, a, s, o, l) {
                    var d = window.XMLHttpRequest && !n.isIE ? new XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP"),
                            c = null !== navigator.userAgent.match(/AppleWebKit/) && null !== navigator.userAgent.match(/Qt/) && null !== navigator.userAgent.match(/Safari/);
                    if (a && (d.onreadystatechange = function () {
                        if (4 == d.readyState || c && 3 == d.readyState) {
                            if ((200 != d.status || "" === d.responseText) && !t.callEvent("onAjaxError", [d]))
                                return;
                            window.setTimeout(function () {
                                "function" == typeof s && s.apply(window, [{
                                        xmlDoc: d,
                                        filePath: i
                                    }]), o && (void 0 !== o.postData ? this.postLong(o.url, o.postData, s) : this.getLong(o.url, s)), s = null, d = null
                            }, 1)
                        }
                    }), "GET" != e || this.cache || (i += (i.indexOf("?") >= 0 ? "&" : "?") + "dhxr" + (new Date).getTime() + "=1"), d.open(e, i, a), l)
                        for (var h in l)
                            d.setRequestHeader(h, l[h]);
                    else
                        "POST" == e.toUpperCase() || "PUT" == e || "DELETE" == e ? d.setRequestHeader("Content-Type", "application/x-www-form-urlencoded") : "GET" == e && (r = null);
                    if (d.setRequestHeader("X-Requested-With", "XMLHttpRequest"), d.send(r), !a)
                        return {
                            xmlDoc: d,
                            filePath: i
                        }
                },
                urlSeparator: function (t) {
                    return -1 != t.indexOf("?") ? "&" : "?"
                }
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            var e = {
                init: function () {
                    for (var e = t.locale, i = e.date.month_short, n = e.date.month_short_hash = {}, r = 0; r < i.length; r++)
                        n[i[r]] = r;
                    for (var i = e.date.month_full, n = e.date.month_full_hash = {}, r = 0; r < i.length; r++)
                        n[i[r]] = r
                },
                date_part: function (t) {
                    var e = new Date(t);
                    return t.setHours(0), this.hour_start(t), t.getHours() && (t.getDate() < e.getDate() || t.getMonth() < e.getMonth() || t.getFullYear() < e.getFullYear()) && t.setTime(t.getTime() + 36e5 * (24 - t.getHours())), t
                },
                time_part: function (t) {
                    return (t.valueOf() / 1e3 - 60 * t.getTimezoneOffset()) % 86400
                },
                week_start: function (e) {
                    var i = e.getDay();
                    return t.config.start_on_monday && (0 === i ? i = 6 : i--), this.date_part(this.add(e, -1 * i, "day"))
                },
                month_start: function (t) {
                    return t.setDate(1), this.date_part(t)
                },
                quarter_start: function (t) {
                    this.month_start(t);
                    var e, i = t.getMonth();
                    return e = i >= 9 ? 9 : i >= 6 ? 6 : i >= 3 ? 3 : 0, t.setMonth(e), t
                },
                year_start: function (t) {
                    return t.setMonth(0), this.month_start(t)
                },
                day_start: function (t) {
                    return this.date_part(t)
                },
                hour_start: function (t) {
                    return t.getMinutes() && t.setMinutes(0), this.minute_start(t), t
                },
                minute_start: function (t) {
                    return t.getSeconds() && t.setSeconds(0), t.getMilliseconds() && t.setMilliseconds(0), t
                },
                _add_days: function (t, e) {
                    var i = new Date(t.valueOf());
                    return i.setDate(i.getDate() + e), e >= 0 && !t.getHours() && i.getHours() && (i.getDate() <= t.getDate() || i.getMonth() < t.getMonth() || i.getFullYear() < t.getFullYear()) && i.setTime(i.getTime() + 36e5 * (24 - i.getHours())), i
                },
                add: function (t, e, i) {
                    var n = new Date(t.valueOf());
                    switch (i) {
                        case "day":
                            n = this._add_days(n, e);
                            break;
                        case "week":
                            n = this._add_days(n, 7 * e);
                            break;
                        case "month":
                            n.setMonth(n.getMonth() + e);
                            break;
                        case "year":
                            n.setYear(n.getFullYear() + e);
                            break;
                        case "hour":
                            n.setTime(n.getTime() + 60 * e * 60 * 1e3);
                            break;
                        case "minute":
                            n.setTime(n.getTime() + 60 * e * 1e3);
                            break;
                        default:
                            return this["add_" + i](t, e, i)
                    }
                    return n
                },
                add_quarter: function (t, e) {
                    return this.add(t, 3 * e, "month")
                },
                to_fixed: function (t) {
                    return t < 10 ? "0" + t : t
                },
                copy: function (t) {
                    return new Date(t.valueOf())
                },
                date_to_str: function (i, n) {
                    i = i.replace(/%[a-zA-Z]/g, function (t) {
                        switch (t) {
                            case "%d":
                                return '"+to_fixed(date.getDate())+"';
                            case "%m":
                                return '"+to_fixed((date.getMonth()+1))+"';
                            case "%j":
                                return '"+date.getDate()+"';
                            case "%n":
                                return '"+(date.getMonth()+1)+"';
                            case "%y":
                                return '"+to_fixed(date.getFullYear()%100)+"';
                            case "%Y":
                                return '"+date.getFullYear()+"';
                            case "%D":
                                return '"+locale.date.day_short[date.getDay()]+"';
                            case "%l":
                                return '"+locale.date.day_full[date.getDay()]+"';
                            case "%M":
                                return '"+locale.date.month_short[date.getMonth()]+"';
                            case "%F":
                                return '"+locale.date.month_full[date.getMonth()]+"';
                            case "%h":
                                return '"+to_fixed((date.getHours()+11)%12+1)+"';
                            case "%g":
                                return '"+((date.getHours()+11)%12+1)+"';
                            case "%G":
                                return '"+date.getHours()+"';
                            case "%H":
                                return '"+to_fixed(date.getHours())+"';
                            case "%i":
                                return '"+to_fixed(date.getMinutes())+"';
                            case "%a":
                                return '"+(date.getHours()>11?"pm":"am")+"';
                            case "%A":
                                return '"+(date.getHours()>11?"PM":"AM")+"';
                            case "%s":
                                return '"+to_fixed(date.getSeconds())+"';
                            case "%W":
                                return '"+to_fixed(getISOWeek(date))+"';
                            default:
                                return t
                        }
                    }), n && (i = i.replace(/date\.get/g, "date.getUTC"));
                    var r = new Function("date", "to_fixed", "locale", "getISOWeek", 'return "' + i + '";');
                    return function (i) {
                        return r(i, e.to_fixed, t.locale, e.getISOWeek)
                    }
                },
                str_to_date: function (e, i) {
                    for (var n = "var temp=date.match(/[a-zA-Z]+|[0-9]+/g);", r = e.match(/%[a-zA-Z]/g), a = 0; a < r.length; a++)
                        switch (r[a]) {
                            case "%j":
                            case "%d":
                                n += "set[2]=temp[" + a + "]||1;";
                                break;
                            case "%n":
                            case "%m":
                                n += "set[1]=(temp[" + a + "]||1)-1;";
                                break;
                            case "%y":
                                n += "set[0]=temp[" + a + "]*1+(temp[" + a + "]>50?1900:2000);";
                                break;
                            case "%g":
                            case "%G":
                            case "%h":
                            case "%H":
                                n += "set[3]=temp[" + a + "]||0;";
                                break;
                            case "%i":
                                n += "set[4]=temp[" + a + "]||0;";
                                break;
                            case "%Y":
                                n += "set[0]=temp[" + a + "]||0;";
                                break;
                            case "%a":
                            case "%A":
                                n += "set[3]=set[3]%12+((temp[" + a + "]||'').toLowerCase()=='am'?0:12);";
                                break;
                            case "%s":
                                n += "set[5]=temp[" + a + "]||0;";
                                break;
                            case "%M":
                                n += "set[1]=locale.date.month_short_hash[temp[" + a + "]]||0;";
                                break;
                            case "%F":
                                n += "set[1]=locale.date.month_full_hash[temp[" + a + "]]||0;"
                        }
                    var s = "set[0],set[1],set[2],set[3],set[4],set[5]";
                    i && (s = " Date.UTC(" + s + ")");
                    var o = new Function("date", "locale", "var set=[0,0,1,0,0,0]; " + n + " return new Date(" + s + ");");
                    return function (e) {
                        return o(e, t.locale)
                    }
                },
                getISOWeek: function (t) {
                    if (!t)
                        return !1;
                    var e = t.getDay();
                    0 === e && (e = 7);
                    var i = new Date(t.valueOf());
                    i.setDate(t.getDate() + (4 - e));
                    var n = i.getFullYear(),
                            r = Math.round((i.getTime() - new Date(n, 0, 1).getTime()) / 864e5);
                    return 1 + Math.floor(r / 7)
                },
                getUTCISOWeek: function (t) {
                    return this.getISOWeek(t)
                },
                convert_to_utc: function (t) {
                    return new Date(t.getUTCFullYear(), t.getUTCMonth(), t.getUTCDate(), t.getUTCHours(), t.getUTCMinutes(), t.getUTCSeconds())
                },
                parseDate: function (e, i) {
                    return e && !e.getFullYear && (t.defined(i) && (i = "string" == typeof i ? t.defined(t.templates[i]) ? t.templates[i] : t.date.str_to_date(i) : t.templates.xml_date), e = e ? i(e) : null), e
                }
            };
            return e
        }
    }, function (t, e, i) {
        var n = i(2),
                r = i(0),
                a = i(10);
        t.exports = function (t) {
            function e(t) {
                return {
                    target: t.target || t.srcElement,
                    pageX: t.pageX,
                    pageY: t.pageY,
                    clientX: t.clientX,
                    clientY: t.clientY,
                    metaKey: t.metaKey,
                    shiftKey: t.shiftKey,
                    ctrlKey: t.ctrlKey,
                    altKey: t.altKey
                }
            }

            function i(i, a) {
                this._obj = i, this._settings = a || {}, n(this);
                var s = this.getInputMethods();
                this._drag_start_timer = null, t.attachEvent("onGanttScroll", r.bind(function (t, e) {
                    this.clearDragTimer()
                }, this));
                for (var o = 0; o < s.length; o++)
                    r.bind(function (n) {
                        t.event(i, n.down, r.bind(function (a) {
                            n.accessor(a) && (this._settings.original_target = e(a), t.config.touch ? (this.clearDragTimer(), this._drag_start_timer = setTimeout(r.bind(function () {
                                this.dragStart(i, a, n)
                            }, this), t.config.touch_drag)) : this.dragStart(i, a, n))
                        }, this)), t.event(document.body, n.up, r.bind(function (t) {
                            n.accessor(t) && this.clearDragTimer()
                        }, this))
                    }, this)(s[o])
            }
            return i.prototype = {
                traceDragEvents: function (e, i) {
                    var n = r.bind(function (t) {
                        return this.dragMove(e, t, i.accessor)
                    }, this),
                            s = (r.bind(function (t) {
                                return this.dragScroll(e, t)
                            }, this), r.bind(function (t) {
                                return t && t.preventDefault && t.preventDefault(), (t || event).cancelBubble = !0, !(!r.defined(this.config.updates_per_second) || a(this, this.config.updates_per_second)) || n(t)
                            }, this)),
                            o = r.bind(function (n) {
                                return t.eventRemove(document.body, i.move, s), t.eventRemove(document.body, i.up, o), this.dragEnd(e)
                            }, this);
                    t.event(document.body, i.move, s), t.event(document.body, i.up, o)
                },
                checkPositionChange: function (t) {
                    var e = t.x - this.config.pos.x,
                            i = t.y - this.config.pos.y;
                    return Math.sqrt(Math.pow(Math.abs(e), 2) + Math.pow(Math.abs(i), 2)) > this.config.sensitivity
                },
                initDnDMarker: function () {
                    var t = this.config.marker = document.createElement("div");
                    t.className = "gantt_drag_marker", t.innerHTML = "Dragging object", document.body.appendChild(t)
                },
                backupEventTarget: function (i, n) {
                    if (t.config.touch) {
                        var r = n(i),
                                a = r.target || r.srcElement,
                                s = a.cloneNode(!0);
                        this.config.original_target = e(r), this.config.original_target.target = s, this.config.backup_element = a, a.parentNode.appendChild(s), a.style.display = "none", document.body.appendChild(a)
                    }
                },
                getInputMethods: function () {
                    var e = [];
                    if (e.push({
                        move: "mousemove",
                        down: "mousedown",
                        up: "mouseup",
                        accessor: function (t) {
                            return t
                        }
                    }), t.config.touch) {
                        var i = !0;
                        try {
                            document.createEvent("TouchEvent")
                        } catch (t) {
                            i = !1
                        }
                        i ? e.push({
                            move: "touchmove",
                            down: "touchstart",
                            up: "touchend",
                            accessor: function (t) {
                                return t.touches && t.touches.length > 1 ? null : t.touches[0] ? {
                                    target: document.elementFromPoint(t.touches[0].clientX, t.touches[0].clientY),
                                    pageX: t.touches[0].pageX,
                                    pageY: t.touches[0].pageY,
                                    clientX: t.touches[0].clientX,
                                    clientY: t.touches[0].clientY
                                } : t
                            }
                        }) : window.navigator.pointerEnabled ? e.push({
                            move: "pointermove",
                            down: "pointerdown",
                            up: "pointerup",
                            accessor: function (t) {
                                return "mouse" == t.pointerType ? null : t
                            }
                        }) : window.navigator.msPointerEnabled && e.push({
                            move: "MSPointerMove",
                            down: "MSPointerDown",
                            up: "MSPointerUp",
                            accessor: function (t) {
                                return t.pointerType == t.MSPOINTER_TYPE_MOUSE ? null : t
                            }
                        })
                    }
                    return e
                },
                clearDragTimer: function () {
                    this._drag_start_timer && (clearTimeout(this._drag_start_timer), this._drag_start_timer = null)
                },
                dragStart: function (e, i, n) {
                    this.config = {
                        obj: e,
                        marker: null,
                        started: !1,
                        pos: this.getPosition(i),
                        sensitivity: 4
                    }, this._settings && r.mixin(this.config, this._settings, !0), this.traceDragEvents(e, n), t._prevent_touch_scroll = !0, document.body.className += " gantt_noselect", t.config.touch && this.dragMove(e, i, n.accessor)
                },
                dragMove: function (e, i, n) {
                    var r = n(i);
                    if (r) {
                        if (!this.config.marker && !this.config.started) {
                            var a = this.getPosition(r);
                            if (t.config.touch || this.checkPositionChange(a)) {
                                if (this.config.started = !0, this.config.ignore = !1, !1 === this.callEvent("onBeforeDragStart", [e, this.config.original_target]))
                                    return this.config.ignore = !0, !0;
                                this.backupEventTarget(i, n), this.initDnDMarker(), t._touch_feedback(), this.callEvent("onAfterDragStart", [e, this.config.original_target])
                            } else
                                this.config.ignore = !0
                        }
                        return this.config.ignore ? void 0 : (r.pos = this.getPosition(r), this.config.marker.style.left = r.pos.x + "px", this.config.marker.style.top = r.pos.y + "px", this.callEvent("onDragMove", [e, r]), !1)
                    }
                },
                dragEnd: function (e) {
                    var i = this.config.backup_element;
                    i && i.parentNode && i.parentNode.removeChild(i), t._prevent_touch_scroll = !1, this.config.marker && (this.config.marker.parentNode.removeChild(this.config.marker), this.config.marker = null, this.callEvent("onDragEnd", [])), document.body.className = document.body.className.replace(" gantt_noselect", "")
                },
                getPosition: function (t) {
                    var e = 0,
                            i = 0;
                    return t = t || window.event, t.pageX || t.pageY ? (e = t.pageX, i = t.pageY) : (t.clientX || t.clientY) && (e = t.clientX + document.body.scrollLeft + document.documentElement.scrollLeft, i = t.clientY + document.body.scrollTop + document.documentElement.scrollTop), {
                        x: e,
                        y: i
                    }
                }
            }, i
        }
    }, function (t, e) {
        t.exports = function (t) {
            function e(e, i, r) {
                r = r || e;
                var a = t.config,
                        s = t.templates;
                t.config[e] && n[r] != a[e] && (i && s[r] || (s[r] = t.date.date_to_str(a[e]), n[r] = a[e]))
            }

            function i() {
                var i = t.locale.labels;
                i.gantt_save_btn = i.icon_save, i.gantt_cancel_btn = i.icon_cancel, i.gantt_delete_btn = i.icon_delete;
                var n = t.date,
                        r = n.date_to_str,
                        a = t.config;
                e("date_scale", !0, void 0, t.config, t.templates), e("date_grid", !0, "grid_date_format", t.config, t.templates), e("task_date", !0, void 0, t.config, t.templates), t.mixin(t.templates, {
                    xml_date: n.str_to_date(a.xml_date, a.server_utc),
                    xml_format: r(a.xml_date, a.server_utc),
                    api_date: n.str_to_date(a.api_date),
                    progress_text: function (t, e, i) {
                        return ""
                    },
                    grid_header_class: function (t, e) {
                        return ""
                    },
                    task_text: function (t, e, i) {
                        return i.text
                    },
                    task_class: function (t, e, i) {
                        return ""
                    },
                    grid_row_class: function (t, e, i) {
                        return ""
                    },
                    task_row_class: function (t, e, i) {
                        return ""
                    },
                    task_cell_class: function (t, e) {
                        return ""
                    },
                    scale_cell_class: function (t) {
                        return ""
                    },
                    scale_row_class: function (t) {
                        return ""
                    },
                    grid_indent: function (t) {
                        return "<div class='gantt_tree_indent'></div>"
                    },
                    grid_folder: function (t) {
                        return "<div class='gantt_tree_icon gantt_folder_" + (t.$open ? "open" : "closed") + "'></div>"
                    },
                    grid_file: function (t) {
                        return "<div class='gantt_tree_icon gantt_file'></div>"
                    },
                    grid_open: function (t) {
                        return "<div class='gantt_tree_icon gantt_" + (t.$open ? "close" : "open") + "'></div>"
                    },
                    grid_blank: function (t) {
                        return "<div class='gantt_tree_icon gantt_blank'></div>"
                    },
                    date_grid: function (e, i) {
                        return i && t.isUnscheduledTask(i) && t.config.show_unscheduled ? t.templates.task_unscheduled_time(i) : t.templates.grid_date_format(e)
                    },
                    task_time: function (e, i, n) {
                        return t.isUnscheduledTask(n) && t.config.show_unscheduled ? t.templates.task_unscheduled_time(n) : t.templates.task_date(e) + " - " + t.templates.task_date(i)
                    },
                    task_unscheduled_time: function (t) {
                        return ""
                    },
                    time_picker: r(a.time_picker),
                    link_class: function (t) {
                        return ""
                    },
                    link_description: function (e) {
                        var i = t.getTask(e.source),
                                n = t.getTask(e.target);
                        return "<b>" + i.text + "</b> &ndash;  <b>" + n.text + "</b>"
                    },
                    drag_link: function (e, i, n, r) {
                        e = t.getTask(e);
                        var a = t.locale.labels,
                                s = "<b>" + e.text + "</b> " + (i ? a.link_start : a.link_end) + "<br/>";
                        return n && (n = t.getTask(n), s += "<b> " + n.text + "</b> " + (r ? a.link_start : a.link_end) + "<br/>"), s
                    },
                    drag_link_class: function (e, i, n, r) {
                        var a = "";
                        if (e && n) {
                            a = " " + (t.isLinkAllowed(e, n, i, r) ? "gantt_link_allow" : "gantt_link_deny")
                        }
                        return "gantt_link_tooltip" + a
                    },
                    tooltip_date_format: n.date_to_str("%Y-%m-%d"),
                    tooltip_text: function (e, i, n) {
                        return "<b>Task:</b> " + n.text + "<br/><b>Start date:</b> " + t.templates.tooltip_date_format(e) + "<br/><b>End date:</b> " + t.templates.tooltip_date_format(i)
                    }
                })
            }
            var n = {};
            return {
                initTemplates: i,
                initTemplate: e
            }
        }
    }, function (t, e, i) {
        var n = i(0),
                r = function () {
                    function t(t) {
                        if (t)
                            return r[t].method();
                        var e = {};
                        for (var i in r)
                            r[i].internal || n.mixin(e, r[i].method(), !0);
                        return e
                    }

                    function e(t, e, i) {
                        r[t] = {
                            method: e,
                            internal: i
                        }
                    }

                    function i(t) {
                        delete r[t]
                    }
                    var r = {};
                    return {
                        getState: t,
                        registerProvider: e,
                        unregisterProvider: i
                    }
                };
        t.exports = r
    }, function (t, e, i) {
        var n = i(0);
        i(1);
        t.exports = function (t) {
            function e(t, e) {
                var i = t.callback;
                k.hide(t.box), p = t.box = null, i && i(e)
            }

            function r(t) {
                if (p) {
                    t = t || event;
                    var i = t.which || event.keyCode,
                            n = !1;
                    if (b.keyboard) {
                        if (13 == i || 32 == i) {
                            var r = t.target || t.srcElement;
                            y.getClassName(r).indexOf("gantt_popup_button") > -1 && r.click ? r.click() : (e(p, !0), n = !0)
                        }
                        27 == i && (e(p, !1), n = !0)
                    }
                    if (n)
                        return t.preventDefault && t.preventDefault(), !(t.cancelBubble = !0)
                } else
                    ;
            }

            function a(t) {
                a.cover || (a.cover = document.createElement("DIV"), a.cover.onkeydown = r, a.cover.className = "dhx_modal_cover", document.body.appendChild(a.cover));
                document.body.scrollHeight;
                a.cover.style.display = t ? "inline-block" : "none"
            }

            function s(e, i, n) {
                var r = t._waiAria.messageButtonAttrString(e),
                        a = i.toLowerCase().replace(/ /g, "_");
                return "<div " + r + " class='gantt_popup_button dhtmlx_popup_button gantt_" + a + "_button dhtmlx_" + a + "_button' result='" + n + "' ><div>" + e + "</div></div>"
            }

            function o(e) {
                b.area || (b.area = document.createElement("DIV"), b.area.className = "gantt_message_area dhtmlx_message_area", b.area.style[b.position] = "5px", document.body.appendChild(b.area)), b.hide(e.id);
                var i = document.createElement("DIV");
                return i.innerHTML = "<div>" + e.text + "</div>", i.className = "gantt-info dhtmlx-info gantt-" + e.type + " dhtmlx-" + e.type, i.onclick = function () {
                    b.hide(e.id), e = null
                }, t._waiAria.messageInfoAttr(i), "bottom" == b.position && b.area.firstChild ? b.area.insertBefore(i, b.area.firstChild) : b.area.appendChild(i), e.expire > 0 && (b.timers[e.id] = window.setTimeout(function () {
                    b.hide(e.id)
                }, e.expire)), b.pull[e.id] = i, i = null, e.id
            }

            function l() {
                for (var t = [].slice.apply(arguments, [0]), e = 0; e < t.length; e++)
                    if (t[e])
                        return t[e]
            }

            function d(i, r, a) {
                var o = document.createElement("DIV"),
                        d = (t.locale, n.uid());
                t._waiAria.messageModalAttr(o, d), o.className = " gantt_modal_box dhtmlx_modal_box gantt-" + i.type + " dhtmlx-" + i.type, o.setAttribute("dhxbox", 1);
                var c = "";
                if (i.width && (o.style.width = i.width), i.height && (o.style.height = i.height), i.title && (c += '<div class="gantt_popup_title dhtmlx_popup_title">' + i.title + "</div>"), c += '<div class="gantt_popup_text dhtmlx_popup_text" id="' + d + '"><span>' + (i.content ? "" : i.text) + '</span></div><div  class="gantt_popup_controls dhtmlx_popup_controls">', r && (c += s(l(i.ok, t.locale.labels.message_ok, "OK"), "ok", !0)), a && (c += s(l(i.cancel, t.locale.labels.message_cancel, "Cancel"), "cancel", !1)), i.buttons)
                    for (var h = 0; h < i.buttons.length; h++) {
                        var u = i.buttons[h];
                        if ("object" == typeof u) {
                            var _ = u.label,
                                    g = u.css || "gantt_" + u.label.toLowerCase() + "_button dhtmlx_" + u.label.toLowerCase() + "_button",
                                    f = u.value || h;
                            c += s(_, g, f)
                        } else
                            c += s(u, u, h)
                    }
                if (c += "</div>", o.innerHTML = c, i.content) {
                    var v = i.content;
                    "string" == typeof v && (v = document.getElementById(v)), "none" == v.style.display && (v.style.display = ""), o.childNodes[i.title ? 1 : 0].appendChild(v)
                }
                return o.onclick = function (t) {
                    t = t || event;
                    var n = t.target || t.srcElement;
                    if (n.className || (n = n.parentNode), "gantt_popup_button" == n.className.split(" ")[0]) {
                        var r = n.getAttribute("result");
                        r = "true" == r || "false" != r && r, e(i, r)
                    }
                }, i.box = o, (r || a) && (p = i), o
            }

            function c(e, i, n) {
                var s = e.tagName ? e : d(e, i, n);
                e.hidden || a(!0), document.body.appendChild(s);
                var o = Math.abs(Math.floor(((window.innerWidth || document.documentElement.offsetWidth) - s.offsetWidth) / 2)),
                        l = Math.abs(Math.floor(((window.innerHeight || document.documentElement.offsetHeight) - s.offsetHeight) / 2));
                return "top" == e.position ? s.style.top = "-3px" : s.style.top = l + "px", s.style.left = o + "px", s.onkeydown = r, k.focus(s), e.hidden && k.hide(s), t.callEvent("onMessagePopup", [s]), s
            }

            function h(t) {
                return c(t, !0, !1)
            }

            function u(t) {
                return c(t, !0, !0)
            }

            function _(t) {
                return c(t)
            }

            function g(t, e, i) {
                return "object" != typeof t && ("function" == typeof e && (i = e, e = ""), t = {
                    text: t,
                    type: e,
                    callback: i
                }), t
            }

            function f(t, e, i, r) {
                return "object" != typeof t && (t = {
                    text: t,
                    type: e,
                    expire: i,
                    id: r
                }), t.id = t.id || n.uid(), t.expire = t.expire || b.expire, t
            }
            var p = null;
            t.event(document, "keydown", r, !0);
            var v = function () {
                var t = g.apply(this, arguments);
                return t.type = t.type || "confirm", h(t)
            },
                    m = function () {
                        var t = g.apply(this, arguments);
                        return t.type = t.type || "alert", u(t)
                    },
                    k = function () {
                        var t = g.apply(this, arguments);
                        return t.type = t.type || "alert", _(t)
                    };
            k.hide = function (e) {
                for (; e && e.getAttribute && !e.getAttribute("dhxbox"); )
                    e = e.parentNode;
                e && (e.parentNode.removeChild(e), a(!1), t.callEvent("onAfterMessagePopup", [e]))
            };
            var y = i(1);
            k.focus = function (t) {
                setTimeout(function () {
                    var e = y.getFocusableNodes(t);
                    e.length && e[0].focus && e[0].focus()
                }, 1)
            };
            var b = function (t, e, i, n) {
                switch (t = f.apply(this, arguments), t.type = t.type || "info", t.type.split("-")[0]) {
                    case "alert":
                        return h(t);
                    case "confirm":
                        return u(t);
                    case "modalbox":
                        return _(t);
                    default:
                        return o(t)
                }
            };
            b.seed = (new Date).valueOf(), b.uid = n.uid, b.expire = 4e3, b.keyboard = !0, b.position = "top", b.pull = {}, b.timers = {}, b.hideAll = function () {
                for (var t in b.pull)
                    b.hide(t)
            }, b.hide = function (t) {
                var e = b.pull[t];
                e && e.parentNode && (window.setTimeout(function () {
                    e.parentNode.removeChild(e), e = null
                }, 2e3), e.className += " hidden", b.timers[t] && window.clearTimeout(b.timers[t]), delete b.pull[t])
            };
            var $ = [];
            return t.attachEvent("onMessagePopup", function (t) {
                $.push(t)
            }), t.attachEvent("onAfterMessagePopup", function (t) {
                for (var e = 0; e < $.length; e++)
                    $[e] === t && ($.splice(e, 1), e--)
            }), t.attachEvent("onDestroy", function () {
                a.cover && a.cover.parentNode && a.cover.parentNode.removeChild(a.cover);
                for (var t = 0; t < $.length; t++)
                    $[t].parentNode && $[t].parentNode.removeChild($[t]);
                $ = null, b.area && b.area.parentNode && b.area.parentNode.removeChild(b.area), b = null
            }), {
                alert: v,
                confirm: m,
                message: b,
                modalbox: k
            }
        }
    }, function (t, e, i) {
        function n(t) {
            function e(e, i) {
                var n = i(t);
                n.onCreated && n.onCreated(e), e.attachEvent("onReady", function () {
                    n.onInitialized && n.onInitialized(e)
                }), e.attachEvent("onDestroy", function () {
                    n.onDestroyed && n.onDestroyed(e)
                })
            }
            var i = r.createFactory(t);
            i.registerView("cell", o), i.registerView("resizer", h), i.registerView("scrollbar", u), i.registerView("layout", l, function (t) {
                "main" === (t.$config ? t.$config.id : null) && e(t, w)
            }), i.registerView("viewcell", c), i.registerView("multiview", d), i.registerView("timeline", _, function (t) {
                "timeline" !== (t.$config ? t.$config.id : null) && "task" != t.$config.bind || e(t, $)
            }), i.registerView("grid", g, function (t) {
                "grid" !== (t.$config ? t.$config.id : null) && "task" != t.$config.bind || e(t, b)
            }), i.registerView("resourceGrid", f), i.registerView("resourceTimeline", p);
            var n = s(t);
            return {
                factory: i,
                mouseEvents: a.init(t),
                layersApi: n.init(),
                render: {
                    gridLine: y(t),
                    taskBg: m(t),
                    taskBar: v(t),
                    link: k(t)
                },
                layersService: {
                    getDataRender: function (e) {
                        return n.getDataRender(e, t)
                    },
                    createDataRender: function (e) {
                        return n.createDataRender(e, t)
                    }
                }
            }
        }
        var r = i(38),
                a = i(40),
                s = i(41),
                o = i(5),
                l = i(12),
                d = i(44),
                c = i(45),
                h = i(46),
                u = i(47),
                _ = i(13),
                g = i(8),
                f = i(51),
                p = i(52),
                v = i(53),
                m = i(54),
                k = i(55),
                y = i(56),
                b = i(57),
                $ = i(59),
                w = i(62);
        t.exports = {
            init: n
        }
    }, function (t, e, i) {
        var n = i(0),
                r = i(39),
                a = function (t) {
                    function e(t, e) {
                        var n = "cell";
                        return t.view ? n = "viewcell" : t.resizer ? n = "resizer" : t.rows || t.cols ? n = "layout" : t.views && (n = "multiview"), i.call(this, n, null, t, e)
                    }

                    function i(e, i, a, s) {
                        var o = l[e];
                        if (!o || !o.create)
                            return !1;
                        "resizer" != e || a.mode || (s.$config.cols ? a.mode = "x" : a.mode = "y"), "viewcell" != e || "scrollbar" != a.view || a.scroll || (s.$config.cols ? a.scroll = "y" : a.scroll = "x");
                        var a = n.copy(a);
                        a.id || d[a.view] || (a.id = a.view), a.id && !a.css && (a.css = a.id + "_cell");
                        var c = new o.create(i, a, this, t);
                        return o.configure && o.configure(c), r(c, s), c.$id || (c.$id = a.id || t.uid()), c.$parent || "object" != typeof i || (c.$parent = i), c.$config || (c.$config = a), d[c.$id] && (c.$id = t.uid()), d[c.$id] = c, c
                    }

                    function a() {
                        d = {}
                    }

                    function s(t, e, i) {
                        l[t] = {
                            create: e,
                            configure: i
                        }
                    }

                    function o(t) {
                        return d[t]
                    }
                    var l = {},
                            d = {};
                    return {
                        initUI: e,
                        reset: a,
                        registerView: s,
                        createView: i,
                        getView: o
                    }
                };
        t.exports = {
            createFactory: a
        }
    }, function (t, e, i) {
        function n(t) {
            a.mixin(this, t, !0)
        }

        function r(t, e) {
            var i = this.$config[t];
            return i ? i instanceof n ? i : (n.prototype = e, this.$config[t] = new n(i), this.$config[t]) : e
        }
        var a = i(0),
                s = function (t) {
                    var e, i;
                    return {
                        $getConfig: function () {
                            return e || (e = t ? t.$getConfig() : this.$gantt.config), r.call(this, "config", e)
                        },
                        $getTemplates: function () {
                            return i || (i = t ? t.$getTemplates() : this.$gantt.templates), r.call(this, "templates", i)
                        }
                    }
                };
        t.exports = function (t, e) {
            a.mixin(t, s(e))
        }
    }, function (t, e, i) {
        var n = i(1),
                r = function (t) {
                    return function (e) {
                        function i(t, e, i, n) {
                            u[t][e] || (u[t][e] = []), u[t][e].push({
                                handler: i,
                                root: n
                            })
                        }

                        function n(t, e, i, n) {
                            var r = u[t][e];
                            if (r)
                                for (var a = 0; a < r.length; a++)
                                    (i || r[a].root) && r[a].root !== i || r[a].handler.apply(this, n)
                        }

                        function r(t) {
                            t = t || window.event;
                            var i = (t.target || t.srcElement, e.locate(t)),
                                    n = s(t, u.click),
                                    r = !0;
                            if (null !== i ? r = !e.checkEvent("onTaskClick") || e.callEvent("onTaskClick", [i, t]) : e.callEvent("onEmptyClick", [t]), r) {
                                if (!o(n, t, i))
                                    return;
                                i && e.getTask(i) && e.config.select_task && !e.config.multiselect && e.selectTask(i)
                            }
                        }

                        function a(t) {
                            t = t || window.event;
                            var i = t.target || t.srcElement,
                                    n = e.locate(i),
                                    r = e.locate(i, e.config.link_attribute),
                                    a = !e.checkEvent("onContextMenu") || e.callEvent("onContextMenu", [n, r, t]);
                            return a || (t.preventDefault ? t.preventDefault() : t.returnValue = !1), a
                        }

                        function s(e, i) {
                            for (var n = e.target || e.srcElement, r = []; n; ) {
                                var a = t.getClassName(n);
                                if (a) {
                                    a = a.split(" ");
                                    for (var s = 0; s < a.length; s++)
                                        if (a[s] && i[a[s]])
                                            for (var o = i[a[s]], l = 0; l < o.length; l++)
                                                o[l].root && !t.isChildOf(n, o[l].root) || r.push(o[l].handler)
                                }
                                n = n.parentNode
                            }
                            return r
                        }

                        function o(t, i, n) {
                            for (var r = !0, a = 0; a < t.length; a++) {
                                var s = t[a].call(e, i, n, i.target || i.srcElement);
                                r = r && !(void 0 !== s && !0 !== s)
                            }
                            return r
                        }

                        function l(t) {
                            t = t || window.event;
                            var i = (t.target || t.srcElement, e.locate(t)),
                                    n = s(t, u.doubleclick),
                                    r = !e.checkEvent("onTaskDblClick") || e.callEvent("onTaskDblClick", [i, t]);
                            if (r) {
                                if (!o(n, t, i))
                                    return;
                                null !== i && e.getTask(i) && r && e.config.details_on_dblclick && e.showLightbox(i)
                            }
                        }

                        function d(t) {
                            if (e.checkEvent("onMouseMove")) {
                                var i = e.locate(t);
                                e._last_move_event = t, e.callEvent("onMouseMove", [i, t])
                            }
                        }

                        function c(t, e, i, n) {
                            if (u[t])
                                for (var r = 0; r < u[t].length; r++)
                                    u[t][r].root == n && (u[t].splice(r, 1), r--)
                        }

                        function h(t) {
                            _.detachAll(), t && (_.attach(t, "click", r), _.attach(t, "dblclick", l), _.attach(t, "mousemove", d), _.attach(t, "contextmenu", a))
                        }
                        var u = {
                            click: {},
                            doubleclick: {},
                            contextMenu: {}
                        },
                                _ = e._createDomEventScope();
                        return {
                            reset: h,
                            global: function (t, e, n) {
                                i(t, e, n, null)
                            },
                            delegate: i,
                            detach: c,
                            callHandler: n,
                            onDoubleClick: l,
                            onMouseMove: d,
                            onContextMenu: a,
                            onClick: r,
                            destructor: function () {
                                h(), u = null, _ = null
                            }
                        }
                    }
                }(n);
        t.exports = {
            init: r
        }
    }, function (t, e, i) {
        var n = i(42),
                r = function (t) {
                    var e = n(t);
                    return {
                        getDataRender: function (e) {
                            return t.$services.getService("layer:" + e) || null
                        },
                        createDataRender: function (i) {
                            var n = i.name,
                                    r = i.defaultContainer,
                                    a = i.defaultContainerSibling,
                                    s = e.createGroup(r, a, function (t, e) {
                                        if (!s.filters)
                                            return !0;
                                        for (var i = 0; i < s.filters.length; i++)
                                            if (!1 === s.filters[i](t, e))
                                                return !1
                                    });
                            return t.$services.setService("layer:" + n, function () {
                                return s
                            }), t.attachEvent("onGanttReady", function () {
                                s.addLayer()
                            }), s
                        },
                        init: function () {
                            var e = this.createDataRender({
                                name: "task",
                                defaultContainer: function () {
                                    return t.$task_data ? t.$task_data : t.$ui.getView("timeline") ? t.$ui.getView("timeline").$task_data : void 0
                                },
                                defaultContainerSibling: function () {
                                    return t.$task_links ? t.$task_links : t.$ui.getView("timeline") ? t.$ui.getView("timeline").$task_links : void 0
                                },
                                filter: function (t) {}
                            }, t),
                                    i = this.createDataRender({
                                        name: "link",
                                        defaultContainer: function () {
                                            return t.$task_data ? t.$task_data : t.$ui.getView("timeline") ? t.$ui.getView("timeline").$task_data : void 0
                                        }
                                    }, t);
                            return {
                                addTaskLayer: function (t) {
                                    return e.addLayer(t)
                                },
                                _getTaskLayers: function () {
                                    return e.getLayers()
                                },
                                removeTaskLayer: function (t) {
                                    e.removeLayer(t)
                                },
                                _clearTaskLayers: function () {
                                    e.clear()
                                },
                                addLinkLayer: function (t) {
                                    return i.addLayer(t)
                                },
                                _getLinkLayers: function () {
                                    return i.getLayers()
                                },
                                removeLinkLayer: function (t) {
                                    i.removeLayer(t)
                                },
                                _clearLinkLayers: function () {
                                    i.clear()
                                }
                            }
                        }
                    }
                };
        t.exports = r
    }, function (t, e, i) {
        function n(t) {
            return t instanceof Array || (t = Array.prototype.slice.call(arguments, 0)),
                    function (e) {
                        for (var i = !0, n = 0, r = t.length; n < r; n++) {
                            var a = t[n];
                            a && (i = i && !1 !== a(e.id, e))
                        }
                        return i
                    }
        }
        var r = i(43),
                a = i(0),
                s = i(1),
                o = function (t) {
                    var e = r(t);
                    return {
                        createGroup: function (i, r, o) {
                            var l = {
                                tempCollection: [],
                                renderers: {},
                                container: i,
                                filters: [],
                                getLayers: function () {
                                    this._add();
                                    var t = [];
                                    for (var e in this.renderers)
                                        t.push(this.renderers[e]);
                                    return t
                                },
                                getLayer: function (t) {
                                    return this.renderers[t]
                                },
                                _add: function (t) {
                                    t && (t.id = t.id || a.uid(), this.tempCollection.push(t));
                                    for (var i = this.container(), n = this.tempCollection, o = 0; o < n.length; o++)
                                        if (t = n[o], this.container() || t && t.container && s.isChildOf(t.container, document.body)) {
                                            var l = t.container,
                                                    d = t.id,
                                                    c = t.topmost;
                                            if (!l.parentNode)
                                                if (c)
                                                    i.appendChild(l);
                                                else {
                                                    var h = r ? r() : i.firstChild;
                                                    h ? i.insertBefore(l, h) : i.appendChild(l)
                                                }
                                            this.renderers[d] = e.getRenderer(d, t, l), this.tempCollection.splice(o, 1), o--
                                        }
                                },
                                addLayer: function (t) {
                                    return t && ("function" == typeof t && (t = {
                                        renderer: t
                                    }), void 0 === t.filter ? t.filter = n(o || []) : t.filter instanceof Array && (t.filter.push(o), t.filter = n(t.filter)), t.container || (t.container = document.createElement("div"))), this._add(t), t ? t.id : void 0
                                },
                                eachLayer: function (t) {
                                    for (var e in this.renderers)
                                        t(this.renderers[e])
                                },
                                removeLayer: function (t) {
                                    this.renderers[t] && (this.renderers[t].destructor(), delete this.renderers[t])
                                },
                                clear: function () {
                                    for (var t in this.renderers)
                                        this.renderers[t].destructor();
                                    this.renderers = {}
                                }
                            };
                            return t.attachEvent("onDestroy", function () {
                                l.clear(), l = null
                            }), l
                        }
                    }
                };
        t.exports = o
    }, function (t, e) {
        var i = function (t) {
            function e(e, i, s) {
                if (a[e])
                    return a[e];
                //i.renderer || t.assert(!1, "Invalid renderer call");
                var o = function (t) {
                    return i.renderer.call(this, t, i.host)
                },
                        l = i.filter;
                return s && s.setAttribute(n.config().layer_attribute, !0), a[e] = {
                    render_item: function (e, i) {
                        if (i = i || s, l && !l(e))
                            return void this.remove_item(e.id);
                        var n = o.call(t, e);
                        this.append(e, n, i)
                    },
                    clear: function (t) {
                        this.rendered = r[e] = {}, this.clear_container(t)
                    },
                    clear_container: function (t) {
                        (t = t || s) && (t.innerHTML = "")
                    },
                    render_items: function (t, e) {
                        e = e || s;
                        var i = document.createDocumentFragment();
                        this.clear(e);
                        for (var n = 0, r = t.length; n < r; n++)
                            this.render_item(t[n], i);
                        e.appendChild(i)
                    },
                    append: function (t, e, i) {
                        if (!e)
                            return void(this.rendered[t.id] && this.remove_item(t.id));
                        this.rendered[t.id] && this.rendered[t.id].parentNode ? this.replace_item(t.id, e) : i.appendChild(e), this.rendered[t.id] = e
                    },
                    replace_item: function (t, e) {
                        var i = this.rendered[t];
                        i && i.parentNode && i.parentNode.replaceChild(e, i), this.rendered[t] = e
                    },
                    remove_item: function (t) {
                        this.hide(t), delete this.rendered[t]
                    },
                    hide: function (t) {
                        var e = this.rendered[t];
                        e && e.parentNode && e.parentNode.removeChild(e)
                    },
                    restore: function (t) {
                        var e = this.rendered[t.id];
                        e ? e.parentNode || this.append(t, e, s) : this.render_item(t, s)
                    },
                    change_id: function (t, e) {
                        this.rendered[e] = this.rendered[t], delete this.rendered[t]
                    },
                    rendered: r[e],
                    node: s,
                    destructor: function () {
                        this.clear(), delete a[e], delete r[e]
                    }
                }, a[e]
            }

            function i() {
                for (var t in a)
                    e(t).destructor()
            }
            var n = t.$services,
                    r = {},
                    a = {};
            return {
                getRenderer: e,
                clearRenderers: i
            }
        };
        t.exports = i
    }, function (t, e, i) {
        var n = i(3),
                r = i(12),
                a = i(5),
                s = function (t) {
                    "use strict";

                    function e(e, i, n) {
                        for (var r = t.apply(this, arguments) || this, a = 0; a < r.$cells.length; a++)
                            r.$cells[a].$config.hidden = 0 !== a;
                        return r.$cell = r.$cells[0], r.$name = "viewLayout", r
                    }
                    return n(e, t), e.prototype.cell = function (e) {
                        var i = t.prototype.cell.call(this, e);
                        return i.$view || this.$fill(null, this), i
                    }, e.prototype.moveView = function (t) {
                        var e = this.$view;
                        this.$cell && (this.$cell.$config.hidden = !0, e.removeChild(this.$cell.$view)), this.$cell = t, e.appendChild(t.$view)
                    }, e.prototype.setSize = function (t, e) {
                        a.prototype.setSize.call(this, t, e)
                    }, e.prototype.setContentSize = function () {
                        var t = this.$lastSize;
                        this.$cell.setSize(t.contentX, t.contentY)
                    }, e.prototype.getSize = function () {
                        var e = t.prototype.getSize.call(this);
                        if (this.$cell) {
                            var i = this.$cell.getSize();
                            if (this.$config.byMaxSize)
                                for (var n = 0; n < this.$cells.length; n++) {
                                    var r = this.$cells[n].getSize();
                                    for (var a in i)
                                        i[a] = Math.max(i[a], r[a])
                                }
                            for (var s in e)
                                e[s] = e[s] || i[s];
                            e.gravity = Math.max(e.gravity, i.gravity)
                        }
                        return e
                    }, e
                }(r);
        t.exports = s
    }, function (t, e, i) {
        var n = i(3),
                r = i(0),
                a = i(5),
                s = function (t) {
                    "use strict";

                    function e(e, i, n) {
                        var a = t.apply(this, arguments) || this;
                        if (i.view) {
                            i.id && (this.$id = r.uid());
                            var s = r.copy(i);
                            if (delete s.config, delete s.templates, this.$content = this.$factory.createView(i.view, this, s, this), !this.$content)
                                return !1
                        }
                        return a.$name = "viewCell", a
                    }
                    return n(e, t), e.prototype.destructor = function () {
                        this.clear(), t.prototype.destructor.call(this)
                    }, e.prototype.clear = function () {
                        if (this.$initialized = !1, this.$content) {
                            var e = this.$content.unload || this.$content.destructor;
                            e && e.call(this.$content)
                        }
                        t.prototype.clear.call(this)
                    }, e.prototype.scrollTo = function (e, i) {
                        this.$content && this.$content.scrollTo ? this.$content.scrollTo(e, i) : t.prototype.scrollTo.call(this, e, i)
                    }, e.prototype._setContentSize = function (t, e) {
                        var i = this._getBorderSizes(),
                                n = t + i.horizontal,
                                r = e + i.vertical;
                        this.$config.width = n, this.$config.height = r
                    }, e.prototype.setSize = function (e, i) {
                        if (t.prototype.setSize.call(this, e, i), !this.$preResize && this.$content && !this.$initialized) {
                            this.$initialized = !0;
                            var n = this.$view.childNodes[0],
                                    r = this.$view.childNodes[1];
                            r || (r = n), this.$content.init(r)
                        }
                    }, e.prototype.setContentSize = function () {
                        !this.$preResize && this.$content && this.$initialized && this.$content.setSize(this.$lastSize.contentX, this.$lastSize.contentY)
                    }, e.prototype.getContentSize = function () {
                        var e = t.prototype.getContentSize.call(this);
                        if (this.$content && this.$initialized) {
                            var i = this.$content.getSize();
                            e.width = void 0 === i.contentX ? i.width : i.contentX, e.height = void 0 === i.contentY ? i.height : i.contentY
                        }
                        var n = this._getBorderSizes();
                        return e.width += n.horizontal, e.height += n.vertical, e
                    }, e
                }(a);
        t.exports = s
    }, function (t, e, i) {
        var n = i(3),
                r = i(1),
                a = i(0),
                s = i(5),
                o = function (t) {
                    "use strict";

                    function e(e, i, n) {
                        var r, a, s = t.apply(this, arguments) || this;
                        return s._moveHandler = function (t) {
                            s._moveResizer(s._resizer, t.pageX, t.pageY)
                        }, s._upHandler = function () {
                            var t = s._getNewSizes();
                            !1 !== s.callEvent("onResizeEnd", [r, a, t ? t.back : 0, t ? t.front : 0]) && s._setSizes(), s._setBackground(!1), s._clearResizer(), s._clearListeneres()
                        }, s._clearListeneres = function () {
                            this.$domEvents.detach(document, "mouseup", s._upHandler), this.$domEvents.detach(document, "mousemove", s._moveHandler), this.$domEvents.detach(document, "mousemove", s._startOnMove), this.$domEvents.detach(document, "mouseup", s._cancelDND)
                        }, s._callStartDNDEvent = function () {
                            if (this._xMode ? (r = this._behind.$config.width || this._behind.$view.offsetWidth, a = this._front.$config.width || this._front.$view.offsetWidth) : (r = this._behind.$config.height || this._behind.$view.offsetHeight, a = this._front.$config.height || this._front.$view.offsetHeight), !1 === s.callEvent("onResizeStart", [r, a]))
                                return !1
                        }, s._startDND = function (t) {
                            if (!1 !== this._callStartDNDEvent()) {
                                var e = !1;
                                this._eachGroupItem(function (t) {
                                    t._getSiblings(), !1 === t._callStartDNDEvent() && (e = !0)
                                }), e || (s._moveHandler(t), s.$domEvents.attach(document, "mousemove", s._moveHandler), s.$domEvents.attach(document, "mouseup", s._upHandler))
                            }
                        }, s._cancelDND = function () {
                            s._setBackground(!1), s._clearResizer(), s._clearListeneres()
                        }, s._startOnMove = function (t) {
                            s._isPosChanged(t) && (s._clearListeneres(), s._startDND(t))
                        }, s._downHandler = function (t) {
                            s._getSiblings(), s._behind.$config.collapsed || s._front.$config.collapsed || (s._setBackground(!0), s._resizer = s._setResizer(), s._positions = {
                                x: t.pageX,
                                y: t.pageY,
                                timestamp: Date.now()
                            }, s.$domEvents.attach(document, "mousemove", s._startOnMove), s.$domEvents.attach(document, "mouseup", s._cancelDND))
                        }, s.$name = "resizer", s
                    }
                    return n(e, t), e.prototype.init = function () {
                        var e = this;
                        t.prototype.init.call(this), this._xMode = "x" === this.$config.mode, this._xMode && !this.$config.width ? this.$config.width = this.$config.minWidth = 1 : this._xMode || this.$config.height || (this.$config.height = this.$config.minHeight = 1), this.$config.margin = -1, this.$domEvents.attach(this.$view, "mousedown", e._downHandler)
                    }, e.prototype.$toHTML = function () {
                        var t = this.$config.mode,
                                e = this.$config.css || "";
                        return "<div class='gantt_layout_cell gantt_resizer gantt_resizer_" + t + "'><div class='gantt_layout_content gantt_resizer_" + t + (e ? " " + e : "") + "'></div></div>"
                    }, e.prototype._clearResizer = function () {
                        this._resizer && (this._resizer.parentNode && this._resizer.parentNode.removeChild(this._resizer), this._resizer = null)
                    }, e.prototype._isPosChanged = function (t) {
                        return !!this._positions && (Math.abs(this._positions.x - t.pageX) > 3 || Math.abs(this._positions.y - t.pageY) > 3 || Date.now() - this._positions.timestamp > 300)
                    }, e.prototype._getSiblings = function () {
                        var t = this.$parent.getCells();
                        this.$config.prev && (this._behind = this.$factory.getView(this.$config.prev), this._behind instanceof s || (this._behind = this._behind.$parent)), this.$config.next && (this._front = this.$factory.getView(this.$config.next), this._front instanceof s || (this._front = this._behind.$parent));
                        for (var e = 0; e < t.length; e++)
                            this === t[e] && (this._behind || (this._behind = t[e - 1]), this._front || (this._front = t[e + 1]))
                    }, e.prototype._setBackground = function (t) {
                        var e = "gantt_resizing";
                        if (!t)
                            return r.removeClassName(this._behind.$view, e), r.removeClassName(this._front.$view, e), void r.removeClassName(document.body, "gantt_noselect");
                        r.addClassName(this._behind.$view, e, !0), r.addClassName(this._front.$view, e, !0), r.addClassName(document.body, "gantt_noselect", !0)
                    }, e.prototype._setResizer = function () {
                        var t = document.createElement("div");
                        return t.className = "gantt_resizer_stick", this.$view.appendChild(t), this.$view.style.overflow = "visible", t.style.height = this.$view.style.height, t
                    }, e.prototype._getDirection = function (t, e) {
                        var i;
                        return i = this._xMode ? t - this._positions.x : e - this._positions.y, i ? i < 0 ? -1 : 1 : 0
                    }, e.prototype._getResizePosition = function (t, e) {
                        var i, n, r, a, s;
                        this._xMode ? (i = t - this._positions.x, n = this._behind.$config.width || this._behind.$view.offsetWidth, a = this._front.$config.width || this._front.$view.offsetWidth, r = this._behind.$config.minWidth, s = this._front.$config.minWidth) : (i = e - this._positions.y, n = this._behind.$config.height || this._behind.$view.offsetHeight, a = this._front.$config.height || this._front.$view.offsetHeight, r = this._front.$config.minHeight, s = this._front.$config.minHeight);
                        var o, l, d = this._getDirection(t, e);
                        if (-1 === d) {
                            if (l = a - i, o = n - Math.abs(i), a - i > this._front.$config.maxWidth)
                                return;
                            Math.abs(i) >= n && (i = -Math.abs(n - 2)), n - Math.abs(i) <= r && (i = -Math.abs(n - r))
                        } else
                            l = a - Math.abs(i), o = n + i, n + i > this._behind.$config.maxWidth && (i = this._behind.$config.maxWidth - n), Math.abs(i) >= a && (i = a - 2), a - Math.abs(i) <= s && (i = Math.abs(a - s));
                        return -1 === d ? (l = a - i, o = n - Math.abs(i)) : (l = a - Math.abs(i), o = n + i), {
                            size: i,
                            newFrontSide: l,
                            newBehindSide: o
                        }
                    }, e.prototype._getGroupName = function () {
                        return this._getSiblings(), this._front.$config.group || this._behind.$config.group
                    }, e.prototype._eachGroupItem = function (t, e) {
                        for (var i = this.$factory.getView("main"), n = this._getGroupName(), r = i.getCellsByType("resizer"), a = 0; a < r.length; a++)
                            r[a]._getGroupName() == n && r[a] != this && t.call(e || this, r[a])
                    }, e.prototype._getGroupResizePosition = function (t, e) {
                        var i = this._getResizePosition(t, e);
                        if (!this._getGroupName())
                            return i;
                        var n = [i];
                        this._eachGroupItem(function (i) {
                            i._getSiblings();
                            var r = a.copy(this._positions);
                            this._xMode ? r.x += i._behind.$config.width - this._behind.$config.width : r.y += i._behind.$config.height - this._behind.$config.height, i._positions = r, n.push(i._getResizePosition(t, e))
                        });
                        for (var r, s = 0; s < n.length; s++) {
                            if (!n[s])
                                return;
                            void 0 === r ? r = n[s] : n[s].newBehindSide > r.newBehindSide && (r = n[s])
                        }
                        return r
                    }, e.prototype._moveResizer = function (t, e, i) {
                        if (0 !== e) {
                            var n = this._getGroupResizePosition(e, i);
                            n && 1 !== Math.abs(n.size) && (this._xMode ? (t.style.left = n.size + "px", this._positions.nextX = n.size || 0) : (t.style.top = n.size + "px", this._positions.nextY = n.size || 0), this.callEvent("onResize", [n.newBehindSide, n.newFrontSide]))
                        }
                    }, e.prototype._setGravity = function (t) {
                        var e = this._xMode ? "offsetWidth" : "offsetHeight",
                                i = this._xMode ? this._positions.nextX : this._positions.nextY,
                                n = this._front.$view[e],
                                r = this._behind.$view[e],
                                a = this._front.getSize().gravity,
                                s = this._behind.getSize().gravity,
                                o = (n - i) / n * a,
                                l = (r + i) / r * s;
                        "front" !== t && (this._front.$config.gravity = o), "behind" !== t && (this._behind.$config.gravity = l)
                    }, e.prototype._getNewSizes = function () {
                        var t = this._xMode ? this._behind.$config.width : this._behind.$config.height,
                                e = this._xMode ? this._front.$config.width : this._front.$config.height,
                                i = this._xMode ? this._positions.nextX : this._positions.nextY;
                        return e || t ? {
                            front: e ? e - i || 1 : 0,
                            back: t ? t + i || 1 : 0
                        } : null
                    }, e.prototype._assignNewSizes = function (t) {
                        this._getSiblings();
                        var e = this._xMode ? "width" : "height";
                        t ? (t.front ? this._front.$config[e] = t.front : this._setGravity("behind"), t.back ? this._behind.$config[e] = t.back : this._setGravity("front")) : this._setGravity()
                    }, e.prototype._setSizes = function () {
                        this._resizer && this.$view.removeChild(this._resizer);
                        var t = this._getNewSizes();
                        if (this._positions.nextX || this._positions.nextY) {
                            this._assignNewSizes(t);
                            var e = this._xMode ? "width" : "height";
                            this._front.$config.group ? this.$gantt.$layout._syncCellSizes(this._front.$config.group, this._front.$config[e]) : this._behind.$config.group && this.$gantt.$layout._syncCellSizes(this._behind.$config.group, this._behind.$config[e]), this._getGroupName() ? this.$factory.getView("main").resize() : this.$parent.resize()
                        }
                    }, e
                }(s);
        t.exports = o
    }, function (t, e, i) {
        var n = i(3),
                r = i(1),
                a = i(0),
                s = i(6),
                o = (i(2), i(5)),
                l = function (t) {
                    "use strict";

                    function e(e, i, n, r) {
                        var s = t.apply(this, arguments) || this;
                        this.$config = a.mixin(i, {
                            scroll: "x"
                        }), s._scrollHorizontalHandler = a.bind(s._scrollHorizontalHandler, s), s._scrollVerticalHandler = a.bind(s._scrollVerticalHandler, s), s._outerScrollVerticalHandler = a.bind(s._outerScrollVerticalHandler, s), s._outerScrollHorizontalHandler = a.bind(s._outerScrollHorizontalHandler, s), s._mouseWheelHandler = a.bind(s._mouseWheelHandler, s), this.$config.hidden = !0;
                        var o = r.config.scroll_size;
                        return r.env.isIE && (o += 1), this._isHorizontal() ? (s.$config.height = o, s.$parent.$config.height = o) : (s.$config.width = o, s.$parent.$config.width = o), this.$config.scrollPosition = 0, s.$name = "scroller", s
                    }

                    function i(t, e) {
                        if (e.push(t), t.$cells)
                            for (var n = 0; n < t.$cells.length; n++)
                                i(t.$cells[n], e)
                    }
                    return n(e, t), e.prototype.init = function (t) {
                        t.innerHTML = this.$toHTML(), this.$view = t.firstChild, this.$view || this.init(), this._isVertical() ? this._initVertical() : this._initHorizontal(), this._initMouseWheel(), this._initLinkedViews()
                    }, e.prototype.$toHTML = function () {
                        return "<div class='gantt_layout_cell " + (this._isHorizontal() ? "gantt_hor_scroll" : "gantt_ver_scroll") + "'><div style='" + (this._isHorizontal() ? "width:2000px" : "height:2000px") + "'></div></div>"
                    }, e.prototype._getRootParent = function () {
                        for (var t = this.$parent; t && t.$parent; )
                            t = t.$parent;
                        if (t)
                            return t
                    }, e.prototype._eachView = function () {
                        var t = [];
                        return i(this._getRootParent(), t), t
                    }, e.prototype._getLinkedViews = function () {
                        for (var t = this._eachView(), e = [], i = 0; i < t.length; i++)
                            t[i].$config && (this._isVertical() && t[i].$config.scrollY == this.$id || this._isHorizontal() && t[i].$config.scrollX == this.$id) && e.push(t[i]);
                        return e
                    }, e.prototype._initHorizontal = function () {
                        this.$scroll_hor = this.$view, this.$domEvents.attach(this.$view, "scroll", this._scrollHorizontalHandler)
                    }, e.prototype._initLinkedViews = function () {
                        for (var t = this._getLinkedViews(), e = this._isVertical() ? "gantt_layout_outer_scroll gantt_layout_outer_scroll_vertical" : "gantt_layout_outer_scroll gantt_layout_outer_scroll_horizontal", i = 0; i < t.length; i++)
                            r.addClassName(t[i].$view || t[i].getNode(), e)
                    }, e.prototype._initVertical = function () {
                        this.$scroll_ver = this.$view, this.$domEvents.attach(this.$view, "scroll", this._scrollVerticalHandler)
                    }, e.prototype._updateLinkedViews = function () {
                        this._getRootParent()
                    }, e.prototype._initMouseWheel = function () {
                        s.isFF ? this.$domEvents.attach(this._getRootParent().$view, "wheel", this._mouseWheelHandler) : this.$domEvents.attach(this._getRootParent().$view, "mousewheel", this._mouseWheelHandler)
                    }, e.prototype.scrollHorizontally = function (t) {
                        if (!this._scrolling) {
                            this._scrolling = !0, this.$scroll_hor.scrollLeft = t, this.$config.codeScrollLeft = t, t = this.$scroll_hor.scrollLeft;
                            for (var e = this._getLinkedViews(), i = 0; i < e.length; i++)
                                e[i].scrollTo && e[i].scrollTo(t, void 0);
                            var n = this.$config.scrollPosition;
                            this.$config.scrollPosition = t, this.callEvent("onScroll", [n, t, this.$config.scroll]), this._scrolling = !1
                        }
                    }, e.prototype.scrollVertically = function (t) {
                        if (!this._scrolling) {
                            this._scrolling = !0, this.$scroll_ver.scrollTop = t, t = this.$scroll_ver.scrollTop;
                            for (var e = this._getLinkedViews(), i = 0; i < e.length; i++)
                                e[i].scrollTo && e[i].scrollTo(void 0, t);
                            var n = this.$config.scrollPosition;
                            this.$config.scrollPosition = t, this.callEvent("onScroll", [n, t, this.$config.scroll]), this._scrolling = !1
                        }
                    }, e.prototype._isVertical = function () {
                        return "y" == this.$config.scroll
                    }, e.prototype._isHorizontal = function () {
                        return "x" == this.$config.scroll
                    }, e.prototype._scrollHorizontalHandler = function (t) {
                        if (!this._isVertical() && !this._scrolling) {
                            if (new Date - (this._wheel_time || 0) < 100)
                                return !0;
                            if (!this.$gantt._touch_scroll_active) {
                                var e = this.$scroll_hor.scrollLeft;
                                this.scrollHorizontally(e), this._oldLeft = this.$scroll_hor.scrollLeft
                            }
                        }
                    }, e.prototype._outerScrollHorizontalHandler = function (t) {
                        this._isVertical()
                    }, e.prototype.show = function () {
                        this.$parent.show()
                    }, e.prototype.hide = function () {
                        this.$parent.hide()
                    }, e.prototype._getScrollSize = function () {
                        for (var t, e = 0, i = 0, n = this._isHorizontal(), r = this._getLinkedViews(), a = n ? "scrollWidth" : "scrollHeight", s = n ? "contentX" : "contentY", o = n ? "x" : "y", l = this._getScrollOffset(), d = 0; d < r.length; d++)
                            if ((t = r[d]) && t.$content && t.$content.getSize && !t.$config.hidden) {
                                var c, h = t.$content.getSize();
                                if (c = h.hasOwnProperty(a) ? h[a] : h[s], l)
                                    h[s] > h[o] && h[s] > e && c > h[o] - l + 2 && (e = c + (n ? 0 : 2), i = h[o]);
                                else {
                                    var u = Math.max(h[s] - c, 0),
                                            _ = Math.max(h[o] - u, 0);
                                    c += u, c > _ && c > e && (e = c, i = h[o])
                                }
                            }
                        return {
                            outerScroll: i,
                            innerScroll: e
                        }
                    }, e.prototype.scroll = function (t) {
                        this._isHorizontal() ? this.scrollHorizontally(t) : this.scrollVertically(t)
                    }, e.prototype.getScrollState = function () {
                        return {
                            visible: this.isVisible(),
                            direction: this.$config.scroll,
                            size: this.$config.outerSize,
                            scrollSize: this.$config.scrollSize || 0,
                            position: this.$config.scrollPosition || 0
                        }
                    }, e.prototype.setSize = function (e, i) {
                        t.prototype.setSize.apply(this, arguments);
                        var n = this._getScrollSize(),
                                r = (this._isVertical() ? i : e) - this._getScrollOffset() + (this._isHorizontal() ? 1 : 0);
                        n.innerScroll && r > n.outerScroll && (n.innerScroll += r - n.outerScroll), this.$config.scrollSize = n.innerScroll, this.$config.width = e, this.$config.height = i, this._setScrollSize(n.innerScroll)
                    }, e.prototype.isVisible = function () {
                        return !(!this.$parent || !this.$parent.$view.parentNode)
                    }, e.prototype.shouldShow = function () {
                        var t = this._getScrollSize();
                        return !(!t.innerScroll && this.$parent && this.$parent.$view.parentNode) && !(!t.innerScroll || this.$parent && this.$parent.$view.parentNode)
                    }, e.prototype.shouldHide = function () {
                        return !(this._getScrollSize().innerScroll || !this.$parent || !this.$parent.$view.parentNode)
                    }, e.prototype.toggleVisibility = function () {
                        this.shouldHide() ? this.hide() : this.shouldShow() && this.show()
                    }, e.prototype._getScaleOffset = function (t) {
                        var e = 0;
                        return !t || "timeline" != t.$config.view && "grid" != t.$config.view || (e = t.$content.$getConfig().scale_height), e
                    }, e.prototype._getScrollOffset = function () {
                        var t = 0;
                        if (this._isVertical()) {
                            var e = this.$parent.$parent;
                            t = Math.max(this._getScaleOffset(e.getPrevSibling(this.$parent.$id)), this._getScaleOffset(e.getNextSibling(this.$parent.$id)))
                        } else
                            for (var i = this._getLinkedViews(), n = 0; n < i.length; n++) {
                                var r = i[n],
                                        a = r.$parent,
                                        s = a.$cells,
                                        o = s[s.length - 1];
                                if (o && "scrollbar" == o.$config.view && !1 === o.$config.hidden) {
                                    t = o.$config.width;
                                    break
                                }
                            }
                        return t || 0
                    }, e.prototype._setScrollSize = function (t) {
                        var e = this._isHorizontal() ? "width" : "height",
                                i = this._isHorizontal() ? this.$scroll_hor : this.$scroll_ver,
                                n = this._getScrollOffset(),
                                a = i.firstChild;
                        n ? this._isVertical() ? (this.$config.outerSize = this.$config.height - n + 3, i.style.height = this.$config.outerSize + "px", i.style.top = n - 1 + "px", r.addClassName(i, this.$parent._borders.top), r.addClassName(i.parentNode, "gantt_task_vscroll")) : (this.$config.outerSize = this.$config.width - n + 1, i.style.width = this.$config.outerSize + "px") : (i.style.top = "auto", r.removeClassName(i, this.$parent._borders.top), r.removeClassName(i.parentNode, "gantt_task_vscroll"), this.$config.outerSize = this.$config.height), a.style[e] = t + "px"
                    }, e.prototype._scrollVerticalHandler = function (t) {
                        if (!this._scrollHorizontalHandler() && !this._scrolling && !this.$gantt._touch_scroll_active) {
                            var e = this.$scroll_ver.scrollTop;
                            e != this._oldTop && (this.scrollVertically(e), this._oldTop = this.$scroll_ver.scrollTop)
                        }
                    }, e.prototype._outerScrollVerticalHandler = function (t) {
                        this._scrollHorizontalHandler()
                    }, e.prototype._checkWheelTarget = function (t) {
                        for (var e = this._getLinkedViews().concat(this), i = 0; i < e.length; i++) {
                            var n = e[i].$view;
                            if (r.isChildOf(t, n))
                                return !0
                        }
                        return !1
                    }, e.prototype._mouseWheelHandler = function (t) {
                        var e = t.target || t.srcElement;
                        if (this._checkWheelTarget(e)) {
                            this._wheel_time = new Date;
                            var i = {},
                                    n = s.isFF,
                                    r = n ? -20 * t.deltaX : 2 * t.wheelDeltaX,
                                    a = n ? -40 * t.deltaY : t.wheelDelta;
                            if (!t.shiftKey || t.deltaX || t.wheelDeltaX || (r = 2 * a, a = 0), r && Math.abs(r) > Math.abs(a)) {
                                if (this._isVertical())
                                    return;
                                if (i.x)
                                    return !0;
                                if (!this.$scroll_hor || !this.$scroll_hor.offsetWidth)
                                    return !0;
                                var o = r / -40,
                                        l = this._oldLeft,
                                        d = l + 30 * o;
                                if (this.scrollHorizontally(d), this.$scroll_hor.scrollLeft = d, l == this.$scroll_hor.scrollLeft)
                                    return !0;
                                this._oldLeft = this.$scroll_hor.scrollLeft
                            } else {
                                if (this._isHorizontal())
                                    return;
                                if (i.y)
                                    return !0;
                                if (!this.$scroll_ver || !this.$scroll_ver.offsetHeight)
                                    return !0;
                                var o = a / -40;
                                void 0 === a && (o = t.detail);
                                var c = this._oldTop,
                                        h = this.$scroll_ver.scrollTop + 30 * o;
                                if (this.scrollVertically(h), this.$scroll_ver.scrollTop = h, c == this.$scroll_ver.scrollTop)
                                    return !0;
                                this._oldTop = this.$scroll_ver.scrollTop
                            }
                            return t.preventDefault && t.preventDefault(), t.cancelBubble = !0, !1
                        }
                    }, e
                }(o);
        t.exports = l
    }, function (t, e, i) {
        function n(t) {
            var e = t.date,
                    i = t.$services;
            return {
                getSum: function (t, e, i) {
                    void 0 === i && (i = t.length - 1), void 0 === e && (e = 0);
                    for (var n = 0, r = e; r <= i; r++)
                        n += t[r];
                    return n
                },
                setSumWidth: function (t, e, i, n) {
                    var r = e.width;
                    void 0 === n && (n = r.length - 1), void 0 === i && (i = 0);
                    var a = n - i + 1;
                    if (!(i > r.length - 1 || a <= 0 || n > r.length - 1)) {
                        var s = this.getSum(r, i, n),
                                o = t - s;
                        this.adjustSize(o, r, i, n), this.adjustSize(-o, r, n + 1), e.full_width = this.getSum(r)
                    }
                },
                splitSize: function (t, e) {
                    for (var i = [], n = 0; n < e; n++)
                        i[n] = 0;
                    return this.adjustSize(t, i), i
                },
                adjustSize: function (t, e, i, n) {
                    i || (i = 0), void 0 === n && (n = e.length - 1);
                    for (var r = n - i + 1, a = this.getSum(e, i, n), s = 0, o = i; o <= n; o++) {
                        var l = Math.floor(t * (a ? e[o] / a : 1 / r));
                        a -= e[o], t -= l, r--, e[o] += l, s += l
                    }
                    e[e.length - 1] += t
                },
                sortScales: function (t) {
                    function i(t, i) {
                        var n = new Date(1970, 0, 1);
                        return e.add(n, i, t) - n
                    }
                    t.sort(function (t, e) {
                        return i(t.unit, t.step) < i(e.unit, e.step) ? 1 : i(t.unit, t.step) > i(e.unit, e.step) ? -1 : 0
                    });
                    for (var n = 0; n < t.length; n++)
                        t[n].index = n
                },
                primaryScale: function () {
                    return i.getService("templateLoader").initTemplate("date_scale", void 0, void 0, i.config(), i.templates()), {
                        unit: i.config().scale_unit,
                        step: i.config().step,
                        template: i.templates().date_scale,
                        date: i.config().date_scale,
                        css: i.templates().scale_cell_class
                    }
                },
                prepareConfigs: function (t, e, i, n, r, a, s) {
                    for (var o = this.splitSize(n, t.length), l = i, d = [], c = t.length - 1; c >= 0; c--) {
                        var h = c == t.length - 1,
                                u = this.initScaleConfig(t[c], r, a);
                        h && this.processIgnores(u), this.initColSizes(u, e, l, o[c]), this.limitVisibleRange(u), h && (l = u.full_width), d.unshift(u)
                    }
                    for (var c = 0; c < d.length - 1; c++)
                        this.alineScaleColumns(d[d.length - 1], d[c]);
                    for (var c = 0; c < d.length; c++)
                        s && this.reverseScale(d[c]), this.setPosSettings(d[c]);
                    return d
                },
                reverseScale: function (t) {
                    t.width = t.width.reverse(), t.trace_x = t.trace_x.reverse();
                    var e = t.trace_indexes;
                    t.trace_indexes = {}, t.trace_index_transition = {}, t.rtl = !0;
                    for (var i = 0; i < t.trace_x.length; i++)
                        t.trace_indexes[t.trace_x[i].valueOf()] = i, t.trace_index_transition[e[t.trace_x[i].valueOf()]] = i;
                    return t
                },
                setPosSettings: function (t) {
                    for (var e = 0, i = t.trace_x.length; e < i; e++)
                        t.left.push((t.width[e - 1] || 0) + (t.left[e - 1] || 0))
                },
                _ignore_time_config: function (t, n) {
                    if (i.config().skip_off_time) {
                        for (var r = !0, a = t, s = 0; s < n.step; s++)
                            s && (a = e.add(t, s, n.unit)), r = r && !this.isWorkTime(a, n.unit);
                        return r
                    }
                    return !1
                },
                processIgnores: function (t) {
                    t.ignore_x = {}, t.display_count = t.count
                },
                initColSizes: function (t, i, n, r) {
                    var a = n;
                    t.height = r;
                    var s = void 0 === t.display_count ? t.count : t.display_count;
                    s || (s = 1), t.col_width = Math.floor(a / s), i && t.col_width < i && (t.col_width = i, a = t.col_width * s), t.width = [];
                    for (var o = t.ignore_x || {}, l = 0; l < t.trace_x.length; l++)
                        if (o[t.trace_x[l].valueOf()] || t.display_count == t.count)
                            t.width[l] = 0;
                        else {
                            var d = 1;
                            if ("month" == t.unit) {
                                var c = Math.round((e.add(t.trace_x[l], t.step, t.unit) - t.trace_x[l]) / 864e5);
                                d = c
                            }
                            t.width[l] = d
                        }
                    this.adjustSize(a - this.getSum(t.width), t.width), t.full_width = this.getSum(t.width)
                },
                initScaleConfig: function (t, e, i) {
                    var n = r.mixin({
                        count: 0,
                        col_width: 0,
                        full_width: 0,
                        height: 0,
                        width: [],
                        left: [],
                        trace_x: [],
                        trace_indexes: {},
                        min_date: new Date(e),
                        max_date: new Date(i)
                    }, t);
                    return this.eachColumn(t.unit, t.step, e, i, function (t) {
                        n.count++, n.trace_x.push(new Date(t)), n.trace_indexes[t.valueOf()] = n.trace_x.length - 1
                    }), n.trace_x_ascending = n.trace_x.slice(), n
                },
                iterateScales: function (t, e, i, n, r) {
                    for (var a = e.trace_x, s = t.trace_x, o = i || 0, l = n || s.length - 1, d = 0, c = 1; c < a.length; c++) {
                        var h = t.trace_indexes[+a[c]];
                        void 0 !== h && h <= l && (r && r.apply(this, [d, c, o, h]), o = h, d = c)
                    }
                },
                alineScaleColumns: function (t, e, i, n) {
                    this.iterateScales(t, e, i, n, function (i, n, r, a) {
                        var s = this.getSum(t.width, r, a - 1);
                        this.getSum(e.width, i, n - 1) != s && this.setSumWidth(s, e, i, n - 1)
                    })
                },
                eachColumn: function (i, n, r, a, s) {
                    var o = new Date(r),
                            l = new Date(a);
                    e[i + "_start"] && (o = e[i + "_start"](o));
                    var d = new Date(o);
                    for (+d >= +l && (l = e.add(d, n, i)); +d < +l; ) {
                        s.call(this, new Date(d));
                        var c = d.getTimezoneOffset();
                        d = e.add(d, n, i), d = t._correct_dst_change(d, c, n, i), e[i + "_start"] && (d = e[i + "_start"](d))
                    }
                },
                limitVisibleRange: function (t) {
                    var i = t.trace_x,
                            n = t.width.length - 1,
                            r = 0;
                    if (+i[0] < +t.min_date && 0 != n) {
                        var a = Math.floor(t.width[0] * ((i[1] - t.min_date) / (i[1] - i[0])));
                        r += t.width[0] - a, t.width[0] = a, i[0] = new Date(t.min_date)
                    }
                    var s = i.length - 1,
                            o = i[s],
                            l = e.add(o, t.step, t.unit);
                    if (+l > +t.max_date && s > 0) {
                        var a = t.width[s] - Math.floor(t.width[s] * ((l - t.max_date) / (l - o)));
                        r += t.width[s] - a, t.width[s] = a
                    }
                    if (r) {
                        for (var d = this.getSum(t.width), c = 0, h = 0; h < t.width.length; h++) {
                            var u = Math.floor(r * (t.width[h] / d));
                            t.width[h] += u, c += u
                        }
                        this.adjustSize(r - c, t.width)
                    }
                }
            }
        }
        var r = i(0);
        t.exports = n
    }, function (t, e, i) {
        var n = i(0),
                r = i(6),
                a = function (t, e) {
                    function i(t, e, i, n) {
                        if (e.static_background) {
                            if (document.createElement("canvas").getContext) {
                                t.innerHTML = "";
                                var r = _(t),
                                        a = h(r, e, i),
                                        s = u(a, e, i, n),
                                        o = document.createDocumentFragment();
                                s.forEach(function (t) {
                                    o.appendChild(t)
                                }), t.appendChild(o)
                            }
                        }
                    }

                    function n(t) {
                        for (var e = t.width, i = {}, n = 0; n < e.length; n++)
                            1 * e[n] && (i[e[n]] = !0);
                        return i
                    }

                    function r(t) {
                        var e = /^rgba?\(([\d]{1,3}), *([\d]{1,3}), *([\d]{1,3}) *(,( *[\d\.]+ *))?\)$/i.exec(t);
                        return e ? {
                            r: 1 * e[1],
                            g: 1 * e[2],
                            b: 1 * e[3],
                            a: 255 * e[5] || 255
                        } : null
                    }

                    function a(t) {
                        return g[t] || null
                    }

                    function s(t, e, i) {
                        return (t + "" + e + i.bottomBorderColor + i.rightBorderColor).replace(/[^\w\d]/g, "")
                    }

                    function o() {
                        var t = document.getElementById(f);
                        return t || (t = document.createElement("style"), t.id = f, document.body.appendChild(t)), t
                    }

                    function l(t, e) {
                        g[t] = e
                    }

                    function d(t, e, i) {
                        function n(e, i, n, r) {
                            var s = t * a,
                                    o = 4 * (i * s + e);
                            r.data[o] = n.r, r.data[o + 1] = n.g, r.data[o + 2] = n.b, r.data[o + 3] = n.a
                        }
                        var a = Math.floor(500 / t) || 1,
                                s = Math.floor(500 / e) || 1,
                                o = document.createElement("canvas");
                        o.height = e * s, o.width = t * a;
                        var l = o.getContext("2d");
                        return function (t, e, i, a, s, o) {
                            var l = s.createImageData(e * a, t * i);
                            l.imageSmoothingEnabled = !1;
                            for (var d = 1 * o.rightBorderWidth, c = r(o.rightBorderColor), h = 0, u = 0, _ = 0, g = 1; g <= a; g++)
                                for (h = g * e - 1, _ = 0; _ < d; _++)
                                    for (u = 0; u < t * i; u++)
                                        n(h - _, u, c, l);
                            var f = 1 * o.bottomBorderWidth,
                                    p = r(o.bottomBorderColor);
                            u = 0;
                            for (var v = 1; v <= i; v++)
                                for (u = v * t - 1, _ = 0; _ < f; _++)
                                    for (h = 0; h < e * a; h++)
                                        n(h, u - _, p, l);
                            s.putImageData(l, 0, 0)
                        }(e, t, s, a, l, i), o.toDataURL()
                    }

                    function c(t) {
                        return "gantt-static-bg-" + t
                    }

                    function h(t, e, i) {
                        var r = {},
                                h = n(i),
                                u = e.row_height,
                                _ = "";
                        for (var g in h) {
                            var f = 1 * g,
                                    p = s(f, u, t);
                            if (!a(p)) {
                                var v = d(f, u, t);
                                l(p, v), _ += "." + c(p) + "{ background-image: url('" + v + "');}"
                            }
                            r[g] = c(p)
                        }
                        if (_) {
                            o().innerHTML += _
                        }
                        return r
                    }

                    function u(t, i, n, r) {
                        var a, s, o = [],
                                l = 0,
                                d = n.width.filter(function (t) {
                                    return !!t
                                }),
                                c = 0,
                                h = 1e5;
                        if (e.isIE) {
                            var u = navigator.appVersion || "";
                            -1 == u.indexOf("Windows NT 6.2") && -1 == u.indexOf("Windows NT 6.1") && -1 == u.indexOf("Windows NT 6.0") || (h = 2e4)
                        }
                        for (var _ = 0; _ < d.length; _++) {
                            var g = d[_];
                            if (g != s && void 0 !== s || _ == d.length - 1 || l > h) {
                                for (var f = r, p = 0, v = Math.floor(h / i.row_height) * i.row_height, m = l; f > 0; ) {
                                    var k = Math.min(f, v);
                                    f -= v, a = document.createElement("div"), a.style.height = k + "px", a.style.position = "absolute", a.style.top = p + "px", a.style.left = c + "px", a.style.whiteSpace = "no-wrap", a.className = t[s || g], _ == d.length - 1 && (m = g + m - 1), a.style.width = m + "px", o.push(a), p += k
                                }
                                l = 0, c += m
                            }
                            g && (l += g, s = g)
                        }
                        return o
                    }

                    function _(t) {
                        var e = document.createElement("div");
                        e.className = "gantt_task_cell";
                        var i = document.createElement("div");
                        i.className = "gantt_task_row", i.appendChild(e), t.appendChild(i);
                        var n = getComputedStyle(i),
                                r = getComputedStyle(e),
                                a = {
                                    bottomBorderWidth: n.getPropertyValue("border-bottom-width").replace("px", ""),
                                    rightBorderWidth: r.getPropertyValue("border-right-width").replace("px", ""),
                                    bottomBorderColor: n.getPropertyValue("border-bottom-color"),
                                    rightBorderColor: r.getPropertyValue("border-right-color")
                                };
                        return t.removeChild(i), a
                    }
                    var g = {},
                            f = "gantt-static-bg-styles-" + t.uid();
                    return {
                        render: i
                    }
                };
        t.exports = {
            create: function () {
                return a(n, r)
            }
        }
    }, function (t, e, i) {
        function n(t, e) {
            var i = function () {
                for (var i = e.getGridColumns(), n = e.$getConfig(), r = 0, a = e.$config.width, s = n.scale_height, o = 0; o < i.length; o++) {
                    var l, d = (i.length, i[o]);
                    if (r += d.width, l = n.rtl ? a - r : r, d.resize) {
                        var c = document.createElement("div");
                        c.className = "gantt_grid_column_resize_wrap", c.style.top = "0px", c.style.height = s + "px", c.innerHTML = "<div class='gantt_grid_column_resize'></div>", c.setAttribute(n.grid_resizer_column_attribute, o), t._waiAria.gridSeparatorAttr(c), e.$grid_scale.appendChild(c), c.style.left = Math.max(0, l) + "px"
                    }
                }
            },
                    n = {
                        column_before_start: t.bind(function (t, i, n) {
                            var a = e.$getConfig();
                            if (!r.locateAttribute(n, a.grid_resizer_column_attribute))
                                return !1;
                            var s = this.locate(n, a.grid_resizer_column_attribute),
                                    o = e.getGridColumns()[s];
                            return !1 !== e.callEvent("onColumnResizeStart", [s, o]) && void 0
                        }, t),
                        column_after_start: t.bind(function (t, i, n) {
                            var r = e.$getConfig(),
                                    a = this.locate(n, r.grid_resizer_column_attribute);
                            t.config.marker.innerHTML = "", t.config.marker.className += " gantt_grid_resize_area", t.config.marker.style.height = e.$grid.offsetHeight + "px", t.config.marker.style.top = "0px", t.config.drag_index = a
                        }, t),
                        column_drag_move: t.bind(function (t, i, n) {
                            var a = e.$getConfig(),
                                    s = t.config,
                                    o = e.getGridColumns(),
                                    l = parseInt(s.drag_index, 10),
                                    d = o[l],
                                    c = r.getNodePosition(e.$grid_scale),
                                    h = parseInt(s.marker.style.left, 10),
                                    u = d.min_width ? d.min_width : a.min_grid_column_width,
                                    _ = e.$grid_data.offsetWidth,
                                    g = 0,
                                    f = 0;
                            a.rtl ? h = c.x + c.width - 1 - h : h -= c.x - 1;
                            for (var p = 0; p < l; p++)
                                u += o[p].width, g += o[p].width;
                            if (h < u && (h = u), a.keep_grid_width) {
                                for (var v = 0, p = l + 1; p < o.length; p++)
                                    o[p].min_width ? _ -= o[p].min_width : a.min_grid_column_width && (_ -= a.min_grid_column_width), o[p].max_width && !1 !== v ? v += o[p].max_width : v = !1;
                                v && (u = e.$grid_data.offsetWidth - v), h < u && (h = u), h > _ && (h = _)
                            }
                            return s.left = h - 1, f = Math.abs(h - g), d.max_width && f > d.max_width && (f = d.max_width), a.rtl && (g = c.width - g + 2 - f), s.marker.style.top = c.y + "px", s.marker.style.left = c.x - 1 + g + "px", s.marker.style.width = f + "px", e.callEvent("onColumnResize", [l, o[l], f - 1]), !0
                        }, t),
                        column_drag_end: t.bind(function (i, n, r) {
                            for (var a = e.$getConfig(), s = e.getGridColumns(), o = 0, l = parseInt(i.config.drag_index, 10), d = s[l], c = 0; c < l; c++)
                                o += s[c].width;
                            var h = d.min_width && i.config.left - o < d.min_width ? d.min_width : i.config.left - o;
                            if (d.max_width && d.max_width < h && (h = d.max_width), !1 !== e.callEvent("onColumnResizeEnd", [l, d, h]) && d.width != h) {
                                if (d.width = h, a.keep_grid_width)
                                    o = a.grid_width;
                                else
                                    for (var c = l, u = s.length; c < u; c++)
                                        o += s[c].width;
                                e.callEvent("onColumnResizeComplete", [s, e._setColumnsWidth(o, l)]), e.$config.scrollable || t.$layout._syncCellSizes(e.$config.group, a.grid_width), this.render()
                            }
                        }, t)
                    };
            return {
                init: function () {
                    var i = t.$services.getService("dnd"),
                            r = e.$getConfig(),
                            a = new i(e.$grid_scale, {
                                updates_per_second: 60
                            });
                    t.defined(r.dnd_sensitivity) && (a.config.sensitivity = r.dnd_sensitivity), a.attachEvent("onBeforeDragStart", function (t, e) {
                        return n.column_before_start(a, t, e)
                    }), a.attachEvent("onAfterDragStart", function (t, e) {
                        return n.column_after_start(a, t, e)
                    }), a.attachEvent("onDragMove", function (t, e) {
                        return n.column_drag_move(a, t, e)
                    }), a.attachEvent("onDragEnd", function (t, e) {
                        return n.column_drag_end(a, t, e)
                    })
                },
                doOnRender: i
            }
        }
        var r = i(1);
        t.exports = n
    }, function (t, e, i) {
        var n = i(1),
                r = i(0),
                a = i(15),
                s = i(8),
                o = i(3),
                l = function (t) {
                    function e(e, i, n, r) {
                        return t.apply(this, arguments) || this
                    }
                    return o(e, t), r.mixin(e.prototype, {
                        init: function () {
                            void 0 === this.$config.bind && (this.$config.bind = this.$getConfig().resource_store), t.prototype.init.apply(this, arguments)
                        },
                        _initEvents: function () {
                            t.prototype._initEvents.apply(this, arguments), this._mouseDelegates.delegate("click", "gantt_row", gantt.bind(function (t, e, i) {
                                var r = this.$config.rowStore;
                                if (!r)
                                    return !0;
                                var a = n.locateAttribute(t, this.$config.item_attribute);
                                return a && r.select(a.getAttribute(this.$config.item_attribute)), !1
                            }, this), this.$grid)
                        }
                    }, !0), r.mixin(e.prototype, a(t), !0), e
                }(s);
        t.exports = l
    }, function (t, e, i) {
        var n = i(0),
                r = i(13),
                a = i(15),
                s = i(3),
                o = function (t) {
                    function e(e, i, n, r) {
                        var a = t.apply(this, arguments) || this;
                        return a.$config.bindLinks = null, a
                    }
                    return s(e, t), n.mixin(e.prototype, {
                        init: function () {
                            void 0 === this.$config.bind && (this.$config.bind = this.$getConfig().resource_store), t.prototype.init.apply(this, arguments)
                        },
                        _createLayerConfig: function () {
                            var t = this,
                                    e = function () {
                                        return t.isVisible()
                                    };
                            return {
                                tasks: [{
                                        renderer: this.$gantt.$ui.layers.resourceRow,
                                        container: this.$task_bars,
                                        filter: [e]
                                    }, {
                                        renderer: this.$gantt.$ui.layers.taskBg,
                                        container: this.$task_bg,
                                        filter: [e]
                                    }],
                                links: []
                            }
                        }
                    }, !0), n.mixin(e.prototype, a(t), !0), e
                }(r);
        t.exports = o
    }, function (t, e) {
        function i(t) {
            function e(e, n) {
                var r = n.$getConfig(),
                        a = r.type_renderers,
                        s = a[t.getTaskType(e.type)],
                        o = i;
                return s ? s.call(t, e, function (e) {
                    return o.call(t, e, n)
                }, n) : o.call(t, e, n)
            }

            function i(e, i) {
                if (!t._isAllowedUnscheduledTask(e)) {
                    var n = i.getItemPosition(e),
                            s = i.$getConfig(),
                            d = i.$getTemplates(),
                            u = i.getItemHeight(),
                            _ = t.getTaskType(e.type),
                            g = Math.floor((t.config.row_height - u) / 2);
                    _ == s.types.milestone && s.link_line_width > 1 && (g += 1), _ == s.types.milestone && (n.left -= Math.round(u / 2), n.width = u);
                    var f = document.createElement("div"),
                            p = Math.round(n.width);
                    i.$config.item_attribute && f.setAttribute(i.$config.item_attribute, e.id), s.show_progress && _ != s.types.milestone && l(e, f, p, s, d);
                    var v = o(e, p, d);
                    e.textColor && (v.style.color = e.textColor), f.appendChild(v);
                    var m = c("gantt_task_line", d.task_class(e.start_date, e.end_date, e), e.id, i);
                    (e.color || e.progressColor || e.textColor) && (m += " gantt_task_inline_color"), f.className = m;
                    var k = ["left:" + n.left + "px", "top:" + (g + n.top) + "px", "height:" + u + "px", "line-height:" + Math.max(u < 30 ? u - 2 : u, 0) + "px", "width:" + p + "px"];
                    e.color && k.push("background-color:" + e.color), e.textColor && k.push("color:" + e.textColor), f.style.cssText = k.join(";");
                    var y = r(e, s, d);
                    y && f.appendChild(y), y = a(e, s, d), y && f.appendChild(y), t._waiAria.setTaskBarAttr(e, f);
                    var b = t.getState();
                    return t.isReadonly(e) || (s.drag_resize && !t.isSummaryTask(e) && _ != s.types.milestone && h(f, "gantt_task_drag", e, function (t) {
                        var e = document.createElement("div");
                        return e.className = t, e
                    }, s), s.drag_links && s.show_links && h(f, "gantt_link_control", e, function (t) {
                        var e = document.createElement("div");
                        e.className = t, e.style.cssText = ["height:" + u + "px", "line-height:" + u + "px"].join(";");
                        var i = document.createElement("div");
                        i.className = "gantt_link_point fa fa-plus";
                        var n = !1;
                        return b.link_source_id && s.touch && (n = !0), i.style.display = n ? "block" : "", e.appendChild(i), e
                    }, s)), f
                }
            }

            function n(t, e, i) {
                if (!e)
                    return null;
                var n = e(t.start_date, t.end_date, t);
                if (!n)
                    return null;
                var r = document.createElement("div");
                return r.className = "gantt_side_content " + i, r.innerHTML = n, r
            }

            function r(t, e, i) {
                var r = "gantt_left " + s(!e.rtl, t);
                return n(t, i.leftside_text, r)
            }

            function a(t, e, i) {
                var r = "gantt_right " + s(!!e.rtl, t);
                return n(t, i.rightside_text, r)
            }

            function s(e, i) {
                var n = d(e);
                for (var r in n)
                    for (var a = i[r], s = 0; s < a.length; s++)
                        for (var o = t.getLink(a[s]), l = 0; l < n[r].length; l++)
                            if (o.type == n[r][l])
                                return "gantt_link_crossing";
                return ""
            }

            function o(e, i, n) {
                var r = document.createElement("div");
                return t.getTaskType(e.type) != t.config.types.milestone && (r.innerHTML = n.task_text(e.start_date, e.end_date, e)), r.className = "gantt_task_content", r
            }

            function l(e, i, n, r, a) {
                var s = 1 * e.progress || 0;
                n = Math.max(n - 2, 0);
                var o = document.createElement("div"),
                        l = Math.round(n * s);
                l = Math.min(n, l), e.progressColor && (o.style.backgroundColor = e.progressColor, o.style.opacity = 1), o.style.width = l + "px", o.className = "gantt_task_progress", o.innerHTML = a.progress_text(e.start_date, e.end_date, e), r.rtl && (o.style.position = "absolute", o.style.right = "0px");
                var d = document.createElement("div");
                if (d.className = "gantt_task_progress_wrapper", d.appendChild(o), i.appendChild(d), t.config.drag_progress && !t.isReadonly(e)) {
                    var c = document.createElement("div"),
                            h = l;
                    r.rtl && (h = n - l), c.style.left = h + "px", c.className = "gantt_task_progress_drag", o.appendChild(c), i.appendChild(c)
                }
            }

            function d(e) {
                return e ? {
                    $source: [t.config.links.start_to_start],
                    $target: [t.config.links.start_to_start, t.config.links.finish_to_start]
                } : {
                    $source: [t.config.links.finish_to_start, t.config.links.finish_to_finish],
                    $target: [t.config.links.finish_to_finish]
                }
            }

            function c(e, i, n, r) {
                var a = r.$getConfig(),
                        s = [e];
                i && s.push(i);
                var o = t.getState(),
                        l = t.getTask(n);
                if (t.getTaskType(l.type) == a.types.milestone && s.push("gantt_milestone"), t.getTaskType(l.type) == a.types.project && s.push("gantt_project"), t.isSummaryTask(l) && s.push("gantt_dependent_task"), a.select_task && n == o.selected_task && s.push("gantt_selected"), n == o.drag_id && (s.push("gantt_drag_" + o.drag_mode), o.touch_drag && s.push("gantt_touch_" + o.drag_mode)), o.link_source_id == n && s.push("gantt_link_source"), o.link_target_id == n && s.push("gantt_link_target"), a.highlight_critical_path && t.isCriticalTask && t.isCriticalTask(l) && s.push("gantt_critical_task"), o.link_landing_area && o.link_target_id && o.link_source_id && o.link_target_id != o.link_source_id) {
                    var d = o.link_source_id,
                            c = o.link_from_start,
                            h = o.link_to_start,
                            u = t.isLinkAllowed(d, n, c, h),
                            _ = "";
                    _ = u ? h ? "link_start_allow" : "link_finish_allow" : h ? "link_start_deny" : "link_finish_deny", s.push(_)
                }
                return s.join(" ")
            }

            function h(e, i, n, r, a) {
                var s, o, l = t.getState();
                +n.start_date >= +l.min_date && (s = [i, a.rtl ? "task_right" : "task_left", "task_start_date"], o = r(s.join(" ")), o.setAttribute("data-bind-property", "start_date"), e.appendChild(o)), +n.end_date <= +l.max_date && (s = [i, a.rtl ? "task_left" : "task_right", "task_end_date"], o = r(s.join(" ")), o.setAttribute("data-bind-property", "end_date"), e.appendChild(o))
            }
            return e
        }
        t.exports = i
    }, function (t, e) {
        function i(t) {
            function e(e, i) {
                var n = i.$getConfig(),
                        r = i.$getTemplates(),
                        a = i.getScale(),
                        s = a.count,
                        o = document.createElement("div");
                if (n.show_task_cells)
                    for (var l = 0; l < s; l++) {
                        var d = a.width[l],
                                c = "";
                        if (d > 0) {
                            var h = document.createElement("div");
                            h.style.width = d + "px", c = "gantt_task_cell" + (l == s - 1 ? " gantt_last_cell" : ""), _ = r.task_cell_class(e, a.trace_x[l]), _ && (c += " " + _), h.className = c, o.appendChild(h)
                        }
                    }
                var u = t.getGlobalTaskIndex(e.id) % 2 != 0,
                        _ = r.task_row_class(e.start_date, e.end_date, e),
                        g = "gantt_task_row" + (u ? " odd" : "") + (_ ? " " + _ : "");
                return i.$config.rowStore.getSelectedId() == e.id && (g += " gantt_selected"), o.className = g, n.smart_rendering && (o.style.position = "absolute", o.style.top = i.getItemTop(e.id) + "px", o.style.width = "100%"), o.style.height = n.row_height + "px", i.$config.item_attribute && o.setAttribute(i.$config.item_attribute, e.id), o
            }
            return e
        }
        t.exports = i
    }, function (t, e) {
        function i(t) {
            function e(e, n) {
                var s = n.$getConfig(),
                        o = a.get_points(e, n),
                        l = r.get_lines(o, n),
                        d = document.createElement("div"),
                        c = "gantt_task_link";
                e.color && (c += " gantt_link_inline_color");
                var h = t.templates.link_class ? t.templates.link_class(e) : "";
                h && (c += " " + h), s.highlight_critical_path && t.isCriticalLink && t.isCriticalLink(e) && (c += " gantt_critical_link"), d.className = c, n.$config.link_attribute && d.setAttribute(n.$config.link_attribute, e.id);
                for (var u = 0; u < l.length; u++) {
                    u == l.length - 1 && (l[u].size -= s.link_arrow_size);
                    var _ = r.render_line(l[u], l[u + 1], n);
                    e.color && (_.firstChild.style.backgroundColor = e.color), d.appendChild(_)
                }
                var g = l[l.length - 1].direction,
                        f = i(o[o.length - 1], g, n);
                return e.color && (f.style.borderColor = e.color), d.appendChild(f), t._waiAria.linkAttr(e, d), d
            }

            function i(t, e, i) {
                var n = i.$getConfig(),
                        a = document.createElement("div"),
                        s = t.y,
                        o = t.x,
                        l = n.link_arrow_size,
                        d = n.row_height,
                        c = "gantt_link_arrow gantt_link_arrow_" + e;
                switch (e) {
                    case r.dirs.right:
                        s -= (l - d) / 2, o -= l;
                        break;
                    case r.dirs.left:
                        s -= (l - d) / 2;
                        break;
                    case r.dirs.up:
                        o -= l;
                        break;
                    case r.dirs.down:
                        s += 2 * l, o -= l
                }
                return a.style.cssText = ["top:" + s + "px", "left:" + o + "px"].join(";"), a.className = c, a
            }

            function n(e, i) {
                var n = i.$getConfig(),
                        r = i.getItemPosition(e);
                if (t.getTaskType(e.type) == n.types.milestone) {
                    var a = t.getTaskHeight(),
                            s = Math.sqrt(2 * a * a);
                    r.left -= s / 2, r.width = s
                }
                return r
            }
            var r = {
                current_pos: null,
                dirs: {
                    left: "left",
                    right: "right",
                    up: "up",
                    down: "down"
                },
                path: [],
                clear: function () {
                    this.current_pos = null, this.path = []
                },
                point: function (e) {
                    this.current_pos = t.copy(e)
                },
                get_lines: function (t) {
                    this.clear(), this.point(t[0]);
                    for (var e = 1; e < t.length; e++)
                        this.line_to(t[e]);
                    return this.get_path()
                },
                line_to: function (e) {
                    var i = t.copy(e),
                            n = this.current_pos,
                            r = this._get_line(n, i);
                    this.path.push(r), this.current_pos = i
                },
                get_path: function () {
                    return this.path
                },
                get_wrapper_sizes: function (t, e) {
                    var i, n = e.$getConfig(),
                            r = n.link_wrapper_width,
                            a = (n.link_line_width, t.y + (n.row_height - r) / 2);
                    switch (t.direction) {
                        case this.dirs.left:
                            i = {
                                top: a,
                                height: r,
                                lineHeight: r,
                                left: t.x - t.size - r / 2,
                                width: t.size + r
                            };
                            break;
                        case this.dirs.right:
                            i = {
                                top: a,
                                lineHeight: r,
                                height: r,
                                left: t.x - r / 2,
                                width: t.size + r
                            };
                            break;
                        case this.dirs.up:
                            i = {
                                top: a - t.size,
                                lineHeight: t.size + r,
                                height: t.size + r,
                                left: t.x - r / 2,
                                width: r
                            };
                            break;
                        case this.dirs.down:
                            i = {
                                top: a,
                                lineHeight: t.size + r,
                                height: t.size + r,
                                left: t.x - r / 2,
                                width: r
                            }
                    }
                    return i
                },
                get_line_sizes: function (t, e) {
                    var i, n = e.$getConfig(),
                            r = n.link_line_width,
                            a = n.link_wrapper_width,
                            s = t.size + r;
                    switch (t.direction) {
                        case this.dirs.left:
                        case this.dirs.right:
                            i = {
                                height: r,
                                width: s,
                                marginTop: (a - r) / 2,
                                marginLeft: (a - r) / 2
                            };
                            break;
                        case this.dirs.up:
                        case this.dirs.down:
                            i = {
                                height: s,
                                width: r,
                                marginTop: (a - r) / 2,
                                marginLeft: (a - r) / 2
                            }
                    }
                    return i
                },
                render_line: function (t, e, i) {
                    var n = this.get_wrapper_sizes(t, i),
                            r = document.createElement("div");
                    r.style.cssText = ["top:" + n.top + "px", "left:" + n.left + "px", "height:" + n.height + "px", "width:" + n.width + "px"].join(";"), r.className = "gantt_line_wrapper";
                    var a = this.get_line_sizes(t, i),
                            s = document.createElement("div");
                    return s.style.cssText = ["height:" + a.height + "px", "width:" + a.width + "px", "margin-top:" + a.marginTop + "px", "margin-left:" + a.marginLeft + "px"].join(";"), s.className = "gantt_link_line_" + t.direction, r.appendChild(s), r
                },
                _get_line: function (t, e) {
                    var i = this.get_direction(t, e),
                            n = {
                                x: t.x,
                                y: t.y,
                                direction: this.get_direction(t, e)
                            };
                    return i == this.dirs.left || i == this.dirs.right ? n.size = Math.abs(t.x - e.x) : n.size = Math.abs(t.y - e.y), n
                },
                get_direction: function (t, e) {
                    return e.x < t.x ? this.dirs.left : e.x > t.x ? this.dirs.right : e.y > t.y ? this.dirs.down : this.dirs.up
                }
            },
                    a = {
                        path: [],
                        clear: function () {
                            this.path = []
                        },
                        current: function () {
                            return this.path[this.path.length - 1]
                        },
                        point: function (e) {
                            return e ? (this.path.push(t.copy(e)), e) : this.current()
                        },
                        point_to: function (e, i, n) {
                            n = n ? {
                                x: n.x,
                                y: n.y
                            } : t.copy(this.point());
                            var a = r.dirs;
                            switch (e) {
                                case a.left:
                                    n.x -= i;
                                    break;
                                case a.right:
                                    n.x += i;
                                    break;
                                case a.up:
                                    n.y -= i;
                                    break;
                                case a.down:
                                    n.y += i
                            }
                            return this.point(n)
                        },
                        get_points: function (e, i) {
                            var n = this.get_endpoint(e, i),
                                    a = t.config,
                                    s = n.e_y - n.y,
                                    o = n.e_x - n.x,
                                    l = r.dirs;
                            this.clear(), this.point({
                                x: n.x,
                                y: n.y
                            });
                            var d = 2 * a.link_arrow_size,
                                    c = this.get_line_type(e, i.$getConfig()),
                                    h = n.e_x > n.x;
                            if (c.from_start && c.to_start)
                                this.point_to(l.left, d), h ? (this.point_to(l.down, s), this.point_to(l.right, o)) : (this.point_to(l.right, o), this.point_to(l.down, s)), this.point_to(l.right, d);
                            else if (!c.from_start && c.to_start)
                                if (h = n.e_x > n.x + 2 * d, this.point_to(l.right, d), h)
                                    o -= d, this.point_to(l.down, s), this.point_to(l.right, o);
                                else {
                                    o -= 2 * d;
                                    var u = s > 0 ? 1 : -1;
                                    this.point_to(l.down, u * (a.row_height / 2)), this.point_to(l.right, o), this.point_to(l.down, u * (Math.abs(s) - a.row_height / 2)), this.point_to(l.right, d)
                                }
                            else if (c.from_start || c.to_start) {
                                if (c.from_start && !c.to_start)
                                    if (h = n.e_x > n.x - 2 * d, this.point_to(l.left, d), h) {
                                        o += 2 * d;
                                        var u = s > 0 ? 1 : -1;
                                        this.point_to(l.down, u * (a.row_height / 2)), this.point_to(l.right, o), this.point_to(l.down, u * (Math.abs(s) - a.row_height / 2)), this.point_to(l.left, d)
                                    } else
                                        o += d, this.point_to(l.down, s), this.point_to(l.right, o)
                            } else
                                this.point_to(l.right, d), h ? (this.point_to(l.right, o), this.point_to(l.down, s)) : (this.point_to(l.down, s), this.point_to(l.right, o)), this.point_to(l.left, d);
                            return this.path
                        },
                        get_line_type: function (e, i) {
                            var n = i.links,
                                    r = !1,
                                    a = !1;
                            return e.type == n.start_to_start ? r = a = !0 : e.type == n.finish_to_finish ? r = a = !1 : e.type == n.finish_to_start ? (r = !1, a = !0) : e.type == n.start_to_finish ? (r = !0, a = !1) : null, i.rtl && (r = !r, a = !a), {
                                from_start: r,
                                to_start: a
                            }
                        },
                        get_endpoint: function (e, i) {
                            var r = i.$getConfig(),
                                    a = (r.links, this.get_line_type(e, r)),
                                    s = a.from_start,
                                    o = a.to_start,
                                    l = t.getTask(e.source),
                                    d = t.getTask(e.target),
                                    c = n(l, i),
                                    h = n(d, i);
                            return {
                                x: s ? c.left : c.left + c.width,
                                e_x: o ? h.left : h.left + h.width,
                                y: c.top,
                                e_y: h.top
                            }
                        }
                    };
            return e
        }
        t.exports = i
    }, function (t, e, i) {
        function n(t) {
            function e(e, i) {
                var n = i.getGridColumns(),
                        a = i.$getConfig(),
                        s = i.$getTemplates(),
                        o = i.$config.rowStore;
                a.rtl && (n = n.reverse());
                for (var l, d = [], c = 0; c < n.length; c++) {
                    var h, u, _, g = c == n.length - 1,
                            f = n[c];
                    if ("add" == f.name) {
                        var p = t._waiAria.gridAddButtonAttrString(f);
                        u = "<div " + p + " class='gantt_add'></div>", _ = ""
                    } else
                        u = f.template ? f.template(e) : e[f.name], r.isDate(u) && (u = s.date_grid(u, e)), _ = u, u = "<div class='gantt_tree_content'>" + u + "</div>";
                    var v = "gantt_cell" + (g ? " gantt_last_cell" : ""),
                            m = [];
                    if (f.tree) {
                        for (var k = 0; k < e.$level; k++)
                            m.push(s.grid_indent(e));
                        l = o.hasChild(e.id), l ? (m.push(s.grid_open(e)), m.push(s.grid_folder(e))) : (m.push(s.grid_blank(e)), m.push(s.grid_file(e)))
                    }
                    var y = "width:" + (f.width - (g ? 1 : 0)) + "px;";
                    this.defined(f.align) && (y += "text-align:" + f.align + ";");
                    var p = t._waiAria.gridCellAttrString(f, _);
                    m.push(u), a.rtl && (m = m.reverse()), h = "<div class='" + v + "' style='" + y + "' " + p + ">" + m.join("") + "</div>", d.push(h)
                }
                var v = t.getGlobalTaskIndex(e.id) % 2 == 0 ? "" : " odd";
                if (v += e.$transparent ? " gantt_transparent" : "", v += e.$dataprocessor_class ? " " + e.$dataprocessor_class : "", s.grid_row_class) {
                    var b = s.grid_row_class.call(t, e.start_date, e.end_date, e);
                    b && (v += " " + b)
                }
                o.getSelectedId() == e.id && (v += " gantt_selected");
                var $ = document.createElement("div");
                $.className = "gantt_row" + v;
                var w = i.getItemHeight();
                return $.style.height = w + "px", $.style.lineHeight = w + "px", a.smart_rendering && ($.style.position = "absolute", $.style.left = "0px", $.style.top = i.getItemTop(e.id) + "px"), i.$config.item_attribute && $.setAttribute(i.$config.item_attribute, e.id), t._waiAria.taskRowAttr(e, $), $.innerHTML = d.join(""), $
            }
            return e
        }
        var r = i(4);
        t.exports = n
    }, function (t, e, i) {
        var n = i(0),
                r = i(58),
                a = function () {
                    return function (t) {
                        return {
                            onCreated: function (e) {
                                e.$config = n.mixin(e.$config, {
                                    bind: "task"
                                }), "grid" == e.$config.id && this.extendGantt(e), this._mouseDelegates = i(9)(t)
                            },
                            onInitialized: function (e) {
                                e.$getConfig().order_branch && r.init(e.$gantt, e), this.initEvents(e, t), "grid" == e.$config.id && this.extendDom(e)
                            },
                            onDestroyed: function (e) {
                                this.clearEvents(e, t)
                            },
                            initEvents: function (t, e) {
                                this._mouseDelegates.delegate("click", "gantt_row", e.bind(function (i, n, r) {
                                    var a = t.$getConfig();
                                    if (null !== n) {
                                        var s = this.getTask(n);
                                        a.scroll_on_click && !e._is_icon_open_click(i) && this.showDate(s.start_date), e.callEvent("onTaskRowClick", [n, r])
                                    }
                                }, e), t.$grid), this._mouseDelegates.delegate("click", "gantt_grid_head_cell", e.bind(function (i, n, r) {
                                    var a = r.getAttribute("column_id");
                                    if (e.callEvent("onGridHeaderClick", [a, i])) {
                                        var s = t.$getConfig();
                                        if ("add" == a) {
                                            return void e.$services.getService("mouseEvents").callHandler("click", "gantt_add", t.$grid, [i, s.root_id])
                                        }
                                        if (s.sort) {
                                            for (var o, l = a, d = 0; d < s.columns.length; d++)
                                                if (s.columns[d].name == a) {
                                                    o = s.columns[d];
                                                    break
                                                }
                                            if (o && void 0 !== o.sort && !0 !== o.sort && !(l = o.sort))
                                                return;
                                            var c = this._sort && this._sort.direction && this._sort.name == a ? this._sort.direction : "desc";
                                            c = "desc" == c ? "asc" : "desc", this._sort = {
                                                name: a,
                                                direction: c
                                            }, this.sort(l, "desc" == c)
                                        }
                                    }
                                }, e), t.$grid), this._mouseDelegates.delegate("click", "gantt_add", e.bind(function (i, n, r) {
                                    if (!t.$getConfig().readonly) {
                                        var a = {};
                                        return this.createTask(a, n || e.config.root_id), !1
                                    }
                                }, e), t.$grid)
                            },
                            clearEvents: function (t, e) {
                                this._mouseDelegates.destructor(), this._mouseDelegates = null
                            },
                            extendDom: function (e) {
                                t.$grid = e.$grid, t.$grid_scale = e.$grid_scale, t.$grid_data = e.$grid_data
                            },
                            extendGantt: function (e) {
                                t.getGridColumns = t.bind(e.getGridColumns, e), e.attachEvent("onColumnResizeStart", function () {
                                    return t.callEvent("onColumnResizeStart", arguments)
                                }), e.attachEvent("onColumnResize", function () {
                                    return t.callEvent("onColumnResize", arguments)
                                }), e.attachEvent("onColumnResizeEnd", function () {
                                    return t.callEvent("onColumnResizeEnd", arguments)
                                }), e.attachEvent("onColumnResizeComplete", function (e, i) {
                                    t.config.grid_width = i
                                })
                            }
                        }
                    }
                }();
        t.exports = a
    }, function (t, e, i) {
        function n(t, e) {
            function i(t) {
                return r.locateAttribute(t, e.$config.item_attribute)
            }

            function n() {
                return t.getDatastore(e.$config.bind)
            }
            var a = t.$services.getService("dnd");
            if (e.$config.bind && t.getDatastore(e.$config.bind)) {
                var s = new a(e.$grid_data, {
                    updates_per_second: 60
                });
                t.defined(e.$getConfig().dnd_sensitivity) && (s.config.sensitivity = e.$getConfig().dnd_sensitivity), s.attachEvent("onBeforeDragStart", t.bind(function (r, a) {
                    var o = i(a);
                    if (!o)
                        return !1;
                    t.hideQuickInfo && t._hideQuickInfo();
                    var l = o.getAttribute(e.$config.item_attribute),
                            d = n(),
                            c = d.getItem(l);
                    return !t.isReadonly(c) && (s.config.initial_open_state = c.$open, !!t.callEvent("onRowDragStart", [l, a.target || a.srcElement, a]) && void 0)
                }, t)), s.attachEvent("onAfterDragStart", t.bind(function (t, r) {
                    var a = i(r);
                    s.config.marker.innerHTML = a.outerHTML;
                    var o = s.config.marker.firstChild;
                    o && (o.style.position = "static"), s.config.id = a.getAttribute(e.$config.item_attribute);
                    s.config.marker.children[0].className += " gantt_transparent2"
                    
                    var l = n(),
                            d = l.getItem(s.config.id);
                            
                    
                    s.config.index = l.getBranchIndex(s.config.id), s.config.parent = d.parent, d.$open = !1, d.$transparent = !0, this.refreshData()
                }, t)), s.lastTaskOfLevel = function (t) {
                    for (var e = null, i = n(), r = i.getItems(), a = 0, s = r.length; a < s; a++)
                        r[a].$level == t && (e = r[a]);
                    return e ? e.id : null
                }, s._getGridPos = t.bind(function (t) {
                    var i = r.getNodePosition(e.$grid_data),
                            a = n(),
                            s = i.x,
                            o = t.pos.y - 10,
                            l = e.$getConfig();
                    o < i.y && (o = i.y);
                    var d = a.countVisible() * l.row_height;
                    return o > i.y + d - l.row_height && (o = i.y + d - l.row_height), i.x = s, i.y = o, i
                }, t), s._getTargetY = t.bind(function (t) {
                    var i = r.getNodePosition(e.$grid_data),
                            n = t.pageY - i.y + (e.$state.scrollTop || 0);
                    return n < 0 && (n = 0), n
                }, t), s._getTaskByY = t.bind(function (t, i) {
                    var r = e.$getConfig(),
                            a = n();
                    t = t || 0;
                    var s = Math.floor(t / r.row_height);
                    return s = i < s ? s - 1 : s, s > a.countVisible() - 1 ? null : a.getIdByIndex(s)
                }, t), s.attachEvent("onDragMove", t.bind(function (t, i) {
                    function r(t, e) {
                        return !d.isChildOf(_.id, e.id) && (t.$level == e.$level || l.order_branch_free)
                    }
                    var a = s.config,
                            o = s._getGridPos(i),
                            l = e.$getConfig(),
                            d = n();
                    a.marker.style.left = o.x + 10 + "px", a.marker.style.top = o.y + "px";
                    var c = d.getItem(s.config.id),
                            h = s._getTargetY(i),
                            u = s._getTaskByY(h, d.getIndexById(c.id));
                    var parent = d.getItem(u);
                    
                    if ( (d.exists(u) || (u = s.lastTaskOfLevel(l.order_branch_free ? c.$level : 0)) == s.config.id && (u = null), d.exists(u))) {
                        var _ = d.getItem(u);
                        if ( (d.getIndexById(_.id) * l.row_height + l.row_height / 2 < h)) {
                            var g = d.getIndexById(_.id),
                                    f = d.getNext(_.id),
                                    p = d.getItem(f);
                            if (p) {
                                if (p.id == c.id)
                                    return l.order_branch_free && d.isChildOf(c.id, _.id) && 1 == d.getChildren(_.id).length ? void d.move(c.id, d.getBranchIndex(_.id) + 1, d.getParent(_.id)) : void 0;
                                _ = p
                            } else if (f = d.getIdByIndex(g), p = d.getItem(f), r(p, c) && p.id != c.id)
                                return void d.move(c.id, -1, d.getParent(p.id))
                        } else if (h > 15 && parent.priority == '2' && (l.order_branch_free && _.id != c.id && r(_, c))) {
                            
                            if (!d.hasChild(_.id))
                                return _.$open = !0, void d.move(c.id, -1, _.id);
                            if (d.getIndexById(_.id) || l.row_height / 3 < h)
                                return
                        }
                        for (var g = d.getIndexById(_.id), v = d.getIdByIndex(g - 1), m = d.getItem(v), k = 1;
                                (!m || m.id == _.id) && g - k >= 0; )
                            v = d.getIdByIndex(g - k), m = d.getItem(v), k++;
                        if (c.id == _.id)
                            return;
                        r(_, c) && c.id != _.id ? d.move(c.id, 0, 0, _.id) : _.$level != c.$level - 1 || d.getChildren(_.id).length ? m && r(m, c) && c.id != m.id && d.move(c.id, -1, d.getParent(m.id)) : d.move(c.id, 0, _.id)
                    }
                    return !0
                }, t)), s.attachEvent("onDragEnd", t.bind(function () {
                    var t = n(),
                            e = t.getItem(s.config.id);
                            var parent = t.getItem(e.parent);     
                    if(parent.priority == '2' || parent.priority == '1'){
                        onDragEndItem(e);
                        e.$transparent = !1, e.$open = s.config.initial_open_state, !1 === this.callEvent("onBeforeRowDragEnd", [s.config.id, s.config.parent, s.config.index]) ? (t.move(s.config.id, s.config.index, s.config.parent), e.$drop_target = null) : this.callEvent("onRowDragEnd", [s.config.id, e.$drop_target]), t.refresh(e.id)
                    }
                }, t))
            }
        }
        var r = i(1);
        t.exports = {
            init: n
        }
    }, function (t, e, i) {
        var n = i(0),
                r = i(60),
                a = i(61),
                s = i(1),
                o = function () {
                    return function (t) {
                        var e = t.$services;
                        return {
                            onCreated: function (e) {
                                var s = e.$config;
                                s.bind = n.defined(s.bind) ? s.bind : "task", s.bindLinks = n.defined(s.bindLinks) ? s.bindLinks : "link", e._linksDnD = a.createLinkDND(), e._tasksDnD = r.createTaskDND(), e._tasksDnD.extend(e), this._mouseDelegates = i(9)(t)
                            },
                            onInitialized: function (e) {
                                this._attachDomEvents(t), this._attachStateProvider(t, e), e._tasksDnD.init(e, t), e._linksDnD.init(e, t), "timeline" == e.$config.id && this.extendDom(e)
                            },
                            onDestroyed: function (e) {
                                this._clearDomEvents(t), this._clearStateProvider(t)
                            },
                            extendDom: function (e) {
                                t.$task = e.$task, t.$task_scale = e.$task_scale, t.$task_data = e.$task_data, t.$task_bg = e.$task_bg, t.$task_links = e.$task_links, t.$task_bars = e.$task_bars
                            },
                            _clearDomEvents: function () {
                                this._mouseDelegates.destructor(), this._mouseDelegates = null
                            },
                            _attachDomEvents: function (t) {
                                function e(e, i) {
                                    if (e && this.callEvent("onLinkDblClick", [e, i])) {
                                        var n = this.getLink(e);
                                        if (this.isReadonly(n))
                                            return;
                                        var r = this.locale.labels.link + " " + this.templates.link_description(this.getLink(e)) + " " + this.locale.labels.confirm_link_deleting;
                                        window.setTimeout(function () {
//                                            t._dhtmlx_confirm(r, "", function () {
//                                                t.deleteLink(e)
//                                            })
                                            onBeforeDeleteLink(n,r);
                                        }, this.config.touch ? 300 : 1)
                                    }
                                }
                                this._mouseDelegates.delegate("click", "gantt_task_link", t.bind(function (t, e) {
                                    var i = this.locate(t, this.config.link_attribute);
                                    i && this.callEvent("onLinkClick", [i, t])
                                }, t), this.$task), this._mouseDelegates.delegate("click", "gantt_scale_cell", t.bind(function (e, i) {
                                    var n = s.getRelativeEventPosition(e, t.$task_data),
                                            r = t.dateFromPos(n.x),
                                            a = Math.floor(t.columnIndexByDate(r)),
                                            o = t.getScale().trace_x[a];
                                    t.callEvent("onScaleClick", [e, o])
                                }, t), this.$task), this._mouseDelegates.delegate("doubleclick", "gantt_task_link", t.bind(function (i, n, r) {
                                    var n = this.locate(i, t.config.link_attribute);
                                    e.call(this, n, i)
                                }, t), this.$task), this._mouseDelegates.delegate("doubleclick", "gantt_link_point", t.bind(function (t, i, n) {
                                    var i = this.locate(t),
                                            r = this.getTask(i),
                                            a = null;
                                    return n.parentNode && s.getClassName(n.parentNode) && (a = s.getClassName(n.parentNode).indexOf("_left") > -1 ? r.$target[0] : r.$source[0]), a && e.call(this, a, t), !1
                                }, t), this.$task)
                            },
                            _attachStateProvider: function (t, i) {
                                var n = i;
                                e.getService("state").registerProvider("tasksTimeline", function () {
                                    return {
                                        scale_unit: n._tasks ? n._tasks.unit : void 0,
                                        scale_step: n._tasks ? n._tasks.step : void 0
                                    }
                                })
                            },
                            _clearStateProvider: function () {
                                e.getService("state").unregisterProvider("tasksTimeline")
                            }
                        }
                    }
                }();
        t.exports = o
    }, function (t, e, i) {
        function n(t, e) {
            var i = e.$services;
            return {
                drag: null,
                dragMultiple: {},
                _events: {
                    before_start: {},
                    before_finish: {},
                    after_finish: {}
                },
                _handlers: {},
                init: function () {
                    this._domEvents = e._createDomEventScope(), this.clear_drag_state();
                    var t = e.config.drag_mode;
                    this.set_actions(), i.getService("state").registerProvider("tasksDnd", s.bind(function () {
                        return {
                            drag_id: this.drag ? this.drag.id : void 0,
                            drag_mode: this.drag ? this.drag.mode : void 0,
                            drag_from_start: this.drag ? this.drag.left : void 0
                        }
                    }, this));
                    var n = {
                        before_start: "onBeforeTaskDrag",
                        before_finish: "onBeforeTaskChanged",
                        after_finish: "onAfterTaskDrag"
                    };
                    for (var r in this._events)
                        for (var a in t)
                            this._events[r][a] = n[r];
                    this._handlers[t.move] = this._move, this._handlers[t.resize] = this._resize, this._handlers[t.progress] = this._resize_progress
                },
                set_actions: function () {
                    var i = t.$task_data;
                    this._domEvents.attach(i, "mousemove", e.bind(function (t) {
                        this.on_mouse_move(t || event)
                    }, this)), this._domEvents.attach(i, "mousedown", e.bind(function (t) {
                        this.on_mouse_down(t || event)
                    }, this)), this._domEvents.attach(i, "mouseup", e.bind(function (t) {
                        this.on_mouse_up(t || event)
                    }, this))
                },
                clear_drag_state: function () {
                    this.drag = {
                        id: null,
                        mode: null,
                        pos: null,
                        start_x: null,
                        start_y: null,
                        obj: null,
                        left: null
                    }, this.dragMultiple = {}
                },
                _resize: function (i, n, r) {
                    var a = t.$getConfig(),
                            s = this._drag_task_coords(i, r);
                    r.left ? (i.start_date = e.dateFromPos(s.start + n), i.start_date || (i.start_date = new Date(e.getState().min_date))) : (i.end_date = e.dateFromPos(s.end + n), i.end_date || (i.end_date = new Date(e.getState().max_date))), i.end_date - i.start_date < a.min_duration && (r.left ? i.start_date = e.calculateEndDate({
                        start_date: i.end_date,
                        duration: -1,
                        task: i
                    }) : i.end_date = e.calculateEndDate({
                        start_date: i.start_date,
                        duration: 1,
                        task: i
                    })), e._init_task_timing(i)
                },
                _resize_progress: function (e, i, n) {
                    var r = this._drag_task_coords(e, n),
                            a = t.$getConfig(),
                            s = a.rtl ? r.start - n.pos.x : n.pos.x - r.start,
                            o = Math.max(0, s);
                    e.progress = Math.min(1, o / Math.abs(r.end - r.start))
                },
                _find_max_shift: function (t, i) {
                    var n;
                    for (var r in t) {
                        var a = t[r],
                                s = e.getTask(a.id),
                                o = this._drag_task_coords(s, a),
                                l = e.posFromDate(new Date(e.getState().min_date)),
                                d = e.posFromDate(new Date(e.getState().max_date));
                        if (o.end + i > d) {
                            var c = d - o.end;
                            (c < n || void 0 === n) && (n = c)
                        } else if (o.start + i < l) {
                            var h = l - o.start;
                            (h < n || void 0 === n) && (n = h)
                        }
                    }
                    return n
                },
                _move: function (t, i, n) {
                    var r = this._drag_task_coords(t, n),
                            a = e.dateFromPos(r.start + i),
                            s = e.dateFromPos(r.end + i);
                    a ? s ? (t.start_date = a, t.end_date = s) : (t.end_date = new Date(e.getState().max_date), t.start_date = e.dateFromPos(e.posFromDate(t.end_date) - (r.end - r.start))) : (t.start_date = new Date(e.getState().min_date), t.end_date = e.dateFromPos(e.posFromDate(t.start_date) + (r.end - r.start)))
                },
                _drag_task_coords: function (t, i) {
                    return {
                        start: i.obj_s_x = i.obj_s_x || e.posFromDate(t.start_date),
                        end: i.obj_e_x = i.obj_e_x || e.posFromDate(t.end_date)
                    }
                },
                _mouse_position_change: function (t, e) {
                    var i = t.x - e.x,
                            n = t.y - e.y;
                    return Math.sqrt(i * i + n * n)
                },
                _is_number: function (t) {
                    return !isNaN(parseFloat(t)) && isFinite(t)
                },
                on_mouse_move: function (t) {
                    if (this.drag.start_drag) {
                        var i = a.getRelativeEventPosition(t, e.$task_data),
                                n = this.drag.start_drag.start_x,
                                r = this.drag.start_drag.start_y;
                        (Date.now() - this.drag.timestamp > 50 || this._is_number(n) && this._is_number(r) && this._mouse_position_change({
                            x: n,
                            y: r
                        }, i) > 20) && this._start_dnd(t)
                    }
                    if (this.drag.mode) {
                        if (!o(this, 40))
                            return;
                        this._update_on_move(t)
                    }
                },
                _update_item_on_move: function (t, i, n, r, a) {
                    var s = e.getTask(i),
                            o = e.mixin({}, s),
                            l = e.mixin({}, s);
                    this._handlers[n].apply(this, [l, t, r]), e.mixin(s, l, !0), e.callEvent("onTaskDrag", [s.id, n, l, o, a]), e.mixin(s, l, !0), e.refreshTask(i)
                },
                _update_on_move: function (i) {
                    var n = this.drag,
                            r = t.$getConfig();
                    if (n.mode) {
                        var o = a.getRelativeEventPosition(i, t.$task_data);
                        if (n.pos && n.pos.x == o.x)
                            return;
                        n.pos = o;
                        var l = e.dateFromPos(o.x);
                        if (!l || isNaN(l.getTime()))
                            return;
                        var d = o.x - n.start_x,
                                c = e.getTask(n.id);
                        if (this._handlers[n.mode]) {
                            if (e.isSummaryTask(c) && e.config.drag_project && n.mode == r.drag_mode.move) {
                                var h = {};
                                h[n.id] = s.copy(n);
                                var u = this._find_max_shift(s.mixin(h, this.dragMultiple), d);
                                void 0 !== u && (d = u), this._update_item_on_move(d, n.id, n.mode, n, i);
                                for (var _ in this.dragMultiple) {
                                    var g = this.dragMultiple[_];
                                    this._update_item_on_move(d, g.id, g.mode, g, i)
                                }
                            } else
                                this._update_item_on_move(d, n.id, n.mode, n, i);
                            e._update_parents(n.id)
                        }
                    }
                },
                on_mouse_down: function (i, n) {
                    if (2 != i.button || void 0 === i.button) {
                        var r = t.$getConfig(),
                                s = e.locate(i),
                                o = null;
                        if (e.isTaskExists(s) && (o = e.getTask(s)), !e.isReadonly(o) && !this.drag.mode) {
                            this.clear_drag_state(), n = n || i.target || i.srcElement;
                            var l = a.getClassName(n),
                                    d = this._get_drag_mode(l, n);
                            if (!l || !d)
                                return n.parentNode ? this.on_mouse_down(i, n.parentNode) : void 0;
                            if (d)
                                if (d.mode && d.mode != r.drag_mode.ignore && r["drag_" + d.mode]) {
                                    if (s = e.locate(n), o = e.copy(e.getTask(s) || {}), e.isReadonly(o))
                                        return this.clear_drag_state(), !1;
                                    if (e.isSummaryTask(o) && !r.drag_project && d.mode != r.drag_mode.progress)
                                        return void this.clear_drag_state();
                                    d.id = s;
                                    var c = a.getRelativeEventPosition(i, e.$task_data);
                                    d.start_x = c.x, d.start_y = c.y, d.obj = o, this.drag.start_drag = d, this.drag.timestamp = Date.now()
                                } else
                                    this.clear_drag_state();
                            else if (e.checkEvent("onMouseDown") && e.callEvent("onMouseDown", [l.split(" ")[0]]) && n.parentNode)
                                return this.on_mouse_down(i, n.parentNode)
                        }
                    }
                },
                _fix_dnd_scale_time: function (i, n) {
                    function r(i) {
                        if (e.config.correct_work_time) {
                            var n = t.$getConfig();
                            e.isWorkTime(i.start_date, void 0, i) || (i.start_date = e.calculateEndDate({
                                start_date: i.start_date,
                                duration: -1,
                                unit: n.duration_unit,
                                task: i
                            }))
                        }
                    }
                    var a = t.$getConfig(),
                            s = e.getScale().unit,
                            o = e.getScale().step;
                    a.round_dnd_dates || (s = "minute", o = a.time_step), n.mode == a.drag_mode.resize ? n.left ? (i.start_date = e.roundDate({
                        date: i.start_date,
                        unit: s,
                        step: o
                    }), r(i)) : (i.end_date = e.roundDate({
                        date: i.end_date,
                        unit: s,
                        step: o
                    }), function (i) {
                        if (e.config.correct_work_time) {
                            var n = t.$getConfig();
                            e.isWorkTime(new Date(i.end_date - 1), void 0, i) || (i.end_date = e.calculateEndDate({
                                start_date: i.end_date,
                                duration: 1,
                                unit: n.duration_unit,
                                task: i
                            }))
                        }
                    }(i)) : n.mode == a.drag_mode.move && (i.start_date = e.roundDate({
                        date: i.start_date,
                        unit: s,
                        step: o
                    }), r(i), i.end_date = e.calculateEndDate(i))
                },
                _fix_working_times: function (i, n) {
                    var r = t.$getConfig(),
                            n = n || {
                                mode: r.drag_mode.move
                            };
                    n.mode == r.drag_mode.resize ? n.left ? i.start_date = e.getClosestWorkTime({
                        date: i.start_date,
                        dir: "future",
                        task: i
                    }) : i.end_date = e.getClosestWorkTime({
                        date: i.end_date,
                        dir: "past",
                        task: i
                    }) : n.mode == r.drag_mode.move && e.correctTaskWorkTime(i)
                },
                _finalize_mouse_up: function (t, i, n, r) {
                    var a = e.getTask(t);
                    if (i.work_time && i.correct_work_time && this._fix_working_times(a, n), this._fix_dnd_scale_time(a, n), this._fireEvent("before_finish", n.mode, [t, n.mode, e.copy(n.obj), r])) {
                        var s = t;
                        e._init_task_timing(a), this.clear_drag_state(), e.updateTask(a.id), this._fireEvent("after_finish", n.mode, [s, n.mode, r])
                    } else
                        this.clear_drag_state(), t == n.id && (n.obj._dhx_changed = !1, e.mixin(a, n.obj, !0)), e.refreshTask(a.id)
                },
                on_mouse_up: function (i) {
                    var n = this.drag;
                    if (n.mode && n.id) {
                        var r = t.$getConfig(),
                                a = e.getTask(n.id),
                                s = this.dragMultiple;
                        if (this._finalize_mouse_up(n.id, r, n, i), e.isSummaryTask(a) && r.drag_project && n.mode == r.drag_mode.move)
                            for (var o in s)
                                this._finalize_mouse_up(s[o].id, r, s[o], i)
                    }
                    this.clear_drag_state()
                },
                _get_drag_mode: function (e, i) {
                    var n = t.$getConfig(),
                            r = n.drag_mode,
                            a = (e || "").split(" "),
                            s = a[0],
                            o = {
                                mode: null,
                                left: null
                            };
                    switch (s) {
                        case "gantt_task_line":
                        case "gantt_task_content":
                            o.mode = r.move;
                            break;
                        case "gantt_task_drag":
                            o.mode = r.resize;
                            var l = i.getAttribute("data-bind-property");
                            o.left = "start_date" == l;
                            break;
                        case "gantt_task_progress_drag":
                            o.mode = r.progress;
                            break;
                        case "gantt_link_control":
                        case "gantt_link_point":
                            o.mode = r.ignore;
                            break;
                        default:
                            o = null
                    }
                    return o
                },
                _start_dnd: function (i) {
                    var n = this.drag = this.drag.start_drag;
                    delete n.start_drag;
                    var r = t.$getConfig(),
                            a = n.id;
                    if (r["drag_" + n.mode] && e.callEvent("onBeforeDrag", [a, n.mode, i]) && this._fireEvent("before_start", n.mode, [a, n.mode, i])) {
                        delete n.start_drag;
                        var s = e.getTask(a);
                        e.isSummaryTask(s) && e.config.drag_project && n.mode == r.drag_mode.move && e.eachTask(function (t) {
                            this.dragMultiple[t.id] = e.mixin({
                                id: t.id,
                                obj: t
                            }, this.drag)
                        }, s.id, this), e.callEvent("onTaskDragStart", [])
                    } else
                        this.clear_drag_state()
                },
                _fireEvent: function (t, i, n) {
                    //e.assert(this._events[t], "Invalid stage:{" + t + "}");
                    var r = this._events[t][i];
                    return e.assert(r, "Unknown after drop mode:{" + i + "}"), null, !e.checkEvent(r) || e.callEvent(r, n)
                },
                round_task_dates: function (e) {
                    var i = this.drag,
                            n = t.$getConfig();
                    i || (i = {
                        mode: n.drag_mode.move
                    }), this._fix_dnd_scale_time(e, i)
                },
                destructor: function () {
                    this._domEvents.detachAll()
                }
            }
        }

        function r() {
            var t;
            return {
                extend: function (e) {
                    e.roundTaskDates = function (e) {
                        t.round_task_dates(e)
                    }
                },
                init: function (e, i) {
                    return t = n(e, i), e._tasks_dnd = t, t.init(i)
                },
                destructor: function () {
                    t.destructor(), t = null
                }
            }
        }
        var a = i(1),
                s = i(0),
                o = i(10);
        t.exports = {
            createTaskDND: r
        }
    }, function (t, e, i) {
        var n = i(1),
                r = function (t, e) {
                    function i() {
                        var e = t.getItemHeight();
                        return Math.round(Math.sqrt(2 * e * e)) - 2
                    }

                    function r(t) {
                        return e.getTaskType(t.type) == e.config.types.milestone
                    }

                    function a() {
                        return {
                            link_source_id: k,
                            link_target_id: v,
                            link_from_start: y,
                            link_to_start: m,
                            link_landing_area: p
                        }
                    }

                    function s(t, i, n, a, s) {
                        var l = o(t, function (t) {
                            return e.getTaskPosition(t)
                        }, a),
                                d = {
                                    x: l.x,
                                    y: l.y
                                };
                        i || (d.x = l.xEnd), d.y += e.config.row_height / 2;
                        var c = r(t) && s ? 2 : 0;
                        return n = n || 0, a.rtl && (n *= -1), d.x += (i ? -1 : 1) * n - c, d
                    }

                    function o(t, n, r) {
                        var a = n(t),
                                s = {
                                    x: a.left,
                                    y: a.top,
                                    width: a.width,
                                    height: a.height
                                };
                        if (r.rtl ? (s.xEnd = s.x, s.x = s.xEnd + s.width) : s.xEnd = s.x + s.width, s.yEnd = s.y + s.height, e.getTaskType(t.type) == e.config.types.milestone) {
                            var o = i();
                            s.x += (r.rtl ? 1 : -1) * (o / 2), s.xEnd += (r.rtl ? -1 : 1) * (o / 2), s.width = a.xEnd - a.x
                        }
                        return s
                    }

                    function l(t) {
                        var i = a(),
                                n = ["gantt_link_tooltip"];
                        i.link_source_id && i.link_target_id && (e.isLinkAllowed(i.link_source_id, i.link_target_id, i.link_from_start, i.link_to_start) ? n.push("gantt_allowed_link") : n.push("gantt_invalid_link"));
                        var r = e.templates.drag_link_class(i.link_source_id, i.link_from_start, i.link_target_id, i.link_to_start);
                        r && n.push(r);
                        var s = "<div class='" + r + "'>" + e.templates.drag_link(i.link_source_id, i.link_from_start, i.link_target_id, i.link_to_start) + "</div>";
                        t.innerHTML = s
                    }

                    function d(t, e) {
                        t.style.left = e.x + 5 + "px", t.style.top = e.y + 5 + "px"
                    }

                    function c() {
                        k = y = v = null, m = !0
                    }

                    function h(t, i, n, r) {
                        var s = g(),
                                o = a(),
                                l = ["gantt_link_direction"];
                        e.templates.link_direction_class && l.push(e.templates.link_direction_class(o.link_source_id, o.link_from_start, o.link_target_id, o.link_to_start));
                        var d = Math.sqrt(Math.pow(n - t, 2) + Math.pow(r - i, 2));
                        if (d = Math.max(0, d - 3)) {
                            s.className = l.join(" ");
                            var c = (r - i) / (n - t),
                                    h = Math.atan(c);
                            2 == _(t, n, i, r) ? h += Math.PI : 3 == _(t, n, i, r) && (h -= Math.PI);
                            var f = Math.sin(h),
                                    p = Math.cos(h),
                                    v = Math.round(i),
                                    m = Math.round(t),
                                    k = ["-webkit-transform: rotate(" + h + "rad)", "-moz-transform: rotate(" + h + "rad)", "-ms-transform: rotate(" + h + "rad)", "-o-transform: rotate(" + h + "rad)", "transform: rotate(" + h + "rad)", "width:" + Math.round(d) + "px"];
                            if (-1 != window.navigator.userAgent.indexOf("MSIE 8.0")) {
                                k.push('-ms-filter: "' + u(f, p) + '"');
                                var y = Math.abs(Math.round(t - n)),
                                        b = Math.abs(Math.round(r - i));
                                switch (_(t, n, i, r)) {
                                    case 1:
                                        v -= b;
                                        break;
                                    case 2:
                                        m -= y, v -= b;
                                        break;
                                    case 3:
                                        m -= y
                                }
                            }
                            k.push("top:" + v + "px"), k.push("left:" + m + "px"), s.style.cssText = k.join(";")
                        }
                    }

                    function u(t, e) {
                        return "progid:DXImageTransform.Microsoft.Matrix(M11 = " + e + ",M12 = -" + t + ",M21 = " + t + ",M22 = " + e + ",SizingMethod = 'auto expand')"
                    }

                    function _(t, e, i, n) {
                        return e >= t ? n <= i ? 1 : 4 : n <= i ? 2 : 3
                    }

                    function g() {
                        return x._direction || (x._direction = document.createElement("div"), t.$task_links.appendChild(x._direction)), x._direction
                    }

                    function f() {
                        x._direction && (x._direction.parentNode && x._direction.parentNode.removeChild(x._direction), x._direction = null)
                    }
                    var p, v, m, k, y, b = e.$services,
                            $ = b.getService("state"),
                            w = b.getService("dnd");
                    $.registerProvider("linksDnD", a);
                    var x = new w(t.$task_bars, {
                        sensitivity: 0,
                        updates_per_second: 60
                    });
                    x.attachEvent("onBeforeDragStart", e.bind(function (i, r) {
                        var a = r.target || r.srcElement;
                        if (c(), e.getState().drag_id)
                            return !1;
                        if (n.locateClassName(a, "gantt_link_point")) {
                            n.locateClassName(a, "task_start_date") && (y = !0);
                            var o = e.locate(r);
                            k = o;
                            var l = e.getTask(o);
                            if (e.isReadonly(l))
                                return c(), !1;
                            return this._dir_start = s(l, !!y, 0, t.$getConfig(), !0), !0
                        }
                        return !1
                    }, this)), x.attachEvent("onAfterDragStart", e.bind(function (t, i) {
                        e.config.touch && e.refreshData(), l(x.config.marker)
                    }, this)), x.attachEvent("onDragMove", e.bind(function (i, r) {
                        var a = x.config,
                                o = x.getPosition(r);
                        d(a.marker, o);
                        var c = !!n.locateClassName(r, "gantt_link_control"),
                                u = v,
                                _ = p,
                                g = m,
                                f = e.locate(r),
                                k = !0;
                        if (c && (k = !n.locateClassName(r, "task_end_date"), c = !!f), v = f, p = c, m = k, c) {
                            var y = e.getTask(f),
                                    b = t.$getConfig(),
                                    $ = n.locateClassName(r, "gantt_link_control"),
                                    w = 0;
                            $ && (w = Math.floor($.offsetWidth / 2)), this._dir_end = s(y, !!m, w, b)
                        } else
                            this._dir_end = n.getRelativeEventPosition(r, t.$task_data);
                        var S = !(_ == c && u == f && g == k);
                        return S && (u && e.refreshTask(u, !1), f && e.refreshTask(f, !1)), S && l(a.marker), h(this._dir_start.x, this._dir_start.y, this._dir_end.x, this._dir_end.y), !0
                    }, this)), x.attachEvent("onDragEnd", e.bind(function () {
                        var t = a();

                        onUpdateLink(t);

                        if (t.link_source_id && t.link_target_id && t.link_source_id != t.link_target_id) {
                            var i = e._get_link_type(t.link_from_start, t.link_to_start),
                                    n = {
                                        source: t.link_source_id,
                                        target: t.link_target_id,
                                        type: i
                                    };
                            n.type && e.isLinkAllowed(n) && e.addLink(n)
                        }
                        c(), e.config.touch ? e.refreshData() : (t.link_source_id && e.refreshTask(t.link_source_id, !1), t.link_target_id && e.refreshTask(t.link_target_id, !1)), f()
                    }, this))
                };
        t.exports = {
            createLinkDND: function () {
                return {
                    init: r
                }
            }
        }
    }, function (t, e, i) {
        var n = i(1),
                r = function () {
                    return function (t) {
                        return {
                            getVerticalScrollbar: function () {
                                return t.$ui.getView("scrollVer")
                            },
                            getHorizontalScrollbar: function () {
                                return t.$ui.getView("scrollHor")
                            },
                            _legacyGridResizerClass: function (t) {
                                for (var e = t.getCellsByType("resizer"), i = 0; i < e.length; i++) {
                                    var n = e[i],
                                            r = !1,
                                            a = n.$parent.getPrevSibling(n.$id);
                                    if (a && a.$config && "grid" === a.$config.id)
                                        r = !0;
                                    else {
                                        var s = n.$parent.getNextSibling(n.$id);
                                        s && s.$config && "grid" === s.$config.id && (r = !0)
                                    }
                                    r && (n.$config.css = (n.$config.css ? n.$config.css + " " : "") + "gantt_grid_resize_wrap")
                                }
                            },
                            onCreated: function (e) {
                                var i = !0;
                                this._legacyGridResizerClass(e), e.attachEvent("onBeforeResize", function () {
                                    var r = t.$ui.getView("timeline");
                                    r && (r.$config.hidden = r.$parent.$config.hidden = !t.config.show_chart);
                                    var a = t.$ui.getView("grid");
                                    if (a) {
                                        var s = t.config.show_grid;
                                        if (i) {
                                            var o = a._getColsTotalWidth();
                                            !1 !== o && (t.config.grid_width = o), s = s && !!t.config.grid_width, t.config.show_grid = s
                                        }
                                        if (a.$config.hidden = a.$parent.$config.hidden = !s, !a.$config.hidden) {
                                            var l = a._getGridWidthLimits();
                                            if (l[0] && t.config.grid_width < l[0] && (t.config.grid_width = l[0]), l[1] && t.config.grid_width > l[1] && (t.config.grid_width = l[1]), r && t.config.show_chart)
                                                if (a.$config.width = t.config.grid_width - 1, i)
                                                    a.$parent.$config.width = t.config.grid_width, a.$parent.$config.group && t.$layout._syncCellSizes(a.$parent.$config.group, a.$parent.$config.width);
                                                else if (r && !n.isChildOf(r.$task, e.$view)) {
                                                    if (!a.$config.original_grid_width) {
                                                        var d = t.skins[t.skin];
                                                        d && d.config && d.config.grid_width ? a.$config.original_grid_width = d.config.grid_width : a.$config.original_grid_width = 0
                                                    }
                                                    t.config.grid_width = a.$config.original_grid_width, a.$parent.$config.width = t.config.grid_width
                                                } else
                                                    a.$parent._setContentSize(a.$config.width, a.$config.height);
                                            else
                                                r && n.isChildOf(r.$task, e.$view) && (a.$config.original_grid_width = t.config.grid_width), i || (a.$parent.$config.width = 0)
                                        }
                                        i = !1
                                    }
                                }), t._getVerticalScrollbar = this.getVerticalScrollbar, t._getHorizontalScrollbar = this.getHorizontalScrollbar;
                                var r = this.getVerticalScrollbar(),
                                        a = this.getHorizontalScrollbar();
                                r && r.attachEvent("onScroll", function (e, i, n) {
                                    var r = t.getScrollState();
                                    t.callEvent("onGanttScroll", [r.x, e, r.x, i])
                                }), a && a.attachEvent("onScroll", function (e, i, n) {
                                    var r = t.getScrollState();
                                    t.callEvent("onGanttScroll", [e, r.y, i, r.y])
                                }), e.attachEvent("onResize", function () {
                                    r && !t.$scroll_ver && (t.$scroll_ver = r.$scroll_ver), a && !t.$scroll_hor && (t.$scroll_hor = a.$scroll_hor)
                                })
                            },
                            _findGridResizer: function (t, e) {
                                for (var i, n = t.getCellsByType("resizer"), r = !0, a = 0; a < n.length; a++) {
                                    var s = n[a];
                                    s._getSiblings();
                                    var o = s._behind,
                                            l = s._front;
                                    if (o && o.$content === e || o.isChild && o.isChild(e)) {
                                        i = s, r = !0;
                                        break
                                    }
                                    if (l && l.$content === e || l.isChild && l.isChild(e)) {
                                        i = s, r = !1;
                                        break
                                    }
                                }
                                return {
                                    resizer: i,
                                    gridFirst: r
                                }
                            },
                            onInitialized: function (e) {
                                var i = t.$ui.getView("grid"),
                                        n = this._findGridResizer(e, i);
                                if (n.resizer) {
                                    var r, a = n.gridFirst,
                                            s = n.resizer;
                                    s.attachEvent("onResizeStart", function (e, i) {
                                        var n = t.$ui.getView("grid"),
                                                s = n ? n.$parent : null;
                                        if (s) {
                                            var o = n._getGridWidthLimits();
                                            n.$config.scrollable || (s.$config.minWidth = o[0]), s.$config.maxWidth = o[1]
                                        }
                                        return r = a ? e : i, t.callEvent("onGridResizeStart", [r])
                                    }), s.attachEvent("onResize", function (e, i) {
                                        var n = a ? e : i;
                                        return t.callEvent("onGridResize", [r, n])
                                    }), s.attachEvent("onResizeEnd", function (e, i, n, r) {
                                        var s = a ? e : i,
                                                o = a ? n : r,
                                                l = t.$ui.getView("grid"),
                                                d = l ? l.$parent : null;
                                        d && (d.$config.minWidth = void 0);
                                        var c = t.callEvent("onGridResizeEnd", [s, o]);
                                        return c && (t.config.grid_width = o), c
                                    })
                                }
                            },
                            onDestroyed: function (t) {}
                        }
                    }
                }();
        t.exports = r
    }, function (t, e) {
        t.exports = function (t) {}
    }, function (t, e) {
        function i() {
            function t(t) {
                return t.$ui.getView("timeline")
            }

            function e(t) {
                return t.$ui.getView("grid")
            }

            function i(t) {
                return t.$ui.getView("scrollVer")
            }

            function n(t) {
                return t.$ui.getView("scrollHor")
            }

            function r(t, e, i, n) {
                var r = t(this);
                return r && r.isVisible() ? r[e].apply(r, i) : n ? n() : a
            }
            var a = "DEFAULT_VALUE";
            return {
                dateFromPos: function (e) {
                    var i = r.call(this, t, "dateFromPos", Array.prototype.slice.call(arguments));
                    return i === a ? this.getState().min_date : i
                },
                posFromDate: function (e) {
                    var i = r.call(this, t, "posFromDate", [e]);
                    return i === a ? 0 : i
                },
                getRowTop: function (i) {
                    var n = this,
                            s = r.call(n, t, "getRowTop", [i], function () {
                                return r.call(n, e, "getRowTop", [i])
                            });
                    return s === a ? 0 : s
                },
                getTaskTop: function (i) {
                    var n = this,
                            s = r.call(n, t, "getItemTop", [i], function () {
                                return r.call(n, e, "getItemTop", [i])
                            });
                    return s === a ? 0 : s
                },
                getTaskPosition: function (e, i, n) {
                    var s = r.call(this, t, "getItemPosition", [e, i, n]);
                    if (s === a) {
                        return {
                            left: 0,
                            top: this.getTaskTop(e.id),
                            height: this.getTaskHeight(),
                            width: 0
                        }
                    }
                    return s
                },
                getTaskHeight: function () {
                    var i = this,
                            n = r.call(i, t, "getItemHeight", [], function () {
                                return r.call(i, e, "getItemHeight", [])
                            });
                    return n === a ? 0 : n
                },
                columnIndexByDate: function (e) {
                    var i = r.call(this, t, "columnIndexByDate", [e]);
                    return i === a ? 0 : i
                },
                roundTaskDates: function () {
                    r.call(this, t, "roundTaskDates", [])
                },
                getScale: function () {
                    var e = r.call(this, t, "getScale", []);
                    return e === a ? null : e
                },
                getTaskNode: function (e) {
                    var i = t(this);
                    return i && i.isVisible() ? i._taskRenderer.rendered[e] : null
                },
                getLinkNode: function (e) {
                    var i = t(this);
                    return i.isVisible() ? i._linkRenderer.rendered[e] : null
                },
                scrollTo: function (t, e) {
                    var r = i(this),
                            a = n(this),
                            s = {
                                position: 0
                            },
                            o = {
                                position: 0
                            };
                    r && (o = r.getScrollState()), a && (s = a.getScrollState()), a && 1 * t == t && a.scroll(t), r && 1 * e == e && r.scroll(e);
                    var l = {
                        position: 0
                    },
                            d = {
                                position: 0
                            };
                    r && (l = r.getScrollState()), a && (d = a.getScrollState()), this.callEvent("onGanttScroll", [s.position, o.position, d.position, l.position])
                },
                showDate: function (t) {
                    var e = this.posFromDate(t),
                            i = Math.max(e - this.config.task_scroll_offset, 0);
                    this.scrollTo(i)
                },
                showTask: function (t) {
                    var e, i = this.getTaskPosition(this.getTask(t)),
                            n = Math.max(i.left - this.config.task_scroll_offset, 0),
                            r = this._scroll_state().y;
                    e = r ? i.top - (r - this.config.row_height) / 2 : i.top, this.scrollTo(n, e)
                },
                _scroll_state: function () {
                    var t = {
                        x: !1,
                        y: !1,
                        x_pos: 0,
                        y_pos: 0,
                        scroll_size: this.config.scroll_size + 1,
                        x_inner: 0,
                        y_inner: 0
                    },
                            e = i(this),
                            r = n(this);
                    if (r) {
                        var a = r.getScrollState();
                        a.visible && (t.x = a.size, t.x_inner = a.scrollSize), t.x_pos = a.position || 0
                    }
                    if (e) {
                        var s = e.getScrollState();
                        s.visible && (t.y = s.size, t.y_inner = s.scrollSize), t.y_pos = s.position || 0
                    }
                    return t
                },
                getScrollState: function () {
                    var t = this._scroll_state();
                    return {
                        x: t.x_pos,
                        y: t.y_pos,
                        inner_width: t.x,
                        inner_height: t.y,
                        width: t.x_inner,
                        height: t.y_inner
                    }
                }
            }
        }
        t.exports = i
    }, function (t, e, i) {
        function n(t) {
            function e(e) {
                if (t.isTaskExists(e.source)) {
                    var i = t.getTask(e.source);
                    i.$source = i.$source || [], i.$source.push(e.id)
                }
                if (t.isTaskExists(e.target)) {
                    var n = t.getTask(e.target);
                    n.$target = n.$target || [], n.$target.push(e.id)
                }
            }

            function n(e) {
                if (t.isTaskExists(e.source))
                    for (var i = t.getTask(e.source), n = 0; n < i.$source.length; n++)
                        if (i.$source[n] == e.id) {
                            i.$source.splice(n, 1);
                            break
                        }
                if (t.isTaskExists(e.target))
                    for (var r = t.getTask(e.target), n = 0; n < r.$target.length; n++)
                        if (r.$target[n] == e.id) {
                            r.$target.splice(n, 1);
                            break
                        }
            }

            function o() {
                for (var i = null, n = t.$data.tasksStore.getItems(), r = 0, a = n.length; r < a; r++)
                    i = n[r], i.$source = [], i.$target = [];
                for (var s = t.$data.linksStore.getItems(), r = 0, a = s.length; r < a; r++) {
                    e(s[r])
                }
            }

            function l(t) {
                var e = t.source,
                        i = t.target;
                for (var n in t.events)
                    !function (t, n) {
                        e.attachEvent(t, function () {
                            return i.callEvent(n, Array.prototype.slice.call(arguments))
                        }, n)
                    }(n, t.events[n])
            }

            function d(e) {
                this.defined(e.id) || (e.id = this.uid()), e.start_date && (e.start_date = t.date.parseDate(e.start_date, "xml_date")), e.end_date && (e.end_date = t.date.parseDate(e.end_date, "xml_date"));
                var i = null;
                return (e.duration || 0 === e.duration) && (e.duration = i = 1 * e.duration), i && (e.start_date && !e.end_date ? e.end_date = this.calculateEndDate(e) : !e.start_date && e.end_date && (e.start_date = this.calculateEndDate({
                    start_date: e.end_date,
                    duration: -e.duration,
                    task: e
                }))), this._isAllowedUnscheduledTask(e) && this._set_default_task_timing(e), this._init_task_timing(e), e.start_date && e.end_date && this.correctTaskWorkTime(e), e.$source = [], e.$target = [], void 0 === e.parent && this.setParent(e, this.config.root_id), e
            }

            function c(t) {
                return this.defined(t.id) || (t.id = this.uid()), t
            }
            var h = a.create();
            r.mixin(t, h);
            var u = t.createDatastore({
                name: "task",
                type: "treeDatastore",
                rootId: function () {
                    return t.config.root_id
                },
                initItem: r.bind(d, t)
            }),
                    _ = t.createDatastore({
                        name: "link",
                        initItem: r.bind(c, t)
                    });
            u.attachEvent("onBeforeRefreshAll", function () {
                for (var e = u.getVisibleItems(), i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.$index = i, t.resetProjectDates(n)
                }
            }), u.attachEvent("onFilterItem", function (e, i) {
                var n = null,
                        r = null;
                if (t.config.start_date && t.config.end_date) {
                    if (t._isAllowedUnscheduledTask(i))
                        return !0;
                    if (n = t.config.start_date.valueOf(), r = t.config.end_date.valueOf(), +i.start_date > r || +i.end_date < +n)
                        return !1
                }
                return !0
            }), u.attachEvent("onIdChange", function (e, i) {
                t._update_flags(e, i)
            }), u.attachEvent("onAfterUpdate", function (e) {
                t._update_parents(e)
            }), u.attachEvent("onAfterItemMove", function (e, i, n) {
                var r = t.getTask(e);
                null !== this.getNextSibling(e) ? r.$drop_target = this.getNextSibling(e) : null !== this.getPrevSibling(e) ? r.$drop_target = "next:" + this.getPrevSibling(e) : r.$drop_target = "next:null"
            }), u.attachEvent("onStoreUpdated", function (e, i, n) {
                if ("delete" == n && t._update_flags(e, null), !t.$services.getService("state").getState("batchUpdate").batch_update) {
                    if (t.config.fit_tasks && "paint" !== n) {
                        var r = t.getState();
                        s(t);
                        var a = t.getState();
                        if (+r.min_date != +a.min_date || +r.max_date != +a.max_date)
                            return t.render(), t.callEvent("onScaleAdjusted", []), !0
                    }
                    "add" == n || "move" == n || "delete" == n ? t.$layout.resize() : e || _.refresh()
                }
            }), _.attachEvent("onAfterAdd", function (t, i) {
                e(i)
            }), _.attachEvent("onAfterUpdate", function (t, e) {
                o()
            }), _.attachEvent("onAfterDelete", function (t, e) {
                n(e)
            }), _.attachEvent("onBeforeIdChange", function (i, r) {
                n(t.mixin({
                    id: i
                }, t.$data.linksStore.getItem(r))), e(t.$data.linksStore.getItem(r))
            }), _.attachEvent("onFilterItem", function (e, i) {
                return !!t.config.show_links && (!(!t.isTaskVisible(i.source) || !t.isTaskVisible(i.target) || t._isAllowedUnscheduledTask(t.getTask(i.source)) || t._isAllowedUnscheduledTask(t.getTask(i.target))) && t.callEvent("onBeforeLinkDisplay", [e, i]))
            }),
                    function () {
                        var e = i(20),
                                r = {};
                        t.attachEvent("onBeforeTaskDelete", function (i, n) {
                            return r[i] = e.getSubtreeLinks(t, i), !0
                        }), t.attachEvent("onAfterTaskDelete", function (e, i) {
                            r[e] && t.$data.linksStore.silent(function () {
                                for (var i in r[e])
                                    t.$data.linksStore.removeItem(i), n(r[e][i]);
                                r[e] = null
                            })
                        })
                    }(), t.attachEvent("onAfterLinkDelete", function (e, i) {
                //t.refreshTask(i.source), t.refreshTask(i.target)
                onDeleteLink(i);

            }), t.attachEvent("onParse", o), l({
                source: _,
                target: t,
                events: {
                    onItemLoading: "onLinkLoading",
                    onBeforeAdd: "onBeforeLinkAdd",
                    onAfterAdd: "onAfterLinkAdd",
                    onBeforeUpdate: "onBeforeLinkUpdate",
                    onAfterUpdate: "onAfterLinkUpdate",
                    onBeforeDelete: "onBeforeLinkDelete",
                    onAfterDelete: "onAfterLinkDelete",
                    onIdChange: "onLinkIdChange"
                }
            }), l({
                source: u,
                target: t,
                events: {
                    onItemLoading: "onTaskLoading",
                    onBeforeAdd: "onBeforeTaskAdd",
                    onAfterAdd: "onAfterTaskAdd",
                    onBeforeUpdate: "onBeforeTaskUpdate",
                    onAfterUpdate: "onAfterTaskUpdate",
                    onBeforeDelete: "onBeforeTaskDelete",
                    onAfterDelete: "onAfterTaskDelete",
                    onIdChange: "onTaskIdChange",
                    onBeforeItemMove: "onBeforeTaskMove",
                    onAfterItemMove: "onAfterTaskMove",
                    onFilterItem: "onBeforeTaskDisplay",
                    onItemOpen: "onTaskOpened",
                    onItemClose: "onTaskClosed",
                    onBeforeSelect: "onBeforeTaskSelected",
                    onAfterSelect: "onTaskSelected",
                    onAfterUnselect: "onTaskUnselected"
                }
            }), t.$data = {
                tasksStore: u,
                linksStore: _
            }
        }
        var r = i(0),
                a = i(66),
                s = i(19);
        t.exports = n
    }, function (t, e, i) {
        function n() {
            for (var t = this.$services.getService("datastores"), e = [], i = 0; i < t.length; i++)
                e.push(this.getDatastore(t[i]));
            return e
        }

        function r() {
            var t = a.mixin({}, u());
            return a.mixin(t, s()), a.mixin(t, o()), t
        }
        var a = i(0),
                s = i(67),
                o = i(68),
                l = i(16),
                d = i(18),
                c = i(69),
                h = i(70),
                u = function () {
                    return {
                        createDatastore: function (t) {
                            var e = "treedatastore" == (t.type || "").toLowerCase() ? d : l;
                            if (t) {
                                var i = this;
                                t.openInitially = function () {
                                    return i.config.open_tree_initially
                                }
                            }
                            var n = new e(t);
                            if (this.mixin(n, c()), t.name) {
                                this.$services.setService("datastore:" + t.name, function () {
                                    return n
                                });
                                var r = this.$services.getService("datastores");
                                r || (r = [], this.$services.setService("datastores", function () {
                                    return r
                                })), r.push(t.name), h.bindDataStore(t.name, this)
                            }
                            return n
                        },
                        getDatastore: function (t) {
                            return this.$services.getService("datastore:" + t)
                        },
                        refreshData: function () {
                            var t = this.getScrollState();
                            this.callEvent("onBeforeDataRender", []);
                            for (var e = n.call(this), i = 0; i < e.length; i++)
                                e[i].refresh();
                            (t.x || t.y) && this.scrollTo(t.x, t.y), this.callEvent("onDataRender", [])
                        },
                        isChildOf: function (t, e) {
                            return this.$data.tasksStore.isChildOf(t, e)
                        },
                        refreshTask: function (t, e) {
                            var i = this.getTask(t);
                            if (i && this.isTaskVisible(t)) {
                                if (this.$data.tasksStore.refresh(t, !!this.getState().drag_id), void 0 !== e && !e)
                                    return;
                                for (var n = 0; n < i.$source.length; n++)
                                    this.refreshLink(i.$source[n]);
                                for (var n = 0; n < i.$target.length; n++)
                                    this.refreshLink(i.$target[n])
                            }
                        },
                        refreshLink: function (t) {
                            this.$data.linksStore.refresh(t)
                        },
                        silent: function (t) {
                            var e = this;
                            e.$data.tasksStore.silent(function () {
                                e.$data.linksStore.silent(function () {
                                    t()
                                })
                            })
                        },
                        clearAll: function () {
                            for (var t = n.call(this), e = 0; e < t.length; e++)
                                t[e].clearAll();
                            this._update_flags(), this.userdata = {}, this.callEvent("onClear", []), this.render()
                        },
                        _clear_data: function () {
                            this.$data.tasksStore.clearAll(), this.$data.linksStore.clearAll(), this._update_flags(), this.userdata = {}
                        },
                        selectTask: function (t) {
                            var e = this.$data.tasksStore;
                            return !!this.config.select_task && (t && e.select(t), e.getSelectedId())
                        },
                        unselectTask: function (t) {
                            this.$data.tasksStore.unselect(t)
                        },
                        getSelectedId: function () {
                            return this.$data.tasksStore.getSelectedId()
                        }
                    }
                };
        t.exports = {
            create: r
        }
    }, function (t, e, i) {
        var n = i(0),
                r = function () {
                    return {
                        getTask: function (t) {
                            //this.assert(t, "Invalid argument for gantt.getTask");
                            var e = this.$data.tasksStore.getItem(t);
                            return null, e
                        },
                        getTaskByTime: function (t, e) {
                            var i = this.$data.tasksStore.getItems(),
                                    n = [];
                            if (t || e) {
                                t = +t || -1 / 0, e = +e || 1 / 0;
                                for (var r = 0; r < i.length; r++) {
                                    var a = i[r];
                                    +a.start_date < e && +a.end_date > t && n.push(a)
                                }
                            } else
                                n = i;
                            return n
                        },
                        isTaskExists: function (t) {
                            return this.$data.tasksStore.exists(t)
                        },
                        updateTask: function (t, e) {
                            n.defined(e) || (e = this.getTask(t)), this.$data.tasksStore.updateItem(t, e), this.refreshTask(t)
                        },
                        addTask: function (t, e, i) {
                            return n.defined(t.id) || (t.id = n.uid()), n.defined(e) || (e = this.getParent(t) || 0), this.isTaskExists(e) || (e = 0), this.setParent(t, e), this.$data.tasksStore.addItem(t, i, e)
                        },
                        deleteTask: function (t) {
                            return this.$data.tasksStore.removeItem(t)
                        },
                        getTaskCount: function () {
                            return this.$data.tasksStore.count()
                        },
                        getVisibleTaskCount: function () {
                            return this.$data.tasksStore.countVisible()
                        },
                        getTaskIndex: function (t) {
                            return this.$data.tasksStore.getBranchIndex(t)
                        },
                        getGlobalTaskIndex: function (t) {
                            return null, this.$data.tasksStore.getIndexById(t)
                        },
                        eachTask: function (t, e, i) {
                            return this.$data.tasksStore.eachItem(n.bind(t, i || this), e)
                        },
                        eachParent: function (t, e, i) {
                            return this.$data.tasksStore.eachParent(n.bind(t, i || this), e)
                        },
                        changeTaskId: function (t, e) {
                            this.$data.tasksStore.changeId(t, e);
                            var i = this.$data.tasksStore.getItem(e),
                                    n = [];
                            i.$source && (n = n.concat(i.$source)), i.$target && (n = n.concat(i.$target));
                            for (var r = 0; r < n.length; r++) {
                                var a = this.getLink(n[r]);
                                a.source == t && (a.source = e), a.target == t && (a.target = e)
                            }
                        },
                        calculateTaskLevel: function (t) {
                            return this.$data.tasksStore.calculateItemLevel(t)
                        },
                        getNext: function (t) {
                            return this.$data.tasksStore.getNext(t)
                        },
                        getPrev: function (t) {
                            return this.$data.tasksStore.getPrev(t)
                        },
                        getParent: function (t) {
                            return this.$data.tasksStore.getParent(t)
                        },
                        setParent: function (t, e, i) {
                            return this.$data.tasksStore.setParent(t, e, i)
                        },
                        getSiblings: function (t) {
                            return this.$data.tasksStore.getSiblings(t).slice()
                        },
                        getNextSibling: function (t) {
                            return this.$data.tasksStore.getNextSibling(t)
                        },
                        getPrevSibling: function (t) {
                            return this.$data.tasksStore.getPrevSibling(t)
                        },
                        getTaskByIndex: function (t) {
                            var e = this.$data.tasksStore.getIdByIndex(t);
                            return this.isTaskExists(e) ? this.getTask(e) : null
                        },
                        getChildren: function (t) {
                            return this.$data.tasksStore.getChildren(t).slice()
                        },
                        hasChild: function (t) {
                            return this.$data.tasksStore.hasChild(t)
                        },
                        open: function (t) {
                            this.$data.tasksStore.open(t)
                        },
                        close: function (t) {
                            this.$data.tasksStore.close(t)
                        },
                        moveTask: function (t, e, i) {
                            this.$data.tasksStore.move.apply(this.$data.tasksStore, arguments)
                        },
                        sort: function (t, e, i, n) {
                            var r = !n;
                            this.$data.tasksStore.sort(t, e, i), r && this.render()
                        }
                    }
                };
        t.exports = r
    }, function (t, e, i) {
        var n = i(0),
                r = function () {
                    return {
                        getLinkCount: function () {
                            return this.$data.linksStore.count()
                        },
                        getLink: function (t) {
                            return this.$data.linksStore.getItem(t)
                        },
                        getLinks: function () {
                            return this.$data.linksStore.getItems()
                        },
                        isLinkExists: function (t) {
                            return this.$data.linksStore.exists(t)
                        },
                        addLink: function (t) {
                            return this.$data.linksStore.addItem(t)
                        },
                        updateLink: function (t, e) {
                            n.defined(e) || (e = this.getLink(t)), this.$data.linksStore.updateItem(t, e)
                        },
                        deleteLink: function (t) {
                            return this.$data.linksStore.removeItem(t)
                        },
                        changeLinkId: function (t, e) {
                            return this.$data.linksStore.changeId(t, e)
                        }
                    }
                };
        t.exports = r
    }, function (t, e) {
        function i() {
            var t = null;
            return {
                select: function (e) {
                    if (e) {
                        if (t == e)
                            return t;
                        if (!this._skip_refresh && !this.callEvent("onBeforeSelect", [e]))
                            return !1;
                        this.unselect(), t = e, this._skip_refresh || (this.refresh(e), this.callEvent("onAfterSelect", [e]))
                    }
                    return t
                },
                getSelectedId: function () {
                    return t
                },
                unselect: function (e) {
                    var e = e || t;
                    e && (t = null, this._skip_refresh || (this.refresh(e), this.callEvent("onAfterUnselect", [e])))
                }
            }
        }
        t.exports = i
    }, function (t, e) {
        var i = function (t, e) {
            function i(t) {
                return !!t.$services.getService("state").getState("batchUpdate").batch_update
            }

            function n(t, e, i, n) {
                for (var r = 0; r < t.length; r++)
                    t[r].change_id(e, i)
            }
            var r = e.getDatastore(t),
                    a = {
                        renderItem: function (t, e) {
                            var i = e.getLayers(),
                                    n = r.getItem(t);
                            if (n && r.isVisible(t))
                                for (var a = 0; a < i.length; a++)
                                    i[a].render_item(n)
                        },
                        renderItems: function (t) {
                            for (var e = t.getLayers(), i = 0; i < e.length; i++)
                                e[i].clear();
                            for (var n = r.getVisibleItems(), i = 0; i < e.length; i++)
                                e[i].render_items(n)
                        }
                    };
            r.attachEvent("onStoreUpdated", function (n, s, o) {
                if (!i(e)) {
                    var l = e.$services.getService("layers").getDataRender(t);
                    l && (r.filter(), n && "move" != o && "delete" != o ? (r.callEvent("onBeforeRefreshItem", [s.id]), a.renderItem(s.id, l), r.callEvent("onAfterRefreshItem", [s.id])) : (r.callEvent("onBeforeRefreshAll", []), a.renderItems(l), r.callEvent("onAfterRefreshAll", [])))
                }
            }), r.attachEvent("onItemOpen", function () {
                e.render()
            }), r.attachEvent("onItemClose", function () {
                e.render()
            }), r.attachEvent("onIdChange", function (s, o) {
                if (r.getSelectedId() == s && r.silent(function () {
                    r.unselect(s), r.select(o)
                }), r.callEvent("onBeforeIdChange", [s, o]), !i(e)) {
                    var l = e.$services.getService("layers").getDataRender(t);
                    n(l.getLayers(), s, o, r.getItem(o)), a.renderItem(o, l)
                }
            })
        };
        t.exports = {
            bindDataStore: i
        }
    }, function (t, e, i) {
        function n(t) {
            function e(t, e) {
                delete e.$gantt, delete e.setGanttMode, delete e._getRowData, e.afterUpdate = d, delete t._dp, delete t._change_id, delete t._row_style, delete t._delete_task, delete t._sendTaskOrder, s.forEach(c, function (e) {
                    t.detachEvent(e)
                }), c = []
            }

            function n(t, e) {
                e.setGanttMode = function (t) {
                    var i = e.modes || {};
                    e._ganttMode && (i[e._ganttMode] = {
                        _in_progress: e._in_progress,
                        _invalid: e._invalid,
                        updatedRows: e.updatedRows
                    });
                    var n = i[t];
                    n || (n = i[t] = {
                        _in_progress: {},
                        _invalid: {},
                        updatedRows: []
                    }), e._in_progress = n._in_progress, e._invalid = n._invalid, e.updatedRows = n.updatedRows, e.modes = i, e._ganttMode = t
                }, d = e.afterUpdate, e.afterUpdate = function () {
                    var t;
                    t = 3 == arguments.length ? arguments[1] : arguments[4];
                    var i = e._ganttMode,
                            n = t.filePath;
                    i = "REST" != this._tMode ? -1 != n.indexOf("gantt_mode=links") ? "links" : "tasks" : n.indexOf("/link") > n.indexOf("/task") ? "links" : "tasks", e.setGanttMode(i);
                    var r = d.apply(e, arguments);
                    return e.setGanttMode(i), r
                }, e._getRowData = t.bind(function (i, n) {
                    var r;
                    r = "tasks" == e._ganttMode ? this.isTaskExists(i) ? this.getTask(i) : {
                        id: i
                    } : this.isLinkExists(i) ? this.getLink(i) : {
                        id: i
                    }, r = t.copy(r);
                    var s = {};
                    for (var o in r)
                        if ("$" != o.substr(0, 1)) {
                            var l = r[o];
                            a.isDate(l) ? s[o] = this.templates.xml_format(l) : s[o] = null === l ? "" : l
                        }
                    var d = this._get_task_timing_mode(r);
                    return d.$no_start && (r.start_date = "", r.duration = ""), d.$no_end && (r.end_date = "", r.duration = ""), s[e.action_param] = this.getUserData(i, e.action_param), s
                }, t)
            }

            function o(t, e) {
                t._change_id = t.bind(function (t, i) {
                    "tasks" != e._ganttMode ? this.changeLinkId(t, i) : this.changeTaskId(t, i)
                }, this), t._row_style = function (i, n) {
                    if ("tasks" == e._ganttMode && t.isTaskExists(i)) {
                        t.getTask(i).$dataprocessor_class = n, t.refreshTask(i)
                    }
                }, t._delete_task = function (t, e) {}, t._sendTaskOrder = function (t, i) {
                    i.$drop_target && (e.setGanttMode("tasks"), this.getTask(t).target = i.$drop_target, e.setUpdated(t, !0, "order"), delete this.getTask(t).$drop_target)
                }, this._dp = e
            }

            function l(t, e) {
                function n(i) {
                    for (var n = e.updatedRows.slice(), r = !1, a = 0; a < n.length && !e._in_progress[i]; a++)
                        n[a] == i && ("inserted" == t.getUserData(i, "!nativeeditor_status") && (r = !0), e.setUpdated(i, !1));
                    return r
                }

                function r(t) {
                    var e = [];
                    return t.$source && (e = e.concat(t.$source)), t.$target && (e = e.concat(t.$target)), e
                }
                c.push(this.attachEvent("onAfterTaskAdd", function (t, i) {
                    e.setGanttMode("tasks"), e.setUpdated(t, !0, "inserted")
                })), c.push(this.attachEvent("onAfterTaskUpdate", function (i, n) {

                    e.setGanttMode("tasks"), e.setUpdated(i, !0), t._sendTaskOrder(i, n)
                }));
                var a = i(20),
                        s = {};
                c.push(this.attachEvent("onBeforeTaskDelete", function (e, i) {
                    return !t.config.cascade_delete || (s[e] = {
                        tasks: a.getSubtreeTasks(t, e),
                        links: a.getSubtreeLinks(t, e)
                    }, !0)
                })), c.push(this.attachEvent("onAfterTaskDelete", function (i, r) {
                    if (e.setGanttMode("tasks"), !n(i)) {
                        if (t.config.cascade_delete && s[i]) {
                            var a = e.updateMode;
                            e.setUpdateMode("off");
                            var o = s[i];
                            for (var l in o.tasks)
                                n(l) || e.setUpdated(l, !0, "deleted");
                            e.setGanttMode("links");
                            for (var l in o.links)
                                n(l) || e.setUpdated(l, !0, "deleted");
                            s[i] = null, "off" != a && e.sendAllData(), e.setGanttMode("tasks"), e.setUpdateMode(a)
                        }
                        e.setUpdated(i, !0, "deleted"), "off" == e.updateMode || e._tSend || e.sendAllData()
                    }
                })), c.push(this.attachEvent("onAfterLinkUpdate", function (t, i) {
                    e.setGanttMode("links"), e.setUpdated(t, !0)
                })), c.push(this.attachEvent("onAfterLinkAdd", function (t, i) {
                    e.setGanttMode("links"), e.setUpdated(t, !0, "inserted")
                })), c.push(this.attachEvent("onAfterLinkDelete", function (t, i) {
                    e.setGanttMode("links"), !n(t) && e.setUpdated(t, !0, "deleted")
                })), c.push(this.attachEvent("onRowDragEnd", function (e, i) {
                    t._sendTaskOrder(e, t.getTask(e))

                }));
                var o = null,
                        l = null;
                c.push(this.attachEvent("onTaskIdChange", function (i, n) {
                    if (e._waitMode) {
                        var a = t.getChildren(n);
                        if (a.length) {
                            o = o || {};
                            for (var s = 0; s < a.length; s++) {
                                var d = this.getTask(a[s]);
                                o[d.id] = d
                            }
                        }
                        var c = this.getTask(n),
                                h = r(c);
                        if (h.length) {
                            l = l || {};
                            for (var s = 0; s < h.length; s++) {
                                var u = this.getLink(h[s]);
                                l[u.id] = u
                            }
                        }
                    }
                })), e.attachEvent("onAfterUpdateFinish", function () {
                    (o || l) && (t.batchUpdate(function () {
                        for (var e in o)
                            t.updateTask(o[e].id);
                        for (var e in l)
                            t.updateLink(l[e].id);
                        o = null, l = null
                    }), o ? t._dp.setGanttMode("tasks") : t._dp.setGanttMode("links"))
                }), e.attachEvent("onBeforeDataSending", function () {
                    var e = this._serverProcessor;
                    if ("REST" == this._tMode) {
                        var i = this._ganttMode.substr(0, this._ganttMode.length - 1);
                        e = e.substring(0, e.indexOf("?") > -1 ? e.indexOf("?") : e.length), this.serverProcessor = e + ("/" == e.slice(-1) ? "" : "/") + i
                    } else
                        this.serverProcessor = e + t.ajax.urlSeparator(e) + "gantt_mode=" + this._ganttMode;
                    return !0
                })
            }
            t.dataProcessor = i(73);
            var d, c = [];
            t._dp_init = function (i) {

            }, t.getUserData = function (t, e) {
                return this.userdata || (this.userdata = {}), this.userdata[t] && this.userdata[t][e] ? this.userdata[t][e] : ""
            }, t.setUserData = function (t, e, i) {
                this.userdata || (this.userdata = {}), this.userdata[t] || (this.userdata[t] = {}), this.userdata[t][e] = i
            }
        }
        var r = i(72),
                a = i(4),
                s = i(7);
        t.exports = n
    }, function (t, e, i) {
        function n(t, e, i, n) {
            var r = t.data || this.xml._xmlNodeToJSON(t.firstChild),
                    a = {
                        add: this.addTask,
                        isExist: this.isTaskExists
                    };
            "links" == n && (a.add = this.addLink, a.isExist = this.isLinkExists), a.isExist.call(this, e) || (r.id = e, a.add.call(this, r))
        }

        function r(t, e, i, n) {
            var r = {
                delete: this.deleteTask,
                isExist: this.isTaskExists
            };
            "links" == n && (r.delete = this.deleteLink, r.isExist = this.isLinkExists), r.isExist.call(this, e) && r.delete.call(this, e)
        }

        function a(t, e) {
            e.attachEvent("insertCallback", s.bind(n, t)), e.attachEvent("updateCallback", s.bind(r, t)), e.attachEvent("deleteCallback", s.bind(r, t))
        }
        var s = i(0);
        t.exports = a
    }, function (t, e, i) {
        var n = i(0),
                r = i(2),
                a = function (t) {
                    return this.serverProcessor = t, this.action_param = "!nativeeditor_status", this.object = null, this.updatedRows = [], this.autoUpdate = !0, this.updateMode = "cell", this._tMode = "GET", this._headers = null, this._payload = null, this.post_delim = "_", this._waitMode = 0, this._in_progress = {}, this._invalid = {}, this.mandatoryFields = [], this.messages = [], this.styles = {
                        updated: "font-weight:bold;",
                        inserted: "font-weight:bold;",
                        deleted: "text-decoration : line-through;",
                        invalid: "background-color:FFE0E0;",
                        invalid_cell: "border-bottom:2px solid red;",
                        error: "color:red;",
                        clear: "font-weight:normal;text-decoration:none;"
                    }, this.enableUTFencoding(!0), r(this), this
                };
        a.prototype = {
            setTransactionMode: function (t, e) {
                "object" == typeof t ? (this._tMode = t.mode || this._tMode, n.defined(t.headers) && (this._headers = t.headers), n.defined(t.payload) && (this._payload = t.payload)) : (this._tMode = t, this._tSend = e), "REST" == this._tMode && (this._tSend = !1, this._endnm = !0), "JSON" == this._tMode && (this._tSend = !1, this._endnm = !0, this._headers = this._headers || {}, this._headers["Content-type"] = "application/json")
            },
            escape: function (t) {
                return this._utf ? encodeURIComponent(t) : escape(t)
            },
            enableUTFencoding: function (t) {
                this._utf = !!t
            },
            setDataColumns: function (t) {
                this._columns = "string" == typeof t ? t.split(",") : t
            },
            getSyncState: function () {
                return !this.updatedRows.length
            },
            enableDataNames: function (t) {
                this._endnm = !!t
            },
            enablePartialDataSend: function (t) {
                this._changed = !!t
            },
            setUpdateMode: function (t, e) {
                this.autoUpdate = "cell" == t, this.updateMode = t, this.dnd = e
            },
            ignore: function (t, e) {
                this._silent_mode = !0, t.call(e || window), this._silent_mode = !1
            },
            setUpdated: function (t, e, i) {
                if (!this._silent_mode) {
                    var n = this.findRow(t);
                    i = i || "updated";
                    var r = this.obj.getUserData(t, this.action_param);
                    r && "updated" == i && (i = r), e ? (this.set_invalid(t, !1), this.updatedRows[n] = t, this.obj.setUserData(t, this.action_param, i), this._in_progress[t] && (this._in_progress[t] = "wait")) : this.is_invalid(t) || (this.updatedRows.splice(n, 1), this.obj.setUserData(t, this.action_param, "")), e || this._clearUpdateFlag(t), this.markRow(t, e, i), e && this.autoUpdate && this.sendData(t)
                }
            },
            _clearUpdateFlag: function (t) {},
            markRow: function (t, e, i) {
                var n = "",
                        r = this.is_invalid(t);
                if (r && (n = this.styles[r], e = !0), this.callEvent("onRowMark", [t, e, i, r]) && (n = this.styles[e ? i : "clear"] + n, this.obj[this._methods[0]](t, n), r && r.details)) {
                    n += this.styles[r + "_cell"];
                    for (var a = 0; a < r.details.length; a++)
                        r.details[a] && this.obj[this._methods[1]](t, a, n)
                }
            },
            getState: function (t) {
                return this.obj.getUserData(t, this.action_param)
            },
            is_invalid: function (t) {
                return this._invalid[t]
            },
            set_invalid: function (t, e, i) {
                i && (e = {
                    value: e,
                    details: i,
                    toString: function () {
                        return this.value.toString()
                    }
                }), this._invalid[t] = e
            },
            checkBeforeUpdate: function (t) {
                return !0
            },
            sendData: function (t) {
                if (!this._waitMode || "tree" != this.obj.mytype && !this.obj._h2) {
                    if (this.obj.editStop && this.obj.editStop(), void 0 === t || this._tSend)
                        return this.sendAllData();
                    if (this._in_progress[t])
                        return !1;
                    if (this.messages = [], !this.checkBeforeUpdate(t) && this.callEvent("onValidationError", [t, this.messages]))
                        return !1;
                    this._beforeSendData(this._getRowData(t), t)
                }
            },
            _beforeSendData: function (t, e) {
                if (!this.callEvent("onBeforeUpdate", [e, this.getState(e), t]))
                    return !1;
                this._sendData(t, e)
            },
            serialize: function (t, e) {
                if ("string" == typeof t)
                    return t;
                if (void 0 !== e)
                    return this.serialize_one(t, "");
                var i = [],
                        n = [];
                for (var r in t)
                    t.hasOwnProperty(r) && (i.push(this.serialize_one(t[r], r + this.post_delim)), n.push(r));
                return i.push("ids=" + this.escape(n.join(","))), this.$gantt.security_key && i.push("dhx_security=" + this.$gantt.security_key), i.join("&")
            },
            serialize_one: function (t, e) {
                if ("string" == typeof t)
                    return t;
                var i = [];
                for (var n in t)
                    if (t.hasOwnProperty(n)) {
                        if (("id" == n || n == this.action_param) && "REST" == this._tMode)
                            continue;
                        i.push(this.escape((e || "") + n) + "=" + this.escape(t[n]))
                    }
                return i.join("&")
            },
            _applyPayload: function (t) {
                var e = this.$gantt.ajax;
                if (this._payload)
                    for (var i in this._payload)
                        t = t + e.urlSeparator(t) + this.escape(i) + "=" + this.escape(this._payload[i]);
                return t
            },
            _sendData: function (t, e) {
                if (t) {
                    if (!this.callEvent("onBeforeDataSending", e ? [e, this.getState(e), t] : [null, null, t]))
                        return !1;
                    e && (this._in_progress[e] = (new Date).valueOf());
                    var i = this,
                            n = function (n) {
                                var r = [];
                                if (e)
                                    r.push(e);
                                else if (t)
                                    for (var a in t)
                                        r.push(a);
                                return i.afterUpdate(i, n, r)
                            },
                            r = this.$gantt.ajax,
                            a = this.serverProcessor + (this._user ? r.urlSeparator(this.serverProcessor) + ["dhx_user=" + this._user, "dhx_version=" + this.obj.getUserData(0, "version")].join("&") : ""),
                            s = this._applyPayload(a);
                    if ("GET" == this._tMode)
                        r.query({
                            url: s + r.urlSeparator(s) + this.serialize(t, e),
                            method: "GET",
                            callback: n,
                            headers: this._headers
                        });
                    else if ("POST" == this._tMode)
                        r.query({
                            url: s,
                            method: "POST",
                            headers: this._headers,
                            data: this.serialize(t, e),
                            callback: n
                        });
                    else if ("JSON" == this._tMode) {
                        var o = t[this.action_param],
                                l = {};
                        for (var d in t)
                            l[d] = t[d];
                        delete l[this.action_param], delete l.id, delete l.gr_id, r.query({
                            url: s,
                            method: "POST",
                            headers: this._headers,
                            callback: n,
                            data: JSON.stringify({
                                id: e,
                                action: o,
                                data: l
                            })
                        })
                    } else if ("REST" == this._tMode) {
                        var c = this.getState(e),
                                h = a.replace(/(\&|\?)editing\=true/, ""),
                                l = "",
                                u = "post";
                        "inserted" == c ? l = this.serialize(t, e) : "deleted" == c ? (u = "DELETE", h = h + ("/" == h.slice(-1) ? "" : "/") + e) : (u = "PUT", l = this.serialize(t, e), h = h + ("/" == h.slice(-1) ? "" : "/") + e), h = this._applyPayload(h), r.query({
                            url: h,
                            method: u,
                            headers: this._headers,
                            data: l,
                            callback: n
                        })
                    }
                    this._waitMode++
                }
            },
            sendAllData: function () {
                if (this.updatedRows.length) {
                    this.messages = [];
                    for (var t = !0, e = 0; e < this.updatedRows.length; e++)
                        t &= this.checkBeforeUpdate(this.updatedRows[e]);
                    if (!t && !this.callEvent("onValidationError", ["", this.messages]))
                        return !1;
                    if (this._tSend)
                        this._sendData(this._getAllData());
                    else
                        for (var e = 0; e < this.updatedRows.length; e++)
                            if (!this._in_progress[this.updatedRows[e]]) {
                                if (this.is_invalid(this.updatedRows[e]))
                                    continue;
                                if (this._beforeSendData(this._getRowData(this.updatedRows[e]), this.updatedRows[e]), this._waitMode && ("tree" == this.obj.mytype || this.obj._h2))
                                    return
                            }
                }
            },
            _getAllData: function (t) {
                for (var e = {}, i = !1, n = 0; n < this.updatedRows.length; n++) {
                    var r = this.updatedRows[n];
                    if (!this._in_progress[r] && !this.is_invalid(r)) {
                        var a = this._getRowData(r);
                        this.callEvent("onBeforeUpdate", [r, this.getState(r), a]) && (e[r] = a, i = !0, this._in_progress[r] = (new Date).valueOf())
                    }
                }
                return i ? e : null
            },
            setVerificator: function (t, e) {
                this.mandatoryFields[t] = e || function (t) {
                    return "" !== t
                }
            },
            clearVerificator: function (t) {
                this.mandatoryFields[t] = !1
            },
            findRow: function (t) {
                var e = 0;
                for (e = 0; e < this.updatedRows.length && t != this.updatedRows[e]; e++)
                    ;
                return e
            },
            defineAction: function (t, e) {
                this._uActions || (this._uActions = []), this._uActions[t] = e
            },
            afterUpdateCallback: function (t, e, i, n) {
                var r = t,
                        a = "error" != i && "invalid" != i;
                if (a || this.set_invalid(t, i), this._uActions && this._uActions[i] && !this._uActions[i](n))
                    return delete this._in_progress[r];
                "wait" != this._in_progress[r] && this.setUpdated(t, !1);
                var s = t;
                switch (i) {
                    case "inserted":
                    case "insert":
                        e != t && (this.setUpdated(t, !1), this.obj[this._methods[2]](t, e), t = e);
                        break;
                    case "delete":
                    case "deleted":
                        return this.obj.setUserData(t, this.action_param, "true_deleted"), this.obj[this._methods[3]](t), delete this._in_progress[r], this.callEvent("onAfterUpdate", [t, i, e, n])
                }
                "wait" != this._in_progress[r] ? (a && this.obj.setUserData(t, this.action_param, ""), delete this._in_progress[r]) : (delete this._in_progress[r], this.setUpdated(e, !0, this.obj.getUserData(t, this.action_param))), this.callEvent("onAfterUpdate", [s, i, e, n])
            },
            afterUpdate: function (t, e, i) {

                var n = this.$gantt.ajax;
                if (window.JSON) {
                    var r;
                    try {
                        r = JSON.parse(e.xmlDoc.responseText)
                    } catch (t) {
                        e.xmlDoc.responseText.length || (r = {})
                    }
                    if (r) {
                        var a = r.action || this.getState(i) || "updated",
                                s = r.sid || i[0],
                                o = r.tid || i[0];
                        return t.afterUpdateCallback(s, o, a, r), void t.finalizeUpdate()
                    }
                }
                var l = n.xmltop("data", e.xmlDoc);
                if (!l)
                    return this.cleanUpdate(i);
                var d = n.xpath("//data/action", l);
                if (!d.length)
                    return this.cleanUpdate(i);
                for (var c = 0; c < d.length; c++) {
                    var h = d[c],
                            a = h.getAttribute("type"),
                            s = h.getAttribute("sid"),
                            o = h.getAttribute("tid");
                    t.afterUpdateCallback(s, o, a, h)
                }
                t.finalizeUpdate()
            },
            cleanUpdate: function (t) {
                if (t)
                    for (var e = 0; e < t.length; e++)
                        delete this._in_progress[t[e]]
            },
            finalizeUpdate: function () {
                this._waitMode && this._waitMode--, ("tree" == this.obj.mytype || this.obj._h2) && this.updatedRows.length && this.sendData(), this.callEvent("onAfterUpdateFinish", []), this.updatedRows.length || this.callEvent("onFullSync", [])
            },
            init: function (t) {
                this.obj = t, this.obj._dp_init && this.obj._dp_init(this)
            },
            setOnAfterUpdate: function (t) {
                this.attachEvent("onAfterUpdate", t)
            },
            enableDebug: function (t) {},
            setOnBeforeUpdateHandler: function (t) {
                this.attachEvent("onBeforeDataSending", t)
            },
            setAutoUpdate: function (t, e) {
                t = t || 2e3, this._user = e || (new Date).valueOf(), this._need_update = !1, this._update_busy = !1, this.attachEvent("onAfterUpdate", function (t, e, i, n) {
                    this.afterAutoUpdate(t, e, i, n)
                }), this.attachEvent("onFullSync", function () {
                    this.fullSync()
                });
                var i = this;
                window.setInterval(function () {
                    i.loadUpdate()
                }, t)
            },
            afterAutoUpdate: function (t, e, i, n) {
                return "collision" != e || (this._need_update = !0, !1)
            },
            fullSync: function () {
                return this._need_update && (this._need_update = !1, this.loadUpdate()), !0
            },
            getUpdates: function (t, e) {
                var i = this.$gantt.ajax;
                if (this._update_busy)
                    return !1;
                this._update_busy = !0, i.get(t, e)
            },
            _v: function (t) {
                return t.firstChild ? t.firstChild.nodeValue : ""
            },
            _a: function (t) {
                for (var e = [], i = 0; i < t.length; i++)
                    e[i] = this._v(t[i]);
                return e
            },
            loadUpdate: function () {
                var t = this.$gantt.ajax,
                        e = this,
                        i = this.obj.getUserData(0, "version"),
                        n = this.serverProcessor + t.urlSeparator(this.serverProcessor) + ["dhx_user=" + this._user, "dhx_version=" + i].join("&");
                n = n.replace("editing=true&", ""), this.getUpdates(n, function (i) {
                    var n = t.xpath("//userdata", i);
                    e.obj.setUserData(0, "version", e._v(n[0]));
                    var r = t.xpath("//update", i);
                    if (r.length) {
                        e._silent_mode = !0;
                        for (var a = 0; a < r.length; a++) {
                            var s = r[a].getAttribute("status"),
                                    o = r[a].getAttribute("id"),
                                    l = r[a].getAttribute("parent");
                            switch (s) {
                                case "inserted":
                                    e.callEvent("insertCallback", [r[a], o, l]);
                                    break;
                                case "updated":
                                    e.callEvent("updateCallback", [r[a], o, l]);
                                    break;
                                case "deleted":
                                    e.callEvent("deleteCallback", [r[a], o, l])
                            }
                        }
                        e._silent_mode = !1
                    }
                    e._update_busy = !1, e = null
                })
            },
            destructor: function () {
                this.callEvent("onDestroy", []), this.detachAllEvents(), this.updatedRows = [], this._in_progress = {}, this._invalid = {}, this._headers = null, this._payload = null, this.obj = null
            }
        }, t.exports = a
    }, function (t, e, i) {
        t.exports = function (t) {
            for (var e = [i(75), i(76), i(77), i(78), i(79), i(80)], n = 0; n < e.length; n++)
                e[n] && e[n](t)
        }
    }, function (t, e, i) {
        var n = i(1);
        t.exports = function (t) {
            function e() {
                return t.$task || t.$grid || t.$root
            }

            function i() {
                var e = !!document.querySelector(".gantt_drag_marker"),
                        i = !!document.querySelector(".gantt_drag_marker.gantt_grid_resize_area"),
                        n = !!document.querySelector(".gantt_link_direction");
                return f = e && !i && !n, !(!t.getState().drag_mode && !e || i)
            }

            function r(i) {
                if (p && (clearTimeout(p), p = null), i) {
                    var r = t.config.autoscroll_speed;
                    r && r < 10 && (r = 10), p = setTimeout(function () {
                        g = setInterval(o, r || _), v = n.getNodePosition(e())
                    }, t.config.autoscroll_delay || u)
                }
            }

            function a(t) {
                t ? (r(!0), m.started || (m.x = k.x, m.y = k.y, m.started = !0)) : (g && (clearInterval(g), g = null), r(!1), m.started = !1)
            }

            function s(e) {
                var n = i();
                if (!g && !p || n || a(!1), !t.config.autoscroll || !n)
                    return !1;
                k = {
                    x: e.clientX,
                    y: e.clientY
                }, !g && n && a(!0)
            }

            function o() {
                if (!i())
                    return a(!1), !1;
                var r = n.getNodePosition(e()),
                        s = k.x - r.x,
                        o = k.y - r.y,
                        c = f ? 0 : l(s, r.width, m.x - r.x),
                        u = l(o, r.height, m.y - r.y),
                        _ = t.getScrollState(),
                        g = _.y,
                        p = _.inner_height,
                        v = _.height,
                        y = _.x,
                        b = _.inner_width,
                        $ = _.width;
                u && !p ? u = 0 : u < 0 && !g ? u = 0 : u > 0 && g + p >= v + 2 && (u = 0), c && !b ? c = 0 : c < 0 && !y ? c = 0 : c > 0 && y + b >= $ && (c = 0);
                var w = t.config.autoscroll_step;
                w && w < 2 && (w = 2), c *= w || h, u *= w || h, (c || u) && d(c, u)
            }

            function l(t, e, i) {
                return t - c < 0 && t < i ? -1 : t > e - c && t > i ? 1 : 0
            }

            function d(e, i) {
                var n = t.getScrollState(),
                        r = null,
                        a = null;
                e && (r = n.x + e, r = Math.min(n.width, r), r = Math.max(0, r)), i && (a = n.y + i, a = Math.min(n.height, a), a = Math.max(0, a)), t.scrollTo(r, a)
            }
            var c = 50,
                    h = 30,
                    u = 10,
                    _ = 50,
                    g = null,
                    f = !1,
                    p = null,
                    v = {},
                    m = {
                        started: !1
                    },
                    k = {};
            t.attachEvent("onGanttReady", function () {
                t.eventRemove(document.body, "mousemove", s), t.event(document.body, "mousemove", s)
            })
        }
    }, function (t, e) {
        function i(t) {
            function e(t, e) {
                e = "function" == typeof e ? e : function () {}, s[t] || (s[t] = this[t], this[t] = e)
            }

            function i(t) {
                s[t] && (this[t] = s[t], s[t] = null)
            }

            function n(t) {
                for (var i in t)
                    e.call(this, i, t[i])
            }

            function r() {
                for (var t in s)
                    i.call(this, t)
            }

            function a(t) {
                try {
                    t()
                } catch (t) {
                    window.console.error(t)
                }
            }
            var s = {},
                    o = !1;
            return t.$services.getService("state").registerProvider("batchUpdate", function () {
                return {
                    batch_update: o
                }
            }, !0),
                    function (t, e) {
                        if (o)
                            return void a(t);
                        var i, s = this._dp && "off" != this._dp.updateMode;
                        s && (i = this._dp.updateMode, this._dp.setUpdateMode("off"));
                        var l = {},
                                d = {
                                    render: !0,
                                    refreshData: !0,
                                    refreshTask: !0,
                                    refreshLink: !0,
                                    resetProjectDates: function (t) {
                                        l[t.id] = t
                                    }
                                };
                        n.call(this, d), o = !0, this.callEvent("onBeforeBatchUpdate", []), a(t), this.callEvent("onAfterBatchUpdate", []), r.call(this);
                        for (var c in l)
                            this.resetProjectDates(l[c]);
                        o = !1, e || this.render(), s && (this._dp.setUpdateMode(i), this._dp.setGanttMode("tasks"), this._dp.sendData(), this._dp.setGanttMode("links"), this._dp.sendData())
                    }
        }
        t.exports = function (t) {
            t.batchUpdate = i(t)
        }
    }, function (t, e) {
        var i = function (t) {
            return {
                _needRecalc: !0,
                reset: function () {
                    this._needRecalc = !0
                },
                _isRecalcNeeded: function () {
                    return !this._isGroupSort() && this._needRecalc
                },
                _isGroupSort: function () {
                    return !(!t._groups || !t._groups.is_active())
                },
                _getWBSCode: function (t) {
                    return t ? (this._isRecalcNeeded() && this._calcWBS(), t.$virtual ? "" : this._isGroupSort() ? t.$wbs || "" : (t.$wbs || (this.reset(), this._calcWBS()), t.$wbs)) : ""
                },
                _setWBSCode: function (t, e) {
                    t.$wbs = e
                },
                getWBSCode: function (t) {
                    return this._getWBSCode(t)
                },
                _calcWBS: function () {
                    if (this._isRecalcNeeded()) {
                        var e = !0;
                        t.eachTask(function (i) {
                            if (e)
                                return e = !1, void this._setWBSCode(i, "1");
                            var n = t.getPrevSibling(i.id);
                            if (null !== n) {
                                var r = t.getTask(n).$wbs;
                                r && (r = r.split("."), r[r.length - 1]++, this._setWBSCode(i, r.join(".")))
                            } else {
                                var a = t.getParent(i.id);
                                this._setWBSCode(i, t.getTask(a).$wbs + ".1")
                            }
                        }, t.config.root_id, this), this._needRecalc = !1
                    }
                }
            }
        };
        t.exports = function (t) {
            var e = i(t);
            t.getWBSCode = function (t) {
                return e.getWBSCode(t)
            }, t.attachEvent("onAfterTaskMove", function () {
                return e.reset(), !0
            }), t.attachEvent("onBeforeParse", function () {
                return e.reset(), !0
            }), t.attachEvent("onAfterTaskDelete", function () {
                return e.reset(), !0
            }), t.attachEvent("onAfterTaskAdd", function () {
                return e.reset(), !0
            })
        }
    }, function (t, e) {
        window.jQuery && function (t) {
            var e = [];
            t.fn.dhx_gantt = function (i) {
                if ("string" != typeof (i = i || {})) {
                    var n = [];
                    return this.each(function () {
                        if (this && this.getAttribute)
                            if (this.gantt || window.gantt.$root == this)
                                n.push("object" == typeof this.gantt ? this.gantt : window.gantt);
                            else {
                                var t = window.gantt.$container && window.Gantt ? window.Gantt.getGanttInstance() : window.gantt;
                                for (var e in i)
                                    "data" != e && (t.config[e] = i[e]);
                                t.init(this), i.data && t.parse(i.data), n.push(t)
                            }
                    }), 1 === n.length ? n[0] : n
                }
                if (e[i])
                    return e[i].apply(this, []);
                t.error("Method " + i + " does not exist on jQuery.dhx_gantt")
            }
        }(jQuery), t.exports = null
    }, function (t, e) {
        window.dhtmlx && (dhtmlx.attaches || (dhtmlx.attaches = {}), dhtmlx.attaches.attachGantt = function (t, e, i) {
            var n = document.createElement("DIV");
            i = i || window.gantt, n.id = "gantt_" + i.uid(), n.style.width = "100%", n.style.height = "100%", n.cmp = "grid", document.body.appendChild(n), this.attachObject(n.id), this.dataType = "gantt", this.dataObj = i;
            var r = this.vs[this.av];
            r.grid = i, i.init(n.id, t, e), n.firstChild.style.border = "none", r.gridId = n.id, r.gridObj = n;
            return this.vs[this._viewRestore()].grid
        }), void 0 !== window.dhtmlXCellObject && (dhtmlXCellObject.prototype.attachGantt = function (t, e, i) {
            i = i || window.gantt;
            var n = document.createElement("DIV");
            n.id = "gantt_" + i.uid(), n.style.width = "100%", n.style.height = "100%", n.cmp = "grid", document.body.appendChild(n), this.attachObject(n.id), this.dataType = "gantt", this.dataObj = i, i.init(n.id, t, e), n.firstChild.style.border = "none";
            return n = null, this.callEvent("_onContentAttach", []), this.dataObj
        }), t.exports = null
    }, function (t, e) {
        function i(t) {
            function e(t, e) {
                return "function" == typeof t ? i(t) : e instanceof Array ? n(t, e) : n(t, [e])
            }

            function i(e) {
                var i = [];
                return t.eachTask(function (t) {
                    e(t) && i.push(t)
                }), i
            }

            function n(e, i) {
                for (var n, r = i.join("_") + "_" + e, a = {}, s = 0; s < i.length; s++)
                    a[i[s]] = !0;
                return o[r] ? n = o[r] : (n = o[r] = [], t.eachTask(function (i) {
                    a[i[e]] && i.type != t.config.types.project && n.push(i)
                })), n
            }

            function r(t, e, i) {
                var n = [t, e, i.unit, i.step].join("_");
                return o[n] ? o[n] : o[n] = a(t, e, i)
            }

            function a(i, n, r) {
                for (var a = e(i, n), s = r.unit, o = {}, l = 0; l < a.length; l++)
                    for (var d = a[l], c = t.date[s + "_start"](new Date(d.start_date)); c < d.end_date; ) {
                        var h = c;
                        if (c = t.date.add(c, 1, s), t.isWorkTime({
                            date: h,
                            task: d
                        })) {
                            var u = h.valueOf();
                            o[u] || (o[u] = {
                                tasks: []
                            }), o[u].tasks.push(d)
                        }
                    }
                var _, g, f = [];
                for (var l in o)
                    _ = new Date(1 * l), g = t.date.add(_, 1, s), f.push({
                        start_date: _,
                        end_date: g,
                        tasks: o[l].tasks
                    });
                return f
            }

            function s(t, e) {
                for (var i = e.$getConfig(), n = e.$getTemplates(), a = r(i.resource_property, t.id, e.getScale()), s = [], o = 0; o < a.length; o++) {
                    var l = a[o],
                            d = n.resource_cell_class(l.start_date, l.end_date, t, l.tasks),
                            c = n.resource_cell_value(l.start_date, l.end_date, t, l.tasks);
                    if (d || c) {
                        var h = e.getItemPosition(t, l.start_date, l.end_date),
                                u = document.createElement("div");
                        u.className = ["gantt_resource_marker", d].join(" "), u.style.cssText = ["left:" + h.left + "px", "width:" + h.width + "px", "height:" + (i.row_height - 1) + "px", "line-height:" + (i.row_height - 1) + "px", "top:" + h.top + "px"].join(";"), c && (u.innerHTML = c), s.push(u)
                    }
                }
                var _ = null;
                if (s.length) {
                    _ = document.createElement("div");
                    for (var o = 0; o < s.length; o++)
                        _.appendChild(s[o])
                }
                return _
            }
            var o = {},
                    l = {};
            return t.$data.tasksStore.attachEvent("onStoreUpdated", function () {
                o = {}, l = {}
            }), {
                renderLine: s,
                filterTasks: e
            }
        }
        t.exports = function (t) {
            var e = i(t);
            t.getTaskBy = e.filterTasks, t.$ui.layers.resourceRow = e.renderLine, t.config.resource_property = "owner_id", t.config.resource_store = "resource", t.templates.resource_cell_class = function (t, e, i, n) {
                return n.length <= 1 ? "gantt_resource_marker_ok" : "gantt_resource_marker_overtime"
            }, t.templates.resource_cell_value = function (t, e, i, n) {
                return 8 * n.length
            }
        }
    }, function (t, e, i) {
        t.exports = function (t) {
            function e(e) {
                if (t.config.branch_loading && t._load_url) {
                    if (!t.getUserData(e, "was_rendered") && !t.getChildren(e).length && t.hasChild(e))
                        return !0
                }
                return !1
            }
            var n = i(18),
                    r = n.prototype.hasChild;
            t.$data.tasksStore.hasChild = function (e) {
                return !!r.apply(this, arguments) || !!this.exists(e) && this.getItem(e)[t.config.branch_loading_property]
            }, t.attachEvent("onTaskOpened", function (i) {
                if (t.config.branch_loading && t._load_url && e(i)) {
                    var n = t._load_url;
                    n = n.replace(/(\?|&)?parent_id=.+&?/, "");
                    var r = n.indexOf("?") >= 0 ? "&" : "?",
                            a = t.getScrollState().y || 0;
                    t.load(n + r + "parent_id=" + encodeURIComponent(i), this._load_type, function () {
                        a && t.scrollTo(null, a)
                    }), t.setUserData(i, "was_rendered", !0)
                }
            })
        }
    }, function (t, e, i) {
        var n = i(8);
        t.exports = function (t) {
            i(83)(t), n.prototype.getGridColumns = function () {
                for (var t = this.$getConfig(), e = t.columns, i = [], n = 0; n < e.length; n++)
                    e[n].hide || i.push(e[n]);
                return i
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.getGridColumn = function (e) {
                for (var i = t.config.columns, n = 0; n < i.length; n++)
                    if (i[n].name == e)
                        return i[n];
                return null
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            function e(t) {
                return (t + "").replace(r, " ").replace(a, " ")
            }

            function i(t) {
                return (t + "").replace(s, "&#39;")
            }

            function n() {
                return !t.config.wai_aria_attributes
            }
            var r = new RegExp("<(?:.|\n)*?>", "gm"),
                    a = new RegExp(" +", "gm"),
                    s = new RegExp("'", "gm");
            t._waiAria = {
                getAttributeString: function (t) {
                    var n = [" "];
                    for (var r in t) {
                        var a = i(e(t[r]));
                        n.push(r + "='" + a + "'")
                    }
                    return n.push(" "), n.join(" ")
                },
                getTimelineCellAttr: function (e) {
                    return t._waiAria.getAttributeString({
                        "aria-label": e
                    })
                },
                _taskCommonAttr: function (i, n) {
                    i.start_date && i.end_date && (n.setAttribute("aria-label", e(t.templates.tooltip_text(i.start_date, i.end_date, i))), t.isReadonly(i) && n.setAttribute("aria-readonly", !0), i.$dataprocessor_class && n.setAttribute("aria-busy", !0), n.setAttribute("aria-selected", t.getState().selected_task == i.id || t.isSelectedTask && t.isSelectedTask(i.id) ? "true" : "false"))
                },
                setTaskBarAttr: function (e, i) {
                    this._taskCommonAttr(e, i), !t.isReadonly(e) && t.config.drag_move && (e.id != t.getState().drag_id ? i.setAttribute("aria-grabbed", !1) : i.setAttribute("aria-grabbed", !0))
                },
                taskRowAttr: function (e, i) {
                    this._taskCommonAttr(e, i), !t.isReadonly(e) && t.config.order_branch && i.setAttribute("aria-grabbed", !1), i.setAttribute("role", "row"), i.setAttribute("aria-level", e.$level), t.hasChild(e.id) && i.setAttribute("aria-expanded", e.$open ? "true" : "false")
                },
                linkAttr: function (i, n) {
                    var r = t.config.links,
                            a = i.type == r.finish_to_start || i.type == r.start_to_start,
                            s = i.type == r.start_to_start || i.type == r.start_to_finish,
                            o = t.locale.labels.link + " " + t.templates.drag_link(i.source, s, i.target, a);
                    n.setAttribute("aria-label", e(o)), t.isReadonly(i) && n.setAttribute("aria-readonly", !0)
                },
                gridSeparatorAttr: function (t) {
                    t.setAttribute("role", "separator")
                },
                lightboxHiddenAttr: function (t) {
                    t.setAttribute("aria-hidden", "true")
                },
                lightboxVisibleAttr: function (t) {
                    t.setAttribute("aria-hidden", "false")
                },
                lightboxAttr: function (t) {
                    t.setAttribute("role", "dialog"), t.setAttribute("aria-hidden", "true"), t.firstChild.setAttribute("role", "heading")
                },
                lightboxButtonAttrString: function (e) {
                    return this.getAttributeString({
                        role: "button",
                        "aria-label": t.locale.labels[e],
                        tabindex: "0"
                    })
                },
                lightboxHeader: function (t, e) {
                    t.setAttribute("aria-label", e)
                },
                lightboxSelectAttrString: function (e) {
                    var i = "";
                    switch (e) {
                        case "%Y":
                            i = t.locale.labels.years;
                            break;
                        case "%m":
                            i = t.locale.labels.months;
                            break;
                        case "%d":
                            i = t.locale.labels.days;
                            break;
                        case "%H:%i":
                            i = t.locale.labels.hours + t.locale.labels.minutes
                    }
                    return t._waiAria.getAttributeString({
                        "aria-label": i
                    })
                },
                lightboxDurationInputAttrString: function (e) {
                    return this.getAttributeString({
                        "aria-label": t.locale.labels.column_duration,
                        "aria-valuemin": "0"
                    })
                },
                gridAttrString: function () {
                    return [" role='treegrid'", t.config.multiselect ? "aria-multiselectable='true'" : "aria-multiselectable='false'", " "].join(" ")
                },
                gridScaleRowAttrString: function () {
                    return "role='row'"
                },
                gridScaleCellAttrString: function (e, i) {
                    var n = "";
                    if ("add" == e.name)
                        n = this.getAttributeString({
                            role: "button",
                            "aria-label": t.locale.labels.new_task
                        });
                    else {
                        var r = {
                            role: "columnheader",
                            "aria-label": i
                        };
                        t._sort && t._sort.name == e.name && ("asc" == t._sort.direction ? r["aria-sort"] = "ascending" : r["aria-sort"] = "descending"), n = this.getAttributeString(r)
                    }
                    return n
                },
                gridDataAttrString: function () {
                    return "role='rowgroup'"
                },
                gridCellAttrString: function (t, e) {
                    return this.getAttributeString({
                        role: "gridcell",
                        "aria-label": e
                    })
                },
                gridAddButtonAttrString: function (e) {
                    return this.getAttributeString({
                        role: "button",
                        "aria-label": t.locale.labels.new_task
                    })
                },
                messageButtonAttrString: function (t) {
                    return "tabindex='0' role='button' aria-label='" + t + "'"
                },
                messageInfoAttr: function (t) {
                    t.setAttribute("role", "alert")
                },
                messageModalAttr: function (t, e) {
                    t.setAttribute("role", "dialog"), e && t.setAttribute("aria-labelledby", e)
                },
                quickInfoAttr: function (t) {
                    t.setAttribute("role", "dialog")
                },
                quickInfoHeaderAttrString: function () {
                    return " role='heading' "
                },
                quickInfoHeader: function (t, e) {
                    t.setAttribute("aria-label", e)
                },
                quickInfoButtonAttrString: function (e) {
                    return t._waiAria.getAttributeString({
                        role: "button",
                        "aria-label": e,
                        tabindex: "0"
                    })
                },
                tooltipAttr: function (t) {
                    t.setAttribute("role", "tooltip")
                },
                tooltipVisibleAttr: function (t) {
                    t.setAttribute("aria-hidden", "false")
                },
                tooltipHiddenAttr: function (t) {
                    t.setAttribute("aria-hidden", "true")
                }
            };
            for (var o in t._waiAria)
                t._waiAria[o] = function (t) {
                    return function () {
                        return n() ? "" : t.apply(this, arguments)
                    }
                }(t._waiAria[o])
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.isReadonly = function (t) {
                return (!t || !t[this.config.editable_property]) && (t && t[this.config.readonly_property] || this.config.readonly)
            }
        }
    }, function (t, e, i) {
        var n = (i(0), i(4));
        t.exports = function (t) {
            t.load = function (e, i, n) {
                this._load_url = e, null;
                var r = "json",
                        a = null;
                arguments.length >= 3 ? (r = i, a = n) : "string" == typeof arguments[1] ? r = arguments[1] : "function" == typeof arguments[1] && (a = arguments[1]), this._load_type = r, this.callEvent("onLoadStart", [e, r]), this.ajax.get(e, t.bind(function (t) {
                    this.on_load(t, r), this.callEvent("onLoadEnd", [e, r]), "function" == typeof a && a.call(this)
                }, this))
            }, t.parse = function (t, e) {
                this.on_load({
                    xmlDoc: {
                        responseText: t
                    }
                }, e)
            }, t.serialize = function (t) {
                return t = t || "json", this[t].serialize()
            }, t.on_load = function (t, e) {
                this.callEvent("onBeforeParse", []), e || (e = "json"), null;
                var i = t.xmlDoc.responseText,
                        n = this[e].parse(i, t);
                this._process_loading(n)
            }, t._process_loading = function (t) {
                t.collections && this._load_collections(t.collections), this.$data.tasksStore.parse(t.data);
                var e = t.links || (t.collections ? t.collections.links : []);
                if (this.$data.linksStore.parse(e), this.callEvent("onParse", []), this.render(), this.config.initial_scroll) {
                    var i = this.getTaskByIndex(0),
                            n = i ? i.id : this.config.root_id;
                    this.isTaskExists(n) && this.showTask(n)
                }
            }, t._load_collections = function (t) {
                var e = !1;
                for (var i in t)
                    if (t.hasOwnProperty(i)) {
                        e = !0;
                        var n = t[i],
                                r = this.serverList[i];
                        if (!r)
                            continue;
                        r.splice(0, r.length);
                        for (var a = 0; a < n.length; a++) {
                            var s = n[a],
                                    o = this.copy(s);
                            o.key = o.value;
                            for (var l in s)
                                if (s.hasOwnProperty(l)) {
                                    if ("value" == l || "label" == l)
                                        continue;
                                    o[l] = s[l]
                                }
                            r.push(o)
                        }
                    }
                e && this.callEvent("onOptionsLoad", [])
            }, t.attachEvent("onBeforeTaskDisplay", function (t, e) {
                return !e.$ignore
            }), t.json = {
                parse: function (e) {
                    return null, "string" == typeof e && (window.JSON ? e = JSON.parse(e) : t.assert(!1, "JSON is not supported")), e.dhx_security && (t.security_key = e.dhx_security), e
                },
                serializeTask: function (t) {
                    return this._copyObject(t)
                },
                serializeLink: function (t) {
                    return this._copyLink(t)
                },
                _copyLink: function (t) {
                    var e = {};
                    for (var i in t)
                        e[i] = t[i];
                    return e
                },
                _copyObject: function (e) {
                    var i = {};
                    for (var r in e)
                        "$" != r.charAt(0) && (i[r] = e[r], n.isDate(i[r]) && (i[r] = t.templates.xml_format(i[r])));
                    return i
                },
                serialize: function () {
                    var e = [],
                            i = [];
                    t.eachTask(function (i) {
                        t.resetProjectDates(i), e.push(this.serializeTask(i))
                    }, t.config.root_id, this);
                    for (var n = t.getLinks(), r = 0; r < n.length; r++)
                        i.push(this.serializeLink(n[r]));
                    return {
                        data: e,
                        links: i
                    }
                }
            }, t.xml = {
                _xmlNodeToJSON: function (t, e) {
                    for (var i = {}, n = 0; n < t.attributes.length; n++)
                        i[t.attributes[n].name] = t.attributes[n].value;
                    if (!e) {
                        for (var n = 0; n < t.childNodes.length; n++) {
                            var r = t.childNodes[n];
                            1 == r.nodeType && (i[r.tagName] = r.firstChild ? r.firstChild.nodeValue : "")
                        }
                        i.text || (i.text = t.firstChild ? t.firstChild.nodeValue : "")
                    }
                    return i
                },
                _getCollections: function (e) {
                    for (var i = {}, n = t.ajax.xpath("//coll_options", e), r = 0; r < n.length; r++)
                        for (var a = n[r].getAttribute("for"), s = i[a] = [], o = t.ajax.xpath(".//item", n[r]), l = 0; l < o.length; l++) {
                            for (var d = o[l], c = d.attributes, h = {
                                key: o[l].getAttribute("value"),
                                label: o[l].getAttribute("label")
                            }, u = 0; u < c.length; u++) {
                                var _ = c[u];
                                "value" != _.nodeName && "label" != _.nodeName && (h[_.nodeName] = _.nodeValue)
                            }
                            s.push(h)
                        }
                    return i
                },
                _getXML: function (e, i, n) {
                    n = n || "data", i.getXMLTopNode || (i = t.ajax.parse(i));
                    var r = t.ajax.xmltop(n, i.xmlDoc);
                    if (!r || r.tagName != n)
                        throw "Invalid XML data";
                    var a = r.getAttribute("dhx_security");
                    return a && (t.security_key = a), r
                },
                parse: function (e, i) {
                    i = this._getXML(e, i);
                    for (var n = {}, r = n.data = [], a = t.ajax.xpath("//task", i), s = 0; s < a.length; s++)
                        r[s] = this._xmlNodeToJSON(a[s]);
                    return n.collections = this._getCollections(i), n
                },
                _copyLink: function (t) {
                    return "<item id='" + t.id + "' source='" + t.source + "' target='" + t.target + "' type='" + t.type + "' />"
                },
                _copyObject: function (t) {
                    return "<task id='" + t.id + "' parent='" + (t.parent || "") + "' start_date='" + t.start_date + "' duration='" + t.duration + "' open='" + !!t.open + "' progress='" + t.progress + "' end_date='" + t.end_date + "'><![CDATA[" + t.text + "]]></task>"
                },
                serialize: function () {
                    for (var e = [], i = [], n = t.json.serialize(), r = 0, a = n.data.length; r < a; r++)
                        e.push(this._copyObject(n.data[r]));
                    for (var r = 0, a = n.links.length; r < a; r++)
                        i.push(this._copyLink(n.links[r]));
                    return "<data>" + e.join("") + "<coll_options for='links'>" + i.join("") + "</coll_options></data>"
                }
            }, t.oldxml = {
                parse: function (e, i) {
                    i = t.xml._getXML(e, i, "projects");
                    for (var n = {
                        collections: {
                            links: []
                        }
                    }, r = n.data = [], a = t.ajax.xpath("//task", i), s = 0; s < a.length; s++) {
                        r[s] = t.xml._xmlNodeToJSON(a[s]);
                        var o = a[s].parentNode;
                        "project" == o.tagName ? r[s].parent = "project-" + o.getAttribute("id") : r[s].parent = o.parentNode.getAttribute("id")
                    }
                    a = t.ajax.xpath("//project", i);
                    for (var s = 0; s < a.length; s++) {
                        var l = t.xml._xmlNodeToJSON(a[s], !0);
                        l.id = "project-" + l.id, r.push(l)
                    }
                    for (var s = 0; s < r.length; s++) {
                        var l = r[s];
                        l.start_date = l.startdate || l.est, l.end_date = l.enddate, l.text = l.name, l.duration = l.duration / 8, l.open = 1, l.duration || l.end_date || (l.duration = 1), l.predecessortasks && n.collections.links.push({
                            target: l.id,
                            source: l.predecessortasks,
                            type: t.config.links.finish_to_start
                        })
                    }
                    return n
                },
                serialize: function () {
                    t.message("Serialization to 'old XML' is not implemented")
                }
            }, t.serverList = function (t, e) {
                return e ? this.serverList[t] = e.slice(0) : this.serverList[t] || (this.serverList[t] = []), this.serverList[t]
            }
        }
    }, function (t, e, i) {
        var n = i(88),
                r = i(91),
                a = i(93),
                s = i(0);
        t.exports = function (t) {
            var e = new n(t),
                    i = new r(e),
                    o = a.create(e, i);
            s.mixin(t, o)
        }
    }, function (t, e, i) {
        function n(t) {
            this.$gantt = t, this._calendars = {}
        }
        var r = i(0),
                a = i(21),
                s = i(89);
        n.prototype = {
            _calendars: {},
            _getDayHoursForMultiple: function (t, e) {
                for (var i = [], n = !0, r = 0, a = !1, s = this.$gantt.date.day_start(new Date(e)), o = 0; o < 24; o++)
                    a = t.reduce(function (t, e) {
                        return t && e._is_work_hour(s)
                    }, !0), a ? (n ? (i[r] = o, i[r + 1] = o + 1, r += 2) : i[r - 1] += 1, n = !1) : n || (n = !0), s = this.$gantt.date.add(s, 1, "hour");
                return i.length || (i = !1), i
            },
            mergeCalendars: function () {
                var t, e = this.createCalendar(),
                        i = [],
                        n = Array.prototype.slice.call(arguments, 0);
                e.worktime.hours = [0, 24], e.worktime.dates = {};
                var r = this.$gantt.date.day_start(new Date(2592e5));
                for (t = 0; t < 7; t++)
                    i = this._getDayHoursForMultiple(n, r), e.worktime.dates[t] = i, r = this.$gantt.date.add(r, 1, "day");
                for (var a = 0; a < n.length; a++)
                    for (var s in n[a].worktime.dates)
                        +s > 1e4 && (i = this._getDayHoursForMultiple(n, new Date(+s)), e.worktime.dates[s] = i);
                return e
            },
            _convertWorktimeSettings: function (t) {
                var e = t.days;
                if (e) {
                    t.dates = t.dates || {};
                    for (var i = 0; i < e.length; i++)
                        t.dates[i] = e[i], e[i] instanceof Array || (t.dates[i] = !!e[i]);
                    delete t.days
                }
                return t
            },
            createCalendar: function (t) {
                var e;
                t || (t = {}), e = t.worktime ? r.copy(t.worktime) : r.copy(t);
                var i = r.copy(this.defaults.fulltime.worktime);
                r.mixin(e, i);
                var n = r.uid(),
                        o = {
                            id: n + "",
                            worktime: this._convertWorktimeSettings(e)
                        },
                        l = new s(this.$gantt, a(this.$gantt));
                return r.mixin(l, o), l._tryChangeCalendarSettings(function () {}) ? l : null
            },
            getCalendar: function (t) {
                return t = t || "global", this.createDefaultCalendars(), this._calendars[t]
            },
            getCalendars: function () {
                var t = [];
                for (var e in this._calendars)
                    t.push(this.getCalendar(e));
                return t
            },
            getTaskCalendar: function (t) {
                var e = this.$gantt.$services.config();
                if (!t)
                    return this.getCalendar();
                if (t[e.calendar_property])
                    return this.getCalendar(t[e.calendar_property]);
                if (e.resource_calendars)
                    for (var i in e.resource_calendars) {
                        var n = e.resource_calendars[i];
                        if (t[i]) {
                            var r = n[t[i]];
                            if (r)
                                return this.getCalendar(r)
                        }
                    }
                return this.getCalendar()
            },
            addCalendar: function (t) {
                if (!(t instanceof s)) {
                    var e = t.id;
                    t = this.createCalendar(t), t.id = e
                }
                var i = this.$gantt.$services.config();
                return t.id = t.id || r.uid(), this._calendars[t.id] = t, i.worktimes || (i.worktimes = {}), i.worktimes[t.id] = t.worktime, t.id
            },
            deleteCalendar: function (t) {
                var e = this.$gantt.$services.config();
                return !!t && (!!this._calendars[t] && (delete this._calendars[t], e.worktimes && e.worktimes[t] && delete e.worktimes[t], !0))
            },
            restoreConfigCalendars: function (t) {
                for (var e in t)
                    if (!this._calendars[e]) {
                        var i = t[e],
                                n = this.createCalendar(i);
                        n.id = e, this.addCalendar(n)
                    }
            },
            defaults: {
                global: {
                    id: "global",
                    worktime: {
                        hours: [8, 17],
                        days: [0, 1, 1, 1, 1, 1, 0]
                    }
                },
                fulltime: {
                    id: "fulltime",
                    worktime: {
                        hours: [0, 24],
                        days: [1, 1, 1, 1, 1, 1, 1]
                    }
                }
            },
            createDefaultCalendars: function () {
                var t = this.$gantt.$services.config();
                this.restoreConfigCalendars(this.defaults), this.restoreConfigCalendars(t.worktimes)
            }
        }, t.exports = n
    }, function (t, e, i) {
        function n(t, e) {
            this.argumentsHelper = e, this.$gantt = t, this._workingUnitsCache = new r
        }
        var r = i(90),
                a = i(0);
        n.prototype = {
            units: ["year", "month", "week", "day", "hour", "minute"],
            _getUnitOrder: function (t) {
                for (var e = 0, i = this.units.length; e < i; e++)
                    if (this.units[e] == t)
                        return e
            },
            _timestamp: function (t) {
                var e = null;
                return t.day || 0 === t.day ? e = t.day : t.date && (e = Date.UTC(t.date.getFullYear(), t.date.getMonth(), t.date.getDate())), e
            },
            _checkIfWorkingUnit: function (t, e, i) {
                return void 0 === i && (i = this._getUnitOrder(e)), void 0 === i || !(i && !this._isWorkTime(t, this.units[i - 1], i - 1)) && (!this["_is_work_" + e] || this["_is_work_" + e](t))
            },
            _is_work_day: function (t) {
                var e = this._getWorkHours(t);
                return e instanceof Array && e.length > 0
            },
            _is_work_hour: function (t) {
                for (var e = this._getWorkHours(t), i = t.getHours(), n = 0; n < e.length; n += 2) {
                    if (void 0 === e[n + 1])
                        return e[n] == i;
                    if (i >= e[n] && i < e[n + 1])
                        return !0
                }
                return !1
            },
            _internDatesPull: {},
            _nextDate: function (t, e, i) {
                return this.$gantt.date.add(t, i, e)
            },
            _getWorkUnitsBetweenGeneric: function (t, e, i, n) {
                var r = this.$gantt.date,
                        a = new Date(t),
                        s = new Date(e);
                n = n || 1;
                var o, l, d = 0,
                        c = null,
                        h = !1;
                o = r[i + "_start"](new Date(a)), o.valueOf() != a.valueOf() && (h = !0);
                var u = !1;
                l = r[i + "_start"](new Date(e)), l.valueOf() != e.valueOf() && (u = !0);
                for (var _ = !1; a.valueOf() < s.valueOf(); )
                    c = this._nextDate(a, i, n), _ = c.valueOf() > s.valueOf(), this._isWorkTime(a, i) && ((h || u && _) && (o = r[i + "_start"](new Date(a)), l = r.add(o, n, i)), h ? (h = !1, c = this._nextDate(o, i, n), d += (l.valueOf() - a.valueOf()) / (l.valueOf() - o.valueOf())) : u && _ ? (u = !1, d += (s.valueOf() - a.valueOf()) / (l.valueOf() - o.valueOf())) : d++), a = c;
                return d
            },
            _getHoursPerDay: function (t) {
                for (var e = this._getWorkHours(t), i = 0, n = 0; n < e.length; n += 2)
                    i += e[n + 1] - e[n] || 0;
                return i
            },
            _getWorkHoursForRange: function (t, e) {
                for (var i = 0, n = new Date(t), r = new Date(e); n.valueOf() < r.valueOf(); )
                    this._isWorkTime(n, "day") && (i += this._getHoursPerDay(n)), n = this._nextDate(n, "day", 1);
                return i
            },
            _getWorkUnitsBetweenHours: function (t, e, i, n) {
                var r = new Date(t),
                        a = new Date(e);
                n = n || 1;
                var s = new Date(r),
                        o = this.$gantt.date.add(this.$gantt.date.day_start(new Date(r)), 1, "day");
                if (a.valueOf() <= o.valueOf())
                    return this._getWorkUnitsBetweenGeneric(t, e, i, n);
                var l = this.$gantt.date.day_start(new Date(a)),
                        d = a,
                        c = this._getWorkUnitsBetweenGeneric(s, o, i, n),
                        h = this._getWorkUnitsBetweenGeneric(l, d, i, n),
                        u = this._getWorkHoursForRange(o, l);
                return u = u / n + c + h
            },
            _getCalendar: function () {
                return this.worktime
            },
            _setCalendar: function (t) {
                this.worktime = t
            },
            _tryChangeCalendarSettings: function (t) {
                var e = JSON.stringify(this._getCalendar());
                return t(), !this._isEmptyCalendar(this._getCalendar()) || (null, this._setCalendar(JSON.parse(e)), this._workingUnitsCache.clear(), !1)
            },
            _isEmptyCalendar: function (t) {
                var e = !1,
                        i = [],
                        n = !0;
                for (var r in t.dates)
                    e |= !!t.dates[r], i.push(r);
                for (var a = [], r = 0; r < i.length; r++)
                    i[r] < 10 && a.push(i[r]);
                a.sort();
                for (var r = 0; r < 7; r++)
                    a[r] != r && (n = !1);
                return n ? !e : !(e || t.hours)
            },
            getWorkHours: function () {
                var t = this.argumentsHelper.getWorkHoursArguments.apply(this.argumentsHelper, arguments);
                return this._getWorkHours(t.date)
            },
            _getWorkHours: function (t) {
                var e = this._timestamp({
                    date: t
                }),
                        i = !0,
                        n = this._getCalendar();
                return void 0 !== n.dates[e] ? i = n.dates[e] : void 0 !== n.dates[t.getDay()] && (i = n.dates[t.getDay()]), !0 === i ? n.hours : i || []
            },
            setWorkTime: function (t) {
                return this._tryChangeCalendarSettings(a.bind(function () {
                    var e = void 0 === t.hours || t.hours,
                            i = this._timestamp(t);
                    null !== i ? this._getCalendar().dates[i] = e : this._getCalendar().hours = e, this._workingUnitsCache.clear()
                }, this))
            },
            unsetWorkTime: function (t) {
                return this._tryChangeCalendarSettings(a.bind(function () {
                    if (t) {
                        var e = this._timestamp(t);
                        null !== e && delete this._getCalendar().dates[e]
                    } else
                        this.reset_calendar();
                    this._workingUnitsCache.clear()
                }, this))
            },
            _isWorkTime: function (t, e, i) {
                var n = this._workingUnitsCache.get(e, t);
                return -1 == n && (n = this._checkIfWorkingUnit(t, e, i), this._workingUnitsCache.put(e, t, n)), n
            },
            isWorkTime: function () {
                var t = this.argumentsHelper.isWorkTimeArguments.apply(this.argumentsHelper, arguments);
                return this._isWorkTime(t.date, t.unit)
            },
            calculateDuration: function () {
                var t = this.argumentsHelper.getDurationArguments.apply(this.argumentsHelper, arguments);
                if (!t.unit)
                    return !1;
                var e = 0;
                return e = "hour" == t.unit ? this._getWorkUnitsBetweenHours(t.start_date, t.end_date, t.unit, t.step) : this._getWorkUnitsBetweenGeneric(t.start_date, t.end_date, t.unit, t.step), Math.round(e)
            },
            hasDuration: function () {
                var t = this.argumentsHelper.getDurationArguments.apply(this.argumentsHelper, arguments),
                        e = t.start_date,
                        i = t.end_date,
                        n = t.unit,
                        r = t.step;
                if (!n)
                    return !1;
                var a = new Date(e),
                        s = new Date(i);
                for (r = r || 1; a.valueOf() < s.valueOf(); ) {
                    if (this._isWorkTime(a, n))
                        return !0;
                    a = this._nextDate(a, n, r)
                }
                return !1
            },
            calculateEndDate: function () {
                var t = this.argumentsHelper.calculateEndDateArguments.apply(this.argumentsHelper, arguments),
                        e = t.start_date,
                        i = t.duration,
                        n = t.unit,
                        r = t.step,
                        a = t.duration >= 0 ? 1 : -1;
                return this._calculateEndDate(e, i, n, r * a)
            },
            _calculateEndDate: function (t, e, i, n) {
                if (!i)
                    return !1;
                var r = new Date(t),
                        a = 0;
                for (n = n || 1, e = Math.abs(1 * e); a < e; ) {
                    var s = this._nextDate(r, i, n);
                    this._isWorkTime(n > 0 ? new Date(s.valueOf() - 1) : new Date(s.valueOf() + 1), i) && a++, r = s
                }
                return r
            },
            getClosestWorkTime: function () {
                var t = this.argumentsHelper.getClosestWorkTimeArguments.apply(this.argumentsHelper, arguments);
                return this._getClosestWorkTime(t)
            },
            _getClosestWorkTime: function (t) {
                if (this._isWorkTime(t.date, t.unit))
                    return t.date;
                var e = t.unit,
                        i = this.$gantt.date[e + "_start"](t.date),
                        n = new Date(i),
                        r = new Date(i),
                        a = !0,
                        s = 0,
                        o = "any" == t.dir || !t.dir,
                        l = 1;
                "past" == t.dir && (l = -1);
                for (var d = this._getUnitOrder(e), c = this.units[d - 1]; !this._isWorkTime(i, e); ) {
                    if (c && !this._isWorkTime(i, c)) {
                        var h = this.$gantt.copy(t);
                        h.date = i, h.unit = c, i = this._getClosestWorkTime(h)
                    }
                    o && (i = a ? n : r, l *= -1);
                    var u = i.getTimezoneOffset();
                    if (i = this.$gantt.date.add(i, l, e), i = this.$gantt._correct_dst_change(i, u, l, e), this.$gantt.date[e + "_start"] && (i = this.$gantt.date[e + "_start"](i)), o && (a ? n = i : r = i), a = !a, ++s > 3e3)
                        return null, !1
                }
                return i != r && "past" != t.dir || (i = this.$gantt.date.add(i, 1, e)), i
            }
        }, t.exports = n
    }, function (t, e) {
        function i() {
            this._cache = {}
        }
        i.prototype = {
            get: function (t, e) {
                var i = -1,
                        n = this._cache;
                if (n && n[t]) {
                    var r = n[t],
                            a = e.getTime();
                    void 0 !== r[a] && (i = r[a])
                }
                return i
            },
            put: function (t, e, i) {
                if (!t || !e)
                    return !1;
                var n = this._cache,
                        r = e.getTime();
                return i = !!i, !!n && (n[t] || (n[t] = {}), n[t][r] = i, !0)
            },
            clear: function () {
                this._cache = {}
            }
        }, t.exports = i
    }, function (t, e, i) {
        function n(t) {
            this.$gantt = t.$gantt, this.argumentsHelper = r(this.$gantt), this.calendarManager = t, this.$disabledCalendar = new a(this.$gantt, this.argumentsHelper)
        }
        var r = i(21),
                a = i(92);
        n.prototype = {
            _getCalendar: function (t) {
                var e;
                if (this.$gantt.$services.config().work_time) {
                    var i = this.calendarManager;
                    t.task ? e = i.getTaskCalendar(t.task) : t.id ? e = i.getTaskCalendar(t) : t.calendar && (e = t.calendar), e || (e = i.getTaskCalendar())
                } else
                    e = this.$disabledCalendar;
                return e
            },
            getWorkHours: function (t) {
                return t = this.argumentsHelper.getWorkHoursArguments.apply(this.argumentsHelper, arguments), this._getCalendar(t).getWorkHours(t.date)
            },
            setWorkTime: function (t, e) {
                return t = this.argumentsHelper.setWorkTimeArguments.apply(this.argumentsHelper, arguments), e || (e = this.calendarManager.getCalendar()), e.setWorkTime(t)
            },
            unsetWorkTime: function (t, e) {
                return t = this.argumentsHelper.unsetWorkTimeArguments.apply(this.argumentsHelper, arguments), e || (e = this.calendarManager.getCalendar()), e.unsetWorkTime(t)
            },
            isWorkTime: function (t, e, i, n) {
                var r = this.argumentsHelper.isWorkTimeArguments.apply(this.argumentsHelper, arguments);
                return n = this._getCalendar(r), n.isWorkTime(r)
            },
            getClosestWorkTime: function (t) {
                return t = this.argumentsHelper.getClosestWorkTimeArguments.apply(this.argumentsHelper, arguments), this._getCalendar(t).getClosestWorkTime(t)
            },
            calculateDuration: function () {
                var t = this.argumentsHelper.getDurationArguments.apply(this.argumentsHelper, arguments);
                return this._getCalendar(t).calculateDuration(t)
            },
            hasDuration: function () {
                var t = this.argumentsHelper.hasDurationArguments.apply(this.argumentsHelper, arguments);
                return this._getCalendar(t).hasDuration(t)
            },
            calculateEndDate: function (t) {
                var t = this.argumentsHelper.calculateEndDateArguments.apply(this.argumentsHelper, arguments);
                return this._getCalendar(t).calculateEndDate(t)
            }
        }, t.exports = n
    }, function (t, e) {
        function i(t, e) {
            this.argumentsHelper = e, this.$gantt = t
        }
        i.prototype = {
            getWorkHours: function () {
                return [0, 24]
            },
            setWorkTime: function () {
                return !0
            },
            unsetWorkTime: function () {
                return !0
            },
            isWorkTime: function () {
                return !0
            },
            getClosestWorkTime: function (t) {
                return this.argumentsHelper.getClosestWorkTimeArguments.apply(this.argumentsHelper, arguments).date
            },
            calculateDuration: function () {
                var t = this.argumentsHelper.getDurationArguments.apply(this.argumentsHelper, arguments),
                        e = t.start_date,
                        i = t.end_date,
                        n = t.unit,
                        r = t.step;
                return this._calculateDuration(e, i, n, r)
            },
            _calculateDuration: function (t, e, i, n) {
                var r = this.$gantt.date,
                        a = {
                            week: 6048e5,
                            day: 864e5,
                            hour: 36e5,
                            minute: 6e4
                        },
                        s = 0;
                if (a[i])
                    s = Math.round((e - t) / (n * a[i]));
                else {
                    for (var o = new Date(t), l = new Date(e); o.valueOf() < l.valueOf(); )
                        s += 1, o = r.add(o, n, i);
                    o.valueOf() != e.valueOf() && (s += (l - o) / (r.add(o, n, i) - o))
                }
                return Math.round(s)
            },
            hasDuration: function () {
                var t = this.argumentsHelper.getDurationArguments.apply(this.argumentsHelper, arguments),
                        e = t.start_date,
                        i = t.end_date,
                        n = t.unit;
                t.step;
                return !!n && (e = new Date(e), i = new Date(i), e.valueOf() < i.valueOf())
            },
            calculateEndDate: function () {
                var t = this.argumentsHelper.calculateEndDateArguments.apply(this.argumentsHelper, arguments),
                        e = t.start_date,
                        i = t.duration,
                        n = t.unit,
                        r = t.step;
                return this.$gantt.date.add(e, r * i, n)
            }
        }, t.exports = i
    }, function (t, e, i) {
        var n = i(0),
                r = function (t, e) {
                    return {
                        getWorkHours: function (t) {
                            return e.getWorkHours(t)
                        },
                        setWorkTime: function (t) {
                            return e.setWorkTime(t)
                        },
                        unsetWorkTime: function (t) {
                            e.unsetWorkTime(t)
                        },
                        isWorkTime: function (t, i, n) {
                            return e.isWorkTime(t, i, n)
                        },
                        getClosestWorkTime: function (t) {
                            return e.getClosestWorkTime(t)
                        },
                        calculateDuration: function (t, i, n) {
                            return e.calculateDuration(t, i, n)
                        },
                        _hasDuration: function (t, i, n) {
                            return e.hasDuration(t, i, n)
                        },
                        calculateEndDate: function (t, i, n, r) {
                            return e.calculateEndDate(t, i, n, r)
                        },
                        createCalendar: n.bind(t.createCalendar, t),
                        addCalendar: n.bind(t.addCalendar, t),
                        getCalendar: n.bind(t.getCalendar, t),
                        getCalendars: n.bind(t.getCalendars, t),
                        getTaskCalendar: n.bind(t.getTaskCalendar, t),
                        deleteCalendar: n.bind(t.deleteCalendar, t)
                    }
                };
        t.exports = {
            create: r
        }
    }, function (t, e, i) {
        var n = i(4);
        i(7);
        t.exports = function (t) {
            t.isUnscheduledTask = function (t) {
                return !!t.unscheduled || !t.start_date
            }, t._isAllowedUnscheduledTask = function (e) {
                return !(!e.unscheduled || !t.config.show_unscheduled)
            }, t.isTaskVisible = function (e) {
                if (!this.isTaskExists(e))
                    return !1;
                var i = this.getTask(e);
                this.getTaskType(i.type);
                return !!(+i.start_date <= +this._max_date && +i.end_date >= +this._min_date || t._isAllowedUnscheduledTask(i)) && !!(t.getGlobalTaskIndex(e) >= 0)
            }, t._defaultTaskDate = function (e, i) {
                var n = !(!i || i == this.config.root_id) && this.getTask(i),
                        r = "";
                if (n)
                    r = n.start_date;
                else {
                    var a = this.getTaskByIndex(0);
                    r = a ? a.start_date ? a.start_date : a.end_date ? this.calculateEndDate({
                        start_date: a.end_date,
                        duration: -this.config.duration_step
                    }) : "" : this.config.start_date || this.getState().min_date
                }
                return null, new Date(r)
            }, t._set_default_task_timing = function (e) {
                e.start_date = e.start_date || t._defaultTaskDate(e, this.getParent(e)), e.duration = e.duration || this.config.duration_step, e.end_date = e.end_date || this.calculateEndDate(e)
            }, t.createTask = function (e, i, n) {
                if (e = e || {}, t.defined(e.id) || (e.id = t.uid()), e.start_date || (e.start_date = t._defaultTaskDate(e, i)), void 0 === e.text && (e.text = t.locale.labels.new_task), void 0 === e.duration && (e.duration = 1), i) {
                    this.setParent(e, i, !0);
                    this.getTask(i).$open = !0
                }
                return this.callEvent("onTaskCreated", [e]) ? (this.config.details_on_create ? (e.$new = !0, this.silent(function () {
                    t.$data.tasksStore.addItem(e, n)
                }), this.selectTask(e.id), this.refreshData(), this.showLightbox(e.id)) : this.addTask(e, i, n) && (this.showTask(e.id), this.selectTask(e.id)), e.id) : null
            }, t._update_flags = function (e, i) {
                var n = t.$data.tasksStore;
                void 0 === e ? (this._lightbox_id = null, n.silent(function () {
                    n.unselect()
                }), this._tasks_dnd && this._tasks_dnd.drag && (this._tasks_dnd.drag.id = null)) : (this._lightbox_id == e && (this._lightbox_id = i), n.getSelectedId() == e && n.silent(function () {
                    n.unselect(e), n.select(i)
                }), this._tasks_dnd && this._tasks_dnd.drag && this._tasks_dnd.drag.id == e && (this._tasks_dnd.drag.id = i))
            }, t._get_task_timing_mode = function (t, e) {
                var i = this.getTaskType(t.type),
                        n = {
                            type: i,
                            $no_start: !1,
                            $no_end: !1
                        };
                return e || i != t.$rendered_type ? (i == this.config.types.project ? n.$no_end = n.$no_start = !0 : i != this.config.types.milestone && (n.$no_end = !(t.end_date || t.duration), n.$no_start = !t.start_date, this._isAllowedUnscheduledTask(t) && (n.$no_end = n.$no_start = !1)), n) : (n.$no_start = t.$no_start, n.$no_end = t.$no_end, n)
            }, t._init_task_timing = function (e) {
                var i = t._get_task_timing_mode(e, !0),
                        n = e.$rendered_type != i.type,
                        r = i.type;
                n && (e.$no_start = i.$no_start, e.$no_end = i.$no_end, e.$rendered_type = i.type), n && r != this.config.types.milestone && r == this.config.types.project && this._set_default_task_timing(e), r == this.config.types.milestone && (e.end_date = e.start_date), e.start_date && e.end_date && (e.duration = this.calculateDuration(e)), e.duration = e.duration || 0
            }, t.isSummaryTask = function (e) {
                var i = t._get_task_timing_mode(e);
                return !(!i.$no_end && !i.$no_start)
            }, t.resetProjectDates = function (t) {
                var e = this._get_task_timing_mode(t);
                if (e.$no_end || e.$no_start) {
                    var i = this.getSubtaskDates(t.id);
                    this._assign_project_dates(t, i.start_date, i.end_date)
                }
            }, t.getSubtaskDuration = function (e) {
                var i = 0,
                        n = void 0 !== e ? e : t.config.root_id;
                return this.eachTask(function (e) {
                    this.getTaskType(e.type) == t.config.types.project || this.isUnscheduledTask(e) || (i += e.duration)
                }, n), i
            }, t.getSubtaskDates = function (e) {
                var i = null,
                        n = null,
                        r = void 0 !== e ? e : t.config.root_id;
                return this.eachTask(function (e) {
                    this.getTaskType(e.type) == t.config.types.project || this.isUnscheduledTask(e) || (e.start_date && !e.$no_start && (!i || i > e.start_date.valueOf()) && (i = e.start_date.valueOf()), e.end_date && !e.$no_end && (!n || n < e.end_date.valueOf()) && (n = e.end_date.valueOf()))
                }, r), {
                    start_date: i ? new Date(i) : null,
                    end_date: n ? new Date(n) : null
                }
            }, t._assign_project_dates = function (t, e, i) {
                var n = this._get_task_timing_mode(t);
                n.$no_start && (t.start_date = e && e != 1 / 0 ? new Date(e) : this._defaultTaskDate(t, this.getParent(t))), n.$no_end && (t.end_date = i && i != -1 / 0 ? new Date(i) : this.calculateEndDate({
                    start_date: t.start_date,
                    duration: this.config.duration_step,
                    task: t
                })), (n.$no_start || n.$no_end) && this._init_task_timing(t)
            }, t._update_parents = function (e, i) {
                if (e) {
                    var n = this.getTask(e),
                            r = this.getParent(n),
                            a = this._get_task_timing_mode(n),
                            s = !0;
                    if (a.$no_start || a.$no_end) {
                        var o = n.start_date.valueOf(),
                                l = n.end_date.valueOf();
                        t.resetProjectDates(n), o == n.start_date.valueOf() && l == n.end_date.valueOf() && (s = !1), s && !i && this.refreshTask(n.id, !0)
                    }
                    s && r && this.isTaskExists(r) && this._update_parents(r, i)
                }
            }, t.roundDate = function (e) {
                var i = t.getScale();
                n.isDate(e) && (e = {
                    date: e,
                    unit: i ? i.unit : t.config.duration_unit,
                    step: i ? i.step : t.config.duration_step
                });
                var r = e.date,
                        a = e.step,
                        s = e.unit;
                if (!i)
                    return r;
                var o, l, d;
                if (s == i.unit && a == i.step && +r >= +i.min_date && +r <= +i.max_date)
                    d = Math.floor(t.columnIndexByDate(r)), i.trace_x[d] || (d -= 1, i.rtl && (d = 0)), l = new Date(i.trace_x[d]), o = t.date.add(l, a, s);
                else {
                    for (d = Math.floor(t.columnIndexByDate(r)), o = t.date[s + "_start"](new Date(i.min_date)), i.trace_x[d] && (o = t.date[s + "_start"](i.trace_x[d])); +o < +r; ) {
                        o = t.date[s + "_start"](t.date.add(o, a, s));
                        var c = o.getTimezoneOffset();
                        o = t._correct_dst_change(o, c, o, s), t.date[s + "_start"] && (o = t.date[s + "_start"](o))
                    }
                    l = t.date.add(o, -1 * a, s)
                }
                return e.dir && "future" == e.dir ? o : e.dir && "past" == e.dir ? l : Math.abs(r - l) < Math.abs(o - r) ? l : o
            }, t.correctTaskWorkTime = function (e) {
                t.config.work_time && t.config.correct_work_time && (this.isWorkTime(e.start_date, void 0, e) ? this.isWorkTime(new Date(+e.end_date - 1), void 0, e) || (e.end_date = this.calculateEndDate(e)) : (e.start_date = this.getClosestWorkTime({
                    date: e.start_date,
                    dir: "future",
                    task: e
                }), e.end_date = this.calculateEndDate(e)))
            }, t.attachEvent("onBeforeTaskUpdate", function (e, i) {
                return t._init_task_timing(i), !0
            }), t.attachEvent("onBeforeTaskAdd", function (e, i) {
                return t._init_task_timing(i), !0
            })
        }
    }, function (t, e, i) {
        t.exports = function (t) {
            function e(t) {
                var e = t.$ui.getView("timeline");
                return !(!e || !e.isVisible())
            }

            function n(t) {
                for (var e in this.config.types)
                    if (this.config.types[e] == t)
                        return e;
                return "task"
            }
            var r = i(1),
                    a = i(7);
            t._lightbox_methods = {}, t._lightbox_template = "<div class='gantt_cal_ltitle'><span class='gantt_mark'>&nbsp;</span><span class='gantt_time'></span><span class='gantt_title'></span></div><div class='gantt_cal_larea'></div>", t.$services.getService("state").registerProvider("lightbox", function () {
                return {
                    lightbox: t._lightbox_id
                }
            }), t.showLightbox = function (e) {
                if (e && !t.isReadonly(this.getTask(e)) && this.callEvent("onBeforeLightbox", [e])) {
                    var i = this.getTask(e),
                            n = this.getLightbox(this.getTaskType(i.type), i);
                    //this._center_lightbox(n), this.showCover(), this._fill_lightbox(e, n), this._waiAria.lightboxVisibleAttr(n), this.callEvent("onLightbox", [e])
                }
            }, t._get_timepicker_step = function () {
                if (this.config.round_dnd_dates) {
                    var i;
                    if (e(this)) {
                        var n = t.getScale();
                        i = a.getSecondsInUnit(n.unit) * n.step / 60
                    }
                    return (!i || i >= 1440) && (i = this.config.time_step), i
                }
                return this.config.time_step
            }, t.getLabel = function (t, e) {
                for (var i = this._get_typed_lightbox_config(), n = 0; n < i.length; n++)
                    if (i[n].map_to == t)
                        for (var r = i[n].options, a = 0; a < r.length; a++)
                            if (r[a].key == e)
                                return r[a].label;
                return ""
            }, t.updateCollection = function (e, i) {
                i = i.slice(0);
                var n = t.serverList(e);
                if (!n)
                    return !1;
                n.splice(0, n.length), n.push.apply(n, i || []), t.resetLightbox()
            }, t.getLightboxType = function () {
                return this.getTaskType(this._lightbox_type)
            }, t.getLightbox = function (e, i) {
                onModalAddGanttDialog(e, i);
            }, t._render_sections = function (t) {
                for (var e = "", i = 0; i < t.length; i++) {
                    var n = this.form_blocks[t[i].type];
                    if (n) {
                        t[i].id = "area_" + this.uid();
                        var r = t[i].hidden ? " style='display:none'" : "",
                                a = "";
                        t[i].button && (a = "<div class='gantt_custom_button' index='" + i + "'><div class='gantt_custom_button_" + t[i].button + "'></div><div class='gantt_custom_button_label'>" + this.locale.labels["button_" + t[i].button] + "</div></div>"), this.config.wide_form && (e += "<div class='gantt_wrap_section' " + r + ">"), e += "<div id='" + t[i].id + "' class='gantt_cal_lsection'><label>" + a + this.locale.labels["section_" + t[i].name] + "</label></div>" + n.render.call(this, t[i]), e += "</div>"
                    }
                }
                return e
            }, t.resizeLightbox = function () {
                var t = this._lightbox;
                if (t) {
                    var e = t.childNodes[1];
                    e.style.height = "0px", e.style.height = e.scrollHeight + "px", t.style.height = e.scrollHeight + this.config.lightbox_additional_height + "px", e.style.height = e.scrollHeight + "px"
                }
            }, t._center_lightbox = function (t) {
                if (t) {
                    t.style.display = "block";
                    var e = window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop,
                            i = window.pageXOffset || document.body.scrollLeft || document.documentElement.scrollLeft,
                            n = window.innerHeight || document.documentElement.clientHeight;
                    t.style.top = e ? Math.round(e + Math.max((n - t.offsetHeight) / 2, 0)) + "px" : Math.round(Math.max((n - t.offsetHeight) / 2, 0) + 9) + "px", document.documentElement.scrollWidth > document.body.offsetWidth ? t.style.left = Math.round(i + (document.body.offsetWidth - t.offsetWidth) / 2) + "px" : t.style.left = Math.round((document.body.offsetWidth - t.offsetWidth) / 2) + "px"
                }
            }, t.showCover = function () {
                if (!this._cover) {
                    this._cover = document.createElement("DIV"), this._cover.className = "gantt_cal_cover";
                    var t = void 0 !== document.height ? document.height : document.body.offsetHeight,
                            e = document.documentElement ? document.documentElement.scrollHeight : 0;
                    this._cover.style.height = Math.max(t, e) + "px", document.body.appendChild(this._cover)
                }
            }, t._init_lightbox_events = function () {
                t.lightbox_events = {}, t.lightbox_events.gantt_save_btn = function (e) {
                    t._save_lightbox()
                }, t.lightbox_events.gantt_delete_btn = function (e) {
                    t.callEvent("onLightboxDelete", [t._lightbox_id]) && (t.isTaskExists(t._lightbox_id) ? t.$click.buttons.delete(t._lightbox_id) : t.hideLightbox())
                }, t.lightbox_events.gantt_cancel_btn = function (e) {
                    t._cancel_lightbox()
                }, t.lightbox_events.default = function (e, i) {
                    if (i.getAttribute("dhx_button"))
                        t.callEvent("onLightboxButton", [i.className, i, e]);
                    else {
                        var n, a, s, o = r.getClassName(i);
                        if (-1 != o.indexOf("gantt_custom_button"))
                            if (-1 != o.indexOf("gantt_custom_button_"))
                                for (n = i.parentNode.getAttribute("index"), s = i; s && - 1 == r.getClassName(s).indexOf("gantt_cal_lsection"); )
                                    s = s.parentNode;
                            else
                                n = i.getAttribute("index"), s = i.parentNode, i = i.firstChild;
                        var l = t._get_typed_lightbox_config();
                        n && (n *= 1, a = t.form_blocks[l[1 * n].type], a.button_click(n, i, s, s.nextSibling))
                    }
                }, this.event(t.getLightbox(), "click", function (e) {
                    e = e || window.event;
                    var i = e.target ? e.target : e.srcElement,
                            n = r.getClassName(i);
                    return n || (i = i.previousSibling, n = r.getClassName(i)), i && n && 0 === n.indexOf("gantt_btn_set") && (i = i.firstChild, n = r.getClassName(i)), !(!i || !n) && (t.defined(t.lightbox_events[i.className]) ? t.lightbox_events[i.className] : t.lightbox_events.default)(e, i)
                }), t.getLightbox().onkeydown = function (e) {
                    var i = e || window.event,
                            n = e.target || e.srcElement,
                            a = !!(r.getClassName(n).indexOf("gantt_btn_set") > -1);
                    switch ((e || i).keyCode) {
                        case 32:
                            if ((e || i).shiftKey)
                                return;
                            a && n.click && n.click();
                            break;
                        case t.keys.edit_save:
                            if ((e || i).shiftKey)
                                return;
                            a && n.click ? n.click() : t._save_lightbox();
                            break;
                        case t.keys.edit_cancel:
                            t._cancel_lightbox()
                    }
                }
            }, t._cancel_lightbox = function () {
                var e = this.getLightboxValues();
                this.callEvent("onLightboxCancel", [this._lightbox_id, e.$new]), t.isTaskExists(e.id) && e.$new && this.silent(function () {
                    t.$data.tasksStore.removeItem(e.id), t._update_flags(e.id, null)
                }), this.refreshData(), this.hideLightbox()
            }, t._save_lightbox = function () {
                var t = this.getLightboxValues();
                this.callEvent("onLightboxSave", [this._lightbox_id, t, !!t.$new]) && (t.$new ? (delete t.$new, this.addTask(t)) : this.isTaskExists(t.id) && (this.mixin(this.getTask(t.id), t, !0), this.refreshTask(t.id), this.updateTask(t.id)), this.refreshData(), this.hideLightbox())
            }, t._resolve_default_mapping = function (t) {
                var e = t.map_to;
                return {
                    time: !0,
                    time_optional: !0,
                    duration: !0,
                    duration_optional: !0
                }[t.type] && ("auto" == t.map_to ? e = {
                    start_date: "start_date",
                    end_date: "end_date",
                    duration: "duration"
                } : "string" == typeof t.map_to && (e = {
                    start_date: t.map_to
                })), e
            }, t.getLightboxValues = function () {
                var e = {};
                t.isTaskExists(this._lightbox_id) && (e = this.mixin({}, this.getTask(this._lightbox_id)));
                for (var i = this._get_typed_lightbox_config(), n = 0; n < i.length; n++) {
                    var r = document.getElementById(i[n].id);
                    r = r ? r.nextSibling : r;
                    var a = this.form_blocks[i[n].type];
                    if (a) {
                        var s = a.get_value.call(this, r, e, i[n]),
                                o = t._resolve_default_mapping(i[n]);
                        if ("string" == typeof o && "auto" != o)
                            e[o] = s;
                        else if ("object" == typeof o)
                            for (var l in o)
                                o[l] && (e[o[l]] = s[l])
                    }
                }
                return e
            }, t.hideLightbox = function () {
                var t = this.getLightbox();
                t && (t.style.display = "none"), this._waiAria.lightboxHiddenAttr(t), this._lightbox_id = null, this.hideCover(), this.callEvent("onAfterLightbox", [])
            }, t.hideCover = function () {
                this._cover && this._cover.parentNode.removeChild(this._cover), this._cover = null
            }, t.resetLightbox = function () {
                t._lightbox && !t._custom_lightbox && t._lightbox.parentNode.removeChild(t._lightbox), t._lightbox = null
            }, t._set_lightbox_values = function (e, i) {
                var n = e,
                        r = i.getElementsByTagName("span"),
                        a = [];
                t.templates.lightbox_header ? (a.push(""), a.push(t.templates.lightbox_header(n.start_date, n.end_date, n)), r[1].innerHTML = "", r[2].innerHTML = t.templates.lightbox_header(n.start_date, n.end_date, n)) : (a.push(this.templates.task_time(n.start_date, n.end_date, n)), a.push((this.templates.task_text(n.start_date, n.end_date, n) || "").substr(0, 70)), r[1].innerHTML = this.templates.task_time(n.start_date, n.end_date, n), r[2].innerHTML = (this.templates.task_text(n.start_date, n.end_date, n) || "").substr(0, 70)), r[1].innerHTML = a[0], r[2].innerHTML = a[1], t._waiAria.lightboxHeader(i, a.join(" "));
                for (var s = this._get_typed_lightbox_config(this.getLightboxType()), o = 0; o < s.length; o++) {
                    var l = s[o];
                    if (this.form_blocks[l.type]) {
                        var d = document.getElementById(l.id).nextSibling,
                                c = this.form_blocks[l.type],
                                h = t._resolve_default_mapping(s[o]),
                                u = this.defined(n[h]) ? n[h] : l.default_value;
                        c.set_value.call(t, d, u, n, l), l.focus && c.focus.call(t, d)
                    }
                }
                e.id && (t._lightbox_id = e.id)
            }, t._fill_lightbox = function (t, e) {
                var i = this.getTask(t);
                this._set_lightbox_values(i, e)
            }, t.getLightboxSection = function (e) {
                var i = this._get_typed_lightbox_config(),
                        n = 0;
                for (n; n < i.length && i[n].name != e; n++)
                    ;
                var r = i[n];
                if (!r)
                    return null;
                this._lightbox || this.getLightbox();
                var a = document.getElementById(r.id),
                        s = a.nextSibling,
                        o = {
                            section: r,
                            header: a,
                            node: s,
                            getValue: function (e) {
                                return t.form_blocks[r.type].get_value.call(t, s, e || {}, r)
                            },
                            setValue: function (e, i) {
                                return t.form_blocks[r.type].set_value.call(t, s, e, i || {}, r)
                            }
                        },
                        l = this._lightbox_methods["get_" + r.type + "_control"];
                return l ? l(o) : o
            }, t._lightbox_methods.get_template_control = function (t) {
                return t.control = t.node, t
            }, t._lightbox_methods.get_select_control = function (t) {
                return t.control = t.node.getElementsByTagName("select")[0], t
            }, t._lightbox_methods.get_textarea_control = function (t) {
                return t.control = t.node.getElementsByTagName("textarea")[0], t
            }, t._lightbox_methods.get_time_control = function (t) {
                return t.control = t.node.getElementsByTagName("select"), t
            }, t._init_dnd_events = function () {
                this.event(document.body, "mousemove", t._move_while_dnd), this.event(document.body, "mouseup", t._finish_dnd), t._init_dnd_events = function () {}
            }, t._move_while_dnd = function (e) {
                if (t._dnd_start_lb) {
                    document.gantt_unselectable || (document.body.className += " gantt_unselectable", document.gantt_unselectable = !0);
                    var i = t.getLightbox(),
                            n = e && e.target ? [e.pageX, e.pageY] : [event.clientX, event.clientY];
                    i.style.top = t._lb_start[1] + n[1] - t._dnd_start_lb[1] + "px", i.style.left = t._lb_start[0] + n[0] - t._dnd_start_lb[0] + "px"
                }
            }, t._ready_to_dnd = function (e) {
                var i = t.getLightbox();
                t._lb_start = [parseInt(i.style.left, 10), parseInt(i.style.top, 10)], t._dnd_start_lb = e && e.target ? [e.pageX, e.pageY] : [event.clientX, event.clientY]
            }, t._finish_dnd = function () {
                t._lb_start && (t._lb_start = t._dnd_start_lb = !1, document.body.className = document.body.className.replace(" gantt_unselectable", ""), document.gantt_unselectable = !1)
            }, t._focus = function (e, i) {
                if (e && e.focus)
                    if (t.config.touch)
                        ;
                    else
                        try {
                            i && e.select && e.select(), e.focus()
                        } catch (t) {
                        }
            }, t.form_blocks = {
                getTimePicker: function (e, i) {
                    var n = e.time_format;
                    if (!n) {
                        var n = ["%d", "%m", "%Y"],
                                r = t.getScale(),
                                s = r ? r.unit : t.config.duration_unit;
                        a.getSecondsInUnit(s) < a.getSecondsInUnit("day") && n.push("%H:%i")
                    }
                    e._time_format_order = {
                        size: 0
                    };
                    var o = this.config,
                            l = this.date.date_part(new Date(t._min_date.valueOf())),
                            d = 1440,
                            c = 0;
                    t.config.limit_time_select && (d = 60 * o.last_hour + 1, c = 60 * o.first_hour, l.setHours(o.first_hour));
                    for (var h = "", u = 0; u < n.length; u++) {
                        var _ = n[u];
                        u > 0 && (h += " ");
                        var g = "";
                        switch (_) {
                            case "%Y":
                                e._time_format_order[2] = u, e._time_format_order.size++;
                                var f, p, v, m;
                                e.year_range && (isNaN(e.year_range) ? e.year_range.push && (v = e.year_range[0], m = e.year_range[1]) : f = e.year_range), f = f || 10, p = p || Math.floor(f / 2), v = v || l.getFullYear() - p, m = m || v + f;
                                for (var k = v; k < m; k++)
                                    g += "<option value='" + k + "'>" + k + "</option>";
                                break;
                            case "%m":
                                e._time_format_order[1] = u, e._time_format_order.size++;
                                for (var k = 0; k < 12; k++)
                                    g += "<option value='" + k + "'>" + this.locale.date.month_full[k] + "</option>";
                                break;
                            case "%d":
                                e._time_format_order[0] = u, e._time_format_order.size++;
                                for (var k = 1; k < 32; k++)
                                    g += "<option value='" + k + "'>" + k + "</option>";
                                break;
                            case "%H:%i":
                                e._time_format_order[3] = u, e._time_format_order.size++;
                                var k = c,
                                        y = l.getDate();
                                for (e._time_values = []; k < d; )
                                    g += "<option value='" + k + "'>" + this.templates.time_picker(l) + "</option>", e._time_values.push(k), l.setTime(l.valueOf() + 60 * this._get_timepicker_step() * 1e3), k = 24 * (l.getDate() != y ? 1 : 0) * 60 + 60 * l.getHours() + l.getMinutes()
                        }
                        if (g) {
                            var b = t._waiAria.lightboxSelectAttrString(_);
                            h += "<select " + (e.readonly ? "disabled='disabled'" : "") + (i ? " style='display:none' " : "") + b + ">" + g + "</select>"
                        }
                    }
                    return h
                },
                _fill_lightbox_select: function (e, i, n, r, a) {
                    if (e[i + r[0]].value = n.getDate(), e[i + r[1]].value = n.getMonth(), e[i + r[2]].value = n.getFullYear(), t.defined(r[3])) {
                        var s = 60 * n.getHours() + n.getMinutes();
                        s = Math.round(s / t._get_timepicker_step()) * t._get_timepicker_step();
                        var o = e[i + r[3]];
                        o.value = s, o.setAttribute("data-value", s)
                    }
                },
                template: {
                    render: function (t) {
                        return "<div class='gantt_cal_ltext gantt_cal_template' style='height:" + (t.height || "30") + "px;'></div>"
                    },
                    set_value: function (t, e, i, n) {
                        t.innerHTML = e || ""
                    },
                    get_value: function (t, e, i) {
                        return t.innerHTML || ""
                    },
                    focus: function (t) {}
                },
                textarea: {
                    render: function (t) {
                        return "<div class='gantt_cal_ltext' style='height:" + (t.height || "130") + "px;'><textarea></textarea></div>"
                    },
                    set_value: function (t, e, i) {
                        this.form_blocks.textarea._get_input(t).value = e || ""
                    },
                    get_value: function (t, e) {
                        return this.form_blocks.textarea._get_input(t).value
                    },
                    focus: function (e) {
                        var i = this.form_blocks.textarea._get_input(e);
                        t._focus(i, !0)
                    },
                    _get_input: function (t) {
                        return t.querySelector("textarea")
                    }
                },
                select: {
                    render: function (t) {
                        for (var e = (t.height || "23") + "px", i = "<div class='gantt_cal_ltext' style='height:" + e + ";'><select style='width:100%;'>", n = 0; n < t.options.length; n++)
                            i += "<option value='" + t.options[n].key + "'>" + t.options[n].label + "</option>";
                        return i += "</select></div>"
                    },
                    set_value: function (t, e, i, n) {
                        var r = t.firstChild;
                        !r._dhx_onchange && n.onchange && (r.onchange = n.onchange, r._dhx_onchange = !0), void 0 === e && (e = (r.options[0] || {}).value), r.value = e || ""
                    },
                    get_value: function (t, e) {
                        return t.firstChild.value
                    },
                    focus: function (e) {
                        var i = e.firstChild;
                        t._focus(i, !0)
                    }
                },
                time: {
                    render: function (t) {
                        var e = this.form_blocks.getTimePicker.call(this, t),
                                i = ["<div style='height:" + (t.height || 30) + "px;padding-top:0px;font-size:inherit;text-align:center;' class='gantt_section_time'>"];
                        return i.push(e), t.single_date ? (e = this.form_blocks.getTimePicker.call(this, t, !0), i.push("<span></span>")) : i.push("<span style='font-weight:normal; font-size:10pt;'> &nbsp;&ndash;&nbsp; </span>"), i.push(e), i.push("</div>"), i.join("")
                    },
                    set_value: function (e, i, n, r) {
                        var a = r,
                                s = e.getElementsByTagName("select"),
                                o = r._time_format_order;
                        if (r._time_format_size, a.auto_end_date)
                            for (var l = function () {
                                h = new Date(s[o[2]].value, s[o[1]].value, s[o[0]].value, 0, 0), u = t.calculateEndDate({
                                    start_date: h,
                                    duration: 1,
                                    task: n
                                }), this.form_blocks._fill_lightbox_select(s, o.size, u, o, a)
                            }, d = 0; d < 4; d++)
                                s[d].onchange = l;
                        var c = t._resolve_default_mapping(r);
                        "string" == typeof c && (c = {
                            start_date: c
                        });
                        var h = n[c.start_date] || new Date,
                                u = n[c.end_date] || t.calculateEndDate({
                            start_date: h,
                            duration: 1,
                            task: n
                        });
                        this.form_blocks._fill_lightbox_select(s, 0, h, o, a), this.form_blocks._fill_lightbox_select(s, o.size, u, o, a)
                    },
                    get_value: function (e, i, n) {
                        var r = e.getElementsByTagName("select"),
                                a = n._time_format_order,
                                s = 0,
                                o = 0;
                        if (t.defined(a[3])) {
                            var l = parseInt(r[a[3]].value, 10);
                            s = Math.floor(l / 60), o = l % 60
                        }
                        var d = new Date(r[a[2]].value, r[a[1]].value, r[a[0]].value, s, o);
                        if (s = o = 0, t.defined(a[3])) {
                            var l = parseInt(r[a.size + a[3]].value, 10);
                            s = Math.floor(l / 60), o = l % 60
                        }
                        var c = new Date(r[a[2] + a.size].value, r[a[1] + a.size].value, r[a[0] + a.size].value, s, o);
                        c <= d && (c = t.date.add(d, t._get_timepicker_step(), "minute"));
                        var h = t._resolve_default_mapping(n),
                                u = {
                                    start_date: new Date(d),
                                    end_date: new Date(c)
                                };
                        return "string" == typeof h ? u.start_date : u
                    },
                    focus: function (e) {
                        t._focus(e.getElementsByTagName("select")[0])
                    }
                },
                duration: {
                    render: function (t) {
                        var e = this.form_blocks.getTimePicker.call(this, t);
                        e = "<div class='gantt_time_selects'>" + e + "</div>";
                        var i = this.locale.labels[this.config.duration_unit + "s"],
                                n = t.single_date ? ' style="display:none"' : "",
                                r = t.readonly ? " disabled='disabled'" : "",
                                a = this._waiAria.lightboxDurationInputAttrString(t),
                                s = "<div class='gantt_duration' " + n + "><input type='button' class='gantt_duration_dec' value='âˆ’'" + r + "><input type='text' value='5' class='gantt_duration_value'" + r + " " + a + "><input type='button' class='gantt_duration_inc' value='+'" + r + "> " + i + " <span></span></div>";
                        return "<div style='height:" + (t.height || 30) + "px;padding-top:0px;font-size:inherit;' class='gantt_section_time'>" + e + " " + s + "</div>"
                    },
                    set_value: function (e, i, n, r) {
                        function a() {
                            var i = t.form_blocks.duration._get_start_date.call(t, e, r),
                                    a = t.form_blocks.duration._get_duration.call(t, e, r),
                                    s = t.calculateEndDate({
                                        start_date: i,
                                        duration: a,
                                        task: n
                                    });
                            u.innerHTML = t.templates.task_date(s)
                        }

                        function s(t) {
                            var e = c.value;
                            e = parseInt(e, 10), window.isNaN(e) && (e = 0), e += t, e < 1 && (e = 1), c.value = e, a()
                        }
                        var o = r,
                                l = e.getElementsByTagName("select"),
                                d = e.getElementsByTagName("input"),
                                c = d[1],
                                h = [d[0], d[2]],
                                u = e.getElementsByTagName("span")[0],
                                _ = r._time_format_order;
                        h[0].onclick = t.bind(function () {
                            s(-1 * this.config.duration_step)
                        }, this), h[1].onclick = t.bind(function () {
                            s(1 * this.config.duration_step)
                        }, this), l[0].onchange = a, l[1].onchange = a, l[2].onchange = a, l[3] && (l[3].onchange = a), c.onkeydown = t.bind(function (t) {
                            t = t || window.event;
                            var e = t.charCode || t.keyCode || t.which;
                            return 40 == e ? (s(-1 * this.config.duration_step), !1) : 38 == e ? (s(1 * this.config.duration_step), !1) : void window.setTimeout(function (t) {
                                a()
                            }, 1)
                        }, this), c.onchange = t.bind(function (t) {
                            a()
                        }, this);
                        var g = t._resolve_default_mapping(r);
                        "string" == typeof g && (g = {
                            start_date: g
                        });
                        var f = n[g.start_date] || new Date,
                                p = n[g.end_date] || t.calculateEndDate({
                            start_date: f,
                            duration: 1,
                            task: n
                        }),
                                v = Math.round(n[g.duration]) || t.calculateDuration({
                            start_date: f,
                            end_date: p,
                            task: n
                        });
                        t.form_blocks._fill_lightbox_select(l, 0, f, _, o), c.value = v, a()
                    },
                    _get_start_date: function (e, i) {
                        var n = e.getElementsByTagName("select"),
                                r = i._time_format_order,
                                a = 0,
                                s = 0;
                        if (t.defined(r[3])) {
                            var o = n[r[3]],
                                    l = parseInt(o.value, 10);
                            isNaN(l) && o.hasAttribute("data-value") && (l = parseInt(o.getAttribute("data-value"), 10)), a = Math.floor(l / 60), s = l % 60
                        }
                        return new Date(n[r[2]].value, n[r[1]].value, n[r[0]].value, a, s)
                    },
                    _get_duration: function (t, e) {
                        var i = t.getElementsByTagName("input")[1];
                        return i = parseInt(i.value, 10), i && !window.isNaN(i) || (i = 1), i < 0 && (i *= -1), i
                    },
                    get_value: function (e, i, n) {
                        var r = t.form_blocks.duration._get_start_date(e, n),
                                a = t.form_blocks.duration._get_duration(e, n),
                                s = t.calculateEndDate({
                                    start_date: r,
                                    duration: a,
                                    task: i
                                }),
                                o = t._resolve_default_mapping(n),
                                l = {
                                    start_date: new Date(r),
                                    end_date: new Date(s),
                                    duration: a
                                };
                        return "string" == typeof o ? l.start_date : l
                    },
                    focus: function (e) {
                        t._focus(e.getElementsByTagName("select")[0])
                    }
                },
                parent: {
                    _filter: function (e, i, n) {
                        var r = i.filter || function () {
                            return !0
                        };
                        e = e.slice(0);
                        for (var a = 0; a < e.length; a++) {
                            var s = e[a];
                            (s.id == n || t.isChildOf(s.id, n) || !1 === r(s.id, s)) && (e.splice(a, 1), a--)
                        }
                        return e
                    },
                    _display: function (e, i) {
                        var n = [],
                                r = [];
                        i && (n = t.getTaskByTime(), e.allow_root && n.unshift({
                            id: t.config.root_id,
                            text: e.root_label || ""
                        }), n = this._filter(n, e, i), e.sort && n.sort(e.sort));
                        for (var a = e.template || t.templates.task_text, s = 0; s < n.length; s++) {
                            var o = a.apply(t, [n[s].start_date, n[s].end_date, n[s]]);
                            void 0 === o && (o = ""), r.push({
                                key: n[s].id,
                                label: o
                            })
                        }
                        return e.options = r, e.map_to = e.map_to || "parent", t.form_blocks.select.render.apply(this, arguments)
                    },
                    render: function (e) {
                        return t.form_blocks.parent._display(e, !1)
                    },
                    set_value: function (e, i, n, r) {
                        var a = document.createElement("div");
                        a.innerHTML = t.form_blocks.parent._display(r, n.id);
                        var s = a.removeChild(a.firstChild);
                        return e.onselect = null, e.parentNode.replaceChild(s, e), t.form_blocks.select.set_value.apply(t, [s, i, n, r])
                    },
                    get_value: function () {
                        return t.form_blocks.select.get_value.apply(t, arguments)
                    },
                    focus: function () {
                        return t.form_blocks.select.focus.apply(t, arguments)
                    }
                }
            }, t._is_lightbox_timepicker = function () {
                for (var t = this._get_typed_lightbox_config(), e = 0; e < t.length; e++)
                    if ("time" == t[e].name && "time" == t[e].type)
                        return !0;
                return !1
            }, t._dhtmlx_confirm = function (e, i, n, r) {
                if (!e)
                    return n();
                var a = {
                    text: e
                };
                i && (a.title = i), r && (a.ok = r), n && (a.callback = function (t) {
                    t && n()
                }), t.confirm(a)
            }, t._get_typed_lightbox_config = function (e) {
                void 0 === e && (e = this.getLightboxType());
                var i = n.call(this, e);
                return t.config.lightbox[i + "_sections"] ? t.config.lightbox[i + "_sections"] : t.config.lightbox.sections
            }, t._silent_redraw_lightbox = function (t) {
                var e = this.getLightboxType();
                if (this.getState().lightbox) {
                    var i = this.getState().lightbox,
                            n = this.getLightboxValues(),
                            r = this.copy(this.getTask(i));
                    this.resetLightbox();
                    var a = this.mixin(r, n, !0),
                            s = this.getLightbox(t || void 0);
                    this._center_lightbox(this.getLightbox()), this._set_lightbox_values(a, s)
                } else
                    this.resetLightbox(), this.getLightbox(t || void 0);
                this.callEvent("onLightboxChange", [e, this.getLightboxType()])
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t._extend_to_optional = function (e) {
                var i = e,
                        n = {
                            render: i.render,
                            focus: i.focus,
                            set_value: function (e, r, a, s) {
                                var o = t._resolve_default_mapping(s);
                                if (!a[o.start_date] || "start_date" == o.start_date && this._isAllowedUnscheduledTask(a)) {
                                    n.disable(e, s);
                                    var l = {};
                                    for (var d in o)
                                        l[o[d]] = a[d];
                                    return i.set_value.call(t, e, r, l, s)
                                }
                                return n.enable(e, s), i.set_value.call(t, e, r, a, s)
                            },
                            get_value: function (e, n, r) {
                                return r.disabled ? {
                                    start_date: null
                                } : i.get_value.call(t, e, n, r)
                            },
                            update_block: function (e, i) {
                                if (t.callEvent("onSectionToggle", [t._lightbox_id, i]), e.style.display = i.disabled ? "none" : "block", i.button) {
                                    var n = e.previousSibling.querySelector(".gantt_custom_button_label"),
                                            r = t.locale.labels,
                                            a = i.disabled ? r[i.name + "_enable_button"] : r[i.name + "_disable_button"];
                                    n.innerHTML = a
                                }
                                t.resizeLightbox()
                            },
                            disable: function (t, e) {
                                e.disabled = !0, n.update_block(t, e)
                            },
                            enable: function (t, e) {
                                e.disabled = !1, n.update_block(t, e)
                            },
                            button_click: function (e, i, r, a) {
                                if (!1 !== t.callEvent("onSectionButton", [t._lightbox_id, r])) {
                                    var s = t._get_typed_lightbox_config()[e];
                                    s.disabled ? n.enable(a, s) : n.disable(a, s)
                                }
                            }
                        };
                return n
            }, t.form_blocks.duration_optional = t._extend_to_optional(t.form_blocks.duration), t.form_blocks.time_optional = t._extend_to_optional(t.form_blocks.time)
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.getTaskType = function (e) {
                var i = e;
                e && "object" == typeof e && (i = e.type);
                for (var n in this.config.types)
                    if (this.config.types[n] == i)
                        return i;
                return t.config.types.task
            }, t.form_blocks.typeselect = {
                render: function (e) {
                    var i = t.config.types,
                            n = t.locale.labels,
                            r = [],
                            a = e.filter || function () {
                                return !0
                            };
                    for (var s in i)
                        !1 == !a(s, i[s]) && r.push({
                            key: i[s],
                            label: n["type_" + s]
                        });
                    e.options = r;
                    var o = e.onchange;
                    return e.onchange = function () {
                        t.getState().lightbox;
                        t.changeLightboxType(this.value), "function" == typeof o && o.apply(this, arguments)
                    }, t.form_blocks.select.render.apply(t, arguments)
                },
                set_value: function () {
                    return t.form_blocks.select.set_value.apply(t, arguments)
                },
                get_value: function () {
                    return t.form_blocks.select.get_value.apply(t, arguments)
                },
                focus: function () {
                    return t.form_blocks.select.focus.apply(this, arguments)
                }
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            function e() {
                return t._cached_functions.update_if_changed(t), t._cached_functions.active || t._cached_functions.activate(), !0
            }
            t._cached_functions = {
                cache: {},
                mode: !1,
                critical_path_mode: !1,
                wrap_methods: function (t, e) {
                    if (e._prefetch_originals)
                        for (var i in e._prefetch_originals)
                            e[i] = e._prefetch_originals[i];
                    e._prefetch_originals = {};
                    for (var i = 0; i < t.length; i++)
                        this.prefetch(t[i], e)
                },
                prefetch: function (t, e) {
                    var i = e[t];
                    if (i) {
                        var n = this;
                        e._prefetch_originals[t] = i, e[t] = function () {
                            for (var e = new Array(arguments.length), r = 0, a = arguments.length; r < a; r++)
                                e[r] = arguments[r];
                            if (n.active) {
                                var s = n.get_arguments_hash(Array.prototype.slice.call(e));
                                n.cache[t] || (n.cache[t] = {});
                                var o = n.cache[t];
                                if (n.has_cached_value(o, s))
                                    return n.get_cached_value(o, s);
                                var l = i.apply(this, e);
                                return n.cache_value(o, s, l), l
                            }
                            return i.apply(this, e)
                        }
                    }
                    return i
                },
                cache_value: function (t, e, i) {
                    this.is_date(i) && (i = new Date(i)), t[e] = i
                },
                has_cached_value: function (t, e) {
                    return t.hasOwnProperty(e)
                },
                get_cached_value: function (t, e) {
                    var i = t[e];
                    return this.is_date(i) && (i = new Date(i)), i
                },
                is_date: function (t) {
                    return t && t.getUTCDate
                },
                get_arguments_hash: function (t) {
                    for (var e = [], i = 0; i < t.length; i++)
                        e.push(this.stringify_argument(t[i]));
                    return "(" + e.join(";") + ")"
                },
                stringify_argument: function (t) {
                    return (t.id ? t.id : this.is_date(t) ? t.valueOf() : t) + ""
                },
                activate: function () {
                    this.clear(), this.active = !0
                },
                deactivate: function () {
                    this.clear(), this.active = !1
                },
                clear: function () {
                    this.cache = {}
                },
                setup: function (t) {
                    var e = [],
                            i = ["_isCriticalTask", "isCriticalLink", "_isProjectEnd", "_getProjectEnd", "_getSlack"];
                    "auto" == this.mode ? t.config.highlight_critical_path && (e = i) : !0 === this.mode && (e = i), this.wrap_methods(e, t)
                },
                update_if_changed: function (t) {
                    (this.critical_path_mode != t.config.highlight_critical_path || this.mode !== t.config.optimize_render) && (this.critical_path_mode = t.config.highlight_critical_path, this.mode = t.config.optimize_render, this.setup(t))
                }
            }, t.attachEvent("onBeforeGanttRender", e), t.attachEvent("onBeforeDataRender", e), t.attachEvent("onBeforeSmartRender", function () {
                e()
            }), t.attachEvent("onBeforeParse", e), t.attachEvent("onDataRender", function () {
                t._cached_functions.deactivate()
            });
            var i = null;
            t.attachEvent("onSmartRender", function () {
                i && clearTimeout(i), i = setTimeout(function () {
                    t._cached_functions.deactivate()
                }, 1e3)
            }), t.attachEvent("onBeforeGanttReady", function () {
                return t._cached_functions.update_if_changed(t), !0
            })
        }
    }, function (t, e) {
        function i(t, e, i) {
            for (var n in e)
                (void 0 === t[n] || i) && (t[n] = e[n])
        }

        function n(t, e) {
            var n = e.skin;
            if (!n || t)
                for (var r = document.getElementsByTagName("link"), a = 0; a < r.length; a++) {
                    var s = r[a].href.match("dhtmlxgantt_([a-z_]+).css");
                    if (s && (e.skins[s[1]] || !n)) {
                        n = s[1];
                        break
                    }
                }
            e.skin = n || "terrace";
            var o = e.skins[e.skin] || e.skins.terrace;
            i(e.config, o.config, t);
            var l = e.getGridColumns();
            l[1] && !e.defined(l[1].width) && (l[1].width = o._second_column_width), l[2] && !e.defined(l[2].width) && (l[2].width = o._third_column_width);
            for (var a = 0; a < l.length; a++) {
                var d = l[a];
                "add" == d.name && (d.width || (d.width = 44), e.defined(d.min_width) && e.defined(d.max_width) || (d.min_width = d.min_width || d.width, d.max_width = d.max_width || d.width), d.min_width && (d.min_width = +d.min_width), d.max_width && (d.max_width = +d.max_width), d.width && (d.width = +d.width, d.width = d.min_width && d.min_width > d.width ? d.min_width : d.width, d.width = d.max_width && d.max_width < d.width ? d.max_width : d.width))
            }
            o.config.task_height && (e.config.task_height = o.config.task_height || "full"), o._lightbox_template && (e._lightbox_template = o._lightbox_template), o._redefine_lightbox_buttons && (e.config.buttons_right = o._redefine_lightbox_buttons.buttons_right, e.config.buttons_left = o._redefine_lightbox_buttons.buttons_left), e.resetLightbox()
        }
        t.exports = function (t) {
            t.resetSkin || (t.resetSkin = function () {
                this.skin = "", n(!0, this)
            }, t.skins = {}, t.attachEvent("onGanttLayoutReady", function () {
                n(!1, this)
            }))
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.skins.skyblue = {
                config: {
                    grid_width: 350,
                    row_height: 27,
                    scale_height: 27,
                    link_line_width: 1,
                    link_arrow_size: 8,
                    lightbox_additional_height: 75
                },
                _second_column_width: 95,
                _third_column_width: 80
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.skins.meadow = {
                config: {
                    grid_width: 350,
                    row_height: 27,
                    scale_height: 30,
                    link_line_width: 2,
                    link_arrow_size: 6,
                    lightbox_additional_height: 72
                },
                _second_column_width: 95,
                _third_column_width: 80
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.skins.terrace = {
                config: {
                    grid_width: 360,
                    row_height: 35,
                    scale_height: 35,
                    link_line_width: 2,
                    link_arrow_size: 6,
                    lightbox_additional_height: 75
                },
                _second_column_width: 90,
                _third_column_width: 70
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.skins.broadway = {
                config: {
                    grid_width: 360,
                    row_height: 35,
                    scale_height: 35,
                    link_line_width: 1,
                    link_arrow_size: 7,
                    lightbox_additional_height: 86
                },
                _second_column_width: 90,
                _third_column_width: 80,
                _lightbox_template: "<div class='gantt_cal_ltitle'><span class='gantt_mark'>&nbsp;</span><span class='gantt_time'></span><span class='gantt_title'></span><div class='gantt_cancel_btn'></div></div><div class='gantt_cal_larea'></div>",
                _config_buttons_left: {},
                _config_buttons_right: {
                    gantt_delete_btn: "icon_delete",
                    gantt_save_btn: "icon_save"
                }
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.skins.material = {
                config: {
                    grid_width: 411,
                    row_height: 34,
                    task_height_offset: 6,
                    scale_height: 36,
                    link_line_width: 2,
                    link_arrow_size: 6,
                    lightbox_additional_height: 80
                },
                _second_column_width: 110,
                _third_column_width: 75,
                _redefine_lightbox_buttons: {
                    buttons_left: ["dhx_delete_btn"],
                    buttons_right: ["dhx_save_btn", "dhx_cancel_btn"]
                }
            }, t.attachEvent("onAfterTaskDrag", function (e) {
                var i = t.getTaskNode(e);
                onUpdateGantt(t, t.getTask(e));
                i && (i.className += " gantt_drag_animation", setTimeout(function () {
                    var t = i.className.indexOf(" gantt_drag_animation");
                    t > -1 && (i.className = i.className.slice(0, t))
                }, 200))
            })
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.skins.contrast_black = {
                config: {
                    grid_width: 360,
                    row_height: 35,
                    scale_height: 35,
                    link_line_width: 2,
                    link_arrow_size: 6,
                    lightbox_additional_height: 75
                },
                _second_column_width: 100,
                _third_column_width: 80
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.skins.contrast_white = {
                config: {
                    grid_width: 360,
                    row_height: 35,
                    scale_height: 35,
                    link_line_width: 2,
                    link_arrow_size: 6,
                    lightbox_additional_height: 75
                },
                _second_column_width: 100,
                _third_column_width: 80
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            function e() {
                var e;
                return t.$ui.getView("timeline") && (e = t.$ui.getView("timeline")._tasks_dnd), e
            }
            t.config.touch_drag = 500, t.config.touch = !0, t.config.touch_feedback = !0, t.config.touch_feedback_duration = 1, t._prevent_touch_scroll = !1, t._touch_feedback = function () {
                t.config.touch_feedback && navigator.vibrate && navigator.vibrate(t.config.touch_feedback_duration)
            }, t.attachEvent("onGanttReady", t.bind(function () {
                if ("force" != this.config.touch && (this.config.touch = this.config.touch && (-1 != navigator.userAgent.indexOf("Mobile") || -1 != navigator.userAgent.indexOf("iPad") || -1 != navigator.userAgent.indexOf("Android") || -1 != navigator.userAgent.indexOf("Touch"))), this.config.touch) {
                    var t = !0;
                    try {
                        document.createEvent("TouchEvent")
                    } catch (e) {
                        t = !1
                    }
                    t ? this._touch_events(["touchmove", "touchstart", "touchend"], function (t) {
                        return t.touches && t.touches.length > 1 ? null : t.touches[0] ? {
                            target: t.target,
                            pageX: t.touches[0].pageX,
                            pageY: t.touches[0].pageY,
                            clientX: t.touches[0].clientX,
                            clientY: t.touches[0].clientY
                        } : t
                    }, function () {
                        return !1
                    }) : window.navigator.pointerEnabled ? this._touch_events(["pointermove", "pointerdown", "pointerup"], function (t) {
                        return "mouse" == t.pointerType ? null : t
                    }, function (t) {
                        return !t || "mouse" == t.pointerType
                    }) : window.navigator.msPointerEnabled && this._touch_events(["MSPointerMove", "MSPointerDown", "MSPointerUp"], function (t) {
                        return t.pointerType == t.MSPOINTER_TYPE_MOUSE ? null : t
                    }, function (t) {
                        return !t || t.pointerType == t.MSPOINTER_TYPE_MOUSE
                    })
                }
            }, t));
            var i = [];
            t._touch_events = function (n, r, a) {
                function s(t) {
                    return t && t.preventDefault && t.preventDefault(), (t || event).cancelBubble = !0, !1
                }

                function o(e) {
                    var i = t._getTaskLayers(),
                            n = t.getTask(e);
                    if (n && t.isTaskVisible(e))
                        for (var r = 0; r < i.length; r++)
                            if ((n = i[r].rendered[e]) && n.getAttribute("task_id") && n.getAttribute("task_id") == e) {
                                var a = n.cloneNode(!0);
                                g = n, i[r].rendered[e] = a, n.style.display = "none", a.className += " gantt_drag_move ", n.parentNode.appendChild(a)
                            }
                }
                for (var l, d = 0, c = !1, h = !1, u = null, _ = null, g = null, f = 0; f < i.length; f++)
                    t.eventRemove(i[f][0], i[f][1], i[f][2]);
                i = [], i.push([t.$container, n[0], function (i) {
                        var n = e();
                        if (!a(i) && c) {
                            _ && clearTimeout(_);
                            var o = r(i);
                            if (n && (n.drag.id || n.drag.start_drag))
                                return n.on_mouse_move(o), i.preventDefault && i.preventDefault(), i.cancelBubble = !0, !1;
                            if (!t._prevent_touch_scroll) {
                                if (o && u) {
                                    var g = u.pageX - o.pageX,
                                            f = u.pageY - o.pageY;
                                    if (!h && (Math.abs(g) > 5 || Math.abs(f) > 5) && (t._touch_scroll_active = h = !0, d = 0, l = t.getScrollState()), h) {
                                        t.scrollTo(l.x + g, l.y + f);
                                        var p = t.getScrollState();
                                        if (l.x != p.x && f > 2 * g || l.y != p.y && g > 2 * f)
                                            return s(i)
                                    }
                                }
                                return s(i)
                            }
                            return !0
                        }
                    }]), i.push([this.$container, "contextmenu", function (t) {
                        if (c)
                            return s(t)
                    }]), i.push([this.$container, n[1], function (i) {
                        if (!a(i)) {
                            if (i.touches && i.touches.length > 1)
                                return void(c = !1);
                            u = r(i), t._locate_css(u, "gantt_hor_scroll") || t._locate_css(u, "gantt_ver_scroll") || (c = !0);
                            var n = e();
                            _ = setTimeout(function () {
                                var e = t.locate(u);
                                n && e && !t._locate_css(u, "gantt_link_control") && !t._locate_css(u, "gantt_grid_data") && (n.on_mouse_down(u), n.drag && n.drag.start_drag && (o(e), n._start_dnd(u), t._touch_drag = !0, t.refreshTask(e), t._touch_feedback())), _ = null
                            }, t.config.touch_drag)
                        }
                    }]), i.push([this.$container, n[2], function (i) {
                        if (!a(i)) {
                            _ && clearTimeout(_), t._touch_drag = !1, c = !1;
                            var n = r(i),
                                    o = e();
                            if (o && o.on_mouse_up(n), g && (t.refreshTask(t.locate(g)), g.parentNode && (g.parentNode.removeChild(g), t._touch_feedback())), t._touch_scroll_active = c = h = !1, g = null, u && d) {
                                var l = new Date;
                                if (l - d < 500) {
                                    t.$services.getService("mouseEvents").onDoubleClick(u), s(i)
                                } else
                                    d = l
                            } else
                                d = new Date
                        }
                    }]);
                for (var f = 0; f < i.length; f++)
                    t.event(i[f][0], i[f][1], i[f][2])
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            t.locale = {
                date: {
                    month_full: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                    month_short: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    day_full: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                    day_short: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
                },
                labels: {
                    new_task: "New task",
                    icon_save: "Save",
                    icon_cancel: "Cancel",
                    icon_details: "Details",
                    icon_edit: "Edit",
                    icon_delete: "Delete",
                    confirm_closing: "",
                    confirm_deleting: "Task will be deleted permanently, are you sure?",
                    section_description: "Description",
                    section_time: "Time period",
                    section_type: "Type",
                    column_wbs: "WBS",
                    column_text: "Task name",
                    column_start_date: "Start time",
                    column_duration: "Duration",
                    column_add: "",
                    link: "Link",
                    confirm_link_deleting: "will be deleted",
                    link_start: " (start)",
                    link_end: " (end)",
                    type_task: "Task",
                    type_project: "Project",
                    type_milestone: "Milestone",
                    minutes: "Minutes",
                    hours: "Hours",
                    days: "Days",
                    weeks: "Week",
                    months: "Months",
                    years: "Years",
                    message_ok: "OK",
                    message_cancel: "Cancel"
                }
            }
        }
    }, function (t, e, i) {
        var n = i(1),
                r = i(7);
        t.exports = function (t) {
            var e = i(19);
            t.assert = i(110)(t), t.init = function (e, i, r) {
                i && r && (this.config.start_date = this._min_date = new Date(i), this.config.end_date = this._max_date = new Date(r)), this.date.init(), this.config.scroll_size || (this.config.scroll_size = n.getScrollSize() || 1);
                var a;
                t.event(window, "resize", function () {
                    clearTimeout(a), a = setTimeout(function () {
                        t.render()
                    }, 300)
                }), this.init = function (t) {
                    this.$container && this.$container.parentNode && (this.$container.parentNode.removeChild(this.$container), this.$container = null), this.$layout && this.$layout.clear(), this._reinit(t)
                }, this._reinit(e)
            }, t._reinit = function (i) {
                this.callEvent("onBeforeGanttReady", []), this.resetLightbox(), this._update_flags(), this.$services.getService("templateLoader").initTemplates(this), this._clearTaskLayers(), this._clearLinkLayers(), this.$layout && (this.$layout.destructor(), this.$ui.reset()), this.$root = n.toNode(i), this.$root && (this.$root.innerHTML = ""), this.$root.gantt = this, e(this), this.config.layout.id = "main", this.$layout = this.$ui.createView("layout", i, this.config.layout), this.$layout.attachEvent("onBeforeResize", function () {
                    for (var e = t.$services.getService("datastores"), i = 0; i < e.length; i++)
                        t.getDatastore(e[i]).filter()
                }), this.$layout.attachEvent("onResize", function () {
                    t.refreshData()
                }), this.callEvent("onGanttLayoutReady", []), this.$layout.render(), t.$container = this.$layout.$container.firstChild, this.callEvent("onTemplatesReady", []), this.$mouseEvents.reset(this.$root), this.callEvent("onGanttReady", []), this.render()
            }, t.$click = {
                buttons: {
                    edit: function (e) {
                        t.showLightbox(e)
                    },
                    delete: function (e) {
                        var i = t.locale.labels.confirm_deleting,
                                n = t.locale.labels.confirm_deleting_title;
                        t._dhtmlx_confirm(i, n, function () {
                            if (!t.isTaskExists(e))
                                return void t.hideLightbox();
                            t.getTask(e).$new ? (t.silent(function () {
                                t.deleteTask(e, !0)
                            }), t.refreshData()) : t.deleteTask(e), t.hideLightbox()
                        })
                    }
                }
            }, t.render = function () {
                this.callEvent("onBeforeGanttRender", []);
                var i = this.getScrollState(),
                        n = i ? i.x : 0;
                if (this._getHorizontalScrollbar()) {
                    n = this._getHorizontalScrollbar().$config.codeScrollLeft || n || 0
                }
                var r = null;
                if (n && (r = t.dateFromPos(n + this.config.task_scroll_offset)), e(this), this.$layout.$config.autosize = this.config.autosize, this.$layout.resize(), this.config.preserve_scroll && i && n) {
                    var a = t.getScrollState();
                    +r == +t.dateFromPos(a.x) && a.y == i.y || (r && this.showDate(r), i.y && t.scrollTo(void 0, i.y))
                }
                this.callEvent("onGanttRender", [])
            }, t.setSizes = t.render, t.locate = function (t) {
                var e = n.getTargetNode(t);
                if ((n.getClassName(e) || "").indexOf("gantt_task_cell") >= 0)
                    return null;
                var i = arguments[1] || this.config.task_attribute,
                        r = n.locateAttribute(e, i);
                return r ? r.getAttribute(i) : null
            }, t._locate_css = function (t, e, i) {
                return n.locateClassName(t, e, i)
            }, t._locateHTML = function (t, e) {
                return n.locateAttribute(t, e || this.config.task_attribute)
            }, t.getTaskRowNode = function (t) {
                for (var e = this.$grid_data.childNodes, i = this.config.task_attribute, n = 0; n < e.length; n++)
                    if (e[n].getAttribute) {
                        var r = e[n].getAttribute(i);
                        if (r == t)
                            return e[n]
                    }
                return null
            }, t.changeLightboxType = function (e) {
                if (this.getLightboxType() == e)
                    return !0;
                t._silent_redraw_lightbox(e)
            }, t._get_link_type = function (e, i) {
                var n = null;
                return e && i ? n = t.config.links.start_to_start : !e && i ? n = t.config.links.finish_to_start : e || i ? e && !i && (n = t.config.links.start_to_finish) : n = t.config.links.finish_to_finish, n
            }, t.isLinkAllowed = function (t, e, i, n) {
                var r = null;
                if (!(r = "object" == typeof t ? t : {
                    source: t,
                    target: e,
                    type: this._get_link_type(i, n)
                }))
                    return !1;
                if (!(r.source && r.target && r.type))
                    return !1;
                if (r.source == r.target)
                    return !1;
                var a = !0;
                return this.checkEvent("onLinkValidation") && (a = this.callEvent("onLinkValidation", [r])), a
            }, t._correct_dst_change = function (e, i, n, a) {
                var s = r.getSecondsInUnit(a) * n;
                if (s > 3600 && s < 86400) {
                    var o = e.getTimezoneOffset() - i;
                    o && (e = t.date.add(e, o, "minute"))
                }
                return e
            }, t.getGridColumns = function () {
                return t.config.columns.slice()
            }, t._is_icon_open_click = function (t) {
                if (!t)
                    return !1;
                var e = t.target || t.srcElement;
                if (!e || !e.className)
                    return !1;
                var i = n.getClassName(e);
                return -1 !== i.indexOf("gantt_tree_icon") && (-1 !== i.indexOf("gantt_close") || -1 !== i.indexOf("gantt_open"))
            }
        }
    }, function (t, e) {
        t.exports = function (t) {
            return function (e, i) {
                e || t.config.show_errors && !1 !== t.callEvent("onError", [i]) && t.message({
                    type: "error",
                    text: i,
                    expire: -1
                })
            }
        }
    }, function (t, e) {
        function i(t) {
            t.destructor = function () {
                t.callEvent("onDestroy", []), this.clearAll(), this.detachAllEvents(), this.$root && delete this.$root.gantt, this._eventRemoveAll(), this.$layout && this.$layout.destructor(), this.resetLightbox(), this._dp && this._dp.destructor && this._dp.destructor(), this.$services.destructor();
                for (var e in this)
                    0 === e.indexOf("$") && delete this[e]
            }
        }
        t.exports = i
    }, function (t, e) {
        t.exports = function (t) {
            function e(e, i) {
                var n = t.env.isIE ? "" : "%c",
                        r = [n, '"', e, '"', n, " has been deprecated in dhtmlxGantt v4.0 and will stop working in v6.0. Use ", n, '"', i, '"', n, " instead. \nSee more details at http://docs.dhtmlx.com/gantt/migrating.html "].join(""),
                        a = window.console.warn || window.console.log,
                        s = [r];
                t.env.isIE || (s = s.concat(["font-weight:bold", "font-weight:normal", "font-weight:bold", "font-weight:normal"])), a.apply(window.console, s)
            }
            window.dhtmlx || (window.dhtmlx = {});
            for (var i = ["message", "alert", "confirm", "modalbox", "uid", "copy", "mixin", "defined", "bind", "assert"], n = [], r = 0; r < i.length; r++)
                window.dhtmlx[i[r]] || (n.push(i[r]), dhtmlx[i[r]] = function (i) {
                    return function () {
                        return e("dhtmlx." + i, "gantt." + i), t[i].apply(t, arguments)
                    }
                }(i[r]));
            t.attachEvent("onDestroy", function () {
                for (var t = 0; t < n.length; t++)
                    delete window.dhtmlx[n[t]];
                n = null
            }), window.dataProcessor || (window.dataProcessor = function (i) {
                return e("new dataProcessor(url)", "new gantt.dataProcessor(url)"), new t.dataProcessor(i)
            }, t.attachEvent("onDestroy", function () {
                window.dataProcessor = null
            }))
        }
    }]);
