<?php

namespace App\Service;

class YandexTranslator
{
    private string $token;
    private string $sourceLangCode;
    private string $targetLangCode;

    const URL = 'https://translate.api.cloud.yandex.net/translate/v2/translate';

    public function __construct()
    {
        $this->token = env('YANDEX_TRANSLATE_SECRET');
        $this->sourceLangCode = 'ru';
        $this->targetLangCode = 'kk';
    }

    public function translate($text)
    {
        $headers = [
            'Content-Type: application/json',
            "Authorization: Api-Key $this->token"
        ];

        $post_data = [
            "targetLanguageCode" => $this->targetLangCode,
            'sourceLanguageCode' => $this->sourceLangCode,
            "texts" => [$text],
        ];

        $data_json = json_encode($post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_URL, self::URL);
        curl_setopt($curl, CURLOPT_POST, true);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;

    }
}
