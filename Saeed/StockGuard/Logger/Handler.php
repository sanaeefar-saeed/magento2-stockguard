<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

class Handler extends Base
{
    protected $loggerType = MonologLogger::INFO;

    protected $fileName = '/var/log/stockguard.log';
}
