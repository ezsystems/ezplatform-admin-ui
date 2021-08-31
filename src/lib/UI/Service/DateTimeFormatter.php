<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Service;

use DateTimeInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DateTimeFormatter implements DateTimeFormatterInterface, TranslationContainerInterface
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function formatDiff(DateTimeInterface $from, DateTimeInterface $to): string
    {
        static $units = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        $diff = $to->diff($from);

        foreach ($units as $attribute => $unit) {
            $count = $diff->$attribute;
            if (0 !== $count) {
                return $this->getDiffMessage($count, (bool)$diff->invert, $unit);
            }
        }

        return $this->getEmptyDiffMessage();
    }

    /**
     * @param  int $count  The diff count
     * @param  bool $invert Whether to invert the count
     * @param  string $unit   The unit must be either year, month, day, hour,
     *                         minute or second
     *
     * @return string
     */
    private function getDiffMessage(int $count, bool $invert, string $unit): string
    {
        $id = sprintf('diff.%s.%s', $invert ? 'ago' : 'in', $unit);
        /** @Ignore */
        return $this->translator->trans($id, ['%count%' => $count], 'time_diff');
    }

    private function getEmptyDiffMessage(): string
    {
        return $this->translator->trans(
            /** @Desc("now") */
            'diff.empty',
            [],
            'time_diff'
        );
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('diff.ago.year', 'time_diff'))->setDesc('1 year ago|%count% years ago'),
            (new Message('diff.ago.month', 'time_diff'))->setDesc('1 month ago|%count% months ago'),
            (new Message('diff.ago.day', 'time_diff'))->setDesc('1 day ago|%count% days ago'),
            (new Message('diff.ago.hour', 'time_diff'))->setDesc('1 hour ago|%count% hours ago'),
            (new Message('diff.ago.minute', 'time_diff'))->setDesc('1 minute ago|%count% minutes ago'),
            (new Message('diff.ago.second', 'time_diff'))->setDesc('1 second ago|%count% seconds ago'),
            (new Message('diff.in.year', 'time_diff'))->setDesc('in 1 second|in %count% seconds'),
            (new Message('diff.in.month', 'time_diff'))->setDesc('in 1 hour|in %count% hours'),
            (new Message('diff.in.day', 'time_diff'))->setDesc('in 1 minute|in %count% minutes'),
            (new Message('diff.in.hour', 'time_diff'))->setDesc('in 1 day|in %count% days'),
            (new Message('diff.in.minute', 'time_diff'))->setDesc('in 1 month|in %count% months'),
            (new Message('diff.in.second', 'time_diff'))->setDesc('in 1 year|in %count% years'),
        ];
    }
}
