<?php

namespace Garissman\Printify\Console\Commands;

use Exception;
use Garissman\Printify\Facades\Printify;
use Garissman\Printify\Structures\Shop;
use Garissman\Printify\Structures\Webhook;
use Garissman\Printify\Structures\Webhooks\EventsEnum;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class RegisterPrintifyWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'printify:register-printify-webhooks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Register Printify Webhooks to your App';

    /**
     * @return void
     * @throws ConnectionException
     * @throws RequestException
     * @throws Exception
     */
    public function handle(): void
    {
        Printify::shop()
            ->all()
            ->each(function (Shop $shop) {
                /** @var Webhook $webhook */
                foreach ($shop->webhook()->all() as $webhook) {
                    $webhook->delete();
                }
                foreach (EventsEnum::cases() as $event) {
                    $shop->webhook()->create($event, config('app.url') . '/webhook/printify');
                }
            });
        $this->info('Done!!');
    }
}
