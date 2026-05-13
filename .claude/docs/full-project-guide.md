# Full Project Guide

**⚠️ DEPRECATED - Most content moved to CLAUDE.md and split standard files**

This file has been deprecated in favor of the token-optimized structure. For up-to-date information, see:

## Current Documentation Structure

### Always Loaded
- **CLAUDE.md** (root) - Essential project info, commands, quick reference

### Load on Demand
- **php-coding-standards.md** - PHP formatting & DocBlocks
- **javascript-jquery-standards.md** - JavaScript & jQuery
- **less-html-standards.md** - LESS/CSS & HTML
- **technical-guidelines.md** - Magento architecture
- **security-performance-standards.md** - Security & performance

---

## Quick Reference (Legacy Content)

### Project Overview
- **Magento Version**: 2.4.8-p3 (Community Edition)
- **PHP**: 8.4 | **Database**: MySQL 8.0.43
- **Environment**: Warden (Docker-based)
- **Working Directory**: `/home/logicrays/warden-sites/ci`

### Key Custom Module Namespaces
- **LR/** - Core business logic modules
- **Cellularisrael/** - Domain-specific Israeli telecom modules
- **Overwrite/** - Third-party overrides

### Payment Integrations
- **CardknoxDevelopment** - Cardknox payment gateway
- **Payplus** - PayPlus payment gateway (Israeli)

### Essential Commands
```bash
# Standard workflow after code changes
bin/magento setup:upgrade && bin/magento cache:clean

# Module management
bin/magento module:enable Vendor_Module
bin/magento module:status Vendor_Module

# Cache (disable heavy caches for development)
bin/magento cache:disable block_html full_page layout
```

### Multi-Store Considerations
Always consider store scope when working with configurations:
```php
// Get store-specific configuration
$value = $this->scopeConfig->getValue(
    $path,
    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
    $storeId
);
```

---

## Migration Note

**Date**: 2025-12-22

This file previously contained comprehensive project documentation but has been optimized to reduce token usage. All essential information is now in CLAUDE.md, which is always loaded and kept up-to-date.

For detailed coding standards, load the appropriate standard file only when needed for your specific task.

---

**For complete and current information, see CLAUDE.md in the project root.**
