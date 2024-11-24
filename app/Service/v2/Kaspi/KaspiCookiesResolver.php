<?php

namespace App\Service\v2\Kaspi;

use Symfony\Component\Process\Process;

class KaspiCookiesResolver
{
    public function resolve()
    {
        return \Cache::remember('KASPI_COOKIES', now()->addMinutes(30), function () {
            $login = config('kaspi.KASPI_MERCHANT_LOGIN');
            $password = config('kaspi.KASPI_MERCHANT_PASSWORD');
            $scriptPath = base_path('node-scripts/cookie-script.js');
            $process = new Process(['node', $scriptPath, $login, $password]);
            $process->setTimeout(300);
            $process->run();

            // Проверка на ошибки
            if (!$process->isSuccessful()) {
                \Log::error('Ошибка при получении каспи куков');
                return null;
            }

            $rawCookies = $process->getOutput();
            return str_replace(["\n", "\r"], '', $rawCookies);
        });
    }
}
