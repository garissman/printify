<?php

namespace Printify;

use Illuminate\Contracts\Container\Container;

class Printify
{
    public function __construct(private Container $container)
    {
        $this->printify_api = new PrintifyApiClient(config("PRINTIFY_API_TOKEN"));
    }

    public function catalog(): PrintifyCatalog
    {
        return new PrintifyCatalog($this->printify_api);
    }
}
