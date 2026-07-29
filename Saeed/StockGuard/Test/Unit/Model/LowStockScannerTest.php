<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Test\Unit\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Saeed\StockGuard\Api\Data\LowStockItemInterfaceFactory;
use Saeed\StockGuard\Model\Config\Config;
use Saeed\StockGuard\Model\Data\LowStockItem;
use Saeed\StockGuard\Model\LowStockScanner;

class LowStockScannerTest extends TestCase
{
    private LowStockScanner $scanner;
    private $stockRegistry;
    private $productRepository;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->stockRegistry = $this->createMock(StockRegistryInterface::class);

        $criteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $criteriaBuilder->method('addFilter')->willReturnSelf();
        $criteriaBuilder->method('create')->willReturn($this->createMock(SearchCriteria::class));

        $itemFactory = $this->createMock(LowStockItemInterfaceFactory::class);
        $itemFactory->method('create')->willReturnCallback(fn () => new LowStockItem());

        $config = $this->createMock(Config::class);
        $config->method('getThreshold')->willReturn(5);

        $this->scanner = new LowStockScanner(
            $this->productRepository,
            $this->stockRegistry,
            $criteriaBuilder,
            $itemFactory,
            $config,
            $this->createMock(LoggerInterface::class)
        );
    }

    public function testScanReturnsOnlyLowStockItems(): void
    {
        $lowProduct = $this->makeProduct(1, 'SKU-LOW', 'Low Product');
        $okProduct = $this->makeProduct(2, 'SKU-OK', 'Healthy Product');

        $searchResults = $this->createMock(SearchResults::class);
        $searchResults->method('getItems')->willReturn([$lowProduct, $okProduct]);
        $this->productRepository->method('getList')->willReturn($searchResults);

        $this->stockRegistry->method('getStockItem')->willReturnCallback(
            fn ($id) => $this->makeStockItem($id === 1 ? 2.0 : 50.0)
        );

        $result = $this->scanner->scan();

        $this->assertCount(1, $result);
        $this->assertSame('SKU-LOW', $result[0]->getSku());
        $this->assertSame(2.0, $result[0]->getQty());
    }

    private function makeProduct(int $id, string $sku, string $name)
    {
        $product = $this->getMockBuilder(\Magento\Catalog\Api\Data\ProductInterface::class)
            ->disableOriginalConstructor()
            ->addMethods([])
            ->getMockForAbstractClass();
        $product->method('getId')->willReturn($id);
        $product->method('getSku')->willReturn($sku);
        $product->method('getName')->willReturn($name);
        return $product;
    }

    private function makeStockItem(float $qty): StockItemInterface
    {
        $stockItem = $this->createMock(StockItemInterface::class);
        $stockItem->method('getQty')->willReturn($qty);
        $stockItem->method('getManageStock')->willReturn(true);
        return $stockItem;
    }
}
