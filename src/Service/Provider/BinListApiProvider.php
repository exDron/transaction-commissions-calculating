<?php
declare(strict_types=1);

namespace App\Service\Provider;

use App\Service\Http\HttpClientInterface;
use JsonException;

class BinListApiProvider implements BinListApiProviderInterface
{
    private HttpClientInterface $httpClient;
    private string $binApiUrl;
    private array $euCountries;
    private float $euCoefficient;
    private float $nonEuCoefficient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->binApiUrl = $_ENV['binApiUrl'];
        $this->euCountries = $_ENV['euCountries'];
        $this->euCoefficient = (float)$_ENV['euCoefficient'];
        $this->nonEuCoefficient = (float)$_ENV['nonEuCoefficient'];
    }

    /**
     * @param int $bin
     *
     * @return float
     *
     * @throws JsonException
     */
    public function getCoefficient(int $bin): float
    {
        $result = $this->httpClient->request($this->binApiUrl . $bin);
        $binResult = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        return in_array($binResult->country->alpha2, $this->euCountries, true) ? $this->euCoefficient : $this->nonEuCoefficient;
    }
}
