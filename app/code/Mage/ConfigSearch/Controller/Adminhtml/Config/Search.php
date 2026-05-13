<?php

declare(strict_types=1);

namespace Mage\ConfigSearch\Controller\Adminhtml\Config;

use Mage\ConfigSearch\Model\ConfigSearchProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Search extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Magento_Config::config';

    public function __construct(
        Context $context,
        private readonly ConfigSearchProvider $searchProvider,
        private readonly JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $query = trim((string) $this->getRequest()->getParam('query', ''));
        $scope = (string) $this->getRequest()->getParam('scope', 'default');
        $scopeCode = (string) $this->getRequest()->getParam('scope_code', '');

        $result = $this->jsonFactory->create();

        if (mb_strlen($query) < 2) {
            return $result->setData(['results' => []]);
        }

        // Set scope params on request so ScopeDefiner::getScope() returns correct scope.
        // The Structure iterators use ScopeDefiner to filter elements by showInDefault/showInWebsite/showInStore.
        if ($scope === 'stores' && $scopeCode !== '') {
            $this->getRequest()->setParam('store', $scopeCode);
            $this->getRequest()->setParam('website', null);
        } elseif ($scope === 'websites' && $scopeCode !== '') {
            $this->getRequest()->setParam('website', $scopeCode);
            $this->getRequest()->setParam('store', null);
        } else {
            $this->getRequest()->setParam('store', null);
            $this->getRequest()->setParam('website', null);
        }

        $results = $this->searchProvider->search($query);

        // Build URLs server-side so the secret key is included automatically
        foreach ($results as &$item) {
            $urlParams = ['section' => $item['section']];
            if ($scope === 'websites' && $scopeCode !== '') {
                $urlParams['website'] = $scopeCode;
            } elseif ($scope === 'stores' && $scopeCode !== '') {
                $urlParams['store'] = $scopeCode;
            }
            $item['url'] = $this->_url->getUrl('adminhtml/system_config/edit', $urlParams);
        }
        unset($item);

        return $result->setData(['results' => $results]);
    }
}
