<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct($param) {
        \Log::info('parma is ' . $param);
    }

    public function backup($param) {
        \Log::info('parma is ' . $param);
        //\Artisan::call('backup:run --only-db');
        $filename = $this->getFilename();
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . storage_path() . "/app/backup/" . $filename;
        $returnVar = NULL;
        $output = NULL;
        exec($command, $returnVar, $output);
        return $returnVar;
        //$path = 'iron_backups/syndey_sweeney.jpg';
        //$url = 'https://sun1.tele2-kz-pavlodar.userapi.com/impg/VM8STqscsdXyznCJAm1BRZaslc_QXfm9425_kg/nrHd34UOj3E.jpg?size=1000x1500&quality=96&sign=dd6b9990876eea981d346a70a0c5d82b&type=album';
        //return $yandexDiskService->upload($path, $url);
    }

    /*private function updateToCloudDisk() {
        $httpClient = new Client();
        $baseURI = ''
        $httpClient->post('')
    }*/

    private function getFilename(): string {
        return "backup-" . now()->format('Y-m-d-H:i') . '.gz';
    }
}
