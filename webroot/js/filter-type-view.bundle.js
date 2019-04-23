(window.webpackJsonp=window.webpackJsonp||[]).push([["filter-type-view"],{"./src/Template/Layout/js/app/components/filter-type.js":
/*!**************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/filter-type.js ***!
  \**************************************************************/
/*! exports provided: default */
/*! all exports used */function(e,t,s){"use strict";s.r(t),t.default={props:{types:{type:Array,default:[]}},data:()=>({selectedTypes:()=>[]}),mounted(){let e=new URLSearchParams(window.location.search).entries(),t=[];for(let a of e){var s=a[0],p=a[1];s.startsWith("filter[type]")&&t.push(p)}this.selectedTypes=t},computed:{},watch:{selectedTypes(e){this.$emit("updatequerytypes",e)}},methods:{togglaAll(){this.selectedTypes.length&&(this.selectedTypes=[])}}}}}]);
//# sourceMappingURL=filter-type-view.bundle.js.map