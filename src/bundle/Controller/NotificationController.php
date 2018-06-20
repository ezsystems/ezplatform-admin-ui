<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\NotificationService;
use eZ\Publish\Core\Notification\Renderer\Registry;
use eZ\Publish\SPI\Persistence\Notification\Notification;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /** @var \eZ\Publish\API\Repository\NotificationService $notificationService */
    protected $notificationService;

    /** @var \eZ\Publish\Core\Notification\Renderer\Registry */
    protected $registry;

    /**
     * @param \eZ\Publish\API\Repository\NotificationService $notificationService
     * @param \eZ\Publish\Core\Notification\Renderer\Registry $registry
     */
    public function __construct(
        NotificationService $notificationService,
        Registry $registry
    ) {
        $this->notificationService = $notificationService;
        $this->registry = $registry;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $limit Notification count per page
     * @param int $page Notification page to return (routing default: 0)
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getNotificationsAction(Request $request, int $limit, int $page): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $response->setData([
                'pending' => $this->notificationService->getUserPendingNotificationCount(),
                'total' => $this->notificationService->getUserNotificationCount(),
                'notifications' => $this->notificationService->getUserNotifications($limit, $page),
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
     * @param int $limit
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderNotificationsAction(int $limit, int $page): Response
    {
        $notifications = $this->notificationService->getUserNotifications($limit, $page);

        $html = '';
        foreach ($notifications as $notification) {
            if ($this->registry->hasRenderer($notification->type)) {
                $renderer = $this->registry->getRenderer($notification->type);
                $html .= $renderer->render($notification);
            }
        }

        return new Response($html);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function countNotificationsAction(): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $response->setData([
                'pending' => $this->notificationService->getUserPendingNotificationCount(),
                'total' => $this->notificationService->getUserNotificationCount(),
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
            /** @var Notification $notification */
            $notification = $this->notificationService->getNotification($notificationId);

            $this->notificationService->markNotificationAsRead($notification->id);

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
