<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\TransactionDto;
use App\Service\Provider\BinListApiProviderInterface;
use App\Service\Provider\ExchangeRatesApiProviderInterface;
use Exception;
use RuntimeException;

final class CalculateCommissionService
{
    private ExchangeRatesApiProviderInterface $ratesApiProvider;
    private BinListApiProviderInterface $binListApiProvider;

    /**
     * @throws Exception
     * @param ExchangeRatesApiProviderInterface $ratesApiProvider
     * @param BinListApiProviderInterface $binListApiProvider
     */
    public function __construct(
        ExchangeRatesApiProviderInterface $ratesApiProvider,
        BinListApiProviderInterface $binListApiProvider
    ) {
        $this->ratesApiProvider = $ratesApiProvider;
        $this->binListApiProvider = $binListApiProvider;
    }

    /**
     * @param TransactionDto[] $transactionsData
     * @return array
     */
    public function calculate(array $transactionsData): array
    {
        if (count($transactionsData) === 0) {
            throw new RuntimeException('Transaction data are missed!');
        }

        $commissions = [];

        foreach ($transactionsData as $transaction) {
            $eurAmount = $this->ratesApiProvider->getEurAmount($transaction->currency, $transaction->amount);
            $coefficient = $this->binListApiProvider->getCoefficient($transaction->bin);
            $commissions[] = round($eurAmount * $coefficient, 2);
        }

        return $commissions;
    }
}
