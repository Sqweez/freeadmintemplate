<?php

namespace App\Listeners;

use App\Http\Controllers\Services\YandexDiskService;
use Spatie\Backup\Events\BackupZipWasCreated;

class BackupCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param BackupZipWasCreated $event
     * @return \Psr\Http\Message\StreamInterface
     */
    public function handle(BackupZipWasCreated $event)
    {
        $pathToZip = $event->pathToZip;
        $filename = explode("\/", $pathToZip);
        $path = 'iron_backups/' . "backup-" . now()->format('Y-m-d-H:i') . '.zip';
        \Log::info('path is ' . $path);
        return (new YandexDiskService())->upload($path, $pathToZip);
    }
}
