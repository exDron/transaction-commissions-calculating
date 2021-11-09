<?php
declare(strict_types=1);

namespace App\Service\Http;

interface HttpClientInterface
{
    public function request(string $url, string $method, array $data);
}
