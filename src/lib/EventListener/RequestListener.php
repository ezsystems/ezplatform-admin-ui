<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListener implements EventSubscriberInterface
{
    /** @var array */
    private $groupsBySiteAccess;

    /**
     * @param $groupsBySiteAccess
     */
    public function __construct(array $groupsBySiteAccess)
    {
        $this->groupsBySiteAccess = $groupsBySiteAccess;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 13],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $requestAttributes = $event->getRequest()->attributes;

        $siteAccess = $requestAttributes->get('siteaccess');
        $allowedGroups = $requestAttributes->get('siteaccess_group_whitelist');

        if (!$siteAccess instanceof SiteAccess || empty($allowedGroups)) {
            return;
        }

        $allowedGroups = (array)$allowedGroups;

        foreach ($this->groupsBySiteAccess[$siteAccess->name] as $group) {
            if (in_array($group, $allowedGroups, true)) {
                return;
            }
        }

        throw new NotFoundHttpException('The route is not allowed in the current SiteAccess');
    }
}
