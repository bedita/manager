(window.webpackJsonp=window.webpackJsonp||[]).push([["menu"],{"./src/Template/Layout/js/app/components/menu.js":
/*!*******************************************************!*\
  !*** ./src/Template/Layout/js/app/components/menu.js ***!
  \*******************************************************/
/*! exports provided: default */
/*! all exports used */function(t,o,p){"use strict";p.r(o),o.default={data:()=>({popUpAction:"",searchString:""}),methods:{togglePopup(t){t==this.popUpAction?this.popUpAction="":(this.popUpAction=t,this.$nextTick(()=>{this.$refs.searchInput.focus()}))},captureKeys(t){switch(t.which||t.keyCode||0){case 13:this.go();break;case 27:this.popUpAction=""}},go(){let t="";"search"==this.popUpAction?t+="/objects?q=":"go"==this.popUpAction&&(t+="/view/"),this.searchString&&t&&(window.location.href=BEDITA.base+t+this.searchString)}}}}}]);
//# sourceMappingURL=menu.bundle.js.map