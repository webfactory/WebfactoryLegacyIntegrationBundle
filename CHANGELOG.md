Changelog
=========

Version 1.3
-----------

* Symfony sub-requests can be dispatched to the legacy app (but the legacy app is still called at most once per web request)

Version 1.2
-----------

* Legacy front controllers don't have to return the HTTP status code anymore

Version 1.1
-----------

* Added a way to embed Symfony 2 output into the legacy response (#5)
* Fixed that `LegacyApplicationDispatchingEventListener` would not play nicely with `SensioFrameworkExtraBundle`'s `@Cache` annotation (#6)

