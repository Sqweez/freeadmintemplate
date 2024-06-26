<?php

namespace App\Providers;

use App\Events\Opt\WholesaleOrderCreated;
use App\Listeners\BackupCreatedListener;
use App\Listeners\Opt\CreateWholesaleOrderInvoice;
use App\Listeners\Opt\CreateWholesaleOrderWaybill;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Spatie\Backup\Events\BackupZipWasCreated;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BackupZipWasCreated::class => [
            BackupCreatedListener::class
        ],
        WholesaleOrderCreated::class => [
            CreateWholesaleOrderInvoice::class,
            CreateWholesaleOrderWaybill::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
