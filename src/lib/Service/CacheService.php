<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Service;

use eZ\Publish\Core\Persistence\Legacy\Content\Type\MemoryCachingHandler as CachingContentTypeHandler;
use eZ\Publish\Core\Persistence\Legacy\Handler as LegacyHandler;
use Psr\Cache\CacheItemPoolInterface;

class CacheService
{
    /**
     * @var \eZ\Publish\Core\Persistence\Legacy\Handler as LegacyHandler
     */
    private $handler;

    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;

    /**
     * CacheService constructor.
     * @param LegacyHandler $handler
     * @param CacheItemPoolInterface $cachePool
     */
    public function __construct(LegacyHandler $handler, CacheItemPoolInterface $cachePool)
    {
        $this->handler = $handler;
        $this->cachePool = $cachePool;
    }

    public function clearContentTypesCache()
    {
        try {
            $contentTypeHandler = $this->handler->contentTypeHandler();
            if ($contentTypeHandler instanceof CachingContentTypeHandler) {
                $contentTypeHandler->clearCache();
            }

            $this->cachePool->clear();
        } catch (\Exception $e) {
            // FIXME: Catch all
        }
    }
}
