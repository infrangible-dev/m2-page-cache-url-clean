<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\PageCacheUrlClean\Helper;

/**
 * The cache tag of the URL of a page.
 */
class Data
{
    /**
     * Returns the cache tag of the URL.
     *
     * @param string $url
     *
     * @return string
     */
    public function getUrlTag(string $url): string
    {
        return sha1($url);
    }
}
