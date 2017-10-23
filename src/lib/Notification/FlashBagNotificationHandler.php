<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Notification;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashBagNotificationHandler implements NotificationHandlerInterface
{
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'danger';

    /** @var FlashBagInterface */
    protected $flashBag;

    /**
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @param string $message
     */
    public function info(string $message): void
    {
        $this->flashBag->add(self::TYPE_INFO, $message);
    }

    /**
     * @param string $message
     */
    public function success(string $message): void
    {
        $this->flashBag->add(self::TYPE_SUCCESS, $message);
    }

    /**
     * @param string $message
     */
    public function warning(string $message): void
    {
        $this->flashBag->add(self::TYPE_WARNING, $message);
    }

    /**
     * @param string $message
     */
    public function error(string $message): void
    {
        $this->flashBag->add(self::TYPE_ERROR, $message);
    }
}