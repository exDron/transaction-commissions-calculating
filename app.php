<?php
declare(strict_types=1);

require './vendor/autoload.php';

use App\Service\CommissionCalculator;
use App\Service\Http\CurlHttpClient;
use App\Service\Parser\FileTransactionDataParser;
use App\Service\Provider\BinListApiProvider;
use App\Service\Provider\ExchangeRatesApiProvider;

try {
    $arguments = $_SERVER['argv'];
    $file = $arguments[1] ?? null;

    $configFile = __DIR__ . '/src/config/config.php';

    if (!is_readable($configFile)) {
        throw new RuntimeException('Config file is missing or permissions are wrong!');
    }

    $config = require $configFile;
    $_ENV = array_merge($_ENV, $config);

    $dataParserService = new FileTransactionDataParser();
    $parsedData = $dataParserService->parse($file);

    $httpClient = new CurlHttpClient();
    $ratesApiProvider = new ExchangeRatesApiProvider($httpClient);
    $binListApiProvider = new BinListApiProvider($httpClient);

    $commissionCalculator = new CommissionCalculator($ratesApiProvider, $binListApiProvider);
    $commissions = $commissionCalculator->calculate($parsedData);

    foreach ($commissions as $commission) {
        echo $commission . "\r\n";
    }
    exit;

} catch (Exception $exception) {
    echo "Something went wrong: {$exception->getMessage()} \r\n";
}
