
## 2.2.0 / 2022-03-15

Main changes on this release below, see also the [complete changelog](https://github.com/bedita/manager/compare/v2.1.0...v2.2.0)

### Integration changes

* Replace AuthComponent with Authentication plugin, see #691
## 2.1.0 / 2022-03-15

Main changes on this release below, see also the [complete changelog](https://github.com/bedita/manager/compare/v2.0.0...v2.1.0)

### User-visible changes

* Categories and tags management and enhancements, see #404 #528 #635 #652 #699 #703
* Interface translations, see #693
* Modules order in dashboard, see #679
* Mapbox enhancements, see #671 #672  #673 #674
* Enhance history, see #654

### Integration changes

* Several refactor for cakephp4 compatibility, see #697 #701 #704  #707 #710  #711  #712  #715  #718
## 2.0.0 / 2021-11-23

Main changes on this release below, see also the [complete changelog](https://github.com/bedita/manager/compare/v1.2.0...v2.0.0).

### User-visible changes

 * New UI, buttons and styles
 * Export data in multiple formats, with config `Exports.formats` (csv, ods, xlsx)
 * Bulk actions improved: change "field", copy/move content to destination folder, associate multiple categories, etc.
 * Fast object creation form customized by type
 * Filters enhanced in index and view pages
 * Module(s) accesses by users role(s)
 * Lock/unlock of objects
 * Concurrent editors evidence
 * `Admin` and `Model` modules for admins
 * History in content view
 * `Categories` as trees in modules views

### Developer-visible changes

 * Node 14 or greater
 * Webpack and all other javascript dependencies upgrade
 * Plugin `dereuromark/cakephp-ide-helper` added to dev requirements
 * `phpoffice/phpspreadsheet` to export data in multiple formats

### Integration changes

 * Composer 2 support
 * Javascript linter [also GH workflow with lint validation]
 * Use bedita/bedita:4.5.0 docker image in CI
 * Test coverage improved
 * Security vulnerabilities fixes

## 1.2.0 / 2020-06-12

Main changes on this release below, see also the [complete changelog](https://github.com/bedita/manager/compare/v1.1.0...v1.2.0).

### User-visible changes

* `Folders` tab tree in single object view
* `Folders` index tree view
* Handle multiple date ranges in object view (see `Events`)
* Display date ranges in index
* Avoid empty `Media` tab in object with no streams view
* Export CSV with active filter
* Add `roles` filter on Users

### Developer-visible changes

* Route to proxy requests to BEdita 4 API from JS
* Remove empty attributes in new objects creation, use defaults
* Improve default config, add `Pagination` default in bootstrap
* Fix a problem on failed save form (remove `id`)

### Integration changes

* Use `bedita/bedita:4.1.0` docker image in CI
* fix vulnerabilities in `acrorn` and other JS libraries
* Test coverage improved


## 1.1.0 / 2020-01-30

Main changes on this release below, see also the [complete changelog](https://github.com/bedita/manager/compare/v1.0.0...v1.1.0).

### User-visible changes

* Display simple map for objects with POINT coordinates
* Project configuration loaded object creation, `language` options available on object creation
* Related objects and folder children order changeable via `drang’n’drop`
* `Model` module has been refactored
* Upon save form data are recovered on error
* (Fix) Avoid 2nd field removal in exported CSV header

### Developer-visible changes

* Multi project support added
* Related objects search API call fixed
* (Fix) `CsrfExceptions` in bootstrap typo
* `Project.name`  set in configuration overrides the one in `/home` response
* (Fix) Properly save fields with null value
* Safe login redirects from any URL

### Integration changes

* `PHP 7.4` step added to Travis CI
* `ExportController` tests have been updated
* Code sniffer rules updated to PSR-12
* Scrutinizer use new PHP analysis

## 1.0.0 / 2019-11-18

First public release of **BEdita Manager** - official backend admin WebApp for BEdita4 API.

