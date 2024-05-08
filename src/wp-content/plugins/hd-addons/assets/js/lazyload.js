/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/vanilla-lazyload/dist/lazyload.min.js":
/*!************************************************************!*\
  !*** ./node_modules/vanilla-lazyload/dist/lazyload.min.js ***!
  \************************************************************/
/***/ (function(module) {

!function(e,t){ true?module.exports=t():0}(this,(function(){"use strict";const e="undefined"!=typeof window,t=e&&!("onscroll"in window)||"undefined"!=typeof navigator&&/(gle|ing|ro)bot|crawl|spider/i.test(navigator.userAgent),a=e&&window.devicePixelRatio>1,n={elements_selector:".lazy",container:t||e?document:null,threshold:300,thresholds:null,data_src:"src",data_srcset:"srcset",data_sizes:"sizes",data_bg:"bg",data_bg_hidpi:"bg-hidpi",data_bg_multi:"bg-multi",data_bg_multi_hidpi:"bg-multi-hidpi",data_bg_set:"bg-set",data_poster:"poster",class_applied:"applied",class_loading:"loading",class_loaded:"loaded",class_error:"error",class_entered:"entered",class_exited:"exited",unobserve_completed:!0,unobserve_entered:!1,cancel_on_exit:!0,callback_enter:null,callback_exit:null,callback_applied:null,callback_loading:null,callback_loaded:null,callback_error:null,callback_finish:null,callback_cancel:null,use_native:!1,restore_on_error:!1},s=e=>Object.assign({},n,e),l=function(e,t){let a;const n="LazyLoad::Initialized",s=new e(t);try{a=new CustomEvent(n,{detail:{instance:s}})}catch(e){a=document.createEvent("CustomEvent"),a.initCustomEvent(n,!1,!1,{instance:s})}window.dispatchEvent(a)},o="src",r="srcset",i="sizes",d="poster",c="llOriginalAttrs",_="data",u="loading",g="loaded",b="applied",h="error",m="native",p="data-",f="ll-status",v=(e,t)=>e.getAttribute(p+t),E=e=>v(e,f),I=(e,t)=>((e,t,a)=>{const n=p+t;null!==a?e.setAttribute(n,a):e.removeAttribute(n)})(e,f,t),y=e=>I(e,null),k=e=>null===E(e),A=e=>E(e)===m,L=[u,g,b,h],w=(e,t,a,n)=>{e&&"function"==typeof e&&(void 0===n?void 0===a?e(t):e(t,a):e(t,a,n))},x=(t,a)=>{e&&""!==a&&t.classList.add(a)},C=(t,a)=>{e&&""!==a&&t.classList.remove(a)},O=e=>e.llTempImage,M=(e,t)=>{if(!t)return;const a=t._observer;a&&a.unobserve(e)},z=(e,t)=>{e&&(e.loadingCount+=t)},N=(e,t)=>{e&&(e.toLoadCount=t)},T=e=>{let t=[];for(let a,n=0;a=e.children[n];n+=1)"SOURCE"===a.tagName&&t.push(a);return t},R=(e,t)=>{const a=e.parentNode;a&&"PICTURE"===a.tagName&&T(a).forEach(t)},G=(e,t)=>{T(e).forEach(t)},D=[o],H=[o,d],V=[o,r,i],F=[_],j=e=>!!e[c],B=e=>e[c],J=e=>delete e[c],S=(e,t)=>{if(j(e))return;const a={};t.forEach((t=>{a[t]=e.getAttribute(t)})),e[c]=a},P=(e,t)=>{if(!j(e))return;const a=B(e);t.forEach((t=>{((e,t,a)=>{a?e.setAttribute(t,a):e.removeAttribute(t)})(e,t,a[t])}))},U=(e,t,a)=>{x(e,t.class_applied),I(e,b),a&&(t.unobserve_completed&&M(e,t),w(t.callback_applied,e,a))},$=(e,t,a)=>{x(e,t.class_loading),I(e,u),a&&(z(a,1),w(t.callback_loading,e,a))},q=(e,t,a)=>{a&&e.setAttribute(t,a)},K=(e,t)=>{q(e,i,v(e,t.data_sizes)),q(e,r,v(e,t.data_srcset)),q(e,o,v(e,t.data_src))},Q={IMG:(e,t)=>{R(e,(e=>{S(e,V),K(e,t)})),S(e,V),K(e,t)},IFRAME:(e,t)=>{S(e,D),q(e,o,v(e,t.data_src))},VIDEO:(e,t)=>{G(e,(e=>{S(e,D),q(e,o,v(e,t.data_src))})),S(e,H),q(e,d,v(e,t.data_poster)),q(e,o,v(e,t.data_src)),e.load()},OBJECT:(e,t)=>{S(e,F),q(e,_,v(e,t.data_src))}},W=["IMG","IFRAME","VIDEO","OBJECT"],X=(e,t)=>{!t||(e=>e.loadingCount>0)(t)||(e=>e.toLoadCount>0)(t)||w(e.callback_finish,t)},Y=(e,t,a)=>{e.addEventListener(t,a),e.llEvLisnrs[t]=a},Z=(e,t,a)=>{e.removeEventListener(t,a)},ee=e=>!!e.llEvLisnrs,te=e=>{if(!ee(e))return;const t=e.llEvLisnrs;for(let a in t){const n=t[a];Z(e,a,n)}delete e.llEvLisnrs},ae=(e,t,a)=>{(e=>{delete e.llTempImage})(e),z(a,-1),(e=>{e&&(e.toLoadCount-=1)})(a),C(e,t.class_loading),t.unobserve_completed&&M(e,a)},ne=(e,t,a)=>{const n=O(e)||e;ee(n)||((e,t,a)=>{ee(e)||(e.llEvLisnrs={});const n="VIDEO"===e.tagName?"loadeddata":"load";Y(e,n,t),Y(e,"error",a)})(n,(s=>{((e,t,a,n)=>{const s=A(t);ae(t,a,n),x(t,a.class_loaded),I(t,g),w(a.callback_loaded,t,n),s||X(a,n)})(0,e,t,a),te(n)}),(s=>{((e,t,a,n)=>{const s=A(t);ae(t,a,n),x(t,a.class_error),I(t,h),w(a.callback_error,t,n),a.restore_on_error&&P(t,V),s||X(a,n)})(0,e,t,a),te(n)}))},se=(e,t,n)=>{(e=>W.indexOf(e.tagName)>-1)(e)?((e,t,a)=>{ne(e,t,a),((e,t,a)=>{const n=Q[e.tagName];n&&(n(e,t),$(e,t,a))})(e,t,a)})(e,t,n):((e,t,n)=>{(e=>{e.llTempImage=document.createElement("IMG")})(e),ne(e,t,n),(e=>{j(e)||(e[c]={backgroundImage:e.style.backgroundImage})})(e),((e,t,n)=>{const s=v(e,t.data_bg),l=v(e,t.data_bg_hidpi),r=a&&l?l:s;r&&(e.style.backgroundImage=`url("${r}")`,O(e).setAttribute(o,r),$(e,t,n))})(e,t,n),((e,t,n)=>{const s=v(e,t.data_bg_multi),l=v(e,t.data_bg_multi_hidpi),o=a&&l?l:s;o&&(e.style.backgroundImage=o,U(e,t,n))})(e,t,n),((e,t,a)=>{const n=v(e,t.data_bg_set);if(!n)return;let s=n.split("|").map((e=>`image-set(${e})`));e.style.backgroundImage=s.join(),U(e,t,a)})(e,t,n)})(e,t,n)},le=e=>{e.removeAttribute(o),e.removeAttribute(r),e.removeAttribute(i)},oe=e=>{R(e,(e=>{P(e,V)})),P(e,V)},re={IMG:oe,IFRAME:e=>{P(e,D)},VIDEO:e=>{G(e,(e=>{P(e,D)})),P(e,H),e.load()},OBJECT:e=>{P(e,F)}},ie=(e,t)=>{(e=>{const t=re[e.tagName];t?t(e):(e=>{if(!j(e))return;const t=B(e);e.style.backgroundImage=t.backgroundImage})(e)})(e),((e,t)=>{k(e)||A(e)||(C(e,t.class_entered),C(e,t.class_exited),C(e,t.class_applied),C(e,t.class_loading),C(e,t.class_loaded),C(e,t.class_error))})(e,t),y(e),J(e)},de=["IMG","IFRAME","VIDEO"],ce=e=>e.use_native&&"loading"in HTMLImageElement.prototype,_e=(e,t,a)=>{e.forEach((e=>(e=>e.isIntersecting||e.intersectionRatio>0)(e)?((e,t,a,n)=>{const s=(e=>L.indexOf(E(e))>=0)(e);I(e,"entered"),x(e,a.class_entered),C(e,a.class_exited),((e,t,a)=>{t.unobserve_entered&&M(e,a)})(e,a,n),w(a.callback_enter,e,t,n),s||se(e,a,n)})(e.target,e,t,a):((e,t,a,n)=>{k(e)||(x(e,a.class_exited),((e,t,a,n)=>{a.cancel_on_exit&&(e=>E(e)===u)(e)&&"IMG"===e.tagName&&(te(e),(e=>{R(e,(e=>{le(e)})),le(e)})(e),oe(e),C(e,a.class_loading),z(n,-1),y(e),w(a.callback_cancel,e,t,n))})(e,t,a,n),w(a.callback_exit,e,t,n))})(e.target,e,t,a)))},ue=e=>Array.prototype.slice.call(e),ge=e=>e.container.querySelectorAll(e.elements_selector),be=e=>(e=>E(e)===h)(e),he=(e,t)=>(e=>ue(e).filter(k))(e||ge(t)),me=function(t,a){const n=s(t);this._settings=n,this.loadingCount=0,((e,t)=>{ce(e)||(t._observer=new IntersectionObserver((a=>{_e(a,e,t)}),(e=>({root:e.container===document?null:e.container,rootMargin:e.thresholds||e.threshold+"px"}))(e)))})(n,this),((t,a)=>{e&&(a._onlineHandler=()=>{((e,t)=>{var a;(a=ge(e),ue(a).filter(be)).forEach((t=>{C(t,e.class_error),y(t)})),t.update()})(t,a)},window.addEventListener("online",a._onlineHandler))})(n,this),this.update(a)};return me.prototype={update:function(e){const a=this._settings,n=he(e,a);var s,l;N(this,n.length),t?this.loadAll(n):ce(a)?((e,t,a)=>{e.forEach((e=>{-1!==de.indexOf(e.tagName)&&((e,t,a)=>{e.setAttribute("loading","lazy"),ne(e,t,a),((e,t)=>{const a=Q[e.tagName];a&&a(e,t)})(e,t),I(e,m)})(e,t,a)})),N(a,0)})(n,a,this):(l=n,(e=>{e.disconnect()})(s=this._observer),((e,t)=>{t.forEach((t=>{e.observe(t)}))})(s,l))},destroy:function(){this._observer&&this._observer.disconnect(),e&&window.removeEventListener("online",this._onlineHandler),ge(this._settings).forEach((e=>{J(e)})),delete this._observer,delete this._settings,delete this._onlineHandler,delete this.loadingCount,delete this.toLoadCount},loadAll:function(e){const t=this._settings;he(e,t).forEach((e=>{M(e,this),se(e,t,this)}))},restoreAll:function(){const e=this._settings;ge(e).forEach((t=>{ie(t,e)}))}},me.load=(e,t)=>{const a=s(t);se(e,a)},me.resetStatus=e=>{y(e)},e&&((e,t)=>{if(t)if(t.length)for(let a,n=0;a=t[n];n+=1)l(e,a);else l(e,t)})(me,window.lazyLoadOptions),me}));


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
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
/*!***************************************************************!*\
  !*** ./wp-content/plugins/hd-addons/resources/js/lazyload.js ***!
  \***************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vanilla_lazyload__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vanilla-lazyload */ "./node_modules/vanilla-lazyload/dist/lazyload.min.js");
/* harmony import */ var vanilla_lazyload__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vanilla_lazyload__WEBPACK_IMPORTED_MODULE_0__);

(function () {
  var ll = new (vanilla_lazyload__WEBPACK_IMPORTED_MODULE_0___default())({
    class_applied: "lz-applied",
    class_loading: "lz-loading",
    class_loaded: "lz-loaded",
    class_error: "lz-error",
    class_entered: "lz-entered",
    class_exited: "lz-exited",
    callback_loaded: function callback_loaded(el) {
      el.removeAttribute('data-src');
    },
    unobserve_entered: true
  });
})();
}();
/******/ })()
;
//# sourceMappingURL=lazyload.js.map