<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Model;

use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Saeed\StockGuard\Api\Data\LowStockItemInterface;
use Saeed\StockGuard\Model\Config\Config;

/**
 * Builds and sends the low-stock digest email using a transactional template.
 */
class Notifier
{
    private const TEMPLATE_ID = 'stockguard_low_stock_email_template';

    public function __construct(
        private readonly TransportBuilder $transportBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly Config $config
    ) {
    }

    /**
     * @param LowStockItemInterface[] $items
     */
    public function notify(array $items): void
    {
        if (!$this->config->isEmailEnabled() || $items === []) {
            return;
        }

        $store = $this->storeManager->getStore();

        $transport = $this->transportBuilder
            ->setTemplateIdentifier(self::TEMPLATE_ID)
            ->setTemplateOptions([
                'area' => Area::AREA_ADMINHTML,
                'store' => Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars([
                'items' => $items,
                'count' => count($items),
                'store_name' => $store->getName(),
                'threshold' => $this->config->getThreshold(),
            ])
            ->setFromByScope('general')
            ->addTo($this->config->getRecipients())
            ->getTransport();

        $transport->sendMessage();
    }
}
