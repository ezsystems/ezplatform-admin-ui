!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t(require("react-dom")):"function"==typeof define&&define.amd?define(["react-dom"],t):"object"==typeof exports?exports.ezEmbedInlineConfig=t(require("react-dom")):(e.eZ=e.eZ||{},e.eZ.ezAlloyEditor=e.eZ.ezAlloyEditor||{},e.eZ.ezAlloyEditor.ezEmbedInlineConfig=t(e.ReactDOM))}("undefined"!=typeof self?self:this,function(e){return function(e){function t(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var n={};return t.m=e,t.c=n,t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=73)}({10:function(t,n){t.exports=e},12:function(e,t,n){"use strict";function r(e){if(Array.isArray(e)){for(var t=0,n=Array(e.length);t<e.length;t++)n[t]=e[t];return n}return Array.from(e)}function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var a=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),l=n(10),i=function(e){return e&&e.__esModule?e:{default:e}}(l),u=function(){function e(){o(this,e)}return a(e,[{key:"getStyles",value:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],t=Translator.trans("toolbar_config_base.heading_label",{},"alloy_editor");return{name:"styles",cfg:{showRemoveStylesItem:!1,styles:[{name:t+" 1",style:{element:"h1"}},{name:t+" 2",style:{element:"h2"}},{name:t+" 3",style:{element:"h3"}},{name:t+" 4",style:{element:"h4"}},{name:t+" 5",style:{element:"h5"}},{name:t+" 6",style:{element:"h6"}},{name:Translator.trans("toolbar_config_base.paragraph_label",{},"alloy_editor"),style:{element:"p"}},{name:Translator.trans("toolbar_config_base.formatted_label",{},"alloy_editor"),style:{element:"pre"}}].concat(r(e))}}}},{key:"getArrowBoxClasses",value:function(){return"ae-arrow-box ae-arrow-box-bottom ez-ae-arrow-box-left"}},{key:"setPosition",value:function(t){var n=t.editor.get("nativeEditor"),r=t.editorEvent.data.nativeEvent,o=r?new CKEDITOR.dom.element(t.editorEvent.data.nativeEvent.target):null,a=!!o&&n.widgets.getByElement(o),l=n.elementPath().block;return l&&!a||(l=o),l.is("li")&&(l=l.getParent()),e.setPositionFor.call(this,l,n)}}],[{key:"outlineTotalWidth",value:function(e){var t=parseInt(e.getComputedStyle("outline-offset"),10),n=parseInt(e.getComputedStyle("outline-width"),10);return isNaN(t)&&(t=1),t+n}},{key:"isEmpty",value:function(e){var t=[].concat(r(e.$.childNodes)),n=t.length,o=!!n&&t.every(function(e){return"#text"===e.nodeName&&!e.data.replace(/\u200B/g,"")}),a=1===n&&"br"===e.$.childNodes.item(0).localName;return 0===n||a||o}},{key:"setPositionFor",value:function(t,n){var r=t.getClientRect(),o=e.outlineTotalWidth(t),a=e.isEmpty(t),l=t,u=0;if(n.widgets.getByElement(t))u=r.left;else{a&&(t.appendHtml("<span>&nbsp;</span>"),l=t.findOne("span"));var s=document.createRange(),f=parseInt(t.$.scrollLeft,10);s.selectNodeContents(l.$),u=s.getBoundingClientRect().left+f,a&&l.remove()}var c=this.getWidgetXYPoint(r.left-o,r.top+t.getWindow().getScrollPosition().y-o,CKEDITOR.SELECTION_BOTTOM_TO_TOP),d=new CKEDITOR.dom.element(i.default.findDOMNode(this));return d.addClass("ae-toolbar-transition"),d.setStyles({left:u-o+"px",top:c[1]+"px"}),!0}}]),e}();t.default=u},73:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function o(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!=typeof t&&"function"!=typeof t?e:t}function a(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}Object.defineProperty(t,"__esModule",{value:!0});var l=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=n(12),u=function(e){return e&&e.__esModule?e:{default:e}}(i),s=function(e){function t(e){r(this,t);var n=o(this,(t.__proto__||Object.getPrototypeOf(t)).call(this,e));return n.name="embedinline",n.buttons=["ezembedupdate","ezblockremove"],n}return a(t,e),l(t,[{key:"test",value:function(e){var t=e.data.nativeEvent;if(!t)return!1;var n=new CKEDITOR.dom.element(t.target),r=e.editor.get("nativeEditor").widgets.getByElement(n);return!(!r||"ezembedinline"!==r.name)}}]),t}(u.default);t.default=s}}).default});
//# sourceMappingURL=ezEmbedInlineConfig.js.map