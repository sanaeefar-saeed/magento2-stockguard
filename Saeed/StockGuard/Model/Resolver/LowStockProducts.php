<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Saeed\StockGuard\Model\Config\Config;
use Saeed\StockGuard\Model\LowStockScanner;

/**
 * GraphQL resolver for `lowStockProducts` — lets a headless/PWA storefront or
 * an admin dashboard pull low-stock data without REST round-trips.
 */
class LowStockProducts implements ResolverInterface
{
    public function __construct(
        private readonly LowStockScanner $scanner,
        private readonly Config $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!$this->config->isEnabled()) {
            throw new GraphQlInputException(__('Stock Guard is disabled.'));
        }

        $threshold = isset($args['threshold']) ? (int)$args['threshold'] : $this->config->getThreshold();
        $items = $this->scanner->scan($threshold);

        return [
            'total_count' => count($items),
            'threshold' => $threshold,
            'items' => array_map(static fn ($item) => [
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'qty' => $item->getQty(),
                'product_id' => $item->getProductId(),
            ], $items),
        ];
    }
}
