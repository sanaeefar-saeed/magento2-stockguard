<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Saeed\StockGuard\Model\Config\Config;
use Saeed\StockGuard\Model\Data\LowStockItemFactory;
use Saeed\StockGuard\Model\Notifier;

/**
 * Fires on catalog_product_save_after: if a single product drops to/below the
 * threshold, send an immediate alert rather than waiting for the daily cron.
 */
class ProductSaveObserver implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly StockRegistryInterface $stockRegistry,
        private readonly Notifier $notifier,
        private readonly LowStockItemFactory $itemFactory
    ) {
    }

    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        /** @var ProductInterface $product */
        $product = $observer->getEvent()->getData('product');
        if (!$product || !$product->getId()) {
            return;
        }

        $stockItem = $this->stockRegistry->getStockItem((int)$product->getId());
        $qty = (float)$stockItem->getQty();

        if ($stockItem->getManageStock() && $qty <= $this->config->getThreshold()) {
            $item = $this->itemFactory->create()
                ->setProductId((int)$product->getId())
                ->setSku((string)$product->getSku())
                ->setName((string)$product->getName())
                ->setQty($qty);

            $this->notifier->notify([$item]);
        }
    }
}
