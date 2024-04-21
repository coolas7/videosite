<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use App\Utils\Interfaces\CacheInterface;

class AdminChangesDataSubscriber implements EventSubscriberInterface
{

    protected $ClearCacheRouteNames = [
        'categories.POST',
        'edit_category.POST',
        'delete_category.GET',
        'delete_video.GET',
        'set_video_duration.GET',
        'update_video_category.POST',
        'like_video.POST',
        'dislike_video.POST',
        'undo_like_video.POST',
        'undo_dislike_video.POST',
    ];


    public function __construct(CacheInterface $cache)
    {

        $this->cache = $cache;

    }


    public function onKernelResponse(ResponseEvent $event): void
    {

        $request = $event->getRequest()->attributes->get('_route') . '.' . $event->getRequest()->getMethod();

        if ( !in_array($request, $this->ClearCacheRouteNames))
        {
            
            return;

        }

        $cache = $this->cache->cache;
        $cache->clear();

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
