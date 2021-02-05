# Laravel Crewmate

## Installation

Install this package:

```bash
composer require davidbschneider/laravel-crewmate
```

Publish configuration

```bash
php artisan vendor:publish --provider="DavidSchneider\LaravelCrewmate\PackageServiceProvider"
```

Migrate:
```bash
php artisan migrate
```

Create first crewmate:

```bash
php artisan crewmate:init <EMAILADDRESS>
```

# Configuration


