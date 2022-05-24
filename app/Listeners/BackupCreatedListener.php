<?php

namespace App\Listeners;

use App\Http\Controllers\Services\YandexDiskService;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;
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
     * @return StreamInterface
     * @throws GuzzleException
     */
    public function handle(BackupZipWasCreated $event): StreamInterface {
        $pathToZip = $event->pathToZip;
        $filename = explode("/", $pathToZip);
        $path = 'iron_backups/' . "backup-" . collect($filename)->last();
        $pathToZip = 'https://ironadmin.ariesdev.kz/storage/backups/Laravel/' . collect($filename)->last();
        return (new YandexDiskService())->upload($path, $pathToZip);
    }
}
