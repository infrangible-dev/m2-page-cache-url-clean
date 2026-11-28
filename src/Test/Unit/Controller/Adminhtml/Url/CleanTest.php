<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\PageCacheUrlClean\Test\Unit\Controller\Adminhtml\Url;

use FeWeDev\Base\Variables;
use Infrangible\PageCacheUrlClean\Controller\Adminhtml\Url\Clean;
use Infrangible\PageCacheUrlClean\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\PageCache\Model\Cache\Type;
use PHPUnit\Framework\TestCase;

/**
 * Tests cleaning the cache of a URL from the cache management.
 */
class CleanTest extends TestCase
{
    /** @var list<string> the messages of the controller */
    private array $messages = [];

    /** @var list<array{string, list<string>}> the modes and tags of the cleanings */
    private array $cleanings = [];

    /** @var string|null the path of the redirect */
    private ?string $redirectPath = null;

    /**
     * Returns the controller with a request with the URL.
     *
     * @param string|null $url
     *
     * @return Clean
     */
    private function createController(?string $url): Clean
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturnCallback(fn(string $name): ?string => $name === 'url' ? $url : null);

        $messageManager = $this->createStub(ManagerInterface::class);
        $messageManager->method('addErrorMessage')->willReturnCallback(
            function ($message) use ($messageManager): ManagerInterface {
                $this->messages[] = 'error: ' . $message;

                return $messageManager;
            }
        );
        $messageManager->method('addSuccessMessage')->willReturnCallback(
            function ($message) use ($messageManager): ManagerInterface {
                $this->messages[] = 'success: ' . $message;

                return $messageManager;
            }
        );

        $redirect = $this->createStub(Redirect::class);
        $redirect->method('setPath')->willReturnCallback(
            function (string $path) use ($redirect): Redirect {
                $this->redirectPath = $path;

                return $redirect;
            }
        );

        $resultFactory = $this->createStub(ResultFactory::class);
        $resultFactory->method('create')->willReturn($redirect);

        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManager);
        $context->method('getResultFactory')->willReturn($resultFactory);

        $pageCacheType = $this->createStub(Type::class);
        $pageCacheType->method('clean')->willReturnCallback(
            function (string $mode, array $tags): bool {
                $this->cleanings[] = [$mode, array_values(array_map('strval', $tags))];

                return true;
            }
        );

        return new Clean($context, new Variables(), $pageCacheType, new Data());
    }

    public function testCleanUrlWithoutSurroundingSpaces(): void
    {
        $this->createController('  https://example.com/bag.html ')->execute();

        $this->assertSame([['matchingAnyTag', [sha1('https://example.com/bag.html')]]], $this->cleanings);
        $this->assertSame(['success: The url was cleaned.'], $this->messages);
        $this->assertSame('adminhtml/cache', $this->redirectPath);
    }

    public function testNoCleaningWithoutUrl(): void
    {
        $this->createController('  ')->execute();

        $this->assertSame([], $this->cleanings);
        $this->assertSame(['error: No url to clean.'], $this->messages);
        $this->assertSame('adminhtml/cache', $this->redirectPath);
    }
}
