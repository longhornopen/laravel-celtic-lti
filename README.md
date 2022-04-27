## Laravel Celtic LTI

This is a package to integrate [CeLTIc LTI](https://github.com/celtic-project/LTI-PHP/) with Laravel.  CeLTIc is a full-fledged LTI library written in generic PHP; this library provides hooks for supporting Laravel's opinionated stance on database access, configuration management, etc.

## Installation

Require this package with composer.

```shell
composer require longhornopen/laravel-celtic-lti
```

Publish this package's assets.

```shell
php artisan vendor:publish --provider="LonghornOpen\LaravelCelticLTI\LtiServiceProvider"
```

Run your app's migrations, which will install the 'lti2_*' database tables.

```php
php artisan migrate
```

Finally, check the `config/lti.php` file to set your app's configuration.

### Laravel without auto-discovery:

If you don't use Laravel's auto-discovery of packages, add the ServiceProvider to the providers array in config/app.php:

```php
LonghornOpen\LaravelCelticLti\LtiServiceProvider
```

## Usage

A sample set of steps to get this library up and running in your app can be found at [https://github.com/longhornopen/laravel-celtic-lti/wiki/Laravel-app-setup](https://github.com/longhornopen/laravel-celtic-lti/wiki/Laravel-app-setup).

## Contributing

We gladly accept GitHub issues containing bug reports or suggestions for improvement.

Pull requests or other offers of development help are appreciated.  If you're wanting to contribute a large change, please open an issue and let us know.

## Thanks

Thanks to the CeLTIc project and to Stephen Vickers in particular.  This library would not be possible without their underlying work.