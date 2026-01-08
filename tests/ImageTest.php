<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\PrintifyImage;

class ImageTest extends TestCase
{
    public $printify_image = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->printify_image = new PrintifyImage($this->api);
    }

    public function test_images_all()
    {
        $images = $this->printify_image->all();
        $this->assertTrue(
            $images instanceof \Illuminate\Support\Collection ||
            $images instanceof \Illuminate\Pagination\LengthAwarePaginator
        );
    }
}
