<?php


namespace Acme\Integration;

use Psr\Cache\CacheException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class DataProviderDecorator implements DataProviderInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    public function __construct(DataProviderInterface $dataProvider, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Request $request): array
    {
        try {
            $cacheKey = $this->getCacheKey($request);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->dataProvider->get($request);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new \DateTime())->modify('+1 day')
                );

            $this->cache->save($cacheItem);

            return $result;
        } catch (CacheException $e) {
            $this->logger->critical($e->getMessage());

            return $this->dataProvider->get($request);
        }
    }

    public function getCacheKey(Request $request): string
    {
        return 'prefix_' . $request->getSomeField();
    }
}
