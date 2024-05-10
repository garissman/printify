<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\PrintifyImage;
use Garissman\Printify\Tests\TestCase;

class ImageTest extends TestCase
{
    public $printify_image = null;

    protected function setUp()
    {
        parent::setUp();
        $this->printify_image = new PrintifyImage($this->api);
    }

    public function testImagesAll()
    {
        $images = $this->printify_image->all();
    }

}
