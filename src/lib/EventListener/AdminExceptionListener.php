<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\SiteAccess\AdminFilter;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;
use Throwable;

class AdminExceptionListener
{
    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var array */
    protected $siteAccessGroups;

    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $rootDir;

    /** @var string */
    protected $kernelEnvironment;

    /**
     * @param Environment $twig
     * @param NotificationHandlerInterface $notificationHandler
     * @param array $siteAccessGroups
     * @param string $kernelRootDir
     * @param string $kernelEnvironment
     */
    public function __construct(
        Environment $twig,
        NotificationHandlerInterface $notificationHandler,
        array $siteAccessGroups,
        string $kernelRootDir,
        string $kernelEnvironment
    ) {
        $this->twig = $twig;
        $this->notificationHandler = $notificationHandler;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->kernelEnvironment = $kernelEnvironment;
        $this->rootDir = $kernelRootDir . '/..';
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->kernelEnvironment !== 'prod') {
            return;
        }

        if (!$this->isAdminException($event)) {
            return;
        }

        $response = new Response();
        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $code = $response->getStatusCode();

        $this->notificationHandler->error($this->getNotificationMessage($exception));

        switch ($code) {
            case 404:
                $content = $this->twig->render('@EzPlatformAdminUi/errors/404.html.twig');
                break;
            case 403:
                $content = $this->twig->render('@EzPlatformAdminUi/errors/403.html.twig');
                break;
            default:
                $content = $this->twig->render('@EzPlatformAdminUi/errors/error.html.twig');
                break;
        }

        $response->setContent($content);
        $event->setResponse($response);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return bool
     */
    private function isAdminException(GetResponseForExceptionEvent $event): bool
    {
        $request = $event->getRequest();

        /** @var SiteAccess $siteAccess */
        $siteAccess = $request->get('siteaccess', new SiteAccess());

        return in_array($siteAccess->name, $this->siteAccessGroups[AdminFilter::ADMIN_GROUP_NAME]);
    }

    /**
     * @param Throwable $exception
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
