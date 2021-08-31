<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\NotificationService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\Notification\Renderer\Registry;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\NotificationAdapter;
use EzSystems\EzPlatformAdminUiBundle\View\EzPagerfantaView;
use EzSystems\EzPlatformAdminUiBundle\View\Template\EzPagerfantaTemplate;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationController extends Controller
{
    /** @var \eZ\Publish\API\Repository\NotificationService */
    protected $notificationService;

    /** @var \eZ\Publish\Core\Notification\Renderer\Registry */
    protected $registry;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    protected $translator;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        NotificationService $notificationService,
        Registry $registry,
        TranslatorInterface $translator,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationService = $notificationService;
        $this->registry = $registry;
        $this->translator = $translator;
        $this->configResolver = $configResolver;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $offset
     * @param int $limit
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getNotificationsAction(Request $request, int $offset, int $limit): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $notificationList = $this->notificationService->loadNotifications($offset, $limit);
            $response->setData([
                'pending' => $this->notificationService->getPendingNotificationCount(),
                'total' => $notificationList->totalCount,
                'notifications' => $notificationList->items,
            ]);
        } catch (\Exception $exception) {
            $response->setData([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderNotificationsPageAction(int $page): Response
    {
        $pagerfanta = new Pagerfanta(
            new NotificationAdapter($this->notificationService)
        );
        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination.notification_limit'));
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        $notifications = '';
        foreach ($pagerfanta->getCurrentPageResults() as $notification) {
            if ($this->registry->hasRenderer($notification->type)) {
                $renderer = $this->registry->getRenderer($notification->type);
                $notifications .= $renderer->render($notification);
            }
        }

        $routeGenerator = function ($page) {
            return $this->generateUrl('ezplatform.notifications.render.page', [
                'page' => $page,
            ]);
        };

        $pagination = (new EzPagerfantaView(new EzPagerfantaTemplate($this->translator)))->render($pagerfanta, $routeGenerator);

        return new Response($this->render('@ezdesign/account/notifications/list.html.twig', [
            'page' => $page,
            'pagination' => $pagination,
            'notifications' => $notifications,
            'pager' => $pagerfanta,
        ])->getContent());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function countNotificationsAction(): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $response->setData([
                'pending' => $this->notificationService->getPendingNotificationCount(),
                'total' => $this->notificationService->getNotificationCount(),
            ]);
        } catch (\Exception $exception) {
            $response->setData([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * We're not able to establish two-way stream (it requires additional
     * server service for websocket connection), so * we need a way to mark notification
     * as read. AJAX call is fine.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $notificationId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function markNotificationAsReadAction(Request $request, $notificationId): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $notification = $this->notificationService->getNotification((int)$notificationId);

            $this->notificationService->markNotificationAsRead($notification);

            $data = ['status' => 'success'];

            if ($this->registry->hasRenderer($notification->type)) {
                $url = $this->registry->getRenderer($notification->type)->generateUrl($notification);

                if ($url) {
                    $data['redirect'] = $url;
                }
            }

            $response->setData($data);
        } catch (\Exception $exception) {
            $response->setData([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            $response->setStatusCode(404);
        }

        return $response;
    }
}
