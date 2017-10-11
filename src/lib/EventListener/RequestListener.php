<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

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
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 13],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
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

        $allowedGroups = is_array($allowedGroups) ?: [$allowedGroups];

        $groups = $this->container->getParameter('ezpublish.siteaccess.groups_by_siteaccess');

        foreach ($groups[$siteAccess->name] as $group) {
            if (in_array($group, $allowedGroups)) {
                return;
            }
        }

        throw new NotFoundHttpException('Route is not allowed in current siteaccess');
    }
}
