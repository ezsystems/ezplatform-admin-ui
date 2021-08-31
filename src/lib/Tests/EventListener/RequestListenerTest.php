<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\EventListener;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\EventListener\RequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListenerTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\EventListener\RequestListener */
    private $requestListener;

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    /** @var \Symfony\Component\HttpKernel\Event\RequestEvent */
    private $event;

    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $httpKernel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestListener = new RequestListener(['some_name' => ['group_1']]);

        $this->request = $this
            ->getMockBuilder(Request::class)
            ->setMethods(['getSession', 'hasSession'])
            ->getMock();

        $this->httpKernel = $this->createMock(HttpKernelInterface::class);

        $this->event = new RequestEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST
        );
    }

    public function testOnKernelRequestDeniedAccess()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The route is not allowed in the current SiteAccess');

        $this->request->attributes->set('siteaccess', new SiteAccess('some_name'));
        $this->request->attributes->set('siteaccess_group_whitelist', ['group_2', 'group_3']);

        $this->requestListener->onKernelRequest($this->event);
    }

    public function testOnKernelRequestAllowAccessWithSubRequest()
    {
        $this->event = new RequestEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::SUB_REQUEST
        );

        $this->assertNull($this->requestListener->onKernelRequest($this->event));
    }

    public function testOnKernelRequestAllowAccessWithoutSiteAccess()
    {
        $this->request->attributes->set('siteaccess', 'not_siteaccess_object');

        $this->assertNull($this->requestListener->onKernelRequest($this->event));
    }

    public function testOnKernelRequestAllowAccessWithoutGroupWhitelist()
    {
        $this->request->attributes->set('siteaccess_group_whitelist', null);

        $this->assertNull($this->requestListener->onKernelRequest($this->event));
    }

    public function testOnKernelRequestAllowAccessWhenGroupMatch()
    {
        $this->request->attributes->set('siteaccess', new SiteAccess('some_name'));
        $this->request->attributes->set('siteaccess_group_whitelist', ['group_1', 'group_2']);

        $this->assertNull($this->requestListener->onKernelRequest($this->event));
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([KernelEvents::REQUEST => ['onKernelRequest', 13]], $this->requestListener::getSubscribedEvents());
    }
}
