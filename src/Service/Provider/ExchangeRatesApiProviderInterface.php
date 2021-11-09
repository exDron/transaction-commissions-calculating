<?php
declare(strict_types=1);

namespace App\Service\Provider;

interface ExchangeRatesApiProviderInterface
{
    public function getEurAmount(string $currency, float $amount): float;
}
