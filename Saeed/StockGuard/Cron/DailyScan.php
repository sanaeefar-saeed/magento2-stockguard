<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Cron;

use Saeed\StockGuard\Model\Config\Config;
use Saeed\StockGuard\Model\LowStockScanner;
use Saeed\StockGuard\Model\Notifier;

/**
 * Scheduled daily scan (see crontab.xml). Collects every low-stock item and
 * sends a single digest email.
 */
class DailyScan
{
    public function __construct(
        private readonly Config $config,
        private readonly LowStockScanner $scanner,
        private readonly Notifier $notifier
    ) {
    }

    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $items = $this->scanner->scan();
        if ($items !== []) {
            $this->notifier->notify($items);
        }
    }
}
