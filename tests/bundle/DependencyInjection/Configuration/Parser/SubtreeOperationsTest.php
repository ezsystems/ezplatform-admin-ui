<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\AdminUi\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\SubtreeOperations;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\SubtreeOperations
 */
final class SubtreeOperationsTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\SubtreeOperations */
    private $parser;

    /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $contextualizer;

    /**
     * @return array<string, array{int}>
     */
    public function getExpectedCopySubtreeLimit(): iterable
    {
        yield 'default = 100' => [100];
        yield 'no limit = -1' => [-1];
        yield 'disabled = 0' => [0];
    }

    protected function setUp(): void
    {
        $this->parser = new SubtreeOperations();
        $this->contextualizer = $this->createMock(ContextualizerInterface::class);
    }

    /**
     * @dataProvider getExpectedCopySubtreeLimit
     */
    public function testCopySubtreeLimit(int $expectedCopySubtreeLimit): void
    {
        $scopeSettings = [
            'subtree_operations' => [
                'copy_subtree' => [
                    'limit' => $expectedCopySubtreeLimit,
                ],
            ],
        ];
        $currentScope = 'admin_group';

        $this->contextualizer
            ->expects(self::once())
            ->method('setContextualParameter')
            ->with(
                'subtree_operations.copy_subtree.limit',
                $currentScope,
                $expectedCopySubtreeLimit
            );

        $this->parser->mapConfig($scopeSettings, $currentScope, $this->contextualizer);
    }

    public function testCopySubtreeLimitNotSet(): void
    {
        $scopeSettings = [
            'subtree_operations' => null,
        ];
        $currentScope = 'admin_group';

        $this->contextualizer
            ->expects(self::never())
            ->method('setContextualParameter');

        $this->parser->mapConfig($scopeSettings, $currentScope, $this->contextualizer);
    }
}
