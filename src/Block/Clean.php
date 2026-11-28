<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\PageCacheUrlClean\Block;

use Infrangible\PageCacheUrlClean\Controller\Adminhtml\Url\Clean as CleanController;
use Magento\Backend\Block\Template;

/**
 * The form for cleaning the cache of a URL on the cache management page, for admin users with the permission.
 */
class Clean extends Template
{
    /**
     * Renders the form, if the admin user may clean the cache of URLs.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        return $this->_authorization->isAllowed(CleanController::ADMIN_RESOURCE) ? parent::_toHtml() : '';
    }

    /**
     * Returns the URL of the form.
     *
     * @return string
     */
    public function getCleanUrl(): string
    {
        return $this->getUrl('infrangible_page_cache_url_clean/url/clean');
    }
}
