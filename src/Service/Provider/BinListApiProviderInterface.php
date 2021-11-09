<?php
declare(strict_types=1);

namespace App\Service\Provider;

interface BinListApiProviderInterface
{
    public function getCoefficient(int $bin): float;
}
