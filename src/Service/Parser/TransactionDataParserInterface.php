<?php
declare(strict_types=1);

namespace App\Service\Parser;

interface TransactionDataParserInterface
{
    public function parse(string $source): array;
}
