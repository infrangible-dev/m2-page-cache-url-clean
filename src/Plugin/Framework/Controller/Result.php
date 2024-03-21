<?php

namespace Infrangible\PageCacheUrlClean\Plugin\Framework\Controller;

use Laminas\Http\Request;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\PageCache\Model\Config;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Result
{
    /** @var Config */
    private $config;

    /** @var \Magento\Framework\App\Request\Http */
    private $request;

    /**
     * @param Config                              $config
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(Config $config, \Magento\Framework\App\Request\Http $request)
    {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @param ResultInterface $subject
     * @param ResultInterface $result
     * @param Http            $response
     *
     * @return ResultInterface
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result,
        Http $response
    ): ResultInterface {
        if (!$this->config->isEnabled() || $this->config->getType() != Config::BUILT_IN) {
            return $result;
        }

        $tagsHeader = $response->getHeader('X-Magento-Tags');
        $tags = [];

        if ($tagsHeader) {
            $tags = explode(',', $tagsHeader->getFieldValue());
            $response->clearHeader('X-Magento-Tags');
        }

        if ($this->request instanceof Request) {
            $url = $this->request->getUriString();
            $tags[] = sha1($url);
        }

        $response->setHeader('X-Magento-Tags', implode(',', $tags));

        return $result;
    }
}
