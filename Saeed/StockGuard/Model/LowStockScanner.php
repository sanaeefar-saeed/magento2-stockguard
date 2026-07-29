<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use Saeed\StockGuard\Api\Data\LowStockItemInterface;
use Saeed\StockGuard\Api\Data\LowStockItemInterfaceFactory;
use Saeed\StockGuard\Model\Config\Config;

/**
 * The heart of the module: scans the catalog for products whose salable qty
 * is at or below the threshold. Kept free of transport concerns so REST,
 * GraphQL, cron and CLI can all reuse it.
 */
class LowStockScanner
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StockRegistryInterface $stockRegistry,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly LowStockItemInterfaceFactory $itemFactory,
        private readonly Config $config,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param int|null $threshold
     * @return LowStockItemInterface[]
     */
    public function scan(?int $threshold = null): array
    {
        $threshold ??= $this->config->getThreshold();
        $results = [];

        $criteria = $this->searchCriteriaBuilder
            ->addFilter('status', 1)
            ->create();

        $products = $this->productRepository->getList($criteria)->getItems();

        foreach ($products as $product) {
            try {
                $stockItem = $this->stockRegistry->getStockItem($product->getId());
                $qty = (float)$stockItem->getQty();

                if ($stockItem->getManageStock() && $qty <= $threshold) {
                    /** @var LowStockItemInterface $item */
                    $item = $this->itemFactory->create();
                    $item->setProductId((int)$product->getId())
                        ->setSku((string)$product->getSku())
                        ->setName((string)$product->getName())
                        ->setQty($qty);
                    $results[] = $item;
                }
            } catch (\Throwable $e) {
                $this->logger->error(
                    sprintf('StockGuard: failed reading stock for product %s: %s', $product->getId(), $e->getMessage())
                );
            }
        }

        return $results;
    }
}
