<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Api;

use Saeed\StockGuard\Api\Data\LowStockItemInterface;

/**
 * Repository contract exposed over REST (see etc/webapi.xml) and consumed
 * internally by the GraphQL resolver, cron job and CLI command.
 */
interface LowStockRepositoryInterface
{
    /**
     * Return all products at or below the given threshold.
     *
     * @param int|null $threshold Falls back to system config when null.
     * @return LowStockItemInterface[]
     */
    public function getList(?int $threshold = null): array;
}
