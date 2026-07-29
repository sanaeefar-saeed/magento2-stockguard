<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Model;

use Saeed\StockGuard\Api\LowStockRepositoryInterface;
use Saeed\StockGuard\Model\Config\Config;
use Magento\Framework\Exception\LocalizedException;

/**
 * Thin repository facade in front of the scanner — this is the class bound to
 * the REST endpoint in webapi.xml.
 */
class LowStockRepository implements LowStockRepositoryInterface
{
    public function __construct(
        private readonly LowStockScanner $scanner,
        private readonly Config $config
    ) {
    }

    public function getList(?int $threshold = null): array
    {
        if (!$this->config->isEnabled()) {
            throw new LocalizedException(__('Stock Guard is disabled in configuration.'));
        }

        return $this->scanner->scan($threshold);
    }
}
