<?php

namespace Infrangible\PageCacheUrlClean\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\AuthorizationInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Clean
    extends Template
{
    /** @var AuthorizationInterface */
    protected $authorization;

    /**
     * @param Context                $context
     * @param AuthorizationInterface $authorization
     * @param array                  $data
     */
    public function __construct(Template\Context $context, AuthorizationInterface $authorization, array $data = [])
    {
        parent::__construct($context, $data);

        $this->authorization = $authorization;
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->authorization->isAllowed('Infrangible_PageCacheUrlClean::infrangible_page_cache_url_clean')) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCleanUrl(): string
    {
        return $this->getUrl('infrangible_page_cache_url_clean/url/clean');
    }
}
