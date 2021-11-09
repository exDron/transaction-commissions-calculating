<?php
declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\Model\TransactionDto;
use JsonException;
use RuntimeException;

class FileTransactionDataParser implements TransactionDataParserInterface
{
    /**
     * @param string $source
     * @return TransactionDto[]
     * @throws JsonException
     */
    public function parse(string $source): array
    {
        if (!is_file($source)) {
            throw new RuntimeException('Transaction data file is missing!');
        }

        $dataRows = explode(PHP_EOL, file_get_contents($source));

        $transactionsData = [];

        foreach ($dataRows as $row) {
            $decodedRow = json_decode($row, false, 512, JSON_THROW_ON_ERROR);
            $transactionDto = new TransactionDto();
            $transactionDto->bin = (int) $decodedRow->bin;
            $transactionDto->amount = (float) $decodedRow->amount;
            $transactionDto->currency = $decodedRow->currency;
            $transactionsData[] = $transactionDto;
        }

        return $transactionsData;
    }
}
