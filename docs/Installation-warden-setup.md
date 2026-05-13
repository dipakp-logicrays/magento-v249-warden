# Magento 2.4.9 — Warden Installation Guide

Step-by-step setup for a clean **Magento 2.4.9 Community Edition (Luma demo)** environment under [Warden](https://warden.dev/), matching this repo's `.env` profile.

**Target stack:** PHP 8.5 · Composer 2.9 · OpenSearch 3.3 · RabbitMQ 4.2 · Redis 7.2 · MySQL 8.0 · Mailpit · nginx 1.28
**Hostnames:** `app.mage249.test` (storefront/admin) · `status.mage249.test` · `mailpit.mage249.test`

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Bootstrap the project directory](#2-bootstrap-the-project-directory)
3. [Start Warden](#3-start-warden)
4. [Install Magento via Composer](#4-install-magento-via-composer)
5. [Run `setup:install`](#5-run-setupinstall)
6. [Configure the application](#6-configure-the-application)
7. [Create the admin user](#7-create-the-admin-user)
8. [Final cache + index pass](#8-final-cache--index-pass)
9. [Add hosts entries](#9-add-hosts-entries-on-the-host-machine)
10. [Access URLs](#10-access-urls)
11. [Troubleshooting](#11-troubleshooting)

---

## 1. Prerequisites

- Docker + Warden installed and running on the host (`warden --version`).
- Magento Marketplace credentials (public + private keys) from <https://commercemarketplace.adobe.com/customer/accessKeys/>.
- Free ports `80`, `443`, `8025`, `9000` on the host.

---

## 2. Bootstrap the project directory

```bash
# Replace the parent path with your own Warden sites root
cd /home/logicrays/warden-sites
mkdir m249
cd m249
```

Then initialise Warden's `.env` for a Magento 2 project:

```bash
warden env-init m249 magento2
```

> **Alternative — restore from bundled archive**
> If you have `fresh-m2-setup/magento249-warden-fresh-setup.tar.gz` from this repo, extract it into the project root instead of running `env-init`:
>
> ```bash
> tar -xzf fresh-m2-setup/magento249-warden-fresh-setup.tar.gz -C /home/logicrays/warden-sites/m249/
> ```

---

## 3. Start Warden

```bash
# Bring up shared Warden services (traefik, dnsmasq, mailhog, etc.)
warden svc up

# Sign a TLS certificate for the project domain
warden sign-certificate mage249.test

# Bring up this project's containers
warden env up

# Drop into the php-fpm container — all later commands run inside it
warden shell
```

---

## 4. Install Magento via Composer

Inside the container (`warden shell`):

```bash
# Configure Marketplace credentials once (persists to ~/.composer/auth.json)
composer global config http-basic.repo.magento.com <public_key> <private_key>

# Create the project in /tmp and rsync into the project root so the
# Magento source lands directly at /var/www/html (Warden's nginx web root).
composer create-project \
  --repository-url=https://repo.magento.com/ \
  magento/project-community-edition=2.4.9 /tmp/m2-install

rsync -a /tmp/m2-install/ /var/www/html/
rm -rf /tmp/m2-install
```

> The `/tmp` + `rsync` pattern is required because `composer create-project .` refuses a non-empty target — and the project root already contains `.warden/`, `.env`, etc.

---

## 5. Run `setup:install`

```bash
bin/magento setup:install \
  --base-url=https://app.mage249.test/ \
  --base-url-secure=https://app.mage249.test/ \
  --backend-frontname=backend \
  --use-rewrites=1 \
  --use-secure=1 \
  --use-secure-admin=1 \
  --admin-firstname=Admin \
  --admin-lastname=User \
  --admin-email=admin@example.com \
  --admin-user=admin \
  --admin-password='admin@123' \
  --language=en_US \
  --currency=USD \
  --timezone=UTC \
  --db-host=db \
  --db-name=magento \
  --db-user=magento \
  --db-password=magento \
  --amqp-host=rabbitmq \
  --amqp-port=5672 \
  --amqp-user=guest \
  --amqp-password=guest \
  --search-engine=opensearch \
  --opensearch-host=opensearch \
  --opensearch-port=9200 \
  --opensearch-index-prefix=magento2 \
  --opensearch-enable-auth=0 \
  --opensearch-timeout=15 \
  --session-save=redis \
  --session-save-redis-host=redis \
  --session-save-redis-port=6379 \
  --session-save-redis-db=2 \
  --session-save-redis-max-concurrency=20 \
  --cache-backend=redis \
  --cache-backend-redis-server=redis \
  --cache-backend-redis-db=0 \
  --cache-backend-redis-port=6379 \
  --page-cache=redis \
  --page-cache-redis-server=redis \
  --page-cache-redis-db=1 \
  --page-cache-redis-port=6379
```

> **Varnish:** disabled in this repo's `.env` (`WARDEN_VARNISH=0`). Do **not** pass `--http-cache-hosts=varnish:80`. To enable Varnish later, flip the env flag, recreate the env, and rerun config.

---

## 6. Configure the application

```bash
bin/magento config:set --lock-env web/unsecure/base_url   "https://app.mage249.test/"
bin/magento config:set --lock-env web/secure/base_url     "https://app.mage249.test/"
bin/magento config:set --lock-env web/secure/offloader_header X-Forwarded-Proto
bin/magento config:set --lock-env web/secure/use_in_frontend 1
bin/magento config:set --lock-env web/secure/use_in_adminhtml 1
bin/magento config:set --lock-env web/seo/use_rewrites 1

# Full-page-cache backend: 1 = built-in (no Varnish); use 2 if Varnish is enabled
bin/magento config:set --lock-env system/full_page_cache/caching_application 1
bin/magento config:set --lock-env system/full_page_cache/ttl 604800

bin/magento config:set --lock-env catalog/search/enable_eav_indexer 1
bin/magento config:set --lock-env dev/static/sign 0

bin/magento deploy:mode:set developer
bin/magento cache:disable block_html full_page
```

---

## 7. Create the admin user

The `setup:install` step above already created `admin` / `admin@123`. To create additional admins later:

```bash
bin/magento admin:user:create \
  --admin-user=<username> \
  --admin-password='<password>' \
  --admin-email='<email>' \
  --admin-firstname=<first> \
  --admin-lastname=<last>
```

Disable Adobe 2FA modules for the local-only demo (skip this on shared/staging boxes):

```bash
bin/magento module:disable Magento_TwoFactorAuth Magento_AdminAdobeImsTwoFactorAuth
```

---

## 8. Final cache + index pass

```bash
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento indexer:reindex
bin/magento cache:flush
```

Shorter equivalents (Magento aliases) — handy during day-to-day dev:

```bash
bin/magento s:up && bin/magento s:d:c && bin/magento s:s:d -f
```

---

## 9. Add hosts entries (on the host machine)

Outside the container:

```bash
sudo nano /etc/hosts
```

Append:

```text
# Magento 2.4.9 Warden (mage249)
127.0.0.1 app.mage249.test
127.0.0.1 status.mage249.test
127.0.0.1 mailpit.mage249.test
```

---

## 10. Access URLs

| Service        | URL                                |
| -------------- | ---------------------------------- |
| Storefront     | <https://app.mage249.test/>        |
| Admin          | <https://app.mage249.test/admin> |
| Status panel   | <https://status.mage249.test/>     |
| Mailpit        | <https://mailpit.mage249.test/>    |
| Portainer      | <http://localhost:9000/>           |

---

## 11. Troubleshooting

| Symptom                                                                  | Resolution                                                                                   |
| ------------------------------------------------------------------------ | -------------------------------------------------------------------------------------------- |
| Browser shows nginx default / 404                                        | Confirm Magento landed at `/var/www/html/`, not `/var/www/html/project-community-edition/`.  |
| `Failed loading Zend extension 'opcache.so'` warning                     | Already fixed in this repo via `.warden/php/10-opcache.ini`. Verify the bind mount is live.  |
| `Could not authenticate against repo.magento.com`                        | Re-run `composer global config http-basic.repo.magento.com ...` with valid Marketplace keys. |
| `setup:install` fails on OpenSearch                                      | Confirm container is healthy: `warden env ps` → `mage249-opensearch-1` should be `running`.  |
| Static content missing after editing themes                              | `bin/magento cache:flush && bin/magento s:s:d -f`                                            |
| Admin URL unknown                                                        | `bin/magento info:adminuri`                                                                  |
| Need to start over                                                       | `warden env down -v && warden env up && warden shell` then re-run `setup:install`.           |

---

**Module management cheat sheet**

```bash
bin/magento module:status
bin/magento module:enable <Vendor_Module>
bin/magento module:disable <Vendor_Module>
```

**Cache management cheat sheet**

```bash
bin/magento cache:status
bin/magento cache:clean
bin/magento cache:flush
bin/magento cache:disable block_html full_page layout
```
