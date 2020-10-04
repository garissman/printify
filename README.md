# Printify PHP SDK
----
Basic PHP wrapper for working with Printify API.
API endpoint documentation can be found here: <https://developers.printify.com/>

**This is a work in progress**, Webhooks and events are to come soon.

## Installation
----
Using composer, run `composer require jacob-hyde/printify-php-sdk`
Check out **example** and **test** directories for more specific usage examples.

### Basic Usage
----
Create a new instance of the Printify API and pass it to the endpoint class. For example the Catalog:
```
use Printify\PrintifyApiClient;
use Printify\PrintifyCatalog;

$printify_api = new PrintifyApiClient(<Shop Access Token>);
$printify_catalog = new PrintifyCatalog($printify_api);
$catalog_items = $printify_catalog->all();
```

### Shop Based Endpoints
----
For shop based endpoints, pass along the shop ID in the endpoint constructor. For Example:
```
use Printify\PrintifyOrders;
$printify_orders = new PrintifyOrders($printify_api, <Shop ID>);
$orders = $printify_orders->all();
```
Endpoints that need a shop ID:
* Products
* Orders
* Uploads

### Endpoints
----
* [Shops](https://github.com/jacob-hyde/Printify-PHP-SDK/docs/shops.md)
* [Catalog](https://github.com/jacob-hyde/Printify-PHP-SDK/docs/catalog.md)
* [Products](https://github.com/jacob-hyde/Printify-PHP-SDK/docs/products.md)
* [Orders](https://github.com/jacob-hyde/Printify-PHP-SDK/docs/orders.md)
* [Uploads](https://github.com/jacob-hyde/Printify-PHP-SDK/docs/uploads.md)
