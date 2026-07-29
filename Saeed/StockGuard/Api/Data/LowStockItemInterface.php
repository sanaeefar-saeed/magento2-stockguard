<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Api\Data;

/**
 * Data contract for a single low-stock item.
 *
 * Service-contract interface so the same DTO is reusable across REST, GraphQL,
 * the admin grid and CLI — a single source of truth for the shape of the data.
 */
interface LowStockItemInterface
{
    public const SKU = 'sku';
    public const NAME = 'name';
    public const QTY = 'qty';
    public const PRODUCT_ID = 'product_id';

    public function getSku(): string;

    public function setSku(string $sku): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getQty(): float;

    public function setQty(float $qty): self;

    public function getProductId(): int;

    public function setProductId(int $productId): self;
}
