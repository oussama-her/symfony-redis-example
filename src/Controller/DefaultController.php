<?php

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @Route("/default", name="app_default")
     */
    public function index(): Response
    {
        $cacheKey = '123';

        try {
            $cachedItem = $this->cache->getItem($cacheKey);
            $cachedItem->set('some value');
            $this->cache->save($cachedItem);
        } catch (InvalidArgumentException $e) {
            throw new \http\Exception\InvalidArgumentException($e->getMessage());
        }

        if (!$cachedItem->isHit()) {
            $cachedItem->set('some value');
            $this->cache->save($cachedItem);
        }

        return $this->json(['hit' => $cachedItem->isHit(), 'value' => $cachedItem->get()]);
    }
}
