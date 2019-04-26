(window.webpackJsonp=window.webpackJsonp||[]).push([["vendors/async/is-buffer"],{"./node_modules/is-buffer/index.js":function(n,o){function e(n){return!!n.constructor&&"function"==typeof n.constructor.isBuffer&&n.constructor.isBuffer(n)}
/*!
 * Determine if an object is a Buffer
 *
 * @author   Feross Aboukhadijeh <https://feross.org>
 * @license  MIT
 */
n.exports=function(n){return null!=n&&(e(n)||function(n){return"function"==typeof n.readFloatLE&&"function"==typeof n.slice&&e(n.slice(0,0))}(n)||!!n._isBuffer)}}}]);