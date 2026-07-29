<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Logger;

use Monolog\Logger as MonologLogger;

/**
 * Dedicated channel so module activity lands in var/log/stockguard.log.
 */
class Logger extends MonologLogger
{
}
