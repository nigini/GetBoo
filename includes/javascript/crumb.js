// Based on Del.icio.us
function niceExtend(dest, src){
	if(!src) return dest
	if(src.html) { dest.innerHTML = src.html; delete src.html }
	if(src.css) { dest.className = src.css; delete src.css }
	if(src.attr) {
		var s = src.attr
		for(var k in s) dest.setAttribute(k, s[k])
		delete src.attr
	}
	if(src.style) {
		var d = dest.style, s = src.style
		for(var k in s) d[k] = s[k]
		delete src.style
	}
	for(var k in src) dest[k] = src[k]
	return dest
}

function create(o,t){
	if (o == 'text') return document.createTextNode(t||'')
	else {
		var e = document.createElement(o)
		if (t) {
			if (typeof t == 'string') e.innerHTML = t
			else niceExtend(e, t)
		}
		return e
}}

function extend(dest, src){
	if(!src) return dest
	for(var k in src) dest[k] = src[k]
	return dest
}
extend( String.prototype, {
	include: function(t) { return this.indexOf(t) >= 0 ? true : false },
	trim: function(){ return this.replace(/^\s+|\s+$/g,'') },
	splitrim: function(t){ return this.trim().split(new RegExp('\\s*'+t+'\\s*')) },
	encodeTag: function() { return encodeURIComponent(this).replace(/%2F/g, '/') },
	unescHtml: function(){ var i,e={'&lt;':'<','&gt;':'>','&amp;':'&','&quot;':'"'},t=this; for(i in e) t=t.replace(new RegExp(i,'g'),e[i]); return t },
	escHtml: function(){ var i,e={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'},t=this; for(i in e) t=t.replace(new RegExp(i,'g'),e[i]); return t },
	escRegExp: function(){ return this.replace(/[\\$*+?()=!|,{}\[\]\.^]/g,'\\$&') }
})

function $id(id){ if (typeof id == 'string') return document.getElementById(id); return id }

var Crumb = {
	go: function(root){
		var p = $id('crumb')
		var tag = p.innerHTML.unescHtml()
		var o = create('input', {css: 'crumb', originalValue: tag, value: tag, root: root || '/tag/',
			onblur: Crumb.blur, onfocus: Crumb.focus, onmouseover: Crumb.mouseover, onmouseout: Crumb.mouseout,
			onkeyup: Crumb.keyhandler, onkeypress: Crumb.keyhandler})
		p.innerHTML = ''; p.appendChild(o)
		Crumb.sizer = makeTextSize(getTextStyle(o), p)
		o.style.width = getTextSize(tag, Crumb.sizer) + 20 + 'px'
	},
	mouseover: function() { addClass(this, 'crumb-focus') },
	mouseout: function() { if(!this.focused) rmClass(this, 'crumb-focus') },
	focus: function() { this.focused = true; addClass(this, 'crumb-focus') },
	blur: function() {
		if (this.submitting) return false
		this.focused = false
		this.value = this.originalValue
		rmClass(this, 'crumb-focus')
		this.style.width = getTextSize(this.value, Crumb.sizer) + 20 + 'px'
	},
	keyhandler: function(e) { e = e||window.event
		if (e.type == 'keypress' && e.keyCode == 13) {
			var tag = this.value.replace(/ +/g, '+')
			if (tag) {
				this.submitting = true
				location.href = this.root + tag
		}}
		this.style.width = getTextSize(this.value, Crumb.sizer) + 20 + 'px'
}}

function getTextStyle(o){
	return { fontSize: getStyle(o, 'font-size'), fontFamily: getStyle(o, 'font-family'), fontWeight: getStyle(o, 'font-weight') }
}
function makeTextSize(style, appendTo){
	style = extend({zborder: '1px solid red', visibility: 'hidden', position: 'absolute', top: 0, left: 0}, style)
	var div = create('div', {style: style})
	appendTo.appendChild(div)
	return div
}
function getTextSize(text, o){
	o.innerHTML = text.escHtml().replace(/ /g, '&nbsp;')
	return o.offsetWidth
}
function getTextWidth(text, style, appendTo){
	style = extend({border: '1px solid red', zvisibility: 'hidden', position: 'absolute', top: 0, left: 0}, style)
	var div = create('div', {style: style, html: text.escHtml().replace(/ /g, '&nbsp;')})
	appendTo.appendChild(div)
	var w = div.offsetWidth
	remove(div)
	return w
}
function addClass(o,klass){ if(!isA(o,klass)) o.className += ' ' + klass }
function getStyle(o,s) {
	if (document.defaultView && document.defaultView.getComputedStyle) return document.defaultView.getComputedStyle(o,null).getPropertyValue(s)
	else if (o.currentStyle) { return o.currentStyle[s.replace(/-([^-])/g, function(a,b){return b.toUpperCase()})] }
}
function isA(o,klass){ if(!o.className) return false; return new RegExp('\\b'+klass+'\\b').test(o.className) }
function rmClass(o,klass){ o.className = o.className.replace(new RegExp('\\s*\\b'+klass+'\\b'),'') }
