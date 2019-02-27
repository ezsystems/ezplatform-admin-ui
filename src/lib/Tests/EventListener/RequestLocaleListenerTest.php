<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\EventListener\RequestLocaleListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\Translation\TranslatorInterface;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class RequestLocaleListenerTest extends TestCase
{
    private const ADMIN_SITEACCESS = 'admin_siteaccess';

    private const NON_ADMIN_SITEACCESS = 'non_admin_siteaccess';

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface|MockObject */
    private $httpKernel;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    protected function setUp()
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->request = $this
            ->getMockBuilder(Request::class)
            ->setMethods(['getSession', 'hasSession', 'setLocale'])
            ->getMock();

        $this->request->attributes->set('siteaccess', new SiteAccess(self::ADMIN_SITEACCESS));

        $this->httpKernel = $this->createMock(HttpKernelInterface::class);

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->userLanguagePreferenceProvider = $this
            ->getMockBuilder(UserLanguagePreferenceProviderInterface::class)
            ->setConstructorArgs([$requestStack])
            ->getMock();

        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->configResolver
            ->method('getParameter')
            ->willReturn([]);
    }

    public function testLocaleIsNotSetOnNonAdminSiteaccess(): void
    {
        $translator = $this->translatorWithSetLocaleExpectsNever();

        $request = $this->requestWithSetLocaleExpectsNever();

        $request->attributes->set('siteaccess', new SiteAccess(self::NON_ADMIN_SITEACCESS));

        $event = new GetResponseEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $requestLocaleListener = new RequestLocaleListener(
            ['admin_group' => [self::ADMIN_SITEACCESS]],
            [],
            $translator,
            $this->userLanguagePreferenceProvider,
            $this->configResolver
        );

        $requestLocaleListener->onKernelRequest($event);
    }

    public function testLocaleIsNotSetOnSubRequest(): void
    {
        $translator = $this->translatorWithSetLocaleExpectsNever();

        $request = $this->requestWithSetLocaleExpectsNever();

        $request->attributes->set('siteaccess', new SiteAccess(self::ADMIN_SITEACCESS));

        $event = new GetResponseEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::SUB_REQUEST
        );

        $requestLocaleListener = new RequestLocaleListener(
            [],
            [],
            $translator,
            $this->userLanguagePreferenceProvider,
            $this->configResolver
        );

        $requestLocaleListener->onKernelRequest($event);
    }

    public function testLocaleIsSet(): void
    {
        $this->translator
            ->expects($this->once())
            ->method('setLocale')
            ->with('en_US');

        $this->request
            ->expects($this->once())
            ->method('setLocale')
            ->with('en_US');

        $event = new GetResponseEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->userLanguagePreferenceProvider
            ->method('getPreferredLocales')
            ->willReturn(['en_US']);

        $requestLocaleListener = new RequestLocaleListener(
            ['admin_group' => [self::ADMIN_SITEACCESS]],
            ['en_GB', 'en_US'],
            $this->translator,
            $this->userLanguagePreferenceProvider,
            $this->configResolver
        );

        $requestLocaleListener->onKernelRequest($event);
    }

    public function testLocaleIsSetWithoutAvailableTranslation(): void
    {
        $this->translator
            ->expects($this->once())
            ->method('setLocale')
            ->with('en_US');

        $this->request
            ->expects($this->once())
            ->method('setLocale')
            ->with('en_US');

        $event = new GetResponseEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->userLanguagePreferenceProvider
            ->method('getPreferredLocales')
            ->willReturn(['en_US']);

        $requestLocaleListener = new RequestLocaleListener(
            ['admin_group' => [self::ADMIN_SITEACCESS]],
            [],
            $this->translator,
            $this->userLanguagePreferenceProvider,
            $this->configResolver
        );

        $requestLocaleListener->onKernelRequest($event);
    }

    public function testSubscribedEvents(): void
    {
        $requestLocaleListener = new RequestLocaleListener(
            ['admin_group' => [self::ADMIN_SITEACCESS]],
            [],
            $this->translator,
            $this->userLanguagePreferenceProvider,
            $this->configResolver
        );

        $this->assertSame([KernelEvents::REQUEST => ['onKernelRequest', 6]], $requestLocaleListener::getSubscribedEvents());
    }

    public function testNonSiteaccessInRequest(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Must be instance of %s', SiteAccess::class));

        $this->request->attributes->set('siteaccess', new Attribute());

        $event = new GetResponseEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $requestLocaleListener = new RequestLocaleListener(
            ['admin_group' => [self::ADMIN_SITEACCESS]],
            [],
            $this->translator,
            $this->userLanguagePreferenceProvider,
            $this->configResolver
        );

        $requestLocaleListener->onKernelRequest($event);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Translation\TranslatorInterface
     */
    private function translatorWithSetLocaleExpectsNever(): MockObject
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->never())
            ->method('setLocale');

        return $translator;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Request
     */
    private function requestWithSetLocaleExpectsNever(): MockObject
    {
        $request = $this
            ->getMockBuilder(Request::class)
            ->setMethods(['getSession', 'hasSession', 'setLocale'])
            ->getMock();
        $request
            ->expects($this->never())
            ->method('setLocale');

        return $request;
    }
}
