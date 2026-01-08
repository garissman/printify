# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel PHP SDK for the Printify API (`garissman/printify`). It provides a wrapper around Printify's print-on-demand service API, enabling Laravel applications to manage shops, products, orders, catalogs, images, and webhooks.

## Commands

### Testing
```bash
# Run all tests
./vendor/bin/phpunit

# Run a specific test file
./vendor/bin/phpunit tests/ProductsTest.php

# Run a specific test method
./vendor/bin/phpunit --filter testProductCreate
```

**Test Setup:** Copy `tests/Credentials.php.dist` to `tests/Credentials.php` and fill in your Printify API credentials.

### Package Installation
```bash
composer install
```

### Publishing Config (in consuming Laravel app)
```bash
php artisan vendor:publish --provider="Garissman\Printify\PrintifyServiceProvider"
```

### Artisan Command
```bash
php artisan printify:register-printify-webhooks
```

## Architecture

### Entry Points
- **Facade:** `Garissman\Printify\Facades\Printify` - Main entry point for Laravel apps
- **Service Provider:** `PrintifyServiceProvider` - Registers the package with Laravel, publishes config

### Core Classes
- **`Printify`** (`src/Printify.php`) - Main orchestrator that instantiates endpoint classes
- **`PrintifyApiClient`** - HTTP client wrapper using Laravel's HTTP facade (Guzzle-based)
- **`PrintifyBaseEndpoint`** - Abstract base class for all API endpoint classes

### Endpoint Classes (extend `PrintifyBaseEndpoint`)
| Class | Purpose | Shop Required |
|-------|---------|---------------|
| `PrintifyCatalog` | Blueprints and print providers | No |
| `PrintifyShop` | Shop management | No |
| `PrintifyImage` | Image uploads | No |
| `PrintifyProducts` | Product CRUD and publishing | Yes |
| `PrintifyOrders` | Order management | Yes |
| `PrintifyWebhooks` | Webhook registration | Yes |

### Structures (Data Objects)
Located in `src/Structures/`, these classes extend `BaseStructure` and represent API responses:
- `Shop`, `Product`, `Image`, `Webhook`
- `Order/Order`, `Order/LineItem`, `Order/Shipment`
- `Catalog/Blueprint`, `Catalog/PrintProvider`, `Catalog/Variant`, `Catalog/Shipping`

`BaseStructure` provides dynamic property access via `__get`/`__set` magic methods on an internal `$attributes` array.

### Usage Pattern
```php
use Garissman\Printify\Facades\Printify;

// Non-shop endpoints
Printify::catalog()->all();

// Shop-based endpoints (shop auto-resolved from config if not passed)
Printify::product()->all();
Printify::order($shop)->find($id);
```

### Configuration
Config file: `config/printify.php` (published from `stubs/printify.php`)
- `PRINTIFY_API_TOKEN` - API token from Printify
- `default_shop_name` - Used to auto-select shop when not explicitly passed
