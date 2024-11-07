<?php

namespace Garissman\Printify\Structures;

use Garissman\Printify\Facades\Printify;
use Garissman\Printify\PrintifyProducts;
use Garissman\Printify\PrintifyWebhooks;

/**
 * @property string $id
 */
class Shop extends BaseStructure
{
    public function product(): PrintifyProducts
    {
        return Printify::product($this);
    }

    public function webhook(): PrintifyWebhooks
    {
        return Printify::webhook($this);
    }
}
