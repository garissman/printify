<?php

namespace Garissman\Printify\Structures;

use Garissman\Printify\Facades\Printify;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

/**
 * @property Shop $shop
 * @property string $title
 */
class Product extends BaseStructure
{

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function publishingSucceeded(): bool
    {
        return Printify::product($this->shop)
            ->publishing_succeeded(
                $this->id,
                route('store.front.product.view', ['product' => $this->title]),
            );
    }
}
