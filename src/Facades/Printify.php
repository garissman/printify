<?php

declare(strict_types=1);

/**
 * This file is part of Scout Extended.
 *
 * (c) Algolia Team <contact@algolia.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Garissman\Printify\Facades;

use Garissman\Printify\PrintifyCatalog;
use Garissman\Printify\PrintifyImage;
use Garissman\Printify\PrintifyOrders;
use Garissman\Printify\PrintifyProducts;
use Garissman\Printify\PrintifyShop;
use Garissman\Printify\PrintifyWebhooks;
use Garissman\Printify\Structures\Shop;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PrintifyCatalog catalog()
 * @method static PrintifyImage image()
 * @method static PrintifyShop shop()
 * @method static PrintifyOrders order(Shop $shop = null)
 * @method static PrintifyProducts product(Shop $shop = null)
 * @method static PrintifyWebhooks webhook(Shop $shop = null)
 */
class Printify extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return \Garissman\Printify\Printify::class;
    }
}
