<?php
/**
 * Public alias for the application entry point
 *
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */

use Magento\Framework\App\Bootstrap;

try {
    require __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    http_response_code(500);
    exit(1);
}

// CUSTOM: maintenance page wins even in developer mode (Magento only enforces it in production).
$maintenanceFlagFile = BP . '/var/.maintenance.flag';
if (file_exists($maintenanceFlagFile)) {
    $maintenanceIpFile = BP . '/var/.maintenance.ip';
    $allowedIps = file_exists($maintenanceIpFile)
        ? array_filter(array_map('trim', explode(',', file_get_contents($maintenanceIpFile))))
        : [];
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remoteAddr, $allowedIps)) {
        require __DIR__ . '/errors/503.php';
        exit;
    }
}

// CUSTOM: bootstrap once to read deployment config so we can resolve the website context.
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

// CUSTOM: look up HTTP_HOST against the website_configuration map in env.php.
$deploymentConfig = $objectManager->get(\Magento\Framework\App\DeploymentConfig::class);
$websiteConfiguration = $deploymentConfig->get('website_configuration');

$params = $_SERVER;
if (is_array($websiteConfiguration)) {
    foreach ($websiteConfiguration as $website) {
        if ($_SERVER['HTTP_HOST'] === $website['domain']) {
            $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = $website['run_code'];
            $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = $website['run_type'];
            break;
        }
    }
}

// CUSTOM: maintenance was handled above — skip Magento's internal assertion.
if (file_exists($maintenanceFlagFile)) {
    $params[Bootstrap::PARAM_REQUIRE_MAINTENANCE] = null;
}

$bootstrap = Bootstrap::create(BP, $params);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);
