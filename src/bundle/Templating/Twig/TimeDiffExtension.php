<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use DateTime;
use DateTimeInterface;
use EzSystems\EzPlatformAdminUi\UI\Service\DateTimeFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeDiffExtension extends AbstractExtension
{
    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\DateTimeFormatter */
    private $dateTimeFormatter;

    public function __construct(DateTimeFormatter $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function getFilters()
    {
        return [
            new TwigFilter(
                'ez_datetime_diff',
                [$this, 'diff'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function diff(DateTimeInterface $from, ?DateTimeInterface $to = null): string
    {
        return $this->dateTimeFormatter->formatDiff($from, $to ?? new DateTime());
    }
}
