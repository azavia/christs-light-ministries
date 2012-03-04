/*  */
/**
 * Copyright (c) 2007 Peter Michaux. All rights reserved.
 * petermichaux@gmail.com
 * http://forkjavascript.org
 * Code licensed under the MIT License:
 * http://dev.michaux.ca/svn/fork/trunk/public/javascripts/fork/MIT-LICENSE
 */

var FORK = FORK || {};

FORK.Ajax = function(method, url, options) {
  
  this.setOptions(options);
  
  this.method = method.toUpperCase();

  this.request = FORK.Ajax.newXMLHttpRequest();
  if (!this.request) {return true;}

  this.aborted = false;

  var self = this;

  //this.timer;  
  if (this.options.timeout) {
    this.timer = setTimeout(function() {self.onTimeout();}, this.options.timeout);
  }

  this.request.onreadystatechange = function() {self.onReadyStateChange();};

  this.body = this.options.body || {};
  this.setMethod();

  this.body = (function(oBody) {
    var aBody = [];
    for (var p in oBody) {
      aBody.push(encodeURIComponent(p) + "=" + encodeURIComponent(oBody[p]));      
    }
    return ((aBody.length > 0) ? aBody.join("&") : null);
  })(this.body);

  var serialization = null;
  if (this.options.form) {
    serialization = FORK.Ajax.serializeForm(this.options.form);
  }

  if (this.body && serialization) {
    this.body = serialization + "&" + this.body;
  } else if (serialization) {
    this.body = serialization;
  }

  if (this.method === 'GET') {
    if (this.body) {
      url = url + ( url.match(/\?/) ? '&' : '?') + this.body;
    }
    this.body = null;
  }

  this.request.open(this.method, url, true);

  if (this.method === "POST") {
    this.request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  }

  if (this.options.headers) {
    for (p in this.options.headers) {
      this.request.setRequestHeader(p, this.options.headers[p]);
    }
  }
  
  this.request.send(this.body);
};

FORK.Ajax.prototype.setOptions = function(options) {
  this.options = options || {};
};

FORK.Ajax.prototype.setMethod = function() {
  if (this.method === 'GET') {
    this.body._uniqueId = (new Date()).getTime() + "" + FORK.Ajax.transactionId++;
  }
};

FORK.Ajax.transactionId = 0;


FORK.Ajax.newXMLHttpRequest = function() {
  var fs = [
    function() { return new ActiveXObject("Microsoft.XMLHTTP"); },
    function() { return new ActiveXObject("Msxml2.XMLHTTP"); },
    function() { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); },
    function() { return new XMLHttpRequest(); }
  ];

  for (var i=fs.length; i--; ) {
    try {
      var r = fs[i]();
      if (r) {
        FORK.Ajax.newXMLHttpRequest = fs[i];
        return r;
      }
    } catch (e) {}
  }

  (FORK.Ajax.newXMLHttpRequest = function() {return null;})();
};

FORK.Ajax.serializeForm = function(f) {
	if (typeof f == 'string') {
		f = document.getElementById(f) || document.forms[f];
	}

	var els = f.elements,
	    cereal = []; // the serialization of the form data into a string

	function add(n, v) { 
		cereal.push(encodeURIComponent(n) + "=" + encodeURIComponent(v));
	}

	for (var i=0, ilen=els.length; i<ilen; i++) {
		var el = els[i];
		if (!el.disabled) {
			switch (el.type) {
				case 'text': case 'password': case 'hidden': case 'textarea':
					add(el.name, el.value);
					break;
				case 'select-one':
					if (el.selectedIndex >= 0) {
						add(el.name, el.options[el.selectedIndex].value);
					}
					break;
				case 'select-multiple':
					for (var j=0, jlen=el.options.length; j<jlen; j++) {
					  var opt = el.options[j];
						if (opt.selected) {
							add(el.name, opt.value);
						}
					}
					break;
				case 'checkbox': case 'radio':
					if (el.checked) {
						add(el.name, el.value);
					}
					break;
			}
		}
	}
	if (this.button) {
	  add(this.button.name, this.button.value);
	  this.button = null;
	}
	return ((cereal.length > 0) ? cereal.join("&") : null);
};


FORK.Ajax.setButton = function(el) {
  this.button = {name:el.name, value:el.value};
};


FORK.Ajax.prototype.doCallback = function(sMethod) {
  if (this.options.scope) {
		this.options[sMethod].call(this.options.scope, this.request, this.options.argument);
	} else {
		this.options[sMethod](this.request, this.options.argument);
	}
};

FORK.Ajax.prototype.onReadyStateChange = function() {
  if (!this.aborted && this.request.readyState === 4) {
    if (this.timer) {clearTimeout(this.timer);}
    if (this.request) { // why this conditional?
      this.handleReadyState4();
    }
    this.request = null;
  }
  
};

FORK.Ajax.prototype.handleReadyState4 = function() {
  var request = this.request,
      options = this.options;

	var status; // holds the request status
  
	try {
		status = request.status;
	} catch(e) {
		status = 13030;
	}

  if (status == 12002 || // Server timeout
      status == 12029 || // dropped connections
      status == 12030 || // dropped connections
      status == 12031 || // dropped connections
      status == 12152 || // Connection closed by server.
      status == 13030) { // See above comments for variable status.
    this.request = {status: 0,
                    statusText: "communication failure",
                    argument: options.argument};
  }

  if (options.before) {
		this.doCallback("before");
  }

  this.status = status;
  this.middleCallback();

	if (options.after) {
		this.doCallback("after");
  }
}; // handleReadyState4()

FORK.Ajax.prototype.middleCallback = function() {
  if (this.options["on"+this.status]) {
		this.doCallback("on"+this.status);
  } else if (this.status >= 200 && this.status < 300 && this.options.onSuccess) {
		this.doCallback("onSuccess");
	} else if ((this.status < 200 || this.status >= 300) && this.options.onFailure) {
    this.doCallback("onFailure");	
	}	else if (this.options.onComplete) {
		this.doCallback("onComplete");
	}	
};

FORK.Ajax.prototype.abort = function() {
  this.aborted = true;
  this.request.abort();
  this.request = null;
};

FORK.Ajax.prototype.onTimeout = function() {
  this.aborted = true;
  this.request.abort();
  this.handleTimeout();
  this.request = null;
};

FORK.Ajax.prototype.handleTimeout = function() {
  if (this.options.before) {
    this.doCallback("before");
  }
  if (this.options.onTimeout) {
    this.doCallback("onTimeout");
  }
	if (this.options.after) {
	  this.doCallback("after");
	}
};


FORK.Ajax.isSupported = (function(){
  var en = false,
      x;

  try {
    if (typeof (function(){}).call === "function" &&
        (x = FORK.Ajax.newXMLHttpRequest()) && // yes just one equals sign
        x.readyState === 0) {
      en = true;
    }
  } catch(e) {
    en = false;
  }

  try {
    if (!x.setRequestHeader) {
      en = false;
    }
  } catch(e) {}


  function cannotPost() {
    var xhr = new XMLHttpRequest();
    try {
      xhr.send("asdf");
    } catch (e) {
      if (-1 !== e.toString().indexOf("Could not convert JavaScript argument arg 0 [nsIXMLHttpRequest.send]")) {
        return true;
      }
    }
    return false;
  }
  if (this.XMLHttpRequest && cannotPost()) {
    en = false;
  }
  
  return function(){return en;};
})();

// cookie.js ------------------------------------------------------------------

FORK.Cookie = function(name) {
  
  this.$name = name;

  var all = document.cookie;

  if (all === '') {return;}

  var start = all.indexOf(name + '=');
  if (start == -1) {return;}

  start += name.length + 1;
  var end = all.indexOf(';', start);
  if (end == -1) {end = all.length;}
  var val = all.substring(start, end);
  
  var crumbs = val.split('&'),
      crumb;
  for (var i=crumbs.length; i--; ) {
    crumb = crumbs[i].split(':');
    this[crumb[0]] = decodeURIComponent(crumb[1]);
  }
};


FORK.Cookie.prototype.store = function(days, path, domain, secure) {

  var val = '';
  
  for (var p in this) {
    if ((p.charAt(0) == '$') || ((typeof this[p]) == 'function')) {
      continue;
    }

    if (val !== '') {
      val += '&';
    }

    val += p + ':' + encodeURIComponent(this[p]);
  }

  var cookie = this.$name + '=' + val;
  
  if (days === 0) { 
    cookie += "; expires=Fri, 02-Jan-1970 00:00:00 GMT";
  } else if (days) { 
    cookie += "; expires=" + ((new Date((new Date()).getTime() + days*86400000)).toUTCString());
  } 

  if (path) {cookie += "; path=" + path;}
  if (domain) {cookie += "; domain=" + domain;}
  if (secure) {cookie += "; secure";}

  document.cookie = cookie;
};


FORK.Cookie.prototype.remove = function(path, domain, secure) {
  for (var p in this) {
    if (p.charAt(0) != '$' && typeof this[p] != 'function') {
      delete this[p];
    }
  }
  this.store(0, path, domain, secure);
};


// FORK.Cookie.isSupported = (function() {
//   var en = false;
//   document.cookie = "FORKtestcookie=test";  // Set test cookie
//   if ( (document.cookie.indexOf("FORKtestcookie=test") !== -1) &&
//        this.encodeURIComponent) { // note that here "this" refers to the global/window object
//     en = true;
//     document.cookie = "FORKtestcookie=test; expires=Fri, 02-Jan-1970 00:00:00 GMT";  // Delete test cookie
//   }
//   return function() {return en;};
// })();

// dom.js ---------------------------------------------------------------------

FORK.Dom = {
  
  getElementsBy: function(method, tag, root) {
    tag = tag || '*';
    if (typeof root == "string") { root = document.getElementById(root); }
    root = root || document;

    var nodes = [];
    var elements = root.getElementsByTagName(tag);

    if ( !elements.length && tag == '*' && root.all ) {
      elements = root.all; // IE < 6
    }

    for (var i=0, len=elements.length; i<len; ++i) {
      if (method(elements[i])) { nodes[nodes.length] = elements[i]; }
    }

    return nodes;
  },

  hasClass: function(el, className) {
    if (typeof el == 'string') { el = document.getElementById(el);}
    var re = new RegExp('(?:^|\\s+)' + className + '(?:\\s+|$)');
    return re.test(el.className);
  },

  getElementsByClass: function(className, options) {
    options = options || {};
    var thisC = this;
    var method = function(el) { return thisC.hasClass(el, className); };
    return this.getElementsBy(method, options.tag, options.root);
  },

  addClass: function(el, className) {
    if (typeof el == 'string') { el = document.getElementById(el);}
    if (this.hasClass(el, className)) { return; } // already present
    el.className = [el.className, className].join(' ');
  },

  removeClass: function(el, className) {
    if (typeof el == 'string') { el = document.getElementById(el);}
    if (!this.hasClass(el, className)) { return; } // not present
    var re = new RegExp('(?:^|\\s+)' + className + '(?:\\s+|$)', 'g');
    var c = el.className;
    el.className = c.replace(re, ' ');
    if ( this.hasClass(el, className) ) { // in case of multiple adjacent
      this.removeClass(el, className);
    }
  },
  
  isSupported: (function() {
    var re = /(?:^|\s+)a(?:\s+|$)/g,
        en = false;

    if (document.getElementById &&
        typeof RegExp === "function" &&
        typeof "".replace === "function" &&
        "a".match(re) // Opera 6.0.6 doesn't syntax error on the regular expression above but this match will return null
      ) {
      en = true;
    }    
    return function() {return en;};
  })()

};

// mutate.js ------------------------------------------------------------------

FORK.Mutate = {

  scriptRegExp: /<script.*?>((\n|\r|.)*?)<\/script>/img,

  getScripts: function(html) {
    var ss = [], // an array of found scripts
        match;
    while (match = this.scriptRegExp.exec(html)) { // yes really just one equals sign
      ss.push(match[1]);
    }
    return ss;
  },

  stripScripts: function(html) {
    return html.replace(this.scriptRegExp, '');
  },

  evalScript: function() {
    eval(arguments[0]);
  },

  evalScripts: function(scripts) {
    for (var i=0, l=scripts.length; i<l; i++) {
      this.evalScript(scripts[i]);
    }
  },

  update: function(el, html, evalScripts) {
    if (typeof el == "string") {el=document.getElementById(el);}
    for (i=el.childNodes.length; i--; ) {
      this.remove(el.childNodes[i]);
    }
    this.insertBottom(el, html, evalScripts);
  },

  replace: function(el, html, evalScripts) {
    if (typeof el == "string") {el=document.getElementById(el);}
    var ss = this.getScripts(html);
    html = this.stripScripts(html);

    var parent = el.parentNode;
    var next = el.nextSibling;

    this.remove(el);

    var m,
        n = this.outsideParser(el,html).firstChild;
    while (m = n) { // Yes really just one equals sign. This saves a line of code.
       n = m.nextSibling;
       parent.insertBefore(m, next);
     }

     if (evalScripts === undefined || evalScripts === 'eval') {
       this.evalScripts(ss);
     }
  },

  outsideParser: function(el, html) {
    var p = document.createElement('div'),
    tagName = el.tagName.toLowerCase();

    if (tagName.match(/t(body|head|foot)/)) { // must be trying to replace the tbody, thead, or tfoot
      p.innerHTML = '<table>' + html + '</table>';
      p = p.childNodes[0];
    } else if (tagName == 'tr') { // must be trying to replace one row with one or more rows
      p.innerHTML = '<table><tbody>' + html + '</tbody></table>';
      p = p.childNodes[0].childNodes[0];
    } else if (tagName == 'td') { // must be trying to replace one data cell with one or more cells
      p.innerHTML = '<table><tbody><tr>' + html + '</tr></tbody></table>';
      p = p.childNodes[0].childNodes[0].childNodes[0];
    } else {
      p.innerHTML = html;
    }

    return p;
  },
  
  insideParser: function(el, html) {
    var p = document.createElement('div'),
        tagName = el.tagName.toLowerCase();

    if (tagName == 'table') { // must be trying to insert tbody, thead, or tfoot
      p.innerHTML = '<table>' + html + '</table>';
      p = p.childNodes[0];
    } else if (tagName.match(/t(head|body|foot)/)) { // must be trying to insert rows.
      p.innerHTML = '<table><tbody>' + html + '</tbody></table>';
      p = p.childNodes[0].childNodes[0];
    } else if (tagName == 'tr') { // must be trying to insert some td elements
      p.innerHTML = '<table><tbody><tr>' + html + '</tr></tbody></table>';
      p = p.childNodes[0].childNodes[0].childNodes[0];
    } else {
      p.innerHTML = html;
    }
    return p;
  },

  _insert: function(el, html, evalScripts, parser, inserter) {
    if (typeof el == "string") {el=document.getElementById(el);}
    var ss = this.getScripts(html);
    html = this.stripScripts(html);

    inserter(el, parser(el, html));
    
    if (evalScripts === undefined || evalScripts === 'eval') {
      this.evalScripts(ss);
    }
  },

  insertBefore: function(el, html, evalScripts) {
    this._insert(el, html, evalScripts, this.outsideParser, 
                 function(el, p) {
                   var m,
                       n = p.firstChild;
                   while (m = n) { // yes just one equals sign. saves a line of code
                     n = m.nextSibling;
                     el.parentNode.insertBefore(m, el);
                   }

                 });
  },

  insertTop: function(el, html, evalScripts) {
    this._insert(el, html, evalScripts, this.insideParser,
                 function(el, p) {
                   var ns = p.childNodes;
                   for (var i=ns.length; i--; ){
                     el.insertBefore(ns[i], el.firstChild);
                   }
                 });
  },
  
  insertBottom: function(el, html, evalScripts) {
    this._insert(el, html, evalScripts, this.insideParser,
                 function(el, p) {
                   var m,
                       n = p.firstChild;
                   while (m = n) { // yes just one equals sign. saves a line of code
                     n = m.nextSibling;
                     el.appendChild(m);
                   }
                 });
  },

  insertAfter: function(el, html, evalScripts) {
    this._insert(el, html, evalScripts, this.outsideParser,
                 function(el, p) {
                   var ns = p.childNodes;
                   for (var i=ns.length; i--; ){
                     el.parentNode.insertBefore(ns[i], el.nextSibling);
                   }
                 });
  },

  remove: function(el) {
    if (typeof el == "string") {el=document.getElementById(el);}
    var fe = FORK.Event;
    if (fe && fe.purgeElement) {
      fe.purgeElement(el, {deep:true});
    }
    el.parentNode.removeChild(el);
  },

  isSupported: function() {
    var en = false,
        b;

    if (document.getElementById &&
        typeof "".replace === "function" &&
        document.createElement &&
        (b = document.createElement('div')) && // yes just one equals sign
        typeof b.innerHTML === 'string') {
      en = true;
    }

    FORK.Mutate.isSupported = function() {return en;};
    return en;
  }

};

// scroll.js ------------------------------------------------------------------

FORK.Scroll = {
  
  getX: function() {
    FORK.Scroll.setup();
    return FORK.Scroll.getX();
  },
  
  getY: function() {
    FORK.Scroll.setup();
    return FORK.Scroll.getY();
  },
  
  setup: (function(){
    var global = this;

    return function() {
      var readScroll,
          readScrollY = 'scrollTop',
          readScrollX = 'scrollLeft';

      if (typeof global.pageXOffset == 'number') {
        readScroll = global;
        readScrollY = 'pageYOffset';
        readScrollX = 'pageXOffset';

      } else if ((typeof document.compatMode === 'string') &&
                 (document.compatMode.indexOf('CSS') >= 0) &&
                 (document.documentElement) &&
                 (typeof document.documentElement.scrollLeft=='number')) {

        readScroll =  document.documentElement;

      } else if ((document.body) &&
                 (typeof document.body.scrollLeft === 'number')) {
        readScroll =  document.body;    
      } else {
        FORK.Scroll.getX = FORK.Scroll.getY = function() {return NaN;};
        return;
      }
      FORK.Scroll.getX = function() {return readScroll[readScrollX];};
      FORK.Scroll.getY = function() {return readScroll[readScrollY];};  
    };
    
  })(),
  
  isSupported: function() {
    var en = true;
    
    if (isNaN(FORK.Scroll.getX())) {
      en = false;
    }
    
    FORK.Scroll.isSupported = function() {return en;};
    return en;
  }

};

// event.js -------------------------------------------------------------------

FORK.Event = {
  
  listeners: [],

  unloadListeners: [],

  _useLegacyListener: function(type) {
    return (type === 'click' || type == 'dblclick'); // use === once here to get NN4 to syntax error asap
  },

  addListener: function(el, type, fn, options) {
    if (!this._isSupported()) {return false;}
    if (typeof el == "string") { el = document.getElementById(el); }
    options = options || {};
    
    var obj = {el:el, type:type, fn:fn, options:options};
    var scope = (options.scope) ? options.scope : el;
    var argument = options.argument;
    obj.wrappedFn = function(e) {
                      return fn.call(scope, e, argument);
                    };
    
    if ("unload" == type && this.unloadListenerAttached) {
      if (this._getCacheIndex(this.unloadListeners, el, type, fn) < 0) {
        this.unloadListeners.push(obj);
      }
      return;
    }

    var attached = false;
    
    if (this._useLegacyListener(type)) {
      if (!el['on' + type] ||
          !el['on' + type].legacyListeners) { // overwrites existing DOM0 event handler.
                                              // TODO write docs about this.

        el['on' + type] = function(e) {
                             e = e || window.event;
                             var lls = arguments.callee.legacyListeners;
                             for (var i=0, len=lls.length; i<len; i++) {
                               var l = lls[i];
                               if (l) {
                                 try {
                                   l.wrappedFn(e);
                                 } catch (err) {}
                               }
                             }
                           };

        el['on' + type].legacyListeners = [];

      } else if (this._getCacheIndex(el['on' + type].legacyListeners, el, type, fn) >= 0) {
        return;
      }

      el['on' + type].legacyListeners.push(obj);
      
      attached = true;
      
    } else if (el.addEventListener) {
        el.addEventListener(type, obj.wrappedFn, false);
        attached = true;
    } else if (el.attachEvent) {
        el.attachEvent("on" + type, obj.wrappedFn);
        attached = true;
    }

    if (attached) {
      this.listeners.push(obj);

      if ("unload"==type && options.scope==this) {this.unloadListenerAttached = true;}
    }
  },
  
  removeListener: function(el, type, fn) {
    if (typeof el == "string") { el = document.getElementById(el); }

    var cache = (type=='unload' ? this.unloadListeners : this.listeners);

    var i = this._getCacheIndex(cache, el, type, fn);
    if (i < 0) {return;} // not found
    var obj = cache[i];
    cache.splice(i, 1);

    if (type != 'unload') {
      if (this._useLegacyListener(type)) {
        i = this._getCacheIndex(el['on' + type].legacyListeners, el, type, fn);
        el['on' + type].legacyListeners.splice(i, 1);
        if (el['on' + type].legacyListeners.length < 1) {
          el['on' + type] = null;
        }
      } else if (el.removeEventListener) {
        el.removeEventListener(type, obj.wrappedFn, false);
      } else if (el.detachEvent) {
        el.detachEvent("on" + type, obj.wrappedFn);
      }
    }
    
    obj.fn = null;
    obj.wrappedFn = null;      
  },

  _getCacheIndex: function(arr, el, type, fn) {
    for (var i=arr.length; i--; ) {
      var li = arr[i];
      if ( li && li.el == el  && li.type == type && li.fn == fn ) {
        return i;
      }
    }
    return -1;
  },

  _unload: function(e) {
    e = e || window.event;
    var i, l, len;
    for (i=0,len=this.unloadListeners.length; i<len; ++i) {
      l = this.unloadListeners[i];
      if (l) {
        try {
          l.wrappedFn(e);
        } catch (err) {}
        l.fn = null;
        l.wrappedFn = null;
      }
    }
    for (i=this.listeners.length; i-- ; ) {
      var li = this.listeners[i];
      if (li) {
        this.removeListener(li.el, li.type, li.fn);
      } 
    }
  },
  
  purgeElement: function(el, options) {
    if (typeof el == 'string') { el = document.getElementById(el);}
    options = options || {};
    var i,
        elListeners = this._getListeners(el, options.type);

    for (i=elListeners.length; i--; ) {
      var l = elListeners[i];
      this.removeListener(el, l.type, l.fn);
    }
    
    if (options.deep && el.childNodes) {
      for (i=el.childNodes.length; i--; ) {
        this.purgeElement(el.childNodes[i], options);
      }
    }
  },

  _getListeners: function(el, type) {
    var elListeners = [];
    for (var i=this.listeners.length; i--;) {
      var l = this.listeners[i];
      if (l && l.el === el &&
          (!type || type === l.type) ) {
        elListeners.push(l);
      }
    }
    return elListeners;
  },
  
  stopPropagation: function(e) {
    if (e.stopPropagation) {
      e.stopPropagation();
      return true;
    }
    if (e.cancelBubble !== undefined) { // cancelBubble false by default
      e.cancelBubble = true;
      return true;
    }
    return false;
  },

  preventDefault: function(e) {
    if (e.preventDefault) {
      e.preventDefault();
      return true;
    }
    if (e.cancelBubble !== undefined){ // can't test returnValue directly?
      e.returnValue = false;
      return true;
    }
    return false;
  },

  getTarget: function(e) {
    var t = e.target || e.srcElement;
    return this.resolveTextNode(t);
  },

  resolveTextNode: function(node) {
    if (node && node.nodeName && "#TEXT" == node.nodeName.toUpperCase()) {
      return node.parentNode;
    }
    return node;
  },

  getRelatedTarget: function(e) {
    var t = e.relatedTarget;
    if (!t) {
      if (e.type == "mouseout") {
        t = e.toElement;
      } else if (e.type == "mouseover") {
        t = e.fromElement;
      }
    }
    return this.resolveTextNode(t);
  },

  getPageX: (function(){
              
              function page(e) {return e.pageX;} // Firefox, Safari
              var getX = FORK.Scroll.getX; // save two dot operations for efficiency
              function client(e) {return getX()+e.clientX;} // IE
              function not() {return NaN;}
              
              return function(e) {
                if (typeof e.pageX == 'number') {
                  FORK.Event.getPageX = page;
                } else if (FORK.Scroll && !isNaN(FORK.Scroll.getX()) && typeof e.clientX == 'number') {
                  FORK.Event.getPageX = client;
                } else {
                  FORK.Event.getPageX = not;
                }
                return FORK.Event.getPageX(e);
              };
              
             })(),

  getPageY: (function(){
              function page(e) {return e.pageY;}
              var getY = FORK.Scroll.getY;
              function client(e) {return getY()+e.clientY;}
              function not() {return NaN;}

              return function(e) {
                if (typeof e.pageY == 'number') {
                  FORK.Event.getPageY = page;
                } else if (FORK.Scroll && !isNaN(FORK.Scroll.getY()) && typeof e.clientY == 'number') {
                    FORK.Event.getPageY = client;
                } else {
                  FORK.Event.getPageY = not;
                }
                return FORK.Event.getPageY(e);
              };
            })(),

  _isSupported: (function() {
    var en = false;
    if (typeof (function(){}).call === "function" &&
        document.getElementById &&
        typeof([].splice) === "function" &&
        typeof([].push) === "function" &&
        (window.addEventListener || window.attachEvent)
       ) {
      en = true;
    }
    return function() {return en;};
  })(),
  
  isSupported: function() {
    var en = false;
    if (FORK.Event._isSupported() && FORK.Scroll && FORK.Scroll.isSupported()) {
      en = true;
    }
    FORK.Event.isSupported = function() {return en;};
    return en;
  }
  
};
/* handle IE memory leak on page unload */
FORK.Event.addListener(window, "unload", FORK.Event._unload, {scope: FORK.Event});

// extend.js ------------------------------------------------------------------

FORK.extend = function(sub, sup) {
   function F() {}
   F.prototype = sup.prototype;
   sub.prototype = new F();
   sub.prototype.constructor = sub;
   sub.superConstructor = sup;
   sub.superPrototype = sup.prototype; // not necessary but nice convenience
};

// part of style.js -----------------------------------------------------------

FORK.Style = {
  setOpacity: function(el, val) {
    if (typeof el == 'string') {el=document.getElementById(el);}
    if (val<0.00001) {val = 0;}
    var s = el.style;
    if (typeof s.filter == 'string') {
      s.filter = s.filter.replace(/alpha\([^\)]*\)/gi,'') + ((val < 1) ? 'alpha(opacity='+val*100+')' : '');

      if (!el.currentStyle ||
          !el.currentStyle.hasLayout) {
        el.style.zoom = 1;
      }
    } else {
      s.opacity = val;
      s.MozOpacity = val;
      s.KhtmlOpacity = val;
    }
  }
};
