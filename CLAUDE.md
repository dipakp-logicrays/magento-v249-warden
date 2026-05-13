# CLAUDE.md - Quick Reference

**Cellular Israel** - Magento 2 Luma Demo

For comprehensive documentation, see `.claude/docs/full-project-guide.md`

---

## 🎯 Essential Info

- **Host Working Directory**: `/home/logicrays/warden-sites/m249`
- **Container Working Directory**: `/var/www/html`
- **Environment**: Warden (Docker-based development)
- **Magento Version**: 2.4.9 (Community Edition — Luma demo)
- **PHP**: 8.5 | **Database**: MySQL 8.0 (MariaDB 11.8/12.3 also supported)
- **Search**: OpenSearch 3.3 | **Queue**: RabbitMQ 4.2 | **Cache**: Redis 7.2
- **Composer**: 2.9 (Magento 2.4.9 requires 2.9.3+)
- **Mode**: Developer mode
- **Current Branch**: `main` (fresh setup)

---

## 🔧 Essential Commands

```bash
# Warden Environment
# Access PHP container shell (run commands inside container)
warden shell

# Standard workflow after code changes (inside container)
bin/magento setup:upgrade && bin/magento cache:clean

# Module management (inside container)
bin/magento module:enable Vendor_Module
bin/magento module:status Vendor_Module

# Cache (disable heavy caches for development)
bin/magento cache:disable block_html full_page layout
bin/magento cache:clean config layout

# Compilation & Deploy (inside container)
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f

# Warden container management (from host)
warden env up      # Start environment
warden env down    # Stop environment
warden env ps      # Check container status

# If you want to run db sql query, use warden db connect command
# Fresh Warden setup uses the default 'magento' database. Confirm the
# active db name in app/etc/env.php after install, then use:
warden db connect -e "USE magento; SELECT * FROM core_config_data LIMIT 5;"
```

## 🔍 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| Module not loading | Check `app/etc/config.php` |
| Changes not reflecting | `bin/magento cache:clean` |
| DI errors | `bin/magento setup:di:compile` |
| DB schema issues | `bin/magento setup:upgrade` |

---

## 📚 Reference Documentation

**Coding Standards** (load only when needed for specific tasks):
- PHP & DocBlock: `.claude/docs/php-coding-standards.md`
- JavaScript/jQuery: `.claude/docs/javascript-jquery-standards.md`
- LESS & HTML: `.claude/docs/less-html-standards.md`
- Technical Architecture: `.claude/docs/technical-guidelines.md`
- Security & Performance: `.claude/docs/security-performance-standards.md`

**External Resources**:
- Magento DevDocs: https://developer.adobe.com/commerce/docs/
- Adobe Commerce PHP: https://developer.adobe.com/commerce/php/development/

---

## 💡 Best Practices Summary

1. **Plugins over rewrites** - Use interceptors for modifications
2. **Factories for objects** - Never use `new` for Magento objects
3. **Type safety** - Always declare types (PHP 8.4+)
4. **No static methods** - Use dependency injection
5. **Avoid inheritance** - Prefer composition
6. **Multi-store aware** - Always consider store context
7. **RTL support** - Consider Hebrew/English layouts

---

## 📝 Git Commit Rules

- **Never** add `Co-Authored-By: Claude Opus 4.7 <noreply@anthropic.com>` to commit messages

---

## 📝 Response Formatting Rules

- **Always use relative full paths from project root** when referencing files in responses. Never use just the filename or a partial path.
  - **Correct**: `app/code/LR/ProvisionOrder/Helper/Data.php:1434`
  - **Wrong**: `Helper/Data.php:1434` or `Data.php:1434`
  - **Correct**: `app/code/LR/ProvisionOrder/view/frontend/email/provision_order_device_returned_email_template.html`
  - **Wrong**: `provision_order_device_returned_email_template.html`

---

**Token-Optimized**: This file contains only essential information. For detailed documentation, module lists, extensive examples, and troubleshooting guides, see `.claude/docs/` directory.

