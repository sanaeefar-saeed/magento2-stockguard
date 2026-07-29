<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Model\Data;

use Magento\Framework\DataObject;
use Saeed\StockGuard\Api\Data\LowStockItemInterface;

/**
 * Lightweight DTO backed by DataObject so it serializes cleanly for the
 * Web API framework without a database table.
 */
class LowStockItem extends DataObject implements LowStockItemInterface
{
    public function getSku(): string
    {
        return (string)$this->getData(self::SKU);
    }

    public function setSku(string $sku): LowStockItemInterface
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    public function setName(string $name): LowStockItemInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getQty(): float
    {
        return (float)$this->getData(self::QTY);
    }

    public function setQty(float $qty): LowStockItemInterface
    {
        return $this->setData(self::QTY, $qty);
    }

    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int $productId): LowStockItemInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }
}
