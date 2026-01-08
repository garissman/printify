<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\PrintifyShop;
use Garissman\Printify\PrintifyWebhooks;
use Garissman\Printify\Structures\Shop;
use Garissman\Printify\Structures\Webhook;
use Garissman\Printify\Structures\Webhooks\EventsEnum;
use Illuminate\Support\Collection;

class WebhooksTest extends TestCase
{
    public $printify_webhooks = null;

    protected Shop $shop;

    protected function setUp(): void
    {
        parent::setUp();
        $printifyShop = new PrintifyShop($this->api);
        $shops = $printifyShop->all();

        // Find shop by name if configured, otherwise use first shop
        if (property_exists(Credentials::class, 'shop_name') && Credentials::$shop_name) {
            $this->shop = $shops->firstWhere('title', Credentials::$shop_name) ?? $shops->first();
        } else {
            $this->shop = $shops->first();
        }

        $this->printify_webhooks = new PrintifyWebhooks($this->api, $this->shop);

        // Clean up any existing webhooks (silently ignore failures from stale data)
        try {
            $webhooks = $this->printify_webhooks->all();
            foreach ($webhooks as $webhook) {
                try {
                    $host = parse_url($webhook->url, PHP_URL_HOST);
                    $this->printify_webhooks->delete($webhook->id, $host);
                } catch (\Exception $e) {
                    // Silently ignore deletion failures (stale webhooks from previous runs)
                }
            }
        } catch (\Exception $e) {
            // Ignore errors during cleanup
        }
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('printify.webhook_secret', 'test-secret-for-phpunit');
    }

    public function test_webhooks_all()
    {
        $createdWebhook = $this->_createWebhook();
        $webhooks = $this->printify_webhooks->all();
        $this->assertInstanceOf(Collection::class, $webhooks);
        $this->assertGreaterThanOrEqual(1, $webhooks->count());

        // Get first webhook to verify structure
        $webhook = $webhooks->first();
        $this->assertInstanceOf(Webhook::class, $webhook);
        $structure = [
            'id',
            'topic',
            'url',
            'shop_id',
        ];
        $this->assertArrayStructure($structure, $webhook->toArray());
        $this->_safeDelete($createdWebhook->id);
    }

    public function test_create_webhook()
    {
        $webhook = $this->_createWebhook();
        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertNotEmpty($webhook->id);
        $this->assertEquals('order:created', $webhook->topic);
        $this->_safeDelete($webhook->id);
    }

    public function test_update_webhook()
    {
        $webhook = $this->_createWebhook();
        $updated_url = 'https://example.com/webhooks/updated';
        $updatedWebhook = $this->printify_webhooks->update($webhook->id, $updated_url);
        $this->assertEquals($updated_url, $updatedWebhook->url);
        $this->_safeDelete($webhook->id);
    }

    public function test_delete_webhook()
    {
        $webhook = $this->_createWebhook();
        // Extract host from webhook URL for the delete API requirement
        $host = parse_url($webhook->url, PHP_URL_HOST);
        $response = $this->printify_webhooks->delete($webhook->id, $host);
        $this->assertTrue($response);
    }

    private function _createWebhook(): Webhook
    {
        // Use unique URL to avoid "webhook already exists" errors
        $uniqueUrl = 'https://example.com/webhooks/' . uniqid();
        return $this->printify_webhooks->create(EventsEnum::OrderCreated, $uniqueUrl);
    }

    private function _safeDelete(string $id, string $host = 'example.com'): void
    {
        try {
            $this->printify_webhooks->delete($id, $host);
        } catch (\Exception $e) {
            // Silently ignore deletion failures
        }
    }
}
