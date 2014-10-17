Integrate a Symfony 1.4 project
---

To integrate a Symfony 1.4 project into Symfony2 with this bundle, the following changes need to be done:

### Symfony2 files

- in the app/config/config.yml file, preceed the path of the legacyApplicationBootstrapFile with ../ as shown below

```yml
webfactory_legacy_integration:
    # Whether your legacy application returns text/html as XHTML 1.0 or Polyglot HTML5.
    parsingMode: html5
    # Bootstrap file for the legacy application (see next section)
    legacyApplicationBootstrapFile: ../legacy/web/index_legacy.php
```

### Symfony 1.4 project files

- create a directory named legacy in the root of the Symfony2 project
- copy all files from your Symfony 1.4 project into this new directory
- make sure the permissions of your Symfony 1.4 project files are set as needed (cache and log must be writable by apache, etc)
- if your Symfony 1.4 project has databases, make sure they're created and populated with neccesary data

### Notes

- you have to clear your cache in your Symfony2 AND your Symfony 1.4 project after you make changes that require a clear cache
- you have to create a new bundle and a new route with @Legacy\Dispatch and @Legacy\PassThru for each of your Symfony 1.4 route
- your Symfony 1.4 project will no longer function properly if you move it out of the Symfony2 framework

### Swiftmailer conflict

When you test your project, you may run into an FatalErrorException because Symfony 1.4 is trying to redeclare the Swift class; both your Symfony 1.4 and Symfony2 have SwiftMailer. A workaround is to remove SwiftMailer from Symfony 1.4:

- in the legacy/lib/vendor/symfony/lib/config/sfFactoryConfigHandler.class.php file, on line 44, comment out the 'mailer' element from the $factories array as shown below:

```php
// available list of factories
$factories = array('view_cache_manager', 'logger', 'i18n', 'controller', 'request', 'response', 'routing', 'storage', 'user', 'view_cache');//, 'mailer');
```

- clear both your Symfony 1.4 and Symfony2 cache, and run your application again
