<?php
declare(strict_types=1);

namespace Saeed\StockGuard\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Typed accessor over core_config_data. Keeps config paths in one place so
 * the rest of the module never touches raw string paths.
 */
class Config
{
    private const XML_ENABLED = 'stockguard/general/enabled';
    private const XML_THRESHOLD = 'stockguard/general/threshold';
    private const XML_EMAIL_ENABLED = 'stockguard/notification/email_enabled';
    private const XML_RECIPIENT = 'stockguard/notification/recipient';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getThreshold(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_THRESHOLD, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isEmailEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_EMAIL_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /** @return string[] */
    public function getRecipients(?int $storeId = null): array
    {
        $raw = (string)$this->scopeConfig->getValue(self::XML_RECIPIENT, ScopeInterface::SCOPE_STORE, $storeId);
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
}
