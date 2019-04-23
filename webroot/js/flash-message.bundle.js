(window.webpackJsonp=window.webpackJsonp||[]).push([["flash-message"],{"./src/Template/Layout/js/app/components/flash-message.js":
/*!****************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/flash-message.js ***!
  \****************************************************************/
/*! exports provided: default */
/*! all exports used */function(e,i,s){"use strict";s.r(i),i.default={props:{timeout:{type:Number,default:4},isBlocking:{type:Boolean,default:!1},waitPanelAnimation:{type:Number,default:.5}},data:()=>({isVisible:!0,isDumpVisible:!1}),mounted(){this.$nextTick(()=>{this.isBlocking||setTimeout(()=>{this.hide()},1e3*this.timeout)})},methods:{hide(){this.isVisible=!this.isVisible,setTimeout(()=>{this.$refs.flashMessagesContainer.remove()},1e3*this.waitPanelAnimation)}}}}}]);
//# sourceMappingURL=flash-message.bundle.js.map