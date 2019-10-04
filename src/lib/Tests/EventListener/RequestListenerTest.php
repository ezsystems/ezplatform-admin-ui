<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\EventListener;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use PHPUnit\Framework\TestCase;
use EzSystems\EzPlatformAdminUi\EventListener\RequestListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPUnit\Framework\MockObject\MockObject;

class RequestListenerTest extends TestCase
{
    /** @var RequestListener */
    private $requestListener;

    /** @var Request */
    private $request;

    /** @var RequestEvent */
    private $event;

    /** @var HttpKernelInterface|MockObject */
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
        $this->expectExceptionMessage('Route is not allowed in current siteaccess');

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
