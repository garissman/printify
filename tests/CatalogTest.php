<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\PrintifyCatalog;
use Illuminate\Support\Collection;
use Garissman\Printify\Structures\Catalog\Blueprint;
use Garissman\Printify\Structures\Catalog\PrintProvider;
use Garissman\Printify\Structures\Catalog\Shipping;
use Garissman\Printify\Structures\Catalog\Variant;

class CatalogTest extends TestCase
{
    const CATALOG_ITEM_ID = 5;

    const PRINT_PROVIDER_ID = 1;

    public $printify_catalog = null;

    public function test_catalog_all()
    {
        $catalog = $this->printify_catalog->all();
        $this->assertInstanceOf(Collection::class, $catalog);
        $this->assertTrue((count($catalog) > 0));
        $blueprint = $catalog[0];
        $this->assertInstanceOf(Blueprint::class, $blueprint);
        $structure = [
            'id',
            'title',
            'description',
            'brand',
            'model',
            'images',
        ];
        $this->assertArrayStructure($structure, $blueprint->toArray());
    }

    public function test_get_catalog()
    {
        $blueprint = $this->printify_catalog->find(self::CATALOG_ITEM_ID);
        $this->assertInstanceOf(Blueprint::class, $blueprint);
        $structure = [
            'id',
            'title',
            'description',
            'brand',
            'model',
            'images',
        ];
        $this->assertArrayStructure($structure, $blueprint->toArray());
    }

    public function test_blueprint_print_provider()
    {
        $print_providers = $this->printify_catalog->print_providers(self::CATALOG_ITEM_ID);
        $this->assertInstanceOf(Collection::class, $print_providers);
        $this->assertTrue((count($print_providers) > 0));
        $provider = $print_providers[0];
        $this->assertInstanceOf(PrintProvider::class, $provider);
        $structure = ['id', 'title', 'location'];
        $this->assertArrayStructure($structure, $provider->toArray());
    }

    public function test_print_provider_variants()
    {
        $print_providers = $this->printify_catalog->print_providers(self::CATALOG_ITEM_ID);
        $print_provider = $print_providers[0];
        $this->assertInstanceOf(PrintProvider::class, $print_provider);
        $print_provider_variants = $this->printify_catalog->print_provider_variants(self::CATALOG_ITEM_ID, $print_provider->id);
        $this->assertInstanceOf(Collection::class, $print_provider_variants);
        $this->assertTrue((count($print_provider_variants) > 0));
        $variant = $print_provider_variants[0];
        $this->assertInstanceOf(Variant::class, $variant);
        $structure = ['id', 'title', 'options' => ['color', 'size'], 'placeholders'];
        $this->assertArrayStructure($structure, $variant->toArray());
        $placeholder = $variant->placeholders[0];
        $structure = ['position', 'height', 'width'];
        $this->assertArrayStructure($structure, $placeholder);
    }

    public function test_print_provider_shipping()
    {
        $print_providers = $this->printify_catalog->print_providers(self::CATALOG_ITEM_ID);
        $print_provider = $print_providers[0];
        $this->assertInstanceOf(PrintProvider::class, $print_provider);
        $print_provider_shipping = $this->printify_catalog->print_provider_shipping(self::CATALOG_ITEM_ID, $print_provider->id);
        $this->assertInstanceOf(Shipping::class, $print_provider_shipping);
        $structure = ['handling_time' => ['value', 'unit'], 'profiles'];
        $this->assertArrayStructure($structure, $print_provider_shipping->toArray());
        $this->assertTrue(is_array($print_provider_shipping->profiles));
        $profile = $print_provider_shipping->profiles[0];
        $this->assertTrue((count($profile['variant_ids']) > 0));
        $structure = ['variant_ids', 'first_item' => ['currency', 'cost'], 'additional_items' => ['currency', 'cost'], 'countries'];
        $this->assertArrayStructure($structure, $profile);
    }

    public function test_all_print_providers()
    {
        $print_providers = $this->printify_catalog->all_print_providers();
        $this->assertInstanceOf(Collection::class, $print_providers);
        $this->assertTrue((count($print_providers) > 0));
        $provider = $print_providers[0];
        $this->assertInstanceOf(PrintProvider::class, $provider);
        // Note: address2 is optional in the API response
        $structure = ['id', 'title', 'location' => ['address1', 'city', 'country', 'region', 'zip']];
        $this->assertArrayStructure($structure, $provider->toArray());
    }

    public function test_print_provider()
    {
        $print_provider = $this->printify_catalog->print_provider(self::PRINT_PROVIDER_ID);
        $this->assertInstanceOf(PrintProvider::class, $print_provider);
        // Note: address2 is optional in the API response
        $structure = ['id', 'title', 'location' => ['address1', 'city', 'country', 'region', 'zip']];
        $this->assertArrayStructure($structure, $print_provider->toArray());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->printify_catalog = new PrintifyCatalog($this->api);
    }
}
