# Database Operations with Warden

Common database operations for Warden-based Magento 2 development.

## Database Configuration
- **Database**: `magento`
- **User**: `magento` / **Password**: `magento`
- **Root Password**: `magento`
- **MySQL Version**: 8.0.44 *(Adobe Commerce 2.4.9 also officially supports MariaDB 11.8 / 12.3 — switch via `MYSQL_DISTRIBUTION` in `.env` if needed.)*

---

## Connection

### Connect to Database
```bash
# Interactive connection (magento user)
warden db connect

# Execute query
warden db connect -e "SHOW DATABASES;"

# As root user
warden env exec db mysql -uroot -pmagento

# Execute query as root
warden env exec db mysql -uroot -pmagento -e "SHOW DATABASES;"
```

---

## Database Management

### Create Database
```bash
# Create database (requires root)
warden env exec db mysql -uroot -pmagento -e "CREATE DATABASE testdb;"

# Create with UTF-8 charset
warden env exec db mysql -uroot -pmagento -e "CREATE DATABASE testdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Grant permissions to magento user
warden env exec db mysql -uroot -pmagento -e "GRANT ALL PRIVILEGES ON testdb.* TO 'magento'@'%'; FLUSH PRIVILEGES;"
```

### List & Drop Database
```bash
# List all databases
warden env exec db mysql -uroot -pmagento -e "SHOW DATABASES;"

# Drop database
warden env exec db mysql -uroot -pmagento -e "DROP DATABASE IF EXISTS testdb;"
```

### Database Size
```bash
# Check database size
warden env exec db mysql -uroot -pmagento -e "
SELECT
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
GROUP BY table_schema;"
```

### Tables
```bash
# Show tables
warden db connect -e "SHOW TABLES;"

# Show table sizes (top 20)
warden db connect -e "
SELECT
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'magento'
ORDER BY (data_length + index_length) DESC
LIMIT 20;"
```

---

## Backup & Restore

### Quick backup — `bin/db_backup`

A wrapper script lives at [`bin/db_backup`](../bin/db_backup) that handles the common case (existence check, timestamped path, gzip, human-readable size) in one command. Run from the project root:

```bash
# Default: dumps the 'magento' database into backup/db/daily/DD-MM-YYYY/...sql.gz
./bin/db_backup

# Back up a different database
./bin/db_backup mage249_test

# Override defaults via env vars
DB_USER=root DB_PASS=magento BACKUP_BASE_DIR=/tmp/dumps ./bin/db_backup

# See full usage
./bin/db_backup --help
```

Output filename pattern: `backup/db/daily/<DD-MM-YYYY>/<dbname>_<YYYYMMDD_HHMMSS>.sql.gz`.

### Manual backup commands
```bash
# Full backup (default magento database)
warden db dump > backup_$(date +%Y%m%d_%H%M%S).sql

# Compressed backup (use -T to disable TTY when piping)
warden env exec -T db mysqldump -uroot -pmagento magento | gzip > backup.sql.gz

# Backup a specific database (e.g., testdb)
warden env exec -T db mysqldump -uroot -pmagento testdb > testdb_backup.sql
warden env exec -T db mysqldump -uroot -pmagento testdb | gzip > testdb_backup.sql.gz
```

### Restore (Default `magento` Database)
```bash
# Restore from backup
warden db import < backup.sql

# Restore compressed backup
gunzip < backup.sql.gz | warden db import
```

### Import into a Specific Database (e.g., `testdb`)

> **Note:** `warden db import` always targets the default `magento` database.
> To import into a different database like `testdb`, use `warden env exec db mysql` with the database name.

```bash
# Step 1: Create the database (if it doesn't exist)
warden env exec db mysql -uroot -pmagento -e "CREATE DATABASE IF NOT EXISTS testdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
warden env exec db mysql -uroot -pmagento -e "GRANT ALL PRIVILEGES ON testdb.* TO 'magento'@'%'; FLUSH PRIVILEGES;"

# Step 2: Import from a .sql file (use -T flag to disable TTY when piping/redirecting)
warden env exec -T db mysql -uroot -pmagento testdb < backup.sql

# Import from a compressed .sql.gz file
gunzip < backup.sql.gz | warden env exec -T db mysql -uroot -pmagento testdb

# Clone the existing magento database into testdb
warden env exec -T db mysqldump -uroot -pmagento magento | warden env exec -T db mysql -uroot -pmagento testdb
```

### Export Database

> **Tip:** Use `-T` flag to disable TTY allocation when piping or redirecting output.

```bash
# Export default magento database to a .sql file
warden env exec -T db mysqldump -uroot -pmagento magento > export_$(date +%Y%m%d_%H%M%S).sql

# Export with compression (.sql.gz)
warden env exec -T db mysqldump -uroot -pmagento magento | gzip > export_$(date +%Y%m%d_%H%M%S).sql.gz

# Export a specific database (e.g., testdb)
warden env exec -T db mysqldump -uroot -pmagento testdb > testdb_export.sql
warden env exec -T db mysqldump -uroot -pmagento testdb | gzip > testdb_export.sql.gz

# Export specific tables only
warden env exec -T db mysqldump -uroot -pmagento magento catalog_product_entity catalog_category_entity > tables_export.sql

# Export structure only (no data)
warden env exec -T db mysqldump -uroot -pmagento --no-data magento > structure_only.sql

# Export data only (no CREATE TABLE statements)
warden env exec -T db mysqldump -uroot -pmagento --no-create-info magento > data_only.sql

# Export excluding specific tables (e.g., log/report tables)
warden env exec -T db mysqldump -uroot -pmagento magento \
  --ignore-table=magento.report_event \
  --ignore-table=magento.report_viewed_product_index \
  --ignore-table=magento.customer_log \
  --ignore-table=magento.customer_visitor \
  | gzip > export_no_logs.sql.gz
```

---

## Magento Operations

### Reset Admin Password
```bash
warden db connect -e "
UPDATE admin_user
SET password = CONCAT(SHA2('NewPassword123', 256), ':xxxxxxxx:1')
WHERE username = 'admin';"
```

### Disable Two-Factor Authentication
```bash
warden db connect -e "DELETE FROM core_config_data WHERE path LIKE 'twofactorauth%';"
warden shell -c "bin/magento cache:flush"
```

### Reset Base URLs
```bash
warden db connect -e "
UPDATE core_config_data
SET value = 'https://app.mage249.test/'
WHERE path IN ('web/unsecure/base_url', 'web/secure/base_url');"
```

### Check Configuration
```bash
warden db connect -e "
SELECT * FROM core_config_data
WHERE path LIKE '%base_url%'
   OR path LIKE '%secure%';"
```

### Clear Log Tables
```bash
warden db connect -e "
TRUNCATE table report_event;
TRUNCATE table report_viewed_product_index;
TRUNCATE table customer_log;
TRUNCATE table customer_visitor;"
```

---

## Troubleshooting

### Connection Issues
```bash
# Check container status
warden env ps

# View database logs
warden env logs db

# Restart database
warden env restart db
```

### Import Errors
```bash
# Error: "the input device is not a TTY"
# Add -T flag to disable TTY allocation when piping or redirecting input
# Wrong:  gunzip < backup.sql.gz | warden env exec db mysql -uroot -pmagento testdb
# Right:  gunzip < backup.sql.gz | warden env exec -T db mysql -uroot -pmagento testdb

# Error: MySQL server has gone away
# Increase max_allowed_packet
warden env exec db mysql -uroot -pmagento -e "SET GLOBAL max_allowed_packet=1073741824;"
```

---

## Quick Reference

```bash
# Connect
warden db connect

# Quick wrapped backup (recommended)
./bin/db_backup

# Manual backup
warden db dump > backup.sql

# Restore (default magento DB)
warden db import < backup.sql

# Import into a specific database (e.g., testdb) — use -T when piping/redirecting
warden env exec -T db mysql -uroot -pmagento testdb < backup.sql

# Export database
warden env exec -T db mysqldump -uroot -pmagento magento > export.sql

# Export compressed
warden env exec -T db mysqldump -uroot -pmagento magento | gzip > export.sql.gz

# Clone magento DB to testdb
warden env exec -T db mysqldump -uroot -pmagento magento | warden env exec -T db mysql -uroot -pmagento testdb

# Create database
warden env exec db mysql -uroot -pmagento -e "CREATE DATABASE dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Execute query
warden db connect -e "SHOW TABLES;"

# Container shell
warden env exec db bash
```

---

**Last Updated**: May 13, 2026
