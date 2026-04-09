# UPGRADING

## 3.0.0

* Doctrine annotation support has been removed. Replace all annotation usages with PHP 8 attributes:
  * `@Legacy\Dispatch` → `#[Legacy\Dispatch]`
  * `@Legacy\Passthru` → `#[Legacy\Passthru]`
  * `@Legacy\IgnoreRedirect` → `#[Legacy\IgnoreRedirect]`
  * `@Legacy\IgnoreHeader("some-name")` → `#[Legacy\IgnoreHeader('some-name')]`
  * `@Legacy\KeepHeaders` → `#[Legacy\KeepHeaders]`
  * `@Legacy\KeepCookies` → `#[Legacy\KeepCookies]`
  * `@Legacy\Filter(class="...")` → `#[Legacy\Filter(class: '...')]`
* The `doctrine/annotations` package is no longer required.
* PHP 8.0 is now the minimum required version.

## 2.4.0

* Add support for PHP 8 attributes.

## Older

* The webfactory.integration.filter tag has been renamed to "webfactory_legacy_integration.filter"
