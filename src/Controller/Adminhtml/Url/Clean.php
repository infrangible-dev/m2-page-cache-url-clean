<?php

namespace Infrangible\PageCacheUrlClean\Controller\Adminhtml\Url;

use Exception;
use FeWeDev\Base\Variables;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\PageCache\Model\Cache\Type;
use Zend_Cache;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Clean
    extends Action
{
    public const ADMIN_RESOURCE = 'Infrangible_PageCacheUrlClean::infrangible_page_cache_url_clean';

    /** @var Variables */
    protected $variables;

    /** @var Type */
    protected $pageCacheType;

    /**
     * @param Context   $context
     * @param Variables $variableHelper
     * @param Type      $pageCacheType
     */
    public function __construct(Context $context, Variables $variableHelper, Type $pageCacheType)
    {
        parent::__construct($context);

        $this->variables = $variableHelper;
        $this->pageCacheType = $pageCacheType;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        try {
            $url = $this->_request->getParam('url');

            if ($this->variables->isEmpty($url)) {
                $this->messageManager->addErrorMessage(__('No url to clean.'));
            } else {
                $this->pageCacheType->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, [sha1($url)]);

                $this->messageManager->addSuccessMessage(__('The url was cleaned.'));
            }
        } catch (Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, __('An error occurred while cleaning the url.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('adminhtml/cache');
    }
}
