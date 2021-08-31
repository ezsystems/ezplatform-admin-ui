<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Throwable;
use Twig\Environment;
use Twig\Error\RuntimeError;

class AdminExceptionListener
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var \Twig\Environment */
    protected $twig;

    /** @var \Symfony\WebpackEncoreBundle\Asset\TagRenderer */
    protected $encoreTagRenderer;

    /** @var \Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface */
    private $entrypointLookupCollection;

    /** @var array */
    protected $siteAccessGroups;

    /** @var string */
    protected $rootDir;

    /** @var string */
    protected $kernelEnvironment;

    /**
     * @param \Twig\Environment $twig
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\WebpackEncoreBundle\Asset\TagRenderer $encoreTagRenderer
     * @param \Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface $entrypointLookupCollection
     * @param array $siteAccessGroups
     * @param string $kernelRootDir
     * @param string $kernelEnvironment
     */
    public function __construct(
        Environment $twig,
        NotificationHandlerInterface $notificationHandler,
        TagRenderer $encoreTagRenderer,
        EntrypointLookupCollectionInterface $entrypointLookupCollection,
        array $siteAccessGroups,
        string $kernelProjectDir,
        string $kernelEnvironment
    ) {
        $this->twig = $twig;
        $this->notificationHandler = $notificationHandler;
        $this->encoreTagRenderer = $encoreTagRenderer;
        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->rootDir = $kernelProjectDir;
        $this->kernelEnvironment = $kernelEnvironment;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if ($this->kernelEnvironment !== 'prod') {
            return;
        }

        if (!$this->isAdminException($event)) {
            return;
        }

        $response = new Response();
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $code = $response->getStatusCode();

        // map exception to UI notification
        $this->notificationHandler->error(/** @Ignore */ $this->getNotificationMessage($exception));

        if ($exception instanceof RuntimeError) {
            // If exception is coming from the template where encore already
            // rendered resources it would result in no CSS/JS on error page.
            // Thus we reset TagRenderer to prevent it from breaking error page.
            $this->encoreTagRenderer->reset();
            $this->entrypointLookupCollection->getEntrypointLookup('ezplatform')->reset();
        }

        switch ($code) {
            case 404:
                $content = $this->twig->render('@ezdesign/ui/error_page/404.html.twig');
                break;
            case 403:
                $content = $this->twig->render('@ezdesign/ui/error_page/403.html.twig');
                break;
            default:
                $content = $this->twig->render('@ezdesign/ui/error_page/unknown.html.twig');
                break;
        }

        $response->setContent($content);
        $event->setResponse($response);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     *
     * @return bool
     */
    private function isAdminException(ExceptionEvent $event): bool
    {
        $request = $event->getRequest();

        /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteAccess */
        $siteAccess = $request->get('siteaccess', new SiteAccess('default'));

        return \in_array($siteAccess->name, $this->siteAccessGroups[EzPlatformAdminUiBundle::ADMIN_GROUP_NAME]);
    }

    /**
     * @param \Throwable $exception
     *
     * @return string
     */
    private function getNotificationMessage(Throwable $exception): string
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getMessage();
        }

        $file = new SplFileInfo($exception->getFile());
        $line = $exception->getLine();

        $relativePathname = (new Filesystem())->makePathRelative($file->getPath(), $this->rootDir) . $file->getFilename();

        $message = $exception->getMessage();

        return sprintf('%s [in %s:%d]', $message, $relativePathname, $line);
    }
}
