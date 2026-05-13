# magento-v249-warden

A **Magento 2.4.9 Community Edition (Luma demo)** workspace running on [Warden](https://warden.dev/).

| Stack       | Version                                        |
| ----------- | ---------------------------------------------- |
| Magento     | 2.4.9 Community Edition                        |
| PHP         | 8.5 (OPcache statically compiled, dev-disabled) |
| Composer    | 2.9                                            |
| MySQL       | 8.0                                            |
| OpenSearch  | 3.3                                            |
| RabbitMQ    | 4.2                                            |
| Redis       | 7.2                                            |
| nginx       | 1.28                                           |
| Mail        | Mailpit (SMTP capture)                         |
| Env type    | Warden `magento2` (`mage249.test`)             |

---

## Table of Contents

- [Quick start](#quick-start)
- [Documentation](#documentation)
- [Repository layout](#repository-layout)
- [Common Warden commands](#common-warden-commands)
- [Host service helpers](#host-service-helpers)
  - [Stop conflicting host services](#stop-conflicting-host-services)
  - [Start Docker services](#start-docker-services)
- [Access URLs](#access-urls)

---

## Quick start

Clone the repo, then follow the full installation guide — it walks through Warden init, certificate signing, Composer install, `setup:install`, and post-install configuration end-to-end.

```bash
git clone git@github.com:dipakp-logicrays/magento-v249-warden.git m249
cd m249
```

Then jump to → **[docs/Installation-warden-setup.md](docs/Installation-warden-setup.md)**.

---

## Documentation

| Doc                                                                                 | What it covers                                                                                          |
| ----------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| [docs/Installation-warden-setup.md](docs/Installation-warden-setup.md)              | End-to-end Magento 2.4.9 install on Warden — prerequisites, install, configure, admin, troubleshooting. |
| [CLAUDE.md](CLAUDE.md)                                                              | Quick reference for AI-assisted work in this repo (commands, conventions, doc map).                     |
| [.claude/docs/](.claude/docs/)                                                      | Coding standards (PHP, JS/jQuery, LESS/HTML, technical, security/perf).                                 |

---

## Repository layout

```text
m249/
├── .warden/                  # Warden env override (warden-env.yml) + PHP/nginx config
│   ├── nginx/custom.conf
│   ├── php/zz-config.ini     # General PHP tuning
│   ├── php/10-opcache.ini    # Overrides the broken opcache.so loader on PHP 8.5
│   ├── php-fpm/www.conf
│   └── warden-env.yml
├── .env                      # Warden environment (versions, flags, domain)
├── .claude/                  # AI coding-standard docs
├── docs/                     # Project documentation
│   └── Installation-warden-setup.md
├── fresh-m2-setup/           # Bundled fresh-install tarball for fast onboarding
├── dbdata/                   # MySQL bind-mount (gitignored)
├── mailpitdata/              # Mailpit SQLite bind-mount (gitignored)
├── app/, bin/, lib/, pub/, setup/, vendor/, ...   # Magento 2.4.9 codebase
└── README.md
```

---

## Common Warden commands

Run from the host machine, in the project root:

```bash
warden env up        # Start project containers
warden env down      # Stop project containers
warden env ps        # Show container status
warden env restart   # Restart all project containers
warden shell         # Drop into the php-fpm container
warden db connect    # Open a mysql client against the project DB
warden sign-certificate mage249.test   # (Re)issue the project TLS cert
```

Inside the container (`warden shell`):

```bash
bin/magento cache:flush
bin/magento setup:upgrade && bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento indexer:reindex
```

---

## Host service helpers

Warden binds ports 80/443/3306/9200 etc. on the host. If you also run Apache/MySQL/Elasticsearch/Mailpit as host-level services, they'll collide with the containers. The two helpers below stop those host services and (re)start Docker cleanly.

> Save each block as a script (e.g. `scripts/stop-host-services.sh`) and `chmod +x` it, or paste the body into a shell. Both require `sudo`.

### Stop conflicting host services

```bash
#!/usr/bin/env bash
# Stops host-level Apache2, MySQL, Elasticsearch/OpenSearch, and Mailpit
# so Warden's containers can bind their ports.

echo "🌐 Stopping Apache2..."
sudo service apache2 stop
echo

echo "🗄️  Stopping MySQL..."
sudo service mysql stop
echo

echo "🔍 Stopping Elasticsearch..."
sudo service elasticsearch stop
echo

echo "🔎 Stopping OpenSearch..."
sudo service opensearch stop
echo

echo "📧 Stopping Mailpit..."
sudo systemctl stop mailpit.service
echo

# echo "⚡ Stopping Redis Server..."
# sudo systemctl redis stop
# sudo systemctl redis-server stop
```

### Start Docker services

```bash
#!/usr/bin/env bash
# Ensures Docker and containerd are enabled and running on the host.

echo "✅ Enabling docker.service..."
sudo systemctl enable docker.service
echo

echo "🔌 Starting docker.socket..."
sudo systemctl start docker.socket
echo

echo "🐳 Starting Docker..."
sudo service docker start
echo

echo "✅ Enabling containerd.service..."
sudo systemctl enable containerd.service
echo

echo "🐳 Starting containerd.service..."
sudo systemctl start containerd.service
echo

echo "🔐 Setting permission on /var/run/docker.sock..."
sudo chmod 777 /var/run/docker.sock
echo

echo "✅ All Docker services started successfully."
```

---

## Access URLs

| Service      | URL                                |
| ------------ | ---------------------------------- |
| Storefront   | <https://app.mage249.test/>        |
| Admin        | <https://app.mage249.test/admin>   |
| Status panel | <https://status.mage249.test/>     |
| Mailpit      | <https://mailpit.mage249.test/>    |
| Portainer    | <http://localhost:9000/>           |

> Make sure these hostnames resolve to `127.0.0.1` in your `/etc/hosts` — see the [Add hosts entries](docs/Installation-warden-setup.md#9-add-hosts-entries-on-the-host-machine) section in the installation guide.
