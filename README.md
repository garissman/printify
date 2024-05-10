# Printify PHP SDK
----
Basic PHP wrapper for working with Printify API.
API endpoint documentation can be found here: <https://developers.printify.com/>

## Installation
----
`composer create-project arimoralesjordan/printify`

## Installation
----
`php artisan vendor:publish --provider="Printify\ScoutServiceProvider"`

Check out **example** and **test** directories for more specific usage examples.

### Basic Usage
----
Create a new instance of the Printify API and pass it to the endpoint class. For example the Catalog:

```
use Garissman\Printify\PrintifyApiClient;
use Garissman\Printify\PrintifyCatalog;

$printify_api = new PrintifyApiClient(<Shop Access Token>);
$printify_catalog = new PrintifyCatalog($printify_api);
$catalog_items = $printify_catalog->all();
```

### Shop Based Endpoints
----
For shop based endpoints, pass along the shop ID in the endpoint constructor. For Example:

```
use Garissman\Printify\PrintifyOrders;
$printify_orders = new PrintifyOrders($printify_api, <Shop ID>);
$orders = $printify_orders->all();
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
