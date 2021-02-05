# Laravel Crewmate

Laravel authentication for a secondary group of users (i.e. admins, team members, employees, etc).

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


