/* drag-mock library
https://github.com/andywer/drag-mock
version 1.4.0

The MIT License (MIT)

Copyright (c) 2015 Andy Wermke

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/


!function t(e,n,r){function a(i,u){if(!n[i]){if(!e[i]){var s="function"==typeof require&&require;if(!u&&s)return s(i,!0);if(o)return o(i,!0);var c=new Error("Cannot find module '"+i+"'");throw c.code="MODULE_NOT_FOUND",c}var f=n[i]={exports:{}};e[i][0].call(f.exports,function(t){var n=e[i][1][t];return a(n?n:t)},f,f.exports,t,e,n,r)}return n[i].exports}for(var o="function"==typeof require&&require,i=0;i<r.length;i++)a(r[i]);return a}({1:[function(t){var e=t("./src/index.js");"function"==typeof define&&define("dragMock",function(){return e}),window.dragMock=e},{"./src/index.js":5}],2:[function(t,e){function n(t,e){var n=t.indexOf(e);n>=0&&t.splice(n,1)}var r=function(){this.dataByFormat={},this.dropEffect="none",this.effectAllowed="all",this.files=[],this.types=[]};r.prototype.clearData=function(t){t?(delete this.dataByFormat[t],n(this.types,t)):(this.dataByFormat={},this.types=[])},r.prototype.getData=function(t){return this.dataByFormat[t]},r.prototype.setData=function(t,e){return this.dataByFormat[t]=e,this.types.indexOf(t)<0&&this.types.push(t),!0},r.prototype.setDragImage=function(){},e.exports=r},{}],3:[function(t,e){function n(){}function r(t,e,r){if("function"==typeof e&&(r=e,e=null),!t||"object"!=typeof t)throw new Error("Expected first parameter to be a targetElement. Instead got: "+t);return{targetElement:t,eventProperties:e||{},configCallback:r||n}}function a(t,e,n){e&&(e.length<2?n&&e(t):e(t,t.type))}function o(t,e,n,r,o,u){e.forEach(function(e){var s=i.createEvent(e,o,r),c=e===n;a(s,u,c),t.dispatchEvent(s)})}var i=t("./eventFactory"),u=t("./DataTransfer"),s=function(){this.lastDragSource=null,this.lastDataTransfer=null,this.pendingActionsQueue=[]};s.prototype._queue=function(t){this.pendingActionsQueue.push(t),1===this.pendingActionsQueue.length&&this._queueExecuteNext()},s.prototype._queueExecuteNext=function(){if(0!==this.pendingActionsQueue.length){var t=this,e=this.pendingActionsQueue[0],n=function(){t.pendingActionsQueue.shift(),t._queueExecuteNext()};0===e.length?(e.call(this),n()):e.call(this,n)}},s.prototype.dragStart=function(t,e,n){var a=r(t,e,n),i=["mousedown","dragstart","drag"],s=new u;return this._queue(function(){o(a.targetElement,i,"drag",s,a.eventProperties,a.configCallback),this.lastDragSource=t,this.lastDataTransfer=s}),this},s.prototype.dragOver=function(t,e,n){var a=r(t,e,n),i=["mousemove","mouseover","dragover"];return this._queue(function(){o(a.targetElement,i,"drag",this.lastDataTransfer,a.eventProperties,a.configCallback)}),this},s.prototype.dragLeave=function(t,e,n){var a=r(t,e,n),i=["mousemove","mouseover","dragleave"];return this._queue(function(){o(a.targetElement,i,"dragleave",this.lastDataTransfer,a.eventProperties,a.configCallback)}),this},s.prototype.drop=function(t,e,n){var a=r(t,e,n),i=["mousemove","mouseup","drop"],u=["dragend"];return this._queue(function(){o(a.targetElement,i,"drop",this.lastDataTransfer,a.eventProperties,a.configCallback),this.lastDragSource&&o(this.lastDragSource,u,"drop",this.lastDataTransfer,a.eventProperties,a.configCallback)}),this},s.prototype.then=function(t){return this._queue(function(){t.call(this)}),this},s.prototype.delay=function(t){return this._queue(function(e){window.setTimeout(e,t)}),this},e.exports=s},{"./DataTransfer":2,"./eventFactory":4}],4:[function(t,e){function n(t,e){for(var n in e)e.hasOwnProperty(n)&&(t[n]=e[n]);return t}function r(t,e,r){"DragEvent"===e&&(e="CustomEvent");var a=window[e],o={view:window,bubbles:!0,cancelable:!0};n(o,r);var i=new a(t,o);return n(i,r),i}function a(t,e,r){var a;switch(e){case"MouseEvent":a=document.createEvent("MouseEvent"),a.initEvent(t,!0,!0);break;default:a=document.createEvent("CustomEvent"),a.initCustomEvent(t,!0,!0,0)}return r&&n(a,r),a}function o(t,e,n){try{return r(t,e,n)}catch(o){return a(t,e,n)}}var i=t("./DataTransfer"),u=["drag","dragstart","dragover","dragend","drop","dragleave"],s={createEvent:function(t,e,n){var r="CustomEvent";t.match(/^mouse/)&&(r="MouseEvent");var a=o(t,r,e);return u.indexOf(t)>-1&&(a.dataTransfer=n||new i),a}};e.exports=s},{"./DataTransfer":2}],5:[function(t,e){function n(t,e,n){return t[e].apply(t,n)}var r=t("./DragDropAction"),a={dragStart:function(){return n(new r,"dragStart",arguments)},dragOver:function(){return n(new r,"dragOver",arguments)},dragLeave:function(){return n(new r,"dragLeave",arguments)},drop:function(){return n(new r,"drop",arguments)},delay:function(){return n(new r,"delay",arguments)},DataTransfer:t("./DataTransfer"),DragDropAction:t("./DragDropAction"),eventFactory:t("./eventFactory")};e.exports=a},{"./DataTransfer":2,"./DragDropAction":3,"./eventFactory":4}]},{},[1]);