Installation


# Go to the project directory, below is my project directory, Please note, use your own path
cd /home/logicrays/warden-sites
mkdir m249
cd m249

# run env-init to create the .env file with the configuration needed for Warden and Docker to work with the project.
warden env-init m249 magento2
# or 

Download fresh-m2-setup/m249/magento249-warden-fresh-setup.tar.gz
Extract on your magento root path: e.g /home/logicrays/warden-sites/m249/


# Start warden services up
warden svc up

# Next you’ll want to start the project environment:
warden env up

# Drop into a shell within the project environment. Commands following this step in the setup procedure will be run from within the php-fpm docker container this launches you into:
warden shell


# Drop into a shell within the project environment. Commands following this step in the setup procedure will be run from within the php-fpm docker container this launches you into:
warden shell

# Configure global Magento Marketplace credentials
composer global config http-basic.repo.magento.com <username> <password>

# Create project composer 
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=2.4.9 /tmp/m2-install

rsync -a /tmp/m2-install/ /var/www/html/
rm -rf /tmp/m2-install

# Once the above command runs successfully, move all files and the directory root from `project-community-edition` 

## Install Application
bin/magento setup:install \
     --backend-frontname=backend \
     --amqp-host=rabbitmq \
     --amqp-port=5672 \
     --amqp-user=guest \
     --amqp-password=guest \
     --db-host=db \
     --db-name=magento \
     --db-user=magento \
     --db-password=magento \
     --search-engine=opensearch \
     --opensearch-host=opensearch \
     --opensearch-port=9200 \
     --opensearch-index-prefix=magento2 \
     --opensearch-enable-auth=0 \
     --opensearch-timeout=15 \
     --http-cache-hosts=varnish:80 \
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

## Configure Application
bin/magento config:set --lock-env web/unsecure/base_url \
 "https://${TRAEFIK_SUBDOMAIN}.${TRAEFIK_DOMAIN}/"

bin/magento config:set --lock-env web/secure/base_url \
 "https://${TRAEFIK_SUBDOMAIN}.${TRAEFIK_DOMAIN}/"

bin/magento config:set --lock-env web/secure/offloader_header X-Forwarded-Proto

bin/magento config:set --lock-env web/secure/use_in_frontend 1
bin/magento config:set --lock-env web/secure/use_in_adminhtml 1
bin/magento config:set --lock-env web/seo/use_rewrites 1

bin/magento config:set --lock-env system/full_page_cache/caching_application 2
bin/magento config:set --lock-env system/full_page_cache/ttl 604800

bin/magento config:set --lock-env catalog/search/enable_eav_indexer 1

bin/magento config:set --lock-env dev/static/sign 0

bin/magento deploy:mode:set -s developer
bin/magento cache:disable block_html full_page

bin/magento indexer:reindex
bin/magento cache:flush

# Run Magento commands
bin/magento s:up
bin/magento s:d:c
bin/magento s:s:d -f

# Create admin user:: user: admin password: admin@123
bin/magento admin:user:create

# Disable 2FA modules
bin/magento mod:disable Magento_TwoFactorAuth Magento_AdminAdobeImsTwoFactorAuth

#Run Magento commands again
bin/magento s:up && bin/magento s:d:c && bin/magento s:s:d -f


# Add host entry outside the container
sudo nano /etc/hosts

```
# magento 2.4.9 warden
127.0.0.1 app.mage249.test
127.0.0.1 status.mage249.test