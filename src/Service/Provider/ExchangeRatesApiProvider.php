<?php
declare(strict_types=1);

namespace App\Service\Provider;

use App\Service\Http\HttpClientInterface;
use JsonException;
use LogicException;

class ExchangeRatesApiProvider implements ExchangeRatesApiProviderInterface
{
    private HttpClientInterface $httpClient;
    private string $defaultCurrency;
    private string $ratesApiUrl;
    private string $ratesApiKey;
    private array $rates;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->defaultCurrency = $_ENV['defaultCurrency'];
        $this->ratesApiUrl = $_ENV['ratesApiUrl'];
        $this->ratesApiKey = $_ENV['ratesApiKey'];
        $this->rates = [];
    }

    /**
     * @param string $currency
     * @param float $amount
     *
     * @return float
     *
     * @throws JsonException
     */
    public function getEurAmount(string $currency, float $amount): float
    {
        if ($currency === $this->defaultCurrency) {
            return $amount;
        }

        if (count($this->rates) === 0) {
            $url = $this->ratesApiUrl . '?access_key=' . $this->ratesApiKey . '&base=' . $this->defaultCurrency;
            $result = $this->httpClient->request($url);
            $rates = json_decode($result, true, 512, JSON_THROW_ON_ERROR)['rates'];
            $this->rates = $rates;
        }

        if (!isset($this->rates[$currency])) {
            throw new LogicException('Currency is not supported!');
        }

        return $amount / $this->rates[$currency];
    }
}
