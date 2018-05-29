(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["vendors"],{

/***/ "./node_modules/decamelize/index.js":
/*!******************************************!*\
  !*** ./node_modules/decamelize/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

const xRegExp = __webpack_require__(/*! xregexp */ "./node_modules/xregexp/lib/index.js");

module.exports = (text, separator) => {
	if (typeof text !== 'string') {
		throw new TypeError('Expected a string');
	}

	separator = typeof separator === 'undefined' ? '_' : separator;

	const regex1 = xRegExp('([\\p{Ll}\\d])(\\p{Lu})', 'g');
	const regex2 = xRegExp('(\\p{Lu}+)(\\p{Lu}[\\p{Ll}\\d]+)', 'g');

	return text
		// TODO: Use this instead of `xregexp` when targeting Node.js 10:
		// .replace(/([\p{Lowercase_Letter}\d])(\p{Uppercase_Letter})/gu, `$1${separator}$2`)
		// .replace(/(\p{Lowercase_Letter}+)(\p{Uppercase_Letter}[\p{Lowercase_Letter}\d]+)/gu, `$1${separator}$2`)
		.replace(regex1, `$1${separator}$2`)
		.replace(regex2, `$1${separator}$2`)
		.toLowerCase();
};


/***/ }),

/***/ "./node_modules/flatpickr/dist/flatpickr.min.css":
/*!*******************************************************!*\
  !*** ./node_modules/flatpickr/dist/flatpickr.min.css ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./node_modules/flatpickr/dist/flatpickr.min.js":
/*!******************************************************!*\
  !*** ./node_modules/flatpickr/dist/flatpickr.min.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* flatpickr v4.4.7,, @license MIT */
!function(e,t){ true?module.exports=t():undefined}(this,function(){"use strict";var Q=function(e){return("0"+e).slice(-2)},X=function(e){return!0===e?1:0};function ee(n,a,i){var o;return void 0===i&&(i=!1),function(){var e=this,t=arguments;null!==o&&clearTimeout(o),o=window.setTimeout(function(){o=null,i||n.apply(e,t)},a),i&&!o&&n.apply(e,t)}}var te=function(e){return e instanceof Array?e:[e]},e=function(){},ne=function(e,t,n){return n.months[t?"shorthand":"longhand"][e]},D={D:e,F:function(e,t,n){e.setMonth(n.months.longhand.indexOf(t))},G:function(e,t){e.setHours(parseFloat(t))},H:function(e,t){e.setHours(parseFloat(t))},J:function(e,t){e.setDate(parseFloat(t))},K:function(e,t,n){e.setHours(e.getHours()%12+12*X(new RegExp(n.amPM[1],"i").test(t)))},M:function(e,t,n){e.setMonth(n.months.shorthand.indexOf(t))},S:function(e,t){e.setSeconds(parseFloat(t))},U:function(e,t){return new Date(1e3*parseFloat(t))},W:function(e,t){var n=parseInt(t);return new Date(e.getFullYear(),0,2+7*(n-1),0,0,0,0)},Y:function(e,t){e.setFullYear(parseFloat(t))},Z:function(e,t){return new Date(t)},d:function(e,t){e.setDate(parseFloat(t))},h:function(e,t){e.setHours(parseFloat(t))},i:function(e,t){e.setMinutes(parseFloat(t))},j:function(e,t){e.setDate(parseFloat(t))},l:e,m:function(e,t){e.setMonth(parseFloat(t)-1)},n:function(e,t){e.setMonth(parseFloat(t)-1)},s:function(e,t){e.setSeconds(parseFloat(t))},w:e,y:function(e,t){e.setFullYear(2e3+parseFloat(t))}},ae={D:"(\\w+)",F:"(\\w+)",G:"(\\d\\d|\\d)",H:"(\\d\\d|\\d)",J:"(\\d\\d|\\d)\\w+",K:"",M:"(\\w+)",S:"(\\d\\d|\\d)",U:"(.+)",W:"(\\d\\d|\\d)",Y:"(\\d{4})",Z:"(.+)",d:"(\\d\\d|\\d)",h:"(\\d\\d|\\d)",i:"(\\d\\d|\\d)",j:"(\\d\\d|\\d)",l:"(\\w+)",m:"(\\d\\d|\\d)",n:"(\\d\\d|\\d)",s:"(\\d\\d|\\d)",w:"(\\d\\d|\\d)",y:"(\\d{2})"},c={Z:function(e){return e.toISOString()},D:function(e,t,n){return t.weekdays.shorthand[c.w(e,t,n)]},F:function(e,t,n){return ne(c.n(e,t,n)-1,!1,t)},G:function(e,t,n){return Q(c.h(e,t,n))},H:function(e){return Q(e.getHours())},J:function(e,t){return void 0!==t.ordinal?e.getDate()+t.ordinal(e.getDate()):e.getDate()},K:function(e,t){return t.amPM[X(11<e.getHours())]},M:function(e,t){return ne(e.getMonth(),!0,t)},S:function(e){return Q(e.getSeconds())},U:function(e){return e.getTime()/1e3},W:function(e,t,n){return n.getWeek(e)},Y:function(e){return e.getFullYear()},d:function(e){return Q(e.getDate())},h:function(e){return e.getHours()%12?e.getHours()%12:12},i:function(e){return Q(e.getMinutes())},j:function(e){return e.getDate()},l:function(e,t){return t.weekdays.longhand[e.getDay()]},m:function(e){return Q(e.getMonth()+1)},n:function(e){return e.getMonth()+1},s:function(e){return e.getSeconds()},w:function(e){return e.getDay()},y:function(e){return String(e.getFullYear()).substring(2)}},ie={weekdays:{shorthand:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],longhand:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]},months:{shorthand:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],longhand:["January","February","March","April","May","June","July","August","September","October","November","December"]},daysInMonth:[31,28,31,30,31,30,31,31,30,31,30,31],firstDayOfWeek:0,ordinal:function(e){var t=e%100;if(3<t&&t<21)return"th";switch(t%10){case 1:return"st";case 2:return"nd";case 3:return"rd";default:return"th"}},rangeSeparator:" to ",weekAbbreviation:"Wk",scrollTitle:"Scroll to increment",toggleTitle:"Click to toggle",amPM:["AM","PM"],yearAriaLabel:"Year"},oe=function(e){var t=e.config,o=void 0===t?w:t,n=e.l10n,r=void 0===n?ie:n;return function(a,e,t){if(void 0!==o.formatDate)return o.formatDate(a,e);var i=t||r;return e.split("").map(function(e,t,n){return c[e]&&"\\"!==n[t-1]?c[e](a,i,o):"\\"!==e?e:""}).join("")}},re=function(e){var t=e.config,h=void 0===t?w:t,n=e.l10n,v=void 0===n?ie:n;return function(e,t,n){if(0===e||e){var a,i=e;if(e instanceof Date)a=new Date(e.getTime());else if("string"!=typeof e&&void 0!==e.toFixed)a=new Date(e);else if("string"==typeof e){var o=t||(h||w).dateFormat,r=String(e).trim();if("today"===r)a=new Date,n=!0;else if(/Z$/.test(r)||/GMT$/.test(r))a=new Date(e);else if(h&&h.parseDate)a=h.parseDate(e,o);else{a=h&&h.noCalendar?new Date((new Date).setHours(0,0,0,0)):new Date((new Date).getFullYear(),0,1,0,0,0,0);for(var c,l=[],d=0,s=0,u="";d<o.length;d++){var f=o[d],m="\\"===f,g="\\"===o[d-1]||m;if(ae[f]&&!g){u+=ae[f];var p=new RegExp(u).exec(e);p&&(c=!0)&&l["Y"!==f?"push":"unshift"]({fn:D[f],val:p[++s]})}else m||(u+=".");l.forEach(function(e){var t=e.fn,n=e.val;return a=t(a,n,v)||a})}a=c?a:void 0}}if(a instanceof Date&&!isNaN(a.getTime()))return!0===n&&a.setHours(0,0,0,0),a;h.errorHandler(new Error("Invalid date provided: "+i))}}};function ce(e,t,n){return void 0===n&&(n=!0),!1!==n?new Date(e.getTime()).setHours(0,0,0,0)-new Date(t.getTime()).setHours(0,0,0,0):e.getTime()-t.getTime()}var le=function(e,t,n){return e>Math.min(t,n)&&e<Math.max(t,n)},de={DAY:864e5},w={_disable:[],_enable:[],allowInput:!1,altFormat:"F j, Y",altInput:!1,altInputClass:"form-control input",animate:"object"==typeof window&&-1===window.navigator.userAgent.indexOf("MSIE"),ariaDateFormat:"F j, Y",clickOpens:!0,closeOnSelect:!0,conjunction:", ",dateFormat:"Y-m-d",defaultHour:12,defaultMinute:0,defaultSeconds:0,disable:[],disableMobile:!1,enable:[],enableSeconds:!1,enableTime:!1,errorHandler:function(e){return"undefined"!=typeof console&&console.warn(e)},getWeek:function(e){var t=new Date(e.getTime());t.setHours(0,0,0,0),t.setDate(t.getDate()+3-(t.getDay()+6)%7);var n=new Date(t.getFullYear(),0,4);return 1+Math.round(((t.getTime()-n.getTime())/864e5-3+(n.getDay()+6)%7)/7)},hourIncrement:1,ignoredFocusElements:[],inline:!1,locale:"default",minuteIncrement:5,mode:"single",nextArrow:"<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z' /></svg>",noCalendar:!1,now:new Date,onChange:[],onClose:[],onDayCreate:[],onDestroy:[],onKeyDown:[],onMonthChange:[],onOpen:[],onParseConfig:[],onReady:[],onValueUpdate:[],onYearChange:[],onPreCalendarPosition:[],plugins:[],position:"auto",positionElement:void 0,prevArrow:"<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z' /></svg>",shorthandCurrentMonth:!1,showMonths:1,static:!1,time_24hr:!1,weekNumbers:!1,wrap:!1};function se(e,t,n){if(!0===n)return e.classList.add(t);e.classList.remove(t)}function ue(e,t,n){var a=window.document.createElement(e);return t=t||"",n=n||"",a.className=t,void 0!==n&&(a.textContent=n),a}function fe(e){for(;e.firstChild;)e.removeChild(e.firstChild)}function me(e,t){var n=ue("div","numInputWrapper"),a=ue("input","numInput "+e),i=ue("span","arrowUp"),o=ue("span","arrowDown");if(a.type="text",a.pattern="\\d*",void 0!==t)for(var r in t)a.setAttribute(r,t[r]);return n.appendChild(a),n.appendChild(i),n.appendChild(o),n}"function"!=typeof Object.assign&&(Object.assign=function(n){if(!n)throw TypeError("Cannot convert undefined or null to object");for(var e=arguments.length,a=new Array(1<e?e-1:0),t=1;t<e;t++)a[t-1]=arguments[t];for(var i=function(){var t=a[o];t&&Object.keys(t).forEach(function(e){return n[e]=t[e]})},o=0;o<a.length;o++)i();return n});var ge=300;function r(u,f){var p={config:Object.assign({},pe.defaultConfig),l10n:ie};function m(e){return e.bind(p)}function t(){var t=p.config;!1===t.weekNumbers&&1===t.showMonths||!0!==t.noCalendar&&window.requestAnimationFrame(function(){if(p.calendarContainer.style.visibility="hidden",p.calendarContainer.style.display="block",void 0!==p.daysContainer){var e=(p.days.offsetWidth+1)*t.showMonths;p.daysContainer.style.width=e+"px",p.calendarContainer.style.width=e+(void 0!==p.weekWrapper?p.weekWrapper.offsetWidth:0)+"px",p.calendarContainer.style.removeProperty("visibility"),p.calendarContainer.style.removeProperty("display")}})}function d(e){0!==p.selectedDates.length&&(!function(e){e.preventDefault();var t="keydown"===e.type,n=e.target;void 0!==p.amPM&&e.target===p.amPM&&(p.amPM.textContent=p.l10n.amPM[X(p.amPM.textContent===p.l10n.amPM[0])]);var a=parseFloat(n.getAttribute("data-min")),i=parseFloat(n.getAttribute("data-max")),o=parseFloat(n.getAttribute("data-step")),r=parseInt(n.value,10),c=e.delta||(t?38===e.which?1:-1:0),l=r+o*c;if(void 0!==n.value&&2===n.value.length){var d=n===p.hourElement,s=n===p.minuteElement;l<a?(l=i+l+X(!d)+(X(d)&&X(!p.amPM)),s&&v(void 0,-1,p.hourElement)):i<l&&(l=n===p.hourElement?l-i-X(!p.amPM):a,s&&v(void 0,1,p.hourElement)),p.amPM&&d&&(1===o?l+r===23:Math.abs(l-r)>o)&&(p.amPM.textContent=p.l10n.amPM[X(p.amPM.textContent===p.l10n.amPM[0])]),n.value=Q(l)}}(e),"input"!==e.type?(s(),V()):setTimeout(function(){s(),V()},ge))}function s(){if(void 0!==p.hourElement&&void 0!==p.minuteElement){var e,t,n=(parseInt(p.hourElement.value.slice(-2),10)||0)%24,a=(parseInt(p.minuteElement.value,10)||0)%60,i=void 0!==p.secondElement?(parseInt(p.secondElement.value,10)||0)%60:0;void 0!==p.amPM&&(e=n,t=p.amPM.textContent,n=e%12+12*X(t===p.l10n.amPM[1]));var o=void 0!==p.config.minTime||p.config.minDate&&p.minDateHasTime&&p.latestSelectedDateObj&&0===ce(p.latestSelectedDateObj,p.config.minDate,!0);if(void 0!==p.config.maxTime||p.config.maxDate&&p.maxDateHasTime&&p.latestSelectedDateObj&&0===ce(p.latestSelectedDateObj,p.config.maxDate,!0)){var r=void 0!==p.config.maxTime?p.config.maxTime:p.config.maxDate;(n=Math.min(n,r.getHours()))===r.getHours()&&(a=Math.min(a,r.getMinutes())),a===r.getMinutes()&&(i=Math.min(i,r.getSeconds()))}if(o){var c=void 0!==p.config.minTime?p.config.minTime:p.config.minDate;(n=Math.max(n,c.getHours()))===c.getHours()&&(a=Math.max(a,c.getMinutes())),a===c.getMinutes()&&(i=Math.max(i,c.getSeconds()))}l(n,a,i)}}function i(e){var t=e||p.latestSelectedDateObj;t&&l(t.getHours(),t.getMinutes(),t.getSeconds())}function g(){var e=p.config.defaultHour,t=p.config.defaultMinute,n=p.config.defaultSeconds;if(void 0!==p.config.minDate){var a=p.config.minDate.getHours(),i=p.config.minDate.getMinutes();(e=Math.max(e,a))===a&&(t=Math.max(i,t)),e===a&&t===i&&(n=p.config.minDate.getSeconds())}if(void 0!==p.config.maxDate){var o=p.config.maxDate.getHours(),r=p.config.maxDate.getMinutes();(e=Math.min(e,o))===o&&(t=Math.min(r,t)),e===o&&t===r&&(n=p.config.maxDate.getSeconds())}l(e,t,n)}function l(e,t,n){void 0!==p.latestSelectedDateObj&&p.latestSelectedDateObj.setHours(e%24,t,n||0,0),p.hourElement&&p.minuteElement&&!p.isMobile&&(p.hourElement.value=Q(p.config.time_24hr?e:(12+e)%12+12*X(e%12==0)),p.minuteElement.value=Q(t),void 0!==p.amPM&&(p.amPM.textContent=p.l10n.amPM[X(12<=e)]),void 0!==p.secondElement&&(p.secondElement.value=Q(n)))}function n(e){var t=parseInt(e.target.value)+(e.delta||0);(1<t/1e3||"Enter"===e.key&&!/[^\d]/.test(t.toString()))&&_(t)}function o(t,n,a,i){return n instanceof Array?n.forEach(function(e){return o(t,e,a,i)}):t instanceof Array?t.forEach(function(e){return o(e,n,a,i)}):(t.addEventListener(n,a,i),void p._handlers.push({element:t,event:n,handler:a,options:i}))}function a(t){return function(e){1===e.which&&t(e)}}function h(){q("onChange")}function r(e){var t=void 0!==e?p.parseDate(e):p.latestSelectedDateObj||(p.config.minDate&&p.config.minDate>p.now?p.config.minDate:p.config.maxDate&&p.config.maxDate<p.now?p.config.maxDate:p.now);try{void 0!==t&&(p.currentYear=t.getFullYear(),p.currentMonth=t.getMonth())}catch(e){e.message="Invalid date supplied: "+t,p.config.errorHandler(e)}p.redraw()}function c(e){~e.target.className.indexOf("arrow")&&v(e,e.target.classList.contains("arrowUp")?1:-1)}function v(e,t,n){var a=e&&e.target,i=n||a&&a.parentNode&&a.parentNode.firstChild,o=$("increment");o.delta=t,i&&i.dispatchEvent(o)}function D(e,t,n,a){var i,o=F(t,!0),r=ue("span","flatpickr-day "+e,t.getDate().toString());return r.dateObj=t,r.$i=a,r.setAttribute("aria-label",p.formatDate(t,p.config.ariaDateFormat)),-1===e.indexOf("hidden")&&0===ce(t,p.now)&&((p.todayDateElem=r).classList.add("today"),r.setAttribute("aria-current","date")),o?(r.tabIndex=-1,z(t)&&(r.classList.add("selected"),p.selectedDateElem=r,"range"===p.config.mode&&(se(r,"startRange",p.selectedDates[0]&&0===ce(t,p.selectedDates[0],!0)),se(r,"endRange",p.selectedDates[1]&&0===ce(t,p.selectedDates[1],!0)),"nextMonthDay"===e&&r.classList.add("inRange")))):r.classList.add("disabled"),"range"===p.config.mode&&(i=t,!("range"!==p.config.mode||p.selectedDates.length<2)&&0<=ce(i,p.selectedDates[0])&&ce(i,p.selectedDates[1])<=0&&!z(t)&&r.classList.add("inRange")),p.weekNumbers&&1===p.config.showMonths&&"prevMonthDay"!==e&&n%7==1&&p.weekNumbers.insertAdjacentHTML("beforeend","<span class='flatpickr-day'>"+p.config.getWeek(t)+"</span>"),q("onDayCreate",r),r}function w(e){e.focus(),"range"===p.config.mode&&N(e)}function b(e){for(var t=0<e?0:p.config.showMonths-1,n=0<e?p.config.showMonths:-1,a=t;a!=n;a+=e)for(var i=p.daysContainer.children[a],o=0<e?0:i.children.length-1,r=0<e?i.children.length:-1,c=o;c!=r;c+=e){var l=i.children[c];if(-1===l.className.indexOf("hidden")&&F(l.dateObj))return l}}function M(e,t){var n=P(document.activeElement),a=void 0!==e?e:n?document.activeElement:void 0!==p.selectedDateElem&&P(p.selectedDateElem)?p.selectedDateElem:void 0!==p.todayDateElem&&P(p.todayDateElem)?p.todayDateElem:b(0<t?1:-1);return void 0===a?p._input.focus():n?void function(e,t){for(var n=-1===e.className.indexOf("Month")?e.dateObj.getMonth():p.currentMonth,a=0<t?p.config.showMonths:-1,i=0<t?1:-1,o=n-p.currentMonth;o!=a;o+=i)for(var r=p.daysContainer.children[o],c=n-p.currentMonth===o?e.$i+t:t<0?r.children.length-1:0,l=r.children.length,d=c;0<=d&&d<l&&d!=(0<t?l:-1);d+=i){var s=r.children[d];if(-1===s.className.indexOf("hidden")&&F(s.dateObj)&&Math.abs(e.$i-d)>=Math.abs(t))return w(s)}p.changeMonth(i),M(b(i),0)}(a,t):w(a)}function C(e,t){for(var n=(new Date(e,t,1).getDay()-p.l10n.firstDayOfWeek+7)%7,a=p.utils.getDaysInMonth((t-1+12)%12),i=p.utils.getDaysInMonth(t),o=window.document.createDocumentFragment(),r=1<p.config.showMonths,c=r?"prevMonthDay hidden":"prevMonthDay",l=r?"nextMonthDay hidden":"nextMonthDay",d=a+1-n,s=0;d<=a;d++,s++)o.appendChild(D(c,new Date(e,t-1,d),d,s));for(d=1;d<=i;d++,s++)o.appendChild(D("",new Date(e,t,d),d,s));for(var u=i+1;u<=42-n&&(1===p.config.showMonths||s%7!=0);u++,s++)o.appendChild(D(l,new Date(e,t+1,u%i),u,s));var f=ue("div","dayContainer");return f.appendChild(o),f}function y(){if(void 0!==p.daysContainer){fe(p.daysContainer),p.weekNumbers&&fe(p.weekNumbers);for(var e=document.createDocumentFragment(),t=0;t<p.config.showMonths;t++){var n=new Date(p.currentYear,p.currentMonth,1);n.setMonth(p.currentMonth+t),e.appendChild(C(n.getFullYear(),n.getMonth()))}p.daysContainer.appendChild(e),p.days=p.daysContainer.firstChild}}function x(){var e=ue("div","flatpickr-month"),t=window.document.createDocumentFragment(),n=ue("span","cur-month");n.title=p.l10n.scrollTitle;var a=me("cur-year",{tabindex:"-1"}),i=a.childNodes[0];i.title=p.l10n.scrollTitle,i.setAttribute("aria-label",p.l10n.yearAriaLabel),p.config.minDate&&i.setAttribute("data-min",p.config.minDate.getFullYear().toString()),p.config.maxDate&&(i.setAttribute("data-max",p.config.maxDate.getFullYear().toString()),i.disabled=!!p.config.minDate&&p.config.minDate.getFullYear()===p.config.maxDate.getFullYear());var o=ue("div","flatpickr-current-month");return o.appendChild(n),o.appendChild(a),t.appendChild(o),e.appendChild(t),{container:e,yearElement:i,monthElement:n}}function E(){fe(p.monthNav),p.monthNav.appendChild(p.prevMonthNav);for(var e=p.config.showMonths;e--;){var t=x();p.yearElements.push(t.yearElement),p.monthElements.push(t.monthElement),p.monthNav.appendChild(t.container)}p.monthNav.appendChild(p.nextMonthNav)}function T(){p.weekdayContainer?fe(p.weekdayContainer):p.weekdayContainer=ue("div","flatpickr-weekdays");for(var e=p.config.showMonths;e--;){var t=ue("div","flatpickr-weekdaycontainer");p.weekdayContainer.appendChild(t)}return k(),p.weekdayContainer}function k(){var e=p.l10n.firstDayOfWeek,t=p.l10n.weekdays.shorthand.concat();0<e&&e<t.length&&(t=t.splice(e,t.length).concat(t.splice(0,e)));for(var n=p.config.showMonths;n--;)p.weekdayContainer.children[n].innerHTML="\n      <span class=flatpickr-weekday>\n        "+t.join("</span><span class=flatpickr-weekday>")+"\n      </span>\n      "}function I(e,t){void 0===t&&(t=!0);var n=t?e:e-p.currentMonth;n<0&&!0===p._hidePrevMonthArrow||0<n&&!0===p._hideNextMonthArrow||(p.currentMonth+=n,(p.currentMonth<0||11<p.currentMonth)&&(p.currentYear+=11<p.currentMonth?1:-1,p.currentMonth=(p.currentMonth+12)%12,q("onYearChange")),y(),q("onMonthChange"),G())}function O(e){return!(!p.config.appendTo||!p.config.appendTo.contains(e))||p.calendarContainer.contains(e)}function S(t){if(p.isOpen&&!p.config.inline){var e=O(t.target),n=t.target===p.input||t.target===p.altInput||p.element.contains(t.target)||t.path&&t.path.indexOf&&(~t.path.indexOf(p.input)||~t.path.indexOf(p.altInput)),a="blur"===t.type?n&&t.relatedTarget&&!O(t.relatedTarget):!n&&!e,i=!p.config.ignoredFocusElements.some(function(e){return e.contains(t.target)});a&&i&&(p.close(),"range"===p.config.mode&&1===p.selectedDates.length&&(p.clear(!1),p.redraw()))}}function _(e){if(!(!e||p.config.minDate&&e<p.config.minDate.getFullYear()||p.config.maxDate&&e>p.config.maxDate.getFullYear())){var t=e,n=p.currentYear!==t;p.currentYear=t||p.currentYear,p.config.maxDate&&p.currentYear===p.config.maxDate.getFullYear()?p.currentMonth=Math.min(p.config.maxDate.getMonth(),p.currentMonth):p.config.minDate&&p.currentYear===p.config.minDate.getFullYear()&&(p.currentMonth=Math.max(p.config.minDate.getMonth(),p.currentMonth)),n&&(p.redraw(),q("onYearChange"))}}function F(e,t){void 0===t&&(t=!0);var n=p.parseDate(e,void 0,t);if(p.config.minDate&&n&&ce(n,p.config.minDate,void 0!==t?t:!p.minDateHasTime)<0||p.config.maxDate&&n&&0<ce(n,p.config.maxDate,void 0!==t?t:!p.maxDateHasTime))return!1;if(0===p.config.enable.length&&0===p.config.disable.length)return!0;if(void 0===n)return!1;for(var a,i=0<p.config.enable.length,o=i?p.config.enable:p.config.disable,r=0;r<o.length;r++){if("function"==typeof(a=o[r])&&a(n))return i;if(a instanceof Date&&void 0!==n&&a.getTime()===n.getTime())return i;if("string"==typeof a&&void 0!==n){var c=p.parseDate(a,void 0,!0);return c&&c.getTime()===n.getTime()?i:!i}if("object"==typeof a&&void 0!==n&&a.from&&a.to&&n.getTime()>=a.from.getTime()&&n.getTime()<=a.to.getTime())return i}return!i}function P(e){return void 0!==p.daysContainer&&(-1===e.className.indexOf("hidden")&&p.daysContainer.contains(e))}function A(e){var t=e.target===p._input,n=O(e.target),a=p.config.allowInput,i=p.isOpen&&(!a||!t),o=p.config.inline&&t&&!a;if(13===e.keyCode&&t){if(a)return p.setDate(p._input.value,!0,e.target===p.altInput?p.config.altFormat:p.config.dateFormat),e.target.blur();p.open()}else if(n||i||o){var r=!!p.timeContainer&&p.timeContainer.contains(e.target);switch(e.keyCode){case 13:r?V():J(e);break;case 27:e.preventDefault(),R();break;case 8:case 46:t&&!p.config.allowInput&&(e.preventDefault(),p.clear());break;case 37:case 39:if(r)p.hourElement&&p.hourElement.focus();else if(e.preventDefault(),void 0!==p.daysContainer&&(!1===a||P(document.activeElement))){var c=39===e.keyCode?1:-1;e.ctrlKey?(I(c),M(b(1),0)):M(void 0,c)}break;case 38:case 40:e.preventDefault();var l=40===e.keyCode?1:-1;p.daysContainer?e.ctrlKey?(_(p.currentYear-l),M(b(1),0)):r||M(void 0,7*l):p.config.enableTime&&(!r&&p.hourElement&&p.hourElement.focus(),d(e),p._debouncedChange());break;case 9:if(!r)break;e.target===p.hourElement?(e.preventDefault(),p.minuteElement.select()):e.target===p.minuteElement&&(p.secondElement||p.amPM)?(e.preventDefault(),void 0!==p.secondElement?p.secondElement.focus():void 0!==p.amPM&&(e.preventDefault(),p.amPM.focus())):e.target===p.secondElement&&p.amPM&&(e.preventDefault(),p.amPM.focus())}}if(void 0!==p.amPM&&e.target===p.amPM)switch(e.key){case p.l10n.amPM[0].charAt(0):case p.l10n.amPM[0].charAt(0).toLowerCase():p.amPM.textContent=p.l10n.amPM[0],s(),V();break;case p.l10n.amPM[1].charAt(0):case p.l10n.amPM[1].charAt(0).toLowerCase():p.amPM.textContent=p.l10n.amPM[1],s(),V()}q("onKeyDown",e)}function N(o){if(1===p.selectedDates.length&&o.classList.contains("flatpickr-day")&&!o.classList.contains("disabled")){for(var r=o.dateObj.getTime(),c=p.parseDate(p.selectedDates[0],void 0,!0).getTime(),e=Math.min(r,p.selectedDates[0].getTime()),t=Math.max(r,p.selectedDates[0].getTime()),l=!1,d=0,s=0,n=e;n<t;n+=de.DAY)F(new Date(n),!0)||(l=l||e<n&&n<t,n<c&&(!d||d<n)?d=n:c<n&&(!s||n<s)&&(s=n));for(var u=0;u<p.config.showMonths;u++)for(var f=p.daysContainer.children[u],m=p.daysContainer.children[u-1],a=function(e,t){var n=f.children[e],a=n.dateObj.getTime(),i=0<d&&a<d||0<s&&s<a;return i?(n.classList.add("notAllowed"),["inRange","startRange","endRange"].forEach(function(e){n.classList.remove(e)}),"continue"):l&&!i?"continue":(["startRange","inRange","endRange","notAllowed"].forEach(function(e){n.classList.remove(e)}),o.classList.add(r<p.selectedDates[0].getTime()?"startRange":"endRange"),void(!f.contains(o)&&0<u&&m&&m.lastChild.dateObj.getTime()>=a||(c<r&&a===c?n.classList.add("startRange"):r<c&&a===c&&n.classList.add("endRange"),d<=a&&(0===s||a<=s)&&le(a,c,r)&&n.classList.add("inRange"))))},i=0,g=f.children.length;i<g;i++)a(i)}}function Y(){!p.isOpen||p.config.static||p.config.inline||L()}function j(a){return function(e){var t=p.config["_"+a+"Date"]=p.parseDate(e,p.config.dateFormat),n=p.config["_"+("min"===a?"max":"min")+"Date"];void 0!==t&&(p["min"===a?"minDateHasTime":"maxDateHasTime"]=0<t.getHours()||0<t.getMinutes()||0<t.getSeconds()),p.selectedDates&&(p.selectedDates=p.selectedDates.filter(function(e){return F(e)}),p.selectedDates.length||"min"!==a||i(t),V()),p.daysContainer&&(W(),void 0!==t?p.currentYearElement[a]=t.getFullYear().toString():p.currentYearElement.removeAttribute(a),p.currentYearElement.disabled=!!n&&void 0!==t&&n.getFullYear()===t.getFullYear())}}function H(){"object"!=typeof p.config.locale&&void 0===pe.l10ns[p.config.locale]&&p.config.errorHandler(new Error("flatpickr: invalid locale "+p.config.locale)),p.l10n=Object.assign({},pe.l10ns.default,"object"==typeof p.config.locale?p.config.locale:"default"!==p.config.locale?pe.l10ns[p.config.locale]:void 0),ae.K="("+p.l10n.amPM[0]+"|"+p.l10n.amPM[1]+"|"+p.l10n.amPM[0].toLowerCase()+"|"+p.l10n.amPM[1].toLowerCase()+")",p.formatDate=oe(p)}function L(e){if(void 0!==p.calendarContainer){q("onPreCalendarPosition");var t=e||p._positionElement,n=Array.prototype.reduce.call(p.calendarContainer.children,function(e,t){return e+t.offsetHeight},0),a=p.calendarContainer.offsetWidth,i=p.config.position.split(" "),o=i[0],r=1<i.length?i[1]:null,c=t.getBoundingClientRect(),l=window.innerHeight-c.bottom,d="above"===o||"below"!==o&&l<n&&c.top>n,s=window.pageYOffset+c.top+(d?-n-2:t.offsetHeight+2);if(se(p.calendarContainer,"arrowTop",!d),se(p.calendarContainer,"arrowBottom",d),!p.config.inline){var u=window.pageXOffset+c.left-(null!=r&&"center"===r?(a-c.width)/2:0),f=window.document.body.offsetWidth-c.right,m=u+a>window.document.body.offsetWidth;se(p.calendarContainer,"rightMost",m),p.config.static||(p.calendarContainer.style.top=s+"px",m?(p.calendarContainer.style.left="auto",p.calendarContainer.style.right=f+"px"):(p.calendarContainer.style.left=u+"px",p.calendarContainer.style.right="auto"))}}}function W(){p.config.noCalendar||p.isMobile||(G(),y())}function R(){p._input.focus(),-1!==window.navigator.userAgent.indexOf("MSIE")||void 0!==navigator.msMaxTouchPoints?setTimeout(p.close,0):p.close()}function J(e){e.preventDefault(),e.stopPropagation();var t=function e(t,n){return n(t)?t:t.parentNode?e(t.parentNode,n):void 0}(e.target,function(e){return e.classList&&e.classList.contains("flatpickr-day")&&!e.classList.contains("disabled")&&!e.classList.contains("notAllowed")});if(void 0!==t){var n=t,a=p.latestSelectedDateObj=new Date(n.dateObj.getTime()),i=(a.getMonth()<p.currentMonth||a.getMonth()>p.currentMonth+p.config.showMonths-1)&&"range"!==p.config.mode;if(p.selectedDateElem=n,"single"===p.config.mode)p.selectedDates=[a];else if("multiple"===p.config.mode){var o=z(a);o?p.selectedDates.splice(parseInt(o),1):p.selectedDates.push(a)}else"range"===p.config.mode&&(2===p.selectedDates.length&&p.clear(!1),p.selectedDates.push(a),0!==ce(a,p.selectedDates[0],!0)&&p.selectedDates.sort(function(e,t){return e.getTime()-t.getTime()}));if(s(),i){var r=p.currentYear!==a.getFullYear();p.currentYear=a.getFullYear(),p.currentMonth=a.getMonth(),r&&q("onYearChange"),q("onMonthChange")}if(G(),y(),g(),V(),p.config.enableTime&&setTimeout(function(){return p.showTimeInput=!0},50),"range"===p.config.mode&&(1===p.selectedDates.length?N(n):G()),i||"range"===p.config.mode||1!==p.config.showMonths?p.selectedDateElem&&p.selectedDateElem.focus():w(n),void 0!==p.hourElement&&setTimeout(function(){return void 0!==p.hourElement&&p.hourElement.select()},451),p.config.closeOnSelect){var c="single"===p.config.mode&&!p.config.enableTime,l="range"===p.config.mode&&2===p.selectedDates.length&&!p.config.enableTime;(c||l)&&R()}h()}}p.parseDate=re({config:p.config,l10n:p.l10n}),p._handlers=[],p._bind=o,p._setHoursFromDate=i,p._positionCalendar=L,p.changeMonth=I,p.changeYear=_,p.clear=function(e){void 0===e&&(e=!0);p.input.value="",void 0!==p.altInput&&(p.altInput.value="");void 0!==p.mobileInput&&(p.mobileInput.value="");p.selectedDates=[],p.latestSelectedDateObj=void 0,!(p.showTimeInput=!1)===p.config.enableTime&&g();p.redraw(),e&&q("onChange")},p.close=function(){p.isOpen=!1,p.isMobile||(p.calendarContainer.classList.remove("open"),p._input.classList.remove("active"));q("onClose")},p._createElement=ue,p.destroy=function(){void 0!==p.config&&q("onDestroy");for(var e=p._handlers.length;e--;){var t=p._handlers[e];t.element.removeEventListener(t.event,t.handler,t.options)}p._handlers=[],p.mobileInput?(p.mobileInput.parentNode&&p.mobileInput.parentNode.removeChild(p.mobileInput),p.mobileInput=void 0):p.calendarContainer&&p.calendarContainer.parentNode&&p.calendarContainer.parentNode.removeChild(p.calendarContainer);p.altInput&&(p.input.type="text",p.altInput.parentNode&&p.altInput.parentNode.removeChild(p.altInput),delete p.altInput);p.input&&(p.input.type=p.input._type,p.input.classList.remove("flatpickr-input"),p.input.removeAttribute("readonly"),p.input.value="");["_showTimeInput","latestSelectedDateObj","_hideNextMonthArrow","_hidePrevMonthArrow","__hideNextMonthArrow","__hidePrevMonthArrow","isMobile","isOpen","selectedDateElem","minDateHasTime","maxDateHasTime","days","daysContainer","_input","_positionElement","innerContainer","rContainer","monthNav","todayDateElem","calendarContainer","weekdayContainer","prevMonthNav","nextMonthNav","currentMonthElement","currentYearElement","navigationCurrentMonth","selectedDateElem","config"].forEach(function(e){try{delete p[e]}catch(e){}})},p.isEnabled=F,p.jumpToDate=r,p.open=function(e,t){void 0===t&&(t=p._input);if(!0===p.isMobile)return e&&(e.preventDefault(),e.target&&e.target.blur()),setTimeout(function(){void 0!==p.mobileInput&&p.mobileInput.focus()},0),void q("onOpen");if(p._input.disabled||p.config.inline)return;var n=p.isOpen;p.isOpen=!0,n||(p.calendarContainer.classList.add("open"),p._input.classList.add("active"),q("onOpen"),L(t));!0===p.config.enableTime&&!0===p.config.noCalendar&&(0===p.selectedDates.length&&(p.setDate(void 0!==p.config.minDate?new Date(p.config.minDate.getTime()):new Date,!1),g(),V()),!1!==p.config.allowInput||void 0!==e&&p.timeContainer.contains(e.relatedTarget)||setTimeout(function(){return p.hourElement.select()},50))},p.redraw=W,p.set=function(e,t){null!==e&&"object"==typeof e?Object.assign(p.config,e):(p.config[e]=t,void 0!==K[e]&&K[e].forEach(function(e){return e()}));p.redraw(),r()},p.setDate=function(e,t,n){void 0===t&&(t=!1);void 0===n&&(n=p.config.dateFormat);if(0!==e&&!e)return p.clear(t);B(e,n),p.showTimeInput=0<p.selectedDates.length,p.latestSelectedDateObj=p.selectedDates[0],p.redraw(),r(),i(),V(t),t&&q("onChange")},p.toggle=function(e){if(!0===p.isOpen)return p.close();p.open(e)};var K={locale:[H,k],showMonths:[E,t,T]};function B(e,t){var n=[];if(e instanceof Array)n=e.map(function(e){return p.parseDate(e,t)});else if(e instanceof Date||"number"==typeof e)n=[p.parseDate(e,t)];else if("string"==typeof e)switch(p.config.mode){case"single":case"time":n=[p.parseDate(e,t)];break;case"multiple":n=e.split(p.config.conjunction).map(function(e){return p.parseDate(e,t)});break;case"range":n=e.split(p.l10n.rangeSeparator).map(function(e){return p.parseDate(e,t)})}else p.config.errorHandler(new Error("Invalid date supplied: "+JSON.stringify(e)));p.selectedDates=n.filter(function(e){return e instanceof Date&&F(e,!1)}),"range"===p.config.mode&&p.selectedDates.sort(function(e,t){return e.getTime()-t.getTime()})}function U(e){return e.slice().map(function(e){return"string"==typeof e||"number"==typeof e||e instanceof Date?p.parseDate(e,void 0,!0):e&&"object"==typeof e&&e.from&&e.to?{from:p.parseDate(e.from,void 0),to:p.parseDate(e.to,void 0)}:e}).filter(function(e){return e})}function q(e,t){var n=p.config[e];if(void 0!==n&&0<n.length)for(var a=0;n[a]&&a<n.length;a++)n[a](p.selectedDates,p.input.value,p,t);"onChange"===e&&(p.input.dispatchEvent($("change")),p.input.dispatchEvent($("input")))}function $(e){var t=document.createEvent("Event");return t.initEvent(e,!0,!0),t}function z(e){for(var t=0;t<p.selectedDates.length;t++)if(0===ce(p.selectedDates[t],e))return""+t;return!1}function G(){p.config.noCalendar||p.isMobile||!p.monthNav||(p.yearElements.forEach(function(e,t){var n=new Date(p.currentYear,p.currentMonth,1);n.setMonth(p.currentMonth+t),p.monthElements[t].textContent=ne(n.getMonth(),p.config.shorthandCurrentMonth,p.l10n)+" ",e.value=n.getFullYear().toString()}),p._hidePrevMonthArrow=void 0!==p.config.minDate&&(p.currentYear===p.config.minDate.getFullYear()?p.currentMonth<=p.config.minDate.getMonth():p.currentYear<p.config.minDate.getFullYear()),p._hideNextMonthArrow=void 0!==p.config.maxDate&&(p.currentYear===p.config.maxDate.getFullYear()?p.currentMonth+1>p.config.maxDate.getMonth():p.currentYear>p.config.maxDate.getFullYear()))}function V(e){if(void 0===e&&(e=!0),0===p.selectedDates.length)return p.clear(e);void 0!==p.mobileInput&&p.mobileFormatStr&&(p.mobileInput.value=void 0!==p.latestSelectedDateObj?p.formatDate(p.latestSelectedDateObj,p.mobileFormatStr):"");var t="range"!==p.config.mode?p.config.conjunction:p.l10n.rangeSeparator;p.input.value=p.selectedDates.map(function(e){return p.formatDate(e,p.config.dateFormat)}).join(t),void 0!==p.altInput&&(p.altInput.value=p.selectedDates.map(function(e){return p.formatDate(e,p.config.altFormat)}).join(t)),!1!==e&&q("onValueUpdate")}function Z(e){e.preventDefault();var t=p.prevMonthNav.contains(e.target),n=p.nextMonthNav.contains(e.target);t||n?I(t?-1:1):0<=p.yearElements.indexOf(e.target)?e.target.select():e.target.classList.contains("arrowUp")?p.changeYear(p.currentYear+1):e.target.classList.contains("arrowDown")&&p.changeYear(p.currentYear-1)}return function(){p.element=p.input=u,p.isOpen=!1,function(){var e=["wrap","weekNumbers","allowInput","clickOpens","time_24hr","enableTime","noCalendar","altInput","shorthandCurrentMonth","inline","static","enableSeconds","disableMobile"],t=["onChange","onClose","onDayCreate","onDestroy","onKeyDown","onMonthChange","onOpen","onParseConfig","onReady","onValueUpdate","onYearChange","onPreCalendarPosition"],n=Object.assign({},f,JSON.parse(JSON.stringify(u.dataset||{}))),a={};p.config.parseDate=n.parseDate,p.config.formatDate=n.formatDate,Object.defineProperty(p.config,"enable",{get:function(){return p.config._enable},set:function(e){p.config._enable=U(e)}}),Object.defineProperty(p.config,"disable",{get:function(){return p.config._disable},set:function(e){p.config._disable=U(e)}});var i="time"===n.mode;n.dateFormat||!n.enableTime&&!i||(a.dateFormat=n.noCalendar||i?"H:i"+(n.enableSeconds?":S":""):pe.defaultConfig.dateFormat+" H:i"+(n.enableSeconds?":S":"")),n.altInput&&(n.enableTime||i)&&!n.altFormat&&(a.altFormat=n.noCalendar||i?"h:i"+(n.enableSeconds?":S K":" K"):pe.defaultConfig.altFormat+" h:i"+(n.enableSeconds?":S":"")+" K"),Object.defineProperty(p.config,"minDate",{get:function(){return p.config._minDate},set:j("min")}),Object.defineProperty(p.config,"maxDate",{get:function(){return p.config._maxDate},set:j("max")});var o=function(t){return function(e){p.config["min"===t?"_minTime":"_maxTime"]=p.parseDate(e,"H:i")}};Object.defineProperty(p.config,"minTime",{get:function(){return p.config._minTime},set:o("min")}),Object.defineProperty(p.config,"maxTime",{get:function(){return p.config._maxTime},set:o("max")}),"time"===n.mode&&(p.config.noCalendar=!0,p.config.enableTime=!0),Object.assign(p.config,a,n);for(var r=0;r<e.length;r++)p.config[e[r]]=!0===p.config[e[r]]||"true"===p.config[e[r]];for(var c=t.length;c--;)void 0!==p.config[t[c]]&&(p.config[t[c]]=te(p.config[t[c]]||[]).map(m));p.isMobile=!p.config.disableMobile&&!p.config.inline&&"single"===p.config.mode&&!p.config.disable.length&&!p.config.enable.length&&!p.config.weekNumbers&&/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);for(var l=0;l<p.config.plugins.length;l++){var d=p.config.plugins[l](p)||{};for(var s in d)~t.indexOf(s)?p.config[s]=te(d[s]).map(m).concat(p.config[s]):void 0===n[s]&&(p.config[s]=d[s])}q("onParseConfig")}(),H(),function(){if(p.input=p.config.wrap?u.querySelector("[data-input]"):u,!p.input)return p.config.errorHandler(new Error("Invalid input element specified"));p.input._type=p.input.type,p.input.type="text",p.input.classList.add("flatpickr-input"),p._input=p.input,p.config.altInput&&(p.altInput=ue(p.input.nodeName,p.input.className+" "+p.config.altInputClass),p._input=p.altInput,p.altInput.placeholder=p.input.placeholder,p.altInput.disabled=p.input.disabled,p.altInput.required=p.input.required,p.altInput.tabIndex=p.input.tabIndex,p.altInput.type="text",p.input.setAttribute("type","hidden"),!p.config.static&&p.input.parentNode&&p.input.parentNode.insertBefore(p.altInput,p.input.nextSibling)),p.config.allowInput||p._input.setAttribute("readonly","readonly"),p._positionElement=p.config.positionElement||p._input}(),function(){p.selectedDates=[],p.now=p.parseDate(p.config.now)||new Date;var e=p.config.defaultDate||(0<p.input.placeholder.length&&p.input.value===p.input.placeholder?null:p.input.value);e&&B(e,p.config.dateFormat);var t=0<p.selectedDates.length?p.selectedDates[0]:p.config.minDate&&p.config.minDate.getTime()>p.now.getTime()?p.config.minDate:p.config.maxDate&&p.config.maxDate.getTime()<p.now.getTime()?p.config.maxDate:p.now;p.currentYear=t.getFullYear(),p.currentMonth=t.getMonth(),0<p.selectedDates.length&&(p.latestSelectedDateObj=p.selectedDates[0]),void 0!==p.config.minTime&&(p.config.minTime=p.parseDate(p.config.minTime,"H:i")),void 0!==p.config.maxTime&&(p.config.maxTime=p.parseDate(p.config.maxTime,"H:i")),p.minDateHasTime=!!p.config.minDate&&(0<p.config.minDate.getHours()||0<p.config.minDate.getMinutes()||0<p.config.minDate.getSeconds()),p.maxDateHasTime=!!p.config.maxDate&&(0<p.config.maxDate.getHours()||0<p.config.maxDate.getMinutes()||0<p.config.maxDate.getSeconds()),Object.defineProperty(p,"showTimeInput",{get:function(){return p._showTimeInput},set:function(e){p._showTimeInput=e,p.calendarContainer&&se(p.calendarContainer,"showTimeInput",e),p.isOpen&&L()}})}(),p.utils={getDaysInMonth:function(e,t){return void 0===e&&(e=p.currentMonth),void 0===t&&(t=p.currentYear),1===e&&(t%4==0&&t%100!=0||t%400==0)?29:p.l10n.daysInMonth[e]}},p.isMobile||function(){var e=window.document.createDocumentFragment();if(p.calendarContainer=ue("div","flatpickr-calendar"),p.calendarContainer.tabIndex=-1,!p.config.noCalendar){if(e.appendChild((p.monthNav=ue("div","flatpickr-months"),p.yearElements=[],p.monthElements=[],p.prevMonthNav=ue("span","flatpickr-prev-month"),p.prevMonthNav.innerHTML=p.config.prevArrow,p.nextMonthNav=ue("span","flatpickr-next-month"),p.nextMonthNav.innerHTML=p.config.nextArrow,E(),Object.defineProperty(p,"_hidePrevMonthArrow",{get:function(){return p.__hidePrevMonthArrow},set:function(e){p.__hidePrevMonthArrow!==e&&(se(p.prevMonthNav,"disabled",e),p.__hidePrevMonthArrow=e)}}),Object.defineProperty(p,"_hideNextMonthArrow",{get:function(){return p.__hideNextMonthArrow},set:function(e){p.__hideNextMonthArrow!==e&&(se(p.nextMonthNav,"disabled",e),p.__hideNextMonthArrow=e)}}),p.currentYearElement=p.yearElements[0],G(),p.monthNav)),p.innerContainer=ue("div","flatpickr-innerContainer"),p.config.weekNumbers){var t=function(){p.calendarContainer.classList.add("hasWeeks");var e=ue("div","flatpickr-weekwrapper");e.appendChild(ue("span","flatpickr-weekday",p.l10n.weekAbbreviation));var t=ue("div","flatpickr-weeks");return e.appendChild(t),{weekWrapper:e,weekNumbers:t}}(),n=t.weekWrapper,a=t.weekNumbers;p.innerContainer.appendChild(n),p.weekNumbers=a,p.weekWrapper=n}p.rContainer=ue("div","flatpickr-rContainer"),p.rContainer.appendChild(T()),p.daysContainer||(p.daysContainer=ue("div","flatpickr-days"),p.daysContainer.tabIndex=-1),y(),p.rContainer.appendChild(p.daysContainer),p.innerContainer.appendChild(p.rContainer),e.appendChild(p.innerContainer)}p.config.enableTime&&e.appendChild(function(){p.calendarContainer.classList.add("hasTime"),p.config.noCalendar&&p.calendarContainer.classList.add("noCalendar"),p.timeContainer=ue("div","flatpickr-time"),p.timeContainer.tabIndex=-1;var e=ue("span","flatpickr-time-separator",":"),t=me("flatpickr-hour");p.hourElement=t.childNodes[0];var n=me("flatpickr-minute");if(p.minuteElement=n.childNodes[0],p.hourElement.tabIndex=p.minuteElement.tabIndex=-1,p.hourElement.value=Q(p.latestSelectedDateObj?p.latestSelectedDateObj.getHours():p.config.time_24hr?p.config.defaultHour:function(e){switch(e%24){case 0:case 12:return 12;default:return e%12}}(p.config.defaultHour)),p.minuteElement.value=Q(p.latestSelectedDateObj?p.latestSelectedDateObj.getMinutes():p.config.defaultMinute),p.hourElement.setAttribute("data-step",p.config.hourIncrement.toString()),p.minuteElement.setAttribute("data-step",p.config.minuteIncrement.toString()),p.hourElement.setAttribute("data-min",p.config.time_24hr?"0":"1"),p.hourElement.setAttribute("data-max",p.config.time_24hr?"23":"12"),p.minuteElement.setAttribute("data-min","0"),p.minuteElement.setAttribute("data-max","59"),p.timeContainer.appendChild(t),p.timeContainer.appendChild(e),p.timeContainer.appendChild(n),p.config.time_24hr&&p.timeContainer.classList.add("time24hr"),p.config.enableSeconds){p.timeContainer.classList.add("hasSeconds");var a=me("flatpickr-second");p.secondElement=a.childNodes[0],p.secondElement.value=Q(p.latestSelectedDateObj?p.latestSelectedDateObj.getSeconds():p.config.defaultSeconds),p.secondElement.setAttribute("data-step",p.minuteElement.getAttribute("data-step")),p.secondElement.setAttribute("data-min",p.minuteElement.getAttribute("data-min")),p.secondElement.setAttribute("data-max",p.minuteElement.getAttribute("data-max")),p.timeContainer.appendChild(ue("span","flatpickr-time-separator",":")),p.timeContainer.appendChild(a)}return p.config.time_24hr||(p.amPM=ue("span","flatpickr-am-pm",p.l10n.amPM[X(11<(p.latestSelectedDateObj?p.hourElement.value:p.config.defaultHour))]),p.amPM.title=p.l10n.toggleTitle,p.amPM.tabIndex=-1,p.timeContainer.appendChild(p.amPM)),p.timeContainer}()),se(p.calendarContainer,"rangeMode","range"===p.config.mode),se(p.calendarContainer,"animate",!0===p.config.animate),se(p.calendarContainer,"multiMonth",1<p.config.showMonths),p.calendarContainer.appendChild(e);var i=void 0!==p.config.appendTo&&void 0!==p.config.appendTo.nodeType;if((p.config.inline||p.config.static)&&(p.calendarContainer.classList.add(p.config.inline?"inline":"static"),p.config.inline&&(!i&&p.element.parentNode?p.element.parentNode.insertBefore(p.calendarContainer,p._input.nextSibling):void 0!==p.config.appendTo&&p.config.appendTo.appendChild(p.calendarContainer)),p.config.static)){var o=ue("div","flatpickr-wrapper");p.element.parentNode&&p.element.parentNode.insertBefore(o,p.element),o.appendChild(p.element),p.altInput&&o.appendChild(p.altInput),o.appendChild(p.calendarContainer)}p.config.static||p.config.inline||(void 0!==p.config.appendTo?p.config.appendTo:window.document.body).appendChild(p.calendarContainer)}(),function(){if(p.config.wrap&&["open","close","toggle","clear"].forEach(function(t){Array.prototype.forEach.call(p.element.querySelectorAll("[data-"+t+"]"),function(e){return o(e,"click",p[t])})}),p.isMobile)return function(){var e=p.config.enableTime?p.config.noCalendar?"time":"datetime-local":"date";p.mobileInput=ue("input",p.input.className+" flatpickr-mobile"),p.mobileInput.step=p.input.getAttribute("step")||"any",p.mobileInput.tabIndex=1,p.mobileInput.type=e,p.mobileInput.disabled=p.input.disabled,p.mobileInput.required=p.input.required,p.mobileInput.placeholder=p.input.placeholder,p.mobileFormatStr="datetime-local"===e?"Y-m-d\\TH:i:S":"date"===e?"Y-m-d":"H:i:S",0<p.selectedDates.length&&(p.mobileInput.defaultValue=p.mobileInput.value=p.formatDate(p.selectedDates[0],p.mobileFormatStr)),p.config.minDate&&(p.mobileInput.min=p.formatDate(p.config.minDate,"Y-m-d")),p.config.maxDate&&(p.mobileInput.max=p.formatDate(p.config.maxDate,"Y-m-d")),p.input.type="hidden",void 0!==p.altInput&&(p.altInput.type="hidden");try{p.input.parentNode&&p.input.parentNode.insertBefore(p.mobileInput,p.input.nextSibling)}catch(e){}o(p.mobileInput,"change",function(e){p.setDate(e.target.value,!1,p.mobileFormatStr),q("onChange"),q("onClose")})}();var e=ee(Y,50);p._debouncedChange=ee(h,ge),p.daysContainer&&!/iPhone|iPad|iPod/i.test(navigator.userAgent)&&o(p.daysContainer,"mouseover",function(e){"range"===p.config.mode&&N(e.target)}),o(window.document.body,"keydown",A),p.config.static||o(p._input,"keydown",A),p.config.inline||p.config.static||o(window,"resize",e),void 0!==window.ontouchstart?o(window.document,"click",S):o(window.document,"mousedown",a(S)),o(window.document,"focus",S,{capture:!0}),!0===p.config.clickOpens&&(o(p._input,"focus",p.open),o(p._input,"mousedown",a(p.open))),void 0!==p.daysContainer&&(o(p.monthNav,"mousedown",a(Z)),o(p.monthNav,["keyup","increment"],n),o(p.daysContainer,"mousedown",a(J))),void 0!==p.timeContainer&&void 0!==p.minuteElement&&void 0!==p.hourElement&&(o(p.timeContainer,["input","increment"],d),o(p.timeContainer,"mousedown",a(c)),o(p.timeContainer,["input","increment"],p._debouncedChange,{passive:!0}),o([p.hourElement,p.minuteElement],["focus","click"],function(e){return e.target.select()}),void 0!==p.secondElement&&o(p.secondElement,"focus",function(){return p.secondElement&&p.secondElement.select()}),void 0!==p.amPM&&o(p.amPM,"mousedown",a(function(e){d(e),h()})))}(),(p.selectedDates.length||p.config.noCalendar)&&(p.config.enableTime&&i(p.config.noCalendar?p.latestSelectedDateObj||p.config.minDate:void 0),V(!1)),t(),p.showTimeInput=0<p.selectedDates.length||p.config.noCalendar;var e=/^((?!chrome|android).)*safari/i.test(navigator.userAgent);!p.isMobile&&e&&L(),q("onReady")}(),p}function n(e,t){for(var n=Array.prototype.slice.call(e),a=[],i=0;i<n.length;i++){var o=n[i];try{if(null!==o.getAttribute("data-fp-omit"))continue;void 0!==o._flatpickr&&(o._flatpickr.destroy(),o._flatpickr=void 0),o._flatpickr=r(o,t||{}),a.push(o._flatpickr)}catch(e){console.error(e)}}return 1===a.length?a[0]:a}"undefined"!=typeof HTMLElement&&(HTMLCollection.prototype.flatpickr=NodeList.prototype.flatpickr=function(e){return n(this,e)},HTMLElement.prototype.flatpickr=function(e){return n([this],e)});var pe=function(e,t){return e instanceof NodeList?n(e,t):n("string"==typeof e?window.document.querySelectorAll(e):[e],t)};return pe.defaultConfig=w,pe.l10ns={en:Object.assign({},ie),default:Object.assign({},ie)},pe.localize=function(e){pe.l10ns.default=Object.assign({},pe.l10ns.default,e)},pe.setDefaults=function(e){pe.defaultConfig=Object.assign({},pe.defaultConfig,e)},pe.parseDate=re({}),pe.formatDate=oe({}),pe.compareDates=ce,"undefined"!=typeof jQuery&&(jQuery.fn.flatpickr=function(e){return n(this,e)}),Date.prototype.fp_incr=function(e){return new Date(this.getFullYear(),this.getMonth(),this.getDate()+("string"==typeof e?parseInt(e,10):e))},"undefined"!=typeof window&&(window.flatpickr=pe),pe});

/***/ }),

/***/ "./node_modules/jsoneditor/dist/jsoneditor-minimalist.js":
/*!***************************************************************!*\
  !*** ./node_modules/jsoneditor/dist/jsoneditor-minimalist.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/*!
 * jsoneditor.js
 *
 * @brief
 * JSONEditor is a web-based tool to view, edit, format, and validate JSON.
 * It has various modes such as a tree editor, a code editor, and a plain text
 * editor.
 *
 * Supported browsers: Chrome, Firefox, Safari, Opera, Internet Explorer 8+
 *
 * @license
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * Copyright (c) 2011-2017 Jos de Jong, http://jsoneditoronline.org
 *
 * @author  Jos de Jong, <wjosdejong@gmail.com>
 * @version 5.15.0
 * @date    2018-05-02
 */
(function webpackUniversalModuleDefinition(root, factory) {
	if(true)
		module.exports = factory();
	else {}
})(this, function() {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var Ajv;
	try {
	  Ajv = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"ajv\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));
	}
	catch (err) {
	  // no problem... when we need Ajv we will throw a neat exception
	}

	var treemode = __webpack_require__(1);
	var textmode = __webpack_require__(15);
	var util = __webpack_require__(4);

	/**
	 * @constructor JSONEditor
	 * @param {Element} container    Container element
	 * @param {Object}  [options]    Object with options. available options:
	 *                               {String} mode        Editor mode. Available values:
	 *                                                    'tree' (default), 'view',
	 *                                                    'form', 'text', and 'code'.
	 *                               {function} onChange  Callback method, triggered
	 *                                                    on change of contents
	 *                               {function} onError   Callback method, triggered
	 *                                                    when an error occurs
	 *                               {Boolean} search     Enable search box.
	 *                                                    True by default
	 *                                                    Only applicable for modes
	 *                                                    'tree', 'view', and 'form'
	 *                               {Boolean} history    Enable history (undo/redo).
	 *                                                    True by default
	 *                                                    Only applicable for modes
	 *                                                    'tree', 'view', and 'form'
	 *                               {String} name        Field name for the root node.
	 *                                                    Only applicable for modes
	 *                                                    'tree', 'view', and 'form'
	 *                               {Number} indentation     Number of indentation
	 *                                                        spaces. 4 by default.
	 *                                                        Only applicable for
	 *                                                        modes 'text' and 'code'
	 *                               {boolean} escapeUnicode  If true, unicode
	 *                                                        characters are escaped.
	 *                                                        false by default.
	 *                               {boolean} sortObjectKeys If true, object keys are
	 *                                                        sorted before display.
	 *                                                        false by default.
	 *                               {function} onSelectionChange Callback method, 
	 *                                                            triggered on node selection change
	 *                                                            Only applicable for modes
	 *                                                            'tree', 'view', and 'form'
	 *                               {function} onTextSelectionChange Callback method, 
	 *                                                                triggered on text selection change
	 *                                                                Only applicable for modes
	 *                                                                'text' and 'code'
	 * @param {Object | undefined} json JSON object
	 */
	function JSONEditor (container, options, json) {
	  if (!(this instanceof JSONEditor)) {
	    throw new Error('JSONEditor constructor called without "new".');
	  }

	  // check for unsupported browser (IE8 and older)
	  var ieVersion = util.getInternetExplorerVersion();
	  if (ieVersion != -1 && ieVersion < 9) {
	    throw new Error('Unsupported browser, IE9 or newer required. ' +
	        'Please install the newest version of your browser.');
	  }

	  if (options) {
	    // check for deprecated options
	    if (options.error) {
	      console.warn('Option "error" has been renamed to "onError"');
	      options.onError = options.error;
	      delete options.error;
	    }
	    if (options.change) {
	      console.warn('Option "change" has been renamed to "onChange"');
	      options.onChange = options.change;
	      delete options.change;
	    }
	    if (options.editable) {
	      console.warn('Option "editable" has been renamed to "onEditable"');
	      options.onEditable = options.editable;
	      delete options.editable;
	    }

	    // validate options
	    if (options) {
	      var VALID_OPTIONS = [
	        'ajv', 'schema', 'schemaRefs','templates',
	        'ace', 'theme','autocomplete',
	        'onChange', 'onEditable', 'onError', 'onModeChange', 'onSelectionChange', 'onTextSelectionChange',
	        'escapeUnicode', 'history', 'search', 'mode', 'modes', 'name', 'indentation', 
	        'sortObjectKeys', 'navigationBar', 'statusBar', 'languages', 'language'
	      ];

	      Object.keys(options).forEach(function (option) {
	        if (VALID_OPTIONS.indexOf(option) === -1) {
	          console.warn('Unknown option "' + option + '". This option will be ignored');
	        }
	      });
	    }
	  }

	  if (arguments.length) {
	    this._create(container, options, json);
	  }
	}

	/**
	 * Configuration for all registered modes. Example:
	 * {
	 *     tree: {
	 *         mixin: TreeEditor,
	 *         data: 'json'
	 *     },
	 *     text: {
	 *         mixin: TextEditor,
	 *         data: 'text'
	 *     }
	 * }
	 *
	 * @type { Object.<String, {mixin: Object, data: String} > }
	 */
	JSONEditor.modes = {};

	// debounce interval for JSON schema vaidation in milliseconds
	JSONEditor.prototype.DEBOUNCE_INTERVAL = 150;

	/**
	 * Create the JSONEditor
	 * @param {Element} container    Container element
	 * @param {Object}  [options]    See description in constructor
	 * @param {Object | undefined} json JSON object
	 * @private
	 */
	JSONEditor.prototype._create = function (container, options, json) {
	  this.container = container;
	  this.options = options || {};
	  this.json = json || {};

	  var mode = this.options.mode || (this.options.modes && this.options.modes[0]) || 'tree';
	  this.setMode(mode);
	};

	/**
	 * Destroy the editor. Clean up DOM, event listeners, and web workers.
	 */
	JSONEditor.prototype.destroy = function () {};

	/**
	 * Set JSON object in editor
	 * @param {Object | undefined} json      JSON data
	 */
	JSONEditor.prototype.set = function (json) {
	  this.json = json;
	};

	/**
	 * Get JSON from the editor
	 * @returns {Object} json
	 */
	JSONEditor.prototype.get = function () {
	  return this.json;
	};

	/**
	 * Set string containing JSON for the editor
	 * @param {String | undefined} jsonText
	 */
	JSONEditor.prototype.setText = function (jsonText) {
	  this.json = util.parse(jsonText);
	};

	/**
	 * Get stringified JSON contents from the editor
	 * @returns {String} jsonText
	 */
	JSONEditor.prototype.getText = function () {
	  return JSON.stringify(this.json);
	};

	/**
	 * Set a field name for the root node.
	 * @param {String | undefined} name
	 */
	JSONEditor.prototype.setName = function (name) {
	  if (!this.options) {
	    this.options = {};
	  }
	  this.options.name = name;
	};

	/**
	 * Get the field name for the root node.
	 * @return {String | undefined} name
	 */
	JSONEditor.prototype.getName = function () {
	  return this.options && this.options.name;
	};

	/**
	 * Change the mode of the editor.
	 * JSONEditor will be extended with all methods needed for the chosen mode.
	 * @param {String} mode     Available modes: 'tree' (default), 'view', 'form',
	 *                          'text', and 'code'.
	 */
	JSONEditor.prototype.setMode = function (mode) {
	  var container = this.container;
	  var options = util.extend({}, this.options);
	  var oldMode = options.mode;
	  var data;
	  var name;

	  options.mode = mode;
	  var config = JSONEditor.modes[mode];
	  if (config) {
	    try {
	      var asText = (config.data == 'text');
	      name = this.getName();
	      data = this[asText ? 'getText' : 'get'](); // get text or json

	      this.destroy();
	      util.clear(this);
	      util.extend(this, config.mixin);
	      this.create(container, options);

	      this.setName(name);
	      this[asText ? 'setText' : 'set'](data); // set text or json

	      if (typeof config.load === 'function') {
	        try {
	          config.load.call(this);
	        }
	        catch (err) {
	          console.error(err);
	        }
	      }

	      if (typeof options.onModeChange === 'function' && mode !== oldMode) {
	        try {
	          options.onModeChange(mode, oldMode);
	        }
	        catch (err) {
	          console.error(err);
	        }
	      }
	    }
	    catch (err) {
	      this._onError(err);
	    }
	  }
	  else {
	    throw new Error('Unknown mode "' + options.mode + '"');
	  }
	};

	/**
	 * Get the current mode
	 * @return {string}
	 */
	JSONEditor.prototype.getMode = function () {
	  return this.options.mode;
	};

	/**
	 * Throw an error. If an error callback is configured in options.error, this
	 * callback will be invoked. Else, a regular error is thrown.
	 * @param {Error} err
	 * @private
	 */
	JSONEditor.prototype._onError = function(err) {
	  if (this.options && typeof this.options.onError === 'function') {
	    this.options.onError(err);
	  }
	  else {
	    throw err;
	  }
	};

	/**
	 * Set a JSON schema for validation of the JSON object.
	 * To remove the schema, call JSONEditor.setSchema(null)
	 * @param {Object | null} schema
	 * @param {Object.<string, Object>=} schemaRefs Schemas that are referenced using the `$ref` property from the JSON schema that are set in the `schema` option,
	 +  the object structure in the form of `{reference_key: schemaObject}`
	 */
	JSONEditor.prototype.setSchema = function (schema, schemaRefs) {
	  // compile a JSON schema validator if a JSON schema is provided
	  if (schema) {
	    var ajv;
	    try {
	      // grab ajv from options if provided, else create a new instance
	      ajv = this.options.ajv || Ajv({ allErrors: true, verbose: true });

	    }
	    catch (err) {
	      console.warn('Failed to create an instance of Ajv, JSON Schema validation is not available. Please use a JSONEditor bundle including Ajv, or pass an instance of Ajv as via the configuration option `ajv`.');
	    }

	    if (ajv) {
	      if(schemaRefs) {
	        for (var ref in schemaRefs) {
	          ajv.removeSchema(ref);  // When updating a schema - old refs has to be removed first
	          if(schemaRefs[ref]) {
	            ajv.addSchema(schemaRefs[ref], ref);
	          }
	        }
	        this.options.schemaRefs = schemaRefs;
	      }
	      this.validateSchema = ajv.compile(schema);

	      // add schema to the options, so that when switching to an other mode,
	      // the set schema is not lost
	      this.options.schema = schema;

	      // validate now
	      this.validate();
	    }

	    this.refresh(); // update DOM
	  }
	  else {
	    // remove current schema
	    this.validateSchema = null;
	    this.options.schema = null;
	    this.options.schemaRefs = null;
	    this.validate(); // to clear current error messages
	    this.refresh();  // update DOM
	  }
	};

	/**
	 * Validate current JSON object against the configured JSON schema
	 * Throws an exception when no JSON schema is configured
	 */
	JSONEditor.prototype.validate = function () {
	  // must be implemented by treemode and textmode
	};

	/**
	 * Refresh the rendered contents
	 */
	JSONEditor.prototype.refresh = function () {
	  // can be implemented by treemode and textmode
	};

	/**
	 * Register a plugin with one ore multiple modes for the JSON Editor.
	 *
	 * A mode is described as an object with properties:
	 *
	 * - `mode: String`           The name of the mode.
	 * - `mixin: Object`          An object containing the mixin functions which
	 *                            will be added to the JSONEditor. Must contain functions
	 *                            create, get, getText, set, and setText. May have
	 *                            additional functions.
	 *                            When the JSONEditor switches to a mixin, all mixin
	 *                            functions are added to the JSONEditor, and then
	 *                            the function `create(container, options)` is executed.
	 * - `data: 'text' | 'json'`  The type of data that will be used to load the mixin.
	 * - `[load: function]`       An optional function called after the mixin
	 *                            has been loaded.
	 *
	 * @param {Object | Array} mode  A mode object or an array with multiple mode objects.
	 */
	JSONEditor.registerMode = function (mode) {
	  var i, prop;

	  if (util.isArray(mode)) {
	    // multiple modes
	    for (i = 0; i < mode.length; i++) {
	      JSONEditor.registerMode(mode[i]);
	    }
	  }
	  else {
	    // validate the new mode
	    if (!('mode' in mode)) throw new Error('Property "mode" missing');
	    if (!('mixin' in mode)) throw new Error('Property "mixin" missing');
	    if (!('data' in mode)) throw new Error('Property "data" missing');
	    var name = mode.mode;
	    if (name in JSONEditor.modes) {
	      throw new Error('Mode "' + name + '" already registered');
	    }

	    // validate the mixin
	    if (typeof mode.mixin.create !== 'function') {
	      throw new Error('Required function "create" missing on mixin');
	    }
	    var reserved = ['setMode', 'registerMode', 'modes'];
	    for (i = 0; i < reserved.length; i++) {
	      prop = reserved[i];
	      if (prop in mode.mixin) {
	        throw new Error('Reserved property "' + prop + '" not allowed in mixin');
	      }
	    }

	    JSONEditor.modes[name] = mode;
	  }
	};

	// register tree and text modes
	JSONEditor.registerMode(treemode);
	JSONEditor.registerMode(textmode);

	module.exports = JSONEditor;


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';


	var Highlighter = __webpack_require__(2);
	var History = __webpack_require__(3);
	var SearchBox = __webpack_require__(6);
	var ContextMenu = __webpack_require__(7);
	var TreePath = __webpack_require__(9);
	var Node = __webpack_require__(10);
	var ModeSwitcher = __webpack_require__(13);
	var util = __webpack_require__(4);
	var autocomplete = __webpack_require__(14);
	var translate = __webpack_require__(8).translate;
	var setLanguages = __webpack_require__(8).setLanguages;
	var setLanguage = __webpack_require__(8).setLanguage;

	// create a mixin with the functions for tree mode
	var treemode = {};

	/**
	 * Create a tree editor
	 * @param {Element} container    Container element
	 * @param {Object}  [options]    Object with options. available options:
	 *                               {String} mode            Editor mode. Available values:
	 *                                                        'tree' (default), 'view',
	 *                                                        and 'form'.
	 *                               {Boolean} search         Enable search box.
	 *                                                        True by default
	 *                               {Boolean} history        Enable history (undo/redo).
	 *                                                        True by default
	 *                               {function} onChange      Callback method, triggered
	 *                                                        on change of contents
	 *                               {String} name            Field name for the root node.
	 *                               {boolean} escapeUnicode  If true, unicode
	 *                                                        characters are escaped.
	 *                                                        false by default.
	 *                               {Object} schema          A JSON Schema for validation
	 * @private
	 */
	treemode.create = function (container, options) {
	  if (!container) {
	    throw new Error('No container element provided.');
	  }
	  this.container = container;
	  this.dom = {};
	  this.highlighter = new Highlighter();
	  this.selection = undefined; // will hold the last input selection
	  this.multiselection = {
	    nodes: []
	  };
	  this.validateSchema = null; // will be set in .setSchema(schema)
	  this.errorNodes = [];

	  this.node = null;
	  this.focusTarget = null;

	  this._setOptions(options);

	  if (options.autocomplete)
	      this.autocomplete = new autocomplete(options.autocomplete);

	  if (this.options.history && this.options.mode !== 'view') {
	    this.history = new History(this);
	  }

	  this._createFrame();
	  this._createTable();
	};

	/**
	 * Destroy the editor. Clean up DOM, event listeners, and web workers.
	 */
	treemode.destroy = function () {
	  if (this.frame && this.container && this.frame.parentNode == this.container) {
	    this.container.removeChild(this.frame);
	    this.frame = null;
	  }
	  this.container = null;

	  this.dom = null;

	  this.clear();
	  this.node = null;
	  this.focusTarget = null;
	  this.selection = null;
	  this.multiselection = null;
	  this.errorNodes = null;
	  this.validateSchema = null;
	  this._debouncedValidate = null;

	  if (this.history) {
	    this.history.destroy();
	    this.history = null;
	  }

	  if (this.searchBox) {
	    this.searchBox.destroy();
	    this.searchBox = null;
	  }

	  if (this.modeSwitcher) {
	    this.modeSwitcher.destroy();
	    this.modeSwitcher = null;
	  }
	};

	/**
	 * Initialize and set default options
	 * @param {Object}  [options]    See description in constructor
	 * @private
	 */
	treemode._setOptions = function (options) {
	  this.options = {
	    search: true,
	    history: true,
	    mode: 'tree',
	    name: undefined,   // field name of root node
	    schema: null,
	    schemaRefs: null,
	    autocomplete: null,
	    navigationBar : true,
	    onSelectionChange: null
	  };

	  // copy all options
	  if (options) {
	    for (var prop in options) {
	      if (options.hasOwnProperty(prop)) {
	        this.options[prop] = options[prop];
	      }
	    }
	  }

	  // compile a JSON schema validator if a JSON schema is provided
	  this.setSchema(this.options.schema, this.options.schemaRefs);

	  // create a debounced validate function
	  this._debouncedValidate = util.debounce(this.validate.bind(this), this.DEBOUNCE_INTERVAL);

	  if (options.onSelectionChange) {
	    this.onSelectionChange(options.onSelectionChange);
	  }

	  setLanguages(this.options.languages);
	  setLanguage(this.options.language)
	};

	/**
	 * Set JSON object in editor
	 * @param {Object | undefined} json      JSON data
	 * @param {String}             [name]    Optional field name for the root node.
	 *                                       Can also be set using setName(name).
	 */
	treemode.set = function (json, name) {
	  // adjust field name for root node
	  if (name) {
	    // TODO: deprecated since version 2.2.0. Cleanup some day.
	    console.warn('Second parameter "name" is deprecated. Use setName(name) instead.');
	    this.options.name = name;
	  }

	  // verify if json is valid JSON, ignore when a function
	  if (json instanceof Function || (json === undefined)) {
	    this.clear();
	  }
	  else {
	    this.content.removeChild(this.table);  // Take the table offline

	    // replace the root node
	    var params = {
	      field: this.options.name,
	      value: json
	    };
	    var node = new Node(this, params);
	    this._setRoot(node);

	    // validate JSON schema (if configured)
	    this.validate();

	    // expand
	    var recurse = false;
	    this.node.expand(recurse);

	    this.content.appendChild(this.table);  // Put the table online again
	  }

	  // TODO: maintain history, store last state and previous document
	  if (this.history) {
	    this.history.clear();
	  }

	  // clear search
	  if (this.searchBox) {
	    this.searchBox.clear();
	  }
	};

	/**
	 * Get JSON object from editor
	 * @return {Object | undefined} json
	 */
	treemode.get = function () {
	  // remove focus from currently edited node
	  if (this.focusTarget) {
	    var node = Node.getNodeFromTarget(this.focusTarget);
	    if (node) {
	      node.blur();
	    }
	  }

	  if (this.node) {
	    return this.node.getValue();
	  }
	  else {
	    return undefined;
	  }
	};

	/**
	 * Get the text contents of the editor
	 * @return {String} jsonText
	 */
	treemode.getText = function() {
	  return JSON.stringify(this.get());
	};

	/**
	 * Set the text contents of the editor
	 * @param {String} jsonText
	 */
	treemode.setText = function(jsonText) {
	  try {
	    this.set(util.parse(jsonText)); // this can throw an error
	  }
	  catch (err) {
	    // try to sanitize json, replace JavaScript notation with JSON notation
	    var sanitizedJsonText = util.sanitize(jsonText);

	    // try to parse again
	    this.set(util.parse(sanitizedJsonText)); // this can throw an error
	  }
	};

	/**
	 * Set a field name for the root node.
	 * @param {String | undefined} name
	 */
	treemode.setName = function (name) {
	  this.options.name = name;
	  if (this.node) {
	    this.node.updateField(this.options.name);
	  }
	};

	/**
	 * Get the field name for the root node.
	 * @return {String | undefined} name
	 */
	treemode.getName = function () {
	  return this.options.name;
	};

	/**
	 * Set focus to the editor. Focus will be set to:
	 * - the first editable field or value, or else
	 * - to the expand button of the root node, or else
	 * - to the context menu button of the root node, or else
	 * - to the first button in the top menu
	 */
	treemode.focus = function () {
	  var input = this.content.querySelector('[contenteditable=true]');
	  if (input) {
	    input.focus();
	  }
	  else if (this.node.dom.expand) {
	    this.node.dom.expand.focus();
	  }
	  else if (this.node.dom.menu) {
	    this.node.dom.menu.focus();
	  }
	  else {
	    // focus to the first button in the menu
	    input = this.frame.querySelector('button');
	    if (input) {
	      input.focus();
	    }
	  }
	};

	/**
	 * Remove the root node from the editor
	 */
	treemode.clear = function () {
	  if (this.node) {
	    this.node.collapse();
	    this.tbody.removeChild(this.node.getDom());
	    delete this.node;
	  }
	};

	/**
	 * Set the root node for the json editor
	 * @param {Node} node
	 * @private
	 */
	treemode._setRoot = function (node) {
	  this.clear();

	  this.node = node;

	  // append to the dom
	  this.tbody.appendChild(node.getDom());
	};

	/**
	 * Search text in all nodes
	 * The nodes will be expanded when the text is found one of its childs,
	 * else it will be collapsed. Searches are case insensitive.
	 * @param {String} text
	 * @return {Object[]} results  Array with nodes containing the search results
	 *                             The result objects contains fields:
	 *                             - {Node} node,
	 *                             - {String} elem  the dom element name where
	 *                                              the result is found ('field' or
	 *                                              'value')
	 */
	treemode.search = function (text) {
	  var results;
	  if (this.node) {
	    this.content.removeChild(this.table);  // Take the table offline
	    results = this.node.search(text);
	    this.content.appendChild(this.table);  // Put the table online again
	  }
	  else {
	    results = [];
	  }

	  return results;
	};

	/**
	 * Expand all nodes
	 */
	treemode.expandAll = function () {
	  if (this.node) {
	    this.content.removeChild(this.table);  // Take the table offline
	    this.node.expand();
	    this.content.appendChild(this.table);  // Put the table online again
	  }
	};

	/**
	 * Collapse all nodes
	 */
	treemode.collapseAll = function () {
	  if (this.node) {
	    this.content.removeChild(this.table);  // Take the table offline
	    this.node.collapse();
	    this.content.appendChild(this.table);  // Put the table online again
	  }
	};

	/**
	 * The method onChange is called whenever a field or value is changed, created,
	 * deleted, duplicated, etc.
	 * @param {String} action  Change action. Available values: "editField",
	 *                         "editValue", "changeType", "appendNode",
	 *                         "removeNode", "duplicateNode", "moveNode", "expand",
	 *                         "collapse".
	 * @param {Object} params  Object containing parameters describing the change.
	 *                         The parameters in params depend on the action (for
	 *                         example for "editValue" the Node, old value, and new
	 *                         value are provided). params contains all information
	 *                         needed to undo or redo the action.
	 * @private
	 */
	treemode._onAction = function (action, params) {
	  // add an action to the history
	  if (this.history) {
	    this.history.add(action, params);
	  }

	  this._onChange();
	};

	/**
	 * Handle a change:
	 * - Validate JSON schema
	 * - Send a callback to the onChange listener if provided
	 * @private
	 */
	treemode._onChange = function () {
	  // validate JSON schema (if configured)
	  this._debouncedValidate();

	  // trigger the onChange callback
	  if (this.options.onChange) {
	    try {
	      this.options.onChange();
	    }
	    catch (err) {
	      console.error('Error in onChange callback: ', err);
	    }
	  }
	};

	/**
	 * Validate current JSON object against the configured JSON schema
	 * Throws an exception when no JSON schema is configured
	 */
	treemode.validate = function () {
	  // clear all current errors
	  if (this.errorNodes) {
	    this.errorNodes.forEach(function (node) {
	      node.setError(null);
	    });
	  }

	  var root = this.node;
	  if (!root) { // TODO: this should be redundant but is needed on mode switch
	    return;
	  }

	  // check for duplicate keys
	  var duplicateErrors = root.validate();

	  // validate the JSON
	  var schemaErrors = [];
	  if (this.validateSchema) {
	    var valid = this.validateSchema(root.getValue());
	    if (!valid) {
	      // apply all new errors
	      schemaErrors = this.validateSchema.errors
	          .map(function (error) {
	            return util.improveSchemaError(error);
	          })
	          .map(function findNode (error) {
	            return {
	              node: root.findNode(error.dataPath),
	              error: error
	            }
	          })
	          .filter(function hasNode (entry) {
	            return entry.node != null
	          });
	    }
	  }

	  var errorNodes = duplicateErrors.concat(schemaErrors);
	  var parentPairs = errorNodes
	      .reduce(function (all, entry) {
	          return entry.node
	              .findParents()
	              .filter(function (parent) {
	                  return !all.some(function (pair) {
	                    return pair[0] === parent;
	                  });
	              })
	              .map(function (parent) {
	                  return [parent, entry.node];
	              })
	              .concat(all);
	      }, []);

	  this.errorNodes = parentPairs
	      .map(function (pair) {
	          return {
	            node: pair[0],
	            child: pair[1],
	            error: {
	              message: pair[0].type === 'object'
	                  ? 'Contains invalid properties' // object
	                  : 'Contains invalid items'      // array
	            }
	          };
	      })
	      .concat(errorNodes)
	      .map(function setError (entry) {
	        entry.node.setError(entry.error, entry.child);
	        return entry.node;
	      });
	};

	/**
	 * Refresh the rendered contents
	 */
	treemode.refresh = function () {
	  if (this.node) {
	    this.node.updateDom({recurse: true});
	  }
	};

	/**
	 * Start autoscrolling when given mouse position is above the top of the
	 * editor contents, or below the bottom.
	 * @param {Number} mouseY  Absolute mouse position in pixels
	 */
	treemode.startAutoScroll = function (mouseY) {
	  var me = this;
	  var content = this.content;
	  var top = util.getAbsoluteTop(content);
	  var height = content.clientHeight;
	  var bottom = top + height;
	  var margin = 24;
	  var interval = 50; // ms

	  if ((mouseY < top + margin) && content.scrollTop > 0) {
	    this.autoScrollStep = ((top + margin) - mouseY) / 3;
	  }
	  else if (mouseY > bottom - margin &&
	      height + content.scrollTop < content.scrollHeight) {
	    this.autoScrollStep = ((bottom - margin) - mouseY) / 3;
	  }
	  else {
	    this.autoScrollStep = undefined;
	  }

	  if (this.autoScrollStep) {
	    if (!this.autoScrollTimer) {
	      this.autoScrollTimer = setInterval(function () {
	        if (me.autoScrollStep) {
	          content.scrollTop -= me.autoScrollStep;
	        }
	        else {
	          me.stopAutoScroll();
	        }
	      }, interval);
	    }
	  }
	  else {
	    this.stopAutoScroll();
	  }
	};

	/**
	 * Stop auto scrolling. Only applicable when scrolling
	 */
	treemode.stopAutoScroll = function () {
	  if (this.autoScrollTimer) {
	    clearTimeout(this.autoScrollTimer);
	    delete this.autoScrollTimer;
	  }
	  if (this.autoScrollStep) {
	    delete this.autoScrollStep;
	  }
	};


	/**
	 * Set the focus to an element in the editor, set text selection, and
	 * set scroll position.
	 * @param {Object} selection  An object containing fields:
	 *                            {Element | undefined} dom     The dom element
	 *                                                          which has focus
	 *                            {Range | TextRange} range     A text selection
	 *                            {Node[]} nodes                Nodes in case of multi selection
	 *                            {Number} scrollTop            Scroll position
	 */
	treemode.setDomSelection = function (selection) {
	  if (!selection) {
	    return;
	  }

	  if ('scrollTop' in selection && this.content) {
	    // TODO: animated scroll
	    this.content.scrollTop = selection.scrollTop;
	  }
	  if (selection.nodes) {
	    // multi-select
	    this.select(selection.nodes);
	  }
	  if (selection.range) {
	    util.setSelectionOffset(selection.range);
	  }
	  if (selection.dom) {
	    selection.dom.focus();
	  }
	};

	/**
	 * Get the current focus
	 * @return {Object} selection An object containing fields:
	 *                            {Element | undefined} dom     The dom element
	 *                                                          which has focus
	 *                            {Range | TextRange} range     A text selection
	 *                            {Node[]} nodes                Nodes in case of multi selection
	 *                            {Number} scrollTop            Scroll position
	 */
	treemode.getDomSelection = function () {
	  var range = util.getSelectionOffset();
	  if (range && range.container.nodeName !== 'DIV') { // filter on (editable) divs)
	    range = null;
	  }

	  return {
	    dom: this.focusTarget,
	    range: range,
	    nodes: this.multiselection.nodes.slice(0),
	    scrollTop: this.content ? this.content.scrollTop : 0
	  };
	};

	/**
	 * Adjust the scroll position such that given top position is shown at 1/4
	 * of the window height.
	 * @param {Number} top
	 * @param {function(boolean)} [callback]   Callback, executed when animation is
	 *                                         finished. The callback returns true
	 *                                         when animation is finished, or false
	 *                                         when not.
	 */
	treemode.scrollTo = function (top, callback) {
	  var content = this.content;
	  if (content) {
	    var editor = this;
	    // cancel any running animation
	    if (editor.animateTimeout) {
	      clearTimeout(editor.animateTimeout);
	      delete editor.animateTimeout;
	    }
	    if (editor.animateCallback) {
	      editor.animateCallback(false);
	      delete editor.animateCallback;
	    }

	    // calculate final scroll position
	    var height = content.clientHeight;
	    var bottom = content.scrollHeight - height;
	    var finalScrollTop = Math.min(Math.max(top - height / 4, 0), bottom);

	    // animate towards the new scroll position
	    var animate = function () {
	      var scrollTop = content.scrollTop;
	      var diff = (finalScrollTop - scrollTop);
	      if (Math.abs(diff) > 3) {
	        content.scrollTop += diff / 3;
	        editor.animateCallback = callback;
	        editor.animateTimeout = setTimeout(animate, 50);
	      }
	      else {
	        // finished
	        if (callback) {
	          callback(true);
	        }
	        content.scrollTop = finalScrollTop;
	        delete editor.animateTimeout;
	        delete editor.animateCallback;
	      }
	    };
	    animate();
	  }
	  else {
	    if (callback) {
	      callback(false);
	    }
	  }
	};

	/**
	 * Create main frame
	 * @private
	 */
	treemode._createFrame = function () {
	  // create the frame
	  this.frame = document.createElement('div');
	  this.frame.className = 'jsoneditor jsoneditor-mode-' + this.options.mode;
	  this.container.appendChild(this.frame);

	  // create one global event listener to handle all events from all nodes
	  var editor = this;
	  function onEvent(event) {
	    // when switching to mode "code" or "text" via the menu, some events
	    // are still fired whilst the _onEvent methods is already removed.
	    if (editor._onEvent) {
	      editor._onEvent(event);
	    }
	  }
	  this.frame.onclick = function (event) {
	    var target = event.target;// || event.srcElement;

	    onEvent(event);

	    // prevent default submit action of buttons when editor is located
	    // inside a form
	    if (target.nodeName == 'BUTTON') {
	      event.preventDefault();
	    }
	  };
	  this.frame.oninput = onEvent;
	  this.frame.onchange = onEvent;
	  this.frame.onkeydown = onEvent;
	  this.frame.onkeyup = onEvent;
	  this.frame.oncut = onEvent;
	  this.frame.onpaste = onEvent;
	  this.frame.onmousedown = onEvent;
	  this.frame.onmouseup = onEvent;
	  this.frame.onmouseover = onEvent;
	  this.frame.onmouseout = onEvent;
	  // Note: focus and blur events do not propagate, therefore they defined
	  // using an eventListener with useCapture=true
	  // see http://www.quirksmode.org/blog/archives/2008/04/delegating_the.html
	  util.addEventListener(this.frame, 'focus', onEvent, true);
	  util.addEventListener(this.frame, 'blur', onEvent, true);
	  this.frame.onfocusin = onEvent;  // for IE
	  this.frame.onfocusout = onEvent; // for IE

	  // create menu
	  this.menu = document.createElement('div');
	  this.menu.className = 'jsoneditor-menu';
	  this.frame.appendChild(this.menu);

	  // create expand all button
	  var expandAll = document.createElement('button');
	  expandAll.type = 'button';
	  expandAll.className = 'jsoneditor-expand-all';
	  expandAll.title = translate('expandAll');
	  expandAll.onclick = function () {
	    editor.expandAll();
	  };
	  this.menu.appendChild(expandAll);

	  // create collapse all button
	  var collapseAll = document.createElement('button');
	  collapseAll.type = 'button';
	  collapseAll.title = translate('collapseAll');
	  collapseAll.className = 'jsoneditor-collapse-all';
	  collapseAll.onclick = function () {
	    editor.collapseAll();
	  };
	  this.menu.appendChild(collapseAll);

	  // create undo/redo buttons
	  if (this.history) {
	    // create undo button
	    var undo = document.createElement('button');
	    undo.type = 'button';
	    undo.className = 'jsoneditor-undo jsoneditor-separator';
	    undo.title = translate('undo');
	    undo.onclick = function () {
	      editor._onUndo();
	    };
	    this.menu.appendChild(undo);
	    this.dom.undo = undo;

	    // create redo button
	    var redo = document.createElement('button');
	    redo.type = 'button';
	    redo.className = 'jsoneditor-redo';
	    redo.title = translate('redo');
	    redo.onclick = function () {
	      editor._onRedo();
	    };
	    this.menu.appendChild(redo);
	    this.dom.redo = redo;

	    // register handler for onchange of history
	    this.history.onChange = function () {
	      undo.disabled = !editor.history.canUndo();
	      redo.disabled = !editor.history.canRedo();
	    };
	    this.history.onChange();
	  }

	  // create mode box
	  if (this.options && this.options.modes && this.options.modes.length) {
	    var me = this;
	    this.modeSwitcher = new ModeSwitcher(this.menu, this.options.modes, this.options.mode, function onSwitch(mode) {
	      me.modeSwitcher.destroy();

	      // switch mode and restore focus
	      me.setMode(mode);
	      me.modeSwitcher.focus();
	    });
	  }

	  // create search box
	  if (this.options.search) {
	    this.searchBox = new SearchBox(this, this.menu);
	  }

	  if(this.options.navigationBar) {
	    // create second menu row for treepath
	    this.navBar = document.createElement('div');
	    this.navBar.className = 'jsoneditor-navigation-bar nav-bar-empty';
	    this.frame.appendChild(this.navBar);

	    this.treePath = new TreePath(this.navBar);
	    this.treePath.onSectionSelected(this._onTreePathSectionSelected.bind(this));
	    this.treePath.onContextMenuItemSelected(this._onTreePathMenuItemSelected.bind(this));
	  }
	};

	/**
	 * Perform an undo action
	 * @private
	 */
	treemode._onUndo = function () {
	  if (this.history) {
	    // undo last action
	    this.history.undo();

	    // fire change event
	    this._onChange();
	  }
	};

	/**
	 * Perform a redo action
	 * @private
	 */
	treemode._onRedo = function () {
	  if (this.history) {
	    // redo last action
	    this.history.redo();

	    // fire change event
	    this._onChange();
	  }
	};

	/**
	 * Event handler
	 * @param event
	 * @private
	 */
	treemode._onEvent = function (event) {
	  if (event.type === 'keydown') {
	    this._onKeyDown(event);
	  }

	  if (event.type === 'focus') {
	    this.focusTarget = event.target;
	  }

	  if (event.type === 'mousedown') {
	    this._startDragDistance(event);
	  }
	  if (event.type === 'mousemove' || event.type === 'mouseup' || event.type === 'click') {
	    this._updateDragDistance(event);
	  }

	  var node = Node.getNodeFromTarget(event.target);

	  if (node && this.options && this.options.navigationBar && node && (event.type === 'keydown' || event.type === 'mousedown')) {
	    // apply on next tick, right after the new key press is applied
	    var me = this;
	    setTimeout(function () {
	      me._updateTreePath(node.getNodePath());
	    })
	  }

	  if (node && node.selected) {
	    if (event.type === 'click') {
	      if (event.target === node.dom.menu) {
	        this.showContextMenu(event.target);

	        // stop propagation (else we will open the context menu of a single node)
	        return;
	      }

	      // deselect a multi selection
	      if (!event.hasMoved) {
	        this.deselect();
	      }
	    }

	    if (event.type === 'mousedown') {
	      // drag multiple nodes
	      Node.onDragStart(this.multiselection.nodes, event);
	    }
	  }
	  else {
	    if (event.type === 'mousedown') {
	      this.deselect();

	      if (node && event.target === node.dom.drag) {
	        // drag a singe node
	        Node.onDragStart(node, event);
	      }
	      else if (!node || (event.target !== node.dom.field && event.target !== node.dom.value && event.target !== node.dom.select)) {
	        // select multiple nodes
	        this._onMultiSelectStart(event);
	      }
	    }
	  }

	  if (node) {
	    node.onEvent(event);
	  }
	};

	/**
	 * Update TreePath components
	 * @param {Array<Node>} pathNodes list of nodes in path from root to selection 
	 * @private
	 */
	treemode._updateTreePath = function (pathNodes) {
	  if (pathNodes && pathNodes.length) {
	    util.removeClassName(this.navBar, 'nav-bar-empty');
	    
	    var pathObjs = [];
	    pathNodes.forEach(function (node) {
	      var pathObj = {
	        name: getName(node),
	        node: node,
	        children: []
	      }
	      if (node.childs && node.childs.length) {
	        node.childs.forEach(function (childNode) {
	          pathObj.children.push({
	            name: getName(childNode),
	            node: childNode
	          });
	        });
	      }
	      pathObjs.push(pathObj);
	    });
	    this.treePath.setPath(pathObjs);
	  } else {
	    util.addClassName(this.navBar, 'nav-bar-empty');
	  }

	  function getName(node) {
	    return node.field !== undefined
	        ? node._escapeHTML(node.field)
	        : (isNaN(node.index) ? node.type : node.index);
	  }
	};

	/**
	 * Callback for tree path section selection - focus the selected node in the tree
	 * @param {Object} pathObj path object that was represents the selected section node
	 * @private
	 */
	treemode._onTreePathSectionSelected = function (pathObj) {
	  if(pathObj && pathObj.node) {
	    pathObj.node.expandTo();
	    pathObj.node.focus();
	  }
	};

	/**
	 * Callback for tree path menu item selection - rebuild the path accrding to the new selection and focus the selected node in the tree
	 * @param {Object} pathObj path object that was represents the parent section node
	 * @param {String} selection selected section child
	 * @private
	 */
	treemode._onTreePathMenuItemSelected = function (pathObj, selection) {
	  if(pathObj && pathObj.children.length) {
	    var selectionObj = pathObj.children.find(function (obj) {
	      return obj.name === selection;
	    });
	    if(selectionObj && selectionObj.node) {
	      this._updateTreePath(selectionObj.node.getNodePath());
	      selectionObj.node.expandTo();
	      selectionObj.node.focus();
	    }
	  }
	};

	treemode._startDragDistance = function (event) {
	  this.dragDistanceEvent = {
	    initialTarget: event.target,
	    initialPageX: event.pageX,
	    initialPageY: event.pageY,
	    dragDistance: 0,
	    hasMoved: false
	  };
	};

	treemode._updateDragDistance = function (event) {
	  if (!this.dragDistanceEvent) {
	    this._startDragDistance(event);
	  }

	  var diffX = event.pageX - this.dragDistanceEvent.initialPageX;
	  var diffY = event.pageY - this.dragDistanceEvent.initialPageY;

	  this.dragDistanceEvent.dragDistance = Math.sqrt(diffX * diffX + diffY * diffY);
	  this.dragDistanceEvent.hasMoved =
	      this.dragDistanceEvent.hasMoved || this.dragDistanceEvent.dragDistance > 10;

	  event.dragDistance = this.dragDistanceEvent.dragDistance;
	  event.hasMoved = this.dragDistanceEvent.hasMoved;

	  return event.dragDistance;
	};

	/**
	 * Start multi selection of nodes by dragging the mouse
	 * @param event
	 * @private
	 */
	treemode._onMultiSelectStart = function (event) {
	  var node = Node.getNodeFromTarget(event.target);

	  if (this.options.mode !== 'tree' || this.options.onEditable !== undefined) {
	    // dragging not allowed in modes 'view' and 'form'
	    // TODO: allow multiselection of items when option onEditable is specified
	    return;
	  }

	  this.multiselection = {
	    start: node || null,
	    end: null,
	    nodes: []
	  };

	  this._startDragDistance(event);

	  var editor = this;
	  if (!this.mousemove) {
	    this.mousemove = util.addEventListener(window, 'mousemove', function (event) {
	      editor._onMultiSelect(event);
	    });
	  }
	  if (!this.mouseup) {
	    this.mouseup = util.addEventListener(window, 'mouseup', function (event ) {
	      editor._onMultiSelectEnd(event);
	    });
	  }

	};

	/**
	 * Multiselect nodes by dragging
	 * @param event
	 * @private
	 */
	treemode._onMultiSelect = function (event) {
	  event.preventDefault();

	  this._updateDragDistance(event);
	  if (!event.hasMoved) {
	    return;
	  }

	  var node = Node.getNodeFromTarget(event.target);

	  if (node) {
	    if (this.multiselection.start == null) {
	      this.multiselection.start = node;
	    }
	    this.multiselection.end = node;
	  }

	  // deselect previous selection
	  this.deselect();

	  // find the selected nodes in the range from first to last
	  var start = this.multiselection.start;
	  var end = this.multiselection.end || this.multiselection.start;
	  if (start && end) {
	    // find the top level childs, all having the same parent
	    this.multiselection.nodes = this._findTopLevelNodes(start, end);
	    if (this.multiselection.nodes && this.multiselection.nodes.length) {
	      var firstNode = this.multiselection.nodes[0];
	      if (this.multiselection.start === firstNode || this.multiselection.start.isDescendantOf(firstNode)) {
	        this.multiselection.direction = 'down';
	      } else {
	        this.multiselection.direction = 'up';
	      }
	    }
	    this.select(this.multiselection.nodes);
	  }
	};

	/**
	 * End of multiselect nodes by dragging
	 * @param event
	 * @private
	 */
	treemode._onMultiSelectEnd = function (event) {
	  // set focus to the context menu button of the first node
	  if (this.multiselection.nodes[0]) {
	    this.multiselection.nodes[0].dom.menu.focus();
	  }

	  this.multiselection.start = null;
	  this.multiselection.end = null;

	  // cleanup global event listeners
	  if (this.mousemove) {
	    util.removeEventListener(window, 'mousemove', this.mousemove);
	    delete this.mousemove;
	  }
	  if (this.mouseup) {
	    util.removeEventListener(window, 'mouseup', this.mouseup);
	    delete this.mouseup;
	  }
	};

	/**
	 * deselect currently selected nodes
	 * @param {boolean} [clearStartAndEnd=false]  If true, the `start` and `end`
	 *                                            state is cleared too. 
	 */
	treemode.deselect = function (clearStartAndEnd) {
	  var selectionChanged = !!this.multiselection.nodes.length;
	  this.multiselection.nodes.forEach(function (node) {
	    node.setSelected(false);
	  });
	  this.multiselection.nodes = [];

	  if (clearStartAndEnd) {
	    this.multiselection.start = null;
	    this.multiselection.end = null;
	  }

	  if (selectionChanged) {
	    if (this._selectionChangedHandler) {
	      this._selectionChangedHandler();
	    }
	  }
	};

	/**
	 * select nodes
	 * @param {Node[] | Node} nodes
	 */
	treemode.select = function (nodes) {
	  if (!Array.isArray(nodes)) {
	    return this.select([nodes]);
	  }

	  if (nodes) {
	    this.deselect();

	    this.multiselection.nodes = nodes.slice(0);

	    var first = nodes[0];
	    nodes.forEach(function (node) {
	      node.setSelected(true, node === first);
	    });

	    if (this._selectionChangedHandler) {
	      var selection = this.getSelection();
	      this._selectionChangedHandler(selection.start, selection.end);
	    }
	  }
	};

	/**
	 * From two arbitrary selected nodes, find their shared parent node.
	 * From that parent node, select the two child nodes in the brances going to
	 * nodes `start` and `end`, and select all childs in between.
	 * @param {Node} start
	 * @param {Node} end
	 * @return {Array.<Node>} Returns an ordered list with child nodes
	 * @private
	 */
	treemode._findTopLevelNodes = function (start, end) {
	  var startPath = start.getNodePath();
	  var endPath = end.getNodePath();
	  var i = 0;
	  while (i < startPath.length && startPath[i] === endPath[i]) {
	    i++;
	  }
	  var root = startPath[i - 1];
	  var startChild = startPath[i];
	  var endChild = endPath[i];

	  if (!startChild || !endChild) {
	    if (root.parent) {
	      // startChild is a parent of endChild or vice versa
	      startChild = root;
	      endChild = root;
	      root = root.parent
	    }
	    else {
	      // we have selected the root node (which doesn't have a parent)
	      startChild = root.childs[0];
	      endChild = root.childs[root.childs.length - 1];
	    }
	  }

	  if (root && startChild && endChild) {
	    var startIndex = root.childs.indexOf(startChild);
	    var endIndex = root.childs.indexOf(endChild);
	    var firstIndex = Math.min(startIndex, endIndex);
	    var lastIndex = Math.max(startIndex, endIndex);

	    return root.childs.slice(firstIndex, lastIndex + 1);
	  }
	  else {
	    return [];
	  }
	};

	/**
	 * Event handler for keydown. Handles shortcut keys
	 * @param {Event} event
	 * @private
	 */
	treemode._onKeyDown = function (event) {
	  var keynum = event.which || event.keyCode;
	  var altKey = event.altKey;
	  var ctrlKey = event.ctrlKey;
	  var metaKey = event.metaKey;
	  var shiftKey = event.shiftKey;
	  var handled = false;

	  if (keynum == 9) { // Tab or Shift+Tab
	    var me = this;
	    setTimeout(function () {
	      // select all text when moving focus to an editable div
	      util.selectContentEditable(me.focusTarget);
	    }, 0);
	  }

	  if (this.searchBox) {
	    if (ctrlKey && keynum == 70) { // Ctrl+F
	      this.searchBox.dom.search.focus();
	      this.searchBox.dom.search.select();
	      handled = true;
	    }
	    else if (keynum == 114 || (ctrlKey && keynum == 71)) { // F3 or Ctrl+G
	      var focus = true;
	      if (!shiftKey) {
	        // select next search result (F3 or Ctrl+G)
	        this.searchBox.next(focus);
	      }
	      else {
	        // select previous search result (Shift+F3 or Ctrl+Shift+G)
	        this.searchBox.previous(focus);
	      }

	      handled = true;
	    }
	  }

	  if (this.history) {
	    if (ctrlKey && !shiftKey && keynum == 90) { // Ctrl+Z
	      // undo
	      this._onUndo();
	      handled = true;
	    }
	    else if (ctrlKey && shiftKey && keynum == 90) { // Ctrl+Shift+Z
	      // redo
	      this._onRedo();
	      handled = true;
	    }
	  }

	  if ((this.options.autocomplete) && (!handled)) {
	      if (!ctrlKey && !altKey && !metaKey && (event.key.length == 1 || keynum == 8 || keynum == 46)) {
	          handled = false;
	          var jsonElementType = "";
	          if (event.target.className.indexOf("jsoneditor-value") >= 0) jsonElementType = "value";
	          if (event.target.className.indexOf("jsoneditor-field") >= 0) jsonElementType = "field";

	          var node = Node.getNodeFromTarget(event.target);
	          // Activate autocomplete
	          setTimeout(function (hnode, element) {
	              if (element.innerText.length > 0) {
	                  var result = this.options.autocomplete.getOptions(element.innerText, hnode.getPath(), jsonElementType, hnode.editor);
	                  if (result === null) {
	                      this.autocomplete.hideDropDown();
	                  } else if (typeof result.then === 'function') {
	                      // probably a promise
	                      if (result.then(function (obj) {
	                          if (obj === null) {
	                              this.autocomplete.hideDropDown();
	                          } else if (obj.options) {
	                              this.autocomplete.show(element, obj.startFrom, obj.options);
	                          } else {
	                              this.autocomplete.show(element, 0, obj);
	                          }
	                      }.bind(this)));
	                  } else {
	                      // definitely not a promise
	                      if (result.options)
	                          this.autocomplete.show(element, result.startFrom, result.options);
	                      else
	                          this.autocomplete.show(element, 0, result);
	                  }
	              }
	              else
	                  this.autocomplete.hideDropDown();

	          }.bind(this, node, event.target), 50);
	      } 
	  }

	  if (handled) {
	    event.preventDefault();
	    event.stopPropagation();
	  }
	};

	/**
	 * Create main table
	 * @private
	 */
	treemode._createTable = function () {
	  var contentOuter = document.createElement('div');
	  contentOuter.className = 'jsoneditor-outer';
	  if(this.options.navigationBar) {
	    util.addClassName(contentOuter, 'has-nav-bar');
	  }
	  this.contentOuter = contentOuter;

	  this.content = document.createElement('div');
	  this.content.className = 'jsoneditor-tree';
	  contentOuter.appendChild(this.content);

	  this.table = document.createElement('table');
	  this.table.className = 'jsoneditor-tree';
	  this.content.appendChild(this.table);

	  // create colgroup where the first two columns don't have a fixed
	  // width, and the edit columns do have a fixed width
	  var col;
	  this.colgroupContent = document.createElement('colgroup');
	  if (this.options.mode === 'tree') {
	    col = document.createElement('col');
	    col.width = "24px";
	    this.colgroupContent.appendChild(col);
	  }
	  col = document.createElement('col');
	  col.width = "24px";
	  this.colgroupContent.appendChild(col);
	  col = document.createElement('col');
	  this.colgroupContent.appendChild(col);
	  this.table.appendChild(this.colgroupContent);

	  this.tbody = document.createElement('tbody');
	  this.table.appendChild(this.tbody);

	  this.frame.appendChild(contentOuter);
	};

	/**
	 * Show a contextmenu for this node.
	 * Used for multiselection
	 * @param {HTMLElement} anchor   Anchor element to attach the context menu to.
	 * @param {function} [onClose]   Callback method called when the context menu
	 *                               is being closed.
	 */
	treemode.showContextMenu = function (anchor, onClose) {
	  var items = [];
	  var editor = this;

	  // create duplicate button
	  items.push({
	    text: translate('duplicateText'),
	    title: translate('duplicateTitle'),
	    className: 'jsoneditor-duplicate',
	    click: function () {
	      Node.onDuplicate(editor.multiselection.nodes);
	    }
	  });

	  // create remove button
	  items.push({
	    text: translate('remove'),
	    title: translate('removeTitle'),
	    className: 'jsoneditor-remove',
	    click: function () {
	      Node.onRemove(editor.multiselection.nodes);
	    }
	  });

	  var menu = new ContextMenu(items, {close: onClose});
	  menu.show(anchor, this.content);
	};

	/**
	 * Get current selected nodes
	 * @return {{start:SerializableNode, end: SerializableNode}}
	 */
	treemode.getSelection = function () {
	  var selection = {
	    start: null,
	    end: null
	  };
	  if (this.multiselection.nodes && this.multiselection.nodes.length) {
	    if (this.multiselection.nodes.length) {
	      var selection1 = this.multiselection.nodes[0];
	      var selection2 = this.multiselection.nodes[this.multiselection.nodes.length - 1];
	      if (this.multiselection.direction === 'down') {
	        selection.start = selection1.serialize();
	        selection.end = selection2.serialize();
	      } else {
	        selection.start = selection2.serialize();
	        selection.end = selection1.serialize();
	      }
	    }
	  }
	  return selection;
	};

	/**
	 * Callback registraion for selection change
	 * @param {selectionCallback} callback 
	 * 
	 * @callback selectionCallback
	 * @param {SerializableNode=} start
	 * @param {SerializableNode=} end
	 */
	treemode.onSelectionChange = function (callback) {
	  if (typeof callback === 'function') {
	    this._selectionChangedHandler = util.debounce(callback, this.DEBOUNCE_INTERVAL);
	  }
	};

	/**
	 * Select range of nodes.
	 * For selecting single node send only the start parameter
	 * For clear the selection do not send any parameter
	 * If the nodes are not from the same level the first common parent will be selected
	 * @param {{path: Array.<String>}} start object contains the path for selection start 
	 * @param {{path: Array.<String>}=} end object contains the path for selection end
	 */
	treemode.setSelection = function (start, end) {
	  // check for old usage
	  if (start && start.dom && start.range) {
	    console.warn('setSelection/getSelection usage for text selection is depracated and should not be used, see documantaion for supported selection options');
	    this.setDomSelection(start);
	  }

	  var nodes = this._getNodeIntsncesByRange(start, end);
	  
	  nodes.forEach(function(node) {
	    node.expandTo();
	  });
	  this.select(nodes);
	};

	/**
	 * Returns a set of Nodes according to a range of selection
	 * @param {{path: Array.<String>}} start object contains the path for range start 
	 * @param {{path: Array.<String>}=} end object contains the path for range end
	 * @return {Array.<Node>} Node intances on the given range
	 * @private
	 */
	treemode._getNodeIntsncesByRange = function (start, end) {
	  var startNode, endNode;

	  if (start && start.path) {
	    startNode = this.node.findNodeByPath(start.path);
	    if (end && end.path) {
	      endNode = this.node.findNodeByPath(end.path);
	    }
	  }

	  var nodes = [];
	  if (startNode instanceof Node) {
	    if (endNode instanceof Node && endNode !== startNode) {
	      if (startNode.parent === endNode.parent) {
	        var start, end;
	        if (startNode.getIndex() < endNode.getIndex()) {
	          start = startNode;
	          end = endNode;
	        } else {
	          start = endNode;
	          end = startNode;
	        }
	        var current = start;
	        nodes.push(current);
	        do {
	          current = current.nextSibling();
	          nodes.push(current);
	        } while (current && current !== end);
	      } else {
	        nodes = this._findTopLevelNodes(startNode, endNode);
	      }
	    } else {
	      nodes.push(startNode);
	    }
	  }

	  return nodes;

	};

	treemode.getNodesByRange = function (start, end) {
	  var nodes = this._getNodeIntsncesByRange(start, end);
	  var serializableNodes = [];

	  nodes.forEach(function (node){
	    serializableNodes.push(node.serialize());
	  });

	  return serializableNodes;
	}

	// define modes
	module.exports = [
	  {
	    mode: 'tree',
	    mixin: treemode,
	    data: 'json'
	  },
	  {
	    mode: 'view',
	    mixin: treemode,
	    data: 'json'
	  },
	  {
	    mode: 'form',
	    mixin: treemode,
	    data: 'json'
	  }
	];


/***/ },
/* 2 */
/***/ function(module, exports) {

	'use strict';

	/**
	 * The highlighter can highlight/unhighlight a node, and
	 * animate the visibility of a context menu.
	 * @constructor Highlighter
	 */
	function Highlighter () {
	  this.locked = false;
	}

	/**
	 * Hightlight given node and its childs
	 * @param {Node} node
	 */
	Highlighter.prototype.highlight = function (node) {
	  if (this.locked) {
	    return;
	  }

	  if (this.node != node) {
	    // unhighlight current node
	    if (this.node) {
	      this.node.setHighlight(false);
	    }

	    // highlight new node
	    this.node = node;
	    this.node.setHighlight(true);
	  }

	  // cancel any current timeout
	  this._cancelUnhighlight();
	};

	/**
	 * Unhighlight currently highlighted node.
	 * Will be done after a delay
	 */
	Highlighter.prototype.unhighlight = function () {
	  if (this.locked) {
	    return;
	  }

	  var me = this;
	  if (this.node) {
	    this._cancelUnhighlight();

	    // do the unhighlighting after a small delay, to prevent re-highlighting
	    // the same node when moving from the drag-icon to the contextmenu-icon
	    // or vice versa.
	    this.unhighlightTimer = setTimeout(function () {
	      me.node.setHighlight(false);
	      me.node = undefined;
	      me.unhighlightTimer = undefined;
	    }, 0);
	  }
	};

	/**
	 * Cancel an unhighlight action (if before the timeout of the unhighlight action)
	 * @private
	 */
	Highlighter.prototype._cancelUnhighlight = function () {
	  if (this.unhighlightTimer) {
	    clearTimeout(this.unhighlightTimer);
	    this.unhighlightTimer = undefined;
	  }
	};

	/**
	 * Lock highlighting or unhighlighting nodes.
	 * methods highlight and unhighlight do not work while locked.
	 */
	Highlighter.prototype.lock = function () {
	  this.locked = true;
	};

	/**
	 * Unlock highlighting or unhighlighting nodes
	 */
	Highlighter.prototype.unlock = function () {
	  this.locked = false;
	};

	module.exports = Highlighter;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var util = __webpack_require__(4);

	/**
	 * @constructor History
	 * Store action history, enables undo and redo
	 * @param {JSONEditor} editor
	 */
	function History (editor) {
	  this.editor = editor;
	  this.history = [];
	  this.index = -1;

	  this.clear();

	  // map with all supported actions
	  this.actions = {
	    'editField': {
	      'undo': function (params) {
	        params.node.updateField(params.oldValue);
	      },
	      'redo': function (params) {
	        params.node.updateField(params.newValue);
	      }
	    },
	    'editValue': {
	      'undo': function (params) {
	        params.node.updateValue(params.oldValue);
	      },
	      'redo': function (params) {
	        params.node.updateValue(params.newValue);
	      }
	    },
	    'changeType': {
	      'undo': function (params) {
	        params.node.changeType(params.oldType);
	      },
	      'redo': function (params) {
	        params.node.changeType(params.newType);
	      }
	    },

	    'appendNodes': {
	      'undo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.parent.removeChild(node);
	        });
	      },
	      'redo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.parent.appendChild(node);
	        });
	      }
	    },
	    'insertBeforeNodes': {
	      'undo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.parent.removeChild(node);
	        });
	      },
	      'redo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.parent.insertBefore(node, params.beforeNode);
	        });
	      }
	    },
	    'insertAfterNodes': {
	      'undo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.parent.removeChild(node);
	        });
	      },
	      'redo': function (params) {
	        var afterNode = params.afterNode;
	        params.nodes.forEach(function (node) {
	          params.parent.insertAfter(params.node, afterNode);
	          afterNode = node;
	        });
	      }
	    },
	    'removeNodes': {
	      'undo': function (params) {
	        var parent = params.parent;
	        var beforeNode = parent.childs[params.index] || parent.append;
	        params.nodes.forEach(function (node) {
	          parent.insertBefore(node, beforeNode);
	        });
	      },
	      'redo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.parent.removeChild(node);
	        });
	      }
	    },
	    'duplicateNodes': {
	      'undo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.parent.removeChild(node);
	        });
	      },
	      'redo': function (params) {
	        var afterNode = params.afterNode;
	        params.nodes.forEach(function (node) {
	          params.parent.insertAfter(node, afterNode);
	          afterNode = node;
	        });
	      }
	    },
	    'moveNodes': {
	      'undo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.oldBeforeNode.parent.moveBefore(node, params.oldBeforeNode);
	        });
	      },
	      'redo': function (params) {
	        params.nodes.forEach(function (node) {
	          params.newBeforeNode.parent.moveBefore(node, params.newBeforeNode);
	        });
	      }
	    },

	    'sort': {
	      'undo': function (params) {
	        var node = params.node;
	        node.hideChilds();
	        node.sort = params.oldSort;
	        node.childs = params.oldChilds;
	        node.showChilds();
	      },
	      'redo': function (params) {
	        var node = params.node;
	        node.hideChilds();
	        node.sort = params.newSort;
	        node.childs = params.newChilds;
	        node.showChilds();
	      }
	    }

	    // TODO: restore the original caret position and selection with each undo
	    // TODO: implement history for actions "expand", "collapse", "scroll", "setDocument"
	  };
	}

	/**
	 * The method onChange is executed when the History is changed, and can
	 * be overloaded.
	 */
	History.prototype.onChange = function () {};

	/**
	 * Add a new action to the history
	 * @param {String} action  The executed action. Available actions: "editField",
	 *                         "editValue", "changeType", "appendNode",
	 *                         "removeNode", "duplicateNode", "moveNode"
	 * @param {Object} params  Object containing parameters describing the change.
	 *                         The parameters in params depend on the action (for
	 *                         example for "editValue" the Node, old value, and new
	 *                         value are provided). params contains all information
	 *                         needed to undo or redo the action.
	 */
	History.prototype.add = function (action, params) {
	  this.index++;
	  this.history[this.index] = {
	    'action': action,
	    'params': params,
	    'timestamp': new Date()
	  };

	  // remove redo actions which are invalid now
	  if (this.index < this.history.length - 1) {
	    this.history.splice(this.index + 1, this.history.length - this.index - 1);
	  }

	  // fire onchange event
	  this.onChange();
	};

	/**
	 * Clear history
	 */
	History.prototype.clear = function () {
	  this.history = [];
	  this.index = -1;

	  // fire onchange event
	  this.onChange();
	};

	/**
	 * Check if there is an action available for undo
	 * @return {Boolean} canUndo
	 */
	History.prototype.canUndo = function () {
	  return (this.index >= 0);
	};

	/**
	 * Check if there is an action available for redo
	 * @return {Boolean} canRedo
	 */
	History.prototype.canRedo = function () {
	  return (this.index < this.history.length - 1);
	};

	/**
	 * Undo the last action
	 */
	History.prototype.undo = function () {
	  if (this.canUndo()) {
	    var obj = this.history[this.index];
	    if (obj) {
	      var action = this.actions[obj.action];
	      if (action && action.undo) {
	        action.undo(obj.params);
	        if (obj.params.oldSelection) {
	          this.editor.setDomSelection(obj.params.oldSelection);
	        }
	      }
	      else {
	        console.error(new Error('unknown action "' + obj.action + '"'));
	      }
	    }
	    this.index--;

	    // fire onchange event
	    this.onChange();
	  }
	};

	/**
	 * Redo the last action
	 */
	History.prototype.redo = function () {
	  if (this.canRedo()) {
	    this.index++;

	    var obj = this.history[this.index];
	    if (obj) {
	      var action = this.actions[obj.action];
	      if (action && action.redo) {
	        action.redo(obj.params);
	        if (obj.params.newSelection) {
	          this.editor.setDomSelection(obj.params.newSelection);
	        }
	      }
	      else {
	        console.error(new Error('unknown action "' + obj.action + '"'));
	      }
	    }

	    // fire onchange event
	    this.onChange();
	  }
	};

	/**
	 * Destroy history
	 */
	History.prototype.destroy = function () {
	  this.editor = null;

	  this.history = [];
	  this.index = -1;
	};

	module.exports = History;


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var jsonlint = __webpack_require__(5);

	/**
	 * Parse JSON using the parser built-in in the browser.
	 * On exception, the jsonString is validated and a detailed error is thrown.
	 * @param {String} jsonString
	 * @return {JSON} json
	 */
	exports.parse = function parse(jsonString) {
	  try {
	    return JSON.parse(jsonString);
	  }
	  catch (err) {
	    // try to throw a more detailed error message using validate
	    exports.validate(jsonString);

	    // rethrow the original error
	    throw err;
	  }
	};

	/**
	 * Sanitize a JSON-like string containing. For example changes JavaScript
	 * notation into JSON notation.
	 * This function for example changes a string like "{a: 2, 'b': {c: 'd'}"
	 * into '{"a": 2, "b": {"c": "d"}'
	 * @param {string} jsString
	 * @returns {string} json
	 */
	exports.sanitize = function (jsString) {
	  // escape all single and double quotes inside strings
	  var chars = [];
	  var i = 0;

	  //If JSON starts with a function (characters/digits/"_-"), remove this function.
	  //This is useful for "stripping" JSONP objects to become JSON
	  //For example: /* some comment */ function_12321321 ( [{"a":"b"}] ); => [{"a":"b"}]
	  var match = jsString.match(/^\s*(\/\*(.|[\r\n])*?\*\/)?\s*[\da-zA-Z_$]+\s*\(([\s\S]*)\)\s*;?\s*$/);
	  if (match) {
	    jsString = match[3];
	  }

	  var controlChars = {
	    '\b': '\\b',
	    '\f': '\\f',
	    '\n': '\\n',
	    '\r': '\\r',
	    '\t': '\\t'
	  };

	  var quote = '\'';
	  var quoteDbl = '"';
	  var quoteLeft = '\u2018';
	  var quoteRight = '\u2019';
	  var quoteDblLeft = '\u201C';
	  var quoteDblRight = '\u201D';
	  var graveAccent = '\u0060';
	  var acuteAccent = '\u00B4';

	  // helper functions to get the current/prev/next character
	  function curr () { return jsString.charAt(i);     }
	  function next()  { return jsString.charAt(i + 1); }
	  function prev()  { return jsString.charAt(i - 1); }

	  // get the last parsed non-whitespace character
	  function lastNonWhitespace () {
	    var p = chars.length - 1;

	    while (p >= 0) {
	      var pp = chars[p];
	      if (pp !== ' ' && pp !== '\n' && pp !== '\r' && pp !== '\t') { // non whitespace
	        return pp;
	      }
	      p--;
	    }

	    return '';
	  }

	  // skip a block comment '/* ... */'
	  function skipBlockComment () {
	    i += 2;
	    while (i < jsString.length && (curr() !== '*' || next() !== '/')) {
	      i++;
	    }
	    i += 2;
	  }

	  // skip a comment '// ...'
	  function skipComment () {
	    i += 2;
	    while (i < jsString.length && (curr() !== '\n')) {
	      i++;
	    }
	  }

	  // parse single or double quoted string
	  function parseString(endQuote) {
	    chars.push('"');
	    i++;
	    var c = curr();
	    while (i < jsString.length && c !== endQuote) {
	      if (c === '"' && prev() !== '\\') {
	        // unescaped double quote, escape it
	        chars.push('\\"');
	      }
	      else if (controlChars.hasOwnProperty(c)) {
	        // replace unescaped control characters with escaped ones
	        chars.push(controlChars[c])
	      }
	      else if (c === '\\') {
	        // remove the escape character when followed by a single quote ', not needed
	        i++;
	        c = curr();
	        if (c !== '\'') {
	          chars.push('\\');
	        }
	        chars.push(c);
	      }
	      else {
	        // regular character
	        chars.push(c);
	      }

	      i++;
	      c = curr();
	    }
	    if (c === endQuote) {
	      chars.push('"');
	      i++;
	    }
	  }

	  // parse an unquoted key
	  function parseKey() {
	    var specialValues = ['null', 'true', 'false'];
	    var key = '';
	    var c = curr();

	    var regexp = /[a-zA-Z_$\d]/; // letter, number, underscore, dollar character
	    while (regexp.test(c)) {
	      key += c;
	      i++;
	      c = curr();
	    }

	    if (specialValues.indexOf(key) === -1) {
	      chars.push('"' + key + '"');
	    }
	    else {
	      chars.push(key);
	    }
	  }

	  while(i < jsString.length) {
	    var c = curr();

	    if (c === '/' && next() === '*') {
	      skipBlockComment();
	    }
	    else if (c === '/' && next() === '/') {
	      skipComment();
	    }
	    else if (c === '\u00A0' || (c >= '\u2000' && c <= '\u200A') || c === '\u202F' || c === '\u205F' || c === '\u3000') {
	      // special white spaces (like non breaking space)
	      chars.push(' ')
	      i++
	    }
	    else if (c === quote) {
	      parseString(quote);
	    }
	    else if (c === quoteDbl) {
	      parseString(quoteDbl);
	    }
	    else if (c === graveAccent) {
	      parseString(acuteAccent);
	    }
	    else if (c === quoteLeft) {
	      parseString(quoteRight);
	    }
	    else if (c === quoteDblLeft) {
	      parseString(quoteDblRight);
	    }
	    else if (/[a-zA-Z_$]/.test(c) && ['{', ','].indexOf(lastNonWhitespace()) !== -1) {
	      // an unquoted object key (like a in '{a:2}')
	      parseKey();
	    }
	    else {
	      chars.push(c);
	      i++;
	    }
	  }

	  return chars.join('');
	};

	/**
	 * Escape unicode characters.
	 * For example input '\u2661' (length 1) will output '\\u2661' (length 5).
	 * @param {string} text
	 * @return {string}
	 */
	exports.escapeUnicodeChars = function (text) {
	  // see https://www.wikiwand.com/en/UTF-16
	  // note: we leave surrogate pairs as two individual chars,
	  // as JSON doesn't interpret them as a single unicode char.
	  return text.replace(/[\u007F-\uFFFF]/g, function(c) {
	    return '\\u'+('0000' + c.charCodeAt(0).toString(16)).slice(-4);
	  })
	};

	/**
	 * Validate a string containing a JSON object
	 * This method uses JSONLint to validate the String. If JSONLint is not
	 * available, the built-in JSON parser of the browser is used.
	 * @param {String} jsonString   String with an (invalid) JSON object
	 * @throws Error
	 */
	exports.validate = function validate(jsonString) {
	  if (typeof(jsonlint) != 'undefined') {
	    jsonlint.parse(jsonString);
	  }
	  else {
	    JSON.parse(jsonString);
	  }
	};

	/**
	 * Extend object a with the properties of object b
	 * @param {Object} a
	 * @param {Object} b
	 * @return {Object} a
	 */
	exports.extend = function extend(a, b) {
	  for (var prop in b) {
	    if (b.hasOwnProperty(prop)) {
	      a[prop] = b[prop];
	    }
	  }
	  return a;
	};

	/**
	 * Remove all properties from object a
	 * @param {Object} a
	 * @return {Object} a
	 */
	exports.clear = function clear (a) {
	  for (var prop in a) {
	    if (a.hasOwnProperty(prop)) {
	      delete a[prop];
	    }
	  }
	  return a;
	};

	/**
	 * Get the type of an object
	 * @param {*} object
	 * @return {String} type
	 */
	exports.type = function type (object) {
	  if (object === null) {
	    return 'null';
	  }
	  if (object === undefined) {
	    return 'undefined';
	  }
	  if ((object instanceof Number) || (typeof object === 'number')) {
	    return 'number';
	  }
	  if ((object instanceof String) || (typeof object === 'string')) {
	    return 'string';
	  }
	  if ((object instanceof Boolean) || (typeof object === 'boolean')) {
	    return 'boolean';
	  }
	  if ((object instanceof RegExp) || (typeof object === 'regexp')) {
	    return 'regexp';
	  }
	  if (exports.isArray(object)) {
	    return 'array';
	  }

	  return 'object';
	};

	/**
	 * Test whether a text contains a url (matches when a string starts
	 * with 'http://*' or 'https://*' and has no whitespace characters)
	 * @param {String} text
	 */
	var isUrlRegex = /^https?:\/\/\S+$/;
	exports.isUrl = function isUrl (text) {
	  return (typeof text == 'string' || text instanceof String) &&
	      isUrlRegex.test(text);
	};

	/**
	 * Tes whether given object is an Array
	 * @param {*} obj
	 * @returns {boolean} returns true when obj is an array
	 */
	exports.isArray = function (obj) {
	  return Object.prototype.toString.call(obj) === '[object Array]';
	};

	/**
	 * Retrieve the absolute left value of a DOM element
	 * @param {Element} elem    A dom element, for example a div
	 * @return {Number} left    The absolute left position of this element
	 *                          in the browser page.
	 */
	exports.getAbsoluteLeft = function getAbsoluteLeft(elem) {
	  var rect = elem.getBoundingClientRect();
	  return rect.left + window.pageXOffset || document.scrollLeft || 0;
	};

	/**
	 * Retrieve the absolute top value of a DOM element
	 * @param {Element} elem    A dom element, for example a div
	 * @return {Number} top     The absolute top position of this element
	 *                          in the browser page.
	 */
	exports.getAbsoluteTop = function getAbsoluteTop(elem) {
	  var rect = elem.getBoundingClientRect();
	  return rect.top + window.pageYOffset || document.scrollTop || 0;
	};

	/**
	 * add a className to the given elements style
	 * @param {Element} elem
	 * @param {String} className
	 */
	exports.addClassName = function addClassName(elem, className) {
	  var classes = elem.className.split(' ');
	  if (classes.indexOf(className) == -1) {
	    classes.push(className); // add the class to the array
	    elem.className = classes.join(' ');
	  }
	};

	/**
	 * add a className to the given elements style
	 * @param {Element} elem
	 * @param {String} className
	 */
	exports.removeClassName = function removeClassName(elem, className) {
	  var classes = elem.className.split(' ');
	  var index = classes.indexOf(className);
	  if (index != -1) {
	    classes.splice(index, 1); // remove the class from the array
	    elem.className = classes.join(' ');
	  }
	};

	/**
	 * Strip the formatting from the contents of a div
	 * the formatting from the div itself is not stripped, only from its childs.
	 * @param {Element} divElement
	 */
	exports.stripFormatting = function stripFormatting(divElement) {
	  var childs = divElement.childNodes;
	  for (var i = 0, iMax = childs.length; i < iMax; i++) {
	    var child = childs[i];

	    // remove the style
	    if (child.style) {
	      // TODO: test if child.attributes does contain style
	      child.removeAttribute('style');
	    }

	    // remove all attributes
	    var attributes = child.attributes;
	    if (attributes) {
	      for (var j = attributes.length - 1; j >= 0; j--) {
	        var attribute = attributes[j];
	        if (attribute.specified === true) {
	          child.removeAttribute(attribute.name);
	        }
	      }
	    }

	    // recursively strip childs
	    exports.stripFormatting(child);
	  }
	};

	/**
	 * Set focus to the end of an editable div
	 * code from Nico Burns
	 * http://stackoverflow.com/users/140293/nico-burns
	 * http://stackoverflow.com/questions/1125292/how-to-move-cursor-to-end-of-contenteditable-entity
	 * @param {Element} contentEditableElement   A content editable div
	 */
	exports.setEndOfContentEditable = function setEndOfContentEditable(contentEditableElement) {
	  var range, selection;
	  if(document.createRange) {
	    range = document.createRange();//Create a range (a range is a like the selection but invisible)
	    range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
	    range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
	    selection = window.getSelection();//get the selection object (allows you to change selection)
	    selection.removeAllRanges();//remove any selections already made
	    selection.addRange(range);//make the range you have just created the visible selection
	  }
	};

	/**
	 * Select all text of a content editable div.
	 * http://stackoverflow.com/a/3806004/1262753
	 * @param {Element} contentEditableElement   A content editable div
	 */
	exports.selectContentEditable = function selectContentEditable(contentEditableElement) {
	  if (!contentEditableElement || contentEditableElement.nodeName != 'DIV') {
	    return;
	  }

	  var sel, range;
	  if (window.getSelection && document.createRange) {
	    range = document.createRange();
	    range.selectNodeContents(contentEditableElement);
	    sel = window.getSelection();
	    sel.removeAllRanges();
	    sel.addRange(range);
	  }
	};

	/**
	 * Get text selection
	 * http://stackoverflow.com/questions/4687808/contenteditable-selected-text-save-and-restore
	 * @return {Range | TextRange | null} range
	 */
	exports.getSelection = function getSelection() {
	  if (window.getSelection) {
	    var sel = window.getSelection();
	    if (sel.getRangeAt && sel.rangeCount) {
	      return sel.getRangeAt(0);
	    }
	  }
	  return null;
	};

	/**
	 * Set text selection
	 * http://stackoverflow.com/questions/4687808/contenteditable-selected-text-save-and-restore
	 * @param {Range | TextRange | null} range
	 */
	exports.setSelection = function setSelection(range) {
	  if (range) {
	    if (window.getSelection) {
	      var sel = window.getSelection();
	      sel.removeAllRanges();
	      sel.addRange(range);
	    }
	  }
	};

	/**
	 * Get selected text range
	 * @return {Object} params  object containing parameters:
	 *                              {Number}  startOffset
	 *                              {Number}  endOffset
	 *                              {Element} container  HTML element holding the
	 *                                                   selected text element
	 *                          Returns null if no text selection is found
	 */
	exports.getSelectionOffset = function getSelectionOffset() {
	  var range = exports.getSelection();

	  if (range && 'startOffset' in range && 'endOffset' in range &&
	      range.startContainer && (range.startContainer == range.endContainer)) {
	    return {
	      startOffset: range.startOffset,
	      endOffset: range.endOffset,
	      container: range.startContainer.parentNode
	    };
	  }

	  return null;
	};

	/**
	 * Set selected text range in given element
	 * @param {Object} params   An object containing:
	 *                              {Element} container
	 *                              {Number} startOffset
	 *                              {Number} endOffset
	 */
	exports.setSelectionOffset = function setSelectionOffset(params) {
	  if (document.createRange && window.getSelection) {
	    var selection = window.getSelection();
	    if(selection) {
	      var range = document.createRange();

	      if (!params.container.firstChild) {
	        params.container.appendChild(document.createTextNode(''));
	      }

	      // TODO: do not suppose that the first child of the container is a textnode,
	      //       but recursively find the textnodes
	      range.setStart(params.container.firstChild, params.startOffset);
	      range.setEnd(params.container.firstChild, params.endOffset);

	      exports.setSelection(range);
	    }
	  }
	};

	/**
	 * Get the inner text of an HTML element (for example a div element)
	 * @param {Element} element
	 * @param {Object} [buffer]
	 * @return {String} innerText
	 */
	exports.getInnerText = function getInnerText(element, buffer) {
	  var first = (buffer == undefined);
	  if (first) {
	    buffer = {
	      'text': '',
	      'flush': function () {
	        var text = this.text;
	        this.text = '';
	        return text;
	      },
	      'set': function (text) {
	        this.text = text;
	      }
	    };
	  }

	  // text node
	  if (element.nodeValue) {
	    return buffer.flush() + element.nodeValue;
	  }

	  // divs or other HTML elements
	  if (element.hasChildNodes()) {
	    var childNodes = element.childNodes;
	    var innerText = '';

	    for (var i = 0, iMax = childNodes.length; i < iMax; i++) {
	      var child = childNodes[i];

	      if (child.nodeName == 'DIV' || child.nodeName == 'P') {
	        var prevChild = childNodes[i - 1];
	        var prevName = prevChild ? prevChild.nodeName : undefined;
	        if (prevName && prevName != 'DIV' && prevName != 'P' && prevName != 'BR') {
	          innerText += '\n';
	          buffer.flush();
	        }
	        innerText += exports.getInnerText(child, buffer);
	        buffer.set('\n');
	      }
	      else if (child.nodeName == 'BR') {
	        innerText += buffer.flush();
	        buffer.set('\n');
	      }
	      else {
	        innerText += exports.getInnerText(child, buffer);
	      }
	    }

	    return innerText;
	  }
	  else {
	    if (element.nodeName == 'P' && exports.getInternetExplorerVersion() != -1) {
	      // On Internet Explorer, a <p> with hasChildNodes()==false is
	      // rendered with a new line. Note that a <p> with
	      // hasChildNodes()==true is rendered without a new line
	      // Other browsers always ensure there is a <br> inside the <p>,
	      // and if not, the <p> does not render a new line
	      return buffer.flush();
	    }
	  }

	  // br or unknown
	  return '';
	};

	/**
	 * Returns the version of Internet Explorer or a -1
	 * (indicating the use of another browser).
	 * Source: http://msdn.microsoft.com/en-us/library/ms537509(v=vs.85).aspx
	 * @return {Number} Internet Explorer version, or -1 in case of an other browser
	 */
	exports.getInternetExplorerVersion = function getInternetExplorerVersion() {
	  if (_ieVersion == -1) {
	    var rv = -1; // Return value assumes failure.
	    if (navigator.appName == 'Microsoft Internet Explorer')
	    {
	      var ua = navigator.userAgent;
	      var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
	      if (re.exec(ua) != null) {
	        rv = parseFloat( RegExp.$1 );
	      }
	    }

	    _ieVersion = rv;
	  }

	  return _ieVersion;
	};

	/**
	 * Test whether the current browser is Firefox
	 * @returns {boolean} isFirefox
	 */
	exports.isFirefox = function isFirefox () {
	  return (navigator.userAgent.indexOf("Firefox") != -1);
	};

	/**
	 * cached internet explorer version
	 * @type {Number}
	 * @private
	 */
	var _ieVersion = -1;

	/**
	 * Add and event listener. Works for all browsers
	 * @param {Element}     element    An html element
	 * @param {string}      action     The action, for example "click",
	 *                                 without the prefix "on"
	 * @param {function}    listener   The callback function to be executed
	 * @param {boolean}     [useCapture] false by default
	 * @return {function}   the created event listener
	 */
	exports.addEventListener = function addEventListener(element, action, listener, useCapture) {
	  if (element.addEventListener) {
	    if (useCapture === undefined)
	      useCapture = false;

	    if (action === "mousewheel" && exports.isFirefox()) {
	      action = "DOMMouseScroll";  // For Firefox
	    }

	    element.addEventListener(action, listener, useCapture);
	    return listener;
	  } else if (element.attachEvent) {
	    // Old IE browsers
	    var f = function () {
	      return listener.call(element, window.event);
	    };
	    element.attachEvent("on" + action, f);
	    return f;
	  }
	};

	/**
	 * Remove an event listener from an element
	 * @param {Element}  element   An html dom element
	 * @param {string}   action    The name of the event, for example "mousedown"
	 * @param {function} listener  The listener function
	 * @param {boolean}  [useCapture]   false by default
	 */
	exports.removeEventListener = function removeEventListener(element, action, listener, useCapture) {
	  if (element.removeEventListener) {
	    if (useCapture === undefined)
	      useCapture = false;

	    if (action === "mousewheel" && exports.isFirefox()) {
	      action = "DOMMouseScroll";  // For Firefox
	    }

	    element.removeEventListener(action, listener, useCapture);
	  } else if (element.detachEvent) {
	    // Old IE browsers
	    element.detachEvent("on" + action, listener);
	  }
	};

	/**
	 * Parse a JSON path like '.items[3].name' into an array
	 * @param {string} jsonPath
	 * @return {Array}
	 */
	exports.parsePath = function parsePath(jsonPath) {
	  var prop, remainder;

	  if (jsonPath.length === 0) {
	    return [];
	  }

	  // find a match like '.prop'
	  var match = jsonPath.match(/^\.(\w+)/);
	  if (match) {
	    prop = match[1];
	    remainder = jsonPath.substr(prop.length + 1);
	  }
	  else if (jsonPath[0] === '[') {
	    // find a match like
	    var end = jsonPath.indexOf(']');
	    if (end === -1) {
	      throw new SyntaxError('Character ] expected in path');
	    }
	    if (end === 1) {
	      throw new SyntaxError('Index expected after [');
	    }

	    var value = jsonPath.substring(1, end);
	    if (value[0] === '\'') {
	      // ajv produces string prop names with single quotes, so we need
	      // to reformat them into valid double-quoted JSON strings
	      value = '\"' + value.substring(1, value.length - 1) + '\"';
	    }

	    prop = value === '*' ? value : JSON.parse(value); // parse string and number
	    remainder = jsonPath.substr(end + 1);
	  }
	  else {
	    throw new SyntaxError('Failed to parse path');
	  }

	  return [prop].concat(parsePath(remainder))
	};

	/**
	 * Improve the error message of a JSON schema error
	 * @param {Object} error
	 * @return {Object} The error
	 */
	exports.improveSchemaError = function (error) {
	  if (error.keyword === 'enum' && Array.isArray(error.schema)) {
	    var enums = error.schema;
	    if (enums) {
	      enums = enums.map(function (value) {
	        return JSON.stringify(value);
	      });

	      if (enums.length > 5) {
	        var more = ['(' + (enums.length - 5) + ' more...)'];
	        enums = enums.slice(0, 5);
	        enums.push(more);
	      }
	      error.message = 'should be equal to one of: ' + enums.join(', ');
	    }
	  }

	  if (error.keyword === 'additionalProperties') {
	    error.message = 'should NOT have additional property: ' + error.params.additionalProperty;
	  }

	  return error;
	};

	/**
	 * Test whether the child rect fits completely inside the parent rect.
	 * @param {ClientRect} parent
	 * @param {ClientRect} child
	 * @param {number} margin
	 */
	exports.insideRect = function (parent, child, margin) {
	  var _margin = margin !== undefined ? margin : 0;
	  return child.left   - _margin >= parent.left
	      && child.right  + _margin <= parent.right
	      && child.top    - _margin >= parent.top
	      && child.bottom + _margin <= parent.bottom;
	};

	/**
	 * Returns a function, that, as long as it continues to be invoked, will not
	 * be triggered. The function will be called after it stops being called for
	 * N milliseconds.
	 *
	 * Source: https://davidwalsh.name/javascript-debounce-function
	 *
	 * @param {function} func
	 * @param {number} wait                 Number in milliseconds
	 * @param {boolean} [immediate=false]   If `immediate` is passed, trigger the
	 *                                      function on the leading edge, instead
	 *                                      of the trailing.
	 * @return {function} Return the debounced function
	 */
	exports.debounce = function debounce(func, wait, immediate) {
	  var timeout;
	  return function() {
	    var context = this, args = arguments;
	    var later = function() {
	      timeout = null;
	      if (!immediate) func.apply(context, args);
	    };
	    var callNow = immediate && !timeout;
	    clearTimeout(timeout);
	    timeout = setTimeout(later, wait);
	    if (callNow) func.apply(context, args);
	  };
	};

	/**
	 * Determines the difference between two texts.
	 * Can only detect one removed or inserted block of characters.
	 * @param {string} oldText
	 * @param {string} newText
	 * @return {{start: number, end: number}} Returns the start and end
	 *                                        of the changed part in newText.
	 */
	exports.textDiff = function textDiff(oldText, newText) {
	  var len = newText.length;
	  var start = 0;
	  var oldEnd = oldText.length;
	  var newEnd = newText.length;

	  while (newText.charAt(start) === oldText.charAt(start)
	  && start < len) {
	    start++;
	  }

	  while (newText.charAt(newEnd - 1) === oldText.charAt(oldEnd - 1)
	  && newEnd > start && oldEnd > 0) {
	    newEnd--;
	    oldEnd--;
	  }

	  return {start: start, end: newEnd};
	};


	/**
	 * Return an object with the selection range or cursor position (if both have the same value)
	 * Support also old browsers (IE8-)
	 * Source: http://ourcodeworld.com/articles/read/282/how-to-get-the-current-cursor-position-and-selection-within-a-text-input-or-textarea-in-javascript
	 * @param {DOMElement} el A dom element of a textarea or input text.
	 * @return {Object} reference Object with 2 properties (start and end) with the identifier of the location of the cursor and selected text.
	 **/
	exports.getInputSelection = function(el) {
	  var startIndex = 0, endIndex = 0, normalizedValue, range, textInputRange, len, endRange;

	  if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
	      startIndex = el.selectionStart;
	      endIndex = el.selectionEnd;
	  } else {
	      range = document.selection.createRange();

	      if (range && range.parentElement() == el) {
	          len = el.value.length;
	          normalizedValue = el.value.replace(/\r\n/g, "\n");

	          // Create a working TextRange that lives only in the input
	          textInputRange = el.createTextRange();
	          textInputRange.moveToBookmark(range.getBookmark());

	          // Check if the startIndex and endIndex of the selection are at the very end
	          // of the input, since moveStart/moveEnd doesn't return what we want
	          // in those cases
	          endRange = el.createTextRange();
	          endRange.collapse(false);

	          if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
	              startIndex = endIndex = len;
	          } else {
	              startIndex = -textInputRange.moveStart("character", -len);
	              startIndex += normalizedValue.slice(0, startIndex).split("\n").length - 1;

	              if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
	                  endIndex = len;
	              } else {
	                  endIndex = -textInputRange.moveEnd("character", -len);
	                  endIndex += normalizedValue.slice(0, endIndex).split("\n").length - 1;
	              }
	          }
	      }
	  }

	  return {
	      startIndex: startIndex,
	      endIndex: endIndex,
	      start: _positionForIndex(startIndex),
	      end: _positionForIndex(endIndex)
	  };

	  /**
	   * Returns textarea row and column position for certain index
	   * @param {Number} index text index
	   * @returns {{row: Number, col: Number}}
	   */
	  function _positionForIndex(index) {
	    var textTillIndex = el.value.substring(0,index);
	    var row = (textTillIndex.match(/\n/g) || []).length + 1;
	    var col = textTillIndex.length - textTillIndex.lastIndexOf("\n");

	    return {
	      row: row,
	      column: col
	    }
	  }
	}

	/**
	 * Returns the index for certaion position in text element
	 * @param {DOMElement} el A dom element of a textarea or input text.
	 * @param {Number} row row value, > 0, if exceeds rows number - last row will be returned
	 * @param {Number} column column value, > 0, if exceeds column length - end of column will be returned
	 * @returns {Number} index of position in text, -1 if not found
	 */
	exports.getIndexForPosition = function(el, row, column) {
	  var text = el.value || '';
	  if (row > 0 && column > 0) {
	    var rows = text.split('\n', row);
	    row = Math.min(rows.length, row);
	    column = Math.min(rows[row - 1].length, column - 1);
	    var columnCount = (row == 1 ? column : column + 1); // count new line on multiple rows
	    return rows.slice(0, row - 1).join('\n').length + columnCount;
	  }
	  return -1;
	}


	if (typeof Element !== 'undefined') {
	  // Polyfill for array remove
	  (function () {
	    function polyfill (item) {
	      if (item.hasOwnProperty('remove')) {
	        return;
	      }
	      Object.defineProperty(item, 'remove', {
	        configurable: true,
	        enumerable: true,
	        writable: true,
	        value: function remove() {
	          if (this.parentNode != null)
	            this.parentNode.removeChild(this);
	        }
	      });
	    }

	    if (typeof Element !== 'undefined')       { polyfill(Element.prototype); }
	    if (typeof CharacterData !== 'undefined') { polyfill(CharacterData.prototype); }
	    if (typeof DocumentType !== 'undefined')  { polyfill(DocumentType.prototype); }
	  })();
	}


	// Polyfill for startsWith
	if (!String.prototype.startsWith) {
	    String.prototype.startsWith = function (searchString, position) {
	        position = position || 0;
	        return this.substr(position, searchString.length) === searchString;
	    };
	}

	// Polyfill for Array.find
	if (!Array.prototype.find) {
	  Array.prototype.find = function(callback) {    
	    for (var i = 0; i < this.length; i++) {
	      var element = this[i];
	      if ( callback.call(this, element, i, this) ) {
	        return element;
	      }
	    }
	  }
	}

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	/* Jison generated parser */
	var jsonlint = (function(){
	var parser = {trace: function trace() { },
	yy: {},
	symbols_: {"error":2,"JSONString":3,"STRING":4,"JSONNumber":5,"NUMBER":6,"JSONNullLiteral":7,"NULL":8,"JSONBooleanLiteral":9,"TRUE":10,"FALSE":11,"JSONText":12,"JSONValue":13,"EOF":14,"JSONObject":15,"JSONArray":16,"{":17,"}":18,"JSONMemberList":19,"JSONMember":20,":":21,",":22,"[":23,"]":24,"JSONElementList":25,"$accept":0,"$end":1},
	terminals_: {2:"error",4:"STRING",6:"NUMBER",8:"NULL",10:"TRUE",11:"FALSE",14:"EOF",17:"{",18:"}",21:":",22:",",23:"[",24:"]"},
	productions_: [0,[3,1],[5,1],[7,1],[9,1],[9,1],[12,2],[13,1],[13,1],[13,1],[13,1],[13,1],[13,1],[15,2],[15,3],[20,3],[19,1],[19,3],[16,2],[16,3],[25,1],[25,3]],
	performAction: function anonymous(yytext,yyleng,yylineno,yy,yystate,$$,_$) {

	var $0 = $$.length - 1;
	switch (yystate) {
	case 1: // replace escaped characters with actual character
	          this.$ = yytext.replace(/\\(\\|")/g, "$"+"1")
	                     .replace(/\\n/g,'\n')
	                     .replace(/\\r/g,'\r')
	                     .replace(/\\t/g,'\t')
	                     .replace(/\\v/g,'\v')
	                     .replace(/\\f/g,'\f')
	                     .replace(/\\b/g,'\b');
	        
	break;
	case 2:this.$ = Number(yytext);
	break;
	case 3:this.$ = null;
	break;
	case 4:this.$ = true;
	break;
	case 5:this.$ = false;
	break;
	case 6:return this.$ = $$[$0-1];
	break;
	case 13:this.$ = {};
	break;
	case 14:this.$ = $$[$0-1];
	break;
	case 15:this.$ = [$$[$0-2], $$[$0]];
	break;
	case 16:this.$ = {}; this.$[$$[$0][0]] = $$[$0][1];
	break;
	case 17:this.$ = $$[$0-2]; $$[$0-2][$$[$0][0]] = $$[$0][1];
	break;
	case 18:this.$ = [];
	break;
	case 19:this.$ = $$[$0-1];
	break;
	case 20:this.$ = [$$[$0]];
	break;
	case 21:this.$ = $$[$0-2]; $$[$0-2].push($$[$0]);
	break;
	}
	},
	table: [{3:5,4:[1,12],5:6,6:[1,13],7:3,8:[1,9],9:4,10:[1,10],11:[1,11],12:1,13:2,15:7,16:8,17:[1,14],23:[1,15]},{1:[3]},{14:[1,16]},{14:[2,7],18:[2,7],22:[2,7],24:[2,7]},{14:[2,8],18:[2,8],22:[2,8],24:[2,8]},{14:[2,9],18:[2,9],22:[2,9],24:[2,9]},{14:[2,10],18:[2,10],22:[2,10],24:[2,10]},{14:[2,11],18:[2,11],22:[2,11],24:[2,11]},{14:[2,12],18:[2,12],22:[2,12],24:[2,12]},{14:[2,3],18:[2,3],22:[2,3],24:[2,3]},{14:[2,4],18:[2,4],22:[2,4],24:[2,4]},{14:[2,5],18:[2,5],22:[2,5],24:[2,5]},{14:[2,1],18:[2,1],21:[2,1],22:[2,1],24:[2,1]},{14:[2,2],18:[2,2],22:[2,2],24:[2,2]},{3:20,4:[1,12],18:[1,17],19:18,20:19},{3:5,4:[1,12],5:6,6:[1,13],7:3,8:[1,9],9:4,10:[1,10],11:[1,11],13:23,15:7,16:8,17:[1,14],23:[1,15],24:[1,21],25:22},{1:[2,6]},{14:[2,13],18:[2,13],22:[2,13],24:[2,13]},{18:[1,24],22:[1,25]},{18:[2,16],22:[2,16]},{21:[1,26]},{14:[2,18],18:[2,18],22:[2,18],24:[2,18]},{22:[1,28],24:[1,27]},{22:[2,20],24:[2,20]},{14:[2,14],18:[2,14],22:[2,14],24:[2,14]},{3:20,4:[1,12],20:29},{3:5,4:[1,12],5:6,6:[1,13],7:3,8:[1,9],9:4,10:[1,10],11:[1,11],13:30,15:7,16:8,17:[1,14],23:[1,15]},{14:[2,19],18:[2,19],22:[2,19],24:[2,19]},{3:5,4:[1,12],5:6,6:[1,13],7:3,8:[1,9],9:4,10:[1,10],11:[1,11],13:31,15:7,16:8,17:[1,14],23:[1,15]},{18:[2,17],22:[2,17]},{18:[2,15],22:[2,15]},{22:[2,21],24:[2,21]}],
	defaultActions: {16:[2,6]},
	parseError: function parseError(str, hash) {
	    throw new Error(str);
	},
	parse: function parse(input) {
	    var self = this,
	        stack = [0],
	        vstack = [null], // semantic value stack
	        lstack = [], // location stack
	        table = this.table,
	        yytext = '',
	        yylineno = 0,
	        yyleng = 0,
	        recovering = 0,
	        TERROR = 2,
	        EOF = 1;

	    //this.reductionCount = this.shiftCount = 0;

	    this.lexer.setInput(input);
	    this.lexer.yy = this.yy;
	    this.yy.lexer = this.lexer;
	    if (typeof this.lexer.yylloc == 'undefined')
	        this.lexer.yylloc = {};
	    var yyloc = this.lexer.yylloc;
	    lstack.push(yyloc);

	    if (typeof this.yy.parseError === 'function')
	        this.parseError = this.yy.parseError;

	    function popStack (n) {
	        stack.length = stack.length - 2*n;
	        vstack.length = vstack.length - n;
	        lstack.length = lstack.length - n;
	    }

	    function lex() {
	        var token;
	        token = self.lexer.lex() || 1; // $end = 1
	        // if token isn't its numeric value, convert
	        if (typeof token !== 'number') {
	            token = self.symbols_[token] || token;
	        }
	        return token;
	    }

	    var symbol, preErrorSymbol, state, action, a, r, yyval={},p,len,newState, expected;
	    while (true) {
	        // retreive state number from top of stack
	        state = stack[stack.length-1];

	        // use default actions if available
	        if (this.defaultActions[state]) {
	            action = this.defaultActions[state];
	        } else {
	            if (symbol == null)
	                symbol = lex();
	            // read action for current state and first input
	            action = table[state] && table[state][symbol];
	        }

	        // handle parse error
	        _handle_error:
	        if (typeof action === 'undefined' || !action.length || !action[0]) {

	            if (!recovering) {
	                // Report error
	                expected = [];
	                for (p in table[state]) if (this.terminals_[p] && p > 2) {
	                    expected.push("'"+this.terminals_[p]+"'");
	                }
	                var errStr = '';
	                if (this.lexer.showPosition) {
	                    errStr = 'Parse error on line '+(yylineno+1)+":\n"+this.lexer.showPosition()+"\nExpecting "+expected.join(', ') + ", got '" + this.terminals_[symbol]+ "'";
	                } else {
	                    errStr = 'Parse error on line '+(yylineno+1)+": Unexpected " +
	                                  (symbol == 1 /*EOF*/ ? "end of input" :
	                                              ("'"+(this.terminals_[symbol] || symbol)+"'"));
	                }
	                this.parseError(errStr,
	                    {text: this.lexer.match, token: this.terminals_[symbol] || symbol, line: this.lexer.yylineno, loc: yyloc, expected: expected});
	            }

	            // just recovered from another error
	            if (recovering == 3) {
	                if (symbol == EOF) {
	                    throw new Error(errStr || 'Parsing halted.');
	                }

	                // discard current lookahead and grab another
	                yyleng = this.lexer.yyleng;
	                yytext = this.lexer.yytext;
	                yylineno = this.lexer.yylineno;
	                yyloc = this.lexer.yylloc;
	                symbol = lex();
	            }

	            // try to recover from error
	            while (1) {
	                // check for error recovery rule in this state
	                if ((TERROR.toString()) in table[state]) {
	                    break;
	                }
	                if (state == 0) {
	                    throw new Error(errStr || 'Parsing halted.');
	                }
	                popStack(1);
	                state = stack[stack.length-1];
	            }

	            preErrorSymbol = symbol; // save the lookahead token
	            symbol = TERROR;         // insert generic error symbol as new lookahead
	            state = stack[stack.length-1];
	            action = table[state] && table[state][TERROR];
	            recovering = 3; // allow 3 real symbols to be shifted before reporting a new error
	        }

	        // this shouldn't happen, unless resolve defaults are off
	        if (action[0] instanceof Array && action.length > 1) {
	            throw new Error('Parse Error: multiple actions possible at state: '+state+', token: '+symbol);
	        }

	        switch (action[0]) {

	            case 1: // shift
	                //this.shiftCount++;

	                stack.push(symbol);
	                vstack.push(this.lexer.yytext);
	                lstack.push(this.lexer.yylloc);
	                stack.push(action[1]); // push state
	                symbol = null;
	                if (!preErrorSymbol) { // normal execution/no error
	                    yyleng = this.lexer.yyleng;
	                    yytext = this.lexer.yytext;
	                    yylineno = this.lexer.yylineno;
	                    yyloc = this.lexer.yylloc;
	                    if (recovering > 0)
	                        recovering--;
	                } else { // error just occurred, resume old lookahead f/ before error
	                    symbol = preErrorSymbol;
	                    preErrorSymbol = null;
	                }
	                break;

	            case 2: // reduce
	                //this.reductionCount++;

	                len = this.productions_[action[1]][1];

	                // perform semantic action
	                yyval.$ = vstack[vstack.length-len]; // default to $$ = $1
	                // default location, uses first token for firsts, last for lasts
	                yyval._$ = {
	                    first_line: lstack[lstack.length-(len||1)].first_line,
	                    last_line: lstack[lstack.length-1].last_line,
	                    first_column: lstack[lstack.length-(len||1)].first_column,
	                    last_column: lstack[lstack.length-1].last_column
	                };
	                r = this.performAction.call(yyval, yytext, yyleng, yylineno, this.yy, action[1], vstack, lstack);

	                if (typeof r !== 'undefined') {
	                    return r;
	                }

	                // pop off stack
	                if (len) {
	                    stack = stack.slice(0,-1*len*2);
	                    vstack = vstack.slice(0, -1*len);
	                    lstack = lstack.slice(0, -1*len);
	                }

	                stack.push(this.productions_[action[1]][0]);    // push nonterminal (reduce)
	                vstack.push(yyval.$);
	                lstack.push(yyval._$);
	                // goto new state = table[STATE][NONTERMINAL]
	                newState = table[stack[stack.length-2]][stack[stack.length-1]];
	                stack.push(newState);
	                break;

	            case 3: // accept
	                return true;
	        }

	    }

	    return true;
	}};
	/* Jison generated lexer */
	var lexer = (function(){
	var lexer = ({EOF:1,
	parseError:function parseError(str, hash) {
	        if (this.yy.parseError) {
	            this.yy.parseError(str, hash);
	        } else {
	            throw new Error(str);
	        }
	    },
	setInput:function (input) {
	        this._input = input;
	        this._more = this._less = this.done = false;
	        this.yylineno = this.yyleng = 0;
	        this.yytext = this.matched = this.match = '';
	        this.conditionStack = ['INITIAL'];
	        this.yylloc = {first_line:1,first_column:0,last_line:1,last_column:0};
	        return this;
	    },
	input:function () {
	        var ch = this._input[0];
	        this.yytext+=ch;
	        this.yyleng++;
	        this.match+=ch;
	        this.matched+=ch;
	        var lines = ch.match(/\n/);
	        if (lines) this.yylineno++;
	        this._input = this._input.slice(1);
	        return ch;
	    },
	unput:function (ch) {
	        this._input = ch + this._input;
	        return this;
	    },
	more:function () {
	        this._more = true;
	        return this;
	    },
	less:function (n) {
	        this._input = this.match.slice(n) + this._input;
	    },
	pastInput:function () {
	        var past = this.matched.substr(0, this.matched.length - this.match.length);
	        return (past.length > 20 ? '...':'') + past.substr(-20).replace(/\n/g, "");
	    },
	upcomingInput:function () {
	        var next = this.match;
	        if (next.length < 20) {
	            next += this._input.substr(0, 20-next.length);
	        }
	        return (next.substr(0,20)+(next.length > 20 ? '...':'')).replace(/\n/g, "");
	    },
	showPosition:function () {
	        var pre = this.pastInput();
	        var c = new Array(pre.length + 1).join("-");
	        return pre + this.upcomingInput() + "\n" + c+"^";
	    },
	next:function () {
	        if (this.done) {
	            return this.EOF;
	        }
	        if (!this._input) this.done = true;

	        var token,
	            match,
	            tempMatch,
	            index,
	            col,
	            lines;
	        if (!this._more) {
	            this.yytext = '';
	            this.match = '';
	        }
	        var rules = this._currentRules();
	        for (var i=0;i < rules.length; i++) {
	            tempMatch = this._input.match(this.rules[rules[i]]);
	            if (tempMatch && (!match || tempMatch[0].length > match[0].length)) {
	                match = tempMatch;
	                index = i;
	                if (!this.options.flex) break;
	            }
	        }
	        if (match) {
	            lines = match[0].match(/\n.*/g);
	            if (lines) this.yylineno += lines.length;
	            this.yylloc = {first_line: this.yylloc.last_line,
	                           last_line: this.yylineno+1,
	                           first_column: this.yylloc.last_column,
	                           last_column: lines ? lines[lines.length-1].length-1 : this.yylloc.last_column + match[0].length}
	            this.yytext += match[0];
	            this.match += match[0];
	            this.yyleng = this.yytext.length;
	            this._more = false;
	            this._input = this._input.slice(match[0].length);
	            this.matched += match[0];
	            token = this.performAction.call(this, this.yy, this, rules[index],this.conditionStack[this.conditionStack.length-1]);
	            if (this.done && this._input) this.done = false;
	            if (token) return token;
	            else return;
	        }
	        if (this._input === "") {
	            return this.EOF;
	        } else {
	            this.parseError('Lexical error on line '+(this.yylineno+1)+'. Unrecognized text.\n'+this.showPosition(), 
	                    {text: "", token: null, line: this.yylineno});
	        }
	    },
	lex:function lex() {
	        var r = this.next();
	        if (typeof r !== 'undefined') {
	            return r;
	        } else {
	            return this.lex();
	        }
	    },
	begin:function begin(condition) {
	        this.conditionStack.push(condition);
	    },
	popState:function popState() {
	        return this.conditionStack.pop();
	    },
	_currentRules:function _currentRules() {
	        return this.conditions[this.conditionStack[this.conditionStack.length-1]].rules;
	    },
	topState:function () {
	        return this.conditionStack[this.conditionStack.length-2];
	    },
	pushState:function begin(condition) {
	        this.begin(condition);
	    }});
	lexer.options = {};
	lexer.performAction = function anonymous(yy,yy_,$avoiding_name_collisions,YY_START) {

	var YYSTATE=YY_START
	switch($avoiding_name_collisions) {
	case 0:/* skip whitespace */
	break;
	case 1:return 6
	break;
	case 2:yy_.yytext = yy_.yytext.substr(1,yy_.yyleng-2); return 4
	break;
	case 3:return 17
	break;
	case 4:return 18
	break;
	case 5:return 23
	break;
	case 6:return 24
	break;
	case 7:return 22
	break;
	case 8:return 21
	break;
	case 9:return 10
	break;
	case 10:return 11
	break;
	case 11:return 8
	break;
	case 12:return 14
	break;
	case 13:return 'INVALID'
	break;
	}
	};
	lexer.rules = [/^(?:\s+)/,/^(?:(-?([0-9]|[1-9][0-9]+))(\.[0-9]+)?([eE][-+]?[0-9]+)?\b)/,/^(?:"(?:\\[\\"bfnrt/]|\\u[a-fA-F0-9]{4}|[^\\\0-\x09\x0a-\x1f"])*")/,/^(?:\{)/,/^(?:\})/,/^(?:\[)/,/^(?:\])/,/^(?:,)/,/^(?::)/,/^(?:true\b)/,/^(?:false\b)/,/^(?:null\b)/,/^(?:$)/,/^(?:.)/];
	lexer.conditions = {"INITIAL":{"rules":[0,1,2,3,4,5,6,7,8,9,10,11,12,13],"inclusive":true}};


	;
	return lexer;})()
	parser.lexer = lexer;
	return parser;
	})();
	if (true) {
	  exports.parser = jsonlint;
	  exports.parse = jsonlint.parse.bind(jsonlint);
	}

/***/ },
/* 6 */
/***/ function(module, exports) {

	'use strict';

	/**
	 * @constructor SearchBox
	 * Create a search box in given HTML container
	 * @param {JSONEditor} editor    The JSON Editor to attach to
	 * @param {Element} container               HTML container element of where to
	 *                                          create the search box
	 */
	function SearchBox (editor, container) {
	  var searchBox = this;

	  this.editor = editor;
	  this.timeout = undefined;
	  this.delay = 200; // ms
	  this.lastText = undefined;

	  this.dom = {};
	  this.dom.container = container;

	  var table = document.createElement('table');
	  this.dom.table = table;
	  table.className = 'jsoneditor-search';
	  container.appendChild(table);
	  var tbody = document.createElement('tbody');
	  this.dom.tbody = tbody;
	  table.appendChild(tbody);
	  var tr = document.createElement('tr');
	  tbody.appendChild(tr);

	  var td = document.createElement('td');
	  tr.appendChild(td);
	  var results = document.createElement('div');
	  this.dom.results = results;
	  results.className = 'jsoneditor-results';
	  td.appendChild(results);

	  td = document.createElement('td');
	  tr.appendChild(td);
	  var divInput = document.createElement('div');
	  this.dom.input = divInput;
	  divInput.className = 'jsoneditor-frame';
	  divInput.title = 'Search fields and values';
	  td.appendChild(divInput);

	  // table to contain the text input and search button
	  var tableInput = document.createElement('table');
	  divInput.appendChild(tableInput);
	  var tbodySearch = document.createElement('tbody');
	  tableInput.appendChild(tbodySearch);
	  tr = document.createElement('tr');
	  tbodySearch.appendChild(tr);

	  var refreshSearch = document.createElement('button');
	  refreshSearch.type = 'button';
	  refreshSearch.className = 'jsoneditor-refresh';
	  td = document.createElement('td');
	  td.appendChild(refreshSearch);
	  tr.appendChild(td);

	  var search = document.createElement('input');
	  // search.type = 'button';
	  this.dom.search = search;
	  search.oninput = function (event) {
	    searchBox._onDelayedSearch(event);
	  };
	  search.onchange = function (event) { // For IE 9
	    searchBox._onSearch();
	  };
	  search.onkeydown = function (event) {
	    searchBox._onKeyDown(event);
	  };
	  search.onkeyup = function (event) {
	    searchBox._onKeyUp(event);
	  };
	  refreshSearch.onclick = function (event) {
	    search.select();
	  };

	  // TODO: ESC in FF restores the last input, is a FF bug, https://bugzilla.mozilla.org/show_bug.cgi?id=598819
	  td = document.createElement('td');
	  td.appendChild(search);
	  tr.appendChild(td);

	  var searchNext = document.createElement('button');
	  searchNext.type = 'button';
	  searchNext.title = 'Next result (Enter)';
	  searchNext.className = 'jsoneditor-next';
	  searchNext.onclick = function () {
	    searchBox.next();
	  };
	  td = document.createElement('td');
	  td.appendChild(searchNext);
	  tr.appendChild(td);

	  var searchPrevious = document.createElement('button');
	  searchPrevious.type = 'button';
	  searchPrevious.title = 'Previous result (Shift+Enter)';
	  searchPrevious.className = 'jsoneditor-previous';
	  searchPrevious.onclick = function () {
	    searchBox.previous();
	  };
	  td = document.createElement('td');
	  td.appendChild(searchPrevious);
	  tr.appendChild(td);
	}

	/**
	 * Go to the next search result
	 * @param {boolean} [focus]   If true, focus will be set to the next result
	 *                            focus is false by default.
	 */
	SearchBox.prototype.next = function(focus) {
	  if (this.results != undefined) {
	    var index = (this.resultIndex != undefined) ? this.resultIndex + 1 : 0;
	    if (index > this.results.length - 1) {
	      index = 0;
	    }
	    this._setActiveResult(index, focus);
	  }
	};

	/**
	 * Go to the prevous search result
	 * @param {boolean} [focus]   If true, focus will be set to the next result
	 *                            focus is false by default.
	 */
	SearchBox.prototype.previous = function(focus) {
	  if (this.results != undefined) {
	    var max = this.results.length - 1;
	    var index = (this.resultIndex != undefined) ? this.resultIndex - 1 : max;
	    if (index < 0) {
	      index = max;
	    }
	    this._setActiveResult(index, focus);
	  }
	};

	/**
	 * Set new value for the current active result
	 * @param {Number} index
	 * @param {boolean} [focus]   If true, focus will be set to the next result.
	 *                            focus is false by default.
	 * @private
	 */
	SearchBox.prototype._setActiveResult = function(index, focus) {
	  // de-activate current active result
	  if (this.activeResult) {
	    var prevNode = this.activeResult.node;
	    var prevElem = this.activeResult.elem;
	    if (prevElem == 'field') {
	      delete prevNode.searchFieldActive;
	    }
	    else {
	      delete prevNode.searchValueActive;
	    }
	    prevNode.updateDom();
	  }

	  if (!this.results || !this.results[index]) {
	    // out of range, set to undefined
	    this.resultIndex = undefined;
	    this.activeResult = undefined;
	    return;
	  }

	  this.resultIndex = index;

	  // set new node active
	  var node = this.results[this.resultIndex].node;
	  var elem = this.results[this.resultIndex].elem;
	  if (elem == 'field') {
	    node.searchFieldActive = true;
	  }
	  else {
	    node.searchValueActive = true;
	  }
	  this.activeResult = this.results[this.resultIndex];
	  node.updateDom();

	  // TODO: not so nice that the focus is only set after the animation is finished
	  node.scrollTo(function () {
	    if (focus) {
	      node.focus(elem);
	    }
	  });
	};

	/**
	 * Cancel any running onDelayedSearch.
	 * @private
	 */
	SearchBox.prototype._clearDelay = function() {
	  if (this.timeout != undefined) {
	    clearTimeout(this.timeout);
	    delete this.timeout;
	  }
	};

	/**
	 * Start a timer to execute a search after a short delay.
	 * Used for reducing the number of searches while typing.
	 * @param {Event} event
	 * @private
	 */
	SearchBox.prototype._onDelayedSearch = function (event) {
	  // execute the search after a short delay (reduces the number of
	  // search actions while typing in the search text box)
	  this._clearDelay();
	  var searchBox = this;
	  this.timeout = setTimeout(function (event) {
	    searchBox._onSearch();
	  },
	  this.delay);
	};

	/**
	 * Handle onSearch event
	 * @param {boolean} [forceSearch]  If true, search will be executed again even
	 *                                 when the search text is not changed.
	 *                                 Default is false.
	 * @private
	 */
	SearchBox.prototype._onSearch = function (forceSearch) {
	  this._clearDelay();

	  var value = this.dom.search.value;
	  var text = (value.length > 0) ? value : undefined;
	  if (text != this.lastText || forceSearch) {
	    // only search again when changed
	    this.lastText = text;
	    this.results = this.editor.search(text);
	    this._setActiveResult(undefined);

	    // display search results
	    if (text != undefined) {
	      var resultCount = this.results.length;
	      switch (resultCount) {
	        case 0: this.dom.results.innerHTML = 'no&nbsp;results'; break;
	        case 1: this.dom.results.innerHTML = '1&nbsp;result'; break;
	        default: this.dom.results.innerHTML = resultCount + '&nbsp;results'; break;
	      }
	    }
	    else {
	      this.dom.results.innerHTML = '';
	    }
	  }
	};

	/**
	 * Handle onKeyDown event in the input box
	 * @param {Event} event
	 * @private
	 */
	SearchBox.prototype._onKeyDown = function (event) {
	  var keynum = event.which;
	  if (keynum == 27) { // ESC
	    this.dom.search.value = '';  // clear search
	    this._onSearch();
	    event.preventDefault();
	    event.stopPropagation();
	  }
	  else if (keynum == 13) { // Enter
	    if (event.ctrlKey) {
	      // force to search again
	      this._onSearch(true);
	    }
	    else if (event.shiftKey) {
	      // move to the previous search result
	      this.previous();
	    }
	    else {
	      // move to the next search result
	      this.next();
	    }
	    event.preventDefault();
	    event.stopPropagation();
	  }
	};

	/**
	 * Handle onKeyUp event in the input box
	 * @param {Event} event
	 * @private
	 */
	SearchBox.prototype._onKeyUp = function (event) {
	  var keynum = event.keyCode;
	  if (keynum != 27 && keynum != 13) { // !show and !Enter
	    this._onDelayedSearch(event);   // For IE 9
	  }
	};

	/**
	 * Clear the search results
	 */
	SearchBox.prototype.clear = function () {
	  this.dom.search.value = '';
	  this._onSearch();
	};

	/**
	 * Destroy the search box
	 */
	SearchBox.prototype.destroy = function () {
	  this.editor = null;
	  this.dom.container.removeChild(this.dom.table);
	  this.dom = null;

	  this.results = null;
	  this.activeResult = null;

	  this._clearDelay();

	};

	module.exports = SearchBox;


/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var util = __webpack_require__(4);
	var translate = __webpack_require__(8).translate;

	/**
	 * Node.getRootNode shim
	 * @param  {Node} node node to check
	 * @return {Node}      node's rootNode or `window` if there is ShadowDOM is not supported.
	 */
	function getRootNode(node){
	    return node.getRootNode && node.getRootNode() || window;
	}

	/**
	 * A context menu
	 * @param {Object[]} items    Array containing the menu structure
	 *                            TODO: describe structure
	 * @param {Object} [options]  Object with options. Available options:
	 *                            {function} close    Callback called when the
	 *                                                context menu is being closed.
	 * @constructor
	 */
	function ContextMenu (items, options) {
	  this.dom = {};

	  var me = this;
	  var dom = this.dom;
	  this.anchor = undefined;
	  this.items = items;
	  this.eventListeners = {};
	  this.selection = undefined; // holds the selection before the menu was opened
	  this.onClose = options ? options.close : undefined;

	  // create root element
	  var root = document.createElement('div');
	  root.className = 'jsoneditor-contextmenu-root';
	  dom.root = root;

	  // create a container element
	  var menu = document.createElement('div');
	  menu.className = 'jsoneditor-contextmenu';
	  dom.menu = menu;
	  root.appendChild(menu);

	  // create a list to hold the menu items
	  var list = document.createElement('ul');
	  list.className = 'jsoneditor-menu';
	  menu.appendChild(list);
	  dom.list = list;
	  dom.items = []; // list with all buttons

	  // create a (non-visible) button to set the focus to the menu
	  var focusButton = document.createElement('button');
	  focusButton.type = 'button';
	  dom.focusButton = focusButton;
	  var li = document.createElement('li');
	  li.style.overflow = 'hidden';
	  li.style.height = '0';
	  li.appendChild(focusButton);
	  list.appendChild(li);

	  function createMenuItems (list, domItems, items) {
	    items.forEach(function (item) {
	      if (item.type == 'separator') {
	        // create a separator
	        var separator = document.createElement('div');
	        separator.className = 'jsoneditor-separator';
	        li = document.createElement('li');
	        li.appendChild(separator);
	        list.appendChild(li);
	      }
	      else {
	        var domItem = {};

	        // create a menu item
	        var li = document.createElement('li');
	        list.appendChild(li);

	        // create a button in the menu item
	        var button = document.createElement('button');
	        button.type = 'button';
	        button.className = item.className;
	        domItem.button = button;
	        if (item.title) {
	          button.title = item.title;
	        }
	        if (item.click) {
	          button.onclick = function (event) {
	            event.preventDefault();
	            me.hide();
	            item.click();
	          };
	        }
	        li.appendChild(button);

	        // create the contents of the button
	        if (item.submenu) {
	          // add the icon to the button
	          var divIcon = document.createElement('div');
	          divIcon.className = 'jsoneditor-icon';
	          button.appendChild(divIcon);
	          var divText = document.createElement('div');
	          divText.className = 'jsoneditor-text' +
	              (item.click ? '' : ' jsoneditor-right-margin');
	          divText.appendChild(document.createTextNode(item.text));
	          button.appendChild(divText);

	          var buttonSubmenu;
	          if (item.click) {
	            // submenu and a button with a click handler
	            button.className += ' jsoneditor-default';

	            var buttonExpand = document.createElement('button');
	            buttonExpand.type = 'button';
	            domItem.buttonExpand = buttonExpand;
	            buttonExpand.className = 'jsoneditor-expand';
	            buttonExpand.innerHTML = '<div class="jsoneditor-expand"></div>';
	            li.appendChild(buttonExpand);
	            if (item.submenuTitle) {
	              buttonExpand.title = item.submenuTitle;
	            }

	            buttonSubmenu = buttonExpand;
	          }
	          else {
	            // submenu and a button without a click handler
	            var divExpand = document.createElement('div');
	            divExpand.className = 'jsoneditor-expand';
	            button.appendChild(divExpand);

	            buttonSubmenu = button;
	          }

	          // attach a handler to expand/collapse the submenu
	          buttonSubmenu.onclick = function (event) {
	            event.preventDefault();
	            me._onExpandItem(domItem);
	            buttonSubmenu.focus();
	          };

	          // create the submenu
	          var domSubItems = [];
	          domItem.subItems = domSubItems;
	          var ul = document.createElement('ul');
	          domItem.ul = ul;
	          ul.className = 'jsoneditor-menu';
	          ul.style.height = '0';
	          li.appendChild(ul);
	          createMenuItems(ul, domSubItems, item.submenu);
	        }
	        else {
	          // no submenu, just a button with clickhandler
	          button.innerHTML = '<div class="jsoneditor-icon"></div>' +
	              '<div class="jsoneditor-text">' + translate(item.text) + '</div>';
	        }

	        domItems.push(domItem);
	      }
	    });
	  }
	  createMenuItems(list, this.dom.items, items);

	  // TODO: when the editor is small, show the submenu on the right instead of inline?

	  // calculate the max height of the menu with one submenu expanded
	  this.maxHeight = 0; // height in pixels
	  items.forEach(function (item) {
	    var height = (items.length + (item.submenu ? item.submenu.length : 0)) * 24;
	    me.maxHeight = Math.max(me.maxHeight, height);
	  });
	}

	/**
	 * Get the currently visible buttons
	 * @return {Array.<HTMLElement>} buttons
	 * @private
	 */
	ContextMenu.prototype._getVisibleButtons = function () {
	  var buttons = [];
	  var me = this;
	  this.dom.items.forEach(function (item) {
	    buttons.push(item.button);
	    if (item.buttonExpand) {
	      buttons.push(item.buttonExpand);
	    }
	    if (item.subItems && item == me.expandedItem) {
	      item.subItems.forEach(function (subItem) {
	        buttons.push(subItem.button);
	        if (subItem.buttonExpand) {
	          buttons.push(subItem.buttonExpand);
	        }
	        // TODO: change to fully recursive method
	      });
	    }
	  });

	  return buttons;
	};

	// currently displayed context menu, a singleton. We may only have one visible context menu
	ContextMenu.visibleMenu = undefined;

	/**
	 * Attach the menu to an anchor
	 * @param {HTMLElement} anchor          Anchor where the menu will be attached
	 *                                      as sibling.
	 * @param {HTMLElement} [contentWindow] The DIV with with the (scrollable) contents
	 */
	ContextMenu.prototype.show = function (anchor, contentWindow) {
	  this.hide();

	  // determine whether to display the menu below or above the anchor
	  var showBelow = true;
	  var parent = anchor.parentNode;
	  var anchorRect = anchor.getBoundingClientRect();
	  var parentRect = parent.getBoundingClientRect()

	  if (contentWindow) {
	    
	    var contentRect = contentWindow.getBoundingClientRect();

	    if (anchorRect.bottom + this.maxHeight < contentRect.bottom) {
	      // fits below -> show below
	    }
	    else if (anchorRect.top - this.maxHeight > contentRect.top) {
	      // fits above -> show above
	      showBelow = false;
	    }
	    else {
	      // doesn't fit above nor below -> show below
	    }
	  }

	  var leftGap = anchorRect.left - parentRect.left;
	  var topGap = anchorRect.top - parentRect.top;

	  // position the menu
	  if (showBelow) {
	    // display the menu below the anchor
	    var anchorHeight = anchor.offsetHeight;
	    this.dom.menu.style.left = leftGap + 'px';
	    this.dom.menu.style.top = topGap + anchorHeight + 'px';
	    this.dom.menu.style.bottom = '';
	  }
	  else {
	    // display the menu above the anchor
	    this.dom.menu.style.left = leftGap + 'px';
	    this.dom.menu.style.top = topGap + 'px';
	    this.dom.menu.style.bottom = '0px';
	  }

	  // find the root node of the page (window, or a shadow dom root element)
	  this.rootNode = getRootNode(anchor);

	  // attach the menu to the parent of the anchor
	  parent.insertBefore(this.dom.root, parent.firstChild);

	  // create and attach event listeners
	  var me = this;
	  var list = this.dom.list;
	  this.eventListeners.mousedown = util.addEventListener(this.rootNode, 'mousedown', function (event) {
	    // hide menu on click outside of the menu
	    var target = event.target;
	    if ((target != list) && !me._isChildOf(target, list)) {
	      me.hide();
	      event.stopPropagation();
	      event.preventDefault();
	    }
	  });
	  this.eventListeners.keydown = util.addEventListener(this.rootNode, 'keydown', function (event) {
	    me._onKeyDown(event);
	  });

	  // move focus to the first button in the context menu
	  this.selection = util.getSelection();
	  this.anchor = anchor;
	  setTimeout(function () {
	    me.dom.focusButton.focus();
	  }, 0);

	  if (ContextMenu.visibleMenu) {
	    ContextMenu.visibleMenu.hide();
	  }
	  ContextMenu.visibleMenu = this;
	};

	/**
	 * Hide the context menu if visible
	 */
	ContextMenu.prototype.hide = function () {
	  // remove the menu from the DOM
	  if (this.dom.root.parentNode) {
	    this.dom.root.parentNode.removeChild(this.dom.root);
	    if (this.onClose) {
	      this.onClose();
	    }
	  }

	  // remove all event listeners
	  // all event listeners are supposed to be attached to document.
	  for (var name in this.eventListeners) {
	    if (this.eventListeners.hasOwnProperty(name)) {
	      var fn = this.eventListeners[name];
	      if (fn) {
	        util.removeEventListener(this.rootNode, name, fn);
	      }
	      delete this.eventListeners[name];
	    }
	  }

	  if (ContextMenu.visibleMenu == this) {
	    ContextMenu.visibleMenu = undefined;
	  }
	};

	/**
	 * Expand a submenu
	 * Any currently expanded submenu will be hided.
	 * @param {Object} domItem
	 * @private
	 */
	ContextMenu.prototype._onExpandItem = function (domItem) {
	  var me = this;
	  var alreadyVisible = (domItem == this.expandedItem);

	  // hide the currently visible submenu
	  var expandedItem = this.expandedItem;
	  if (expandedItem) {
	    //var ul = expandedItem.ul;
	    expandedItem.ul.style.height = '0';
	    expandedItem.ul.style.padding = '';
	    setTimeout(function () {
	      if (me.expandedItem != expandedItem) {
	        expandedItem.ul.style.display = '';
	        util.removeClassName(expandedItem.ul.parentNode, 'jsoneditor-selected');
	      }
	    }, 300); // timeout duration must match the css transition duration
	    this.expandedItem = undefined;
	  }

	  if (!alreadyVisible) {
	    var ul = domItem.ul;
	    ul.style.display = 'block';
	    var height = ul.clientHeight; // force a reflow in Firefox
	    setTimeout(function () {
	      if (me.expandedItem == domItem) {
	        var childsHeight = 0;
	        for (var i = 0; i < ul.childNodes.length; i++) {
	          childsHeight += ul.childNodes[i].clientHeight;
	        }
	        ul.style.height = childsHeight + 'px';
	        ul.style.padding = '5px 10px';
	      }
	    }, 0);
	    util.addClassName(ul.parentNode, 'jsoneditor-selected');
	    this.expandedItem = domItem;
	  }
	};

	/**
	 * Handle onkeydown event
	 * @param {Event} event
	 * @private
	 */
	ContextMenu.prototype._onKeyDown = function (event) {
	  var target = event.target;
	  var keynum = event.which;
	  var handled = false;
	  var buttons, targetIndex, prevButton, nextButton;

	  if (keynum == 27) { // ESC
	    // hide the menu on ESC key

	    // restore previous selection and focus
	    if (this.selection) {
	      util.setSelection(this.selection);
	    }
	    if (this.anchor) {
	      this.anchor.focus();
	    }

	    this.hide();

	    handled = true;
	  }
	  else if (keynum == 9) { // Tab
	    if (!event.shiftKey) { // Tab
	      buttons = this._getVisibleButtons();
	      targetIndex = buttons.indexOf(target);
	      if (targetIndex == buttons.length - 1) {
	        // move to first button
	        buttons[0].focus();
	        handled = true;
	      }
	    }
	    else { // Shift+Tab
	      buttons = this._getVisibleButtons();
	      targetIndex = buttons.indexOf(target);
	      if (targetIndex == 0) {
	        // move to last button
	        buttons[buttons.length - 1].focus();
	        handled = true;
	      }
	    }
	  }
	  else if (keynum == 37) { // Arrow Left
	    if (target.className == 'jsoneditor-expand') {
	      buttons = this._getVisibleButtons();
	      targetIndex = buttons.indexOf(target);
	      prevButton = buttons[targetIndex - 1];
	      if (prevButton) {
	        prevButton.focus();
	      }
	    }
	    handled = true;
	  }
	  else if (keynum == 38) { // Arrow Up
	    buttons = this._getVisibleButtons();
	    targetIndex = buttons.indexOf(target);
	    prevButton = buttons[targetIndex - 1];
	    if (prevButton && prevButton.className == 'jsoneditor-expand') {
	      // skip expand button
	      prevButton = buttons[targetIndex - 2];
	    }
	    if (!prevButton) {
	      // move to last button
	      prevButton = buttons[buttons.length - 1];
	    }
	    if (prevButton) {
	      prevButton.focus();
	    }
	    handled = true;
	  }
	  else if (keynum == 39) { // Arrow Right
	    buttons = this._getVisibleButtons();
	    targetIndex = buttons.indexOf(target);
	    nextButton = buttons[targetIndex + 1];
	    if (nextButton && nextButton.className == 'jsoneditor-expand') {
	      nextButton.focus();
	    }
	    handled = true;
	  }
	  else if (keynum == 40) { // Arrow Down
	    buttons = this._getVisibleButtons();
	    targetIndex = buttons.indexOf(target);
	    nextButton = buttons[targetIndex + 1];
	    if (nextButton && nextButton.className == 'jsoneditor-expand') {
	      // skip expand button
	      nextButton = buttons[targetIndex + 2];
	    }
	    if (!nextButton) {
	      // move to first button
	      nextButton = buttons[0];
	    }
	    if (nextButton) {
	      nextButton.focus();
	      handled = true;
	    }
	    handled = true;
	  }
	  // TODO: arrow left and right

	  if (handled) {
	    event.stopPropagation();
	    event.preventDefault();
	  }
	};

	/**
	 * Test if an element is a child of a parent element.
	 * @param {Element} child
	 * @param {Element} parent
	 * @return {boolean} isChild
	 */
	ContextMenu.prototype._isChildOf = function (child, parent) {
	  var e = child.parentNode;
	  while (e) {
	    if (e == parent) {
	      return true;
	    }
	    e = e.parentNode;
	  }

	  return false;
	};

	module.exports = ContextMenu;


/***/ },
/* 8 */
/***/ function(module, exports) {

	'use strict';

	var _locales = ['en', 'pt-BR'];
	var _defs = {
	    en: {
	        'array': 'Array',
	        'auto': 'Auto',
	        'appendText': 'Append',
	        'appendTitle': 'Append a new field with type \'auto\' after this field (Ctrl+Shift+Ins)',
	        'appendSubmenuTitle': 'Select the type of the field to be appended',
	        'appendTitleAuto': 'Append a new field with type \'auto\' (Ctrl+Shift+Ins)',
	        'ascending': 'Ascending',
	        'ascendingTitle': 'Sort the childs of this ${type} in ascending order',
	        'actionsMenu': 'Click to open the actions menu (Ctrl+M)',
	        'collapseAll': 'Collapse all fields',
	        'descending': 'Descending',
	        'descendingTitle': 'Sort the childs of this ${type} in descending order',
	        'drag': 'Drag to move this field (Alt+Shift+Arrows)',
	        'duplicateKey': 'duplicate key',
	        'duplicateText': 'Duplicate',
	        'duplicateTitle': 'Duplicate selected fields (Ctrl+D)',
	        'duplicateField': 'Duplicate this field (Ctrl+D)',
	        'empty': 'empty',
	        'expandAll': 'Expand all fields',
	        'expandTitle': 'Click to expand/collapse this field (Ctrl+E). \n' +
	            'Ctrl+Click to expand/collapse including all childs.',
	        'insert': 'Insert',
	        'insertTitle': 'Insert a new field with type \'auto\' before this field (Ctrl+Ins)',
	        'insertSub': 'Select the type of the field to be inserted',
	        'object': 'Object',
	        'redo': 'Redo (Ctrl+Shift+Z)',
	        'removeText': 'Remove',
	        'removeTitle': 'Remove selected fields (Ctrl+Del)',
	        'removeField': 'Remove this field (Ctrl+Del)',
	        'sort': 'Sort',
	        'sortTitle': 'Sort the childs of this ',
	        'string': 'String',
	        'type': 'Type',
	        'typeTitle': 'Change the type of this field',
	        'openUrl': 'Ctrl+Click or Ctrl+Enter to open url in new window',
	        'undo': 'Undo last action (Ctrl+Z)',
	        'validationCannotMove': 'Cannot move a field into a child of itself',
	        'autoType': 'Field type "auto". ' +
	            'The field type is automatically determined from the value ' +
	            'and can be a string, number, boolean, or null.',
	        'objectType': 'Field type "object". ' +
	            'An object contains an unordered set of key/value pairs.',
	        'arrayType': 'Field type "array". ' +
	            'An array contains an ordered collection of values.',
	        'stringType': 'Field type "string". ' +
	            'Field type is not determined from the value, ' +
	            'but always returned as string.'
	    },
	    'pt-BR': {
	        'array': 'Lista',
	        'auto': 'Automatico',
	        'appendText': 'Adicionar',
	        'appendTitle': 'Adicionar novo campo com tipo \'auto\' depois deste campo (Ctrl+Shift+Ins)',
	        'appendSubmenuTitle': 'Selecione o tipo do campo a ser adicionado',
	        'appendTitleAuto': 'Adicionar novo campo com tipo \'auto\' (Ctrl+Shift+Ins)',
	        'ascending': 'Ascendente',
	        'ascendingTitle': 'Organizar filhor do tipo ${type} em crescente',
	        'actionsMenu': 'Clique para abrir o menu de ações (Ctrl+M)',
	        'collapseAll': 'Fechar todos campos',
	        'descending': 'Descendente',
	        'descendingTitle': 'Organizar o filhos do tipo ${type} em decrescente',
	        'duplicateKey': 'chave duplicada',
	        'drag': 'Arraste para mover este campo (Alt+Shift+Arrows)',
	        'duplicateText': 'Duplicar',
	        'duplicateTitle': 'Duplicar campos selecionados (Ctrl+D)',
	        'duplicateField': 'Duplicar este campo (Ctrl+D)',
	        'empty': 'vazio',
	        'expandAll': 'Expandir todos campos',
	        'expandTitle': 'Clique para expandir/encolher este campo (Ctrl+E). \n' +
	            'Ctrl+Click para expandir/encolher incluindo todos os filhos.',
	        'insert': 'Inserir',
	        'insertTitle': 'Inserir um novo campo do tipo \'auto\' antes deste campo (Ctrl+Ins)',
	        'insertSub': 'Selecionar o tipo de campo a ser inserido',
	        'object': 'Objeto',
	        'redo': 'Refazer (Ctrl+Shift+Z)',
	        'removeText': 'Remover',
	        'removeTitle': 'Remover campos selecionados (Ctrl+Del)',
	        'removeField': 'Remover este campo (Ctrl+Del)',
	        'sort': 'Organizar',
	        'sortTitle': 'Organizar os filhos deste ',
	        'string': 'Texto',
	        'type': 'Tipo',
	        'typeTitle': 'Mudar o tipo deste campo',
	        'openUrl': 'Ctrl+Click ou Ctrl+Enter para abrir link em nova janela',
	        'undo': 'Desfazer último ação (Ctrl+Z)',
	        'validationCannotMove': 'Não pode mover um campo como filho dele mesmo',
	        'autoType': 'Campo do tipo "auto". ' +
	            'O tipo do campo é determinao automaticamente a partir do seu valor ' +
	            'e pode ser texto, número, verdade/falso ou nulo.',
	        'objectType': 'Campo do tipo "objeto". ' +
	            'Um objeto contém uma lista de pares com chave e valor.',
	        'arrayType': 'Campo do tipo "lista". ' +
	            'Uma lista contem uma coleção de valores ordenados.',
	        'stringType': 'Campo do tipo "string". ' +
	            'Campo do tipo nao é determinado através do seu valor, ' +
	            'mas sempre retornara um texto.'
	    }
	};

	var _defaultLang = 'en';
	var _lang;
	var userLang = navigator.language || navigator.userLanguage;
	_lang = _locales.find(function (l) {
	    return l === userLang;
	});
	if (!_lang) {
	    _lang = _defaultLang;
	}

	module.exports = {
	    // supported locales
	    _locales: _locales,
	    _defs: _defs,
	    _lang: _lang,
	    setLanguage: function (lang) {
	        if (!lang) {
	            return;
	        }
	        var langFound = _locales.find(function (l) {
	            return l === lang;
	        });
	        if (langFound) {
	            _lang = langFound;
	        } else {
	            console.error('Language not found');
	        }
	    },
	    setLanguages: function (languages) {
	        if (!languages) {
	            return;
	        }
	        for (var key in languages) {
	            var langFound = _locales.find(function (l) {
	                return l === key;
	            });
	            if (!langFound) {
	                _locales.push(key);
	            }
	            _defs[key] = Object.assign({}, _defs[_defaultLang], _defs[key], languages[key]);
	        }
	    },
	    translate: function (key, data, lang) {
	        if (!lang) {
	            lang = _lang;
	        }
	        var text = _defs[lang][key];
	        if (data) {
	            for (key in data) {
	                text = text.replace('${' + key + '}', data[key]);
	            }
	        }
	        return text || key;
	    }
	};

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var ContextMenu = __webpack_require__(7);

	/**
	 * Creates a component that visualize path selection in tree based editors
	 * @param {HTMLElement} container 
	 * @constructor
	 */
	function TreePath(container) {
	  if (container) {
	    this.path = document.createElement('div');
	    this.path.className = 'jsoneditor-treepath';
	    container.appendChild(this.path);
	    this.reset();
	  }
	};

	/**
	 * Reset component to initial status
	 */
	TreePath.prototype.reset = function () {
	  this.path.innerHTML = '';
	}

	/**
	 * Renders the component UI according to a given path objects
	 * @param {Array<name: String, childs: Array>} pathObjs a list of path objects
	 * 
	 */
	TreePath.prototype.setPath = function (pathObjs) {
	  var me = this;
	  this.reset();
	  if (pathObjs && pathObjs.length) {
	    pathObjs.forEach(function (pathObj, idx) {
	      var pathEl = document.createElement('span');
	      var sepEl;
	      pathEl.className = 'jsoneditor-treepath-element';
	      pathEl.innerText = pathObj.name;
	      pathEl.onclick = _onSegmentClick.bind(me, pathObj);
	  
	      me.path.appendChild(pathEl);

	      if (pathObj.children.length) {
	        sepEl = document.createElement('span');
	        sepEl.className = 'jsoneditor-treepath-seperator';
	        sepEl.innerHTML = '&#9658;';

	        sepEl.onclick = function () {
	          var items = [];
	          pathObj.children.forEach(function (child) {
	            items.push({
	              'text': child.name,
	              'className': 'jsoneditor-type-modes' + (pathObjs[idx + 1] + 1 && pathObjs[idx + 1].name === child.name ? ' jsoneditor-selected' : ''),
	              'click': _onContextMenuItemClick.bind(me, pathObj, child.name)
	            });
	          });
	          var menu = new ContextMenu(items);
	          menu.show(sepEl);
	        };

	        me.path.appendChild(sepEl, me.container);
	      }

	      if(idx === pathObjs.length - 1) {
	        var leftRectPos = (sepEl || pathEl).getBoundingClientRect().left;
	        if(me.path.offsetWidth < leftRectPos) {
	          me.path.scrollLeft = leftRectPos;
	        }
	      }
	    });
	  }

	  function _onSegmentClick(pathObj) {
	    if (this.selectionCallback) {
	      this.selectionCallback(pathObj);
	    }
	  };

	  function _onContextMenuItemClick(pathObj, selection) {
	    if (this.contextMenuCallback) {
	      this.contextMenuCallback(pathObj, selection);
	    }
	  };
	};

	/**
	 * set a callback function for selection of path section
	 * @param {Function} callback function to invoke when section is selected
	 */
	TreePath.prototype.onSectionSelected = function (callback) {
	  if (typeof callback === 'function') {
	    this.selectionCallback = callback;      
	  }
	};

	/**
	 * set a callback function for selection of path section
	 * @param {Function} callback function to invoke when section is selected
	 */
	TreePath.prototype.onContextMenuItemSelected = function (callback) {
	  if (typeof callback === 'function') {
	    this.contextMenuCallback = callback;
	  }
	};

	module.exports = TreePath;

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var naturalSort = __webpack_require__(11);
	var ContextMenu = __webpack_require__(7);
	var appendNodeFactory = __webpack_require__(12);
	var util = __webpack_require__(4);
	var translate = __webpack_require__(8).translate;

	/**
	 * @constructor Node
	 * Create a new Node
	 * @param {./treemode} editor
	 * @param {Object} [params] Can contain parameters:
	 *                          {string}  field
	 *                          {boolean} fieldEditable
	 *                          {*}       value
	 *                          {String}  type  Can have values 'auto', 'array',
	 *                                          'object', or 'string'.
	 */
	function Node (editor, params) {
	  /** @type {./treemode} */
	  this.editor = editor;
	  this.dom = {};
	  this.expanded = false;

	  if(params && (params instanceof Object)) {
	    this.setField(params.field, params.fieldEditable);
	    this.setValue(params.value, params.type);
	  }
	  else {
	    this.setField('');
	    this.setValue(null);
	  }

	  this._debouncedOnChangeValue = util.debounce(this._onChangeValue.bind(this), Node.prototype.DEBOUNCE_INTERVAL);
	  this._debouncedOnChangeField = util.debounce(this._onChangeField.bind(this), Node.prototype.DEBOUNCE_INTERVAL);
	}

	// debounce interval for keyboard input in milliseconds
	Node.prototype.DEBOUNCE_INTERVAL = 150;

	/**
	 * Determine whether the field and/or value of this node are editable
	 * @private
	 */
	Node.prototype._updateEditability = function () {
	  this.editable = {
	    field: true,
	    value: true
	  };

	  if (this.editor) {
	    this.editable.field = this.editor.options.mode === 'tree';
	    this.editable.value = this.editor.options.mode !== 'view';

	    if ((this.editor.options.mode === 'tree' || this.editor.options.mode === 'form') &&
	        (typeof this.editor.options.onEditable === 'function')) {
	      var editable = this.editor.options.onEditable({
	        field: this.field,
	        value: this.value,
	        path: this.getPath()
	      });

	      if (typeof editable === 'boolean') {
	        this.editable.field = editable;
	        this.editable.value = editable;
	      }
	      else {
	        if (typeof editable.field === 'boolean') this.editable.field = editable.field;
	        if (typeof editable.value === 'boolean') this.editable.value = editable.value;
	      }
	    }
	  }
	};

	/**
	 * Get the path of this node
	 * @return {String[]} Array containing the path to this node
	 */
	Node.prototype.getPath = function () {
	  var node = this;
	  var path = [];
	  while (node) {
	    var field = node.getName();
	    if (field !== undefined) {
	      path.unshift(field);
	    }
	    node = node.parent;
	  }
	  return path;
	};

	/**
	 * Get node serializable name
	 * @returns {String|Number}
	 */
	Node.prototype.getName = function () {
	 return !this.parent
	 ? undefined  // do not add an (optional) field name of the root node
	 :  (this.parent.type != 'array')
	     ? this.field
	     : this.index;
	};

	/**
	 * Find child node by serializable path
	 * @param {Array<String>} path 
	 */
	Node.prototype.findNodeByPath = function (path) {
	  if (!path) {
	    return;
	  }

	  if (path.length == 0) {
	    return this;
	  }

	  if (path.length && this.childs && this.childs.length) {
	    for (var i=0; i < this.childs.length; ++i) {
	      if (('' + path[0]) === ('' + this.childs[i].getName())) {
	        return this.childs[i].findNodeByPath(path.slice(1));
	      }
	    }
	  }
	};

	/**
	 * @typedef {{value: String|Object|Number|Boolean, path: Array.<String|Number>}} SerializableNode
	 * 
	 * Returns serializable representation for the node
	 * @return {SerializedNode}
	 */
	Node.prototype.serialize = function () {
	  return {
	    value: this.getValue(),
	    path: this.getPath()
	  };
	};

	/**
	 * Find a Node from a JSON path like '.items[3].name'
	 * @param {string} jsonPath
	 * @return {Node | null} Returns the Node when found, returns null if not found
	 */
	Node.prototype.findNode = function (jsonPath) {
	  var path = util.parsePath(jsonPath);
	  var node = this;
	  while (node && path.length > 0) {
	    var prop = path.shift();
	    if (typeof prop === 'number') {
	      if (node.type !== 'array') {
	        throw new Error('Cannot get child node at index ' + prop + ': node is no array');
	      }
	      node = node.childs[prop];
	    }
	    else { // string
	      if (node.type !== 'object') {
	        throw new Error('Cannot get child node ' + prop + ': node is no object');
	      }
	      node = node.childs.filter(function (child) {
	        return child.field === prop;
	      })[0];
	    }
	  }

	  return node;
	};

	/**
	 * Find all parents of this node. The parents are ordered from root node towards
	 * the original node.
	 * @return {Array.<Node>}
	 */
	Node.prototype.findParents = function () {
	  var parents = [];
	  var parent = this.parent;
	  while (parent) {
	    parents.unshift(parent);
	    parent = parent.parent;
	  }
	  return parents;
	};

	/**
	 *
	 * @param {{dataPath: string, keyword: string, message: string, params: Object, schemaPath: string} | null} error
	 * @param {Node} [child]  When this is the error of a parent node, pointing
	 *                        to an invalid child node, the child node itself
	 *                        can be provided. If provided, clicking the error
	 *                        icon will set focus to the invalid child node.
	 */
	Node.prototype.setError = function (error, child) {
	  // ensure the dom exists
	  this.getDom();

	  this.error = error;
	  var tdError = this.dom.tdError;
	  if (error) {
	    if (!tdError) {
	      tdError = document.createElement('td');
	      this.dom.tdError = tdError;
	      this.dom.tdValue.parentNode.appendChild(tdError);
	    }

	    var popover = document.createElement('div');
	    popover.className = 'jsoneditor-popover jsoneditor-right';
	    popover.appendChild(document.createTextNode(error.message));

	    var button = document.createElement('button');
	    button.type = 'button';
	    button.className = 'jsoneditor-schema-error';
	    button.appendChild(popover);

	    // update the direction of the popover
	    button.onmouseover = button.onfocus = function updateDirection() {
	      var directions = ['right', 'above', 'below', 'left'];
	      for (var i = 0; i < directions.length; i++) {
	        var direction = directions[i];
	        popover.className = 'jsoneditor-popover jsoneditor-' + direction;

	        var contentRect = this.editor.content.getBoundingClientRect();
	        var popoverRect = popover.getBoundingClientRect();
	        var margin = 20; // account for a scroll bar
	        var fit = util.insideRect(contentRect, popoverRect, margin);

	        if (fit) {
	          break;
	        }
	      }
	    }.bind(this);

	    // when clicking the error icon, expand all nodes towards the invalid
	    // child node, and set focus to the child node
	    if (child) {
	      button.onclick = function showInvalidNode() {
	        child.findParents().forEach(function (parent) {
	          parent.expand(false);
	        });

	        child.scrollTo(function () {
	          child.focus();
	        });
	      };
	    }

	    // apply the error message to the node
	    while (tdError.firstChild) {
	      tdError.removeChild(tdError.firstChild);
	    }
	    tdError.appendChild(button);
	  }
	  else {
	    if (tdError) {
	      this.dom.tdError.parentNode.removeChild(this.dom.tdError);
	      delete this.dom.tdError;
	    }
	  }
	};

	/**
	 * Get the index of this node: the index in the list of childs where this
	 * node is part of
	 * @return {number} Returns the index, or -1 if this is the root node
	 */
	Node.prototype.getIndex = function () {
	  return this.parent ? this.parent.childs.indexOf(this) : -1;
	};

	/**
	 * Set parent node
	 * @param {Node} parent
	 */
	Node.prototype.setParent = function(parent) {
	  this.parent = parent;
	};

	/**
	 * Set field
	 * @param {String}  field
	 * @param {boolean} [fieldEditable]
	 */
	Node.prototype.setField = function(field, fieldEditable) {
	  this.field = field;
	  this.previousField = field;
	  this.fieldEditable = (fieldEditable === true);
	};

	/**
	 * Get field
	 * @return {String}
	 */
	Node.prototype.getField = function() {
	  if (this.field === undefined) {
	    this._getDomField();
	  }

	  return this.field;
	};

	/**
	 * Set value. Value is a JSON structure or an element String, Boolean, etc.
	 * @param {*} value
	 * @param {String} [type]  Specify the type of the value. Can be 'auto',
	 *                         'array', 'object', or 'string'
	 */
	Node.prototype.setValue = function(value, type) {
	  var childValue, child;

	  // first clear all current childs (if any)
	  var childs = this.childs;
	  if (childs) {
	    while (childs.length) {
	      this.removeChild(childs[0]);
	    }
	  }

	  // TODO: remove the DOM of this Node

	  this.type = this._getType(value);

	  // check if type corresponds with the provided type
	  if (type && type != this.type) {
	    if (type == 'string' && this.type == 'auto') {
	      this.type = type;
	    }
	    else {
	      throw new Error('Type mismatch: ' +
	          'cannot cast value of type "' + this.type +
	          ' to the specified type "' + type + '"');
	    }
	  }

	  if (this.type == 'array') {
	    // array
	    this.childs = [];
	    for (var i = 0, iMax = value.length; i < iMax; i++) {
	      childValue = value[i];
	      if (childValue !== undefined && !(childValue instanceof Function)) {
	        // ignore undefined and functions
	        child = new Node(this.editor, {
	          value: childValue
	        });
	        this.appendChild(child);
	      }
	    }
	    this.value = '';
	  }
	  else if (this.type == 'object') {
	    // object
	    this.childs = [];
	    for (var childField in value) {
	      if (value.hasOwnProperty(childField)) {
	        childValue = value[childField];
	        if (childValue !== undefined && !(childValue instanceof Function)) {
	          // ignore undefined and functions
	          child = new Node(this.editor, {
	            field: childField,
	            value: childValue
	          });
	          this.appendChild(child);
	        }
	      }
	    }
	    this.value = '';

	    // sort object keys
	    if (this.editor.options.sortObjectKeys === true) {
	      this.sort('asc');
	    }
	  }
	  else {
	    // value
	    this.childs = undefined;
	    this.value = value;
	  }

	  this.previousValue = this.value;
	};

	/**
	 * Get value. Value is a JSON structure
	 * @return {*} value
	 */
	Node.prototype.getValue = function() {
	  //var childs, i, iMax;

	  if (this.type == 'array') {
	    var arr = [];
	    this.childs.forEach (function (child) {
	      arr.push(child.getValue());
	    });
	    return arr;
	  }
	  else if (this.type == 'object') {
	    var obj = {};
	    this.childs.forEach (function (child) {
	      obj[child.getField()] = child.getValue();
	    });
	    return obj;
	  }
	  else {
	    if (this.value === undefined) {
	      this._getDomValue();
	    }

	    return this.value;
	  }
	};

	/**
	 * Get the nesting level of this node
	 * @return {Number} level
	 */
	Node.prototype.getLevel = function() {
	  return (this.parent ? this.parent.getLevel() + 1 : 0);
	};

	/**
	 * Get jsonpath of the current node
	 * @return {Node[]} Returns an array with nodes
	 */
	Node.prototype.getNodePath = function () {
	  var path = this.parent ? this.parent.getNodePath() : [];
	  path.push(this);
	  return path;
	};

	/**
	 * Create a clone of a node
	 * The complete state of a clone is copied, including whether it is expanded or
	 * not. The DOM elements are not cloned.
	 * @return {Node} clone
	 */
	Node.prototype.clone = function() {
	  var clone = new Node(this.editor);
	  clone.type = this.type;
	  clone.field = this.field;
	  clone.fieldInnerText = this.fieldInnerText;
	  clone.fieldEditable = this.fieldEditable;
	  clone.value = this.value;
	  clone.valueInnerText = this.valueInnerText;
	  clone.expanded = this.expanded;

	  if (this.childs) {
	    // an object or array
	    var cloneChilds = [];
	    this.childs.forEach(function (child) {
	      var childClone = child.clone();
	      childClone.setParent(clone);
	      cloneChilds.push(childClone);
	    });
	    clone.childs = cloneChilds;
	  }
	  else {
	    // a value
	    clone.childs = undefined;
	  }

	  return clone;
	};

	/**
	 * Expand this node and optionally its childs.
	 * @param {boolean} [recurse] Optional recursion, true by default. When
	 *                            true, all childs will be expanded recursively
	 */
	Node.prototype.expand = function(recurse) {
	  if (!this.childs) {
	    return;
	  }

	  // set this node expanded
	  this.expanded = true;
	  if (this.dom.expand) {
	    this.dom.expand.className = 'jsoneditor-expanded';
	  }

	  this.showChilds();

	  if (recurse !== false) {
	    this.childs.forEach(function (child) {
	      child.expand(recurse);
	    });
	  }
	};

	/**
	 * Collapse this node and optionally its childs.
	 * @param {boolean} [recurse] Optional recursion, true by default. When
	 *                            true, all childs will be collapsed recursively
	 */
	Node.prototype.collapse = function(recurse) {
	  if (!this.childs) {
	    return;
	  }

	  this.hideChilds();

	  // collapse childs in case of recurse
	  if (recurse !== false) {
	    this.childs.forEach(function (child) {
	      child.collapse(recurse);
	    });

	  }

	  // make this node collapsed
	  if (this.dom.expand) {
	    this.dom.expand.className = 'jsoneditor-collapsed';
	  }
	  this.expanded = false;
	};

	/**
	 * Recursively show all childs when they are expanded
	 */
	Node.prototype.showChilds = function() {
	  var childs = this.childs;
	  if (!childs) {
	    return;
	  }
	  if (!this.expanded) {
	    return;
	  }

	  var tr = this.dom.tr;
	  var table = tr ? tr.parentNode : undefined;
	  if (table) {
	    // show row with append button
	    var append = this.getAppend();
	    var nextTr = tr.nextSibling;
	    if (nextTr) {
	      table.insertBefore(append, nextTr);
	    }
	    else {
	      table.appendChild(append);
	    }

	    // show childs
	    this.childs.forEach(function (child) {
	      table.insertBefore(child.getDom(), append);
	      child.showChilds();
	    });
	  }
	};

	/**
	 * Hide the node with all its childs
	 */
	Node.prototype.hide = function() {
	  var tr = this.dom.tr;
	  var table = tr ? tr.parentNode : undefined;
	  if (table) {
	    table.removeChild(tr);
	  }
	  this.hideChilds();
	};


	/**
	 * Recursively hide all childs
	 */
	Node.prototype.hideChilds = function() {
	  var childs = this.childs;
	  if (!childs) {
	    return;
	  }
	  if (!this.expanded) {
	    return;
	  }

	  // hide append row
	  var append = this.getAppend();
	  if (append.parentNode) {
	    append.parentNode.removeChild(append);
	  }

	  // hide childs
	  this.childs.forEach(function (child) {
	    child.hide();
	  });
	};


	/**
	 * Goes through the path from the node to the root and ensures that it is expanded
	 */
	Node.prototype.expandTo = function() {
	  var currentNode = this.parent;
	  while (currentNode) {
	    if (!currentNode.expanded) {
	      currentNode.expand();
	    }
	    currentNode = currentNode.parent;
	  }
	};


	/**
	 * Add a new child to the node.
	 * Only applicable when Node value is of type array or object
	 * @param {Node} node
	 */
	Node.prototype.appendChild = function(node) {
	  if (this._hasChilds()) {
	    // adjust the link to the parent
	    node.setParent(this);
	    node.fieldEditable = (this.type == 'object');
	    if (this.type == 'array') {
	      node.index = this.childs.length;
	    }
	    this.childs.push(node);

	    if (this.expanded) {
	      // insert into the DOM, before the appendRow
	      var newTr = node.getDom();
	      var appendTr = this.getAppend();
	      var table = appendTr ? appendTr.parentNode : undefined;
	      if (appendTr && table) {
	        table.insertBefore(newTr, appendTr);
	      }

	      node.showChilds();
	    }

	    this.updateDom({'updateIndexes': true});
	    node.updateDom({'recurse': true});
	  }
	};


	/**
	 * Move a node from its current parent to this node
	 * Only applicable when Node value is of type array or object
	 * @param {Node} node
	 * @param {Node} beforeNode
	 */
	Node.prototype.moveBefore = function(node, beforeNode) {
	  if (this._hasChilds()) {
	    // create a temporary row, to prevent the scroll position from jumping
	    // when removing the node
	    var tbody = (this.dom.tr) ? this.dom.tr.parentNode : undefined;
	    if (tbody) {
	      var trTemp = document.createElement('tr');
	      trTemp.style.height = tbody.clientHeight + 'px';
	      tbody.appendChild(trTemp);
	    }

	    if (node.parent) {
	      node.parent.removeChild(node);
	    }

	    if (beforeNode instanceof AppendNode) {
	      this.appendChild(node);
	    }
	    else {
	      this.insertBefore(node, beforeNode);
	    }

	    if (tbody) {
	      tbody.removeChild(trTemp);
	    }
	  }
	};

	/**
	 * Move a node from its current parent to this node
	 * Only applicable when Node value is of type array or object.
	 * If index is out of range, the node will be appended to the end
	 * @param {Node} node
	 * @param {Number} index
	 */
	Node.prototype.moveTo = function (node, index) {
	  if (node.parent == this) {
	    // same parent
	    var currentIndex = this.childs.indexOf(node);
	    if (currentIndex < index) {
	      // compensate the index for removal of the node itself
	      index++;
	    }
	  }

	  var beforeNode = this.childs[index] || this.append;
	  this.moveBefore(node, beforeNode);
	};

	/**
	 * Insert a new child before a given node
	 * Only applicable when Node value is of type array or object
	 * @param {Node} node
	 * @param {Node} beforeNode
	 */
	Node.prototype.insertBefore = function(node, beforeNode) {
	  if (this._hasChilds()) {
	    if (beforeNode == this.append) {
	      // append to the child nodes

	      // adjust the link to the parent
	      node.setParent(this);
	      node.fieldEditable = (this.type == 'object');
	      this.childs.push(node);
	    }
	    else {
	      // insert before a child node
	      var index = this.childs.indexOf(beforeNode);
	      if (index == -1) {
	        throw new Error('Node not found');
	      }

	      // adjust the link to the parent
	      node.setParent(this);
	      node.fieldEditable = (this.type == 'object');
	      this.childs.splice(index, 0, node);
	    }

	    if (this.expanded) {
	      // insert into the DOM
	      var newTr = node.getDom();
	      var nextTr = beforeNode.getDom();
	      var table = nextTr ? nextTr.parentNode : undefined;
	      if (nextTr && table) {
	        table.insertBefore(newTr, nextTr);
	      }

	      node.showChilds();
	    }

	    this.updateDom({'updateIndexes': true});
	    node.updateDom({'recurse': true});
	  }
	};

	/**
	 * Insert a new child before a given node
	 * Only applicable when Node value is of type array or object
	 * @param {Node} node
	 * @param {Node} afterNode
	 */
	Node.prototype.insertAfter = function(node, afterNode) {
	  if (this._hasChilds()) {
	    var index = this.childs.indexOf(afterNode);
	    var beforeNode = this.childs[index + 1];
	    if (beforeNode) {
	      this.insertBefore(node, beforeNode);
	    }
	    else {
	      this.appendChild(node);
	    }
	  }
	};

	/**
	 * Search in this node
	 * The node will be expanded when the text is found one of its childs, else
	 * it will be collapsed. Searches are case insensitive.
	 * @param {String} text
	 * @return {Node[]} results  Array with nodes containing the search text
	 */
	Node.prototype.search = function(text) {
	  var results = [];
	  var index;
	  var search = text ? text.toLowerCase() : undefined;

	  // delete old search data
	  delete this.searchField;
	  delete this.searchValue;

	  // search in field
	  if (this.field != undefined) {
	    var field = String(this.field).toLowerCase();
	    index = field.indexOf(search);
	    if (index != -1) {
	      this.searchField = true;
	      results.push({
	        'node': this,
	        'elem': 'field'
	      });
	    }

	    // update dom
	    this._updateDomField();
	  }

	  // search in value
	  if (this._hasChilds()) {
	    // array, object

	    // search the nodes childs
	    if (this.childs) {
	      var childResults = [];
	      this.childs.forEach(function (child) {
	        childResults = childResults.concat(child.search(text));
	      });
	      results = results.concat(childResults);
	    }

	    // update dom
	    if (search != undefined) {
	      var recurse = false;
	      if (childResults.length == 0) {
	        this.collapse(recurse);
	      }
	      else {
	        this.expand(recurse);
	      }
	    }
	  }
	  else {
	    // string, auto
	    if (this.value != undefined ) {
	      var value = String(this.value).toLowerCase();
	      index = value.indexOf(search);
	      if (index != -1) {
	        this.searchValue = true;
	        results.push({
	          'node': this,
	          'elem': 'value'
	        });
	      }
	    }

	    // update dom
	    this._updateDomValue();
	  }

	  return results;
	};

	/**
	 * Move the scroll position such that this node is in the visible area.
	 * The node will not get the focus
	 * @param {function(boolean)} [callback]
	 */
	Node.prototype.scrollTo = function(callback) {
	  if (!this.dom.tr || !this.dom.tr.parentNode) {
	    // if the node is not visible, expand its parents
	    var parent = this.parent;
	    var recurse = false;
	    while (parent) {
	      parent.expand(recurse);
	      parent = parent.parent;
	    }
	  }

	  if (this.dom.tr && this.dom.tr.parentNode) {
	    this.editor.scrollTo(this.dom.tr.offsetTop, callback);
	  }
	};


	// stores the element name currently having the focus
	Node.focusElement = undefined;

	/**
	 * Set focus to this node
	 * @param {String} [elementName]  The field name of the element to get the
	 *                                focus available values: 'drag', 'menu',
	 *                                'expand', 'field', 'value' (default)
	 */
	Node.prototype.focus = function(elementName) {
	  Node.focusElement = elementName;

	  if (this.dom.tr && this.dom.tr.parentNode) {
	    var dom = this.dom;

	    switch (elementName) {
	      case 'drag':
	        if (dom.drag) {
	          dom.drag.focus();
	        }
	        else {
	          dom.menu.focus();
	        }
	        break;

	      case 'menu':
	        dom.menu.focus();
	        break;

	      case 'expand':
	        if (this._hasChilds()) {
	          dom.expand.focus();
	        }
	        else if (dom.field && this.fieldEditable) {
	          dom.field.focus();
	          util.selectContentEditable(dom.field);
	        }
	        else if (dom.value && !this._hasChilds()) {
	          dom.value.focus();
	          util.selectContentEditable(dom.value);
	        }
	        else {
	          dom.menu.focus();
	        }
	        break;

	      case 'field':
	        if (dom.field && this.fieldEditable) {
	          dom.field.focus();
	          util.selectContentEditable(dom.field);
	        }
	        else if (dom.value && !this._hasChilds()) {
	          dom.value.focus();
	          util.selectContentEditable(dom.value);
	        }
	        else if (this._hasChilds()) {
	          dom.expand.focus();
	        }
	        else {
	          dom.menu.focus();
	        }
	        break;

	      case 'value':
	      default:
	        if (dom.select) {
	          // enum select box
	          dom.select.focus();
	        }
	        else if (dom.value && !this._hasChilds()) {
	          dom.value.focus();
	          util.selectContentEditable(dom.value);
	        }
	        else if (dom.field && this.fieldEditable) {
	          dom.field.focus();
	          util.selectContentEditable(dom.field);
	        }
	        else if (this._hasChilds()) {
	          dom.expand.focus();
	        }
	        else {
	          dom.menu.focus();
	        }
	        break;
	    }
	  }
	};

	/**
	 * Select all text in an editable div after a delay of 0 ms
	 * @param {Element} editableDiv
	 */
	Node.select = function(editableDiv) {
	  setTimeout(function () {
	    util.selectContentEditable(editableDiv);
	  }, 0);
	};

	/**
	 * Update the values from the DOM field and value of this node
	 */
	Node.prototype.blur = function() {
	  // retrieve the actual field and value from the DOM.
	  this._getDomValue(false);
	  this._getDomField(false);
	};

	/**
	 * Check if given node is a child. The method will check recursively to find
	 * this node.
	 * @param {Node} node
	 * @return {boolean} containsNode
	 */
	Node.prototype.containsNode = function(node) {
	  if (this == node) {
	    return true;
	  }

	  var childs = this.childs;
	  if (childs) {
	    // TODO: use the js5 Array.some() here?
	    for (var i = 0, iMax = childs.length; i < iMax; i++) {
	      if (childs[i].containsNode(node)) {
	        return true;
	      }
	    }
	  }

	  return false;
	};

	/**
	 * Move given node into this node
	 * @param {Node} node           the childNode to be moved
	 * @param {Node} beforeNode     node will be inserted before given
	 *                                         node. If no beforeNode is given,
	 *                                         the node is appended at the end
	 * @private
	 */
	Node.prototype._move = function(node, beforeNode) {
	  if (node == beforeNode) {
	    // nothing to do...
	    return;
	  }

	  // check if this node is not a child of the node to be moved here
	  if (node.containsNode(this)) {
	    throw new Error(translate('validationCannotMove'));
	  }

	  // remove the original node
	  if (node.parent) {
	    node.parent.removeChild(node);
	  }

	  // create a clone of the node
	  var clone = node.clone();
	  node.clearDom();

	  // insert or append the node
	  if (beforeNode) {
	    this.insertBefore(clone, beforeNode);
	  }
	  else {
	    this.appendChild(clone);
	  }

	  /* TODO: adjust the field name (to prevent equal field names)
	   if (this.type == 'object') {
	   }
	   */
	};

	/**
	 * Remove a child from the node.
	 * Only applicable when Node value is of type array or object
	 * @param {Node} node   The child node to be removed;
	 * @return {Node | undefined} node  The removed node on success,
	 *                                             else undefined
	 */
	Node.prototype.removeChild = function(node) {
	  if (this.childs) {
	    var index = this.childs.indexOf(node);

	    if (index != -1) {
	      node.hide();

	      // delete old search results
	      delete node.searchField;
	      delete node.searchValue;

	      var removedNode = this.childs.splice(index, 1)[0];
	      removedNode.parent = null;

	      this.updateDom({'updateIndexes': true});

	      return removedNode;
	    }
	  }

	  return undefined;
	};

	/**
	 * Remove a child node node from this node
	 * This method is equal to Node.removeChild, except that _remove fire an
	 * onChange event.
	 * @param {Node} node
	 * @private
	 */
	Node.prototype._remove = function (node) {
	  this.removeChild(node);
	};

	/**
	 * Change the type of the value of this Node
	 * @param {String} newType
	 */
	Node.prototype.changeType = function (newType) {
	  var oldType = this.type;

	  if (oldType == newType) {
	    // type is not changed
	    return;
	  }

	  if ((newType == 'string' || newType == 'auto') &&
	      (oldType == 'string' || oldType == 'auto')) {
	    // this is an easy change
	    this.type = newType;
	  }
	  else {
	    // change from array to object, or from string/auto to object/array
	    var table = this.dom.tr ? this.dom.tr.parentNode : undefined;
	    var lastTr;
	    if (this.expanded) {
	      lastTr = this.getAppend();
	    }
	    else {
	      lastTr = this.getDom();
	    }
	    var nextTr = (lastTr && lastTr.parentNode) ? lastTr.nextSibling : undefined;

	    // hide current field and all its childs
	    this.hide();
	    this.clearDom();

	    // adjust the field and the value
	    this.type = newType;

	    // adjust childs
	    if (newType == 'object') {
	      if (!this.childs) {
	        this.childs = [];
	      }

	      this.childs.forEach(function (child, index) {
	        child.clearDom();
	        delete child.index;
	        child.fieldEditable = true;
	        if (child.field == undefined) {
	          child.field = '';
	        }
	      });

	      if (oldType == 'string' || oldType == 'auto') {
	        this.expanded = true;
	      }
	    }
	    else if (newType == 'array') {
	      if (!this.childs) {
	        this.childs = [];
	      }

	      this.childs.forEach(function (child, index) {
	        child.clearDom();
	        child.fieldEditable = false;
	        child.index = index;
	      });

	      if (oldType == 'string' || oldType == 'auto') {
	        this.expanded = true;
	      }
	    }
	    else {
	      this.expanded = false;
	    }

	    // create new DOM
	    if (table) {
	      if (nextTr) {
	        table.insertBefore(this.getDom(), nextTr);
	      }
	      else {
	        table.appendChild(this.getDom());
	      }
	    }
	    this.showChilds();
	  }

	  if (newType == 'auto' || newType == 'string') {
	    // cast value to the correct type
	    if (newType == 'string') {
	      this.value = String(this.value);
	    }
	    else {
	      this.value = this._stringCast(String(this.value));
	    }

	    this.focus();
	  }

	  this.updateDom({'updateIndexes': true});
	};

	/**
	 * Retrieve value from DOM
	 * @param {boolean} [silent]  If true (default), no errors will be thrown in
	 *                            case of invalid data
	 * @private
	 */
	Node.prototype._getDomValue = function(silent) {
	  if (this.dom.value && this.type != 'array' && this.type != 'object') {
	    this.valueInnerText = util.getInnerText(this.dom.value);
	  }

	  if (this.valueInnerText != undefined) {
	    try {
	      // retrieve the value
	      var value;
	      if (this.type == 'string') {
	        value = this._unescapeHTML(this.valueInnerText);
	      }
	      else {
	        var str = this._unescapeHTML(this.valueInnerText);
	        value = this._stringCast(str);
	      }
	      if (value !== this.value) {
	        this.value = value;
	        this._debouncedOnChangeValue();
	      }
	    }
	    catch (err) {
	      this.value = undefined;
	      // TODO: sent an action with the new, invalid value?
	      if (silent !== true) {
	        throw err;
	      }
	    }
	  }
	};

	/**
	 * Handle a changed value
	 * @private
	 */
	Node.prototype._onChangeValue = function () {
	  // get current selection, then override the range such that we can select
	  // the added/removed text on undo/redo
	  var oldSelection = this.editor.getDomSelection();
	  if (oldSelection.range) {
	    var undoDiff = util.textDiff(String(this.value), String(this.previousValue));
	    oldSelection.range.startOffset = undoDiff.start;
	    oldSelection.range.endOffset = undoDiff.end;
	  }
	  var newSelection = this.editor.getDomSelection();
	  if (newSelection.range) {
	    var redoDiff = util.textDiff(String(this.previousValue), String(this.value));
	    newSelection.range.startOffset = redoDiff.start;
	    newSelection.range.endOffset = redoDiff.end;
	  }

	  this.editor._onAction('editValue', {
	    node: this,
	    oldValue: this.previousValue,
	    newValue: this.value,
	    oldSelection: oldSelection,
	    newSelection: newSelection
	  });

	  this.previousValue = this.value;
	};

	/**
	 * Handle a changed field
	 * @private
	 */
	Node.prototype._onChangeField = function () {
	  // get current selection, then override the range such that we can select
	  // the added/removed text on undo/redo
	  var oldSelection = this.editor.getDomSelection();
	  var previous = this.previousField || '';
	  if (oldSelection.range) {
	    var undoDiff = util.textDiff(this.field, previous);
	    oldSelection.range.startOffset = undoDiff.start;
	    oldSelection.range.endOffset = undoDiff.end;
	  }
	  var newSelection = this.editor.getDomSelection();
	  if (newSelection.range) {
	    var redoDiff = util.textDiff(previous, this.field);
	    newSelection.range.startOffset = redoDiff.start;
	    newSelection.range.endOffset = redoDiff.end;
	  }

	  this.editor._onAction('editField', {
	    node: this,
	    oldValue: this.previousField,
	    newValue: this.field,
	    oldSelection: oldSelection,
	    newSelection: newSelection
	  });

	  this.previousField = this.field;
	};

	/**
	 * Update dom value:
	 * - the text color of the value, depending on the type of the value
	 * - the height of the field, depending on the width
	 * - background color in case it is empty
	 * @private
	 */
	Node.prototype._updateDomValue = function () {
	  var domValue = this.dom.value;
	  if (domValue) {
	    var classNames = ['jsoneditor-value'];


	    // set text color depending on value type
	    var value = this.value;
	    var type = (this.type == 'auto') ? util.type(value) : this.type;
	    var isUrl = type == 'string' && util.isUrl(value);
	    classNames.push('jsoneditor-' + type);
	    if (isUrl) {
	      classNames.push('jsoneditor-url');
	    }

	    // visual styling when empty
	    var isEmpty = (String(this.value) == '' && this.type != 'array' && this.type != 'object');
	    if (isEmpty) {
	      classNames.push('jsoneditor-empty');
	    }

	    // highlight when there is a search result
	    if (this.searchValueActive) {
	      classNames.push('jsoneditor-highlight-active');
	    }
	    if (this.searchValue) {
	      classNames.push('jsoneditor-highlight');
	    }

	    domValue.className = classNames.join(' ');

	    // update title
	    if (type == 'array' || type == 'object') {
	      var count = this.childs ? this.childs.length : 0;
	      domValue.title = this.type + ' containing ' + count + ' items';
	    }
	    else if (isUrl && this.editable.value) {
	      domValue.title = translate('openUrl');
	    }
	    else {
	      domValue.title = '';
	    }

	    // show checkbox when the value is a boolean
	    if (type === 'boolean' && this.editable.value) {
	      if (!this.dom.checkbox) {
	        this.dom.checkbox = document.createElement('input');
	        this.dom.checkbox.type = 'checkbox';
	        this.dom.tdCheckbox = document.createElement('td');
	        this.dom.tdCheckbox.className = 'jsoneditor-tree';
	        this.dom.tdCheckbox.appendChild(this.dom.checkbox);

	        this.dom.tdValue.parentNode.insertBefore(this.dom.tdCheckbox, this.dom.tdValue);
	      }

	      this.dom.checkbox.checked = this.value;
	    }
	    else {
	      // cleanup checkbox when displayed
	      if (this.dom.tdCheckbox) {
	        this.dom.tdCheckbox.parentNode.removeChild(this.dom.tdCheckbox);
	        delete this.dom.tdCheckbox;
	        delete this.dom.checkbox;
	      }
	    }

	    if (this.enum && this.editable.value) {
	      // create select box when this node has an enum object
	      if (!this.dom.select) {
	        this.dom.select = document.createElement('select');
	        this.id = this.field + "_" + new Date().getUTCMilliseconds();
	        this.dom.select.id = this.id;
	        this.dom.select.name = this.dom.select.id;

	        //Create the default empty option
	        this.dom.select.option = document.createElement('option');
	        this.dom.select.option.value = '';
	        this.dom.select.option.innerHTML = '--';
	        this.dom.select.appendChild(this.dom.select.option);

	        //Iterate all enum values and add them as options
	        for(var i = 0; i < this.enum.length; i++) {
	          this.dom.select.option = document.createElement('option');
	          this.dom.select.option.value = this.enum[i];
	          this.dom.select.option.innerHTML = this.enum[i];
	          if(this.dom.select.option.value == this.value){
	            this.dom.select.option.selected = true;
	          }
	          this.dom.select.appendChild(this.dom.select.option);
	        }

	        this.dom.tdSelect = document.createElement('td');
	        this.dom.tdSelect.className = 'jsoneditor-tree';
	        this.dom.tdSelect.appendChild(this.dom.select);
	        this.dom.tdValue.parentNode.insertBefore(this.dom.tdSelect, this.dom.tdValue);
	      }

	      // If the enum is inside a composite type display
	      // both the simple input and the dropdown field
	      if(this.schema && (
	          !this.schema.hasOwnProperty("oneOf") &&
	          !this.schema.hasOwnProperty("anyOf") &&
	          !this.schema.hasOwnProperty("allOf"))
	      ) {
	        this.valueFieldHTML = this.dom.tdValue.innerHTML;
	        this.dom.tdValue.style.visibility = 'hidden';
	        this.dom.tdValue.innerHTML = '';
	      } else {
	        delete this.valueFieldHTML;
	      }
	    }
	    else {
	      // cleanup select box when displayed
	      if (this.dom.tdSelect) {
	        this.dom.tdSelect.parentNode.removeChild(this.dom.tdSelect);
	        delete this.dom.tdSelect;
	        delete this.dom.select;
	        this.dom.tdValue.innerHTML = this.valueFieldHTML;
	        this.dom.tdValue.style.visibility = '';
	        delete this.valueFieldHTML;
	      }
	    }

	    // strip formatting from the contents of the editable div
	    util.stripFormatting(domValue);
	  }
	};

	/**
	 * Update dom field:
	 * - the text color of the field, depending on the text
	 * - the height of the field, depending on the width
	 * - background color in case it is empty
	 * @private
	 */
	Node.prototype._updateDomField = function () {
	  var domField = this.dom.field;
	  if (domField) {
	    // make backgound color lightgray when empty
	    var isEmpty = (String(this.field) == '' && this.parent.type != 'array');
	    if (isEmpty) {
	      util.addClassName(domField, 'jsoneditor-empty');
	    }
	    else {
	      util.removeClassName(domField, 'jsoneditor-empty');
	    }

	    // highlight when there is a search result
	    if (this.searchFieldActive) {
	      util.addClassName(domField, 'jsoneditor-highlight-active');
	    }
	    else {
	      util.removeClassName(domField, 'jsoneditor-highlight-active');
	    }
	    if (this.searchField) {
	      util.addClassName(domField, 'jsoneditor-highlight');
	    }
	    else {
	      util.removeClassName(domField, 'jsoneditor-highlight');
	    }

	    // strip formatting from the contents of the editable div
	    util.stripFormatting(domField);
	  }
	};

	/**
	 * Retrieve field from DOM
	 * @param {boolean} [silent]  If true (default), no errors will be thrown in
	 *                            case of invalid data
	 * @private
	 */
	Node.prototype._getDomField = function(silent) {
	  if (this.dom.field && this.fieldEditable) {
	    this.fieldInnerText = util.getInnerText(this.dom.field);
	  }

	  if (this.fieldInnerText != undefined) {
	    try {
	      var field = this._unescapeHTML(this.fieldInnerText);

	      if (field !== this.field) {
	        this.field = field;
	        this._debouncedOnChangeField();
	      }
	    }
	    catch (err) {
	      this.field = undefined;
	      // TODO: sent an action here, with the new, invalid value?
	      if (silent !== true) {
	        throw err;
	      }
	    }
	  }
	};

	/**
	 * Validate this node and all it's childs
	 * @return {Array.<{node: Node, error: {message: string}}>} Returns a list with duplicates
	 */
	Node.prototype.validate = function () {
	  var errors = [];

	  // find duplicate keys
	  if (this.type === 'object') {
	    var keys = {};
	    var duplicateKeys = [];
	    for (var i = 0; i < this.childs.length; i++) {
	      var child = this.childs[i];
	      if (keys.hasOwnProperty(child.field)) {
	        duplicateKeys.push(child.field);
	      }
	      keys[child.field] = true;
	    }

	    if (duplicateKeys.length > 0) {
	      errors = this.childs
	          .filter(function (node) {
	            return duplicateKeys.indexOf(node.field) !== -1;
	          })
	          .map(function (node) {
	            return {
	              node: node,
	              error: {
	                message: translate('duplicateKey') + ' "' + node.field + '"'
	              }
	            }
	          });
	    }
	  }

	  // recurse over the childs
	  if (this.childs) {
	    for (var i = 0; i < this.childs.length; i++) {
	      var e = this.childs[i].validate();
	      if (e.length > 0) {
	        errors = errors.concat(e);
	      }
	    }
	  }

	  return errors;
	};

	/**
	 * Clear the dom of the node
	 */
	Node.prototype.clearDom = function() {
	  // TODO: hide the node first?
	  //this.hide();
	  // TODO: recursively clear dom?

	  this.dom = {};
	};

	/**
	 * Get the HTML DOM TR element of the node.
	 * The dom will be generated when not yet created
	 * @return {Element} tr    HTML DOM TR Element
	 */
	Node.prototype.getDom = function() {
	  var dom = this.dom;
	  if (dom.tr) {
	    return dom.tr;
	  }

	  this._updateEditability();

	  // create row
	  dom.tr = document.createElement('tr');
	  dom.tr.node = this;

	  if (this.editor.options.mode === 'tree') { // note: we take here the global setting
	    var tdDrag = document.createElement('td');
	    if (this.editable.field) {
	      // create draggable area
	      if (this.parent) {
	        var domDrag = document.createElement('button');
	        domDrag.type = 'button';
	        dom.drag = domDrag;
	        domDrag.className = 'jsoneditor-dragarea';
	        domDrag.title = translate('drag');
	        tdDrag.appendChild(domDrag);
	      }
	    }
	    dom.tr.appendChild(tdDrag);

	    // create context menu
	    var tdMenu = document.createElement('td');
	    var menu = document.createElement('button');
	    menu.type = 'button';
	    dom.menu = menu;
	    menu.className = 'jsoneditor-contextmenu';
	    menu.title = translate('actionsMenu');
	    tdMenu.appendChild(dom.menu);
	    dom.tr.appendChild(tdMenu);
	  }

	  // create tree and field
	  var tdField = document.createElement('td');
	  dom.tr.appendChild(tdField);
	  dom.tree = this._createDomTree();
	  tdField.appendChild(dom.tree);

	  this.updateDom({'updateIndexes': true});

	  return dom.tr;
	};

	/**
	 * DragStart event, fired on mousedown on the dragarea at the left side of a Node
	 * @param {Node[] | Node} nodes
	 * @param {Event} event
	 */
	Node.onDragStart = function (nodes, event) {
	  if (!Array.isArray(nodes)) {
	    return Node.onDragStart([nodes], event);
	  }
	  if (nodes.length === 0) {
	    return;
	  }

	  var firstNode = nodes[0];
	  var lastNode = nodes[nodes.length - 1];
	  var draggedNode = Node.getNodeFromTarget(event.target);
	  var beforeNode = lastNode.nextSibling();
	  var editor = firstNode.editor;

	  // in case of multiple selected nodes, offsetY prevents the selection from
	  // jumping when you start dragging one of the lower down nodes in the selection
	  var offsetY = util.getAbsoluteTop(draggedNode.dom.tr) - util.getAbsoluteTop(firstNode.dom.tr);

	  if (!editor.mousemove) {
	    editor.mousemove = util.addEventListener(window, 'mousemove', function (event) {
	      Node.onDrag(nodes, event);
	    });
	  }

	  if (!editor.mouseup) {
	    editor.mouseup = util.addEventListener(window, 'mouseup',function (event ) {
	      Node.onDragEnd(nodes, event);
	    });
	  }

	  editor.highlighter.lock();
	  editor.drag = {
	    oldCursor: document.body.style.cursor,
	    oldSelection: editor.getDomSelection(),
	    oldBeforeNode: beforeNode,
	    mouseX: event.pageX,
	    offsetY: offsetY,
	    level: firstNode.getLevel()
	  };
	  document.body.style.cursor = 'move';

	  event.preventDefault();
	};

	/**
	 * Drag event, fired when moving the mouse while dragging a Node
	 * @param {Node[] | Node} nodes
	 * @param {Event} event
	 */
	Node.onDrag = function (nodes, event) {
	  if (!Array.isArray(nodes)) {
	    return Node.onDrag([nodes], event);
	  }
	  if (nodes.length === 0) {
	    return;
	  }

	  // TODO: this method has grown too large. Split it in a number of methods
	  var editor = nodes[0].editor;
	  var mouseY = event.pageY - editor.drag.offsetY;
	  var mouseX = event.pageX;
	  var trThis, trPrev, trNext, trFirst, trLast, trRoot;
	  var nodePrev, nodeNext;
	  var topThis, topPrev, topFirst, heightThis, bottomNext, heightNext;
	  var moved = false;

	  // TODO: add an ESC option, which resets to the original position

	  // move up/down
	  var firstNode = nodes[0];
	  trThis = firstNode.dom.tr;
	  topThis = util.getAbsoluteTop(trThis);
	  heightThis = trThis.offsetHeight;
	  if (mouseY < topThis) {
	    // move up
	    trPrev = trThis;
	    do {
	      trPrev = trPrev.previousSibling;
	      nodePrev = Node.getNodeFromTarget(trPrev);
	      topPrev = trPrev ? util.getAbsoluteTop(trPrev) : 0;
	    }
	    while (trPrev && mouseY < topPrev);

	    if (nodePrev && !nodePrev.parent) {
	      nodePrev = undefined;
	    }

	    if (!nodePrev) {
	      // move to the first node
	      trRoot = trThis.parentNode.firstChild;
	      trPrev = trRoot ? trRoot.nextSibling : undefined;
	      nodePrev = Node.getNodeFromTarget(trPrev);
	      if (nodePrev == firstNode) {
	        nodePrev = undefined;
	      }
	    }

	    if (nodePrev) {
	      // check if mouseY is really inside the found node
	      trPrev = nodePrev.dom.tr;
	      topPrev = trPrev ? util.getAbsoluteTop(trPrev) : 0;
	      if (mouseY > topPrev + heightThis) {
	        nodePrev = undefined;
	      }
	    }

	    if (nodePrev) {
	      nodes.forEach(function (node) {
	        nodePrev.parent.moveBefore(node, nodePrev);
	      });
	      moved = true;
	    }
	  }
	  else {
	    // move down
	    var lastNode = nodes[nodes.length - 1];
	    trLast = (lastNode.expanded && lastNode.append) ? lastNode.append.getDom() : lastNode.dom.tr;
	    trFirst = trLast ? trLast.nextSibling : undefined;
	    if (trFirst) {
	      topFirst = util.getAbsoluteTop(trFirst);
	      trNext = trFirst;
	      do {
	        nodeNext = Node.getNodeFromTarget(trNext);
	        if (trNext) {
	          bottomNext = trNext.nextSibling ?
	              util.getAbsoluteTop(trNext.nextSibling) : 0;
	          heightNext = trNext ? (bottomNext - topFirst) : 0;

	          if (nodeNext.parent.childs.length == nodes.length &&
	              nodeNext.parent.childs[nodes.length - 1] == lastNode) {
	            // We are about to remove the last child of this parent,
	            // which will make the parents appendNode visible.
	            topThis += 27;
	            // TODO: dangerous to suppose the height of the appendNode a constant of 27 px.
	          }
	        }

	        trNext = trNext.nextSibling;
	      }
	      while (trNext && mouseY > topThis + heightNext);

	      if (nodeNext && nodeNext.parent) {
	        // calculate the desired level
	        var diffX = (mouseX - editor.drag.mouseX);
	        var diffLevel = Math.round(diffX / 24 / 2);
	        var level = editor.drag.level + diffLevel; // desired level
	        var levelNext = nodeNext.getLevel();     // level to be

	        // find the best fitting level (move upwards over the append nodes)
	        trPrev = nodeNext.dom.tr.previousSibling;
	        while (levelNext < level && trPrev) {
	          nodePrev = Node.getNodeFromTarget(trPrev);

	          var isDraggedNode = nodes.some(function (node) {
	            return node === nodePrev || nodePrev.isDescendantOf(node);
	          });

	          if (isDraggedNode) {
	            // neglect the dragged nodes themselves and their childs
	          }
	          else if (nodePrev instanceof AppendNode) {
	            var childs = nodePrev.parent.childs;
	            if (childs.length != nodes.length || childs[nodes.length - 1] != lastNode) {
	              // non-visible append node of a list of childs
	              // consisting of not only this node (else the
	              // append node will change into a visible "empty"
	              // text when removing this node).
	              nodeNext = Node.getNodeFromTarget(trPrev);
	              levelNext = nodeNext.getLevel();
	            }
	            else {
	              break;
	            }
	          }
	          else {
	            break;
	          }

	          trPrev = trPrev.previousSibling;
	        }

	        // move the node when its position is changed
	        if (trLast.nextSibling != nodeNext.dom.tr) {
	          nodes.forEach(function (node) {
	            nodeNext.parent.moveBefore(node, nodeNext);
	          });
	          moved = true;
	        }
	      }
	    }
	  }

	  if (moved) {
	    // update the dragging parameters when moved
	    editor.drag.mouseX = mouseX;
	    editor.drag.level = firstNode.getLevel();
	  }

	  // auto scroll when hovering around the top of the editor
	  editor.startAutoScroll(mouseY);

	  event.preventDefault();
	};

	/**
	 * Drag event, fired on mouseup after having dragged a node
	 * @param {Node[] | Node} nodes
	 * @param {Event} event
	 */
	Node.onDragEnd = function (nodes, event) {
	  if (!Array.isArray(nodes)) {
	    return Node.onDrag([nodes], event);
	  }
	  if (nodes.length === 0) {
	    return;
	  }

	  var firstNode = nodes[0];
	  var editor = firstNode.editor;
	  var parent = firstNode.parent;
	  var firstIndex = parent.childs.indexOf(firstNode);
	  var beforeNode = parent.childs[firstIndex + nodes.length] || parent.append;

	  // set focus to the context menu button of the first node
	  if (nodes[0]) {
	    nodes[0].dom.menu.focus();
	  }

	  var params = {
	    nodes: nodes,
	    oldSelection: editor.drag.oldSelection,
	    newSelection: editor.getDomSelection(),
	    oldBeforeNode: editor.drag.oldBeforeNode,
	    newBeforeNode: beforeNode
	  };

	  if (params.oldBeforeNode != params.newBeforeNode) {
	    // only register this action if the node is actually moved to another place
	    editor._onAction('moveNodes', params);
	  }

	  document.body.style.cursor = editor.drag.oldCursor;
	  editor.highlighter.unlock();
	  nodes.forEach(function (node) {
	    if (event.target !== node.dom.drag && event.target !== node.dom.menu) {
	      editor.highlighter.unhighlight();
	    }
	  });
	  delete editor.drag;

	  if (editor.mousemove) {
	    util.removeEventListener(window, 'mousemove', editor.mousemove);
	    delete editor.mousemove;
	  }
	  if (editor.mouseup) {
	    util.removeEventListener(window, 'mouseup', editor.mouseup);
	    delete editor.mouseup;
	  }

	  // Stop any running auto scroll
	  editor.stopAutoScroll();

	  event.preventDefault();
	};

	/**
	 * Test if this node is a sescendant of an other node
	 * @param {Node} node
	 * @return {boolean} isDescendant
	 * @private
	 */
	Node.prototype.isDescendantOf = function (node) {
	  var n = this.parent;
	  while (n) {
	    if (n == node) {
	      return true;
	    }
	    n = n.parent;
	  }

	  return false;
	};

	/**
	 * Create an editable field
	 * @return {Element} domField
	 * @private
	 */
	Node.prototype._createDomField = function () {
	  return document.createElement('div');
	};

	/**
	 * Set highlighting for this node and all its childs.
	 * Only applied to the currently visible (expanded childs)
	 * @param {boolean} highlight
	 */
	Node.prototype.setHighlight = function (highlight) {
	  if (this.dom.tr) {
	    if (highlight) {
	      util.addClassName(this.dom.tr, 'jsoneditor-highlight');
	    }
	    else {
	      util.removeClassName(this.dom.tr, 'jsoneditor-highlight');
	    }

	    if (this.append) {
	      this.append.setHighlight(highlight);
	    }

	    if (this.childs) {
	      this.childs.forEach(function (child) {
	        child.setHighlight(highlight);
	      });
	    }
	  }
	};

	/**
	 * Select or deselect a node
	 * @param {boolean} selected
	 * @param {boolean} [isFirst]
	 */
	Node.prototype.setSelected = function (selected, isFirst) {
	  this.selected = selected;

	  if (this.dom.tr) {
	    if (selected) {
	      util.addClassName(this.dom.tr, 'jsoneditor-selected');
	    }
	    else {
	      util.removeClassName(this.dom.tr, 'jsoneditor-selected');
	    }

	    if (isFirst) {
	      util.addClassName(this.dom.tr, 'jsoneditor-first');
	    }
	    else {
	      util.removeClassName(this.dom.tr, 'jsoneditor-first');
	    }

	    if (this.append) {
	      this.append.setSelected(selected);
	    }

	    if (this.childs) {
	      this.childs.forEach(function (child) {
	        child.setSelected(selected);
	      });
	    }
	  }
	};

	/**
	 * Update the value of the node. Only primitive types are allowed, no Object
	 * or Array is allowed.
	 * @param {String | Number | Boolean | null} value
	 */
	Node.prototype.updateValue = function (value) {
	  this.value = value;
	  this.updateDom();
	};

	/**
	 * Update the field of the node.
	 * @param {String} field
	 */
	Node.prototype.updateField = function (field) {
	  this.field = field;
	  this.updateDom();
	};

	/**
	 * Update the HTML DOM, optionally recursing through the childs
	 * @param {Object} [options] Available parameters:
	 *                          {boolean} [recurse]         If true, the
	 *                          DOM of the childs will be updated recursively.
	 *                          False by default.
	 *                          {boolean} [updateIndexes]   If true, the childs
	 *                          indexes of the node will be updated too. False by
	 *                          default.
	 */
	Node.prototype.updateDom = function (options) {
	  // update level indentation
	  var domTree = this.dom.tree;
	  if (domTree) {
	    domTree.style.marginLeft = this.getLevel() * 24 + 'px';
	  }

	  // apply field to DOM
	  var domField = this.dom.field;
	  if (domField) {
	    if (this.fieldEditable) {
	      // parent is an object
	      domField.contentEditable = this.editable.field;
	      domField.spellcheck = false;
	      domField.className = 'jsoneditor-field';
	    }
	    else {
	      // parent is an array this is the root node
	      domField.className = 'jsoneditor-readonly';
	    }

	    var fieldText;
	    if (this.index != undefined) {
	      fieldText = this.index;
	    }
	    else if (this.field != undefined) {
	      fieldText = this.field;
	    }
	    else if (this._hasChilds()) {
	      fieldText = this.type;
	    }
	    else {
	      fieldText = '';
	    }
	    domField.innerHTML = this._escapeHTML(fieldText);

	    this._updateSchema();
	  }

	  // apply value to DOM
	  var domValue = this.dom.value;
	  if (domValue) {
	    var count = this.childs ? this.childs.length : 0;
	    if (this.type == 'array') {
	      domValue.innerHTML = '[' + count + ']';
	      util.addClassName(this.dom.tr, 'jsoneditor-expandable');
	    }
	    else if (this.type == 'object') {
	      domValue.innerHTML = '{' + count + '}';
	      util.addClassName(this.dom.tr, 'jsoneditor-expandable');
	    }
	    else {
	      domValue.innerHTML = this._escapeHTML(this.value);
	      util.removeClassName(this.dom.tr, 'jsoneditor-expandable');
	    }
	  }

	  // update field and value
	  this._updateDomField();
	  this._updateDomValue();

	  // update childs indexes
	  if (options && options.updateIndexes === true) {
	    // updateIndexes is true or undefined
	    this._updateDomIndexes();
	  }

	  if (options && options.recurse === true) {
	    // recurse is true or undefined. update childs recursively
	    if (this.childs) {
	      this.childs.forEach(function (child) {
	        child.updateDom(options);
	      });
	    }
	  }

	  // update row with append button
	  if (this.append) {
	    this.append.updateDom();
	  }
	};

	/**
	 * Locate the JSON schema of the node and check for any enum type
	 * @private
	 */
	Node.prototype._updateSchema = function () {
	  //Locating the schema of the node and checking for any enum type
	  if(this.editor && this.editor.options) {
	    // find the part of the json schema matching this nodes path
	    this.schema = this.editor.options.schema 
	        ? Node._findSchema(this.editor.options.schema, this.getPath())
	        : null;
	    if (this.schema) {
	      this.enum = Node._findEnum(this.schema);
	    }
	    else {
	      delete this.enum;
	    }
	  }
	};

	/**
	 * find an enum definition in a JSON schema, as property `enum` or inside
	 * one of the schemas composites (`oneOf`, `anyOf`, `allOf`)
	 * @param  {Object} schema
	 * @return {Array | null} Returns the enum when found, null otherwise.
	 * @private
	 */
	Node._findEnum = function (schema) {
	  if (schema.enum) {
	    return schema.enum;
	  }

	  var composite = schema.oneOf || schema.anyOf || schema.allOf;
	  if (composite) {
	    var match = composite.filter(function (entry) {return entry.enum});
	    if (match.length > 0) {
	      return match[0].enum;
	    }
	  }

	  return null
	};

	/**
	 * Return the part of a JSON schema matching given path.
	 * @param {Object} schema
	 * @param {Array.<string | number>} path
	 * @return {Object | null}
	 * @private
	 */
	Node._findSchema = function (schema, path) {
	  var childSchema = schema;
	  var foundSchema = childSchema;

	  var allSchemas = schema.oneOf || schema.anyOf || schema.allOf;
	  if (!allSchemas) {
	    allSchemas = [schema];
	  }

	  for (var j = 0; j < allSchemas.length; j++) {
	    childSchema = allSchemas[j];

	    for (var i = 0; i < path.length && childSchema; i++) {
	      var key = path[i];

	      if (typeof key === 'string' && childSchema.patternProperties && i == path.length - 1) {
	        for (var prop in childSchema.patternProperties) {
	          foundSchema = Node._findSchema(childSchema.patternProperties[prop], path.slice(i, path.length));
	        }
	      }
	      else if (childSchema.items && childSchema.items.properties) {
	        childSchema = childSchema.items.properties[key];
	        if (childSchema) {
	          foundSchema = Node._findSchema(childSchema, path.slice(i, path.length));
	        }
	      }
	      else if (typeof key === 'string' && childSchema.properties) {
	        childSchema = childSchema.properties[key] || null;
	        if (childSchema) {
	          foundSchema = Node._findSchema(childSchema, path.slice(i, path.length));
	        }
	      }
	      else if (typeof key === 'number' && childSchema.items) {
	        childSchema = childSchema.items;
	        if (childSchema) {
	          foundSchema = Node._findSchema(childSchema, path.slice(i, path.length));
	        }
	      }
	    }

	  }
	  return foundSchema
	};

	/**
	 * Update the DOM of the childs of a node: update indexes and undefined field
	 * names.
	 * Only applicable when structure is an array or object
	 * @private
	 */
	Node.prototype._updateDomIndexes = function () {
	  var domValue = this.dom.value;
	  var childs = this.childs;
	  if (domValue && childs) {
	    if (this.type == 'array') {
	      childs.forEach(function (child, index) {
	        child.index = index;
	        var childField = child.dom.field;
	        if (childField) {
	          childField.innerHTML = index;
	        }
	      });
	    }
	    else if (this.type == 'object') {
	      childs.forEach(function (child) {
	        if (child.index != undefined) {
	          delete child.index;

	          if (child.field == undefined) {
	            child.field = '';
	          }
	        }
	      });
	    }
	  }
	};

	/**
	 * Create an editable value
	 * @private
	 */
	Node.prototype._createDomValue = function () {
	  var domValue;

	  if (this.type == 'array') {
	    domValue = document.createElement('div');
	    domValue.innerHTML = '[...]';
	  }
	  else if (this.type == 'object') {
	    domValue = document.createElement('div');
	    domValue.innerHTML = '{...}';
	  }
	  else {
	    if (!this.editable.value && util.isUrl(this.value)) {
	      // create a link in case of read-only editor and value containing an url
	      domValue = document.createElement('a');
	      domValue.href = this.value;
	      domValue.innerHTML = this._escapeHTML(this.value);
	    }
	    else {
	      // create an editable or read-only div
	      domValue = document.createElement('div');
	      domValue.contentEditable = this.editable.value;
	      domValue.spellcheck = false;
	      domValue.innerHTML = this._escapeHTML(this.value);
	    }
	  }

	  return domValue;
	};

	/**
	 * Create an expand/collapse button
	 * @return {Element} expand
	 * @private
	 */
	Node.prototype._createDomExpandButton = function () {
	  // create expand button
	  var expand = document.createElement('button');
	  expand.type = 'button';
	  if (this._hasChilds()) {
	    expand.className = this.expanded ? 'jsoneditor-expanded' : 'jsoneditor-collapsed';
	    expand.title = translate('expandTitle');
	  }
	  else {
	    expand.className = 'jsoneditor-invisible';
	    expand.title = '';
	  }

	  return expand;
	};


	/**
	 * Create a DOM tree element, containing the expand/collapse button
	 * @return {Element} domTree
	 * @private
	 */
	Node.prototype._createDomTree = function () {
	  var dom = this.dom;
	  var domTree = document.createElement('table');
	  var tbody = document.createElement('tbody');
	  domTree.style.borderCollapse = 'collapse'; // TODO: put in css
	  domTree.className = 'jsoneditor-values';
	  domTree.appendChild(tbody);
	  var tr = document.createElement('tr');
	  tbody.appendChild(tr);

	  // create expand button
	  var tdExpand = document.createElement('td');
	  tdExpand.className = 'jsoneditor-tree';
	  tr.appendChild(tdExpand);
	  dom.expand = this._createDomExpandButton();
	  tdExpand.appendChild(dom.expand);
	  dom.tdExpand = tdExpand;

	  // create the field
	  var tdField = document.createElement('td');
	  tdField.className = 'jsoneditor-tree';
	  tr.appendChild(tdField);
	  dom.field = this._createDomField();
	  tdField.appendChild(dom.field);
	  dom.tdField = tdField;

	  // create a separator
	  var tdSeparator = document.createElement('td');
	  tdSeparator.className = 'jsoneditor-tree';
	  tr.appendChild(tdSeparator);
	  if (this.type != 'object' && this.type != 'array') {
	    tdSeparator.appendChild(document.createTextNode(':'));
	    tdSeparator.className = 'jsoneditor-separator';
	  }
	  dom.tdSeparator = tdSeparator;

	  // create the value
	  var tdValue = document.createElement('td');
	  tdValue.className = 'jsoneditor-tree';
	  tr.appendChild(tdValue);
	  dom.value = this._createDomValue();
	  tdValue.appendChild(dom.value);
	  dom.tdValue = tdValue;

	  return domTree;
	};

	/**
	 * Handle an event. The event is caught centrally by the editor
	 * @param {Event} event
	 */
	Node.prototype.onEvent = function (event) {
	  var type = event.type,
	      target = event.target || event.srcElement,
	      dom = this.dom,
	      node = this,
	      expandable = this._hasChilds();

	  // check if mouse is on menu or on dragarea.
	  // If so, highlight current row and its childs
	  if (target == dom.drag || target == dom.menu) {
	    if (type == 'mouseover') {
	      this.editor.highlighter.highlight(this);
	    }
	    else if (type == 'mouseout') {
	      this.editor.highlighter.unhighlight();
	    }
	  }

	  // context menu events
	  if (type == 'click' && target == dom.menu) {
	    var highlighter = node.editor.highlighter;
	    highlighter.highlight(node);
	    highlighter.lock();
	    util.addClassName(dom.menu, 'jsoneditor-selected');
	    this.showContextMenu(dom.menu, function () {
	      util.removeClassName(dom.menu, 'jsoneditor-selected');
	      highlighter.unlock();
	      highlighter.unhighlight();
	    });
	  }

	  // expand events
	  if (type == 'click') {
	    if (target == dom.expand ||
	        ((node.editor.options.mode === 'view' || node.editor.options.mode === 'form') && target.nodeName === 'DIV')) {
	      if (expandable) {
	        var recurse = event.ctrlKey; // with ctrl-key, expand/collapse all
	        this._onExpand(recurse);
	      }
	    }
	  }

	  // swap the value of a boolean when the checkbox displayed left is clicked
	  if (type == 'change' && target == dom.checkbox) {
	    this.dom.value.innerHTML = !this.value;
	    this._getDomValue();
	  }

	  // update the value of the node based on the selected option
	  if (type == 'change' && target == dom.select) {
	    this.dom.value.innerHTML = dom.select.value;
	    this._getDomValue();
	    this._updateDomValue();
	  }

	  // value events
	  var domValue = dom.value;
	  if (target == domValue) {
	    //noinspection FallthroughInSwitchStatementJS
	    switch (type) {
	      case 'blur':
	      case 'change':
	        this._getDomValue(true);
	        this._updateDomValue();
	        if (this.value) {
	          domValue.innerHTML = this._escapeHTML(this.value);
	        }
	        break;

	      case 'input':
	        //this._debouncedGetDomValue(true); // TODO
	        this._getDomValue(true);
	        this._updateDomValue();
	        break;

	      case 'keydown':
	      case 'mousedown':
	          // TODO: cleanup
	        this.editor.selection = this.editor.getDomSelection();
	        break;

	      case 'click':
	        if (event.ctrlKey && this.editable.value) {
	          // if read-only, we use the regular click behavior of an anchor
	          if (util.isUrl(this.value)) {
	            event.preventDefault();
	            window.open(this.value, '_blank');
	          }
	        }
	        break;

	      case 'keyup':
	        //this._debouncedGetDomValue(true); // TODO
	        this._getDomValue(true);
	        this._updateDomValue();
	        break;

	      case 'cut':
	      case 'paste':
	        setTimeout(function () {
	          node._getDomValue(true);
	          node._updateDomValue();
	        }, 1);
	        break;
	    }
	  }

	  // field events
	  var domField = dom.field;
	  if (target == domField) {
	    switch (type) {
	      case 'blur':
	      case 'change':
	        this._getDomField(true);
	        this._updateDomField();
	        if (this.field) {
	          domField.innerHTML = this._escapeHTML(this.field);
	        }
	        break;

	      case 'input':
	        this._getDomField(true);
	        this._updateSchema();
	        this._updateDomField();
	        this._updateDomValue();
	        break;

	      case 'keydown':
	      case 'mousedown':
	        this.editor.selection = this.editor.getDomSelection();
	        break;

	      case 'keyup':
	        this._getDomField(true);
	        this._updateDomField();
	        break;

	      case 'cut':
	      case 'paste':
	        setTimeout(function () {
	          node._getDomField(true);
	          node._updateDomField();
	        }, 1);
	        break;
	    }
	  }

	  // focus
	  // when clicked in whitespace left or right from the field or value, set focus
	  var domTree = dom.tree;
	  if (target == domTree.parentNode && type == 'click' && !event.hasMoved) {
	    var left = (event.offsetX != undefined) ?
	        (event.offsetX < (this.getLevel() + 1) * 24) :
	        (event.pageX < util.getAbsoluteLeft(dom.tdSeparator));// for FF
	    if (left || expandable) {
	      // node is expandable when it is an object or array
	      if (domField) {
	        util.setEndOfContentEditable(domField);
	        domField.focus();
	      }
	    }
	    else {
	      if (domValue && !this.enum) {
	        util.setEndOfContentEditable(domValue);
	        domValue.focus();
	      }
	    }
	  }
	  if (((target == dom.tdExpand && !expandable) || target == dom.tdField || target == dom.tdSeparator) &&
	      (type == 'click' && !event.hasMoved)) {
	    if (domField) {
	      util.setEndOfContentEditable(domField);
	      domField.focus();
	    }
	  }

	  if (type == 'keydown') {
	    this.onKeyDown(event);
	  }
	};

	/**
	 * Key down event handler
	 * @param {Event} event
	 */
	Node.prototype.onKeyDown = function (event) {
	  var keynum = event.which || event.keyCode;
	  var target = event.target || event.srcElement;
	  var ctrlKey = event.ctrlKey;
	  var shiftKey = event.shiftKey;
	  var altKey = event.altKey;
	  var handled = false;
	  var prevNode, nextNode, nextDom, nextDom2;
	  var editable = this.editor.options.mode === 'tree';
	  var oldSelection;
	  var oldBeforeNode;
	  var nodes;
	  var multiselection;
	  var selectedNodes = this.editor.multiselection.nodes.length > 0
	      ? this.editor.multiselection.nodes
	      : [this];
	  var firstNode = selectedNodes[0];
	  var lastNode = selectedNodes[selectedNodes.length - 1];

	  // console.log(ctrlKey, keynum, event.charCode); // TODO: cleanup
	  if (keynum == 13) { // Enter
	    if (target == this.dom.value) {
	      if (!this.editable.value || event.ctrlKey) {
	        if (util.isUrl(this.value)) {
	          window.open(this.value, '_blank');
	          handled = true;
	        }
	      }
	    }
	    else if (target == this.dom.expand) {
	      var expandable = this._hasChilds();
	      if (expandable) {
	        var recurse = event.ctrlKey; // with ctrl-key, expand/collapse all
	        this._onExpand(recurse);
	        target.focus();
	        handled = true;
	      }
	    }
	  }
	  else if (keynum == 68) {  // D
	    if (ctrlKey && editable) {   // Ctrl+D
	      Node.onDuplicate(selectedNodes);
	      handled = true;
	    }
	  }
	  else if (keynum == 69) { // E
	    if (ctrlKey) {       // Ctrl+E and Ctrl+Shift+E
	      this._onExpand(shiftKey);  // recurse = shiftKey
	      target.focus(); // TODO: should restore focus in case of recursing expand (which takes DOM offline)
	      handled = true;
	    }
	  }
	  else if (keynum == 77 && editable) { // M
	    if (ctrlKey) { // Ctrl+M
	      this.showContextMenu(target);
	      handled = true;
	    }
	  }
	  else if (keynum == 46 && editable) { // Del
	    if (ctrlKey) {       // Ctrl+Del
	      Node.onRemove(selectedNodes);
	      handled = true;
	    }
	  }
	  else if (keynum == 45 && editable) { // Ins
	    if (ctrlKey && !shiftKey) {       // Ctrl+Ins
	      this._onInsertBefore();
	      handled = true;
	    }
	    else if (ctrlKey && shiftKey) {   // Ctrl+Shift+Ins
	      this._onInsertAfter();
	      handled = true;
	    }
	  }
	  else if (keynum == 35) { // End
	    if (altKey) { // Alt+End
	      // find the last node
	      var endNode = this._lastNode();
	      if (endNode) {
	        endNode.focus(Node.focusElement || this._getElementName(target));
	      }
	      handled = true;
	    }
	  }
	  else if (keynum == 36) { // Home
	    if (altKey) { // Alt+Home
	      // find the first node
	      var homeNode = this._firstNode();
	      if (homeNode) {
	        homeNode.focus(Node.focusElement || this._getElementName(target));
	      }
	      handled = true;
	    }
	  }
	  else if (keynum == 37) {        // Arrow Left
	    if (altKey && !shiftKey) {  // Alt + Arrow Left
	      // move to left element
	      var prevElement = this._previousElement(target);
	      if (prevElement) {
	        this.focus(this._getElementName(prevElement));
	      }
	      handled = true;
	    }
	    else if (altKey && shiftKey && editable) { // Alt + Shift + Arrow left
	      if (lastNode.expanded) {
	        var appendDom = lastNode.getAppend();
	        nextDom = appendDom ? appendDom.nextSibling : undefined;
	      }
	      else {
	        var dom = lastNode.getDom();
	        nextDom = dom.nextSibling;
	      }
	      if (nextDom) {
	        nextNode = Node.getNodeFromTarget(nextDom);
	        nextDom2 = nextDom.nextSibling;
	        nextNode2 = Node.getNodeFromTarget(nextDom2);
	        if (nextNode && nextNode instanceof AppendNode &&
	            !(lastNode.parent.childs.length == 1) &&
	            nextNode2 && nextNode2.parent) {
	          oldSelection = this.editor.getDomSelection();
	          oldBeforeNode = lastNode.nextSibling();

	          selectedNodes.forEach(function (node) {
	            nextNode2.parent.moveBefore(node, nextNode2);
	          });
	          this.focus(Node.focusElement || this._getElementName(target));

	          this.editor._onAction('moveNodes', {
	            nodes: selectedNodes,
	            oldBeforeNode: oldBeforeNode,
	            newBeforeNode: nextNode2,
	            oldSelection: oldSelection,
	            newSelection: this.editor.getDomSelection()
	          });
	        }
	      }
	    }
	  }
	  else if (keynum == 38) {        // Arrow Up
	    if (altKey && !shiftKey) {  // Alt + Arrow Up
	      // find the previous node
	      prevNode = this._previousNode();
	      if (prevNode) {
	        this.editor.deselect(true);
	        prevNode.focus(Node.focusElement || this._getElementName(target));
	      }
	      handled = true;
	    }
	    else if (!altKey && ctrlKey && shiftKey && editable) { // Ctrl + Shift + Arrow Up
	      // select multiple nodes
	      prevNode = this._previousNode();
	      if (prevNode) {
	        multiselection = this.editor.multiselection;
	        multiselection.start = multiselection.start || this;
	        multiselection.end = prevNode;
	        nodes = this.editor._findTopLevelNodes(multiselection.start, multiselection.end);

	        this.editor.select(nodes);
	        prevNode.focus('field'); // select field as we know this always exists
	      }
	      handled = true;
	    }
	    else if (altKey && shiftKey && editable) { // Alt + Shift + Arrow Up
	      // find the previous node
	      prevNode = firstNode._previousNode();
	      if (prevNode && prevNode.parent) {
	        oldSelection = this.editor.getDomSelection();
	        oldBeforeNode = lastNode.nextSibling();

	        selectedNodes.forEach(function (node) {
	          prevNode.parent.moveBefore(node, prevNode);
	        });
	        this.focus(Node.focusElement || this._getElementName(target));

	        this.editor._onAction('moveNodes', {
	          nodes: selectedNodes,
	          oldBeforeNode: oldBeforeNode,
	          newBeforeNode: prevNode,
	          oldSelection: oldSelection,
	          newSelection: this.editor.getDomSelection()
	        });
	      }
	      handled = true;
	    }
	  }
	  else if (keynum == 39) {        // Arrow Right
	    if (altKey && !shiftKey) {  // Alt + Arrow Right
	      // move to right element
	      var nextElement = this._nextElement(target);
	      if (nextElement) {
	        this.focus(this._getElementName(nextElement));
	      }
	      handled = true;
	    }
	    else if (altKey && shiftKey && editable) { // Alt + Shift + Arrow Right
	      dom = firstNode.getDom();
	      var prevDom = dom.previousSibling;
	      if (prevDom) {
	        prevNode = Node.getNodeFromTarget(prevDom);
	        if (prevNode && prevNode.parent &&
	            (prevNode instanceof AppendNode)
	            && !prevNode.isVisible()) {
	          oldSelection = this.editor.getDomSelection();
	          oldBeforeNode = lastNode.nextSibling();

	          selectedNodes.forEach(function (node) {
	            prevNode.parent.moveBefore(node, prevNode);
	          });
	          this.focus(Node.focusElement || this._getElementName(target));

	          this.editor._onAction('moveNodes', {
	            nodes: selectedNodes,
	            oldBeforeNode: oldBeforeNode,
	            newBeforeNode: prevNode,
	            oldSelection: oldSelection,
	            newSelection: this.editor.getDomSelection()
	          });
	        }
	      }
	    }
	  }
	  else if (keynum == 40) {        // Arrow Down
	    if (altKey && !shiftKey) {  // Alt + Arrow Down
	      // find the next node
	      nextNode = this._nextNode();
	      if (nextNode) {
	        this.editor.deselect(true);
	        nextNode.focus(Node.focusElement || this._getElementName(target));
	      }
	      handled = true;
	    }
	    else if (!altKey && ctrlKey && shiftKey && editable) { // Ctrl + Shift + Arrow Down
	      // select multiple nodes
	      nextNode = this._nextNode();
	      if (nextNode) {
	        multiselection = this.editor.multiselection;
	        multiselection.start = multiselection.start || this;
	        multiselection.end = nextNode;
	        nodes = this.editor._findTopLevelNodes(multiselection.start, multiselection.end);

	        this.editor.select(nodes);
	        nextNode.focus('field'); // select field as we know this always exists
	      }
	      handled = true;
	    }
	    else if (altKey && shiftKey && editable) { // Alt + Shift + Arrow Down
	      // find the 2nd next node and move before that one
	      if (lastNode.expanded) {
	        nextNode = lastNode.append ? lastNode.append._nextNode() : undefined;
	      }
	      else {
	        nextNode = lastNode._nextNode();
	      }
	      var nextNode2 = nextNode && (nextNode._nextNode() || nextNode.parent.append);
	      if (nextNode2 && nextNode2.parent) {
	        oldSelection = this.editor.getDomSelection();
	        oldBeforeNode = lastNode.nextSibling();

	        selectedNodes.forEach(function (node) {
	          nextNode2.parent.moveBefore(node, nextNode2);
	        });
	        this.focus(Node.focusElement || this._getElementName(target));

	        this.editor._onAction('moveNodes', {
	          nodes: selectedNodes,
	          oldBeforeNode: oldBeforeNode,
	          newBeforeNode: nextNode2,
	          oldSelection: oldSelection,
	          newSelection: this.editor.getDomSelection()
	        });
	      }
	      handled = true;
	    }
	  }

	  if (handled) {
	    event.preventDefault();
	    event.stopPropagation();
	  }
	};

	/**
	 * Handle the expand event, when clicked on the expand button
	 * @param {boolean} recurse   If true, child nodes will be expanded too
	 * @private
	 */
	Node.prototype._onExpand = function (recurse) {
	  if (recurse) {
	    // Take the table offline
	    var table = this.dom.tr.parentNode; // TODO: not nice to access the main table like this
	    var frame = table.parentNode;
	    var scrollTop = frame.scrollTop;
	    frame.removeChild(table);
	  }

	  if (this.expanded) {
	    this.collapse(recurse);
	  }
	  else {
	    this.expand(recurse);
	  }

	  if (recurse) {
	    // Put the table online again
	    frame.appendChild(table);
	    frame.scrollTop = scrollTop;
	  }
	};

	/**
	 * Remove nodes
	 * @param {Node[] | Node} nodes
	 */
	Node.onRemove = function(nodes) {
	  if (!Array.isArray(nodes)) {
	    return Node.onRemove([nodes]);
	  }

	  if (nodes && nodes.length > 0) {
	    var firstNode = nodes[0];
	    var parent = firstNode.parent;
	    var editor = firstNode.editor;
	    var firstIndex = firstNode.getIndex();
	    editor.highlighter.unhighlight();

	    // adjust the focus
	    var oldSelection = editor.getDomSelection();
	    Node.blurNodes(nodes);
	    var newSelection = editor.getDomSelection();

	    // remove the nodes
	    nodes.forEach(function (node) {
	      node.parent._remove(node);
	    });

	    // store history action
	    editor._onAction('removeNodes', {
	      nodes: nodes.slice(0), // store a copy of the array!
	      parent: parent,
	      index: firstIndex,
	      oldSelection: oldSelection,
	      newSelection: newSelection
	    });
	  }
	};


	/**
	 * Duplicate nodes
	 * duplicated nodes will be added right after the original nodes
	 * @param {Node[] | Node} nodes
	 */
	Node.onDuplicate = function(nodes) {
	  if (!Array.isArray(nodes)) {
	    return Node.onDuplicate([nodes]);
	  }

	  if (nodes && nodes.length > 0) {
	    var lastNode = nodes[nodes.length - 1];
	    var parent = lastNode.parent;
	    var editor = lastNode.editor;

	    editor.deselect(editor.multiselection.nodes);

	    // duplicate the nodes
	    var oldSelection = editor.getDomSelection();
	    var afterNode = lastNode;
	    var clones = nodes.map(function (node) {
	      var clone = node.clone();
	      parent.insertAfter(clone, afterNode);
	      afterNode = clone;
	      return clone;
	    });

	    // set selection to the duplicated nodes
	    if (nodes.length === 1) {
	      clones[0].focus();
	    }
	    else {
	      editor.select(clones);
	    }
	    var newSelection = editor.getDomSelection();

	    editor._onAction('duplicateNodes', {
	      afterNode: lastNode,
	      nodes: clones,
	      parent: parent,
	      oldSelection: oldSelection,
	      newSelection: newSelection
	    });
	  }
	};

	/**
	 * Handle insert before event
	 * @param {String} [field]
	 * @param {*} [value]
	 * @param {String} [type]   Can be 'auto', 'array', 'object', or 'string'
	 * @private
	 */
	Node.prototype._onInsertBefore = function (field, value, type) {
	  var oldSelection = this.editor.getDomSelection();

	  var newNode = new Node(this.editor, {
	    field: (field != undefined) ? field : '',
	    value: (value != undefined) ? value : '',
	    type: type
	  });
	  newNode.expand(true);
	  this.parent.insertBefore(newNode, this);
	  this.editor.highlighter.unhighlight();
	  newNode.focus('field');
	  var newSelection = this.editor.getDomSelection();

	  this.editor._onAction('insertBeforeNodes', {
	    nodes: [newNode],
	    beforeNode: this,
	    parent: this.parent,
	    oldSelection: oldSelection,
	    newSelection: newSelection
	  });
	};

	/**
	 * Handle insert after event
	 * @param {String} [field]
	 * @param {*} [value]
	 * @param {String} [type]   Can be 'auto', 'array', 'object', or 'string'
	 * @private
	 */
	Node.prototype._onInsertAfter = function (field, value, type) {
	  var oldSelection = this.editor.getDomSelection();

	  var newNode = new Node(this.editor, {
	    field: (field != undefined) ? field : '',
	    value: (value != undefined) ? value : '',
	    type: type
	  });
	  newNode.expand(true);
	  this.parent.insertAfter(newNode, this);
	  this.editor.highlighter.unhighlight();
	  newNode.focus('field');
	  var newSelection = this.editor.getDomSelection();

	  this.editor._onAction('insertAfterNodes', {
	    nodes: [newNode],
	    afterNode: this,
	    parent: this.parent,
	    oldSelection: oldSelection,
	    newSelection: newSelection
	  });
	};

	/**
	 * Handle append event
	 * @param {String} [field]
	 * @param {*} [value]
	 * @param {String} [type]   Can be 'auto', 'array', 'object', or 'string'
	 * @private
	 */
	Node.prototype._onAppend = function (field, value, type) {
	  var oldSelection = this.editor.getDomSelection();

	  var newNode = new Node(this.editor, {
	    field: (field != undefined) ? field : '',
	    value: (value != undefined) ? value : '',
	    type: type
	  });
	  newNode.expand(true);
	  this.parent.appendChild(newNode);
	  this.editor.highlighter.unhighlight();
	  newNode.focus('field');
	  var newSelection = this.editor.getDomSelection();

	  this.editor._onAction('appendNodes', {
	    nodes: [newNode],
	    parent: this.parent,
	    oldSelection: oldSelection,
	    newSelection: newSelection
	  });
	};

	/**
	 * Change the type of the node's value
	 * @param {String} newType
	 * @private
	 */
	Node.prototype._onChangeType = function (newType) {
	  var oldType = this.type;
	  if (newType != oldType) {
	    var oldSelection = this.editor.getDomSelection();
	    this.changeType(newType);
	    var newSelection = this.editor.getDomSelection();

	    this.editor._onAction('changeType', {
	      node: this,
	      oldType: oldType,
	      newType: newType,
	      oldSelection: oldSelection,
	      newSelection: newSelection
	    });
	  }
	};

	/**
	 * Sort the child's of the node. Only applicable when the node has type 'object'
	 * or 'array'.
	 * @param {String} direction   Sorting direction. Available values: "asc", "desc"
	 * @private
	 */
	Node.prototype.sort = function (direction) {
	  if (!this._hasChilds()) {
	    return;
	  }

	  var order = (direction == 'desc') ? -1 : 1;
	  var prop = (this.type == 'array') ? 'value': 'field';
	  this.hideChilds();

	  var oldChilds = this.childs;
	  var oldSortOrder = this.sortOrder;

	  // copy the array (the old one will be kept for an undo action
	  this.childs = this.childs.concat();

	  // sort the arrays
	  this.childs.sort(function (a, b) {
	    return order * naturalSort(a[prop], b[prop]);
	  });
	  this.sortOrder = (order == 1) ? 'asc' : 'desc';

	  this.editor._onAction('sort', {
	    node: this,
	    oldChilds: oldChilds,
	    oldSort: oldSortOrder,
	    newChilds: this.childs,
	    newSort: this.sortOrder
	  });

	  this.showChilds();
	};

	/**
	 * Create a table row with an append button.
	 * @return {HTMLElement | undefined} buttonAppend or undefined when inapplicable
	 */
	Node.prototype.getAppend = function () {
	  if (!this.append) {
	    this.append = new AppendNode(this.editor);
	    this.append.setParent(this);
	  }
	  return this.append.getDom();
	};

	/**
	 * Find the node from an event target
	 * @param {Node} target
	 * @return {Node | undefined} node  or undefined when not found
	 * @static
	 */
	Node.getNodeFromTarget = function (target) {
	  while (target) {
	    if (target.node) {
	      return target.node;
	    }
	    target = target.parentNode;
	  }

	  return undefined;
	};

	/**
	 * Remove the focus of given nodes, and move the focus to the (a) node before,
	 * (b) the node after, or (c) the parent node.
	 * @param {Array.<Node> | Node} nodes
	 */
	Node.blurNodes = function (nodes) {
	  if (!Array.isArray(nodes)) {
	    Node.blurNodes([nodes]);
	    return;
	  }

	  var firstNode = nodes[0];
	  var parent = firstNode.parent;
	  var firstIndex = firstNode.getIndex();

	  if (parent.childs[firstIndex + nodes.length]) {
	    parent.childs[firstIndex + nodes.length].focus();
	  }
	  else if (parent.childs[firstIndex - 1]) {
	    parent.childs[firstIndex - 1].focus();
	  }
	  else {
	    parent.focus();
	  }
	};

	/**
	 * Get the next sibling of current node
	 * @return {Node} nextSibling
	 */
	Node.prototype.nextSibling = function () {
	  var index = this.parent.childs.indexOf(this);
	  return this.parent.childs[index + 1] || this.parent.append;
	};

	/**
	 * Get the previously rendered node
	 * @return {Node | null} previousNode
	 */
	Node.prototype._previousNode = function () {
	  var prevNode = null;
	  var dom = this.getDom();
	  if (dom && dom.parentNode) {
	    // find the previous field
	    var prevDom = dom;
	    do {
	      prevDom = prevDom.previousSibling;
	      prevNode = Node.getNodeFromTarget(prevDom);
	    }
	    while (prevDom && (prevNode instanceof AppendNode && !prevNode.isVisible()));
	  }
	  return prevNode;
	};

	/**
	 * Get the next rendered node
	 * @return {Node | null} nextNode
	 * @private
	 */
	Node.prototype._nextNode = function () {
	  var nextNode = null;
	  var dom = this.getDom();
	  if (dom && dom.parentNode) {
	    // find the previous field
	    var nextDom = dom;
	    do {
	      nextDom = nextDom.nextSibling;
	      nextNode = Node.getNodeFromTarget(nextDom);
	    }
	    while (nextDom && (nextNode instanceof AppendNode && !nextNode.isVisible()));
	  }

	  return nextNode;
	};

	/**
	 * Get the first rendered node
	 * @return {Node | null} firstNode
	 * @private
	 */
	Node.prototype._firstNode = function () {
	  var firstNode = null;
	  var dom = this.getDom();
	  if (dom && dom.parentNode) {
	    var firstDom = dom.parentNode.firstChild;
	    firstNode = Node.getNodeFromTarget(firstDom);
	  }

	  return firstNode;
	};

	/**
	 * Get the last rendered node
	 * @return {Node | null} lastNode
	 * @private
	 */
	Node.prototype._lastNode = function () {
	  var lastNode = null;
	  var dom = this.getDom();
	  if (dom && dom.parentNode) {
	    var lastDom = dom.parentNode.lastChild;
	    lastNode =  Node.getNodeFromTarget(lastDom);
	    while (lastDom && (lastNode instanceof AppendNode && !lastNode.isVisible())) {
	      lastDom = lastDom.previousSibling;
	      lastNode =  Node.getNodeFromTarget(lastDom);
	    }
	  }
	  return lastNode;
	};

	/**
	 * Get the next element which can have focus.
	 * @param {Element} elem
	 * @return {Element | null} nextElem
	 * @private
	 */
	Node.prototype._previousElement = function (elem) {
	  var dom = this.dom;
	  // noinspection FallthroughInSwitchStatementJS
	  switch (elem) {
	    case dom.value:
	      if (this.fieldEditable) {
	        return dom.field;
	      }
	    // intentional fall through
	    case dom.field:
	      if (this._hasChilds()) {
	        return dom.expand;
	      }
	    // intentional fall through
	    case dom.expand:
	      return dom.menu;
	    case dom.menu:
	      if (dom.drag) {
	        return dom.drag;
	      }
	    // intentional fall through
	    default:
	      return null;
	  }
	};

	/**
	 * Get the next element which can have focus.
	 * @param {Element} elem
	 * @return {Element | null} nextElem
	 * @private
	 */
	Node.prototype._nextElement = function (elem) {
	  var dom = this.dom;
	  // noinspection FallthroughInSwitchStatementJS
	  switch (elem) {
	    case dom.drag:
	      return dom.menu;
	    case dom.menu:
	      if (this._hasChilds()) {
	        return dom.expand;
	      }
	    // intentional fall through
	    case dom.expand:
	      if (this.fieldEditable) {
	        return dom.field;
	      }
	    // intentional fall through
	    case dom.field:
	      if (!this._hasChilds()) {
	        return dom.value;
	      }
	    default:
	      return null;
	  }
	};

	/**
	 * Get the dom name of given element. returns null if not found.
	 * For example when element == dom.field, "field" is returned.
	 * @param {Element} element
	 * @return {String | null} elementName  Available elements with name: 'drag',
	 *                                      'menu', 'expand', 'field', 'value'
	 * @private
	 */
	Node.prototype._getElementName = function (element) {
	  var dom = this.dom;
	  for (var name in dom) {
	    if (dom.hasOwnProperty(name)) {
	      if (dom[name] == element) {
	        return name;
	      }
	    }
	  }
	  return null;
	};

	/**
	 * Test if this node has childs. This is the case when the node is an object
	 * or array.
	 * @return {boolean} hasChilds
	 * @private
	 */
	Node.prototype._hasChilds = function () {
	  return this.type == 'array' || this.type == 'object';
	};

	// titles with explanation for the different types
	Node.TYPE_TITLES = {
	  'auto': translate('autoType'),
	  'object': translate('objectType'),
	  'array': translate('arrayType'),
	  'string': translate('stringType')
	};

	Node.prototype.addTemplates = function (menu, append) {
	    var node = this;
	    var templates = node.editor.options.templates;
	    if (templates == null) return;
	    if (templates.length) {
	        // create a separator
	        menu.push({
	            'type': 'separator'
	        });
	    }
	    var appendData = function (name, data) {
	        node._onAppend(name, data);
	    };
	    var insertData = function (name, data) {
	        node._onInsertBefore(name, data);
	    };
	    templates.forEach(function (template) {
	        menu.push({
	            text: template.text,
	            className: (template.className || 'jsoneditor-type-object'),
	            title: template.title,
	            click: (append ? appendData.bind(this, template.field, template.value) : insertData.bind(this, template.field, template.value))
	        });
	    });
	};

	/**
	 * Show a contextmenu for this node
	 * @param {HTMLElement} anchor   Anchor element to attach the context menu to
	 *                               as sibling.
	 * @param {function} [onClose]   Callback method called when the context menu
	 *                               is being closed.
	 */
	Node.prototype.showContextMenu = function (anchor, onClose) {
	  var node = this;
	  var titles = Node.TYPE_TITLES;
	  var items = [];

	  if (this.editable.value) {
	    items.push({
	      text: translate('type'),
	      title: translate('typeTitle'),
	      className: 'jsoneditor-type-' + this.type,
	      submenu: [
	        {
	          text: translate('auto'),
	          className: 'jsoneditor-type-auto' +
	              (this.type == 'auto' ? ' jsoneditor-selected' : ''),
	          title: titles.auto,
	          click: function () {
	            node._onChangeType('auto');
	          }
	        },
	        {
	          text: translate('array'),
	          className: 'jsoneditor-type-array' +
	              (this.type == 'array' ? ' jsoneditor-selected' : ''),
	          title: titles.array,
	          click: function () {
	            node._onChangeType('array');
	          }
	        },
	        {
	          text: translate('object'),
	          className: 'jsoneditor-type-object' +
	              (this.type == 'object' ? ' jsoneditor-selected' : ''),
	          title: titles.object,
	          click: function () {
	            node._onChangeType('object');
	          }
	        },
	        {
	          text: translate('string'),
	          className: 'jsoneditor-type-string' +
	              (this.type == 'string' ? ' jsoneditor-selected' : ''),
	          title: titles.string,
	          click: function () {
	            node._onChangeType('string');
	          }
	        }
	      ]
	    });
	  }

	  if (this._hasChilds()) {
	    var direction = ((this.sortOrder == 'asc') ? 'desc': 'asc');
	    items.push({
	      text: translate('sort'),
	      title: translate('sortTitle') + this.type,
	      className: 'jsoneditor-sort-' + direction,
	      click: function () {
	        node.sort(direction);
	      },
	      submenu: [
	        {
	          text: translate('ascending'),
	          className: 'jsoneditor-sort-asc',
	          title: translate('ascendingTitle' , {type: this.type}),
	          click: function () {
	            node.sort('asc');
	          }
	        },
	        {
	          text: translate('descending'),
	          className: 'jsoneditor-sort-desc',
	          title: translate('descendingTitle' , {type: this.type}),
	          click: function () {
	            node.sort('desc');
	          }
	        }
	      ]
	    });
	  }

	  if (this.parent && this.parent._hasChilds()) {
	    if (items.length) {
	      // create a separator
	      items.push({
	        'type': 'separator'
	      });
	    }

	    // create append button (for last child node only)
	    var childs = node.parent.childs;
	    if (node == childs[childs.length - 1]) {
	        var appendSubmenu = [
	            {
	                text: translate('auto'),
	                className: 'jsoneditor-type-auto',
	                title: titles.auto,
	                click: function () {
	                    node._onAppend('', '', 'auto');
	                }
	            },
	            {
	                text: translate('array'),
	                className: 'jsoneditor-type-array',
	                title: titles.array,
	                click: function () {
	                    node._onAppend('', []);
	                }
	            },
	            {
	                text: translate('object'),
	                className: 'jsoneditor-type-object',
	                title: titles.object,
	                click: function () {
	                    node._onAppend('', {});
	                }
	            },
	            {
	                text: translate('string'),
	                className: 'jsoneditor-type-string',
	                title: titles.string,
	                click: function () {
	                    node._onAppend('', '', 'string');
	                }
	            }
	        ];
	        node.addTemplates(appendSubmenu, true);
	        items.push({
	            text: translate('appendText'),
	            title: translate('appendTitle'),
	            submenuTitle: translate('appendSubmenuTitle'),
	            className: 'jsoneditor-append',
	            click: function () {
	                node._onAppend('', '', 'auto');
	            },
	            submenu: appendSubmenu
	        });
	    }



	    // create insert button
	    var insertSubmenu = [
	        {
	            text: translate('auto'),
	            className: 'jsoneditor-type-auto',
	            title: titles.auto,
	            click: function () {
	                node._onInsertBefore('', '', 'auto');
	            }
	        },
	        {
	            text: translate('array'),
	            className: 'jsoneditor-type-array',
	            title: titles.array,
	            click: function () {
	                node._onInsertBefore('', []);
	            }
	        },
	        {
	            text: translate('object'),
	            className: 'jsoneditor-type-object',
	            title: titles.object,
	            click: function () {
	                node._onInsertBefore('', {});
	            }
	        },
	        {
	            text: translate('string'),
	            className: 'jsoneditor-type-string',
	            title: titles.string,
	            click: function () {
	                node._onInsertBefore('', '', 'string');
	            }
	        }
	    ];
	    node.addTemplates(insertSubmenu, false);
	    items.push({
	      text: translate('insert'),
	      title: translate('insertTitle'),
	      submenuTitle: translate('insertSub'),
	      className: 'jsoneditor-insert',
	      click: function () {
	        node._onInsertBefore('', '', 'auto');
	      },
	      submenu: insertSubmenu
	    });

	    if (this.editable.field) {
	      // create duplicate button
	      items.push({
	        text: translate('duplicateText'),
	        title: translate('duplicateField'),
	        className: 'jsoneditor-duplicate',
	        click: function () {
	          Node.onDuplicate(node);
	        }
	      });

	      // create remove button
	      items.push({
	        text: translate('removeText'),
	        title: translate('removeField'),
	        className: 'jsoneditor-remove',
	        click: function () {
	          Node.onRemove(node);
	        }
	      });
	    }
	  }

	  var menu = new ContextMenu(items, {close: onClose});
	  menu.show(anchor, this.editor.content);
	};

	/**
	 * get the type of a value
	 * @param {*} value
	 * @return {String} type   Can be 'object', 'array', 'string', 'auto'
	 * @private
	 */
	Node.prototype._getType = function(value) {
	  if (value instanceof Array) {
	    return 'array';
	  }
	  if (value instanceof Object) {
	    return 'object';
	  }
	  if (typeof(value) == 'string' && typeof(this._stringCast(value)) != 'string') {
	    return 'string';
	  }

	  return 'auto';
	};

	/**
	 * cast contents of a string to the correct type. This can be a string,
	 * a number, a boolean, etc
	 * @param {String} str
	 * @return {*} castedStr
	 * @private
	 */
	Node.prototype._stringCast = function(str) {
	  var lower = str.toLowerCase(),
	      num = Number(str),          // will nicely fail with '123ab'
	      numFloat = parseFloat(str); // will nicely fail with '  '

	  if (str == '') {
	    return '';
	  }
	  else if (lower == 'null') {
	    return null;
	  }
	  else if (lower == 'true') {
	    return true;
	  }
	  else if (lower == 'false') {
	    return false;
	  }
	  else if (!isNaN(num) && !isNaN(numFloat)) {
	    return num;
	  }
	  else {
	    return str;
	  }
	};

	/**
	 * escape a text, such that it can be displayed safely in an HTML element
	 * @param {String} text
	 * @return {String} escapedText
	 * @private
	 */
	Node.prototype._escapeHTML = function (text) {
	  if (typeof text !== 'string') {
	    return String(text);
	  }
	  else {
	    var htmlEscaped = String(text)
	        .replace(/&/g, '&amp;')    // must be replaced first!
	        .replace(/</g, '&lt;')
	        .replace(/>/g, '&gt;')
	        .replace(/  /g, ' &nbsp;') // replace double space with an nbsp and space
	        .replace(/^ /, '&nbsp;')   // space at start
	        .replace(/ $/, '&nbsp;');  // space at end

	    var json = JSON.stringify(htmlEscaped);
	    var html = json.substring(1, json.length - 1);
	    if (this.editor.options.escapeUnicode === true) {
	      html = util.escapeUnicodeChars(html);
	    }
	    return html;
	  }
	};

	/**
	 * unescape a string.
	 * @param {String} escapedText
	 * @return {String} text
	 * @private
	 */
	Node.prototype._unescapeHTML = function (escapedText) {
	  var json = '"' + this._escapeJSON(escapedText) + '"';
	  var htmlEscaped = util.parse(json);

	  return htmlEscaped
	      .replace(/&lt;/g, '<')
	      .replace(/&gt;/g, '>')
	      .replace(/&nbsp;|\u00A0/g, ' ')
	      .replace(/&amp;/g, '&');   // must be replaced last
	};

	/**
	 * escape a text to make it a valid JSON string. The method will:
	 *   - replace unescaped double quotes with '\"'
	 *   - replace unescaped backslash with '\\'
	 *   - replace returns with '\n'
	 * @param {String} text
	 * @return {String} escapedText
	 * @private
	 */
	Node.prototype._escapeJSON = function (text) {
	  // TODO: replace with some smart regex (only when a new solution is faster!)
	  var escaped = '';
	  var i = 0;
	  while (i < text.length) {
	    var c = text.charAt(i);
	    if (c == '\n') {
	      escaped += '\\n';
	    }
	    else if (c == '\\') {
	      escaped += c;
	      i++;

	      c = text.charAt(i);
	      if (c === '' || '"\\/bfnrtu'.indexOf(c) == -1) {
	        escaped += '\\';  // no valid escape character
	      }
	      escaped += c;
	    }
	    else if (c == '"') {
	      escaped += '\\"';
	    }
	    else {
	      escaped += c;
	    }
	    i++;
	  }

	  return escaped;
	};

	// TODO: find a nicer solution to resolve this circular dependency between Node and AppendNode
	var AppendNode = appendNodeFactory(Node);

	module.exports = Node;


/***/ },
/* 11 */
/***/ function(module, exports) {

	/*
	 * Natural Sort algorithm for Javascript - Version 0.7 - Released under MIT license
	 * Author: Jim Palmer (based on chunking idea from Dave Koelle)
	 */
	/*jshint unused:false */
	module.exports = function naturalSort (a, b) {
		"use strict";
		var re = /(^([+\-]?(?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?)?$|^0x[0-9a-f]+$|\d+)/gi,
			sre = /(^[ ]*|[ ]*$)/g,
			dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
			hre = /^0x[0-9a-f]+$/i,
			ore = /^0/,
			i = function(s) { return naturalSort.insensitive && ('' + s).toLowerCase() || '' + s; },
			// convert all to strings strip whitespace
			x = i(a).replace(sre, '') || '',
			y = i(b).replace(sre, '') || '',
			// chunk/tokenize
			xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
			yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
			// numeric, hex or date detection
			xD = parseInt(x.match(hre), 16) || (xN.length !== 1 && x.match(dre) && Date.parse(x)),
			yD = parseInt(y.match(hre), 16) || xD && y.match(dre) && Date.parse(y) || null,
			oFxNcL, oFyNcL;
		// first try and sort Hex codes or Dates
		if (yD) {
			if ( xD < yD ) { return -1; }
			else if ( xD > yD ) { return 1; }
		}
		// natural sorting through split numeric strings and default strings
		for(var cLoc=0, numS=Math.max(xN.length, yN.length); cLoc < numS; cLoc++) {
			// find floats not starting with '0', string or 0 if not defined (Clint Priest)
			oFxNcL = !(xN[cLoc] || '').match(ore) && parseFloat(xN[cLoc]) || xN[cLoc] || 0;
			oFyNcL = !(yN[cLoc] || '').match(ore) && parseFloat(yN[cLoc]) || yN[cLoc] || 0;
			// handle numeric vs string comparison - number < string - (Kyle Adams)
			if (isNaN(oFxNcL) !== isNaN(oFyNcL)) { return (isNaN(oFxNcL)) ? 1 : -1; }
			// rely on string comparison if different types - i.e. '02' < 2 != '02' < '2'
			else if (typeof oFxNcL !== typeof oFyNcL) {
				oFxNcL += '';
				oFyNcL += '';
			}
			if (oFxNcL < oFyNcL) { return -1; }
			if (oFxNcL > oFyNcL) { return 1; }
		}
		return 0;
	};


/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var util = __webpack_require__(4);
	var ContextMenu = __webpack_require__(7);
	var translate = __webpack_require__(8).translate;

	/**
	 * A factory function to create an AppendNode, which depends on a Node
	 * @param {Node} Node
	 */
	function appendNodeFactory(Node) {
	  /**
	   * @constructor AppendNode
	   * @extends Node
	   * @param {TreeEditor} editor
	   * Create a new AppendNode. This is a special node which is created at the
	   * end of the list with childs for an object or array
	   */
	  function AppendNode (editor) {
	    /** @type {TreeEditor} */
	    this.editor = editor;
	    this.dom = {};
	  }

	  AppendNode.prototype = new Node();

	  /**
	   * Return a table row with an append button.
	   * @return {Element} dom   TR element
	   */
	  AppendNode.prototype.getDom = function () {
	    // TODO: implement a new solution for the append node
	    var dom = this.dom;

	    if (dom.tr) {
	      return dom.tr;
	    }

	    this._updateEditability();

	    // a row for the append button
	    var trAppend = document.createElement('tr');
	    trAppend.node = this;
	    dom.tr = trAppend;

	    // TODO: consistent naming

	    if (this.editor.options.mode === 'tree') {
	      // a cell for the dragarea column
	      dom.tdDrag = document.createElement('td');

	      // create context menu
	      var tdMenu = document.createElement('td');
	      dom.tdMenu = tdMenu;
	      var menu = document.createElement('button');
	      menu.type = 'button';
	      menu.className = 'jsoneditor-contextmenu';
	      menu.title = 'Click to open the actions menu (Ctrl+M)';
	      dom.menu = menu;
	      tdMenu.appendChild(dom.menu);
	    }

	    // a cell for the contents (showing text 'empty')
	    var tdAppend = document.createElement('td');
	    var domText = document.createElement('div');
	    domText.innerHTML = '(' + translate('empty') + ')';
	    domText.className = 'jsoneditor-readonly';
	    tdAppend.appendChild(domText);
	    dom.td = tdAppend;
	    dom.text = domText;

	    this.updateDom();

	    return trAppend;
	  };

	  /**
	   * Update the HTML dom of the Node
	   */
	  AppendNode.prototype.updateDom = function () {
	    var dom = this.dom;
	    var tdAppend = dom.td;
	    if (tdAppend) {
	      tdAppend.style.paddingLeft = (this.getLevel() * 24 + 26) + 'px';
	      // TODO: not so nice hard coded offset
	    }

	    var domText = dom.text;
	    if (domText) {
	      domText.innerHTML = '(' + translate('empty') + ' ' + this.parent.type + ')';
	    }

	    // attach or detach the contents of the append node:
	    // hide when the parent has childs, show when the parent has no childs
	    var trAppend = dom.tr;
	    if (!this.isVisible()) {
	      if (dom.tr.firstChild) {
	        if (dom.tdDrag) {
	          trAppend.removeChild(dom.tdDrag);
	        }
	        if (dom.tdMenu) {
	          trAppend.removeChild(dom.tdMenu);
	        }
	        trAppend.removeChild(tdAppend);
	      }
	    }
	    else {
	      if (!dom.tr.firstChild) {
	        if (dom.tdDrag) {
	          trAppend.appendChild(dom.tdDrag);
	        }
	        if (dom.tdMenu) {
	          trAppend.appendChild(dom.tdMenu);
	        }
	        trAppend.appendChild(tdAppend);
	      }
	    }
	  };

	  /**
	   * Check whether the AppendNode is currently visible.
	   * the AppendNode is visible when its parent has no childs (i.e. is empty).
	   * @return {boolean} isVisible
	   */
	  AppendNode.prototype.isVisible = function () {
	    return (this.parent.childs.length == 0);
	  };

	  /**
	   * Show a contextmenu for this node
	   * @param {HTMLElement} anchor   The element to attach the menu to.
	   * @param {function} [onClose]   Callback method called when the context menu
	   *                               is being closed.
	   */
	  AppendNode.prototype.showContextMenu = function (anchor, onClose) {
	    var node = this;
	    var titles = Node.TYPE_TITLES;
	    var appendSubmenu = [
	        {
	            text: translate('auto'),
	            className: 'jsoneditor-type-auto',
	            title: titles.auto,
	            click: function () {
	                node._onAppend('', '', 'auto');
	            }
	        },
	        {
	            text: translate('array'),
	            className: 'jsoneditor-type-array',
	            title: titles.array,
	            click: function () {
	                node._onAppend('', []);
	            }
	        },
	        {
	            text: translate('object'),
	            className: 'jsoneditor-type-object',
	            title: titles.object,
	            click: function () {
	                node._onAppend('', {});
	            }
	        },
	        {
	            text: translate('string'),
	            className: 'jsoneditor-type-string',
	            title: titles.string,
	            click: function () {
	                node._onAppend('', '', 'string');
	            }
	        }
	    ];
	    node.addTemplates(appendSubmenu, true);
	    var items = [
	      // create append button
	      {
	        'text': translate('appendText'),
	        'title': translate('appendTitleAuto'),
	        'submenuTitle': translate('appendSubmenuTitle'),
	        'className': 'jsoneditor-insert',
	        'click': function () {
	          node._onAppend('', '', 'auto');
	        },
	        'submenu': appendSubmenu
	      }
	    ];

	    var menu = new ContextMenu(items, {close: onClose});
	    menu.show(anchor, this.editor.content);
	  };

	  /**
	   * Handle an event. The event is catched centrally by the editor
	   * @param {Event} event
	   */
	  AppendNode.prototype.onEvent = function (event) {
	    var type = event.type;
	    var target = event.target || event.srcElement;
	    var dom = this.dom;

	    // highlight the append nodes parent
	    var menu = dom.menu;
	    if (target == menu) {
	      if (type == 'mouseover') {
	        this.editor.highlighter.highlight(this.parent);
	      }
	      else if (type == 'mouseout') {
	        this.editor.highlighter.unhighlight();
	      }
	    }

	    // context menu events
	    if (type == 'click' && target == dom.menu) {
	      var highlighter = this.editor.highlighter;
	      highlighter.highlight(this.parent);
	      highlighter.lock();
	      util.addClassName(dom.menu, 'jsoneditor-selected');
	      this.showContextMenu(dom.menu, function () {
	        util.removeClassName(dom.menu, 'jsoneditor-selected');
	        highlighter.unlock();
	        highlighter.unhighlight();
	      });
	    }

	    if (type == 'keydown') {
	      this.onKeyDown(event);
	    }
	  };

	  return AppendNode;
	}

	module.exports = appendNodeFactory;


/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var ContextMenu = __webpack_require__(7);

	/**
	 * Create a select box to be used in the editor menu's, which allows to switch mode
	 * @param {HTMLElement} container
	 * @param {String[]} modes  Available modes: 'code', 'form', 'text', 'tree', 'view'
	 * @param {String} current  Available modes: 'code', 'form', 'text', 'tree', 'view'
	 * @param {function(mode: string)} onSwitch  Callback invoked on switch
	 * @constructor
	 */
	function ModeSwitcher(container, modes, current, onSwitch) {
	  // available modes
	  var availableModes = {
	    code: {
	      'text': 'Code',
	      'title': 'Switch to code highlighter',
	      'click': function () {
	        onSwitch('code')
	      }
	    },
	    form: {
	      'text': 'Form',
	      'title': 'Switch to form editor',
	      'click': function () {
	        onSwitch('form');
	      }
	    },
	    text: {
	      'text': 'Text',
	      'title': 'Switch to plain text editor',
	      'click': function () {
	        onSwitch('text');
	      }
	    },
	    tree: {
	      'text': 'Tree',
	      'title': 'Switch to tree editor',
	      'click': function () {
	        onSwitch('tree');
	      }
	    },
	    view: {
	      'text': 'View',
	      'title': 'Switch to tree view',
	      'click': function () {
	        onSwitch('view');
	      }
	    }
	  };

	  // list the selected modes
	  var items = [];
	  for (var i = 0; i < modes.length; i++) {
	    var mode = modes[i];
	    var item = availableModes[mode];
	    if (!item) {
	      throw new Error('Unknown mode "' + mode + '"');
	    }

	    item.className = 'jsoneditor-type-modes' + ((current == mode) ? ' jsoneditor-selected' : '');
	    items.push(item);
	  }

	  // retrieve the title of current mode
	  var currentMode = availableModes[current];
	  if (!currentMode) {
	    throw new Error('Unknown mode "' + current + '"');
	  }
	  var currentTitle = currentMode.text;

	  // create the html element
	  var box = document.createElement('button');
	  box.type = 'button';
	  box.className = 'jsoneditor-modes jsoneditor-separator';
	  box.innerHTML = currentTitle + ' &#x25BE;';
	  box.title = 'Switch editor mode';
	  box.onclick = function () {
	    var menu = new ContextMenu(items);
	    menu.show(box);
	  };

	  var frame = document.createElement('div');
	  frame.className = 'jsoneditor-modes';
	  frame.style.position = 'relative';
	  frame.appendChild(box);

	  container.appendChild(frame);

	  this.dom = {
	    container: container,
	    box: box,
	    frame: frame
	  };
	}

	/**
	 * Set focus to switcher
	 */
	ModeSwitcher.prototype.focus = function () {
	  this.dom.box.focus();
	};

	/**
	 * Destroy the ModeSwitcher, remove from DOM
	 */
	ModeSwitcher.prototype.destroy = function () {
	  if (this.dom && this.dom.frame && this.dom.frame.parentNode) {
	    this.dom.frame.parentNode.removeChild(this.dom.frame);
	  }
	  this.dom = null;
	};

	module.exports = ModeSwitcher;


/***/ },
/* 14 */
/***/ function(module, exports) {

	'use strict';

	function completely(config) {
	    config = config || {};
	    config.confirmKeys = config.confirmKeys || [39, 35, 9] // right, end, tab 
	    config.caseSensitive = config.caseSensitive || false    // autocomplete case sensitive

	    var fontSize = '';
	    var fontFamily = '';    

	    var wrapper = document.createElement('div');
	    wrapper.style.position = 'relative';
	    wrapper.style.outline = '0';
	    wrapper.style.border = '0';
	    wrapper.style.margin = '0';
	    wrapper.style.padding = '0';

	    var dropDown = document.createElement('div');
	    dropDown.className = 'autocomplete dropdown';
	    dropDown.style.position = 'absolute';
	    dropDown.style.visibility = 'hidden';

	    var spacer;
	    var leftSide; // <-- it will contain the leftSide part of the textfield (the bit that was already autocompleted)
	    var createDropDownController = function (elem, rs) {
	        var rows = [];
	        var ix = 0;
	        var oldIndex = -1;

	        var onMouseOver = function () { this.style.outline = '1px solid #ddd'; }
	        var onMouseOut = function () { this.style.outline = '0'; }
	        var onMouseDown = function () { p.hide(); p.onmouseselection(this.__hint, p.rs); }

	        var p = {
	            rs: rs,
	            hide: function () {
	                elem.style.visibility = 'hidden';
	                //rs.hideDropDown();
	            },
	            refresh: function (token, array) {
	                elem.style.visibility = 'hidden';
	                ix = 0;
	                elem.innerHTML = '';
	                var vph = (window.innerHeight || document.documentElement.clientHeight);
	                var rect = elem.parentNode.getBoundingClientRect();
	                var distanceToTop = rect.top - 6;                        // heuristic give 6px 
	                var distanceToBottom = vph - rect.bottom - 6;  // distance from the browser border.

	                rows = [];
	                for (var i = 0; i < array.length; i++) {

	                    if (  (config.caseSensitive && array[i].indexOf(token) !== 0)
	                        ||(!config.caseSensitive && array[i].toLowerCase().indexOf(token.toLowerCase()) !== 0)) { continue; }

	                    var divRow = document.createElement('div');
	                    divRow.className = 'item';
	                    //divRow.style.color = config.color;
	                    divRow.onmouseover = onMouseOver;
	                    divRow.onmouseout = onMouseOut;
	                    divRow.onmousedown = onMouseDown;
	                    divRow.__hint = array[i];
	                    divRow.innerHTML = array[i].substring(0, token.length) + '<b>' + array[i].substring(token.length) + '</b>';
	                    rows.push(divRow);
	                    elem.appendChild(divRow);
	                }
	                if (rows.length === 0) {
	                    return; // nothing to show.
	                }
	                if (rows.length === 1 && (   (token.toLowerCase() === rows[0].__hint.toLowerCase() && !config.caseSensitive) 
	                                           ||(token === rows[0].__hint && config.caseSensitive))){
	                    return; // do not show the dropDown if it has only one element which matches what we have just displayed.
	                }

	                if (rows.length < 2) return;
	                p.highlight(0);

	                if (distanceToTop > distanceToBottom * 3) {        // Heuristic (only when the distance to the to top is 4 times more than distance to the bottom
	                    elem.style.maxHeight = distanceToTop + 'px';  // we display the dropDown on the top of the input text
	                    elem.style.top = '';
	                    elem.style.bottom = '100%';
	                } else {
	                    elem.style.top = '100%';
	                    elem.style.bottom = '';
	                    elem.style.maxHeight = distanceToBottom + 'px';
	                }
	                elem.style.visibility = 'visible';
	            },
	            highlight: function (index) {
	                if (oldIndex != -1 && rows[oldIndex]) {
	                    rows[oldIndex].className = "item";
	                }
	                rows[index].className = "item hover"; 
	                oldIndex = index;
	            },
	            move: function (step) { // moves the selection either up or down (unless it's not possible) step is either +1 or -1.
	                if (elem.style.visibility === 'hidden') return ''; // nothing to move if there is no dropDown. (this happens if the user hits escape and then down or up)
	                if (ix + step === -1 || ix + step === rows.length) return rows[ix].__hint; // NO CIRCULAR SCROLLING. 
	                ix += step;
	                p.highlight(ix);
	                return rows[ix].__hint;//txtShadow.value = uRows[uIndex].__hint ;
	            },
	            onmouseselection: function () { } // it will be overwritten. 
	        };
	        return p;
	    }

	    function setEndOfContenteditable(contentEditableElement) {
	        var range, selection;
	        if (document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
	        {
	            range = document.createRange();//Create a range (a range is a like the selection but invisible)
	            range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
	            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
	            selection = window.getSelection();//get the selection object (allows you to change selection)
	            selection.removeAllRanges();//remove any selections already made
	            selection.addRange(range);//make the range you have just created the visible selection
	        }
	        else if (document.selection)//IE 8 and lower
	        {
	            range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
	            range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
	            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
	            range.select();//Select the range (make it the visible selection
	        }
	    }

	    function calculateWidthForText(text) {
	        if (spacer === undefined) { // on first call only.
	            spacer = document.createElement('span');
	            spacer.style.visibility = 'hidden';
	            spacer.style.position = 'fixed';
	            spacer.style.outline = '0';
	            spacer.style.margin = '0';
	            spacer.style.padding = '0';
	            spacer.style.border = '0';
	            spacer.style.left = '0';
	            spacer.style.whiteSpace = 'pre';
	            spacer.style.fontSize = fontSize;
	            spacer.style.fontFamily = fontFamily;
	            spacer.style.fontWeight = 'normal';
	            document.body.appendChild(spacer);
	        }

	        // Used to encode an HTML string into a plain text.
	        // taken from http://stackoverflow.com/questions/1219860/javascript-jquery-html-encoding
	        spacer.innerHTML = String(text).replace(/&/g, '&amp;')
	            .replace(/"/g, '&quot;')
	            .replace(/'/g, '&#39;')
	            .replace(/</g, '&lt;')
	            .replace(/>/g, '&gt;');
	        return spacer.getBoundingClientRect().right;
	    }

	    var rs = {
	        onArrowDown: function () { }, // defaults to no action.
	        onArrowUp: function () { },   // defaults to no action.
	        onEnter: function () { },     // defaults to no action.
	        onTab: function () { },       // defaults to no action.
	        startFrom: 0,
	        options: [],
	        element: null,
	        elementHint: null,
	        elementStyle: null,
	        wrapper: wrapper,      // Only to allow  easy access to the HTML elements to the final user (possibly for minor customizations)
	        show: function (element, startPos, options) {
	            this.startFrom = startPos;
	            this.wrapper.remove();
	            if (this.elementHint) {
	                this.elementHint.remove();
	                this.elementHint = null;
	            }
	            
	            if (fontSize == '') {
	                fontSize = window.getComputedStyle(element).getPropertyValue('font-size');
	            }
	            if (fontFamily == '') {
	                fontFamily = window.getComputedStyle(element).getPropertyValue('font-family');
	            }
	            
	            var w = element.getBoundingClientRect().right - element.getBoundingClientRect().left;
	            dropDown.style.marginLeft = '0';
	            dropDown.style.marginTop = element.getBoundingClientRect().height + 'px';
	            this.options = options;

	            if (this.element != element) {
	                this.element = element;
	                this.elementStyle = {
	                    zIndex: this.element.style.zIndex,
	                    position: this.element.style.position,
	                    backgroundColor: this.element.style.backgroundColor,
	                    borderColor: this.element.style.borderColor
	                }
	            }

	            this.element.style.zIndex = 3;
	            this.element.style.position = 'relative';
	            this.element.style.backgroundColor = 'transparent';
	            this.element.style.borderColor = 'transparent';

	            this.elementHint = element.cloneNode();
	            this.elementHint.className = 'autocomplete hint';
	            this.elementHint.style.zIndex = 2;
	            this.elementHint.style.position = 'absolute';
	            this.elementHint.onfocus = function () { this.element.focus(); }.bind(this);



	            if (this.element.addEventListener) {
	                this.element.removeEventListener("keydown", keyDownHandler);
	                this.element.addEventListener("keydown", keyDownHandler, false);
	                this.element.removeEventListener("blur", onBlurHandler);
	                this.element.addEventListener("blur", onBlurHandler, false);                
	            } 

	            wrapper.appendChild(this.elementHint);
	            wrapper.appendChild(dropDown);
	            element.parentElement.appendChild(wrapper);


	            this.repaint(element);
	        },
	        setText: function (text) {
	            this.element.innerText = text;
	        },
	        getText: function () {
	            return this.element.innerText;
	        },
	        hideDropDown: function () {
	            this.wrapper.remove();
	            if (this.elementHint) {
	                this.elementHint.remove();
	                this.elementHint = null;
	                dropDownController.hide();
	                this.element.style.zIndex = this.elementStyle.zIndex;
	                this.element.style.position = this.elementStyle.position;
	                this.element.style.backgroundColor = this.elementStyle.backgroundColor;
	                this.element.style.borderColor = this.elementStyle.borderColor;
	            }
	            
	        },
	        repaint: function (element) {
	            var text = element.innerText;
	            text = text.replace('\n', '');

	            var startFrom = this.startFrom;
	            var options = this.options;
	            var optionsLength = this.options.length;

	            // breaking text in leftSide and token.
	            
	            var token = text.substring(this.startFrom);
	            leftSide = text.substring(0, this.startFrom);
	            
	            for (var i = 0; i < optionsLength; i++) {
	                var opt = this.options[i];
	                if (   (!config.caseSensitive && opt.toLowerCase().indexOf(token.toLowerCase()) === 0)
	                    || (config.caseSensitive && opt.indexOf(token) === 0)) {   // <-- how about upperCase vs. lowercase
	                    this.elementHint.innerText = leftSide + token + opt.substring(token.length);
	                    this.elementHint.realInnerText = leftSide + opt;
	                    break;
	                }
	            }
	            // moving the dropDown and refreshing it.
	            dropDown.style.left = calculateWidthForText(leftSide) + 'px';
	            dropDownController.refresh(token, this.options);
	            this.elementHint.style.width = calculateWidthForText(this.elementHint.innerText) + 10 + 'px'
	            var wasDropDownHidden = (dropDown.style.visibility == 'hidden');
	            if (!wasDropDownHidden)
	                this.elementHint.style.width = calculateWidthForText(this.elementHint.innerText) + dropDown.clientWidth + 'px';
	        }
	    };

	    var dropDownController = createDropDownController(dropDown, rs);

	    var keyDownHandler = function (e) {
	        //console.log("Keydown:" + e.keyCode);
	        e = e || window.event;
	        var keyCode = e.keyCode;

	        if (this.elementHint == null) return;

	        if (keyCode == 33) { return; } // page up (do nothing)
	        if (keyCode == 34) { return; } // page down (do nothing);

	        if (keyCode == 27) { //escape
	            rs.hideDropDown();
	            rs.element.focus();
	            e.preventDefault();
	            e.stopPropagation();
	            return;
	        }

	        var text = this.element.innerText;
	        text = text.replace('\n', '');
	        var startFrom = this.startFrom;

	        if (config.confirmKeys.indexOf(keyCode) >= 0) { //  (autocomplete triggered)
	            if (keyCode == 9) {                 
	                if (this.elementHint.innerText.length == 0) {
	                    rs.onTab(); 
	                }
	            }
	            if (this.elementHint.innerText.length > 0) { // if there is a hint               
	                if (this.element.innerText != this.elementHint.realInnerText) {
	                    this.element.innerText = this.elementHint.realInnerText;
	                    rs.hideDropDown();
	                    setEndOfContenteditable(this.element);
	                    if (keyCode == 9) {                
	                        rs.element.focus();
	                        e.preventDefault();
	                        e.stopPropagation();
	                    }
	                }                
	            }
	            return;
	        }

	        if (keyCode == 13) {       // enter  (autocomplete triggered)
	            if (this.elementHint.innerText.length == 0) { // if there is a hint
	                rs.onEnter();
	            } else {
	                var wasDropDownHidden = (dropDown.style.visibility == 'hidden');
	                dropDownController.hide();

	                if (wasDropDownHidden) {
	                    rs.hideDropDown();
	                    rs.element.focus();
	                    rs.onEnter();
	                    return;
	                }

	                this.element.innerText = this.elementHint.realInnerText;
	                rs.hideDropDown();
	                setEndOfContenteditable(this.element);
	                e.preventDefault();
	                e.stopPropagation();
	            }
	            return;
	        }

	        if (keyCode == 40) {     // down
	            var token = text.substring(this.startFrom);
	            var m = dropDownController.move(+1);
	            if (m == '') { rs.onArrowDown(); }
	            this.elementHint.innerText = leftSide + token + m.substring(token.length);
	            this.elementHint.realInnerText = leftSide + m;
	            e.preventDefault();
	            e.stopPropagation();
	            return;
	        }

	        if (keyCode == 38) {    // up
	            var token = text.substring(this.startFrom);
	            var m = dropDownController.move(-1);
	            if (m == '') { rs.onArrowUp(); }
	            this.elementHint.innerText = leftSide + token + m.substring(token.length);
	            this.elementHint.realInnerText = leftSide + m;
	            e.preventDefault();
	            e.stopPropagation();
	            return;
	        }

	    }.bind(rs);

	    var onBlurHandler = function (e) {
	        rs.hideDropDown();
	        //console.log("Lost focus.");
	    }.bind(rs);

	    dropDownController.onmouseselection = function (text, rs) {
	        rs.element.innerText = rs.elementHint.innerText = leftSide + text;        
	        rs.hideDropDown();   
	        window.setTimeout(function () {
	            rs.element.focus();
	            setEndOfContenteditable(rs.element);  
	        }, 1);              
	    };

	    return rs;
	}

	module.exports = completely;

/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var ace = __webpack_require__(16);
	var ModeSwitcher = __webpack_require__(13);
	var util = __webpack_require__(4);

	// create a mixin with the functions for text mode
	var textmode = {};

	var MAX_ERRORS = 3; // maximum number of displayed errors at the bottom

	var DEFAULT_THEME = 'ace/theme/jsoneditor';

	/**
	 * Create a text editor
	 * @param {Element} container
	 * @param {Object} [options]   Object with options. available options:
	 *                             {String} mode             Available values:
	 *                                                       "text" (default)
	 *                                                       or "code".
	 *                             {Number} indentation      Number of indentation
	 *                                                       spaces. 2 by default.
	 *                             {function} onChange       Callback method
	 *                                                       triggered on change
	 *                             {function} onModeChange   Callback method
	 *                                                       triggered after setMode
	 *                             {function} onEditable     Determine if textarea is readOnly
	 *                                                       readOnly defaults true
	 *                             {Object} ace              A custom instance of
	 *                                                       Ace editor.
	 *                             {boolean} escapeUnicode   If true, unicode
	 *                                                       characters are escaped.
	 *                                                       false by default.
	 *                             {function} onTextSelectionChange Callback method, 
	 *                                                              triggered on text selection change
	 * @private
	 */
	textmode.create = function (container, options) {
	  // read options
	  options = options || {};
	  
	  if(typeof options.statusBar === 'undefined') {
	    options.statusBar = true;
	  }

	  this.options = options;

	  // indentation
	  if (options.indentation) {
	    this.indentation = Number(options.indentation);
	  }
	  else {
	    this.indentation = 2; // number of spaces
	  }

	  // grab ace from options if provided
	  var _ace = options.ace ? options.ace : ace;
	  // TODO: make the option options.ace deprecated, it's not needed anymore (see #309)

	  // determine mode
	  this.mode = (options.mode == 'code') ? 'code' : 'text';
	  if (this.mode == 'code') {
	    // verify whether Ace editor is available and supported
	    if (typeof _ace === 'undefined') {
	      this.mode = 'text';
	      console.warn('Failed to load Ace editor, falling back to plain text mode. Please use a JSONEditor bundle including Ace, or pass Ace as via the configuration option `ace`.');
	    }
	  }

	  // determine theme
	  this.theme = options.theme || DEFAULT_THEME;
	  if (this.theme === DEFAULT_THEME && _ace) {
	    try {
	      __webpack_require__(20);
	    }
	    catch (err) {
	      console.error(err);
	    }
	  }

	  if (options.onTextSelectionChange) {
	    this.onTextSelectionChange(options.onTextSelectionChange);
	  }

	  var me = this;
	  this.container = container;
	  this.dom = {};
	  this.aceEditor = undefined;  // ace code editor
	  this.textarea = undefined;  // plain text editor (fallback when Ace is not available)
	  this.validateSchema = null;

	  // create a debounced validate function
	  this._debouncedValidate = util.debounce(this.validate.bind(this), this.DEBOUNCE_INTERVAL);

	  this.width = container.clientWidth;
	  this.height = container.clientHeight;

	  this.frame = document.createElement('div');
	  this.frame.className = 'jsoneditor jsoneditor-mode-' + this.options.mode;
	  this.frame.onclick = function (event) {
	    // prevent default submit action when the editor is located inside a form
	    event.preventDefault();
	  };
	  this.frame.onkeydown = function (event) {
	    me._onKeyDown(event);
	  };
	  
	  // create menu
	  this.menu = document.createElement('div');
	  this.menu.className = 'jsoneditor-menu';
	  this.frame.appendChild(this.menu);

	  // create format button
	  var buttonFormat = document.createElement('button');
	  buttonFormat.type = 'button';
	  buttonFormat.className = 'jsoneditor-format';
	  buttonFormat.title = 'Format JSON data, with proper indentation and line feeds (Ctrl+\\)';
	  this.menu.appendChild(buttonFormat);
	  buttonFormat.onclick = function () {
	    try {
	      me.format();
	      me._onChange();
	    }
	    catch (err) {
	      me._onError(err);
	    }
	  };

	  // create compact button
	  var buttonCompact = document.createElement('button');
	  buttonCompact.type = 'button';
	  buttonCompact.className = 'jsoneditor-compact';
	  buttonCompact.title = 'Compact JSON data, remove all whitespaces (Ctrl+Shift+\\)';
	  this.menu.appendChild(buttonCompact);
	  buttonCompact.onclick = function () {
	    try {
	      me.compact();
	      me._onChange();
	    }
	    catch (err) {
	      me._onError(err);
	    }
	  };

	  // create repair button
	  var buttonRepair = document.createElement('button');
	  buttonRepair.type = 'button';
	  buttonRepair.className = 'jsoneditor-repair';
	  buttonRepair.title = 'Repair JSON: fix quotes and escape characters, remove comments and JSONP notation, turn JavaScript objects into JSON.';
	  this.menu.appendChild(buttonRepair);
	  buttonRepair.onclick = function () {
	    try {
	      me.repair();
	      me._onChange();
	    }
	    catch (err) {
	      me._onError(err);
	    }
	  };

	  // create mode box
	  if (this.options && this.options.modes && this.options.modes.length) {
	    this.modeSwitcher = new ModeSwitcher(this.menu, this.options.modes, this.options.mode, function onSwitch(mode) {
	      // switch mode and restore focus
	      me.setMode(mode);
	      me.modeSwitcher.focus();
	    });
	  }

	  var emptyNode = {};
	  var isReadOnly = (this.options.onEditable
	  && typeof(this.options.onEditable === 'function')
	  && !this.options.onEditable(emptyNode));

	  this.content = document.createElement('div');
	  this.content.className = 'jsoneditor-outer';
	  this.frame.appendChild(this.content);

	  this.container.appendChild(this.frame);

	  if (this.mode == 'code') {
	    this.editorDom = document.createElement('div');
	    this.editorDom.style.height = '100%'; // TODO: move to css
	    this.editorDom.style.width = '100%'; // TODO: move to css
	    this.content.appendChild(this.editorDom);

	    var aceEditor = _ace.edit(this.editorDom);
	    aceEditor.$blockScrolling = Infinity;
	    aceEditor.setTheme(this.theme);
	    aceEditor.setOptions({ readOnly: isReadOnly });
	    aceEditor.setShowPrintMargin(false);
	    aceEditor.setFontSize(13);
	    aceEditor.getSession().setMode('ace/mode/json');
	    aceEditor.getSession().setTabSize(this.indentation);
	    aceEditor.getSession().setUseSoftTabs(true);
	    aceEditor.getSession().setUseWrapMode(true);
	    aceEditor.commands.bindKey('Ctrl-L', null);    // disable Ctrl+L (is used by the browser to select the address bar)
	    aceEditor.commands.bindKey('Command-L', null); // disable Ctrl+L (is used by the browser to select the address bar)
	    this.aceEditor = aceEditor;

	    // TODO: deprecated since v5.0.0. Cleanup backward compatibility some day
	    if (!this.hasOwnProperty('editor')) {
	      Object.defineProperty(this, 'editor', {
	        get: function () {
	          console.warn('Property "editor" has been renamed to "aceEditor".');
	          return me.aceEditor;
	        },
	        set: function (aceEditor) {
	          console.warn('Property "editor" has been renamed to "aceEditor".');
	          me.aceEditor = aceEditor;
	        }
	      });
	    }

	    var poweredBy = document.createElement('a');
	    poweredBy.appendChild(document.createTextNode('powered by ace'));
	    poweredBy.href = 'http://ace.ajax.org';
	    poweredBy.target = '_blank';
	    poweredBy.className = 'jsoneditor-poweredBy';
	    poweredBy.onclick = function () {
	      // TODO: this anchor falls below the margin of the content,
	      // therefore the normal a.href does not work. We use a click event
	      // for now, but this should be fixed.
	      window.open(poweredBy.href, poweredBy.target);
	    };
	    this.menu.appendChild(poweredBy);

	    // register onchange event
	    aceEditor.on('change', this._onChange.bind(this));
	    aceEditor.on('changeSelection', this._onSelect.bind(this));
	  }
	  else {
	    // load a plain text textarea
	    var textarea = document.createElement('textarea');
	    textarea.className = 'jsoneditor-text';
	    textarea.spellcheck = false;
	    this.content.appendChild(textarea);
	    this.textarea = textarea;
	    this.textarea.readOnly = isReadOnly;

	    // register onchange event
	    if (this.textarea.oninput === null) {
	      this.textarea.oninput = this._onChange.bind(this);
	    }
	    else {
	      // oninput is undefined. For IE8-
	      this.textarea.onchange = this._onChange.bind(this);
	    }

	    textarea.onselect = this._onSelect.bind(this);
	    textarea.onmousedown = this._onMouseDown.bind(this);
	    textarea.onblur = this._onBlur.bind(this);
	  }

	  var validationErrorsContainer = document.createElement('div');
	  validationErrorsContainer.className = 'validation-errors-container';
	  this.dom.validationErrorsContainer = validationErrorsContainer;
	  this.frame.appendChild(validationErrorsContainer);

	  if (options.statusBar) {
	    util.addClassName(this.content, 'has-status-bar');

	    this.curserInfoElements = {};
	    var statusBar = document.createElement('div');
	    this.dom.statusBar = statusBar;
	    statusBar.className = 'jsoneditor-statusbar';
	    this.frame.appendChild(statusBar);

	    var lnLabel = document.createElement('span');
	    lnLabel.className = 'jsoneditor-curserinfo-label';
	    lnLabel.innerText = 'Ln:';

	    var lnVal = document.createElement('span');
	    lnVal.className = 'jsoneditor-curserinfo-val';
	    lnVal.innerText = '1';

	    statusBar.appendChild(lnLabel);
	    statusBar.appendChild(lnVal);

	    var colLabel = document.createElement('span');
	    colLabel.className = 'jsoneditor-curserinfo-label';
	    colLabel.innerText = 'Col:';

	    var colVal = document.createElement('span');
	    colVal.className = 'jsoneditor-curserinfo-val';
	    colVal.innerText = '1';

	    statusBar.appendChild(colLabel);
	    statusBar.appendChild(colVal);

	    this.curserInfoElements.colVal = colVal;
	    this.curserInfoElements.lnVal = lnVal;

	    var countLabel = document.createElement('span');
	    countLabel.className = 'jsoneditor-curserinfo-label';
	    countLabel.innerText = 'characters selected';
	    countLabel.style.display = 'none';

	    var countVal = document.createElement('span');
	    countVal.className = 'jsoneditor-curserinfo-count';
	    countVal.innerText = '0';
	    countVal.style.display = 'none';

	    this.curserInfoElements.countLabel = countLabel;
	    this.curserInfoElements.countVal = countVal;

	    statusBar.appendChild(countVal);
	    statusBar.appendChild(countLabel);
	  }

	  this.setSchema(this.options.schema, this.options.schemaRefs);  
	};

	/**
	 * Handle a change:
	 * - Validate JSON schema
	 * - Send a callback to the onChange listener if provided
	 * @private
	 */
	textmode._onChange = function () {
	  // validate JSON schema (if configured)
	  this._debouncedValidate();

	  // trigger the onChange callback
	  if (this.options.onChange) {
	    try {
	      this.options.onChange();
	    }
	    catch (err) {
	      console.error('Error in onChange callback: ', err);
	    }
	  }
	};

	/**
	 * Handle text selection
	 * Calculates the cursor position and selection range and updates menu
	 * @private
	 */
	textmode._onSelect = function () {
	  this._updateCursorInfo();
	  this._emitSelectionChange();
	};

	/**
	 * Event handler for keydown. Handles shortcut keys
	 * @param {Event} event
	 * @private
	 */
	textmode._onKeyDown = function (event) {
	  var keynum = event.which || event.keyCode;
	  var handled = false;

	  if (keynum == 220 && event.ctrlKey) {
	    if (event.shiftKey) { // Ctrl+Shift+\
	      this.compact();
	      this._onChange();
	    }
	    else { // Ctrl+\
	      this.format();
	      this._onChange();
	    }
	    handled = true;
	  }

	  if (handled) {
	    event.preventDefault();
	    event.stopPropagation();
	  }

	  this._updateCursorInfo();
	  this._emitSelectionChange();
	};

	/**
	 * Event handler for mousedown.
	 * @param {Event} event
	 * @private
	 */
	textmode._onMouseDown = function (event) {
	  this._updateCursorInfo();
	  this._emitSelectionChange();
	};

	/**
	 * Event handler for blur.
	 * @param {Event} event
	 * @private
	 */
	textmode._onBlur = function (event) {
	  this._updateCursorInfo();
	  this._emitSelectionChange();
	};

	/**
	 * Update the cursor info and the status bar, if presented
	 */
	textmode._updateCursorInfo = function () {
	  var me = this;
	  var line, col, count;

	  if (this.textarea) {
	    setTimeout(function() { //this to verify we get the most updated textarea cursor selection
	      var selectionRange = util.getInputSelection(me.textarea);
	      
	      if (selectionRange.startIndex !== selectionRange.endIndex) {
	        count = selectionRange.endIndex - selectionRange.startIndex;
	      }
	      
	      if (count && me.cursorInfo && me.cursorInfo.line === selectionRange.end.row && me.cursorInfo.column === selectionRange.end.column) {
	        line = selectionRange.start.row;
	        col = selectionRange.start.column;
	      } else {
	        line = selectionRange.end.row;
	        col = selectionRange.end.column;
	      }
	      
	      me.cursorInfo = {
	        line: line,
	        column: col,
	        count: count
	      }

	      if(me.options.statusBar) {
	        updateDisplay();
	      }
	    },0);
	    
	  } else if (this.aceEditor && this.curserInfoElements) {
	    var curserPos = this.aceEditor.getCursorPosition();
	    var selectedText = this.aceEditor.getSelectedText();

	    line = curserPos.row + 1;
	    col = curserPos.column + 1;
	    count = selectedText.length;

	    me.cursorInfo = {
	      line: line,
	      column: col,
	      count: count
	    }

	    if(this.options.statusBar) {
	      updateDisplay();
	    }
	  }

	  function updateDisplay() {

	    if (me.curserInfoElements.countVal.innerText !== count) {
	      me.curserInfoElements.countVal.innerText = count;
	      me.curserInfoElements.countVal.style.display = count ? 'inline' : 'none';
	      me.curserInfoElements.countLabel.style.display = count ? 'inline' : 'none';
	    }
	    me.curserInfoElements.lnVal.innerText = line;
	    me.curserInfoElements.colVal.innerText = col;
	  }
	};

	/**
	 * emits selection change callback, if given
	 * @private
	 */
	textmode._emitSelectionChange = function () {
	  if(this._selectionChangedHandler) {
	    var currentSelection = this.getTextSelection();
	    this._selectionChangedHandler(currentSelection.start, currentSelection.end, currentSelection.text);
	  }
	}

	/**
	 * Destroy the editor. Clean up DOM, event listeners, and web workers.
	 */
	textmode.destroy = function () {
	  // remove old ace editor
	  if (this.aceEditor) {
	    this.aceEditor.destroy();
	    this.aceEditor = null;
	  }

	  if (this.frame && this.container && this.frame.parentNode == this.container) {
	    this.container.removeChild(this.frame);
	  }

	  if (this.modeSwitcher) {
	    this.modeSwitcher.destroy();
	    this.modeSwitcher = null;
	  }

	  this.textarea = null;
	  
	  this._debouncedValidate = null;
	};

	/**
	 * Compact the code in the text editor
	 */
	textmode.compact = function () {
	  var json = this.get();
	  var text = JSON.stringify(json);
	  this.setText(text);
	};

	/**
	 * Format the code in the text editor
	 */
	textmode.format = function () {
	  var json = this.get();
	  var text = JSON.stringify(json, null, this.indentation);
	  this.setText(text);
	};

	/**
	 * Repair the code in the text editor
	 */
	textmode.repair = function () {
	  var text = this.getText();
	  var sanitizedText = util.sanitize(text);
	  this.setText(sanitizedText);
	};

	/**
	 * Set focus to the formatter
	 */
	textmode.focus = function () {
	  if (this.textarea) {
	    this.textarea.focus();
	  }
	  if (this.aceEditor) {
	    this.aceEditor.focus();
	  }
	};

	/**
	 * Resize the formatter
	 */
	textmode.resize = function () {
	  if (this.aceEditor) {
	    var force = false;
	    this.aceEditor.resize(force);
	  }
	};

	/**
	 * Set json data in the formatter
	 * @param {Object} json
	 */
	textmode.set = function(json) {
	  this.setText(JSON.stringify(json, null, this.indentation));
	};

	/**
	 * Get json data from the formatter
	 * @return {Object} json
	 */
	textmode.get = function() {
	  var text = this.getText();
	  var json;

	  try {
	    json = util.parse(text); // this can throw an error
	  }
	  catch (err) {
	    // try to sanitize json, replace JavaScript notation with JSON notation
	    text = util.sanitize(text);

	    // try to parse again
	    json = util.parse(text); // this can throw an error
	  }

	  return json;
	};

	/**
	 * Get the text contents of the editor
	 * @return {String} jsonText
	 */
	textmode.getText = function() {
	  if (this.textarea) {
	    return this.textarea.value;
	  }
	  if (this.aceEditor) {
	    return this.aceEditor.getValue();
	  }
	  return '';
	};

	/**
	 * Set the text contents of the editor
	 * @param {String} jsonText
	 */
	textmode.setText = function(jsonText) {
	  var text;

	  if (this.options.escapeUnicode === true) {
	    text = util.escapeUnicodeChars(jsonText);
	  }
	  else {
	    text = jsonText;
	  }

	  if (this.textarea) {
	    this.textarea.value = text;
	  }
	  if (this.aceEditor) {
	    // prevent emitting onChange events while setting new text
	    var originalOnChange = this.options.onChange;
	    this.options.onChange = null;

	    this.aceEditor.setValue(text, -1);

	    this.options.onChange = originalOnChange;
	  }
	  // validate JSON schema
	  this.validate();
	};

	/**
	 * Validate current JSON object against the configured JSON schema
	 * Throws an exception when no JSON schema is configured
	 */
	textmode.validate = function () {
	  // clear all current errors
	  if (this.dom.validationErrors) {
	    this.dom.validationErrors.parentNode.removeChild(this.dom.validationErrors);
	    this.dom.validationErrors = null;

	    this.content.style.marginBottom = '';
	    this.content.style.paddingBottom = '';
	  }

	  var doValidate = false;
	  var errors = [];
	  var json;
	  try {
	    json = this.get(); // this can fail when there is no valid json
	    doValidate = true;
	  }
	  catch (err) {
	    // no valid JSON, don't validate
	  }

	  // only validate the JSON when parsing the JSON succeeded
	  if (doValidate && this.validateSchema) {
	    var valid = this.validateSchema(json);
	    if (!valid) {
	      errors = this.validateSchema.errors.map(function (error) {
	        return util.improveSchemaError(error);
	      });
	    }
	  }

	  if (errors.length > 0) {  
	    // limit the number of displayed errors
	    var limit = errors.length > MAX_ERRORS;
	    if (limit) {
	      errors = errors.slice(0, MAX_ERRORS);
	      var hidden = this.validateSchema.errors.length - MAX_ERRORS;
	      errors.push('(' + hidden + ' more errors...)')
	    }

	    var validationErrors = document.createElement('div');
	    validationErrors.innerHTML = '<table class="jsoneditor-text-errors">' +
	        '<tbody>' +
	        errors.map(function (error) {
	          var message;
	          if (typeof error === 'string') {
	            message = '<td colspan="2"><pre>' + error + '</pre></td>';
	          }
	          else {
	            message = '<td>' + error.dataPath + '</td>' +
	                '<td>' + error.message + '</td>';
	          }

	          return '<tr><td><button class="jsoneditor-schema-error"></button></td>' + message + '</tr>'
	        }).join('') +
	        '</tbody>' +
	        '</table>';

	    this.dom.validationErrors = validationErrors;
	    this.dom.validationErrorsContainer.appendChild(validationErrors);

	    var height = validationErrors.clientHeight +
	        (this.dom.statusBar ? this.dom.statusBar.clientHeight : 0);
	    this.content.style.marginBottom = (-height) + 'px';
	    this.content.style.paddingBottom = height + 'px';
	  }

	  // update the height of the ace editor
	  if (this.aceEditor) {
	    var force = false;
	    this.aceEditor.resize(force);
	  }
	};

	/**
	 * Get the selection details
	 * @returns {{start:{row:Number, column:Number},end:{row:Number, column:Number},text:String}}
	 */
	textmode.getTextSelection = function () {
	  var selection = {};
	  if (this.textarea) {
	    var selectionRange = util.getInputSelection(this.textarea);

	    if (this.cursorInfo && this.cursorInfo.line === selectionRange.end.row && this.cursorInfo.column === selectionRange.end.column) {
	      //selection direction is bottom => up
	      selection.start = selectionRange.end;
	      selection.end = selectionRange.start;
	    } else {
	      selection = selectionRange;
	    }

	    return {
	      start: selection.start,
	      end: selection.end,
	      text: this.textarea.value.substring(selectionRange.startIndex, selectionRange.endIndex)
	    }
	  }

	  if (this.aceEditor) {
	    var aceSelection = this.aceEditor.getSelection();
	    var selectedText = this.aceEditor.getSelectedText();
	    var range = aceSelection.getRange();
	    var lead = aceSelection.getSelectionLead();

	    if (lead.row === range.end.row && lead.column === range.end.column) {
	      selection = range;
	    } else {
	      //selection direction is bottom => up
	      selection.start = range.end;
	      selection.end = range.start;
	    }
	    
	    return {
	      start: {
	        row: selection.start.row + 1,
	        column: selection.start.column + 1
	      },
	      end: {
	        row: selection.end.row + 1,
	        column: selection.end.column + 1
	      },
	      text: selectedText
	    };
	  }
	};

	/**
	 * Callback registraion for selection change
	 * @param {selectionCallback} callback
	 * 
	 * @callback selectionCallback
	 * @param {{row:Number, column:Number}} startPos selection start position
	 * @param {{row:Number, column:Number}} endPos selected end position
	 * @param {String} text selected text
	 */
	textmode.onTextSelectionChange = function (callback) {
	  if (typeof callback === 'function') {
	    this._selectionChangedHandler = util.debounce(callback, this.DEBOUNCE_INTERVAL);
	  }
	};

	/**
	 * Set selection on editor's text
	 * @param {{row:Number, column:Number}} startPos selection start position
	 * @param {{row:Number, column:Number}} endPos selected end position
	 */
	textmode.setTextSelection = function (startPos, endPos) {

	  if (!startPos || !endPos) return;

	  if (this.textarea) {
	    var startIndex = util.getIndexForPosition(this.textarea, startPos.row, startPos.column);
	    var endIndex = util.getIndexForPosition(this.textarea, endPos.row, endPos.column);
	    if (startIndex > -1 && endIndex  > -1) {
	      if (this.textarea.setSelectionRange) { 
	        this.textarea.focus();
	        this.textarea.setSelectionRange(startIndex, endIndex);
	      } else if (this.textarea.createTextRange) { // IE < 9
	        var range = this.textarea.createTextRange();
	        range.collapse(true);
	        range.moveEnd('character', endIndex);
	        range.moveStart('character', startIndex);
	        range.select();
	      }
	    }
	  } else if (this.aceEditor) {
	    var range = {
	      start:{
	        row: startPos.row - 1,
	        column: startPos.column - 1
	      },
	      end:{
	        row: endPos.row - 1,
	        column: endPos.column - 1
	      }
	    };
	    this.aceEditor.selection.setRange(range);
	  }
	};

	// define modes
	module.exports = [
	  {
	    mode: 'text',
	    mixin: textmode,
	    data: 'text',
	    load: textmode.format
	  },
	  {
	    mode: 'code',
	    mixin: textmode,
	    data: 'text',
	    load: textmode.format
	  }
	];


/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	var ace
	if (window.ace) {
	  // use the already loaded instance of Ace
	  ace = window.ace
	}
	else {
	  try {
	    // load brace
	    ace = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"brace\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

	    // load required Ace plugins
	    __webpack_require__(17);
	    __webpack_require__(19);
	  }
	  catch (err) {
	    // failed to load brace (can be minimalist bundle).
	    // No worries, the editor will fall back to plain text if needed.
	  }
	}

	module.exports = ace;


/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	ace.define("ace/mode/json_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"], function(acequire, exports, module) {
	"use strict";

	var oop = acequire("../lib/oop");
	var TextHighlightRules = acequire("./text_highlight_rules").TextHighlightRules;

	var JsonHighlightRules = function() {
	    this.$rules = {
	        "start" : [
	            {
	                token : "variable", // single line
	                regex : '["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]\\s*(?=:)'
	            }, {
	                token : "string", // single line
	                regex : '"',
	                next  : "string"
	            }, {
	                token : "constant.numeric", // hex
	                regex : "0[xX][0-9a-fA-F]+\\b"
	            }, {
	                token : "constant.numeric", // float
	                regex : "[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"
	            }, {
	                token : "constant.language.boolean",
	                regex : "(?:true|false)\\b"
	            }, {
	                token : "text", // single quoted strings are not allowed
	                regex : "['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"
	            }, {
	                token : "comment", // comments are not allowed, but who cares?
	                regex : "\\/\\/.*$"
	            }, {
	                token : "comment.start", // comments are not allowed, but who cares?
	                regex : "\\/\\*",
	                next  : "comment"
	            }, {
	                token : "paren.lparen",
	                regex : "[[({]"
	            }, {
	                token : "paren.rparen",
	                regex : "[\\])}]"
	            }, {
	                token : "text",
	                regex : "\\s+"
	            }
	        ],
	        "string" : [
	            {
	                token : "constant.language.escape",
	                regex : /\\(?:x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|["\\\/bfnrt])/
	            }, {
	                token : "string",
	                regex : '"|$',
	                next  : "start"
	            }, {
	                defaultToken : "string"
	            }
	        ],
	        "comment" : [
	            {
	                token : "comment.end", // comments are not allowed, but who cares?
	                regex : "\\*\\/",
	                next  : "start"
	            }, {
	                defaultToken: "comment"
	            }
	        ]
	    };
	    
	};

	oop.inherits(JsonHighlightRules, TextHighlightRules);

	exports.JsonHighlightRules = JsonHighlightRules;
	});

	ace.define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"], function(acequire, exports, module) {
	"use strict";

	var Range = acequire("../range").Range;

	var MatchingBraceOutdent = function() {};

	(function() {

	    this.checkOutdent = function(line, input) {
	        if (! /^\s+$/.test(line))
	            return false;

	        return /^\s*\}/.test(input);
	    };

	    this.autoOutdent = function(doc, row) {
	        var line = doc.getLine(row);
	        var match = line.match(/^(\s*\})/);

	        if (!match) return 0;

	        var column = match[1].length;
	        var openBracePos = doc.findMatchingBracket({row: row, column: column});

	        if (!openBracePos || openBracePos.row == row) return 0;

	        var indent = this.$getIndent(doc.getLine(openBracePos.row));
	        doc.replace(new Range(row, 0, row, column-1), indent);
	    };

	    this.$getIndent = function(line) {
	        return line.match(/^\s*/)[0];
	    };

	}).call(MatchingBraceOutdent.prototype);

	exports.MatchingBraceOutdent = MatchingBraceOutdent;
	});

	ace.define("ace/mode/folding/cstyle",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"], function(acequire, exports, module) {
	"use strict";

	var oop = acequire("../../lib/oop");
	var Range = acequire("../../range").Range;
	var BaseFoldMode = acequire("./fold_mode").FoldMode;

	var FoldMode = exports.FoldMode = function(commentRegex) {
	    if (commentRegex) {
	        this.foldingStartMarker = new RegExp(
	            this.foldingStartMarker.source.replace(/\|[^|]*?$/, "|" + commentRegex.start)
	        );
	        this.foldingStopMarker = new RegExp(
	            this.foldingStopMarker.source.replace(/\|[^|]*?$/, "|" + commentRegex.end)
	        );
	    }
	};
	oop.inherits(FoldMode, BaseFoldMode);

	(function() {
	    
	    this.foldingStartMarker = /([\{\[\(])[^\}\]\)]*$|^\s*(\/\*)/;
	    this.foldingStopMarker = /^[^\[\{\(]*([\}\]\)])|^[\s\*]*(\*\/)/;
	    this.singleLineBlockCommentRe= /^\s*(\/\*).*\*\/\s*$/;
	    this.tripleStarBlockCommentRe = /^\s*(\/\*\*\*).*\*\/\s*$/;
	    this.startRegionRe = /^\s*(\/\*|\/\/)#?region\b/;
	    this._getFoldWidgetBase = this.getFoldWidget;
	    this.getFoldWidget = function(session, foldStyle, row) {
	        var line = session.getLine(row);
	    
	        if (this.singleLineBlockCommentRe.test(line)) {
	            if (!this.startRegionRe.test(line) && !this.tripleStarBlockCommentRe.test(line))
	                return "";
	        }
	    
	        var fw = this._getFoldWidgetBase(session, foldStyle, row);
	    
	        if (!fw && this.startRegionRe.test(line))
	            return "start"; // lineCommentRegionStart
	    
	        return fw;
	    };

	    this.getFoldWidgetRange = function(session, foldStyle, row, forceMultiline) {
	        var line = session.getLine(row);
	        
	        if (this.startRegionRe.test(line))
	            return this.getCommentRegionBlock(session, line, row);
	        
	        var match = line.match(this.foldingStartMarker);
	        if (match) {
	            var i = match.index;

	            if (match[1])
	                return this.openingBracketBlock(session, match[1], row, i);
	                
	            var range = session.getCommentFoldRange(row, i + match[0].length, 1);
	            
	            if (range && !range.isMultiLine()) {
	                if (forceMultiline) {
	                    range = this.getSectionRange(session, row);
	                } else if (foldStyle != "all")
	                    range = null;
	            }
	            
	            return range;
	        }

	        if (foldStyle === "markbegin")
	            return;

	        var match = line.match(this.foldingStopMarker);
	        if (match) {
	            var i = match.index + match[0].length;

	            if (match[1])
	                return this.closingBracketBlock(session, match[1], row, i);

	            return session.getCommentFoldRange(row, i, -1);
	        }
	    };
	    
	    this.getSectionRange = function(session, row) {
	        var line = session.getLine(row);
	        var startIndent = line.search(/\S/);
	        var startRow = row;
	        var startColumn = line.length;
	        row = row + 1;
	        var endRow = row;
	        var maxRow = session.getLength();
	        while (++row < maxRow) {
	            line = session.getLine(row);
	            var indent = line.search(/\S/);
	            if (indent === -1)
	                continue;
	            if  (startIndent > indent)
	                break;
	            var subRange = this.getFoldWidgetRange(session, "all", row);
	            
	            if (subRange) {
	                if (subRange.start.row <= startRow) {
	                    break;
	                } else if (subRange.isMultiLine()) {
	                    row = subRange.end.row;
	                } else if (startIndent == indent) {
	                    break;
	                }
	            }
	            endRow = row;
	        }
	        
	        return new Range(startRow, startColumn, endRow, session.getLine(endRow).length);
	    };
	    this.getCommentRegionBlock = function(session, line, row) {
	        var startColumn = line.search(/\s*$/);
	        var maxRow = session.getLength();
	        var startRow = row;
	        
	        var re = /^\s*(?:\/\*|\/\/|--)#?(end)?region\b/;
	        var depth = 1;
	        while (++row < maxRow) {
	            line = session.getLine(row);
	            var m = re.exec(line);
	            if (!m) continue;
	            if (m[1]) depth--;
	            else depth++;

	            if (!depth) break;
	        }

	        var endRow = row;
	        if (endRow > startRow) {
	            return new Range(startRow, startColumn, endRow, line.length);
	        }
	    };

	}).call(FoldMode.prototype);

	});

	ace.define("ace/mode/json",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/json_highlight_rules","ace/mode/matching_brace_outdent","ace/mode/behaviour/cstyle","ace/mode/folding/cstyle","ace/worker/worker_client"], function(acequire, exports, module) {
	"use strict";

	var oop = acequire("../lib/oop");
	var TextMode = acequire("./text").Mode;
	var HighlightRules = acequire("./json_highlight_rules").JsonHighlightRules;
	var MatchingBraceOutdent = acequire("./matching_brace_outdent").MatchingBraceOutdent;
	var CstyleBehaviour = acequire("./behaviour/cstyle").CstyleBehaviour;
	var CStyleFoldMode = acequire("./folding/cstyle").FoldMode;
	var WorkerClient = acequire("../worker/worker_client").WorkerClient;

	var Mode = function() {
	    this.HighlightRules = HighlightRules;
	    this.$outdent = new MatchingBraceOutdent();
	    this.$behaviour = new CstyleBehaviour();
	    this.foldingRules = new CStyleFoldMode();
	};
	oop.inherits(Mode, TextMode);

	(function() {

	    this.getNextLineIndent = function(state, line, tab) {
	        var indent = this.$getIndent(line);

	        if (state == "start") {
	            var match = line.match(/^.*[\{\(\[]\s*$/);
	            if (match) {
	                indent += tab;
	            }
	        }

	        return indent;
	    };

	    this.checkOutdent = function(state, line, input) {
	        return this.$outdent.checkOutdent(line, input);
	    };

	    this.autoOutdent = function(state, doc, row) {
	        this.$outdent.autoOutdent(doc, row);
	    };

	    this.createWorker = function(session) {
	        var worker = new WorkerClient(["ace"], __webpack_require__(18), "JsonWorker");
	        worker.attachToDocument(session.getDocument());

	        worker.on("annotate", function(e) {
	            session.setAnnotations(e.data);
	        });

	        worker.on("terminate", function() {
	            session.clearAnnotations();
	        });

	        return worker;
	    };


	    this.$id = "ace/mode/json";
	}).call(Mode.prototype);

	exports.Mode = Mode;
	});


/***/ },
/* 18 */
/***/ function(module, exports) {

	module.exports.id = 'ace/mode/json_worker';
	module.exports.src = "\"no use strict\";!function(window){function resolveModuleId(id,paths){for(var testPath=id,tail=\"\";testPath;){var alias=paths[testPath];if(\"string\"==typeof alias)return alias+tail;if(alias)return alias.location.replace(/\\/*$/,\"/\")+(tail||alias.main||alias.name);if(alias===!1)return\"\";var i=testPath.lastIndexOf(\"/\");if(-1===i)break;tail=testPath.substr(i)+tail,testPath=testPath.slice(0,i)}return id}if(!(void 0!==window.window&&window.document||window.acequire&&window.define)){window.console||(window.console=function(){var msgs=Array.prototype.slice.call(arguments,0);postMessage({type:\"log\",data:msgs})},window.console.error=window.console.warn=window.console.log=window.console.trace=window.console),window.window=window,window.ace=window,window.onerror=function(message,file,line,col,err){postMessage({type:\"error\",data:{message:message,data:err.data,file:file,line:line,col:col,stack:err.stack}})},window.normalizeModule=function(parentId,moduleName){if(-1!==moduleName.indexOf(\"!\")){var chunks=moduleName.split(\"!\");return window.normalizeModule(parentId,chunks[0])+\"!\"+window.normalizeModule(parentId,chunks[1])}if(\".\"==moduleName.charAt(0)){var base=parentId.split(\"/\").slice(0,-1).join(\"/\");for(moduleName=(base?base+\"/\":\"\")+moduleName;-1!==moduleName.indexOf(\".\")&&previous!=moduleName;){var previous=moduleName;moduleName=moduleName.replace(/^\\.\\//,\"\").replace(/\\/\\.\\//,\"/\").replace(/[^\\/]+\\/\\.\\.\\//,\"\")}}return moduleName},window.acequire=function acequire(parentId,id){if(id||(id=parentId,parentId=null),!id.charAt)throw Error(\"worker.js acequire() accepts only (parentId, id) as arguments\");id=window.normalizeModule(parentId,id);var module=window.acequire.modules[id];if(module)return module.initialized||(module.initialized=!0,module.exports=module.factory().exports),module.exports;if(!window.acequire.tlns)return console.log(\"unable to load \"+id);var path=resolveModuleId(id,window.acequire.tlns);return\".js\"!=path.slice(-3)&&(path+=\".js\"),window.acequire.id=id,window.acequire.modules[id]={},importScripts(path),window.acequire(parentId,id)},window.acequire.modules={},window.acequire.tlns={},window.define=function(id,deps,factory){if(2==arguments.length?(factory=deps,\"string\"!=typeof id&&(deps=id,id=window.acequire.id)):1==arguments.length&&(factory=id,deps=[],id=window.acequire.id),\"function\"!=typeof factory)return window.acequire.modules[id]={exports:factory,initialized:!0},void 0;deps.length||(deps=[\"require\",\"exports\",\"module\"]);var req=function(childId){return window.acequire(id,childId)};window.acequire.modules[id]={exports:{},factory:function(){var module=this,returnExports=factory.apply(this,deps.map(function(dep){switch(dep){case\"require\":return req;case\"exports\":return module.exports;case\"module\":return module;default:return req(dep)}}));return returnExports&&(module.exports=returnExports),module}}},window.define.amd={},acequire.tlns={},window.initBaseUrls=function(topLevelNamespaces){for(var i in topLevelNamespaces)acequire.tlns[i]=topLevelNamespaces[i]},window.initSender=function(){var EventEmitter=window.acequire(\"ace/lib/event_emitter\").EventEmitter,oop=window.acequire(\"ace/lib/oop\"),Sender=function(){};return function(){oop.implement(this,EventEmitter),this.callback=function(data,callbackId){postMessage({type:\"call\",id:callbackId,data:data})},this.emit=function(name,data){postMessage({type:\"event\",name:name,data:data})}}.call(Sender.prototype),new Sender};var main=window.main=null,sender=window.sender=null;window.onmessage=function(e){var msg=e.data;if(msg.event&&sender)sender._signal(msg.event,msg.data);else if(msg.command)if(main[msg.command])main[msg.command].apply(main,msg.args);else{if(!window[msg.command])throw Error(\"Unknown command:\"+msg.command);window[msg.command].apply(window,msg.args)}else if(msg.init){window.initBaseUrls(msg.tlns),acequire(\"ace/lib/es5-shim\"),sender=window.sender=window.initSender();var clazz=acequire(msg.module)[msg.classname];main=window.main=new clazz(sender)}}}}(this),ace.define(\"ace/lib/oop\",[\"require\",\"exports\",\"module\"],function(acequire,exports){\"use strict\";exports.inherits=function(ctor,superCtor){ctor.super_=superCtor,ctor.prototype=Object.create(superCtor.prototype,{constructor:{value:ctor,enumerable:!1,writable:!0,configurable:!0}})},exports.mixin=function(obj,mixin){for(var key in mixin)obj[key]=mixin[key];return obj},exports.implement=function(proto,mixin){exports.mixin(proto,mixin)}}),ace.define(\"ace/range\",[\"require\",\"exports\",\"module\"],function(acequire,exports){\"use strict\";var comparePoints=function(p1,p2){return p1.row-p2.row||p1.column-p2.column},Range=function(startRow,startColumn,endRow,endColumn){this.start={row:startRow,column:startColumn},this.end={row:endRow,column:endColumn}};(function(){this.isEqual=function(range){return this.start.row===range.start.row&&this.end.row===range.end.row&&this.start.column===range.start.column&&this.end.column===range.end.column},this.toString=function(){return\"Range: [\"+this.start.row+\"/\"+this.start.column+\"] -> [\"+this.end.row+\"/\"+this.end.column+\"]\"},this.contains=function(row,column){return 0==this.compare(row,column)},this.compareRange=function(range){var cmp,end=range.end,start=range.start;return cmp=this.compare(end.row,end.column),1==cmp?(cmp=this.compare(start.row,start.column),1==cmp?2:0==cmp?1:0):-1==cmp?-2:(cmp=this.compare(start.row,start.column),-1==cmp?-1:1==cmp?42:0)},this.comparePoint=function(p){return this.compare(p.row,p.column)},this.containsRange=function(range){return 0==this.comparePoint(range.start)&&0==this.comparePoint(range.end)},this.intersects=function(range){var cmp=this.compareRange(range);return-1==cmp||0==cmp||1==cmp},this.isEnd=function(row,column){return this.end.row==row&&this.end.column==column},this.isStart=function(row,column){return this.start.row==row&&this.start.column==column},this.setStart=function(row,column){\"object\"==typeof row?(this.start.column=row.column,this.start.row=row.row):(this.start.row=row,this.start.column=column)},this.setEnd=function(row,column){\"object\"==typeof row?(this.end.column=row.column,this.end.row=row.row):(this.end.row=row,this.end.column=column)},this.inside=function(row,column){return 0==this.compare(row,column)?this.isEnd(row,column)||this.isStart(row,column)?!1:!0:!1},this.insideStart=function(row,column){return 0==this.compare(row,column)?this.isEnd(row,column)?!1:!0:!1},this.insideEnd=function(row,column){return 0==this.compare(row,column)?this.isStart(row,column)?!1:!0:!1},this.compare=function(row,column){return this.isMultiLine()||row!==this.start.row?this.start.row>row?-1:row>this.end.row?1:this.start.row===row?column>=this.start.column?0:-1:this.end.row===row?this.end.column>=column?0:1:0:this.start.column>column?-1:column>this.end.column?1:0},this.compareStart=function(row,column){return this.start.row==row&&this.start.column==column?-1:this.compare(row,column)},this.compareEnd=function(row,column){return this.end.row==row&&this.end.column==column?1:this.compare(row,column)},this.compareInside=function(row,column){return this.end.row==row&&this.end.column==column?1:this.start.row==row&&this.start.column==column?-1:this.compare(row,column)},this.clipRows=function(firstRow,lastRow){if(this.end.row>lastRow)var end={row:lastRow+1,column:0};else if(firstRow>this.end.row)var end={row:firstRow,column:0};if(this.start.row>lastRow)var start={row:lastRow+1,column:0};else if(firstRow>this.start.row)var start={row:firstRow,column:0};return Range.fromPoints(start||this.start,end||this.end)},this.extend=function(row,column){var cmp=this.compare(row,column);if(0==cmp)return this;if(-1==cmp)var start={row:row,column:column};else var end={row:row,column:column};return Range.fromPoints(start||this.start,end||this.end)},this.isEmpty=function(){return this.start.row===this.end.row&&this.start.column===this.end.column},this.isMultiLine=function(){return this.start.row!==this.end.row},this.clone=function(){return Range.fromPoints(this.start,this.end)},this.collapseRows=function(){return 0==this.end.column?new Range(this.start.row,0,Math.max(this.start.row,this.end.row-1),0):new Range(this.start.row,0,this.end.row,0)},this.toScreenRange=function(session){var screenPosStart=session.documentToScreenPosition(this.start),screenPosEnd=session.documentToScreenPosition(this.end);return new Range(screenPosStart.row,screenPosStart.column,screenPosEnd.row,screenPosEnd.column)},this.moveBy=function(row,column){this.start.row+=row,this.start.column+=column,this.end.row+=row,this.end.column+=column}}).call(Range.prototype),Range.fromPoints=function(start,end){return new Range(start.row,start.column,end.row,end.column)},Range.comparePoints=comparePoints,Range.comparePoints=function(p1,p2){return p1.row-p2.row||p1.column-p2.column},exports.Range=Range}),ace.define(\"ace/apply_delta\",[\"require\",\"exports\",\"module\"],function(acequire,exports){\"use strict\";exports.applyDelta=function(docLines,delta){var row=delta.start.row,startColumn=delta.start.column,line=docLines[row]||\"\";switch(delta.action){case\"insert\":var lines=delta.lines;if(1===lines.length)docLines[row]=line.substring(0,startColumn)+delta.lines[0]+line.substring(startColumn);else{var args=[row,1].concat(delta.lines);docLines.splice.apply(docLines,args),docLines[row]=line.substring(0,startColumn)+docLines[row],docLines[row+delta.lines.length-1]+=line.substring(startColumn)}break;case\"remove\":var endColumn=delta.end.column,endRow=delta.end.row;row===endRow?docLines[row]=line.substring(0,startColumn)+line.substring(endColumn):docLines.splice(row,endRow-row+1,line.substring(0,startColumn)+docLines[endRow].substring(endColumn))}}}),ace.define(\"ace/lib/event_emitter\",[\"require\",\"exports\",\"module\"],function(acequire,exports){\"use strict\";var EventEmitter={},stopPropagation=function(){this.propagationStopped=!0},preventDefault=function(){this.defaultPrevented=!0};EventEmitter._emit=EventEmitter._dispatchEvent=function(eventName,e){this._eventRegistry||(this._eventRegistry={}),this._defaultHandlers||(this._defaultHandlers={});var listeners=this._eventRegistry[eventName]||[],defaultHandler=this._defaultHandlers[eventName];if(listeners.length||defaultHandler){\"object\"==typeof e&&e||(e={}),e.type||(e.type=eventName),e.stopPropagation||(e.stopPropagation=stopPropagation),e.preventDefault||(e.preventDefault=preventDefault),listeners=listeners.slice();for(var i=0;listeners.length>i&&(listeners[i](e,this),!e.propagationStopped);i++);return defaultHandler&&!e.defaultPrevented?defaultHandler(e,this):void 0}},EventEmitter._signal=function(eventName,e){var listeners=(this._eventRegistry||{})[eventName];if(listeners){listeners=listeners.slice();for(var i=0;listeners.length>i;i++)listeners[i](e,this)}},EventEmitter.once=function(eventName,callback){var _self=this;callback&&this.addEventListener(eventName,function newCallback(){_self.removeEventListener(eventName,newCallback),callback.apply(null,arguments)})},EventEmitter.setDefaultHandler=function(eventName,callback){var handlers=this._defaultHandlers;if(handlers||(handlers=this._defaultHandlers={_disabled_:{}}),handlers[eventName]){var old=handlers[eventName],disabled=handlers._disabled_[eventName];disabled||(handlers._disabled_[eventName]=disabled=[]),disabled.push(old);var i=disabled.indexOf(callback);-1!=i&&disabled.splice(i,1)}handlers[eventName]=callback},EventEmitter.removeDefaultHandler=function(eventName,callback){var handlers=this._defaultHandlers;if(handlers){var disabled=handlers._disabled_[eventName];if(handlers[eventName]==callback)handlers[eventName],disabled&&this.setDefaultHandler(eventName,disabled.pop());else if(disabled){var i=disabled.indexOf(callback);-1!=i&&disabled.splice(i,1)}}},EventEmitter.on=EventEmitter.addEventListener=function(eventName,callback,capturing){this._eventRegistry=this._eventRegistry||{};var listeners=this._eventRegistry[eventName];return listeners||(listeners=this._eventRegistry[eventName]=[]),-1==listeners.indexOf(callback)&&listeners[capturing?\"unshift\":\"push\"](callback),callback},EventEmitter.off=EventEmitter.removeListener=EventEmitter.removeEventListener=function(eventName,callback){this._eventRegistry=this._eventRegistry||{};var listeners=this._eventRegistry[eventName];if(listeners){var index=listeners.indexOf(callback);-1!==index&&listeners.splice(index,1)}},EventEmitter.removeAllListeners=function(eventName){this._eventRegistry&&(this._eventRegistry[eventName]=[])},exports.EventEmitter=EventEmitter}),ace.define(\"ace/anchor\",[\"require\",\"exports\",\"module\",\"ace/lib/oop\",\"ace/lib/event_emitter\"],function(acequire,exports){\"use strict\";var oop=acequire(\"./lib/oop\"),EventEmitter=acequire(\"./lib/event_emitter\").EventEmitter,Anchor=exports.Anchor=function(doc,row,column){this.$onChange=this.onChange.bind(this),this.attach(doc),column===void 0?this.setPosition(row.row,row.column):this.setPosition(row,column)};(function(){function $pointsInOrder(point1,point2,equalPointsInOrder){var bColIsAfter=equalPointsInOrder?point1.column<=point2.column:point1.column<point2.column;return point1.row<point2.row||point1.row==point2.row&&bColIsAfter}function $getTransformedPoint(delta,point,moveIfEqual){var deltaIsInsert=\"insert\"==delta.action,deltaRowShift=(deltaIsInsert?1:-1)*(delta.end.row-delta.start.row),deltaColShift=(deltaIsInsert?1:-1)*(delta.end.column-delta.start.column),deltaStart=delta.start,deltaEnd=deltaIsInsert?deltaStart:delta.end;return $pointsInOrder(point,deltaStart,moveIfEqual)?{row:point.row,column:point.column}:$pointsInOrder(deltaEnd,point,!moveIfEqual)?{row:point.row+deltaRowShift,column:point.column+(point.row==deltaEnd.row?deltaColShift:0)}:{row:deltaStart.row,column:deltaStart.column}}oop.implement(this,EventEmitter),this.getPosition=function(){return this.$clipPositionToDocument(this.row,this.column)},this.getDocument=function(){return this.document},this.$insertRight=!1,this.onChange=function(delta){if(!(delta.start.row==delta.end.row&&delta.start.row!=this.row||delta.start.row>this.row)){var point=$getTransformedPoint(delta,{row:this.row,column:this.column},this.$insertRight);this.setPosition(point.row,point.column,!0)}},this.setPosition=function(row,column,noClip){var pos;if(pos=noClip?{row:row,column:column}:this.$clipPositionToDocument(row,column),this.row!=pos.row||this.column!=pos.column){var old={row:this.row,column:this.column};this.row=pos.row,this.column=pos.column,this._signal(\"change\",{old:old,value:pos})}},this.detach=function(){this.document.removeEventListener(\"change\",this.$onChange)},this.attach=function(doc){this.document=doc||this.document,this.document.on(\"change\",this.$onChange)},this.$clipPositionToDocument=function(row,column){var pos={};return row>=this.document.getLength()?(pos.row=Math.max(0,this.document.getLength()-1),pos.column=this.document.getLine(pos.row).length):0>row?(pos.row=0,pos.column=0):(pos.row=row,pos.column=Math.min(this.document.getLine(pos.row).length,Math.max(0,column))),0>column&&(pos.column=0),pos}}).call(Anchor.prototype)}),ace.define(\"ace/document\",[\"require\",\"exports\",\"module\",\"ace/lib/oop\",\"ace/apply_delta\",\"ace/lib/event_emitter\",\"ace/range\",\"ace/anchor\"],function(acequire,exports){\"use strict\";var oop=acequire(\"./lib/oop\"),applyDelta=acequire(\"./apply_delta\").applyDelta,EventEmitter=acequire(\"./lib/event_emitter\").EventEmitter,Range=acequire(\"./range\").Range,Anchor=acequire(\"./anchor\").Anchor,Document=function(textOrLines){this.$lines=[\"\"],0===textOrLines.length?this.$lines=[\"\"]:Array.isArray(textOrLines)?this.insertMergedLines({row:0,column:0},textOrLines):this.insert({row:0,column:0},textOrLines)};(function(){oop.implement(this,EventEmitter),this.setValue=function(text){var len=this.getLength()-1;this.remove(new Range(0,0,len,this.getLine(len).length)),this.insert({row:0,column:0},text)},this.getValue=function(){return this.getAllLines().join(this.getNewLineCharacter())},this.createAnchor=function(row,column){return new Anchor(this,row,column)},this.$split=0===\"aaa\".split(/a/).length?function(text){return text.replace(/\\r\\n|\\r/g,\"\\n\").split(\"\\n\")}:function(text){return text.split(/\\r\\n|\\r|\\n/)},this.$detectNewLine=function(text){var match=text.match(/^.*?(\\r\\n|\\r|\\n)/m);this.$autoNewLine=match?match[1]:\"\\n\",this._signal(\"changeNewLineMode\")},this.getNewLineCharacter=function(){switch(this.$newLineMode){case\"windows\":return\"\\r\\n\";case\"unix\":return\"\\n\";default:return this.$autoNewLine||\"\\n\"}},this.$autoNewLine=\"\",this.$newLineMode=\"auto\",this.setNewLineMode=function(newLineMode){this.$newLineMode!==newLineMode&&(this.$newLineMode=newLineMode,this._signal(\"changeNewLineMode\"))},this.getNewLineMode=function(){return this.$newLineMode},this.isNewLine=function(text){return\"\\r\\n\"==text||\"\\r\"==text||\"\\n\"==text},this.getLine=function(row){return this.$lines[row]||\"\"},this.getLines=function(firstRow,lastRow){return this.$lines.slice(firstRow,lastRow+1)},this.getAllLines=function(){return this.getLines(0,this.getLength())},this.getLength=function(){return this.$lines.length},this.getTextRange=function(range){return this.getLinesForRange(range).join(this.getNewLineCharacter())},this.getLinesForRange=function(range){var lines;if(range.start.row===range.end.row)lines=[this.getLine(range.start.row).substring(range.start.column,range.end.column)];else{lines=this.getLines(range.start.row,range.end.row),lines[0]=(lines[0]||\"\").substring(range.start.column);var l=lines.length-1;range.end.row-range.start.row==l&&(lines[l]=lines[l].substring(0,range.end.column))}return lines},this.insertLines=function(row,lines){return console.warn(\"Use of document.insertLines is deprecated. Use the insertFullLines method instead.\"),this.insertFullLines(row,lines)},this.removeLines=function(firstRow,lastRow){return console.warn(\"Use of document.removeLines is deprecated. Use the removeFullLines method instead.\"),this.removeFullLines(firstRow,lastRow)},this.insertNewLine=function(position){return console.warn(\"Use of document.insertNewLine is deprecated. Use insertMergedLines(position, ['', '']) instead.\"),this.insertMergedLines(position,[\"\",\"\"])},this.insert=function(position,text){return 1>=this.getLength()&&this.$detectNewLine(text),this.insertMergedLines(position,this.$split(text))},this.insertInLine=function(position,text){var start=this.clippedPos(position.row,position.column),end=this.pos(position.row,position.column+text.length);return this.applyDelta({start:start,end:end,action:\"insert\",lines:[text]},!0),this.clonePos(end)},this.clippedPos=function(row,column){var length=this.getLength();void 0===row?row=length:0>row?row=0:row>=length&&(row=length-1,column=void 0);var line=this.getLine(row);return void 0==column&&(column=line.length),column=Math.min(Math.max(column,0),line.length),{row:row,column:column}},this.clonePos=function(pos){return{row:pos.row,column:pos.column}},this.pos=function(row,column){return{row:row,column:column}},this.$clipPosition=function(position){var length=this.getLength();return position.row>=length?(position.row=Math.max(0,length-1),position.column=this.getLine(length-1).length):(position.row=Math.max(0,position.row),position.column=Math.min(Math.max(position.column,0),this.getLine(position.row).length)),position},this.insertFullLines=function(row,lines){row=Math.min(Math.max(row,0),this.getLength());var column=0;this.getLength()>row?(lines=lines.concat([\"\"]),column=0):(lines=[\"\"].concat(lines),row--,column=this.$lines[row].length),this.insertMergedLines({row:row,column:column},lines)},this.insertMergedLines=function(position,lines){var start=this.clippedPos(position.row,position.column),end={row:start.row+lines.length-1,column:(1==lines.length?start.column:0)+lines[lines.length-1].length};return this.applyDelta({start:start,end:end,action:\"insert\",lines:lines}),this.clonePos(end)},this.remove=function(range){var start=this.clippedPos(range.start.row,range.start.column),end=this.clippedPos(range.end.row,range.end.column);return this.applyDelta({start:start,end:end,action:\"remove\",lines:this.getLinesForRange({start:start,end:end})}),this.clonePos(start)},this.removeInLine=function(row,startColumn,endColumn){var start=this.clippedPos(row,startColumn),end=this.clippedPos(row,endColumn);return this.applyDelta({start:start,end:end,action:\"remove\",lines:this.getLinesForRange({start:start,end:end})},!0),this.clonePos(start)},this.removeFullLines=function(firstRow,lastRow){firstRow=Math.min(Math.max(0,firstRow),this.getLength()-1),lastRow=Math.min(Math.max(0,lastRow),this.getLength()-1);var deleteFirstNewLine=lastRow==this.getLength()-1&&firstRow>0,deleteLastNewLine=this.getLength()-1>lastRow,startRow=deleteFirstNewLine?firstRow-1:firstRow,startCol=deleteFirstNewLine?this.getLine(startRow).length:0,endRow=deleteLastNewLine?lastRow+1:lastRow,endCol=deleteLastNewLine?0:this.getLine(endRow).length,range=new Range(startRow,startCol,endRow,endCol),deletedLines=this.$lines.slice(firstRow,lastRow+1);return this.applyDelta({start:range.start,end:range.end,action:\"remove\",lines:this.getLinesForRange(range)}),deletedLines},this.removeNewLine=function(row){this.getLength()-1>row&&row>=0&&this.applyDelta({start:this.pos(row,this.getLine(row).length),end:this.pos(row+1,0),action:\"remove\",lines:[\"\",\"\"]})},this.replace=function(range,text){if(range instanceof Range||(range=Range.fromPoints(range.start,range.end)),0===text.length&&range.isEmpty())return range.start;if(text==this.getTextRange(range))return range.end;this.remove(range);var end;return end=text?this.insert(range.start,text):range.start},this.applyDeltas=function(deltas){for(var i=0;deltas.length>i;i++)this.applyDelta(deltas[i])},this.revertDeltas=function(deltas){for(var i=deltas.length-1;i>=0;i--)this.revertDelta(deltas[i])},this.applyDelta=function(delta,doNotValidate){var isInsert=\"insert\"==delta.action;(isInsert?1>=delta.lines.length&&!delta.lines[0]:!Range.comparePoints(delta.start,delta.end))||(isInsert&&delta.lines.length>2e4&&this.$splitAndapplyLargeDelta(delta,2e4),applyDelta(this.$lines,delta,doNotValidate),this._signal(\"change\",delta))},this.$splitAndapplyLargeDelta=function(delta,MAX){for(var lines=delta.lines,l=lines.length,row=delta.start.row,column=delta.start.column,from=0,to=0;;){from=to,to+=MAX-1;var chunk=lines.slice(from,to);if(to>l){delta.lines=chunk,delta.start.row=row+from,delta.start.column=column;break}chunk.push(\"\"),this.applyDelta({start:this.pos(row+from,column),end:this.pos(row+to,column=0),action:delta.action,lines:chunk},!0)}},this.revertDelta=function(delta){this.applyDelta({start:this.clonePos(delta.start),end:this.clonePos(delta.end),action:\"insert\"==delta.action?\"remove\":\"insert\",lines:delta.lines.slice()})},this.indexToPosition=function(index,startRow){for(var lines=this.$lines||this.getAllLines(),newlineLength=this.getNewLineCharacter().length,i=startRow||0,l=lines.length;l>i;i++)if(index-=lines[i].length+newlineLength,0>index)return{row:i,column:index+lines[i].length+newlineLength};return{row:l-1,column:lines[l-1].length}},this.positionToIndex=function(pos,startRow){for(var lines=this.$lines||this.getAllLines(),newlineLength=this.getNewLineCharacter().length,index=0,row=Math.min(pos.row,lines.length),i=startRow||0;row>i;++i)index+=lines[i].length+newlineLength;return index+pos.column}}).call(Document.prototype),exports.Document=Document}),ace.define(\"ace/lib/lang\",[\"require\",\"exports\",\"module\"],function(acequire,exports){\"use strict\";exports.last=function(a){return a[a.length-1]},exports.stringReverse=function(string){return string.split(\"\").reverse().join(\"\")},exports.stringRepeat=function(string,count){for(var result=\"\";count>0;)1&count&&(result+=string),(count>>=1)&&(string+=string);return result};var trimBeginRegexp=/^\\s\\s*/,trimEndRegexp=/\\s\\s*$/;exports.stringTrimLeft=function(string){return string.replace(trimBeginRegexp,\"\")},exports.stringTrimRight=function(string){return string.replace(trimEndRegexp,\"\")},exports.copyObject=function(obj){var copy={};for(var key in obj)copy[key]=obj[key];return copy},exports.copyArray=function(array){for(var copy=[],i=0,l=array.length;l>i;i++)copy[i]=array[i]&&\"object\"==typeof array[i]?this.copyObject(array[i]):array[i];return copy},exports.deepCopy=function deepCopy(obj){if(\"object\"!=typeof obj||!obj)return obj;var copy;if(Array.isArray(obj)){copy=[];for(var key=0;obj.length>key;key++)copy[key]=deepCopy(obj[key]);return copy}if(\"[object Object]\"!==Object.prototype.toString.call(obj))return obj;copy={};for(var key in obj)copy[key]=deepCopy(obj[key]);return copy},exports.arrayToMap=function(arr){for(var map={},i=0;arr.length>i;i++)map[arr[i]]=1;return map},exports.createMap=function(props){var map=Object.create(null);for(var i in props)map[i]=props[i];return map},exports.arrayRemove=function(array,value){for(var i=0;array.length>=i;i++)value===array[i]&&array.splice(i,1)},exports.escapeRegExp=function(str){return str.replace(/([.*+?^${}()|[\\]\\/\\\\])/g,\"\\\\$1\")},exports.escapeHTML=function(str){return str.replace(/&/g,\"&#38;\").replace(/\"/g,\"&#34;\").replace(/'/g,\"&#39;\").replace(/</g,\"&#60;\")},exports.getMatchOffsets=function(string,regExp){var matches=[];return string.replace(regExp,function(str){matches.push({offset:arguments[arguments.length-2],length:str.length})}),matches},exports.deferredCall=function(fcn){var timer=null,callback=function(){timer=null,fcn()},deferred=function(timeout){return deferred.cancel(),timer=setTimeout(callback,timeout||0),deferred};return deferred.schedule=deferred,deferred.call=function(){return this.cancel(),fcn(),deferred},deferred.cancel=function(){return clearTimeout(timer),timer=null,deferred},deferred.isPending=function(){return timer},deferred},exports.delayedCall=function(fcn,defaultTimeout){var timer=null,callback=function(){timer=null,fcn()},_self=function(timeout){null==timer&&(timer=setTimeout(callback,timeout||defaultTimeout))};return _self.delay=function(timeout){timer&&clearTimeout(timer),timer=setTimeout(callback,timeout||defaultTimeout)},_self.schedule=_self,_self.call=function(){this.cancel(),fcn()},_self.cancel=function(){timer&&clearTimeout(timer),timer=null},_self.isPending=function(){return timer},_self}}),ace.define(\"ace/worker/mirror\",[\"require\",\"exports\",\"module\",\"ace/range\",\"ace/document\",\"ace/lib/lang\"],function(acequire,exports){\"use strict\";acequire(\"../range\").Range;var Document=acequire(\"../document\").Document,lang=acequire(\"../lib/lang\"),Mirror=exports.Mirror=function(sender){this.sender=sender;var doc=this.doc=new Document(\"\"),deferredUpdate=this.deferredUpdate=lang.delayedCall(this.onUpdate.bind(this)),_self=this;sender.on(\"change\",function(e){var data=e.data;if(data[0].start)doc.applyDeltas(data);else for(var i=0;data.length>i;i+=2){if(Array.isArray(data[i+1]))var d={action:\"insert\",start:data[i],lines:data[i+1]};else var d={action:\"remove\",start:data[i],end:data[i+1]};doc.applyDelta(d,!0)}return _self.$timeout?deferredUpdate.schedule(_self.$timeout):(_self.onUpdate(),void 0)})};(function(){this.$timeout=500,this.setTimeout=function(timeout){this.$timeout=timeout},this.setValue=function(value){this.doc.setValue(value),this.deferredUpdate.schedule(this.$timeout)},this.getValue=function(callbackId){this.sender.callback(this.doc.getValue(),callbackId)},this.onUpdate=function(){},this.isPending=function(){return this.deferredUpdate.isPending()}}).call(Mirror.prototype)}),ace.define(\"ace/mode/json/json_parse\",[\"require\",\"exports\",\"module\"],function(){\"use strict\";var at,ch,text,value,escapee={'\"':'\"',\"\\\\\":\"\\\\\",\"/\":\"/\",b:\"\\b\",f:\"\\f\",n:\"\\n\",r:\"\\r\",t:\"\t\"},error=function(m){throw{name:\"SyntaxError\",message:m,at:at,text:text}},next=function(c){return c&&c!==ch&&error(\"Expected '\"+c+\"' instead of '\"+ch+\"'\"),ch=text.charAt(at),at+=1,ch},number=function(){var number,string=\"\";for(\"-\"===ch&&(string=\"-\",next(\"-\"));ch>=\"0\"&&\"9\">=ch;)string+=ch,next();if(\".\"===ch)for(string+=\".\";next()&&ch>=\"0\"&&\"9\">=ch;)string+=ch;if(\"e\"===ch||\"E\"===ch)for(string+=ch,next(),(\"-\"===ch||\"+\"===ch)&&(string+=ch,next());ch>=\"0\"&&\"9\">=ch;)string+=ch,next();return number=+string,isNaN(number)?(error(\"Bad number\"),void 0):number},string=function(){var hex,i,uffff,string=\"\";if('\"'===ch)for(;next();){if('\"'===ch)return next(),string;if(\"\\\\\"===ch)if(next(),\"u\"===ch){for(uffff=0,i=0;4>i&&(hex=parseInt(next(),16),isFinite(hex));i+=1)uffff=16*uffff+hex;string+=String.fromCharCode(uffff)}else{if(\"string\"!=typeof escapee[ch])break;string+=escapee[ch]}else string+=ch}error(\"Bad string\")},white=function(){for(;ch&&\" \">=ch;)next()},word=function(){switch(ch){case\"t\":return next(\"t\"),next(\"r\"),next(\"u\"),next(\"e\"),!0;case\"f\":return next(\"f\"),next(\"a\"),next(\"l\"),next(\"s\"),next(\"e\"),!1;case\"n\":return next(\"n\"),next(\"u\"),next(\"l\"),next(\"l\"),null}error(\"Unexpected '\"+ch+\"'\")},array=function(){var array=[];if(\"[\"===ch){if(next(\"[\"),white(),\"]\"===ch)return next(\"]\"),array;for(;ch;){if(array.push(value()),white(),\"]\"===ch)return next(\"]\"),array;next(\",\"),white()}}error(\"Bad array\")},object=function(){var key,object={};if(\"{\"===ch){if(next(\"{\"),white(),\"}\"===ch)return next(\"}\"),object;for(;ch;){if(key=string(),white(),next(\":\"),Object.hasOwnProperty.call(object,key)&&error('Duplicate key \"'+key+'\"'),object[key]=value(),white(),\"}\"===ch)return next(\"}\"),object;next(\",\"),white()}}error(\"Bad object\")};return value=function(){switch(white(),ch){case\"{\":return object();case\"[\":return array();case'\"':return string();case\"-\":return number();default:return ch>=\"0\"&&\"9\">=ch?number():word()}},function(source,reviver){var result;return text=source,at=0,ch=\" \",result=value(),white(),ch&&error(\"Syntax error\"),\"function\"==typeof reviver?function walk(holder,key){var k,v,value=holder[key];if(value&&\"object\"==typeof value)for(k in value)Object.hasOwnProperty.call(value,k)&&(v=walk(value,k),void 0!==v?value[k]=v:delete value[k]);return reviver.call(holder,key,value)}({\"\":result},\"\"):result}}),ace.define(\"ace/mode/json_worker\",[\"require\",\"exports\",\"module\",\"ace/lib/oop\",\"ace/worker/mirror\",\"ace/mode/json/json_parse\"],function(acequire,exports){\"use strict\";var oop=acequire(\"../lib/oop\"),Mirror=acequire(\"../worker/mirror\").Mirror,parse=acequire(\"./json/json_parse\"),JsonWorker=exports.JsonWorker=function(sender){Mirror.call(this,sender),this.setTimeout(200)};oop.inherits(JsonWorker,Mirror),function(){this.onUpdate=function(){var value=this.doc.getValue(),errors=[];try{value&&parse(value)}catch(e){var pos=this.doc.indexToPosition(e.at-1);errors.push({row:pos.row,column:pos.column,text:e.message,type:\"error\"})}this.sender.emit(\"annotate\",errors)}}.call(JsonWorker.prototype)}),ace.define(\"ace/lib/es5-shim\",[\"require\",\"exports\",\"module\"],function(){function Empty(){}function doesDefinePropertyWork(object){try{return Object.defineProperty(object,\"sentinel\",{}),\"sentinel\"in object}catch(exception){}}function toInteger(n){return n=+n,n!==n?n=0:0!==n&&n!==1/0&&n!==-(1/0)&&(n=(n>0||-1)*Math.floor(Math.abs(n))),n}Function.prototype.bind||(Function.prototype.bind=function(that){var target=this;if(\"function\"!=typeof target)throw new TypeError(\"Function.prototype.bind called on incompatible \"+target);var args=slice.call(arguments,1),bound=function(){if(this instanceof bound){var result=target.apply(this,args.concat(slice.call(arguments)));return Object(result)===result?result:this}return target.apply(that,args.concat(slice.call(arguments)))};return target.prototype&&(Empty.prototype=target.prototype,bound.prototype=new Empty,Empty.prototype=null),bound});var defineGetter,defineSetter,lookupGetter,lookupSetter,supportsAccessors,call=Function.prototype.call,prototypeOfArray=Array.prototype,prototypeOfObject=Object.prototype,slice=prototypeOfArray.slice,_toString=call.bind(prototypeOfObject.toString),owns=call.bind(prototypeOfObject.hasOwnProperty);if((supportsAccessors=owns(prototypeOfObject,\"__defineGetter__\"))&&(defineGetter=call.bind(prototypeOfObject.__defineGetter__),defineSetter=call.bind(prototypeOfObject.__defineSetter__),lookupGetter=call.bind(prototypeOfObject.__lookupGetter__),lookupSetter=call.bind(prototypeOfObject.__lookupSetter__)),2!=[1,2].splice(0).length)if(function(){function makeArray(l){var a=Array(l+2);return a[0]=a[1]=0,a}var lengthBefore,array=[];return array.splice.apply(array,makeArray(20)),array.splice.apply(array,makeArray(26)),lengthBefore=array.length,array.splice(5,0,\"XXX\"),lengthBefore+1==array.length,lengthBefore+1==array.length?!0:void 0\n}()){var array_splice=Array.prototype.splice;Array.prototype.splice=function(start,deleteCount){return arguments.length?array_splice.apply(this,[void 0===start?0:start,void 0===deleteCount?this.length-start:deleteCount].concat(slice.call(arguments,2))):[]}}else Array.prototype.splice=function(pos,removeCount){var length=this.length;pos>0?pos>length&&(pos=length):void 0==pos?pos=0:0>pos&&(pos=Math.max(length+pos,0)),length>pos+removeCount||(removeCount=length-pos);var removed=this.slice(pos,pos+removeCount),insert=slice.call(arguments,2),add=insert.length;if(pos===length)add&&this.push.apply(this,insert);else{var remove=Math.min(removeCount,length-pos),tailOldPos=pos+remove,tailNewPos=tailOldPos+add-remove,tailCount=length-tailOldPos,lengthAfterRemove=length-remove;if(tailOldPos>tailNewPos)for(var i=0;tailCount>i;++i)this[tailNewPos+i]=this[tailOldPos+i];else if(tailNewPos>tailOldPos)for(i=tailCount;i--;)this[tailNewPos+i]=this[tailOldPos+i];if(add&&pos===lengthAfterRemove)this.length=lengthAfterRemove,this.push.apply(this,insert);else for(this.length=lengthAfterRemove+add,i=0;add>i;++i)this[pos+i]=insert[i]}return removed};Array.isArray||(Array.isArray=function(obj){return\"[object Array]\"==_toString(obj)});var boxedString=Object(\"a\"),splitString=\"a\"!=boxedString[0]||!(0 in boxedString);if(Array.prototype.forEach||(Array.prototype.forEach=function(fun){var object=toObject(this),self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):object,thisp=arguments[1],i=-1,length=self.length>>>0;if(\"[object Function]\"!=_toString(fun))throw new TypeError;for(;length>++i;)i in self&&fun.call(thisp,self[i],i,object)}),Array.prototype.map||(Array.prototype.map=function(fun){var object=toObject(this),self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):object,length=self.length>>>0,result=Array(length),thisp=arguments[1];if(\"[object Function]\"!=_toString(fun))throw new TypeError(fun+\" is not a function\");for(var i=0;length>i;i++)i in self&&(result[i]=fun.call(thisp,self[i],i,object));return result}),Array.prototype.filter||(Array.prototype.filter=function(fun){var value,object=toObject(this),self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):object,length=self.length>>>0,result=[],thisp=arguments[1];if(\"[object Function]\"!=_toString(fun))throw new TypeError(fun+\" is not a function\");for(var i=0;length>i;i++)i in self&&(value=self[i],fun.call(thisp,value,i,object)&&result.push(value));return result}),Array.prototype.every||(Array.prototype.every=function(fun){var object=toObject(this),self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):object,length=self.length>>>0,thisp=arguments[1];if(\"[object Function]\"!=_toString(fun))throw new TypeError(fun+\" is not a function\");for(var i=0;length>i;i++)if(i in self&&!fun.call(thisp,self[i],i,object))return!1;return!0}),Array.prototype.some||(Array.prototype.some=function(fun){var object=toObject(this),self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):object,length=self.length>>>0,thisp=arguments[1];if(\"[object Function]\"!=_toString(fun))throw new TypeError(fun+\" is not a function\");for(var i=0;length>i;i++)if(i in self&&fun.call(thisp,self[i],i,object))return!0;return!1}),Array.prototype.reduce||(Array.prototype.reduce=function(fun){var object=toObject(this),self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):object,length=self.length>>>0;if(\"[object Function]\"!=_toString(fun))throw new TypeError(fun+\" is not a function\");if(!length&&1==arguments.length)throw new TypeError(\"reduce of empty array with no initial value\");var result,i=0;if(arguments.length>=2)result=arguments[1];else for(;;){if(i in self){result=self[i++];break}if(++i>=length)throw new TypeError(\"reduce of empty array with no initial value\")}for(;length>i;i++)i in self&&(result=fun.call(void 0,result,self[i],i,object));return result}),Array.prototype.reduceRight||(Array.prototype.reduceRight=function(fun){var object=toObject(this),self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):object,length=self.length>>>0;if(\"[object Function]\"!=_toString(fun))throw new TypeError(fun+\" is not a function\");if(!length&&1==arguments.length)throw new TypeError(\"reduceRight of empty array with no initial value\");var result,i=length-1;if(arguments.length>=2)result=arguments[1];else for(;;){if(i in self){result=self[i--];break}if(0>--i)throw new TypeError(\"reduceRight of empty array with no initial value\")}do i in this&&(result=fun.call(void 0,result,self[i],i,object));while(i--);return result}),Array.prototype.indexOf&&-1==[0,1].indexOf(1,2)||(Array.prototype.indexOf=function(sought){var self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):toObject(this),length=self.length>>>0;if(!length)return-1;var i=0;for(arguments.length>1&&(i=toInteger(arguments[1])),i=i>=0?i:Math.max(0,length+i);length>i;i++)if(i in self&&self[i]===sought)return i;return-1}),Array.prototype.lastIndexOf&&-1==[0,1].lastIndexOf(0,-3)||(Array.prototype.lastIndexOf=function(sought){var self=splitString&&\"[object String]\"==_toString(this)?this.split(\"\"):toObject(this),length=self.length>>>0;if(!length)return-1;var i=length-1;for(arguments.length>1&&(i=Math.min(i,toInteger(arguments[1]))),i=i>=0?i:length-Math.abs(i);i>=0;i--)if(i in self&&sought===self[i])return i;return-1}),Object.getPrototypeOf||(Object.getPrototypeOf=function(object){return object.__proto__||(object.constructor?object.constructor.prototype:prototypeOfObject)}),!Object.getOwnPropertyDescriptor){var ERR_NON_OBJECT=\"Object.getOwnPropertyDescriptor called on a non-object: \";Object.getOwnPropertyDescriptor=function(object,property){if(\"object\"!=typeof object&&\"function\"!=typeof object||null===object)throw new TypeError(ERR_NON_OBJECT+object);if(owns(object,property)){var descriptor,getter,setter;if(descriptor={enumerable:!0,configurable:!0},supportsAccessors){var prototype=object.__proto__;object.__proto__=prototypeOfObject;var getter=lookupGetter(object,property),setter=lookupSetter(object,property);if(object.__proto__=prototype,getter||setter)return getter&&(descriptor.get=getter),setter&&(descriptor.set=setter),descriptor}return descriptor.value=object[property],descriptor}}}if(Object.getOwnPropertyNames||(Object.getOwnPropertyNames=function(object){return Object.keys(object)}),!Object.create){var createEmpty;createEmpty=null===Object.prototype.__proto__?function(){return{__proto__:null}}:function(){var empty={};for(var i in empty)empty[i]=null;return empty.constructor=empty.hasOwnProperty=empty.propertyIsEnumerable=empty.isPrototypeOf=empty.toLocaleString=empty.toString=empty.valueOf=empty.__proto__=null,empty},Object.create=function(prototype,properties){var object;if(null===prototype)object=createEmpty();else{if(\"object\"!=typeof prototype)throw new TypeError(\"typeof prototype[\"+typeof prototype+\"] != 'object'\");var Type=function(){};Type.prototype=prototype,object=new Type,object.__proto__=prototype}return void 0!==properties&&Object.defineProperties(object,properties),object}}if(Object.defineProperty){var definePropertyWorksOnObject=doesDefinePropertyWork({}),definePropertyWorksOnDom=\"undefined\"==typeof document||doesDefinePropertyWork(document.createElement(\"div\"));if(!definePropertyWorksOnObject||!definePropertyWorksOnDom)var definePropertyFallback=Object.defineProperty}if(!Object.defineProperty||definePropertyFallback){var ERR_NON_OBJECT_DESCRIPTOR=\"Property description must be an object: \",ERR_NON_OBJECT_TARGET=\"Object.defineProperty called on non-object: \",ERR_ACCESSORS_NOT_SUPPORTED=\"getters & setters can not be defined on this javascript engine\";Object.defineProperty=function(object,property,descriptor){if(\"object\"!=typeof object&&\"function\"!=typeof object||null===object)throw new TypeError(ERR_NON_OBJECT_TARGET+object);if(\"object\"!=typeof descriptor&&\"function\"!=typeof descriptor||null===descriptor)throw new TypeError(ERR_NON_OBJECT_DESCRIPTOR+descriptor);if(definePropertyFallback)try{return definePropertyFallback.call(Object,object,property,descriptor)}catch(exception){}if(owns(descriptor,\"value\"))if(supportsAccessors&&(lookupGetter(object,property)||lookupSetter(object,property))){var prototype=object.__proto__;object.__proto__=prototypeOfObject,delete object[property],object[property]=descriptor.value,object.__proto__=prototype}else object[property]=descriptor.value;else{if(!supportsAccessors)throw new TypeError(ERR_ACCESSORS_NOT_SUPPORTED);owns(descriptor,\"get\")&&defineGetter(object,property,descriptor.get),owns(descriptor,\"set\")&&defineSetter(object,property,descriptor.set)}return object}}Object.defineProperties||(Object.defineProperties=function(object,properties){for(var property in properties)owns(properties,property)&&Object.defineProperty(object,property,properties[property]);return object}),Object.seal||(Object.seal=function(object){return object}),Object.freeze||(Object.freeze=function(object){return object});try{Object.freeze(function(){})}catch(exception){Object.freeze=function(freezeObject){return function(object){return\"function\"==typeof object?object:freezeObject(object)}}(Object.freeze)}if(Object.preventExtensions||(Object.preventExtensions=function(object){return object}),Object.isSealed||(Object.isSealed=function(){return!1}),Object.isFrozen||(Object.isFrozen=function(){return!1}),Object.isExtensible||(Object.isExtensible=function(object){if(Object(object)===object)throw new TypeError;for(var name=\"\";owns(object,name);)name+=\"?\";object[name]=!0;var returnValue=owns(object,name);return delete object[name],returnValue}),!Object.keys){var hasDontEnumBug=!0,dontEnums=[\"toString\",\"toLocaleString\",\"valueOf\",\"hasOwnProperty\",\"isPrototypeOf\",\"propertyIsEnumerable\",\"constructor\"],dontEnumsLength=dontEnums.length;for(var key in{toString:null})hasDontEnumBug=!1;Object.keys=function(object){if(\"object\"!=typeof object&&\"function\"!=typeof object||null===object)throw new TypeError(\"Object.keys called on a non-object\");var keys=[];for(var name in object)owns(object,name)&&keys.push(name);if(hasDontEnumBug)for(var i=0,ii=dontEnumsLength;ii>i;i++){var dontEnum=dontEnums[i];owns(object,dontEnum)&&keys.push(dontEnum)}return keys}}Date.now||(Date.now=function(){return(new Date).getTime()});var ws=\"\t\\n\u000b\\f\\r   ᠎             　\\u2028\\u2029﻿\";if(!String.prototype.trim||ws.trim()){ws=\"[\"+ws+\"]\";var trimBeginRegexp=RegExp(\"^\"+ws+ws+\"*\"),trimEndRegexp=RegExp(ws+ws+\"*$\");String.prototype.trim=function(){return(this+\"\").replace(trimBeginRegexp,\"\").replace(trimEndRegexp,\"\")}}var toObject=function(o){if(null==o)throw new TypeError(\"can't convert \"+o+\" to object\");return Object(o)}});";

/***/ },
/* 19 */
/***/ function(module, exports) {

	ace.define("ace/ext/searchbox",["require","exports","module","ace/lib/dom","ace/lib/lang","ace/lib/event","ace/keyboard/hash_handler","ace/lib/keys"], function(acequire, exports, module) {
	"use strict";

	var dom = acequire("../lib/dom");
	var lang = acequire("../lib/lang");
	var event = acequire("../lib/event");
	var searchboxCss = "\
	.ace_search {\
	background-color: #ddd;\
	color: #666;\
	border: 1px solid #cbcbcb;\
	border-top: 0 none;\
	overflow: hidden;\
	margin: 0;\
	padding: 4px 6px 0 4px;\
	position: absolute;\
	top: 0;\
	z-index: 99;\
	white-space: normal;\
	}\
	.ace_search.left {\
	border-left: 0 none;\
	border-radius: 0px 0px 5px 0px;\
	left: 0;\
	}\
	.ace_search.right {\
	border-radius: 0px 0px 0px 5px;\
	border-right: 0 none;\
	right: 0;\
	}\
	.ace_search_form, .ace_replace_form {\
	margin: 0 20px 4px 0;\
	overflow: hidden;\
	line-height: 1.9;\
	}\
	.ace_replace_form {\
	margin-right: 0;\
	}\
	.ace_search_form.ace_nomatch {\
	outline: 1px solid red;\
	}\
	.ace_search_field {\
	border-radius: 3px 0 0 3px;\
	background-color: white;\
	color: black;\
	border: 1px solid #cbcbcb;\
	border-right: 0 none;\
	box-sizing: border-box!important;\
	outline: 0;\
	padding: 0;\
	font-size: inherit;\
	margin: 0;\
	line-height: inherit;\
	padding: 0 6px;\
	min-width: 17em;\
	vertical-align: top;\
	}\
	.ace_searchbtn {\
	border: 1px solid #cbcbcb;\
	line-height: inherit;\
	display: inline-block;\
	padding: 0 6px;\
	background: #fff;\
	border-right: 0 none;\
	border-left: 1px solid #dcdcdc;\
	cursor: pointer;\
	margin: 0;\
	position: relative;\
	box-sizing: content-box!important;\
	color: #666;\
	}\
	.ace_searchbtn:last-child {\
	border-radius: 0 3px 3px 0;\
	border-right: 1px solid #cbcbcb;\
	}\
	.ace_searchbtn:disabled {\
	background: none;\
	cursor: default;\
	}\
	.ace_searchbtn:hover {\
	background-color: #eef1f6;\
	}\
	.ace_searchbtn.prev, .ace_searchbtn.next {\
	padding: 0px 0.7em\
	}\
	.ace_searchbtn.prev:after, .ace_searchbtn.next:after {\
	content: \"\";\
	border: solid 2px #888;\
	width: 0.5em;\
	height: 0.5em;\
	border-width:  2px 0 0 2px;\
	display:inline-block;\
	transform: rotate(-45deg);\
	}\
	.ace_searchbtn.next:after {\
	border-width: 0 2px 2px 0 ;\
	}\
	.ace_searchbtn_close {\
	background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAcCAYAAABRVo5BAAAAZ0lEQVR42u2SUQrAMAhDvazn8OjZBilCkYVVxiis8H4CT0VrAJb4WHT3C5xU2a2IQZXJjiQIRMdkEoJ5Q2yMqpfDIo+XY4k6h+YXOyKqTIj5REaxloNAd0xiKmAtsTHqW8sR2W5f7gCu5nWFUpVjZwAAAABJRU5ErkJggg==) no-repeat 50% 0;\
	border-radius: 50%;\
	border: 0 none;\
	color: #656565;\
	cursor: pointer;\
	font: 16px/16px Arial;\
	padding: 0;\
	height: 14px;\
	width: 14px;\
	top: 9px;\
	right: 7px;\
	position: absolute;\
	}\
	.ace_searchbtn_close:hover {\
	background-color: #656565;\
	background-position: 50% 100%;\
	color: white;\
	}\
	.ace_button {\
	margin-left: 2px;\
	cursor: pointer;\
	-webkit-user-select: none;\
	-moz-user-select: none;\
	-o-user-select: none;\
	-ms-user-select: none;\
	user-select: none;\
	overflow: hidden;\
	opacity: 0.7;\
	border: 1px solid rgba(100,100,100,0.23);\
	padding: 1px;\
	box-sizing:    border-box!important;\
	color: black;\
	}\
	.ace_button:hover {\
	background-color: #eee;\
	opacity:1;\
	}\
	.ace_button:active {\
	background-color: #ddd;\
	}\
	.ace_button.checked {\
	border-color: #3399ff;\
	opacity:1;\
	}\
	.ace_search_options{\
	margin-bottom: 3px;\
	text-align: right;\
	-webkit-user-select: none;\
	-moz-user-select: none;\
	-o-user-select: none;\
	-ms-user-select: none;\
	user-select: none;\
	clear: both;\
	}\
	.ace_search_counter {\
	float: left;\
	font-family: arial;\
	padding: 0 8px;\
	}";
	var HashHandler = acequire("../keyboard/hash_handler").HashHandler;
	var keyUtil = acequire("../lib/keys");

	var MAX_COUNT = 999;

	dom.importCssString(searchboxCss, "ace_searchbox");

	var html = '<div class="ace_search right">\
	    <span action="hide" class="ace_searchbtn_close"></span>\
	    <div class="ace_search_form">\
	        <input class="ace_search_field" placeholder="Search for" spellcheck="false"></input>\
	        <span action="findPrev" class="ace_searchbtn prev"></span>\
	        <span action="findNext" class="ace_searchbtn next"></span>\
	        <span action="findAll" class="ace_searchbtn" title="Alt-Enter">All</span>\
	    </div>\
	    <div class="ace_replace_form">\
	        <input class="ace_search_field" placeholder="Replace with" spellcheck="false"></input>\
	        <span action="replaceAndFindNext" class="ace_searchbtn">Replace</span>\
	        <span action="replaceAll" class="ace_searchbtn">All</span>\
	    </div>\
	    <div class="ace_search_options">\
	        <span action="toggleReplace" class="ace_button" title="Toggel Replace mode"\
	            style="float:left;margin-top:-2px;padding:0 5px;">+</span>\
	        <span class="ace_search_counter"></span>\
	        <span action="toggleRegexpMode" class="ace_button" title="RegExp Search">.*</span>\
	        <span action="toggleCaseSensitive" class="ace_button" title="CaseSensitive Search">Aa</span>\
	        <span action="toggleWholeWords" class="ace_button" title="Whole Word Search">\\b</span>\
	        <span action="searchInSelection" class="ace_button" title="Search In Selection">S</span>\
	    </div>\
	</div>'.replace(/> +/g, ">");

	var SearchBox = function(editor, range, showReplaceForm) {
	    var div = dom.createElement("div");
	    div.innerHTML = html;
	    this.element = div.firstChild;

	    this.setSession = this.setSession.bind(this);

	    this.$init();
	    this.setEditor(editor);
	};

	(function() {
	    this.setEditor = function(editor) {
	        editor.searchBox = this;
	        editor.renderer.scroller.appendChild(this.element);
	        this.editor = editor;
	    };

	    this.setSession = function(e) {
	        this.searchRange = null;
	        this.$syncOptions(true);
	    };

	    this.$initElements = function(sb) {
	        this.searchBox = sb.querySelector(".ace_search_form");
	        this.replaceBox = sb.querySelector(".ace_replace_form");
	        this.searchOption = sb.querySelector("[action=searchInSelection]");
	        this.replaceOption = sb.querySelector("[action=toggleReplace]");
	        this.regExpOption = sb.querySelector("[action=toggleRegexpMode]");
	        this.caseSensitiveOption = sb.querySelector("[action=toggleCaseSensitive]");
	        this.wholeWordOption = sb.querySelector("[action=toggleWholeWords]");
	        this.searchInput = this.searchBox.querySelector(".ace_search_field");
	        this.replaceInput = this.replaceBox.querySelector(".ace_search_field");
	        this.searchCounter = sb.querySelector(".ace_search_counter");
	    };
	    
	    this.$init = function() {
	        var sb = this.element;
	        
	        this.$initElements(sb);
	        
	        var _this = this;
	        event.addListener(sb, "mousedown", function(e) {
	            setTimeout(function(){
	                _this.activeInput.focus();
	            }, 0);
	            event.stopPropagation(e);
	        });
	        event.addListener(sb, "click", function(e) {
	            var t = e.target || e.srcElement;
	            var action = t.getAttribute("action");
	            if (action && _this[action])
	                _this[action]();
	            else if (_this.$searchBarKb.commands[action])
	                _this.$searchBarKb.commands[action].exec(_this);
	            event.stopPropagation(e);
	        });

	        event.addCommandKeyListener(sb, function(e, hashId, keyCode) {
	            var keyString = keyUtil.keyCodeToString(keyCode);
	            var command = _this.$searchBarKb.findKeyCommand(hashId, keyString);
	            if (command && command.exec) {
	                command.exec(_this);
	                event.stopEvent(e);
	            }
	        });

	        this.$onChange = lang.delayedCall(function() {
	            _this.find(false, false);
	        });

	        event.addListener(this.searchInput, "input", function() {
	            _this.$onChange.schedule(20);
	        });
	        event.addListener(this.searchInput, "focus", function() {
	            _this.activeInput = _this.searchInput;
	            _this.searchInput.value && _this.highlight();
	        });
	        event.addListener(this.replaceInput, "focus", function() {
	            _this.activeInput = _this.replaceInput;
	            _this.searchInput.value && _this.highlight();
	        });
	    };
	    this.$closeSearchBarKb = new HashHandler([{
	        bindKey: "Esc",
	        name: "closeSearchBar",
	        exec: function(editor) {
	            editor.searchBox.hide();
	        }
	    }]);
	    this.$searchBarKb = new HashHandler();
	    this.$searchBarKb.bindKeys({
	        "Ctrl-f|Command-f": function(sb) {
	            var isReplace = sb.isReplace = !sb.isReplace;
	            sb.replaceBox.style.display = isReplace ? "" : "none";
	            sb.replaceOption.checked = false;
	            sb.$syncOptions();
	            sb.searchInput.focus();
	        },
	        "Ctrl-H|Command-Option-F": function(sb) {
	            sb.replaceOption.checked = true;
	            sb.$syncOptions();
	            sb.replaceInput.focus();
	        },
	        "Ctrl-G|Command-G": function(sb) {
	            sb.findNext();
	        },
	        "Ctrl-Shift-G|Command-Shift-G": function(sb) {
	            sb.findPrev();
	        },
	        "esc": function(sb) {
	            setTimeout(function() { sb.hide();});
	        },
	        "Return": function(sb) {
	            if (sb.activeInput == sb.replaceInput)
	                sb.replace();
	            sb.findNext();
	        },
	        "Shift-Return": function(sb) {
	            if (sb.activeInput == sb.replaceInput)
	                sb.replace();
	            sb.findPrev();
	        },
	        "Alt-Return": function(sb) {
	            if (sb.activeInput == sb.replaceInput)
	                sb.replaceAll();
	            sb.findAll();
	        },
	        "Tab": function(sb) {
	            (sb.activeInput == sb.replaceInput ? sb.searchInput : sb.replaceInput).focus();
	        }
	    });

	    this.$searchBarKb.addCommands([{
	        name: "toggleRegexpMode",
	        bindKey: {win: "Alt-R|Alt-/", mac: "Ctrl-Alt-R|Ctrl-Alt-/"},
	        exec: function(sb) {
	            sb.regExpOption.checked = !sb.regExpOption.checked;
	            sb.$syncOptions();
	        }
	    }, {
	        name: "toggleCaseSensitive",
	        bindKey: {win: "Alt-C|Alt-I", mac: "Ctrl-Alt-R|Ctrl-Alt-I"},
	        exec: function(sb) {
	            sb.caseSensitiveOption.checked = !sb.caseSensitiveOption.checked;
	            sb.$syncOptions();
	        }
	    }, {
	        name: "toggleWholeWords",
	        bindKey: {win: "Alt-B|Alt-W", mac: "Ctrl-Alt-B|Ctrl-Alt-W"},
	        exec: function(sb) {
	            sb.wholeWordOption.checked = !sb.wholeWordOption.checked;
	            sb.$syncOptions();
	        }
	    }, {
	        name: "toggleReplace",
	        exec: function(sb) {
	            sb.replaceOption.checked = !sb.replaceOption.checked;
	            sb.$syncOptions();
	        }
	    }, {
	        name: "searchInSelection",
	        exec: function(sb) {
	            sb.searchOption.checked = !sb.searchRange;
	            sb.setSearchRange(sb.searchOption.checked && sb.editor.getSelectionRange());
	            sb.$syncOptions();
	        }
	    }]);

	    this.setSearchRange = function(range) {
	        this.searchRange = range;
	        if (range) {
	            this.searchRangeMarker = this.editor.session.addMarker(range, "ace_active-line");
	        } else if (this.searchRangeMarker) {
	            this.editor.session.removeMarker(this.searchRangeMarker);
	            this.searchRangeMarker = null;
	        }
	    };

	    this.$syncOptions = function(preventScroll) {
	        dom.setCssClass(this.replaceOption, "checked", this.searchRange);
	        dom.setCssClass(this.searchOption, "checked", this.searchOption.checked);
	        this.replaceOption.textContent = this.replaceOption.checked ? "-" : "+";
	        dom.setCssClass(this.regExpOption, "checked", this.regExpOption.checked);
	        dom.setCssClass(this.wholeWordOption, "checked", this.wholeWordOption.checked);
	        dom.setCssClass(this.caseSensitiveOption, "checked", this.caseSensitiveOption.checked);
	        this.replaceBox.style.display = this.replaceOption.checked ? "" : "none";
	        this.find(false, false, preventScroll);
	    };

	    this.highlight = function(re) {
	        this.editor.session.highlight(re || this.editor.$search.$options.re);
	        this.editor.renderer.updateBackMarkers();
	    };
	    this.find = function(skipCurrent, backwards, preventScroll) {
	        var range = this.editor.find(this.searchInput.value, {
	            skipCurrent: skipCurrent,
	            backwards: backwards,
	            wrap: true,
	            regExp: this.regExpOption.checked,
	            caseSensitive: this.caseSensitiveOption.checked,
	            wholeWord: this.wholeWordOption.checked,
	            preventScroll: preventScroll,
	            range: this.searchRange
	        });
	        var noMatch = !range && this.searchInput.value;
	        dom.setCssClass(this.searchBox, "ace_nomatch", noMatch);
	        this.editor._emit("findSearchBox", { match: !noMatch });
	        this.highlight();
	        this.updateCounter();
	    };
	    this.updateCounter = function() {
	        var editor = this.editor;
	        var regex = editor.$search.$options.re;
	        var all = 0;
	        var before = 0;
	        if (regex) {
	            var value = this.searchRange
	                ? editor.session.getTextRange(this.searchRange)
	                : editor.getValue();

	            var offset = editor.session.doc.positionToIndex(editor.selection.anchor);
	            if (this.searchRange)
	                offset -= editor.session.doc.positionToIndex(this.searchRange.start);

	            var last = regex.lastIndex = 0;
	            var m;
	            while ((m = regex.exec(value))) {
	                all++;
	                last = m.index;
	                if (last <= offset)
	                    before++;
	                if (all > MAX_COUNT)
	                    break;
	                if (!m[0]) {
	                    regex.lastIndex = last += 1;
	                    if (last >= value.length)
	                        break;
	                }
	            }
	        }
	        this.searchCounter.textContent = before + " of " + (all > MAX_COUNT ? MAX_COUNT + "+" : all);
	    };
	    this.findNext = function() {
	        this.find(true, false);
	    };
	    this.findPrev = function() {
	        this.find(true, true);
	    };
	    this.findAll = function(){
	        var range = this.editor.findAll(this.searchInput.value, {            
	            regExp: this.regExpOption.checked,
	            caseSensitive: this.caseSensitiveOption.checked,
	            wholeWord: this.wholeWordOption.checked
	        });
	        var noMatch = !range && this.searchInput.value;
	        dom.setCssClass(this.searchBox, "ace_nomatch", noMatch);
	        this.editor._emit("findSearchBox", { match: !noMatch });
	        this.highlight();
	        this.hide();
	    };
	    this.replace = function() {
	        if (!this.editor.getReadOnly())
	            this.editor.replace(this.replaceInput.value);
	    };    
	    this.replaceAndFindNext = function() {
	        if (!this.editor.getReadOnly()) {
	            this.editor.replace(this.replaceInput.value);
	            this.findNext();
	        }
	    };
	    this.replaceAll = function() {
	        if (!this.editor.getReadOnly())
	            this.editor.replaceAll(this.replaceInput.value);
	    };

	    this.hide = function() {
	        this.active = false;
	        this.setSearchRange(null);
	        this.editor.off("changeSession", this.setSession);

	        this.element.style.display = "none";
	        this.editor.keyBinding.removeKeyboardHandler(this.$closeSearchBarKb);
	        this.editor.focus();
	    };
	    this.show = function(value, isReplace) {
	        this.active = true;
	        this.editor.on("changeSession", this.setSession);
	        this.element.style.display = "";
	        this.replaceOption.checked = isReplace;

	        if (value)
	            this.searchInput.value = value;
	        
	        this.searchInput.focus();
	        this.searchInput.select();

	        this.editor.keyBinding.addKeyboardHandler(this.$closeSearchBarKb);

	        this.$syncOptions(true);
	    };

	    this.isFocused = function() {
	        var el = document.activeElement;
	        return el == this.searchInput || el == this.replaceInput;
	    };
	}).call(SearchBox.prototype);

	exports.SearchBox = SearchBox;

	exports.Search = function(editor, isReplace) {
	    var sb = editor.searchBox || new SearchBox(editor);
	    sb.show(editor.session.getTextRange(), isReplace);
	};

	});
	                (function() {
	                    ace.acequire(["ace/ext/searchbox"], function() {});
	                })();
	            

/***/ },
/* 20 */
/***/ function(module, exports) {

	/* ***** BEGIN LICENSE BLOCK *****
	 * Distributed under the BSD license:
	 *
	 * Copyright (c) 2010, Ajax.org B.V.
	 * All rights reserved.
	 * 
	 * Redistribution and use in source and binary forms, with or without
	 * modification, are permitted provided that the following conditions are met:
	 *     * Redistributions of source code must retain the above copyright
	 *       notice, this list of conditions and the following disclaimer.
	 *     * Redistributions in binary form must reproduce the above copyright
	 *       notice, this list of conditions and the following disclaimer in the
	 *       documentation and/or other materials provided with the distribution.
	 *     * Neither the name of Ajax.org B.V. nor the
	 *       names of its contributors may be used to endorse or promote products
	 *       derived from this software without specific prior written permission.
	 * 
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	 * DISCLAIMED. IN NO EVENT SHALL AJAX.ORG B.V. BE LIABLE FOR ANY
	 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	 *
	 * ***** END LICENSE BLOCK ***** */

	ace.define('ace/theme/jsoneditor', ['require', 'exports', 'module', 'ace/lib/dom'], function(acequire, exports, module) {

	exports.isDark = false;
	exports.cssClass = "ace-jsoneditor";
	exports.cssText = ".ace-jsoneditor .ace_gutter {\
	background: #ebebeb;\
	color: #333\
	}\
	\
	.ace-jsoneditor.ace_editor {\
	font-family: \"dejavu sans mono\", \"droid sans mono\", consolas, monaco, \"lucida console\", \"courier new\", courier, monospace, sans-serif;\
	line-height: 1.3;\
	background-color: #fff;\
	}\
	.ace-jsoneditor .ace_print-margin {\
	width: 1px;\
	background: #e8e8e8\
	}\
	.ace-jsoneditor .ace_scroller {\
	background-color: #FFFFFF\
	}\
	.ace-jsoneditor .ace_text-layer {\
	color: gray\
	}\
	.ace-jsoneditor .ace_variable {\
	color: #1a1a1a\
	}\
	.ace-jsoneditor .ace_cursor {\
	border-left: 2px solid #000000\
	}\
	.ace-jsoneditor .ace_overwrite-cursors .ace_cursor {\
	border-left: 0px;\
	border-bottom: 1px solid #000000\
	}\
	.ace-jsoneditor .ace_marker-layer .ace_selection {\
	background: lightgray\
	}\
	.ace-jsoneditor.ace_multiselect .ace_selection.ace_start {\
	box-shadow: 0 0 3px 0px #FFFFFF;\
	border-radius: 2px\
	}\
	.ace-jsoneditor .ace_marker-layer .ace_step {\
	background: rgb(255, 255, 0)\
	}\
	.ace-jsoneditor .ace_marker-layer .ace_bracket {\
	margin: -1px 0 0 -1px;\
	border: 1px solid #BFBFBF\
	}\
	.ace-jsoneditor .ace_marker-layer .ace_active-line {\
	background: #FFFBD1\
	}\
	.ace-jsoneditor .ace_gutter-active-line {\
	background-color : #dcdcdc\
	}\
	.ace-jsoneditor .ace_marker-layer .ace_selected-word {\
	border: 1px solid lightgray\
	}\
	.ace-jsoneditor .ace_invisible {\
	color: #BFBFBF\
	}\
	.ace-jsoneditor .ace_keyword,\
	.ace-jsoneditor .ace_meta,\
	.ace-jsoneditor .ace_support.ace_constant.ace_property-value {\
	color: #AF956F\
	}\
	.ace-jsoneditor .ace_keyword.ace_operator {\
	color: #484848\
	}\
	.ace-jsoneditor .ace_keyword.ace_other.ace_unit {\
	color: #96DC5F\
	}\
	.ace-jsoneditor .ace_constant.ace_language {\
	color: darkorange\
	}\
	.ace-jsoneditor .ace_constant.ace_numeric {\
	color: red\
	}\
	.ace-jsoneditor .ace_constant.ace_character.ace_entity {\
	color: #BF78CC\
	}\
	.ace-jsoneditor .ace_invalid {\
	color: #FFFFFF;\
	background-color: #FF002A;\
	}\
	.ace-jsoneditor .ace_fold {\
	background-color: #AF956F;\
	border-color: #000000\
	}\
	.ace-jsoneditor .ace_storage,\
	.ace-jsoneditor .ace_support.ace_class,\
	.ace-jsoneditor .ace_support.ace_function,\
	.ace-jsoneditor .ace_support.ace_other,\
	.ace-jsoneditor .ace_support.ace_type {\
	color: #C52727\
	}\
	.ace-jsoneditor .ace_string {\
	color: green\
	}\
	.ace-jsoneditor .ace_comment {\
	color: #BCC8BA\
	}\
	.ace-jsoneditor .ace_entity.ace_name.ace_tag,\
	.ace-jsoneditor .ace_entity.ace_other.ace_attribute-name {\
	color: #606060\
	}\
	.ace-jsoneditor .ace_markup.ace_underline {\
	text-decoration: underline\
	}\
	.ace-jsoneditor .ace_indent-guide {\
	background: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAE0lEQVQImWP4////f4bLly//BwAmVgd1/w11/gAAAABJRU5ErkJggg==\") right repeat-y\
	}";

	var dom = acequire("../lib/dom");
	dom.importCssString(exports.cssText, exports.cssClass);
	});


/***/ }
/******/ ])
});
;

/***/ }),

/***/ "./node_modules/jsoneditor/dist/jsoneditor.min.css":
/*!*********************************************************!*\
  !*** ./node_modules/jsoneditor/dist/jsoneditor.min.css ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./node_modules/process/browser.js":
/*!*****************************************!*\
  !*** ./node_modules/process/browser.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),

/***/ "./node_modules/setimmediate/setImmediate.js":
/*!***************************************************!*\
  !*** ./node_modules/setimmediate/setImmediate.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global, process) {(function (global, undefined) {
    "use strict";

    if (global.setImmediate) {
        return;
    }

    var nextHandle = 1; // Spec says greater than zero
    var tasksByHandle = {};
    var currentlyRunningATask = false;
    var doc = global.document;
    var registerImmediate;

    function setImmediate(callback) {
      // Callback can either be a function or a string
      if (typeof callback !== "function") {
        callback = new Function("" + callback);
      }
      // Copy function arguments
      var args = new Array(arguments.length - 1);
      for (var i = 0; i < args.length; i++) {
          args[i] = arguments[i + 1];
      }
      // Store and register the task
      var task = { callback: callback, args: args };
      tasksByHandle[nextHandle] = task;
      registerImmediate(nextHandle);
      return nextHandle++;
    }

    function clearImmediate(handle) {
        delete tasksByHandle[handle];
    }

    function run(task) {
        var callback = task.callback;
        var args = task.args;
        switch (args.length) {
        case 0:
            callback();
            break;
        case 1:
            callback(args[0]);
            break;
        case 2:
            callback(args[0], args[1]);
            break;
        case 3:
            callback(args[0], args[1], args[2]);
            break;
        default:
            callback.apply(undefined, args);
            break;
        }
    }

    function runIfPresent(handle) {
        // From the spec: "Wait until any invocations of this algorithm started before this one have completed."
        // So if we're currently running a task, we'll need to delay this invocation.
        if (currentlyRunningATask) {
            // Delay by doing a setTimeout. setImmediate was tried instead, but in Firefox 7 it generated a
            // "too much recursion" error.
            setTimeout(runIfPresent, 0, handle);
        } else {
            var task = tasksByHandle[handle];
            if (task) {
                currentlyRunningATask = true;
                try {
                    run(task);
                } finally {
                    clearImmediate(handle);
                    currentlyRunningATask = false;
                }
            }
        }
    }

    function installNextTickImplementation() {
        registerImmediate = function(handle) {
            process.nextTick(function () { runIfPresent(handle); });
        };
    }

    function canUsePostMessage() {
        // The test against `importScripts` prevents this implementation from being installed inside a web worker,
        // where `global.postMessage` means something completely different and can't be used for this purpose.
        if (global.postMessage && !global.importScripts) {
            var postMessageIsAsynchronous = true;
            var oldOnMessage = global.onmessage;
            global.onmessage = function() {
                postMessageIsAsynchronous = false;
            };
            global.postMessage("", "*");
            global.onmessage = oldOnMessage;
            return postMessageIsAsynchronous;
        }
    }

    function installPostMessageImplementation() {
        // Installs an event handler on `global` for the `message` event: see
        // * https://developer.mozilla.org/en/DOM/window.postMessage
        // * http://www.whatwg.org/specs/web-apps/current-work/multipage/comms.html#crossDocumentMessages

        var messagePrefix = "setImmediate$" + Math.random() + "$";
        var onGlobalMessage = function(event) {
            if (event.source === global &&
                typeof event.data === "string" &&
                event.data.indexOf(messagePrefix) === 0) {
                runIfPresent(+event.data.slice(messagePrefix.length));
            }
        };

        if (global.addEventListener) {
            global.addEventListener("message", onGlobalMessage, false);
        } else {
            global.attachEvent("onmessage", onGlobalMessage);
        }

        registerImmediate = function(handle) {
            global.postMessage(messagePrefix + handle, "*");
        };
    }

    function installMessageChannelImplementation() {
        var channel = new MessageChannel();
        channel.port1.onmessage = function(event) {
            var handle = event.data;
            runIfPresent(handle);
        };

        registerImmediate = function(handle) {
            channel.port2.postMessage(handle);
        };
    }

    function installReadyStateChangeImplementation() {
        var html = doc.documentElement;
        registerImmediate = function(handle) {
            // Create a <script> element; its readystatechange event will be fired asynchronously once it is inserted
            // into the document. Do so, thus queuing up the task. Remember to clean up once it's been called.
            var script = doc.createElement("script");
            script.onreadystatechange = function () {
                runIfPresent(handle);
                script.onreadystatechange = null;
                html.removeChild(script);
                script = null;
            };
            html.appendChild(script);
        };
    }

    function installSetTimeoutImplementation() {
        registerImmediate = function(handle) {
            setTimeout(runIfPresent, 0, handle);
        };
    }

    // If supported, we should attach to the prototype of global, since that is where setTimeout et al. live.
    var attachTo = Object.getPrototypeOf && Object.getPrototypeOf(global);
    attachTo = attachTo && attachTo.setTimeout ? attachTo : global;

    // Don't get fooled by e.g. browserify environments.
    if ({}.toString.call(global.process) === "[object process]") {
        // For Node.js before 0.9
        installNextTickImplementation();

    } else if (canUsePostMessage()) {
        // For non-IE10 modern browsers
        installPostMessageImplementation();

    } else if (global.MessageChannel) {
        // For web workers, where supported
        installMessageChannelImplementation();

    } else if (doc && "onreadystatechange" in doc.createElement("script")) {
        // For IE 6–8
        installReadyStateChangeImplementation();

    } else {
        // For older browsers
        installSetTimeoutImplementation();
    }

    attachTo.setImmediate = setImmediate;
    attachTo.clearImmediate = clearImmediate;
}(typeof self === "undefined" ? typeof global === "undefined" ? this : global : self));

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js"), __webpack_require__(/*! ./../process/browser.js */ "./node_modules/process/browser.js")))

/***/ }),

/***/ "./node_modules/sleep-promise/build/esm.mjs":
/*!**************************************************!*\
  !*** ./node_modules/sleep-promise/build/esm.mjs ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(__webpack_module__, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function createSleepPromise(timeout) {
  return new Promise(function (resolve) {
    setTimeout(resolve, timeout);
  });
}

function sleep(timeout) {
  var sleepPromise = createSleepPromise(timeout); // Pass value through, if used in a promise chain

  function promiseFunction(value) {
    return sleepPromise.then(function () {
      return value;
    });
  } // Normal promise


  promiseFunction.then = function () {
    return sleepPromise.then.apply(sleepPromise, arguments);
  };

  promiseFunction.catch = Promise.resolve().catch;
  return promiseFunction;
}

/* harmony default export */ __webpack_exports__["default"] = (sleep);


/***/ }),

/***/ "./node_modules/timers-browserify/main.js":
/*!************************************************!*\
  !*** ./node_modules/timers-browserify/main.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var scope = (typeof global !== "undefined" && global) ||
            (typeof self !== "undefined" && self) ||
            window;
var apply = Function.prototype.apply;

// DOM APIs, for completeness

exports.setTimeout = function() {
  return new Timeout(apply.call(setTimeout, scope, arguments), clearTimeout);
};
exports.setInterval = function() {
  return new Timeout(apply.call(setInterval, scope, arguments), clearInterval);
};
exports.clearTimeout =
exports.clearInterval = function(timeout) {
  if (timeout) {
    timeout.close();
  }
};

function Timeout(id, clearFn) {
  this._id = id;
  this._clearFn = clearFn;
}
Timeout.prototype.unref = Timeout.prototype.ref = function() {};
Timeout.prototype.close = function() {
  this._clearFn.call(scope, this._id);
};

// Does not start the time, just sets up the members needed.
exports.enroll = function(item, msecs) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = msecs;
};

exports.unenroll = function(item) {
  clearTimeout(item._idleTimeoutId);
  item._idleTimeout = -1;
};

exports._unrefActive = exports.active = function(item) {
  clearTimeout(item._idleTimeoutId);

  var msecs = item._idleTimeout;
  if (msecs >= 0) {
    item._idleTimeoutId = setTimeout(function onTimeout() {
      if (item._onTimeout)
        item._onTimeout();
    }, msecs);
  }
};

// setimmediate attaches itself to the global object
__webpack_require__(/*! setimmediate */ "./node_modules/setimmediate/setImmediate.js");
// On some exotic environments, it's not clear which object `setimmediate` was
// able to install onto.  Search each possibility in the same order as the
// `setimmediate` library.
exports.setImmediate = (typeof self !== "undefined" && self.setImmediate) ||
                       (typeof global !== "undefined" && global.setImmediate) ||
                       (this && this.setImmediate);
exports.clearImmediate = (typeof self !== "undefined" && self.clearImmediate) ||
                         (typeof global !== "undefined" && global.clearImmediate) ||
                         (this && this.clearImmediate);

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/v-hotkey/index.js":
/*!****************************************!*\
  !*** ./node_modules/v-hotkey/index.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

!function(e,t){ true?module.exports=t():undefined}(this,function(){return function(e){function t(o){if(n[o])return n[o].exports;var r=n[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,t),r.l=!0,r.exports}var n={};return t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,o){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:o})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=1)}([function(e,t,n){var o,r,a;!function(n,u){r=[t],o=u,void 0!==(a="function"==typeof o?o.apply(t,r):o)&&(e.exports=a)}(0,function(e){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e};e.default=function(e){if(e&&"object"===(void 0===e?"undefined":t(e))){var r=e.which||e.keyCode||e.charCode;r&&(e=r)}if("number"==typeof e)return names[e];var a=String(e),u=n[a.toLowerCase()];if(u)return u;var u=o[a.toLowerCase()];return u||(1===a.length?a.charCodeAt(0):void 0)};var n=e.codes={backspace:8,tab:9,enter:13,shift:16,ctrl:17,alt:18,"pause/break":19,"caps lock":20,esc:27,space:32,"page up":33,"page down":34,end:35,home:36,left:37,up:38,right:39,down:40,insert:45,delete:46,command:91,"left command":91,"right command":93,"numpad *":106,"numpad +":107,"numpad -":109,"numpad .":110,"numpad /":111,"num lock":144,"scroll lock":145,"my computer":182,"my calculator":183,";":186,"=":187,",":188,"-":189,".":190,"/":191,"`":192,"[":219,"\\":220,"]":221,"'":222},o=e.aliases={windows:91,"⇧":16,"⌥":18,"⌃":17,"⌘":91,ctl:17,control:17,option:18,pause:19,break:19,caps:20,return:13,escape:27,spc:32,pgup:33,pgdn:34,ins:45,del:46,cmd:91};for(r=97;r<123;r++)n[String.fromCharCode(r)]=r-32;for(var r=48;r<58;r++)n[r-48]=r;for(r=1;r<13;r++)n["f"+r]=r+111;for(r=0;r<10;r++)n["numpad "+r]=r+96})},function(e,t,n){var o,r,a;!function(u,c){r=[e,t,n(0)],o=c,void 0!==(a="function"==typeof o?o.apply(t,r):o)&&(e.exports=a)}(0,function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=function(e){return e&&e.__esModule?e:{default:e}}(n),r=function(e){return Object.keys(e).map(function(t){var n={};return t.split("+").forEach(function(e){switch(e.toLowerCase()){case"ctrl":case"alt":case"shift":case"meta":n[e]=!0;break;default:n.keyCode=(0,o.default)(e)}}),n.callback=e[t],n})};t.default={install:function(e){e.directive("hotkey",{bind:function(e,t,n,o){e._keymap=r(t.value),e._keymapHasKeyUp=e._keymap.some(function(e){return e.callback.keyup}),e._keyHandler=function(t){var n=!0,o=!1,r=void 0;try{for(var a,u=e._keymap[Symbol.iterator]();!(n=(a=u.next()).done);n=!0){var c=a.value,i=c.keyCode===t.keyCode&&!!c.ctrl===t.ctrlKey&&!!c.alt===t.altKey&&!!c.shift===t.shiftKey&&!!c.meta===t.metaKey&&("keydown"===t.type?c.callback.keydown||c.callback:c.callback.keyup);i&&i(t)}}catch(e){o=!0,r=e}finally{try{!n&&u.return&&u.return()}finally{if(o)throw r}}},document.addEventListener("keydown",e._keyHandler),e._keymapHasKeyUp&&document.addEventListener("keyup",e._keyHandler)},unbind:function(e,t,n,o){document.removeEventListener("keydown",e._keyHandler),e._keymapHasKeyUp&&document.removeEventListener("keyup",e._keyHandler)}})}},e.exports=t.default})}])});

/***/ }),

/***/ "./node_modules/vue/dist/vue.min.js":
/*!******************************************!*\
  !*** ./node_modules/vue/dist/vue.min.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global, setImmediate) {/*!
 * Vue.js v2.5.16
 * (c) 2014-2018 Evan You
 * Released under the MIT License.
 */
!function(e,t){ true?module.exports=t():undefined}(this,function(){"use strict";var y=Object.freeze({});function M(e){return null==e}function D(e){return null!=e}function S(e){return!0===e}function T(e){return"string"==typeof e||"number"==typeof e||"symbol"==typeof e||"boolean"==typeof e}function P(e){return null!==e&&"object"==typeof e}var r=Object.prototype.toString;function l(e){return"[object Object]"===r.call(e)}function i(e){var t=parseFloat(String(e));return 0<=t&&Math.floor(t)===t&&isFinite(e)}function t(e){return null==e?"":"object"==typeof e?JSON.stringify(e,null,2):String(e)}function F(e){var t=parseFloat(e);return isNaN(t)?e:t}function s(e,t){for(var n=Object.create(null),r=e.split(","),i=0;i<r.length;i++)n[r[i]]=!0;return t?function(e){return n[e.toLowerCase()]}:function(e){return n[e]}}var c=s("slot,component",!0),u=s("key,ref,slot,slot-scope,is");function f(e,t){if(e.length){var n=e.indexOf(t);if(-1<n)return e.splice(n,1)}}var n=Object.prototype.hasOwnProperty;function p(e,t){return n.call(e,t)}function e(t){var n=Object.create(null);return function(e){return n[e]||(n[e]=t(e))}}var o=/-(\w)/g,g=e(function(e){return e.replace(o,function(e,t){return t?t.toUpperCase():""})}),d=e(function(e){return e.charAt(0).toUpperCase()+e.slice(1)}),a=/\B([A-Z])/g,_=e(function(e){return e.replace(a,"-$1").toLowerCase()});var v=Function.prototype.bind?function(e,t){return e.bind(t)}:function(n,r){function e(e){var t=arguments.length;return t?1<t?n.apply(r,arguments):n.call(r,e):n.call(r)}return e._length=n.length,e};function h(e,t){t=t||0;for(var n=e.length-t,r=new Array(n);n--;)r[n]=e[n+t];return r}function m(e,t){for(var n in t)e[n]=t[n];return e}function b(e){for(var t={},n=0;n<e.length;n++)e[n]&&m(t,e[n]);return t}function $(e,t,n){}var O=function(e,t,n){return!1},w=function(e){return e};function C(t,n){if(t===n)return!0;var e=P(t),r=P(n);if(!e||!r)return!e&&!r&&String(t)===String(n);try{var i=Array.isArray(t),o=Array.isArray(n);if(i&&o)return t.length===n.length&&t.every(function(e,t){return C(e,n[t])});if(i||o)return!1;var a=Object.keys(t),s=Object.keys(n);return a.length===s.length&&a.every(function(e){return C(t[e],n[e])})}catch(e){return!1}}function x(e,t){for(var n=0;n<e.length;n++)if(C(e[n],t))return n;return-1}function R(e){var t=!1;return function(){t||(t=!0,e.apply(this,arguments))}}var E="data-server-rendered",k=["component","directive","filter"],A=["beforeCreate","created","beforeMount","mounted","beforeUpdate","updated","beforeDestroy","destroyed","activated","deactivated","errorCaptured"],j={optionMergeStrategies:Object.create(null),silent:!1,productionTip:!1,devtools:!1,performance:!1,errorHandler:null,warnHandler:null,ignoredElements:[],keyCodes:Object.create(null),isReservedTag:O,isReservedAttr:O,isUnknownElement:O,getTagNamespace:$,parsePlatformTagName:w,mustUseProp:O,_lifecycleHooks:A};function N(e,t,n,r){Object.defineProperty(e,t,{value:n,enumerable:!!r,writable:!0,configurable:!0})}var L=/[^\w.$]/;var I,H="__proto__"in{},B="undefined"!=typeof window,U="undefined"!=typeof WXEnvironment&&!!WXEnvironment.platform,V=U&&WXEnvironment.platform.toLowerCase(),z=B&&window.navigator.userAgent.toLowerCase(),K=z&&/msie|trident/.test(z),J=z&&0<z.indexOf("msie 9.0"),q=z&&0<z.indexOf("edge/"),W=(z&&z.indexOf("android"),z&&/iphone|ipad|ipod|ios/.test(z)||"ios"===V),G=(z&&/chrome\/\d+/.test(z),{}.watch),Z=!1;if(B)try{var X={};Object.defineProperty(X,"passive",{get:function(){Z=!0}}),window.addEventListener("test-passive",null,X)}catch(e){}var Y=function(){return void 0===I&&(I=!B&&!U&&"undefined"!=typeof global&&"server"===global.process.env.VUE_ENV),I},Q=B&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__;function ee(e){return"function"==typeof e&&/native code/.test(e.toString())}var te,ne="undefined"!=typeof Symbol&&ee(Symbol)&&"undefined"!=typeof Reflect&&ee(Reflect.ownKeys);te="undefined"!=typeof Set&&ee(Set)?Set:function(){function e(){this.set=Object.create(null)}return e.prototype.has=function(e){return!0===this.set[e]},e.prototype.add=function(e){this.set[e]=!0},e.prototype.clear=function(){this.set=Object.create(null)},e}();var re=$,ie=0,oe=function(){this.id=ie++,this.subs=[]};oe.prototype.addSub=function(e){this.subs.push(e)},oe.prototype.removeSub=function(e){f(this.subs,e)},oe.prototype.depend=function(){oe.target&&oe.target.addDep(this)},oe.prototype.notify=function(){for(var e=this.subs.slice(),t=0,n=e.length;t<n;t++)e[t].update()},oe.target=null;var ae=[];function se(e){oe.target&&ae.push(oe.target),oe.target=e}function ce(){oe.target=ae.pop()}var le=function(e,t,n,r,i,o,a,s){this.tag=e,this.data=t,this.children=n,this.text=r,this.elm=i,this.ns=void 0,this.context=o,this.fnContext=void 0,this.fnOptions=void 0,this.fnScopeId=void 0,this.key=t&&t.key,this.componentOptions=a,this.componentInstance=void 0,this.parent=void 0,this.raw=!1,this.isStatic=!1,this.isRootInsert=!0,this.isComment=!1,this.isCloned=!1,this.isOnce=!1,this.asyncFactory=s,this.asyncMeta=void 0,this.isAsyncPlaceholder=!1},ue={child:{configurable:!0}};ue.child.get=function(){return this.componentInstance},Object.defineProperties(le.prototype,ue);var fe=function(e){void 0===e&&(e="");var t=new le;return t.text=e,t.isComment=!0,t};function pe(e){return new le(void 0,void 0,void 0,String(e))}function de(e){var t=new le(e.tag,e.data,e.children,e.text,e.elm,e.context,e.componentOptions,e.asyncFactory);return t.ns=e.ns,t.isStatic=e.isStatic,t.key=e.key,t.isComment=e.isComment,t.fnContext=e.fnContext,t.fnOptions=e.fnOptions,t.fnScopeId=e.fnScopeId,t.isCloned=!0,t}var ve=Array.prototype,he=Object.create(ve);["push","pop","shift","unshift","splice","sort","reverse"].forEach(function(o){var a=ve[o];N(he,o,function(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var n,r=a.apply(this,e),i=this.__ob__;switch(o){case"push":case"unshift":n=e;break;case"splice":n=e.slice(2)}return n&&i.observeArray(n),i.dep.notify(),r})});var me=Object.getOwnPropertyNames(he),ye=!0;function ge(e){ye=e}var _e=function(e){(this.value=e,this.dep=new oe,this.vmCount=0,N(e,"__ob__",this),Array.isArray(e))?((H?be:$e)(e,he,me),this.observeArray(e)):this.walk(e)};function be(e,t,n){e.__proto__=t}function $e(e,t,n){for(var r=0,i=n.length;r<i;r++){var o=n[r];N(e,o,t[o])}}function we(e,t){var n;if(P(e)&&!(e instanceof le))return p(e,"__ob__")&&e.__ob__ instanceof _e?n=e.__ob__:ye&&!Y()&&(Array.isArray(e)||l(e))&&Object.isExtensible(e)&&!e._isVue&&(n=new _e(e)),t&&n&&n.vmCount++,n}function Ce(n,e,r,t,i){var o=new oe,a=Object.getOwnPropertyDescriptor(n,e);if(!a||!1!==a.configurable){var s=a&&a.get;s||2!==arguments.length||(r=n[e]);var c=a&&a.set,l=!i&&we(r);Object.defineProperty(n,e,{enumerable:!0,configurable:!0,get:function(){var e=s?s.call(n):r;return oe.target&&(o.depend(),l&&(l.dep.depend(),Array.isArray(e)&&function e(t){for(var n=void 0,r=0,i=t.length;r<i;r++)(n=t[r])&&n.__ob__&&n.__ob__.dep.depend(),Array.isArray(n)&&e(n)}(e))),e},set:function(e){var t=s?s.call(n):r;e===t||e!=e&&t!=t||(c?c.call(n,e):r=e,l=!i&&we(e),o.notify())}})}}function xe(e,t,n){if(Array.isArray(e)&&i(t))return e.length=Math.max(e.length,t),e.splice(t,1,n),n;if(t in e&&!(t in Object.prototype))return e[t]=n;var r=e.__ob__;return e._isVue||r&&r.vmCount?n:r?(Ce(r.value,t,n),r.dep.notify(),n):e[t]=n}function ke(e,t){if(Array.isArray(e)&&i(t))e.splice(t,1);else{var n=e.__ob__;e._isVue||n&&n.vmCount||p(e,t)&&(delete e[t],n&&n.dep.notify())}}_e.prototype.walk=function(e){for(var t=Object.keys(e),n=0;n<t.length;n++)Ce(e,t[n])},_e.prototype.observeArray=function(e){for(var t=0,n=e.length;t<n;t++)we(e[t])};var Ae=j.optionMergeStrategies;function Oe(e,t){if(!t)return e;for(var n,r,i,o=Object.keys(t),a=0;a<o.length;a++)r=e[n=o[a]],i=t[n],p(e,n)?l(r)&&l(i)&&Oe(r,i):xe(e,n,i);return e}function Se(n,r,i){return i?function(){var e="function"==typeof r?r.call(i,i):r,t="function"==typeof n?n.call(i,i):n;return e?Oe(e,t):t}:r?n?function(){return Oe("function"==typeof r?r.call(this,this):r,"function"==typeof n?n.call(this,this):n)}:r:n}function Te(e,t){return t?e?e.concat(t):Array.isArray(t)?t:[t]:e}function Ee(e,t,n,r){var i=Object.create(e||null);return t?m(i,t):i}Ae.data=function(e,t,n){return n?Se(e,t,n):t&&"function"!=typeof t?e:Se(e,t)},A.forEach(function(e){Ae[e]=Te}),k.forEach(function(e){Ae[e+"s"]=Ee}),Ae.watch=function(e,t,n,r){if(e===G&&(e=void 0),t===G&&(t=void 0),!t)return Object.create(e||null);if(!e)return t;var i={};for(var o in m(i,e),t){var a=i[o],s=t[o];a&&!Array.isArray(a)&&(a=[a]),i[o]=a?a.concat(s):Array.isArray(s)?s:[s]}return i},Ae.props=Ae.methods=Ae.inject=Ae.computed=function(e,t,n,r){if(!e)return t;var i=Object.create(null);return m(i,e),t&&m(i,t),i},Ae.provide=Se;var je=function(e,t){return void 0===t?e:t};function Ne(n,r,i){"function"==typeof r&&(r=r.options),function(e,t){var n=e.props;if(n){var r,i,o={};if(Array.isArray(n))for(r=n.length;r--;)"string"==typeof(i=n[r])&&(o[g(i)]={type:null});else if(l(n))for(var a in n)i=n[a],o[g(a)]=l(i)?i:{type:i};e.props=o}}(r),function(e,t){var n=e.inject;if(n){var r=e.inject={};if(Array.isArray(n))for(var i=0;i<n.length;i++)r[n[i]]={from:n[i]};else if(l(n))for(var o in n){var a=n[o];r[o]=l(a)?m({from:o},a):{from:a}}}}(r),function(e){var t=e.directives;if(t)for(var n in t){var r=t[n];"function"==typeof r&&(t[n]={bind:r,update:r})}}(r);var e=r.extends;if(e&&(n=Ne(n,e,i)),r.mixins)for(var t=0,o=r.mixins.length;t<o;t++)n=Ne(n,r.mixins[t],i);var a,s={};for(a in n)c(a);for(a in r)p(n,a)||c(a);function c(e){var t=Ae[e]||je;s[e]=t(n[e],r[e],i,e)}return s}function Le(e,t,n,r){if("string"==typeof n){var i=e[t];if(p(i,n))return i[n];var o=g(n);if(p(i,o))return i[o];var a=d(o);return p(i,a)?i[a]:i[n]||i[o]||i[a]}}function Ie(e,t,n,r){var i=t[e],o=!p(n,e),a=n[e],s=Pe(Boolean,i.type);if(-1<s)if(o&&!p(i,"default"))a=!1;else if(""===a||a===_(e)){var c=Pe(String,i.type);(c<0||s<c)&&(a=!0)}if(void 0===a){a=function(e,t,n){if(!p(t,"default"))return;var r=t.default;if(e&&e.$options.propsData&&void 0===e.$options.propsData[n]&&void 0!==e._props[n])return e._props[n];return"function"==typeof r&&"Function"!==Me(t.type)?r.call(e):r}(r,i,e);var l=ye;ge(!0),we(a),ge(l)}return a}function Me(e){var t=e&&e.toString().match(/^\s*function (\w+)/);return t?t[1]:""}function De(e,t){return Me(e)===Me(t)}function Pe(e,t){if(!Array.isArray(t))return De(t,e)?0:-1;for(var n=0,r=t.length;n<r;n++)if(De(t[n],e))return n;return-1}function Fe(e,t,n){if(t)for(var r=t;r=r.$parent;){var i=r.$options.errorCaptured;if(i)for(var o=0;o<i.length;o++)try{if(!1===i[o].call(r,e,t,n))return}catch(e){Re(e,r,"errorCaptured hook")}}Re(e,t,n)}function Re(e,t,n){if(j.errorHandler)try{return j.errorHandler.call(null,e,t,n)}catch(e){He(e,null,"config.errorHandler")}He(e,t,n)}function He(e,t,n){if(!B&&!U||"undefined"==typeof console)throw e;console.error(e)}var Be,Ue,Ve=[],ze=!1;function Ke(){ze=!1;for(var e=Ve.slice(0),t=Ve.length=0;t<e.length;t++)e[t]()}var Je=!1;if("undefined"!=typeof setImmediate&&ee(setImmediate))Ue=function(){setImmediate(Ke)};else if("undefined"==typeof MessageChannel||!ee(MessageChannel)&&"[object MessageChannelConstructor]"!==MessageChannel.toString())Ue=function(){setTimeout(Ke,0)};else{var qe=new MessageChannel,We=qe.port2;qe.port1.onmessage=Ke,Ue=function(){We.postMessage(1)}}if("undefined"!=typeof Promise&&ee(Promise)){var Ge=Promise.resolve();Be=function(){Ge.then(Ke),W&&setTimeout($)}}else Be=Ue;function Ze(e,t){var n;if(Ve.push(function(){if(e)try{e.call(t)}catch(e){Fe(e,t,"nextTick")}else n&&n(t)}),ze||(ze=!0,Je?Ue():Be()),!e&&"undefined"!=typeof Promise)return new Promise(function(e){n=e})}var Xe=new te;function Ye(e){!function e(t,n){var r,i;var o=Array.isArray(t);if(!o&&!P(t)||Object.isFrozen(t)||t instanceof le)return;if(t.__ob__){var a=t.__ob__.dep.id;if(n.has(a))return;n.add(a)}if(o)for(r=t.length;r--;)e(t[r],n);else for(i=Object.keys(t),r=i.length;r--;)e(t[i[r]],n)}(e,Xe),Xe.clear()}var Qe,et=e(function(e){var t="&"===e.charAt(0),n="~"===(e=t?e.slice(1):e).charAt(0),r="!"===(e=n?e.slice(1):e).charAt(0);return{name:e=r?e.slice(1):e,once:n,capture:r,passive:t}});function tt(e){function i(){var e=arguments,t=i.fns;if(!Array.isArray(t))return t.apply(null,arguments);for(var n=t.slice(),r=0;r<n.length;r++)n[r].apply(null,e)}return i.fns=e,i}function nt(e,t,n,r,i){var o,a,s,c;for(o in e)a=e[o],s=t[o],c=et(o),M(a)||(M(s)?(M(a.fns)&&(a=e[o]=tt(a)),n(c.name,a,c.once,c.capture,c.passive,c.params)):a!==s&&(s.fns=a,e[o]=s));for(o in t)M(e[o])&&r((c=et(o)).name,t[o],c.capture)}function rt(e,t,n){var r;e instanceof le&&(e=e.data.hook||(e.data.hook={}));var i=e[t];function o(){n.apply(this,arguments),f(r.fns,o)}M(i)?r=tt([o]):D(i.fns)&&S(i.merged)?(r=i).fns.push(o):r=tt([i,o]),r.merged=!0,e[t]=r}function it(e,t,n,r,i){if(D(t)){if(p(t,n))return e[n]=t[n],i||delete t[n],!0;if(p(t,r))return e[n]=t[r],i||delete t[r],!0}return!1}function ot(e){return T(e)?[pe(e)]:Array.isArray(e)?function e(t,n){var r=[];var i,o,a,s;for(i=0;i<t.length;i++)M(o=t[i])||"boolean"==typeof o||(a=r.length-1,s=r[a],Array.isArray(o)?0<o.length&&(at((o=e(o,(n||"")+"_"+i))[0])&&at(s)&&(r[a]=pe(s.text+o[0].text),o.shift()),r.push.apply(r,o)):T(o)?at(s)?r[a]=pe(s.text+o):""!==o&&r.push(pe(o)):at(o)&&at(s)?r[a]=pe(s.text+o.text):(S(t._isVList)&&D(o.tag)&&M(o.key)&&D(n)&&(o.key="__vlist"+n+"_"+i+"__"),r.push(o)));return r}(e):void 0}function at(e){return D(e)&&D(e.text)&&!1===e.isComment}function st(e,t){return(e.__esModule||ne&&"Module"===e[Symbol.toStringTag])&&(e=e.default),P(e)?t.extend(e):e}function ct(e){return e.isComment&&e.asyncFactory}function lt(e){if(Array.isArray(e))for(var t=0;t<e.length;t++){var n=e[t];if(D(n)&&(D(n.componentOptions)||ct(n)))return n}}function ut(e,t,n){n?Qe.$once(e,t):Qe.$on(e,t)}function ft(e,t){Qe.$off(e,t)}function pt(e,t,n){Qe=e,nt(t,n||{},ut,ft),Qe=void 0}function dt(e,t){var n={};if(!e)return n;for(var r=0,i=e.length;r<i;r++){var o=e[r],a=o.data;if(a&&a.attrs&&a.attrs.slot&&delete a.attrs.slot,o.context!==t&&o.fnContext!==t||!a||null==a.slot)(n.default||(n.default=[])).push(o);else{var s=a.slot,c=n[s]||(n[s]=[]);"template"===o.tag?c.push.apply(c,o.children||[]):c.push(o)}}for(var l in n)n[l].every(vt)&&delete n[l];return n}function vt(e){return e.isComment&&!e.asyncFactory||" "===e.text}function ht(e,t){t=t||{};for(var n=0;n<e.length;n++)Array.isArray(e[n])?ht(e[n],t):t[e[n].key]=e[n].fn;return t}var mt=null;function yt(e){for(;e&&(e=e.$parent);)if(e._inactive)return!0;return!1}function gt(e,t){if(t){if(e._directInactive=!1,yt(e))return}else if(e._directInactive)return;if(e._inactive||null===e._inactive){e._inactive=!1;for(var n=0;n<e.$children.length;n++)gt(e.$children[n]);_t(e,"activated")}}function _t(t,n){se();var e=t.$options[n];if(e)for(var r=0,i=e.length;r<i;r++)try{e[r].call(t)}catch(e){Fe(e,t,n+" hook")}t._hasHookEvent&&t.$emit("hook:"+n),ce()}var bt=[],$t=[],wt={},Ct=!1,xt=!1,kt=0;function At(){var e,t;for(xt=!0,bt.sort(function(e,t){return e.id-t.id}),kt=0;kt<bt.length;kt++)t=(e=bt[kt]).id,wt[t]=null,e.run();var n=$t.slice(),r=bt.slice();kt=bt.length=$t.length=0,wt={},Ct=xt=!1,function(e){for(var t=0;t<e.length;t++)e[t]._inactive=!0,gt(e[t],!0)}(n),function(e){var t=e.length;for(;t--;){var n=e[t],r=n.vm;r._watcher===n&&r._isMounted&&_t(r,"updated")}}(r),Q&&j.devtools&&Q.emit("flush")}var Ot=0,St=function(e,t,n,r,i){this.vm=e,i&&(e._watcher=this),e._watchers.push(this),r?(this.deep=!!r.deep,this.user=!!r.user,this.lazy=!!r.lazy,this.sync=!!r.sync):this.deep=this.user=this.lazy=this.sync=!1,this.cb=n,this.id=++Ot,this.active=!0,this.dirty=this.lazy,this.deps=[],this.newDeps=[],this.depIds=new te,this.newDepIds=new te,this.expression="","function"==typeof t?this.getter=t:(this.getter=function(e){if(!L.test(e)){var n=e.split(".");return function(e){for(var t=0;t<n.length;t++){if(!e)return;e=e[n[t]]}return e}}}(t),this.getter||(this.getter=function(){})),this.value=this.lazy?void 0:this.get()};St.prototype.get=function(){var e;se(this);var t=this.vm;try{e=this.getter.call(t,t)}catch(e){if(!this.user)throw e;Fe(e,t,'getter for watcher "'+this.expression+'"')}finally{this.deep&&Ye(e),ce(),this.cleanupDeps()}return e},St.prototype.addDep=function(e){var t=e.id;this.newDepIds.has(t)||(this.newDepIds.add(t),this.newDeps.push(e),this.depIds.has(t)||e.addSub(this))},St.prototype.cleanupDeps=function(){for(var e=this.deps.length;e--;){var t=this.deps[e];this.newDepIds.has(t.id)||t.removeSub(this)}var n=this.depIds;this.depIds=this.newDepIds,this.newDepIds=n,this.newDepIds.clear(),n=this.deps,this.deps=this.newDeps,this.newDeps=n,this.newDeps.length=0},St.prototype.update=function(){this.lazy?this.dirty=!0:this.sync?this.run():function(e){var t=e.id;if(null==wt[t]){if(wt[t]=!0,xt){for(var n=bt.length-1;kt<n&&bt[n].id>e.id;)n--;bt.splice(n+1,0,e)}else bt.push(e);Ct||(Ct=!0,Ze(At))}}(this)},St.prototype.run=function(){if(this.active){var e=this.get();if(e!==this.value||P(e)||this.deep){var t=this.value;if(this.value=e,this.user)try{this.cb.call(this.vm,e,t)}catch(e){Fe(e,this.vm,'callback for watcher "'+this.expression+'"')}else this.cb.call(this.vm,e,t)}}},St.prototype.evaluate=function(){this.value=this.get(),this.dirty=!1},St.prototype.depend=function(){for(var e=this.deps.length;e--;)this.deps[e].depend()},St.prototype.teardown=function(){if(this.active){this.vm._isBeingDestroyed||f(this.vm._watchers,this);for(var e=this.deps.length;e--;)this.deps[e].removeSub(this);this.active=!1}};var Tt={enumerable:!0,configurable:!0,get:$,set:$};function Et(e,t,n){Tt.get=function(){return this[t][n]},Tt.set=function(e){this[t][n]=e},Object.defineProperty(e,n,Tt)}function jt(e){e._watchers=[];var t=e.$options;t.props&&function(n,r){var i=n.$options.propsData||{},o=n._props={},a=n.$options._propKeys=[];n.$parent&&ge(!1);var e=function(e){a.push(e);var t=Ie(e,r,i,n);Ce(o,e,t),e in n||Et(n,"_props",e)};for(var t in r)e(t);ge(!0)}(e,t.props),t.methods&&function(e,t){e.$options.props;for(var n in t)e[n]=null==t[n]?$:v(t[n],e)}(e,t.methods),t.data?function(e){var t=e.$options.data;l(t=e._data="function"==typeof t?function(e,t){se();try{return e.call(t,t)}catch(e){return Fe(e,t,"data()"),{}}finally{ce()}}(t,e):t||{})||(t={});var n=Object.keys(t),r=e.$options.props,i=(e.$options.methods,n.length);for(;i--;){var o=n[i];r&&p(r,o)||(void 0,36!==(a=(o+"").charCodeAt(0))&&95!==a&&Et(e,"_data",o))}var a;we(t,!0)}(e):we(e._data={},!0),t.computed&&function(e,t){var n=e._computedWatchers=Object.create(null),r=Y();for(var i in t){var o=t[i],a="function"==typeof o?o:o.get;r||(n[i]=new St(e,a||$,$,Nt)),i in e||Lt(e,i,o)}}(e,t.computed),t.watch&&t.watch!==G&&function(e,t){for(var n in t){var r=t[n];if(Array.isArray(r))for(var i=0;i<r.length;i++)Mt(e,n,r[i]);else Mt(e,n,r)}}(e,t.watch)}var Nt={lazy:!0};function Lt(e,t,n){var r=!Y();"function"==typeof n?(Tt.get=r?It(t):n,Tt.set=$):(Tt.get=n.get?r&&!1!==n.cache?It(t):n.get:$,Tt.set=n.set?n.set:$),Object.defineProperty(e,t,Tt)}function It(t){return function(){var e=this._computedWatchers&&this._computedWatchers[t];if(e)return e.dirty&&e.evaluate(),oe.target&&e.depend(),e.value}}function Mt(e,t,n,r){return l(n)&&(n=(r=n).handler),"string"==typeof n&&(n=e[n]),e.$watch(t,n,r)}function Dt(t,e){if(t){for(var n=Object.create(null),r=ne?Reflect.ownKeys(t).filter(function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}):Object.keys(t),i=0;i<r.length;i++){for(var o=r[i],a=t[o].from,s=e;s;){if(s._provided&&p(s._provided,a)){n[o]=s._provided[a];break}s=s.$parent}if(!s&&"default"in t[o]){var c=t[o].default;n[o]="function"==typeof c?c.call(e):c}}return n}}function Pt(e,t){var n,r,i,o,a;if(Array.isArray(e)||"string"==typeof e)for(n=new Array(e.length),r=0,i=e.length;r<i;r++)n[r]=t(e[r],r);else if("number"==typeof e)for(n=new Array(e),r=0;r<e;r++)n[r]=t(r+1,r);else if(P(e))for(o=Object.keys(e),n=new Array(o.length),r=0,i=o.length;r<i;r++)a=o[r],n[r]=t(e[a],a,r);return D(n)&&(n._isVList=!0),n}function Ft(e,t,n,r){var i,o=this.$scopedSlots[e];if(o)n=n||{},r&&(n=m(m({},r),n)),i=o(n)||t;else{var a=this.$slots[e];a&&(a._rendered=!0),i=a||t}var s=n&&n.slot;return s?this.$createElement("template",{slot:s},i):i}function Rt(e){return Le(this.$options,"filters",e)||w}function Ht(e,t){return Array.isArray(e)?-1===e.indexOf(t):e!==t}function Bt(e,t,n,r,i){var o=j.keyCodes[t]||n;return i&&r&&!j.keyCodes[t]?Ht(i,r):o?Ht(o,e):r?_(r)!==t:void 0}function Ut(n,r,i,o,a){if(i)if(P(i)){var s;Array.isArray(i)&&(i=b(i));var e=function(t){if("class"===t||"style"===t||u(t))s=n;else{var e=n.attrs&&n.attrs.type;s=o||j.mustUseProp(r,e,t)?n.domProps||(n.domProps={}):n.attrs||(n.attrs={})}t in s||(s[t]=i[t],a&&((n.on||(n.on={}))["update:"+t]=function(e){i[t]=e}))};for(var t in i)e(t)}else;return n}function Vt(e,t){var n=this._staticTrees||(this._staticTrees=[]),r=n[e];return r&&!t||Kt(r=n[e]=this.$options.staticRenderFns[e].call(this._renderProxy,null,this),"__static__"+e,!1),r}function zt(e,t,n){return Kt(e,"__once__"+t+(n?"_"+n:""),!0),e}function Kt(e,t,n){if(Array.isArray(e))for(var r=0;r<e.length;r++)e[r]&&"string"!=typeof e[r]&&Jt(e[r],t+"_"+r,n);else Jt(e,t,n)}function Jt(e,t,n){e.isStatic=!0,e.key=t,e.isOnce=n}function qt(e,t){if(t)if(l(t)){var n=e.on=e.on?m({},e.on):{};for(var r in t){var i=n[r],o=t[r];n[r]=i?[].concat(i,o):o}}else;return e}function Wt(e){e._o=zt,e._n=F,e._s=t,e._l=Pt,e._t=Ft,e._q=C,e._i=x,e._m=Vt,e._f=Rt,e._k=Bt,e._b=Ut,e._v=pe,e._e=fe,e._u=ht,e._g=qt}function Gt(e,t,n,o,r){var a,s=r.options;p(o,"_uid")?(a=Object.create(o))._original=o:o=(a=o)._original;var i=S(s._compiled),c=!i;this.data=e,this.props=t,this.children=n,this.parent=o,this.listeners=e.on||y,this.injections=Dt(s.inject,o),this.slots=function(){return dt(n,o)},i&&(this.$options=s,this.$slots=this.slots(),this.$scopedSlots=e.scopedSlots||y),s._scopeId?this._c=function(e,t,n,r){var i=rn(a,e,t,n,r,c);return i&&!Array.isArray(i)&&(i.fnScopeId=s._scopeId,i.fnContext=o),i}:this._c=function(e,t,n,r){return rn(a,e,t,n,r,c)}}function Zt(e,t,n,r){var i=de(e);return i.fnContext=n,i.fnOptions=r,t.slot&&((i.data||(i.data={})).slot=t.slot),i}function Xt(e,t){for(var n in t)e[g(n)]=t[n]}Wt(Gt.prototype);var Yt={init:function(e,t,n,r){if(e.componentInstance&&!e.componentInstance._isDestroyed&&e.data.keepAlive){var i=e;Yt.prepatch(i,i)}else{(e.componentInstance=function(e,t,n,r){var i={_isComponent:!0,parent:t,_parentVnode:e,_parentElm:n||null,_refElm:r||null},o=e.data.inlineTemplate;D(o)&&(i.render=o.render,i.staticRenderFns=o.staticRenderFns);return new e.componentOptions.Ctor(i)}(e,mt,n,r)).$mount(t?e.elm:void 0,t)}},prepatch:function(e,t){var n=t.componentOptions;!function(e,t,n,r,i){var o=!!(i||e.$options._renderChildren||r.data.scopedSlots||e.$scopedSlots!==y);if(e.$options._parentVnode=r,e.$vnode=r,e._vnode&&(e._vnode.parent=r),e.$options._renderChildren=i,e.$attrs=r.data.attrs||y,e.$listeners=n||y,t&&e.$options.props){ge(!1);for(var a=e._props,s=e.$options._propKeys||[],c=0;c<s.length;c++){var l=s[c],u=e.$options.props;a[l]=Ie(l,u,t,e)}ge(!0),e.$options.propsData=t}n=n||y;var f=e.$options._parentListeners;e.$options._parentListeners=n,pt(e,n,f),o&&(e.$slots=dt(i,r.context),e.$forceUpdate())}(t.componentInstance=e.componentInstance,n.propsData,n.listeners,t,n.children)},insert:function(e){var t,n=e.context,r=e.componentInstance;r._isMounted||(r._isMounted=!0,_t(r,"mounted")),e.data.keepAlive&&(n._isMounted?((t=r)._inactive=!1,$t.push(t)):gt(r,!0))},destroy:function(e){var t=e.componentInstance;t._isDestroyed||(e.data.keepAlive?function e(t,n){if(!(n&&(t._directInactive=!0,yt(t))||t._inactive)){t._inactive=!0;for(var r=0;r<t.$children.length;r++)e(t.$children[r]);_t(t,"deactivated")}}(t,!0):t.$destroy())}},Qt=Object.keys(Yt);function en(e,t,n,r,i){if(!M(e)){var o=n.$options._base;if(P(e)&&(e=o.extend(e)),"function"==typeof e){var a,s,c,l,u,f,p;if(M(e.cid)&&void 0===(e=function(t,n,e){if(S(t.error)&&D(t.errorComp))return t.errorComp;if(D(t.resolved))return t.resolved;if(S(t.loading)&&D(t.loadingComp))return t.loadingComp;if(!D(t.contexts)){var r=t.contexts=[e],i=!0,o=function(){for(var e=0,t=r.length;e<t;e++)r[e].$forceUpdate()},a=R(function(e){t.resolved=st(e,n),i||o()}),s=R(function(e){D(t.errorComp)&&(t.error=!0,o())}),c=t(a,s);return P(c)&&("function"==typeof c.then?M(t.resolved)&&c.then(a,s):D(c.component)&&"function"==typeof c.component.then&&(c.component.then(a,s),D(c.error)&&(t.errorComp=st(c.error,n)),D(c.loading)&&(t.loadingComp=st(c.loading,n),0===c.delay?t.loading=!0:setTimeout(function(){M(t.resolved)&&M(t.error)&&(t.loading=!0,o())},c.delay||200)),D(c.timeout)&&setTimeout(function(){M(t.resolved)&&s(null)},c.timeout))),i=!1,t.loading?t.loadingComp:t.resolved}t.contexts.push(e)}(a=e,o,n)))return s=a,c=t,l=n,u=r,f=i,(p=fe()).asyncFactory=s,p.asyncMeta={data:c,context:l,children:u,tag:f},p;t=t||{},dn(e),D(t.model)&&function(e,t){var n=e.model&&e.model.prop||"value",r=e.model&&e.model.event||"input";(t.props||(t.props={}))[n]=t.model.value;var i=t.on||(t.on={});D(i[r])?i[r]=[t.model.callback].concat(i[r]):i[r]=t.model.callback}(e.options,t);var d=function(e,t,n){var r=t.options.props;if(!M(r)){var i={},o=e.attrs,a=e.props;if(D(o)||D(a))for(var s in r){var c=_(s);it(i,a,s,c,!0)||it(i,o,s,c,!1)}return i}}(t,e);if(S(e.options.functional))return function(e,t,n,r,i){var o=e.options,a={},s=o.props;if(D(s))for(var c in s)a[c]=Ie(c,s,t||y);else D(n.attrs)&&Xt(a,n.attrs),D(n.props)&&Xt(a,n.props);var l=new Gt(n,a,i,r,e),u=o.render.call(null,l._c,l);if(u instanceof le)return Zt(u,n,l.parent,o);if(Array.isArray(u)){for(var f=ot(u)||[],p=new Array(f.length),d=0;d<f.length;d++)p[d]=Zt(f[d],n,l.parent,o);return p}}(e,d,t,n,r);var v=t.on;if(t.on=t.nativeOn,S(e.options.abstract)){var h=t.slot;t={},h&&(t.slot=h)}!function(e){for(var t=e.hook||(e.hook={}),n=0;n<Qt.length;n++){var r=Qt[n];t[r]=Yt[r]}}(t);var m=e.options.name||i;return new le("vue-component-"+e.cid+(m?"-"+m:""),t,void 0,void 0,void 0,n,{Ctor:e,propsData:d,listeners:v,tag:i,children:r},a)}}}var tn=1,nn=2;function rn(e,t,n,r,i,o){return(Array.isArray(n)||T(n))&&(i=r,r=n,n=void 0),S(o)&&(i=nn),function(e,t,n,r,i){if(D(n)&&D(n.__ob__))return fe();D(n)&&D(n.is)&&(t=n.is);if(!t)return fe();Array.isArray(r)&&"function"==typeof r[0]&&((n=n||{}).scopedSlots={default:r[0]},r.length=0);i===nn?r=ot(r):i===tn&&(r=function(e){for(var t=0;t<e.length;t++)if(Array.isArray(e[t]))return Array.prototype.concat.apply([],e);return e}(r));var o,a;if("string"==typeof t){var s;a=e.$vnode&&e.$vnode.ns||j.getTagNamespace(t),o=j.isReservedTag(t)?new le(j.parsePlatformTagName(t),n,r,void 0,void 0,e):D(s=Le(e.$options,"components",t))?en(s,n,e,r,t):new le(t,n,r,void 0,void 0,e)}else o=en(t,n,e,r);return Array.isArray(o)?o:D(o)?(D(a)&&function e(t,n,r){t.ns=n;"foreignObject"===t.tag&&(n=void 0,r=!0);if(D(t.children))for(var i=0,o=t.children.length;i<o;i++){var a=t.children[i];D(a.tag)&&(M(a.ns)||S(r)&&"svg"!==a.tag)&&e(a,n,r)}}(o,a),D(n)&&function(e){P(e.style)&&Ye(e.style);P(e.class)&&Ye(e.class)}(n),o):fe()}(e,t,n,r,i)}var on,an,sn,cn,ln,un,fn,pn=0;function dn(e){var t=e.options;if(e.super){var n=dn(e.super);if(n!==e.superOptions){e.superOptions=n;var r=function(e){var t,n=e.options,r=e.extendOptions,i=e.sealedOptions;for(var o in n)n[o]!==i[o]&&(t||(t={}),t[o]=vn(n[o],r[o],i[o]));return t}(e);r&&m(e.extendOptions,r),(t=e.options=Ne(n,e.extendOptions)).name&&(t.components[t.name]=e)}}return t}function vn(e,t,n){if(Array.isArray(e)){var r=[];n=Array.isArray(n)?n:[n],t=Array.isArray(t)?t:[t];for(var i=0;i<e.length;i++)(0<=t.indexOf(e[i])||n.indexOf(e[i])<0)&&r.push(e[i]);return r}return e}function hn(e){this._init(e)}function mn(e){e.cid=0;var a=1;e.extend=function(e){e=e||{};var t=this,n=t.cid,r=e._Ctor||(e._Ctor={});if(r[n])return r[n];var i=e.name||t.options.name,o=function(e){this._init(e)};return((o.prototype=Object.create(t.prototype)).constructor=o).cid=a++,o.options=Ne(t.options,e),o.super=t,o.options.props&&function(e){var t=e.options.props;for(var n in t)Et(e.prototype,"_props",n)}(o),o.options.computed&&function(e){var t=e.options.computed;for(var n in t)Lt(e.prototype,n,t[n])}(o),o.extend=t.extend,o.mixin=t.mixin,o.use=t.use,k.forEach(function(e){o[e]=t[e]}),i&&(o.options.components[i]=o),o.superOptions=t.options,o.extendOptions=e,o.sealedOptions=m({},o.options),r[n]=o}}function yn(e){return e&&(e.Ctor.options.name||e.tag)}function gn(e,t){return Array.isArray(e)?-1<e.indexOf(t):"string"==typeof e?-1<e.split(",").indexOf(t):(n=e,"[object RegExp]"===r.call(n)&&e.test(t));var n}function _n(e,t){var n=e.cache,r=e.keys,i=e._vnode;for(var o in n){var a=n[o];if(a){var s=yn(a.componentOptions);s&&!t(s)&&bn(n,o,r,i)}}}function bn(e,t,n,r){var i=e[t];!i||r&&i.tag===r.tag||i.componentInstance.$destroy(),e[t]=null,f(n,t)}hn.prototype._init=function(e){var t,n,r,i,o=this;o._uid=pn++,o._isVue=!0,e&&e._isComponent?function(e,t){var n=e.$options=Object.create(e.constructor.options),r=t._parentVnode;n.parent=t.parent,n._parentVnode=r,n._parentElm=t._parentElm,n._refElm=t._refElm;var i=r.componentOptions;n.propsData=i.propsData,n._parentListeners=i.listeners,n._renderChildren=i.children,n._componentTag=i.tag,t.render&&(n.render=t.render,n.staticRenderFns=t.staticRenderFns)}(o,e):o.$options=Ne(dn(o.constructor),e||{},o),function(e){var t=e.$options,n=t.parent;if(n&&!t.abstract){for(;n.$options.abstract&&n.$parent;)n=n.$parent;n.$children.push(e)}e.$parent=n,e.$root=n?n.$root:e,e.$children=[],e.$refs={},e._watcher=null,e._inactive=null,e._directInactive=!1,e._isMounted=!1,e._isDestroyed=!1,e._isBeingDestroyed=!1}((o._renderProxy=o)._self=o),function(e){e._events=Object.create(null),e._hasHookEvent=!1;var t=e.$options._parentListeners;t&&pt(e,t)}(o),function(i){i._vnode=null,i._staticTrees=null;var e=i.$options,t=i.$vnode=e._parentVnode,n=t&&t.context;i.$slots=dt(e._renderChildren,n),i.$scopedSlots=y,i._c=function(e,t,n,r){return rn(i,e,t,n,r,!1)},i.$createElement=function(e,t,n,r){return rn(i,e,t,n,r,!0)};var r=t&&t.data;Ce(i,"$attrs",r&&r.attrs||y,null,!0),Ce(i,"$listeners",e._parentListeners||y,null,!0)}(o),_t(o,"beforeCreate"),(n=Dt((t=o).$options.inject,t))&&(ge(!1),Object.keys(n).forEach(function(e){Ce(t,e,n[e])}),ge(!0)),jt(o),(i=(r=o).$options.provide)&&(r._provided="function"==typeof i?i.call(r):i),_t(o,"created"),o.$options.el&&o.$mount(o.$options.el)},on=hn,an={get:function(){return this._data}},sn={get:function(){return this._props}},Object.defineProperty(on.prototype,"$data",an),Object.defineProperty(on.prototype,"$props",sn),on.prototype.$set=xe,on.prototype.$delete=ke,on.prototype.$watch=function(e,t,n){if(l(t))return Mt(this,e,t,n);(n=n||{}).user=!0;var r=new St(this,e,t,n);return n.immediate&&t.call(this,r.value),function(){r.teardown()}},ln=/^hook:/,(cn=hn).prototype.$on=function(e,t){if(Array.isArray(e))for(var n=0,r=e.length;n<r;n++)this.$on(e[n],t);else(this._events[e]||(this._events[e]=[])).push(t),ln.test(e)&&(this._hasHookEvent=!0);return this},cn.prototype.$once=function(e,t){var n=this;function r(){n.$off(e,r),t.apply(n,arguments)}return r.fn=t,n.$on(e,r),n},cn.prototype.$off=function(e,t){var n=this;if(!arguments.length)return n._events=Object.create(null),n;if(Array.isArray(e)){for(var r=0,i=e.length;r<i;r++)this.$off(e[r],t);return n}var o=n._events[e];if(!o)return n;if(!t)return n._events[e]=null,n;if(t)for(var a,s=o.length;s--;)if((a=o[s])===t||a.fn===t){o.splice(s,1);break}return n},cn.prototype.$emit=function(t){var n=this,e=n._events[t];if(e){e=1<e.length?h(e):e;for(var r=h(arguments,1),i=0,o=e.length;i<o;i++)try{e[i].apply(n,r)}catch(e){Fe(e,n,'event handler for "'+t+'"')}}return n},(un=hn).prototype._update=function(e,t){var n=this;n._isMounted&&_t(n,"beforeUpdate");var r=n.$el,i=n._vnode,o=mt;(mt=n)._vnode=e,i?n.$el=n.__patch__(i,e):(n.$el=n.__patch__(n.$el,e,t,!1,n.$options._parentElm,n.$options._refElm),n.$options._parentElm=n.$options._refElm=null),mt=o,r&&(r.__vue__=null),n.$el&&(n.$el.__vue__=n),n.$vnode&&n.$parent&&n.$vnode===n.$parent._vnode&&(n.$parent.$el=n.$el)},un.prototype.$forceUpdate=function(){this._watcher&&this._watcher.update()},un.prototype.$destroy=function(){var e=this;if(!e._isBeingDestroyed){_t(e,"beforeDestroy"),e._isBeingDestroyed=!0;var t=e.$parent;!t||t._isBeingDestroyed||e.$options.abstract||f(t.$children,e),e._watcher&&e._watcher.teardown();for(var n=e._watchers.length;n--;)e._watchers[n].teardown();e._data.__ob__&&e._data.__ob__.vmCount--,e._isDestroyed=!0,e.__patch__(e._vnode,null),_t(e,"destroyed"),e.$off(),e.$el&&(e.$el.__vue__=null),e.$vnode&&(e.$vnode.parent=null)}},Wt((fn=hn).prototype),fn.prototype.$nextTick=function(e){return Ze(e,this)},fn.prototype._render=function(){var t,n=this,e=n.$options,r=e.render,i=e._parentVnode;i&&(n.$scopedSlots=i.data.scopedSlots||y),n.$vnode=i;try{t=r.call(n._renderProxy,n.$createElement)}catch(e){Fe(e,n,"render"),t=n._vnode}return t instanceof le||(t=fe()),t.parent=i,t};var $n,wn,Cn,xn=[String,RegExp,Array],kn={KeepAlive:{name:"keep-alive",abstract:!0,props:{include:xn,exclude:xn,max:[String,Number]},created:function(){this.cache=Object.create(null),this.keys=[]},destroyed:function(){for(var e in this.cache)bn(this.cache,e,this.keys)},mounted:function(){var e=this;this.$watch("include",function(t){_n(e,function(e){return gn(t,e)})}),this.$watch("exclude",function(t){_n(e,function(e){return!gn(t,e)})})},render:function(){var e=this.$slots.default,t=lt(e),n=t&&t.componentOptions;if(n){var r=yn(n),i=this.include,o=this.exclude;if(i&&(!r||!gn(i,r))||o&&r&&gn(o,r))return t;var a=this.cache,s=this.keys,c=null==t.key?n.Ctor.cid+(n.tag?"::"+n.tag:""):t.key;a[c]?(t.componentInstance=a[c].componentInstance,f(s,c),s.push(c)):(a[c]=t,s.push(c),this.max&&s.length>parseInt(this.max)&&bn(a,s[0],s,this._vnode)),t.data.keepAlive=!0}return t||e&&e[0]}}};$n=hn,Cn={get:function(){return j}},Object.defineProperty($n,"config",Cn),$n.util={warn:re,extend:m,mergeOptions:Ne,defineReactive:Ce},$n.set=xe,$n.delete=ke,$n.nextTick=Ze,$n.options=Object.create(null),k.forEach(function(e){$n.options[e+"s"]=Object.create(null)}),m(($n.options._base=$n).options.components,kn),$n.use=function(e){var t=this._installedPlugins||(this._installedPlugins=[]);if(-1<t.indexOf(e))return this;var n=h(arguments,1);return n.unshift(this),"function"==typeof e.install?e.install.apply(e,n):"function"==typeof e&&e.apply(null,n),t.push(e),this},$n.mixin=function(e){return this.options=Ne(this.options,e),this},mn($n),wn=$n,k.forEach(function(n){wn[n]=function(e,t){return t?("component"===n&&l(t)&&(t.name=t.name||e,t=this.options._base.extend(t)),"directive"===n&&"function"==typeof t&&(t={bind:t,update:t}),this.options[n+"s"][e]=t):this.options[n+"s"][e]}}),Object.defineProperty(hn.prototype,"$isServer",{get:Y}),Object.defineProperty(hn.prototype,"$ssrContext",{get:function(){return this.$vnode&&this.$vnode.ssrContext}}),Object.defineProperty(hn,"FunctionalRenderContext",{value:Gt}),hn.version="2.5.16";var An=s("style,class"),On=s("input,textarea,option,select,progress"),Sn=function(e,t,n){return"value"===n&&On(e)&&"button"!==t||"selected"===n&&"option"===e||"checked"===n&&"input"===e||"muted"===n&&"video"===e},Tn=s("contenteditable,draggable,spellcheck"),En=s("allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,default,defaultchecked,defaultmuted,defaultselected,defer,disabled,enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,required,reversed,scoped,seamless,selected,sortable,translate,truespeed,typemustmatch,visible"),jn="http://www.w3.org/1999/xlink",Nn=function(e){return":"===e.charAt(5)&&"xlink"===e.slice(0,5)},Ln=function(e){return Nn(e)?e.slice(6,e.length):""},In=function(e){return null==e||!1===e};function Mn(e){for(var t=e.data,n=e,r=e;D(r.componentInstance);)(r=r.componentInstance._vnode)&&r.data&&(t=Dn(r.data,t));for(;D(n=n.parent);)n&&n.data&&(t=Dn(t,n.data));return function(e,t){if(D(e)||D(t))return Pn(e,Fn(t));return""}(t.staticClass,t.class)}function Dn(e,t){return{staticClass:Pn(e.staticClass,t.staticClass),class:D(e.class)?[e.class,t.class]:t.class}}function Pn(e,t){return e?t?e+" "+t:e:t||""}function Fn(e){return Array.isArray(e)?function(e){for(var t,n="",r=0,i=e.length;r<i;r++)D(t=Fn(e[r]))&&""!==t&&(n&&(n+=" "),n+=t);return n}(e):P(e)?function(e){var t="";for(var n in e)e[n]&&(t&&(t+=" "),t+=n);return t}(e):"string"==typeof e?e:""}var Rn={svg:"http://www.w3.org/2000/svg",math:"http://www.w3.org/1998/Math/MathML"},Hn=s("html,body,base,head,link,meta,style,title,address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,div,dd,dl,dt,figcaption,figure,picture,hr,img,li,main,ol,p,pre,ul,a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,embed,object,param,source,canvas,script,noscript,del,ins,caption,col,colgroup,table,thead,tbody,td,th,tr,button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,output,progress,select,textarea,details,dialog,menu,menuitem,summary,content,element,shadow,template,blockquote,iframe,tfoot"),Bn=s("svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view",!0),Un=function(e){return Hn(e)||Bn(e)};function Vn(e){return Bn(e)?"svg":"math"===e?"math":void 0}var zn=Object.create(null);var Kn=s("text,number,password,search,email,tel,url");function Jn(e){if("string"==typeof e){var t=document.querySelector(e);return t||document.createElement("div")}return e}var qn=Object.freeze({createElement:function(e,t){var n=document.createElement(e);return"select"!==e||t.data&&t.data.attrs&&void 0!==t.data.attrs.multiple&&n.setAttribute("multiple","multiple"),n},createElementNS:function(e,t){return document.createElementNS(Rn[e],t)},createTextNode:function(e){return document.createTextNode(e)},createComment:function(e){return document.createComment(e)},insertBefore:function(e,t,n){e.insertBefore(t,n)},removeChild:function(e,t){e.removeChild(t)},appendChild:function(e,t){e.appendChild(t)},parentNode:function(e){return e.parentNode},nextSibling:function(e){return e.nextSibling},tagName:function(e){return e.tagName},setTextContent:function(e,t){e.textContent=t},setStyleScope:function(e,t){e.setAttribute(t,"")}}),Wn={create:function(e,t){Gn(t)},update:function(e,t){e.data.ref!==t.data.ref&&(Gn(e,!0),Gn(t))},destroy:function(e){Gn(e,!0)}};function Gn(e,t){var n=e.data.ref;if(D(n)){var r=e.context,i=e.componentInstance||e.elm,o=r.$refs;t?Array.isArray(o[n])?f(o[n],i):o[n]===i&&(o[n]=void 0):e.data.refInFor?Array.isArray(o[n])?o[n].indexOf(i)<0&&o[n].push(i):o[n]=[i]:o[n]=i}}var Zn=new le("",{},[]),Xn=["create","activate","update","remove","destroy"];function Yn(e,t){return e.key===t.key&&(e.tag===t.tag&&e.isComment===t.isComment&&D(e.data)===D(t.data)&&function(e,t){if("input"!==e.tag)return!0;var n,r=D(n=e.data)&&D(n=n.attrs)&&n.type,i=D(n=t.data)&&D(n=n.attrs)&&n.type;return r===i||Kn(r)&&Kn(i)}(e,t)||S(e.isAsyncPlaceholder)&&e.asyncFactory===t.asyncFactory&&M(t.asyncFactory.error))}function Qn(e,t,n){var r,i,o={};for(r=t;r<=n;++r)D(i=e[r].key)&&(o[i]=r);return o}var er={create:tr,update:tr,destroy:function(e){tr(e,Zn)}};function tr(e,t){(e.data.directives||t.data.directives)&&function(t,n){var e,r,i,o=t===Zn,a=n===Zn,s=rr(t.data.directives,t.context),c=rr(n.data.directives,n.context),l=[],u=[];for(e in c)r=s[e],i=c[e],r?(i.oldValue=r.value,ir(i,"update",n,t),i.def&&i.def.componentUpdated&&u.push(i)):(ir(i,"bind",n,t),i.def&&i.def.inserted&&l.push(i));if(l.length){var f=function(){for(var e=0;e<l.length;e++)ir(l[e],"inserted",n,t)};o?rt(n,"insert",f):f()}u.length&&rt(n,"postpatch",function(){for(var e=0;e<u.length;e++)ir(u[e],"componentUpdated",n,t)});if(!o)for(e in s)c[e]||ir(s[e],"unbind",t,t,a)}(e,t)}var nr=Object.create(null);function rr(e,t){var n,r,i,o=Object.create(null);if(!e)return o;for(n=0;n<e.length;n++)(r=e[n]).modifiers||(r.modifiers=nr),(o[(i=r,i.rawName||i.name+"."+Object.keys(i.modifiers||{}).join("."))]=r).def=Le(t.$options,"directives",r.name);return o}function ir(t,n,r,e,i){var o=t.def&&t.def[n];if(o)try{o(r.elm,t,r,e,i)}catch(e){Fe(e,r.context,"directive "+t.name+" "+n+" hook")}}var or=[Wn,er];function ar(e,t){var n=t.componentOptions;if(!(D(n)&&!1===n.Ctor.options.inheritAttrs||M(e.data.attrs)&&M(t.data.attrs))){var r,i,o=t.elm,a=e.data.attrs||{},s=t.data.attrs||{};for(r in D(s.__ob__)&&(s=t.data.attrs=m({},s)),s)i=s[r],a[r]!==i&&sr(o,r,i);for(r in(K||q)&&s.value!==a.value&&sr(o,"value",s.value),a)M(s[r])&&(Nn(r)?o.removeAttributeNS(jn,Ln(r)):Tn(r)||o.removeAttribute(r))}}function sr(e,t,n){-1<e.tagName.indexOf("-")?cr(e,t,n):En(t)?In(n)?e.removeAttribute(t):(n="allowfullscreen"===t&&"EMBED"===e.tagName?"true":t,e.setAttribute(t,n)):Tn(t)?e.setAttribute(t,In(n)||"false"===n?"false":"true"):Nn(t)?In(n)?e.removeAttributeNS(jn,Ln(t)):e.setAttributeNS(jn,t,n):cr(e,t,n)}function cr(t,e,n){if(In(n))t.removeAttribute(e);else{if(K&&!J&&"TEXTAREA"===t.tagName&&"placeholder"===e&&!t.__ieph){var r=function(e){e.stopImmediatePropagation(),t.removeEventListener("input",r)};t.addEventListener("input",r),t.__ieph=!0}t.setAttribute(e,n)}}var lr={create:ar,update:ar};function ur(e,t){var n=t.elm,r=t.data,i=e.data;if(!(M(r.staticClass)&&M(r.class)&&(M(i)||M(i.staticClass)&&M(i.class)))){var o=Mn(t),a=n._transitionClasses;D(a)&&(o=Pn(o,Fn(a))),o!==n._prevClass&&(n.setAttribute("class",o),n._prevClass=o)}}var fr,pr,dr,vr,hr,mr,yr={create:ur,update:ur},gr=/[\w).+\-_$\]]/;function _r(e){var t,n,r,i,o,a=!1,s=!1,c=!1,l=!1,u=0,f=0,p=0,d=0;for(r=0;r<e.length;r++)if(n=t,t=e.charCodeAt(r),a)39===t&&92!==n&&(a=!1);else if(s)34===t&&92!==n&&(s=!1);else if(c)96===t&&92!==n&&(c=!1);else if(l)47===t&&92!==n&&(l=!1);else if(124!==t||124===e.charCodeAt(r+1)||124===e.charCodeAt(r-1)||u||f||p){switch(t){case 34:s=!0;break;case 39:a=!0;break;case 96:c=!0;break;case 40:p++;break;case 41:p--;break;case 91:f++;break;case 93:f--;break;case 123:u++;break;case 125:u--}if(47===t){for(var v=r-1,h=void 0;0<=v&&" "===(h=e.charAt(v));v--);h&&gr.test(h)||(l=!0)}}else void 0===i?(d=r+1,i=e.slice(0,r).trim()):m();function m(){(o||(o=[])).push(e.slice(d,r).trim()),d=r+1}if(void 0===i?i=e.slice(0,r).trim():0!==d&&m(),o)for(r=0;r<o.length;r++)i=br(i,o[r]);return i}function br(e,t){var n=t.indexOf("(");if(n<0)return'_f("'+t+'")('+e+")";var r=t.slice(0,n),i=t.slice(n+1);return'_f("'+r+'")('+e+(")"!==i?","+i:i)}function $r(e){console.error("[Vue compiler]: "+e)}function wr(e,t){return e?e.map(function(e){return e[t]}).filter(function(e){return e}):[]}function Cr(e,t,n){(e.props||(e.props=[])).push({name:t,value:n}),e.plain=!1}function xr(e,t,n){(e.attrs||(e.attrs=[])).push({name:t,value:n}),e.plain=!1}function kr(e,t,n){e.attrsMap[t]=n,e.attrsList.push({name:t,value:n})}function Ar(e,t,n,r,i,o){var a;(r=r||y).capture&&(delete r.capture,t="!"+t),r.once&&(delete r.once,t="~"+t),r.passive&&(delete r.passive,t="&"+t),"click"===t&&(r.right?(t="contextmenu",delete r.right):r.middle&&(t="mouseup")),r.native?(delete r.native,a=e.nativeEvents||(e.nativeEvents={})):a=e.events||(e.events={});var s={value:n.trim()};r!==y&&(s.modifiers=r);var c=a[t];Array.isArray(c)?i?c.unshift(s):c.push(s):a[t]=c?i?[s,c]:[c,s]:s,e.plain=!1}function Or(e,t,n){var r=Sr(e,":"+t)||Sr(e,"v-bind:"+t);if(null!=r)return _r(r);if(!1!==n){var i=Sr(e,t);if(null!=i)return JSON.stringify(i)}}function Sr(e,t,n){var r;if(null!=(r=e.attrsMap[t]))for(var i=e.attrsList,o=0,a=i.length;o<a;o++)if(i[o].name===t){i.splice(o,1);break}return n&&delete e.attrsMap[t],r}function Tr(e,t,n){var r=n||{},i=r.number,o="$$v",a=o;r.trim&&(a="(typeof $$v === 'string'? $$v.trim(): $$v)"),i&&(a="_n("+a+")");var s=Er(t,a);e.model={value:"("+t+")",expression:'"'+t+'"',callback:"function ($$v) {"+s+"}"}}function Er(e,t){var n=function(e){if(e=e.trim(),fr=e.length,e.indexOf("[")<0||e.lastIndexOf("]")<fr-1)return-1<(vr=e.lastIndexOf("."))?{exp:e.slice(0,vr),key:'"'+e.slice(vr+1)+'"'}:{exp:e,key:null};pr=e,vr=hr=mr=0;for(;!Nr();)Lr(dr=jr())?Mr(dr):91===dr&&Ir(dr);return{exp:e.slice(0,hr),key:e.slice(hr+1,mr)}}(e);return null===n.key?e+"="+t:"$set("+n.exp+", "+n.key+", "+t+")"}function jr(){return pr.charCodeAt(++vr)}function Nr(){return fr<=vr}function Lr(e){return 34===e||39===e}function Ir(e){var t=1;for(hr=vr;!Nr();)if(Lr(e=jr()))Mr(e);else if(91===e&&t++,93===e&&t--,0===t){mr=vr;break}}function Mr(e){for(var t=e;!Nr()&&(e=jr())!==t;);}var Dr,Pr="__r",Fr="__c";function Rr(e,t,n,r,i){var o,a,s,c,l;t=(o=t)._withTask||(o._withTask=function(){Je=!0;var e=o.apply(null,arguments);return Je=!1,e}),n&&(a=t,s=e,c=r,l=Dr,t=function e(){null!==a.apply(null,arguments)&&Hr(s,e,c,l)}),Dr.addEventListener(e,t,Z?{capture:r,passive:i}:r)}function Hr(e,t,n,r){(r||Dr).removeEventListener(e,t._withTask||t,n)}function Br(e,t){if(!M(e.data.on)||!M(t.data.on)){var n=t.data.on||{},r=e.data.on||{};Dr=t.elm,function(e){if(D(e[Pr])){var t=K?"change":"input";e[t]=[].concat(e[Pr],e[t]||[]),delete e[Pr]}D(e[Fr])&&(e.change=[].concat(e[Fr],e.change||[]),delete e[Fr])}(n),nt(n,r,Rr,Hr,t.context),Dr=void 0}}var Ur={create:Br,update:Br};function Vr(e,t){if(!M(e.data.domProps)||!M(t.data.domProps)){var n,r,i,o,a=t.elm,s=e.data.domProps||{},c=t.data.domProps||{};for(n in D(c.__ob__)&&(c=t.data.domProps=m({},c)),s)M(c[n])&&(a[n]="");for(n in c){if(r=c[n],"textContent"===n||"innerHTML"===n){if(t.children&&(t.children.length=0),r===s[n])continue;1===a.childNodes.length&&a.removeChild(a.childNodes[0])}if("value"===n){var l=M(a._value=r)?"":String(r);o=l,(i=a).composing||"OPTION"!==i.tagName&&!function(e,t){var n=!0;try{n=document.activeElement!==e}catch(e){}return n&&e.value!==t}(i,o)&&!function(e,t){var n=e.value,r=e._vModifiers;if(D(r)){if(r.lazy)return!1;if(r.number)return F(n)!==F(t);if(r.trim)return n.trim()!==t.trim()}return n!==t}(i,o)||(a.value=l)}else a[n]=r}}}var zr={create:Vr,update:Vr},Kr=e(function(e){var n={},r=/:(.+)/;return e.split(/;(?![^(]*\))/g).forEach(function(e){if(e){var t=e.split(r);1<t.length&&(n[t[0].trim()]=t[1].trim())}}),n});function Jr(e){var t=qr(e.style);return e.staticStyle?m(e.staticStyle,t):t}function qr(e){return Array.isArray(e)?b(e):"string"==typeof e?Kr(e):e}var Wr,Gr=/^--/,Zr=/\s*!important$/,Xr=function(e,t,n){if(Gr.test(t))e.style.setProperty(t,n);else if(Zr.test(n))e.style.setProperty(t,n.replace(Zr,""),"important");else{var r=Qr(t);if(Array.isArray(n))for(var i=0,o=n.length;i<o;i++)e.style[r]=n[i];else e.style[r]=n}},Yr=["Webkit","Moz","ms"],Qr=e(function(e){if(Wr=Wr||document.createElement("div").style,"filter"!==(e=g(e))&&e in Wr)return e;for(var t=e.charAt(0).toUpperCase()+e.slice(1),n=0;n<Yr.length;n++){var r=Yr[n]+t;if(r in Wr)return r}});function ei(e,t){var n=t.data,r=e.data;if(!(M(n.staticStyle)&&M(n.style)&&M(r.staticStyle)&&M(r.style))){var i,o,a=t.elm,s=r.staticStyle,c=r.normalizedStyle||r.style||{},l=s||c,u=qr(t.data.style)||{};t.data.normalizedStyle=D(u.__ob__)?m({},u):u;var f=function(e,t){var n,r={};if(t)for(var i=e;i.componentInstance;)(i=i.componentInstance._vnode)&&i.data&&(n=Jr(i.data))&&m(r,n);(n=Jr(e.data))&&m(r,n);for(var o=e;o=o.parent;)o.data&&(n=Jr(o.data))&&m(r,n);return r}(t,!0);for(o in l)M(f[o])&&Xr(a,o,"");for(o in f)(i=f[o])!==l[o]&&Xr(a,o,null==i?"":i)}}var ti={create:ei,update:ei};function ni(t,e){if(e&&(e=e.trim()))if(t.classList)-1<e.indexOf(" ")?e.split(/\s+/).forEach(function(e){return t.classList.add(e)}):t.classList.add(e);else{var n=" "+(t.getAttribute("class")||"")+" ";n.indexOf(" "+e+" ")<0&&t.setAttribute("class",(n+e).trim())}}function ri(t,e){if(e&&(e=e.trim()))if(t.classList)-1<e.indexOf(" ")?e.split(/\s+/).forEach(function(e){return t.classList.remove(e)}):t.classList.remove(e),t.classList.length||t.removeAttribute("class");else{for(var n=" "+(t.getAttribute("class")||"")+" ",r=" "+e+" ";0<=n.indexOf(r);)n=n.replace(r," ");(n=n.trim())?t.setAttribute("class",n):t.removeAttribute("class")}}function ii(e){if(e){if("object"==typeof e){var t={};return!1!==e.css&&m(t,oi(e.name||"v")),m(t,e),t}return"string"==typeof e?oi(e):void 0}}var oi=e(function(e){return{enterClass:e+"-enter",enterToClass:e+"-enter-to",enterActiveClass:e+"-enter-active",leaveClass:e+"-leave",leaveToClass:e+"-leave-to",leaveActiveClass:e+"-leave-active"}}),ai=B&&!J,si="transition",ci="animation",li="transition",ui="transitionend",fi="animation",pi="animationend";ai&&(void 0===window.ontransitionend&&void 0!==window.onwebkittransitionend&&(li="WebkitTransition",ui="webkitTransitionEnd"),void 0===window.onanimationend&&void 0!==window.onwebkitanimationend&&(fi="WebkitAnimation",pi="webkitAnimationEnd"));var di=B?window.requestAnimationFrame?window.requestAnimationFrame.bind(window):setTimeout:function(e){return e()};function vi(e){di(function(){di(e)})}function hi(e,t){var n=e._transitionClasses||(e._transitionClasses=[]);n.indexOf(t)<0&&(n.push(t),ni(e,t))}function mi(e,t){e._transitionClasses&&f(e._transitionClasses,t),ri(e,t)}function yi(t,e,n){var r=_i(t,e),i=r.type,o=r.timeout,a=r.propCount;if(!i)return n();var s=i===si?ui:pi,c=0,l=function(){t.removeEventListener(s,u),n()},u=function(e){e.target===t&&++c>=a&&l()};setTimeout(function(){c<a&&l()},o+1),t.addEventListener(s,u)}var gi=/\b(transform|all)(,|$)/;function _i(e,t){var n,r=window.getComputedStyle(e),i=r[li+"Delay"].split(", "),o=r[li+"Duration"].split(", "),a=bi(i,o),s=r[fi+"Delay"].split(", "),c=r[fi+"Duration"].split(", "),l=bi(s,c),u=0,f=0;return t===si?0<a&&(n=si,u=a,f=o.length):t===ci?0<l&&(n=ci,u=l,f=c.length):f=(n=0<(u=Math.max(a,l))?l<a?si:ci:null)?n===si?o.length:c.length:0,{type:n,timeout:u,propCount:f,hasTransform:n===si&&gi.test(r[li+"Property"])}}function bi(n,e){for(;n.length<e.length;)n=n.concat(n);return Math.max.apply(null,e.map(function(e,t){return $i(e)+$i(n[t])}))}function $i(e){return 1e3*Number(e.slice(0,-1))}function wi(n,e){var r=n.elm;D(r._leaveCb)&&(r._leaveCb.cancelled=!0,r._leaveCb());var t=ii(n.data.transition);if(!M(t)&&!D(r._enterCb)&&1===r.nodeType){for(var i=t.css,o=t.type,a=t.enterClass,s=t.enterToClass,c=t.enterActiveClass,l=t.appearClass,u=t.appearToClass,f=t.appearActiveClass,p=t.beforeEnter,d=t.enter,v=t.afterEnter,h=t.enterCancelled,m=t.beforeAppear,y=t.appear,g=t.afterAppear,_=t.appearCancelled,b=t.duration,$=mt,w=mt.$vnode;w&&w.parent;)$=(w=w.parent).context;var C=!$._isMounted||!n.isRootInsert;if(!C||y||""===y){var x=C&&l?l:a,k=C&&f?f:c,A=C&&u?u:s,O=C&&m||p,S=C&&"function"==typeof y?y:d,T=C&&g||v,E=C&&_||h,j=F(P(b)?b.enter:b),N=!1!==i&&!J,L=ki(S),I=r._enterCb=R(function(){N&&(mi(r,A),mi(r,k)),I.cancelled?(N&&mi(r,x),E&&E(r)):T&&T(r),r._enterCb=null});n.data.show||rt(n,"insert",function(){var e=r.parentNode,t=e&&e._pending&&e._pending[n.key];t&&t.tag===n.tag&&t.elm._leaveCb&&t.elm._leaveCb(),S&&S(r,I)}),O&&O(r),N&&(hi(r,x),hi(r,k),vi(function(){mi(r,x),I.cancelled||(hi(r,A),L||(xi(j)?setTimeout(I,j):yi(r,o,I)))})),n.data.show&&(e&&e(),S&&S(r,I)),N||L||I()}}}function Ci(e,t){var n=e.elm;D(n._enterCb)&&(n._enterCb.cancelled=!0,n._enterCb());var r=ii(e.data.transition);if(M(r)||1!==n.nodeType)return t();if(!D(n._leaveCb)){var i=r.css,o=r.type,a=r.leaveClass,s=r.leaveToClass,c=r.leaveActiveClass,l=r.beforeLeave,u=r.leave,f=r.afterLeave,p=r.leaveCancelled,d=r.delayLeave,v=r.duration,h=!1!==i&&!J,m=ki(u),y=F(P(v)?v.leave:v),g=n._leaveCb=R(function(){n.parentNode&&n.parentNode._pending&&(n.parentNode._pending[e.key]=null),h&&(mi(n,s),mi(n,c)),g.cancelled?(h&&mi(n,a),p&&p(n)):(t(),f&&f(n)),n._leaveCb=null});d?d(_):_()}function _(){g.cancelled||(e.data.show||((n.parentNode._pending||(n.parentNode._pending={}))[e.key]=e),l&&l(n),h&&(hi(n,a),hi(n,c),vi(function(){mi(n,a),g.cancelled||(hi(n,s),m||(xi(y)?setTimeout(g,y):yi(n,o,g)))})),u&&u(n,g),h||m||g())}}function xi(e){return"number"==typeof e&&!isNaN(e)}function ki(e){if(M(e))return!1;var t=e.fns;return D(t)?ki(Array.isArray(t)?t[0]:t):1<(e._length||e.length)}function Ai(e,t){!0!==t.data.show&&wi(t)}var Oi=function(e){var r,t,g={},n=e.modules,_=e.nodeOps;for(r=0;r<Xn.length;++r)for(g[Xn[r]]=[],t=0;t<n.length;++t)D(n[t][Xn[r]])&&g[Xn[r]].push(n[t][Xn[r]]);function o(e){var t=_.parentNode(e);D(t)&&_.removeChild(t,e)}function b(e,t,n,r,i,o,a){if(D(e.elm)&&D(o)&&(e=o[a]=de(e)),e.isRootInsert=!i,!function(e,t,n,r){var i=e.data;if(D(i)){var o=D(e.componentInstance)&&i.keepAlive;if(D(i=i.hook)&&D(i=i.init)&&i(e,!1,n,r),D(e.componentInstance))return d(e,t),S(o)&&function(e,t,n,r){for(var i,o=e;o.componentInstance;)if(o=o.componentInstance._vnode,D(i=o.data)&&D(i=i.transition)){for(i=0;i<g.activate.length;++i)g.activate[i](Zn,o);t.push(o);break}u(n,e.elm,r)}(e,t,n,r),!0}}(e,t,n,r)){var s=e.data,c=e.children,l=e.tag;D(l)?(e.elm=e.ns?_.createElementNS(e.ns,l):_.createElement(l,e),f(e),v(e,c,t),D(s)&&h(e,t)):S(e.isComment)?e.elm=_.createComment(e.text):e.elm=_.createTextNode(e.text),u(n,e.elm,r)}}function d(e,t){D(e.data.pendingInsert)&&(t.push.apply(t,e.data.pendingInsert),e.data.pendingInsert=null),e.elm=e.componentInstance.$el,$(e)?(h(e,t),f(e)):(Gn(e),t.push(e))}function u(e,t,n){D(e)&&(D(n)?n.parentNode===e&&_.insertBefore(e,t,n):_.appendChild(e,t))}function v(e,t,n){if(Array.isArray(t))for(var r=0;r<t.length;++r)b(t[r],n,e.elm,null,!0,t,r);else T(e.text)&&_.appendChild(e.elm,_.createTextNode(String(e.text)))}function $(e){for(;e.componentInstance;)e=e.componentInstance._vnode;return D(e.tag)}function h(e,t){for(var n=0;n<g.create.length;++n)g.create[n](Zn,e);D(r=e.data.hook)&&(D(r.create)&&r.create(Zn,e),D(r.insert)&&t.push(e))}function f(e){var t;if(D(t=e.fnScopeId))_.setStyleScope(e.elm,t);else for(var n=e;n;)D(t=n.context)&&D(t=t.$options._scopeId)&&_.setStyleScope(e.elm,t),n=n.parent;D(t=mt)&&t!==e.context&&t!==e.fnContext&&D(t=t.$options._scopeId)&&_.setStyleScope(e.elm,t)}function y(e,t,n,r,i,o){for(;r<=i;++r)b(n[r],o,e,t,!1,n,r)}function w(e){var t,n,r=e.data;if(D(r))for(D(t=r.hook)&&D(t=t.destroy)&&t(e),t=0;t<g.destroy.length;++t)g.destroy[t](e);if(D(t=e.children))for(n=0;n<e.children.length;++n)w(e.children[n])}function C(e,t,n,r){for(;n<=r;++n){var i=t[n];D(i)&&(D(i.tag)?(a(i),w(i)):o(i.elm))}}function a(e,t){if(D(t)||D(e.data)){var n,r=g.remove.length+1;for(D(t)?t.listeners+=r:t=function(e,t){function n(){0==--n.listeners&&o(e)}return n.listeners=t,n}(e.elm,r),D(n=e.componentInstance)&&D(n=n._vnode)&&D(n.data)&&a(n,t),n=0;n<g.remove.length;++n)g.remove[n](e,t);D(n=e.data.hook)&&D(n=n.remove)?n(e,t):t()}else o(e.elm)}function x(e,t,n,r){for(var i=n;i<r;i++){var o=t[i];if(D(o)&&Yn(e,o))return i}}function k(e,t,n,r){if(e!==t){var i=t.elm=e.elm;if(S(e.isAsyncPlaceholder))D(t.asyncFactory.resolved)?O(e.elm,t,n):t.isAsyncPlaceholder=!0;else if(S(t.isStatic)&&S(e.isStatic)&&t.key===e.key&&(S(t.isCloned)||S(t.isOnce)))t.componentInstance=e.componentInstance;else{var o,a=t.data;D(a)&&D(o=a.hook)&&D(o=o.prepatch)&&o(e,t);var s=e.children,c=t.children;if(D(a)&&$(t)){for(o=0;o<g.update.length;++o)g.update[o](e,t);D(o=a.hook)&&D(o=o.update)&&o(e,t)}M(t.text)?D(s)&&D(c)?s!==c&&function(e,t,n,r,i){for(var o,a,s,c=0,l=0,u=t.length-1,f=t[0],p=t[u],d=n.length-1,v=n[0],h=n[d],m=!i;c<=u&&l<=d;)M(f)?f=t[++c]:M(p)?p=t[--u]:Yn(f,v)?(k(f,v,r),f=t[++c],v=n[++l]):Yn(p,h)?(k(p,h,r),p=t[--u],h=n[--d]):Yn(f,h)?(k(f,h,r),m&&_.insertBefore(e,f.elm,_.nextSibling(p.elm)),f=t[++c],h=n[--d]):(Yn(p,v)?(k(p,v,r),m&&_.insertBefore(e,p.elm,f.elm),p=t[--u]):(M(o)&&(o=Qn(t,c,u)),M(a=D(v.key)?o[v.key]:x(v,t,c,u))?b(v,r,e,f.elm,!1,n,l):Yn(s=t[a],v)?(k(s,v,r),t[a]=void 0,m&&_.insertBefore(e,s.elm,f.elm)):b(v,r,e,f.elm,!1,n,l)),v=n[++l]);u<c?y(e,M(n[d+1])?null:n[d+1].elm,n,l,d,r):d<l&&C(0,t,c,u)}(i,s,c,n,r):D(c)?(D(e.text)&&_.setTextContent(i,""),y(i,null,c,0,c.length-1,n)):D(s)?C(0,s,0,s.length-1):D(e.text)&&_.setTextContent(i,""):e.text!==t.text&&_.setTextContent(i,t.text),D(a)&&D(o=a.hook)&&D(o=o.postpatch)&&o(e,t)}}}function A(e,t,n){if(S(n)&&D(e.parent))e.parent.data.pendingInsert=t;else for(var r=0;r<t.length;++r)t[r].data.hook.insert(t[r])}var m=s("attrs,class,staticClass,staticStyle,key");function O(e,t,n,r){var i,o=t.tag,a=t.data,s=t.children;if(r=r||a&&a.pre,t.elm=e,S(t.isComment)&&D(t.asyncFactory))return t.isAsyncPlaceholder=!0;if(D(a)&&(D(i=a.hook)&&D(i=i.init)&&i(t,!0),D(i=t.componentInstance)))return d(t,n),!0;if(D(o)){if(D(s))if(e.hasChildNodes())if(D(i=a)&&D(i=i.domProps)&&D(i=i.innerHTML)){if(i!==e.innerHTML)return!1}else{for(var c=!0,l=e.firstChild,u=0;u<s.length;u++){if(!l||!O(l,s[u],n,r)){c=!1;break}l=l.nextSibling}if(!c||l)return!1}else v(t,s,n);if(D(a)){var f=!1;for(var p in a)if(!m(p)){f=!0,h(t,n);break}!f&&a.class&&Ye(a.class)}}else e.data!==t.text&&(e.data=t.text);return!0}return function(e,t,n,r,i,o){if(!M(t)){var a,s=!1,c=[];if(M(e))s=!0,b(t,c,i,o);else{var l=D(e.nodeType);if(!l&&Yn(e,t))k(e,t,c,r);else{if(l){if(1===e.nodeType&&e.hasAttribute(E)&&(e.removeAttribute(E),n=!0),S(n)&&O(e,t,c))return A(t,c,!0),e;a=e,e=new le(_.tagName(a).toLowerCase(),{},[],void 0,a)}var u=e.elm,f=_.parentNode(u);if(b(t,c,u._leaveCb?null:f,_.nextSibling(u)),D(t.parent))for(var p=t.parent,d=$(t);p;){for(var v=0;v<g.destroy.length;++v)g.destroy[v](p);if(p.elm=t.elm,d){for(var h=0;h<g.create.length;++h)g.create[h](Zn,p);var m=p.data.hook.insert;if(m.merged)for(var y=1;y<m.fns.length;y++)m.fns[y]()}else Gn(p);p=p.parent}D(f)?C(0,[e],0,0):D(e.tag)&&w(e)}}return A(t,c,s),t.elm}D(e)&&w(e)}}({nodeOps:qn,modules:[lr,yr,Ur,zr,ti,B?{create:Ai,activate:Ai,remove:function(e,t){!0!==e.data.show?Ci(e,t):t()}}:{}].concat(or)});J&&document.addEventListener("selectionchange",function(){var e=document.activeElement;e&&e.vmodel&&Mi(e,"input")});var Si={inserted:function(e,t,n,r){"select"===n.tag?(r.elm&&!r.elm._vOptions?rt(n,"postpatch",function(){Si.componentUpdated(e,t,n)}):Ti(e,t,n.context),e._vOptions=[].map.call(e.options,Ni)):("textarea"===n.tag||Kn(e.type))&&(e._vModifiers=t.modifiers,t.modifiers.lazy||(e.addEventListener("compositionstart",Li),e.addEventListener("compositionend",Ii),e.addEventListener("change",Ii),J&&(e.vmodel=!0)))},componentUpdated:function(e,t,n){if("select"===n.tag){Ti(e,t,n.context);var r=e._vOptions,i=e._vOptions=[].map.call(e.options,Ni);if(i.some(function(e,t){return!C(e,r[t])}))(e.multiple?t.value.some(function(e){return ji(e,i)}):t.value!==t.oldValue&&ji(t.value,i))&&Mi(e,"change")}}};function Ti(e,t,n){Ei(e,t,n),(K||q)&&setTimeout(function(){Ei(e,t,n)},0)}function Ei(e,t,n){var r=t.value,i=e.multiple;if(!i||Array.isArray(r)){for(var o,a,s=0,c=e.options.length;s<c;s++)if(a=e.options[s],i)o=-1<x(r,Ni(a)),a.selected!==o&&(a.selected=o);else if(C(Ni(a),r))return void(e.selectedIndex!==s&&(e.selectedIndex=s));i||(e.selectedIndex=-1)}}function ji(t,e){return e.every(function(e){return!C(e,t)})}function Ni(e){return"_value"in e?e._value:e.value}function Li(e){e.target.composing=!0}function Ii(e){e.target.composing&&(e.target.composing=!1,Mi(e.target,"input"))}function Mi(e,t){var n=document.createEvent("HTMLEvents");n.initEvent(t,!0,!0),e.dispatchEvent(n)}function Di(e){return!e.componentInstance||e.data&&e.data.transition?e:Di(e.componentInstance._vnode)}var Pi={model:Si,show:{bind:function(e,t,n){var r=t.value,i=(n=Di(n)).data&&n.data.transition,o=e.__vOriginalDisplay="none"===e.style.display?"":e.style.display;r&&i?(n.data.show=!0,wi(n,function(){e.style.display=o})):e.style.display=r?o:"none"},update:function(e,t,n){var r=t.value;!r!=!t.oldValue&&((n=Di(n)).data&&n.data.transition?(n.data.show=!0,r?wi(n,function(){e.style.display=e.__vOriginalDisplay}):Ci(n,function(){e.style.display="none"})):e.style.display=r?e.__vOriginalDisplay:"none")},unbind:function(e,t,n,r,i){i||(e.style.display=e.__vOriginalDisplay)}}},Fi={name:String,appear:Boolean,css:Boolean,mode:String,type:String,enterClass:String,leaveClass:String,enterToClass:String,leaveToClass:String,enterActiveClass:String,leaveActiveClass:String,appearClass:String,appearActiveClass:String,appearToClass:String,duration:[Number,String,Object]};function Ri(e){var t=e&&e.componentOptions;return t&&t.Ctor.options.abstract?Ri(lt(t.children)):e}function Hi(e){var t={},n=e.$options;for(var r in n.propsData)t[r]=e[r];var i=n._parentListeners;for(var o in i)t[g(o)]=i[o];return t}function Bi(e,t){if(/\d-keep-alive$/.test(t.tag))return e("keep-alive",{props:t.componentOptions.propsData})}var Ui={name:"transition",props:Fi,abstract:!0,render:function(e){var t=this,n=this.$slots.default;if(n&&(n=n.filter(function(e){return e.tag||ct(e)})).length){var r=this.mode,i=n[0];if(function(e){for(;e=e.parent;)if(e.data.transition)return!0}(this.$vnode))return i;var o=Ri(i);if(!o)return i;if(this._leaving)return Bi(e,i);var a="__transition-"+this._uid+"-";o.key=null==o.key?o.isComment?a+"comment":a+o.tag:T(o.key)?0===String(o.key).indexOf(a)?o.key:a+o.key:o.key;var s,c,l=(o.data||(o.data={})).transition=Hi(this),u=this._vnode,f=Ri(u);if(o.data.directives&&o.data.directives.some(function(e){return"show"===e.name})&&(o.data.show=!0),f&&f.data&&(s=o,(c=f).key!==s.key||c.tag!==s.tag)&&!ct(f)&&(!f.componentInstance||!f.componentInstance._vnode.isComment)){var p=f.data.transition=m({},l);if("out-in"===r)return this._leaving=!0,rt(p,"afterLeave",function(){t._leaving=!1,t.$forceUpdate()}),Bi(e,i);if("in-out"===r){if(ct(o))return u;var d,v=function(){d()};rt(l,"afterEnter",v),rt(l,"enterCancelled",v),rt(p,"delayLeave",function(e){d=e})}}return i}}},Vi=m({tag:String,moveClass:String},Fi);function zi(e){e.elm._moveCb&&e.elm._moveCb(),e.elm._enterCb&&e.elm._enterCb()}function Ki(e){e.data.newPos=e.elm.getBoundingClientRect()}function Ji(e){var t=e.data.pos,n=e.data.newPos,r=t.left-n.left,i=t.top-n.top;if(r||i){e.data.moved=!0;var o=e.elm.style;o.transform=o.WebkitTransform="translate("+r+"px,"+i+"px)",o.transitionDuration="0s"}}delete Vi.mode;var qi={Transition:Ui,TransitionGroup:{props:Vi,render:function(e){for(var t=this.tag||this.$vnode.data.tag||"span",n=Object.create(null),r=this.prevChildren=this.children,i=this.$slots.default||[],o=this.children=[],a=Hi(this),s=0;s<i.length;s++){var c=i[s];c.tag&&null!=c.key&&0!==String(c.key).indexOf("__vlist")&&(o.push(c),((n[c.key]=c).data||(c.data={})).transition=a)}if(r){for(var l=[],u=[],f=0;f<r.length;f++){var p=r[f];p.data.transition=a,p.data.pos=p.elm.getBoundingClientRect(),n[p.key]?l.push(p):u.push(p)}this.kept=e(t,null,l),this.removed=u}return e(t,null,o)},beforeUpdate:function(){this.__patch__(this._vnode,this.kept,!1,!0),this._vnode=this.kept},updated:function(){var e=this.prevChildren,r=this.moveClass||(this.name||"v")+"-move";e.length&&this.hasMove(e[0].elm,r)&&(e.forEach(zi),e.forEach(Ki),e.forEach(Ji),this._reflow=document.body.offsetHeight,e.forEach(function(e){if(e.data.moved){var n=e.elm,t=n.style;hi(n,r),t.transform=t.WebkitTransform=t.transitionDuration="",n.addEventListener(ui,n._moveCb=function e(t){t&&!/transform$/.test(t.propertyName)||(n.removeEventListener(ui,e),n._moveCb=null,mi(n,r))})}}))},methods:{hasMove:function(e,t){if(!ai)return!1;if(this._hasMove)return this._hasMove;var n=e.cloneNode();e._transitionClasses&&e._transitionClasses.forEach(function(e){ri(n,e)}),ni(n,t),n.style.display="none",this.$el.appendChild(n);var r=_i(n);return this.$el.removeChild(n),this._hasMove=r.hasTransform}}}};hn.config.mustUseProp=Sn,hn.config.isReservedTag=Un,hn.config.isReservedAttr=An,hn.config.getTagNamespace=Vn,hn.config.isUnknownElement=function(e){if(!B)return!0;if(Un(e))return!1;if(e=e.toLowerCase(),null!=zn[e])return zn[e];var t=document.createElement(e);return-1<e.indexOf("-")?zn[e]=t.constructor===window.HTMLUnknownElement||t.constructor===window.HTMLElement:zn[e]=/HTMLUnknownElement/.test(t.toString())},m(hn.options.directives,Pi),m(hn.options.components,qi),hn.prototype.__patch__=B?Oi:$,hn.prototype.$mount=function(e,t){return e=e&&B?Jn(e):void 0,r=e,i=t,(n=this).$el=r,n.$options.render||(n.$options.render=fe),_t(n,"beforeMount"),new St(n,function(){n._update(n._render(),i)},$,null,!0),i=!1,null==n.$vnode&&(n._isMounted=!0,_t(n,"mounted")),n;var n,r,i},B&&setTimeout(function(){j.devtools&&Q&&Q.emit("init",hn)},0);var Wi=/\{\{((?:.|\n)+?)\}\}/g,Gi=/[-.*+?^${}()|[\]\/\\]/g,Zi=e(function(e){var t=e[0].replace(Gi,"\\$&"),n=e[1].replace(Gi,"\\$&");return new RegExp(t+"((?:.|\\n)+?)"+n,"g")});var Xi={staticKeys:["staticClass"],transformNode:function(e,t){t.warn;var n=Sr(e,"class");n&&(e.staticClass=JSON.stringify(n));var r=Or(e,"class",!1);r&&(e.classBinding=r)},genData:function(e){var t="";return e.staticClass&&(t+="staticClass:"+e.staticClass+","),e.classBinding&&(t+="class:"+e.classBinding+","),t}};var Yi,Qi={staticKeys:["staticStyle"],transformNode:function(e,t){t.warn;var n=Sr(e,"style");n&&(e.staticStyle=JSON.stringify(Kr(n)));var r=Or(e,"style",!1);r&&(e.styleBinding=r)},genData:function(e){var t="";return e.staticStyle&&(t+="staticStyle:"+e.staticStyle+","),e.styleBinding&&(t+="style:("+e.styleBinding+"),"),t}},eo=function(e){return(Yi=Yi||document.createElement("div")).innerHTML=e,Yi.textContent},to=s("area,base,br,col,embed,frame,hr,img,input,isindex,keygen,link,meta,param,source,track,wbr"),no=s("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source"),ro=s("address,article,aside,base,blockquote,body,caption,col,colgroup,dd,details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,title,tr,track"),io=/^\s*([^\s"'<>\/=]+)(?:\s*(=)\s*(?:"([^"]*)"+|'([^']*)'+|([^\s"'=<>`]+)))?/,oo="[a-zA-Z_][\\w\\-\\.]*",ao="((?:"+oo+"\\:)?"+oo+")",so=new RegExp("^<"+ao),co=/^\s*(\/?)>/,lo=new RegExp("^<\\/"+ao+"[^>]*>"),uo=/^<!DOCTYPE [^>]+>/i,fo=/^<!\--/,po=/^<!\[/,vo=!1;"x".replace(/x(.)?/g,function(e,t){vo=""===t});var ho=s("script,style,textarea",!0),mo={},yo={"&lt;":"<","&gt;":">","&quot;":'"',"&amp;":"&","&#10;":"\n","&#9;":"\t"},go=/&(?:lt|gt|quot|amp);/g,_o=/&(?:lt|gt|quot|amp|#10|#9);/g,bo=s("pre,textarea",!0),$o=function(e,t){return e&&bo(e)&&"\n"===t[0]};var wo,Co,xo,ko,Ao,Oo,So,To,Eo=/^@|^v-on:/,jo=/^v-|^@|^:/,No=/([^]*?)\s+(?:in|of)\s+([^]*)/,Lo=/,([^,\}\]]*)(?:,([^,\}\]]*))?$/,Io=/^\(|\)$/g,Mo=/:(.*)$/,Do=/^:|^v-bind:/,Po=/\.[^.]+/g,Fo=e(eo);function Ro(e,t,n){return{type:1,tag:e,attrsList:t,attrsMap:function(e){for(var t={},n=0,r=e.length;n<r;n++)t[e[n].name]=e[n].value;return t}(t),parent:n,children:[]}}function Ho(e,p){wo=p.warn||$r,Oo=p.isPreTag||O,So=p.mustUseProp||O,To=p.getTagNamespace||O,xo=wr(p.modules,"transformNode"),ko=wr(p.modules,"preTransformNode"),Ao=wr(p.modules,"postTransformNode"),Co=p.delimiters;var d,v,h=[],i=!1!==p.preserveWhitespace,m=!1,y=!1;function g(e){e.pre&&(m=!1),Oo(e.tag)&&(y=!1);for(var t=0;t<Ao.length;t++)Ao[t](e,p)}return function(i,d){for(var e,v,h=[],m=d.expectHTML,y=d.isUnaryTag||O,g=d.canBeLeftOpenTag||O,a=0;i;){if(e=i,v&&ho(v)){var r=0,o=v.toLowerCase(),t=mo[o]||(mo[o]=new RegExp("([\\s\\S]*?)(</"+o+"[^>]*>)","i")),n=i.replace(t,function(e,t,n){return r=n.length,ho(o)||"noscript"===o||(t=t.replace(/<!\--([\s\S]*?)-->/g,"$1").replace(/<!\[CDATA\[([\s\S]*?)]]>/g,"$1")),$o(o,t)&&(t=t.slice(1)),d.chars&&d.chars(t),""});a+=i.length-n.length,i=n,A(o,a-r,a)}else{var s=i.indexOf("<");if(0===s){if(fo.test(i)){var c=i.indexOf("--\x3e");if(0<=c){d.shouldKeepComment&&d.comment(i.substring(4,c)),C(c+3);continue}}if(po.test(i)){var l=i.indexOf("]>");if(0<=l){C(l+2);continue}}var u=i.match(uo);if(u){C(u[0].length);continue}var f=i.match(lo);if(f){var p=a;C(f[0].length),A(f[1],p,a);continue}var _=x();if(_){k(_),$o(v,i)&&C(1);continue}}var b=void 0,$=void 0,w=void 0;if(0<=s){for($=i.slice(s);!(lo.test($)||so.test($)||fo.test($)||po.test($)||(w=$.indexOf("<",1))<0);)s+=w,$=i.slice(s);b=i.substring(0,s),C(s)}s<0&&(b=i,i=""),d.chars&&b&&d.chars(b)}if(i===e){d.chars&&d.chars(i);break}}function C(e){a+=e,i=i.substring(e)}function x(){var e=i.match(so);if(e){var t,n,r={tagName:e[1],attrs:[],start:a};for(C(e[0].length);!(t=i.match(co))&&(n=i.match(io));)C(n[0].length),r.attrs.push(n);if(t)return r.unarySlash=t[1],C(t[0].length),r.end=a,r}}function k(e){var t=e.tagName,n=e.unarySlash;m&&("p"===v&&ro(t)&&A(v),g(t)&&v===t&&A(t));for(var r,i,o,a=y(t)||!!n,s=e.attrs.length,c=new Array(s),l=0;l<s;l++){var u=e.attrs[l];vo&&-1===u[0].indexOf('""')&&(""===u[3]&&delete u[3],""===u[4]&&delete u[4],""===u[5]&&delete u[5]);var f=u[3]||u[4]||u[5]||"",p="a"===t&&"href"===u[1]?d.shouldDecodeNewlinesForHref:d.shouldDecodeNewlines;c[l]={name:u[1],value:(r=f,i=p,o=i?_o:go,r.replace(o,function(e){return yo[e]}))}}a||(h.push({tag:t,lowerCasedTag:t.toLowerCase(),attrs:c}),v=t),d.start&&d.start(t,c,a,e.start,e.end)}function A(e,t,n){var r,i;if(null==t&&(t=a),null==n&&(n=a),e&&(i=e.toLowerCase()),e)for(r=h.length-1;0<=r&&h[r].lowerCasedTag!==i;r--);else r=0;if(0<=r){for(var o=h.length-1;r<=o;o--)d.end&&d.end(h[o].tag,t,n);h.length=r,v=r&&h[r-1].tag}else"br"===i?d.start&&d.start(e,[],!0,t,n):"p"===i&&(d.start&&d.start(e,[],!1,t,n),d.end&&d.end(e,t,n))}A()}(e,{warn:wo,expectHTML:p.expectHTML,isUnaryTag:p.isUnaryTag,canBeLeftOpenTag:p.canBeLeftOpenTag,shouldDecodeNewlines:p.shouldDecodeNewlines,shouldDecodeNewlinesForHref:p.shouldDecodeNewlinesForHref,shouldKeepComment:p.comments,start:function(e,t,n){var r=v&&v.ns||To(e);K&&"svg"===r&&(t=function(e){for(var t=[],n=0;n<e.length;n++){var r=e[n];Ko.test(r.name)||(r.name=r.name.replace(Jo,""),t.push(r))}return t}(t));var i,o,a,s,c,l=Ro(e,t,v);r&&(l.ns=r),"style"!==(i=l).tag&&("script"!==i.tag||i.attrsMap.type&&"text/javascript"!==i.attrsMap.type)||Y()||(l.forbidden=!0);for(var u=0;u<ko.length;u++)l=ko[u](l,p)||l;if(m||(null!=Sr(o=l,"v-pre")&&(o.pre=!0),l.pre&&(m=!0)),Oo(l.tag)&&(y=!0),m?function(e){var t=e.attrsList.length;if(t)for(var n=e.attrs=new Array(t),r=0;r<t;r++)n[r]={name:e.attrsList[r].name,value:JSON.stringify(e.attrsList[r].value)};else e.pre||(e.plain=!0)}(l):l.processed||(Uo(l),function(e){var t=Sr(e,"v-if");if(t)e.if=t,Vo(e,{exp:t,block:e});else{null!=Sr(e,"v-else")&&(e.else=!0);var n=Sr(e,"v-else-if");n&&(e.elseif=n)}}(l),null!=Sr(a=l,"v-once")&&(a.once=!0),Bo(l,p)),d?h.length||d.if&&(l.elseif||l.else)&&Vo(d,{exp:l.elseif,block:l}):d=l,v&&!l.forbidden)if(l.elseif||l.else)s=l,(c=function(e){var t=e.length;for(;t--;){if(1===e[t].type)return e[t];e.pop()}}(v.children))&&c.if&&Vo(c,{exp:s.elseif,block:s});else if(l.slotScope){v.plain=!1;var f=l.slotTarget||'"default"';(v.scopedSlots||(v.scopedSlots={}))[f]=l}else v.children.push(l),l.parent=v;n?g(l):(v=l,h.push(l))},end:function(){var e=h[h.length-1],t=e.children[e.children.length-1];t&&3===t.type&&" "===t.text&&!y&&e.children.pop(),h.length-=1,v=h[h.length-1],g(e)},chars:function(e){if(v&&(!K||"textarea"!==v.tag||v.attrsMap.placeholder!==e)){var t,n,r=v.children;if(e=y||e.trim()?"script"===(t=v).tag||"style"===t.tag?e:Fo(e):i&&r.length?" ":"")!m&&" "!==e&&(n=function(e,t){var n=t?Zi(t):Wi;if(n.test(e)){for(var r,i,o,a=[],s=[],c=n.lastIndex=0;r=n.exec(e);){c<(i=r.index)&&(s.push(o=e.slice(c,i)),a.push(JSON.stringify(o)));var l=_r(r[1].trim());a.push("_s("+l+")"),s.push({"@binding":l}),c=i+r[0].length}return c<e.length&&(s.push(o=e.slice(c)),a.push(JSON.stringify(o))),{expression:a.join("+"),tokens:s}}}(e,Co))?r.push({type:2,expression:n.expression,tokens:n.tokens,text:e}):" "===e&&r.length&&" "===r[r.length-1].text||r.push({type:3,text:e})}},comment:function(e){v.children.push({type:3,text:e,isComment:!0})}}),d}function Bo(e,t){var n,r,i,o;(r=Or(n=e,"key"))&&(n.key=r),e.plain=!e.key&&!e.attrsList.length,(o=Or(i=e,"ref"))&&(i.ref=o,i.refInFor=function(e){for(var t=e;t;){if(void 0!==t.for)return!0;t=t.parent}return!1}(i)),function(e){if("slot"===e.tag)e.slotName=Or(e,"name");else{var t;"template"===e.tag?(t=Sr(e,"scope"),e.slotScope=t||Sr(e,"slot-scope")):(t=Sr(e,"slot-scope"))&&(e.slotScope=t);var n=Or(e,"slot");n&&(e.slotTarget='""'===n?'"default"':n,"template"===e.tag||e.slotScope||xr(e,"slot",n))}}(e),function(e){var t;(t=Or(e,"is"))&&(e.component=t);null!=Sr(e,"inline-template")&&(e.inlineTemplate=!0)}(e);for(var a=0;a<xo.length;a++)e=xo[a](e,t)||e;!function(e){var t,n,r,i,o,a,s,c=e.attrsList;for(t=0,n=c.length;t<n;t++)if(r=i=c[t].name,o=c[t].value,jo.test(r))if(e.hasBindings=!0,(a=zo(r))&&(r=r.replace(Po,"")),Do.test(r))r=r.replace(Do,""),o=_r(o),s=!1,a&&(a.prop&&(s=!0,"innerHtml"===(r=g(r))&&(r="innerHTML")),a.camel&&(r=g(r)),a.sync&&Ar(e,"update:"+g(r),Er(o,"$event"))),s||!e.component&&So(e.tag,e.attrsMap.type,r)?Cr(e,r,o):xr(e,r,o);else if(Eo.test(r))r=r.replace(Eo,""),Ar(e,r,o,a,!1);else{var l=(r=r.replace(jo,"")).match(Mo),u=l&&l[1];u&&(r=r.slice(0,-(u.length+1))),p=r,d=i,v=o,h=u,m=a,((f=e).directives||(f.directives=[])).push({name:p,rawName:d,value:v,arg:h,modifiers:m}),f.plain=!1}else xr(e,r,JSON.stringify(o)),!e.component&&"muted"===r&&So(e.tag,e.attrsMap.type,r)&&Cr(e,r,"true");var f,p,d,v,h,m}(e)}function Uo(e){var t;if(t=Sr(e,"v-for")){var n=function(e){var t=e.match(No);if(!t)return;var n={};n.for=t[2].trim();var r=t[1].trim().replace(Io,""),i=r.match(Lo);i?(n.alias=r.replace(Lo,""),n.iterator1=i[1].trim(),i[2]&&(n.iterator2=i[2].trim())):n.alias=r;return n}(t);n&&m(e,n)}}function Vo(e,t){e.ifConditions||(e.ifConditions=[]),e.ifConditions.push(t)}function zo(e){var t=e.match(Po);if(t){var n={};return t.forEach(function(e){n[e.slice(1)]=!0}),n}}var Ko=/^xmlns:NS\d+/,Jo=/^NS\d+:/;function qo(e){return Ro(e.tag,e.attrsList.slice(),e.parent)}var Wo=[Xi,Qi,{preTransformNode:function(e,t){if("input"===e.tag){var n,r=e.attrsMap;if(!r["v-model"])return;if((r[":type"]||r["v-bind:type"])&&(n=Or(e,"type")),r.type||n||!r["v-bind"]||(n="("+r["v-bind"]+").type"),n){var i=Sr(e,"v-if",!0),o=i?"&&("+i+")":"",a=null!=Sr(e,"v-else",!0),s=Sr(e,"v-else-if",!0),c=qo(e);Uo(c),kr(c,"type","checkbox"),Bo(c,t),c.processed=!0,c.if="("+n+")==='checkbox'"+o,Vo(c,{exp:c.if,block:c});var l=qo(e);Sr(l,"v-for",!0),kr(l,"type","radio"),Bo(l,t),Vo(c,{exp:"("+n+")==='radio'"+o,block:l});var u=qo(e);return Sr(u,"v-for",!0),kr(u,":type",n),Bo(u,t),Vo(c,{exp:i,block:u}),a?c.else=!0:s&&(c.elseif=s),c}}}}];var Go,Zo,Xo,Yo={expectHTML:!0,modules:Wo,directives:{model:function(e,t,n){var r,i,o,a,s,c,l,u,f,p,d,v,h,m,y,g,_=t.value,b=t.modifiers,$=e.tag,w=e.attrsMap.type;if(e.component)return Tr(e,_,b),!1;if("select"===$)h=e,m=_,g=(g='var $$selectedVal = Array.prototype.filter.call($event.target.options,function(o){return o.selected}).map(function(o){var val = "_value" in o ? o._value : o.value;return '+((y=b)&&y.number?"_n(val)":"val")+"});")+" "+Er(m,"$event.target.multiple ? $$selectedVal : $$selectedVal[0]"),Ar(h,"change",g,null,!0);else if("input"===$&&"checkbox"===w)c=e,l=_,f=(u=b)&&u.number,p=Or(c,"value")||"null",d=Or(c,"true-value")||"true",v=Or(c,"false-value")||"false",Cr(c,"checked","Array.isArray("+l+")?_i("+l+","+p+")>-1"+("true"===d?":("+l+")":":_q("+l+","+d+")")),Ar(c,"change","var $$a="+l+",$$el=$event.target,$$c=$$el.checked?("+d+"):("+v+");if(Array.isArray($$a)){var $$v="+(f?"_n("+p+")":p)+",$$i=_i($$a,$$v);if($$el.checked){$$i<0&&("+Er(l,"$$a.concat([$$v])")+")}else{$$i>-1&&("+Er(l,"$$a.slice(0,$$i).concat($$a.slice($$i+1))")+")}}else{"+Er(l,"$$c")+"}",null,!0);else if("input"===$&&"radio"===w)r=e,i=_,a=(o=b)&&o.number,s=Or(r,"value")||"null",Cr(r,"checked","_q("+i+","+(s=a?"_n("+s+")":s)+")"),Ar(r,"change",Er(i,s),null,!0);else if("input"===$||"textarea"===$)!function(e,t,n){var r=e.attrsMap.type,i=n||{},o=i.lazy,a=i.number,s=i.trim,c=!o&&"range"!==r,l=o?"change":"range"===r?Pr:"input",u="$event.target.value";s&&(u="$event.target.value.trim()"),a&&(u="_n("+u+")");var f=Er(t,u);c&&(f="if($event.target.composing)return;"+f),Cr(e,"value","("+t+")"),Ar(e,l,f,null,!0),(s||a)&&Ar(e,"blur","$forceUpdate()")}(e,_,b);else if(!j.isReservedTag($))return Tr(e,_,b),!1;return!0},text:function(e,t){t.value&&Cr(e,"textContent","_s("+t.value+")")},html:function(e,t){t.value&&Cr(e,"innerHTML","_s("+t.value+")")}},isPreTag:function(e){return"pre"===e},isUnaryTag:to,mustUseProp:Sn,canBeLeftOpenTag:no,isReservedTag:Un,getTagNamespace:Vn,staticKeys:(Go=Wo,Go.reduce(function(e,t){return e.concat(t.staticKeys||[])},[]).join(","))},Qo=e(function(e){return s("type,tag,attrsList,attrsMap,plain,parent,children,attrs"+(e?","+e:""))});function ea(e,t){e&&(Zo=Qo(t.staticKeys||""),Xo=t.isReservedTag||O,function e(t){t.static=function(e){if(2===e.type)return!1;if(3===e.type)return!0;return!(!e.pre&&(e.hasBindings||e.if||e.for||c(e.tag)||!Xo(e.tag)||function(e){for(;e.parent;){if("template"!==(e=e.parent).tag)return!1;if(e.for)return!0}return!1}(e)||!Object.keys(e).every(Zo)))}(t);if(1===t.type){if(!Xo(t.tag)&&"slot"!==t.tag&&null==t.attrsMap["inline-template"])return;for(var n=0,r=t.children.length;n<r;n++){var i=t.children[n];e(i),i.static||(t.static=!1)}if(t.ifConditions)for(var o=1,a=t.ifConditions.length;o<a;o++){var s=t.ifConditions[o].block;e(s),s.static||(t.static=!1)}}}(e),function e(t,n){if(1===t.type){if((t.static||t.once)&&(t.staticInFor=n),t.static&&t.children.length&&(1!==t.children.length||3!==t.children[0].type))return void(t.staticRoot=!0);if(t.staticRoot=!1,t.children)for(var r=0,i=t.children.length;r<i;r++)e(t.children[r],n||!!t.for);if(t.ifConditions)for(var o=1,a=t.ifConditions.length;o<a;o++)e(t.ifConditions[o].block,n)}}(e,!1))}var ta=/^([\w$_]+|\([^)]*?\))\s*=>|^function\s*\(/,na=/^[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['[^']*?']|\["[^"]*?"]|\[\d+]|\[[A-Za-z_$][\w$]*])*$/,ra={esc:27,tab:9,enter:13,space:32,up:38,left:37,right:39,down:40,delete:[8,46]},ia={esc:"Escape",tab:"Tab",enter:"Enter",space:" ",up:["Up","ArrowUp"],left:["Left","ArrowLeft"],right:["Right","ArrowRight"],down:["Down","ArrowDown"],delete:["Backspace","Delete"]},oa=function(e){return"if("+e+")return null;"},aa={stop:"$event.stopPropagation();",prevent:"$event.preventDefault();",self:oa("$event.target !== $event.currentTarget"),ctrl:oa("!$event.ctrlKey"),shift:oa("!$event.shiftKey"),alt:oa("!$event.altKey"),meta:oa("!$event.metaKey"),left:oa("'button' in $event && $event.button !== 0"),middle:oa("'button' in $event && $event.button !== 1"),right:oa("'button' in $event && $event.button !== 2")};function sa(e,t,n){var r=t?"nativeOn:{":"on:{";for(var i in e)r+='"'+i+'":'+ca(i,e[i])+",";return r.slice(0,-1)+"}"}function ca(t,e){if(!e)return"function(){}";if(Array.isArray(e))return"["+e.map(function(e){return ca(t,e)}).join(",")+"]";var n=na.test(e.value),r=ta.test(e.value);if(e.modifiers){var i="",o="",a=[];for(var s in e.modifiers)if(aa[s])o+=aa[s],ra[s]&&a.push(s);else if("exact"===s){var c=e.modifiers;o+=oa(["ctrl","shift","alt","meta"].filter(function(e){return!c[e]}).map(function(e){return"$event."+e+"Key"}).join("||"))}else a.push(s);return a.length&&(i+="if(!('button' in $event)&&"+a.map(la).join("&&")+")return null;"),o&&(i+=o),"function($event){"+i+(n?"return "+e.value+"($event)":r?"return ("+e.value+")($event)":e.value)+"}"}return n||r?e.value:"function($event){"+e.value+"}"}function la(e){var t=parseInt(e,10);if(t)return"$event.keyCode!=="+t;var n=ra[e],r=ia[e];return"_k($event.keyCode,"+JSON.stringify(e)+","+JSON.stringify(n)+",$event.key,"+JSON.stringify(r)+")"}var ua={on:function(e,t){e.wrapListeners=function(e){return"_g("+e+","+t.value+")"}},bind:function(t,n){t.wrapData=function(e){return"_b("+e+",'"+t.tag+"',"+n.value+","+(n.modifiers&&n.modifiers.prop?"true":"false")+(n.modifiers&&n.modifiers.sync?",true":"")+")"}},cloak:$},fa=function(e){this.options=e,this.warn=e.warn||$r,this.transforms=wr(e.modules,"transformCode"),this.dataGenFns=wr(e.modules,"genData"),this.directives=m(m({},ua),e.directives);var t=e.isReservedTag||O;this.maybeComponent=function(e){return!t(e.tag)},this.onceId=0,this.staticRenderFns=[]};function pa(e,t){var n=new fa(t);return{render:"with(this){return "+(e?da(e,n):'_c("div")')+"}",staticRenderFns:n.staticRenderFns}}function da(e,t){if(e.staticRoot&&!e.staticProcessed)return va(e,t);if(e.once&&!e.onceProcessed)return ha(e,t);if(e.for&&!e.forProcessed)return f=t,v=(u=e).for,h=u.alias,m=u.iterator1?","+u.iterator1:"",y=u.iterator2?","+u.iterator2:"",u.forProcessed=!0,(d||"_l")+"(("+v+"),function("+h+m+y+"){return "+(p||da)(u,f)+"})";if(e.if&&!e.ifProcessed)return ma(e,t);if("template"!==e.tag||e.slotTarget){if("slot"===e.tag)return function(e,t){var n=e.slotName||'"default"',r=_a(e,t),i="_t("+n+(r?","+r:""),o=e.attrs&&"{"+e.attrs.map(function(e){return g(e.name)+":"+e.value}).join(",")+"}",a=e.attrsMap["v-bind"];!o&&!a||r||(i+=",null");o&&(i+=","+o);a&&(i+=(o?"":",null")+","+a);return i+")"}(e,t);var n;if(e.component)a=e.component,c=t,l=(s=e).inlineTemplate?null:_a(s,c,!0),n="_c("+a+","+ya(s,c)+(l?","+l:"")+")";else{var r=e.plain?void 0:ya(e,t),i=e.inlineTemplate?null:_a(e,t,!0);n="_c('"+e.tag+"'"+(r?","+r:"")+(i?","+i:"")+")"}for(var o=0;o<t.transforms.length;o++)n=t.transforms[o](e,n);return n}return _a(e,t)||"void 0";var a,s,c,l,u,f,p,d,v,h,m,y}function va(e,t){return e.staticProcessed=!0,t.staticRenderFns.push("with(this){return "+da(e,t)+"}"),"_m("+(t.staticRenderFns.length-1)+(e.staticInFor?",true":"")+")"}function ha(e,t){if(e.onceProcessed=!0,e.if&&!e.ifProcessed)return ma(e,t);if(e.staticInFor){for(var n="",r=e.parent;r;){if(r.for){n=r.key;break}r=r.parent}return n?"_o("+da(e,t)+","+t.onceId+++","+n+")":da(e,t)}return va(e,t)}function ma(e,t,n,r){return e.ifProcessed=!0,function e(t,n,r,i){if(!t.length)return i||"_e()";var o=t.shift();return o.exp?"("+o.exp+")?"+a(o.block)+":"+e(t,n,r,i):""+a(o.block);function a(e){return r?r(e,n):e.once?ha(e,n):da(e,n)}}(e.ifConditions.slice(),t,n,r)}function ya(e,t){var n,r,i="{",o=function(e,t){var n=e.directives;if(!n)return;var r,i,o,a,s="directives:[",c=!1;for(r=0,i=n.length;r<i;r++){o=n[r],a=!0;var l=t.directives[o.name];l&&(a=!!l(e,o,t.warn)),a&&(c=!0,s+='{name:"'+o.name+'",rawName:"'+o.rawName+'"'+(o.value?",value:("+o.value+"),expression:"+JSON.stringify(o.value):"")+(o.arg?',arg:"'+o.arg+'"':"")+(o.modifiers?",modifiers:"+JSON.stringify(o.modifiers):"")+"},")}if(c)return s.slice(0,-1)+"]"}(e,t);o&&(i+=o+","),e.key&&(i+="key:"+e.key+","),e.ref&&(i+="ref:"+e.ref+","),e.refInFor&&(i+="refInFor:true,"),e.pre&&(i+="pre:true,"),e.component&&(i+='tag:"'+e.tag+'",');for(var a=0;a<t.dataGenFns.length;a++)i+=t.dataGenFns[a](e);if(e.attrs&&(i+="attrs:{"+wa(e.attrs)+"},"),e.props&&(i+="domProps:{"+wa(e.props)+"},"),e.events&&(i+=sa(e.events,!1,t.warn)+","),e.nativeEvents&&(i+=sa(e.nativeEvents,!0,t.warn)+","),e.slotTarget&&!e.slotScope&&(i+="slot:"+e.slotTarget+","),e.scopedSlots&&(i+=(n=e.scopedSlots,r=t,"scopedSlots:_u(["+Object.keys(n).map(function(e){return ga(e,n[e],r)}).join(",")+"]),")),e.model&&(i+="model:{value:"+e.model.value+",callback:"+e.model.callback+",expression:"+e.model.expression+"},"),e.inlineTemplate){var s=function(e,t){var n=e.children[0];if(1===n.type){var r=pa(n,t.options);return"inlineTemplate:{render:function(){"+r.render+"},staticRenderFns:["+r.staticRenderFns.map(function(e){return"function(){"+e+"}"}).join(",")+"]}"}}(e,t);s&&(i+=s+",")}return i=i.replace(/,$/,"")+"}",e.wrapData&&(i=e.wrapData(i)),e.wrapListeners&&(i=e.wrapListeners(i)),i}function ga(e,t,n){return t.for&&!t.forProcessed?(r=e,o=n,a=(i=t).for,s=i.alias,c=i.iterator1?","+i.iterator1:"",l=i.iterator2?","+i.iterator2:"",i.forProcessed=!0,"_l(("+a+"),function("+s+c+l+"){return "+ga(r,i,o)+"})"):"{key:"+e+",fn:"+("function("+String(t.slotScope)+"){return "+("template"===t.tag?t.if?t.if+"?"+(_a(t,n)||"undefined")+":undefined":_a(t,n)||"undefined":da(t,n))+"}")+"}";var r,i,o,a,s,c,l}function _a(e,t,n,r,i){var o=e.children;if(o.length){var a=o[0];if(1===o.length&&a.for&&"template"!==a.tag&&"slot"!==a.tag)return(r||da)(a,t);var s=n?function(e,t){for(var n=0,r=0;r<e.length;r++){var i=e[r];if(1===i.type){if(ba(i)||i.ifConditions&&i.ifConditions.some(function(e){return ba(e.block)})){n=2;break}(t(i)||i.ifConditions&&i.ifConditions.some(function(e){return t(e.block)}))&&(n=1)}}return n}(o,t.maybeComponent):0,c=i||$a;return"["+o.map(function(e){return c(e,t)}).join(",")+"]"+(s?","+s:"")}}function ba(e){return void 0!==e.for||"template"===e.tag||"slot"===e.tag}function $a(e,t){return 1===e.type?da(e,t):3===e.type&&e.isComment?(r=e,"_e("+JSON.stringify(r.text)+")"):"_v("+(2===(n=e).type?n.expression:Ca(JSON.stringify(n.text)))+")";var n,r}function wa(e){for(var t="",n=0;n<e.length;n++){var r=e[n];t+='"'+r.name+'":'+Ca(r.value)+","}return t.slice(0,-1)}function Ca(e){return e.replace(/\u2028/g,"\\u2028").replace(/\u2029/g,"\\u2029")}new RegExp("\\b"+"do,if,for,let,new,try,var,case,else,with,await,break,catch,class,const,super,throw,while,yield,delete,export,import,return,switch,default,extends,finally,continue,debugger,function,arguments".split(",").join("\\b|\\b")+"\\b"),new RegExp("\\b"+"delete,typeof,void".split(",").join("\\s*\\([^\\)]*\\)|\\b")+"\\s*\\([^\\)]*\\)");function xa(t,n){try{return new Function(t)}catch(e){return n.push({err:e,code:t}),$}}var ka,Aa,Oa=(ka=function(e,t){var n=Ho(e.trim(),t);!1!==t.optimize&&ea(n,t);var r=pa(n,t);return{ast:n,render:r.render,staticRenderFns:r.staticRenderFns}},function(s){function e(e,t){var n=Object.create(s),r=[],i=[];if(n.warn=function(e,t){(t?i:r).push(e)},t)for(var o in t.modules&&(n.modules=(s.modules||[]).concat(t.modules)),t.directives&&(n.directives=m(Object.create(s.directives||null),t.directives)),t)"modules"!==o&&"directives"!==o&&(n[o]=t[o]);var a=ka(e,n);return a.errors=r,a.tips=i,a}return{compile:e,compileToFunctions:(c=e,l=Object.create(null),function(e,t,n){(t=m({},t)).warn,delete t.warn;var r=t.delimiters?String(t.delimiters)+e:e;if(l[r])return l[r];var i=c(e,t),o={},a=[];return o.render=xa(i.render,a),o.staticRenderFns=i.staticRenderFns.map(function(e){return xa(e,a)}),l[r]=o})};var c,l})(Yo).compileToFunctions;function Sa(e){return(Aa=Aa||document.createElement("div")).innerHTML=e?'<a href="\n"/>':'<div a="\n"/>',0<Aa.innerHTML.indexOf("&#10;")}var Ta=!!B&&Sa(!1),Ea=!!B&&Sa(!0),ja=e(function(e){var t=Jn(e);return t&&t.innerHTML}),Na=hn.prototype.$mount;return hn.prototype.$mount=function(e,t){if((e=e&&Jn(e))===document.body||e===document.documentElement)return this;var n=this.$options;if(!n.render){var r=n.template;if(r)if("string"==typeof r)"#"===r.charAt(0)&&(r=ja(r));else{if(!r.nodeType)return this;r=r.innerHTML}else e&&(r=function(e){{if(e.outerHTML)return e.outerHTML;var t=document.createElement("div");return t.appendChild(e.cloneNode(!0)),t.innerHTML}}(e));if(r){var i=Oa(r,{shouldDecodeNewlines:Ta,shouldDecodeNewlinesForHref:Ea,delimiters:n.delimiters,comments:n.comments},this),o=i.render,a=i.staticRenderFns;n.render=o,n.staticRenderFns=a}}return Na.call(this,e,t)},hn.compile=Oa,hn});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js"), __webpack_require__(/*! ./../../timers-browserify/main.js */ "./node_modules/timers-browserify/main.js").setImmediate))

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1, eval)("this");
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "./node_modules/xregexp/lib/addons/build.js":
/*!**************************************************!*\
  !*** ./node_modules/xregexp/lib/addons/build.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

/*!
 * XRegExp.build 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2012-2017 MIT License
 */

exports.default = function (XRegExp) {
    var REGEX_DATA = 'xregexp';
    var subParts = /(\()(?!\?)|\\([1-9]\d*)|\\[\s\S]|\[(?:[^\\\]]|\\[\s\S])*\]/g;
    var parts = XRegExp.union([/\({{([\w$]+)}}\)|{{([\w$]+)}}/, subParts], 'g', {
        conjunction: 'or'
    });

    /**
     * Strips a leading `^` and trailing unescaped `$`, if both are present.
     *
     * @private
     * @param {String} pattern Pattern to process.
     * @returns {String} Pattern with edge anchors removed.
     */
    function deanchor(pattern) {
        // Allow any number of empty noncapturing groups before/after anchors, because regexes
        // built/generated by XRegExp sometimes include them
        var leadingAnchor = /^(?:\(\?:\))*\^/;
        var trailingAnchor = /\$(?:\(\?:\))*$/;

        if (leadingAnchor.test(pattern) && trailingAnchor.test(pattern) &&
        // Ensure that the trailing `$` isn't escaped
        trailingAnchor.test(pattern.replace(/\\[\s\S]/g, ''))) {
            return pattern.replace(leadingAnchor, '').replace(trailingAnchor, '');
        }

        return pattern;
    }

    /**
     * Converts the provided value to an XRegExp. Native RegExp flags are not preserved.
     *
     * @private
     * @param {String|RegExp} value Value to convert.
     * @param {Boolean} [addFlagX] Whether to apply the `x` flag in cases when `value` is not
     *   already a regex generated by XRegExp
     * @returns {RegExp} XRegExp object with XRegExp syntax applied.
     */
    function asXRegExp(value, addFlagX) {
        var flags = addFlagX ? 'x' : '';
        return XRegExp.isRegExp(value) ? value[REGEX_DATA] && value[REGEX_DATA].captureNames ?
        // Don't recompile, to preserve capture names
        value :
        // Recompile as XRegExp
        XRegExp(value.source, flags) :
        // Compile string as XRegExp
        XRegExp(value, flags);
    }

    function interpolate(substitution) {
        return substitution instanceof RegExp ? substitution : XRegExp.escape(substitution);
    }

    function reduceToSubpatternsObject(subpatterns, interpolated, subpatternIndex) {
        subpatterns['subpattern' + subpatternIndex] = interpolated;
        return subpatterns;
    }

    function embedSubpatternAfter(raw, subpatternIndex, rawLiterals) {
        var hasSubpattern = subpatternIndex < rawLiterals.length - 1;
        return raw + (hasSubpattern ? '{{subpattern' + subpatternIndex + '}}' : '');
    }

    /**
     * Provides tagged template literals that create regexes with XRegExp syntax and flags. The
     * provided pattern is handled as a raw string, so backslashes don't need to be escaped.
     *
     * Interpolation of strings and regexes shares the features of `XRegExp.build`. Interpolated
     * patterns are treated as atomic units when quantified, interpolated strings have their special
     * characters escaped, a leading `^` and trailing unescaped `$` are stripped from interpolated
     * regexes if both are present, and any backreferences within an interpolated regex are
     * rewritten to work within the overall pattern.
     *
     * @memberOf XRegExp
     * @param {String} [flags] Any combination of XRegExp flags.
     * @returns {Function} Handler for template literals that construct regexes with XRegExp syntax.
     * @example
     *
     * const h12 = /1[0-2]|0?[1-9]/;
     * const h24 = /2[0-3]|[01][0-9]/;
     * const hours = XRegExp.tag('x')`${h12} : | ${h24}`;
     * const minutes = /^[0-5][0-9]$/;
     * // Note that explicitly naming the 'minutes' group is required for named backreferences
     * const time = XRegExp.tag('x')`^ ${hours} (?<minutes>${minutes}) $`;
     * time.test('10:59'); // -> true
     * XRegExp.exec('10:59', time).minutes; // -> '59'
     */
    XRegExp.tag = function (flags) {
        return function (literals) {
            for (var _len = arguments.length, substitutions = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
                substitutions[_key - 1] = arguments[_key];
            }

            var subpatterns = substitutions.map(interpolate).reduce(reduceToSubpatternsObject, {});
            var pattern = literals.raw.map(embedSubpatternAfter).join('');
            return XRegExp.build(pattern, subpatterns, flags);
        };
    };

    /**
     * Builds regexes using named subpatterns, for readability and pattern reuse. Backreferences in
     * the outer pattern and provided subpatterns are automatically renumbered to work correctly.
     * Native flags used by provided subpatterns are ignored in favor of the `flags` argument.
     *
     * @memberOf XRegExp
     * @param {String} pattern XRegExp pattern using `{{name}}` for embedded subpatterns. Allows
     *   `({{name}})` as shorthand for `(?<name>{{name}})`. Patterns cannot be embedded within
     *   character classes.
     * @param {Object} subs Lookup object for named subpatterns. Values can be strings or regexes. A
     *   leading `^` and trailing unescaped `$` are stripped from subpatterns, if both are present.
     * @param {String} [flags] Any combination of XRegExp flags.
     * @returns {RegExp} Regex with interpolated subpatterns.
     * @example
     *
     * const time = XRegExp.build('(?x)^ {{hours}} ({{minutes}}) $', {
     *   hours: XRegExp.build('{{h12}} : | {{h24}}', {
     *     h12: /1[0-2]|0?[1-9]/,
     *     h24: /2[0-3]|[01][0-9]/
     *   }, 'x'),
     *   minutes: /^[0-5][0-9]$/
     * });
     * time.test('10:59'); // -> true
     * XRegExp.exec('10:59', time).minutes; // -> '59'
     */
    XRegExp.build = function (pattern, subs, flags) {
        flags = flags || '';
        // Used with `asXRegExp` calls for `pattern` and subpatterns in `subs`, to work around how
        // some browsers convert `RegExp('\n')` to a regex that contains the literal characters `\`
        // and `n`. See more details at <https://github.com/slevithan/xregexp/pull/163>.
        var addFlagX = flags.indexOf('x') !== -1;
        var inlineFlags = /^\(\?([\w$]+)\)/.exec(pattern);
        // Add flags within a leading mode modifier to the overall pattern's flags
        if (inlineFlags) {
            flags = XRegExp._clipDuplicates(flags + inlineFlags[1]);
        }

        var data = {};
        for (var p in subs) {
            if (subs.hasOwnProperty(p)) {
                // Passing to XRegExp enables extended syntax and ensures independent validity,
                // lest an unescaped `(`, `)`, `[`, or trailing `\` breaks the `(?:)` wrapper. For
                // subpatterns provided as native regexes, it dies on octals and adds the property
                // used to hold extended regex instance data, for simplicity.
                var sub = asXRegExp(subs[p], addFlagX);
                data[p] = {
                    // Deanchoring allows embedding independently useful anchored regexes. If you
                    // really need to keep your anchors, double them (i.e., `^^...$$`).
                    pattern: deanchor(sub.source),
                    names: sub[REGEX_DATA].captureNames || []
                };
            }
        }

        // Passing to XRegExp dies on octals and ensures the outer pattern is independently valid;
        // helps keep this simple. Named captures will be put back.
        var patternAsRegex = asXRegExp(pattern, addFlagX);

        // 'Caps' is short for 'captures'
        var numCaps = 0;
        var numPriorCaps = void 0;
        var numOuterCaps = 0;
        var outerCapsMap = [0];
        var outerCapNames = patternAsRegex[REGEX_DATA].captureNames || [];
        var output = patternAsRegex.source.replace(parts, function ($0, $1, $2, $3, $4) {
            var subName = $1 || $2;
            var capName = void 0;
            var intro = void 0;
            var localCapIndex = void 0;
            // Named subpattern
            if (subName) {
                if (!data.hasOwnProperty(subName)) {
                    throw new ReferenceError('Undefined property ' + $0);
                }
                // Named subpattern was wrapped in a capturing group
                if ($1) {
                    capName = outerCapNames[numOuterCaps];
                    outerCapsMap[++numOuterCaps] = ++numCaps;
                    // If it's a named group, preserve the name. Otherwise, use the subpattern name
                    // as the capture name
                    intro = '(?<' + (capName || subName) + '>';
                } else {
                    intro = '(?:';
                }
                numPriorCaps = numCaps;
                var rewrittenSubpattern = data[subName].pattern.replace(subParts, function (match, paren, backref) {
                    // Capturing group
                    if (paren) {
                        capName = data[subName].names[numCaps - numPriorCaps];
                        ++numCaps;
                        // If the current capture has a name, preserve the name
                        if (capName) {
                            return '(?<' + capName + '>';
                        }
                        // Backreference
                    } else if (backref) {
                        localCapIndex = +backref - 1;
                        // Rewrite the backreference
                        return data[subName].names[localCapIndex] ?
                        // Need to preserve the backreference name in case using flag `n`
                        '\\k<' + data[subName].names[localCapIndex] + '>' : '\\' + (+backref + numPriorCaps);
                    }
                    return match;
                });
                return '' + intro + rewrittenSubpattern + ')';
            }
            // Capturing group
            if ($3) {
                capName = outerCapNames[numOuterCaps];
                outerCapsMap[++numOuterCaps] = ++numCaps;
                // If the current capture has a name, preserve the name
                if (capName) {
                    return '(?<' + capName + '>';
                }
                // Backreference
            } else if ($4) {
                localCapIndex = +$4 - 1;
                // Rewrite the backreference
                return outerCapNames[localCapIndex] ?
                // Need to preserve the backreference name in case using flag `n`
                '\\k<' + outerCapNames[localCapIndex] + '>' : '\\' + outerCapsMap[+$4];
            }
            return $0;
        });

        return XRegExp(output, flags);
    };
};

module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/addons/matchrecursive.js":
/*!***********************************************************!*\
  !*** ./node_modules/xregexp/lib/addons/matchrecursive.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

/*!
 * XRegExp.matchRecursive 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2009-2017 MIT License
 */

exports.default = function (XRegExp) {

    /**
     * Returns a match detail object composed of the provided values.
     *
     * @private
     */
    function row(name, value, start, end) {
        return {
            name: name,
            value: value,
            start: start,
            end: end
        };
    }

    /**
     * Returns an array of match strings between outermost left and right delimiters, or an array of
     * objects with detailed match parts and position data. An error is thrown if delimiters are
     * unbalanced within the data.
     *
     * @memberOf XRegExp
     * @param {String} str String to search.
     * @param {String} left Left delimiter as an XRegExp pattern.
     * @param {String} right Right delimiter as an XRegExp pattern.
     * @param {String} [flags] Any native or XRegExp flags, used for the left and right delimiters.
     * @param {Object} [options] Lets you specify `valueNames` and `escapeChar` options.
     * @returns {Array} Array of matches, or an empty array.
     * @example
     *
     * // Basic usage
     * let str = '(t((e))s)t()(ing)';
     * XRegExp.matchRecursive(str, '\\(', '\\)', 'g');
     * // -> ['t((e))s', '', 'ing']
     *
     * // Extended information mode with valueNames
     * str = 'Here is <div> <div>an</div></div> example';
     * XRegExp.matchRecursive(str, '<div\\s*>', '</div>', 'gi', {
     *   valueNames: ['between', 'left', 'match', 'right']
     * });
     * // -> [
     * // {name: 'between', value: 'Here is ',       start: 0,  end: 8},
     * // {name: 'left',    value: '<div>',          start: 8,  end: 13},
     * // {name: 'match',   value: ' <div>an</div>', start: 13, end: 27},
     * // {name: 'right',   value: '</div>',         start: 27, end: 33},
     * // {name: 'between', value: ' example',       start: 33, end: 41}
     * // ]
     *
     * // Omitting unneeded parts with null valueNames, and using escapeChar
     * str = '...{1}.\\{{function(x,y){return {y:x}}}';
     * XRegExp.matchRecursive(str, '{', '}', 'g', {
     *   valueNames: ['literal', null, 'value', null],
     *   escapeChar: '\\'
     * });
     * // -> [
     * // {name: 'literal', value: '...',  start: 0, end: 3},
     * // {name: 'value',   value: '1',    start: 4, end: 5},
     * // {name: 'literal', value: '.\\{', start: 6, end: 9},
     * // {name: 'value',   value: 'function(x,y){return {y:x}}', start: 10, end: 37}
     * // ]
     *
     * // Sticky mode via flag y
     * str = '<1><<<2>>><3>4<5>';
     * XRegExp.matchRecursive(str, '<', '>', 'gy');
     * // -> ['1', '<<2>>', '3']
     */
    XRegExp.matchRecursive = function (str, left, right, flags, options) {
        flags = flags || '';
        options = options || {};
        var global = flags.indexOf('g') !== -1;
        var sticky = flags.indexOf('y') !== -1;
        // Flag `y` is controlled internally
        var basicFlags = flags.replace(/y/g, '');
        var escapeChar = options.escapeChar;
        var vN = options.valueNames;
        var output = [];
        var openTokens = 0;
        var delimStart = 0;
        var delimEnd = 0;
        var lastOuterEnd = 0;
        var outerStart = void 0;
        var innerStart = void 0;
        var leftMatch = void 0;
        var rightMatch = void 0;
        var esc = void 0;
        left = XRegExp(left, basicFlags);
        right = XRegExp(right, basicFlags);

        if (escapeChar) {
            if (escapeChar.length > 1) {
                throw new Error('Cannot use more than one escape character');
            }
            escapeChar = XRegExp.escape(escapeChar);
            // Example of concatenated `esc` regex:
            // `escapeChar`: '%'
            // `left`: '<'
            // `right`: '>'
            // Regex is: /(?:%[\S\s]|(?:(?!<|>)[^%])+)+/
            esc = new RegExp('(?:' + escapeChar + '[\\S\\s]|(?:(?!' +
            // Using `XRegExp.union` safely rewrites backreferences in `left` and `right`.
            // Intentionally not passing `basicFlags` to `XRegExp.union` since any syntax
            // transformation resulting from those flags was already applied to `left` and
            // `right` when they were passed through the XRegExp constructor above.
            XRegExp.union([left, right], '', { conjunction: 'or' }).source + ')[^' + escapeChar + '])+)+',
            // Flags `gy` not needed here
            flags.replace(/[^imu]+/g, ''));
        }

        while (true) {
            // If using an escape character, advance to the delimiter's next starting position,
            // skipping any escaped characters in between
            if (escapeChar) {
                delimEnd += (XRegExp.exec(str, esc, delimEnd, 'sticky') || [''])[0].length;
            }
            leftMatch = XRegExp.exec(str, left, delimEnd);
            rightMatch = XRegExp.exec(str, right, delimEnd);
            // Keep the leftmost match only
            if (leftMatch && rightMatch) {
                if (leftMatch.index <= rightMatch.index) {
                    rightMatch = null;
                } else {
                    leftMatch = null;
                }
            }
            // Paths (LM: leftMatch, RM: rightMatch, OT: openTokens):
            // LM | RM | OT | Result
            // 1  | 0  | 1  | loop
            // 1  | 0  | 0  | loop
            // 0  | 1  | 1  | loop
            // 0  | 1  | 0  | throw
            // 0  | 0  | 1  | throw
            // 0  | 0  | 0  | break
            // The paths above don't include the sticky mode special case. The loop ends after the
            // first completed match if not `global`.
            if (leftMatch || rightMatch) {
                delimStart = (leftMatch || rightMatch).index;
                delimEnd = delimStart + (leftMatch || rightMatch)[0].length;
            } else if (!openTokens) {
                break;
            }
            if (sticky && !openTokens && delimStart > lastOuterEnd) {
                break;
            }
            if (leftMatch) {
                if (!openTokens) {
                    outerStart = delimStart;
                    innerStart = delimEnd;
                }
                ++openTokens;
            } else if (rightMatch && openTokens) {
                if (! --openTokens) {
                    if (vN) {
                        if (vN[0] && outerStart > lastOuterEnd) {
                            output.push(row(vN[0], str.slice(lastOuterEnd, outerStart), lastOuterEnd, outerStart));
                        }
                        if (vN[1]) {
                            output.push(row(vN[1], str.slice(outerStart, innerStart), outerStart, innerStart));
                        }
                        if (vN[2]) {
                            output.push(row(vN[2], str.slice(innerStart, delimStart), innerStart, delimStart));
                        }
                        if (vN[3]) {
                            output.push(row(vN[3], str.slice(delimStart, delimEnd), delimStart, delimEnd));
                        }
                    } else {
                        output.push(str.slice(innerStart, delimStart));
                    }
                    lastOuterEnd = delimEnd;
                    if (!global) {
                        break;
                    }
                }
            } else {
                throw new Error('Unbalanced delimiter found in string');
            }
            // If the delimiter matched an empty string, avoid an infinite loop
            if (delimStart === delimEnd) {
                ++delimEnd;
            }
        }

        if (global && !sticky && vN && vN[0] && str.length > lastOuterEnd) {
            output.push(row(vN[0], str.slice(lastOuterEnd), lastOuterEnd, str.length));
        }

        return output;
    };
};

module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/addons/unicode-base.js":
/*!*********************************************************!*\
  !*** ./node_modules/xregexp/lib/addons/unicode-base.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

/*!
 * XRegExp Unicode Base 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2008-2017 MIT License
 */

exports.default = function (XRegExp) {

    /**
     * Adds base support for Unicode matching:
     * - Adds syntax `\p{..}` for matching Unicode tokens. Tokens can be inverted using `\P{..}` or
     *   `\p{^..}`. Token names ignore case, spaces, hyphens, and underscores. You can omit the
     *   braces for token names that are a single letter (e.g. `\pL` or `PL`).
     * - Adds flag A (astral), which enables 21-bit Unicode support.
     * - Adds the `XRegExp.addUnicodeData` method used by other addons to provide character data.
     *
     * Unicode Base relies on externally provided Unicode character data. Official addons are
     * available to provide data for Unicode categories, scripts, blocks, and properties.
     *
     * @requires XRegExp
     */

    // ==--------------------------==
    // Private stuff
    // ==--------------------------==

    // Storage for Unicode data
    var unicode = {};

    // Reuse utils
    var dec = XRegExp._dec;
    var hex = XRegExp._hex;
    var pad4 = XRegExp._pad4;

    // Generates a token lookup name: lowercase, with hyphens, spaces, and underscores removed
    function normalize(name) {
        return name.replace(/[- _]+/g, '').toLowerCase();
    }

    // Gets the decimal code of a literal code unit, \xHH, \uHHHH, or a backslash-escaped literal
    function charCode(chr) {
        var esc = /^\\[xu](.+)/.exec(chr);
        return esc ? dec(esc[1]) : chr.charCodeAt(chr[0] === '\\' ? 1 : 0);
    }

    // Inverts a list of ordered BMP characters and ranges
    function invertBmp(range) {
        var output = '';
        var lastEnd = -1;

        XRegExp.forEach(range, /(\\x..|\\u....|\\?[\s\S])(?:-(\\x..|\\u....|\\?[\s\S]))?/, function (m) {
            var start = charCode(m[1]);
            if (start > lastEnd + 1) {
                output += '\\u' + pad4(hex(lastEnd + 1));
                if (start > lastEnd + 2) {
                    output += '-\\u' + pad4(hex(start - 1));
                }
            }
            lastEnd = charCode(m[2] || m[1]);
        });

        if (lastEnd < 0xFFFF) {
            output += '\\u' + pad4(hex(lastEnd + 1));
            if (lastEnd < 0xFFFE) {
                output += '-\\uFFFF';
            }
        }

        return output;
    }

    // Generates an inverted BMP range on first use
    function cacheInvertedBmp(slug) {
        var prop = 'b!';
        return unicode[slug][prop] || (unicode[slug][prop] = invertBmp(unicode[slug].bmp));
    }

    // Combines and optionally negates BMP and astral data
    function buildAstral(slug, isNegated) {
        var item = unicode[slug];
        var combined = '';

        if (item.bmp && !item.isBmpLast) {
            combined = '[' + item.bmp + ']' + (item.astral ? '|' : '');
        }
        if (item.astral) {
            combined += item.astral;
        }
        if (item.isBmpLast && item.bmp) {
            combined += (item.astral ? '|' : '') + '[' + item.bmp + ']';
        }

        // Astral Unicode tokens always match a code point, never a code unit
        return isNegated ? '(?:(?!' + combined + ')(?:[\uD800-\uDBFF][\uDC00-\uDFFF]|[\0-\uFFFF]))' : '(?:' + combined + ')';
    }

    // Builds a complete astral pattern on first use
    function cacheAstral(slug, isNegated) {
        var prop = isNegated ? 'a!' : 'a=';
        return unicode[slug][prop] || (unicode[slug][prop] = buildAstral(slug, isNegated));
    }

    // ==--------------------------==
    // Core functionality
    // ==--------------------------==

    /*
     * Add astral mode (flag A) and Unicode token syntax: `\p{..}`, `\P{..}`, `\p{^..}`, `\pC`.
     */
    XRegExp.addToken(
    // Use `*` instead of `+` to avoid capturing `^` as the token name in `\p{^}`
    /\\([pP])(?:{(\^?)([^}]*)}|([A-Za-z]))/, function (match, scope, flags) {
        var ERR_DOUBLE_NEG = 'Invalid double negation ';
        var ERR_UNKNOWN_NAME = 'Unknown Unicode token ';
        var ERR_UNKNOWN_REF = 'Unicode token missing data ';
        var ERR_ASTRAL_ONLY = 'Astral mode required for Unicode token ';
        var ERR_ASTRAL_IN_CLASS = 'Astral mode does not support Unicode tokens within character classes';
        // Negated via \P{..} or \p{^..}
        var isNegated = match[1] === 'P' || !!match[2];
        // Switch from BMP (0-FFFF) to astral (0-10FFFF) mode via flag A
        var isAstralMode = flags.indexOf('A') !== -1;
        // Token lookup name. Check `[4]` first to avoid passing `undefined` via `\p{}`
        var slug = normalize(match[4] || match[3]);
        // Token data object
        var item = unicode[slug];

        if (match[1] === 'P' && match[2]) {
            throw new SyntaxError(ERR_DOUBLE_NEG + match[0]);
        }
        if (!unicode.hasOwnProperty(slug)) {
            throw new SyntaxError(ERR_UNKNOWN_NAME + match[0]);
        }

        // Switch to the negated form of the referenced Unicode token
        if (item.inverseOf) {
            slug = normalize(item.inverseOf);
            if (!unicode.hasOwnProperty(slug)) {
                throw new ReferenceError(ERR_UNKNOWN_REF + match[0] + ' -> ' + item.inverseOf);
            }
            item = unicode[slug];
            isNegated = !isNegated;
        }

        if (!(item.bmp || isAstralMode)) {
            throw new SyntaxError(ERR_ASTRAL_ONLY + match[0]);
        }
        if (isAstralMode) {
            if (scope === 'class') {
                throw new SyntaxError(ERR_ASTRAL_IN_CLASS);
            }

            return cacheAstral(slug, isNegated);
        }

        return scope === 'class' ? isNegated ? cacheInvertedBmp(slug) : item.bmp : (isNegated ? '[^' : '[') + item.bmp + ']';
    }, {
        scope: 'all',
        optionalFlags: 'A',
        leadChar: '\\'
    });

    /**
     * Adds to the list of Unicode tokens that XRegExp regexes can match via `\p` or `\P`.
     *
     * @memberOf XRegExp
     * @param {Array} data Objects with named character ranges. Each object may have properties
     *   `name`, `alias`, `isBmpLast`, `inverseOf`, `bmp`, and `astral`. All but `name` are
     *   optional, although one of `bmp` or `astral` is required (unless `inverseOf` is set). If
     *   `astral` is absent, the `bmp` data is used for BMP and astral modes. If `bmp` is absent,
     *   the name errors in BMP mode but works in astral mode. If both `bmp` and `astral` are
     *   provided, the `bmp` data only is used in BMP mode, and the combination of `bmp` and
     *   `astral` data is used in astral mode. `isBmpLast` is needed when a token matches orphan
     *   high surrogates *and* uses surrogate pairs to match astral code points. The `bmp` and
     *   `astral` data should be a combination of literal characters and `\xHH` or `\uHHHH` escape
     *   sequences, with hyphens to create ranges. Any regex metacharacters in the data should be
     *   escaped, apart from range-creating hyphens. The `astral` data can additionally use
     *   character classes and alternation, and should use surrogate pairs to represent astral code
     *   points. `inverseOf` can be used to avoid duplicating character data if a Unicode token is
     *   defined as the exact inverse of another token.
     * @example
     *
     * // Basic use
     * XRegExp.addUnicodeData([{
     *   name: 'XDigit',
     *   alias: 'Hexadecimal',
     *   bmp: '0-9A-Fa-f'
     * }]);
     * XRegExp('\\p{XDigit}:\\p{Hexadecimal}+').test('0:3D'); // -> true
     */
    XRegExp.addUnicodeData = function (data) {
        var ERR_NO_NAME = 'Unicode token requires name';
        var ERR_NO_DATA = 'Unicode token has no character data ';
        var item = void 0;

        for (var i = 0; i < data.length; ++i) {
            item = data[i];
            if (!item.name) {
                throw new Error(ERR_NO_NAME);
            }
            if (!(item.inverseOf || item.bmp || item.astral)) {
                throw new Error(ERR_NO_DATA + item.name);
            }
            unicode[normalize(item.name)] = item;
            if (item.alias) {
                unicode[normalize(item.alias)] = item;
            }
        }

        // Reset the pattern cache used by the `XRegExp` constructor, since the same pattern and
        // flags might now produce different results
        XRegExp.cache.flush('patterns');
    };

    /**
     * @ignore
     *
     * Return a reference to the internal Unicode definition structure for the given Unicode
     * Property if the given name is a legal Unicode Property for use in XRegExp `\p` or `\P` regex
     * constructs.
     *
     * @memberOf XRegExp
     * @param {String} name Name by which the Unicode Property may be recognized (case-insensitive),
     *   e.g. `'N'` or `'Number'`. The given name is matched against all registered Unicode
     *   Properties and Property Aliases.
     * @returns {Object} Reference to definition structure when the name matches a Unicode Property.
     *
     * @note
     * For more info on Unicode Properties, see also http://unicode.org/reports/tr18/#Categories.
     *
     * @note
     * This method is *not* part of the officially documented API and may change or be removed in
     * the future. It is meant for userland code that wishes to reuse the (large) internal Unicode
     * structures set up by XRegExp.
     */
    XRegExp._getUnicodeProperty = function (name) {
        var slug = normalize(name);
        return unicode[slug];
    };
};

module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/addons/unicode-blocks.js":
/*!***********************************************************!*\
  !*** ./node_modules/xregexp/lib/addons/unicode-blocks.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

/*!
 * XRegExp Unicode Blocks 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2010-2017 MIT License
 * Unicode data by Mathias Bynens <mathiasbynens.be>
 */

exports.default = function (XRegExp) {

    /**
     * Adds support for all Unicode blocks. Block names use the prefix 'In'. E.g.,
     * `\p{InBasicLatin}`. Token names are case insensitive, and any spaces, hyphens, and
     * underscores are ignored.
     *
     * Uses Unicode 9.0.0.
     *
     * @requires XRegExp, Unicode Base
     */

    if (!XRegExp.addUnicodeData) {
        throw new ReferenceError('Unicode Base must be loaded before Unicode Blocks');
    }

    XRegExp.addUnicodeData([{
        name: 'InAdlam',
        astral: '\uD83A[\uDD00-\uDD5F]'
    }, {
        name: 'InAegean_Numbers',
        astral: '\uD800[\uDD00-\uDD3F]'
    }, {
        name: 'InAhom',
        astral: '\uD805[\uDF00-\uDF3F]'
    }, {
        name: 'InAlchemical_Symbols',
        astral: '\uD83D[\uDF00-\uDF7F]'
    }, {
        name: 'InAlphabetic_Presentation_Forms',
        bmp: '\uFB00-\uFB4F'
    }, {
        name: 'InAnatolian_Hieroglyphs',
        astral: '\uD811[\uDC00-\uDE7F]'
    }, {
        name: 'InAncient_Greek_Musical_Notation',
        astral: '\uD834[\uDE00-\uDE4F]'
    }, {
        name: 'InAncient_Greek_Numbers',
        astral: '\uD800[\uDD40-\uDD8F]'
    }, {
        name: 'InAncient_Symbols',
        astral: '\uD800[\uDD90-\uDDCF]'
    }, {
        name: 'InArabic',
        bmp: '\u0600-\u06FF'
    }, {
        name: 'InArabic_Extended_A',
        bmp: '\u08A0-\u08FF'
    }, {
        name: 'InArabic_Mathematical_Alphabetic_Symbols',
        astral: '\uD83B[\uDE00-\uDEFF]'
    }, {
        name: 'InArabic_Presentation_Forms_A',
        bmp: '\uFB50-\uFDFF'
    }, {
        name: 'InArabic_Presentation_Forms_B',
        bmp: '\uFE70-\uFEFF'
    }, {
        name: 'InArabic_Supplement',
        bmp: '\u0750-\u077F'
    }, {
        name: 'InArmenian',
        bmp: '\u0530-\u058F'
    }, {
        name: 'InArrows',
        bmp: '\u2190-\u21FF'
    }, {
        name: 'InAvestan',
        astral: '\uD802[\uDF00-\uDF3F]'
    }, {
        name: 'InBalinese',
        bmp: '\u1B00-\u1B7F'
    }, {
        name: 'InBamum',
        bmp: '\uA6A0-\uA6FF'
    }, {
        name: 'InBamum_Supplement',
        astral: '\uD81A[\uDC00-\uDE3F]'
    }, {
        name: 'InBasic_Latin',
        bmp: '\0-\x7F'
    }, {
        name: 'InBassa_Vah',
        astral: '\uD81A[\uDED0-\uDEFF]'
    }, {
        name: 'InBatak',
        bmp: '\u1BC0-\u1BFF'
    }, {
        name: 'InBengali',
        bmp: '\u0980-\u09FF'
    }, {
        name: 'InBhaiksuki',
        astral: '\uD807[\uDC00-\uDC6F]'
    }, {
        name: 'InBlock_Elements',
        bmp: '\u2580-\u259F'
    }, {
        name: 'InBopomofo',
        bmp: '\u3100-\u312F'
    }, {
        name: 'InBopomofo_Extended',
        bmp: '\u31A0-\u31BF'
    }, {
        name: 'InBox_Drawing',
        bmp: '\u2500-\u257F'
    }, {
        name: 'InBrahmi',
        astral: '\uD804[\uDC00-\uDC7F]'
    }, {
        name: 'InBraille_Patterns',
        bmp: '\u2800-\u28FF'
    }, {
        name: 'InBuginese',
        bmp: '\u1A00-\u1A1F'
    }, {
        name: 'InBuhid',
        bmp: '\u1740-\u175F'
    }, {
        name: 'InByzantine_Musical_Symbols',
        astral: '\uD834[\uDC00-\uDCFF]'
    }, {
        name: 'InCJK_Compatibility',
        bmp: '\u3300-\u33FF'
    }, {
        name: 'InCJK_Compatibility_Forms',
        bmp: '\uFE30-\uFE4F'
    }, {
        name: 'InCJK_Compatibility_Ideographs',
        bmp: '\uF900-\uFAFF'
    }, {
        name: 'InCJK_Compatibility_Ideographs_Supplement',
        astral: '\uD87E[\uDC00-\uDE1F]'
    }, {
        name: 'InCJK_Radicals_Supplement',
        bmp: '\u2E80-\u2EFF'
    }, {
        name: 'InCJK_Strokes',
        bmp: '\u31C0-\u31EF'
    }, {
        name: 'InCJK_Symbols_and_Punctuation',
        bmp: '\u3000-\u303F'
    }, {
        name: 'InCJK_Unified_Ideographs',
        bmp: '\u4E00-\u9FFF'
    }, {
        name: 'InCJK_Unified_Ideographs_Extension_A',
        bmp: '\u3400-\u4DBF'
    }, {
        name: 'InCJK_Unified_Ideographs_Extension_B',
        astral: '[\uD840-\uD868][\uDC00-\uDFFF]|\uD869[\uDC00-\uDEDF]'
    }, {
        name: 'InCJK_Unified_Ideographs_Extension_C',
        astral: '\uD869[\uDF00-\uDFFF]|[\uD86A-\uD86C][\uDC00-\uDFFF]|\uD86D[\uDC00-\uDF3F]'
    }, {
        name: 'InCJK_Unified_Ideographs_Extension_D',
        astral: '\uD86D[\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1F]'
    }, {
        name: 'InCJK_Unified_Ideographs_Extension_E',
        astral: '\uD86E[\uDC20-\uDFFF]|[\uD86F-\uD872][\uDC00-\uDFFF]|\uD873[\uDC00-\uDEAF]'
    }, {
        name: 'InCarian',
        astral: '\uD800[\uDEA0-\uDEDF]'
    }, {
        name: 'InCaucasian_Albanian',
        astral: '\uD801[\uDD30-\uDD6F]'
    }, {
        name: 'InChakma',
        astral: '\uD804[\uDD00-\uDD4F]'
    }, {
        name: 'InCham',
        bmp: '\uAA00-\uAA5F'
    }, {
        name: 'InCherokee',
        bmp: '\u13A0-\u13FF'
    }, {
        name: 'InCherokee_Supplement',
        bmp: '\uAB70-\uABBF'
    }, {
        name: 'InCombining_Diacritical_Marks',
        bmp: '\u0300-\u036F'
    }, {
        name: 'InCombining_Diacritical_Marks_Extended',
        bmp: '\u1AB0-\u1AFF'
    }, {
        name: 'InCombining_Diacritical_Marks_Supplement',
        bmp: '\u1DC0-\u1DFF'
    }, {
        name: 'InCombining_Diacritical_Marks_for_Symbols',
        bmp: '\u20D0-\u20FF'
    }, {
        name: 'InCombining_Half_Marks',
        bmp: '\uFE20-\uFE2F'
    }, {
        name: 'InCommon_Indic_Number_Forms',
        bmp: '\uA830-\uA83F'
    }, {
        name: 'InControl_Pictures',
        bmp: '\u2400-\u243F'
    }, {
        name: 'InCoptic',
        bmp: '\u2C80-\u2CFF'
    }, {
        name: 'InCoptic_Epact_Numbers',
        astral: '\uD800[\uDEE0-\uDEFF]'
    }, {
        name: 'InCounting_Rod_Numerals',
        astral: '\uD834[\uDF60-\uDF7F]'
    }, {
        name: 'InCuneiform',
        astral: '\uD808[\uDC00-\uDFFF]'
    }, {
        name: 'InCuneiform_Numbers_and_Punctuation',
        astral: '\uD809[\uDC00-\uDC7F]'
    }, {
        name: 'InCurrency_Symbols',
        bmp: '\u20A0-\u20CF'
    }, {
        name: 'InCypriot_Syllabary',
        astral: '\uD802[\uDC00-\uDC3F]'
    }, {
        name: 'InCyrillic',
        bmp: '\u0400-\u04FF'
    }, {
        name: 'InCyrillic_Extended_A',
        bmp: '\u2DE0-\u2DFF'
    }, {
        name: 'InCyrillic_Extended_B',
        bmp: '\uA640-\uA69F'
    }, {
        name: 'InCyrillic_Extended_C',
        bmp: '\u1C80-\u1C8F'
    }, {
        name: 'InCyrillic_Supplement',
        bmp: '\u0500-\u052F'
    }, {
        name: 'InDeseret',
        astral: '\uD801[\uDC00-\uDC4F]'
    }, {
        name: 'InDevanagari',
        bmp: '\u0900-\u097F'
    }, {
        name: 'InDevanagari_Extended',
        bmp: '\uA8E0-\uA8FF'
    }, {
        name: 'InDingbats',
        bmp: '\u2700-\u27BF'
    }, {
        name: 'InDomino_Tiles',
        astral: '\uD83C[\uDC30-\uDC9F]'
    }, {
        name: 'InDuployan',
        astral: '\uD82F[\uDC00-\uDC9F]'
    }, {
        name: 'InEarly_Dynastic_Cuneiform',
        astral: '\uD809[\uDC80-\uDD4F]'
    }, {
        name: 'InEgyptian_Hieroglyphs',
        astral: '\uD80C[\uDC00-\uDFFF]|\uD80D[\uDC00-\uDC2F]'
    }, {
        name: 'InElbasan',
        astral: '\uD801[\uDD00-\uDD2F]'
    }, {
        name: 'InEmoticons',
        astral: '\uD83D[\uDE00-\uDE4F]'
    }, {
        name: 'InEnclosed_Alphanumeric_Supplement',
        astral: '\uD83C[\uDD00-\uDDFF]'
    }, {
        name: 'InEnclosed_Alphanumerics',
        bmp: '\u2460-\u24FF'
    }, {
        name: 'InEnclosed_CJK_Letters_and_Months',
        bmp: '\u3200-\u32FF'
    }, {
        name: 'InEnclosed_Ideographic_Supplement',
        astral: '\uD83C[\uDE00-\uDEFF]'
    }, {
        name: 'InEthiopic',
        bmp: '\u1200-\u137F'
    }, {
        name: 'InEthiopic_Extended',
        bmp: '\u2D80-\u2DDF'
    }, {
        name: 'InEthiopic_Extended_A',
        bmp: '\uAB00-\uAB2F'
    }, {
        name: 'InEthiopic_Supplement',
        bmp: '\u1380-\u139F'
    }, {
        name: 'InGeneral_Punctuation',
        bmp: '\u2000-\u206F'
    }, {
        name: 'InGeometric_Shapes',
        bmp: '\u25A0-\u25FF'
    }, {
        name: 'InGeometric_Shapes_Extended',
        astral: '\uD83D[\uDF80-\uDFFF]'
    }, {
        name: 'InGeorgian',
        bmp: '\u10A0-\u10FF'
    }, {
        name: 'InGeorgian_Supplement',
        bmp: '\u2D00-\u2D2F'
    }, {
        name: 'InGlagolitic',
        bmp: '\u2C00-\u2C5F'
    }, {
        name: 'InGlagolitic_Supplement',
        astral: '\uD838[\uDC00-\uDC2F]'
    }, {
        name: 'InGothic',
        astral: '\uD800[\uDF30-\uDF4F]'
    }, {
        name: 'InGrantha',
        astral: '\uD804[\uDF00-\uDF7F]'
    }, {
        name: 'InGreek_Extended',
        bmp: '\u1F00-\u1FFF'
    }, {
        name: 'InGreek_and_Coptic',
        bmp: '\u0370-\u03FF'
    }, {
        name: 'InGujarati',
        bmp: '\u0A80-\u0AFF'
    }, {
        name: 'InGurmukhi',
        bmp: '\u0A00-\u0A7F'
    }, {
        name: 'InHalfwidth_and_Fullwidth_Forms',
        bmp: '\uFF00-\uFFEF'
    }, {
        name: 'InHangul_Compatibility_Jamo',
        bmp: '\u3130-\u318F'
    }, {
        name: 'InHangul_Jamo',
        bmp: '\u1100-\u11FF'
    }, {
        name: 'InHangul_Jamo_Extended_A',
        bmp: '\uA960-\uA97F'
    }, {
        name: 'InHangul_Jamo_Extended_B',
        bmp: '\uD7B0-\uD7FF'
    }, {
        name: 'InHangul_Syllables',
        bmp: '\uAC00-\uD7AF'
    }, {
        name: 'InHanunoo',
        bmp: '\u1720-\u173F'
    }, {
        name: 'InHatran',
        astral: '\uD802[\uDCE0-\uDCFF]'
    }, {
        name: 'InHebrew',
        bmp: '\u0590-\u05FF'
    }, {
        name: 'InHigh_Private_Use_Surrogates',
        bmp: '\uDB80-\uDBFF'
    }, {
        name: 'InHigh_Surrogates',
        bmp: '\uD800-\uDB7F'
    }, {
        name: 'InHiragana',
        bmp: '\u3040-\u309F'
    }, {
        name: 'InIPA_Extensions',
        bmp: '\u0250-\u02AF'
    }, {
        name: 'InIdeographic_Description_Characters',
        bmp: '\u2FF0-\u2FFF'
    }, {
        name: 'InIdeographic_Symbols_and_Punctuation',
        astral: '\uD81B[\uDFE0-\uDFFF]'
    }, {
        name: 'InImperial_Aramaic',
        astral: '\uD802[\uDC40-\uDC5F]'
    }, {
        name: 'InInscriptional_Pahlavi',
        astral: '\uD802[\uDF60-\uDF7F]'
    }, {
        name: 'InInscriptional_Parthian',
        astral: '\uD802[\uDF40-\uDF5F]'
    }, {
        name: 'InJavanese',
        bmp: '\uA980-\uA9DF'
    }, {
        name: 'InKaithi',
        astral: '\uD804[\uDC80-\uDCCF]'
    }, {
        name: 'InKana_Supplement',
        astral: '\uD82C[\uDC00-\uDCFF]'
    }, {
        name: 'InKanbun',
        bmp: '\u3190-\u319F'
    }, {
        name: 'InKangxi_Radicals',
        bmp: '\u2F00-\u2FDF'
    }, {
        name: 'InKannada',
        bmp: '\u0C80-\u0CFF'
    }, {
        name: 'InKatakana',
        bmp: '\u30A0-\u30FF'
    }, {
        name: 'InKatakana_Phonetic_Extensions',
        bmp: '\u31F0-\u31FF'
    }, {
        name: 'InKayah_Li',
        bmp: '\uA900-\uA92F'
    }, {
        name: 'InKharoshthi',
        astral: '\uD802[\uDE00-\uDE5F]'
    }, {
        name: 'InKhmer',
        bmp: '\u1780-\u17FF'
    }, {
        name: 'InKhmer_Symbols',
        bmp: '\u19E0-\u19FF'
    }, {
        name: 'InKhojki',
        astral: '\uD804[\uDE00-\uDE4F]'
    }, {
        name: 'InKhudawadi',
        astral: '\uD804[\uDEB0-\uDEFF]'
    }, {
        name: 'InLao',
        bmp: '\u0E80-\u0EFF'
    }, {
        name: 'InLatin_Extended_Additional',
        bmp: '\u1E00-\u1EFF'
    }, {
        name: 'InLatin_Extended_A',
        bmp: '\u0100-\u017F'
    }, {
        name: 'InLatin_Extended_B',
        bmp: '\u0180-\u024F'
    }, {
        name: 'InLatin_Extended_C',
        bmp: '\u2C60-\u2C7F'
    }, {
        name: 'InLatin_Extended_D',
        bmp: '\uA720-\uA7FF'
    }, {
        name: 'InLatin_Extended_E',
        bmp: '\uAB30-\uAB6F'
    }, {
        name: 'InLatin_1_Supplement',
        bmp: '\x80-\xFF'
    }, {
        name: 'InLepcha',
        bmp: '\u1C00-\u1C4F'
    }, {
        name: 'InLetterlike_Symbols',
        bmp: '\u2100-\u214F'
    }, {
        name: 'InLimbu',
        bmp: '\u1900-\u194F'
    }, {
        name: 'InLinear_A',
        astral: '\uD801[\uDE00-\uDF7F]'
    }, {
        name: 'InLinear_B_Ideograms',
        astral: '\uD800[\uDC80-\uDCFF]'
    }, {
        name: 'InLinear_B_Syllabary',
        astral: '\uD800[\uDC00-\uDC7F]'
    }, {
        name: 'InLisu',
        bmp: '\uA4D0-\uA4FF'
    }, {
        name: 'InLow_Surrogates',
        bmp: '\uDC00-\uDFFF'
    }, {
        name: 'InLycian',
        astral: '\uD800[\uDE80-\uDE9F]'
    }, {
        name: 'InLydian',
        astral: '\uD802[\uDD20-\uDD3F]'
    }, {
        name: 'InMahajani',
        astral: '\uD804[\uDD50-\uDD7F]'
    }, {
        name: 'InMahjong_Tiles',
        astral: '\uD83C[\uDC00-\uDC2F]'
    }, {
        name: 'InMalayalam',
        bmp: '\u0D00-\u0D7F'
    }, {
        name: 'InMandaic',
        bmp: '\u0840-\u085F'
    }, {
        name: 'InManichaean',
        astral: '\uD802[\uDEC0-\uDEFF]'
    }, {
        name: 'InMarchen',
        astral: '\uD807[\uDC70-\uDCBF]'
    }, {
        name: 'InMathematical_Alphanumeric_Symbols',
        astral: '\uD835[\uDC00-\uDFFF]'
    }, {
        name: 'InMathematical_Operators',
        bmp: '\u2200-\u22FF'
    }, {
        name: 'InMeetei_Mayek',
        bmp: '\uABC0-\uABFF'
    }, {
        name: 'InMeetei_Mayek_Extensions',
        bmp: '\uAAE0-\uAAFF'
    }, {
        name: 'InMende_Kikakui',
        astral: '\uD83A[\uDC00-\uDCDF]'
    }, {
        name: 'InMeroitic_Cursive',
        astral: '\uD802[\uDDA0-\uDDFF]'
    }, {
        name: 'InMeroitic_Hieroglyphs',
        astral: '\uD802[\uDD80-\uDD9F]'
    }, {
        name: 'InMiao',
        astral: '\uD81B[\uDF00-\uDF9F]'
    }, {
        name: 'InMiscellaneous_Mathematical_Symbols_A',
        bmp: '\u27C0-\u27EF'
    }, {
        name: 'InMiscellaneous_Mathematical_Symbols_B',
        bmp: '\u2980-\u29FF'
    }, {
        name: 'InMiscellaneous_Symbols',
        bmp: '\u2600-\u26FF'
    }, {
        name: 'InMiscellaneous_Symbols_and_Arrows',
        bmp: '\u2B00-\u2BFF'
    }, {
        name: 'InMiscellaneous_Symbols_and_Pictographs',
        astral: '\uD83C[\uDF00-\uDFFF]|\uD83D[\uDC00-\uDDFF]'
    }, {
        name: 'InMiscellaneous_Technical',
        bmp: '\u2300-\u23FF'
    }, {
        name: 'InModi',
        astral: '\uD805[\uDE00-\uDE5F]'
    }, {
        name: 'InModifier_Tone_Letters',
        bmp: '\uA700-\uA71F'
    }, {
        name: 'InMongolian',
        bmp: '\u1800-\u18AF'
    }, {
        name: 'InMongolian_Supplement',
        astral: '\uD805[\uDE60-\uDE7F]'
    }, {
        name: 'InMro',
        astral: '\uD81A[\uDE40-\uDE6F]'
    }, {
        name: 'InMultani',
        astral: '\uD804[\uDE80-\uDEAF]'
    }, {
        name: 'InMusical_Symbols',
        astral: '\uD834[\uDD00-\uDDFF]'
    }, {
        name: 'InMyanmar',
        bmp: '\u1000-\u109F'
    }, {
        name: 'InMyanmar_Extended_A',
        bmp: '\uAA60-\uAA7F'
    }, {
        name: 'InMyanmar_Extended_B',
        bmp: '\uA9E0-\uA9FF'
    }, {
        name: 'InNKo',
        bmp: '\u07C0-\u07FF'
    }, {
        name: 'InNabataean',
        astral: '\uD802[\uDC80-\uDCAF]'
    }, {
        name: 'InNew_Tai_Lue',
        bmp: '\u1980-\u19DF'
    }, {
        name: 'InNewa',
        astral: '\uD805[\uDC00-\uDC7F]'
    }, {
        name: 'InNumber_Forms',
        bmp: '\u2150-\u218F'
    }, {
        name: 'InOgham',
        bmp: '\u1680-\u169F'
    }, {
        name: 'InOl_Chiki',
        bmp: '\u1C50-\u1C7F'
    }, {
        name: 'InOld_Hungarian',
        astral: '\uD803[\uDC80-\uDCFF]'
    }, {
        name: 'InOld_Italic',
        astral: '\uD800[\uDF00-\uDF2F]'
    }, {
        name: 'InOld_North_Arabian',
        astral: '\uD802[\uDE80-\uDE9F]'
    }, {
        name: 'InOld_Permic',
        astral: '\uD800[\uDF50-\uDF7F]'
    }, {
        name: 'InOld_Persian',
        astral: '\uD800[\uDFA0-\uDFDF]'
    }, {
        name: 'InOld_South_Arabian',
        astral: '\uD802[\uDE60-\uDE7F]'
    }, {
        name: 'InOld_Turkic',
        astral: '\uD803[\uDC00-\uDC4F]'
    }, {
        name: 'InOptical_Character_Recognition',
        bmp: '\u2440-\u245F'
    }, {
        name: 'InOriya',
        bmp: '\u0B00-\u0B7F'
    }, {
        name: 'InOrnamental_Dingbats',
        astral: '\uD83D[\uDE50-\uDE7F]'
    }, {
        name: 'InOsage',
        astral: '\uD801[\uDCB0-\uDCFF]'
    }, {
        name: 'InOsmanya',
        astral: '\uD801[\uDC80-\uDCAF]'
    }, {
        name: 'InPahawh_Hmong',
        astral: '\uD81A[\uDF00-\uDF8F]'
    }, {
        name: 'InPalmyrene',
        astral: '\uD802[\uDC60-\uDC7F]'
    }, {
        name: 'InPau_Cin_Hau',
        astral: '\uD806[\uDEC0-\uDEFF]'
    }, {
        name: 'InPhags_pa',
        bmp: '\uA840-\uA87F'
    }, {
        name: 'InPhaistos_Disc',
        astral: '\uD800[\uDDD0-\uDDFF]'
    }, {
        name: 'InPhoenician',
        astral: '\uD802[\uDD00-\uDD1F]'
    }, {
        name: 'InPhonetic_Extensions',
        bmp: '\u1D00-\u1D7F'
    }, {
        name: 'InPhonetic_Extensions_Supplement',
        bmp: '\u1D80-\u1DBF'
    }, {
        name: 'InPlaying_Cards',
        astral: '\uD83C[\uDCA0-\uDCFF]'
    }, {
        name: 'InPrivate_Use_Area',
        bmp: '\uE000-\uF8FF'
    }, {
        name: 'InPsalter_Pahlavi',
        astral: '\uD802[\uDF80-\uDFAF]'
    }, {
        name: 'InRejang',
        bmp: '\uA930-\uA95F'
    }, {
        name: 'InRumi_Numeral_Symbols',
        astral: '\uD803[\uDE60-\uDE7F]'
    }, {
        name: 'InRunic',
        bmp: '\u16A0-\u16FF'
    }, {
        name: 'InSamaritan',
        bmp: '\u0800-\u083F'
    }, {
        name: 'InSaurashtra',
        bmp: '\uA880-\uA8DF'
    }, {
        name: 'InSharada',
        astral: '\uD804[\uDD80-\uDDDF]'
    }, {
        name: 'InShavian',
        astral: '\uD801[\uDC50-\uDC7F]'
    }, {
        name: 'InShorthand_Format_Controls',
        astral: '\uD82F[\uDCA0-\uDCAF]'
    }, {
        name: 'InSiddham',
        astral: '\uD805[\uDD80-\uDDFF]'
    }, {
        name: 'InSinhala',
        bmp: '\u0D80-\u0DFF'
    }, {
        name: 'InSinhala_Archaic_Numbers',
        astral: '\uD804[\uDDE0-\uDDFF]'
    }, {
        name: 'InSmall_Form_Variants',
        bmp: '\uFE50-\uFE6F'
    }, {
        name: 'InSora_Sompeng',
        astral: '\uD804[\uDCD0-\uDCFF]'
    }, {
        name: 'InSpacing_Modifier_Letters',
        bmp: '\u02B0-\u02FF'
    }, {
        name: 'InSpecials',
        bmp: '\uFFF0-\uFFFF'
    }, {
        name: 'InSundanese',
        bmp: '\u1B80-\u1BBF'
    }, {
        name: 'InSundanese_Supplement',
        bmp: '\u1CC0-\u1CCF'
    }, {
        name: 'InSuperscripts_and_Subscripts',
        bmp: '\u2070-\u209F'
    }, {
        name: 'InSupplemental_Arrows_A',
        bmp: '\u27F0-\u27FF'
    }, {
        name: 'InSupplemental_Arrows_B',
        bmp: '\u2900-\u297F'
    }, {
        name: 'InSupplemental_Arrows_C',
        astral: '\uD83E[\uDC00-\uDCFF]'
    }, {
        name: 'InSupplemental_Mathematical_Operators',
        bmp: '\u2A00-\u2AFF'
    }, {
        name: 'InSupplemental_Punctuation',
        bmp: '\u2E00-\u2E7F'
    }, {
        name: 'InSupplemental_Symbols_and_Pictographs',
        astral: '\uD83E[\uDD00-\uDDFF]'
    }, {
        name: 'InSupplementary_Private_Use_Area_A',
        astral: '[\uDB80-\uDBBF][\uDC00-\uDFFF]'
    }, {
        name: 'InSupplementary_Private_Use_Area_B',
        astral: '[\uDBC0-\uDBFF][\uDC00-\uDFFF]'
    }, {
        name: 'InSutton_SignWriting',
        astral: '\uD836[\uDC00-\uDEAF]'
    }, {
        name: 'InSyloti_Nagri',
        bmp: '\uA800-\uA82F'
    }, {
        name: 'InSyriac',
        bmp: '\u0700-\u074F'
    }, {
        name: 'InTagalog',
        bmp: '\u1700-\u171F'
    }, {
        name: 'InTagbanwa',
        bmp: '\u1760-\u177F'
    }, {
        name: 'InTags',
        astral: '\uDB40[\uDC00-\uDC7F]'
    }, {
        name: 'InTai_Le',
        bmp: '\u1950-\u197F'
    }, {
        name: 'InTai_Tham',
        bmp: '\u1A20-\u1AAF'
    }, {
        name: 'InTai_Viet',
        bmp: '\uAA80-\uAADF'
    }, {
        name: 'InTai_Xuan_Jing_Symbols',
        astral: '\uD834[\uDF00-\uDF5F]'
    }, {
        name: 'InTakri',
        astral: '\uD805[\uDE80-\uDECF]'
    }, {
        name: 'InTamil',
        bmp: '\u0B80-\u0BFF'
    }, {
        name: 'InTangut',
        astral: '[\uD81C-\uD821][\uDC00-\uDFFF]'
    }, {
        name: 'InTangut_Components',
        astral: '\uD822[\uDC00-\uDEFF]'
    }, {
        name: 'InTelugu',
        bmp: '\u0C00-\u0C7F'
    }, {
        name: 'InThaana',
        bmp: '\u0780-\u07BF'
    }, {
        name: 'InThai',
        bmp: '\u0E00-\u0E7F'
    }, {
        name: 'InTibetan',
        bmp: '\u0F00-\u0FFF'
    }, {
        name: 'InTifinagh',
        bmp: '\u2D30-\u2D7F'
    }, {
        name: 'InTirhuta',
        astral: '\uD805[\uDC80-\uDCDF]'
    }, {
        name: 'InTransport_and_Map_Symbols',
        astral: '\uD83D[\uDE80-\uDEFF]'
    }, {
        name: 'InUgaritic',
        astral: '\uD800[\uDF80-\uDF9F]'
    }, {
        name: 'InUnified_Canadian_Aboriginal_Syllabics',
        bmp: '\u1400-\u167F'
    }, {
        name: 'InUnified_Canadian_Aboriginal_Syllabics_Extended',
        bmp: '\u18B0-\u18FF'
    }, {
        name: 'InVai',
        bmp: '\uA500-\uA63F'
    }, {
        name: 'InVariation_Selectors',
        bmp: '\uFE00-\uFE0F'
    }, {
        name: 'InVariation_Selectors_Supplement',
        astral: '\uDB40[\uDD00-\uDDEF]'
    }, {
        name: 'InVedic_Extensions',
        bmp: '\u1CD0-\u1CFF'
    }, {
        name: 'InVertical_Forms',
        bmp: '\uFE10-\uFE1F'
    }, {
        name: 'InWarang_Citi',
        astral: '\uD806[\uDCA0-\uDCFF]'
    }, {
        name: 'InYi_Radicals',
        bmp: '\uA490-\uA4CF'
    }, {
        name: 'InYi_Syllables',
        bmp: '\uA000-\uA48F'
    }, {
        name: 'InYijing_Hexagram_Symbols',
        bmp: '\u4DC0-\u4DFF'
    }]);
};

module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/addons/unicode-categories.js":
/*!***************************************************************!*\
  !*** ./node_modules/xregexp/lib/addons/unicode-categories.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

/*!
 * XRegExp Unicode Categories 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2010-2017 MIT License
 * Unicode data by Mathias Bynens <mathiasbynens.be>
 */

exports.default = function (XRegExp) {

    /**
     * Adds support for Unicode's general categories. E.g., `\p{Lu}` or `\p{Uppercase Letter}`. See
     * category descriptions in UAX #44 <http://unicode.org/reports/tr44/#GC_Values_Table>. Token
     * names are case insensitive, and any spaces, hyphens, and underscores are ignored.
     *
     * Uses Unicode 9.0.0.
     *
     * @requires XRegExp, Unicode Base
     */

    if (!XRegExp.addUnicodeData) {
        throw new ReferenceError('Unicode Base must be loaded before Unicode Categories');
    }

    XRegExp.addUnicodeData([{
        name: 'C',
        alias: 'Other',
        isBmpLast: true,
        bmp: '\0-\x1F\x7F-\x9F\xAD\u0378\u0379\u0380-\u0383\u038B\u038D\u03A2\u0530\u0557\u0558\u0560\u0588\u058B\u058C\u0590\u05C8-\u05CF\u05EB-\u05EF\u05F5-\u0605\u061C\u061D\u06DD\u070E\u070F\u074B\u074C\u07B2-\u07BF\u07FB-\u07FF\u082E\u082F\u083F\u085C\u085D\u085F-\u089F\u08B5\u08BE-\u08D3\u08E2\u0984\u098D\u098E\u0991\u0992\u09A9\u09B1\u09B3-\u09B5\u09BA\u09BB\u09C5\u09C6\u09C9\u09CA\u09CF-\u09D6\u09D8-\u09DB\u09DE\u09E4\u09E5\u09FC-\u0A00\u0A04\u0A0B-\u0A0E\u0A11\u0A12\u0A29\u0A31\u0A34\u0A37\u0A3A\u0A3B\u0A3D\u0A43-\u0A46\u0A49\u0A4A\u0A4E-\u0A50\u0A52-\u0A58\u0A5D\u0A5F-\u0A65\u0A76-\u0A80\u0A84\u0A8E\u0A92\u0AA9\u0AB1\u0AB4\u0ABA\u0ABB\u0AC6\u0ACA\u0ACE\u0ACF\u0AD1-\u0ADF\u0AE4\u0AE5\u0AF2-\u0AF8\u0AFA-\u0B00\u0B04\u0B0D\u0B0E\u0B11\u0B12\u0B29\u0B31\u0B34\u0B3A\u0B3B\u0B45\u0B46\u0B49\u0B4A\u0B4E-\u0B55\u0B58-\u0B5B\u0B5E\u0B64\u0B65\u0B78-\u0B81\u0B84\u0B8B-\u0B8D\u0B91\u0B96-\u0B98\u0B9B\u0B9D\u0BA0-\u0BA2\u0BA5-\u0BA7\u0BAB-\u0BAD\u0BBA-\u0BBD\u0BC3-\u0BC5\u0BC9\u0BCE\u0BCF\u0BD1-\u0BD6\u0BD8-\u0BE5\u0BFB-\u0BFF\u0C04\u0C0D\u0C11\u0C29\u0C3A-\u0C3C\u0C45\u0C49\u0C4E-\u0C54\u0C57\u0C5B-\u0C5F\u0C64\u0C65\u0C70-\u0C77\u0C84\u0C8D\u0C91\u0CA9\u0CB4\u0CBA\u0CBB\u0CC5\u0CC9\u0CCE-\u0CD4\u0CD7-\u0CDD\u0CDF\u0CE4\u0CE5\u0CF0\u0CF3-\u0D00\u0D04\u0D0D\u0D11\u0D3B\u0D3C\u0D45\u0D49\u0D50-\u0D53\u0D64\u0D65\u0D80\u0D81\u0D84\u0D97-\u0D99\u0DB2\u0DBC\u0DBE\u0DBF\u0DC7-\u0DC9\u0DCB-\u0DCE\u0DD5\u0DD7\u0DE0-\u0DE5\u0DF0\u0DF1\u0DF5-\u0E00\u0E3B-\u0E3E\u0E5C-\u0E80\u0E83\u0E85\u0E86\u0E89\u0E8B\u0E8C\u0E8E-\u0E93\u0E98\u0EA0\u0EA4\u0EA6\u0EA8\u0EA9\u0EAC\u0EBA\u0EBE\u0EBF\u0EC5\u0EC7\u0ECE\u0ECF\u0EDA\u0EDB\u0EE0-\u0EFF\u0F48\u0F6D-\u0F70\u0F98\u0FBD\u0FCD\u0FDB-\u0FFF\u10C6\u10C8-\u10CC\u10CE\u10CF\u1249\u124E\u124F\u1257\u1259\u125E\u125F\u1289\u128E\u128F\u12B1\u12B6\u12B7\u12BF\u12C1\u12C6\u12C7\u12D7\u1311\u1316\u1317\u135B\u135C\u137D-\u137F\u139A-\u139F\u13F6\u13F7\u13FE\u13FF\u169D-\u169F\u16F9-\u16FF\u170D\u1715-\u171F\u1737-\u173F\u1754-\u175F\u176D\u1771\u1774-\u177F\u17DE\u17DF\u17EA-\u17EF\u17FA-\u17FF\u180E\u180F\u181A-\u181F\u1878-\u187F\u18AB-\u18AF\u18F6-\u18FF\u191F\u192C-\u192F\u193C-\u193F\u1941-\u1943\u196E\u196F\u1975-\u197F\u19AC-\u19AF\u19CA-\u19CF\u19DB-\u19DD\u1A1C\u1A1D\u1A5F\u1A7D\u1A7E\u1A8A-\u1A8F\u1A9A-\u1A9F\u1AAE\u1AAF\u1ABF-\u1AFF\u1B4C-\u1B4F\u1B7D-\u1B7F\u1BF4-\u1BFB\u1C38-\u1C3A\u1C4A-\u1C4C\u1C89-\u1CBF\u1CC8-\u1CCF\u1CF7\u1CFA-\u1CFF\u1DF6-\u1DFA\u1F16\u1F17\u1F1E\u1F1F\u1F46\u1F47\u1F4E\u1F4F\u1F58\u1F5A\u1F5C\u1F5E\u1F7E\u1F7F\u1FB5\u1FC5\u1FD4\u1FD5\u1FDC\u1FF0\u1FF1\u1FF5\u1FFF\u200B-\u200F\u202A-\u202E\u2060-\u206F\u2072\u2073\u208F\u209D-\u209F\u20BF-\u20CF\u20F1-\u20FF\u218C-\u218F\u23FF\u2427-\u243F\u244B-\u245F\u2B74\u2B75\u2B96\u2B97\u2BBA-\u2BBC\u2BC9\u2BD2-\u2BEB\u2BF0-\u2BFF\u2C2F\u2C5F\u2CF4-\u2CF8\u2D26\u2D28-\u2D2C\u2D2E\u2D2F\u2D68-\u2D6E\u2D71-\u2D7E\u2D97-\u2D9F\u2DA7\u2DAF\u2DB7\u2DBF\u2DC7\u2DCF\u2DD7\u2DDF\u2E45-\u2E7F\u2E9A\u2EF4-\u2EFF\u2FD6-\u2FEF\u2FFC-\u2FFF\u3040\u3097\u3098\u3100-\u3104\u312E-\u3130\u318F\u31BB-\u31BF\u31E4-\u31EF\u321F\u32FF\u4DB6-\u4DBF\u9FD6-\u9FFF\uA48D-\uA48F\uA4C7-\uA4CF\uA62C-\uA63F\uA6F8-\uA6FF\uA7AF\uA7B8-\uA7F6\uA82C-\uA82F\uA83A-\uA83F\uA878-\uA87F\uA8C6-\uA8CD\uA8DA-\uA8DF\uA8FE\uA8FF\uA954-\uA95E\uA97D-\uA97F\uA9CE\uA9DA-\uA9DD\uA9FF\uAA37-\uAA3F\uAA4E\uAA4F\uAA5A\uAA5B\uAAC3-\uAADA\uAAF7-\uAB00\uAB07\uAB08\uAB0F\uAB10\uAB17-\uAB1F\uAB27\uAB2F\uAB66-\uAB6F\uABEE\uABEF\uABFA-\uABFF\uD7A4-\uD7AF\uD7C7-\uD7CA\uD7FC-\uF8FF\uFA6E\uFA6F\uFADA-\uFAFF\uFB07-\uFB12\uFB18-\uFB1C\uFB37\uFB3D\uFB3F\uFB42\uFB45\uFBC2-\uFBD2\uFD40-\uFD4F\uFD90\uFD91\uFDC8-\uFDEF\uFDFE\uFDFF\uFE1A-\uFE1F\uFE53\uFE67\uFE6C-\uFE6F\uFE75\uFEFD-\uFF00\uFFBF-\uFFC1\uFFC8\uFFC9\uFFD0\uFFD1\uFFD8\uFFD9\uFFDD-\uFFDF\uFFE7\uFFEF-\uFFFB\uFFFE\uFFFF',
        astral: '\uD800[\uDC0C\uDC27\uDC3B\uDC3E\uDC4E\uDC4F\uDC5E-\uDC7F\uDCFB-\uDCFF\uDD03-\uDD06\uDD34-\uDD36\uDD8F\uDD9C-\uDD9F\uDDA1-\uDDCF\uDDFE-\uDE7F\uDE9D-\uDE9F\uDED1-\uDEDF\uDEFC-\uDEFF\uDF24-\uDF2F\uDF4B-\uDF4F\uDF7B-\uDF7F\uDF9E\uDFC4-\uDFC7\uDFD6-\uDFFF]|\uD801[\uDC9E\uDC9F\uDCAA-\uDCAF\uDCD4-\uDCD7\uDCFC-\uDCFF\uDD28-\uDD2F\uDD64-\uDD6E\uDD70-\uDDFF\uDF37-\uDF3F\uDF56-\uDF5F\uDF68-\uDFFF]|\uD802[\uDC06\uDC07\uDC09\uDC36\uDC39-\uDC3B\uDC3D\uDC3E\uDC56\uDC9F-\uDCA6\uDCB0-\uDCDF\uDCF3\uDCF6-\uDCFA\uDD1C-\uDD1E\uDD3A-\uDD3E\uDD40-\uDD7F\uDDB8-\uDDBB\uDDD0\uDDD1\uDE04\uDE07-\uDE0B\uDE14\uDE18\uDE34-\uDE37\uDE3B-\uDE3E\uDE48-\uDE4F\uDE59-\uDE5F\uDEA0-\uDEBF\uDEE7-\uDEEA\uDEF7-\uDEFF\uDF36-\uDF38\uDF56\uDF57\uDF73-\uDF77\uDF92-\uDF98\uDF9D-\uDFA8\uDFB0-\uDFFF]|\uD803[\uDC49-\uDC7F\uDCB3-\uDCBF\uDCF3-\uDCF9\uDD00-\uDE5F\uDE7F-\uDFFF]|\uD804[\uDC4E-\uDC51\uDC70-\uDC7E\uDCBD\uDCC2-\uDCCF\uDCE9-\uDCEF\uDCFA-\uDCFF\uDD35\uDD44-\uDD4F\uDD77-\uDD7F\uDDCE\uDDCF\uDDE0\uDDF5-\uDDFF\uDE12\uDE3F-\uDE7F\uDE87\uDE89\uDE8E\uDE9E\uDEAA-\uDEAF\uDEEB-\uDEEF\uDEFA-\uDEFF\uDF04\uDF0D\uDF0E\uDF11\uDF12\uDF29\uDF31\uDF34\uDF3A\uDF3B\uDF45\uDF46\uDF49\uDF4A\uDF4E\uDF4F\uDF51-\uDF56\uDF58-\uDF5C\uDF64\uDF65\uDF6D-\uDF6F\uDF75-\uDFFF]|\uD805[\uDC5A\uDC5C\uDC5E-\uDC7F\uDCC8-\uDCCF\uDCDA-\uDD7F\uDDB6\uDDB7\uDDDE-\uDDFF\uDE45-\uDE4F\uDE5A-\uDE5F\uDE6D-\uDE7F\uDEB8-\uDEBF\uDECA-\uDEFF\uDF1A-\uDF1C\uDF2C-\uDF2F\uDF40-\uDFFF]|\uD806[\uDC00-\uDC9F\uDCF3-\uDCFE\uDD00-\uDEBF\uDEF9-\uDFFF]|\uD807[\uDC09\uDC37\uDC46-\uDC4F\uDC6D-\uDC6F\uDC90\uDC91\uDCA8\uDCB7-\uDFFF]|\uD808[\uDF9A-\uDFFF]|\uD809[\uDC6F\uDC75-\uDC7F\uDD44-\uDFFF]|[\uD80A\uD80B\uD80E-\uD810\uD812-\uD819\uD823-\uD82B\uD82D\uD82E\uD830-\uD833\uD837\uD839\uD83F\uD874-\uD87D\uD87F-\uDB3F\uDB41-\uDBFF][\uDC00-\uDFFF]|\uD80D[\uDC2F-\uDFFF]|\uD811[\uDE47-\uDFFF]|\uD81A[\uDE39-\uDE3F\uDE5F\uDE6A-\uDE6D\uDE70-\uDECF\uDEEE\uDEEF\uDEF6-\uDEFF\uDF46-\uDF4F\uDF5A\uDF62\uDF78-\uDF7C\uDF90-\uDFFF]|\uD81B[\uDC00-\uDEFF\uDF45-\uDF4F\uDF7F-\uDF8E\uDFA0-\uDFDF\uDFE1-\uDFFF]|\uD821[\uDFED-\uDFFF]|\uD822[\uDEF3-\uDFFF]|\uD82C[\uDC02-\uDFFF]|\uD82F[\uDC6B-\uDC6F\uDC7D-\uDC7F\uDC89-\uDC8F\uDC9A\uDC9B\uDCA0-\uDFFF]|\uD834[\uDCF6-\uDCFF\uDD27\uDD28\uDD73-\uDD7A\uDDE9-\uDDFF\uDE46-\uDEFF\uDF57-\uDF5F\uDF72-\uDFFF]|\uD835[\uDC55\uDC9D\uDCA0\uDCA1\uDCA3\uDCA4\uDCA7\uDCA8\uDCAD\uDCBA\uDCBC\uDCC4\uDD06\uDD0B\uDD0C\uDD15\uDD1D\uDD3A\uDD3F\uDD45\uDD47-\uDD49\uDD51\uDEA6\uDEA7\uDFCC\uDFCD]|\uD836[\uDE8C-\uDE9A\uDEA0\uDEB0-\uDFFF]|\uD838[\uDC07\uDC19\uDC1A\uDC22\uDC25\uDC2B-\uDFFF]|\uD83A[\uDCC5\uDCC6\uDCD7-\uDCFF\uDD4B-\uDD4F\uDD5A-\uDD5D\uDD60-\uDFFF]|\uD83B[\uDC00-\uDDFF\uDE04\uDE20\uDE23\uDE25\uDE26\uDE28\uDE33\uDE38\uDE3A\uDE3C-\uDE41\uDE43-\uDE46\uDE48\uDE4A\uDE4C\uDE50\uDE53\uDE55\uDE56\uDE58\uDE5A\uDE5C\uDE5E\uDE60\uDE63\uDE65\uDE66\uDE6B\uDE73\uDE78\uDE7D\uDE7F\uDE8A\uDE9C-\uDEA0\uDEA4\uDEAA\uDEBC-\uDEEF\uDEF2-\uDFFF]|\uD83C[\uDC2C-\uDC2F\uDC94-\uDC9F\uDCAF\uDCB0\uDCC0\uDCD0\uDCF6-\uDCFF\uDD0D-\uDD0F\uDD2F\uDD6C-\uDD6F\uDDAD-\uDDE5\uDE03-\uDE0F\uDE3C-\uDE3F\uDE49-\uDE4F\uDE52-\uDEFF]|\uD83D[\uDED3-\uDEDF\uDEED-\uDEEF\uDEF7-\uDEFF\uDF74-\uDF7F\uDFD5-\uDFFF]|\uD83E[\uDC0C-\uDC0F\uDC48-\uDC4F\uDC5A-\uDC5F\uDC88-\uDC8F\uDCAE-\uDD0F\uDD1F\uDD28-\uDD2F\uDD31\uDD32\uDD3F\uDD4C-\uDD4F\uDD5F-\uDD7F\uDD92-\uDDBF\uDDC1-\uDFFF]|\uD869[\uDED7-\uDEFF]|\uD86D[\uDF35-\uDF3F]|\uD86E[\uDC1E\uDC1F]|\uD873[\uDEA2-\uDFFF]|\uD87E[\uDE1E-\uDFFF]|\uDB40[\uDC00-\uDCFF\uDDF0-\uDFFF]'
    }, {
        name: 'Cc',
        alias: 'Control',
        bmp: '\0-\x1F\x7F-\x9F'
    }, {
        name: 'Cf',
        alias: 'Format',
        bmp: '\xAD\u0600-\u0605\u061C\u06DD\u070F\u08E2\u180E\u200B-\u200F\u202A-\u202E\u2060-\u2064\u2066-\u206F\uFEFF\uFFF9-\uFFFB',
        astral: '\uD804\uDCBD|\uD82F[\uDCA0-\uDCA3]|\uD834[\uDD73-\uDD7A]|\uDB40[\uDC01\uDC20-\uDC7F]'
    }, {
        name: 'Cn',
        alias: 'Unassigned',
        bmp: '\u0378\u0379\u0380-\u0383\u038B\u038D\u03A2\u0530\u0557\u0558\u0560\u0588\u058B\u058C\u0590\u05C8-\u05CF\u05EB-\u05EF\u05F5-\u05FF\u061D\u070E\u074B\u074C\u07B2-\u07BF\u07FB-\u07FF\u082E\u082F\u083F\u085C\u085D\u085F-\u089F\u08B5\u08BE-\u08D3\u0984\u098D\u098E\u0991\u0992\u09A9\u09B1\u09B3-\u09B5\u09BA\u09BB\u09C5\u09C6\u09C9\u09CA\u09CF-\u09D6\u09D8-\u09DB\u09DE\u09E4\u09E5\u09FC-\u0A00\u0A04\u0A0B-\u0A0E\u0A11\u0A12\u0A29\u0A31\u0A34\u0A37\u0A3A\u0A3B\u0A3D\u0A43-\u0A46\u0A49\u0A4A\u0A4E-\u0A50\u0A52-\u0A58\u0A5D\u0A5F-\u0A65\u0A76-\u0A80\u0A84\u0A8E\u0A92\u0AA9\u0AB1\u0AB4\u0ABA\u0ABB\u0AC6\u0ACA\u0ACE\u0ACF\u0AD1-\u0ADF\u0AE4\u0AE5\u0AF2-\u0AF8\u0AFA-\u0B00\u0B04\u0B0D\u0B0E\u0B11\u0B12\u0B29\u0B31\u0B34\u0B3A\u0B3B\u0B45\u0B46\u0B49\u0B4A\u0B4E-\u0B55\u0B58-\u0B5B\u0B5E\u0B64\u0B65\u0B78-\u0B81\u0B84\u0B8B-\u0B8D\u0B91\u0B96-\u0B98\u0B9B\u0B9D\u0BA0-\u0BA2\u0BA5-\u0BA7\u0BAB-\u0BAD\u0BBA-\u0BBD\u0BC3-\u0BC5\u0BC9\u0BCE\u0BCF\u0BD1-\u0BD6\u0BD8-\u0BE5\u0BFB-\u0BFF\u0C04\u0C0D\u0C11\u0C29\u0C3A-\u0C3C\u0C45\u0C49\u0C4E-\u0C54\u0C57\u0C5B-\u0C5F\u0C64\u0C65\u0C70-\u0C77\u0C84\u0C8D\u0C91\u0CA9\u0CB4\u0CBA\u0CBB\u0CC5\u0CC9\u0CCE-\u0CD4\u0CD7-\u0CDD\u0CDF\u0CE4\u0CE5\u0CF0\u0CF3-\u0D00\u0D04\u0D0D\u0D11\u0D3B\u0D3C\u0D45\u0D49\u0D50-\u0D53\u0D64\u0D65\u0D80\u0D81\u0D84\u0D97-\u0D99\u0DB2\u0DBC\u0DBE\u0DBF\u0DC7-\u0DC9\u0DCB-\u0DCE\u0DD5\u0DD7\u0DE0-\u0DE5\u0DF0\u0DF1\u0DF5-\u0E00\u0E3B-\u0E3E\u0E5C-\u0E80\u0E83\u0E85\u0E86\u0E89\u0E8B\u0E8C\u0E8E-\u0E93\u0E98\u0EA0\u0EA4\u0EA6\u0EA8\u0EA9\u0EAC\u0EBA\u0EBE\u0EBF\u0EC5\u0EC7\u0ECE\u0ECF\u0EDA\u0EDB\u0EE0-\u0EFF\u0F48\u0F6D-\u0F70\u0F98\u0FBD\u0FCD\u0FDB-\u0FFF\u10C6\u10C8-\u10CC\u10CE\u10CF\u1249\u124E\u124F\u1257\u1259\u125E\u125F\u1289\u128E\u128F\u12B1\u12B6\u12B7\u12BF\u12C1\u12C6\u12C7\u12D7\u1311\u1316\u1317\u135B\u135C\u137D-\u137F\u139A-\u139F\u13F6\u13F7\u13FE\u13FF\u169D-\u169F\u16F9-\u16FF\u170D\u1715-\u171F\u1737-\u173F\u1754-\u175F\u176D\u1771\u1774-\u177F\u17DE\u17DF\u17EA-\u17EF\u17FA-\u17FF\u180F\u181A-\u181F\u1878-\u187F\u18AB-\u18AF\u18F6-\u18FF\u191F\u192C-\u192F\u193C-\u193F\u1941-\u1943\u196E\u196F\u1975-\u197F\u19AC-\u19AF\u19CA-\u19CF\u19DB-\u19DD\u1A1C\u1A1D\u1A5F\u1A7D\u1A7E\u1A8A-\u1A8F\u1A9A-\u1A9F\u1AAE\u1AAF\u1ABF-\u1AFF\u1B4C-\u1B4F\u1B7D-\u1B7F\u1BF4-\u1BFB\u1C38-\u1C3A\u1C4A-\u1C4C\u1C89-\u1CBF\u1CC8-\u1CCF\u1CF7\u1CFA-\u1CFF\u1DF6-\u1DFA\u1F16\u1F17\u1F1E\u1F1F\u1F46\u1F47\u1F4E\u1F4F\u1F58\u1F5A\u1F5C\u1F5E\u1F7E\u1F7F\u1FB5\u1FC5\u1FD4\u1FD5\u1FDC\u1FF0\u1FF1\u1FF5\u1FFF\u2065\u2072\u2073\u208F\u209D-\u209F\u20BF-\u20CF\u20F1-\u20FF\u218C-\u218F\u23FF\u2427-\u243F\u244B-\u245F\u2B74\u2B75\u2B96\u2B97\u2BBA-\u2BBC\u2BC9\u2BD2-\u2BEB\u2BF0-\u2BFF\u2C2F\u2C5F\u2CF4-\u2CF8\u2D26\u2D28-\u2D2C\u2D2E\u2D2F\u2D68-\u2D6E\u2D71-\u2D7E\u2D97-\u2D9F\u2DA7\u2DAF\u2DB7\u2DBF\u2DC7\u2DCF\u2DD7\u2DDF\u2E45-\u2E7F\u2E9A\u2EF4-\u2EFF\u2FD6-\u2FEF\u2FFC-\u2FFF\u3040\u3097\u3098\u3100-\u3104\u312E-\u3130\u318F\u31BB-\u31BF\u31E4-\u31EF\u321F\u32FF\u4DB6-\u4DBF\u9FD6-\u9FFF\uA48D-\uA48F\uA4C7-\uA4CF\uA62C-\uA63F\uA6F8-\uA6FF\uA7AF\uA7B8-\uA7F6\uA82C-\uA82F\uA83A-\uA83F\uA878-\uA87F\uA8C6-\uA8CD\uA8DA-\uA8DF\uA8FE\uA8FF\uA954-\uA95E\uA97D-\uA97F\uA9CE\uA9DA-\uA9DD\uA9FF\uAA37-\uAA3F\uAA4E\uAA4F\uAA5A\uAA5B\uAAC3-\uAADA\uAAF7-\uAB00\uAB07\uAB08\uAB0F\uAB10\uAB17-\uAB1F\uAB27\uAB2F\uAB66-\uAB6F\uABEE\uABEF\uABFA-\uABFF\uD7A4-\uD7AF\uD7C7-\uD7CA\uD7FC-\uD7FF\uFA6E\uFA6F\uFADA-\uFAFF\uFB07-\uFB12\uFB18-\uFB1C\uFB37\uFB3D\uFB3F\uFB42\uFB45\uFBC2-\uFBD2\uFD40-\uFD4F\uFD90\uFD91\uFDC8-\uFDEF\uFDFE\uFDFF\uFE1A-\uFE1F\uFE53\uFE67\uFE6C-\uFE6F\uFE75\uFEFD\uFEFE\uFF00\uFFBF-\uFFC1\uFFC8\uFFC9\uFFD0\uFFD1\uFFD8\uFFD9\uFFDD-\uFFDF\uFFE7\uFFEF-\uFFF8\uFFFE\uFFFF',
        astral: '\uD800[\uDC0C\uDC27\uDC3B\uDC3E\uDC4E\uDC4F\uDC5E-\uDC7F\uDCFB-\uDCFF\uDD03-\uDD06\uDD34-\uDD36\uDD8F\uDD9C-\uDD9F\uDDA1-\uDDCF\uDDFE-\uDE7F\uDE9D-\uDE9F\uDED1-\uDEDF\uDEFC-\uDEFF\uDF24-\uDF2F\uDF4B-\uDF4F\uDF7B-\uDF7F\uDF9E\uDFC4-\uDFC7\uDFD6-\uDFFF]|\uD801[\uDC9E\uDC9F\uDCAA-\uDCAF\uDCD4-\uDCD7\uDCFC-\uDCFF\uDD28-\uDD2F\uDD64-\uDD6E\uDD70-\uDDFF\uDF37-\uDF3F\uDF56-\uDF5F\uDF68-\uDFFF]|\uD802[\uDC06\uDC07\uDC09\uDC36\uDC39-\uDC3B\uDC3D\uDC3E\uDC56\uDC9F-\uDCA6\uDCB0-\uDCDF\uDCF3\uDCF6-\uDCFA\uDD1C-\uDD1E\uDD3A-\uDD3E\uDD40-\uDD7F\uDDB8-\uDDBB\uDDD0\uDDD1\uDE04\uDE07-\uDE0B\uDE14\uDE18\uDE34-\uDE37\uDE3B-\uDE3E\uDE48-\uDE4F\uDE59-\uDE5F\uDEA0-\uDEBF\uDEE7-\uDEEA\uDEF7-\uDEFF\uDF36-\uDF38\uDF56\uDF57\uDF73-\uDF77\uDF92-\uDF98\uDF9D-\uDFA8\uDFB0-\uDFFF]|\uD803[\uDC49-\uDC7F\uDCB3-\uDCBF\uDCF3-\uDCF9\uDD00-\uDE5F\uDE7F-\uDFFF]|\uD804[\uDC4E-\uDC51\uDC70-\uDC7E\uDCC2-\uDCCF\uDCE9-\uDCEF\uDCFA-\uDCFF\uDD35\uDD44-\uDD4F\uDD77-\uDD7F\uDDCE\uDDCF\uDDE0\uDDF5-\uDDFF\uDE12\uDE3F-\uDE7F\uDE87\uDE89\uDE8E\uDE9E\uDEAA-\uDEAF\uDEEB-\uDEEF\uDEFA-\uDEFF\uDF04\uDF0D\uDF0E\uDF11\uDF12\uDF29\uDF31\uDF34\uDF3A\uDF3B\uDF45\uDF46\uDF49\uDF4A\uDF4E\uDF4F\uDF51-\uDF56\uDF58-\uDF5C\uDF64\uDF65\uDF6D-\uDF6F\uDF75-\uDFFF]|\uD805[\uDC5A\uDC5C\uDC5E-\uDC7F\uDCC8-\uDCCF\uDCDA-\uDD7F\uDDB6\uDDB7\uDDDE-\uDDFF\uDE45-\uDE4F\uDE5A-\uDE5F\uDE6D-\uDE7F\uDEB8-\uDEBF\uDECA-\uDEFF\uDF1A-\uDF1C\uDF2C-\uDF2F\uDF40-\uDFFF]|\uD806[\uDC00-\uDC9F\uDCF3-\uDCFE\uDD00-\uDEBF\uDEF9-\uDFFF]|\uD807[\uDC09\uDC37\uDC46-\uDC4F\uDC6D-\uDC6F\uDC90\uDC91\uDCA8\uDCB7-\uDFFF]|\uD808[\uDF9A-\uDFFF]|\uD809[\uDC6F\uDC75-\uDC7F\uDD44-\uDFFF]|[\uD80A\uD80B\uD80E-\uD810\uD812-\uD819\uD823-\uD82B\uD82D\uD82E\uD830-\uD833\uD837\uD839\uD83F\uD874-\uD87D\uD87F-\uDB3F\uDB41-\uDB7F][\uDC00-\uDFFF]|\uD80D[\uDC2F-\uDFFF]|\uD811[\uDE47-\uDFFF]|\uD81A[\uDE39-\uDE3F\uDE5F\uDE6A-\uDE6D\uDE70-\uDECF\uDEEE\uDEEF\uDEF6-\uDEFF\uDF46-\uDF4F\uDF5A\uDF62\uDF78-\uDF7C\uDF90-\uDFFF]|\uD81B[\uDC00-\uDEFF\uDF45-\uDF4F\uDF7F-\uDF8E\uDFA0-\uDFDF\uDFE1-\uDFFF]|\uD821[\uDFED-\uDFFF]|\uD822[\uDEF3-\uDFFF]|\uD82C[\uDC02-\uDFFF]|\uD82F[\uDC6B-\uDC6F\uDC7D-\uDC7F\uDC89-\uDC8F\uDC9A\uDC9B\uDCA4-\uDFFF]|\uD834[\uDCF6-\uDCFF\uDD27\uDD28\uDDE9-\uDDFF\uDE46-\uDEFF\uDF57-\uDF5F\uDF72-\uDFFF]|\uD835[\uDC55\uDC9D\uDCA0\uDCA1\uDCA3\uDCA4\uDCA7\uDCA8\uDCAD\uDCBA\uDCBC\uDCC4\uDD06\uDD0B\uDD0C\uDD15\uDD1D\uDD3A\uDD3F\uDD45\uDD47-\uDD49\uDD51\uDEA6\uDEA7\uDFCC\uDFCD]|\uD836[\uDE8C-\uDE9A\uDEA0\uDEB0-\uDFFF]|\uD838[\uDC07\uDC19\uDC1A\uDC22\uDC25\uDC2B-\uDFFF]|\uD83A[\uDCC5\uDCC6\uDCD7-\uDCFF\uDD4B-\uDD4F\uDD5A-\uDD5D\uDD60-\uDFFF]|\uD83B[\uDC00-\uDDFF\uDE04\uDE20\uDE23\uDE25\uDE26\uDE28\uDE33\uDE38\uDE3A\uDE3C-\uDE41\uDE43-\uDE46\uDE48\uDE4A\uDE4C\uDE50\uDE53\uDE55\uDE56\uDE58\uDE5A\uDE5C\uDE5E\uDE60\uDE63\uDE65\uDE66\uDE6B\uDE73\uDE78\uDE7D\uDE7F\uDE8A\uDE9C-\uDEA0\uDEA4\uDEAA\uDEBC-\uDEEF\uDEF2-\uDFFF]|\uD83C[\uDC2C-\uDC2F\uDC94-\uDC9F\uDCAF\uDCB0\uDCC0\uDCD0\uDCF6-\uDCFF\uDD0D-\uDD0F\uDD2F\uDD6C-\uDD6F\uDDAD-\uDDE5\uDE03-\uDE0F\uDE3C-\uDE3F\uDE49-\uDE4F\uDE52-\uDEFF]|\uD83D[\uDED3-\uDEDF\uDEED-\uDEEF\uDEF7-\uDEFF\uDF74-\uDF7F\uDFD5-\uDFFF]|\uD83E[\uDC0C-\uDC0F\uDC48-\uDC4F\uDC5A-\uDC5F\uDC88-\uDC8F\uDCAE-\uDD0F\uDD1F\uDD28-\uDD2F\uDD31\uDD32\uDD3F\uDD4C-\uDD4F\uDD5F-\uDD7F\uDD92-\uDDBF\uDDC1-\uDFFF]|\uD869[\uDED7-\uDEFF]|\uD86D[\uDF35-\uDF3F]|\uD86E[\uDC1E\uDC1F]|\uD873[\uDEA2-\uDFFF]|\uD87E[\uDE1E-\uDFFF]|\uDB40[\uDC00\uDC02-\uDC1F\uDC80-\uDCFF\uDDF0-\uDFFF]|[\uDBBF\uDBFF][\uDFFE\uDFFF]'
    }, {
        name: 'Co',
        alias: 'Private_Use',
        bmp: '\uE000-\uF8FF',
        astral: '[\uDB80-\uDBBE\uDBC0-\uDBFE][\uDC00-\uDFFF]|[\uDBBF\uDBFF][\uDC00-\uDFFD]'
    }, {
        name: 'Cs',
        alias: 'Surrogate',
        bmp: '\uD800-\uDFFF'
    }, {
        name: 'L',
        alias: 'Letter',
        bmp: 'A-Za-z\xAA\xB5\xBA\xC0-\xD6\xD8-\xF6\xF8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u037F\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u052F\u0531-\u0556\u0559\u0561-\u0587\u05D0-\u05EA\u05F0-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u08A0-\u08B4\u08B6-\u08BD\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0980\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0AF9\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D\u0C58-\u0C5A\u0C60\u0C61\u0C80\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D54-\u0D56\u0D5F-\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F5\u13F8-\u13FD\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u16F1-\u16F8\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1877\u1880-\u1884\u1887-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191E\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19B0-\u19C9\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1C80-\u1C88\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FD5\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA69D\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA7AE\uA7B0-\uA7B7\uA7F7-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA8FD\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uA9E0-\uA9E4\uA9E6-\uA9EF\uA9FA-\uA9FE\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA7E-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uAB30-\uAB5A\uAB5C-\uAB65\uAB70-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC',
        astral: '\uD800[\uDC00-\uDC0B\uDC0D-\uDC26\uDC28-\uDC3A\uDC3C\uDC3D\uDC3F-\uDC4D\uDC50-\uDC5D\uDC80-\uDCFA\uDE80-\uDE9C\uDEA0-\uDED0\uDF00-\uDF1F\uDF30-\uDF40\uDF42-\uDF49\uDF50-\uDF75\uDF80-\uDF9D\uDFA0-\uDFC3\uDFC8-\uDFCF]|\uD801[\uDC00-\uDC9D\uDCB0-\uDCD3\uDCD8-\uDCFB\uDD00-\uDD27\uDD30-\uDD63\uDE00-\uDF36\uDF40-\uDF55\uDF60-\uDF67]|\uD802[\uDC00-\uDC05\uDC08\uDC0A-\uDC35\uDC37\uDC38\uDC3C\uDC3F-\uDC55\uDC60-\uDC76\uDC80-\uDC9E\uDCE0-\uDCF2\uDCF4\uDCF5\uDD00-\uDD15\uDD20-\uDD39\uDD80-\uDDB7\uDDBE\uDDBF\uDE00\uDE10-\uDE13\uDE15-\uDE17\uDE19-\uDE33\uDE60-\uDE7C\uDE80-\uDE9C\uDEC0-\uDEC7\uDEC9-\uDEE4\uDF00-\uDF35\uDF40-\uDF55\uDF60-\uDF72\uDF80-\uDF91]|\uD803[\uDC00-\uDC48\uDC80-\uDCB2\uDCC0-\uDCF2]|\uD804[\uDC03-\uDC37\uDC83-\uDCAF\uDCD0-\uDCE8\uDD03-\uDD26\uDD50-\uDD72\uDD76\uDD83-\uDDB2\uDDC1-\uDDC4\uDDDA\uDDDC\uDE00-\uDE11\uDE13-\uDE2B\uDE80-\uDE86\uDE88\uDE8A-\uDE8D\uDE8F-\uDE9D\uDE9F-\uDEA8\uDEB0-\uDEDE\uDF05-\uDF0C\uDF0F\uDF10\uDF13-\uDF28\uDF2A-\uDF30\uDF32\uDF33\uDF35-\uDF39\uDF3D\uDF50\uDF5D-\uDF61]|\uD805[\uDC00-\uDC34\uDC47-\uDC4A\uDC80-\uDCAF\uDCC4\uDCC5\uDCC7\uDD80-\uDDAE\uDDD8-\uDDDB\uDE00-\uDE2F\uDE44\uDE80-\uDEAA\uDF00-\uDF19]|\uD806[\uDCA0-\uDCDF\uDCFF\uDEC0-\uDEF8]|\uD807[\uDC00-\uDC08\uDC0A-\uDC2E\uDC40\uDC72-\uDC8F]|\uD808[\uDC00-\uDF99]|\uD809[\uDC80-\uDD43]|[\uD80C\uD81C-\uD820\uD840-\uD868\uD86A-\uD86C\uD86F-\uD872][\uDC00-\uDFFF]|\uD80D[\uDC00-\uDC2E]|\uD811[\uDC00-\uDE46]|\uD81A[\uDC00-\uDE38\uDE40-\uDE5E\uDED0-\uDEED\uDF00-\uDF2F\uDF40-\uDF43\uDF63-\uDF77\uDF7D-\uDF8F]|\uD81B[\uDF00-\uDF44\uDF50\uDF93-\uDF9F\uDFE0]|\uD821[\uDC00-\uDFEC]|\uD822[\uDC00-\uDEF2]|\uD82C[\uDC00\uDC01]|\uD82F[\uDC00-\uDC6A\uDC70-\uDC7C\uDC80-\uDC88\uDC90-\uDC99]|\uD835[\uDC00-\uDC54\uDC56-\uDC9C\uDC9E\uDC9F\uDCA2\uDCA5\uDCA6\uDCA9-\uDCAC\uDCAE-\uDCB9\uDCBB\uDCBD-\uDCC3\uDCC5-\uDD05\uDD07-\uDD0A\uDD0D-\uDD14\uDD16-\uDD1C\uDD1E-\uDD39\uDD3B-\uDD3E\uDD40-\uDD44\uDD46\uDD4A-\uDD50\uDD52-\uDEA5\uDEA8-\uDEC0\uDEC2-\uDEDA\uDEDC-\uDEFA\uDEFC-\uDF14\uDF16-\uDF34\uDF36-\uDF4E\uDF50-\uDF6E\uDF70-\uDF88\uDF8A-\uDFA8\uDFAA-\uDFC2\uDFC4-\uDFCB]|\uD83A[\uDC00-\uDCC4\uDD00-\uDD43]|\uD83B[\uDE00-\uDE03\uDE05-\uDE1F\uDE21\uDE22\uDE24\uDE27\uDE29-\uDE32\uDE34-\uDE37\uDE39\uDE3B\uDE42\uDE47\uDE49\uDE4B\uDE4D-\uDE4F\uDE51\uDE52\uDE54\uDE57\uDE59\uDE5B\uDE5D\uDE5F\uDE61\uDE62\uDE64\uDE67-\uDE6A\uDE6C-\uDE72\uDE74-\uDE77\uDE79-\uDE7C\uDE7E\uDE80-\uDE89\uDE8B-\uDE9B\uDEA1-\uDEA3\uDEA5-\uDEA9\uDEAB-\uDEBB]|\uD869[\uDC00-\uDED6\uDF00-\uDFFF]|\uD86D[\uDC00-\uDF34\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1D\uDC20-\uDFFF]|\uD873[\uDC00-\uDEA1]|\uD87E[\uDC00-\uDE1D]'
    }, {
        name: 'Ll',
        alias: 'Lowercase_Letter',
        bmp: 'a-z\xB5\xDF-\xF6\xF8-\xFF\u0101\u0103\u0105\u0107\u0109\u010B\u010D\u010F\u0111\u0113\u0115\u0117\u0119\u011B\u011D\u011F\u0121\u0123\u0125\u0127\u0129\u012B\u012D\u012F\u0131\u0133\u0135\u0137\u0138\u013A\u013C\u013E\u0140\u0142\u0144\u0146\u0148\u0149\u014B\u014D\u014F\u0151\u0153\u0155\u0157\u0159\u015B\u015D\u015F\u0161\u0163\u0165\u0167\u0169\u016B\u016D\u016F\u0171\u0173\u0175\u0177\u017A\u017C\u017E-\u0180\u0183\u0185\u0188\u018C\u018D\u0192\u0195\u0199-\u019B\u019E\u01A1\u01A3\u01A5\u01A8\u01AA\u01AB\u01AD\u01B0\u01B4\u01B6\u01B9\u01BA\u01BD-\u01BF\u01C6\u01C9\u01CC\u01CE\u01D0\u01D2\u01D4\u01D6\u01D8\u01DA\u01DC\u01DD\u01DF\u01E1\u01E3\u01E5\u01E7\u01E9\u01EB\u01ED\u01EF\u01F0\u01F3\u01F5\u01F9\u01FB\u01FD\u01FF\u0201\u0203\u0205\u0207\u0209\u020B\u020D\u020F\u0211\u0213\u0215\u0217\u0219\u021B\u021D\u021F\u0221\u0223\u0225\u0227\u0229\u022B\u022D\u022F\u0231\u0233-\u0239\u023C\u023F\u0240\u0242\u0247\u0249\u024B\u024D\u024F-\u0293\u0295-\u02AF\u0371\u0373\u0377\u037B-\u037D\u0390\u03AC-\u03CE\u03D0\u03D1\u03D5-\u03D7\u03D9\u03DB\u03DD\u03DF\u03E1\u03E3\u03E5\u03E7\u03E9\u03EB\u03ED\u03EF-\u03F3\u03F5\u03F8\u03FB\u03FC\u0430-\u045F\u0461\u0463\u0465\u0467\u0469\u046B\u046D\u046F\u0471\u0473\u0475\u0477\u0479\u047B\u047D\u047F\u0481\u048B\u048D\u048F\u0491\u0493\u0495\u0497\u0499\u049B\u049D\u049F\u04A1\u04A3\u04A5\u04A7\u04A9\u04AB\u04AD\u04AF\u04B1\u04B3\u04B5\u04B7\u04B9\u04BB\u04BD\u04BF\u04C2\u04C4\u04C6\u04C8\u04CA\u04CC\u04CE\u04CF\u04D1\u04D3\u04D5\u04D7\u04D9\u04DB\u04DD\u04DF\u04E1\u04E3\u04E5\u04E7\u04E9\u04EB\u04ED\u04EF\u04F1\u04F3\u04F5\u04F7\u04F9\u04FB\u04FD\u04FF\u0501\u0503\u0505\u0507\u0509\u050B\u050D\u050F\u0511\u0513\u0515\u0517\u0519\u051B\u051D\u051F\u0521\u0523\u0525\u0527\u0529\u052B\u052D\u052F\u0561-\u0587\u13F8-\u13FD\u1C80-\u1C88\u1D00-\u1D2B\u1D6B-\u1D77\u1D79-\u1D9A\u1E01\u1E03\u1E05\u1E07\u1E09\u1E0B\u1E0D\u1E0F\u1E11\u1E13\u1E15\u1E17\u1E19\u1E1B\u1E1D\u1E1F\u1E21\u1E23\u1E25\u1E27\u1E29\u1E2B\u1E2D\u1E2F\u1E31\u1E33\u1E35\u1E37\u1E39\u1E3B\u1E3D\u1E3F\u1E41\u1E43\u1E45\u1E47\u1E49\u1E4B\u1E4D\u1E4F\u1E51\u1E53\u1E55\u1E57\u1E59\u1E5B\u1E5D\u1E5F\u1E61\u1E63\u1E65\u1E67\u1E69\u1E6B\u1E6D\u1E6F\u1E71\u1E73\u1E75\u1E77\u1E79\u1E7B\u1E7D\u1E7F\u1E81\u1E83\u1E85\u1E87\u1E89\u1E8B\u1E8D\u1E8F\u1E91\u1E93\u1E95-\u1E9D\u1E9F\u1EA1\u1EA3\u1EA5\u1EA7\u1EA9\u1EAB\u1EAD\u1EAF\u1EB1\u1EB3\u1EB5\u1EB7\u1EB9\u1EBB\u1EBD\u1EBF\u1EC1\u1EC3\u1EC5\u1EC7\u1EC9\u1ECB\u1ECD\u1ECF\u1ED1\u1ED3\u1ED5\u1ED7\u1ED9\u1EDB\u1EDD\u1EDF\u1EE1\u1EE3\u1EE5\u1EE7\u1EE9\u1EEB\u1EED\u1EEF\u1EF1\u1EF3\u1EF5\u1EF7\u1EF9\u1EFB\u1EFD\u1EFF-\u1F07\u1F10-\u1F15\u1F20-\u1F27\u1F30-\u1F37\u1F40-\u1F45\u1F50-\u1F57\u1F60-\u1F67\u1F70-\u1F7D\u1F80-\u1F87\u1F90-\u1F97\u1FA0-\u1FA7\u1FB0-\u1FB4\u1FB6\u1FB7\u1FBE\u1FC2-\u1FC4\u1FC6\u1FC7\u1FD0-\u1FD3\u1FD6\u1FD7\u1FE0-\u1FE7\u1FF2-\u1FF4\u1FF6\u1FF7\u210A\u210E\u210F\u2113\u212F\u2134\u2139\u213C\u213D\u2146-\u2149\u214E\u2184\u2C30-\u2C5E\u2C61\u2C65\u2C66\u2C68\u2C6A\u2C6C\u2C71\u2C73\u2C74\u2C76-\u2C7B\u2C81\u2C83\u2C85\u2C87\u2C89\u2C8B\u2C8D\u2C8F\u2C91\u2C93\u2C95\u2C97\u2C99\u2C9B\u2C9D\u2C9F\u2CA1\u2CA3\u2CA5\u2CA7\u2CA9\u2CAB\u2CAD\u2CAF\u2CB1\u2CB3\u2CB5\u2CB7\u2CB9\u2CBB\u2CBD\u2CBF\u2CC1\u2CC3\u2CC5\u2CC7\u2CC9\u2CCB\u2CCD\u2CCF\u2CD1\u2CD3\u2CD5\u2CD7\u2CD9\u2CDB\u2CDD\u2CDF\u2CE1\u2CE3\u2CE4\u2CEC\u2CEE\u2CF3\u2D00-\u2D25\u2D27\u2D2D\uA641\uA643\uA645\uA647\uA649\uA64B\uA64D\uA64F\uA651\uA653\uA655\uA657\uA659\uA65B\uA65D\uA65F\uA661\uA663\uA665\uA667\uA669\uA66B\uA66D\uA681\uA683\uA685\uA687\uA689\uA68B\uA68D\uA68F\uA691\uA693\uA695\uA697\uA699\uA69B\uA723\uA725\uA727\uA729\uA72B\uA72D\uA72F-\uA731\uA733\uA735\uA737\uA739\uA73B\uA73D\uA73F\uA741\uA743\uA745\uA747\uA749\uA74B\uA74D\uA74F\uA751\uA753\uA755\uA757\uA759\uA75B\uA75D\uA75F\uA761\uA763\uA765\uA767\uA769\uA76B\uA76D\uA76F\uA771-\uA778\uA77A\uA77C\uA77F\uA781\uA783\uA785\uA787\uA78C\uA78E\uA791\uA793-\uA795\uA797\uA799\uA79B\uA79D\uA79F\uA7A1\uA7A3\uA7A5\uA7A7\uA7A9\uA7B5\uA7B7\uA7FA\uAB30-\uAB5A\uAB60-\uAB65\uAB70-\uABBF\uFB00-\uFB06\uFB13-\uFB17\uFF41-\uFF5A',
        astral: '\uD801[\uDC28-\uDC4F\uDCD8-\uDCFB]|\uD803[\uDCC0-\uDCF2]|\uD806[\uDCC0-\uDCDF]|\uD835[\uDC1A-\uDC33\uDC4E-\uDC54\uDC56-\uDC67\uDC82-\uDC9B\uDCB6-\uDCB9\uDCBB\uDCBD-\uDCC3\uDCC5-\uDCCF\uDCEA-\uDD03\uDD1E-\uDD37\uDD52-\uDD6B\uDD86-\uDD9F\uDDBA-\uDDD3\uDDEE-\uDE07\uDE22-\uDE3B\uDE56-\uDE6F\uDE8A-\uDEA5\uDEC2-\uDEDA\uDEDC-\uDEE1\uDEFC-\uDF14\uDF16-\uDF1B\uDF36-\uDF4E\uDF50-\uDF55\uDF70-\uDF88\uDF8A-\uDF8F\uDFAA-\uDFC2\uDFC4-\uDFC9\uDFCB]|\uD83A[\uDD22-\uDD43]'
    }, {
        name: 'Lm',
        alias: 'Modifier_Letter',
        bmp: '\u02B0-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0374\u037A\u0559\u0640\u06E5\u06E6\u07F4\u07F5\u07FA\u081A\u0824\u0828\u0971\u0E46\u0EC6\u10FC\u17D7\u1843\u1AA7\u1C78-\u1C7D\u1D2C-\u1D6A\u1D78\u1D9B-\u1DBF\u2071\u207F\u2090-\u209C\u2C7C\u2C7D\u2D6F\u2E2F\u3005\u3031-\u3035\u303B\u309D\u309E\u30FC-\u30FE\uA015\uA4F8-\uA4FD\uA60C\uA67F\uA69C\uA69D\uA717-\uA71F\uA770\uA788\uA7F8\uA7F9\uA9CF\uA9E6\uAA70\uAADD\uAAF3\uAAF4\uAB5C-\uAB5F\uFF70\uFF9E\uFF9F',
        astral: '\uD81A[\uDF40-\uDF43]|\uD81B[\uDF93-\uDF9F\uDFE0]'
    }, {
        name: 'Lo',
        alias: 'Other_Letter',
        bmp: '\xAA\xBA\u01BB\u01C0-\u01C3\u0294\u05D0-\u05EA\u05F0-\u05F2\u0620-\u063F\u0641-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u0800-\u0815\u0840-\u0858\u08A0-\u08B4\u08B6-\u08BD\u0904-\u0939\u093D\u0950\u0958-\u0961\u0972-\u0980\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0AF9\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D\u0C58-\u0C5A\u0C60\u0C61\u0C80\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D54-\u0D56\u0D5F-\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E45\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10D0-\u10FA\u10FD-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u16F1-\u16F8\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17DC\u1820-\u1842\u1844-\u1877\u1880-\u1884\u1887-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191E\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19B0-\u19C9\u1A00-\u1A16\u1A20-\u1A54\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C77\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u2135-\u2138\u2D30-\u2D67\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u3006\u303C\u3041-\u3096\u309F\u30A1-\u30FA\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FD5\uA000-\uA014\uA016-\uA48C\uA4D0-\uA4F7\uA500-\uA60B\uA610-\uA61F\uA62A\uA62B\uA66E\uA6A0-\uA6E5\uA78F\uA7F7\uA7FB-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA8FD\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9E0-\uA9E4\uA9E7-\uA9EF\uA9FA-\uA9FE\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA6F\uAA71-\uAA76\uAA7A\uAA7E-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB\uAADC\uAAE0-\uAAEA\uAAF2\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uABC0-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF66-\uFF6F\uFF71-\uFF9D\uFFA0-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC',
        astral: '\uD800[\uDC00-\uDC0B\uDC0D-\uDC26\uDC28-\uDC3A\uDC3C\uDC3D\uDC3F-\uDC4D\uDC50-\uDC5D\uDC80-\uDCFA\uDE80-\uDE9C\uDEA0-\uDED0\uDF00-\uDF1F\uDF30-\uDF40\uDF42-\uDF49\uDF50-\uDF75\uDF80-\uDF9D\uDFA0-\uDFC3\uDFC8-\uDFCF]|\uD801[\uDC50-\uDC9D\uDD00-\uDD27\uDD30-\uDD63\uDE00-\uDF36\uDF40-\uDF55\uDF60-\uDF67]|\uD802[\uDC00-\uDC05\uDC08\uDC0A-\uDC35\uDC37\uDC38\uDC3C\uDC3F-\uDC55\uDC60-\uDC76\uDC80-\uDC9E\uDCE0-\uDCF2\uDCF4\uDCF5\uDD00-\uDD15\uDD20-\uDD39\uDD80-\uDDB7\uDDBE\uDDBF\uDE00\uDE10-\uDE13\uDE15-\uDE17\uDE19-\uDE33\uDE60-\uDE7C\uDE80-\uDE9C\uDEC0-\uDEC7\uDEC9-\uDEE4\uDF00-\uDF35\uDF40-\uDF55\uDF60-\uDF72\uDF80-\uDF91]|\uD803[\uDC00-\uDC48]|\uD804[\uDC03-\uDC37\uDC83-\uDCAF\uDCD0-\uDCE8\uDD03-\uDD26\uDD50-\uDD72\uDD76\uDD83-\uDDB2\uDDC1-\uDDC4\uDDDA\uDDDC\uDE00-\uDE11\uDE13-\uDE2B\uDE80-\uDE86\uDE88\uDE8A-\uDE8D\uDE8F-\uDE9D\uDE9F-\uDEA8\uDEB0-\uDEDE\uDF05-\uDF0C\uDF0F\uDF10\uDF13-\uDF28\uDF2A-\uDF30\uDF32\uDF33\uDF35-\uDF39\uDF3D\uDF50\uDF5D-\uDF61]|\uD805[\uDC00-\uDC34\uDC47-\uDC4A\uDC80-\uDCAF\uDCC4\uDCC5\uDCC7\uDD80-\uDDAE\uDDD8-\uDDDB\uDE00-\uDE2F\uDE44\uDE80-\uDEAA\uDF00-\uDF19]|\uD806[\uDCFF\uDEC0-\uDEF8]|\uD807[\uDC00-\uDC08\uDC0A-\uDC2E\uDC40\uDC72-\uDC8F]|\uD808[\uDC00-\uDF99]|\uD809[\uDC80-\uDD43]|[\uD80C\uD81C-\uD820\uD840-\uD868\uD86A-\uD86C\uD86F-\uD872][\uDC00-\uDFFF]|\uD80D[\uDC00-\uDC2E]|\uD811[\uDC00-\uDE46]|\uD81A[\uDC00-\uDE38\uDE40-\uDE5E\uDED0-\uDEED\uDF00-\uDF2F\uDF63-\uDF77\uDF7D-\uDF8F]|\uD81B[\uDF00-\uDF44\uDF50]|\uD821[\uDC00-\uDFEC]|\uD822[\uDC00-\uDEF2]|\uD82C[\uDC00\uDC01]|\uD82F[\uDC00-\uDC6A\uDC70-\uDC7C\uDC80-\uDC88\uDC90-\uDC99]|\uD83A[\uDC00-\uDCC4]|\uD83B[\uDE00-\uDE03\uDE05-\uDE1F\uDE21\uDE22\uDE24\uDE27\uDE29-\uDE32\uDE34-\uDE37\uDE39\uDE3B\uDE42\uDE47\uDE49\uDE4B\uDE4D-\uDE4F\uDE51\uDE52\uDE54\uDE57\uDE59\uDE5B\uDE5D\uDE5F\uDE61\uDE62\uDE64\uDE67-\uDE6A\uDE6C-\uDE72\uDE74-\uDE77\uDE79-\uDE7C\uDE7E\uDE80-\uDE89\uDE8B-\uDE9B\uDEA1-\uDEA3\uDEA5-\uDEA9\uDEAB-\uDEBB]|\uD869[\uDC00-\uDED6\uDF00-\uDFFF]|\uD86D[\uDC00-\uDF34\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1D\uDC20-\uDFFF]|\uD873[\uDC00-\uDEA1]|\uD87E[\uDC00-\uDE1D]'
    }, {
        name: 'Lt',
        alias: 'Titlecase_Letter',
        bmp: '\u01C5\u01C8\u01CB\u01F2\u1F88-\u1F8F\u1F98-\u1F9F\u1FA8-\u1FAF\u1FBC\u1FCC\u1FFC'
    }, {
        name: 'Lu',
        alias: 'Uppercase_Letter',
        bmp: 'A-Z\xC0-\xD6\xD8-\xDE\u0100\u0102\u0104\u0106\u0108\u010A\u010C\u010E\u0110\u0112\u0114\u0116\u0118\u011A\u011C\u011E\u0120\u0122\u0124\u0126\u0128\u012A\u012C\u012E\u0130\u0132\u0134\u0136\u0139\u013B\u013D\u013F\u0141\u0143\u0145\u0147\u014A\u014C\u014E\u0150\u0152\u0154\u0156\u0158\u015A\u015C\u015E\u0160\u0162\u0164\u0166\u0168\u016A\u016C\u016E\u0170\u0172\u0174\u0176\u0178\u0179\u017B\u017D\u0181\u0182\u0184\u0186\u0187\u0189-\u018B\u018E-\u0191\u0193\u0194\u0196-\u0198\u019C\u019D\u019F\u01A0\u01A2\u01A4\u01A6\u01A7\u01A9\u01AC\u01AE\u01AF\u01B1-\u01B3\u01B5\u01B7\u01B8\u01BC\u01C4\u01C7\u01CA\u01CD\u01CF\u01D1\u01D3\u01D5\u01D7\u01D9\u01DB\u01DE\u01E0\u01E2\u01E4\u01E6\u01E8\u01EA\u01EC\u01EE\u01F1\u01F4\u01F6-\u01F8\u01FA\u01FC\u01FE\u0200\u0202\u0204\u0206\u0208\u020A\u020C\u020E\u0210\u0212\u0214\u0216\u0218\u021A\u021C\u021E\u0220\u0222\u0224\u0226\u0228\u022A\u022C\u022E\u0230\u0232\u023A\u023B\u023D\u023E\u0241\u0243-\u0246\u0248\u024A\u024C\u024E\u0370\u0372\u0376\u037F\u0386\u0388-\u038A\u038C\u038E\u038F\u0391-\u03A1\u03A3-\u03AB\u03CF\u03D2-\u03D4\u03D8\u03DA\u03DC\u03DE\u03E0\u03E2\u03E4\u03E6\u03E8\u03EA\u03EC\u03EE\u03F4\u03F7\u03F9\u03FA\u03FD-\u042F\u0460\u0462\u0464\u0466\u0468\u046A\u046C\u046E\u0470\u0472\u0474\u0476\u0478\u047A\u047C\u047E\u0480\u048A\u048C\u048E\u0490\u0492\u0494\u0496\u0498\u049A\u049C\u049E\u04A0\u04A2\u04A4\u04A6\u04A8\u04AA\u04AC\u04AE\u04B0\u04B2\u04B4\u04B6\u04B8\u04BA\u04BC\u04BE\u04C0\u04C1\u04C3\u04C5\u04C7\u04C9\u04CB\u04CD\u04D0\u04D2\u04D4\u04D6\u04D8\u04DA\u04DC\u04DE\u04E0\u04E2\u04E4\u04E6\u04E8\u04EA\u04EC\u04EE\u04F0\u04F2\u04F4\u04F6\u04F8\u04FA\u04FC\u04FE\u0500\u0502\u0504\u0506\u0508\u050A\u050C\u050E\u0510\u0512\u0514\u0516\u0518\u051A\u051C\u051E\u0520\u0522\u0524\u0526\u0528\u052A\u052C\u052E\u0531-\u0556\u10A0-\u10C5\u10C7\u10CD\u13A0-\u13F5\u1E00\u1E02\u1E04\u1E06\u1E08\u1E0A\u1E0C\u1E0E\u1E10\u1E12\u1E14\u1E16\u1E18\u1E1A\u1E1C\u1E1E\u1E20\u1E22\u1E24\u1E26\u1E28\u1E2A\u1E2C\u1E2E\u1E30\u1E32\u1E34\u1E36\u1E38\u1E3A\u1E3C\u1E3E\u1E40\u1E42\u1E44\u1E46\u1E48\u1E4A\u1E4C\u1E4E\u1E50\u1E52\u1E54\u1E56\u1E58\u1E5A\u1E5C\u1E5E\u1E60\u1E62\u1E64\u1E66\u1E68\u1E6A\u1E6C\u1E6E\u1E70\u1E72\u1E74\u1E76\u1E78\u1E7A\u1E7C\u1E7E\u1E80\u1E82\u1E84\u1E86\u1E88\u1E8A\u1E8C\u1E8E\u1E90\u1E92\u1E94\u1E9E\u1EA0\u1EA2\u1EA4\u1EA6\u1EA8\u1EAA\u1EAC\u1EAE\u1EB0\u1EB2\u1EB4\u1EB6\u1EB8\u1EBA\u1EBC\u1EBE\u1EC0\u1EC2\u1EC4\u1EC6\u1EC8\u1ECA\u1ECC\u1ECE\u1ED0\u1ED2\u1ED4\u1ED6\u1ED8\u1EDA\u1EDC\u1EDE\u1EE0\u1EE2\u1EE4\u1EE6\u1EE8\u1EEA\u1EEC\u1EEE\u1EF0\u1EF2\u1EF4\u1EF6\u1EF8\u1EFA\u1EFC\u1EFE\u1F08-\u1F0F\u1F18-\u1F1D\u1F28-\u1F2F\u1F38-\u1F3F\u1F48-\u1F4D\u1F59\u1F5B\u1F5D\u1F5F\u1F68-\u1F6F\u1FB8-\u1FBB\u1FC8-\u1FCB\u1FD8-\u1FDB\u1FE8-\u1FEC\u1FF8-\u1FFB\u2102\u2107\u210B-\u210D\u2110-\u2112\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u2130-\u2133\u213E\u213F\u2145\u2183\u2C00-\u2C2E\u2C60\u2C62-\u2C64\u2C67\u2C69\u2C6B\u2C6D-\u2C70\u2C72\u2C75\u2C7E-\u2C80\u2C82\u2C84\u2C86\u2C88\u2C8A\u2C8C\u2C8E\u2C90\u2C92\u2C94\u2C96\u2C98\u2C9A\u2C9C\u2C9E\u2CA0\u2CA2\u2CA4\u2CA6\u2CA8\u2CAA\u2CAC\u2CAE\u2CB0\u2CB2\u2CB4\u2CB6\u2CB8\u2CBA\u2CBC\u2CBE\u2CC0\u2CC2\u2CC4\u2CC6\u2CC8\u2CCA\u2CCC\u2CCE\u2CD0\u2CD2\u2CD4\u2CD6\u2CD8\u2CDA\u2CDC\u2CDE\u2CE0\u2CE2\u2CEB\u2CED\u2CF2\uA640\uA642\uA644\uA646\uA648\uA64A\uA64C\uA64E\uA650\uA652\uA654\uA656\uA658\uA65A\uA65C\uA65E\uA660\uA662\uA664\uA666\uA668\uA66A\uA66C\uA680\uA682\uA684\uA686\uA688\uA68A\uA68C\uA68E\uA690\uA692\uA694\uA696\uA698\uA69A\uA722\uA724\uA726\uA728\uA72A\uA72C\uA72E\uA732\uA734\uA736\uA738\uA73A\uA73C\uA73E\uA740\uA742\uA744\uA746\uA748\uA74A\uA74C\uA74E\uA750\uA752\uA754\uA756\uA758\uA75A\uA75C\uA75E\uA760\uA762\uA764\uA766\uA768\uA76A\uA76C\uA76E\uA779\uA77B\uA77D\uA77E\uA780\uA782\uA784\uA786\uA78B\uA78D\uA790\uA792\uA796\uA798\uA79A\uA79C\uA79E\uA7A0\uA7A2\uA7A4\uA7A6\uA7A8\uA7AA-\uA7AE\uA7B0-\uA7B4\uA7B6\uFF21-\uFF3A',
        astral: '\uD801[\uDC00-\uDC27\uDCB0-\uDCD3]|\uD803[\uDC80-\uDCB2]|\uD806[\uDCA0-\uDCBF]|\uD835[\uDC00-\uDC19\uDC34-\uDC4D\uDC68-\uDC81\uDC9C\uDC9E\uDC9F\uDCA2\uDCA5\uDCA6\uDCA9-\uDCAC\uDCAE-\uDCB5\uDCD0-\uDCE9\uDD04\uDD05\uDD07-\uDD0A\uDD0D-\uDD14\uDD16-\uDD1C\uDD38\uDD39\uDD3B-\uDD3E\uDD40-\uDD44\uDD46\uDD4A-\uDD50\uDD6C-\uDD85\uDDA0-\uDDB9\uDDD4-\uDDED\uDE08-\uDE21\uDE3C-\uDE55\uDE70-\uDE89\uDEA8-\uDEC0\uDEE2-\uDEFA\uDF1C-\uDF34\uDF56-\uDF6E\uDF90-\uDFA8\uDFCA]|\uD83A[\uDD00-\uDD21]'
    }, {
        name: 'M',
        alias: 'Mark',
        bmp: '\u0300-\u036F\u0483-\u0489\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06DC\u06DF-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u0816-\u0819\u081B-\u0823\u0825-\u0827\u0829-\u082D\u0859-\u085B\u08D4-\u08E1\u08E3-\u0903\u093A-\u093C\u093E-\u094F\u0951-\u0957\u0962\u0963\u0981-\u0983\u09BC\u09BE-\u09C4\u09C7\u09C8\u09CB-\u09CD\u09D7\u09E2\u09E3\u0A01-\u0A03\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81-\u0A83\u0ABC\u0ABE-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AE2\u0AE3\u0B01-\u0B03\u0B3C\u0B3E-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B62\u0B63\u0B82\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD7\u0C00-\u0C03\u0C3E-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C81-\u0C83\u0CBC\u0CBE-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CE2\u0CE3\u0D01-\u0D03\u0D3E-\u0D44\u0D46-\u0D48\u0D4A-\u0D4D\u0D57\u0D62\u0D63\u0D82\u0D83\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F3E\u0F3F\u0F71-\u0F84\u0F86\u0F87\u0F8D-\u0F97\u0F99-\u0FBC\u0FC6\u102B-\u103E\u1056-\u1059\u105E-\u1060\u1062-\u1064\u1067-\u106D\u1071-\u1074\u1082-\u108D\u108F\u109A-\u109D\u135D-\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B4-\u17D3\u17DD\u180B-\u180D\u1885\u1886\u18A9\u1920-\u192B\u1930-\u193B\u1A17-\u1A1B\u1A55-\u1A5E\u1A60-\u1A7C\u1A7F\u1AB0-\u1ABE\u1B00-\u1B04\u1B34-\u1B44\u1B6B-\u1B73\u1B80-\u1B82\u1BA1-\u1BAD\u1BE6-\u1BF3\u1C24-\u1C37\u1CD0-\u1CD2\u1CD4-\u1CE8\u1CED\u1CF2-\u1CF4\u1CF8\u1CF9\u1DC0-\u1DF5\u1DFB-\u1DFF\u20D0-\u20F0\u2CEF-\u2CF1\u2D7F\u2DE0-\u2DFF\u302A-\u302F\u3099\u309A\uA66F-\uA672\uA674-\uA67D\uA69E\uA69F\uA6F0\uA6F1\uA802\uA806\uA80B\uA823-\uA827\uA880\uA881\uA8B4-\uA8C5\uA8E0-\uA8F1\uA926-\uA92D\uA947-\uA953\uA980-\uA983\uA9B3-\uA9C0\uA9E5\uAA29-\uAA36\uAA43\uAA4C\uAA4D\uAA7B-\uAA7D\uAAB0\uAAB2-\uAAB4\uAAB7\uAAB8\uAABE\uAABF\uAAC1\uAAEB-\uAAEF\uAAF5\uAAF6\uABE3-\uABEA\uABEC\uABED\uFB1E\uFE00-\uFE0F\uFE20-\uFE2F',
        astral: '\uD800[\uDDFD\uDEE0\uDF76-\uDF7A]|\uD802[\uDE01-\uDE03\uDE05\uDE06\uDE0C-\uDE0F\uDE38-\uDE3A\uDE3F\uDEE5\uDEE6]|\uD804[\uDC00-\uDC02\uDC38-\uDC46\uDC7F-\uDC82\uDCB0-\uDCBA\uDD00-\uDD02\uDD27-\uDD34\uDD73\uDD80-\uDD82\uDDB3-\uDDC0\uDDCA-\uDDCC\uDE2C-\uDE37\uDE3E\uDEDF-\uDEEA\uDF00-\uDF03\uDF3C\uDF3E-\uDF44\uDF47\uDF48\uDF4B-\uDF4D\uDF57\uDF62\uDF63\uDF66-\uDF6C\uDF70-\uDF74]|\uD805[\uDC35-\uDC46\uDCB0-\uDCC3\uDDAF-\uDDB5\uDDB8-\uDDC0\uDDDC\uDDDD\uDE30-\uDE40\uDEAB-\uDEB7\uDF1D-\uDF2B]|\uD807[\uDC2F-\uDC36\uDC38-\uDC3F\uDC92-\uDCA7\uDCA9-\uDCB6]|\uD81A[\uDEF0-\uDEF4\uDF30-\uDF36]|\uD81B[\uDF51-\uDF7E\uDF8F-\uDF92]|\uD82F[\uDC9D\uDC9E]|\uD834[\uDD65-\uDD69\uDD6D-\uDD72\uDD7B-\uDD82\uDD85-\uDD8B\uDDAA-\uDDAD\uDE42-\uDE44]|\uD836[\uDE00-\uDE36\uDE3B-\uDE6C\uDE75\uDE84\uDE9B-\uDE9F\uDEA1-\uDEAF]|\uD838[\uDC00-\uDC06\uDC08-\uDC18\uDC1B-\uDC21\uDC23\uDC24\uDC26-\uDC2A]|\uD83A[\uDCD0-\uDCD6\uDD44-\uDD4A]|\uDB40[\uDD00-\uDDEF]'
    }, {
        name: 'Mc',
        alias: 'Spacing_Mark',
        bmp: '\u0903\u093B\u093E-\u0940\u0949-\u094C\u094E\u094F\u0982\u0983\u09BE-\u09C0\u09C7\u09C8\u09CB\u09CC\u09D7\u0A03\u0A3E-\u0A40\u0A83\u0ABE-\u0AC0\u0AC9\u0ACB\u0ACC\u0B02\u0B03\u0B3E\u0B40\u0B47\u0B48\u0B4B\u0B4C\u0B57\u0BBE\u0BBF\u0BC1\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCC\u0BD7\u0C01-\u0C03\u0C41-\u0C44\u0C82\u0C83\u0CBE\u0CC0-\u0CC4\u0CC7\u0CC8\u0CCA\u0CCB\u0CD5\u0CD6\u0D02\u0D03\u0D3E-\u0D40\u0D46-\u0D48\u0D4A-\u0D4C\u0D57\u0D82\u0D83\u0DCF-\u0DD1\u0DD8-\u0DDF\u0DF2\u0DF3\u0F3E\u0F3F\u0F7F\u102B\u102C\u1031\u1038\u103B\u103C\u1056\u1057\u1062-\u1064\u1067-\u106D\u1083\u1084\u1087-\u108C\u108F\u109A-\u109C\u17B6\u17BE-\u17C5\u17C7\u17C8\u1923-\u1926\u1929-\u192B\u1930\u1931\u1933-\u1938\u1A19\u1A1A\u1A55\u1A57\u1A61\u1A63\u1A64\u1A6D-\u1A72\u1B04\u1B35\u1B3B\u1B3D-\u1B41\u1B43\u1B44\u1B82\u1BA1\u1BA6\u1BA7\u1BAA\u1BE7\u1BEA-\u1BEC\u1BEE\u1BF2\u1BF3\u1C24-\u1C2B\u1C34\u1C35\u1CE1\u1CF2\u1CF3\u302E\u302F\uA823\uA824\uA827\uA880\uA881\uA8B4-\uA8C3\uA952\uA953\uA983\uA9B4\uA9B5\uA9BA\uA9BB\uA9BD-\uA9C0\uAA2F\uAA30\uAA33\uAA34\uAA4D\uAA7B\uAA7D\uAAEB\uAAEE\uAAEF\uAAF5\uABE3\uABE4\uABE6\uABE7\uABE9\uABEA\uABEC',
        astral: '\uD804[\uDC00\uDC02\uDC82\uDCB0-\uDCB2\uDCB7\uDCB8\uDD2C\uDD82\uDDB3-\uDDB5\uDDBF\uDDC0\uDE2C-\uDE2E\uDE32\uDE33\uDE35\uDEE0-\uDEE2\uDF02\uDF03\uDF3E\uDF3F\uDF41-\uDF44\uDF47\uDF48\uDF4B-\uDF4D\uDF57\uDF62\uDF63]|\uD805[\uDC35-\uDC37\uDC40\uDC41\uDC45\uDCB0-\uDCB2\uDCB9\uDCBB-\uDCBE\uDCC1\uDDAF-\uDDB1\uDDB8-\uDDBB\uDDBE\uDE30-\uDE32\uDE3B\uDE3C\uDE3E\uDEAC\uDEAE\uDEAF\uDEB6\uDF20\uDF21\uDF26]|\uD807[\uDC2F\uDC3E\uDCA9\uDCB1\uDCB4]|\uD81B[\uDF51-\uDF7E]|\uD834[\uDD65\uDD66\uDD6D-\uDD72]'
    }, {
        name: 'Me',
        alias: 'Enclosing_Mark',
        bmp: '\u0488\u0489\u1ABE\u20DD-\u20E0\u20E2-\u20E4\uA670-\uA672'
    }, {
        name: 'Mn',
        alias: 'Nonspacing_Mark',
        bmp: '\u0300-\u036F\u0483-\u0487\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06DC\u06DF-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u0816-\u0819\u081B-\u0823\u0825-\u0827\u0829-\u082D\u0859-\u085B\u08D4-\u08E1\u08E3-\u0902\u093A\u093C\u0941-\u0948\u094D\u0951-\u0957\u0962\u0963\u0981\u09BC\u09C1-\u09C4\u09CD\u09E2\u09E3\u0A01\u0A02\u0A3C\u0A41\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81\u0A82\u0ABC\u0AC1-\u0AC5\u0AC7\u0AC8\u0ACD\u0AE2\u0AE3\u0B01\u0B3C\u0B3F\u0B41-\u0B44\u0B4D\u0B56\u0B62\u0B63\u0B82\u0BC0\u0BCD\u0C00\u0C3E-\u0C40\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C81\u0CBC\u0CBF\u0CC6\u0CCC\u0CCD\u0CE2\u0CE3\u0D01\u0D41-\u0D44\u0D4D\u0D62\u0D63\u0DCA\u0DD2-\u0DD4\u0DD6\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F71-\u0F7E\u0F80-\u0F84\u0F86\u0F87\u0F8D-\u0F97\u0F99-\u0FBC\u0FC6\u102D-\u1030\u1032-\u1037\u1039\u103A\u103D\u103E\u1058\u1059\u105E-\u1060\u1071-\u1074\u1082\u1085\u1086\u108D\u109D\u135D-\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B4\u17B5\u17B7-\u17BD\u17C6\u17C9-\u17D3\u17DD\u180B-\u180D\u1885\u1886\u18A9\u1920-\u1922\u1927\u1928\u1932\u1939-\u193B\u1A17\u1A18\u1A1B\u1A56\u1A58-\u1A5E\u1A60\u1A62\u1A65-\u1A6C\u1A73-\u1A7C\u1A7F\u1AB0-\u1ABD\u1B00-\u1B03\u1B34\u1B36-\u1B3A\u1B3C\u1B42\u1B6B-\u1B73\u1B80\u1B81\u1BA2-\u1BA5\u1BA8\u1BA9\u1BAB-\u1BAD\u1BE6\u1BE8\u1BE9\u1BED\u1BEF-\u1BF1\u1C2C-\u1C33\u1C36\u1C37\u1CD0-\u1CD2\u1CD4-\u1CE0\u1CE2-\u1CE8\u1CED\u1CF4\u1CF8\u1CF9\u1DC0-\u1DF5\u1DFB-\u1DFF\u20D0-\u20DC\u20E1\u20E5-\u20F0\u2CEF-\u2CF1\u2D7F\u2DE0-\u2DFF\u302A-\u302D\u3099\u309A\uA66F\uA674-\uA67D\uA69E\uA69F\uA6F0\uA6F1\uA802\uA806\uA80B\uA825\uA826\uA8C4\uA8C5\uA8E0-\uA8F1\uA926-\uA92D\uA947-\uA951\uA980-\uA982\uA9B3\uA9B6-\uA9B9\uA9BC\uA9E5\uAA29-\uAA2E\uAA31\uAA32\uAA35\uAA36\uAA43\uAA4C\uAA7C\uAAB0\uAAB2-\uAAB4\uAAB7\uAAB8\uAABE\uAABF\uAAC1\uAAEC\uAAED\uAAF6\uABE5\uABE8\uABED\uFB1E\uFE00-\uFE0F\uFE20-\uFE2F',
        astral: '\uD800[\uDDFD\uDEE0\uDF76-\uDF7A]|\uD802[\uDE01-\uDE03\uDE05\uDE06\uDE0C-\uDE0F\uDE38-\uDE3A\uDE3F\uDEE5\uDEE6]|\uD804[\uDC01\uDC38-\uDC46\uDC7F-\uDC81\uDCB3-\uDCB6\uDCB9\uDCBA\uDD00-\uDD02\uDD27-\uDD2B\uDD2D-\uDD34\uDD73\uDD80\uDD81\uDDB6-\uDDBE\uDDCA-\uDDCC\uDE2F-\uDE31\uDE34\uDE36\uDE37\uDE3E\uDEDF\uDEE3-\uDEEA\uDF00\uDF01\uDF3C\uDF40\uDF66-\uDF6C\uDF70-\uDF74]|\uD805[\uDC38-\uDC3F\uDC42-\uDC44\uDC46\uDCB3-\uDCB8\uDCBA\uDCBF\uDCC0\uDCC2\uDCC3\uDDB2-\uDDB5\uDDBC\uDDBD\uDDBF\uDDC0\uDDDC\uDDDD\uDE33-\uDE3A\uDE3D\uDE3F\uDE40\uDEAB\uDEAD\uDEB0-\uDEB5\uDEB7\uDF1D-\uDF1F\uDF22-\uDF25\uDF27-\uDF2B]|\uD807[\uDC30-\uDC36\uDC38-\uDC3D\uDC3F\uDC92-\uDCA7\uDCAA-\uDCB0\uDCB2\uDCB3\uDCB5\uDCB6]|\uD81A[\uDEF0-\uDEF4\uDF30-\uDF36]|\uD81B[\uDF8F-\uDF92]|\uD82F[\uDC9D\uDC9E]|\uD834[\uDD67-\uDD69\uDD7B-\uDD82\uDD85-\uDD8B\uDDAA-\uDDAD\uDE42-\uDE44]|\uD836[\uDE00-\uDE36\uDE3B-\uDE6C\uDE75\uDE84\uDE9B-\uDE9F\uDEA1-\uDEAF]|\uD838[\uDC00-\uDC06\uDC08-\uDC18\uDC1B-\uDC21\uDC23\uDC24\uDC26-\uDC2A]|\uD83A[\uDCD0-\uDCD6\uDD44-\uDD4A]|\uDB40[\uDD00-\uDDEF]'
    }, {
        name: 'N',
        alias: 'Number',
        bmp: '0-9\xB2\xB3\xB9\xBC-\xBE\u0660-\u0669\u06F0-\u06F9\u07C0-\u07C9\u0966-\u096F\u09E6-\u09EF\u09F4-\u09F9\u0A66-\u0A6F\u0AE6-\u0AEF\u0B66-\u0B6F\u0B72-\u0B77\u0BE6-\u0BF2\u0C66-\u0C6F\u0C78-\u0C7E\u0CE6-\u0CEF\u0D58-\u0D5E\u0D66-\u0D78\u0DE6-\u0DEF\u0E50-\u0E59\u0ED0-\u0ED9\u0F20-\u0F33\u1040-\u1049\u1090-\u1099\u1369-\u137C\u16EE-\u16F0\u17E0-\u17E9\u17F0-\u17F9\u1810-\u1819\u1946-\u194F\u19D0-\u19DA\u1A80-\u1A89\u1A90-\u1A99\u1B50-\u1B59\u1BB0-\u1BB9\u1C40-\u1C49\u1C50-\u1C59\u2070\u2074-\u2079\u2080-\u2089\u2150-\u2182\u2185-\u2189\u2460-\u249B\u24EA-\u24FF\u2776-\u2793\u2CFD\u3007\u3021-\u3029\u3038-\u303A\u3192-\u3195\u3220-\u3229\u3248-\u324F\u3251-\u325F\u3280-\u3289\u32B1-\u32BF\uA620-\uA629\uA6E6-\uA6EF\uA830-\uA835\uA8D0-\uA8D9\uA900-\uA909\uA9D0-\uA9D9\uA9F0-\uA9F9\uAA50-\uAA59\uABF0-\uABF9\uFF10-\uFF19',
        astral: '\uD800[\uDD07-\uDD33\uDD40-\uDD78\uDD8A\uDD8B\uDEE1-\uDEFB\uDF20-\uDF23\uDF41\uDF4A\uDFD1-\uDFD5]|\uD801[\uDCA0-\uDCA9]|\uD802[\uDC58-\uDC5F\uDC79-\uDC7F\uDCA7-\uDCAF\uDCFB-\uDCFF\uDD16-\uDD1B\uDDBC\uDDBD\uDDC0-\uDDCF\uDDD2-\uDDFF\uDE40-\uDE47\uDE7D\uDE7E\uDE9D-\uDE9F\uDEEB-\uDEEF\uDF58-\uDF5F\uDF78-\uDF7F\uDFA9-\uDFAF]|\uD803[\uDCFA-\uDCFF\uDE60-\uDE7E]|\uD804[\uDC52-\uDC6F\uDCF0-\uDCF9\uDD36-\uDD3F\uDDD0-\uDDD9\uDDE1-\uDDF4\uDEF0-\uDEF9]|\uD805[\uDC50-\uDC59\uDCD0-\uDCD9\uDE50-\uDE59\uDEC0-\uDEC9\uDF30-\uDF3B]|\uD806[\uDCE0-\uDCF2]|\uD807[\uDC50-\uDC6C]|\uD809[\uDC00-\uDC6E]|\uD81A[\uDE60-\uDE69\uDF50-\uDF59\uDF5B-\uDF61]|\uD834[\uDF60-\uDF71]|\uD835[\uDFCE-\uDFFF]|\uD83A[\uDCC7-\uDCCF\uDD50-\uDD59]|\uD83C[\uDD00-\uDD0C]'
    }, {
        name: 'Nd',
        alias: 'Decimal_Number',
        bmp: '0-9\u0660-\u0669\u06F0-\u06F9\u07C0-\u07C9\u0966-\u096F\u09E6-\u09EF\u0A66-\u0A6F\u0AE6-\u0AEF\u0B66-\u0B6F\u0BE6-\u0BEF\u0C66-\u0C6F\u0CE6-\u0CEF\u0D66-\u0D6F\u0DE6-\u0DEF\u0E50-\u0E59\u0ED0-\u0ED9\u0F20-\u0F29\u1040-\u1049\u1090-\u1099\u17E0-\u17E9\u1810-\u1819\u1946-\u194F\u19D0-\u19D9\u1A80-\u1A89\u1A90-\u1A99\u1B50-\u1B59\u1BB0-\u1BB9\u1C40-\u1C49\u1C50-\u1C59\uA620-\uA629\uA8D0-\uA8D9\uA900-\uA909\uA9D0-\uA9D9\uA9F0-\uA9F9\uAA50-\uAA59\uABF0-\uABF9\uFF10-\uFF19',
        astral: '\uD801[\uDCA0-\uDCA9]|\uD804[\uDC66-\uDC6F\uDCF0-\uDCF9\uDD36-\uDD3F\uDDD0-\uDDD9\uDEF0-\uDEF9]|\uD805[\uDC50-\uDC59\uDCD0-\uDCD9\uDE50-\uDE59\uDEC0-\uDEC9\uDF30-\uDF39]|\uD806[\uDCE0-\uDCE9]|\uD807[\uDC50-\uDC59]|\uD81A[\uDE60-\uDE69\uDF50-\uDF59]|\uD835[\uDFCE-\uDFFF]|\uD83A[\uDD50-\uDD59]'
    }, {
        name: 'Nl',
        alias: 'Letter_Number',
        bmp: '\u16EE-\u16F0\u2160-\u2182\u2185-\u2188\u3007\u3021-\u3029\u3038-\u303A\uA6E6-\uA6EF',
        astral: '\uD800[\uDD40-\uDD74\uDF41\uDF4A\uDFD1-\uDFD5]|\uD809[\uDC00-\uDC6E]'
    }, {
        name: 'No',
        alias: 'Other_Number',
        bmp: '\xB2\xB3\xB9\xBC-\xBE\u09F4-\u09F9\u0B72-\u0B77\u0BF0-\u0BF2\u0C78-\u0C7E\u0D58-\u0D5E\u0D70-\u0D78\u0F2A-\u0F33\u1369-\u137C\u17F0-\u17F9\u19DA\u2070\u2074-\u2079\u2080-\u2089\u2150-\u215F\u2189\u2460-\u249B\u24EA-\u24FF\u2776-\u2793\u2CFD\u3192-\u3195\u3220-\u3229\u3248-\u324F\u3251-\u325F\u3280-\u3289\u32B1-\u32BF\uA830-\uA835',
        astral: '\uD800[\uDD07-\uDD33\uDD75-\uDD78\uDD8A\uDD8B\uDEE1-\uDEFB\uDF20-\uDF23]|\uD802[\uDC58-\uDC5F\uDC79-\uDC7F\uDCA7-\uDCAF\uDCFB-\uDCFF\uDD16-\uDD1B\uDDBC\uDDBD\uDDC0-\uDDCF\uDDD2-\uDDFF\uDE40-\uDE47\uDE7D\uDE7E\uDE9D-\uDE9F\uDEEB-\uDEEF\uDF58-\uDF5F\uDF78-\uDF7F\uDFA9-\uDFAF]|\uD803[\uDCFA-\uDCFF\uDE60-\uDE7E]|\uD804[\uDC52-\uDC65\uDDE1-\uDDF4]|\uD805[\uDF3A\uDF3B]|\uD806[\uDCEA-\uDCF2]|\uD807[\uDC5A-\uDC6C]|\uD81A[\uDF5B-\uDF61]|\uD834[\uDF60-\uDF71]|\uD83A[\uDCC7-\uDCCF]|\uD83C[\uDD00-\uDD0C]'
    }, {
        name: 'P',
        alias: 'Punctuation',
        bmp: '\x21-\x23\x25-\\x2A\x2C-\x2F\x3A\x3B\\x3F\x40\\x5B-\\x5D\x5F\\x7B\x7D\xA1\xA7\xAB\xB6\xB7\xBB\xBF\u037E\u0387\u055A-\u055F\u0589\u058A\u05BE\u05C0\u05C3\u05C6\u05F3\u05F4\u0609\u060A\u060C\u060D\u061B\u061E\u061F\u066A-\u066D\u06D4\u0700-\u070D\u07F7-\u07F9\u0830-\u083E\u085E\u0964\u0965\u0970\u0AF0\u0DF4\u0E4F\u0E5A\u0E5B\u0F04-\u0F12\u0F14\u0F3A-\u0F3D\u0F85\u0FD0-\u0FD4\u0FD9\u0FDA\u104A-\u104F\u10FB\u1360-\u1368\u1400\u166D\u166E\u169B\u169C\u16EB-\u16ED\u1735\u1736\u17D4-\u17D6\u17D8-\u17DA\u1800-\u180A\u1944\u1945\u1A1E\u1A1F\u1AA0-\u1AA6\u1AA8-\u1AAD\u1B5A-\u1B60\u1BFC-\u1BFF\u1C3B-\u1C3F\u1C7E\u1C7F\u1CC0-\u1CC7\u1CD3\u2010-\u2027\u2030-\u2043\u2045-\u2051\u2053-\u205E\u207D\u207E\u208D\u208E\u2308-\u230B\u2329\u232A\u2768-\u2775\u27C5\u27C6\u27E6-\u27EF\u2983-\u2998\u29D8-\u29DB\u29FC\u29FD\u2CF9-\u2CFC\u2CFE\u2CFF\u2D70\u2E00-\u2E2E\u2E30-\u2E44\u3001-\u3003\u3008-\u3011\u3014-\u301F\u3030\u303D\u30A0\u30FB\uA4FE\uA4FF\uA60D-\uA60F\uA673\uA67E\uA6F2-\uA6F7\uA874-\uA877\uA8CE\uA8CF\uA8F8-\uA8FA\uA8FC\uA92E\uA92F\uA95F\uA9C1-\uA9CD\uA9DE\uA9DF\uAA5C-\uAA5F\uAADE\uAADF\uAAF0\uAAF1\uABEB\uFD3E\uFD3F\uFE10-\uFE19\uFE30-\uFE52\uFE54-\uFE61\uFE63\uFE68\uFE6A\uFE6B\uFF01-\uFF03\uFF05-\uFF0A\uFF0C-\uFF0F\uFF1A\uFF1B\uFF1F\uFF20\uFF3B-\uFF3D\uFF3F\uFF5B\uFF5D\uFF5F-\uFF65',
        astral: '\uD800[\uDD00-\uDD02\uDF9F\uDFD0]|\uD801\uDD6F|\uD802[\uDC57\uDD1F\uDD3F\uDE50-\uDE58\uDE7F\uDEF0-\uDEF6\uDF39-\uDF3F\uDF99-\uDF9C]|\uD804[\uDC47-\uDC4D\uDCBB\uDCBC\uDCBE-\uDCC1\uDD40-\uDD43\uDD74\uDD75\uDDC5-\uDDC9\uDDCD\uDDDB\uDDDD-\uDDDF\uDE38-\uDE3D\uDEA9]|\uD805[\uDC4B-\uDC4F\uDC5B\uDC5D\uDCC6\uDDC1-\uDDD7\uDE41-\uDE43\uDE60-\uDE6C\uDF3C-\uDF3E]|\uD807[\uDC41-\uDC45\uDC70\uDC71]|\uD809[\uDC70-\uDC74]|\uD81A[\uDE6E\uDE6F\uDEF5\uDF37-\uDF3B\uDF44]|\uD82F\uDC9F|\uD836[\uDE87-\uDE8B]|\uD83A[\uDD5E\uDD5F]'
    }, {
        name: 'Pc',
        alias: 'Connector_Punctuation',
        bmp: '\x5F\u203F\u2040\u2054\uFE33\uFE34\uFE4D-\uFE4F\uFF3F'
    }, {
        name: 'Pd',
        alias: 'Dash_Punctuation',
        bmp: '\\x2D\u058A\u05BE\u1400\u1806\u2010-\u2015\u2E17\u2E1A\u2E3A\u2E3B\u2E40\u301C\u3030\u30A0\uFE31\uFE32\uFE58\uFE63\uFF0D'
    }, {
        name: 'Pe',
        alias: 'Close_Punctuation',
        bmp: '\\x29\\x5D\x7D\u0F3B\u0F3D\u169C\u2046\u207E\u208E\u2309\u230B\u232A\u2769\u276B\u276D\u276F\u2771\u2773\u2775\u27C6\u27E7\u27E9\u27EB\u27ED\u27EF\u2984\u2986\u2988\u298A\u298C\u298E\u2990\u2992\u2994\u2996\u2998\u29D9\u29DB\u29FD\u2E23\u2E25\u2E27\u2E29\u3009\u300B\u300D\u300F\u3011\u3015\u3017\u3019\u301B\u301E\u301F\uFD3E\uFE18\uFE36\uFE38\uFE3A\uFE3C\uFE3E\uFE40\uFE42\uFE44\uFE48\uFE5A\uFE5C\uFE5E\uFF09\uFF3D\uFF5D\uFF60\uFF63'
    }, {
        name: 'Pf',
        alias: 'Final_Punctuation',
        bmp: '\xBB\u2019\u201D\u203A\u2E03\u2E05\u2E0A\u2E0D\u2E1D\u2E21'
    }, {
        name: 'Pi',
        alias: 'Initial_Punctuation',
        bmp: '\xAB\u2018\u201B\u201C\u201F\u2039\u2E02\u2E04\u2E09\u2E0C\u2E1C\u2E20'
    }, {
        name: 'Po',
        alias: 'Other_Punctuation',
        bmp: '\x21-\x23\x25-\x27\\x2A\x2C\\x2E\x2F\x3A\x3B\\x3F\x40\\x5C\xA1\xA7\xB6\xB7\xBF\u037E\u0387\u055A-\u055F\u0589\u05C0\u05C3\u05C6\u05F3\u05F4\u0609\u060A\u060C\u060D\u061B\u061E\u061F\u066A-\u066D\u06D4\u0700-\u070D\u07F7-\u07F9\u0830-\u083E\u085E\u0964\u0965\u0970\u0AF0\u0DF4\u0E4F\u0E5A\u0E5B\u0F04-\u0F12\u0F14\u0F85\u0FD0-\u0FD4\u0FD9\u0FDA\u104A-\u104F\u10FB\u1360-\u1368\u166D\u166E\u16EB-\u16ED\u1735\u1736\u17D4-\u17D6\u17D8-\u17DA\u1800-\u1805\u1807-\u180A\u1944\u1945\u1A1E\u1A1F\u1AA0-\u1AA6\u1AA8-\u1AAD\u1B5A-\u1B60\u1BFC-\u1BFF\u1C3B-\u1C3F\u1C7E\u1C7F\u1CC0-\u1CC7\u1CD3\u2016\u2017\u2020-\u2027\u2030-\u2038\u203B-\u203E\u2041-\u2043\u2047-\u2051\u2053\u2055-\u205E\u2CF9-\u2CFC\u2CFE\u2CFF\u2D70\u2E00\u2E01\u2E06-\u2E08\u2E0B\u2E0E-\u2E16\u2E18\u2E19\u2E1B\u2E1E\u2E1F\u2E2A-\u2E2E\u2E30-\u2E39\u2E3C-\u2E3F\u2E41\u2E43\u2E44\u3001-\u3003\u303D\u30FB\uA4FE\uA4FF\uA60D-\uA60F\uA673\uA67E\uA6F2-\uA6F7\uA874-\uA877\uA8CE\uA8CF\uA8F8-\uA8FA\uA8FC\uA92E\uA92F\uA95F\uA9C1-\uA9CD\uA9DE\uA9DF\uAA5C-\uAA5F\uAADE\uAADF\uAAF0\uAAF1\uABEB\uFE10-\uFE16\uFE19\uFE30\uFE45\uFE46\uFE49-\uFE4C\uFE50-\uFE52\uFE54-\uFE57\uFE5F-\uFE61\uFE68\uFE6A\uFE6B\uFF01-\uFF03\uFF05-\uFF07\uFF0A\uFF0C\uFF0E\uFF0F\uFF1A\uFF1B\uFF1F\uFF20\uFF3C\uFF61\uFF64\uFF65',
        astral: '\uD800[\uDD00-\uDD02\uDF9F\uDFD0]|\uD801\uDD6F|\uD802[\uDC57\uDD1F\uDD3F\uDE50-\uDE58\uDE7F\uDEF0-\uDEF6\uDF39-\uDF3F\uDF99-\uDF9C]|\uD804[\uDC47-\uDC4D\uDCBB\uDCBC\uDCBE-\uDCC1\uDD40-\uDD43\uDD74\uDD75\uDDC5-\uDDC9\uDDCD\uDDDB\uDDDD-\uDDDF\uDE38-\uDE3D\uDEA9]|\uD805[\uDC4B-\uDC4F\uDC5B\uDC5D\uDCC6\uDDC1-\uDDD7\uDE41-\uDE43\uDE60-\uDE6C\uDF3C-\uDF3E]|\uD807[\uDC41-\uDC45\uDC70\uDC71]|\uD809[\uDC70-\uDC74]|\uD81A[\uDE6E\uDE6F\uDEF5\uDF37-\uDF3B\uDF44]|\uD82F\uDC9F|\uD836[\uDE87-\uDE8B]|\uD83A[\uDD5E\uDD5F]'
    }, {
        name: 'Ps',
        alias: 'Open_Punctuation',
        bmp: '\\x28\\x5B\\x7B\u0F3A\u0F3C\u169B\u201A\u201E\u2045\u207D\u208D\u2308\u230A\u2329\u2768\u276A\u276C\u276E\u2770\u2772\u2774\u27C5\u27E6\u27E8\u27EA\u27EC\u27EE\u2983\u2985\u2987\u2989\u298B\u298D\u298F\u2991\u2993\u2995\u2997\u29D8\u29DA\u29FC\u2E22\u2E24\u2E26\u2E28\u2E42\u3008\u300A\u300C\u300E\u3010\u3014\u3016\u3018\u301A\u301D\uFD3F\uFE17\uFE35\uFE37\uFE39\uFE3B\uFE3D\uFE3F\uFE41\uFE43\uFE47\uFE59\uFE5B\uFE5D\uFF08\uFF3B\uFF5B\uFF5F\uFF62'
    }, {
        name: 'S',
        alias: 'Symbol',
        bmp: '\\x24\\x2B\x3C-\x3E\\x5E\x60\\x7C\x7E\xA2-\xA6\xA8\xA9\xAC\xAE-\xB1\xB4\xB8\xD7\xF7\u02C2-\u02C5\u02D2-\u02DF\u02E5-\u02EB\u02ED\u02EF-\u02FF\u0375\u0384\u0385\u03F6\u0482\u058D-\u058F\u0606-\u0608\u060B\u060E\u060F\u06DE\u06E9\u06FD\u06FE\u07F6\u09F2\u09F3\u09FA\u09FB\u0AF1\u0B70\u0BF3-\u0BFA\u0C7F\u0D4F\u0D79\u0E3F\u0F01-\u0F03\u0F13\u0F15-\u0F17\u0F1A-\u0F1F\u0F34\u0F36\u0F38\u0FBE-\u0FC5\u0FC7-\u0FCC\u0FCE\u0FCF\u0FD5-\u0FD8\u109E\u109F\u1390-\u1399\u17DB\u1940\u19DE-\u19FF\u1B61-\u1B6A\u1B74-\u1B7C\u1FBD\u1FBF-\u1FC1\u1FCD-\u1FCF\u1FDD-\u1FDF\u1FED-\u1FEF\u1FFD\u1FFE\u2044\u2052\u207A-\u207C\u208A-\u208C\u20A0-\u20BE\u2100\u2101\u2103-\u2106\u2108\u2109\u2114\u2116-\u2118\u211E-\u2123\u2125\u2127\u2129\u212E\u213A\u213B\u2140-\u2144\u214A-\u214D\u214F\u218A\u218B\u2190-\u2307\u230C-\u2328\u232B-\u23FE\u2400-\u2426\u2440-\u244A\u249C-\u24E9\u2500-\u2767\u2794-\u27C4\u27C7-\u27E5\u27F0-\u2982\u2999-\u29D7\u29DC-\u29FB\u29FE-\u2B73\u2B76-\u2B95\u2B98-\u2BB9\u2BBD-\u2BC8\u2BCA-\u2BD1\u2BEC-\u2BEF\u2CE5-\u2CEA\u2E80-\u2E99\u2E9B-\u2EF3\u2F00-\u2FD5\u2FF0-\u2FFB\u3004\u3012\u3013\u3020\u3036\u3037\u303E\u303F\u309B\u309C\u3190\u3191\u3196-\u319F\u31C0-\u31E3\u3200-\u321E\u322A-\u3247\u3250\u3260-\u327F\u328A-\u32B0\u32C0-\u32FE\u3300-\u33FF\u4DC0-\u4DFF\uA490-\uA4C6\uA700-\uA716\uA720\uA721\uA789\uA78A\uA828-\uA82B\uA836-\uA839\uAA77-\uAA79\uAB5B\uFB29\uFBB2-\uFBC1\uFDFC\uFDFD\uFE62\uFE64-\uFE66\uFE69\uFF04\uFF0B\uFF1C-\uFF1E\uFF3E\uFF40\uFF5C\uFF5E\uFFE0-\uFFE6\uFFE8-\uFFEE\uFFFC\uFFFD',
        astral: '\uD800[\uDD37-\uDD3F\uDD79-\uDD89\uDD8C-\uDD8E\uDD90-\uDD9B\uDDA0\uDDD0-\uDDFC]|\uD802[\uDC77\uDC78\uDEC8]|\uD805\uDF3F|\uD81A[\uDF3C-\uDF3F\uDF45]|\uD82F\uDC9C|\uD834[\uDC00-\uDCF5\uDD00-\uDD26\uDD29-\uDD64\uDD6A-\uDD6C\uDD83\uDD84\uDD8C-\uDDA9\uDDAE-\uDDE8\uDE00-\uDE41\uDE45\uDF00-\uDF56]|\uD835[\uDEC1\uDEDB\uDEFB\uDF15\uDF35\uDF4F\uDF6F\uDF89\uDFA9\uDFC3]|\uD836[\uDC00-\uDDFF\uDE37-\uDE3A\uDE6D-\uDE74\uDE76-\uDE83\uDE85\uDE86]|\uD83B[\uDEF0\uDEF1]|\uD83C[\uDC00-\uDC2B\uDC30-\uDC93\uDCA0-\uDCAE\uDCB1-\uDCBF\uDCC1-\uDCCF\uDCD1-\uDCF5\uDD10-\uDD2E\uDD30-\uDD6B\uDD70-\uDDAC\uDDE6-\uDE02\uDE10-\uDE3B\uDE40-\uDE48\uDE50\uDE51\uDF00-\uDFFF]|\uD83D[\uDC00-\uDED2\uDEE0-\uDEEC\uDEF0-\uDEF6\uDF00-\uDF73\uDF80-\uDFD4]|\uD83E[\uDC00-\uDC0B\uDC10-\uDC47\uDC50-\uDC59\uDC60-\uDC87\uDC90-\uDCAD\uDD10-\uDD1E\uDD20-\uDD27\uDD30\uDD33-\uDD3E\uDD40-\uDD4B\uDD50-\uDD5E\uDD80-\uDD91\uDDC0]'
    }, {
        name: 'Sc',
        alias: 'Currency_Symbol',
        bmp: '\\x24\xA2-\xA5\u058F\u060B\u09F2\u09F3\u09FB\u0AF1\u0BF9\u0E3F\u17DB\u20A0-\u20BE\uA838\uFDFC\uFE69\uFF04\uFFE0\uFFE1\uFFE5\uFFE6'
    }, {
        name: 'Sk',
        alias: 'Modifier_Symbol',
        bmp: '\\x5E\x60\xA8\xAF\xB4\xB8\u02C2-\u02C5\u02D2-\u02DF\u02E5-\u02EB\u02ED\u02EF-\u02FF\u0375\u0384\u0385\u1FBD\u1FBF-\u1FC1\u1FCD-\u1FCF\u1FDD-\u1FDF\u1FED-\u1FEF\u1FFD\u1FFE\u309B\u309C\uA700-\uA716\uA720\uA721\uA789\uA78A\uAB5B\uFBB2-\uFBC1\uFF3E\uFF40\uFFE3',
        astral: '\uD83C[\uDFFB-\uDFFF]'
    }, {
        name: 'Sm',
        alias: 'Math_Symbol',
        bmp: '\\x2B\x3C-\x3E\\x7C\x7E\xAC\xB1\xD7\xF7\u03F6\u0606-\u0608\u2044\u2052\u207A-\u207C\u208A-\u208C\u2118\u2140-\u2144\u214B\u2190-\u2194\u219A\u219B\u21A0\u21A3\u21A6\u21AE\u21CE\u21CF\u21D2\u21D4\u21F4-\u22FF\u2320\u2321\u237C\u239B-\u23B3\u23DC-\u23E1\u25B7\u25C1\u25F8-\u25FF\u266F\u27C0-\u27C4\u27C7-\u27E5\u27F0-\u27FF\u2900-\u2982\u2999-\u29D7\u29DC-\u29FB\u29FE-\u2AFF\u2B30-\u2B44\u2B47-\u2B4C\uFB29\uFE62\uFE64-\uFE66\uFF0B\uFF1C-\uFF1E\uFF5C\uFF5E\uFFE2\uFFE9-\uFFEC',
        astral: '\uD835[\uDEC1\uDEDB\uDEFB\uDF15\uDF35\uDF4F\uDF6F\uDF89\uDFA9\uDFC3]|\uD83B[\uDEF0\uDEF1]'
    }, {
        name: 'So',
        alias: 'Other_Symbol',
        bmp: '\xA6\xA9\xAE\xB0\u0482\u058D\u058E\u060E\u060F\u06DE\u06E9\u06FD\u06FE\u07F6\u09FA\u0B70\u0BF3-\u0BF8\u0BFA\u0C7F\u0D4F\u0D79\u0F01-\u0F03\u0F13\u0F15-\u0F17\u0F1A-\u0F1F\u0F34\u0F36\u0F38\u0FBE-\u0FC5\u0FC7-\u0FCC\u0FCE\u0FCF\u0FD5-\u0FD8\u109E\u109F\u1390-\u1399\u1940\u19DE-\u19FF\u1B61-\u1B6A\u1B74-\u1B7C\u2100\u2101\u2103-\u2106\u2108\u2109\u2114\u2116\u2117\u211E-\u2123\u2125\u2127\u2129\u212E\u213A\u213B\u214A\u214C\u214D\u214F\u218A\u218B\u2195-\u2199\u219C-\u219F\u21A1\u21A2\u21A4\u21A5\u21A7-\u21AD\u21AF-\u21CD\u21D0\u21D1\u21D3\u21D5-\u21F3\u2300-\u2307\u230C-\u231F\u2322-\u2328\u232B-\u237B\u237D-\u239A\u23B4-\u23DB\u23E2-\u23FE\u2400-\u2426\u2440-\u244A\u249C-\u24E9\u2500-\u25B6\u25B8-\u25C0\u25C2-\u25F7\u2600-\u266E\u2670-\u2767\u2794-\u27BF\u2800-\u28FF\u2B00-\u2B2F\u2B45\u2B46\u2B4D-\u2B73\u2B76-\u2B95\u2B98-\u2BB9\u2BBD-\u2BC8\u2BCA-\u2BD1\u2BEC-\u2BEF\u2CE5-\u2CEA\u2E80-\u2E99\u2E9B-\u2EF3\u2F00-\u2FD5\u2FF0-\u2FFB\u3004\u3012\u3013\u3020\u3036\u3037\u303E\u303F\u3190\u3191\u3196-\u319F\u31C0-\u31E3\u3200-\u321E\u322A-\u3247\u3250\u3260-\u327F\u328A-\u32B0\u32C0-\u32FE\u3300-\u33FF\u4DC0-\u4DFF\uA490-\uA4C6\uA828-\uA82B\uA836\uA837\uA839\uAA77-\uAA79\uFDFD\uFFE4\uFFE8\uFFED\uFFEE\uFFFC\uFFFD',
        astral: '\uD800[\uDD37-\uDD3F\uDD79-\uDD89\uDD8C-\uDD8E\uDD90-\uDD9B\uDDA0\uDDD0-\uDDFC]|\uD802[\uDC77\uDC78\uDEC8]|\uD805\uDF3F|\uD81A[\uDF3C-\uDF3F\uDF45]|\uD82F\uDC9C|\uD834[\uDC00-\uDCF5\uDD00-\uDD26\uDD29-\uDD64\uDD6A-\uDD6C\uDD83\uDD84\uDD8C-\uDDA9\uDDAE-\uDDE8\uDE00-\uDE41\uDE45\uDF00-\uDF56]|\uD836[\uDC00-\uDDFF\uDE37-\uDE3A\uDE6D-\uDE74\uDE76-\uDE83\uDE85\uDE86]|\uD83C[\uDC00-\uDC2B\uDC30-\uDC93\uDCA0-\uDCAE\uDCB1-\uDCBF\uDCC1-\uDCCF\uDCD1-\uDCF5\uDD10-\uDD2E\uDD30-\uDD6B\uDD70-\uDDAC\uDDE6-\uDE02\uDE10-\uDE3B\uDE40-\uDE48\uDE50\uDE51\uDF00-\uDFFA]|\uD83D[\uDC00-\uDED2\uDEE0-\uDEEC\uDEF0-\uDEF6\uDF00-\uDF73\uDF80-\uDFD4]|\uD83E[\uDC00-\uDC0B\uDC10-\uDC47\uDC50-\uDC59\uDC60-\uDC87\uDC90-\uDCAD\uDD10-\uDD1E\uDD20-\uDD27\uDD30\uDD33-\uDD3E\uDD40-\uDD4B\uDD50-\uDD5E\uDD80-\uDD91\uDDC0]'
    }, {
        name: 'Z',
        alias: 'Separator',
        bmp: '\x20\xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000'
    }, {
        name: 'Zl',
        alias: 'Line_Separator',
        bmp: '\u2028'
    }, {
        name: 'Zp',
        alias: 'Paragraph_Separator',
        bmp: '\u2029'
    }, {
        name: 'Zs',
        alias: 'Space_Separator',
        bmp: '\x20\xA0\u1680\u2000-\u200A\u202F\u205F\u3000'
    }]);
};

module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/addons/unicode-properties.js":
/*!***************************************************************!*\
  !*** ./node_modules/xregexp/lib/addons/unicode-properties.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

/*!
 * XRegExp Unicode Properties 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2012-2017 MIT License
 * Unicode data by Mathias Bynens <mathiasbynens.be>
 */

exports.default = function (XRegExp) {

    /**
     * Adds properties to meet the UTS #18 Level 1 RL1.2 requirements for Unicode regex support. See
     * <http://unicode.org/reports/tr18/#RL1.2>. Following are definitions of these properties from
     * UAX #44 <http://unicode.org/reports/tr44/>:
     *
     * - Alphabetic
     *   Characters with the Alphabetic property. Generated from: Lowercase + Uppercase + Lt + Lm +
     *   Lo + Nl + Other_Alphabetic.
     *
     * - Default_Ignorable_Code_Point
     *   For programmatic determination of default ignorable code points. New characters that should
     *   be ignored in rendering (unless explicitly supported) will be assigned in these ranges,
     *   permitting programs to correctly handle the default rendering of such characters when not
     *   otherwise supported.
     *
     * - Lowercase
     *   Characters with the Lowercase property. Generated from: Ll + Other_Lowercase.
     *
     * - Noncharacter_Code_Point
     *   Code points permanently reserved for internal use.
     *
     * - Uppercase
     *   Characters with the Uppercase property. Generated from: Lu + Other_Uppercase.
     *
     * - White_Space
     *   Spaces, separator characters and other control characters which should be treated by
     *   programming languages as "white space" for the purpose of parsing elements.
     *
     * The properties ASCII, Any, and Assigned are also included but are not defined in UAX #44. UTS
     * #18 RL1.2 additionally requires support for Unicode scripts and general categories. These are
     * included in XRegExp's Unicode Categories and Unicode Scripts addons.
     *
     * Token names are case insensitive, and any spaces, hyphens, and underscores are ignored.
     *
     * Uses Unicode 9.0.0.
     *
     * @requires XRegExp, Unicode Base
     */

    if (!XRegExp.addUnicodeData) {
        throw new ReferenceError('Unicode Base must be loaded before Unicode Properties');
    }

    var unicodeData = [{
        name: 'ASCII',
        bmp: '\0-\x7F'
    }, {
        name: 'Alphabetic',
        bmp: 'A-Za-z\xAA\xB5\xBA\xC0-\xD6\xD8-\xF6\xF8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0345\u0370-\u0374\u0376\u0377\u037A-\u037D\u037F\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u052F\u0531-\u0556\u0559\u0561-\u0587\u05B0-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u05D0-\u05EA\u05F0-\u05F2\u0610-\u061A\u0620-\u0657\u0659-\u065F\u066E-\u06D3\u06D5-\u06DC\u06E1-\u06E8\u06ED-\u06EF\u06FA-\u06FC\u06FF\u0710-\u073F\u074D-\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0817\u081A-\u082C\u0840-\u0858\u08A0-\u08B4\u08B6-\u08BD\u08D4-\u08DF\u08E3-\u08E9\u08F0-\u093B\u093D-\u094C\u094E-\u0950\u0955-\u0963\u0971-\u0983\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD-\u09C4\u09C7\u09C8\u09CB\u09CC\u09CE\u09D7\u09DC\u09DD\u09DF-\u09E3\u09F0\u09F1\u0A01-\u0A03\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A3E-\u0A42\u0A47\u0A48\u0A4B\u0A4C\u0A51\u0A59-\u0A5C\u0A5E\u0A70-\u0A75\u0A81-\u0A83\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD-\u0AC5\u0AC7-\u0AC9\u0ACB\u0ACC\u0AD0\u0AE0-\u0AE3\u0AF9\u0B01-\u0B03\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D-\u0B44\u0B47\u0B48\u0B4B\u0B4C\u0B56\u0B57\u0B5C\u0B5D\u0B5F-\u0B63\u0B71\u0B82\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCC\u0BD0\u0BD7\u0C00-\u0C03\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D-\u0C44\u0C46-\u0C48\u0C4A-\u0C4C\u0C55\u0C56\u0C58-\u0C5A\u0C60-\u0C63\u0C80-\u0C83\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCC\u0CD5\u0CD6\u0CDE\u0CE0-\u0CE3\u0CF1\u0CF2\u0D01-\u0D03\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D-\u0D44\u0D46-\u0D48\u0D4A-\u0D4C\u0D4E\u0D54-\u0D57\u0D5F-\u0D63\u0D7A-\u0D7F\u0D82\u0D83\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E01-\u0E3A\u0E40-\u0E46\u0E4D\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB9\u0EBB-\u0EBD\u0EC0-\u0EC4\u0EC6\u0ECD\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F71-\u0F81\u0F88-\u0F97\u0F99-\u0FBC\u1000-\u1036\u1038\u103B-\u103F\u1050-\u1062\u1065-\u1068\u106E-\u1086\u108E\u109C\u109D\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u135F\u1380-\u138F\u13A0-\u13F5\u13F8-\u13FD\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u16EE-\u16F8\u1700-\u170C\u170E-\u1713\u1720-\u1733\u1740-\u1753\u1760-\u176C\u176E-\u1770\u1772\u1773\u1780-\u17B3\u17B6-\u17C8\u17D7\u17DC\u1820-\u1877\u1880-\u18AA\u18B0-\u18F5\u1900-\u191E\u1920-\u192B\u1930-\u1938\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19B0-\u19C9\u1A00-\u1A1B\u1A20-\u1A5E\u1A61-\u1A74\u1AA7\u1B00-\u1B33\u1B35-\u1B43\u1B45-\u1B4B\u1B80-\u1BA9\u1BAC-\u1BAF\u1BBA-\u1BE5\u1BE7-\u1BF1\u1C00-\u1C35\u1C4D-\u1C4F\u1C5A-\u1C7D\u1C80-\u1C88\u1CE9-\u1CEC\u1CEE-\u1CF3\u1CF5\u1CF6\u1D00-\u1DBF\u1DE7-\u1DF4\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2160-\u2188\u24B6-\u24E9\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2DE0-\u2DFF\u2E2F\u3005-\u3007\u3021-\u3029\u3031-\u3035\u3038-\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FD5\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA674-\uA67B\uA67F-\uA6EF\uA717-\uA71F\uA722-\uA788\uA78B-\uA7AE\uA7B0-\uA7B7\uA7F7-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA827\uA840-\uA873\uA880-\uA8C3\uA8C5\uA8F2-\uA8F7\uA8FB\uA8FD\uA90A-\uA92A\uA930-\uA952\uA960-\uA97C\uA980-\uA9B2\uA9B4-\uA9BF\uA9CF\uA9E0-\uA9E4\uA9E6-\uA9EF\uA9FA-\uA9FE\uAA00-\uAA36\uAA40-\uAA4D\uAA60-\uAA76\uAA7A\uAA7E-\uAABE\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEF\uAAF2-\uAAF5\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uAB30-\uAB5A\uAB5C-\uAB65\uAB70-\uABEA\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC',
        astral: '\uD800[\uDC00-\uDC0B\uDC0D-\uDC26\uDC28-\uDC3A\uDC3C\uDC3D\uDC3F-\uDC4D\uDC50-\uDC5D\uDC80-\uDCFA\uDD40-\uDD74\uDE80-\uDE9C\uDEA0-\uDED0\uDF00-\uDF1F\uDF30-\uDF4A\uDF50-\uDF7A\uDF80-\uDF9D\uDFA0-\uDFC3\uDFC8-\uDFCF\uDFD1-\uDFD5]|\uD801[\uDC00-\uDC9D\uDCB0-\uDCD3\uDCD8-\uDCFB\uDD00-\uDD27\uDD30-\uDD63\uDE00-\uDF36\uDF40-\uDF55\uDF60-\uDF67]|\uD802[\uDC00-\uDC05\uDC08\uDC0A-\uDC35\uDC37\uDC38\uDC3C\uDC3F-\uDC55\uDC60-\uDC76\uDC80-\uDC9E\uDCE0-\uDCF2\uDCF4\uDCF5\uDD00-\uDD15\uDD20-\uDD39\uDD80-\uDDB7\uDDBE\uDDBF\uDE00-\uDE03\uDE05\uDE06\uDE0C-\uDE13\uDE15-\uDE17\uDE19-\uDE33\uDE60-\uDE7C\uDE80-\uDE9C\uDEC0-\uDEC7\uDEC9-\uDEE4\uDF00-\uDF35\uDF40-\uDF55\uDF60-\uDF72\uDF80-\uDF91]|\uD803[\uDC00-\uDC48\uDC80-\uDCB2\uDCC0-\uDCF2]|\uD804[\uDC00-\uDC45\uDC82-\uDCB8\uDCD0-\uDCE8\uDD00-\uDD32\uDD50-\uDD72\uDD76\uDD80-\uDDBF\uDDC1-\uDDC4\uDDDA\uDDDC\uDE00-\uDE11\uDE13-\uDE34\uDE37\uDE3E\uDE80-\uDE86\uDE88\uDE8A-\uDE8D\uDE8F-\uDE9D\uDE9F-\uDEA8\uDEB0-\uDEE8\uDF00-\uDF03\uDF05-\uDF0C\uDF0F\uDF10\uDF13-\uDF28\uDF2A-\uDF30\uDF32\uDF33\uDF35-\uDF39\uDF3D-\uDF44\uDF47\uDF48\uDF4B\uDF4C\uDF50\uDF57\uDF5D-\uDF63]|\uD805[\uDC00-\uDC41\uDC43-\uDC45\uDC47-\uDC4A\uDC80-\uDCC1\uDCC4\uDCC5\uDCC7\uDD80-\uDDB5\uDDB8-\uDDBE\uDDD8-\uDDDD\uDE00-\uDE3E\uDE40\uDE44\uDE80-\uDEB5\uDF00-\uDF19\uDF1D-\uDF2A]|\uD806[\uDCA0-\uDCDF\uDCFF\uDEC0-\uDEF8]|\uD807[\uDC00-\uDC08\uDC0A-\uDC36\uDC38-\uDC3E\uDC40\uDC72-\uDC8F\uDC92-\uDCA7\uDCA9-\uDCB6]|\uD808[\uDC00-\uDF99]|\uD809[\uDC00-\uDC6E\uDC80-\uDD43]|[\uD80C\uD81C-\uD820\uD840-\uD868\uD86A-\uD86C\uD86F-\uD872][\uDC00-\uDFFF]|\uD80D[\uDC00-\uDC2E]|\uD811[\uDC00-\uDE46]|\uD81A[\uDC00-\uDE38\uDE40-\uDE5E\uDED0-\uDEED\uDF00-\uDF36\uDF40-\uDF43\uDF63-\uDF77\uDF7D-\uDF8F]|\uD81B[\uDF00-\uDF44\uDF50-\uDF7E\uDF93-\uDF9F\uDFE0]|\uD821[\uDC00-\uDFEC]|\uD822[\uDC00-\uDEF2]|\uD82C[\uDC00\uDC01]|\uD82F[\uDC00-\uDC6A\uDC70-\uDC7C\uDC80-\uDC88\uDC90-\uDC99\uDC9E]|\uD835[\uDC00-\uDC54\uDC56-\uDC9C\uDC9E\uDC9F\uDCA2\uDCA5\uDCA6\uDCA9-\uDCAC\uDCAE-\uDCB9\uDCBB\uDCBD-\uDCC3\uDCC5-\uDD05\uDD07-\uDD0A\uDD0D-\uDD14\uDD16-\uDD1C\uDD1E-\uDD39\uDD3B-\uDD3E\uDD40-\uDD44\uDD46\uDD4A-\uDD50\uDD52-\uDEA5\uDEA8-\uDEC0\uDEC2-\uDEDA\uDEDC-\uDEFA\uDEFC-\uDF14\uDF16-\uDF34\uDF36-\uDF4E\uDF50-\uDF6E\uDF70-\uDF88\uDF8A-\uDFA8\uDFAA-\uDFC2\uDFC4-\uDFCB]|\uD838[\uDC00-\uDC06\uDC08-\uDC18\uDC1B-\uDC21\uDC23\uDC24\uDC26-\uDC2A]|\uD83A[\uDC00-\uDCC4\uDD00-\uDD43\uDD47]|\uD83B[\uDE00-\uDE03\uDE05-\uDE1F\uDE21\uDE22\uDE24\uDE27\uDE29-\uDE32\uDE34-\uDE37\uDE39\uDE3B\uDE42\uDE47\uDE49\uDE4B\uDE4D-\uDE4F\uDE51\uDE52\uDE54\uDE57\uDE59\uDE5B\uDE5D\uDE5F\uDE61\uDE62\uDE64\uDE67-\uDE6A\uDE6C-\uDE72\uDE74-\uDE77\uDE79-\uDE7C\uDE7E\uDE80-\uDE89\uDE8B-\uDE9B\uDEA1-\uDEA3\uDEA5-\uDEA9\uDEAB-\uDEBB]|\uD83C[\uDD30-\uDD49\uDD50-\uDD69\uDD70-\uDD89]|\uD869[\uDC00-\uDED6\uDF00-\uDFFF]|\uD86D[\uDC00-\uDF34\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1D\uDC20-\uDFFF]|\uD873[\uDC00-\uDEA1]|\uD87E[\uDC00-\uDE1D]'
    }, {
        name: 'Any',
        isBmpLast: true,
        bmp: '\0-\uFFFF',
        astral: '[\uD800-\uDBFF][\uDC00-\uDFFF]'
    }, {
        name: 'Default_Ignorable_Code_Point',
        bmp: '\xAD\u034F\u061C\u115F\u1160\u17B4\u17B5\u180B-\u180E\u200B-\u200F\u202A-\u202E\u2060-\u206F\u3164\uFE00-\uFE0F\uFEFF\uFFA0\uFFF0-\uFFF8',
        astral: '\uD82F[\uDCA0-\uDCA3]|\uD834[\uDD73-\uDD7A]|[\uDB40-\uDB43][\uDC00-\uDFFF]'
    }, {
        name: 'Lowercase',
        bmp: 'a-z\xAA\xB5\xBA\xDF-\xF6\xF8-\xFF\u0101\u0103\u0105\u0107\u0109\u010B\u010D\u010F\u0111\u0113\u0115\u0117\u0119\u011B\u011D\u011F\u0121\u0123\u0125\u0127\u0129\u012B\u012D\u012F\u0131\u0133\u0135\u0137\u0138\u013A\u013C\u013E\u0140\u0142\u0144\u0146\u0148\u0149\u014B\u014D\u014F\u0151\u0153\u0155\u0157\u0159\u015B\u015D\u015F\u0161\u0163\u0165\u0167\u0169\u016B\u016D\u016F\u0171\u0173\u0175\u0177\u017A\u017C\u017E-\u0180\u0183\u0185\u0188\u018C\u018D\u0192\u0195\u0199-\u019B\u019E\u01A1\u01A3\u01A5\u01A8\u01AA\u01AB\u01AD\u01B0\u01B4\u01B6\u01B9\u01BA\u01BD-\u01BF\u01C6\u01C9\u01CC\u01CE\u01D0\u01D2\u01D4\u01D6\u01D8\u01DA\u01DC\u01DD\u01DF\u01E1\u01E3\u01E5\u01E7\u01E9\u01EB\u01ED\u01EF\u01F0\u01F3\u01F5\u01F9\u01FB\u01FD\u01FF\u0201\u0203\u0205\u0207\u0209\u020B\u020D\u020F\u0211\u0213\u0215\u0217\u0219\u021B\u021D\u021F\u0221\u0223\u0225\u0227\u0229\u022B\u022D\u022F\u0231\u0233-\u0239\u023C\u023F\u0240\u0242\u0247\u0249\u024B\u024D\u024F-\u0293\u0295-\u02B8\u02C0\u02C1\u02E0-\u02E4\u0345\u0371\u0373\u0377\u037A-\u037D\u0390\u03AC-\u03CE\u03D0\u03D1\u03D5-\u03D7\u03D9\u03DB\u03DD\u03DF\u03E1\u03E3\u03E5\u03E7\u03E9\u03EB\u03ED\u03EF-\u03F3\u03F5\u03F8\u03FB\u03FC\u0430-\u045F\u0461\u0463\u0465\u0467\u0469\u046B\u046D\u046F\u0471\u0473\u0475\u0477\u0479\u047B\u047D\u047F\u0481\u048B\u048D\u048F\u0491\u0493\u0495\u0497\u0499\u049B\u049D\u049F\u04A1\u04A3\u04A5\u04A7\u04A9\u04AB\u04AD\u04AF\u04B1\u04B3\u04B5\u04B7\u04B9\u04BB\u04BD\u04BF\u04C2\u04C4\u04C6\u04C8\u04CA\u04CC\u04CE\u04CF\u04D1\u04D3\u04D5\u04D7\u04D9\u04DB\u04DD\u04DF\u04E1\u04E3\u04E5\u04E7\u04E9\u04EB\u04ED\u04EF\u04F1\u04F3\u04F5\u04F7\u04F9\u04FB\u04FD\u04FF\u0501\u0503\u0505\u0507\u0509\u050B\u050D\u050F\u0511\u0513\u0515\u0517\u0519\u051B\u051D\u051F\u0521\u0523\u0525\u0527\u0529\u052B\u052D\u052F\u0561-\u0587\u13F8-\u13FD\u1C80-\u1C88\u1D00-\u1DBF\u1E01\u1E03\u1E05\u1E07\u1E09\u1E0B\u1E0D\u1E0F\u1E11\u1E13\u1E15\u1E17\u1E19\u1E1B\u1E1D\u1E1F\u1E21\u1E23\u1E25\u1E27\u1E29\u1E2B\u1E2D\u1E2F\u1E31\u1E33\u1E35\u1E37\u1E39\u1E3B\u1E3D\u1E3F\u1E41\u1E43\u1E45\u1E47\u1E49\u1E4B\u1E4D\u1E4F\u1E51\u1E53\u1E55\u1E57\u1E59\u1E5B\u1E5D\u1E5F\u1E61\u1E63\u1E65\u1E67\u1E69\u1E6B\u1E6D\u1E6F\u1E71\u1E73\u1E75\u1E77\u1E79\u1E7B\u1E7D\u1E7F\u1E81\u1E83\u1E85\u1E87\u1E89\u1E8B\u1E8D\u1E8F\u1E91\u1E93\u1E95-\u1E9D\u1E9F\u1EA1\u1EA3\u1EA5\u1EA7\u1EA9\u1EAB\u1EAD\u1EAF\u1EB1\u1EB3\u1EB5\u1EB7\u1EB9\u1EBB\u1EBD\u1EBF\u1EC1\u1EC3\u1EC5\u1EC7\u1EC9\u1ECB\u1ECD\u1ECF\u1ED1\u1ED3\u1ED5\u1ED7\u1ED9\u1EDB\u1EDD\u1EDF\u1EE1\u1EE3\u1EE5\u1EE7\u1EE9\u1EEB\u1EED\u1EEF\u1EF1\u1EF3\u1EF5\u1EF7\u1EF9\u1EFB\u1EFD\u1EFF-\u1F07\u1F10-\u1F15\u1F20-\u1F27\u1F30-\u1F37\u1F40-\u1F45\u1F50-\u1F57\u1F60-\u1F67\u1F70-\u1F7D\u1F80-\u1F87\u1F90-\u1F97\u1FA0-\u1FA7\u1FB0-\u1FB4\u1FB6\u1FB7\u1FBE\u1FC2-\u1FC4\u1FC6\u1FC7\u1FD0-\u1FD3\u1FD6\u1FD7\u1FE0-\u1FE7\u1FF2-\u1FF4\u1FF6\u1FF7\u2071\u207F\u2090-\u209C\u210A\u210E\u210F\u2113\u212F\u2134\u2139\u213C\u213D\u2146-\u2149\u214E\u2170-\u217F\u2184\u24D0-\u24E9\u2C30-\u2C5E\u2C61\u2C65\u2C66\u2C68\u2C6A\u2C6C\u2C71\u2C73\u2C74\u2C76-\u2C7D\u2C81\u2C83\u2C85\u2C87\u2C89\u2C8B\u2C8D\u2C8F\u2C91\u2C93\u2C95\u2C97\u2C99\u2C9B\u2C9D\u2C9F\u2CA1\u2CA3\u2CA5\u2CA7\u2CA9\u2CAB\u2CAD\u2CAF\u2CB1\u2CB3\u2CB5\u2CB7\u2CB9\u2CBB\u2CBD\u2CBF\u2CC1\u2CC3\u2CC5\u2CC7\u2CC9\u2CCB\u2CCD\u2CCF\u2CD1\u2CD3\u2CD5\u2CD7\u2CD9\u2CDB\u2CDD\u2CDF\u2CE1\u2CE3\u2CE4\u2CEC\u2CEE\u2CF3\u2D00-\u2D25\u2D27\u2D2D\uA641\uA643\uA645\uA647\uA649\uA64B\uA64D\uA64F\uA651\uA653\uA655\uA657\uA659\uA65B\uA65D\uA65F\uA661\uA663\uA665\uA667\uA669\uA66B\uA66D\uA681\uA683\uA685\uA687\uA689\uA68B\uA68D\uA68F\uA691\uA693\uA695\uA697\uA699\uA69B-\uA69D\uA723\uA725\uA727\uA729\uA72B\uA72D\uA72F-\uA731\uA733\uA735\uA737\uA739\uA73B\uA73D\uA73F\uA741\uA743\uA745\uA747\uA749\uA74B\uA74D\uA74F\uA751\uA753\uA755\uA757\uA759\uA75B\uA75D\uA75F\uA761\uA763\uA765\uA767\uA769\uA76B\uA76D\uA76F-\uA778\uA77A\uA77C\uA77F\uA781\uA783\uA785\uA787\uA78C\uA78E\uA791\uA793-\uA795\uA797\uA799\uA79B\uA79D\uA79F\uA7A1\uA7A3\uA7A5\uA7A7\uA7A9\uA7B5\uA7B7\uA7F8-\uA7FA\uAB30-\uAB5A\uAB5C-\uAB65\uAB70-\uABBF\uFB00-\uFB06\uFB13-\uFB17\uFF41-\uFF5A',
        astral: '\uD801[\uDC28-\uDC4F\uDCD8-\uDCFB]|\uD803[\uDCC0-\uDCF2]|\uD806[\uDCC0-\uDCDF]|\uD835[\uDC1A-\uDC33\uDC4E-\uDC54\uDC56-\uDC67\uDC82-\uDC9B\uDCB6-\uDCB9\uDCBB\uDCBD-\uDCC3\uDCC5-\uDCCF\uDCEA-\uDD03\uDD1E-\uDD37\uDD52-\uDD6B\uDD86-\uDD9F\uDDBA-\uDDD3\uDDEE-\uDE07\uDE22-\uDE3B\uDE56-\uDE6F\uDE8A-\uDEA5\uDEC2-\uDEDA\uDEDC-\uDEE1\uDEFC-\uDF14\uDF16-\uDF1B\uDF36-\uDF4E\uDF50-\uDF55\uDF70-\uDF88\uDF8A-\uDF8F\uDFAA-\uDFC2\uDFC4-\uDFC9\uDFCB]|\uD83A[\uDD22-\uDD43]'
    }, {
        name: 'Noncharacter_Code_Point',
        bmp: '\uFDD0-\uFDEF\uFFFE\uFFFF',
        astral: '[\uD83F\uD87F\uD8BF\uD8FF\uD93F\uD97F\uD9BF\uD9FF\uDA3F\uDA7F\uDABF\uDAFF\uDB3F\uDB7F\uDBBF\uDBFF][\uDFFE\uDFFF]'
    }, {
        name: 'Uppercase',
        bmp: 'A-Z\xC0-\xD6\xD8-\xDE\u0100\u0102\u0104\u0106\u0108\u010A\u010C\u010E\u0110\u0112\u0114\u0116\u0118\u011A\u011C\u011E\u0120\u0122\u0124\u0126\u0128\u012A\u012C\u012E\u0130\u0132\u0134\u0136\u0139\u013B\u013D\u013F\u0141\u0143\u0145\u0147\u014A\u014C\u014E\u0150\u0152\u0154\u0156\u0158\u015A\u015C\u015E\u0160\u0162\u0164\u0166\u0168\u016A\u016C\u016E\u0170\u0172\u0174\u0176\u0178\u0179\u017B\u017D\u0181\u0182\u0184\u0186\u0187\u0189-\u018B\u018E-\u0191\u0193\u0194\u0196-\u0198\u019C\u019D\u019F\u01A0\u01A2\u01A4\u01A6\u01A7\u01A9\u01AC\u01AE\u01AF\u01B1-\u01B3\u01B5\u01B7\u01B8\u01BC\u01C4\u01C7\u01CA\u01CD\u01CF\u01D1\u01D3\u01D5\u01D7\u01D9\u01DB\u01DE\u01E0\u01E2\u01E4\u01E6\u01E8\u01EA\u01EC\u01EE\u01F1\u01F4\u01F6-\u01F8\u01FA\u01FC\u01FE\u0200\u0202\u0204\u0206\u0208\u020A\u020C\u020E\u0210\u0212\u0214\u0216\u0218\u021A\u021C\u021E\u0220\u0222\u0224\u0226\u0228\u022A\u022C\u022E\u0230\u0232\u023A\u023B\u023D\u023E\u0241\u0243-\u0246\u0248\u024A\u024C\u024E\u0370\u0372\u0376\u037F\u0386\u0388-\u038A\u038C\u038E\u038F\u0391-\u03A1\u03A3-\u03AB\u03CF\u03D2-\u03D4\u03D8\u03DA\u03DC\u03DE\u03E0\u03E2\u03E4\u03E6\u03E8\u03EA\u03EC\u03EE\u03F4\u03F7\u03F9\u03FA\u03FD-\u042F\u0460\u0462\u0464\u0466\u0468\u046A\u046C\u046E\u0470\u0472\u0474\u0476\u0478\u047A\u047C\u047E\u0480\u048A\u048C\u048E\u0490\u0492\u0494\u0496\u0498\u049A\u049C\u049E\u04A0\u04A2\u04A4\u04A6\u04A8\u04AA\u04AC\u04AE\u04B0\u04B2\u04B4\u04B6\u04B8\u04BA\u04BC\u04BE\u04C0\u04C1\u04C3\u04C5\u04C7\u04C9\u04CB\u04CD\u04D0\u04D2\u04D4\u04D6\u04D8\u04DA\u04DC\u04DE\u04E0\u04E2\u04E4\u04E6\u04E8\u04EA\u04EC\u04EE\u04F0\u04F2\u04F4\u04F6\u04F8\u04FA\u04FC\u04FE\u0500\u0502\u0504\u0506\u0508\u050A\u050C\u050E\u0510\u0512\u0514\u0516\u0518\u051A\u051C\u051E\u0520\u0522\u0524\u0526\u0528\u052A\u052C\u052E\u0531-\u0556\u10A0-\u10C5\u10C7\u10CD\u13A0-\u13F5\u1E00\u1E02\u1E04\u1E06\u1E08\u1E0A\u1E0C\u1E0E\u1E10\u1E12\u1E14\u1E16\u1E18\u1E1A\u1E1C\u1E1E\u1E20\u1E22\u1E24\u1E26\u1E28\u1E2A\u1E2C\u1E2E\u1E30\u1E32\u1E34\u1E36\u1E38\u1E3A\u1E3C\u1E3E\u1E40\u1E42\u1E44\u1E46\u1E48\u1E4A\u1E4C\u1E4E\u1E50\u1E52\u1E54\u1E56\u1E58\u1E5A\u1E5C\u1E5E\u1E60\u1E62\u1E64\u1E66\u1E68\u1E6A\u1E6C\u1E6E\u1E70\u1E72\u1E74\u1E76\u1E78\u1E7A\u1E7C\u1E7E\u1E80\u1E82\u1E84\u1E86\u1E88\u1E8A\u1E8C\u1E8E\u1E90\u1E92\u1E94\u1E9E\u1EA0\u1EA2\u1EA4\u1EA6\u1EA8\u1EAA\u1EAC\u1EAE\u1EB0\u1EB2\u1EB4\u1EB6\u1EB8\u1EBA\u1EBC\u1EBE\u1EC0\u1EC2\u1EC4\u1EC6\u1EC8\u1ECA\u1ECC\u1ECE\u1ED0\u1ED2\u1ED4\u1ED6\u1ED8\u1EDA\u1EDC\u1EDE\u1EE0\u1EE2\u1EE4\u1EE6\u1EE8\u1EEA\u1EEC\u1EEE\u1EF0\u1EF2\u1EF4\u1EF6\u1EF8\u1EFA\u1EFC\u1EFE\u1F08-\u1F0F\u1F18-\u1F1D\u1F28-\u1F2F\u1F38-\u1F3F\u1F48-\u1F4D\u1F59\u1F5B\u1F5D\u1F5F\u1F68-\u1F6F\u1FB8-\u1FBB\u1FC8-\u1FCB\u1FD8-\u1FDB\u1FE8-\u1FEC\u1FF8-\u1FFB\u2102\u2107\u210B-\u210D\u2110-\u2112\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u2130-\u2133\u213E\u213F\u2145\u2160-\u216F\u2183\u24B6-\u24CF\u2C00-\u2C2E\u2C60\u2C62-\u2C64\u2C67\u2C69\u2C6B\u2C6D-\u2C70\u2C72\u2C75\u2C7E-\u2C80\u2C82\u2C84\u2C86\u2C88\u2C8A\u2C8C\u2C8E\u2C90\u2C92\u2C94\u2C96\u2C98\u2C9A\u2C9C\u2C9E\u2CA0\u2CA2\u2CA4\u2CA6\u2CA8\u2CAA\u2CAC\u2CAE\u2CB0\u2CB2\u2CB4\u2CB6\u2CB8\u2CBA\u2CBC\u2CBE\u2CC0\u2CC2\u2CC4\u2CC6\u2CC8\u2CCA\u2CCC\u2CCE\u2CD0\u2CD2\u2CD4\u2CD6\u2CD8\u2CDA\u2CDC\u2CDE\u2CE0\u2CE2\u2CEB\u2CED\u2CF2\uA640\uA642\uA644\uA646\uA648\uA64A\uA64C\uA64E\uA650\uA652\uA654\uA656\uA658\uA65A\uA65C\uA65E\uA660\uA662\uA664\uA666\uA668\uA66A\uA66C\uA680\uA682\uA684\uA686\uA688\uA68A\uA68C\uA68E\uA690\uA692\uA694\uA696\uA698\uA69A\uA722\uA724\uA726\uA728\uA72A\uA72C\uA72E\uA732\uA734\uA736\uA738\uA73A\uA73C\uA73E\uA740\uA742\uA744\uA746\uA748\uA74A\uA74C\uA74E\uA750\uA752\uA754\uA756\uA758\uA75A\uA75C\uA75E\uA760\uA762\uA764\uA766\uA768\uA76A\uA76C\uA76E\uA779\uA77B\uA77D\uA77E\uA780\uA782\uA784\uA786\uA78B\uA78D\uA790\uA792\uA796\uA798\uA79A\uA79C\uA79E\uA7A0\uA7A2\uA7A4\uA7A6\uA7A8\uA7AA-\uA7AE\uA7B0-\uA7B4\uA7B6\uFF21-\uFF3A',
        astral: '\uD801[\uDC00-\uDC27\uDCB0-\uDCD3]|\uD803[\uDC80-\uDCB2]|\uD806[\uDCA0-\uDCBF]|\uD835[\uDC00-\uDC19\uDC34-\uDC4D\uDC68-\uDC81\uDC9C\uDC9E\uDC9F\uDCA2\uDCA5\uDCA6\uDCA9-\uDCAC\uDCAE-\uDCB5\uDCD0-\uDCE9\uDD04\uDD05\uDD07-\uDD0A\uDD0D-\uDD14\uDD16-\uDD1C\uDD38\uDD39\uDD3B-\uDD3E\uDD40-\uDD44\uDD46\uDD4A-\uDD50\uDD6C-\uDD85\uDDA0-\uDDB9\uDDD4-\uDDED\uDE08-\uDE21\uDE3C-\uDE55\uDE70-\uDE89\uDEA8-\uDEC0\uDEE2-\uDEFA\uDF1C-\uDF34\uDF56-\uDF6E\uDF90-\uDFA8\uDFCA]|\uD83A[\uDD00-\uDD21]|\uD83C[\uDD30-\uDD49\uDD50-\uDD69\uDD70-\uDD89]'
    }, {
        name: 'White_Space',
        bmp: '\x09-\x0D\x20\x85\xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000'
    }];

    // Add non-generated data
    unicodeData.push({
        name: 'Assigned',
        // Since this is defined as the inverse of Unicode category Cn (Unassigned), the Unicode
        // Categories addon is required to use this property
        inverseOf: 'Cn'
    });

    XRegExp.addUnicodeData(unicodeData);
};

module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/addons/unicode-scripts.js":
/*!************************************************************!*\
  !*** ./node_modules/xregexp/lib/addons/unicode-scripts.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

/*!
 * XRegExp Unicode Scripts 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2010-2017 MIT License
 * Unicode data by Mathias Bynens <mathiasbynens.be>
 */

exports.default = function (XRegExp) {

    /**
     * Adds support for all Unicode scripts. E.g., `\p{Latin}`. Token names are case insensitive,
     * and any spaces, hyphens, and underscores are ignored.
     *
     * Uses Unicode 9.0.0.
     *
     * @requires XRegExp, Unicode Base
     */

    if (!XRegExp.addUnicodeData) {
        throw new ReferenceError('Unicode Base must be loaded before Unicode Scripts');
    }

    XRegExp.addUnicodeData([{
        name: 'Adlam',
        astral: '\uD83A[\uDD00-\uDD4A\uDD50-\uDD59\uDD5E\uDD5F]'
    }, {
        name: 'Ahom',
        astral: '\uD805[\uDF00-\uDF19\uDF1D-\uDF2B\uDF30-\uDF3F]'
    }, {
        name: 'Anatolian_Hieroglyphs',
        astral: '\uD811[\uDC00-\uDE46]'
    }, {
        name: 'Arabic',
        bmp: '\u0600-\u0604\u0606-\u060B\u060D-\u061A\u061E\u0620-\u063F\u0641-\u064A\u0656-\u066F\u0671-\u06DC\u06DE-\u06FF\u0750-\u077F\u08A0-\u08B4\u08B6-\u08BD\u08D4-\u08E1\u08E3-\u08FF\uFB50-\uFBC1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFD\uFE70-\uFE74\uFE76-\uFEFC',
        astral: '\uD803[\uDE60-\uDE7E]|\uD83B[\uDE00-\uDE03\uDE05-\uDE1F\uDE21\uDE22\uDE24\uDE27\uDE29-\uDE32\uDE34-\uDE37\uDE39\uDE3B\uDE42\uDE47\uDE49\uDE4B\uDE4D-\uDE4F\uDE51\uDE52\uDE54\uDE57\uDE59\uDE5B\uDE5D\uDE5F\uDE61\uDE62\uDE64\uDE67-\uDE6A\uDE6C-\uDE72\uDE74-\uDE77\uDE79-\uDE7C\uDE7E\uDE80-\uDE89\uDE8B-\uDE9B\uDEA1-\uDEA3\uDEA5-\uDEA9\uDEAB-\uDEBB\uDEF0\uDEF1]'
    }, {
        name: 'Armenian',
        bmp: '\u0531-\u0556\u0559-\u055F\u0561-\u0587\u058A\u058D-\u058F\uFB13-\uFB17'
    }, {
        name: 'Avestan',
        astral: '\uD802[\uDF00-\uDF35\uDF39-\uDF3F]'
    }, {
        name: 'Balinese',
        bmp: '\u1B00-\u1B4B\u1B50-\u1B7C'
    }, {
        name: 'Bamum',
        bmp: '\uA6A0-\uA6F7',
        astral: '\uD81A[\uDC00-\uDE38]'
    }, {
        name: 'Bassa_Vah',
        astral: '\uD81A[\uDED0-\uDEED\uDEF0-\uDEF5]'
    }, {
        name: 'Batak',
        bmp: '\u1BC0-\u1BF3\u1BFC-\u1BFF'
    }, {
        name: 'Bengali',
        bmp: '\u0980-\u0983\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BC-\u09C4\u09C7\u09C8\u09CB-\u09CE\u09D7\u09DC\u09DD\u09DF-\u09E3\u09E6-\u09FB'
    }, {
        name: 'Bhaiksuki',
        astral: '\uD807[\uDC00-\uDC08\uDC0A-\uDC36\uDC38-\uDC45\uDC50-\uDC6C]'
    }, {
        name: 'Bopomofo',
        bmp: '\u02EA\u02EB\u3105-\u312D\u31A0-\u31BA'
    }, {
        name: 'Brahmi',
        astral: '\uD804[\uDC00-\uDC4D\uDC52-\uDC6F\uDC7F]'
    }, {
        name: 'Braille',
        bmp: '\u2800-\u28FF'
    }, {
        name: 'Buginese',
        bmp: '\u1A00-\u1A1B\u1A1E\u1A1F'
    }, {
        name: 'Buhid',
        bmp: '\u1740-\u1753'
    }, {
        name: 'Canadian_Aboriginal',
        bmp: '\u1400-\u167F\u18B0-\u18F5'
    }, {
        name: 'Carian',
        astral: '\uD800[\uDEA0-\uDED0]'
    }, {
        name: 'Caucasian_Albanian',
        astral: '\uD801[\uDD30-\uDD63\uDD6F]'
    }, {
        name: 'Chakma',
        astral: '\uD804[\uDD00-\uDD34\uDD36-\uDD43]'
    }, {
        name: 'Cham',
        bmp: '\uAA00-\uAA36\uAA40-\uAA4D\uAA50-\uAA59\uAA5C-\uAA5F'
    }, {
        name: 'Cherokee',
        bmp: '\u13A0-\u13F5\u13F8-\u13FD\uAB70-\uABBF'
    }, {
        name: 'Common',
        bmp: '\0-\x40\\x5B-\x60\\x7B-\xA9\xAB-\xB9\xBB-\xBF\xD7\xF7\u02B9-\u02DF\u02E5-\u02E9\u02EC-\u02FF\u0374\u037E\u0385\u0387\u0589\u0605\u060C\u061B\u061C\u061F\u0640\u06DD\u08E2\u0964\u0965\u0E3F\u0FD5-\u0FD8\u10FB\u16EB-\u16ED\u1735\u1736\u1802\u1803\u1805\u1CD3\u1CE1\u1CE9-\u1CEC\u1CEE-\u1CF3\u1CF5\u1CF6\u2000-\u200B\u200E-\u2064\u2066-\u2070\u2074-\u207E\u2080-\u208E\u20A0-\u20BE\u2100-\u2125\u2127-\u2129\u212C-\u2131\u2133-\u214D\u214F-\u215F\u2189-\u218B\u2190-\u23FE\u2400-\u2426\u2440-\u244A\u2460-\u27FF\u2900-\u2B73\u2B76-\u2B95\u2B98-\u2BB9\u2BBD-\u2BC8\u2BCA-\u2BD1\u2BEC-\u2BEF\u2E00-\u2E44\u2FF0-\u2FFB\u3000-\u3004\u3006\u3008-\u3020\u3030-\u3037\u303C-\u303F\u309B\u309C\u30A0\u30FB\u30FC\u3190-\u319F\u31C0-\u31E3\u3220-\u325F\u327F-\u32CF\u3358-\u33FF\u4DC0-\u4DFF\uA700-\uA721\uA788-\uA78A\uA830-\uA839\uA92E\uA9CF\uAB5B\uFD3E\uFD3F\uFE10-\uFE19\uFE30-\uFE52\uFE54-\uFE66\uFE68-\uFE6B\uFEFF\uFF01-\uFF20\uFF3B-\uFF40\uFF5B-\uFF65\uFF70\uFF9E\uFF9F\uFFE0-\uFFE6\uFFE8-\uFFEE\uFFF9-\uFFFD',
        astral: '\uD800[\uDD00-\uDD02\uDD07-\uDD33\uDD37-\uDD3F\uDD90-\uDD9B\uDDD0-\uDDFC\uDEE1-\uDEFB]|\uD82F[\uDCA0-\uDCA3]|\uD834[\uDC00-\uDCF5\uDD00-\uDD26\uDD29-\uDD66\uDD6A-\uDD7A\uDD83\uDD84\uDD8C-\uDDA9\uDDAE-\uDDE8\uDF00-\uDF56\uDF60-\uDF71]|\uD835[\uDC00-\uDC54\uDC56-\uDC9C\uDC9E\uDC9F\uDCA2\uDCA5\uDCA6\uDCA9-\uDCAC\uDCAE-\uDCB9\uDCBB\uDCBD-\uDCC3\uDCC5-\uDD05\uDD07-\uDD0A\uDD0D-\uDD14\uDD16-\uDD1C\uDD1E-\uDD39\uDD3B-\uDD3E\uDD40-\uDD44\uDD46\uDD4A-\uDD50\uDD52-\uDEA5\uDEA8-\uDFCB\uDFCE-\uDFFF]|\uD83C[\uDC00-\uDC2B\uDC30-\uDC93\uDCA0-\uDCAE\uDCB1-\uDCBF\uDCC1-\uDCCF\uDCD1-\uDCF5\uDD00-\uDD0C\uDD10-\uDD2E\uDD30-\uDD6B\uDD70-\uDDAC\uDDE6-\uDDFF\uDE01\uDE02\uDE10-\uDE3B\uDE40-\uDE48\uDE50\uDE51\uDF00-\uDFFF]|\uD83D[\uDC00-\uDED2\uDEE0-\uDEEC\uDEF0-\uDEF6\uDF00-\uDF73\uDF80-\uDFD4]|\uD83E[\uDC00-\uDC0B\uDC10-\uDC47\uDC50-\uDC59\uDC60-\uDC87\uDC90-\uDCAD\uDD10-\uDD1E\uDD20-\uDD27\uDD30\uDD33-\uDD3E\uDD40-\uDD4B\uDD50-\uDD5E\uDD80-\uDD91\uDDC0]|\uDB40[\uDC01\uDC20-\uDC7F]'
    }, {
        name: 'Coptic',
        bmp: '\u03E2-\u03EF\u2C80-\u2CF3\u2CF9-\u2CFF'
    }, {
        name: 'Cuneiform',
        astral: '\uD808[\uDC00-\uDF99]|\uD809[\uDC00-\uDC6E\uDC70-\uDC74\uDC80-\uDD43]'
    }, {
        name: 'Cypriot',
        astral: '\uD802[\uDC00-\uDC05\uDC08\uDC0A-\uDC35\uDC37\uDC38\uDC3C\uDC3F]'
    }, {
        name: 'Cyrillic',
        bmp: '\u0400-\u0484\u0487-\u052F\u1C80-\u1C88\u1D2B\u1D78\u2DE0-\u2DFF\uA640-\uA69F\uFE2E\uFE2F'
    }, {
        name: 'Deseret',
        astral: '\uD801[\uDC00-\uDC4F]'
    }, {
        name: 'Devanagari',
        bmp: '\u0900-\u0950\u0953-\u0963\u0966-\u097F\uA8E0-\uA8FD'
    }, {
        name: 'Duployan',
        astral: '\uD82F[\uDC00-\uDC6A\uDC70-\uDC7C\uDC80-\uDC88\uDC90-\uDC99\uDC9C-\uDC9F]'
    }, {
        name: 'Egyptian_Hieroglyphs',
        astral: '\uD80C[\uDC00-\uDFFF]|\uD80D[\uDC00-\uDC2E]'
    }, {
        name: 'Elbasan',
        astral: '\uD801[\uDD00-\uDD27]'
    }, {
        name: 'Ethiopic',
        bmp: '\u1200-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u135D-\u137C\u1380-\u1399\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E'
    }, {
        name: 'Georgian',
        bmp: '\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u10FF\u2D00-\u2D25\u2D27\u2D2D'
    }, {
        name: 'Glagolitic',
        bmp: '\u2C00-\u2C2E\u2C30-\u2C5E',
        astral: '\uD838[\uDC00-\uDC06\uDC08-\uDC18\uDC1B-\uDC21\uDC23\uDC24\uDC26-\uDC2A]'
    }, {
        name: 'Gothic',
        astral: '\uD800[\uDF30-\uDF4A]'
    }, {
        name: 'Grantha',
        astral: '\uD804[\uDF00-\uDF03\uDF05-\uDF0C\uDF0F\uDF10\uDF13-\uDF28\uDF2A-\uDF30\uDF32\uDF33\uDF35-\uDF39\uDF3C-\uDF44\uDF47\uDF48\uDF4B-\uDF4D\uDF50\uDF57\uDF5D-\uDF63\uDF66-\uDF6C\uDF70-\uDF74]'
    }, {
        name: 'Greek',
        bmp: '\u0370-\u0373\u0375-\u0377\u037A-\u037D\u037F\u0384\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03E1\u03F0-\u03FF\u1D26-\u1D2A\u1D5D-\u1D61\u1D66-\u1D6A\u1DBF\u1F00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FC4\u1FC6-\u1FD3\u1FD6-\u1FDB\u1FDD-\u1FEF\u1FF2-\u1FF4\u1FF6-\u1FFE\u2126\uAB65',
        astral: '\uD800[\uDD40-\uDD8E\uDDA0]|\uD834[\uDE00-\uDE45]'
    }, {
        name: 'Gujarati',
        bmp: '\u0A81-\u0A83\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABC-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AD0\u0AE0-\u0AE3\u0AE6-\u0AF1\u0AF9'
    }, {
        name: 'Gurmukhi',
        bmp: '\u0A01-\u0A03\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A59-\u0A5C\u0A5E\u0A66-\u0A75'
    }, {
        name: 'Han',
        bmp: '\u2E80-\u2E99\u2E9B-\u2EF3\u2F00-\u2FD5\u3005\u3007\u3021-\u3029\u3038-\u303B\u3400-\u4DB5\u4E00-\u9FD5\uF900-\uFA6D\uFA70-\uFAD9',
        astral: '[\uD840-\uD868\uD86A-\uD86C\uD86F-\uD872][\uDC00-\uDFFF]|\uD869[\uDC00-\uDED6\uDF00-\uDFFF]|\uD86D[\uDC00-\uDF34\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1D\uDC20-\uDFFF]|\uD873[\uDC00-\uDEA1]|\uD87E[\uDC00-\uDE1D]'
    }, {
        name: 'Hangul',
        bmp: '\u1100-\u11FF\u302E\u302F\u3131-\u318E\u3200-\u321E\u3260-\u327E\uA960-\uA97C\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uFFA0-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC'
    }, {
        name: 'Hanunoo',
        bmp: '\u1720-\u1734'
    }, {
        name: 'Hatran',
        astral: '\uD802[\uDCE0-\uDCF2\uDCF4\uDCF5\uDCFB-\uDCFF]'
    }, {
        name: 'Hebrew',
        bmp: '\u0591-\u05C7\u05D0-\u05EA\u05F0-\u05F4\uFB1D-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFB4F'
    }, {
        name: 'Hiragana',
        bmp: '\u3041-\u3096\u309D-\u309F',
        astral: '\uD82C\uDC01|\uD83C\uDE00'
    }, {
        name: 'Imperial_Aramaic',
        astral: '\uD802[\uDC40-\uDC55\uDC57-\uDC5F]'
    }, {
        name: 'Inherited',
        bmp: '\u0300-\u036F\u0485\u0486\u064B-\u0655\u0670\u0951\u0952\u1AB0-\u1ABE\u1CD0-\u1CD2\u1CD4-\u1CE0\u1CE2-\u1CE8\u1CED\u1CF4\u1CF8\u1CF9\u1DC0-\u1DF5\u1DFB-\u1DFF\u200C\u200D\u20D0-\u20F0\u302A-\u302D\u3099\u309A\uFE00-\uFE0F\uFE20-\uFE2D',
        astral: '\uD800[\uDDFD\uDEE0]|\uD834[\uDD67-\uDD69\uDD7B-\uDD82\uDD85-\uDD8B\uDDAA-\uDDAD]|\uDB40[\uDD00-\uDDEF]'
    }, {
        name: 'Inscriptional_Pahlavi',
        astral: '\uD802[\uDF60-\uDF72\uDF78-\uDF7F]'
    }, {
        name: 'Inscriptional_Parthian',
        astral: '\uD802[\uDF40-\uDF55\uDF58-\uDF5F]'
    }, {
        name: 'Javanese',
        bmp: '\uA980-\uA9CD\uA9D0-\uA9D9\uA9DE\uA9DF'
    }, {
        name: 'Kaithi',
        astral: '\uD804[\uDC80-\uDCC1]'
    }, {
        name: 'Kannada',
        bmp: '\u0C80-\u0C83\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBC-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CDE\u0CE0-\u0CE3\u0CE6-\u0CEF\u0CF1\u0CF2'
    }, {
        name: 'Katakana',
        bmp: '\u30A1-\u30FA\u30FD-\u30FF\u31F0-\u31FF\u32D0-\u32FE\u3300-\u3357\uFF66-\uFF6F\uFF71-\uFF9D',
        astral: '\uD82C\uDC00'
    }, {
        name: 'Kayah_Li',
        bmp: '\uA900-\uA92D\uA92F'
    }, {
        name: 'Kharoshthi',
        astral: '\uD802[\uDE00-\uDE03\uDE05\uDE06\uDE0C-\uDE13\uDE15-\uDE17\uDE19-\uDE33\uDE38-\uDE3A\uDE3F-\uDE47\uDE50-\uDE58]'
    }, {
        name: 'Khmer',
        bmp: '\u1780-\u17DD\u17E0-\u17E9\u17F0-\u17F9\u19E0-\u19FF'
    }, {
        name: 'Khojki',
        astral: '\uD804[\uDE00-\uDE11\uDE13-\uDE3E]'
    }, {
        name: 'Khudawadi',
        astral: '\uD804[\uDEB0-\uDEEA\uDEF0-\uDEF9]'
    }, {
        name: 'Lao',
        bmp: '\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB9\u0EBB-\u0EBD\u0EC0-\u0EC4\u0EC6\u0EC8-\u0ECD\u0ED0-\u0ED9\u0EDC-\u0EDF'
    }, {
        name: 'Latin',
        bmp: 'A-Za-z\xAA\xBA\xC0-\xD6\xD8-\xF6\xF8-\u02B8\u02E0-\u02E4\u1D00-\u1D25\u1D2C-\u1D5C\u1D62-\u1D65\u1D6B-\u1D77\u1D79-\u1DBE\u1E00-\u1EFF\u2071\u207F\u2090-\u209C\u212A\u212B\u2132\u214E\u2160-\u2188\u2C60-\u2C7F\uA722-\uA787\uA78B-\uA7AE\uA7B0-\uA7B7\uA7F7-\uA7FF\uAB30-\uAB5A\uAB5C-\uAB64\uFB00-\uFB06\uFF21-\uFF3A\uFF41-\uFF5A'
    }, {
        name: 'Lepcha',
        bmp: '\u1C00-\u1C37\u1C3B-\u1C49\u1C4D-\u1C4F'
    }, {
        name: 'Limbu',
        bmp: '\u1900-\u191E\u1920-\u192B\u1930-\u193B\u1940\u1944-\u194F'
    }, {
        name: 'Linear_A',
        astral: '\uD801[\uDE00-\uDF36\uDF40-\uDF55\uDF60-\uDF67]'
    }, {
        name: 'Linear_B',
        astral: '\uD800[\uDC00-\uDC0B\uDC0D-\uDC26\uDC28-\uDC3A\uDC3C\uDC3D\uDC3F-\uDC4D\uDC50-\uDC5D\uDC80-\uDCFA]'
    }, {
        name: 'Lisu',
        bmp: '\uA4D0-\uA4FF'
    }, {
        name: 'Lycian',
        astral: '\uD800[\uDE80-\uDE9C]'
    }, {
        name: 'Lydian',
        astral: '\uD802[\uDD20-\uDD39\uDD3F]'
    }, {
        name: 'Mahajani',
        astral: '\uD804[\uDD50-\uDD76]'
    }, {
        name: 'Malayalam',
        bmp: '\u0D01-\u0D03\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D-\u0D44\u0D46-\u0D48\u0D4A-\u0D4F\u0D54-\u0D63\u0D66-\u0D7F'
    }, {
        name: 'Mandaic',
        bmp: '\u0840-\u085B\u085E'
    }, {
        name: 'Manichaean',
        astral: '\uD802[\uDEC0-\uDEE6\uDEEB-\uDEF6]'
    }, {
        name: 'Marchen',
        astral: '\uD807[\uDC70-\uDC8F\uDC92-\uDCA7\uDCA9-\uDCB6]'
    }, {
        name: 'Meetei_Mayek',
        bmp: '\uAAE0-\uAAF6\uABC0-\uABED\uABF0-\uABF9'
    }, {
        name: 'Mende_Kikakui',
        astral: '\uD83A[\uDC00-\uDCC4\uDCC7-\uDCD6]'
    }, {
        name: 'Meroitic_Cursive',
        astral: '\uD802[\uDDA0-\uDDB7\uDDBC-\uDDCF\uDDD2-\uDDFF]'
    }, {
        name: 'Meroitic_Hieroglyphs',
        astral: '\uD802[\uDD80-\uDD9F]'
    }, {
        name: 'Miao',
        astral: '\uD81B[\uDF00-\uDF44\uDF50-\uDF7E\uDF8F-\uDF9F]'
    }, {
        name: 'Modi',
        astral: '\uD805[\uDE00-\uDE44\uDE50-\uDE59]'
    }, {
        name: 'Mongolian',
        bmp: '\u1800\u1801\u1804\u1806-\u180E\u1810-\u1819\u1820-\u1877\u1880-\u18AA',
        astral: '\uD805[\uDE60-\uDE6C]'
    }, {
        name: 'Mro',
        astral: '\uD81A[\uDE40-\uDE5E\uDE60-\uDE69\uDE6E\uDE6F]'
    }, {
        name: 'Multani',
        astral: '\uD804[\uDE80-\uDE86\uDE88\uDE8A-\uDE8D\uDE8F-\uDE9D\uDE9F-\uDEA9]'
    }, {
        name: 'Myanmar',
        bmp: '\u1000-\u109F\uA9E0-\uA9FE\uAA60-\uAA7F'
    }, {
        name: 'Nabataean',
        astral: '\uD802[\uDC80-\uDC9E\uDCA7-\uDCAF]'
    }, {
        name: 'New_Tai_Lue',
        bmp: '\u1980-\u19AB\u19B0-\u19C9\u19D0-\u19DA\u19DE\u19DF'
    }, {
        name: 'Newa',
        astral: '\uD805[\uDC00-\uDC59\uDC5B\uDC5D]'
    }, {
        name: 'Nko',
        bmp: '\u07C0-\u07FA'
    }, {
        name: 'Ogham',
        bmp: '\u1680-\u169C'
    }, {
        name: 'Ol_Chiki',
        bmp: '\u1C50-\u1C7F'
    }, {
        name: 'Old_Hungarian',
        astral: '\uD803[\uDC80-\uDCB2\uDCC0-\uDCF2\uDCFA-\uDCFF]'
    }, {
        name: 'Old_Italic',
        astral: '\uD800[\uDF00-\uDF23]'
    }, {
        name: 'Old_North_Arabian',
        astral: '\uD802[\uDE80-\uDE9F]'
    }, {
        name: 'Old_Permic',
        astral: '\uD800[\uDF50-\uDF7A]'
    }, {
        name: 'Old_Persian',
        astral: '\uD800[\uDFA0-\uDFC3\uDFC8-\uDFD5]'
    }, {
        name: 'Old_South_Arabian',
        astral: '\uD802[\uDE60-\uDE7F]'
    }, {
        name: 'Old_Turkic',
        astral: '\uD803[\uDC00-\uDC48]'
    }, {
        name: 'Oriya',
        bmp: '\u0B01-\u0B03\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3C-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B5C\u0B5D\u0B5F-\u0B63\u0B66-\u0B77'
    }, {
        name: 'Osage',
        astral: '\uD801[\uDCB0-\uDCD3\uDCD8-\uDCFB]'
    }, {
        name: 'Osmanya',
        astral: '\uD801[\uDC80-\uDC9D\uDCA0-\uDCA9]'
    }, {
        name: 'Pahawh_Hmong',
        astral: '\uD81A[\uDF00-\uDF45\uDF50-\uDF59\uDF5B-\uDF61\uDF63-\uDF77\uDF7D-\uDF8F]'
    }, {
        name: 'Palmyrene',
        astral: '\uD802[\uDC60-\uDC7F]'
    }, {
        name: 'Pau_Cin_Hau',
        astral: '\uD806[\uDEC0-\uDEF8]'
    }, {
        name: 'Phags_Pa',
        bmp: '\uA840-\uA877'
    }, {
        name: 'Phoenician',
        astral: '\uD802[\uDD00-\uDD1B\uDD1F]'
    }, {
        name: 'Psalter_Pahlavi',
        astral: '\uD802[\uDF80-\uDF91\uDF99-\uDF9C\uDFA9-\uDFAF]'
    }, {
        name: 'Rejang',
        bmp: '\uA930-\uA953\uA95F'
    }, {
        name: 'Runic',
        bmp: '\u16A0-\u16EA\u16EE-\u16F8'
    }, {
        name: 'Samaritan',
        bmp: '\u0800-\u082D\u0830-\u083E'
    }, {
        name: 'Saurashtra',
        bmp: '\uA880-\uA8C5\uA8CE-\uA8D9'
    }, {
        name: 'Sharada',
        astral: '\uD804[\uDD80-\uDDCD\uDDD0-\uDDDF]'
    }, {
        name: 'Shavian',
        astral: '\uD801[\uDC50-\uDC7F]'
    }, {
        name: 'Siddham',
        astral: '\uD805[\uDD80-\uDDB5\uDDB8-\uDDDD]'
    }, {
        name: 'SignWriting',
        astral: '\uD836[\uDC00-\uDE8B\uDE9B-\uDE9F\uDEA1-\uDEAF]'
    }, {
        name: 'Sinhala',
        bmp: '\u0D82\u0D83\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DE6-\u0DEF\u0DF2-\u0DF4',
        astral: '\uD804[\uDDE1-\uDDF4]'
    }, {
        name: 'Sora_Sompeng',
        astral: '\uD804[\uDCD0-\uDCE8\uDCF0-\uDCF9]'
    }, {
        name: 'Sundanese',
        bmp: '\u1B80-\u1BBF\u1CC0-\u1CC7'
    }, {
        name: 'Syloti_Nagri',
        bmp: '\uA800-\uA82B'
    }, {
        name: 'Syriac',
        bmp: '\u0700-\u070D\u070F-\u074A\u074D-\u074F'
    }, {
        name: 'Tagalog',
        bmp: '\u1700-\u170C\u170E-\u1714'
    }, {
        name: 'Tagbanwa',
        bmp: '\u1760-\u176C\u176E-\u1770\u1772\u1773'
    }, {
        name: 'Tai_Le',
        bmp: '\u1950-\u196D\u1970-\u1974'
    }, {
        name: 'Tai_Tham',
        bmp: '\u1A20-\u1A5E\u1A60-\u1A7C\u1A7F-\u1A89\u1A90-\u1A99\u1AA0-\u1AAD'
    }, {
        name: 'Tai_Viet',
        bmp: '\uAA80-\uAAC2\uAADB-\uAADF'
    }, {
        name: 'Takri',
        astral: '\uD805[\uDE80-\uDEB7\uDEC0-\uDEC9]'
    }, {
        name: 'Tamil',
        bmp: '\u0B82\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD0\u0BD7\u0BE6-\u0BFA'
    }, {
        name: 'Tangut',
        astral: '\uD81B\uDFE0|[\uD81C-\uD820][\uDC00-\uDFFF]|\uD821[\uDC00-\uDFEC]|\uD822[\uDC00-\uDEF2]'
    }, {
        name: 'Telugu',
        bmp: '\u0C00-\u0C03\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C58-\u0C5A\u0C60-\u0C63\u0C66-\u0C6F\u0C78-\u0C7F'
    }, {
        name: 'Thaana',
        bmp: '\u0780-\u07B1'
    }, {
        name: 'Thai',
        bmp: '\u0E01-\u0E3A\u0E40-\u0E5B'
    }, {
        name: 'Tibetan',
        bmp: '\u0F00-\u0F47\u0F49-\u0F6C\u0F71-\u0F97\u0F99-\u0FBC\u0FBE-\u0FCC\u0FCE-\u0FD4\u0FD9\u0FDA'
    }, {
        name: 'Tifinagh',
        bmp: '\u2D30-\u2D67\u2D6F\u2D70\u2D7F'
    }, {
        name: 'Tirhuta',
        astral: '\uD805[\uDC80-\uDCC7\uDCD0-\uDCD9]'
    }, {
        name: 'Ugaritic',
        astral: '\uD800[\uDF80-\uDF9D\uDF9F]'
    }, {
        name: 'Vai',
        bmp: '\uA500-\uA62B'
    }, {
        name: 'Warang_Citi',
        astral: '\uD806[\uDCA0-\uDCF2\uDCFF]'
    }, {
        name: 'Yi',
        bmp: '\uA000-\uA48C\uA490-\uA4C6'
    }]);
};

module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/index.js":
/*!*******************************************!*\
  !*** ./node_modules/xregexp/lib/index.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _xregexp = __webpack_require__(/*! ./xregexp */ "./node_modules/xregexp/lib/xregexp.js");

var _xregexp2 = _interopRequireDefault(_xregexp);

var _build = __webpack_require__(/*! ./addons/build */ "./node_modules/xregexp/lib/addons/build.js");

var _build2 = _interopRequireDefault(_build);

var _matchrecursive = __webpack_require__(/*! ./addons/matchrecursive */ "./node_modules/xregexp/lib/addons/matchrecursive.js");

var _matchrecursive2 = _interopRequireDefault(_matchrecursive);

var _unicodeBase = __webpack_require__(/*! ./addons/unicode-base */ "./node_modules/xregexp/lib/addons/unicode-base.js");

var _unicodeBase2 = _interopRequireDefault(_unicodeBase);

var _unicodeBlocks = __webpack_require__(/*! ./addons/unicode-blocks */ "./node_modules/xregexp/lib/addons/unicode-blocks.js");

var _unicodeBlocks2 = _interopRequireDefault(_unicodeBlocks);

var _unicodeCategories = __webpack_require__(/*! ./addons/unicode-categories */ "./node_modules/xregexp/lib/addons/unicode-categories.js");

var _unicodeCategories2 = _interopRequireDefault(_unicodeCategories);

var _unicodeProperties = __webpack_require__(/*! ./addons/unicode-properties */ "./node_modules/xregexp/lib/addons/unicode-properties.js");

var _unicodeProperties2 = _interopRequireDefault(_unicodeProperties);

var _unicodeScripts = __webpack_require__(/*! ./addons/unicode-scripts */ "./node_modules/xregexp/lib/addons/unicode-scripts.js");

var _unicodeScripts2 = _interopRequireDefault(_unicodeScripts);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

(0, _build2.default)(_xregexp2.default);
(0, _matchrecursive2.default)(_xregexp2.default);
(0, _unicodeBase2.default)(_xregexp2.default);
(0, _unicodeBlocks2.default)(_xregexp2.default);
(0, _unicodeCategories2.default)(_xregexp2.default);
(0, _unicodeProperties2.default)(_xregexp2.default);
(0, _unicodeScripts2.default)(_xregexp2.default);

exports.default = _xregexp2.default;
module.exports = exports['default'];

/***/ }),

/***/ "./node_modules/xregexp/lib/xregexp.js":
/*!*********************************************!*\
  !*** ./node_modules/xregexp/lib/xregexp.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
/*!
 * XRegExp 4.0.0
 * <xregexp.com>
 * Steven Levithan (c) 2007-2017 MIT License
 */

/**
 * XRegExp provides augmented, extensible regular expressions. You get additional regex syntax and
 * flags, beyond what browsers support natively. XRegExp is also a regex utility belt with tools to
 * make your client-side grepping simpler and more powerful, while freeing you from related
 * cross-browser inconsistencies.
 */

// ==--------------------------==
// Private stuff
// ==--------------------------==

// Property name used for extended regex instance data
var REGEX_DATA = 'xregexp';
// Optional features that can be installed and uninstalled
var features = {
    astral: false
};
// Native methods to use and restore ('native' is an ES3 reserved keyword)
var nativ = {
    exec: RegExp.prototype.exec,
    test: RegExp.prototype.test,
    match: String.prototype.match,
    replace: String.prototype.replace,
    split: String.prototype.split
};
// Storage for fixed/extended native methods
var fixed = {};
// Storage for regexes cached by `XRegExp.cache`
var regexCache = {};
// Storage for pattern details cached by the `XRegExp` constructor
var patternCache = {};
// Storage for regex syntax tokens added internally or by `XRegExp.addToken`
var tokens = [];
// Token scopes
var defaultScope = 'default';
var classScope = 'class';
// Regexes that match native regex syntax, including octals
var nativeTokens = {
    // Any native multicharacter token in default scope, or any single character
    'default': /\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9]\d*|x[\dA-Fa-f]{2}|u(?:[\dA-Fa-f]{4}|{[\dA-Fa-f]+})|c[A-Za-z]|[\s\S])|\(\?(?:[:=!]|<[=!])|[?*+]\?|{\d+(?:,\d*)?}\??|[\s\S]/,
    // Any native multicharacter token in character class scope, or any single character
    'class': /\\(?:[0-3][0-7]{0,2}|[4-7][0-7]?|x[\dA-Fa-f]{2}|u(?:[\dA-Fa-f]{4}|{[\dA-Fa-f]+})|c[A-Za-z]|[\s\S])|[\s\S]/
};
// Any backreference or dollar-prefixed character in replacement strings
var replacementToken = /\$(?:{([\w$]+)}|<([\w$]+)>|(\d\d?|[\s\S]))/g;
// Check for correct `exec` handling of nonparticipating capturing groups
var correctExecNpcg = nativ.exec.call(/()??/, '')[1] === undefined;
// Check for ES6 `flags` prop support
var hasFlagsProp = /x/.flags !== undefined;
// Shortcut to `Object.prototype.toString`
var toString = {}.toString;

function hasNativeFlag(flag) {
    // Can't check based on the presence of properties/getters since browsers might support such
    // properties even when they don't support the corresponding flag in regex construction (tested
    // in Chrome 48, where `'unicode' in /x/` is true but trying to construct a regex with flag `u`
    // throws an error)
    var isSupported = true;
    try {
        // Can't use regex literals for testing even in a `try` because regex literals with
        // unsupported flags cause a compilation error in IE
        new RegExp('', flag);
    } catch (exception) {
        isSupported = false;
    }
    return isSupported;
}
// Check for ES6 `u` flag support
var hasNativeU = hasNativeFlag('u');
// Check for ES6 `y` flag support
var hasNativeY = hasNativeFlag('y');
// Tracker for known flags, including addon flags
var registeredFlags = {
    g: true,
    i: true,
    m: true,
    u: hasNativeU,
    y: hasNativeY
};

/**
 * Attaches extended data and `XRegExp.prototype` properties to a regex object.
 *
 * @private
 * @param {RegExp} regex Regex to augment.
 * @param {Array} captureNames Array with capture names, or `null`.
 * @param {String} xSource XRegExp pattern used to generate `regex`, or `null` if N/A.
 * @param {String} xFlags XRegExp flags used to generate `regex`, or `null` if N/A.
 * @param {Boolean} [isInternalOnly=false] Whether the regex will be used only for internal
 *   operations, and never exposed to users. For internal-only regexes, we can improve perf by
 *   skipping some operations like attaching `XRegExp.prototype` properties.
 * @returns {RegExp} Augmented regex.
 */
function augment(regex, captureNames, xSource, xFlags, isInternalOnly) {
    var p = void 0;

    regex[REGEX_DATA] = {
        captureNames: captureNames
    };

    if (isInternalOnly) {
        return regex;
    }

    // Can't auto-inherit these since the XRegExp constructor returns a nonprimitive value
    if (regex.__proto__) {
        regex.__proto__ = XRegExp.prototype;
    } else {
        for (p in XRegExp.prototype) {
            // An `XRegExp.prototype.hasOwnProperty(p)` check wouldn't be worth it here, since this
            // is performance sensitive, and enumerable `Object.prototype` or `RegExp.prototype`
            // extensions exist on `regex.prototype` anyway
            regex[p] = XRegExp.prototype[p];
        }
    }

    regex[REGEX_DATA].source = xSource;
    // Emulate the ES6 `flags` prop by ensuring flags are in alphabetical order
    regex[REGEX_DATA].flags = xFlags ? xFlags.split('').sort().join('') : xFlags;

    return regex;
}

/**
 * Removes any duplicate characters from the provided string.
 *
 * @private
 * @param {String} str String to remove duplicate characters from.
 * @returns {String} String with any duplicate characters removed.
 */
function clipDuplicates(str) {
    return nativ.replace.call(str, /([\s\S])(?=[\s\S]*\1)/g, '');
}

/**
 * Copies a regex object while preserving extended data and augmenting with `XRegExp.prototype`
 * properties. The copy has a fresh `lastIndex` property (set to zero). Allows adding and removing
 * flags g and y while copying the regex.
 *
 * @private
 * @param {RegExp} regex Regex to copy.
 * @param {Object} [options] Options object with optional properties:
 *   - `addG` {Boolean} Add flag g while copying the regex.
 *   - `addY` {Boolean} Add flag y while copying the regex.
 *   - `removeG` {Boolean} Remove flag g while copying the regex.
 *   - `removeY` {Boolean} Remove flag y while copying the regex.
 *   - `isInternalOnly` {Boolean} Whether the copied regex will be used only for internal
 *     operations, and never exposed to users. For internal-only regexes, we can improve perf by
 *     skipping some operations like attaching `XRegExp.prototype` properties.
 *   - `source` {String} Overrides `<regex>.source`, for special cases.
 * @returns {RegExp} Copy of the provided regex, possibly with modified flags.
 */
function copyRegex(regex, options) {
    if (!XRegExp.isRegExp(regex)) {
        throw new TypeError('Type RegExp expected');
    }

    var xData = regex[REGEX_DATA] || {};
    var flags = getNativeFlags(regex);
    var flagsToAdd = '';
    var flagsToRemove = '';
    var xregexpSource = null;
    var xregexpFlags = null;

    options = options || {};

    if (options.removeG) {
        flagsToRemove += 'g';
    }
    if (options.removeY) {
        flagsToRemove += 'y';
    }
    if (flagsToRemove) {
        flags = nativ.replace.call(flags, new RegExp('[' + flagsToRemove + ']+', 'g'), '');
    }

    if (options.addG) {
        flagsToAdd += 'g';
    }
    if (options.addY) {
        flagsToAdd += 'y';
    }
    if (flagsToAdd) {
        flags = clipDuplicates(flags + flagsToAdd);
    }

    if (!options.isInternalOnly) {
        if (xData.source !== undefined) {
            xregexpSource = xData.source;
        }
        // null or undefined; don't want to add to `flags` if the previous value was null, since
        // that indicates we're not tracking original precompilation flags
        if (xData.flags != null) {
            // Flags are only added for non-internal regexes by `XRegExp.globalize`. Flags are never
            // removed for non-internal regexes, so don't need to handle it
            xregexpFlags = flagsToAdd ? clipDuplicates(xData.flags + flagsToAdd) : xData.flags;
        }
    }

    // Augment with `XRegExp.prototype` properties, but use the native `RegExp` constructor to avoid
    // searching for special tokens. That would be wrong for regexes constructed by `RegExp`, and
    // unnecessary for regexes constructed by `XRegExp` because the regex has already undergone the
    // translation to native regex syntax
    regex = augment(new RegExp(options.source || regex.source, flags), hasNamedCapture(regex) ? xData.captureNames.slice(0) : null, xregexpSource, xregexpFlags, options.isInternalOnly);

    return regex;
}

/**
 * Converts hexadecimal to decimal.
 *
 * @private
 * @param {String} hex
 * @returns {Number}
 */
function dec(hex) {
    return parseInt(hex, 16);
}

/**
 * Returns a pattern that can be used in a native RegExp in place of an ignorable token such as an
 * inline comment or whitespace with flag x. This is used directly as a token handler function
 * passed to `XRegExp.addToken`.
 *
 * @private
 * @param {String} match Match arg of `XRegExp.addToken` handler
 * @param {String} scope Scope arg of `XRegExp.addToken` handler
 * @param {String} flags Flags arg of `XRegExp.addToken` handler
 * @returns {String} Either '' or '(?:)', depending on which is needed in the context of the match.
 */
function getContextualTokenSeparator(match, scope, flags) {
    if (
    // No need to separate tokens if at the beginning or end of a group
    match.input[match.index - 1] === '(' || match.input[match.index + match[0].length] === ')' ||
    // Avoid separating tokens when the following token is a quantifier
    isQuantifierNext(match.input, match.index + match[0].length, flags)) {
        return '';
    }
    // Keep tokens separated. This avoids e.g. inadvertedly changing `\1 1` or `\1(?#)1` to `\11`.
    // This also ensures all tokens remain as discrete atoms, e.g. it avoids converting the syntax
    // error `(? :` into `(?:`.
    return '(?:)';
}

/**
 * Returns native `RegExp` flags used by a regex object.
 *
 * @private
 * @param {RegExp} regex Regex to check.
 * @returns {String} Native flags in use.
 */
function getNativeFlags(regex) {
    return hasFlagsProp ? regex.flags :
    // Explicitly using `RegExp.prototype.toString` (rather than e.g. `String` or concatenation
    // with an empty string) allows this to continue working predictably when
    // `XRegExp.proptotype.toString` is overridden
    nativ.exec.call(/\/([a-z]*)$/i, RegExp.prototype.toString.call(regex))[1];
}

/**
 * Determines whether a regex has extended instance data used to track capture names.
 *
 * @private
 * @param {RegExp} regex Regex to check.
 * @returns {Boolean} Whether the regex uses named capture.
 */
function hasNamedCapture(regex) {
    return !!(regex[REGEX_DATA] && regex[REGEX_DATA].captureNames);
}

/**
 * Converts decimal to hexadecimal.
 *
 * @private
 * @param {Number|String} dec
 * @returns {String}
 */
function hex(dec) {
    return parseInt(dec, 10).toString(16);
}

/**
 * Checks whether the next nonignorable token after the specified position is a quantifier.
 *
 * @private
 * @param {String} pattern Pattern to search within.
 * @param {Number} pos Index in `pattern` to search at.
 * @param {String} flags Flags used by the pattern.
 * @returns {Boolean} Whether the next nonignorable token is a quantifier.
 */
function isQuantifierNext(pattern, pos, flags) {
    var inlineCommentPattern = '\\(\\?#[^)]*\\)';
    var lineCommentPattern = '#[^#\\n]*';
    var quantifierPattern = '[?*+]|{\\d+(?:,\\d*)?}';
    return nativ.test.call(flags.indexOf('x') !== -1 ?
    // Ignore any leading whitespace, line comments, and inline comments
    /^(?:\s|#[^#\n]*|\(\?#[^)]*\))*(?:[?*+]|{\d+(?:,\d*)?})/ :
    // Ignore any leading inline comments
    /^(?:\(\?#[^)]*\))*(?:[?*+]|{\d+(?:,\d*)?})/, pattern.slice(pos));
}

/**
 * Determines whether a value is of the specified type, by resolving its internal [[Class]].
 *
 * @private
 * @param {*} value Object to check.
 * @param {String} type Type to check for, in TitleCase.
 * @returns {Boolean} Whether the object matches the type.
 */
function isType(value, type) {
    return toString.call(value) === '[object ' + type + ']';
}

/**
 * Adds leading zeros if shorter than four characters. Used for fixed-length hexadecimal values.
 *
 * @private
 * @param {String} str
 * @returns {String}
 */
function pad4(str) {
    while (str.length < 4) {
        str = '0' + str;
    }
    return str;
}

/**
 * Checks for flag-related errors, and strips/applies flags in a leading mode modifier. Offloads
 * the flag preparation logic from the `XRegExp` constructor.
 *
 * @private
 * @param {String} pattern Regex pattern, possibly with a leading mode modifier.
 * @param {String} flags Any combination of flags.
 * @returns {Object} Object with properties `pattern` and `flags`.
 */
function prepareFlags(pattern, flags) {
    var i = void 0;

    // Recent browsers throw on duplicate flags, so copy this behavior for nonnative flags
    if (clipDuplicates(flags) !== flags) {
        throw new SyntaxError('Invalid duplicate regex flag ' + flags);
    }

    // Strip and apply a leading mode modifier with any combination of flags except g or y
    pattern = nativ.replace.call(pattern, /^\(\?([\w$]+)\)/, function ($0, $1) {
        if (nativ.test.call(/[gy]/, $1)) {
            throw new SyntaxError('Cannot use flag g or y in mode modifier ' + $0);
        }
        // Allow duplicate flags within the mode modifier
        flags = clipDuplicates(flags + $1);
        return '';
    });

    // Throw on unknown native or nonnative flags
    for (i = 0; i < flags.length; ++i) {
        if (!registeredFlags[flags[i]]) {
            throw new SyntaxError('Unknown regex flag ' + flags[i]);
        }
    }

    return {
        pattern: pattern,
        flags: flags
    };
}

/**
 * Prepares an options object from the given value.
 *
 * @private
 * @param {String|Object} value Value to convert to an options object.
 * @returns {Object} Options object.
 */
function prepareOptions(value) {
    var options = {};

    if (isType(value, 'String')) {
        XRegExp.forEach(value, /[^\s,]+/, function (match) {
            options[match] = true;
        });

        return options;
    }

    return value;
}

/**
 * Registers a flag so it doesn't throw an 'unknown flag' error.
 *
 * @private
 * @param {String} flag Single-character flag to register.
 */
function registerFlag(flag) {
    if (!/^[\w$]$/.test(flag)) {
        throw new Error('Flag must be a single character A-Za-z0-9_$');
    }

    registeredFlags[flag] = true;
}

/**
 * Runs built-in and custom regex syntax tokens in reverse insertion order at the specified
 * position, until a match is found.
 *
 * @private
 * @param {String} pattern Original pattern from which an XRegExp object is being built.
 * @param {String} flags Flags being used to construct the regex.
 * @param {Number} pos Position to search for tokens within `pattern`.
 * @param {Number} scope Regex scope to apply: 'default' or 'class'.
 * @param {Object} context Context object to use for token handler functions.
 * @returns {Object} Object with properties `matchLength`, `output`, and `reparse`; or `null`.
 */
function runTokens(pattern, flags, pos, scope, context) {
    var i = tokens.length;
    var leadChar = pattern[pos];
    var result = null;
    var match = void 0;
    var t = void 0;

    // Run in reverse insertion order
    while (i--) {
        t = tokens[i];
        if (t.leadChar && t.leadChar !== leadChar || t.scope !== scope && t.scope !== 'all' || t.flag && !(flags.indexOf(t.flag) !== -1)) {
            continue;
        }

        match = XRegExp.exec(pattern, t.regex, pos, 'sticky');
        if (match) {
            result = {
                matchLength: match[0].length,
                output: t.handler.call(context, match, scope, flags),
                reparse: t.reparse
            };
            // Finished with token tests
            break;
        }
    }

    return result;
}

/**
 * Enables or disables implicit astral mode opt-in. When enabled, flag A is automatically added to
 * all new regexes created by XRegExp. This causes an error to be thrown when creating regexes if
 * the Unicode Base addon is not available, since flag A is registered by that addon.
 *
 * @private
 * @param {Boolean} on `true` to enable; `false` to disable.
 */
function setAstral(on) {
    features.astral = on;
}

/**
 * Returns the object, or throws an error if it is `null` or `undefined`. This is used to follow
 * the ES5 abstract operation `ToObject`.
 *
 * @private
 * @param {*} value Object to check and return.
 * @returns {*} The provided object.
 */
function toObject(value) {
    // null or undefined
    if (value == null) {
        throw new TypeError('Cannot convert null or undefined to object');
    }

    return value;
}

// ==--------------------------==
// Constructor
// ==--------------------------==

/**
 * Creates an extended regular expression object for matching text with a pattern. Differs from a
 * native regular expression in that additional syntax and flags are supported. The returned object
 * is in fact a native `RegExp` and works with all native methods.
 *
 * @class XRegExp
 * @constructor
 * @param {String|RegExp} pattern Regex pattern string, or an existing regex object to copy.
 * @param {String} [flags] Any combination of flags.
 *   Native flags:
 *     - `g` - global
 *     - `i` - ignore case
 *     - `m` - multiline anchors
 *     - `u` - unicode (ES6)
 *     - `y` - sticky (Firefox 3+, ES6)
 *   Additional XRegExp flags:
 *     - `n` - explicit capture
 *     - `s` - dot matches all (aka singleline)
 *     - `x` - free-spacing and line comments (aka extended)
 *     - `A` - astral (requires the Unicode Base addon)
 *   Flags cannot be provided when constructing one `RegExp` from another.
 * @returns {RegExp} Extended regular expression object.
 * @example
 *
 * // With named capture and flag x
 * XRegExp(`(?<year>  [0-9]{4} ) -?  # year
 *          (?<month> [0-9]{2} ) -?  # month
 *          (?<day>   [0-9]{2} )     # day`, 'x');
 *
 * // Providing a regex object copies it. Native regexes are recompiled using native (not XRegExp)
 * // syntax. Copies maintain extended data, are augmented with `XRegExp.prototype` properties, and
 * // have fresh `lastIndex` properties (set to zero).
 * XRegExp(/regex/);
 */
function XRegExp(pattern, flags) {
    if (XRegExp.isRegExp(pattern)) {
        if (flags !== undefined) {
            throw new TypeError('Cannot supply flags when copying a RegExp');
        }
        return copyRegex(pattern);
    }

    // Copy the argument behavior of `RegExp`
    pattern = pattern === undefined ? '' : String(pattern);
    flags = flags === undefined ? '' : String(flags);

    if (XRegExp.isInstalled('astral') && !(flags.indexOf('A') !== -1)) {
        // This causes an error to be thrown if the Unicode Base addon is not available
        flags += 'A';
    }

    if (!patternCache[pattern]) {
        patternCache[pattern] = {};
    }

    if (!patternCache[pattern][flags]) {
        var context = {
            hasNamedCapture: false,
            captureNames: []
        };
        var scope = defaultScope;
        var output = '';
        var pos = 0;
        var result = void 0;

        // Check for flag-related errors, and strip/apply flags in a leading mode modifier
        var applied = prepareFlags(pattern, flags);
        var appliedPattern = applied.pattern;
        var appliedFlags = applied.flags;

        // Use XRegExp's tokens to translate the pattern to a native regex pattern.
        // `appliedPattern.length` may change on each iteration if tokens use `reparse`
        while (pos < appliedPattern.length) {
            do {
                // Check for custom tokens at the current position
                result = runTokens(appliedPattern, appliedFlags, pos, scope, context);
                // If the matched token used the `reparse` option, splice its output into the
                // pattern before running tokens again at the same position
                if (result && result.reparse) {
                    appliedPattern = appliedPattern.slice(0, pos) + result.output + appliedPattern.slice(pos + result.matchLength);
                }
            } while (result && result.reparse);

            if (result) {
                output += result.output;
                pos += result.matchLength || 1;
            } else {
                // Get the native token at the current position
                var token = XRegExp.exec(appliedPattern, nativeTokens[scope], pos, 'sticky')[0];
                output += token;
                pos += token.length;
                if (token === '[' && scope === defaultScope) {
                    scope = classScope;
                } else if (token === ']' && scope === classScope) {
                    scope = defaultScope;
                }
            }
        }

        patternCache[pattern][flags] = {
            // Use basic cleanup to collapse repeated empty groups like `(?:)(?:)` to `(?:)`. Empty
            // groups are sometimes inserted during regex transpilation in order to keep tokens
            // separated. However, more than one empty group in a row is never needed.
            pattern: nativ.replace.call(output, /(?:\(\?:\))+/g, '(?:)'),
            // Strip all but native flags
            flags: nativ.replace.call(appliedFlags, /[^gimuy]+/g, ''),
            // `context.captureNames` has an item for each capturing group, even if unnamed
            captures: context.hasNamedCapture ? context.captureNames : null
        };
    }

    var generated = patternCache[pattern][flags];
    return augment(new RegExp(generated.pattern, generated.flags), generated.captures, pattern, flags);
}

// Add `RegExp.prototype` to the prototype chain
XRegExp.prototype = /(?:)/;

// ==--------------------------==
// Public properties
// ==--------------------------==

/**
 * The XRegExp version number as a string containing three dot-separated parts. For example,
 * '2.0.0-beta-3'.
 *
 * @static
 * @memberOf XRegExp
 * @type String
 */
XRegExp.version = '4.0.0';

// ==--------------------------==
// Public methods
// ==--------------------------==

// Intentionally undocumented; used in tests and addons
XRegExp._clipDuplicates = clipDuplicates;
XRegExp._hasNativeFlag = hasNativeFlag;
XRegExp._dec = dec;
XRegExp._hex = hex;
XRegExp._pad4 = pad4;

/**
 * Extends XRegExp syntax and allows custom flags. This is used internally and can be used to
 * create XRegExp addons. If more than one token can match the same string, the last added wins.
 *
 * @memberOf XRegExp
 * @param {RegExp} regex Regex object that matches the new token.
 * @param {Function} handler Function that returns a new pattern string (using native regex syntax)
 *   to replace the matched token within all future XRegExp regexes. Has access to persistent
 *   properties of the regex being built, through `this`. Invoked with three arguments:
 *   - The match array, with named backreference properties.
 *   - The regex scope where the match was found: 'default' or 'class'.
 *   - The flags used by the regex, including any flags in a leading mode modifier.
 *   The handler function becomes part of the XRegExp construction process, so be careful not to
 *   construct XRegExps within the function or you will trigger infinite recursion.
 * @param {Object} [options] Options object with optional properties:
 *   - `scope` {String} Scope where the token applies: 'default', 'class', or 'all'.
 *   - `flag` {String} Single-character flag that triggers the token. This also registers the
 *     flag, which prevents XRegExp from throwing an 'unknown flag' error when the flag is used.
 *   - `optionalFlags` {String} Any custom flags checked for within the token `handler` that are
 *     not required to trigger the token. This registers the flags, to prevent XRegExp from
 *     throwing an 'unknown flag' error when any of the flags are used.
 *   - `reparse` {Boolean} Whether the `handler` function's output should not be treated as
 *     final, and instead be reparseable by other tokens (including the current token). Allows
 *     token chaining or deferring.
 *   - `leadChar` {String} Single character that occurs at the beginning of any successful match
 *     of the token (not always applicable). This doesn't change the behavior of the token unless
 *     you provide an erroneous value. However, providing it can increase the token's performance
 *     since the token can be skipped at any positions where this character doesn't appear.
 * @example
 *
 * // Basic usage: Add \a for the ALERT control code
 * XRegExp.addToken(
 *   /\\a/,
 *   () => '\\x07',
 *   {scope: 'all'}
 * );
 * XRegExp('\\a[\\a-\\n]+').test('\x07\n\x07'); // -> true
 *
 * // Add the U (ungreedy) flag from PCRE and RE2, which reverses greedy and lazy quantifiers.
 * // Since `scope` is not specified, it uses 'default' (i.e., transformations apply outside of
 * // character classes only)
 * XRegExp.addToken(
 *   /([?*+]|{\d+(?:,\d*)?})(\??)/,
 *   (match) => `${match[1]}${match[2] ? '' : '?'}`,
 *   {flag: 'U'}
 * );
 * XRegExp('a+', 'U').exec('aaa')[0]; // -> 'a'
 * XRegExp('a+?', 'U').exec('aaa')[0]; // -> 'aaa'
 */
XRegExp.addToken = function (regex, handler, options) {
    options = options || {};
    var optionalFlags = options.optionalFlags;
    var i = void 0;

    if (options.flag) {
        registerFlag(options.flag);
    }

    if (optionalFlags) {
        optionalFlags = nativ.split.call(optionalFlags, '');
        for (i = 0; i < optionalFlags.length; ++i) {
            registerFlag(optionalFlags[i]);
        }
    }

    // Add to the private list of syntax tokens
    tokens.push({
        regex: copyRegex(regex, {
            addG: true,
            addY: hasNativeY,
            isInternalOnly: true
        }),
        handler: handler,
        scope: options.scope || defaultScope,
        flag: options.flag,
        reparse: options.reparse,
        leadChar: options.leadChar
    });

    // Reset the pattern cache used by the `XRegExp` constructor, since the same pattern and flags
    // might now produce different results
    XRegExp.cache.flush('patterns');
};

/**
 * Caches and returns the result of calling `XRegExp(pattern, flags)`. On any subsequent call with
 * the same pattern and flag combination, the cached copy of the regex is returned.
 *
 * @memberOf XRegExp
 * @param {String} pattern Regex pattern string.
 * @param {String} [flags] Any combination of XRegExp flags.
 * @returns {RegExp} Cached XRegExp object.
 * @example
 *
 * while (match = XRegExp.cache('.', 'gs').exec(str)) {
 *   // The regex is compiled once only
 * }
 */
XRegExp.cache = function (pattern, flags) {
    if (!regexCache[pattern]) {
        regexCache[pattern] = {};
    }
    return regexCache[pattern][flags] || (regexCache[pattern][flags] = XRegExp(pattern, flags));
};

// Intentionally undocumented; used in tests
XRegExp.cache.flush = function (cacheName) {
    if (cacheName === 'patterns') {
        // Flush the pattern cache used by the `XRegExp` constructor
        patternCache = {};
    } else {
        // Flush the regex cache populated by `XRegExp.cache`
        regexCache = {};
    }
};

/**
 * Escapes any regular expression metacharacters, for use when matching literal strings. The result
 * can safely be used at any point within a regex that uses any flags.
 *
 * @memberOf XRegExp
 * @param {String} str String to escape.
 * @returns {String} String with regex metacharacters escaped.
 * @example
 *
 * XRegExp.escape('Escaped? <.>');
 * // -> 'Escaped\?\ <\.>'
 */
XRegExp.escape = function (str) {
    return nativ.replace.call(toObject(str), /[-\[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
};

/**
 * Executes a regex search in a specified string. Returns a match array or `null`. If the provided
 * regex uses named capture, named backreference properties are included on the match array.
 * Optional `pos` and `sticky` arguments specify the search start position, and whether the match
 * must start at the specified position only. The `lastIndex` property of the provided regex is not
 * used, but is updated for compatibility. Also fixes browser bugs compared to the native
 * `RegExp.prototype.exec` and can be used reliably cross-browser.
 *
 * @memberOf XRegExp
 * @param {String} str String to search.
 * @param {RegExp} regex Regex to search with.
 * @param {Number} [pos=0] Zero-based index at which to start the search.
 * @param {Boolean|String} [sticky=false] Whether the match must start at the specified position
 *   only. The string `'sticky'` is accepted as an alternative to `true`.
 * @returns {Array} Match array with named backreference properties, or `null`.
 * @example
 *
 * // Basic use, with named backreference
 * let match = XRegExp.exec('U+2620', XRegExp('U\\+(?<hex>[0-9A-F]{4})'));
 * match.hex; // -> '2620'
 *
 * // With pos and sticky, in a loop
 * let pos = 2, result = [], match;
 * while (match = XRegExp.exec('<1><2><3><4>5<6>', /<(\d)>/, pos, 'sticky')) {
 *   result.push(match[1]);
 *   pos = match.index + match[0].length;
 * }
 * // result -> ['2', '3', '4']
 */
XRegExp.exec = function (str, regex, pos, sticky) {
    var cacheKey = 'g';
    var addY = false;
    var fakeY = false;
    var match = void 0;

    addY = hasNativeY && !!(sticky || regex.sticky && sticky !== false);
    if (addY) {
        cacheKey += 'y';
    } else if (sticky) {
        // Simulate sticky matching by appending an empty capture to the original regex. The
        // resulting regex will succeed no matter what at the current index (set with `lastIndex`),
        // and will not search the rest of the subject string. We'll know that the original regex
        // has failed if that last capture is `''` rather than `undefined` (i.e., if that last
        // capture participated in the match).
        fakeY = true;
        cacheKey += 'FakeY';
    }

    regex[REGEX_DATA] = regex[REGEX_DATA] || {};

    // Shares cached copies with `XRegExp.match`/`replace`
    var r2 = regex[REGEX_DATA][cacheKey] || (regex[REGEX_DATA][cacheKey] = copyRegex(regex, {
        addG: true,
        addY: addY,
        source: fakeY ? regex.source + '|()' : undefined,
        removeY: sticky === false,
        isInternalOnly: true
    }));

    pos = pos || 0;
    r2.lastIndex = pos;

    // Fixed `exec` required for `lastIndex` fix, named backreferences, etc.
    match = fixed.exec.call(r2, str);

    // Get rid of the capture added by the pseudo-sticky matcher if needed. An empty string means
    // the original regexp failed (see above).
    if (fakeY && match && match.pop() === '') {
        match = null;
    }

    if (regex.global) {
        regex.lastIndex = match ? r2.lastIndex : 0;
    }

    return match;
};

/**
 * Executes a provided function once per regex match. Searches always start at the beginning of the
 * string and continue until the end, regardless of the state of the regex's `global` property and
 * initial `lastIndex`.
 *
 * @memberOf XRegExp
 * @param {String} str String to search.
 * @param {RegExp} regex Regex to search with.
 * @param {Function} callback Function to execute for each match. Invoked with four arguments:
 *   - The match array, with named backreference properties.
 *   - The zero-based match index.
 *   - The string being traversed.
 *   - The regex object being used to traverse the string.
 * @example
 *
 * // Extracts every other digit from a string
 * const evens = [];
 * XRegExp.forEach('1a2345', /\d/, (match, i) => {
 *   if (i % 2) evens.push(+match[0]);
 * });
 * // evens -> [2, 4]
 */
XRegExp.forEach = function (str, regex, callback) {
    var pos = 0;
    var i = -1;
    var match = void 0;

    while (match = XRegExp.exec(str, regex, pos)) {
        // Because `regex` is provided to `callback`, the function could use the deprecated/
        // nonstandard `RegExp.prototype.compile` to mutate the regex. However, since `XRegExp.exec`
        // doesn't use `lastIndex` to set the search position, this can't lead to an infinite loop,
        // at least. Actually, because of the way `XRegExp.exec` caches globalized versions of
        // regexes, mutating the regex will not have any effect on the iteration or matched strings,
        // which is a nice side effect that brings extra safety.
        callback(match, ++i, str, regex);

        pos = match.index + (match[0].length || 1);
    }
};

/**
 * Copies a regex object and adds flag `g`. The copy maintains extended data, is augmented with
 * `XRegExp.prototype` properties, and has a fresh `lastIndex` property (set to zero). Native
 * regexes are not recompiled using XRegExp syntax.
 *
 * @memberOf XRegExp
 * @param {RegExp} regex Regex to globalize.
 * @returns {RegExp} Copy of the provided regex with flag `g` added.
 * @example
 *
 * const globalCopy = XRegExp.globalize(/regex/);
 * globalCopy.global; // -> true
 */
XRegExp.globalize = function (regex) {
    return copyRegex(regex, { addG: true });
};

/**
 * Installs optional features according to the specified options. Can be undone using
 * `XRegExp.uninstall`.
 *
 * @memberOf XRegExp
 * @param {Object|String} options Options object or string.
 * @example
 *
 * // With an options object
 * XRegExp.install({
 *   // Enables support for astral code points in Unicode addons (implicitly sets flag A)
 *   astral: true
 * });
 *
 * // With an options string
 * XRegExp.install('astral');
 */
XRegExp.install = function (options) {
    options = prepareOptions(options);

    if (!features.astral && options.astral) {
        setAstral(true);
    }
};

/**
 * Checks whether an individual optional feature is installed.
 *
 * @memberOf XRegExp
 * @param {String} feature Name of the feature to check. One of:
 *   - `astral`
 * @returns {Boolean} Whether the feature is installed.
 * @example
 *
 * XRegExp.isInstalled('astral');
 */
XRegExp.isInstalled = function (feature) {
    return !!features[feature];
};

/**
 * Returns `true` if an object is a regex; `false` if it isn't. This works correctly for regexes
 * created in another frame, when `instanceof` and `constructor` checks would fail.
 *
 * @memberOf XRegExp
 * @param {*} value Object to check.
 * @returns {Boolean} Whether the object is a `RegExp` object.
 * @example
 *
 * XRegExp.isRegExp('string'); // -> false
 * XRegExp.isRegExp(/regex/i); // -> true
 * XRegExp.isRegExp(RegExp('^', 'm')); // -> true
 * XRegExp.isRegExp(XRegExp('(?s).')); // -> true
 */
XRegExp.isRegExp = function (value) {
    return toString.call(value) === '[object RegExp]';
}; // isType(value, 'RegExp');

/**
 * Returns the first matched string, or in global mode, an array containing all matched strings.
 * This is essentially a more convenient re-implementation of `String.prototype.match` that gives
 * the result types you actually want (string instead of `exec`-style array in match-first mode,
 * and an empty array instead of `null` when no matches are found in match-all mode). It also lets
 * you override flag g and ignore `lastIndex`, and fixes browser bugs.
 *
 * @memberOf XRegExp
 * @param {String} str String to search.
 * @param {RegExp} regex Regex to search with.
 * @param {String} [scope='one'] Use 'one' to return the first match as a string. Use 'all' to
 *   return an array of all matched strings. If not explicitly specified and `regex` uses flag g,
 *   `scope` is 'all'.
 * @returns {String|Array} In match-first mode: First match as a string, or `null`. In match-all
 *   mode: Array of all matched strings, or an empty array.
 * @example
 *
 * // Match first
 * XRegExp.match('abc', /\w/); // -> 'a'
 * XRegExp.match('abc', /\w/g, 'one'); // -> 'a'
 * XRegExp.match('abc', /x/g, 'one'); // -> null
 *
 * // Match all
 * XRegExp.match('abc', /\w/g); // -> ['a', 'b', 'c']
 * XRegExp.match('abc', /\w/, 'all'); // -> ['a', 'b', 'c']
 * XRegExp.match('abc', /x/, 'all'); // -> []
 */
XRegExp.match = function (str, regex, scope) {
    var global = regex.global && scope !== 'one' || scope === 'all';
    var cacheKey = (global ? 'g' : '') + (regex.sticky ? 'y' : '') || 'noGY';

    regex[REGEX_DATA] = regex[REGEX_DATA] || {};

    // Shares cached copies with `XRegExp.exec`/`replace`
    var r2 = regex[REGEX_DATA][cacheKey] || (regex[REGEX_DATA][cacheKey] = copyRegex(regex, {
        addG: !!global,
        removeG: scope === 'one',
        isInternalOnly: true
    }));

    var result = nativ.match.call(toObject(str), r2);

    if (regex.global) {
        regex.lastIndex = scope === 'one' && result ?
        // Can't use `r2.lastIndex` since `r2` is nonglobal in this case
        result.index + result[0].length : 0;
    }

    return global ? result || [] : result && result[0];
};

/**
 * Retrieves the matches from searching a string using a chain of regexes that successively search
 * within previous matches. The provided `chain` array can contain regexes and or objects with
 * `regex` and `backref` properties. When a backreference is specified, the named or numbered
 * backreference is passed forward to the next regex or returned.
 *
 * @memberOf XRegExp
 * @param {String} str String to search.
 * @param {Array} chain Regexes that each search for matches within preceding results.
 * @returns {Array} Matches by the last regex in the chain, or an empty array.
 * @example
 *
 * // Basic usage; matches numbers within <b> tags
 * XRegExp.matchChain('1 <b>2</b> 3 <b>4 a 56</b>', [
 *   XRegExp('(?is)<b>.*?</b>'),
 *   /\d+/
 * ]);
 * // -> ['2', '4', '56']
 *
 * // Passing forward and returning specific backreferences
 * html = '<a href="http://xregexp.com/api/">XRegExp</a>\
 *         <a href="http://www.google.com/">Google</a>';
 * XRegExp.matchChain(html, [
 *   {regex: /<a href="([^"]+)">/i, backref: 1},
 *   {regex: XRegExp('(?i)^https?://(?<domain>[^/?#]+)'), backref: 'domain'}
 * ]);
 * // -> ['xregexp.com', 'www.google.com']
 */
XRegExp.matchChain = function (str, chain) {
    return function recurseChain(values, level) {
        var item = chain[level].regex ? chain[level] : { regex: chain[level] };
        var matches = [];

        function addMatch(match) {
            if (item.backref) {
                // Safari 4.0.5 (but not 5.0.5+) inappropriately uses sparse arrays to hold the
                // `undefined`s for backreferences to nonparticipating capturing groups. In such
                // cases, a `hasOwnProperty` or `in` check on its own would inappropriately throw
                // the exception, so also check if the backreference is a number that is within the
                // bounds of the array.
                if (!(match.hasOwnProperty(item.backref) || +item.backref < match.length)) {
                    throw new ReferenceError('Backreference to undefined group: ' + item.backref);
                }

                matches.push(match[item.backref] || '');
            } else {
                matches.push(match[0]);
            }
        }

        for (var i = 0; i < values.length; ++i) {
            XRegExp.forEach(values[i], item.regex, addMatch);
        }

        return level === chain.length - 1 || !matches.length ? matches : recurseChain(matches, level + 1);
    }([str], 0);
};

/**
 * Returns a new string with one or all matches of a pattern replaced. The pattern can be a string
 * or regex, and the replacement can be a string or a function to be called for each match. To
 * perform a global search and replace, use the optional `scope` argument or include flag g if using
 * a regex. Replacement strings can use `${n}` or `$<n>` for named and numbered backreferences.
 * Replacement functions can use named backreferences via `arguments[0].name`. Also fixes browser
 * bugs compared to the native `String.prototype.replace` and can be used reliably cross-browser.
 *
 * @memberOf XRegExp
 * @param {String} str String to search.
 * @param {RegExp|String} search Search pattern to be replaced.
 * @param {String|Function} replacement Replacement string or a function invoked to create it.
 *   Replacement strings can include special replacement syntax:
 *     - $$ - Inserts a literal $ character.
 *     - $&, $0 - Inserts the matched substring.
 *     - $` - Inserts the string that precedes the matched substring (left context).
 *     - $' - Inserts the string that follows the matched substring (right context).
 *     - $n, $nn - Where n/nn are digits referencing an existent capturing group, inserts
 *       backreference n/nn.
 *     - ${n}, $<n> - Where n is a name or any number of digits that reference an existent capturing
 *       group, inserts backreference n.
 *   Replacement functions are invoked with three or more arguments:
 *     - The matched substring (corresponds to $& above). Named backreferences are accessible as
 *       properties of this first argument.
 *     - 0..n arguments, one for each backreference (corresponding to $1, $2, etc. above).
 *     - The zero-based index of the match within the total search string.
 *     - The total string being searched.
 * @param {String} [scope='one'] Use 'one' to replace the first match only, or 'all'. If not
 *   explicitly specified and using a regex with flag g, `scope` is 'all'.
 * @returns {String} New string with one or all matches replaced.
 * @example
 *
 * // Regex search, using named backreferences in replacement string
 * const name = XRegExp('(?<first>\\w+) (?<last>\\w+)');
 * XRegExp.replace('John Smith', name, '$<last>, $<first>');
 * // -> 'Smith, John'
 *
 * // Regex search, using named backreferences in replacement function
 * XRegExp.replace('John Smith', name, (match) => `${match.last}, ${match.first}`);
 * // -> 'Smith, John'
 *
 * // String search, with replace-all
 * XRegExp.replace('RegExp builds RegExps', 'RegExp', 'XRegExp', 'all');
 * // -> 'XRegExp builds XRegExps'
 */
XRegExp.replace = function (str, search, replacement, scope) {
    var isRegex = XRegExp.isRegExp(search);
    var global = search.global && scope !== 'one' || scope === 'all';
    var cacheKey = (global ? 'g' : '') + (search.sticky ? 'y' : '') || 'noGY';
    var s2 = search;

    if (isRegex) {
        search[REGEX_DATA] = search[REGEX_DATA] || {};

        // Shares cached copies with `XRegExp.exec`/`match`. Since a copy is used, `search`'s
        // `lastIndex` isn't updated *during* replacement iterations
        s2 = search[REGEX_DATA][cacheKey] || (search[REGEX_DATA][cacheKey] = copyRegex(search, {
            addG: !!global,
            removeG: scope === 'one',
            isInternalOnly: true
        }));
    } else if (global) {
        s2 = new RegExp(XRegExp.escape(String(search)), 'g');
    }

    // Fixed `replace` required for named backreferences, etc.
    var result = fixed.replace.call(toObject(str), s2, replacement);

    if (isRegex && search.global) {
        // Fixes IE, Safari bug (last tested IE 9, Safari 5.1)
        search.lastIndex = 0;
    }

    return result;
};

/**
 * Performs batch processing of string replacements. Used like `XRegExp.replace`, but accepts an
 * array of replacement details. Later replacements operate on the output of earlier replacements.
 * Replacement details are accepted as an array with a regex or string to search for, the
 * replacement string or function, and an optional scope of 'one' or 'all'. Uses the XRegExp
 * replacement text syntax, which supports named backreference properties via `${name}` or
 * `$<name>`.
 *
 * @memberOf XRegExp
 * @param {String} str String to search.
 * @param {Array} replacements Array of replacement detail arrays.
 * @returns {String} New string with all replacements.
 * @example
 *
 * str = XRegExp.replaceEach(str, [
 *   [XRegExp('(?<name>a)'), 'z${name}'],
 *   [/b/gi, 'y'],
 *   [/c/g, 'x', 'one'], // scope 'one' overrides /g
 *   [/d/, 'w', 'all'],  // scope 'all' overrides lack of /g
 *   ['e', 'v', 'all'],  // scope 'all' allows replace-all for strings
 *   [/f/g, ($0) => $0.toUpperCase()]
 * ]);
 */
XRegExp.replaceEach = function (str, replacements) {
    var i = void 0;
    var r = void 0;

    for (i = 0; i < replacements.length; ++i) {
        r = replacements[i];
        str = XRegExp.replace(str, r[0], r[1], r[2]);
    }

    return str;
};

/**
 * Splits a string into an array of strings using a regex or string separator. Matches of the
 * separator are not included in the result array. However, if `separator` is a regex that contains
 * capturing groups, backreferences are spliced into the result each time `separator` is matched.
 * Fixes browser bugs compared to the native `String.prototype.split` and can be used reliably
 * cross-browser.
 *
 * @memberOf XRegExp
 * @param {String} str String to split.
 * @param {RegExp|String} separator Regex or string to use for separating the string.
 * @param {Number} [limit] Maximum number of items to include in the result array.
 * @returns {Array} Array of substrings.
 * @example
 *
 * // Basic use
 * XRegExp.split('a b c', ' ');
 * // -> ['a', 'b', 'c']
 *
 * // With limit
 * XRegExp.split('a b c', ' ', 2);
 * // -> ['a', 'b']
 *
 * // Backreferences in result array
 * XRegExp.split('..word1..', /([a-z]+)(\d+)/i);
 * // -> ['..', 'word', '1', '..']
 */
XRegExp.split = function (str, separator, limit) {
    return fixed.split.call(toObject(str), separator, limit);
};

/**
 * Executes a regex search in a specified string. Returns `true` or `false`. Optional `pos` and
 * `sticky` arguments specify the search start position, and whether the match must start at the
 * specified position only. The `lastIndex` property of the provided regex is not used, but is
 * updated for compatibility. Also fixes browser bugs compared to the native
 * `RegExp.prototype.test` and can be used reliably cross-browser.
 *
 * @memberOf XRegExp
 * @param {String} str String to search.
 * @param {RegExp} regex Regex to search with.
 * @param {Number} [pos=0] Zero-based index at which to start the search.
 * @param {Boolean|String} [sticky=false] Whether the match must start at the specified position
 *   only. The string `'sticky'` is accepted as an alternative to `true`.
 * @returns {Boolean} Whether the regex matched the provided value.
 * @example
 *
 * // Basic use
 * XRegExp.test('abc', /c/); // -> true
 *
 * // With pos and sticky
 * XRegExp.test('abc', /c/, 0, 'sticky'); // -> false
 * XRegExp.test('abc', /c/, 2, 'sticky'); // -> true
 */
// Do this the easy way :-)
XRegExp.test = function (str, regex, pos, sticky) {
    return !!XRegExp.exec(str, regex, pos, sticky);
};

/**
 * Uninstalls optional features according to the specified options. All optional features start out
 * uninstalled, so this is used to undo the actions of `XRegExp.install`.
 *
 * @memberOf XRegExp
 * @param {Object|String} options Options object or string.
 * @example
 *
 * // With an options object
 * XRegExp.uninstall({
 *   // Disables support for astral code points in Unicode addons
 *   astral: true
 * });
 *
 * // With an options string
 * XRegExp.uninstall('astral');
 */
XRegExp.uninstall = function (options) {
    options = prepareOptions(options);

    if (features.astral && options.astral) {
        setAstral(false);
    }
};

/**
 * Returns an XRegExp object that is the union of the given patterns. Patterns can be provided as
 * regex objects or strings. Metacharacters are escaped in patterns provided as strings.
 * Backreferences in provided regex objects are automatically renumbered to work correctly within
 * the larger combined pattern. Native flags used by provided regexes are ignored in favor of the
 * `flags` argument.
 *
 * @memberOf XRegExp
 * @param {Array} patterns Regexes and strings to combine.
 * @param {String} [flags] Any combination of XRegExp flags.
 * @param {Object} [options] Options object with optional properties:
 *   - `conjunction` {String} Type of conjunction to use: 'or' (default) or 'none'.
 * @returns {RegExp} Union of the provided regexes and strings.
 * @example
 *
 * XRegExp.union(['a+b*c', /(dogs)\1/, /(cats)\1/], 'i');
 * // -> /a\+b\*c|(dogs)\1|(cats)\2/i
 *
 * XRegExp.union([/man/, /bear/, /pig/], 'i', {conjunction: 'none'});
 * // -> /manbearpig/i
 */
XRegExp.union = function (patterns, flags, options) {
    options = options || {};
    var conjunction = options.conjunction || 'or';
    var numCaptures = 0;
    var numPriorCaptures = void 0;
    var captureNames = void 0;

    function rewrite(match, paren, backref) {
        var name = captureNames[numCaptures - numPriorCaptures];

        // Capturing group
        if (paren) {
            ++numCaptures;
            // If the current capture has a name, preserve the name
            if (name) {
                return '(?<' + name + '>';
            }
            // Backreference
        } else if (backref) {
            // Rewrite the backreference
            return '\\' + (+backref + numPriorCaptures);
        }

        return match;
    }

    if (!(isType(patterns, 'Array') && patterns.length)) {
        throw new TypeError('Must provide a nonempty array of patterns to merge');
    }

    var parts = /(\()(?!\?)|\\([1-9]\d*)|\\[\s\S]|\[(?:[^\\\]]|\\[\s\S])*\]/g;
    var output = [];
    var pattern = void 0;
    for (var i = 0; i < patterns.length; ++i) {
        pattern = patterns[i];

        if (XRegExp.isRegExp(pattern)) {
            numPriorCaptures = numCaptures;
            captureNames = pattern[REGEX_DATA] && pattern[REGEX_DATA].captureNames || [];

            // Rewrite backreferences. Passing to XRegExp dies on octals and ensures patterns are
            // independently valid; helps keep this simple. Named captures are put back
            output.push(nativ.replace.call(XRegExp(pattern.source).source, parts, rewrite));
        } else {
            output.push(XRegExp.escape(pattern));
        }
    }

    var separator = conjunction === 'none' ? '' : '|';
    return XRegExp(output.join(separator), flags);
};

// ==--------------------------==
// Fixed/extended native methods
// ==--------------------------==

/**
 * Adds named capture support (with backreferences returned as `result.name`), and fixes browser
 * bugs in the native `RegExp.prototype.exec`. Use via `XRegExp.exec`.
 *
 * @memberOf RegExp
 * @param {String} str String to search.
 * @returns {Array} Match array with named backreference properties, or `null`.
 */
fixed.exec = function (str) {
    var origLastIndex = this.lastIndex;
    var match = nativ.exec.apply(this, arguments);

    if (match) {
        // Fix browsers whose `exec` methods don't return `undefined` for nonparticipating capturing
        // groups. This fixes IE 5.5-8, but not IE 9's quirks mode or emulation of older IEs. IE 9
        // in standards mode follows the spec.
        if (!correctExecNpcg && match.length > 1 && match.indexOf('') !== -1) {
            var r2 = copyRegex(this, {
                removeG: true,
                isInternalOnly: true
            });
            // Using `str.slice(match.index)` rather than `match[0]` in case lookahead allowed
            // matching due to characters outside the match
            nativ.replace.call(String(str).slice(match.index), r2, function () {
                for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
                    args[_key] = arguments[_key];
                }

                var len = args.length;
                // Skip index 0 and the last 2
                for (var i = 1; i < len - 2; ++i) {
                    if (args[i] === undefined) {
                        match[i] = undefined;
                    }
                }
            });
        }

        // Attach named capture properties
        if (this[REGEX_DATA] && this[REGEX_DATA].captureNames) {
            // Skip index 0
            for (var i = 1; i < match.length; ++i) {
                var name = this[REGEX_DATA].captureNames[i - 1];
                if (name) {
                    match[name] = match[i];
                }
            }
        }

        // Fix browsers that increment `lastIndex` after zero-length matches
        if (this.global && !match[0].length && this.lastIndex > match.index) {
            this.lastIndex = match.index;
        }
    }

    if (!this.global) {
        // Fixes IE, Opera bug (last tested IE 9, Opera 11.6)
        this.lastIndex = origLastIndex;
    }

    return match;
};

/**
 * Fixes browser bugs in the native `RegExp.prototype.test`.
 *
 * @memberOf RegExp
 * @param {String} str String to search.
 * @returns {Boolean} Whether the regex matched the provided value.
 */
fixed.test = function (str) {
    // Do this the easy way :-)
    return !!fixed.exec.call(this, str);
};

/**
 * Adds named capture support (with backreferences returned as `result.name`), and fixes browser
 * bugs in the native `String.prototype.match`.
 *
 * @memberOf String
 * @param {RegExp|*} regex Regex to search with. If not a regex object, it is passed to `RegExp`.
 * @returns {Array} If `regex` uses flag g, an array of match strings or `null`. Without flag g,
 *   the result of calling `regex.exec(this)`.
 */
fixed.match = function (regex) {
    if (!XRegExp.isRegExp(regex)) {
        // Use the native `RegExp` rather than `XRegExp`
        regex = new RegExp(regex);
    } else if (regex.global) {
        var result = nativ.match.apply(this, arguments);
        // Fixes IE bug
        regex.lastIndex = 0;

        return result;
    }

    return fixed.exec.call(regex, toObject(this));
};

/**
 * Adds support for `${n}` (or `$<n>`) tokens for named and numbered backreferences in replacement
 * text, and provides named backreferences to replacement functions as `arguments[0].name`. Also
 * fixes browser bugs in replacement text syntax when performing a replacement using a nonregex
 * search value, and the value of a replacement regex's `lastIndex` property during replacement
 * iterations and upon completion. Note that this doesn't support SpiderMonkey's proprietary third
 * (`flags`) argument. Use via `XRegExp.replace`.
 *
 * @memberOf String
 * @param {RegExp|String} search Search pattern to be replaced.
 * @param {String|Function} replacement Replacement string or a function invoked to create it.
 * @returns {String} New string with one or all matches replaced.
 */
fixed.replace = function (search, replacement) {
    var isRegex = XRegExp.isRegExp(search);
    var origLastIndex = void 0;
    var captureNames = void 0;
    var result = void 0;

    if (isRegex) {
        if (search[REGEX_DATA]) {
            captureNames = search[REGEX_DATA].captureNames;
        }
        // Only needed if `search` is nonglobal
        origLastIndex = search.lastIndex;
    } else {
        search += ''; // Type-convert
    }

    // Don't use `typeof`; some older browsers return 'function' for regex objects
    if (isType(replacement, 'Function')) {
        // Stringifying `this` fixes a bug in IE < 9 where the last argument in replacement
        // functions isn't type-converted to a string
        result = nativ.replace.call(String(this), search, function () {
            for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
                args[_key2] = arguments[_key2];
            }

            if (captureNames) {
                // Change the `args[0]` string primitive to a `String` object that can store
                // properties. This really does need to use `String` as a constructor
                args[0] = new String(args[0]);
                // Store named backreferences on the first argument
                for (var i = 0; i < captureNames.length; ++i) {
                    if (captureNames[i]) {
                        args[0][captureNames[i]] = args[i + 1];
                    }
                }
            }
            // Update `lastIndex` before calling `replacement`. Fixes IE, Chrome, Firefox, Safari
            // bug (last tested IE 9, Chrome 17, Firefox 11, Safari 5.1)
            if (isRegex && search.global) {
                search.lastIndex = args[args.length - 2] + args[0].length;
            }
            // ES6 specs the context for replacement functions as `undefined`
            return replacement.apply(undefined, args);
        });
    } else {
        // Ensure that the last value of `args` will be a string when given nonstring `this`,
        // while still throwing on null or undefined context
        result = nativ.replace.call(this == null ? this : String(this), search, function () {
            for (var _len3 = arguments.length, args = Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
                args[_key3] = arguments[_key3];
            }

            return nativ.replace.call(String(replacement), replacementToken, replacer);

            function replacer($0, bracketed, angled, dollarToken) {
                bracketed = bracketed || angled;
                // Named or numbered backreference with curly or angled braces
                if (bracketed) {
                    // XRegExp behavior for `${n}` or `$<n>`:
                    // 1. Backreference to numbered capture, if `n` is an integer. Use `0` for the
                    //    entire match. Any number of leading zeros may be used.
                    // 2. Backreference to named capture `n`, if it exists and is not an integer
                    //    overridden by numbered capture. In practice, this does not overlap with
                    //    numbered capture since XRegExp does not allow named capture to use a bare
                    //    integer as the name.
                    // 3. If the name or number does not refer to an existing capturing group, it's
                    //    an error.
                    var n = +bracketed; // Type-convert; drop leading zeros
                    if (n <= args.length - 3) {
                        return args[n] || '';
                    }
                    // Groups with the same name is an error, else would need `lastIndexOf`
                    n = captureNames ? captureNames.indexOf(bracketed) : -1;
                    if (n < 0) {
                        throw new SyntaxError('Backreference to undefined group ' + $0);
                    }
                    return args[n + 1] || '';
                }
                // Else, special variable or numbered backreference without curly braces
                if (dollarToken === '$') {
                    // $$
                    return '$';
                }
                if (dollarToken === '&' || +dollarToken === 0) {
                    // $&, $0 (not followed by 1-9), $00
                    return args[0];
                }
                if (dollarToken === '`') {
                    // $` (left context)
                    return args[args.length - 1].slice(0, args[args.length - 2]);
                }
                if (dollarToken === "'") {
                    // $' (right context)
                    return args[args.length - 1].slice(args[args.length - 2] + args[0].length);
                }
                // Else, numbered backreference without braces
                dollarToken = +dollarToken; // Type-convert; drop leading zero
                // XRegExp behavior for `$n` and `$nn`:
                // - Backrefs end after 1 or 2 digits. Use `${..}` or `$<..>` for more digits.
                // - `$1` is an error if no capturing groups.
                // - `$10` is an error if less than 10 capturing groups. Use `${1}0` or `$<1>0`
                //   instead.
                // - `$01` is `$1` if at least one capturing group, else it's an error.
                // - `$0` (not followed by 1-9) and `$00` are the entire match.
                // Native behavior, for comparison:
                // - Backrefs end after 1 or 2 digits. Cannot reference capturing group 100+.
                // - `$1` is a literal `$1` if no capturing groups.
                // - `$10` is `$1` followed by a literal `0` if less than 10 capturing groups.
                // - `$01` is `$1` if at least one capturing group, else it's a literal `$01`.
                // - `$0` is a literal `$0`.
                if (!isNaN(dollarToken)) {
                    if (dollarToken > args.length - 3) {
                        throw new SyntaxError('Backreference to undefined group ' + $0);
                    }
                    return args[dollarToken] || '';
                }
                // `$` followed by an unsupported char is an error, unlike native JS
                throw new SyntaxError('Invalid token ' + $0);
            }
        });
    }

    if (isRegex) {
        if (search.global) {
            // Fixes IE, Safari bug (last tested IE 9, Safari 5.1)
            search.lastIndex = 0;
        } else {
            // Fixes IE, Opera bug (last tested IE 9, Opera 11.6)
            search.lastIndex = origLastIndex;
        }
    }

    return result;
};

/**
 * Fixes browser bugs in the native `String.prototype.split`. Use via `XRegExp.split`.
 *
 * @memberOf String
 * @param {RegExp|String} separator Regex or string to use for separating the string.
 * @param {Number} [limit] Maximum number of items to include in the result array.
 * @returns {Array} Array of substrings.
 */
fixed.split = function (separator, limit) {
    if (!XRegExp.isRegExp(separator)) {
        // Browsers handle nonregex split correctly, so use the faster native method
        return nativ.split.apply(this, arguments);
    }

    var str = String(this);
    var output = [];
    var origLastIndex = separator.lastIndex;
    var lastLastIndex = 0;
    var lastLength = void 0;

    // Values for `limit`, per the spec:
    // If undefined: pow(2,32) - 1
    // If 0, Infinity, or NaN: 0
    // If positive number: limit = floor(limit); if (limit >= pow(2,32)) limit -= pow(2,32);
    // If negative number: pow(2,32) - floor(abs(limit))
    // If other: Type-convert, then use the above rules
    // This line fails in very strange ways for some values of `limit` in Opera 10.5-10.63, unless
    // Opera Dragonfly is open (go figure). It works in at least Opera 9.5-10.1 and 11+
    limit = (limit === undefined ? -1 : limit) >>> 0;

    XRegExp.forEach(str, separator, function (match) {
        // This condition is not the same as `if (match[0].length)`
        if (match.index + match[0].length > lastLastIndex) {
            output.push(str.slice(lastLastIndex, match.index));
            if (match.length > 1 && match.index < str.length) {
                Array.prototype.push.apply(output, match.slice(1));
            }
            lastLength = match[0].length;
            lastLastIndex = match.index + lastLength;
        }
    });

    if (lastLastIndex === str.length) {
        if (!nativ.test.call(separator, '') || lastLength) {
            output.push('');
        }
    } else {
        output.push(str.slice(lastLastIndex));
    }

    separator.lastIndex = origLastIndex;
    return output.length > limit ? output.slice(0, limit) : output;
};

// ==--------------------------==
// Built-in syntax/flag tokens
// ==--------------------------==

/*
 * Letter escapes that natively match literal characters: `\a`, `\A`, etc. These should be
 * SyntaxErrors but are allowed in web reality. XRegExp makes them errors for cross-browser
 * consistency and to reserve their syntax, but lets them be superseded by addons.
 */
XRegExp.addToken(/\\([ABCE-RTUVXYZaeg-mopqyz]|c(?![A-Za-z])|u(?![\dA-Fa-f]{4}|{[\dA-Fa-f]+})|x(?![\dA-Fa-f]{2}))/, function (match, scope) {
    // \B is allowed in default scope only
    if (match[1] === 'B' && scope === defaultScope) {
        return match[0];
    }
    throw new SyntaxError('Invalid escape ' + match[0]);
}, {
    scope: 'all',
    leadChar: '\\'
});

/*
 * Unicode code point escape with curly braces: `\u{N..}`. `N..` is any one or more digit
 * hexadecimal number from 0-10FFFF, and can include leading zeros. Requires the native ES6 `u` flag
 * to support code points greater than U+FFFF. Avoids converting code points above U+FFFF to
 * surrogate pairs (which could be done without flag `u`), since that could lead to broken behavior
 * if you follow a `\u{N..}` token that references a code point above U+FFFF with a quantifier, or
 * if you use the same in a character class.
 */
XRegExp.addToken(/\\u{([\dA-Fa-f]+)}/, function (match, scope, flags) {
    var code = dec(match[1]);
    if (code > 0x10FFFF) {
        throw new SyntaxError('Invalid Unicode code point ' + match[0]);
    }
    if (code <= 0xFFFF) {
        // Converting to \uNNNN avoids needing to escape the literal character and keep it
        // separate from preceding tokens
        return '\\u' + pad4(hex(code));
    }
    // If `code` is between 0xFFFF and 0x10FFFF, require and defer to native handling
    if (hasNativeU && flags.indexOf('u') !== -1) {
        return match[0];
    }
    throw new SyntaxError('Cannot use Unicode code point above \\u{FFFF} without flag u');
}, {
    scope: 'all',
    leadChar: '\\'
});

/*
 * Empty character class: `[]` or `[^]`. This fixes a critical cross-browser syntax inconsistency.
 * Unless this is standardized (per the ES spec), regex syntax can't be accurately parsed because
 * character class endings can't be determined.
 */
XRegExp.addToken(/\[(\^?)\]/,
// For cross-browser compatibility with ES3, convert [] to \b\B and [^] to [\s\S].
// (?!) should work like \b\B, but is unreliable in some versions of Firefox
/* eslint-disable no-confusing-arrow */
function (match) {
    return match[1] ? '[\\s\\S]' : '\\b\\B';
},
/* eslint-enable no-confusing-arrow */
{ leadChar: '[' });

/*
 * Comment pattern: `(?# )`. Inline comments are an alternative to the line comments allowed in
 * free-spacing mode (flag x).
 */
XRegExp.addToken(/\(\?#[^)]*\)/, getContextualTokenSeparator, { leadChar: '(' });

/*
 * Whitespace and line comments, in free-spacing mode (aka extended mode, flag x) only.
 */
XRegExp.addToken(/\s+|#[^\n]*\n?/, getContextualTokenSeparator, { flag: 'x' });

/*
 * Dot, in dotall mode (aka singleline mode, flag s) only.
 */
XRegExp.addToken(/\./, function () {
    return '[\\s\\S]';
}, {
    flag: 's',
    leadChar: '.'
});

/*
 * Named backreference: `\k<name>`. Backreference names can use the characters A-Z, a-z, 0-9, _,
 * and $ only. Also allows numbered backreferences as `\k<n>`.
 */
XRegExp.addToken(/\\k<([\w$]+)>/, function (match) {
    // Groups with the same name is an error, else would need `lastIndexOf`
    var index = isNaN(match[1]) ? this.captureNames.indexOf(match[1]) + 1 : +match[1];
    var endIndex = match.index + match[0].length;
    if (!index || index > this.captureNames.length) {
        throw new SyntaxError('Backreference to undefined group ' + match[0]);
    }
    // Keep backreferences separate from subsequent literal numbers. This avoids e.g.
    // inadvertedly changing `(?<n>)\k<n>1` to `()\11`.
    return '\\' + index + (endIndex === match.input.length || isNaN(match.input[endIndex]) ? '' : '(?:)');
}, { leadChar: '\\' });

/*
 * Numbered backreference or octal, plus any following digits: `\0`, `\11`, etc. Octals except `\0`
 * not followed by 0-9 and backreferences to unopened capture groups throw an error. Other matches
 * are returned unaltered. IE < 9 doesn't support backreferences above `\99` in regex syntax.
 */
XRegExp.addToken(/\\(\d+)/, function (match, scope) {
    if (!(scope === defaultScope && /^[1-9]/.test(match[1]) && +match[1] <= this.captureNames.length) && match[1] !== '0') {
        throw new SyntaxError('Cannot use octal escape or backreference to undefined group ' + match[0]);
    }
    return match[0];
}, {
    scope: 'all',
    leadChar: '\\'
});

/*
 * Named capturing group; match the opening delimiter only: `(?<name>`. Capture names can use the
 * characters A-Z, a-z, 0-9, _, and $ only. Names can't be integers. Supports Python-style
 * `(?P<name>` as an alternate syntax to avoid issues in some older versions of Opera which natively
 * supported the Python-style syntax. Otherwise, XRegExp might treat numbered backreferences to
 * Python-style named capture as octals.
 */
XRegExp.addToken(/\(\?P?<([\w$]+)>/, function (match) {
    // Disallow bare integers as names because named backreferences are added to match arrays
    // and therefore numeric properties may lead to incorrect lookups
    if (!isNaN(match[1])) {
        throw new SyntaxError('Cannot use integer as capture name ' + match[0]);
    }
    if (match[1] === 'length' || match[1] === '__proto__') {
        throw new SyntaxError('Cannot use reserved word as capture name ' + match[0]);
    }
    if (this.captureNames.indexOf(match[1]) !== -1) {
        throw new SyntaxError('Cannot use same name for multiple groups ' + match[0]);
    }
    this.captureNames.push(match[1]);
    this.hasNamedCapture = true;
    return '(';
}, { leadChar: '(' });

/*
 * Capturing group; match the opening parenthesis only. Required for support of named capturing
 * groups. Also adds explicit capture mode (flag n).
 */
XRegExp.addToken(/\((?!\?)/, function (match, scope, flags) {
    if (flags.indexOf('n') !== -1) {
        return '(?:';
    }
    this.captureNames.push(null);
    return '(';
}, {
    optionalFlags: 'n',
    leadChar: '('
});

exports.default = XRegExp;
module.exports = exports['default'];

/***/ })

}]);
//# sourceMappingURL=vendors.bundle.js.map