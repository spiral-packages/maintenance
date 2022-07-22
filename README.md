# This is my package maintenance

[![PHP](https://img.shields.io/packagist/php-v/spiral-packages/maintenance.svg?style=flat-square)](https://packagist.org/packages/spiral-packages/maintenance)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/spiral-packages/maintenance.svg?style=flat-square)](https://packagist.org/packages/spiral-packages/maintenance)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spiral-packages/maintenance/run-tests?label=tests&style=flat-square)](https://github.com/spiral-packages/maintenance/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spiral-packages/maintenance.svg?style=flat-square)](https://packagist.org/packages/spiral-packages/maintenance)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework 3.0+

## Installation

You can install the package via composer:

```bash
composer require spiral-packages/maintenance
```

After package install you need to register bootloader from the package.

```php
protected const LOAD = [
    // ...
    \Spiral\Maintenance\Bootloader\MaintenanceBootloader::class,
];
```

> Note: if you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Configuration

By default, the package uses `file` driver for storing information about maintenance mode. If you have multiple
instances of your application you need to use `cache` driver with storage that will be accessed from all instances.

```dotenv
MAINTENANCE_DRIVER=cache
MAINTENANCE_CACHE_STORAGE=null
MAINTENANCE_CACHE_KEY=maintenance
```

## Usage

Include `Spiral\Maintenance\Middleware\PreventRequestInMaintenanceModeMiddleware` in your application for routes that
should not have access during maintenance mode.

```php
final class RoutesBootloader extends BaseRoutesBootloader
{
    protected function globalMiddleware(): array
    {
        return [
            \Spiral\Maintenance\Middleware\PreventRequestInMaintenanceModeMiddleware::class,
            // ...
        ];
    }
}
```

To enable maintenance mode, execute the down command:

```bash
php app.php down
```

By default, response code for maintenance mode is `503`, but you may set custom response code

```bash
php app.php down --status=504
```

To disable maintenance mode, use the up command:

```bash
php app.php up
```

When your application is in maintenance mode the middleware
throws `Spiral\Maintenance\Exception\MaintenanceModeHttpException` with defined status code. 

Spiral Framework allows you to pre-render a maintenance mode view that will be returned to the very beginning of the 
request cycle. You may pre-render a template of your choice using `App\ErrorHandler\ViewRenderer`. By default, it looks
for a template in a folder `app/views/exception/{statusCode}.dark.php`

You can create a new view file `app/views/exception/503.dark.php`:

```php
<extends:layout.base title="[[Maintenance mode]]"/>
<use:element path="embed/links" as="homepage:links"/>

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css"/>
</stack:push>

<define:body>
    <div class="wrapper">
        <img src="/images/503.svg" alt="Error 503" width="300px"/>
        <h2>{{ $exception->getMessage() ?? 'Maintenance mode' }}</h2>
    </div>
</define:body>
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [butschster](https://github.com/butschster)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
