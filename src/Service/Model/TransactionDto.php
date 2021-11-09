<?php
declare(strict_types=1);

namespace App\Service\Model;

class TransactionDto
{
    public int $bin;
    public float $amount;
    public string $currency;
}
