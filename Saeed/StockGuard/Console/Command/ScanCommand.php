<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Saeed\StockGuard\Model\LowStockScanner;

/**
 * CLI:  bin/magento saeed:stockguard:scan [--threshold=10]
 * Prints a table of low-stock products — handy for ops and CI checks.
 */
class ScanCommand extends Command
{
    public function __construct(
        private readonly LowStockScanner $scanner,
        private readonly State $appState,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('saeed:stockguard:scan')
            ->setDescription('Scan the catalog for low-stock products')
            ->addOption('threshold', 't', InputOption::VALUE_OPTIONAL, 'Override the configured threshold');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Throwable $e) {
            // Area already set — safe to ignore.
        }

        $threshold = $input->getOption('threshold');
        $items = $this->scanner->scan($threshold !== null ? (int)$threshold : null);

        if ($items === []) {
            $output->writeln('<info>No low-stock products found. </info>');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Product ID', 'SKU', 'Name', 'Qty']);
        foreach ($items as $item) {
            $table->addRow([$item->getProductId(), $item->getSku(), $item->getName(), $item->getQty()]);
        }
        $table->render();

        $output->writeln(sprintf('<comment>%d product(s) at or below threshold.</comment>', count($items)));
        return Command::SUCCESS;
    }
}
