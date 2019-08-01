Gantt.plugin(function(t) {
    ! function(t) {
        function e(i) {
            if (o[i]) return o[i].exports;
            var n = o[i] = {
                i: i,
                l: !1,
                exports: {}
            };
            return t[i].call(n.exports, n, n.exports, e), n.l = !0, n.exports
        }
        var o = {};
        e.m = t, e.c = o, e.d = function(t, o, i) {
            e.o(t, o) || Object.defineProperty(t, o, {
                configurable: !1,
                enumerable: !0,
                get: i
            })
        }, e.n = function(t) {
            var o = t && t.__esModule ? function() {
                return t.default
            } : function() {
                return t
            };
            return e.d(o, "a", o), o
        }, e.o = function(t, e) {
            return Object.prototype.hasOwnProperty.call(t, e)
        }, e.p = "", e(e.s = 33)
    }({
        0: function(t, e) {
            function o(t) {
                var e = 0,
                    o = 0,
                    i = 0,
                    n = 0;
                if (t.getBoundingClientRect) {
                    var r = t.getBoundingClientRect(),
                        l = document.body,
                        a = document.documentElement || document.body.parentNode || document.body,
                        s = window.pageYOffset || a.scrollTop || l.scrollTop,
                        c = window.pageXOffset || a.scrollLeft || l.scrollLeft,
                        u = a.clientTop || l.clientTop || 0,
                        f = a.clientLeft || l.clientLeft || 0;
                    e = r.top + s - u, o = r.left + c - f, i = document.body.offsetWidth - r.right, n = document.body.offsetHeight - r.bottom
                } else {
                    for (; t;) e += parseInt(t.offsetTop, 10), o += parseInt(t.offsetLeft, 10), t = t.offsetParent;
                    i = document.body.offsetWidth - t.offsetWidth - o, n = document.body.offsetHeight - t.offsetHeight - e
                }
                return {
                    y: Math.round(e),
                    x: Math.round(o),
                    width: t.offsetWidth,
                    height: t.offsetHeight,
                    right: Math.round(i),
                    bottom: Math.round(n)
                }
            }

            function i(t) {
                var e = !1,
                    o = !1;
                if (window.getComputedStyle) {
                    var i = window.getComputedStyle(t, null);
                    e = i.display, o = i.visibility
                } else t.currentStyle && (e = t.currentStyle.display, o = t.currentStyle.visibility);
                return "none" != e && "hidden" != o
            }

            function n(t) {
                return !isNaN(t.getAttribute("tabindex")) && 1 * t.getAttribute("tabindex") >= 0
            }

            function r(t) {
                return !{
                    a: !0,
                    area: !0
                }[t.nodeName.loLowerCase()] || !!t.getAttribute("href")
            }

            function l(t) {
                return !{
                    input: !0,
                    select: !0,
                    textarea: !0,
                    button: !0,
                    object: !0
                }[t.nodeName.toLowerCase()] || !t.hasAttribute("disabled")
            }

            function a(t) {
                for (var e = t.querySelectorAll(["a[href]", "area[href]", "input", "select", "textarea", "button", "iframe", "object", "embed", "[tabindex]", "[contenteditable]"].join(", ")), o = Array.prototype.slice.call(e, 0), a = 0; a < o.length; a++) {
                    var s = o[a];
                    (n(s) || l(s) || r(s)) && i(s) || (o.splice(a, 1), a--)
                }
                return o
            }

            function s() {
                var t = document.createElement("div");
                t.style.cssText = "visibility:hidden;position:absolute;left:-1000px;width:100px;padding:0px;margin:0px;height:110px;min-height:100px;overflow-y:scroll;", document.body.appendChild(t);
                var e = t.offsetWidth - t.clientWidth;
                return document.body.removeChild(t), e
            }

            function c(t) {
                if (!t) return "";
                var e = t.className || "";
                return e.baseVal && (e = e.baseVal), e.indexOf || (e = ""), y(e)
            }

            function u(t, e) {
                e && -1 === t.className.indexOf(e) && (t.className += " " + e)
            }

            function f(t, e) {
                e = e.split(" ");
                for (var o = 0; o < e.length; o++) {
                    var i = new RegExp("\\s?\\b" + e[o] + "\\b(?![-_.])", "");
                    t.className = t.className.replace(i, "")
                }
            }

            function d(t) {
                return "string" == typeof t ? document.getElementById(t) || document.querySelector(t) || document.body : t || document.body
            }

            function p(t, e) {
                N.innerHTML = e;
                var o = N.firstChild;
                return t.appendChild(o), o
            }

            function _(t) {
                t && t.parentNode && t.parentNode.removeChild(t)
            }

            function h(t, e) {
                for (var o = t.childNodes, i = o.length, n = [], r = 0; r < i; r++) {
                    var l = o[r];
                    l.className && -1 !== l.className.indexOf(e) && n.push(l)
                }
                return n
            }

            function m(t) {
                var e;
                return t.tagName ? e = t : (t = t || window.event, e = t.target || t.srcElement), e
            }

            function g(t, e) {
                if (e) {
                    for (var o = m(t); o;) {
                        if (o.getAttribute) {
                            if (o.getAttribute(e)) return o
                        }
                        o = o.parentNode
                    }
                    return null
                }
            }

            function y(t) {
                return (String.prototype.trim || function() {
                    return this.replace(/^\s+|\s+$/g, "")
                }).apply(t)
            }

            function v(t, e, o) {
                void 0 === o && (o = !0);
                for (var i = m(t), n = ""; i;) {
                    if (n = c(i)) {
                        var r = n.indexOf(e);
                        if (r >= 0) {
                            if (!o) return i;
                            var l = 0 === r || !y(n.charAt(r - 1)),
                                a = r + e.length >= n.length || !y(n.charAt(r + e.length));
                            if (l && a) return i
                        }
                    }
                    i = i.parentNode
                }
                return null
            }

            function x(t, e) {
                if (t.pageX || t.pageY) var i = {
                    x: t.pageX,
                    y: t.pageY
                };
                var n = document.documentElement,
                    i = {
                        x: t.clientX + n.scrollLeft - n.clientLeft,
                        y: t.clientY + n.scrollTop - n.clientTop
                    },
                    r = o(e);
                return i.x = i.x - r.x + e.scrollLeft, i.y = i.y - r.y + e.scrollTop, i
            }

            function b(t, e) {
                if (!t || !e) return !1;
                for (; t && t != e;) t = t.parentNode;
                return t === e
            }
            var N = document.createElement("div");
            t.exports = {
                getNodePosition: o,
                getFocusableNodes: a,
                getScrollSize: s,
                getClassName: c,
                addClassName: u,
                removeClassName: f,
                insertNode: p,
                removeNode: _,
                getChildNodes: h,
                toNode: d,
                locateClassName: v,
                locateAttribute: g,
                getTargetNode: m,
                getRelativeEventPosition: x,
                isChildOf: b
            }
        },
        33: function(t, e, o) {
            t.exports = o(34)
        },
        34: function(e, o, i) {
            ! function() {
                function e() {
                    return t.$task_data || t.$root
                }

                function o() {
                    return t.$task || t.$root
                }
                t._tooltip = {}, t._tooltip_class = "gantt_tooltip", t.config.tooltip_timeout = 30, t.config.tooltip_offset_y = 20, t.config.tooltip_offset_x = 10, t._create_tooltip = function() {
                    return this._tooltip_html || (this._tooltip_html = document.createElement("div"), this._tooltip_html.className = t._tooltip_class, this._waiAria.tooltipAttr(this._tooltip_html)), this._tooltip_html
                }, t._is_cursor_under_tooltip = function(t, e) {
                    return t.x >= e.pos.x && t.x <= e.pos.x + e.width || t.y >= e.pos.y && t.y <= e.pos.y + e.height
                }, t._show_tooltip = function(i, n) {
                    if (!t.config.touch || t.config.touch_tooltip) {
                        var r = this._create_tooltip();
                        r.innerHTML = i, e().appendChild(r);
                        var l = r.offsetWidth + 20,
                            a = r.offsetHeight + 40,
                            s = o(),
                            c = s.offsetHeight,
                            u = s.offsetWidth,
                            f = this.getScrollState();
                        s === t.$root && (f = {
                            x: 0,
                            y: 0
                        }), t._waiAria.tooltipVisibleAttr(r), n.y += f.y;
                        var d = {
                            x: n.x,
                            y: n.y
                        };
                        n.x += 1 * t.config.tooltip_offset_x || 0, n.y += 1 * t.config.tooltip_offset_y || 0, n.y = Math.min(Math.max(f.y, n.y), f.y + c - a), n.x = Math.min(Math.max(f.x, n.x), f.x + u - l), t._is_cursor_under_tooltip(d, {
                            pos: n,
                            width: l,
                            height: a
                        }) && (d.x + l > u + f.x && (n.x = d.x - (l - 20) - (1 * t.config.tooltip_offset_x || 0)), d.y + a > c + f.y && (n.y = d.y - (a - 40) - (1 * t.config.tooltip_offset_y || 0))), r.style.left = n.x + "px", r.style.top = n.y + "px"
                    }
                }, t._hide_tooltip = function() {
                    this._tooltip_html && this._waiAria.tooltipHiddenAttr(this._tooltip_html), this._tooltip_html && this._tooltip_html.parentNode && this._tooltip_html.parentNode.removeChild(this._tooltip_html), this._tooltip_id = 0
                }, t._is_tooltip = function(e) {
                    var o = e.target || e.srcElement;
                    return t._is_node_child(o, function(t) {
                        return t.className == this._tooltip_class
                    })
                }, t._is_task_line = function(e) {
                    var o = e.target || e.srcElement;
                    return t._is_node_child(o, function(t) {
                        return t == this.$task_data
                    })
                }, t._is_node_child = function(e, o) {
                    for (var i = !1; e && !i;) i = o.call(t, e), e = e.parentNode;
                    return i
                }, t._tooltip_pos = function(t) {
                    if (t.pageX || t.pageY) var o = {
                        x: t.pageX,
                        y: t.pageY
                    };
                    var n = document.documentElement || document.body.parentNode || document.body,
                        o = {
                            x: t.clientX + n.scrollLeft - n.clientLeft,
                            y: t.clientY + n.scrollTop - n.clientTop
                        },
                        r = i(0),
                        l = r.getNodePosition(e());
                    return o.x = o.x - l.x, o.y = o.y - l.y, o
                }, t.attachEvent("onMouseMove", function(e, o) {
                    if (this.config.tooltip_timeout) {
                        document.createEventObject && !document.createEvent && (o = document.createEventObject(o));
                        var i = this.config.tooltip_timeout;
                        this._tooltip_id && !e && (isNaN(this.config.tooltip_hide_timeout) || (i = this.config.tooltip_hide_timeout)), clearTimeout(t._tooltip_ev_timer), t._tooltip_ev_timer = setTimeout(function() {
                            t._init_tooltip(e, o)
                        }, i)
                    } else t._init_tooltip(e, o)
                }), t._init_tooltip = function(t, e) {
                    if (!this._is_tooltip(e) && (t != this._tooltip_id || this._is_task_line(e))) {
                        if (!t) return this._hide_tooltip();
                        this._tooltip_id = t;
                        var o = this.getTask(t),
                            i = this.templates.tooltip_text(o.start_date, o.end_date, o);
                        if (!i) return void this._hide_tooltip();
                        this._show_tooltip(i, this._tooltip_pos(e))
                    }
                }, t.attachEvent("onMouseLeave", function(e) {
                    t._is_tooltip(e) || this._hide_tooltip()
                })
            }()
        }
    })
});