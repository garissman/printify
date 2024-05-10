# Printify PHP Laravel SDK
----
Basic PHP wrapper for working with Printify API, for Laravel.
API endpoint documentation can be found here: <https://developers.printify.com/>

## Installation
----
`composer require garissman/printify`

## Publish
----
After installing, you should publish the configuration file using the vendor:publish Artisan command. This
command will publish the printify.php configuration file to your application's config directory:

`php artisan vendor:publish --provider="Garissman\Printify\PrintifyServiceProvider"`

### Basic Usage
----
Create a new instance of the Printify API and pass it to the endpoint class. For example the Catalog:

```
use Garissman\Printify\Facades\Printify;

Printify::catalog()->all()
```

### Shop Based Endpoints
----
For shop based endpoints, pass along the shop ID in the endpoint constructor. For Example:

```
use Garissman\Printify\Facades\Printify;

Printify::order(<Shop ID>)->all()
```

Endpoints that need a shop ID:

* Products
* Orders
* Uploads
* Webhooks

### Endpoints
----

* [Shops](docs/shops.md)
* [Catalog](docs/catalog.md)
* [Products](docs/products.md)
* [Orders](orders.md)
* [Uploads](docs/uploads.md)
* [Webhooks](docs/webhooks.md)
