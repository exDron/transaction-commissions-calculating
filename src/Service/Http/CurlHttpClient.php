<?php
declare(strict_types=1);

namespace App\Service\Http;

use RuntimeException;

class CurlHttpClient implements HttpClientInterface
{
    public function request(string $url, string $method = 'GET', array $data = [])
    {
        $curl = curl_init();

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                if ($data) {
                    $url = sprintf('%s?%s', $url, http_build_query($data)); // TODO: "$url" - PhpBasic convention 3.9: We do not change argument type or value
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($curl);
        if (!$apiResponse) {
            throw new RuntimeException('Connection Failure!');
        }
        curl_close($curl);

        return $apiResponse;
    }
}
