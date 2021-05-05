<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Renderer;

use Symfony\Component\Stopwatch\Stopwatch;

final class TraceableRenderer implements RendererInterface
{

    /** @var \EzSystems\EzPlatformAdminUi\Component\Renderer\RendererInterface */
    private $decorated;

    /** @var \Symfony\Component\Stopwatch\Stopwatch */
    private $stopwatch;

    public function __construct(RendererInterface $decorated, Stopwatch $stopwatch)
    {
        $this->decorated = $decorated;
        $this->stopwatch = $stopwatch;
    }

    public function renderGroup(string $groupName, array $parameters = []): array
    {
        $event = $this->stopwatch->start(sprintf('%s', $groupName), 'admin-ui');

        try {
            $rendered = $this->decorated->renderGroup($groupName, $parameters);
        } finally {
            if ($event->isStarted()) {
                $event->stop();
            }
        }

        return $rendered;
    }

    public function renderSingle(string $name, $groupName, array $parameters = []): string
    {
        $event = $this->stopwatch->start(sprintf('%s - %s', $groupName, $name), 'admin-ui');

        try {
            $rendered = $this->decorated->renderSingle($name, $groupName, $parameters);
        } finally {
            if ($event->isStarted()) {
                $event->stop();
            }
        }

        return $rendered;
    }
}