<?php
declare(strict_types=1);

use App\Service\CalculateCommissionService;
use App\Service\Model\TransactionDto;
use App\Service\Provider\BinListApiProviderInterface;
use App\Service\Provider\ExchangeRatesApiProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CalculateCommissionsServiceTest extends TestCase
{
    private CalculateCommissionService $calculateCommissionsService;

    /**
     * @var ExchangeRatesApiProviderInterface|mixed|MockObject
     */
    private mixed $exchangeApiProvider;
    /**
     * @var BinListApiProviderInterface|mixed|MockObject
     */
    private mixed $binApiProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->exchangeApiProvider = $this->createMock(ExchangeRatesApiProviderInterface::class);
        $this->binApiProvider = $this->createMock(BinListApiProviderInterface::class);

        $this->calculateCommissionsService = new CalculateCommissionService($this->exchangeApiProvider, $this->binApiProvider);
    }

    public function testTransactionDataCalculationEURinEU(): void
    {
        $this->exchangeApiProvider->expects($this->once())
            ->method('getEurAmount')
            ->with('EUR', 100.00)
            ->willReturn(100.00)
        ;

        $this->binApiProvider->expects($this->once())
            ->method('getCoefficient')
            ->with(45717360)
            ->willReturn(0.01)
        ;

        $transactionDto = new TransactionDto();
        $transactionDto->bin = 45717360;
        $transactionDto->amount = 100.00;
        $transactionDto->currency = 'EUR';

        $results = $this->calculateCommissionsService->calculate([$transactionDto]);

        $this->assertEquals(1, array_shift($results));
    }

    public function testTransactionDataCalculationNonEURinEU(): void
    {
        $transactionDto = new TransactionDto();
        $transactionDto->bin = 516793;
        $transactionDto->amount = 50.00;
        $transactionDto->currency = 'USD';

        $this->exchangeApiProvider->expects($this->once())
            ->method('getEurAmount')
            ->with($transactionDto->currency, $transactionDto->amount)
            ->willReturn(43.00)
        ;

        $this->binApiProvider->expects($this->once())
            ->method('getCoefficient')
            ->with($transactionDto->bin)
            ->willReturn(0.01)
        ;

        $results = $this->calculateCommissionsService->calculate([$transactionDto]);

        $this->assertEquals(0.43, array_shift($results));
    }

    public function testTransactionDataCalculationNonEurNonEU(): void
    {
        $transactionDto = new TransactionDto();
        $transactionDto->bin = 45717360;
        $transactionDto->amount = 110000.00;
        $transactionDto->currency = 'JPY';

        $this->exchangeApiProvider->expects($this->once())
            ->method('getEurAmount')
            ->with($transactionDto->currency, $transactionDto->amount)
            ->willReturn(76.5)
        ;

        $this->binApiProvider->expects($this->once())
            ->method('getCoefficient')
            ->with($transactionDto->bin)
            ->willReturn(0.02)
        ;

        $results = $this->calculateCommissionsService->calculate([$transactionDto]);

        $this->assertEquals(1.53, array_shift($results));
    }

    public function testTransactionDataCalculationWrongData(): void
    {
        $this->expectException(RuntimeException::class);
        $this->calculateCommissionsService->calculate([]);
    }
}
