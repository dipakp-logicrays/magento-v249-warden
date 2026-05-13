<?php

declare(strict_types=1);

namespace Mage\ConfigSearch\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class ConfigSearch extends Template
{
    protected $_template = 'Mage_ConfigSearch::config-search.phtml';

    public function getSearchUrl(): string
    {
        return $this->getUrl('lr_configsearch/config/search');
    }

    public function getCurrentScope(): string
    {
        if ($this->getRequest()->getParam('store')) {
            return 'stores';
        }
        if ($this->getRequest()->getParam('website')) {
            return 'websites';
        }
        return 'default';
    }

    public function getCurrentScopeCode(): string
    {
        return (string) ($this->getRequest()->getParam('store')
            ?: $this->getRequest()->getParam('website')
            ?: '');
    }

    public function getCurrentSection(): string
    {
        return (string) $this->getRequest()->getParam('section', '');
    }
}
