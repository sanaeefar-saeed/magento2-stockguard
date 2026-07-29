# Saeed_StockGuard — Magento 2 / Adobe Commerce Low-Stock Monitor

A production-grade Magento 2 module that watches your catalog for low-stock
products and surfaces them through **REST**, **GraphQL**, an **email digest**,
a **real-time observer**, a **scheduled cron**, and a **CLI command** — built to
demonstrate clean, headless-ready Magento architecture.

> Author: **Saeed Sanaeefar** — Senior Magento & Headless Commerce Engineer
> Adobe Certified Magento 2 Developer

## Why this module exists

Stockouts quietly kill conversion. StockGuard gives merchants and headless
storefronts a single source of truth for "what's about to run out", exposed
over every channel a modern Adobe Commerce stack needs.

## Features

| Capability | Entry point |
|---|---|
| REST endpoint | `GET /rest/V1/stockguard/low-stock` |
| GraphQL query | `lowStockProducts(threshold: Int)` |
| Real-time alert | `catalog_product_save_after` observer |
| Daily digest | cron `saeed_stockguard_daily_scan` (07:00) |
| CLI | `bin/magento saeed:stockguard:scan --threshold=10` |
| Admin config | Stores → Configuration → Saeed Extensions → Stock Guard |
| Dedicated log | `var/log/stockguard.log` |

## Architecture highlights

- **Service contracts** (`Api/`) decouple the data shape from transport, so the
  same DTO powers REST, GraphQL, cron and CLI.
- **Single-responsibility core** (`Model/LowStockScanner`) holds all business
  logic; every channel is a thin adapter over it.
- **Typed config reader** keeps `core_config_data` paths in one place.
- **PHP 8.1+** constructor property promotion, `readonly`, strict types throughout.
- **PSR-3 logging** via a dedicated Monolog channel.
- **PHPUnit** unit test for the scanner.

## Installation

```bash
# Composer (recommended)
composer require saeed/module-stock-guard

# or manual
cp -r Saeed app/code/Saeed
bin/magento module:enable Saeed_StockGuard
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Usage

### REST
```bash
curl -X GET "https://your-store.com/rest/V1/stockguard/low-stock?threshold=10" \
  -H "Authorization: Bearer <admin-token>"
```

### GraphQL
```graphql
{
  lowStockProducts(threshold: 10) {
    total_count
    threshold
    items { sku name qty product_id }
  }
}
```

### CLI
```bash
bin/magento saeed:stockguard:scan --threshold=10
```

## Running tests
```bash
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist \
  app/code/Saeed/StockGuard/Test/Unit
```

## Compatibility
- Magento Open Source / Adobe Commerce 2.4.4 – 2.4.7+
- PHP 8.1 / 8.2 / 8.3

## License
MIT © Saeed Sanaeefar
