<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\PageCacheUrlClean\Controller\Adminhtml\Url;

use Exception;
use FeWeDev\Base\Variables;
use Infrangible\PageCacheUrlClean\Helper\Data;
use LogicException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\PageCache\Model\Cache\Type;
use Zend_Cache;

/**
 * Removes the pages of the URL from the built-in full page cache.
 */
class Clean extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Infrangible_PageCacheUrlClean::infrangible_page_cache_url_clean';

    /**
     * @param Context $context
     * @param Variables $variables
     * @param Type $pageCacheType
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        protected Variables $variables,
        protected Type $pageCacheType,
        protected Data $helper
    ) {
        parent::__construct($context);
    }

    /**
     * Removes the pages of the URL of the request from the cache and returns to the cache management.
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $url = trim($this->variables->stringValueOrDefault($this->getRequest()->getParam('url')));

        if ($url === '') {
            $this->messageManager->addErrorMessage(__('No url to clean.')->render());
        } else {
            try {
                $this->pageCacheType->clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                    [$this->helper->getUrlTag($url)]
                );

                $this->messageManager->addSuccessMessage(__('The url was cleaned.')->render());
            } catch (Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('An error occurred while cleaning the url.')->render()
                );
            }
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (! $resultRedirect instanceof Redirect) {
            throw new LogicException('The result factory created no redirect');
        }

        return $resultRedirect->setPath('adminhtml/cache');
    }
}
