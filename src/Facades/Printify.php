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
use Illuminate\Support\Facades\Facade;

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
