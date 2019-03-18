(window.webpackJsonp=window.webpackJsonp||[]).push([["autosize-textarea"],{"./src/Template/Layout/js/app/components/autosize-textarea.js":
/*!********************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/autosize-textarea.js ***!
  \********************************************************************/
/*! exports provided: default */
/*! all exports used */function(t,e,a){"use strict";a.r(e);var i=a(/*! autosize */"./node_modules/autosize/dist/autosize.js"),s=a.n(i);e.default={props:["value","reset-value"],template:'<textarea @input="handleChange" :value="text"></textarea>',data:()=>({text:"",originalValue:""}),watch:{text(){this.$nextTick(()=>{s()(this.$el)})},value(){this.originalValue=this.value}},mounted(){this.originalValue=this.value,this.text=this.value,this.$nextTick(()=>{s()(this.$el)})},methods:{handleChange(t){this.text=event.target.value,this.$emit("input",this.text)}}}}}]);
//# sourceMappingURL=autosize-textarea.bundle.js.map