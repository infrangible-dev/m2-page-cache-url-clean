<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\PageCacheUrlClean\Plugin\Framework\Controller;

use FeWeDev\Base\Variables;
use Infrangible\PageCacheUrlClean\Helper\Data;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Controller\ResultInterface;
use Magento\PageCache\Model\Config;

/**
 * Adds the tag of the URL of the page to the tags of the page, with which the built-in full page cache saves it.
 */
class Result
{
    /** The header with the tags of the page. */
    private const TAGS_HEADER = 'X-Magento-Tags';

    /**
     * @param Config $config
     * @param HttpRequest $request
     * @param Variables $variables
     * @param Data $helper
     */
    public function __construct(
        protected Config $config,
        protected HttpRequest $request,
        protected Variables $variables,
        protected Data $helper
    ) {
    }

    /**
     * Adds the tag of the URL, if the built-in full page cache is used.
     *
     * @param ResultInterface $subject
     * @param ResultInterface $result
     * @param HttpResponse $response
     *
     * @return ResultInterface
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result,
        HttpResponse $response
    ): ResultInterface {
        if (! $this->config->isEnabled() || $this->variables->intValue($this->config->getType()) !== Config::BUILT_IN) {
            return $result;
        }

        $tagsHeader = $response->getHeader(self::TAGS_HEADER);
        $tags = is_object($tagsHeader) ? explode(',', $tagsHeader->getFieldValue()) : [];

        $tags[] = $this->helper->getUrlTag($this->request->getUriString());

        $response->setHeader(self::TAGS_HEADER, implode(',', array_unique($tags)), true);

        return $result;
    }
}
