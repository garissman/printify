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

namespace Printify\Facades;

use Illuminate\Support\Facades\Facade;
use Printify\PrintifyCatalog;
use Printify\PrintifyImage;
use Printify\PrintifyOrders;
use Printify\PrintifyProducts;
use Printify\PrintifyShop;
use Printify\PrintifyWebhooks;

/**
 * @method static PrintifyCatalog catalog()
 * @method static PrintifyImage image()
 * @method static PrintifyOrders order()
 * @method static PrintifyProducts product()
 * @method static PrintifyShop shop()
 * @method static PrintifyWebhooks webhook()
 *
 */
class Printify extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return \Printify\Printify::class;
    }
}
