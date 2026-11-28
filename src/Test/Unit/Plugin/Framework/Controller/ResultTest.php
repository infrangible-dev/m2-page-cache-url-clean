<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\PageCacheUrlClean\Test\Unit\Plugin\Framework\Controller;

use FeWeDev\Base\Variables;
use Infrangible\PageCacheUrlClean\Helper\Data;
use Infrangible\PageCacheUrlClean\Plugin\Framework\Controller\Result;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Controller\ResultInterface;
use Magento\PageCache\Model\Config;
use PHPUnit\Framework\TestCase;

/**
 * Tests the tag of the URL in the tags of the pages of the built-in full page cache.
 */
class ResultTest extends TestCase
{
    /** The URL of the page in the tests. */
    private const URL = 'https://example.com/bag.html';

    /** @var string|null the tags header that the response got */
    private ?string $setTags = null;

    /**
     * Returns the plugin with the full page cache of the type, enabled or disabled.
     *
     * @param bool $enabled
     * @param int $type
     *
     * @return Result
     */
    private function createPlugin(bool $enabled, int $type = Config::BUILT_IN): Result
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn($enabled);
        $config->method('getType')->willReturn((string)$type);

        $request = $this->createStub(HttpRequest::class);
        $request->method('getUriString')->willReturn(self::URL);

        return new Result($config, $request, new Variables(), new Data());
    }

    /**
     * Returns a response with the tags header, or without it for null.
     *
     * @param string|null $tags
     *
     * @return HttpResponse
     */
    private function createResponse(?string $tags): HttpResponse
    {
        $response = $this->createStub(HttpResponse::class);
        $response->method('getHeader')->willReturn($tags === null ? false : $this->createHeader($tags));
        $response->method('setHeader')->willReturnCallback(
            function (string $name, string $value) use ($response): HttpResponse {
                if ($name === 'X-Magento-Tags') {
                    $this->setTags = $value;
                }

                return $response;
            }
        );

        return $response;
    }

    /**
     * Returns a header with the value.
     *
     * @param string $value
     *
     * @return object
     */
    private function createHeader(string $value): object
    {
        return new class ($value) {
            /**
             * @param string $value
             */
            public function __construct(private readonly string $value)
            {
            }

            /**
             * Returns the value of the header.
             *
             * @return string
             */
            public function getFieldValue(): string
            {
                return $this->value;
            }
        };
    }

    public function testUrlTagAddedToTags(): void
    {
        $result = $this->createStub(ResultInterface::class);

        $this->assertSame(
            $result,
            $this->createPlugin(true)->afterRenderResult($result, $result, $this->createResponse('cat_p_1,cms_b'))
        );
        $this->assertSame('cat_p_1,cms_b,' . sha1(self::URL), $this->setTags);
    }

    public function testUrlTagWithoutOtherTags(): void
    {
        $result = $this->createStub(ResultInterface::class);

        $this->createPlugin(true)->afterRenderResult($result, $result, $this->createResponse(null));

        $this->assertSame(sha1(self::URL), $this->setTags);
    }

    public function testNoTagWithoutBuiltInCache(): void
    {
        $result = $this->createStub(ResultInterface::class);

        $this->createPlugin(false)->afterRenderResult($result, $result, $this->createResponse('cat_p_1'));
        $this->createPlugin(true, Config::VARNISH)
            ->afterRenderResult($result, $result, $this->createResponse('cat_p_1'));

        $this->assertNull($this->setTags);
    }
}
