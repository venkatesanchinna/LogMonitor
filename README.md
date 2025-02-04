# Log Monitor

[![Latest Stable Version](https://poser.pugx.org/venkatesanchinna/log-monitor/v/stable)](https://packagist.org/packages/venkatesanchinna/log-monitor)
[![License](https://poser.pugx.org/venkatesanchinna/log-monitor/license)](https://packagist.org/packages/venkatesanchinna/log-monitor)
[![Total Downloads](https://poser.pugx.org/venkatesanchinna/log-monitor/downloads)](https://packagist.org/packages/venkatesanchinna/log-monitor)
[![Monthly Downloads](https://poser.pugx.org/venkatesanchinna/log-monitor/d/monthly)](https://packagist.org/packages/venkatesanchinna/log-monitor)
[![Daily Downloads](https://poser.pugx.org/venkatesanchinna/log-monitor/d/daily)](https://packagist.org/packages/venkatesanchinna/log-monitor)


Log Monitor is a Laravel package that provides an intuitive UI for viewing and managing application logs. It supports all Laravel versions and works seamlessly with Bootstrap 3 and 4.

## Features

- Supports all Laravel versions
- UI built with Bootstrap 3 & 4
- Real-time log monitoring
- Easy installation and configuration
- Lightweight and fast
- Can preview cusom logs
- Folder based logs


## Installation

You can install the package via Composer:

```bash
composer require venkatesanchinna/log-monitor
```

If you encounter stability issues, you may need to specify the development branch:

```bash
composer require venkatesanchinna/log-monitor:dev-master
```

### Publish Assets and Configurations

After installing, publish the package assets and config files:

```bash
php artisan vendor:publish --tag=log-monitor-assets
php artisan vendor:publish --tag=log-monitor-config
```

## Configuration

The configuration file can be found at:

```bash
config/log-monitor.php
```

You can update settings such as log file location, UI preferences, and log levels.

## Usage

### Accessing the Log Monitor

After installation, you can access the log monitor UI by visiting:

```plaintext
http://your-app-domain/log-monitor
```

### Blade Integration

You can include the log monitor in your Blade templates:

```blade
@include('log-monitor::dashboard')
```

### Publishing Views

To customize the UI, you can publish the views:

```bash
php artisan vendor:publish --tag=log-monitor-views
```

The views will be available in:

```bash
resources/views/vendor/log-monitor/
```

## Translations

To use translations, publish the language files:

```bash
php artisan vendor:publish --tag=log-monitor-lang
```

Translations will be stored in:

```bash
resources/lang/vendor/log-monitor/
```

## Artisan Commands

Log Monitor provides the following artisan commands:

```bash
log-monitor:check         # Check all LogMonitor requirements.
log-monitor:clear         # Clear all generated log files.
log-monitor:publish       # Publish all LogMonitor resources and config files.
log-monitor:stats         # Display stats of all logs.
```

## Credits

This package was inspired from [ARCANEDEV/LogViewer](https://github.com/ARCANEDEV/LogViewer).

## Author

**Venkatesan C**\
Email: [venkatesangee@gmail.com](mailto:venkatesangee@gmail.com)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

