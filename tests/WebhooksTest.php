<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\Collection;
use Garissman\Printify\PrintifyWebhooks;
use Garissman\Printify\Structures\Webhook;
use Garissman\Printify\Tests\TestCase;

class WebhooksTest extends TestCase
{
    public $printify_webhooks = null;

    protected function setUp()
    {
        parent::setUp();
        $this->printify_webhooks = new PrintifyWebhooks($this->api, Credentials::$shop_id);
        $webhooks = $this->printify_webhooks->all();
        foreach ($webhooks as $webhook) {
            $this->printify_webhooks->delete($webhook->id);
        }
    }

    public function testWebhooksAll()
    {
        $this->_createWebhook();
        $webhooks = $this->printify_webhooks->all();
        $this->assertInstanceOf(Collection::class, $webhooks);
        $webhook = $webhooks[0];
        $this->assertInstanceOf(Webhook::class, $webhook);
        $structure = [
            'id',
            'topic',
            'url',
            'shop_id',
            'secret'
        ];
        $this->assertArrayStructure($structure, $webhook->toArray());
        $this->printify_webhooks->delete($webhook->id);
    }

    public function testGetWebhook()
    {
        $webhook = $this->_createWebhook();
        $webhook = $this->printify_webhooks->find($webhook->id);
        $this->assertInstanceOf(Webhook::class, $webhook);
        $structure = [
            'id',
            'topic',
            'url',
            'shop_id',
            'secret'
        ];
        $this->assertArrayStructure($structure, $webhook->toArray());
        $this->printify_webhooks->delete($webhook->id);
    }

    public function testCreateWebhook()
    {
        $webhook = $this->_createWebhook();
        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->printify_webhooks->delete($webhook->id);
    }

    public function testUpdateWebhook()
    {
        $webhook = $this->_createWebhook();
        $updated_url = 'https://example.com/webhooks/1';
        $webhook = $this->printify_webhooks->update($webhook->id, $updated_url);
        $this->assertEquals($updated_url, $webhook->url);
        $this->printify_webhooks->delete($webhook->id);
    }

    public function testDeleteWebhook()
    {
        $webhook = $this->_createWebhook();
        $response = $this->printify_webhooks->delete($webhook->id);
        $this->assertTrue($response);
    }

    private function _createWebhook(): Webhook
    {
        return $this->printify_webhooks->create('order:created', 'https://example.com/webhooks');
    }

}
