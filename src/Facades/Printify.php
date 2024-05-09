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

/**
 * @method static SearchIndex index($searchable)
 * @method static SearchClient client()
 * @method static AnalyticsClient analytics()
 * @method static string searchKey($searchable)
 *
 */
class Printify extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return 'algolia';
    }
}
